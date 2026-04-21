<?php

namespace Watson\Validating\Tests;

use Illuminate\Support\Facades\Event;
use Mockery;
use Watson\Validating\ValidatingObserver;

class ValidatingObserverTest extends TestCase
{
    protected $model;

    protected $observer;

    protected function setUp(): void
    {
        $this->model = Mockery::mock('Illuminate\Database\Eloquent\Model');
        $this->observer = new ValidatingObserver;
    }

    public function test_perform_validation()
    {
        $this->model->shouldReceive('getValidating')->once()->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        Event::shouldReceive('until')
            ->once();

        Event::shouldReceive('dispatch')
            ->once();

        $response = $this->observer->saving($this->model);
        $this->assertNotFalse($response);
    }

    public function test_validation_stops_if_validating_event_returns_non_null()
    {
        $this->model->shouldReceive('getValidating')->once()->andReturn(true);

        Event::shouldReceive('until')->once()->andReturn(false);

        $result = $this->observer->saving($this->model);

        $this->assertNull($result);
    }

    public function test_perform_validation_returns_false()
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

    public function test_perform_validation_throws_exception()
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

    public function test_saving_performs_validation()
    {
        $this->model->shouldReceive('getValidating')->once()->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        Event::shouldReceive('until')
            ->once();

        Event::shouldReceive('dispatch')
            ->once();

        $this->observer->saving($this->model);
    }

    public function test_restoring_performs_validation()
    {
        $this->model->shouldReceive('getValidating')->once()->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        Event::shouldReceive('until')
            ->once();

        Event::shouldReceive('dispatch')
            ->once();

        $this->observer->restoring($this->model);
    }

    public function test_disabled_validation_fires_skipped_event()
    {
        $this->model->shouldReceive('getValidating')->once()->andReturn(false);

        Event::shouldReceive('dispatch')
            ->once();

        $this->observer->saving($this->model);
    }
}
