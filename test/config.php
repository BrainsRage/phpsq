<?php

use phpsq\storages\RedisStorage;

return [
    'storages'             => [
        'redis' => [
            'class' => RedisStorage::class,
        ],
    ],
    'queues'               => [
        'storage' => 'redis',
        'list'    => [
            'test' => [
                'inStoragePrefix' => 'queue_redis',
                'storage'         => 'redis'
            ],
        ],
    ],
    'supervisorConfigPath' => '/etc/supervisor/conf.d/test.conf'
];