(function () {
    tinymce.create("tinymce.plugins.Glossary", {
        init: function (ed, url) {

            ed.addButton('cmtt_exclude', {
                title: 'Exclude from CM Tooltip Glossary',
                image: url + '/icon.png',
                onclick: function () {
                    ed.focus();
                    ed.selection.setContent('[glossary_exclude]' + ed.selection.getContent() + '[/glossary_exclude]');
                }
            });

            ed.addButton('cmtt_parse', {
                title: 'Parse with CM Tooltip Glossary',
                image: url + '/icon.png',
                onclick: function () {
                    ed.focus();
                    ed.selection.setContent('[cm_tooltip_parse]' + ed.selection.getContent() + '[/cm_tooltip_parse]');
                }
            });

        },
        getInfo: function () {
            return{
                longname: "CM Tooltip Glossary",
                author: "CreativeMinds",
                authorurl: "https://www.cminds.com/",
                infourl: "https://www.cminds.com/",
                version: "2.0"
            };
        },
        createControl: function (n, cm) {
            return null;
        }
    });

    tinymce.PluginManager.add("cmtt_glossary", tinymce.plugins.Glossary);
}());