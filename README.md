> ## Repository abandoned 2020-11-27
>
> This repository has been archived since we are not using it anymore internally.
> Feel free to use it AS-IS, we won't be providing any support anymore.

# Mvc-Auth-Token implementation for zend framework 2
This module will take care of HTTP Token authentication as described in the [draft-hammer-http-token-auth-01](http://tools.ietf.org/html/draft-hammer-http-token-auth-01). It should be used with [zf-mvc-auth](https://github.com/zfcampus/zf-mvc-auth). 

The purpose of this module is to provide an extendable interface for validating Tokens. The validation of the Token should be done in a custom adapter.

## Query params
In some situations, it is not possible to add the Authentication header to the request.
Therefor the token parameters can also be added as query parameters:

```
http://yourserver.local/endpoint
    ?token[realm]=realm
    &token[token]=token
    &token[coverage]=coverage
    &token[nonce]=nonce
    &token[timestamp]=timestamp
    &token[auth]=auth
```

*Note*: This part of the authentication is not in the official draft, but could be useful in some situations.


# Installation
```
curl -s https://getcomposer.org/installer | php
php composer.phar install
```

## Module Installation

### Add to composer.json
```
"phpro/zf-mvc-auth-token": "dev-master"
```

### Add module to application.config.php
```php
return array(
    'modules' => array(
        'Phpro\MvcAuthToken',
        // other libs...
    ),
    // Other config
);
```

### Add a new listener and adapter in your module 'module.config.php'
```php
return array(
    'service_manager' => array(
        'invokables' => array(
            'YourModule\Authentication\Adapter\TokenAdapter' => 'YourModule\Authentication\Adapter\TokenAdapter',
        )
    ),
    'mvc-auth-token-authentication-listener' => array(
        'YourModule\Authentication\Listener\TokenListener' => array(
            'adapter' => 'YourModule\Authentication\Adapter\TokenAdapter',
        ),
    ),
);
```

*Note: * The listener is not an actual class.
The AbstractAuthenticationListener will create an AuthenticationListener for you, which is configured with your custom adapter.

### Add a TokenAdapter class to your Module.
e.g. `YourModule\Authentication\Adapter\TokenAdapter`

This custom class will implement the AdapterInterface and should be used to validate your token:


``` php
class YourModule\Authentication\Adapter\TokenAdapter 
    implements \Phpro\MvcAuthToken\Adapter\AdapterInterface
{
    // Implement your own Token Adapter logica
}
```

### Add a new listener in your Module::onBootstrap

Now the last step is to add your configured AuthenticationListener to the MvcAuthEvent.
When the Authentication event is triggered, your listener will handle Token Authorization.

```php
/**
 * @param MvcEvent $e
 */
public function onBootstrap(MvcEvent $e)
{
    $app      = $e->getApplication();
    $events   = $app->getEventManager();
    $services = $app->getServiceManager();

    $events->attach(MvcAuthEvent::EVENT_AUTHENTICATION, $services->get('YourModule\Authentication\Listener\TokenListener'), 1000);
}
```

*Note:* Make sure that the priority is above the current zf-mvc-auth authentication priority.

### How to retrieve the authenticated user?
```php
/** @var \Zend\Authentication\AuthenticationService $authentication */
$authentication = $serviceLocator->get('authentication');
$identity = $authentication->getIdentity();
```

