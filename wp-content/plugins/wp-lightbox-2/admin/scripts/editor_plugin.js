(function() {
    tinymce.create('tinymce.plugins.poll_mce', {
 
        init : function(ed, url){
			
			ed.addCommand('mcepoll_mce', function() {
				ed.windowManager.open({
					file : poll_admin_ajax+'?action=poll_window_manager',
					width : 350 + ed.getLang('poll_mce.delta_width', 0),
					height : 150 + ed.getLang('poll_mce.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});
            ed.addButton('poll_mce', {
            title : 'Insert Poll',
            cmd : 'mcepoll_mce',
            image: poll_admin_url + 'admin/images/icon-polling.png'
            });
        }
    });
 
    tinymce.PluginManager.add('poll_mce', tinymce.plugins.poll_mce);
 
})();