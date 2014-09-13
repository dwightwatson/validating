<?php namespace Watson\Validating;

use \Illuminate\Support\MessageBag;
use \Illuminate\Support\Facades\Validator;
use \Illuminate\Validation\Factory;

trait ValidatingTrait {

    /**
     * Error messages as provided by the validator.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $validationErrors;

    /**
     * Whether the model should undergo validation
     * when saving or not.
     *
     * @var bool
     */
    protected $validating = true;

    /**
     * The Validator factory class used for validation.
     *
     * @return \Illuminate\Validation\Factory
     */
    protected $validator;

    /**
     * Boot the trait. Adds an observer class for validating.
     *
     * @return void
     */
    public static function bootValidatingTrait()
    {
        static::observe(new ValidatingObserver);
    }

    /**
     * Returns whether or not the model will attempt to validate
     * itself when saving.
     *
     * @return bool
     */
    public function getValidating()
    {
        return $this->validating;
    }

     /**
     * Set whether the model should attempt validation on saving.
     *
     * @param  bool $value
     * @return void
     */
    public function setValidating($value)
    {
        $this->validating = (boolean) $value;
    }

    /**
     * Returns whether the model will raise an exception or
     * return a boolean when validating.
     *
     * @return bool
     */
    public function getThrowValidationExceptions()
    {
        return isset($this->throwValidationExceptions) ? $this->throwValidationExceptions : false;
    }

    /**
     * Set whether the model should raise an exception or
     * return a boolean on a failed validation.
     *
     * @param  bool $value
     * @return void
     * @throws InvalidArgumentException
     */
    public function setThrowValidationExceptions($value)
    {
        $this->throwValidationExceptions = (boolean) $value;
    }

    /**
     * Returns whether or not the model will add it's unique
     * identifier to the rules when validating.
     *
     * @return bool
     */
    public function getInjectUniqueIdentifier()
    {
        return isset($this->injectUniqueIdentifier) ? $this->injectUniqueIdentifier : true;
    }

    /**
     * Set the model to add unique identifier to rules when performing
     * validation.
     *
     * @param  bool $value
     * @return void
     * @throws InvalidArgumentException
     */
    public function setInjectUniqueIdentifier($value)
    {
        $this->injectUniqueIdentifier = (boolean) $value;
    }

    /**
     * Get the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
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
     * @param  array $rules
     * @return void
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Get all the rulesets.
     *
     * @return array
     */
    public function getRulesets()
    {
        return $this->rulesets ?: [];
    }

    /**
     * Get a ruleset, and merge it with saving if required.
     *
     * @param  string $ruleset
     * @param  bool   $mergeWithSaving
     * @return array
     */
    public function getRuleset($ruleset, $mergeWithSaving = false)
    {
        $rulesets = $this->getRulesets();

        if (array_key_exists($ruleset, $rulesets))
        {
            if ($mergeWithSaving)
            {
                return $this->mergeRulesets(['saving', $ruleset]);
            }

            return $rulesets[$ruleset];
        }
    }

    /**
     * Set the rules used for a particular ruleset.
     *
     * @param  array  $rules
     * @param  string $ruleset
     * @return void
     */
    public function setRuleset(array $rules, $ruleset)
    {
        $this->rulesets[$ruleset] = $rules;
    }

    /**
     * Helper method to merge rulesets, with later rules overwriting
     * earlier ones
     *
     * @param  array $keys
     * @return array
     */
    public function mergeRulesets($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $rulesets = [];

        foreach ($keys as $key)
        {
            $rulesets[] = $this->getRuleset($key) ?: [];
        }

        return array_filter(call_user_func_array('array_merge', $rulesets));
    }

    /**
     * Get the custom validation messages being used by the model.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->validationMessages ?: [];
    }

    /**
     * Set the validation messages to be used by the validator.
     *
     * @param  array $messages
     * @return void
     */
    public function setMessages(array $messages)
    {
        $this->validationMessages = $messages;
    }

    /**
     * Get the validation error messages from the model.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->validationErrors ?: new MessageBag;
    }

    /**
     * Set the error messages.
     *
     * @param  \Illuminate\Support\MessageBag $validationErrors
     * @return void
     */
    public function setErrors(MessageBag $validationErrors)
    {
        $this->validationErrors = $validationErrors;
    }

    /**
     * Returns whether the model is valid or not.
     *
     * @param  mixed $ruleset
     * @param  bool  $mergeWithSaving
     * @return bool
     */
    public function isValid($ruleset = null, $mergeWithSaving = true)
    {
        $rules = $this->getRuleset($ruleset, $mergeWithSaving) ?: $this->getRules();

        return $this->performValidation($rules);
    }

    /**
     * Returns if the model is valid, otherwise throws an exception.
     *
     * @param  string $ruleset
     * @return bool
     * @throws \Watson\Validating\ValidationException
     */
    public function isValidOrFail($ruleset = null)
    {
        if ( ! $this->isValid($ruleset))
        {
            $this->throwValidationException();
        }

        return true;
    }

