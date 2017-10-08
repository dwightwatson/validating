<?php

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class ValidatingTraitTest extends PHPUnit_Framework_TestCase
{
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

    public function testModelValidationMessages()
    {
        $this->assertEquals(['bar' => 'baz'], DatabaseValidatingTraitStub::modelValidationMessages());
    }


    public function testGetValidationAttributeNames()
    {
        $this->assertEmpty($this->trait->getValidationAttributeNames());
    }

    public function testModelValidationAttributeNames()
    {
        $this->assertEmpty(DatabaseValidatingTraitStub::modelValidationAttributeNames());
    }

    public function testSetValidationAttributeNames()
    {
        $this->trait->setValidationAttributeNames(['bar' => 'baz']);

        $this->assertEquals(['bar' => 'baz'], $this->trait->getValidationAttributeNames());
    }


    public function testGetRules()
    {
        $this->assertEquals(['foo' => 'bar', 'def' => 'array'], $this->trait->getRules());
    }

    public function testRules()
    {
        $this->trait->shouldReceive('getRules')->once()->andReturn('foo');

        $result = $this->trait->rules();

        $this->assertEquals('foo', $result);
    }

    public function testSetRules()
    {
        $this->trait->setRules(['bar' => 'foo']);

        $this->assertEquals(['bar' => 'foo'], $this->trait->getRules());
    }


    public function testAttributesAreMutated()
    {
        $expected = [
            'abc'        => '123',
            'def'        => ['456'],
            'bar'        => 'rab',
            'created_at' => '2015-01-01 00:00:00'
        ];

        $this->assertEquals($expected, $this->trait->getModelAttributes());
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

    public function testIsValidOrFailThrowsException()
    {
        $this->setExpectedException('Watson\Validating\ValidationException');

        Validator::shouldReceive('make')->once()->andReturn(
            Mockery::mock('Illuminate\Contracts\Validation\Validator')
        );

        $this->trait->shouldReceive('isValid')->once()->andReturn(false);

        $this->trait->isValidOrFail();
    }

    public function testIsValidOrFailReturnsTrue()
    {
        $this->trait->shouldReceive('isValid')->once()->andReturn(true);

        $result = $this->trait->isValidOrFail();

        $this->assertTrue($result);
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


    public function testSaveOrFailThrowsExceptionOnInvalidModel()
    {
        $this->setExpectedException('Watson\Validating\ValidationException');

        Validator::shouldReceive('make')->once()->andReturn(
            Mockery::mock('Illuminate\Contracts\Validation\Validator')
        );

        $this->trait->shouldReceive('isInvalid')->once()->andReturn(true);

        $result = $this->trait->saveOrFail();

        $this->assertNull($result);
    }

    public function testSaveOrFailReturnsTrueOnValidModel()
    {
        $this->trait->shouldReceive('isInvalid')->once()->andReturn(false);

        $this->trait->shouldReceive('getModel->parentSaveOrFail')->once()->with(['foo' => 'bar'])->andReturn(true);

        $result = $this->trait->saveOrFail(['foo' => 'bar']);

        $this->assertTrue($result);
    }

    public function testParentSaveOrFailCallsParentSaveOrFail()
    {
        $result = $this->trait->parentSaveOrFail(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $result);
    }


    public function testSaveOrReturn()
    {
        $this->trait->shouldReceive('save')->once()->andReturn('foo');

        $result = $this->trait->saveOrReturn();

        $this->assertEquals('foo', $result);
    }

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

    public function testMakeValidatorSetsValidationAttributeNames()
    {
        $validatorMock = Mockery::mock('ValidatorStub');

        $validatorMock->shouldReceive('make')
            ->once()
            ->andReturn($validatorMock);

        $validatorMock->shouldReceive('setAttributeNames')->once()->with(['foo']);

        $this->trait->setValidator($validatorMock);

        $this->trait->setValidationAttributeNames(['foo']);

        $this->trait->makeValidator();
    }

    public function testThrowValidationException()
    {
        $this->setExpectedException('Watson\Validating\ValidationException');

        Validator::shouldReceive('make')->once()->andReturn(
            Mockery::mock('Illuminate\Contracts\Validation\Validator')
        );

        $this->trait->throwValidationException();
    }
}

class ValidatorStub extends \Illuminate\Validation\Factory
{
    //
}

class ModelStub extends Model
{
    public function saveOrFail(array $options = [])
    {
        return $options;
    }
}

class DatabaseValidatingTraitStub extends ModelStub implements \Watson\Validating\ValidatingInterface
{
    use \Watson\Validating\ValidatingTrait;

    protected $rules = [
        'foo' => 'bar',
        'def' => 'array'
    ];

    protected $casts = [
        'def' => 'array'
    ];

    protected $validationMessages = [
        'bar' => 'baz'
    ];

    protected $attributes = [
        'abc'        => '123',
        'def'        => '["456"]',
        'bar'        => 'bar',
        'created_at' => '2015-01-01 00:00:00'
    ];

    public function getBarAttribute($value)
    {
        return strrev($value);
    }

    protected function isDateCastable($key)
    {
        return false;
    }
}
