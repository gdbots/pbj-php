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
$loader->add('Gdbots\\Tests', __DIR__);


/*
echo 'time            => ' . time() . PHP_EOL;
echo 'microtime()     => ' . microtime() . PHP_EOL;
echo 'microtime(true) => ' . microtime(true) . PHP_EOL;

while(true) {
    // echo 'id => ' . id() . PHP_EOL;
    $tod = gettimeofday();
    $uuidTime = ($tod['sec'] * 10000000) + ($tod['usec'] * 10) + 0x01b21dd213814000;
    echo 'uuidtime   => ' . $uuidTime . PHP_EOL;
    echo 'microtime  => ' . microtime() . PHP_EOL;
    echo 'microtime2 => ' . microtime(true) * 10000 . PHP_EOL . PHP_EOL;
    usleep(150000);
}
*/