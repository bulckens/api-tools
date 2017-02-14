<?php

namespace spec\Bulckens\ApiTests\V1;

use Bulckens\ApiTests\V1\TestAdaptor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TestAdaptorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TestAdaptor::class);
    }
}
