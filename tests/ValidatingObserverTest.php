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

        // Model should return ruleset.
        $this->model->shouldReceive('getRuleset')
            ->andReturn([]);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreatingPerformsValidation()
    {
        $this->observer->creating($this->model);
    }

    public function testUpdatingPerfomsValidation()
    {
        $this->observer->updating($this->model);
    }

    public function testSavingPerformsValidation()
    {
        $this->model->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        $this->observer->saving($this->model);
    }

    public function testDeletingPerformsValidation()
    {
        $this->observer->deleting($this->model);
    }
}