<?php
/*  FV Wordpress Flowplayer - HTML5 video player with Flash fallback    
    Copyright (C) 2013  Foliovision

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 

  global $post;
  $post_id = isset($post->ID) ? $post->ID : 0;
  
  $fv_flowplayer_conf = get_option( 'fvwpflowplayer' );
  $allow_uploads = false;

	if( isset($fv_flowplayer_conf["allowuploads"]) && $fv_flowplayer_conf["allowuploads"] == 'true' ) {
	  $allow_uploads = $fv_flowplayer_conf["allowuploads"];
	  $upload_field_class = ' with-button';
	} else {
	  $upload_field_class = '';
	}
  
  function fv_flowplayer_admin_select_popups($aArgs){
  global $fv_fp;
  
  $aPopupData = get_option('fv_player_popups');

  $sId = (isset($aArgs['id'])?$aArgs['id']:'popups_default');
  $aArgs = wp_parse_args( $aArgs, array( 'id'=>$sId, 'item_id'=>'', 'show_default' => false ) );
  ?>
  <select id="<?php echo $aArgs['id']; ?>" name="<?php echo $aArgs['id']; ?>">
    <?php if( $aArgs['show_default'] ) : ?>
      <option>Use site default</option>
    <?php endif; ?>
    <option <?php if( $aArgs['item_id'] == 'no' ) echo 'selected '; ?>value="no">None</option>
    <option <?php if( $aArgs['item_id'] == 'random' ) echo 'selected '; ?>value="random">Random</option>
    <?php
    if( isset($aPopupData) && is_array($aPopupData) && count($aPopupData) > 0 ) {
      foreach( $aPopupData AS $key => $aPopupAd ) {
        ?><option <?php if( $aArgs['item_id'] == $key ) echo 'selected'; ?> value="<?php echo $key; ?>"><?php
        echo $key;
        if( !empty($aPopupAd['name']) ) echo ' - '.$aPopupAd['name'];
        if( $aPopupAd['disabled'] == 1 ) echo ' (currently disabled)';
        ?></option><?php
      }
    } ?>      
  </select>
  <?php
}
  
	$fv_flowplayer_helper_tag = ( is_plugin_active('jetpack/jetpack.php') ) ? 'b' : 'span';
?>
<style>
.fv-wp-flowplayer-notice { background-color: #FFFFE0; border-color: #E6DB55; margin: 5px 0 15px; padding: 0 0.6em; border-radius: 3px 3px 3px 3px; border-style: solid; border-width: 1px; } 
.fv-wp-flowplayer-notice.fv-wp-flowplayer-note { background-color: #F8F8F8; border-color: #E0E0E0; } 
.fv-wp-flowplayer-notice p { font-family: sans-serif; font-size: 12px; margin: 0.5em 0; padding: 2px; } 
.fv_wp_flowplayer_playlist_remove { display: none; }
#fv-flowplayer-playlist table { border-bottom: 1px #eee solid; }
#fv-flowplayer-playlist table input, #fv-flowplayer-playlist table input.with-button { width: 93%; }
#fv-flowplayer-playlist table input.half-field { width: 46%; }
#fv-flowplayer-playlist table/*:first-child*/ input.with-button { width: 70%; }
#fv-flowplayer-playlist table input.fv_wp_flowplayer_field_subtitles { width: 82%; }
#fv-flowplayer-playlist table input.fv_wp_flowplayer_field_subtitles.with-button { width: 59%; }
#fv-flowplayer-playlist table select.fv_wp_flowplayer_field_subtitles_lang { width: 10%; }
#fv-flowplayer-playlist table tr.video-size { display: none; }
#fv-flowplayer-playlist table tr#fv_wp_flowplayer_add_format_wrapper { display: none; }
#fv-flowplayer-playlist table tr#fv_wp_flowplayer_file_info { display: none; }
#fv-flowplayer-playlist table .fv_wp_flowplayer_field_rtmp { visibility: hidden; }
#fv-flowplayer-playlist table .fv_wp_flowplayer_field_rtmp_wrapper th { visibility: hidden; }
#fv-flowplayer-playlist table .hint { display: none; }
/*#fv-flowplayer-playlist table .button { display: none; }*/
#fv-flowplayer-playlist table:first-child tr.video-size { display: table-row; }
#fv-flowplayer-playlist table:first-child .hint { display: inline; }
#fv-flowplayer-playlist table:first-child tr#fv_wp_flowplayer_add_format_wrapper { display: table-row; }
#fv-flowplayer-playlist table:first-child tr#fv_wp_flowplayer_file_info { display: none; }
#fv-flowplayer-playlist table:first-child .fv_wp_flowplayer_field_rtmp { visibility: visible; }
#fv-flowplayer-playlist table:first-child .fv_wp_flowplayer_field_rtmp_wrapper th { visibility: visible; }
/*#fv-flowplayer-playlist table:first-child .button { display: inline-block; }*/
/*#colorbox, #cboxOverlay, #cboxWrapper{ z-index: 100000; }*/
</style>
  
