<?php

namespace Watson\Validating\Tests;

use Mockery;
use Watson\Validating\ValidatingModel;

class ValidatingModelTest extends TestCase
{
    public function testGetMessageBagCallsGetErrors()
    {
        $mock = Mockery::mock('Watson\Validating\ValidatingModel[getErrors]');

        $mock->shouldReceive('getErrors')->once()->andReturn('foo');

        $result = $mock->getMessageBag();

        $this->assertEquals('foo', $result);
    }
}
