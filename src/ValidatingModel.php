<?php namespace Watson\Validating;

use \Illuminate\Database\Eloquent\Model as Eloquent;

abstract class ValidatingModel extends Eloquent implements ValidatingInterface {

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

}
