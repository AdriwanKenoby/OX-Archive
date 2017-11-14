<?php

use Silex\Provider\WebProfilerServiceProvider;
use \Symfony\Component\Debug\ErrorHandler;
use \Symfony\Component\Debug\ExceptionHandler;

// include the prod configuration
require __DIR__.'/prod.php';

ErrorHandler::register();
ExceptionHandler::register();
// enable the debug mode
$app['debug'] = true;

$app['monolog.logfile'] = array(__DIR__.'/../var/logs/silex_dev.log');

$app->register(new WebProfilerServiceProvider(), array(
    'profiler.cache_dir' => __DIR__.'/../var/cache/profiler',
));
