<?php

add_action( 'admin_init', 'pilcrow_theme_options_init' );
add_action( 'admin_menu', 'pilcrow_theme_options_add_page' );

/**
 * Add theme options page styles
 */
function pilcrow_admin_enqueue_scripts( $hook_suffix ) {
	wp_enqueue_style( 'pilcrow', get_template_directory_uri() . '/inc/theme-options.css', '', '20110801' );
}
add_action( 'admin_print_styles-appearance_page_theme_options', 'pilcrow_admin_enqueue_scripts' );

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
	add_theme_page( __( 'Theme Options', 'pilcrow' ), __( 'Theme Options', 'pilcrow' ), 'edit_theme_options', 'theme_options', 'pilcrow_theme_options_do_page' );
}

/**
 * Return array for our color schemes
 */
function pilcrow_color_schemes() {
	$color_schemes = array(
		'light' => array(
			'value' =>	'light',
			'label' => __( 'Light', 'pilcrow' )
		),
		'dark' => array(
			'value' =>	'dark',
			'label' => __( 'Dark', 'pilcrow' )
		),
		'red' => array(
			'value' =>	'red',
			'label' => __( 'Red', 'pilcrow' )
		),
		'brown' => array(
			'value' =>	'brown',
			'label' => __( 'Brown', 'pilcrow' )
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
			'label' => __( 'Content-Sidebar', 'pilcrow' ),
		),
		'sidebar-content' => array(
			'value' => 'sidebar-content',
			'label' => __( 'Sidebar-Content', 'pilcrow' )
		),
		'content-sidebar-sidebar' => array(
			'value' => 'content-sidebar-sidebar',
			'label' => __( 'Content-Sidebar-Sidebar', 'pilcrow' )
		),
		'sidebar-sidebar-content' => array(
			'value' => 'sidebar-sidebar-content',
			'label' => __( 'Sidebar-Sidebar-Content', 'pilcrow' )
		),
		'sidebar-content-sidebar' => array(
			'value' => 'sidebar-content-sidebar',
			'label' => __( 'Sidebar-Content-Sidebar', 'pilcrow' )
		),
		'no-sidebar' => array(
			'value' => 'no-sidebar',
			'label' => __( 'Full-Width, No Sidebar', 'pilcrow' )
		),
	);

	return $theme_layouts;
}

/**
 * Create the options page
 */
function pilcrow_theme_options_do_page() {
	$options = pilcrow_get_theme_options();
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php printf( __( '%s Theme Options', 'pilcrow' ), wp_get_theme() ); ?></h2>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'pilcrow_options' ); ?>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Color Scheme', 'pilcrow' ); ?></th>
					<td>
						<select name="pilcrow_theme_options[color_scheme]">
						<?php foreach ( pilcrow_color_schemes() as $scheme ) : ?>
							<option value="<?php echo esc_attr( $scheme['value'] ); ?>" <?php selected( $options['color_scheme'], $scheme['value'] ); ?>><?php echo $scheme['label']; ?></option>
						<?php endforeach; ?>
						</select>
						<label class="description" for="pilcrow_theme_options[color_scheme]"><?php _e( 'Select a default color scheme', 'pilcrow' ); ?></label>
					</td>
				</tr>

				<tr valign="top" id="pilcrow-layouts">
					<th scope="row"><?php _e( 'Default Layout', 'pilcrow' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Default Layout', 'pilcrow' ); ?></span></legend>
							<?php foreach ( pilcrow_layouts() as $layout ) : ?>
							<div class="layout">
								<label class="description">
									<input type="radio" name="pilcrow_theme_options[theme_layout]" value="<?php echo esc_attr( $layout['value'] ); ?>" <?php checked( $options['theme_layout'], $layout['value'] ); ?> />
									<span>
										<img src="<?php echo get_template_directory_uri(); ?>/images/<?php echo $layout['value']; ?>.png"/>
										<?php echo $layout['label']; ?>
									</span>
								</label>
							</div>
							<?php endforeach; ?>
						</fieldset>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
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
