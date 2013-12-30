<?php

namespace spec\MvcAuthToken;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TokenSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('MvcAuthToken\Token');
    }

    public function it_should_have_realm()
    {
        $realm = 'realm';
        $this->setRealm($realm);
        $this->getRealm()->shouldReturn($realm);
    }

    public function it_should_have_token()
    {
        $token = 'token';
        $this->setToken($token);
        $this->getToken()->shouldReturn($token);
    }

    public function it_should_have_coverage()
    {
        $coverage = 'coverage';
        $this->setCoverage($coverage);
        $this->getCoverage()->shouldReturn($coverage);
    }

    public function it_should_have_nonce()
    {
        $nonce = 'nonce';
        $this->setNonce($nonce);
        $this->getNonce()->shouldReturn($nonce);
    }

    public function it_should_have_timestamp()
    {
        $timestamp = 123456789;
        $this->setTimestamp($timestamp);
        $this->getTimestamp()->shouldReturn($timestamp);

        $this->setTimestamp('0123456');
        $this->getTimestamp()->shouldReturn(123456);
    }

    public function it_should_have_auth()
    {
        $auth = 'auth';
        $this->setAuth($auth);
        $this->getAuth()->shouldReturn($auth);
    }

}
