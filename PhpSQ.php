<?php
namespace phpsq;

use phpsq\exceptions\PhpSQException;

/**
 * Class PHPSQ
 *
 * @property StorageInterface[] $storages
 * @property Queue[]            $queues
 *
 * @package phpsq
 */
class PhpSQ
{
    const DEFAULT_STORAGE_NAME = 'default';

    private $storages = [];
    private $queues = [];
    private $isInitialized = false;

    public function setAsyncTask(TaskInterface $task)
    {
        $queue = $this->getQueue($task->getQueueName());
        return $queue->addAsyncTask($task);
    }

    public function setSyncTask(TaskInterface $task)
    {
        $queue = $this->getQueue($task->getQueueName());
        $result = $queue->addSyncTask($task);
        return $result;
    }

    public function createWorker($argv)
    {
        $queue = $this->getQueueFromConsoleArgs($argv);
        return new Worker($queue);
    }

    private function getQueueFromConsoleArgs($argv)
    {
        $queueName = null;
        foreach ($argv as $i => $param) {
            if ($i == 0) {
                continue;
            }
            list($key, $value) = explode('=', $param);
            if ($key == "queue") {
                $queueName = $value;
                break;
            }
        }
        if ($queueName) {
            return $this->getQueue($queueName);
        } else {
            throw new PhpSQException("No queue");
        }
    }

    private function getQueue($name)
    {
        if (isset($this->queues[$name])) {
            return $this->queues[$name];
        } else {
            throw new PhpSQException("Undefined queue - {$name}");
        }
    }

    // <editor-fold desc="Singleton stuff">

    private static $instance;  // экземпляра объекта

    private function __construct()
    { /* ... @return Singleton */
    }  // Защищаем от создания через new Singleton

    private function __clone()
    { /* ... @return Singleton */
    }  // Защищаем от создания через клонирование

    private function __wakeup()
    { /* ... @return Singleton */
    }  // Защищаем от создания через unserialize

    public static function getInstance($config = null)
    {    // Возвращает единственный экземпляр класса. @return Singleton
        if (empty(self::$instance)) {
            $instance = new static();
            if ($config) {
                $instance->init($config);
            }
            self::$instance = $instance;
        }
        return self::$instance;
    }

    // </editor-fold>

    // <editor-fold desc="Init stuff">

    public function init($config)
    {
        if (!$this->getIsInitialized()) {
            $this->setConfig($config);
            $this->setIsInitialized(true);
        } else {
            throw new PhpSQException('PhpSQ has been initialized already');
        }
    }

    private function getIsInitialized()
    {
        return $this->isInitialized;
    }

    private function setIsInitialized($value)
    {
        return $this->isInitialized = $value;
    }

    private function setConfig(array $config)
    {
        if(isset($config['supervisorConfigPath'])){

        }

        if (isset($config['storage']) && is_string($config['storage'])) {
            //single storage
            $storageClass = $config['storage'];
            $storage = new $storageClass;
            $this->addStorage(self::DEFAULT_STORAGE_NAME, $storage);
        } elseif (isset($config['storages'])) {
            $storages = $config['storages'];
            if ($storages) {
                foreach ($storages as $name => $storageConfig) {
                    if (isset($storageConfig['class'])) {
                        $storageClass = $storageConfig['class'];
                        $storage = new $storageClass;
                        $this->addStorage($name, $storage);
                    } else {
                        throw new PhpSQException("No class for {$name}-storage config");
                    }
                }
            }
        } else {
            throw new PhpSQException('No storages param in config');
        }

        if ($this->hasStorages()) {
            if (isset($config['queues'])) {
                $queues = $config['queues'];
                $defaultStorage = null;
                if ($queues) {
                    //just queues names array
                    if (!isset($queues['list'])) {
                        foreach ($queues as $name) {
                            $queue = new Queue($name);
                            $queueStorage = $this->getStorageByName(self::DEFAULT_STORAGE_NAME);
                            $queue->setStorage($queueStorage);
                            $this->addQueue($name, $queue);
                        }
                    } elseif ($queues['list']) {

                        if (isset($queues['storage'])) {
                            $defaultQueuesStorageName = $queues['storage'];
                            $defaultStorage = $this->getStorageByName($defaultQueuesStorageName);
                        }

                        $queues = $queues['list'];
                        if ($queues) {
                            foreach ($queues as $name => $queueConfig) {
                                $queue = new Queue($name);
                                if (isset($queueConfig['inStoragePrefix'])) {
                                    $inStoragePrefix = $queueConfig['inStoragePrefix'];
                                    $queue->setInStoragePrefix($inStoragePrefix);
                                }
                                $queueStorage = null;
                                if (isset($queueConfig['storage'])) {
                                    $storageName = $queueConfig['storage'];
                                    $queueStorage = $this->getStorageByName($storageName);
                                } else {
                                    $queueStorage = $defaultStorage;
                                }
                                if ($queueStorage) {
                                    $queue->setStorage($queueStorage);
                                    $this->addQueue($name, $queue);
                                } else {
                                    throw new PhpSQException('Queue storage must be set');
                                }
                            }
                        } else {
                            throw new PhpSQException('No queues');
                        }
                    } else {
                        throw new PhpSQException('No queues list');
                    }
                } else {
                    throw new PhpSQException('No queues config');
                }
            } else {
                throw new PhpSQException('No queues param in config');
            }
        } else {
            throw new PhpSQException('No storages');
        }
    }

    private function addStorage($name, StorageInterface $storage)
    {
        $this->storages[$name] = $storage;
    }

    private function hasStorages()
    {
        return count($this->storages) > 0;
    }

    private function addQueue($name, Queue $queue)
    {
        $this->queues[$name] = $queue;
    }

    private function getStorageByName($name)
    {
        if (isset($this->storages[$name])) {
            return $this->storages[$name];
        } else {
            throw new PhpSQException("Undefined storage - {$name}");
        }
    }

    // </editor-fold>
}