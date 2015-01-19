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
    "from_name": "homer  ",
    "from_email": "homer@thesimpsons.com",
    "priority": 2,
    "sent": false,
    "date_sent": "2014-12-25",
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

function numTimes()
{
    return 2500;
}