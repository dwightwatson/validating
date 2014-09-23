<?php namespace Watson\Validating;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Support\Facades\Event;
use \Watson\Validating\ValidationException;

class ValidatingObserver {

    /**
     * Register the validation event for saving the model. Saving validation
     * should only occur if creating and updating validation does not.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function saving(Model $model)
    {
        return $this->performValidation($model, 'saving');
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
        if ($model->getValidating())
        {
            // Fire the namespaced validating event and prevent validation
            // if it returns a value.
            if ($this->fireValidatingEvent($event, $model) !== null) return;

            if ($model->isValid() === false)
            {
                // Fire the validating.failed event.
                $this->fireValidatedEvent('failed', $model);

                if ($model->getThrowValidationExceptions())
                {
                    $model->throwValidationException();
                }

                return false;
            }
            // Fire the validating.passed event.
            $this->fireValidatedEvent('passed', $model);
        }
        else
        {
            $this->fireValidatedEvent('skipped', $model);
        }
    }

    /**
     * Fire the namespaced validating event.
     *
     * @param  string $event
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    protected function fireValidatingEvent($event, Model $model)
    {
        return Event::until("eloquent.validating.$event: ".get_class($model), $model);
    }

    /**
     * Fire the namespaced post-validation event.
     *
     * @param  string $event
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    protected function fireValidatedEvent($event, Model $model)
    {
        Event::fire("eloquent.validated: ".get_class($model), $model);
        Event::fire("eloquent.validated.$event: ".get_class($model), $model);
    }

}
