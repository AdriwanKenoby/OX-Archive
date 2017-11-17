<?php

// configure your app for the production environment
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// On defini le repertoire de reference pour nos templates twig
$app['twig.path'] = array(__DIR__.'/../views');
// mise en cahe
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

// Pour l'internationalisation
// Necessaire pour utiliser avec SymfonyValidator (validation de donnees issu de formulaire)
$app['translator.domains'] = array();

// configuration d'acces a une base de donnees
$app['db.options'] = array(
  'driver'   => 'pdo_sqlite',
  'path'     => __DIR__.'/../app/app.db',
);

// Par feu seul les utilisateurs enregstrer en BDD peuvent se connecter sinon redirection vers login
$app['security.firewalls'] = array(
  'login' => array(
      'pattern' => '^/login$',
  ),
  // la route check_pass est generee automatiquement par le composant symfony associee
  'secured' => array(
      'pattern' => '^.*$',
      'logout' => array('logout_path' => '/logout', 'invalidate_session' => true),
      'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
      'users' => function() use ($app) {
          return new Archivage\DAO\UserDAO($app['db']);
        }
  )
);
// On defini une hierachie de role
$app['security.role_hierachy'] = array(
  'ROLE_ADMIN' => array('ROLE_USER')
);
// une regle de securite pour acceder a l'espace admin
$app['security.access_rules'] = array(
  array('^/admin', 'ROLE_ADMIN')
);
// un autre channel que celui defini en dev, pour ne looger que des info au sujet des requetes effectuee par les utilisateurs connectes
$app['monolog.prod.logfile'] = __DIR__.'/../var/logs/silex_prod.log';
$app['monolog.prod'] = function ($app) {
    $log = new $app['monolog.logger.class']('prod');
    $handler = new StreamHandler($app['monolog.prod.logfile'], Logger::INFO);
    $log->pushHandler($handler);

    return $log;
};
