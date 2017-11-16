<?php

namespace Archivage\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Archivage\Form\Type\SearchArchiveType;

class HomeController {

    /**
     * User login controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function loginAction(Request $request, Application $app) {
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }

    public function searchAction(Request $request, Application $app) {
        $form = $app['form.factory']->create(SearchArchiveType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $documents = $app['search_engine']->search($form->getData()['query']);
            return $app['twig']->render('search.html.twig', array(
                'documents' => $documents,
            ));
        }
        return $app['twig']->render('search.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
}
