<?php

namespace app\services;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as DB;
use \Monolog\Logger as Logger;
use app\models\User as User;
use app\models\Session as Session;

class UserService
{
    public static function update($data, $userId)
    {
        $user = (new User)->where('id', '=', $userId)->get()->first();

        foreach ((new User)->getFillableFields() as $field)
        {
            if (! empty($data['user'][$field]))
            {
                if ($field == 'password')
                {
                    $user->{$field} = sha1($data['user'][$field]);
                }
                else {
                    $user->{$field} = $data['user'][$field];
                }
            }
        }

        $user->save();

        return $user;
    }

    public static function add($data)
    {
        $user                    = new User();
        $user->firstname          = $data['user']['firstname'];
        $user->lastname           = $data['user']['lastname'];
        $user->email              = $data['user']['email'];
        $user->phone              = $data['user']['phone'];
        $user->password           = sha1($data['user']['password']);
        $user->active             = 1;
        $user->verification_code  = mt_rand(10000, 99999);
        $user->save();

        return $user;
    }

    public static function activate(User $user)
    {
        $user->active = 1;

        return $user->save();
    }
}
