<?php

namespace app\middlewares;

use app\services\AuthService;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as DB;
use app\models\User as User;
use app\models\Session as Session;

class AuthMiddleware extends BaseMiddleware
{
    /**
     * AuthMiddleware
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        if (empty($request->getHeader('token')))
        {
            return $response->withJson(['success' => false, 'exception' => 'Not Authenticated or invalid token.'], 200);
        }

        $session = (new Session())->where('token', '=', $request->getHeader('token'));

        //-- add auth verification here
        if ($session->exists())
        {
            $session = $session->get()->first();

            AuthService::setAuthInfo($session->user_id, $session->token);

            //-- attach user_id to the $request object
            $request = $request->withAttribute('user_id', $session->user_id);
            $request = $request->withAttribute('token', $session->token);

            return $next($request, $response);
        }

        // not authorized, don't call next middleware and return our own response
        return $response->withJson(['success' => false, 'exception' => 'Invalid action. Not authenticated.'], 200);
    }
}