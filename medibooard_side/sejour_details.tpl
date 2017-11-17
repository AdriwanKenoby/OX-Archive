

<table class="tbl">
    <tr>
        <th colspan="4">Consultations</th>
    </tr>
    <tr>
        <th>Consultation</th>
        <th>Patient</th>
        <th>Praticien</th>
        <th>Date d'arrivée</th>
    </tr>
    {{foreach from=$sejour->_ref_consultations item=_consultation}}
        <tr>
            <td>{{$_consultation->_view}}</td>
            <td>{{$sejour->_ref_patient->_view}}</td>
            <td>{{$_consultation->_ref_praticien->_view}}</td>
            <td>{{$_consultation->arrivee}}</td>
        </tr>
    {{/foreach}}


</table>

<table class="tbl">
    <tr>
        <th colspan="6">Opérations</th>
    </tr>
    <tr>
        <th>Opérations</th>
        <th>Patient</th>
        <th>Praticien</th>
        <th>Date</th>
        <th>Temps opératoire</th>
        <th>Actes CCAM</th>
    </tr>
    {{foreach from=$sejour->_ref_operations item=_operation}}
        <tr>
            <td>{{$_operation->_view}}</td>
            <td>{{$sejour->_ref_patient->_view}}</td>
            <td>{{$_operation->_ref_praticien->_view}}</td>
            <td>{{$_operation->date}}</td>
            <td>{{$_operation->temp_operation}}</td>
            <td>
                <ul>
                    {{foreach from=$_operation->_ref_actes_ccam item=_acte}}
                        <li>{{$_acte->code_acte}}</li>
                    {{/foreach}}
                </ul>
            </td>
        </tr>
    {{/foreach}}
</table>

<table class="tbl">
    <tr>
        <th>Liste des allergies</th>
        <td>
            <ul>
                {{foreach from=$sejour->_ref_patient->_ref_dossier_medical->_all_antecedents item=_antecedent}}
                    {{if $_antecedent->type == "alle"}}<li>{{$_antecedent->rques}}</li>{{/if}}
                {{/foreach}}
            </ul>
        </td>
    </tr>

    <tr>
        <th>Liste des antecedents</th>
        <td>
            <ul>
                {{foreach from=$sejour->_ref_patient->_ref_dossier_medical->_all_antecedents item=_antecedent}}
                    {{if $_antecedent->type != "alle"}}<li>{{$_antecedent->rques}}</li>{{/if}}
                {{/foreach}}
            </ul>
        </td>
    </tr>
</table>