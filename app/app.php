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

$app = new Application();
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

$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...
    return $twig;
});

$app['dao.user'] = function ($app) {
    return new Archivage\DAO\UserDAO($app['db']);
};

$app['server_fhir_uri'] = 'https://fhirtest.uhn.ca/baseDstu3/';
$app['fhir_client'] = new Client([
// Base URI is used with relative requests
'base_uri' => $app['server_fhir_uri']
]);

$app['elastic.host'] = "localhost";
$app['elastic.port'] = 9200;
$app['search_engine.index'] = "ox-archive";
$app['search_engine.type'] = "document";

$app['search_engine'] = EngineBuilder::create()->build(
    $app['elastic.host'],
    $app['elastic.port'],
    $app['search_engine.index'],
    $app['search_engine.type']
);

return $app;
