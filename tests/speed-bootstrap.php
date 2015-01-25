<?php

require 'bootstrap.php';

header('Content-Type: text/plain');

use Gdbots\Tests\Pbj\Fixtures\EmailMessage;

/**
 * @return EmailMessage
 */
function createEmailMessage() {
    $json = <<<JSON
{
    "_pbj": "gdbots:tests.pbj:fixtures:email-message:1-0-0",
    "from_name": "homer  ",
    "from_email": "homer@thesimpsons.com",
    "priority": 2,
    "sent": false,
    "date_sent": "2014-12-25T12:12:00.123456+00:00",
    "microtime_sent": "1422122017734617",
    "provider": "gmail",
    "labels": [
        "donuts",
        "mmmm",
        "chicken"
    ]
}
JSON;
    return EmailMessage::fromArray(json_decode($json, true));
}

function numTimes() {
    return 2500;
}