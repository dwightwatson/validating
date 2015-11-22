<?php

use Watson\Validating\ValidatingModel;

class ValidatingModelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    public function testGetMessageBagCallsGetErrors()
    {
        $mock = Mockery::mock('Watson\Validating\ValidatingModel[getErrors]');

        $mock->shouldReceive('getErrors')->once()->andReturn('foo');

        $result = $mock->getMessageBag();

        $this->assertEquals('foo', $result);
    }
}
