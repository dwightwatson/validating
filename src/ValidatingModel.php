<?php namespace Watson\Validating;

use \Illuminate\Database\Eloquent\Model as Eloquent;

abstract class ValidatingModel extends Eloquent implements ValidatingInterface {

    /**
     * Make model validate attributes.
     *
     * @see \Watson\Validating\ValidatingTrait
     */
    use ValidatingTrait;

}
