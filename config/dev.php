<?php

use Silex\Provider\WebProfilerServiceProvider;
use Silex\Provider\VarDumperServiceProvider;
use \Symfony\Component\Debug\ErrorHandler;
use \Symfony\Component\Debug\ExceptionHandler;

// include the prod configuration
require __DIR__.'/prod.php';

ErrorHandler::register();
ExceptionHandler::register();
// enable the debug mode
$app['debug'] = true;

// un logger des evenement recu par le kernel, peut devenir tres gros ... utile en dev
$app['monolog.logfile'] = __DIR__.'/../var/logs/silex_dev.log';

$app->register(new WebProfilerServiceProvider(), array(
    'profiler.cache_dir' => __DIR__.'/../var/cache/profiler',
));

// var dumper mais ca ne fonctionne pas ... doo
$app->register(new VarDumperServiceProvider());
