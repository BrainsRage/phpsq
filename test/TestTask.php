<?php
namespace phpsq\test;

use phpsq\TaskInterface;

class TestTask implements TaskInterface
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

    public function getCommand()
    {
        return 'php /var/www/phpsq/test/command.php taskKey="' . self::class . '" message=' . $this->message;
    }

    public function getLogFile()
    {
        return '/tmp/out.log';
    }
}