<?php

namespace spec\MvcAuthToken;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Prophet;

class TokenServerSpec extends ObjectBehavior
{
    /**
     * @param \MvcAuthToken\Adapter\AdapterInterface $adapter
     */
    protected function mockAdapter($adapter)
    {
        $adapter->validateNonce(Argument::any())->willReturn(true);
        $adapter->validateTimestamp(Argument::any())->willReturn(true);
        $adapter->validateToken(Argument::any())->willReturn(true);
        $this->setAdapter($adapter);
    }

    /**
     * @param \MvcAuthToken\Token $token
     */
    protected function mockToken($token)
    {
        $token->getNonce()->willReturn('nonce');
        $token->getTimestamp()->willReturn(12345);
        $this->setToken($token);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('MvcAuthToken\TokenServer');
    }

    /**
     * @param \MvcAuthToken\Adapter\AdapterInterface $adapter
     */
    public function it_should_have_adapter($adapter)
    {
        $this->setAdapter($adapter);
        $this->getAdapter()->shouldReturn($adapter);
    }

    /**
     * @param \Zend\Http\Request request
     */
    public function it_should_have_request($request)
    {
        $this->setRequest($request);
        $this->getRequest()->shouldReturn($request);
    }

    /**
     * @param \Zend\Http\Response $response
     */
    public function it_should_have_response($response)
    {
        $this->setResponse($response);
        $this->getResponse()->shouldReturn($response);
    }

    /**
     * @param \MvcAuthToken\Token $token
     */
    public function it_should_have_token($token)
    {
        $this->setToken($token);
        $this->getToken()->shouldReturn($token);
    }

    /**
     * @param \Zend\Http\Request $request
     * @param \Zend\Http\Header\Authorization $authorizationHeader
     */
    public function it_should_create_token($request, $authorizationHeader)
    {
        $authorizationHeader->getFieldValue()->willReturn('Token token="user_token_id", auth="encrypted_auth"');
        $request->getHeader('Authorization')->willReturn($authorizationHeader);
        $this->setRequest($request);

        $this->createToken()->shouldReturnAnInstanceOf('MvcAuthToken\Token');
    }

    /**
     * @TODO Wait for phpspec issue to close
     *
     * @param \Zend\Http\Request $request
     * @param \Zend\Http\Header\Authorization $authorizationHeader
     */
    public function it_should_not_create_token_on_invalid_authorization_header($request, $authorizationHeader)
    {
        // Run this spec when this ticket is closed:
        // @link https://github.com/phpspec/phpspec/issues/242
        return;

        // No authentication header was set
        $this->setRequest($request);
        $request->getHeader('Authorization')->willReturn(null);
        $this->shouldThrow('MvcAuthToken\Exception\TokenException')->duringCreateToken();

        // Invalid authentication type
        $authorizationHeader->getFieldValue()->willReturn('Basic base64_user_and_password');
        $request->getHeader('Authorization')->willReturn($authorizationHeader);
        $this->shouldThrow('MvcAuthToken\Exception\TokenException')->duringCreateToken();
    }

    public function it_should_serialize_token_parameters()
    {
        $result = $this->getTokenParameters('token="user_token_id", auth="encrypted_auth');

        $result->shouldBeArray();
        $result['token']->shouldBe('user_token_id');
        $result['auth']->shouldBe('encrypted_auth');
    }

    /**
     * @param \MvcAuthToken\Token $token
     * @param \MvcAuthToken\Adapter\AdapterInterface $adapter
     */
    public function it_should_validate_token($token, $adapter)
    {
        // Mock objects
        $this->mockToken($token);
        $this->mockAdapter($adapter);

        // Run specs:
        $this->validateToken($token)->shouldReturn(true);
        $adapter->validateNonce('nonce')->shouldBeCalled();
        $adapter->validateTimestamp(12345)->shouldBeCalled();
        $adapter->validateToken($token)->shouldBeCalled();
    }

    /**
     * @param \MvcAuthToken\Token $token
     * @param \MvcAuthToken\Adapter\AdapterInterface $adapter
     */
    public function it_should_authenticate_request($token, $adapter)
    {
        $this->mockToken($token);
        $this->mockAdapter($adapter);

        $this->authenticate()->shouldReturn(true);
    }

    /**
     * @param \MvcAuthToken\Token $token
     * @param \MvcAuthToken\Adapter\AdapterInterface $adapter
     */
    public function it_should_be_able_to_retrieve_user_id($token, $adapter)
    {
        $this->mockToken($token);
        $this->mockAdapter($adapter);

        $userId = 'administrator';
        $adapter->getUserId($token)->willReturn($userId);

        $this->getUserId($token)->shouldReturn($userId);
        $adapter->getUserId($token)->shouldBeCalled();
    }

}
