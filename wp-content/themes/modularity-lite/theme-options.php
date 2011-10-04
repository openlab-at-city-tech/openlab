<?php
/**
 * @package WordPress
 * @subpackage Modularity
 */

add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
	register_setting( 'modularity_options', 'modularity_theme_options', 'theme_options_validate' );
}

/**
 * Load up the menu page
 */
function theme_options_add_page() {
	add_theme_page( __( 'Theme Options' ), __( 'Theme Options' ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
}

/**
 * Create the options page
 */
function theme_options_do_page() {
	global $select_options, $radio_options;

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false;

	?>
	<div class="wrap">
		<?php screen_icon(); echo "<h2>" . get_current_theme() . __( ' Theme Options' ) . "</h2>"; ?>
		
		<p>Please read the <a href="<?php bloginfo( 'template_directory' ); ?>/instructions.html" target="_blank">theme instructions</a> if you have questions using this theme.  If you need additional help, <a href="http://graphpaperpress.com/support/" target="_blank" title="visit the Graph Paper Press support forums">visit Graph Paper Press support</a>.</p>

		<?php if ( false !== $_REQUEST['updated'] ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'modularity_options' ); ?>
			<?php $options = get_option( 'modularity_theme_options' ); ?>
			
			<h3><?php _e( 'Optional Sidebar and Slideshow', 'modularity' ); ?></h3>
			<p><?php _e( 'A one-column layout or a two-column layout with sidebar? How about a home page slideshow featuring 950px by 425px image attachments from your most recent posts? The choice is yours.', 'modularity' ); ?></p>			

			<table class="form-table">
				
				<?php
				/**
				 * Sidebar option
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Sidebar', 'modularity' ); ?></th>
					<td>
						<input id="modularity_theme_options[sidebar]" name="modularity_theme_options[sidebar]" type="checkbox" value="1" <?php checked( '1', $options['sidebar'] ); ?> />
						<label class="description" for="modularity_theme_options[sidebar]"><?php _e( 'Yes! I&rsquo;d like to enable the optional sidebar', 'modularity' ); ?></label>
					</td>
				</tr>		

				<?php
				/**
				 * Slideshow option
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Slideshow', 'modularity' ); ?></th>
					<td>
						<input id="modularity_theme_options[slideshow]" name="modularity_theme_options[slideshow]" type="checkbox" value="1" <?php checked( '1', $options['slideshow'] ); ?> />
						<label class="description" for="modularity_theme_options[slideshow]"><?php _e( 'Yes! I&rsquo;d like to enable the optional home page slideshow', 'modularity' ); ?></label>
					</td>
				</tr>		
				
				
			</table>
				
			<h3><?php _e( 'Welcome Message', 'modularity' ); ?></h3>
			<p><?php _e( 'Fill out the following Title and Content fields to enable a Welcome Message on the home page of your site.', 'modularity' ); ?></p>
			
			<table class="form-table">
				
				<?php
				/**
				 * Welcome box title
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Welcome Message Title' ); ?></th>
					<td>
						<input id="modularity_theme_options[welcome_title]" class="regular-text" type="text" name="modularity_theme_options[welcome_title]" value="<?php esc_attr_e( stripslashes( $options['welcome_title'] ) ); ?>" />
					</td>
				</tr>

				<?php
				/**
				 * Welcome box content
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Welcome Message Content' ); ?></th>
					<td>
						<textarea id="modularity_theme_options[welcome_content]" class="large-text" cols="50" rows="10" name="modularity_theme_options[welcome_content]"><?php echo stripslashes( $options['welcome_content'] ); ?></textarea>
					</td>
				</tr>														

			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options' ); ?>" />
			</p>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function theme_options_validate( $input ) {
	global $select_options, $radio_options;

	// Our sidebar checkbox value is either 0 or 1
	if ( ! isset( $input['sidebar'] ) )
		$input['sidebar'] = 0;
	$input['sidebar'] = ( $input['sidebar'] == 1 ? 1 : 0 );

	// Our slideshow checkbox value is either 0 or 1
	if ( ! isset( $input['slideshow'] ) )
		$input['slideshow'] = 0;
	$input['slideshow'] = ( $input['slideshow'] == 1 ? 1 : 0 );

	// Our welcome_box checkbox value is either 0 or 1
	if ( ! isset( $input['welcome_box'] ) )
		$input['welcome_box'] = null;
	$input['welcome_box'] = ( $input['welcome_box'] == 1 ? 1 : 0 );
	
	// Say our text option must be safe text with no HTML tags
	$input['welcome_title'] = wp_filter_nohtml_kses( $input['welcome_title'] );
	
	// Say our textarea option must be safe text with the allowed tags for posts
	$input['welcome_content'] = wp_filter_post_kses( $input['welcome_content'] );	
		
	return $input;
}

// adapted from http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/