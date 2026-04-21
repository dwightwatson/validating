<?php

namespace Watson\Validating\Tests;

use Illuminate\Support\MessageBag;
use Mockery;
use Watson\Validating\ValidationException;

class ValidationExceptionTest extends TestCase
{
    public $validator;

    public $model;

    public $exception;

    protected function setUp(): void
    {
        $translator = Mockery::mock('Illuminate\Contracts\Translation\Translator', [
            'get' => 'The given data was invalid.',
        ]);

        $this->validator = Mockery::mock('Illuminate\Contracts\Validation\Validator', [
            'errors' => new MessageBag,
            'getTranslator' => $translator,
        ]);

        $this->model = Mockery::mock('Illuminate\Database\Eloquent\Model');

        $this->exception = new ValidationException($this->validator, $this->model);
    }

    public function test_model()
    {
        $this->assertEquals($this->model, $this->exception->model());
    }

    public function test_get_model()
    {
        $this->assertEquals($this->model, $this->exception->getModel());
    }

    public function test_get_errors()
    {
        $this->validator->shouldReceive('errors')
            ->once()
            ->andReturn('errors');

        $this->assertEquals('errors', $this->exception->getErrors());
    }

    public function test_gets_message_bag()
    {
        $this->validator->shouldReceive('errors')
            ->once()
            ->andReturn('errors');

        $this->assertEquals('errors', $this->exception->getMessageBag());
    }
}
