<?php namespace Watson\Validating;

use \Illuminate\Database\Eloquent\Model as Eloquent;
use \Illuminate\Support\Contracts\MessageProviderInterface;

abstract class ValidatingModel extends Eloquent implements MessageProviderInterface, ValidatingInterface {

    /**
     * Make model validate attributes.
     *
     * @see \Watson\Validating\ValidatingTrait
     */
    use ValidatingTrait;

    /**
     * The default rules that the model will validate against.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * The rulesets that the model will validate against.
     *
     * @var array
     */
    protected $rulesets = [];

    /**
     * Get the messages for the instance.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getMessageBag()
    {
        return $this->getErrors();
    }

}
