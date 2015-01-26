<?php
namespace phpsq;

interface ITask
{
    /**
     * @return string
     */
    public function getQueueName();

    /**
     * @return mixed
     */
    public function perform();
}