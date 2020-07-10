<?php

namespace app\controllers;

use app\services\PasswordResetService;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as DB;
use \Monolog\Logger as Logger;
use app\models\User as User;
use app\validators\AuthValidator as AuthValidator;
use app\services\AuthService as AuthService;
use app\Mailer as Mailer;

class AuthController extends BaseController
{
    public function __construct(Logger $logger, AuthValidator $validator)
    {
        parent::__construct($logger, $validator);
    }

    public function login(Request $request, Response $response)
    {
        $data = $this->getRequestData($request);

        if (! $this->validator->forLogin()->validate($data))
        {
            //-- validation failed
            return $response->withJson(['success'=> false, 'exception' => $this->validator->getErrorsAsString()], 200);
        }

        if (! $this->validator->forUserActive()->validate($data))
        {
            //-- validation failed
            return $response->withJson(['success'=> true, 'exception' => $this->validator->getErrorsAsString()], 200);
        }

        try
        {
            $user = (new User)->where('email', '=', $data['email'])
                ->where('password', '=', sha1($data['password']))
                ->get()->first();

            $this->responseData['token']   = AuthService::login($user);
            $this->responseData['user']    = $user;
        }
        catch (\Exception $e)
        {
            $this->logger->addError('Could not login user: '. $e->getMessage());

            return $response->withJson(['success'=> false, 'exception' => 'An error occurred while trying to login. Please contact IT Team.'], 500);
        }

        return $response->withJson($this->responseData);
    }

    public function logout(Request $request, Response $response)
    {
        $data = $this->getRequestData($request);

        try
        {
            $this->responseData['success'] = AuthService::logout($data['token']);
        }
        catch (\Exception $e)
        {
            $this->logger->addError('Could not logout user: '. $e->getMessage());

            return $response->withJson(['success'=> false, 'exception' => 'An error occurred while trying to logout. Please contact IT Team.'], 500);
        }

        return $response->withJson($this->responseData);
    }
}
