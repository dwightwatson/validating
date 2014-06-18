<?php namespace Watson\Validating;

use RuntimeException;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Support\MessageBag;

class ValidationException extends RuntimeException implements MessageProviderInterface {

    /**
     * The model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

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
     * Get the messages for the instance.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getMessageBag()
    {
        return $this->getErrors();
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

    /**
     * Get the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model
     * @return void
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

}
