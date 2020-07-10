<?php

namespace app\validators;

use app\services\AuthService;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException as ValidationException;

use app\models\Event as Event;

class EventValidator extends BaseValidator
{
    const EVENT       = 'event';
    const EVENT_ID    = 'event_id';
    const NAME        = 'name';
    const LOCATION    = 'location';
    const DESCRIPTION = 'description';
    const STATUS      = 'status';
    const FROM        = 'from';
    const TO          = 'to';

    public function forStore()
    {
        $this->rules[self::EVENT][self::NAME]        = $this->setRule(v::alnum()->length(1, 255), true);
        $this->rules[self::EVENT][self::LOCATION]    = $this->setRule(v::alnum()->length(1, 255), true);
        $this->rules[self::EVENT][self::DESCRIPTION] = $this->setRule(v::alnum(), true);
        $this->rules[self::EVENT][self::FROM]        = $this->setRule(v::date(), true);
        $this->rules[self::EVENT][self::TO]          = $this->setRule(v::date(), true);

        return $this;
    }

    public function forShow()
    {
        $this->rules[self::EVENT_ID]       = $this->setRule(v::numeric()->noWhitespace()->length(1, 11));
        $this->customRules[self::EVENT_ID] = $this->setCustomRule('invalid');

        return $this;
    }

    public function forUpdate()
    {
        $this->rules[self::EVENT][self::NAME]        = $this->setRule(v::alnum()->length(1, 255));
        $this->rules[self::EVENT][self::LOCATION]    = $this->setRule(v::alnum()->length(1, 255));
        $this->rules[self::EVENT][self::DESCRIPTION] = $this->setRule(v::alnum());
        $this->rules[self::EVENT][self::FROM]        = $this->setRule(v::date());
        $this->rules[self::EVENT][self::TO]          = $this->setRule(v::date());

        $this->customRules[self::EVENT_ID]           = $this->setCustomRule('invalid');

        return $this;
    }

    public function forDelete()
    {
        $this->rules[self::EVENT_ID]       = $this->setRule(v::numeric()->noWhitespace()->length(1, 11));
        $this->customRules[self::EVENT_ID] = $this->setCustomRule('invalid');

        return $this;
    }

    public function event_idInvalid($inputName)
    {
        if (! (new Event)->where('id', '=', $this->data[self::EVENT_ID])->exists())
        {
            $this->errors[$inputName] = "Invalid event_id.";
        }
    }
}
