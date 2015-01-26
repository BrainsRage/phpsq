#!/usr/bin/env php
<?php
use phpsq\PhpSQ;

require __DIR__ . '/../vendor/autoload.php';

$phpsq = PhpSQ::getInstance(__DIR__ . '/config.php');
$worker = $phpsq->createWorker($argv);
$worker->start();