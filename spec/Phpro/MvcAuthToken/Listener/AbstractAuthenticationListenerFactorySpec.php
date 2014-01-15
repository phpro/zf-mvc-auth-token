<?php

namespace spec\Phpro\MvcAuthToken\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Prophet;

class AbstractAuthenticationListenerFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\MvcAuthToken\Listener\AbstractAuthenticationListenerFactory');
    }

    public function it_should_implement_abstractFactoryInterface()
    {
        $this->shouldImplement('Zend\ServiceManager\AbstractFactoryInterface');
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function mockConfig($serviceLocator)
    {
        $prophet = new Prophet();
        $config = [
            'mvc-auth-token-authentication-listener' => [
                'TokenListener' => [
                    'adapter' => 'TokenAdapter',
                ],
            ],
        ];

        $serviceLocator->has('Config')->willReturn(true);
        $serviceLocator->get('Config')->willReturn($config);

        $adapter = $prophet->prophesize('\Phpro\MvcAuthToken\Adapter\AdapterInterface');
        $serviceLocator->has('TokenAdapter')->willReturn(true);
        $serviceLocator->get('TokenAdapter')->willReturn($adapter);

        $tokenServer = $prophet->prophesize('\Phpro\MvcAuthToken\TokenServer');
        $serviceLocator->has('Phpro\MvcAuthToken\TokenServer')->willReturn(true);
        $serviceLocator->get('Phpro\MvcAuthToken\TokenServer')->willReturn($tokenServer);
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function it_should_be_able_to_create_valid_service($serviceLocator)
    {
        $this->mockConfig($serviceLocator);
        $key = 'TokenListener';
        $this->canCreateServiceWithName($serviceLocator, $key, $key)->shouldReturn(true);
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function it_should_not_be_able_to_create_invalid_service($serviceLocator)
    {
        $this->mockConfig($serviceLocator);
        $key = 'NonExistingTokenListener';
        $this->canCreateServiceWithName($serviceLocator, $key, $key)->shouldReturn(false);
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function it_should_create_listener($serviceLocator)
    {
        $this->mockConfig($serviceLocator);
        $key = 'TokenListener';
        $this->createServiceWithName($serviceLocator, $key, $key)
            ->shouldReturnAnInstanceOf('\Phpro\MvcAuthToken\Listener\AuthenticationListener');
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function it_should_not_create_listener_on_invalid_adapter($serviceLocator)
    {
        $key = 'TokenListener';

        // Adapter key does not exist:
        $serviceLocator->has('Config')->willReturn(true);
        $serviceLocator->get('Config')->willReturn(['mvc-auth-token-authentication-listener' => ['TokenListener' => []]]);
        $this->shouldThrow('Phpro\MvcAuthToken\Exception\TokenException')->duringCreateServiceWithName($serviceLocator, $key, $key);

        // Adapter key does not exist
        $this->mockConfig($serviceLocator);
        $serviceLocator->has('TokenAdapter')->willReturn(false);
        $key = 'TokenListener';
        $this->shouldThrow('Phpro\MvcAuthToken\Exception\TokenException')->duringCreateServiceWithName($serviceLocator, $key, $key);

        // Invalid adapter type
        $serviceLocator->has('TokenAdapter')->willReturn(true);
        $serviceLocator->get('TokenAdapter')->willReturn(null);
        $this->shouldThrow('Phpro\MvcAuthToken\Exception\TokenException')->duringCreateServiceWithName($serviceLocator, $key, $key);
    }

}
