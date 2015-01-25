<?php

require 'speed-bootstrap.php';

use Gdbots\Tests\Pbj\Fixtures\EmailMessage;
use Symfony\Component\Yaml\Yaml;

$startTime = microtime(true);
$i = 0;
$message = createEmailMessage();

do {
    $i++;
    $str = Yaml::dump($message->toArray());
    $message = EmailMessage::fromArray(Yaml::parse($str));
} while ($i < numTimes());

echo Yaml::dump($message->toArray()) . PHP_EOL;

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
