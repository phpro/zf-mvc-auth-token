<?php
return [
    'service_manager' => [
        'abstract_factories' => [
            'MvcAuthToken\Listener\AbstractAuthenticationListenerFactory',
        ],
        'invokables' => [
            'MvcAuthToken\TokenServer' => 'MvcAuthToken\TokenServer'
        ]
    ]
];