<?php

namespace Watson\Validating\Tests\Injectors;

use Illuminate\Database\Eloquent\Model;
use Mockery;
use Watson\Validating\Injectors\UniqueWithInjector;
use Watson\Validating\Tests\TestCase;
use Watson\Validating\ValidatingTrait;

class UniqueWithInjectorTest extends TestCase
{
    public $trait;

    protected function setUp(): void
    {
        $this->trait = Mockery::mock(UniqueWithValidatingStub::class)->makePartial();
    }

    public function test_update_rules_uniques_unique_with_with_uniques_infers_attributes()
    {
        $this->trait->exists = true;

        $this->trait->setRules([
            'first_name' => 'unique_with:users,last_name',
        ]);

        $this->trait->updateRulesUniques();

        $result = $this->trait->getRules();

        $this->assertEquals(['first_name' => ['unique_with:users,last_name,1']], $result);
    }

    public function test_update_rules_uniques_unique_with_does_not_override_provided_parameters()
    {
        $this->trait->exists = true;

        $this->trait->setRules([
            'first_name' => 'unique_with:users,last_name,5',
        ]);

        $this->trait->updateRulesUniques();

        $result = $this->trait->getRules();

        $this->assertEquals(['first_name' => ['unique_with:users,last_name,5']], $result);
    }
}

class UniqueWithValidatingStub extends Model
{
    use UniqueWithInjector;
    use ValidatingTrait;

    public function getKey()
    {
        return 1;
    }
}
