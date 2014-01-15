<?php

namespace spec\Phpro\MvcAuthToken\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Prophet;

class AuthenticationListenerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\MvcAuthToken\Listener\AuthenticationListener');
    }

    /**
     * @param \Phpro\MvcAuthToken\TokenServer $tokenServer
     */
    protected function mockTokenServer($tokenServer)
    {
        $prophet = new Prophet();
        $token = $prophet->prophesize('Phpro\MvcAuthToken\Token');
        $token->getToken()->willReturn('token');

        $tokenServer->setAdapter(Argument::any())->willReturn(null);
        $tokenServer->setRequest(Argument::any())->willReturn(null);
        $tokenServer->setResponse(Argument::any())->willReturn(null);
        $tokenServer->getUserId()->willReturn('token');
        $tokenServer->getToken()->willReturn($token);
    }

    /**
     * @param \Phpro\MvcAuthToken\Adapter\AdapterInterface $adapter
     */
    public function it_should_have_adapter($adapter)
    {
        $this->setAdapter($adapter);
        $this->getAdapter()->shouldReturn($adapter);
    }

    /**
     * @param \ZF\MvcAuth\MvcAuthEvent $mvcAuthEvent
     * @param \Zend\Mvc\MvcEvent $mvcEvent
     * @param \Zend\Console\Request $request
     */
    public function it_should_do_nothing_on_console_request($mvcAuthEvent, $mvcEvent, $request)
    {
        $mvcAuthEvent->getMvcEvent()->willReturn($mvcEvent);
        $mvcEvent->getRequest()->willReturn($request);
        $this->__invoke($mvcAuthEvent)->shouldReturn(null);
    }

    /**
     * @param \ZF\MvcAuth\MvcAuthEvent $mvcAuthEvent
     * @param \Zend\Mvc\MvcEvent $mvcEvent
     * @param \Zend\Http\Request $request
     */
    public function it_should_do_nothing_on_invalid_method_type($mvcAuthEvent, $mvcEvent, $request)
    {
        $mvcAuthEvent->getMvcEvent()->willReturn($mvcEvent);
        $mvcEvent->getRequest()->willReturn($request);

        $request->getMethod()->willReturn('HEAD');
        $this->__invoke($mvcAuthEvent)->shouldReturn(null);

        $request->getMethod()->willReturn('OPTIONS');
        $this->__invoke($mvcAuthEvent)->shouldReturn(null);


    }

    /**
     * @param \ZF\MvcAuth\MvcAuthEvent $mvcAuthEvent
     * @param \Zend\Mvc\MvcEvent $mvcEvent
     * @param \Zend\Http\Request $request
     * @param \Zend\Http\Request $response
     * @param \Phpro\MvcAuthToken\TokenServer $tokenServer
     * @param \Phpro\MvcAuthToken\Adapter\AdapterInterface $adapter
     * @param \ZF\MvcAuth\Identity\GuestIdentity $identity
     */
    public function it_should_return_authenticated_identity_when_valid($mvcAuthEvent, $mvcEvent, $request, $response, $tokenServer, $adapter, $identity)
    {
        $mvcAuthEvent->getMvcEvent()->willReturn($mvcEvent);
        $mvcEvent->getRequest()->willReturn($request);
        $mvcEvent->getResponse()->willReturn($response);
        $this->mockTokenServer($tokenServer);
        $this->setTokenServer($tokenServer);
        $this->setAdapter($adapter);

        // String authentication:
        $tokenServer->authenticate()->willReturn(true);
        $this->__invoke($mvcAuthEvent)->shouldReturnAnInstanceOf('ZF\MvcAuth\Identity\AuthenticatedIdentity');

        // Identity authentication
        $tokenServer->getUserId()->willReturn($identity);
        $tokenServer->authenticate()->willReturn(true);
        $this->__invoke($mvcAuthEvent)->shouldBe($identity);
    }


    /**
     * @param \ZF\MvcAuth\MvcAuthEvent $mvcAuthEvent
     * @param \Zend\Mvc\MvcEvent $mvcEvent
     * @param \Zend\Http\Request $request
     * @param \Zend\Http\Request $response
     * @param \Phpro\MvcAuthToken\TokenServer $tokenServer
     * @param \Phpro\MvcAuthToken\Adapter\AdapterInterface $adapter
     */
    public function it_should_return_guest_identity_when_invalid($mvcAuthEvent, $mvcEvent, $request, $response, $tokenServer, $adapter)
    {
        $mvcAuthEvent->getMvcEvent()->willReturn($mvcEvent);
        $mvcEvent->getRequest()->willReturn($request);
        $mvcEvent->getResponse()->willReturn($response);
        $this->mockTokenServer($tokenServer);
        $this->setTokenServer($tokenServer);
        $this->setAdapter($adapter);

        // Invalid token authentication
        $tokenServer->authenticate()->willReturn(false);
        $this->__invoke($mvcAuthEvent)->shouldReturnAnInstanceOf('ZF\MvcAuth\Identity\GuestIdentity');

        // Exception while reading token data
        $tokenServer->authenticate()->willThrow('Phpro\MvcAuthToken\Exception\TokenException');
        $this->__invoke($mvcAuthEvent)->shouldReturnAnInstanceOf('ZF\MvcAuth\Identity\GuestIdentity');
    }

}
