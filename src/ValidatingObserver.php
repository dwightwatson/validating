<?php namespace Watson\Validating;

use Illuminate\Database\Eloquent\Model;

class ValidatingObserver
{
    /**
     * Register the validation event for creating the model.
     *
     * @param  Model  $model
     * @return bool
     */
    public function creating(Model $model)
    {
        return $this->performValidation($model, 'creating');
    }

    /**
     * Register the validation event for updating the model.
     *
     * @param  Model  $model
     * @return bool
     */
    public function updating(Model $model)
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
     * @param  Model  $model
     * @return bool
     */
    public function deleting(Model $model)
    {
        return $this->performValidation($model, 'deleting');
    }

    /**
     * Perform validation with the specified ruleset.
     *
     * @param  Model   $model
     * @param  string  $event
     * @return bool
     */
    protected function performValidation(Model $model, $event)
    {
        // If the model has validating enabled, perform it.
        if ($model->getValidating())
        {
            if ($model->getRuleset($event) || $model->getRules())
            {
                if ($model->isValid($event) === false) return false;
            }
        }
    }
}
