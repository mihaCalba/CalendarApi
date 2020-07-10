<?php

class Dependencies
{
    public static function set($container)
    {
        $container['logger'] = function($c) {
            $logger = new \Monolog\Logger('logger');
            $file_handler = new \Monolog\Handler\StreamHandler('logs/app'. date('Y-m-d') .'.log');
            $logger->pushHandler($file_handler);
            return $logger;
        };

        // Register component on container
        $container['view'] = function ($container) {
            $view = new \Slim\Views\Twig('public/views/', [
                'cache' => false /*'public/views_cache/'*/,
                'debug' => true
            ]);

            // Instantiate and add Slim specific extension
            $router = $container->get('router');
            $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
            $view->addExtension(new Slim\Views\TwigExtension($router, $uri));
            $view->addExtension(new Twig_Extension_Debug());
            $view->addExtension(new Knlv\Slim\Views\TwigMessages(
                new Slim\Flash\Messages()
            ));

            return $view;
        };

        // Register globally to app
        $container['session'] = function ($c) {
            return new \SlimSession\Helper;
        };

        // Register provider
        $container['flash'] = function () {
            return new \Slim\Flash\Messages();
        };


        /**
         *  API
         */
         //VALIDATORS
        $container['UserValidator'] = function ($c) {
            return new app\validators\UserValidator();
        };

        $container['EventValidator'] = function ($c) {
            return new app\validators\EventValidator();
        };

        $container['AuthValidator'] = function ($c) {
            return new app\validators\AuthValidator();
        };

        //CONTROLLERS
        $container['UserController'] = function ($c) {
            return new app\controllers\UserController($c['logger'], $c['UserValidator']);
        };

        $container['EventController'] = function ($c) {
            return new app\controllers\EventController($c['logger'], $c['EventValidator']);
        };

        $container['AuthController'] = function ($c) {
            return new app\controllers\AuthController($c['logger'], $c['AuthValidator']);
        };

        return $container;
    }
}
