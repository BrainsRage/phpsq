<?php
namespace phpsq;

use phpsq\exceptions\PhpSQException;

/**
 * Class Queue
 *
 * @property IStorage $storage
 *
 * @package phpsq
 */
class Queue
{
    private $name;
    private $inStoragePrefix = '';
    private $storage;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setInStoragePrefix($inStoragePrefix)
    {
        $this->inStoragePrefix = $inStoragePrefix;
    }

    private function getInStoragePrefix()
    {
        return mb_strlen($this->inStoragePrefix) > 0 ? $this->inStoragePrefix . '_' : '';
    }

    private function getInStorageKey()
    {
        return $this->getInStoragePrefix() . $this->getName();
    }

    public function setStorage(IStorage $storage)
    {
        $this->storage = $storage;
    }

    private function getStorage()
    {
        return $this->storage;
    }

    public function addAsyncTask(ITask $task)
    {
        $storedTask = new StoredTask($task);
        $taskAdded = $this->getStorage()->addTask($this->getInStorageKey(), $storedTask);
        if (!$taskAdded) {
            throw new PhpSQException("Add Task Error");
        }
        return $taskAdded;
    }

    public function addSyncTask(ITask $task)
    {
        $result = null;
        $resultKey = StringHelper::generateRandomKey();
        $storedTask = new StoredTask($task, $resultKey);
        $taskAdded = $this->getStorage()->addTask($this->getInStorageKey(), $storedTask);
        if ($taskAdded) {
            //waiting
        } else {
            throw new PhpSQException("Add Task Error");
        }
        return $result;
    }

    public function getTask()
    {
        return $this->getStorage()->getTask($this->getInStorageKey());
    }
}