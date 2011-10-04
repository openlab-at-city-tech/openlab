<?php

add_action( 'admin_init', 'pilcrow_theme_options_init' );
add_action( 'admin_menu', 'pilcrow_theme_options_add_page' );

/**
 * Add theme options page styles
 */
wp_register_style( 'pilcrow', get_bloginfo( 'template_directory' ) . '/theme-options.css', '', '0.1' );
if ( isset( $_GET['page'] ) && $_GET['page'] == 'theme_options' ) {
	wp_enqueue_style( 'pilcrow' );
}

/**
 * Init plugin options to white list our options
 */
function pilcrow_theme_options_init(){
	register_setting( 'pilcrow_options', 'pilcrow_theme_options', 'pilcrow_theme_options_validate' );
}

/**
 * Load up the menu page
 */
function pilcrow_theme_options_add_page() {
	add_theme_page( __( 'Theme Options' ), __( 'Theme Options' ), 'edit_theme_options', 'theme_options', 'pilcrow_theme_options_do_page' );
}

/**
 * Return array for our color schemes
 */
function pilcrow_color_schemes() {
	$color_schemes = array(
		'light' => array(
			'value' =>	'light',
			'label' => __( 'Light' )
		),
		'dark' => array(
			'value' =>	'dark',
			'label' => __( 'Dark' )
		),
		'red' => array(
			'value' =>	'red',
			'label' => __( 'Red' )
		),
		'brown' => array(
			'value' =>	'brown',
			'label' => __( 'Brown' )
		),
	);

	return $color_schemes;
}

/**
 * Return array for our layouts
 */
function pilcrow_layouts() {
	$theme_layouts = array(
		'content-sidebar' => array(
			'value' => 'content-sidebar',
			'label' => __( 'Content-Sidebar' ),
		),
		'sidebar-content' => array(
			'value' => 'sidebar-content',
			'label' => __( 'Sidebar-Content' )
		),
		'content-sidebar-sidebar' => array(
			'value' => 'content-sidebar-sidebar',
			'label' => __( 'Content-Sidebar-Sidebar' )
		),
		'sidebar-sidebar-content' => array(
			'value' => 'sidebar-sidebar-content',
			'label' => __( 'Sidebar-Sidebar-Content' )
		),
		'sidebar-content-sidebar' => array(
			'value' => 'sidebar-content-sidebar',
			'label' => __( 'Sidebar-Content-Sidebar' )
		),
		'no-sidebar' => array(
			'value' => 'no-sidebar',
			'label' => __( 'Full-Width, No Sidebar' )
		),
	);

	return $theme_layouts;
}

/**
 * Set default options
 */
function pilcrow_default_options() {
	$options = get_option( 'pilcrow_theme_options' );

	if ( ! isset( $options['color_scheme'] ) ) {
		$options['color_scheme'] = 'light';
		update_option( 'pilcrow_theme_options', $options );
	}

	if ( ! isset( $options['theme_layout'] ) ) {
		$options['theme_layout'] = 'content-sidebar';
		update_option( 'pilcrow_theme_options', $options );
	}
}
add_action( 'init', 'pilcrow_default_options' );

/**
 * Create the options page
 */
function pilcrow_theme_options_do_page() {

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false;

	?>
	<div class="wrap">
		<?php screen_icon(); echo "<h2>" . get_current_theme() . __( ' Theme Options' ) . "</h2>"; ?>

		<?php if ( false !== $_REQUEST['updated'] ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Options saved', 'pilcrow' ); ?></strong></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'pilcrow_options' ); ?>
			<?php $options = get_option( 'pilcrow_theme_options' ); ?>

			<table class="form-table">

				<?php
				/**
				 * Pilcrow Color Scheme
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Color Scheme', 'pilcrow' ); ?></th>
					<td>
						<select name="pilcrow_theme_options[color_scheme]">
							<?php
								$selected = $options['color_scheme'];
								$p = '';
								$r = '';

								foreach ( pilcrow_color_schemes() as $option ) {
									$label = $option['label'];

									if ( $selected == $option['value'] ) // Make default first in list
										$p = "\n\t<option selected='selected' value='" . esc_attr( $option['value'] ) . "'>$label</option>";
									else
										$r .= "\n\t<option value='" . esc_attr( $option['value'] ) . "'>$label</option>";
								}
								echo $p . $r;
							?>
						</select>
						<label class="description" for="pilcrow_theme_options[color_scheme]"><?php _e( 'Select a default color scheme', 'pilcrow' ); ?></label>
					</td>
				</tr>

				<?php
				/**
				 * Pilcrow Layout
				 */
				?>
				<tr valign="top" id="pilcrow-layouts"><th scope="row"><?php _e( 'Default Layout', 'pilcrow' ); ?></th>
					<td>
						<fieldset><legend class="screen-reader-text"><span><?php _e( 'Default Layout', 'pilcrow' ); ?></span></legend>
						<?php
							if ( ! isset( $checked ) )
								$checked = '';
							foreach ( pilcrow_layouts() as $option ) {
								$radio_setting = $options['theme_layout'];

								if ( '' != $radio_setting ) {
									if ( $options['theme_layout'] == $option['value'] ) {
										$checked = "checked=\"checked\"";
									} else {
										$checked = '';
									}
								}
								?>
								<div class="layout">
								<label class="description">
									<input type="radio" name="pilcrow_theme_options[theme_layout]" value="<?php echo esc_attr( $option['value'] ); ?>" <?php echo $checked; ?> />
									<span>
										<img src="<?php bloginfo( 'template_directory' ); ?>/images/<?php echo $option['value']; ?>.png"/>
										<?php echo $option['label']; ?>
									</span>
								</label>
								</div>
								<?php
							}
						?>
						</fieldset>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Options', 'pilcrow' ); ?>" />
			</p>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function pilcrow_theme_options_validate( $input ) {

	// Our color scheme option must actually be in our array of color scheme options
	if ( ! array_key_exists( $input['color_scheme'], pilcrow_color_schemes() ) )
		$input['color_scheme'] = null;

	// Our radio option must actually be in our array of radio options
	if ( ! isset( $input['theme_layout'] ) )
		$input['theme_layout'] = null;
	if ( ! array_key_exists( $input['theme_layout'], pilcrow_layouts() ) )
		$input['theme_layout'] = null;

	return $input;
}

// adapted from http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/