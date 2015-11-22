<?php

use Illuminate\Support\Facades\Event;
use Watson\Validating\ValidatingObserver;

class ValidatingObserverTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->model = Mockery::mock('Illuminate\Database\Eloquent\Model');
        $this->observer = new ValidatingObserver;
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testPerformValidation()
    {
        $this->model->shouldReceive('getValidating')->once()->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        Event::shouldReceive('until')
            ->once();

        Event::shouldReceive('fire')
            ->once();

        $response = $this->observer->saving($this->model);
        $this->assertNotFalse($response);
    }

    public function testValidationStopsIfValidatingEventReturnsNonNull()
    {
        $this->model->shouldReceive('getValidating')->once()->andReturn(true);

        Event::shouldReceive('until')->once()->andReturn(false);

        $result = $this->observer->saving($this->model);

        $this->assertNull($result);
    }

    public function testPerformValidationReturnsFalse()
    {
        $this->model->shouldReceive('getValidating')->once()->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(false);

        $this->model->shouldReceive('getThrowValidationExceptions')
            ->once()
            ->andReturn(false);

        $response = $this->observer->saving($this->model);
        $this->assertFalse($response);
    }

    public function testPerformValidationThrowsException()
    {
        $this->model->shouldReceive('getValidating')->once()->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(false);

        $this->model->shouldReceive('getThrowValidationExceptions')
            ->once()
            ->andReturn(true);

        $this->model->shouldReceive('throwValidationException')
            ->once();

        $response = $this->observer->saving($this->model);
        $this->assertFalse($response);
    }

    public function testSavingPerformsValidation()
    {
        $this->model->shouldReceive('getValidating')->once()->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        Event::shouldReceive('until')
            ->once();

        Event::shouldReceive('fire')
            ->once();

        $this->observer->saving($this->model);
    }

    public function testRestoringPerformsValidation()
    {
        $this->model->shouldReceive('getValidating')->once()->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        Event::shouldReceive('until')
            ->once();

        Event::shouldReceive('fire')
            ->once();

        $this->observer->restoring($this->model);
    }

    public function testDisabledValidationFiresSkippedEvent()
    {
        $this->model->shouldReceive('getValidating')->once()->andReturn(false);

        Event::shouldReceive('fire')
            ->once();

        $this->observer->saving($this->model);
    }
}
