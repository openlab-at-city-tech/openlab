<?php
require_once('afg_libs.php');

if (is_admin()) {
    wp_enqueue_script('jquery');
    wp_enqueue_script('afg_colorbox_script', BASE_URL . "/colorbox/jquery.colorbox-min.js" , array('jquery'));
    wp_enqueue_script('afg_colorbox_js', BASE_URL . "/colorbox/mycolorbox.js" , array('jquery'));
    wp_enqueue_script('afg_highslide_js', BASE_URL . "/highslide/highslide-full.min.js");
    wp_enqueue_style('afg_colorbox_css', BASE_URL . "/colorbox/colorbox.css");
    wp_enqueue_style('afg_highslide_css', BASE_URL . "/highslide/highslide.css");
    wp_enqueue_style('afg_custom_css_style', BASE_URL . "/CodeMirror/lib/codemirror.css");
    wp_enqueue_script('afg_custom_css_js', BASE_URL . "/CodeMirror/lib/codemirror.js");
    wp_enqueue_script('afg_custom_css_theme_js', BASE_URL . "/CodeMirror/mode/css/css.js");
    wp_enqueue_style('afg_custom_css_theme_css', BASE_URL . "/CodeMirror/theme/cobalt.css");
    wp_enqueue_style('afg_custom_css_style', BASE_URL . "/CodeMirror/css/docs.css");
    add_action('admin_head', 'afg_advanced_headers');
}

