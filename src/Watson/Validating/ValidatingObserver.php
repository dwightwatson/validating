<?php namespace Watson\Validating;

class ValidatingObserver
{
    /**
     * Register the validation event for creating the model.
     *
     * @param  Model  $model
     * @return bool
     */
    public function creating($model)
    {
        return $this->performValidation($model, 'creating');
    }

    /**
     * Register the validation event for updating the model.
     *
     * @param  Model  $model
     * @return bool
     */
    public function updating($model)
    {
        return $this->performValidation($model, 'updating');
    }

    /**
     * Register the validation event for saving the model. Saving validation
     * should only occur if creating and updating validation does not.
     *
     * @param  Model  $model
     * @return bool
     */
    public function saving($model)
    {
        if ( ! $model->getRuleset('creating') && ! $model->getRuleset('updating'))
        {
            return $this->performValidation($model, 'saving');            
        }
    }

    /**
     * Register the validation event for deleting the model.
     *
     * @param  Model  $model
     * @return bool
     */
    public function deleting($model)
    {
        return $this->performValidation($model, 'deleting');
    }

    /**
     * Perform validation with the specified ruleset.
     *
     * @param  object  $model
     * @param  string  $event
     * @return bool
     */
    protected function performValidation($model, $event)
    {
        // If the model has validating enabled, perform it.
        return ! ($model->getValidating() && $model->getRuleset($event) && $model->isValid($event) === false);
    }
}
