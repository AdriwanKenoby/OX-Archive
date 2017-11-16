<?php

namespace Archivage\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Archivage\Form\Type\SearchArchiveType;
use SearchEngine\EngineBuilder;

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

    /*
     *             $hash = $attachment->hash->value;
            $filepath = __DIR__.'/../../../archives/'.$title.'.'.$contentType;

            if (!file_exists($filepath)) {
                file_put_contents($filepath, base64_decode($imageData));

                $token = $app['security.token_storage']->getToken();
                if (null !== $token) {
                    $user = $token->getUser();
                }
                $app['monolog.prod']->info(sprintf("User '%s' has download archive %s.", $user->getUsername(), realpath($filepath)));

                EngineBuilder::create()->build()->index($filepath, array(
                    "hash" => $hash
                ));
                $app['session']->getFlashBag()->add('success', realpath($filepath).' store in FS and indexed');
            } else {
                $app['session']->getFlashBag()->add('error', $filepath.' already exist nothing done');
            }
     */

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
