<?php

namespace app\validators;

use app\services\AuthService;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException as ValidationException;

use app\models\User as User;

class UserValidator extends BaseValidator
{
    const USER                = 'user';
    const USER_ID             = 'user_id';
    const FIRSTNAME           = 'firstname';
    const LASTNAME            = 'lastname';
    const EMAIL               = 'email';
    const PHONE               = 'phone';
    const PASSWORD            = 'password';

    public function forStore()
    {
        $this->rules[self::USER][self::FIRSTNAME]      = $this->setRule(v::alpha()->noWhitespace()->length(1, 125), true);
        $this->rules[self::USER][self::LASTNAME]       = $this->setRule(v::alpha()->noWhitespace()->length(1, 125), true);
        $this->rules[self::USER][self::EMAIL]          = $this->setRule(v::email()->noWhitespace()->length(1, 125), true);
        $this->rules[self::USER][self::PHONE]          = $this->setRule(v::alnum()->noWhitespace()->length(1, 125), true);
        $this->rules[self::USER][self::PASSWORD]       = $this->setRule(v::alnum()->noWhitespace()->length(1, 100), true);

        $this->customRules[self::USER][self::EMAIL]    = $this->setCustomRule('exists');

        return $this;
    }

    public function forShow()
    {
        $this->rules[self::USER_ID]       = $this->setRule(v::numeric()->noWhitespace()->length(1, 11));
        $this->customRules[self::USER_ID] = $this->setCustomRule('invalid');

        return $this;
    }

    public function forUpdate()
    {
        $this->rules[self::USER][self::FIRSTNAME] = $this->setRule(v::alpha()->noWhitespace()->length(1, 125));
        $this->rules[self::USER][self::LASTNAME]  = $this->setRule(v::alpha()->noWhitespace()->length(1, 125));
        $this->rules[self::USER][self::EMAIL]     = $this->setRule(v::email()->noWhitespace()->length(1, 125));
        $this->rules[self::USER][self::PHONE]     = $this->setRule(v::alnum()->noWhitespace()->length(1, 125));
        $this->rules[self::USER][self::PASSWORD]  = $this->setRule(v::alnum()->noWhitespace()->length(1, 100));

        $this->customRules[self::USER_ID]           = $this->setCustomRule('invalid|NotAuthUserId');
        $this->customRules[self::USER][self::EMAIL] = $this->setCustomRule('exists');

        return $this;
    }

    public function emailExists($inputName)
    {
        if ((new User)->where('email', '=', $this->data[self::USER][self::EMAIL])->exists())
        {
            $this->errors[$inputName] = "The email address is already registered in the system.";
        }
    }

    public function user_idInvalid($inputName)
    {
        if (! (new User)->where('id', '=', $this->data[self::USER_ID])->exists())
        {
            $this->errors[$inputName] = "Invalid user_id.";
        }
    }

    public function user_idNotAuthUserId($inputName)
    {
        if (AuthService::getAuthUserId() != $this->data[self::USER_ID])
        {
            $this->errors[$inputName] = "Invalid user_id.";
        }
    }
}
