<?php

	/*
	 * Outputs profile form (called from multiple places)
	 */

if ( ! defined( 'ABSPATH' ) ) {	exit; }


function gde_profile_form( $id = 1 ) {
    $p = gde_get_profiles( $id );

    // minimize FOUC
    if ( $p['viewer'] == "standard" ) {
        $hideenh = " hide";
    } else {
        $hideenh = '';
    }

    // setup title & nonce
    $title = __('Default Settings', 'google-document-embedder');
    $naction = "update-default-opts";
    $nname = "_general_default";
?>

<div id="profile-form">

	<form action="" method="post">
	<?php wp_nonce_field($naction, $nname); ?>
	<input type="hidden" name="profile_id" value="<?php echo esc_attr($id); ?>">

	<?php gde_help_link( GDE_STDOPT_URL, 'right' ); ?>
	<h3><?php echo $title; ?></h3>
		
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Default Language', 'google-document-embedder'); ?></th>
					<td>
						<select name="language" id="language">
<?php
	require_once( GDE_PLUGIN_DIR . 'libs/lib-langs.php' );
	$langs = gde_supported_langs();
	
	foreach ( $langs as $code => $desc ) {
		gde_profile_option( $p['language'], $code, $desc );
	}
?>
						</select><br/>
						<span class="gde-fnote"><?php _e('Language of toolbar button tips', 'google-document-embedder'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Default Size', 'google-document-embedder'); ?></th>
					<td>
						&nbsp;<?php _e('Width', 'google-document-embedder'); ?> 
<?php
	gde_profile_text( $p['default_width'], 'default_width', '', '5' );
?>
						&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Height', 'google-document-embedder'); ?> 
<?php
	gde_profile_text( $p['default_height'], 'default_height', '', '5' );
?>
						<br/>
						<span class="gde-fnote"><?php _e('Enter as pixels or percentage (example: 500px or 100%)', 'google-document-embedder'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('File Base URL', 'google-document-embedder'); ?></th>
					<td>
<?php
	gde_profile_text( $p['base_url'], 'base_url', '', '65' );
?>
						<br/>
						<span class="gde-fnote"><?php _e('Any file not starting with <code>http</code> will be prefixed by this value', 'google-document-embedder'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Download Link', 'google-document-embedder'); ?></th>
					<td>
						<select name="link_show" id="link_show">
<?php
	gde_profile_option( $p['link_show'], 'all', __('All Users', 'google-document-embedder'), __('Download link visible to everyone by default', 'google-document-embedder') );
	gde_profile_option( $p['link_show'], 'users', __('Logged-in Users', 'google-document-embedder'), __('Download link visible to logged-in users', 'google-document-embedder') );
	gde_profile_option( $p['link_show'], 'none', __('None', 'google-document-embedder'), __('Download link is not visible by default', 'google-document-embedder') );
?>
						</select><br/>
						<span class="gde-fnote" id="linkshow-h"></span>
					</td>
				</tr>
				<tr valign="top" id="linktext">
					<th scope="row"><?php _e('Link Text', 'google-document-embedder'); ?></th>
					<td>
						<input size="50" name="link_text" value="<?php echo esc_attr($p['link_text']); ?>" type="text"><br/>
						<span class="gde-fnote"><?php _e('You can further customize text using these dynamic replacements:', 'google-document-embedder'); ?></span><br>
						<code>%FILE</code> : <?php _e('filename', 'google-document-embedder'); ?> &nbsp;&nbsp;&nbsp;
						<code>%TYPE</code> : <?php _e('file type', 'google-document-embedder'); ?> &nbsp;&nbsp;&nbsp;
						<code>%SIZE</code> : <?php _e('file size', 'google-document-embedder'); ?>
					</td>
				</tr>
				<tr valign="top" id="linkpos">
					<th scope="row"><?php _e('Link Position', 'google-document-embedder'); ?></th>
					<td>
						<select name="link_pos">
<?php
	gde_profile_option( $p['link_pos'], 'above', __('Above Viewer', 'google-document-embedder') );
	gde_profile_option( $p['link_pos'], 'below', __('Below Viewer', 'google-document-embedder') );
?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="gde-submit">
			<input id="pro-submit" class="button-primary" type="submit" value="<?php _e('Save Changes', 'google-document-embedder'); ?>" name="submit">
		</p>
		
	</form>

</div>

<?php
}
?>
