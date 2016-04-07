<?php

use Watson\Validating\ValidationException;

class ValidationExceptionTest extends PHPUnit_Framework_TestCase
{
    public $messageBag;

    public $model;

    public $exception;

    public function setUp()
    {
        $this->messageBag = Mockery::mock('Illuminate\Support\MessageBag');

        $this->model = Mockery::mock('Illuminate\Database\Eloquent\Model');

        $this->exception = new ValidationException(
            $this->messageBag,
            $this->model
        );
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
       $this->messageBag->shouldReceive('getMessageBag')
           ->once()
           ->andReturn('errors');

       $this->assertEquals('errors', $this->exception->getErrors());
    }

    public function testGetsMessageBag()
    {
        $this->messageBag->shouldReceive('getMessageBag')
            ->once()
            ->andReturn('errors');

        $this->assertEquals('errors', $this->exception->getMessageBag());
    }
}
