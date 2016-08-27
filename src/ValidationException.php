<?php

namespace Watson\Validating;

use RuntimeException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\MessageProvider;

class ValidationException extends RuntimeException implements MessageProvider
{
    /**
     * The message provider implementation.
     *
     * @var \Illuminate\Contracts\Support\MessageProvider
     */
    protected $provider;

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
    public function __construct(MessageProvider $provider, Model $model)
    {
        $this->provider = $provider;
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
        return $this->provider->getMessageBag();
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

    /**
     * Get the validation error message provider.
     *
     * @return \Illuminate\Contracts\Support\MessageProvider
     */
    public function getMessageProvider()
    {
        return $this->provider;
    }
}
