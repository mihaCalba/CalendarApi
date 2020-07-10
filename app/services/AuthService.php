<?php

namespace app\services;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as DB;
use \Monolog\Logger as Logger;
use app\models\User as User;
use app\models\Session as Session;

class AuthService
{
    private static $userId;
    private static $token;

    public static function login(User $user)
    {
        $session = (new Session())->where('user_id', '=', $user->id);

        if ($session->exists())
        {
            return $session->get()->first()->token;
        }

        $token = bin2hex(openssl_random_pseudo_bytes(64));

        $session            = new Session();
        $session->token     = $token;
        $session->user_id   = $user->id;
        $session->save();

        return $token;
    }

    public static function logout($token = null)
    {
        $token = is_null($token) ? self::$token : $token;

        $session = (new Session())->where('token', '=', $token);

        if ($session->exists())
        {
            return $session->get()->first()->delete();
        }

        return true;
    }

    public static function setAuthInfo($userId, $token)
    {
        self::$userId = $userId;
        self::$token  = $token;
    }

    public static function getAuthUserId()
    {
        return self::$userId;
    }

    public static function getToken()
    {
        return self::$token;
    }
}