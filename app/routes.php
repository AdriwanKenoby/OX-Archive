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

$app->before(function (Request $request) use ($app) {
    $app['js_vars']->myGlobalVariable = 'globale value'; 
});

$app->get('/', "Archivage\Controller\HomeController::indexAction")
->bind('home');

$app->post('/', "Archivage\Controller\HomeController::indexAction")
->bind('search_by_period');

// Login form
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

$app->get('/search', "Archivage\Controller\HomeController::searchAction")
->bind('search_archive');

$app->post('/search', "Archivage\Controller\HomeController::searchAction")
->bind('result_search_archive');

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
