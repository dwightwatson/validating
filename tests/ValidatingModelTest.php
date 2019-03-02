<?php

use Watson\Validating\ValidatingModel;
use PHPUnit\Framework\TestCase;

class ValidatingModelTest extends TestCase
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
