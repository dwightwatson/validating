<?php

namespace Watson\Validating;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Factory;

interface ValidatingInterface
{
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
     * Get the casted model attributes.
     *
     * @return array
     */
    public function getModelAttributes();

    /**
     * Get the global validation rules.
     *
     * @return array
     */
    public function getRules();

    /**
     * Set the global validation rules.
     *
     * @param  array $rules
     * @return void
     */
    public function setRules(array $rules = null);

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
     * @return bool
     */
    public function isValid();

    /**
     * Returns if the model is valid, otherwise throws an exception.
     *
     * @return bool
     * @throws \Watson\Validating\ValidationException
     */
    public function isValidOrFail();

    /**
     * Returns whether the model is invalid or not.
     *
     * @return bool
     */
    public function isInvalid();

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
}
