<?php
namespace phpsq;

interface StorageInterface
{
    /**
     * @param            $inStorageQueueKey
     * @param StoredTask $task
     *
     * @return bool
     */
    public function addTask($inStorageQueueKey, StoredTask $task);

    /**
     * @param $inStorageQueueKey
     *
     * @return StoredTask
     */
    public function getTask($inStorageQueueKey);

    /**
     * @param StoredTask $task
     *
     * @return mixed
     */
    public function serializeTaskData(StoredTask $task);

    /**
     * @param $serializedTask
     *
     * @return StoredTask
     */
    public function unserializeTaskData($serializedTask);
}