<?php

namespace spec\Phpro\MvcAuthToken\Exception;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TokenExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\MvcAuthToken\Exception\TokenException');
    }

    public function it_should_extend_exception()
    {
        $this->shouldHaveType('Exception');
    }
}
