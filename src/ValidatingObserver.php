<?php namespace Watson\Validating;

use \Illuminate\Database\Eloquent\Model;
use \Watson\Validating\ValidationException;

class ValidatingObserver {

    /**
     * Register the validation event for creating the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function creating(Model $model)
    {
        return $this->performValidation($model, 'creating');
    }

    /**
     * Register the validation event for updating the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function updating(Model $model)
    {
        return $this->performValidation($model, 'updating');
    }

    /**
     * Register the validation event for saving the model. Saving validation
     * should only occur if creating and updating validation does not.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function saving(Model $model)
    {
        if ( ! $model->getRuleset('creating') && ! $model->getRuleset('updating'))
        {
            return $this->performValidation($model, 'saving');
        }
    }

    /**
     * Register the validation event for deleting the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function deleting(Model $model)
    {
        return $this->performValidation($model, 'deleting');
    }

    /**
     * Register the validation event for restoring the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function restoring(Model $model)
    {
        return $this->performValidation($model, 'restoring');
    }

    /**
     * Perform validation with the specified ruleset.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  string $event
     * @return boolean
     */
    protected function performValidation(Model $model, $event)
    {
        // If the model has validating enabled, perform it.
        if ($model->getValidating() && $model->isValid($event) === false)
        {
            if ($model->getThrowValidationExceptions())
            {
                $exception = new ValidationException(get_class($model) . ' model could not be persisted as it failed validation.');

                $exception->setModel($model);
                $exception->setErrors($model->getErrors());

                throw $exception;
            }

            return false;
        }
    }

}
