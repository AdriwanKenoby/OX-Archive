<?php

namespace Archivage\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Archivage\Form\Type\SearchByPeriodType;
use Archivage\Form\Type\SearchArchiveType;
use SearchEngine\EngineBuilder;
use PHPFHIRGenerated\PHPFHIRResponseParser;

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
            // perform some action...

            $client = $app['guzzle']->getClient();
            $parser = new PHPFHIRResponseParser();

            $fhir_response = $client->get('DocumentReference/206352/_history/1');
            $object = $parser->parse((string)$fhir_response->getBody());
            //$object = json_decode((string)$fhir_response->getBody());
            //$json = json_encode($object);

            $attachment = $object->content[0]->attachment;
            $imageData = $attachment->data->value;
            $contentType = $attachment->contentType->value;
            $title = $attachment->title->value;
            $hash = $attachment->hash->value;
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

            return $app['twig']->render('index.html.twig', array(
                'form' => $form->createView(),
                'data' => $form->getData(),
                'fhir' => (string) $fhir_response->getBody(),
                'img' => 'data: '.$contentType.';base64,'.$imageData
            ));
        }

        return $app['twig']->render('index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function searchAction(Request $request, Application $app) {
        $form = $app['form.factory']->create(SearchArchiveType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $documents = EngineBuilder::create()->build()->search($form->getData()['query']);
            return $app['twig']->render('search.html.twig', array(
                'documents' => $documents,
            ));
        }
        return $app['twig']->render('search.html.twig', array(
            'form' => $form->createView()
        ));
    }

}
