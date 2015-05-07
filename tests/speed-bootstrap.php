<?php

require 'bootstrap.php';

header('Content-Type: text/plain');

use Gdbots\Pbj\Serializer\JsonSerializer;
use Gdbots\Tests\Pbj\Fixtures\EmailMessage;

/**
 * @return EmailMessage
 */
function createEmailMessage() {
    $json = file_get_contents(__DIR__ . '/Gdbots/Tests/Pbj/Fixtures/email-message.json');
    return (new JsonSerializer())->deserialize($json);
}

function numTimes() {
    return 2500;
}