<?php
/**
 * Created by PhpStorm.
 * User: univlr
 * Date: 16/11/17
 * Time: 09:15
 */

namespace Archivage\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Archivage {

    public function archivage(Request $request, Application $app) {

        $data = $request->request->get("str");
        $array = json_decode($data);

        $idSejour = $array->idSejour;
        $dateEntree = $array->dateEntree;
        $dateSortie = $array->dateSortie;
        $patient = $array->patient;
        $dirPath = "/var/www/html/OX-Archive/archives/" .$idSejour;

        if (!is_dir($dirPath)) {

            mkdir($dirPath);
            $documents = $array->documents;

            foreach ($documents as $doc) {

                $mime = $doc->resource->content[0]->attachment->contentType;
                if($mime == "application/pdf"){

                    $mime = "pdf";
                }

                $file = $doc->resource->content[0]->attachment->data;
                //$name = $doc->resource->content[0]->attachment->description;
                $name = "CompteRendu-" .$idSejour;
                $fileName = $name. "." .$mime;
                $filePath = $dirPath. "/" . $name;

                if (!file_exists($filePath)) {

                    file_put_contents($filePath, base64_decode($file));
                    /*$app['search_engine']->index($filePath, array(
                        "idSejour" => $idSejour
                    ));*/
                } else {

                    return new Response("Fichier déjà présent", 401);
                }

                return new Response("Dossier archivé", 200);
            }
        } else {

            return new Response("Dossier déjà présent", 400);
        }
    }
}