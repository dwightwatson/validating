<?php namespace Watson\Validating;

use \Illuminate\Support\MessageBag;
use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Validator;
use \Illuminate\Validation\Factory;

interface ValidatingInterface {

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
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
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
     * @return array
     */
    public function getDefaultRules();

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
     * Get a ruleset, and merge it with saving if required.
     *
     * @param  string $ruleset
     * @param  bool   $mergeWithSaving
     * @return array
     */
    public function getRuleset($ruleset, $mergeWithSaving = false);

    /**
     * Set the rules used for a particular ruleset.
     *
     * @param  array  $rules
     * @param  string $ruleset
     * @return void
     */
    public function setRuleset(array $rules, $ruleset);

    /**
     * Add rules to the existing rules or ruleset, overriding any existing.
     *
     * @param  array   $rules
     * @param  string  $ruleset
     * @return void
     */
    public function addRules(array $rules, $ruleset = null);

    /**
     * Remove rules from the existing rules or ruleset.
     *
     * @param  mixed   $keys
     * @param  string  $ruleset
     * @return void
     */
    public function removeRules($keys, $ruleset = null);

    /**
     * Helper method to merge rulesets, with later rules overwriting
     * earlier ones
     *
     * @param  array $keys
     * @return array
     */
    public function mergeRulesets($keys);

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
     * @param  \Illuminate\Support\MessageBag $validationErrors
     * @return void
     */
    public function setErrors(MessageBag $validationErrors);

    /**
     * Returns whether the model is valid or not.
     *
     * @param  mixed $ruleset
     * @param  bool  $mergeWithSaving
     * @return bool
     */
    public function isValid($ruleset = null, $mergeWithSaving = true);

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
     * @param  bool   $mergeWithSaving
     * @return bool
     */
    public function isInvalid($ruleset = null, $mergeWithSaving = true);

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
     * Get the Validator instance
     *
     * @return \Illuminate\Validation\Factory
     */
    public function getValidator();

    /**
     * Set the Validator instance
     *
     * @param \Illuminate\Validation\Factory $validator
     */
    public function setValidator(Factory $validator);

    /**
     * Get all the confirmation attributes from the input.
     *
     * @return array
     */
    public function getConfirmationAttributes();

    /**
     * Throw a validation exception.
     *
     * @throws \Watson\Validating\ValidationException
     */
    public function throwValidationException();

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

}
