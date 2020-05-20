<?php

require 'speed-bootstrap.php';

use \Gdbots\Pbj\Marshaler\Elastica\DocumentMarshaler;

$startTime = microtime(true);
$i = 0;
$message = createEmailMessage();
$marshaler = new DocumentMarshaler();
//$marshaler->skipValidation(true);

do {
    $i++;
    $document = $marshaler->marshal($message);
    $message = $marshaler->unmarshal($document);
} while ($i < numTimes());

echo json_encode($marshaler->marshal($message)->toArray(), JSON_PRETTY_PRINT) . PHP_EOL;

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

