<?php

namespace app\controllers;

use app\models\VehicleType;
use app\services\AuthService;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as DB;
use \Monolog\Logger as Logger;
use app\models\User as User;
use app\validators\UserValidator as UserValidator;
use app\Mailer as Mailer;
use app\services\UserService as UserService;

class UserController extends BaseController
{

    public function __construct(Logger $logger, UserValidator $validator)
    {
        parent::__construct($logger, $validator);
    }

    public function store(Request $request, Response $response)
    {
        $data = $request->getAttribute('data');

        if (! $this->validator->forStore()->validate($data))
        {
            //-- validation failed
            return $response->withJson(['success'=> false, 'exception' => $this->validator->getErrorsAsString()], 200);
        }

        try
        {
            $this->responseData['user'] = UserService::add($data);
        }
        catch(\Exception $e)
        {
            $this->logger->addInfo('Exception: ' . $e->getMessage());

            return $response->withJson(['success'=> false, 'exception' => 'An error occurred. Please contact IT department.'], 500);
        }

        return $response->withJson($this->responseData);
    }

    public function index(Request $request, Response $response)
    {
        try
        {
            $user = new User();

            if (! empty($this->getQueryParam($request, 'email')))
            {
                $user = $user->where('email', '=', $this->getQueryParam($request, 'email'));
            }

            $this->responseData['users'] = $user->get();
        }
        catch(\Exception $e)
        {
            $this->logger->addInfo('Exception: ' . $e->getMessage());

            return $response->withJson(['success'=> false, 'exception' => 'An error occurred. Please contact IT department.'], 500);
        }

        return $response->withJson($this->responseData);
    }

    public function show(Request $request, Response $response)
    {
        $data = $this->getRequestData($request);

        if (! $this->validator->forShow()->validate($data))
        {
            //-- validation failed
            return $response->withJson(['success'=> false, 'exception' => $this->validator->getErrorsAsString()], 200);
        }

        try
        {
            $user = new User();

            $this->responseData['user'] = $user->where('id', '=', $data['user_id'])->get()->first();
        }
        catch(\Exception $e)
        {
            $this->logger->addInfo('Exception: ' . $e->getMessage());

            return $response->withJson(['success'=> false, 'exception' => 'An error occurred. Please contact IT department.'], 500);
        }

        return $response->withJson($this->responseData);
    }

    public function update(Request $request, Response $response)
    {
        $data = $this->getRequestData($request);

        if (! $this->validator->forUpdate()->validate($data))
        {
            //-- validation failed
            return $response->withJson(['success'=> false, 'exception' => $this->validator->getErrorsAsString()], 200);
        }

        try
        {
            $this->responseData['user'] = UserService::update($data, AuthService::getAuthUserId());
        }
        catch(\Exception $e)
        {
            $this->logger->addInfo('Exception: ' . $e->getMessage());

            return $response->withJson(['success'=> false, 'exception' => 'An error occurred. Please contact IT department.'], 500);
        }

        return $response->withJson($this->responseData);
    }

}
