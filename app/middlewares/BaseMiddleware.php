<?php

namespace app\middlewares;

use \Psr\Http\Message\ServerRequestInterface as Request;

class BaseMiddleware
{
    protected function getRouteParams(Request $request)
    {
        return $request->getAttribute('routeInfo')[2];
    }

    protected function getQueryParams(Request $request)
    {
        return $request->getQueryParams();
    }
}