<?php

use phpsq\storages\RedisStorage;

return [
    'storage' => RedisStorage::class,
    'queues'  => ['test'],
];