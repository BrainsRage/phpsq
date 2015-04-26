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
                if ($task = $storedTask->getTask()) {
                    try {
                        $this->executeCommand($task);
                    } catch (\Exception $ex) {
                        echo 'Error: ' . $ex->getMessage() . PHP_EOL;
                        $this->getQueue()->addAsyncTask($task);
                    }
                }
            } else {
                sleep(1);
            }
        }
    }

    public function executeCommand(TaskInterface $task)
    {
        $command = $task->getCommand();
        if ($logFile = $task->getLogFile()) {
            $command .= ' > ' . $logFile . ' 2>&1';
        }
        echo 'Exec ' . $command . "\n";
        $code = 0;
        $return = [];
        exec(
            $command,
            $return,
            $code
        );
        echo 'Done. Code: ' . $code . '. Return: ' . json_encode($return) . "\n";
        if ($logFile) {
            switch ($code) {
                case 0:
                    echo 'Code is ok, delete logfile ' . $command . "\n";
                    unlink($logFile);
                    break;
                default:
                    break;
            }
        }
    }
}