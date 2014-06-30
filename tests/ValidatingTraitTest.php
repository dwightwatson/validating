<?php

use \Mockery;
use \Illuminate\Support\Facades\Validator;
use \Illuminate\Validation\Factory;

class ValidatingTraitTest extends \PHPUnit_Framework_TestCase {
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


    public function testGetThrowValidationExceptionsDefaultsToFalse()
    {
        $this->assertFalse($this->trait->getThrowValidationExceptions());
    }

    public function testSetThrowValidationExceptionsSetsValue()
    {
        $this->trait->setThrowValidationExceptions(false);

        $this->assertFalse($this->trait->getThrowValidationExceptions());
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


    public function testMergeRulesets()
    {
        $result = $this->trait->mergeRulesets('saving', 'creating');

        $this->assertEquals(['baz' => 'bat', 'foo' => 'baz'], $result);
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

    // saveOrFail

    // saveOrReturn

    public function testPerformValidationReturnsFalseOnInvalidModel()
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

    public function testPerformValidationReturnsTrueOnValidModel()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock(['passes' => true]));

        $result = $this->trait->performValidation();

        $this->assertTrue($result);
    }

    public function testGetValidatorReturnsFactory()
    {
        Validator::shouldReceive('getFacadeRoot')
            ->once()
            ->andReturn(Mockery::mock('\Illuminate\Validation\Factory'));

        $validator = $this->trait->getValidator();
    }

    public function testSetValidator()
    {
        $this->trait->setValidator(Mockery::mock('ValidatorStub'));

        $validator = $this->trait->getValidator();
        $this->assertInstanceOf('ValidatorStub', $validator, get_class($validator));
    }

    // updateRulesUniques

    // updateRulesetUniques

    // injectUniqueIdentifiersToRules

    // prepareUniqueRule


}

class DatabaseValidatingTraitStub implements \Watson\Validating\ValidatingInterface{

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

    protected $validationMessages = [
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

class ValidatorStub extends \Illuminate\Validation\Factory {

}
