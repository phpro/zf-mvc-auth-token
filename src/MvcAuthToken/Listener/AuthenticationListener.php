<?php

namespace MvcAuthToken\Listener;

use MvcAuthToken\Adapter\AdapterInterface;
use MvcAuthToken\Exception\TokenException;
use MvcAuthToken\TokenServer;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use Zend\Http\Request as HttpRequest;
use ZF\MvcAuth\Identity;
use ZF\MvcAuth\MvcAuthEvent;

/**
 * Class AuthenticationListener
 *
 * @package MvcAuthToken\Listener
 */
class AuthenticationListener
{

    /**
     * @var array
     */
    protected $methodsWithoutHash = [
        'HEAD',
        'OPTIONS',
    ];

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var TokenServer
     */
    protected $tokenServer;

    /**
     * @param mixed $adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return mixed
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param \MvcAuthToken\TokenServer $tokenServer
     */
    public function setTokenServer($tokenServer)
    {
        $this->tokenServer = $tokenServer;
    }

    /**
     * @return \MvcAuthToken\TokenServer
     */
    public function getTokenServer()
    {
        return $this->tokenServer;
    }

    /**
     * @param MvcAuthEvent $mvcAuthEvent
     *
     * @return null|Identity\IdentityInterface
     */
    public function __invoke(MvcAuthEvent $mvcAuthEvent)
    {
        $mvcEvent = $mvcAuthEvent->getMvcEvent();
        $request = $mvcEvent->getRequest();
        if (!$request instanceof HttpRequest) {
            return;
        }

        if (in_array($request->getMethod(), $this->methodsWithoutHash)) {
            return;
        }

        $response = $mvcEvent->getResponse();
        $adapter = $this->getAdapter();

        // configure tokenServer
        $tokenServer = $this->getTokenServer();
        $tokenServer->setAdapter($adapter);
        $tokenServer->setRequest($request);
        $tokenServer->setResponse($response);

        try {
            if ($tokenServer->authenticate()) {

                // Use given identity
                $user = $tokenServer->getUserId();
                if ($user instanceof Identity\IdentityInterface) {
                    return $user;
                }

                // Create identity
                $identity = new Identity\AuthenticatedIdentity($user);
                $identity->setName($user);
                return $identity;
            }
        } catch (TokenException $e) {
            // let's make it a guest
        }

        return new Identity\GuestIdentity();
    }

}
