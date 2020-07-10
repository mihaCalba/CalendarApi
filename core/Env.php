<?php

class Env
{
    private static $env;

    public static function get($key)
    {
        self::instance();

        return getenv($key);
    }

    public static function instance()
    {
        if (is_null(self::$env))
        {
            $envPath = realpath(__DIR__ . '/../');

            self::$env = new Dotenv\Dotenv($envPath);
            self::$env->load();
        }

        return self::$env;
    }
}