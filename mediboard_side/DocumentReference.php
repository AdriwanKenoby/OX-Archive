<?php
/**
 * Created by PhpStorm.
 * User: univlr
 * Date: 15/11/17
 * Time: 14:55
 */
// On recupere un parametre de requete
$sejour_id = CValue::get('sejour_id');
// On charge le sejour associe
$sejour = new CSejour();
$sejour->load($sejour_id);

// Les refs associees
$sejour->loadRefPatient();
$sejour->loadRefsOperations();
$sejour->_ref_patient->loadRefDossierMedical();
$sejour->_ref_patient->_ref_dossier_medical->loadRefsAntecedents();

foreach($sejour->loadRefsConsultations() as $consultation ) {
    $consultation->loadRefPraticien();
}

foreach ($sejour->_ref_operations as $operation ) {
    $operation->loadRefPraticien();
    $operation->loadRefsActesCCAM();
}

$cr = new CCompteRendu();
// On recupere les compte rendu associes au sejour
$cr->setObject($sejour);
$crs = $cr->loadMatchingList();

// Un tableau pour stocker nos resultats
$result = array();

foreach ( $crs as $_cr ) {
    $idex = CIdSante400::getMatch($sejour->_class, 'archive', $_cr->_guid, $sejour->_id);
    // Si une archive associe a ce comte rendu a deja ete cree on le charge dans notre tab de resultats
    if ($idex !== null) {
        array_push($result, $_cr);
    }
}

// On commence a construire la structure de l'objet json a renvoyer
$reponse = array(
    "resourceType" => "Bundle",
    "type" => "searchset",
    "total" => count($result),
    "entry" => array()
);

// Si il n' y a des resultats on ne genere pas les documents (ne pas ecraser ou dupliquer)
if (!empty($result)) {

    foreach ($result as $c) {

        $c->makePDFPreview(true);
        // on rempli notre reponse correspondant a la norme FHIR
        array_push($reponse['entry'], array(
            "resource" => array(
                "resourceType" => "DocumentReference",
                "id" => $c->_id,
                "status" => (null !== $sejour->_date_sortie) ? "finished" : "current",
                "content" => array(
                    0 => array(
                        "attachment" => array(
                            "contentType" => "application/pdf",
                            "data" => base64_encode(file_get_contents($c->_ref_file->_file_path)),
                            "hash" => hash_file('md5', $c->_ref_file->_file_path)
                        )
                    )
                ),
                "description" => $c->nom,
                "context" => array(
                    "encounter" => array(
                        "reference" => "Encounter/".$sejour->_id
                    ),
                    "sourcePatientInfo" => array(
                        "reference" => "Patient/".$sejour->_ref_patient->_id
                    )
                )
            )
        ));
    }
} else { // Sinon on genere le pdf les id400 references et on construit la reponse
    $template = new CSmartyDP();
    $template->assign('sejour', $sejour);
    $contentHTML = new CContentHTML();
    $contentHTML->content = $template->fetch("sejour_details.tpl");
    $contentHTML->store();

    $c = new CCompteRendu();
    $c->content_id = $contentHTML->_id;
    $c->nom = $sejour->_guid.'.pdf';
    $c->setObject($sejour);
    $c->store();

    $c->makePDFpreview(true);
    $path = CCdaTools::generatePDFA($c->_ref_file->_file_path);

    $idex = new CIdSante400();
    $idex->setObject($sejour);
    $idex->id400 = $c->_guid;
    $idex->tag = 'archive';
    $idex->datetime_create = CMbDT::dateTime();
    $idex->store();

    array_push($reponse['entry'], array(
        "resource" => array(
            "resourceType" => "DocumentReference",
            "id" => $c->_id,
            "status" => (null !== $sejour->_date_sortie) ? "finished" : "current",
            "content" => array(
                0 => array(
                    "attachment" => array(
                        "contentType" => "application/pdf",
                        "data" => base64_encode(file_get_contents($c->_ref_file->_file_path)),
                        "hash" => hash_file('md5', $c->_ref_file->_file_path)
                    )
                )
            ),
            "description" => $c->nom,
            "context" => array(
                "encounter" => array(
                    "reference" => "Encounter/".$sejour->_id
                ),
                "sourcePatientInfo" => array(
                    "reference" => "Patient/".$sejour->_ref_patient->_id
                )
            )
        )
    ));
}

// Envoi de a reponse
CApp::json($reponse);

//echo json_encode($reponse, JSON_UNESCAPED_SLASHES);
