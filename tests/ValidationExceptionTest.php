<?php

namespace Watson\Validating\Tests;

use Mockery;
use Illuminate\Support\MessageBag;
use Watson\Validating\ValidationException;

class ValidationExceptionTest extends TestCase
{
    public $validator;

    public $model;

    public $exception;

    public function setUp(): void
    {
        $translator = Mockery::mock('Illuminate\Contracts\Translation\Translator', [
            'get' => null,
        ]);

        $this->validator = Mockery::mock('Illuminate\Contracts\Validation\Validator', [
            'errors' => new MessageBag,
            'getTranslator' => $translator,
        ]);

        $this->model = Mockery::mock('Illuminate\Database\Eloquent\Model');

        $this->exception = new ValidationException($this->validator, $this->model);
    }

    public function testModel()
    {
        $this->assertEquals($this->model, $this->exception->model());
    }

    public function testGetModel()
    {
        $this->assertEquals($this->model, $this->exception->getModel());
    }

    public function testGetErrors()
    {
       $this->validator->shouldReceive('errors')
           ->once()
           ->andReturn('errors');

       $this->assertEquals('errors', $this->exception->getErrors());
    }

    public function testGetsMessageBag()
    {
        $this->validator->shouldReceive('errors')
            ->once()
            ->andReturn('errors');

        $this->assertEquals('errors', $this->exception->getMessageBag());
    }
}
