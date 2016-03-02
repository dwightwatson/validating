<?php

class UniqueWithInjectorTest extends PHPUnit_Framework_TestCase
{
    public $trait;

    public function setUp()
    {
        $this->trait = Mockery::mock('UniqueWithValidatingStub')->makePartial();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testUpdateRulesUniquesUniqueWithWithUniquesInfersAttributes()
    {
        $this->trait->exists = true;

        $this->trait->setRules([
            'first_name' => 'unique_with:users,last_name'
        ]);

        $this->trait->updateRulesUniques();

        $result = $this->trait->getRules();

        $this->assertEquals(['first_name' => ['unique_with:users,last_name,1']], $result);
    }

    public function testUpdateRulesUniquesUniqueWithDoesNotOverrideProvidedParameters()
    {
        $this->trait->exists = true;

        $this->trait->setRules([
            'first_name' => 'unique_with:users,last_name,5'
        ]);

        $this->trait->updateRulesUniques();

        $result = $this->trait->getRules();

        $this->assertEquals(['first_name' => ['unique_with:users,last_name,5']], $result);
    }
}

class UniqueWithValidatingStub extends \Illuminate\Database\Eloquent\Model
{
    use \Watson\Validating\ValidatingTrait;
    use \Watson\Validating\Injectors\UniqueWithInjector;

    public function getKey()
    {
        return 1;
    }
}
