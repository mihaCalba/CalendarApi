<?php

class EloquentConnection {

    public static function get($container)
    {
        $capsule = new \Illuminate\Database\Capsule\Manager;
        $capsule->addConnection($container['settings']['db']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        //-- for dev debug
        $capsule::connection()->enableQueryLog();

        return $capsule;
    }
}