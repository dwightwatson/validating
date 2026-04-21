<?php

namespace Watson\Validating;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException as BaseValidationException;

class ValidationException extends BaseValidationException implements MessageProvider
{
    /**
     * The model with validation errors.
     *
     * @var Model
     */
    protected $model;

    /**
     * Create a new validation exception instance.
     *
     * @return void
     */
    public function __construct(Validator $validator, Model $model)
    {
        parent::__construct($validator);

        $this->model = $model;
    }

    /**
     * Get the model with validation errors.
     *
     * @return Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Get the mdoel with validation errors.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model();
    }

    /**
     * Get the validation errors.
     *
     * @return Messagebag
     */
    public function errors()
    {
        return $this->validator->errors();
    }

    /**
     * Get the validation errors.
     *
     * @return Messagebag
     */
    public function getErrors()
    {
        return $this->errors();
    }

    /**
     * Get the messages for the instance.
     *
     * @return Messagebag
     */
    public function getMessageBag()
    {
        return $this->errors();
    }
}
