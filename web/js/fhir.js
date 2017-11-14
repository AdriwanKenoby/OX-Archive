// Récupération des variables définies dans le service js_vars
var JsVars = jQuery('#js-vars').data('vars');

// Récuperer une variable
var fhir = JsVars.fhir;
var globalVariable = JsVars.myGlobalVariable;
console.log(globalVariable)
console.log(fhir);
