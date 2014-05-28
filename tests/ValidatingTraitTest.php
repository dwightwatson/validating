<?php

use Illuminate\Support\Facades\Validator;

class ValidatingTraitTest extends \PHPUnit_Framework_TestCase
{
    public $trait;

    public function setUp()
    {
        $this->trait = Mockery::mock('DatabaseValidatingTraitStub');
        $this->trait->shouldDeferMissing();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testGetsDefaultRules()
    {
        $this->assertEquals(['foo' => 'bar'], $this->trait->getRules());
    }

    public function testSetsDefaultRules()
    {
        $this->trait->setRules(['bar' => 'foo']);

        $this->assertEquals(['bar' => 'foo'], $this->trait->getRules());
    }

    public function testGetsRulesWithName()
    {
        $this->assertEquals(['foo' => 'bar'], $this->trait->getRules('saving'));        
    }

    public function testSetsRulesWithName()
    {
        $this->trait->setRules(['abc' => 123], 'foo');

        $this->assertEquals(['abc' => 123], $this->trait->getRules('foo'));
    }


    public function testGetsMessagesArray()
    {
        $this->assertEquals(['bar' => 'baz'], $this->trait->getMessages());
    }

    public function testSetsMessagesArray()
    {
        $this->trait->setMessages(['bar' => 'foo']);

        $this->assertEquals(['bar' => 'foo'], $this->trait->getMessages());
    }


    public function testGetsErrorsWithoutValidation()
    {
        $this->assertNull($this->trait->getErrors());
    }

    public function testGetsErrorsWithValidation()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock([
                'passes'   => false,
                'messages' => 'foo'
            ]));

        $this->trait->validate();

        $this->assertEquals('foo', $this->trait->getErrors());
    }


    public function testGetsInjectIdentifier()
    {
        $this->assertTrue($this->trait->getInjectIdentifier());
    }

    public function testSetsInjectIdentifierToTrue()
    {
        $this->trait->setInjectIdentifier(true);

        $this->assertTrue($this->trait->getInjectIdentifier());
    }

    public function testSetsInjectIdentifierToFalse()
    {
        $this->trait->setInjectIdentifier(false);

        $this->assertFalse($this->trait->getInjectIdentifier());
    }

    public function testValidateReturnsTrueOnValidModel()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock(['passes' => true]));

        $result = $this->trait->validate();

        $this->assertTrue($result);
    }

    public function testValidateReturnsFalseOnInvalidModel()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock(['passes' => false, 'messages' => 'foo']));

        $result = $this->trait->validate();

        $this->assertFalse($result);
    }
}

class DatabaseValidatingTraitStub
{
    use Watson\Validating\ValidatingTrait;

    public $exists = false;

    protected $addUniqueIdentifierToRules = true;

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

    public function getAttributes()
    {
        return ['abc' => '123'];
    }
}