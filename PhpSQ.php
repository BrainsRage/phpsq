<?php
namespace phpsq;

use phpsq\exceptions\PhpSQException;

/**
 * Class PHPSQ
 *
 * @property IStorage[] $storages
 * @property Queue[]    $queues
 *
 * @package phpsq
 */
class PhpSQ
{
    private $storages = [];
    private $queues = [];
    private $isInitialized = false;

    public function setAsyncTask(ITask $task)
    {
        $queue = $this->getQueue($task->getQueueName());
        return $queue->addAsyncTask($task);
    }

    public function setSyncTask(ITask $task)
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

    public static function getInstance($configFile = null)
    {    // Возвращает единственный экземпляр класса. @return Singleton
        if (empty(self::$instance)) {
            $instance = new static();
            if ($configFile) {
                $instance->init($configFile);
            }
            self::$instance = $instance;
        }
        return self::$instance;
    }

    // </editor-fold>

    // <editor-fold desc="Init stuff">

    public function init($configFile)
    {
        if (!$this->getIsInitialized()) {
            if (file_exists($configFile)) {
                $config = require($configFile);
                $this->setConfig($config);
                $this->setIsInitialized(true);
            } else {
                throw new PhpSQException('Config file not exist');
            }
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
        if (isset($config['storages'])) {
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

                if (isset($config['queues'])) {
                    $queues = $config['queues'];
                    $defaultStorage = null;
                    if ($queues) {
                        if (isset($queues['storage'])) {
                            $defaultQueuesStorageName = $queues['storage'];
                            $defaultStorage = $this->getStorageByName($defaultQueuesStorageName);
                        }
                        if (isset($queues['list'])) {
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
        } else {
            throw new PhpSQException('No storages param in config');
        }
    }

    private function addStorage($name, IStorage $storage)
    {
        $this->storages[$name] = $storage;
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