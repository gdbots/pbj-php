<?php
declare(strict_types=1);

require 'speed-bootstrap.php';

use Gdbots\Pbj\Serializer\JsonSerializer;

$startTime = microtime(true);
$i = 0;
$message = createEmailMessage();
$serializer = new JsonSerializer();
$serializer->skipValidation(true);

do {
    $i++;
    $json = $serializer->serialize($message);
    $message = $serializer->deserialize($json);
} while ($i < numTimes());

echo $serializer->serialize($message, ['json_encode_options' => JSON_PRETTY_PRINT]) . PHP_EOL;

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

