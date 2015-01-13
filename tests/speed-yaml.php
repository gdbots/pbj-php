<?php

require 'speed-bootstrap.php';

use Gdbots\Tests\Pbj\EmailMessage;
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
echo number_format(microtime(true) - $startTime, 6) . ' seconds' . PHP_EOL;
