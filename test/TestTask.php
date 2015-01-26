<?php
namespace phpsq\test;

use phpsq\ITask;

class TestTask implements ITask
{
    public $message = 'no message';

    public function getQueueName()
    {
        return 'test';
    }

    public function perform()
    {
        $filename = '/tmp/test.log';
        $text = $this->message . PHP_EOL;
        if (($fp = @fopen($filename, 'a')) !== false) {
            @fwrite($fp, $text);
            @fclose($fp);
        }
        return true;
    }
}