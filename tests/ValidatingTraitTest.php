<?php

namespace Watson\Validating\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Factory;
use Mockery;
use Watson\Validating\ValidatingInterface;
use Watson\Validating\ValidatingTrait;

class ValidatingTraitTest extends TestCase
{
    public $trait;

    protected function setUp(): void
    {
        $this->trait = Mockery::mock(DatabaseValidatingTraitStub::class)->makePartial();
    }

    public function test_get_validating_defaults_to_true()
    {
        $this->assertTrue($this->trait->getValidating());
    }

    public function test_set_validating_sets_value()
    {
        $this->trait->setValidating(false);

        $this->assertFalse($this->trait->getValidating());
    }

    public function test_get_throw_validation_exceptions_defaults_to_false()
    {
        $this->assertFalse($this->trait->getThrowValidationExceptions());
    }

    public function test_set_throw_validation_exceptions_sets_value()
    {
        $this->trait->setThrowValidationExceptions(false);

        $this->assertFalse($this->trait->getThrowValidationExceptions());
    }

    public function test_get_inject_unique_identifier_defaults_to_true()
    {
        $this->assertTrue($this->trait->getInjectUniqueIdentifier());
    }

    public function test_set_inject_unique_identifier_sets_value()
    {
        $this->trait->setInjectUniqueIdentifier(false);

        $this->assertFalse($this->trait->getInjectUniqueIdentifier());
    }

    public function test_gets_model()
    {
        $this->assertEquals($this->trait, $this->trait->getModel());
    }

    public function test_get_validation_messages()
    {
        $this->assertEquals(['bar' => 'baz'], $this->trait->getValidationMessages());
    }

    public function test_model_validation_messages()
    {
        $this->assertEquals(['bar' => 'baz'], DatabaseValidatingTraitStub::modelValidationMessages());
    }

    public function test_get_validation_attribute_names()
    {
        $this->assertEmpty($this->trait->getValidationAttributeNames());
    }

    public function test_model_validation_attribute_names()
    {
        $this->assertEmpty(DatabaseValidatingTraitStub::modelValidationAttributeNames());
    }

    public function test_set_validation_attribute_names()
    {
        $this->trait->setValidationAttributeNames(['bar' => 'baz']);

        $this->assertEquals(['bar' => 'baz'], $this->trait->getValidationAttributeNames());
    }

    public function test_get_rules()
    {
        $this->assertEquals(['foo' => 'bar', 'def' => 'array'], $this->trait->getRules());
    }

    public function test_rules()
    {
        $this->trait->shouldReceive('getRules')->once()->andReturn('foo');

        $result = $this->trait->rules();

        $this->assertEquals('foo', $result);
    }

    public function test_set_rules()
    {
        $this->trait->setRules(['bar' => 'foo']);

        $this->assertEquals(['bar' => 'foo'], $this->trait->getRules());
    }

    public function test_attributes_are_mutated()
    {
        $expected = [
            'abc' => '123',
            'def' => ['456'],
            'bar' => 'rab',
            'created_at' => '2015-01-01 00:00:00',
            'regular_datetime' => '2015-01-01 00:00:00',
            'custom_date' => '2015-01-01',
        ];

        $this->assertEquals($expected, $this->trait->getModelAttributes());
    }

    public function test_get_errors()
    {
        $this->assertEquals(0, $this->trait->getErrors()->count());
    }

    public function test_set_errors()
    {
        $messageBag = Mockery::mock(MessageBag::class);

        $this->trait->setErrors($messageBag);

        $this->assertSame($messageBag, $this->trait->getErrors());
    }

