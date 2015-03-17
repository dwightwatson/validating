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

    public function testGetValidationMessages()
    {
        $this->assertEquals(['bar' => 'baz'], $this->trait->getValidationMessages());
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

    public function testSetRules()
    {
        $this->trait->setRules(['bar' => 'foo']);

        $this->assertEquals(['bar' => 'foo'], $this->trait->getRules());
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
