<?php

namespace Watson\Validating\Tests\Injectors;

use Illuminate\Database\Eloquent\Model;
use Mockery;
use Watson\Validating\Tests\TestCase;
use Watson\Validating\ValidatingTrait;

class UniqueInjectorTest extends TestCase
{
    public $trait;

    protected function setUp(): void
    {
        $this->trait = Mockery::mock(UniqueValidatingStub::class)->makePartial();
    }

    public function test_update_rules_uniques_without_uniques()
    {
        $this->trait->setRules(['user_id' => ['required']]);

        $this->trait->updateRulesUniques();

        $result = $this->trait->getRules();

        $this->assertEquals(['user_id' => ['required']], $result);
    }

    public function test_update_rules_uniques_with_uniques_infers_attributes()
    {
        $this->trait->exists = true;

        $this->trait->shouldReceive('getTable')->andReturn('users');

        $this->trait->setRules(['user_id' => 'unique']);

        $this->trait->updateRulesUniques();

        $result = $this->trait->getRules();

        $this->assertEquals(['user_id' => ['unique:sqlite.users,user_id,1,id']], $result);
    }

    public function test_get_prepared_rules_uniques()
    {
        $this->trait->exists = true;

        $this->trait->shouldReceive('getTable')->andReturn('users');

        $this->trait->setRules(['user_id' => 'unique']);

        $result = $this->trait->getPreparedRules();

        $this->assertEquals(['user_id' => ['unique:sqlite.users,user_id,1,id']], $result);
    }

    public function test_update_rules_uniques_with_uniques_and_additional_where_clause_infers_attributes()
    {
        $this->trait->exists = true;

        $this->trait->shouldReceive('getTable')->andReturn('users');

        $this->trait->setRules(['user_id' => 'unique:users,user_id,1,id,username,null']);

        $this->trait->updateRulesUniques();

        $result = $this->trait->getRules();

        $this->assertEquals(['user_id' => ['unique:sqlite.users,user_id,1,id,username,test']], $result);
    }

    public function test_update_rules_uniques_with_uniques_and_additional_where_clause_infers_attributes_maintaining_null_value()
    {
        $this->trait->exists = true;

        $this->trait->shouldReceive('getTable')->andReturn('users');

        $this->trait->setRules(['user_id' => 'unique:users,user_id,1,id,deleted,null']);

        $this->trait->updateRulesUniques();

        $result = $this->trait->getRules();

        $this->assertEquals(['user_id' => ['unique:sqlite.users,user_id,1,id,deleted,NULL']], $result);
    }

    public function test_update_rules_uniques_with_non_persisted_model_infers_attributes()
    {
        $this->trait->shouldReceive('getTable')->andReturn('users');

        $this->trait->setRules(['user_id' => 'unique']);

        $this->trait->updateRulesUniques();

        $result = $this->trait->getRules();

        $this->assertEquals(['user_id' => ['unique:sqlite.users,user_id']], $result);
    }

    public function test_update_rules_uniques_works_with_multiple_uniques()
    {
        $this->trait->shouldReceive('getTable')->andReturn('users');

        $this->trait->setRules([
            'email' => 'unique',
            'slug' => 'unique',
        ]);

        $this->trait->updateRulesUniques();

        $result = $this->trait->getRules();

        $this->assertEquals([
            'email' => ['unique:sqlite.users,email'],
            'slug' => ['unique:sqlite.users,slug'],
        ], $result);
    }

    public function test_update_rules_uniques_does_not_override_provided_parameters()
    {
        $this->trait->setRules(['users' => 'unique:foo,bar,5,bat']);

        $this->trait->updateRulesUniques();

        $result = $this->trait->getRules();

        $this->assertEquals(['users' => ['unique:sqlite.foo,bar,5,bat']], $result);
    }
}

class UniqueValidatingStub extends Model
{
    use ValidatingTrait;

    protected $username = 'test';

    protected $deleted = null;

    public function getKey()
    {
        return 1;
    }

    public function getConnectionName()
    {
        return 'sqlite';
    }
}
