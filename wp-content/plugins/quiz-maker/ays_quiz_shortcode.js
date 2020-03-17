
  (function() {
      /* Register the buttons */
      tinymce.create('tinymce.plugins.ays_quiz_button_mce', {
          init : function(ed, url) {
  		   /**
  		   * Adds HTML tag to selected content
  		   */
  			ed.addButton( 'ays_quiz_button_mce', {
  				title : 'Add Quiz',
  				image :  url + '/admin/images/icons/icon-128x128.png',
  				cmd: 'ays_quiz_button_cmd'
  			});

  			ed.addCommand( 'ays_quiz_button_cmd', function() {
  				ed.windowManager.open(
  				{
  					title : 'Quiz Maker',
  					file : ajaxurl + '?action=gen_ays_quiz_shortcode',
  				},
  				{
  					plugin_url : url
  				});

  		   });
  		},
  		createControl : function(n, cm) {
  		   return null;
  		},
  	});
      /* Start the buttons */
      tinymce.PluginManager.add( 'ays_quiz_button_mce', tinymce.plugins.ays_quiz_button_mce );
  })();
