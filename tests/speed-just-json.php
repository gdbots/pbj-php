<?php

require 'speed-bootstrap.php';

$startTime = microtime(true);
$i = 0;
$message = json_decode(file_get_contents(__DIR__ . '/Fixtures/email-message.json'), true);

do {
    $i++;
    $json = json_encode($message);
    $message = json_decode($json, true);
    \Gdbots\Tests\Pbj\Fixtures\EmailMessage::create();
} while ($i < numTimes());

echo json_encode($message, JSON_PRETTY_PRINT) . PHP_EOL;

// speed report
$benchmark = microtime(true) - $startTime;
$seconds = number_format($benchmark, 6);
$totalMessages = numTimes();
$perSecond = floor($totalMessages / $benchmark);
$perMinute = $perSecond * 60;
$perHour = $perMinute * 60;

$perSecond = number_format($perSecond);
$perMinute = number_format($perMinute);
$perHour = number_format($perHour);

$report = <<<STRING

Total Time:
    {$seconds} seconds

Messages Processed:
    {$totalMessages}

Rate:
    {$perSecond} messages / second
    {$perMinute} messages / minute
    {$perHour} messages / hour


STRING;
echo $report;

