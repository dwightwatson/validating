<?php namespace Watson\Validating;

use RuntimeException;
use Illuminate\Support\MessageBag;

class ValidationException extends RuntimeException
{
    /**
     * The validation errors.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * Get the validation errors.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set the validation errors.
     *
     * @param  \Illuminate\Support\MessageBag
     * @return void
     */
    public function setErrors(MessageBag $errors)
    {
        $this->errors = $errors;
    }
}