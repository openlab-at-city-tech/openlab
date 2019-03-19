<?php 
/**
 * Advanced Controls Screen
 *
 * This file contains the closing the tags for the 
 * html settings page.
 * 
 * @package   Easy_Google_Fonts
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.4
 * 
 */
?>
<h3 class="title"><?php _e( 'Google Fonts API Key', $this->plugin_slug ); ?></h3>
<p><?php _e( 'Please enter your google fonts api key in the box below and click the Save Google API Key button.', $this->plugin_slug ); ?></p>
<div class="manage-controls manage-google-key <?php echo $validity; ?>">
	<form enctype="multipart/form-data" method="get" action="" id="" autocomplete="off">
		<input id="egf-google-api-key" type="text" class="" value="<?php echo $api_key; ?>">
		<p class="key-feedback howto">
			<span class="valid-key"><?php _e( 'Your Google API Key is valid and automatic font updates are enabled.', $this->plugin_slug ); ?></span>
			<span class="invalid-key"><?php _e( 'Please enter a valid Google API Key', $this->plugin_slug ); ?></span>
		</p>
		<?php 
			/**
			 * Create Font Control Nonce Fields for Security
			 * 
			 * This ensures that the request to modify controls 
			 * was an intentional request from the user. Used in
			 * the Ajax request for validation.
			 *
			 * @link http://codex.wordpress.org/Function_Reference/wp_nonce_field 	wp_nonce_field()
			 * 
			 */
			wp_nonce_field( 'tt_font_edit_control_instance', 'tt_font_edit_control_instance_nonce' );
			wp_nonce_field( 'tt_font_delete_control_instance', 'tt_font_delete_control_instance_nonce' );
			wp_nonce_field( 'tt_font_create_control_instance', 'tt_font_create_control_instance_nonce' );
		?>			
	</form>
</div><!-- /.manage-google-key -->
<?php 
	submit_button( 
		__( 'Save Google API Key', $this->plugin_slug ), 
		'primary', 
		'submit', 
		false, 
		array( 
			'id' => 'egf_save_api_key',
			'data-redirect-url' => $this->advanced_url,
		) 
	); 
?>
<div class="spinner spinner-left"></div>
<div class="clearfix"></div>

<div class="google-feedback">
	<div class="valid-key">
		<h3><?php _e( 'What happens after I enter a valid Google API key?', $this->plugin_slug ); ?></h3>
		<p><?php _e( 'Your theme will update itself with the latest google fonts automatically.', $this->plugin_slug ); ?></p>
	</div>
</div>
