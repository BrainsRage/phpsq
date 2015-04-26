#!/usr/bin/env php
<?php
use phpsq\PhpSQ;

error_reporting(E_ALL);
ini_set("display_errors", 1);

require(__DIR__ . '/../vendor/autoload.php');

$config = require __DIR__ . '/config.php';
$configSimple = require __DIR__ . '/config_simple.php';
$phpsq = PhpSQ::getInstance($configSimple);
$worker = $phpsq->createWorker($argv);
$worker->start();