<?php namespace Watson\Validating;

class ValidatingObserver
{
    public function creating($model)
    {
        return $this->performValidation($model, 'creating');
    }

    public function updating($model)
    {
        return $this->performValidation($model, 'updating');
    }

    public function saving($model)
    {
        return $this->performValidation($model, 'saving');
    }

    public function deleting($model)
    {
        if ($model->getRules('deleting'))
        {
            return $this->performValidation($model, 'deleting');
        }
    }

    /**
     * If the model has a ruleset for when the model is restoring,
     * run them.
     *
     * @param  object  $model
     * @return bool
    public function restoring($model)
    {
        if ($model->getRules('restoring'))
        {
            return $this->performValidation($model, 'restoring');   
        }
    }

    protected function performValidation($model, $event = null)
    {
        // If the model has validating enabled, perform it.
        if ($model->getValidating())
        {
            return $model->isValid($event);
        }
    }
}