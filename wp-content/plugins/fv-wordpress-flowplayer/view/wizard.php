<?php
/*  FV Wordpress Flowplayer - HTML5 video player with Flash fallback    
    Copyright (C) 2016  Foliovision

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

  global $fv_wp_flowplayer_ver;
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
  
  function fv_flowplayer_admin_select_popups($aArgs) {

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
  
  function fv_player_shortcode_row( $args ) {
    $fv_flowplayer_conf = get_option( 'fvwpflowplayer' );
    $args = wp_parse_args( $args, array(
                          'class' => false,
                          'dropdown' => array( 'Default', 'On', 'Off' ),
                          'id' => false,
                          'label' => '',
                          'live' => true,
                          'name' => '',
                          'playlist_label' => false,
                         ) );
    extract($args);
    
    if( $id ) {
      $id = ' id="'.$id.'"';
    }    
    
    $class .= !isset($fv_flowplayer_conf["interface"][$name]) || $fv_flowplayer_conf["interface"][$name] !== 'true' ? ' fv_player_interface_hide' : '';
    if( $class ) {
      $class = ' class="'.$class.'"';
    }
    
    $live = !$live ? ' data-live-update="false"' : '';
    
    $playlist_label = $playlist_label ? ' data-playlist-label="' . __( $playlist_label, 'fv_flowplayer') . '"  data-single-label="' . __( $label, 'fv_flowplayer') . '"' : '';
    
    ?>
      <tr<?php echo $id.$class; ?>>
        <th scope="row" class="label"><label for="fv_wp_flowplayer_field_<?php echo $name; ?>" class="alignright" <?php echo $playlist_label; ?>><?php _e( $label, 'fv_flowplayer'); ?></label></th>
        <td class="field">
          <select id="fv_wp_flowplayer_field_<?php echo $name; ?>" name="fv_wp_flowplayer_field_<?php echo $name; ?>"<?php echo $live; ?>>
            <?php foreach( $dropdown AS $option ) : ?>
              <?php if( is_array($option) ) : ?>
                <option value="<?php echo $option[0]; ?>"><?php _e( $option[1], 'fv_flowplayer' ); ?></option>
              <?php else : ?>
                <option><?php _e( $option, 'fv_flowplayer' ); ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </td>
      </tr>
    <?php
  }
  
	$fv_flowplayer_helper_tag = ( is_plugin_active('jetpack/jetpack.php') ) ? 'b' : 'span';
?>
<link rel="stylesheet" type="text/css" href="<?php echo flowplayer::get_plugin_url().'/css/shortcode-editor.css'; ?>?ver=<?php echo $fv_wp_flowplayer_ver; ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo flowplayer::get_plugin_url().'/css/s3-browser.css'; ?>?ver=<?php echo $fv_wp_flowplayer_ver; ?>" />
  
<script>
var fvwpflowplayer_helper_tag = '<?php echo $fv_flowplayer_helper_tag ?>';
var fv_wp_flowplayer_re_edit = /\[[^\]]*?<<?php echo $fv_flowplayer_helper_tag; ?>[^>]*?rel="FCKFVWPFlowplayerPlaceholder"[^>]*?>.*?<\/<?php echo $fv_flowplayer_helper_tag; ?>>.*?[^\\]\]/mi;
var fv_wp_flowplayer_re_insert = /<<?php echo $fv_flowplayer_helper_tag; ?>[^>]*?rel="FCKFVWPFlowplayerPlaceholder"[^>]*?>.*?<\/<?php echo $fv_flowplayer_helper_tag; ?>>/gi;
var fv_Player_site_base = "<?php echo home_url('/'); ?>";
<?php global $fv_fp; if( $fv_fp->_get_option('postthumbnail') || $fv_fp->_get_option( array('integrations','featured_img') ) ) : ?>
var fv_flowplayer_set_post_thumbnail_id = <?php echo $post_id; ?>;
var fv_flowplayer_set_post_thumbnail_nonce = '<?php echo wp_create_nonce( "set_post_thumbnail-$post_id" ); ?>';
<?php endif; ?>
var fv_flowplayer_preview_nonce = '<?php echo wp_create_nonce( "fv-player-preview-".get_current_user_id() ); ?>';
</script>

<div style="display: none">
  <div id="fv-player-shortcode-editor">
    <div id="fv-player-shortcode-editor-editor">
      <table>
        <tr>
          <td class="fv-player-shortcode-editor-left">
            <div id="fv-player-shortcode-editor-preview">
              <div id="fv-player-shortcode-editor-preview-spinner" class="fv-player-shortcode-editor-helper"></div>
              <div id="fv-player-shortcode-editor-preview-no" class="fv-player-shortcode-editor-helper">
                <h1><?php _e('Add your video', 'fv-wordpress-flowplayer'); ?></h1>
              </div>
              <div id="fv-player-shortcode-editor-preview-new-tab" class="fv-player-shortcode-editor-helper">
                <a class="button" href="" target="_blank"><?php _e('Playlist too long, click here for preview', 'fv-wordpress-flowplayer'); ?></a>
              </div>
							<div id="fv-player-shortcode-editor-preview-target"></div>
              <input type="button" value="<?php _e('Refresh preview', 'fv_flowplayer'); ?>"  class="button extra-field"  style="display:none;" id="fv-player-shortcode-editor-preview-iframe-refresh" />
            </div>
          </td>
          <td class="fv-player-shortcode-editor-right">
            <input type="text" name="fv_wp_flowplayer_field_player_name" id="fv_wp_flowplayer_field_player_name" placeholder="Playlist name" /> <span id="player_id_top_text"></span>
            <div class="fv-player-tabs-header">
              <h2 class="fv-player-playlist-item-title nav-tab nav-tab-active"></h2>
              <h2 class="nav-tab-wrapper hide-if-no-js">
                <a href="#" class="nav-tab hide-if-singular hide-if-playlist" style="outline: 0;" data-tab="fv-player-tab-playlist"><?php _e('Playlist', 'fv-wordpress-flowplayer'); ?></a>
                <a href="#" class="nav-tab nav-tab-active hide-if-playlist-active" style="outline: 0;" data-tab="fv-player-tab-video-files"><?php _e('Video', 'fv-wordpress-flowplayer'); ?></a>
                <a href="#" class="nav-tab hide-if-playlist-active" style="outline: 0;" data-tab="fv-player-tab-subtitles"><?php _e('Subtitles', 'fv-wordpress-flowplayer'); ?></a>
                <a href="#" class="nav-tab hide-if-playlist" style="outline: 0;" data-tab="fv-player-tab-options"><?php _e('Options', 'fv-wordpress-flowplayer'); ?></a>
                <a href="#" class="nav-tab hide-if-playlist" style="outline: 0;" data-tab="fv-player-tab-actions"><?php _e('Actions', 'fv-wordpress-flowplayer'); ?></a>
                <?php do_action('fv_player_shortcode_editor_tab'); ?>
              </h2>
            </div>
            <div class="fv-player-tabs">
              
              <div class="fv-player-tab fv-player-tab-playlist" style="">
                <div id="fv-player-list-thumb-toggle">
                  <a href="#" id="fv-player-list-list-view" ><span class="dashicons dashicons-list-view"><span class="screen-reader-text">List view</span></span></a>
                  <a href="#" id="fv-player-list-thumb-view" class="active" data-title="<?php _e('Add splash images to enable thumbnail view', 'fv_flowplayer');?>"><span class="dashicons dashicons-exerpt-view"><span class="screen-reader-text">Thumbnail view</span></span></a>
                </div>
                <table class="wp-list-table widefat fixed striped media" width="100%">
                  <thead>
                    <tr>
                      <th><a>Video</a></th>
                      <th><a<?php if( !isset($fv_flowplayer_conf["interface"]["playlist_captions"]) || $fv_flowplayer_conf["interface"]["playlist_captions"] != 'true' ) echo ' class="fv_player_interface_hide"'; ?>>Title</a></th>
                      <!--<th>Dimension</th>
                      <th>Time</th>-->
                    </tr>  
                  </thead>
                  
                  
                  <tbody>
                    <tr>
                      <!--<td class="fvp_item_sort">&nbsp;&nbsp;&nbsp;</td>-->
                      <!--<td class="fvp_item_video"><strong class="has-media-icon">(new video)</strong></td>-->
                      <td class="title column-title" data-colname="File">		
                        <div class="fvp_item_video-side-by-side">
                          <a class="fvp_item_video-thumbnail"></a>
                        </div>
                        <div class="fvp_item_video-side-by-side">
                          <a class="fvp_item_video-filename"></a><br>
                          <a class="fvp_item_remove" role="button">Delete</a>
                        </div>
                      </td>
                      
                      <td class="fvp_item_caption"><div<?php if( !isset($fv_flowplayer_conf["interface"]["playlist_captions"]) || $fv_flowplayer_conf["interface"]["playlist_captions"] != 'true' ) echo ' class="fv_player_interface_hide"'; ?>>-</div></td>
                      <!--<td class="fvp_item_dimension">-</td>-->
                      <!--<td class="fvp_item_time">-</td>-->
                      <!--<td class="fvp_item_remove"><div></div></td>-->
                    </tr> 
                  </tbody>        
                </table>

                <input type="button" value="<?php _e('Insert', 'fv_flowplayer'); ?>" name="insert" class="button-primary extra-field fv_player_field_insert-button" onclick="fv_wp_flowplayer_submit();" />
                &nbsp;&nbsp;&nbsp;&nbsp;<span  class="button"  onclick="fv_flowplayer_playlist_add();"><?php _e(' + Add playlist item', 'fv_flowplayer');?></span>
                  
              </div>
              
              <div class="fv-player-tab fv-player-tab-video-files">
                <table class="slidetoggle describe fv-player-playlist-item" width="100%" data-index="0">
                  <tbody>
                    <?php do_action('fv_flowplayer_shortcode_editor_before'); ?>
                    <tr>
                      <th scope="row" class="label" style="width: 19%;vertical-align:middle;">
                        <a class="alignleft fv_wp_flowplayer_playlist_remove" href="#" onclick="return fv_wp_flowplayer_playlist_remove(this)"><?php _e('(remove)', 'fv_flowplayer'); ?></a>
                        <label for="fv_wp_flowplayer_field_src" class="alignright"><?php _e('Video', 'fv_flowplayer'); ?></label>
                      </th>
                      <td colspan="2" class="field"><input type="text" class="text<?php echo $upload_field_class; ?>" id="fv_wp_flowplayer_field_src" name="fv_wp_flowplayer_field_src" value="" />
                        <?php if ($allow_uploads == "true") { ?>      
                          <a class="button add_media" href="#"><span class="wp-media-buttons-icon"></span> <?php _e('Add Video', 'fv_flowplayer'); ?></a>
                        <?php }; //allow uplads video ?></td>
                    </tr>

                    <tr style="display: none" id="fv_wp_flowplayer_file_info">
                      <th></th>
                      <td colspan="2">
                        <?php _e('Video Duration', 'fv_flowplayer'); ?>: <span id="fv_wp_flowplayer_file_duration"></span><br />
                        <?php _e('File size', 'fv_flowplayer'); ?>: <span id="fv_wp_flowplayer_file_size"></span>
                      </td>
                    </tr>

                    <tr style="display: none;" class="fv_wp_flowplayer_field_src1_wrapper">
                      <th scope="row" class="label" style="width: 19%"></th>
                      <td colspan="2" class="field"><input type="text" class="text<?php echo $upload_field_class; ?>" id="fv_wp_flowplayer_field_src1" name="fv_wp_flowplayer_field_src1" value="" placeholder="<?php _e('Another format', 'fv-wordpress-flowplayer'); ?>" />
                        <?php if ($allow_uploads == "true") { ?> 
                          <a class="button add_media" href="#"><span class="wp-media-buttons-icon"></span> <?php _e('Add Video', 'fv_flowplayer'); ?></a>
                        <?php }; //allow uplads video ?>
                      </td>
                    </tr>

                    <tr style="display: none;" class="fv_wp_flowplayer_field_src2_wrapper">
                      <th scope="row" class="label" style="width: 19%"></th>
                      <td colspan="2" class="field"><input type="text" class="text<?php echo $upload_field_class; ?>" id="fv_wp_flowplayer_field_src2" name="fv_wp_flowplayer_field_src2" value="" placeholder="<?php _e('Another format', 'fv-wordpress-flowplayer'); ?>" />
                        <?php if ($allow_uploads == "true") { ?>  
                          <a class="button add_media" href="#"><span class="wp-media-buttons-icon"></span> <?php _e('Add Video', 'fv_flowplayer'); ?></a>
                        <?php }; //allow uplads video ?>
                      </td>    			
                    </tr>
                    
                    <tr class="hide-if-playlist">
                      <th>
                        <label for="fv_wp_flowplayer_field_width" class="alignright"><?php _e('Size', 'fv_flowplayer'); ?></label> 
                      </th>
                      <td class="field" colspan="2">
                        <input type="text" id="fv_wp_flowplayer_field_width" class="fv_wp_flowplayer_field_width" name="fv_wp_flowplayer_field_width" style="width: 19%; margin-right: 25px;"  value="" placeholder="<?php _e('Width', 'fv_flowplayer'); ?>"/>
                        <input type="text" id="fv_wp_flowplayer_field_height" class="fv_wp_flowplayer_field_height" name="fv_wp_flowplayer_field_height" style="width: 19%" value="" placeholder="<?php _e('Height', 'fv_flowplayer'); ?>"/>
                      </td>
                    </tr>

                    <tr class="fv_wp_flowplayer_field_rtmp_wrapper">
                      <th scope="row" class="label"><label for="fv_wp_flowplayer_field_rtmp" class="alignright"><?php _e('RTMP Server', 'fv_flowplayer'); ?></label> <?php if (!empty($fv_flowplayer_conf["rtmp"]) && $fv_flowplayer_conf["rtmp"]!= 'false') : ?>(<abbr title="<?php _e('Leave empty to use Flash streaming server from plugin settings', 'fv_flowplayer'); ?>">?</abbr>)<?php endif; ?></th>
                      <td colspan="2" class="field">
                        <input type="text" class="text fv_wp_flowplayer_field_rtmp" id="fv_wp_flowplayer_field_rtmp" name="fv_wp_flowplayer_field_rtmp" value="" style="width: 40%" placeholder="<?php if (!empty($fv_flowplayer_conf["rtmp"]) && $fv_flowplayer_conf["rtmp"]!= 'false') echo $fv_flowplayer_conf["rtmp"]; ?>" />
                        &nbsp;<label for="fv_wp_flowplayer_field_rtmp_path"><strong><?php _e('RTMP Path', 'fv_flowplayer'); ?></strong></label>
                        <input type="text" class="text fv_wp_flowplayer_field_rtmp_path" id="fv_wp_flowplayer_field_rtmp_path" name="fv_wp_flowplayer_field_rtmp_path" value="" style="width: 37%" />
                      </td> 
                    </tr>  			

                    <tr id="fv_wp_flowplayer_add_format_wrapper">
                      <th scope="row" class="label"></th>
                      <td class="field" style="width: 50%"><div id="add_format_wrapper"><a href="#" class="partial-underline" onclick="fv_wp_flowplayer_add_format(); return false" style="outline: 0"><span id="add-format">+</span>&nbsp;<?php _e('Add another format', 'fv_flowplayer'); ?></a> <?php _e('(i.e. WebM, OGV)', 'fv_flowplayer'); ?></div></td>
                      <td class="field"><div class="add_rtmp_wrapper"><a href="#" class="partial-underline" onclick="fv_wp_flowplayer_add_rtmp(this); return false" style="outline: 0"><span id="add-rtmp">+</span>&nbsp;<?php _e('Add RTMP', 'fv_flowplayer'); ?></a></div></td>  				
                    </tr>      

                    <tr <?php if( !isset($fv_flowplayer_conf["interface"]["mobile"]) || $fv_flowplayer_conf["interface"]["mobile"] !== 'true' ) echo ' class="fv_player_interface_hide"'; ?> class="first-item-only">
                      <th scope="row" class="label"><label for="fv_wp_flowplayer_field_mobile" class="alignright"><?php _e('Mobile video', 'fv_flowplayer'); ?></label></th>
                      <td class="field" colspan="2"><input type="text" class="text<?php echo $upload_field_class; ?>" id="fv_wp_flowplayer_field_mobile" name="fv_wp_flowplayer_field_mobile" value="" placeholder="<?php _e('Put low-bandwidth video here or leave blank', 'fv_flowplayer'); ?>" />
                        <?php if ($allow_uploads == 'true') { ?>
                          <a class="button add_media" href="#"><span class="wp-media-buttons-icon"></span> <?php _e('Add Video', 'fv_flowplayer'); ?></a>
                        <?php }; //allow uploads splash image ?></td>
                    </tr>

                    <tr>
                      <th scope="row" class="label"><label for="fv_wp_flowplayer_field_splash" class="alignright"><?php _e('Splash Image', 'fv_flowplayer'); ?></label></th>
                      <td class="field" colspan="2"><input type="text" class="text fv_wp_flowplayer_field_splash<?php echo $upload_field_class; ?>" id="fv_wp_flowplayer_field_splash" name="fv_wp_flowplayer_field_splash" value=""/>
                        <?php if ($allow_uploads == 'true') { ?>
                          <a class="button add_media" href="#"><span class="wp-media-buttons-icon"></span> <?php _e('Add Image', 'fv_flowplayer'); ?></a>
                        <?php }; //allow uploads splash image ?></td>
                    </tr>
                    
                    <tr class="<?php if (isset($fv_flowplayer_conf["interface"]["splash_text"]) && $fv_flowplayer_conf["interface"]["splash_text"] == 'true') echo 'splash_text'; else echo 'fv_player_interface_hide'; ?> first-item-only" >
                      <th scope="row" class="label"><label for="fv_wp_flowplayer_field_splash_text" class="alignright"><?php _e('Splash Text', 'fv_flowplayer'); ?></label></th>
                      <td class="field" colspan="2"><input type="text" class="text<?php echo $upload_field_class; ?>" id="fv_wp_flowplayer_field_splash_text" name="fv_wp_flowplayer_field_splash_text" value=""/></td>
                    </tr>
                    
                    <tr class="<?php if (isset($fv_flowplayer_conf["interface"]["playlist_captions"]) && $fv_flowplayer_conf["interface"]["playlist_captions"] == 'true') echo 'playlist_caption'; else echo 'fv_player_interface_hide'; ?>" >
                      <th scope="row" class="label"><label for="fv_wp_flowplayer_field_caption" class="alignright"><?php _e('Title', 'fv_flowplayer'); ?></label></th>
                      <td class="field" colspan="2"><input type="text" class="text<?php echo $upload_field_class; ?>" id="fv_wp_flowplayer_field_caption" name="fv_wp_flowplayer_field_caption" value=""/></td>
                    </tr>

                    <tr class="fv_player_interface_hide">
                        <th scope="row" class="label"><label for="fv_wp_flowplayer_field_live" class="alignright"><?php _e('Live stream', 'fv_flowplayer'); ?></label></th>
                        <td class="field"><input type="checkbox" id="fv_wp_flowplayer_field_live" name="fv_wp_flowplayer_field_live" /></td>
                    </tr>
                    
                    <tr class="fv_player_interface_hide">
                        <th scope="row" class="label"><label for="fv_wp_flowplayer_field_audio" class="alignright"><?php _e('Audio stream', 'fv_flowplayer'); ?></label></th>
                        <td class="field"><input type="checkbox" id="fv_wp_flowplayer_field_audio" name="fv_wp_flowplayer_field_audio" /></td>
                    </tr>                    

                    <?php do_action('fv_flowplayer_shortcode_editor_item_after'); ?>     

                    <?php if (!$allow_uploads && current_user_can('manage_options')) : ?> 
                      <tr>
                        <td colspan="2">
                          <div class="fv-wp-flowplayer-notice"><?php _e('Admin note: Video uploads are currently disabled, set Allow User Uploads to true in', 'fv_flowplayer'); ?> <a href="<?php echo site_url(); ?>/wp-admin/options-general.php?page=fvplayer"><?php _e('Settings', 'fv_flowplayer'); ?></a></div>
                        </td>
                      </tr>            
                    <?php endif; ?>
                    <tr class="submit-button-wrapper">
                      <td></td>
                      <td>
                        <input type="button" value="<?php _e('Insert', 'fv_flowplayer'); ?>" name="insert" class="button-primary extra-field fv_player_field_insert-button" onclick="fv_wp_flowplayer_submit();" />    
                        <a onclick="return fv_flowplayer_playlist_show()" class="playlist_edit button-primary <?php if( !isset($fv_flowplayer_conf["interface"]["playlist"]) || $fv_flowplayer_conf["interface"]["playlist"] !== 'true' ) echo ' fv_player_interface_hide'; ?>" href="#" data-create="<?php _e('Add another video into playlist', 'fv_flowplayer'); ?>" data-edit="<?php _e('Back to playlist', 'fv_flowplayer'); ?>"><?php _e('Add another video into playlist', 'fv_flowplayer'); ?></a>
                      </td>
                    </tr>
                  </tbody>
                </table>      
              </div>

              <div class="fv-player-tab fv-player-tab-subtitles" style="display: none">
                <table width="100%" data-index="0">

                <?php do_action('fv_flowplayer_shortcode_editor_subtitles_tab_prepend'); ?>

                  <tr <?php if( !isset($fv_flowplayer_conf["interface"]["subtitles"]) || $fv_flowplayer_conf["interface"]["subtitles"] !== 'true' ) echo ' class="fv_player_interface_hide"'; ?>>
                    <th scope="row" class="label"><label for="fv_wp_flowplayer_field_subtitles" class="alignright"><?php _e('Subtitles', 'fv_flowplayer'); ?></label></th>
                    <td class="field fv-fp-subtitles" colspan="2">
                      <div class="fv-fp-subtitle">
                        <select class="fv_wp_flowplayer_field_subtitles_lang" name="fv_wp_flowplayer_field_subtitles_lang">
                          <option></option>
                          <?php
                          $aLanguages = flowplayer::get_languages();
                          $aCurrent = explode('-', get_bloginfo('language'));
                          $sCurrent = ''; //aCurrent[0];
                          foreach ($aLanguages AS $sCode => $sLabel) {
                            ?><option value="<?php echo strtolower($sCode); ?>"<?php if (strtolower($sCode) == $sCurrent) echo ' selected'; ?>><?php echo $sCode; ?>&nbsp;&nbsp;(<?php echo $sLabel; ?>)</option>
                            <?php
                          }
                          ?>
                        </select>                
                        <input type="text" class="text<?php echo $upload_field_class; ?> fv_wp_flowplayer_field_subtitles" name="fv_wp_flowplayer_field_subtitles" value=""/>
                        <?php if ($allow_uploads == 'true') { ?>
                          <a class="button add_media" href="#"><span class="wp-media-buttons-icon"></span> <?php _e('Add Subtitles', 'fv_flowplayer'); ?></a>
                          <a class="fv-fp-subtitle-remove" href="#" style="display: none">X</a>
                        <?php }; ?>
                        <div style="clear:both"></div>
                      </div>
                    </td>
                  </tr>
                  <tr class="submit-button-wrapper">
                    <td colspan="2">
                    </td>              
                    <td>
                      <a class="fv_flowplayer_language_add_link" style="outline: 0" onclick="return fv_flowplayer_language_add(false, <?php echo ( isset($fv_flowplayer_conf["interface"]["playlist_captions"]) && $fv_flowplayer_conf["interface"]["playlist_captions"] == 'true' ) ? 'true' : 'false'; ?>)" class="partial-underline" href="#"><span class="add-subtitle-lang">+</span>&nbsp;<?php _e('Add Another Language', 'fv_flowplayer'); ?></a>
                    </td>
                  </tr>
                  <tr class="submit-button-wrapper">
                    <td></td>
                    <td>
                      <input type="button" value="<?php _e('Insert', 'fv_flowplayer'); ?>" name="insert" class="button-primary extra-field fv_player_field_insert-button" onclick="fv_wp_flowplayer_submit();" />    
                      <a style="outline: 0" onclick="return fv_flowplayer_playlist_show()" class="playlist_edit button-primary <?php if( !isset($fv_flowplayer_conf["interface"]["playlist"]) || $fv_flowplayer_conf["interface"]["playlist"] !== 'true' ) echo ' fv_player_interface_hide'; ?>" href="#" data-create="<?php _e('Add another video into playlist', 'fv_flowplayer'); ?>" data-edit="<?php _e('Back to playlist', 'fv_flowplayer'); ?>"><?php _e('Add another video into playlist', 'fv_flowplayer'); ?></a>
                    </td>
                  </tr>
                </table>
              </div>

              <div class="fv-player-tab fv-player-tab-options" style="display: none">
                <table width="100%">
                  
                  <tr class="hide-if-singular">
                    <th>
                      <label for="fv_wp_flowplayer_field_width" class="alignright"><?php _e('Size', 'fv_flowplayer'); ?></label> 
                    </th>
                    <td class="field" colspan="2">
                      <input type="text" id="fv_wp_flowplayer_field_width" class="fv_wp_flowplayer_field_width" name="fv_wp_flowplayer_field_width" style="width: 19%; margin-right: 25px;"  value="" placeholder="<?php _e('Width', 'fv_flowplayer'); ?>"/>
                      <input type="text" id="fv_wp_flowplayer_field_height" class="fv_wp_flowplayer_field_height" name="fv_wp_flowplayer_field_height" style="width: 19%" value="" placeholder="<?php _e('Height', 'fv_flowplayer'); ?>"/>
                    </td>
                  </tr>
                  
                  <?php fv_player_shortcode_row( array( 'label' => 'Autoplay', 'name' => 'autoplay' ) ); ?>
                  <?php fv_player_shortcode_row( array( 'label' => 'Embedding', 'name' => 'embed' ) ); ?>
                  <?php fv_player_shortcode_row( array( 'label' => 'Align', 'name' => 'align', 'dropdown' => array( 'Default', 'Left', 'Right' ) ) ); ?>
                  <?php fv_player_shortcode_row( array( 'label' => 'Controlbar', 'name' => 'controlbar', 'dropdown' => array( 'Default', 'Yes', 'No' ) ) ); ?>
                   <?php fv_player_shortcode_row( array( 'label' => 'Sticky video', 'name' => 'sticky' ) ); ?>
                  <?php fv_player_shortcode_row( array( 'label' => 'Playlist Style', 'name' => 'playlist', 'dropdown' => array( 'Default', 'Tabs', 'Prev/Next', 'Vertical', 'Horizontal', 'Text', 'Slider' ), 'class' => 'hide-if-singular', 'id' => 'fv_wp_flowplayer_add_format_wrapper' ) ); ?>
                  <?php fv_player_shortcode_row( array( 'label' => 'Sharing Buttons', 'name' => 'share', 'dropdown' => array( 'Default', 'Yes', 'No', 'Custom' ) ) ); ?>
                  
                  <tr id="fv_wp_flowplayer_field_share_custom" style="display: none">
                    <th scope="row" class="label"><label for="fv_wp_flowplayer_field_lightbox" class="alignright">Sharing Properties</label></th>
                    <td class="field">    
                      <input type="text" id="fv_wp_flowplayer_field_share_url" name="fv_wp_flowplayer_field_share_url" style="width: 49%" placeholder="URL" />
                      <input type="text" id="fv_wp_flowplayer_field_share_title" name="fv_wp_flowplayer_field_share_title" style="width: 49%" placeholder="Title" />
                    </td>
                  </tr>                  
                  
                  <?php fv_player_shortcode_row( array( 'label' => 'Speed Buttons', 'name' => 'speed', 'dropdown' => array( 'Default', 'Yes', 'No' ) ) ); ?>
                                    
                  <?php fv_player_shortcode_row( array( 'label' => 'Playlist auto advance', 'name' => 'playlist_advance' ) ); ?>
                  
                  <?php do_action('fv_flowplayer_shortcode_editor_tab_options'); ?>
                  
                  <tr class="submit-button-wrapper">
                    <td></td>
                    <td>
                      <input type="button" value="<?php _e('Insert', 'fv_flowplayer'); ?>" name="insert" class="button-primary extra-field fv_player_field_insert-button" onclick="fv_wp_flowplayer_submit();" />    
                      <a style="outline: 0" onclick="return fv_flowplayer_playlist_show()" class="playlist_edit button-primary <?php if( !isset($fv_flowplayer_conf["interface"]["playlist"]) || $fv_flowplayer_conf["interface"]["playlist"] !== 'true' ) echo ' fv_player_interface_hide'; ?>" href="#" data-create="<?php _e('Add another video into playlist', 'fv_flowplayer'); ?>" data-edit="<?php _e('Back to playlist', 'fv_flowplayer'); ?>"><?php _e('Add another video into playlist', 'fv_flowplayer'); ?></a>
                    </td>
                  </tr>
                </table>
              </div>

              <div class="fv-player-tab fv-player-tab-actions" style="display: none">
                <table width="100%">
                  <?php fv_player_shortcode_row( array('label' => 'End of video',
                                                       'playlist_label' => 'End of playlist',
                                                       'name' => 'end_actions',
                                                       'dropdown' => array(
                                                            array('', 'Nothing'),
                                                            array('redirect', 'Redirect'),
                                                            array('loop', 'Loop'),
                                                            array('popup', 'Show popup'),
                                                            array('splashend', 'Show splash screen'),
                                                            array('email_list', 'Collect Emails')),
                                                       'live' => false ) ); ?>

                  <tr class="fv_player_actions_end-toggle">
                    <th scope="row" class="label"><label for="fv_wp_flowplayer_field_redirect" class="alignright"><?php _e('Redirect to', 'fv_flowplayer'); ?></label></th>
                    <td class="field"><input type="text" id="fv_wp_flowplayer_field_redirect" name="fv_wp_flowplayer_field_redirect" style="width: 93%" /></td>
                  </tr>

                  <tr class="fv_player_actions_end-toggle">
                    <th scope="row" class="label"><label for="fv_wp_flowplayer_field_popup_id" class="alignright"><?php _e('End popup', 'fv_flowplayer'); ?></label></th>
                    <td>
                      <?php fv_flowplayer_admin_select_popups(array('id' => 'fv_wp_flowplayer_field_popup_id', 'show_default' => true)) ?>
                      <div style="display: none">
                        <p><span class="dashicons dashicons-warning"></span> <?php _e('You are using the legacy popup functionality. Move the popup code', 'fv-wordpress-flowplayer'); ?> <a href="<?php echo site_url(); ?>/wp-admin/options-general.php?page=fvplayer#tab_popups" target="_target"><?php _e('here', 'fv-wordpress-flowplayer'); ?></a><?php _e(', then use the drop down menu above.', 'fv-wordpress-flowplayer'); ?></p>
                        <textarea type="text" id="fv_wp_flowplayer_field_popup" name="fv_wp_flowplayer_field_popup" style="width: 93%"></textarea>
                      </div>                      
                    </td>
                  </tr>

                  <?php

                  $rawLists = get_option('fv_player_email_lists');
                  $aLists = array();
                  foreach($rawLists as $key => $val){
                    if(!is_numeric($key))
                      continue;
                    $aLists[] = array($key,(empty($val->name) ? "List " . $key : "$val->name" ));
                  }
                  if(count($aLists)){
                    fv_player_shortcode_row( array(
                        'label' => 'E-mail list',
                        'name' => 'email_list',
                        'class' => 'fv_player_actions_end-toggle',
                        'dropdown' =>$aLists,
                        'live' => false ) );
                  }
                  ?>
                  <tr <?php if( !isset($fv_flowplayer_conf["interface"]["ads"]) || $fv_flowplayer_conf["interface"]["ads"] !== 'true' ) echo ' class="fv_player_interface_hide"'; ?>>
                    <th scope="row" class="label"><label for="fv_wp_flowplayer_field_ad" class="alignright"><?php _e('Ad code', 'fv_flowplayer'); ?></label></th>
                    <td>
                      <textarea type="text" id="fv_wp_flowplayer_field_ad" name="fv_wp_flowplayer_field_ad" style="width: 93%"></textarea>
                    </td>
                  </tr> 
                  <tr <?php if( !isset($fv_flowplayer_conf["interface"]["ads"]) || $fv_flowplayer_conf["interface"]["ads"] !== 'true' ) echo ' class="fv_player_interface_hide"'; ?>>
                    <th scope="row" class="label"><label for="fv_wp_flowplayer_field_liststyle" class="alignright"><?php _e('Ad Size', 'fv_flowplayer'); ?></label></th>
                    <td class="field" <?php if( !isset($fv_flowplayer_conf["interface"]["ads"]) || $fv_flowplayer_conf["interface"]["ads"] !== 'true' ) echo ' class="fv_player_interface_hide"'; ?>>
                      <input type="text" id="fv_wp_flowplayer_field_ad_width" name="fv_wp_flowplayer_field_ad_width" style="width: 19%; margin-right: 25px;"  value="" placeholder="<?php _e('Width', 'fv_flowplayer'); ?>"/>
                      <input type="text" id="fv_wp_flowplayer_field_ad_height" name="fv_wp_flowplayer_field_ad_height" style="width: 19%; margin-right: 25px;" value="" placeholder="<?php _e('Height', 'fv_flowplayer'); ?>"/>
                      <input type="checkbox" id="fv_wp_flowplayer_field_ad_skip" name="fv_wp_flowplayer_field_ad_skip" /> <?php _e('Skip global ad in this video', 'fv_flowplayer'); ?>  					
                    </td>
                  </tr>
                  
                  <?php do_action('fv_flowplayer_shortcode_editor_after'); ?>
                  
                  <?php do_action('fv_flowplayer_shortcode_editor_tab_actions'); ?>
                  
                  <tr class="submit-button-wrapper">
                    <td></td>
                    <td>
                      <input type="button" value="<?php _e('Insert', 'fv_flowplayer'); ?>" name="insert" class="button-primary extra-field fv_player_field_insert-button" onclick="fv_wp_flowplayer_submit();" />    
                      <a style="outline: 0" onclick="return fv_flowplayer_playlist_show()" class="playlist_edit button-primary <?php if( !isset($fv_flowplayer_conf["interface"]["playlist"]) || $fv_flowplayer_conf["interface"]["playlist"] !== 'true' ) echo ' fv_player_interface_hide'; ?>" href="#" data-create="<?php _e('Add another video into playlist', 'fv_flowplayer'); ?>" data-edit="<?php _e('Back to playlist', 'fv_flowplayer'); ?>"><?php _e('Add another video into playlist', 'fv_flowplayer'); ?></a>
                    </td>
                  </tr>
                  
                </table>
              </div>
              
              <?php do_action('fv_player_shortcode_editor_tab_content'); ?>
            </div>
            <!--<div id="fv-player-tabs-debug"></div>-->
          </td>
        </tr>
      </table>
    </div>   
  </div>
</div>
