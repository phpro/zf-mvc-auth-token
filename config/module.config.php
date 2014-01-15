<?php
return [
    'service_manager' => [
        'abstract_factories' => [
            'Phpro\MvcAuthToken\Listener\AbstractAuthenticationListenerFactory',
        ],
        'invokables' => [
            'Phpro\MvcAuthToken\TokenServer' => 'Phpro\MvcAuthToken\TokenServer'
        ]
    ]
];