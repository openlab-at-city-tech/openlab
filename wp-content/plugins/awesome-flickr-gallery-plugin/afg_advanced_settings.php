<?php
require_once('afg_libs.php');

function afg_admin_enqueue_scripts() {
    if ( ! empty( $_GET[ 'page' ] ) && 0 === strpos( $_GET[ 'page' ], 'afg_' )) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('afg_custom_css_js', BASE_URL . "/CodeMirror/lib/codemirror.js");
        wp_enqueue_script('afg_custom_css_theme_js', BASE_URL . "/CodeMirror/mode/css/css.js");
        wp_enqueue_style('afg_custom_css_style', BASE_URL . "/CodeMirror/lib/codemirror.css");
        wp_enqueue_style('afg_custom_css_theme_css', BASE_URL . "/CodeMirror/theme/cobalt.css");
        wp_enqueue_style('afg_custom_css_style', BASE_URL . "/CodeMirror/css/docs.css");
        wp_enqueue_style('afg_admin_css', BASE_URL . "/afg_admin.css");
    }
}

if (is_admin()) {
    add_action('admin_enqueue_scripts', 'afg_admin_enqueue_scripts');
    add_action('admin_head', 'afg_advanced_headers');
}

function afg_advanced_headers() {
    echo "
          <link href=\"https://plus.google.com/110562610836727777499\" rel=\"publisher\" />
          <script type=\"text/javascript\" src=\"https://apis.google.com/js/plusone.js\"></script>
          ";
   }

   function afg_advanced_settings_page() {
       $url=$_SERVER['REQUEST_URI'];
   ?>

   <h2><a href='http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin/'><img src="<?php
      echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/></a>Advanced Settings | Awesome Flickr Gallery</h2>

<?php
      if (isset($_POST['afg_advanced_save_changes']) && $_POST['afg_advanced_save_changes']) {
          update_option('afg_disable_slideshow', isset($_POST['afg_disable_slideshow'])? $_POST['afg_disable_slideshow']: '');
          update_option('afg_custom_css', $_POST['afg_custom_css']);
          update_option('afg_cache_refresh_interval', $_POST['afg-cache-refresh-interval']);
          echo "<div class='updated'><p><strong>Settings updated successfully.</strong></p></div>";
      }
?>         
<form method='post' action='<?php echo $url ?>'>
   <div id='afg-wrap'>
<?php global $afg_cache_refresh_interval_map;
echo afg_generate_version_line();
?>
        <div id="afg-main-box">
		<h3>Advanced Settings</h3>
		<table class='widefat afg-settings-box'>
			<tr>
				<th class="afg-label"></th>
				<th class="afg-input"></th>
				<th class="afg-help-bubble"></th>
			</tr>
			<tr>
				<td>Cache Refresh Interval</td>
				<td><select name='afg-cache-refresh-interval' id='afg-cache-refresh-interval'> <?php echo afg_generate_options($afg_cache_refresh_interval_map, get_option('afg_cache_refresh_interval', '1d')); ?>
</select></td>
                              <td> <div class="afg-help">How frequently should cached galleries update? Select a value that relates to your photos upload frequency on Flickr. Default is set to 1 day.</div></td>
			</tr>
		</table>
                     <h3>Custom CSS</h3>
                        <div style="background-color:#FFFFE0; border-color:#E6DB55; maargin:5px 0 15px; border-radius:3px 3px 3px 3px; border-width: 1px; border-style: solid; padding: 8px 10px; line-height: 20px">
                Check <a href='<?php echo BASE_URL . '/afg.css';?>' target='_blank'>afg.css</a> to see existing classes and properties for gallery which you can redefine here. Note that there is no validation applied to CSS Code entered here, so make sure that you enter valid CSS.
                    </div><br/>
                    <textarea id='afg_custom_css' name='afg_custom_css'><?php echo get_option('afg_custom_css');?></textarea>
       <script type="text/javascript">var myCodeMirror = CodeMirror.fromTextArea(document.getElementById('afg_custom_css'), {
       lineNumbers: true, indentUnit: 4, theme: "cobalt", matchBrackets: true} );</script>
            <input style='margin-top:15px' type="submit" name="afg_advanced_save_changes" id="afg_advanced_save_changes" class="button-primary" value="Save Changes" />
        </div>
         <div id="afg-side-box">
<?php
      $message = "Settings on this page are global and hence apply to all your Galleries.";
      echo afg_box('Help', $message);
      echo afg_donate_box();
      echo afg_share_box();
?>
            </div>
      </div>
         </form>
    <?php
   }
?>
