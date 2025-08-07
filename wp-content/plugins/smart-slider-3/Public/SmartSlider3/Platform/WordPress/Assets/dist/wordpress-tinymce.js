(function () {
    tinymce.create('tinymce.plugins.smartslider3', {
        init: function (ed, url) {
            ed.addButton('smartslider3', {
                title: 'Smart Slider 3',
                icon: 'mce-ico mce-i-dashicon dashicons-smart_slider__admin_menu',
                onclick: function () {
                    NextendSmartSliderWPTinyMCEModal(ed);
                }
            });
        },
        createControl: function (n, cm) {
            return null;
        },
        getInfo: function () {
            return {
                longname: "Smart Slider 3",
                author: 'Nextendweb',
                authorurl: 'https://smartslider3.com',
                infourl: 'https://smartslider3.com',
                version: "3.2"
            };
        }
    });
    tinymce.PluginManager.add('smartslider3', tinymce.plugins.smartslider3);
})();