<script>
var fvwpflowplayer_helper_tag = '<?php echo $fv_flowplayer_helper_tag ?>';
var fv_wp_flowplayer_re_edit = /\[[^\]]*?<<?php echo $fv_flowplayer_helper_tag; ?>[^>]*?rel="FCKFVWPFlowplayerPlaceholder"[^>]*?>.*?<\/<?php echo $fv_flowplayer_helper_tag; ?>>.*?[^\\]\]/mi;
var fv_wp_flowplayer_re_insert = /<<?php echo $fv_flowplayer_helper_tag; ?>[^>]*?rel="FCKFVWPFlowplayerPlaceholder"[^>]*?>.*?<\/<?php echo $fv_flowplayer_helper_tag; ?>>/gi;
<?php global $fv_fp; if( isset($fv_fp->conf['postthumbnail']) && $fv_fp->conf['postthumbnail'] == 'true' ) : ?>
var fv_flowplayer_set_post_thumbnail_id = <?php echo $post_id; ?>;
var fv_flowplayer_set_post_thumbnail_nonce = '<?php echo wp_create_nonce( "set_post_thumbnail-$post_id" ); ?>';
<?php endif; ?>
</script>

<div style="display: none">
  <div id="fv-wordpress-flowplayer-popup">
    <div id="fv-flowplayer-playlist">
  	  <table class="slidetoggle describe fv-flowplayer-playlist-item" width="100%">
        <tbody>
          <?php do_action( 'fv_flowplayer_shortcode_editor_before' ); ?>
          <tr>
            <th scope="row" class="label" style="width: 19%">
              <a class="alignleft fv_wp_flowplayer_playlist_remove" href="#" onclick="return fv_wp_flowplayer_playlist_remove(this)"><?php _e('(remove)', 'fv-wordpress-flowplayer'); ?></a>
              <label for="fv_wp_flowplayer_field_src" class="alignright"><?php _e('Video', 'fv-wordpress-flowplayer'); ?></label>
            </th>
            <td colspan="2" class="field"><input type="text" class="text<?php echo $upload_field_class; ?>" id="fv_wp_flowplayer_field_src" name="fv_wp_flowplayer_field_src" value="" />
            <?php if ($allow_uploads=="true") { ?>      
              <a class="button add_media" href="#"><span class="wp-media-buttons-icon"></span> <?php _e('Add Video', 'fv-wordpress-flowplayer'); ?></a>
            <?php }; //allow uplads video ?></td>
          </tr>
    
          <tr style="display: none" id="fv_wp_flowplayer_file_info">
            <th></th>
            <td colspan="2">
              <?php _e('Video Duration', 'fv-wordpress-flowplayer'); ?>: <span id="fv_wp_flowplayer_file_duration"></span><br />
              <?php _e('File size', 'fv-wordpress-flowplayer'); ?>: <span id="fv_wp_flowplayer_file_size"></span>
            </td>
          </tr>
          <tr class="video-size"><th></th>
            <td class="field" colspan="2"><label for="fv_wp_flowplayer_field_width"><?php _e('Width', 'fv-wordpress-flowplayer'); ?> <small>(px)</small></label> <input type="text" id="fv_wp_flowplayer_field_width" class="fv_wp_flowplayer_field_width" name="fv_wp_flowplayer_field_width" style="width: 19%; margin-right: 25px;"  value=""/> <label for="fv_wp_flowplayer_field_height"><?php _e('Height', 'fv-wordpress-flowplayer'); ?> <small>(px)</small></label> <input type="text" id="fv_wp_flowplayer_field_height" class="fv_wp_flowplayer_field_height" name="fv_wp_flowplayer_field_height" style="width: 19%" value=""/></td>
          </tr>
          
          <tr style="display: none;" class="fv_wp_flowplayer_field_src_1_wrapper">
            <th scope="row" class="label" style="width: 19%"><label for="fv_wp_flowplayer_field_src_1" class="alignright"><?php _e('Video', 'fv-wordpress-flowplayer'); ?> <small><?php _e('(another format)', 'fv-wordpress-flowplayer'); ?></small></label></th>
            <td colspan="2" class="field"><input type="text" class="text<?php echo $upload_field_class; ?>" id="fv_wp_flowplayer_field_src_1" name="fv_wp_flowplayer_field_src_1" value=""/>
            <?php if ($allow_uploads=="true") { ?> 
              <a class="button add_media" href="#"><span class="wp-media-buttons-icon"></span> <?php _e('Add Video', 'fv-wordpress-flowplayer'); ?></a>
            <?php }; //allow uplads video ?>
            </td>
          </tr>
          
          <tr style="display: none;" class="fv_wp_flowplayer_field_src_2_wrapper">
            <th scope="row" class="label" style="width: 19%"><label for="fv_wp_flowplayer_field_src_2" class="alignright"><?php _e('Video', 'fv-wordpress-flowplayer'); ?> <small><?php _e('(another format)', 'fv-wordpress-flowplayer'); ?></small></label></th>
            <td colspan="2" class="field"><input type="text" class="text<?php echo $upload_field_class; ?>" id="fv_wp_flowplayer_field_src_2" name="fv_wp_flowplayer_field_src_2" value=""/>
            <?php if ($allow_uploads=="true") {	?>  
              <a class="button add_media" href="#"><span class="wp-media-buttons-icon"></span> <?php _e('Add Video', 'fv-wordpress-flowplayer'); ?></a>
            <?php }; //allow uplads video ?>
            </td>    			
          </tr>
          
          <tr style="display: none;" class="fv_wp_flowplayer_field_rtmp_wrapper">
            <th scope="row" class="label" style="width: 19%"><label for="fv_wp_flowplayer_field_rtmp" class="alignright"><?php _e('RTMP Server', 'fv-wordpress-flowplayer'); ?></label> <?php if( !empty($fv_flowplayer_conf["rtmp"]) ) : ?>(<abbr title="<?php _e('Leave empty to use Flash streaming server from plugin settings', 'fv-wordpress-flowplayer'); ?>">?</abbr>)<?php endif; ?></th>
            <td colspan="2" class="field">
              <input type="text" class="text fv_wp_flowplayer_field_rtmp" id="fv_wp_flowplayer_field_rtmp" name="fv_wp_flowplayer_field_rtmp" value="" style="width: 40%" placeholder="<?php if( !empty($fv_flowplayer_conf["rtmp"]) ) echo $fv_flowplayer_conf["rtmp"]; ?>" />
              &nbsp;<label for="fv_wp_flowplayer_field_rtmp_path"><strong><?php _e('RTMP Path', 'fv-wordpress-flowplayer'); ?></strong></label>
              <input type="text" class="text fv_wp_flowplayer_field_rtmp_path" id="fv_wp_flowplayer_field_rtmp_path" name="fv_wp_flowplayer_field_rtmp_path" value="" style="width: 37%" />
            </td> 
          </tr>  			
          
          <tr id="fv_wp_flowplayer_add_format_wrapper">
            <th scope="row" class="label" style="width: 19%"></th>
            <td class="field" style="width: 50%"><div id="add_format_wrapper"><a href="#" class="partial-underline" onclick="fv_wp_flowplayer_add_format(); return false" style="outline: 0"><span id="add-format">+</span>&nbsp;<?php _e('Add another format', 'fv-wordpress-flowplayer'); ?></a> <?php _e('(i.e. WebM, OGV)', 'fv-wordpress-flowplayer'); ?></div></td>
            <td class="field"><div id="add_rtmp_wrapper"><a href="#" class="partial-underline" onclick="fv_wp_flowplayer_add_rtmp(); return false" style="outline: 0"><span id="add-rtmp">+</span>&nbsp;<?php _e('Add RTMP', 'fv-wordpress-flowplayer'); ?></a></div></td>  				
          </tr>      
          
          <tr<?php if( $fv_flowplayer_conf["interface"]["mobile"] !== 'true' ) echo ' style="display: none"'; ?>>
            <th scope="row" class="label"><label for="fv_wp_flowplayer_field_mobile" class="alignright"><?php _e('Mobile video', 'fv-wordpress-flowplayer'); ?>*</label></th>
            <td class="field" colspan="2"><input type="text" class="text<?php echo $upload_field_class; ?>" id="fv_wp_flowplayer_field_mobile" name="fv_wp_flowplayer_field_mobile" value="" placeholder="<?php _e('Put low-bandwidth video here or leave blank', 'fv-wordpress-flowplayer'); ?>" />
              <?php if ($allow_uploads=='true') { ?>
                <a class="button add_media" href="#"><span class="wp-media-buttons-icon"></span> <?php _e('Add Video', 'fv-wordpress-flowplayer'); ?></a>
              <?php }; //allow uploads splash image ?></td>
          </tr>
          
          <tr>
            <th scope="row" class="label"><label for="fv_wp_flowplayer_field_splash" class="alignright"><?php _e('Splash Image', 'fv-wordpress-flowplayer'); ?></label></th>
            <td class="field" colspan="2"><input type="text" class="text fv_wp_flowplayer_field_splash<?php echo $upload_field_class; ?>" id="fv_wp_flowplayer_field_splash" name="fv_wp_flowplayer_field_splash" value=""/>
              <?php if ($allow_uploads=='true') { ?>
                <a class="button add_media" href="#"><span class="wp-media-buttons-icon"></span> <?php _e('Add Image', 'fv-wordpress-flowplayer'); ?></a>
              <?php }; //allow uploads splash image ?></td>
          </tr>
        		        
          <tr<?php if( $fv_flowplayer_conf["interface"]["subtitles"] !== 'true' ) echo ' style="display: none"'; ?>>
            <th scope="row" class="label"><label for="fv_wp_flowplayer_field_subtitles" class="alignright"><?php _e('Subtitles', 'fv-wordpress-flowplayer'); ?></label></th>
            <td class="field fv-fp-subtitles" colspan="2">
              <div class="fv-fp-subtitle">
                <select class="fv_wp_flowplayer_field_subtitles_lang" name="fv_wp_flowplayer_field_subtitles_lang">
                  <option></option>
                  <?php
                  $aLanguages = flowplayer::get_languages();
                  $aCurrent = explode('-',get_bloginfo('language'));
                  $sCurrent = '';//aCurrent[0];
                  foreach( $aLanguages AS $sCode => $sLabel ) {
                    ?><option value="<?php echo strtolower($sCode); ?>"<?php if( strtolower($sCode) == $sCurrent ) echo ' selected'; ?>><?php echo $sCode; ?>&nbsp;&nbsp;(<?php echo $sLabel; ?>)</option>
                    <?php
                  }
                  ?>
                </select>                
                <input type="text" class="text<?php echo $upload_field_class; ?> fv_wp_flowplayer_field_subtitles" name="fv_wp_flowplayer_field_subtitles" value=""/>
                <?php if ($allow_uploads=='true') { ?>
                  <a class="button add_media" href="#"><span class="wp-media-buttons-icon"></span> <?php _e('Add Subtitles', 'fv-wordpress-flowplayer'); ?></a>
                <a class="fv-fp-subtitle-remove" href="#" style="display: none">X</a>
              <?php }; ?>
              </div>
            </td>
          </tr>
          <tr<?php if( $fv_flowplayer_conf["interface"]["subtitles"] !== 'true' ) echo ' style="display: none"'; ?>>
            <td colspan="2">
            </td>              
            <td>
              <a style="outline: 0" onclick="return fv_flowplayer_language_add(false, <?php echo ( isset($fv_flowplayer_conf["interface"]["playlist_captions"]) && $fv_flowplayer_conf["interface"]["playlist_captions"] == 'true' ) ? 'true' : 'false'; ?>)" class="partial-underline" href="#"><span id="add-rtmp">+</span>&nbsp;<?php _e('Add Another Language', 'fv-wordpress-flowplayer'); ?></a>
            </td>
          </tr>
          
          <tr class="<?php if( isset($fv_flowplayer_conf["interface"]["playlist_captions"]) && $fv_flowplayer_conf["interface"]["playlist_captions"] == 'true' ) echo 'playlist_caption'; ?>" <?php if( !isset($fv_flowplayer_conf["interface"]["playlist_captions"]) || $fv_flowplayer_conf["interface"]["playlist_captions"] !== 'true' ) echo ' style="display: none"'; ?>>
            <th scope="row" class="label"><label for="fv_wp_flowplayer_field_caption" class="alignright"><?php _e('Caption', 'fv-wordpress-flowplayer'); ?></label></th>
            <td class="field" colspan="2"><input type="text" class="text<?php echo $upload_field_class; ?>" id="fv_wp_flowplayer_field_caption" name="fv_wp_flowplayer_field_caption" value=""/></td>
          </tr>
          
          <?php do_action( 'fv_flowplayer_shortcode_editor_item_after' ); ?>
  
        </tbody>
      </table>
    </div><!-- #fv-flowplayer-playlist-->  					      
    <table width="100%">
      <tbody> 
        <?php
        $show_additonal_features = false;
        foreach( $fv_flowplayer_conf["interface"] AS $option ) {
          if( $option == 'true' ) {
            $show_additonal_features = true;
          } else {
            $show_more_features = true;
          }
        }
        ?>
        <tr<?php if( $fv_flowplayer_conf["interface"]["playlist"] !== 'true' ) echo ' style="display: none"'; ?> id="fv_wp_flowplayer_add_format_wrapper">
          <th scope="row" class="label" style="width: 19%"><label for="fv_wp_flowplayer_field_liststyle" class="alignright"><?php _e('Playlist Style', 'fv-wordpress-flowplayer'); ?></label></th>
          <td class="field" style="width: 50%">
            <select id="fv_wp_flowplayer_field_liststyle" name="fv_wp_flowplayer_field_liststyle">
              <option><?php _e('Default',    'fv-wordpress-flowplayer'); ?></option>
              <option><?php _e('Tabs',       'fv-wordpress-flowplayer'); ?></option> 
              <option><?php _e('Prev/Next',  'fv-wordpress-flowplayer'); ?></option>
              <option><?php _e('Vertical',   'fv-wordpress-flowplayer'); ?></option>
              <option><?php _e('Horizontal', 'fv-wordpress-flowplayer'); ?></option>
            </select>          
            <div id="add_rtmp_wrapper" class="alignright"><a style="outline: 0" onclick="return fv_flowplayer_playlist_add(false, <?php echo ( isset($fv_flowplayer_conf["interface"]["playlist_captions"]) && $fv_flowplayer_conf["interface"]["playlist_captions"] == 'true' ) ? 'true' : 'false'; ?>)" class="partial-underline" href="#"><span id="add-rtmp">+</span>&nbsp;<?php _e('Add Playlist Item', 'fv-wordpress-flowplayer'); ?></a></div>
          </td>  				
        </tr>        
        <tr<?php if( !$show_additonal_features ) echo ' style="display: none"';?>>
          <th scope="row" width="19%"></th>
          <td style="text-align: left; padding: 10px 0; text-transform: uppercase;"><?php _e('Additional features', 'fv-wordpress-flowplayer'); ?></td>
        </tr>
        <tr style="display: none">
  				<th valign="top" scope="row" class="label" style="width: 19%"><label for="fv_wp_flowplayer_field_popup"  class="alignright"><?php _e('HTML popup', 'fv-wordpress-flowplayer'); ?></label></th>
  				<td>
            <textarea type="text" id="fv_wp_flowplayer_field_popup" name="fv_wp_flowplayer_field_popup" style="width: 93%"></textarea>
            <p><span class="dashicons dashicons-warning"></span> You are using the legacy popup functionality. Move the popup code <a href="<?php echo site_url(); ?>/wp-admin/options-general.php?page=fvplayer#tab_popups" target="_target">here</a>, then use the drop down below.</p>
          </td>          
  			</tr>
        <tr<?php if( $fv_flowplayer_conf["interface"]["popup"] !== 'true' ) echo ' style="display: none"'; ?>>
  				<th valign="top" scope="row" class="label" style="width: 19%"><label for="fv_wp_flowplayer_field_popup_id" class="alignright"><?php _e('End popup', 'fv-wordpress-flowplayer'); ?></label></th>
  				<td><?php fv_flowplayer_admin_select_popups(array( 'id'=>'fv_wp_flowplayer_field_popup_id', 'show_default' => true ))?></td>
  			</tr>
        <tr<?php if( !isset($fv_flowplayer_conf["interface"]["redirect"]) || $fv_flowplayer_conf["interface"]["redirect"] !== 'true' ) echo ' style="display: none"'; ?>>
  				<th scope="row" class="label"><label for="fv_wp_flowplayer_field_redirect" class="alignright"><?php _e('Redirect to', 'fv-wordpress-flowplayer'); ?></label></th>
  				<td class="field"><input type="text" id="fv_wp_flowplayer_field_redirect" name="fv_wp_flowplayer_field_redirect" style="width: 93%" /></td>
  			</tr>
        <tr<?php if( $fv_flowplayer_conf["interface"]["autoplay"] !== 'true' ) echo ' style="display: none"'; ?>>
  				<th scope="row" class="label"><label for="fv_wp_flowplayer_field_autoplay" class="alignright"><?php _e('Autoplay', 'fv-wordpress-flowplayer'); ?></label></th>
  				<td class="field">
            <select id="fv_wp_flowplayer_field_autoplay" name="fv_wp_flowplayer_field_autoplay">
              <option><?php _e('Default', 'fv-wordpress-flowplayer'); ?></option>
              <option><?php _e('On', 'fv-wordpress-flowplayer'); ?></option>
              <option><?php _e('Off', 'fv-wordpress-flowplayer'); ?></option>
            </select>
          </td>
  			</tr>
        <tr<?php if( !isset($fv_flowplayer_conf["interface"]["loop"]) || $fv_flowplayer_conf["interface"]["loop"] !== 'true' ) { echo ' style="display: none"'; } ?>>
  				<th scope="row" class="label"><label for="fv_wp_flowplayer_field_loop" class="alignright"><?php _e('Loop', 'fv-wordpress-flowplayer'); ?></label></th>
  				<td class="field"><input type="checkbox" id="fv_wp_flowplayer_field_loop" name="fv_wp_flowplayer_field_loop" /></td>
  			</tr>   
        <tr<?php if( !isset($fv_flowplayer_conf["interface"]["splashend"]) || $fv_flowplayer_conf["interface"]["splashend"] !== 'true' ) { echo ' style="display: none"'; } ?>>
          <th scope="row" class="label">
            <label for="fv_wp_flowplayer_field_splashend"><?php _e('Splash end', 'fv-wordpress-flowplayer'); ?></label>
          </th>
          <td>
            <input type="checkbox" id="fv_wp_flowplayer_field_splashend" name="fv_wp_flowplayer_field_splashend" /> <?php _e('(show splash image at the end)', 'fv-wordpress-flowplayer'); ?>
          </td> 
        </tr>    
        <tr<?php if( $fv_flowplayer_conf["interface"]["embed"] !== 'true' ) echo ' style="display: none"'; ?>>
  				<th scope="row" class="label"><label for="fv_wp_flowplayer_field_embed" class="alignright"><?php _e('Embedding', 'fv-wordpress-flowplayer'); ?></label></th>
  				<td class="field">
            <select id="fv_wp_flowplayer_field_embed" name="fv_wp_flowplayer_field_embed">
              <option><?php _e('Default', 'fv-wordpress-flowplayer'); ?></option>
              <option><?php _e('On', 'fv-wordpress-flowplayer'); ?></option>
              <option><?php _e('Off', 'fv-wordpress-flowplayer'); ?></option>
            </select>
          </td>
  			</tr>           
        <tr<?php if( $fv_flowplayer_conf["interface"]["ads"] !== 'true' ) echo ' style="display: none"'; ?>>
  				<th valign="top" scope="row" class="label" style="width: 19%"><label for="fv_wp_flowplayer_field_ad" class="alignright"><?php _e('Ad code', 'fv-wordpress-flowplayer'); ?></label></th>
  				<td>
  					<textarea type="text" id="fv_wp_flowplayer_field_ad" name="fv_wp_flowplayer_field_ad" style="width: 93%"></textarea>
  				</td>
  			</tr> 
  			<tr<?php if( $fv_flowplayer_conf["interface"]["ads"] !== 'true' ) echo ' style="display: none"'; ?>><th></th>
  				<td class="field">
  					<label for="fv_wp_flowplayer_field_ad_width"><?php _e('Width', 'fv-wordpress-flowplayer'); ?> <small>(px)</small></label> <input type="text" id="fv_wp_flowplayer_field_ad_width" name="fv_wp_flowplayer_field_ad_width" style="width: 19%; margin-right: 25px;"  value=""/> <label for="fv_wp_flowplayer_field_ad_height"><?php _e('Height', 'fv-wordpress-flowplayer'); ?> <small>(px)</small></label> <input type="text" id="fv_wp_flowplayer_field_ad_height" name="fv_wp_flowplayer_field_ad_height" style="width: 19%" value=""/><br />
  					<input type="checkbox" id="fv_wp_flowplayer_field_ad_skip" name="fv_wp_flowplayer_field_ad_skip" /> <?php _e('Skip global ad in this video', 'fv-wordpress-flowplayer'); ?>  					
  				</td>
  			</tr>			
        <tr<?php if( $fv_flowplayer_conf["interface"]["align"] !== 'true' ) echo ' style="display: none"'; ?>>
  				<th valign="top" scope="row" class="label" style="width: 19%"><label for="fv_wp_flowplayer_field_align" class="alignright"><?php _e('Align', 'fv-wordpress-flowplayer'); ?></label></th>
  				<td>
            <select id="fv_wp_flowplayer_field_align" name="fv_wp_flowplayer_field_align">
              <option><?php _e('Default', 'fv-wordpress-flowplayer'); ?></option>
              <option><?php _e('Left', 'fv-wordpress-flowplayer'); ?></option>
              <option><?php _e('Right', 'fv-wordpress-flowplayer'); ?></option>
            </select>
  				</td>
  			</tr>
        <tr<?php if( !isset($fv_flowplayer_conf["interface"]["controlbar"]) || $fv_flowplayer_conf["interface"]["controlbar"] !== 'true' ) echo ' style="display: none"'; ?>>
  				<th valign="top" scope="row" class="label" style="width: 19%"><label for="fv_wp_flowplayer_field_controlbar" class="alignright"><?php _e('Controlbar', 'fv-wordpress-flowplayer'); ?></label></th>
  				<td>
            <select id="fv_wp_flowplayer_field_controlbar" name="fv_wp_flowplayer_field_controlbar">
              <option><?php _e('Default', 'fv-wordpress-flowplayer'); ?></option>
              <option><?php _e('Yes', 'fv-wordpress-flowplayer'); ?></option>
              <option><?php _e('No', 'fv-wordpress-flowplayer'); ?></option>
            </select>
  				</td>
  			</tr>
        <tr<?php if( !isset($fv_flowplayer_conf["interface"]['live']) || $fv_flowplayer_conf["interface"]["live"] !== 'true' ) { echo ' style="display: none"'; } ?>>
  				<th scope="row" class="label"><label for="fv_wp_flowplayer_field_live" class="alignright"><?php _e('Live stream', 'fv-wordpress-flowplayer'); ?></label></th>
  				<td class="field"><input type="checkbox" id="fv_wp_flowplayer_field_live" name="fv_wp_flowplayer_field_live" /></td>
  			</tr>
        <tr<?php if( !isset($fv_flowplayer_conf["interface"]['speed']) || $fv_flowplayer_conf["interface"]["speed"] !== 'true' ) { echo ' style="display: none"'; } ?>>
  				<th scope="row" class="label"><label for="fv_wp_flowplayer_field_speed" class="alignright"><?php _e('Speed Buttons', 'fv-wordpress-flowplayer'); ?></label></th>
  				<td class="field">
            <select id="fv_wp_flowplayer_field_speed" name="fv_wp_flowplayer_field_speed">
              <option><?php _e('Default', 'fv-wordpress-flowplayer'); ?></option>
              <option><?php _e('Yes', 'fv-wordpress-flowplayer'); ?></option>
              <option><?php _e('No', 'fv-wordpress-flowplayer'); ?></option>
            </select>
          </td>
  			</tr>                  
        <?php do_action( 'fv_flowplayer_shortcode_editor_after' ); ?>        
  			<tr>
  				<th scope="row" class="label"></th>					
            	<td  style="padding-top: 20px;"><input type="button" value="<?php _e('Insert', 'fv-wordpress-flowplayer'); ?>" name="insert" id="fv_wp_flowplayer_field_insert-button" class="button-primary alignleft" onclick="fv_wp_flowplayer_submit();" />
  				</td>
  			</tr>
            <?php if( !$allow_uploads && current_user_can('manage_options') ) { ?> 
            <tr>
              <td colspan="2">
              	<div class="fv-wp-flowplayer-notice"><?php _e('Admin note: Video uploads are currently disabled, set Allow User Uploads to true in', 'fv-wordpress-flowplayer'); ?> <a href="<?php echo site_url(); ?>/wp-admin/options-general.php?page=fvplayer"><?php _e('Settings', 'fv-wordpress-flowplayer'); ?></a></div>
              </td>
            </tr>            
            <?php } ?>
            <?php if( current_user_can('manage_options') ) { ?> 
            <tr>
              <td colspan="2">
              	<div class="fv-wp-flowplayer-notice fv-wp-flowplayer-note"><?php _e('Admin note: Enable more per video features in Interface options in', 'fv-wordpress-flowplayer'); ?> <a href="<?php echo site_url(); ?>/wp-admin/options-general.php?page=fvplayer#interface"><?php _e('Settings', 'fv-wordpress-flowplayer'); ?></a></div>
              </td>
            </tr>            
            <?php } ?>
			<tr<?php if( $fv_flowplayer_conf["interface"]["mobile"] !== 'true' ) echo ' style="display: none"'; ?>>
			  <td colspan="2">* - <?php _e('currently not working with playlist', 'fv-wordpress-flowplayer'); ?> </td>
			</tr>
  		</tbody>
  	</table>
  </div>
</div>