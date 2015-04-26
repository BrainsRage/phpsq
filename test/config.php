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
                'storage'         => 'redis',
                'numberProcess'   => 5,
                'outFile'         => '/var/www/phpsq/test/%(program_name)s_%(process_num)03d_out',
                'errorFile'       => '/var/www/phpsq/test/%(program_name)s_%(process_num)03d_err'
            ],
        ],
    ],
    'supervisorConfigPath' => '/etc/supervisor/conf.d/test.conf'
];