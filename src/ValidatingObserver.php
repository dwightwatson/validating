<?php

namespace Watson\Validating;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;

class ValidatingObserver
{
    /**
     * Register the validation event for saving the model. Saving validation
     * should only occur if creating and updating validation does not.
     *
     * @return bool
     */
    public function saving(Model $model)
    {
        return $this->performValidation($model, 'saving');
    }

    /**
     * Register the validation event for restoring the model.
     *
     * @return bool
     */
    public function restoring(Model $model)
    {
        return $this->performValidation($model, 'restoring');
    }

    /**
     * Perform validation with the specified ruleset.
     *
     * @param  string  $event
     * @return bool
     */
    protected function performValidation(Model $model, $event)
    {
        // If the model has validating enabled, perform it.
        if ($model->getValidating()) {
            // Fire the namespaced validating event and prevent validation
            // if it returns a value.
            if ($this->fireValidatingEvent($model, $event) !== null) {
                return;
            }

            if ($model->isValid() === false) {
                // Fire the validating failed event.
                $this->fireValidatedEvent($model, 'failed');

                if ($model->getThrowValidationExceptions()) {
                    $model->throwValidationException();
                }

                return false;
            }
            // Fire the validating.passed event.
            $this->fireValidatedEvent($model, 'passed');
        } else {
            $this->fireValidatedEvent($model, 'skipped');
        }
    }

    /**
     * Fire the namespaced validating event.
     *
     * @param  string  $event
     * @return mixed
     */
    protected function fireValidatingEvent(Model $model, $event)
    {
        return Event::until('eloquent.validating: '.get_class($model), [$model, $event]);
    }

    /**
     * Fire the namespaced post-validation event.
     *
     * @param  string  $status
     * @return void
     */
    protected function fireValidatedEvent(Model $model, $status)
    {
        Event::dispatch('eloquent.validated: '.get_class($model), [$model, $status]);
    }
}
