<?php
namespace phpsq;

interface TaskInterface
{
    /**
     * @return string
     */
    public function getQueueName();

    /**
     * @return mixed
     */
    public function perform();

    /**
     * @return mixed
     */
    public function getCommand();

    /**
     * @return mixed
     */
    public function getLogFile();
}