# Mvc-Auth-Token implementation for zend framework 2
This module will take care of HTTP Token authentication as described in the [draft-hammer-http-token-auth-01](http://tools.ietf.org/html/draft-hammer-http-token-auth-01). It should be used with [zf-mvc-auth](https://github.com/zfcampus/zf-mvc-auth). 

The purpose of this module is to provide an extendable interface for validating Tokens. The validation of the Token should be done in a custom adapter.

## Installation
```
curl -s https://getcomposer.org/installer | php
php composer.phar install
```

## Module Installation

### Add to composer.json
```
"phpro/zf2-mvc-auth-token": "dev-master"
```

### Add module to application.config.php
```php
return array(
    'modules' => array(
        'MvcAuthToken',
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

### Add a TokenAdapter to your Module. 
e.g. `YourModule\Authentication\Adapter\TokenAdapter`

``` php
class YourModule\Authentication\Adapter\TokenAdapter 
    implements MvcAuthToken\Adapter\AdapterInterface
{
    // Implement your own Token Adapter logica
}
```

### Add a new listener in your Module::onBootstrap

*Note:* Make sure that the priority is above the current zf-mvc-auth authentication priority.
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

### How to retrieve the authenticated user?
```php
/** @var \Zend\Authentication\AuthenticationService $authentication */
$authentication = $serviceLocator->get('authentication');
$identity = $authentication->getIdentity();
```

