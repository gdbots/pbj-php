<?php

require 'speed-bootstrap.php';

use Gdbots\Pbj\Serializer\YamlSerializer;

$startTime = microtime(true);
$i = 0;
$message = createEmailMessage();
$serializer = new YamlSerializer();

do {
    $i++;
    $yaml = $serializer->serialize($message);
    $message = $serializer->deserialize($yaml);
} while ($i < numTimes());

echo $serializer->serialize($message) . PHP_EOL;

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
