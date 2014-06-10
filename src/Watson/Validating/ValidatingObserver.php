<?php namespace Watson\Validating;

use Illuminate\Support\Facades\Event;

class ValidatingObserver
{
    /**
     * Register the validation event for creating the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function creating($model)
    {
        if (! $this->fire('creating', $model))
        {
            return false;
        }

        return $this->performValidation($model, 'creating');
    }

    /**
     * Register the validation event for updating the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function updating($model)
    {
        if (! $this->fire('updating', $model))
        {
            return false;
        }

        return $this->performValidation($model, 'updating');
    }

    /**
     * Register the validation event for saving the model. Saving validation
     * should only occur if creating and updating validation does not.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function saving($model)
    {
        if (! $this->fire('saving', $model))
        {
            return false;
        }

        if ( ! $model->getRuleset('creating') && ! $model->getRuleset('updating'))
        {
            return $this->performValidation($model, 'saving');            
        }
    }

    /**
     * Register the validation event for deleting the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function deleting($model)
    {
        if (! $this->fire('deleting', $model))
        {
            return false;
        }

        return $this->performValidation($model, 'deleting');
    }

    /**
     * Call the event dispatcher.
     * 
     * @param 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    protected function fire($event, $model)
    {
        return Event::fire('validate:' . $event, [$model]) === false ? false : true;
    }

    /**
     * Perform validation with the specified ruleset.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  string  $event
     * @return bool
     */
    protected function performValidation($model, $event)
    {
        // If the model has validating enabled, perform it.
        if ($model->getValidating() && $model->getRuleset($event))
        {
            return $model->isValid($event);
        }
    }
}