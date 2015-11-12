/* ----------------------------------- */
/**
 * @package WP Simple Anchors Links
 * @link http://www.kilukrumedia.com
 * @copyright Copyright &copy; 2014, Kilukru Media
 * @version: 1.0.0
 */
/* ----------------------------------- */

/*(function($) {

})(jQuery);*/

(function() {
   tinymce.create('tinymce.plugins.wpanchor', {
      init : function(ed, url) {
         ed.addButton('wpanchor', {
            title : 'WP Simple Anchors Links',
            image : url + '/../../images/icons/anchor.png',
            onclick : function() {
               var id = prompt("Give the #ID of the anchor", "");
               //var text = prompt("Your content", "");
               var text = '';

               if (text != null && text != ''){
                  if (id != null && id != '')
                     ed.execCommand('mceInsertContent', false, '[wpanchor id="'+id+'"]'+text+'[/wpanchor]');
                  else
                     ed.execCommand('mceInsertContent', false, '[wpanchor]'+text+'[/wpanchor]');
               }
               else{
                  if (id != null && id != '')
                     ed.execCommand('mceInsertContent', false, '[wpanchor id="'+id+'"]');
                  //else
                  //   ed.execCommand('mceInsertContent', false, '[wpanchor]');
               }
            }
         });
      },
      createControl : function(n, cm) {
         return null;
      },
      getInfo : function() {
         return {
            longname    : "WP Simple Anchors Links",
            author      : 'Alfred Dagenais',
            authorurl   : 'http://www.kilukrumedia.com',
            infourl     : 'http://www.kilukrumedia.com',
            version     : "1.0.0"
         };
      }
   });
   tinymce.PluginManager.add('wpanchor', tinymce.plugins.wpanchor);
})();