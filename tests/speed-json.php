<?php

require 'speed-bootstrap.php';

use Gdbots\Tests\Pbj\EmailMessage;

$startTime = microtime(true);
$i = 0;
$message = createEmailMessage();

do {
    $i++;
    $str = json_encode($message);
    $message = EmailMessage::fromArray(json_decode($str, true));
} while ($i < numTimes());

echo json_encode($message, JSON_PRETTY_PRINT) . PHP_EOL;
echo number_format(microtime(true) - $startTime, 6) . ' seconds' . PHP_EOL;
