<?php

namespace app\validators;

use Respect\Validation\Validator as Validator;
use Respect\Validation\Exceptions\ValidationException as ValidationException;

use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Core class for Validation.
 *
 * Please don't change anything.
 *
 * Class BaseValidator
 * @package app\validators
 */
abstract class BaseValidator
{
    protected $rules       = [];
    protected $customRules = [];
    protected $errors      = [];
    protected $data        = [];

    protected function setRule($rule, $required = false)
    {
        return [
            'rule'     => $rule,
            'required' => $required
        ];
    }

    protected function setCustomRule($rule)
    {
        return [
            'rule' => $rule
        ];
    }

    public function validate($data)
    {
        $this->data = $data;

        $this->processValidation($this->rules, $this->data);

        if (! $this->hasErrors())
        {
            $this->applyCustomRules($this->customRules, $this->rules, $this->data);
        }

        return ! $this->hasErrors();
    }

    /**
     * Recursive method that traverses the rules and data arrays and applies the rules dynamically.
     * Please don't change anything.
     *
     * TODO - needs a fix when multiple rules are added as parent (e.g. $this->rules['user'] + $this->rules['vehicle'])
     * TODO - the recursion doesn't pick up the next element of this array and it's annoying
     *
     * @param $rules
     * @param $data
     * @return bool
     */
    private function processValidation($rules, $data)
    {
        foreach ($rules as $inputName => $validatorRules)
        {
            //-- not the final node, must go deeper
            if (! isset($validatorRules['rule']) && is_array($validatorRules))
            {
                return $this->processValidation($validatorRules, $data[$inputName]);
            }

            if (! isset($data[$inputName]) && $validatorRules['required'])
            {
                // empty and required
                $this->errors[$inputName] = "The field '{$inputName}' is required.";

                continue;
            }
            else if (empty($data[$inputName]) && ! $validatorRules['required'])
            {
                // empty but not required, no error, skip to the next one
                continue;
            }
            else
            {
                // not empty, validating
                $this->applyRule($validatorRules['rule'], $data[$inputName], $inputName);
            }
        }

        return $this->hasErrors();
    }

    private function applyRule(Validator $validator, $input, $inputName)
    {
        $isValid = false;

        try
        {
            $isValid = $validator->check($input);
        }
        catch(ValidationException $exception)
        {
            $exception->setName($inputName);
            $this->errors[$inputName] = $exception->getMainMessage();
        }

        return $isValid;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getErrorsAsString()
    {
        return implode(', ', $this->errors);
    }

    public function hasErrors()
    {
        return ! empty($this->errors);
    }

    /**
     * Recursive method that traverses the rules and data arrays and applies the rules dynamically.
     * Please don't change anything.
     *
     * @param $customRules
     * @param $data
     * @return bool
     */
    public function applyCustomRules($customRules, $validatorRules, $data)
    {
        if(! empty($customRules))
        {
            foreach($customRules as $inputName => $rules)
            {
                //-- not the final node, must go deeper
                if (! isset($rules['rule']) && is_array($rules))
                {
                    return $this->applyCustomRules($rules, $validatorRules[$inputName], $data[$inputName]);
                }

                if (! empty($validatorRules[$inputName]) && empty($data[$inputName]))
                {
                    //-- don't apply the custom rules for a field that has no value (might be optional)
                    continue;
                }

                $rules = explode('|', $rules['rule']);

                foreach($rules as $rule)
                {
                    $functionName = $inputName . ucfirst($rule);
                    $this->{$functionName}($inputName);
                }
            }
        }

        return $this->hasErrors();
    }
}