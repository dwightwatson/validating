<?php

namespace Watson\Validating;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Contracts\Validation\ValidationException as BaseValidationException;

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
     * @param  \Illuminate\Contracts\Support\MessageProvider  $provider
     * @param  \Illuminate\Database\Eloquent\Model            $model
     * @return void
     */
    public function __construct(MessageProvider $provider, Model $model = null)
    {
        parent::__construct($provider);

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
     * @return \Illuminate\Support\MessageBag
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
