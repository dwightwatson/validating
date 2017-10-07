<?php

namespace Watson\Validating;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Validation\ValidationException as BaseValidationException;

class ValidationException extends BaseValidationException implements MessageProvider
{
    /**
     * The model with validation errors.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new validation exception instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @param  \Illuminate\Database\Eloquent\Model         $model
     * @return void
     */
    public function __construct(Validator $validator, Model $model)
    {
        parent::__construct($validator);

        $this->model = $model;
    }

    /**
     * Get the mdoel with validation errors.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Get the mdoel with validation errors.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model();
    }

    /**
     * Get the validation errors.
     *
     * @return \Illuminate\Contracts\Support\Messagebag
     */
    public function errors()
    {
        return $this->validator->errors();
    }

    /**
     * Get the validation errors.
     *
     * @return \Illuminate\Contracts\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors();
    }

    /**
     * Get the messages for the instance.
     *
     * @return \Illuminate\Contracts\Support\MessageBag
     */
    public function getMessageBag()
    {
        return $this->errors();
    }
}
