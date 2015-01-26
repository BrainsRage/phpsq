<?php

namespace phpsq;

class Worker
{
    private $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    private function getQueue()
    {
        return $this->queue;
    }

    public function start()
    {
        while (true) {
            $storedTask = $this->getQueue()->getTask();
            if ($storedTask) {
                echo 'GET task';
                $task = $storedTask->getTask();
                try {
                    $result = $task->perform();
                    echo 'result ' . $result;
                    $resultKey = $storedTask->getResultKey();
                } catch (\Exception $ex) {
                    echo 'Error: ' . $ex->getMessage() . PHP_EOL;
                }
            } else {
                sleep(1);
            }
        }
    }
}