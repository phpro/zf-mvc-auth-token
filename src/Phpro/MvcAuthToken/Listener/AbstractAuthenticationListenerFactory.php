<?php

namespace Phpro\MvcAuthToken\Listener;


use Phpro\MvcAuthToken\Adapter\AdapterInterface;
use Phpro\MvcAuthToken\Exception\TokenException;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractTokenValidationListenerFactory
 *
 * @package Phpro\MvcAuthToken\Listener
 */
class AbstractAuthenticationListenerFactory implements AbstractFactoryInterface
{

    const FACTORY_NAMESPACE = 'mvc-auth-token-authentication-listener';

    /**
     * Cache of canCreateServiceWithName lookups
     * @var array
     */
    protected $lookupCache = array();

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (array_key_exists($requestedName, $this->lookupCache)) {
            return $this->lookupCache[$requestedName];
        }

        if (!$serviceLocator->has('Config')) {
            return false;
        }

        // Validate object is set
        $config = $serviceLocator->get('Config');
        $namespace = self::FACTORY_NAMESPACE;
        if (!isset($config[$namespace]) || !is_array($config[$namespace]) || !isset($config[$namespace][$requestedName])) {
            $this->lookupCache[$requestedName] = false;
            return false;
        }

        $this->lookupCache[$requestedName] = true;
        return true;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return mixed|AuthenticationListener
     * @throws \Phpro\MvcAuthToken\Exception\TokenException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('Config');
        $config = $config[self::FACTORY_NAMESPACE][$requestedName];

        if (!isset($config['adapter'])) {
            throw new TokenException('No adapter configured for the current token authentication adapter.');
        }

        $adapterClass = $config['adapter'];
        if (!$serviceLocator->has($adapterClass)) {
            throw new TokenException(sprintf('The token adapter %s could not be found in the servicelocator.', $adapterClass));
        }

        $adapter = $serviceLocator->get($adapterClass);
        if (!$adapter instanceof AdapterInterface) {
            throw new TokenException(sprintf('The token adapter of %s should implement AdapterInterface.', $adapterClass));
        }

        $tokenServer = $serviceLocator->get('Phpro\MvcAuthToken\TokenServer');

        $listener = new AuthenticationListener();
        $listener->setAdapter($adapter);
        $listener->setTokenServer($tokenServer);

        return $listener;
    }

}
