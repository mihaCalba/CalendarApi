<?php

namespace app\middlewares;


class DataMiddleware extends BaseMiddleware
{
    /**
     * DataMiddleware
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $parsedData = $request->getParsedBody();

        if (! isset($parsedData['data']))
        {
            return $response->withJson(['exception' => "The 'data' object is missing"], 200);
        }

        $request = $request->withAttribute('data', $parsedData['data']);
        
        return $next($request, $response);
    }
}
