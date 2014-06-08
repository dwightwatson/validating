<?php namespace Watson\Validating;

use Illuminate\Support\Facades\Validator;

trait ValidatingTrait
{
    public static function bootValidatingTrait()
    {
        static::observe(new ValidatingObserver);
    }

    /**
     * Error messages as provided by the validator.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /** 
     * Whether the model should undergo validation when
     * saving or not.
     *
     * @var boolean
     */
    protected $validating = true;

    /*
    |--------------------------------------------------------------------------
    | Configuration accessors and mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Returns whether or not the model will attempt to validate itself when
     * saving.
     *
     * @return boolean
     */
    public function getValidating()
    {
        return $this->validating;
    }

    /**
     * Set whether the model should attempt validation on saving.
     *
     * @param  boolean
     * @return void
     */
    protected function setValidating($value)
    {
        if ( ! is_bool($value)) return;

        $this->validating = $value;
    }

    /**
     * Returns whether the model will raise an exception or return a boolean
     * when validating.
     *
     * @return boolean
     */
    public function getThrowValidationExceptions()
    {
        return isset($this->throwValidationExceptions) ? $this->throwValidationExceptions : false;
    }

    /**
     * Set whether the model should raise an exception or return a boolean on
     * a failed validation.
     *
     * @param  boolean
     * @return void
     */
    public function setThrowValidationExceptions($value)
    {
        if ( ! is_bool($value)) return;

        $this->throwValidationExceptions = $value;
    }

    /**
     * Returns whether or not the model will add it's unique identifier to the
     * rules when validating.
     *
     * @return boolean
     */
    public function getInjectUniqueIdentifier()
    {
        return isset($this->injectUniqueIdentifier) ? $this->injectUniqueIdentifier : true;
    }

    /**
     * Set the model to add unique identifier to rules when performing 
     * validation.
     *
     * @param  boolean
     * @return void
     */
    public function setInjectUniqueIdentifier($value)
    {
        if ( ! is_bool($value)) return;

        $this->injectUniqueIdentifier = $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Instance accessors and mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get the model.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this;
    }

    /**
     * Get the global validation rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules ?: [];
    }

    /**
     * Set the global validation rules.
     *
     * @param  array
     * @return void
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
    }

    /**
     * Get a single ruleset if it exists.
     *
     * @param  string
     * @return mixed
     */
    public function getRuleset($ruleset)
    {
        if (array_key_exists($ruleset, $this->rules))
        {
            return $this->rules[$ruleset];
        }
    }

    /**
     * Set either the default rules for the model or add another ruleset
     * to the model..
     *
     * @param  array
     * @param  string
     * @return void
     */
    public function setRuleset($rules, $ruleset = 'saving')
    {
        $this->rules[$ruleset] = $rules;
    }

    /**
     * Get the custom validation messages being used by the model.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages ?: [];
    }

    /**
     * Set the validation messages to be used by the validator.
     *
     * @param  array
     * @return void
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * Get the validation error messages from the model.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Returns whether the model is valid or not.
     *
     * @return boolean
     */
    public function isValid($ruleset = 'saving')
    {
        return $this->validate($ruleset);
    }

    /**
     * Returns whether the model is invalid or not.
     *
     * @return boolean
     */
    public function isInvalid($ruleset = 'saving')
    {
        return ! $this->validate($ruleset);
    }

    /**
     * Force the model to be saved without undergoing
     * validation.
     *
     * @return boolean
     */
    public function forceSave()
    {
        $currentValidatingSetting = $this->getValidating();

        $this->setValidating(false);

        $result = $this->getModel()->save();

        $this->setValidating($currentValidatingSetting);

        return $result;
    }

    /**
     * Perform a one-off save that will raise an exception on validation error
     * instead of returning a boolean (which is the default behaviour).
     *
     * @return void
     */
    public function saveWithException()
    {
        $currentThrowValidationExceptionsSetting = $this->getThrowValidationExceptions();

        $this->setThrowValidationExceptions(true);

        $this->getModel()->save();

        $this->setThrowValidationExceptions($currentThrowValidationExceptionsSetting);
    }

    /**
     * Perform a one-off save that will return a boolean on validation error 
     * instead of raising an exception.
     *
     * @return boolean
     */
    public function saveWithoutException()
    {
        $currentThrowValidationExceptionsSetting = $this->getThrowValidationExceptions();

        $this->setThrowValidationExceptions(false);

        $result = $this->getModel()->save();

        $this->setThrowValidationExceptions($currentThrowValidationExceptionsSetting);

        return $result;
    }

    /**
     * Validate the model against it's rules, returning whether
     * or not it passes and setting the error messages on the model
     * if required.
     *
     * @return boolean
     */
    protected function validate($ruleset = null)
    {
        $rules = $this->getRuleset($ruleset) ?: $this->getRules();

        if ($this->exists && $this->injectUniqueIdentifier)
        {
            $rules = $this->injectUniqueIdentifierToRules($rules);
        }

        $messages = $this->getMessages();

        $validation = Validator::make($this->getAttributes(), $rules, $messages);

        if ($validation->passes()) return true;

        if ($this->getThrowValidationExceptions())
        {
            $exception = new ValidationException('Model failed validation');

            $exception->setErrors($validation->messages());

            throw $exception;
        }
        else
        {
            $this->errors = $validation->messages();

            return false;
        }
    }

    public function updateUniqueRules($ruleset = null)
    {
        $rules = $this->getRules($ruleset);

        $this->setRules($ruleset, $this->injectUniqueIdentifierToRules($rules));
    }

    /** 
     * If the model already exists and it has unique validations
     * it is going to fail validation unless we also pass it's 
     * primary key to the rule so that it may be ignored.
     *
     * This will go through all the rules and append the model's
     * primary key to the unique rules so that the validation will
     * work as expected.
     *
     * @return void
     */
    protected function injectUniqueIdentifierToRules($rules)
    {
        foreach ($rules as $field => &$ruleset)
        {
            // If the ruleset is a pipe-delimited string, convert it to an array.
            $ruleset = is_string($ruleset) ? explode('|', $ruleset) : $ruleset;

            foreach ($ruleset as &$rule)
            {
                if (strpos($rule, 'unique') === 0)
                {
                    $rule = $this->prepareUniqueRule($rule);
                }
            }
        }

        return $rules;
    }

    /**
     * Take a unique rule, add the database table, column and model identifier
     * if required.
     *
     * @param  string  $rule
     * @return string
     */
    protected function prepareUniqueRule($rule)
    {
        $parameters = explode(',', substr($rule, 7));

        // If the table name isn't set, get it.
        if ( ! isset($parameters[0]))
        {
            $parameters[0] = $this->getTable();
        }

        // If the field name isn't set, infer it.
        if ( ! isset($parameters[1]))
        {
            $parameters[1] = $field;
        }

        // If the identifier isn't set, add it.
        if ( ! isset($parameters[2]))
        {
            $parameters[2] = $this->getKey();
        }

        return 'unique:' . implode(',', $parameters);
    }
}
