<?php
namespace phpsq;

class StoredTask
{
    private $task;
    private $resultKey;

    public function __construct(TaskInterface $task, $resultKey = null)
    {
        $this->task = $task;
        $this->resultKey = $resultKey;
    }

    public function getTask()
    {
        return $this->task;
    }

    public function getResultKey()
    {
        return $this->resultKey;
    }
}