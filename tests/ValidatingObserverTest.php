<?php

use Watson\Validating\ValidatingObserver;

class ValidatingObserverTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->model = Mockery::mock('Illuminate\Database\Eloquent\Model');
        $this->observer = new ValidatingObserver;

        // Validating should be enabled.
        $this->model->shouldReceive('getValidating')
            ->andReturn(true);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreatingPerformsValidation()
    {
        $this->model->shouldReceive('getRuleset')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->model->shouldReceive('isValid')
            ->andReturn(true);

        $this->observer->creating($this->model);
    }

    public function testUpdatingPerfomsValidation()
    {
        $this->model->shouldReceive('getRuleset')
            ->once()
            ->with('updating')
            ->andReturn(['foo' => 'bar']);

        $this->model->shouldReceive('isValid')
            ->andReturn(true);

        $this->observer->updating($this->model);
    }

    public function testSavingPerformsValidation()
    {
        $this->model->shouldReceive('getRuleset')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->model->shouldReceive('isValid')
            ->never();

        $this->observer->saving($this->model);
    }

    public function testDeletingPerformsValidation()
    {
        $this->model->shouldReceive('getRuleset')
            ->once()
            ->with('deleting')
            ->andReturn(['foo' => 'bar']);

        $this->model->shouldReceive('isValid')
            ->andReturn(true);

        $this->observer->deleting($this->model);
    }
}