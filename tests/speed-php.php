<?php

require 'speed-bootstrap.php';

use Gdbots\Tests\Pbj\Fixtures\EmailMessage;

$startTime = microtime(true);
$i = 0;
$message = createEmailMessage();
$array = $message->toArray();

do {
    $i++;
    $message = EmailMessage::fromArray($array);
    $str = serialize($message);
    $message = unserialize($str);
} while ($i < numTimes());

echo json_encode($message, JSON_PRETTY_PRINT) . PHP_EOL;

// speed report
$benchmark = microtime(true) - $startTime;
$seconds = number_format($benchmark, 6);
$totalMessages = numTimes();
$perSecond = floor($totalMessages / $benchmark);
$perMinute = $perSecond * 60;
$report = <<<STRING

Total Time:
    {$seconds} seconds

Messages Processed:
    {$totalMessages}

Rate:
    {$perSecond} messages / second
    {$perMinute} messages / minute


STRING;
echo $report;
