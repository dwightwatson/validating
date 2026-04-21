<?php

namespace Watson\Validating\Tests;

use Mockery;

class ValidatingModelTest extends TestCase
{
    public function test_get_message_bag_calls_get_errors()
    {
        $mock = Mockery::mock('Watson\Validating\ValidatingModel[getErrors]');

        $mock->shouldReceive('getErrors')->once()->andReturn('foo');

        $result = $mock->getMessageBag();

        $this->assertEquals('foo', $result);
    }
}
