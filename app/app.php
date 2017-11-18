<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\MonologServiceProvider;
use GuzzleHttp\Client;
use SearchEngine\EngineBuilder;

// Nouvelle applcation Silex
$app = new Application();

// On charge different composant
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new LocaleServiceProvider());
$app->register(new TranslationServiceProvider());
$app->register(new DoctrineServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new SecurityServiceProvider());
$app->register(new MonologServiceProvider());
// Cette classe est un peut "fourre-tout" pour passer des variables nommée du php a twig pour être insérer dans le DOM puis utilisee en javascript
$app['js_vars'] = new stdClass;
// On defini le service twig avec une varible globale qui fais reference a la classe defini precedemment
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...
    $twig->addGlobal('js_vars', $app['js_vars']);
    return $twig;
});
// Ce service qui fais le lien entre notre class user et la base de donnée
$app['dao.user'] = function ($app) {
    return new Archivage\DAO\UserDAO($app['db']);
};

// Vous pouvez configurer l'applcation
$app['archive_directory'] = realpath(__DIR__.'/../archives');
// l'adresse dns de mediboard
$app['mediboard_host'] = "http://192.168.1.21/mediboard/";
// login mediboard
$app['mediboard_login'] = "univlr";
// mot de passe (oups faut bien le mettre quelque part)
$app['mediboard_pass'] = "lrUniv17";
// le module mediboard charge de repondre au client
$app['mediboard_module'] = "tp1_mjahed_veteau";
// Le script charger de repondre a la premiere requete (cote mediboard)
$app['mediboard_tab_encounter'] = "Encounter";
// Le second (on pourrais imaginer un controler hybride qui repond differement selon les params envoyes, plus difficile a maintenir )
$app['mediboard_tab_document_reference'] = "DocumentReference";
// On cree le client guzzle pour interroger mediboard
$app['mediboard_client'] = new Client([
    // Base URI is used with relative requests
    'base_uri' => $app['mediboard_host']
]);

// elasticsearch configuration
$app['elastic.host'] = "localhost";
$app['elastic.port'] = 9200;
// Index et type utiliser (rtfm elasticsearch)
$app['search_engine.index'] = "ox-archive";
$app['search_engine.type'] = "document";

// Ici on cree un service moteur de recherche associe a une instance d'elasticsearch
$app['search_engine'] = EngineBuilder::create()->build(
    $app['elastic.host'],
    $app['elastic.port'],
    $app['search_engine.index'],
    $app['search_engine.type']
);

return $app;
