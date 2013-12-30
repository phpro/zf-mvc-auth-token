<?php
return [
    'service_manager' => [
        'abstract_factories' => [
            'MvcAuthToken\Listener\AbstractTokenValidationListenerFactory',
        ],
        'invokables' => [
            'MvcAuthToken\TokenServer' => 'MvcAuthToken\TokenServer'
        ]
    ]
];