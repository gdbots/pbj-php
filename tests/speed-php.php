<?php

require 'speed-bootstrap.php';

use Gdbots\Pbj\Serializer\PhpSerializer;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;

$startTime = microtime(true);
$i = 0;
$message = createEmailMessage();
$serializer = new PhpSerializer();
$array = $message->toArray();

do {
    $i++;
    $message = EmailMessage::fromArray($array);
    $str = $serializer->serialize($message);
    $message = $serializer->deserialize($str);
} while ($i < numTimes());

echo $serializer->serialize($message) . PHP_EOL;

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