    public function test_is_valid_returns_true_when_validation_passes()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock([
                'passes' => true,
                'messages' => Mockery::mock(MessageBag::class),
            ]));

        $result = $this->trait->isValid();

        $this->assertTrue($result);
    }

    public function test_is_valid_return_false_when_validation_fails()
    {
        $messageBag = Mockery::mock(MessageBag::class);

        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock([
                'passes' => false,
                'messages' => $messageBag,
            ]));

        $result = $this->trait->isValid();

        $this->assertFalse($result);
        $this->assertSame($messageBag, $this->trait->getErrors());
    }

    public function test_is_valid_clears_errors()
    {
        $this->trait->setErrors(Mockery::mock(MessageBag::class));

        $validMessageBag = Mockery::mock(MessageBag::class);

        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock([
                'passes' => true,
                'messages' => $validMessageBag,
            ]));

        $result = $this->trait->isValid();

        $this->assertTrue($result);
        $this->assertSame($validMessageBag, $this->trait->getErrors());
    }

    public function test_is_valid_or_fail_throws_exception()
    {
        $this->expectException('Watson\Validating\ValidationException');

        Validator::shouldReceive('make')->once()->andReturn(
            Mockery::mock('Illuminate\Contracts\Validation\Validator', [
                'errors' => new MessageBag,
                'getTranslator' => Mockery::mock('Illuminate\Contracts\Translation\Translator', [
                    'get' => 'The given data was invalid.',
                ]),
            ])
        );

        $this->trait->shouldReceive('isValid')->once()->andReturn(false);

        $this->trait->isValidOrFail();
    }

    public function test_is_valid_or_fail_returns_true()
    {
        $this->trait->shouldReceive('isValid')->once()->andReturn(true);

        $result = $this->trait->isValidOrFail();

        $this->assertTrue($result);
    }

    public function test_is_invalid_returns_false_if_is_valid_is_true()
    {
        $this->trait->shouldReceive('isValid')->once()->andReturn(true);

        $result = $this->trait->isInvalid();

        $this->assertFalse($result);
    }

    public function test_is_invalid_returns_true_if_is_valid_is_false()
    {
        $this->trait->shouldReceive('isValid')->once()->andReturn(false);

        $result = $this->trait->isInvalid();

        $this->assertTrue($result);
    }

    public function test_force_save_saves_on_invalid_model()
    {
        $this->trait->shouldReceive('save')
            ->once()
            ->andReturn(true);

        $this->trait->setRules(['title' => 'required']);

        $result = $this->trait->forceSave();

        $this->assertTrue($result);
    }

    public function test_save_or_fail_throws_exception_on_invalid_model()
    {
        $this->expectException('Watson\Validating\ValidationException');

        Validator::shouldReceive('make')->once()->andReturn(
            Mockery::mock('Illuminate\Contracts\Validation\Validator', [
                'errors' => new MessageBag,
                'getTranslator' => Mockery::mock('Illuminate\Contracts\Translation\Translator', [
                    'get' => 'The given data was invalid.',
                ]),
            ])
        );

        $this->trait->shouldReceive('isInvalid')->once()->andReturn(true);

        $result = $this->trait->saveOrFail();

        $this->assertNull($result);
    }

    public function test_save_or_fail_returns_true_on_valid_model()
    {
        $this->trait->shouldReceive('isInvalid')->once()->andReturn(false);

        $this->trait->shouldReceive('getModel->parentSaveOrFail')->once()->with(['foo' => 'bar'])->andReturn(true);

        $result = $this->trait->saveOrFail(['foo' => 'bar']);

        $this->assertTrue($result);
    }

    public function test_parent_save_or_fail_calls_parent_save_or_fail()
    {
        $result = $this->trait->parentSaveOrFail(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $result);
    }

    public function test_save_or_return()
    {
        $this->trait->shouldReceive('save')->once()->andReturn('foo');

        $result = $this->trait->saveOrReturn();

        $this->assertEquals('foo', $result);
    }

    public function test_perform_validation_returns_false_on_invalid_model()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock([
                'passes' => false,
                'messages' => Mockery::mock(MessageBag::class),
            ]));

        $this->trait->setThrowValidationExceptions(false);

        $result = $this->trait->performValidation();

        $this->assertFalse($result);
    }

    public function test_perform_validation_returns_true_on_valid_model()
    {
        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock([
                'passes' => true,
                'messages' => Mockery::mock(MessageBag::class),
            ]));

        $result = $this->trait->performValidation();

        $this->assertTrue($result);
    }

    public function test_get_validator_returns_factory()
    {
        Validator::shouldReceive('getFacadeRoot')
            ->once()
            ->andReturn(Mockery::mock(Factory::class));

        $validator = $this->trait->getValidator();

        $this->assertNotNull($validator);
    }

    public function test_set_validator()
    {
        $this->trait->setValidator(Mockery::mock(ValidatorStub::class));

        $validator = $this->trait->getValidator();
        $this->assertInstanceOf(ValidatorStub::class, $validator, get_class($validator));
    }

    public function test_make_validator_sets_validation_attribute_names()
    {
        $validatorMock = Mockery::mock(ValidatorStub::class);

        $validatorMock->shouldReceive('make')
            ->once()
            ->andReturn($validatorMock);

        $validatorMock->shouldReceive('setAttributeNames')->once()->with(['foo']);

        $this->trait->setValidator($validatorMock);

        $this->trait->setValidationAttributeNames(['foo']);

        $this->trait->makeValidator();
    }

    public function test_throw_validation_exception()
    {
        $this->expectException('Watson\Validating\ValidationException');

        Validator::shouldReceive('make')->once()->andReturn(
            Mockery::mock('Illuminate\Contracts\Validation\Validator', [
                'errors' => new MessageBag,
                'getTranslator' => Mockery::mock('Illuminate\Contracts\Translation\Translator', [
                    'get' => 'The given data was invalid.',
                ]),
            ])
        );

        $this->trait->throwValidationException();
    }
}

class ValidatorStub extends Factory
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

class DatabaseValidatingTraitStub extends ModelStub implements ValidatingInterface
{
    use ValidatingTrait;

    protected $rules = [
        'foo' => 'bar',
        'def' => 'array',
    ];

    protected $casts = [
        'def' => 'array',
        'regular_datetime' => 'datetime',
        'custom_date' => 'datetime:Y-m-d',
    ];

    protected $validationMessages = [
        'bar' => 'baz',
    ];

    protected $attributes = [
        'abc' => '123',
        'def' => '["456"]',
        'bar' => 'bar',
        'created_at' => '2015-01-01 00:00:00',
        'regular_datetime' => '2015-01-01 00:00:00',
        'custom_date' => '2015-01-01',
    ];

    public function getBarAttribute($value)
    {
        return strrev($value);
    }
}
