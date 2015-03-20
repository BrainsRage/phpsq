<?php
namespace phpsq\storages;

use phpsq\StorageInterface;
use phpsq\StoredTask;
use Redis;

/**
 * Class RedisStorage
 *
 * @property Redis $redis
 *
 * @package phpsq\storages
 */
class RedisStorage implements StorageInterface
{
    private $redis;

    public function __construct()
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6380);
        $redis->select(3);
        $this->redis = $redis;
    }

    public function addTask($inStorageQueueKey, StoredTask $task)
    {
        $taskDataString = $this->serializeTaskData($task);
        $result = $this->redis->lPush($inStorageQueueKey, $taskDataString);
        $result = boolval($result);
        return $result;
    }

    public function getTask($inStorageQueueKey)
    {
        $task = false;
        $taskDataString = $this->redis->rPop($inStorageQueueKey);
        if ($taskDataString !== false) {
            $task = $this->unserializeTaskData($taskDataString);
        }
        return $task;
    }

    public function serializeTaskData(StoredTask $task)
    {
        return serialize($task);
    }

    public function unserializeTaskData($serializedTask)
    {
        return unserialize($serializedTask);
    }
}