<?php
/**
 * Created by PhpStorm.
 * User: soslow
 * Date: 19.01.2015
 * Time: 22:12
 */

use phpsq\PhpSQ;
use phpsq\test\TestTask;

require(__DIR__ . '/../vendor/autoload.php');

$config = require __DIR__ . '/config.php';
$configSimple = require __DIR__ . '/config_simple.php';
$phpsq = PhpSQ::getInstance($configSimple);

$task = new TestTask();
$task->message = 'hello';
$result = $phpsq->setAsyncTask($task);
var_dump($result);
