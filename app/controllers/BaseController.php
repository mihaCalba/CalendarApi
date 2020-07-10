<?php

namespace app\controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Monolog\Logger as Logger;
use app\validators\BaseValidator as BaseValidator;

class BaseController
{
    protected $logger;
    protected $validator;
    protected $responseData = [
        'success' => true,
        'exception' => ''
    ];

    public function __construct(Logger $logger, BaseValidator $validator= null)
    {
        $this->logger    = $logger;
        $this->validator = $validator;
    }

    protected function getRequestData(Request $request)
    {
        $headerData    = $this->getHeaderData($request);
        $authUserId    = $this->getAuthUserId($request);
        $routeParams   = $this->getRouteParams($request);
        $attributeData = $this->getAttribute($request);

        $data = $headerData + $authUserId + $routeParams + $attributeData;

        return $data;
    }

    protected function getHeaderData(Request $request)
    {
        $data = [];

        if (! empty($request->getHeader('token')[0]))
        {
            $data['token'] = $request->getHeader('token')[0];
        }

        return $data;
    }

    protected function getAuthUserId(Request $request)
    {
        $data = [];

        if (! empty($request->getAttribute('user_id')))
        {
            $data['user_id'] = $request->getAttribute('user_id');
        }

        return $data;
    }

    protected function getAttribute(Request $request, $attribute = 'data')
    {
        return ! empty($request->getAttribute($attribute)) ? $request->getAttribute($attribute) : [];
    }

    protected function getRouteParams(Request $request)
    {
        return ! empty($request->getAttribute('routeInfo')[2]) ? $request->getAttribute('routeInfo')[2] : [];
    }

    protected function getQueryParams(Request $request)
    {
        return $request->getQueryParams();
    }

    protected function getQueryParam(Request $request, $paramName)
    {
        $qParams = $request->getQueryParams();

        if (! empty($qParams[$paramName]))
        {
            return $qParams[$paramName];
        }

        return false;
    }
}