<?php

namespace app\validators;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException as ValidationException;

use app\models\User as User;

class AuthValidator extends BaseValidator
{
    const EMAIL     = 'email';
    const PASSWORD  = 'password';
    const TOKEN     = 'token';

    public function forLogin()
    {
        $this->rules[self::EMAIL]     = $this->setRule(v::email()->noWhitespace()->length(1, 125), true);
        $this->rules[self::PASSWORD]  = $this->setRule(v::alnum()->noWhitespace()->length(1, 100), true);

        $this->customRules['credentials'] = $this->setCustomRule('invalid');

        return $this;
    }

    public function forUserActive()
    {
        $this->customRules['account'] = $this->setCustomRule('inactive');

        return $this;
    }

    public function credentialsInvalid($inputName)
    {
        $user = (new User)->where('email', '=', $this->data['email'])
                          ->where('password', '=', sha1($this->data['password']));

        if (! $user->exists())
        {
            $this->errors[$inputName] = "Invalid credentials. Login failed.";
        }
    }

    public function accountInactive($inputName)
    {
        $user = (new User)->where('email', '=', $this->data['email'])
            ->where('password', '=', sha1($this->data['password']))
            ->get()->first();

        if (! $user->isActive())
        {
            $this->errors[$inputName] = "User account not activated. Please enter the code to activate this account.";
        }
    }
}