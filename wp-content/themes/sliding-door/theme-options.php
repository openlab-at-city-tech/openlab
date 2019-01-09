<?php

add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
	register_setting( 'slidingdoor_options', 'slidingdoor_theme_options', 'theme_options_validate' );
}

/**
 * Load up the menu page
 */
function theme_options_add_page() {
	add_theme_page( __( 'Theme Options', 'sliding-door' ), __( 'Theme Options', 'sliding-door' ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
}



/**
 * Create the options page
 */
function theme_options_do_page() {
	global $select_options, $radio_options;

	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	?>
	<div class="wrap">
		<?php echo "<h2>" . wp_get_theme() . __( ' Theme Options', 'sliding-door' ) . "</h2>"; ?>

		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Options saved', 'sliding-door' ); ?></strong></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'slidingdoor_options' ); ?>
			<?php $options = get_option( 'slidingdoor_theme_options' ); ?>

			<table class="form-table">

				<?php
				/**
				 * A slidingdoor light theme color option
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Theme color style', 'sliding-door' ); ?></th>
					<td>
						<input id="slidingdoor_theme_options[option1]" name="slidingdoor_theme_options[option1]" type="checkbox" value="1" <?php checked( '1', $options['option1'] ); ?> />
						<label class="description" for="slidingdoor_theme_options[option1]"><?php _e( 'Light theme colors', 'sliding-door' ); ?> &nbsp&nbsp  &nbsp (This will make the theme black text on white instead of white on black.)</label> 
					</td>
				</tr>
				
				
					<?php
				/**
				 *  Leave sliders open option
				 */
				?>
				
				<tr valign="top"><th scope="row"><?php _e( 'Leave Sliders open', 'sliding-door' ); ?></th>
					<td>
						<input id="slidingdoor_theme_options[option2]" name="slidingdoor_theme_options[option2]" type="checkbox" value="1" <?php checked( '1', $options['option2'] ); ?> />
						<label class="description" for="slidingdoor_theme_options[option2]"><?php _e( 'Leave Sliders Open', 'sliding-door' ); ?> &nbsp (Leave sliders open according to which page you are on. You must give the pages an 'order' from 0 to 7 in the Wordpress 'Edit Page' screen for this to work.) </label>
					</td>
				</tr>
				
				
		
			</table>


			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'sliding-door' ); ?>" />
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

	// Our checkbox value is either 0 or 1
	if ( ! isset( $input['option1'] ) )
		$input['option1'] = null;
	$input['option1'] = ( $input['option1'] == 1 ? 1 : 0 );

	if ( ! isset( $input['option2'] ) )
		$input['option2'] = null;
	$input['option2'] = ( $input['option2'] == 1 ? 1 : 0 );


	return $input;
}

