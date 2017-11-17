<?php

namespace Archivage\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Archivage\Form\Type\SearchByPeriodType;
use Archivage\Form\Type\SearchArchiveType;
//use PHPFHIRGenerated\PHPFHIRResponseParser;

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

    /**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Request $request, Application $app) {
        $form = $app['form.factory']->create(SearchByPeriodType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $fhir_response = $app['mediboard_client']->get(
                'index.php?login='.$app['mediboard_login'].':'.$app['mediboard_pass'].
                '&m='.$app['mediboard_module'].
                '&tab='.$app['mediboard_tab_encounter'].
                '&date_min='.$data['date_min']->format('Y-m-d').
                '&date_max='.$data['date_max']->format('Y-m-d')
            );

            $fhir_str = (string)$fhir_response->getBody();
            $app['js_vars']->fhir = $fhir_str;
        }

        return $app['twig']->render('index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function searchAction(Request $request, Application $app) {
        $form = $app['form.factory']->create(SearchArchiveType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $documents = $app['search_engine']->search($form->getData()['query']);
            return $app['twig']->render('search.html.twig', array(
                'form' => $form->createView(),
                'documents' => $documents,
            ));
        }
        return $app['twig']->render('search.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function archiveAction(Request $request, Application $app) {
        if ($request->isXmlHttpRequest()) {
            $sejour_id = $request->request->get('sejour_id');

            $fhir_response = $app['mediboard_client']->get(
                'index.php?login='.$app['mediboard_login'].':'.$app['mediboard_pass'].
                '&m='.$app['mediboard_module'].
                '&tab='.$app['mediboard_tab_document_reference'].
                '&sejour_id='.$sejour_id
            );
            $fhir_str = (string)$fhir_response->getBody();
            //$parser = new PHPFHIRResponseParser();
            $fhir_object = json_decode($fhir_str);

            $resource =  $fhir_object->entry[0]->resource;
            $attachment = $resource->content[0]->attachment;
            $imageData = $attachment->data;
            $contentType = $attachment->contentType;
            $title = $resource->description;
            $hash = $attachment->hash;
            $encounter = $resource->context->encounter->reference;
            $patient = $resource->context->sourcePatientInfo->reference;

            $filepath = __DIR__.'/../../../archives/'.$title;

            if (file_exists($filepath)) {
                return new JsonResponse($title, 405);
            }

            file_put_contents($filepath, base64_decode($imageData));
            $token = $app['security.token_storage']->getToken();
            if (null !== $token) {
                $user = $token->getUser();
            }

            $app['monolog.prod']->info(sprintf("User '%s' has download archive %s.", $user->getUsername(), realpath($filepath)));
            $app['search_engine']->index($filepath, array(
                "hash" => $hash,
                "title" => $title,
                "encounter" => $encounter,
                "patient" => $patient
            ));

            return new JsonResponse($fhir_str);
        }
        return new Response("this is not an ajax request", 405);
    }

}
