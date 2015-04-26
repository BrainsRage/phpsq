<?php
use phpsq\test\TestTask;

/**
 * Created by PhpStorm.
 * User: soslow
 * Date: 26.04.2015
 * Time: 21:10
 */

require(__DIR__ . '/../vendor/autoload.php');

function getAttributes($params, $allowed)
{
    $attributes = [];
    foreach ($params as $i => $param) {
        if ($i == 0) {
            continue;
        }
        list($name, $value) = explode('=', $param);
        $attributes[$name] = $value;
    }

    return $attributes;
}

$allowed = [

    'taskKey' => true,
    'message' => true,

];

var_dump($argv);

$attributes = getAttributes($argv, $allowed);

/**
 * @var TestTask $task
 */

echo $attributes['taskKey'];
$task = new $attributes['taskKey']();
$task->message = $attributes['message'];
$task->perform();
