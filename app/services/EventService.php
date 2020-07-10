<?php

namespace app\services;

use app\models\Event;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as DB;
use \Monolog\Logger as Logger;
use app\models\Session as Session;

class EventService
{
    const STATUS_NEW    = 'NEW';
    const STATUS_OPEN   = 'OPEN';
    const STATUS_CLOSED = 'CLOSED';

    public static function add($data)
    {
        $event              = new Event();
        $event->name        = $data['event']['name'];
        $event->location    = $data['event']['location'];
        $event->description = $data['event']['description'];
        $event->status      = self::STATUS_NEW;
        $event->from        = $data['event']['from'];
        $event->to          = $data['event']['to'];
        $event->save();

        return $event;
    }

    public static function update($data, $eventId)
    {
        $event = (new Event())->where('id', '=', $eventId)->get()->first();

        foreach ((new Event)->getFillableFields() as $field)
        {
            if (! empty($data['event'][$field]))
            {
                $event->{$field} = $data['event'][$field];
            }
        }

        $event->save();

        return $event;
    }

    public static function delete($eventId)
    {
        $event = (new Event)->where('id', '=', $eventId)->get()->first();

        return $event->delete();
    }
}