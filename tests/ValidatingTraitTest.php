<?php

use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Validator;

class ValidatingTraitTest extends \PHPUnit_Framework_TestCase {
    public $trait;

    public function setUp()
    {
        $this->trait = Mockery::mock('DatabaseValidatingTraitStub')->makePartial();
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


    public function testGetValidationAttributeNames()
    {
        $this->assertNull($this->trait->getValidationAttributeNames());
    }

    public function testSetValidationAttributeNames()
    {
        $this->trait->setValidationAttributeNames(['bar' => 'baz']);

        $this->assertEquals(['bar' => 'baz'], $this->trait->getValidationAttributeNames());
    }


    public function testGetRules()
    {
        $this->assertEquals(['foo' => 'bar'], $this->trait->getRules());
    }

    public function testGetDefaultRulesWithRuleset()
    {
        $this->assertEquals(['foo' => 'bar'], $this->trait->getDefaultRules());
    }

    public function testGetDefaultRulsetWithRules()
    {
        $this->trait->setRulesets(null);

        $this->assertEquals(['foo' => 'bar'], $this->trait->getDefaultRules());
    }

    public function testGetDefaultRulesetWithoutRules()
    {
        $this->trait->setRulesets(null);
        $this->trait->setRules(null);

        $this->assertEquals([], $this->trait->getDefaultRules());
    }

    public function testSetRules()
    {
        $this->trait->setRules(['bar' => 'foo']);

        $this->assertEquals(['bar' => 'foo'], $this->trait->getRules());
    }


    public function testGetRulesets()
    {
        $rulesets = [
            'creating' => [
                'baz' => 'bat',
                'foo' => 'baz'
            ],
            'saving' => [
                'foo' => 'bar'
            ]
        ];

        $this->assertEquals($rulesets, $this->trait->getRulesets());
    }

    public function testSetRulesets()
    {
        $this->trait->setRulesets(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $this->trait->getRulesets());
    }


    public function testGetRuleset()
    {
        $this->assertEquals(['foo' => 'bar'], $this->trait->getRuleset('saving'));
    }

    public function testSetRulesetWithName()
    {
        $this->trait->setRuleset(['abc' => 123], 'foo');

        $this->assertEquals(['abc' => 123], $this->trait->getRuleset('foo'));
    }

    public function testSetRulesetWithNameWithoutDefaultMerged()
    {
        $this->trait->setRuleset(['abc' => 123], 'foo');

        $this->assertEquals(['abc' => 123], $this->trait->getRuleset('foo', false));
    }

    public function testAddRules()
    {
        $this->trait->addRules(['abc' => 'easy as']);

        $this->assertEquals(['foo' => 'bar', 'abc' => 'easy as'], $this->trait->getRules());
    }

    public function testRemoveRules()
    {
        $this->trait->removeRules('foo');

        $this->assertEquals([], $this->trait->getRules());
    }


    public function testAddRulesToRuleset()
    {
        $this->trait->addRules(['abc' => 'easy as'], 'creating');

        $this->assertEquals(['baz' => 'bat', 'foo' => 'baz', 'abc' => 'easy as'], $this->trait->getRuleset('creating'));
    }

    public function testRemoveRulesFromRuleset()
    {
        $this->trait->removeRules('baz', 'creating');

        $this->assertEquals(['foo' => 'baz'], $this->trait->getRuleset('creating'));
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


    public function testGetErrors()
    {
        $this->assertEquals(0, $this->trait->getErrors()->count());
    }

    public function testSetErrors()
    {
        $messageBag = Mockery::mock('Illuminate\Support\MessageBag');

        $this->trait->setErrors($messageBag);

        $this->assertSame($messageBag, $this->trait->getErrors());
    }


    public function testIsValidReturnsTrueWhenValidationPasses()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock([
              'passes' => true,
              'messages' => Mockery::mock('Illuminate\Support\MessageBag')
            ]));

        $this->trait->shouldReceive('getConfirmationAttributes')
            ->once()
            ->andReturn([]);

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

        $this->trait->shouldReceive('getConfirmationAttributes')
            ->once()
            ->andReturn([]);

        $result = $this->trait->isValid();

        $this->assertFalse($result);
        $this->assertSame($messageBag, $this->trait->getErrors());
    }

    public function testIsValidClearsErrors()
    {
      $this->trait->setErrors(Mockery::mock('Illuminate\Support\MessageBag'));

      $validMessageBag = Mockery::mock('Illuminate\Support\MessageBag');

      Validator::shouldReceive('make')
        ->once()
        ->andReturn(Mockery::mock([
          'passes'   => true,
          'messages' => $validMessageBag
        ]));

      $this->trait->shouldReceive('getConfirmationAttributes')
        ->once()
        ->andReturn([]);

      $result = $this->trait->isValid();

      $this->assertTrue($result);
      $this->assertSame($validMessageBag, $this->trait->getErrors());
    }

    public function testIsInvalidReturnsFalseIfIsValidIsTrue()
    {
        $this->trait->shouldReceive('isValid')->once()->andReturn(true);

        $result = $this->trait->isInvalid();

        $this->assertFalse($result);
    }

    public function testIsInvalidReturnsTrueIfIsValidIsFalse()
    {
      $this->trait->shouldReceive('isValid')->once()->andReturn(false);

      $result = $this->trait->isInvalid();

      $this->assertTrue($result);
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

        $this->trait->shouldReceive('getConfirmationAttributes')
            ->once()
            ->andReturn([]);

        $this->trait->setThrowValidationExceptions(false);

        $result = $this->trait->performValidation();

        $this->assertFalse($result);
    }

    public function testPerformValidationReturnsTrueOnValidModel()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock([
                'passes' => true,
                'messages' => Mockery::mock('Illuminate\Support\MessageBag')
            ]));

        $this->trait->shouldReceive('getConfirmationAttributes')
            ->once()
            ->andReturn([]);

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

    public function testGetConfirmationAttributes()
    {
        Input::shouldReceive('all')
            ->once()
            ->andReturn(['password' => 'foo', 'password_confirmation' => 'bar']);

        $result = $this->trait->getConfirmationAttributes();

        $this->assertEquals(['password_confirmation' => 'bar'], $result);
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
