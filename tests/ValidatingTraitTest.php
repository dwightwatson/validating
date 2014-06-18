<?php

use \Mockery;
use \Illuminate\Support\Facades\Validator;

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

    public function testGetValidatingDefaultsToTrue()
    {
        $this->assertTrue($this->trait->getValidating());
    }

    public function testSetValidatingSetsValue()
    {
        $this->trait->setValidating(false);

        $this->assertFalse($this->trait->getValidating());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetValidatingRaisesException()
    {
        $this->trait->setValidating('foo');
    }


    public function testGetThrowValidationExceptionsDefaultsToTrue()
    {
        $this->assertTrue($this->trait->getThrowValidationExceptions());
    }

    public function testSetThrowValidationExceptionsSetsValue()
    {
        $this->trait->setThrowValidationExceptions(false);

        $this->assertFalse($this->trait->getThrowValidationExceptions());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetThrowValidationExceptionsRaisesException()
    {
        $this->trait->setThrowValidationExceptions('foo');
    }


    public function testGetInjectUniqueIdentifierDefaultsToTrue()
    {
        $this->assertTrue($this->trait->getInjectUniqueIdentifier());
    }

    public function testSetInjectUniqueIdentifierSetsValue()
    {
        $this->trait->setInjectUniqueIdentifier(false);

        $this->assertFalse($this->trait->getInjectUniqueIdentifier());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetInjectUniqueIdentifierRaiseException()
    {
        $this->trait->setInjectUniqueIdentifier('foo');
    }


    public function testGetsModel()
    {
        $this->assertEquals($this->trait, $this->trait->getModel());
    }


    public function testGetRules()
    {
        $this->assertEquals(['foo' => 'bar'], $this->trait->getRules());
    }

    public function testSetRulesSetsValue()
    {
        $this->trait->setRules(['bar' => 'foo']);

        $this->assertEquals(['bar' => 'foo'], $this->trait->getRules());
    }


    public function testGetRulesetWithName()
    {
        $this->assertEquals(['foo' => 'bar'], $this->trait->getRuleset('saving'));
    }

    public function testSetRulesetWithName()
    {
        $this->trait->setRuleset(['abc' => 123], 'foo');

        $this->assertEquals(['abc' => 123], $this->trait->getRuleset('foo'));
    }


    public function testGetMessages()
    {
        $this->assertEquals(['bar' => 'baz'], $this->trait->getMessages());
    }

    public function testSetMessagesSetsValue()
    {
        $this->trait->setMessages(['bar' => 'foo']);

        $this->assertEquals(['bar' => 'foo'], $this->trait->getMessages());
    }


    public function testGetErrors()
    {
        $this->assertNull($this->trait->getErrors());
    }

    public function testSetErrors()
    {
        $messageBag = Mockery::mock('Illuminate\Support\MessageBag');

        $this->trait->setErrors($messageBag);

        $this->assertEquals($messageBag, $this->trait->getErrors());
    }


    public function testIsValidReturnsTrueWhenValidationPasses()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock(['passes' => true]));

        $result = $this->trait->isValid();

        $this->assertTrue($result);
    }

    public function testIsValidReturnFalseWhenValidationFails()
    {
        $messageBag = Mockery::mock('Illuminate\Support\MessageBag');

        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock([
                'passes'   => false,
                'messages' => $messageBag
            ]));

        $result = $this->trait->isValid();

        $this->assertFalse($result);
        $this->assertEquals($messageBag, $this->trait->getErrors());
    }


    public function testIsInvalidReturnsTrueWithValidationFails()
    {
        $messageBag = Mockery::mock('Illuminate\Support\MessageBag');

        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock([
                'passes'   => false,
                'messages' => $messageBag
            ]));

        $result = $this->trait->isInvalid();

        $this->assertTrue($result);
        $this->assertEquals($messageBag, $this->trait->getErrors());
    }

    public function testIsInvalidReturnsFalseWhenValidationPasses()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock(['passes' => true]));

        $result = $this->trait->isInvalid();

        $this->assertFalse($result);
    }


    public function testForceSaveSavesOnInvalidModel()
    {
        $this->trait->shouldReceive('save')
            ->once()
            ->andReturn(true);

        $this->trait->setRules(['title' => 'required']);

        $result = $this->trait->forceSave();

        $this->assertTrue($result);
    }

    // saveWithException

    // saveWithoutException

    // makeValidator

    /**
     * @expectedException \Watson\Validating\ValidationException
     */
    public function testPerformValidationThrowsExceptions()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock([
                'passes'   => false,
                'messages' => Mockery::mock('Illuminate\Support\MessageBag')
            ]));

        $this->trait->performValidation();
    }

    public function testPerformValidationCanReturnBoolean()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock([
                'passes'   => false,
                'messages' => Mockery::mock('Illuminate\Support\MessageBag')
            ]));

        $this->trait->setThrowValidationExceptions(false);

        $result = $this->trait->performValidation();

        $this->assertFalse($result);
    }

    public function testPerformValidationReturnsBooleans()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock(['passes' => true]));

        $result = $this->trait->performValidation();

        $this->assertTrue($result);
    }


    // updateRulesUniques

    // updateRulesetUniques

    // injectUniqueIdentifiersToRules

    // prepareUniqueRule


}

class DatabaseValidatingTraitStub
{
    use \Watson\Validating\ValidatingTrait;

    public $exists = false;

    protected $addUniqueIdentifierToRules = true;

    protected $rules = [
        'foo' => 'bar'
    ];

    protected $rulesets = [
        'creating' => [
            'baz' => 'bat',
            'foo' => 'baz'
        ],
        'saving' => [
            'foo' => 'bar'
        ]
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
