<?php

// configure your app for the production environment
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$app['twig.path'] = array(__DIR__.'/../views');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

$app['translator.domains'] = array();

$app['db.options'] = array(
  'driver'   => 'pdo_sqlite',
  'path'     => __DIR__.'/../app/app.db',
);

$app['security.firewalls'] = array(
  'login' => array(
      'pattern' => '^/login$',
  ),
  'secured' => array(
      'pattern' => '^.*$',
      'logout' => array('logout_path' => '/logout', 'invalidate_session' => true),
      'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
      'users' => function() use ($app) {
          return new Archivage\DAO\UserDAO($app['db']);
        }
  )
);

$app['security.role_hierachy'] = array(
  'ROLE_ADMIN' => array('ROLE_USER')
);

$app['security.access_rules'] = array(
  array('^/admin', 'ROLE_ADMIN')
);

$app['monolog.prod.logfile'] = __DIR__.'/../var/logs/silex_prod.log';
$app['monolog.prod'] = function ($app) {
    $log = new $app['monolog.logger.class']('prod');
    $handler = new StreamHandler($app['monolog.prod.logfile'], Logger::INFO);
    $log->pushHandler($handler);

    return $log;
};
