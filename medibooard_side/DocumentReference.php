<?php
/**
 * Created by PhpStorm.
 * User: univlr
 * Date: 15/11/17
 * Time: 14:55
 */

$sejour_id = CValue::get('sejour_id');
$sejour = new CSejour();
$sejour->load($sejour_id);

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
$cr->setObject($sejour);
$crs = $cr->loadMatchingList();


$result = array();
foreach ( $crs as $_cr ) {
    $idex = CIdSante400::getMatch($sejour->_class, 'archive', $_cr->_guid, $sejour->_id);

    if ($idex !== null) {
        array_push($result, $_cr);
    }
}

$reponse = array(
    "resourceType" => "Bundle",
    "type" => "searchset",
    "total" => count($result),
    "entry" => array()
);

if (!empty($result)) {

    foreach ($result as $c) {

        $c->makePDFPreview(true);
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
} else {
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

CApp::json($reponse);

//echo json_encode($reponse, JSON_UNESCAPED_SLASHES);