    /**
     * Returns whether the model is invalid or not.
     *
     * @param  string $ruleset
     * @param  bool   $mergeWithSaving
     * @return bool
     */
    public function isInvalid($ruleset = null, $mergeWithSaving = true)
    {
        return ! $this->isValid($ruleset, $mergeWithSaving);
    }

    /**
     * Returns if the model is invalid, otherwise throws an exception.
     *
     * @param  string $ruleset
     * @return bool
     * @throws \Watson\Validating\ValidationException
     */
    public function isInvalidOrFail($ruleset = null)
    {
        if ( ! $this->isInvalid($ruleset))
        {
            $this->throwValidationException();
        }

        return true;
    }

    /**
     * Force the model to be saved without undergoing validation.
     *
     * @return bool
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
     * @throws \Watson\Validating\ValidatingException
     */
    public function saveOrFail()
    {
        if ( ! $this->getModel()->save())
        {
            $this->throwValidationException();
        }
    }

    /**
     * Perform a one-off save that will return a boolean on
     * validation error instead of raising an exception.
     *
     * @return bool
     */
    public function saveOrReturn()
    {
        return $this->getModel()->save();
    }

    /**
     * Get the Validator instance
     *
     * @return \Illuminate\Validation\Factory
     */
    public function getValidator()
    {
        return $this->validator ?: Validator::getFacadeRoot();
    }

    /**
     * Set the Validator instance
     *
     * @param \Illuminate\Validation\Factory $validator
     */
    public function setValidator(Factory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Make a Validator instance for a given ruleset.
     *
     * @param  array $rules
     * @return \Illuminate\Validation\Factory
     */
    protected function makeValidator($rules = [])
    {
        // Get the model attributes.
        $attributes = $this->getModel()->getAttributes();

        if ($this->exists && $this->getInjectUniqueIdentifier())
        {
            $rules = $this->injectUniqueIdentifierToRules($rules);
        }

        // Get the custom validation messages.
        $messages = $this->getMessages();

        return $this->getValidator()->make($attributes, $rules, $messages);
    }

    /**
     * Validate the model against it's rules, returning whether
     * or not it passes and setting the error messages on the
     * model if required.
     *
     * @param  array $rules
     * @return bool
     * @throws \Watson\Validating\ValidationException
     */
    protected function performValidation($rules = [])
    {
        $validation = $this->makeValidator($rules);

        $this->setErrors($validation->messages());

        return $validation->passes();
    }

    /**
     * Throw a validation exception.
     *
     * @throws \Watson\Validating\ValidationException
     */
    public function throwValidationException()
    {
        $exception = new ValidationException(get_class($this) . ' model could not be persisted as it failed validation.');

        $exception->setModel($this);
        $exception->setErrors($this->getErrors());

        throw $exception;
    }

    /**
     * Update the unique rules of the global rules to
     * include the model identifier.
     *
     * @return void
     */
    public function updateRulesUniques()
    {
        $rules = $this->getRules();

        $this->setRules($this->injectUniqueIdentifierToRules($rules));
    }

   /**
     * Update the unique rules of the given ruleset to
     * include the model identifier.
     *
     * @param  string $ruleset
     * @return void
     */
    public function updateRulesetUniques($ruleset = null)
    {
        $rules = $this->getRuleset($ruleset);

        $this->setRuleset($ruleset, $this->injectUniqueIdentifierToRules($rules));
    }

    /**
     * If the model already exists and it has unique validations
     * it is going to fail validation unless we also pass it's
     * primary key to the rule so that it may be ignored.
     *
     * This will go through all the rules and append the model's
     * primary key to the unique rules so that the validation
     * will work as expected.
     *
     * @param  array $rules
     * @return array
     */
    protected function injectUniqueIdentifierToRules(array $rules)
    {
        foreach ($rules as $field => &$ruleset)
        {
            // If the ruleset is a pipe-delimited string, convert it to an array.
            $ruleset = is_string($ruleset) ? explode('|', $ruleset) : $ruleset;

            foreach ($ruleset as &$rule)
            {
                if (starts_with($rule, 'unique'))
                {
                    $rule = $this->prepareUniqueRule($rule, $field);
                }
            }
        }

        return $rules;
    }

    /**
     * Take a unique rule, add the database table, column and
     * model identifier if required.
     *
     * @param  string $rule
     * @param  string $field
     * @return string
     */
    protected function prepareUniqueRule($rule, $field)
    {
        $parameters = explode(',', substr($rule, 7));

        // If the table name isn't set, get it.
        if ( ! isset($parameters[0]))
        {
            $parameters[0] = $this->getModel()->getTable();
        }

        // If the field name isn't set, infer it.
        if ( ! isset($parameters[1]))
        {
            $parameters[1] = $field;
        }

        // If the identifier isn't set, add it.
        if ( ! isset($parameters[2]))
        {
            $parameters[2] = $this->getModel()->getKey();
        }

        return 'unique:' . implode(',', $parameters);
    }

}
