<?php

error_reporting(-1);
date_default_timezone_set('UTC');

// Ensure that composer has installed all dependencies
if (!file_exists(dirname(__DIR__) . '/composer.lock')) {
    die("Dependencies must be installed using composer:\n\nphp composer.phar install\n\n"
        . "See http://getcomposer.org for help with installing composer\n");
}

// Include the composer autoloader
$loader = require dirname(__DIR__) . '/vendor/autoload.php';

// auto registers the schema with the MessageResolver
// only done for tests or dynamic messages.
\Gdbots\Pbj\MessageResolver::setDefaultVendor('gdbots');
\Gdbots\Pbj\MessageResolver::setManifestDir(__DIR__ . '/Fixtures/manifests/');
\Gdbots\Tests\Pbj\Fixtures\EmailMessage::schema();
\Gdbots\Tests\Pbj\Fixtures\NestedMessage::schema();
\Gdbots\Tests\Pbj\Fixtures\MapsMessage::schema();
