<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

date_default_timezone_set('Europe/Bucharest');
session_start();

require 'vendor/autoload.php';
require 'core/Config.php';
require 'core/Dependencies.php';
require 'core/EloquentConnection.php';

$app = new \Slim\App(['settings' => Config::get()]);

$container = $app->getContainer();
$container = Dependencies::set($container);
$capsule   = EloquentConnection::get($container);

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

/**
 * API
 */
$app->group('/api', function () use ($app, $container) {
    //users
    $app->post('/login', 'AuthController:login')
        ->add(new app\middlewares\DataMiddleware());
    $app->get('/logout', 'AuthController:logout');

    $app->post('/users', 'UserController:store')
        ->add(new app\middlewares\DataMiddleware());
    $app->get('/users', 'UserController:index');
    $app->get('/users/{user_id}', 'UserController:show');
    $app->patch('/users/{user_id}', 'UserController:update')
        ->add(new app\middlewares\AuthMiddleware())
        ->add(new app\middlewares\DataMiddleware());

    //events
    $app->post('/events', 'EventController:store')
        ->add(new app\middlewares\AuthMiddleware())
        ->add(new app\middlewares\DataMiddleware());
    $app->get('/events', 'EventController:index')
        ->add(new app\middlewares\AuthMiddleware());
    $app->get('/events/{event_id}', 'EventController:show')
        ->add(new app\middlewares\AuthMiddleware());
    $app->patch('/events/{event_id}', 'EventController:update')
        ->add(new app\middlewares\AuthMiddleware())
        ->add(new app\middlewares\DataMiddleware());
    $app->delete('/events/{event_id}', 'EventController:delete')
        ->add(new app\middlewares\AuthMiddleware());

})->add(function ($request, $response, $next) {
    $this->logger->addInfo('Request method: ' . $request->getMethod() . $request->getUri()->getPath());
    $this->logger->addInfo('Request parsed body: ' . json_encode($request->getParsedBody()));

    $receivedToken = $request->getHeader('Authorization');

    if (empty($receivedToken[0]) || $receivedToken[0] != Env::get('API_KEY'))
    {
        return $response->withJson(['success' => false, 'exception' => 'NOT ALLOWED'], 400);
    }

    return $next($request, $response);
});

$app->run();