function afg_advanced_headers() {
    echo "<script type='text/javascript'>
        hs.graphicsDir = '" . BASE_URL . "/highslide/graphics/';
    hs.align = 'center';
    hs.transitions = ['expand', 'crossfade'];
    hs.fadeInOut = true;
    hs.dimmingOpacity = 0.85;
    hs.outlineType = 'rounded-white';
    hs.captionEval = 'this.thumb.alt';
    hs.marginBottom = 115; // make room for the thumbstrip and the controls
    hs.numberPosition = 'caption';
    // Add the slideshow providing the controlbar and the thumbstrip
    hs.addSlideshow({
        //slideshowGroup: 'group1',
        interval: 3500,
            repeat: false,
            useControls: true,
            overlayOptions: {
                className: 'text-controls',
                    position: 'bottom center',
                    relativeTo: 'viewport',
                    offsetY: -60
},
thumbstrip: {
    position: 'bottom center',
        mode: 'horizontal',
        relativeTo: 'viewport'
}
});
      </script>
          <link href=\"https://plus.google.com/110562610836727777499\" rel=\"publisher\" />
          <script type=\"text/javascript\" src=\"https://apis.google.com/js/plusone.js\">
</script>
          ";
   }

   function afg_advanced_settings_page() {
       $url=$_SERVER['REQUEST_URI'];
   ?>
   <div class='wrap'>
   <h2><a href='http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin/'><img src="<?php
      echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/></a>Advanced Settings | Awesome Flickr Gallery</h2>

<?php
      if (isset($_POST['afg_advanced_save_changes']) && $_POST['afg_advanced_save_changes']) {
          update_option('afg_disable_slideshow', isset($_POST['afg_disable_slideshow'])? $_POST['afg_disable_slideshow']: '');
          update_option('afg_slideshow_option', $_POST['afg_slideshow_option']);
          update_option('afg_custom_css', $_POST['afg_custom_css']);
          echo "<div class='updated'><p><strong>Settings updated successfully.</strong></p></div>";
      }
?>
         <form method='post' action='<?php echo $url ?>'>
            <?php echo afg_generate_version_line() ?>
            <div class="postbox-container" style="width:69%; margin-right:1%">
               <div id="poststuff">
                  <div class="postbox" style='box-shadow:0 0 2px'>
                     <h3>Advanced Settings</h3>
                     <table class='form-table'>
                        <tr valign='top'>
                           <th scope='row'>Choose Slideshow</th>
                           <td width='25%'><input type='radio' name='afg_slideshow_option' id='afg_slideshow_option' value='colorbox'
                              <?php if (get_option('afg_slideshow_option') == 'colorbox') echo "checked=''" ?> > ColorBox&nbsp;&nbsp;</input>
                              <input type='radio' name='afg_slideshow_option' id='afg_slideshow_option' value='highslide'
                              <?php if (get_option('afg_slideshow_option') == 'highslide') echo "checked=''" ?> > HighSlide</input>
                           </td>
                           <td><font size='2'>
                                 <b>HighSlide is NOT FREE for Commercial websites</b>.  If you are using
                                 <i>Awesome Flickr Gallery</i> on a commercial website, you need to purchase a license from their website
                                 <a href='http://highslide.com/#licence' target='_blank'>here</a>.  If you want a free slideshow,
                                 use ColorBox instead.</font></td></tr>

                        <tr valign='top'>
                           <th scope='row'>Disable Slideshow</th>
                           <td><input type='checkbox' name='afg_disable_slideshow' id='afg_disable_slideshow' value='yes'
                            <?php if (get_option('afg_disable_slideshow')) echo 'checked=\'\'';?>>
                           </td>
                           <td><font size='2'>Disabling slideshow will remove the slideshow built into the Awesome Flickr Gallery.
                              Use this option if you want to use a different slideshow (probably from your theme or any other plugin).</font>
                        </td>
                    </tr>
                  </table></div>
               <div id="poststuff">
                  <div class="postbox" style='box-shadow:0 0 2px'>
                     <h3>Custom CSS</h3>
                        <div style="background-color:#FFFFE0; border-color:#E6DB55; maargin:5px 0 15px; border-radius:3px 3px 3px 3px; border-width: 1px; border-style: solid; padding: 8px 10px; line-height: 20px">
                Check <a href='<?php echo BASE_URL . '/afg.css';?>' target='_blank'>afg.css</a> to see existing classes and properties for gallery which you can redefine here. Note that there is no validation applied to CSS Code entered here, so make sure that you enter valid CSS.
                    </div><br/>
                    <textarea id='afg_custom_css' name='afg_custom_css'><?php echo get_option('afg_custom_css');?></textarea>
       <script type="text/javascript">var myCodeMirror = CodeMirror.fromTextArea(document.getElementById('afg_custom_css'), {
       lineNumbers: true, indentUnit: 4, theme: "cobalt", matchBrackets: true} );</script>

</div></div>
               </div>
            <input type="submit" name="afg_advanced_save_changes" id="afg_advanced_save_changes" class="button-primary" value="Save Changes" />
         </div>

         <div class="postbox-container" style="width: 29%;">
<?php
      $message = "Settings on this page are global and hence apply to all your Galleries.";
      echo afg_box('Help', $message);
      echo afg_donate_box();
      echo afg_share_box();
?>
            </div>
            <div class="postbox-container" style="width: 49%; margin-right:1%">
               <br />
               <div id="poststuff">
                  <div class="postbox" style='box-shadow:0 0 2px'>
                     <h3>ColorBox Example</h3>
                     <table class='form-table'>
                        <th>Click thumbnail to view slideshow<br /><br />
                           <a href='http://farm3.static.flickr.com/2077/5824491094_54f158e7fa_b_d.jpg'
                              class='afgcolorbox' rel='example4' title='Glowing Lotus'>
                              <img alt='Glowing Lotus' src='http://farm3.static.flickr.com/2077/5824491094_54f158e7fa_s_d.jpg'>
                           </a>
                           <a href='http://farm6.static.flickr.com/5131/5467257036_36d6dcf359_b_d.jpg'
                              class='afgcolorbox' rel='example4' title='Ferocious Cheetah'>
                              <img alt='Ferocious Cheetah' src='http://farm6.static.flickr.com/5131/5467257036_36d6dcf359_s_d.jpg'>
                           </a>
                           <a href='http://farm6.static.flickr.com/5015/5466659297_e983d8bd45_b_d.jpg'
                              class='afgcolorbox' rel='example4' title='Flemingo'>
                              <img alt='Flemingo' src='http://farm6.static.flickr.com/5015/5466659297_e983d8bd45_s_d.jpg'>
                           </a>
                           <a href='http://farm4.static.flickr.com/3650/3395474436_bce116a702_b_d.jpg'
                              class='afgcolorbox' rel='example4' title='Dragonfly'>
                              <img alt='Dragonfly' src='http://farm4.static.flickr.com/3650/3395474436_bce116a702_s_d.jpg'>
                           </a>
                           <a href='http://farm4.static.flickr.com/3641/3395469156_1e82110722_b_d.jpg'
                              class='afgcolorbox' rel='example4' title='Dragonfly'>
                              <img alt='Dragonfly' src='http://farm4.static.flickr.com/3641/3395469156_1e82110722_s_d.jpg'>
                           </a>
                        </th>
                     </table>
            </div></div></div>
            <div class="postbox-container" style="width:50%;">
               </br />
               <div id="poststuff">
                  <div class="postbox" style='box-shadow:0 0 2px'>
                     <h3>HighSlide Example</h3>
                     <table class='form-table'>
                        <th>Click thumbnail to view slideshow<br /><br />
                           <div class='highslide-gallery'>
                              <a href='http://farm3.static.flickr.com/2077/5824491094_54f158e7fa_b_d.jpg'
                                 class='highslide' onclick='return hs.expand(this)' title='Glowing Lotus'>
                                 <img alt='Glowing Lotus' src='http://farm3.static.flickr.com/2077/5824491094_54f158e7fa_s_d.jpg'>
                              </a>
                              <a href='http://farm6.static.flickr.com/5131/5467257036_36d6dcf359_b_d.jpg'
                                 class='highslide' onclick='return hs.expand(this)' title='Ferocious Cheetah'>
                                 <img alt='Ferocious Cheetah' src='http://farm6.static.flickr.com/5131/5467257036_36d6dcf359_s_d.jpg'>
                              </a>
                              <a href='http://farm6.static.flickr.com/5015/5466659297_e983d8bd45_b_d.jpg'
                                 class='highslide' onclick='return hs.expand(this)' title='Flemingo'>
                                 <img alt='Flemingo' src='http://farm6.static.flickr.com/5015/5466659297_e983d8bd45_s_d.jpg'>
                              </a>
                              <a href='http://farm4.static.flickr.com/3650/3395474436_bce116a702_b_d.jpg'
                                 class='highslide' onclick='return hs.expand(this)' title='Dragonfly'>
                                 <img alt='Dragonfly' src='http://farm4.static.flickr.com/3650/3395474436_bce116a702_s_d.jpg'>
                              </a>
                              <a href='http://farm4.static.flickr.com/3641/3395469156_1e82110722_b_d.jpg'
                                 class='highslide' onclick='return hs.expand(this)' title='Dragonfly'>
                                 <img alt='Dragonfly' src='http://farm4.static.flickr.com/3641/3395469156_1e82110722_s_d.jpg'>
                              </a>
                           </div>
                        </th>
                     </table>
                  </div>
               </div>
            </div>
         </form>
      </div>
<?php
   }
?>
