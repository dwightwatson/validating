<?php

namespace Watson\Validating;

use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\MessageBag;

abstract class ValidatingModel extends Eloquent implements MessageProvider, ValidatingInterface
{
    /**
     * Make model validate attributes.
     *
     * @see ValidatingTrait
     */
    use ValidatingTrait;

    /**
     * The default rules that the model will validate against.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Get the messages for the instance.
     *
     * @return MessageBag
     */
    public function getMessageBag()
    {
        return $this->getErrors();
    }
}
