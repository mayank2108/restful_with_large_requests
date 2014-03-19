<?php

require_once __DIR__.'/../vendor/autoload.php';

define('HOST', 'localhost');
define('PORT', 5672);
define('USER', 'phpamqplib');
define('PASS', 'm');
define('VHOST', 'phpamqplib_testbed');

//If this is enabled you can see AMQP output on the CLI
define('AMQP_DEBUG', false);
