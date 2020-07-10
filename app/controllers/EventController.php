<?php

namespace app\controllers;

use app\models\Event;
use app\services\AuthService;
use app\services\EventService;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as DB;
use \Monolog\Logger as Logger;
use app\models\User as User;
use app\validators\EventValidator as EventValidator;
use app\Mailer as Mailer;
use app\services\UserService as UserService;

class EventController extends BaseController
{
    public function __construct(Logger $logger, EventValidator $validator)
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
            $this->responseData['event'] = EventService::add($data);
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
            $event = new Event();

            if (! empty($this->getQueryParam($request, 'date')))
            {
                $event = $event->where('from', '<=', $this->getQueryParam($request, 'date'));
                $event = $event->where('to', '>=', $this->getQueryParam($request, 'date'));
            }

            $this->responseData['events'] = $event->get()->sortBy('from');
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
            $event = new Event();

            $this->responseData['event'] = $event->where('id', '=', $data['event_id'])->get()->first();
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
            $this->responseData['event'] = EventService::update($data, $data['event_id']);
        }
        catch(\Exception $e)
        {
            $this->logger->addInfo('Exception: ' . $e->getMessage());

            return $response->withJson(['success'=> false, 'exception' => 'An error occurred. Please contact IT department.'], 500);
        }

        return $response->withJson($this->responseData);
    }

    public function delete(Request $request, Response $response)
    {
        $data = $this->getRequestData($request);

        if (! $this->validator->forDelete()->validate($data))
        {
            //-- validation failed
            return $response->withJson(['success'=> false, 'exception' => $this->validator->getErrorsAsString()], 200);
        }

        try
        {
            $this->responseData['success'] = EventService::delete($data['event_id']);
        }
        catch(\Exception $e)
        {
            $this->logger->addInfo('Exception: ' . $e->getMessage());

            return $response->withJson(['success'=> false, 'exception' => 'An error occurred. Please contact IT department.'], 500);
        }

        return $response->withJson($this->responseData);
    }

}
