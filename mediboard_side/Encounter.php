<?php
/**
 * Created by PhpStorm.
 * User: univlr
 * Date: 15/11/17
 * Time: 09:13
 */

// On recupere nos parametre de requete
$date_min = CValue::get("date_min");
$date_max = CValue::get("date_max");

// On charge les sejours associees
$where = array();
$where["sejour.entree"] = "BETWEEN '$date_min' AND '$date_max'";
$sejour  = new CSejour();
$sejours = $sejour->loadList($where);

// Construction de la reponse
$reponse = array(
    "resourceType" => "Bundle",
    "entry" => array()
);

foreach ($sejours as $sejour) {
    $sejour->loadRefPatient();
    $patient = $sejour->_ref_patient;
    array_push($reponse['entry'], array(
        "resource" => array(
            "resourceType" => "Encounter",
            "id" => $sejour->_id,
            "status" => (null !== $sejour->_date_sortie) ? "finished" : "current",
            "period" => array(
                "start" => $sejour->_date_entree_prevue,
                "end" => $sejour->_date_sortie_prevue
            ),
            "subject" => array(
                "reference" => "Patient/".$patient->_id,
                "display" => $patient->_p_first_name.' '.$patient->_p_last_name
            )
        )
    ));
}

// envoi de la reponse en json
CApp::json($reponse);
