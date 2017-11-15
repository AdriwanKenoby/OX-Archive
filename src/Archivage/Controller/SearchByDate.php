<?php

namespace Archivage\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Archivage\Form\Type\SearchByPeriodType;

class SearchByDate {

    /**
     * Search by dates controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Request $request, Application $app) {

        //$form = $app['form.factory']->create(SearchByPeriodType::class);
        //$form->handleRequest($request);

        /*if ($form->isSubmitted() && $form->isValid()) {

            $fhir_response = $app['fhir_client']->get('Encounter?_count=10');
            $object = json_decode($fhir_response->getBody());

            return $app['twig']->render('index.html.twig', array(
                'fhir' => $object
            ));
        }*/

        /*return $app['twig']->render('index.html.twig', array(
            'form' => $form->createView()
        ));*/

        return $app['twig']->render('index.html.twig', array(
            //'form' => $form->createView()
        ));
    }
}