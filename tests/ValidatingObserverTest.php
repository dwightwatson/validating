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
        $this->model->shouldReceive('getRuleset')
            ->with('creating')
            ->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        $response = $this->observer->creating($this->model);
        $this->assertNotFalse($response);
    }

    public function testPerformValidationReturnsFalse()
    {
        $this->model->shouldReceive('getRuleset')
            ->with('creating')
            ->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(false);

        $this->model->shouldReceive('getThrowValidationExceptions')
            ->once()
            ->andReturn(false);

        $response = $this->observer->creating($this->model);
        $this->assertFalse($response);
    }

    public function testCreatingPerformsValidation()
    {
        $this->model->shouldReceive('getRuleset')
            ->with('creating')
            ->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->with('creating')
            ->andReturn(true);

        $this->observer->creating($this->model);
    }

    public function testUpdatingPerformsValidation()
    {
        $this->model->shouldReceive('getRuleset')
            ->with('updating')
            ->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->with('updating')
            ->andReturn(true);

        $this->observer->updating($this->model);
    }

    public function testSavingPerformsValidation()
    {
        $this->model->shouldReceive('getRuleset')
            ->with('creating')
            ->andReturn(null);

        $this->model->shouldReceive('getRuleset')
            ->with('updating')
            ->andReturn(null);

        $this->model->shouldReceive('isValid')
            ->with('saving')
            ->andReturn(true);

        $this->observer->saving($this->model);
    }

    public function testSavingWithCreatingRulesDoesNotPerformValidation()
    {
        $this->model->shouldReceive('getRuleset')
            ->with('creating')
            ->andReturn(['foo' => 'bar']);

        $this->model->shouldReceive('getRuleset')
            ->with('updating')
            ->andReturn(['foo' => 'bar']);

        $this->observer->saving($this->model);
    }

    public function testSavingWithUpdatingRulesDoesNotPerformValidation()
    {
        $this->model->shouldReceive('getRuleset')
            ->with('creating')
            ->andReturn(null);

        $this->model->shouldReceive('getRuleset')
            ->with('updating')
            ->andReturn(['foo' => 'bar']);

        $this->observer->saving($this->model);
    }

    public function testSavingWithRulesDoesNotPerformValidation()
    {
        $this->model->shouldReceive('getRuleset')
            ->with('creating')
            ->andReturn(['foo' => 'bar']);

        $this->model->shouldReceive('getRuleset')
            ->with('updating')
            ->andReturn(['foo' => 'bar']);

        $this->observer->saving($this->model);
    }

    public function testDeletingPerformsValidation()
    {
        $this->model->shouldReceive('getRuleset')
            ->with('deleting')
            ->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->with('deleting')
            ->andReturn(true);

        $this->observer->deleting($this->model);
    }

    public function testRestoringPerformsValidation()
    {
        $this->model->shouldReceive('getRuleset')
            ->with('restoring')
            ->andReturn(true);

        $this->model->shouldReceive('isValid')
            ->once()
            ->with('restoring')
            ->andReturn(true);

        $this->observer->restoring($this->model);
    }

}
