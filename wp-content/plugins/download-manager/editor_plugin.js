 
(function() {

    tinymce.create('tinymce.plugins.wpdm_tinyplugin', {

        init : function(ed, url){            
            ed.addCommand('wpdm_mcedonwloadmanager', function() {
                                ed.windowManager.open({
                                        title: 'Download Controller',
                                        file : 'admin.php?wpdm_action=wpdm_tinymce_button',
                                        height: 550,
                                        width:400,                                        
                                        inline : 1
                                }, {
                                        plugin_url : url, // Plugin absolute URL
                                        some_custom_arg : 'custom arg' // Custom argument
                                });
                        });
            
            ed.addButton('wpdm_tinyplugin', {
                title : 'Download Manager: Insert Package or Category',
                cmd : 'wpdm_mcedonwloadmanager',
                image: url + "/img/donwloadmanager.png"
            });
        },

        getInfo : function() {
            return {
                longname : 'WPDC - TinyMCE Button Add-on',
                author : 'Shaon',
                authorurl : 'http://www.wpdownloadmanager.com',
                infourl : 'http://www.wpdownloadmanager.com',
                version : "1.0"
            };
        }
    });

    tinymce.PluginManager.add('wpdm_tinyplugin', tinymce.plugins.wpdm_tinyplugin);
    
})();
