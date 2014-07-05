<?php namespace Watson\Validating;

use \Illuminate\Support\MessageBag;

interface ValidatingInterface {

    /**
     * Boot the trait. Adds an observer class for validating.
     *
     * @return void
     */
    public static function bootValidatingTrait();

    /**
     * Returns whether or not the model will attempt to validate
     * itself when saving.
     *
     * @return bool
     */
    public function getValidating();

    /**
     * Set whether the model should attempt validation on saving.
     *
     * @param  bool $value
     * @return void
     */
    public function setValidating($value);

    /**
     * Returns whether the model will raise an exception or
     * return a boolean when validating.
     *
     * @return bool
     */
    public function getThrowValidationExceptions();

    /**
     * Set whether the model should raise an exception or
     * return a boolean on a failed validation.
     *
     * @param  bool $value
     * @return void
     */
    public function setThrowValidationExceptions($value);

    /**
     * Returns whether or not the model will add it's unique
     * identifier to the rules when validating.
     *
     * @return bool
     */
    public function getInjectUniqueIdentifier();

    /**
     * Set the model to add unique identifier to rules when performing
     * validation.
     *
     * @param  bool $value
     * @return void
     */
    public function setInjectUniqueIdentifier($value);

    /**
     * Get the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel();

    /**
     * Get the global validation rules.
     *
     * @return array
     */
    public function getRules();

    /**
     * Get the default ruleset for any event. Will first search to see if a
     * 'saving' ruleset exists, fallback to '$rules' and otherwise return
     * an empty array
     *
     * @param  string $ruleset
     * @return array
     */
    public function getDefaultRules($ruleset = null);

    /**
     * Set the global validation rules.
     *
     * @param  array $rules
     * @return void
     */
    public function setRules(array $rules = null);

    /**
     * Get all the rulesets.
     *
     * @return array
     */
    public function getRulesets();

    /**
     * Set all the rulesets.
     *
     * @param  array $rulesets
     * @return void
     */
    public function setRulesets(array $rulesets = null);

    /**
     * Get a ruleset.
     *
     * @param  string $ruleset
     * @return array
     */
    public function getRuleset($ruleset);

    /**
     * Set the rules used for a particular ruleset.
     *
     * @param  array  $rules
     * @param  string $ruleset
     * @return void
     */
    public function setRuleset(array $rules, $ruleset);

    /**
     * Get the custom validation messages being used by the model.
     *
     * @return array
     */
    public function getMessages();

    /**
     * Set the validation messages to be used by the validator.
     *
     * @param  array $messages
     * @return void
     */
    public function setMessages(array $messages);

    /**
     * Get the validation error messages from the model.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors();

    /**
     * Set the error messages.
     *
     * @param  \Illuminate\Support\MessageBag $errors
     * @return void
     */
    public function setErrors(MessageBag $errors);

    /**
     * Returns whether the model is valid or not.
     *
     * @param  string $ruleset
     * @return bool
     */
    public function isValid($ruleset = null);

    /**
     * Returns if the model is valid, otherwise throws an exception.
     *
     * @param  string $ruleset
     * @return bool
     * @throws \Watson\Validating\ValidationException
     */
    public function isValidOrFail($ruleset = null);

    /**
     * Returns whether the model is invalid or not.
     *
     * @param  string $ruleset
     * @return bool
     */
    public function isInvalid($ruleset = null);

    /**
     * Returns if the model is invalid, otherwise throws an exception.
     *
     * @param  string $ruleset
     * @return bool
     * @throws \Watson\Validating\ValidationException
     */
    public function isInvalidOrFail($ruleset = null);

    /**
     * Force the model to be saved without undergoing validation.
     *
     * @return bool
     */
    public function forceSave();

    /**
     * Perform a one-off save that will raise an exception on validation error
     * instead of returning a boolean (which is the default behaviour).
     *
     * @return void
     * @throws \Watson\Validating\ValidatingException
     */
    public function saveOrFail();

    /**
     * Perform a one-off save that will return a boolean on
     * validation error instead of raising an exception.
     *
     * @return bool
     */
    public function saveOrReturn();

    /**
     * Make a Validator instance for a given ruleset.
     *
     * @param  string $ruleset
     * @return \Illuminate\Validation\Factory
     */
    function makeValidator($rules = []);

    /**
     * Validate the model against it's rules, returning whether
     * or not it passes and setting the error messages on the
     * model if required.
     *
     * @param  string $ruleset
     * @return bool
     * @throws ValidationException
     */
    function performValidation($ruleset = null);

    /**
     * Update the unique rules of the global rules to
     * include the model identifier.
     *
     * @return void
     */
    public function updateRulesUniques();

    /**
     * Update the unique rules of the given ruleset to
     * include the model identifier.
     *
     * @param  string $ruleset
     * @return void
     */
    public function updateRulesetUniques($ruleset = null);

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
    function injectUniqueIdentifierToRules(array $rules);

    /**
     * Take a unique rule, add the database table, column and
     * model identifier if required.
     *
     * @param  string $rule
     * @param  string $field
     * @return string
     */
    function prepareUniqueRule($rule, $field);

}
