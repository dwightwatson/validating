<?php

use \Mockery;
use \Watson\Validating\ValidationException;

class ValidationExceptionTest extends \PHPUnit_Framework_TestCase {
    public $exception;

    public function setUp()
    {
        $this->exception = new ValidationException;
    }

    public function testGetsErrors()
    {
        $this->assertNull($this->exception->getErrors());
    }

    public function testGetsMessageBag()
    {
        $messageBagMock = Mockery::mock('Illuminate\Support\MessageBag');

        $this->exception->setErrors($messageBagMock);

        $this->assertEquals($messageBagMock, $this->exception->getMessageBag());
    }

    public function testSetsErrors()
    {
        $messageBagMock = Mockery::mock('Illuminate\Support\MessageBag');

        $this->exception->setErrors($messageBagMock);

        $this->assertEquals($messageBagMock, $this->exception->getErrors());
    }


    public function testGetsModel()
    {
        $this->assertNull($this->exception->getModel());
    }

    public function testSetsModel()
    {
        $modelMock = Mockery::mock('Illuminate\Database\Eloquent\Model');

        $this->exception->setModel($modelMock);

        $this->assertEquals($modelMock, $this->exception->getModel());
    }

}
