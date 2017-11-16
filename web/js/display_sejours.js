// Récupération des variables définies dans le service js_vars
var JsVars = jQuery('#js-vars').data('vars');

if( JsVars.fhir !== undefined ) {
    var fhir = JSON.parse(JsVars.fhir);

    var data = [];

    fhir.entry.forEach(function(el) {
        data.push({
            entree: el.resource.period.start,
            sortie: el.resource.period.end,
            id: el.resource.id,
            status: el.resource.status,
            ref_patient: el.resource.subject.reference,
            name: el.resource.subject.display
        });
    });

    $("#grid-sejours").dxDataGrid({
        dataSource: data,
        allowColumnResizing: true,
        allowColumnReordering: true,
        hoverStateEnabled: true,
        showRowLines: true,
        showBorders: true,
        headerFilter: { visible: true },
        loadPanel: { enabled: true },
        paging: { pageSize: 25 },
        pager: { showInfo: true	},
        selection: {
            mode: "multiple",
            showCheckBoxesMode: "always"
        },
        columns: [
            {
                dataField: "entree",
                caption: "Entrée",
                width: 150,
                dataType: "date"
            },
            {
                dataField: "sortie",
                caption: "Sortie",
                width: 150,
                dataType: "date"
            },
            {
                dataField: "status",
                caption: "Status",
                width: 100,
                dataType: "string"
            },
            {
                dataField: "ref_patient",
                caption: "Reference Patient",
                width: 100,
                dataType: "string"
            },
            {
                dataField: "name",
                caption: "nom du patient",
                width: 100,
                dataType: "string"
            }
        ]
    });

    $("#button-archivage").dxButton({
        text: "Archiver",
        type: "success",
        onClick: function (e) {
            var grid         = $("#grid-sejours").dxDataGrid("instance"),
                selectedRows = grid.getSelectedRowsData();
            selectedRows.forEach(function(el) {
                var url = "/archive";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: { sejour_id: el.id }
                })
                .done(function(res) {
                    //$(el).remove();
                    console.log(res);
                })
                .fail(function() {
                    console.log("error");
                });
            });
        }
    });
}
