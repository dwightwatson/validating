<?php

use Illuminate\Support\Facades\Validator;

class ValidatingTraitTest extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require __DIR__.'/../../bootstrap/start.php';
    }
    
    public function setUp()
    {

    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testGetsRulesArray()
    {
        $model = Mockery::mock('DatabaseValidatingTraitStub');
        $model->shouldDeferMissing();

        $this->assertEquals(['foo' => 'bar'], $model->getRules());
    }

    public function testSetsRulesArray()
    {
        $model = Mockery::mock('DatabaseValidatingTraitStub');
        $model->shouldDeferMissing();

        $model->setRules(['bar' => 'foo']);

        $this->assertEquals(['bar' => 'foo'], $model->getRules());
    }

    public function testGetsMessagesArray()
    {
        $model = Mockery::mock('DatabaseValidatingTraitStub');
        $model->shouldDeferMissing();

        $this->assertEquals(['bar' => 'baz'], $model->getMessages());
    }

    public function testSetsMessagesArray()
    {
        $model = Mockery::mock('DatabaseValidatingTraitStub');
        $model->shouldDeferMissing();

        $model->setMessages(['bar' => 'foo']);

        $this->assertEquals(['bar' => 'foo'], $model->getMessages());
    }

    public function testGetsErrorsWithoutValidation()
    {
        $model = Mockery::mock('DatabaseValidatingTraitStub');
        $model->shouldDeferMissing();

        $this->assertEquals([], $model->getErrors());
    }

    public function testValidateReturnsTrueOnValidModel()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock(['passes' => true]));

        $model = Mockery::mock('DatabaseValidatingTraitStub');
        $model->shouldDeferMissing();

        $result = $model->validate();

        $this->assertTrue($result);
    }

    public function testValidateReturnsFalseAndSetsErrorsOnInvalidModel()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock(['passes' => false, 'messages' => 'foo']));

        $model = Mockery::mock('DatabaseValidatingTraitStub');
        $model->shouldDeferMissing();

        $result = $model->validate();

        $this->assertFalse($result);
        $this->assertEquals('foo', $model->getErrors());
    }
}

class DatabaseValidatingTraitStub
{
    use Watson\Validating\ValidatingTrait;

    public $exists = false;

    protected $rules = [
        'foo' => 'bar'
    ];

    protected $messages = [
        'bar' => 'baz'
    ];

    public function getTable()
    {
        return 'foo';
    }

    public function getKeyName()
    {
        return 'id';
    }

    public function getKey()
    {
        return 1;
    }

    public function toArray()
    {
        return ['abc' => '123'];
    }
}