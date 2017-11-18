var JsVars = jQuery('#js-vars').data('vars'),
    archiveDirectory = JsVars.archiveDirectory,
    tree = JSON.parse(JsVars.tree);

$(function () {

    /**
     * Recupere la variable tree de notre "Bag" JsVars
     */
    var temp = [{
        id: "0",
        text: "Archives",
        items: []
    }];
    /**
     * Recupere les clefs du tableau retourne par le json
     * Chaque clef est en fait le nom d'un dossier
     * present dans /archives
     *
     * Object.keys retourne un tableau de clef
     */
    var keys = Object.keys(tree);
    /**
     * Pour chaque clef
     * ajout du dossier ayant le nom de cette cle
     * dans la variable temp
     * et ajout des fichiers dans ce dossier
     *
     * Cet algo n'est pas generique
     * et ne fonctionne que pour des dossiers
     * direct de /archives ayant un seul fichier dedans
     */
    $.each(keys, function (index, el) {        
        temp[0].items.push({

            id: "0_" + index,
            text: el,
            items: [{

                    id: "0_" + index + "_" + index,
                    text: tree[el][0],
                    location: archiveDirectory + '/' + el + '/' + tree[el][0]
                }]
        });
    });
    /**
     * Pour 2 sejours selectionnees (exemple id sejour 9 et 14),
     * temp ressemble a cela :
     * [{
        id: "0",
        text: "Archives",
        items: [{
            id: "0_0",
            text: 9,
            items: [{
                id: "0_0_0",
                text: "CSejour-9.pdf",
                location: "/OX-Archive/archives/9/CSejour-9.pdf"
            }]
        },
        {
            id: "0_1",
            text: 14,
            items: [{
                id: "0_1_1",
                text: "CSejour-14.pdf",
                location: "/OX-Archive/archives/14/CSejour-14.pdf"
            }]
        }]
       }]
     */
    $("#tree-archives").dxTreeView({

        items: temp,
        width: 300,
        searchValue: "",
        onItemClick: function (e) {

            /**
             * Au clic sur un fichier
             * affichage dans une balise embed
             */
            var item = e.itemData;
            $("#archive-details").removeClass("hidden");
            $("#archive-details > embed").attr("src", item.location);
        }
    });
});
