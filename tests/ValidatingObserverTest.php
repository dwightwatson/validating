<?php

use \Illuminate\Support\Facades\Event;
use \Mockery;
use \Watson\Validating\ValidatingObserver;

class ValidatingObserverTest extends \PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->model = Mockery::mock('Illuminate\Database\Eloquent\Model');
        $this->observer = new ValidatingObserver;

        // Enable validation on mock
        $this->model->shouldReceive('getValidating')
            ->andReturn(true);

        Event::shouldReceive('until')
            ->once();

        Event::shouldReceive('fire')
            ->once();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testPerformValidation()
    {
        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        $response = $this->observer->saving($this->model);
        $this->assertNotFalse($response);
    }

    public function testPerformValidationReturnsFalse()
    {
        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(false);

        $this->model->shouldReceive('getThrowValidationExceptions')
            ->once()
            ->andReturn(false);

        $response = $this->observer->saving($this->model);
        $this->assertFalse($response);
    }

    public function testSavingPerformsValidation()
    {
        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        $this->observer->saving($this->model);
    }

    public function testRestoringPerformsValidation()
    {
        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        $this->observer->restoring($this->model);
    }

}
