<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Archivage\Entity\User;
use Archivage\Form\Type\UserType;

// On peut remplir notre classe app['js_vars'] avec des variables qui seront ensuite accssible en JS
$app->before(function (Request $request) use ($app) {
    // set some global javascript variable
    $app['js_vars']->archiveDirectory = $app['archive_directory'] ;
});

// On defini nos routes et on leur associe u controller i.e. une fonction charger de repondre a la requete
// Page d'acceuil
$app->get('/', "Archivage\Controller\HomeController::indexAction")
->bind('home');

// Recherche de sejour compris entre plusieur date
$app->post('/', "Archivage\Controller\HomeController::indexAction");

// page de login
$app->get('/login', "Archivage\Controller\HomeController::loginAction")
->bind('login');

// Admin home page
$app->get('/admin', "Archivage\Controller\AdminController::indexAction")
->bind('admin');

// Add a user
$app->match('/admin/user/add',"Archivage\Controller\AdminController::addUserAction")
->bind('admin_user_add');

// Edit an existing user
$app->match('/admin/user/{id}/edit', "Archivage\Controller\AdminController::editUserAction")
->bind('admin_user_edit');

// Remove a user
$app->get('/admin/user/{id}/delete', "Archivage\Controller\AdminController::deleteUserAction")
->bind('admin_user_delete');

// Acces au moteur de recherche
$app->get('/search', "Archivage\Controller\HomeController::searchAction")
->bind('search_archive');

// Reponse a une recherche
$app->post('/search', "Archivage\Controller\HomeController::searchAction");

// Appel ajax pour archivage a ce controller
$app->post('/archive', "Archivage\Controller\HomeController::archiveAction");

// Explorer l'arborescence du dossier d'archive
$app->get('/explore', "Archivage\Controller\HomeController::exploreAction")
->bind('explore_archive');

// differentes page selon le code HTTP en cas d'erreur
$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
