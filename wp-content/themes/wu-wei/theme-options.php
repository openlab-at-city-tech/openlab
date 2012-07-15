<?php

add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
	register_setting( 'wuwei_options', 'wuwei_theme_options', 'theme_options_validate' );
}

/**
 * Load up the menu page
 */
function theme_options_add_page() {
	add_theme_page('Theme Options', 'Theme Options', 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
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
		<?php screen_icon(); echo "<h2>" . get_current_theme() . " Theme Options" . "</h2>"; ?>

		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class="updated fade"><p><strong><?php echo 'Options saved'; ?></strong></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'wuwei_options' ); ?>
			<?php $options = get_option( 'wuwei_theme_options' ); ?>

			<table class="form-table">

				<?php
				/**
				 * Color scheme option
				 */
				?>
				<tr valign="top"><th scope="row"><?php echo 'Dark color scheme'; ?></th>
					<td>
						<input id="wuwei_theme_options[colorscheme]" name="wuwei_theme_options[colorscheme]" type="checkbox" value="1" <?php checked( '1', $options['colorscheme'] ); ?> />
						<label class="description" for="wuwei_theme_options[colorscheme]"><?php echo 'I&rsquo;d like to use the dark color scheme!'; ?></label>
					</td>
				</tr>

			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php echo 'Save Options'; ?>" />
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

	// Our color scheme checkbox value is either 0 or 1
	if ( ! isset( $input['colorscheme'] ) )
		$input['colorscheme'] = null;
	$input['colorscheme'] = ( $input['colorscheme'] == 1 ? 1 : 0 );

	return $input;
}

// adapted from http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/