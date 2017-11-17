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
        // On revoi notre template avec les params necessaire au par feu
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }

    /**
     * Home page controller.
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function indexAction(Request $request, Application $app) {
        // On cree un formulaire a partir de la classe (le Type) defini dans Form/Type
        $form = $app['form.factory']->create(SearchByPeriodType::class);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valid
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // On interroge mediboard
            $fhir_response = $app['mediboard_client']->get(
                'index.php?login='.$app['mediboard_login'].':'.$app['mediboard_pass'].
                '&m='.$app['mediboard_module'].
                '&tab='.$app['mediboard_tab_encounter'].
                '&date_min='.$data['date_min']->format('Y-m-d').
                '&date_max='.$data['date_max']->format('Y-m-d')
            );

            // on recupere la reponse
            $fhir_str = (string)$fhir_response->getBody();
            // On defini une variable accessible depuis le JS pour affichage
            // On pourais deja imaginer un traitement pour les sejours deja archiver ET terminer
            // i.e ceux qui ne changeront plus pour les retirer de suite de la grid
            $app['js_vars']->fhir = $fhir_str;
        }

        // On renvoi notre template avec une variable form
        return $app['twig']->render('index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Moteur de recherche
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function searchAction(Request $request, Application $app) {
        $form = $app['form.factory']->create(SearchArchiveType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // On recupere les documents associes a une recherche
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

  /**
   * Appel ajax sur la grid.
   * @param Request $request Incoming request
   * @param Application $app Silex application
   */
    public function archiveAction(Request $request, Application $app) {
        // On verifie que l'on a affaire a une requete AJAX
        if ($request->isXmlHttpRequest()) {
            $post = array(
                'sejour_id' => $request->request->get('sejour_id'),
                'patient_name' => $request->request->get('patient_name')
            );

            // On interroge Mediboard
            $fhir_response = $app['mediboard_client']->get(
                'index.php?login='.$app['mediboard_login'].':'.$app['mediboard_pass'].
                '&m='.$app['mediboard_module'].
                '&tab='.$app['mediboard_tab_document_reference'].
                '&sejour_id='.$post['sejour_id']
            );

            $fhir_str = (string)$fhir_response->getBody();
            // Il est possible d'utiliser un parser mais la navigation dans l'object est + complique
            // Cela n'apportais pas grand interet pour le projet nous nous contenteront d'un json_decode
            //$parser = new PHPFHIRResponseParser();
            $fhir_object = json_decode($fhir_str);

            // Reucperation de donnees
            $resource =  $fhir_object->entry[0]->resource;
            $attachment = $resource->content[0]->attachment;
            $imageData = $attachment->data;
            $contentType = $attachment->contentType;
            $title = $resource->description;
            $hash = $attachment->hash;
            $encounter = $resource->context->encounter->reference;
            $patient = $resource->context->sourcePatientInfo->reference;

            $filepath = $app['archive_directory'].'/'.$title;

            // Si le fichier existe on renvoi une erreur avec le nom du fichier
            // On pourrait ajouter le path
            if (file_exists($filepath)) {
                return new JsonResponse($title, 405);
            }

            // Sinon on creer le fichier
            file_put_contents($filepath, base64_decode($imageData));
            // Recuperation de l'utilsateur en cours de session
            $token = $app['security.token_storage']->getToken();
            if (null !== $token) {
                $user = $token->getUser();
            }
            // Inscription dans le fichier log
            $app['monolog.prod']->info(sprintf("User '%s' has download archive %s.", $user->getUsername(), realpath($filepath)));
            // Indexation dans Elasticsearch
            // Ici on peut ajouter autant de champ que l'on souhaite indexer avec le fichier sous forme d'un tableau associatf
            // Le path peut etre relatif, le moteur se charge de resoudre le chemin absolue du fichier
            $app['search_engine']->index($filepath, array(
                "hash" => $hash,
                "title" => $title,
                "encounter" => $encounter,
                "patient" => $post['patient_name']
            ));
            // On renvoi une reponse, pourrais être plus elaborer ..
            return new JsonResponse($title);
        }
        return new Response("this is not an ajax request", 405);
    }

}
