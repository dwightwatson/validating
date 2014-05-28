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

    /**
     * Whether the model should inject it's identifier to the unique
     * validation rules before attempting validation.
     *
     * @var boolean
     */
    protected $injectIdentifier = true;

    /**
     * Get the validation rules being used against the model.
     *
     * @param  string
     * @return array
     */
    public function getRules($ruleset = 'saving')
    {
        // If a ruleset name is given, return that ruleset.
        if (array_key_exists($ruleset, $this->rules))
        {
            return $this->rules[$ruleset];
        }

        // If the specified ruleset does not exist, there may only 
        // be one default ruleset.
        return $this->rules ?: [];
    }

    /**
     * Set either the default rules for the model or add another ruleset
     * to the model..
     *
     * @param  array
     * @param  string
     * @return void
     */
    public function setRules($rules, $ruleset = 'saving')
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
        return $this->messages;
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
     * Returns whether the model will add it's unique identifier 
     * to the rules when validating.
     *
     * @return boolean
     */
    public function getInjectIdentifier()
    {
        return $this->injectIdentifier;
    }

    /**
     * Tell the model to add unique identifier to rules when
     * performing validation.
     *
     * @param  boolean
     * @return void
     */
    public function setInjectIdentifier($value)
    {
        if ( ! is_bool($value)) return;

        $this->injectIdentifier = $value;
    }

    /**
     * Returns whether the model will attempt to validate itself 
     * when saving or not.
     *
     * @return boolean
     */
    public function getValidating()
    {
        return $this->validating;
    }

    /**
     * Tell lthe model whether to attempt validation upon saving or
     * not.
     *
     * @param  boolean
     * @return void
     */
    public function setValidating($value)
    {
        if ( ! is_bool($value)) return;

        $this->validating = $value;
    }

    /**
     * Returns whether the model is valid or not.
     *
     * @return boolean
     */
    public function isValid($ruleset = null)
    {
        return $this->validate($ruleset);
    }

    /**
     * Returns whether the model is invalid or not.
     *
     * @return boolean
     */
    public function isInvalid($ruleet = null)
    {
        return ! $this->validate($ruleset = null);
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

        $result = $this->save();

        $this->setValidating($currentValidatingSetting);

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
        $rules = $this->getRules($ruleset);

        if ($this->exists && $this->injectIdentifier)
        {
            $rules = $this->injectUniqueIdentifier($rules);
        }

        $messages = $this->getMessages();

        $validation = Validator::make($this->getAttributes(), $rules, $messages);

        if ($validation->passes()) return true;

        $this->errors = $validation->messages();

        return false;
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
    protected function injectUniqueIdentifier($rules)
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
