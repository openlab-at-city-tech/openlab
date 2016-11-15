<?php

add_action( 'admin_init', 'coraline_theme_options_init' );
add_action( 'admin_menu', 'coraline_theme_options_add_page' );

/**
 * Add theme options page styles
 */
function coraline_admin_enqueue_scripts( $hook_suffix ) {
	wp_enqueue_style( 'coraline', get_template_directory_uri() . '/inc/theme-options.css', '', '20120106' );
}
add_action( 'admin_print_styles-appearance_page_theme_options', 'coraline_admin_enqueue_scripts' );

/**
 * Init plugin options to white list our options
 */
function coraline_theme_options_init(){
	register_setting( 'coraline_options', 'coraline_theme_options', 'coraline_theme_options_validate' );
}

/**
 * Load up the menu page
 */
function coraline_theme_options_add_page() {
	add_theme_page( __( 'Theme Options', 'coraline' ), __( 'Theme Options', 'coraline' ), 'edit_theme_options', 'theme_options', 'coraline_theme_options_do_page' );
}

/**
 * Return array for our color schemes
 */
function coraline_color_schemes() {
	$color_schemes = array(
		'light' => array(
			'value' =>	'light',
			'label' => __( 'White', 'coraline' )
		),
		'dark' => array(
			'value' =>	'dark',
			'label' => __( 'Black', 'coraline' )
		),
		'pink' => array(
			'value' =>	'pink',
			'label' => __( 'Pink', 'coraline' )
		),
		'blue' => array(
			'value' =>	'blue',
			'label' => __( 'Blue', 'coraline' )
		),
		'purple' => array(
			'value' =>	'purple',
			'label' => __( 'Purple', 'coraline' )
		),
		'red' => array(
			'value' =>	'red',
			'label' => __( 'Red', 'coraline' )
		),
		'brown' => array(
			'value' =>	'brown',
			'label' => __( 'Brown', 'coraline' )
		),
	);

	return $color_schemes;
}

/**
 * Return array for our layouts
 */
function coraline_layouts() {
	$theme_layouts = array(
		'content-sidebar' => array(
			'value' => 'content-sidebar',
			'label' => __( 'Content-Sidebar', 'coraline' ),
		),
		'sidebar-content' => array(
			'value' => 'sidebar-content',
			'label' => __( 'Sidebar-Content', 'coraline' )
		),
		'content-sidebar-sidebar' => array(
			'value' => 'content-sidebar-sidebar',
			'label' => __( 'Content-Sidebar-Sidebar', 'coraline' )
		),
		'sidebar-sidebar-content' => array(
			'value' => 'sidebar-sidebar-content',
			'label' => __( 'Sidebar-Sidebar-Content', 'coraline' )
		),
		'sidebar-content-sidebar' => array(
			'value' => 'sidebar-content-sidebar',
			'label' => __( 'Sidebar-Content-Sidebar', 'coraline' )
		),
		'no-sidebars' => array(
			'value' => 'no-sidebars',
			'label' => __( 'Full Width (No Sidebars)', 'coraline' )
		),
	);

	return $theme_layouts;
}

/**
 * Create the options page
 */
function coraline_theme_options_do_page() {

	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	?>
	<div class="wrap">
		<?php $theme_name = wp_get_theme(); ?>
		<?php echo "<h2>" . sprintf( __( '%1$s Theme Options', 'coraline' ), $theme_name ) . "</h2>"; ?>

		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Options saved', 'coraline' ); ?></strong></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'coraline_options' ); ?>
			<?php $options = coraline_get_theme_options(); ?>

			<table class="form-table">

				<?php
				/**
				 * Coraline Color Scheme
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Color Scheme', 'coraline' ); ?></th>
					<td>
						<select name="coraline_theme_options[color_scheme]">
							<?php
								$selected_color = $options['color_scheme'];
								$p = '';
								$r = '';

								foreach ( coraline_color_schemes() as $option ) {
									$label = $option['label'];

									if ( $selected_color == $option['value'] ) // Make default first in list
										$p = "\n\t<option selected='selected' value='" . esc_attr( $option['value'] ) . "'>$label</option>";
									else
										$r .= "\n\t<option value='" . esc_attr( $option['value'] ) . "'>$label</option>";
								}
								echo $p . $r;
							?>
						</select>
						<label class="description" for="coraline_theme_options[color_scheme]"><?php _e( 'Select a default color scheme', 'coraline' ); ?></label>
					</td>
				</tr>

				<?php
				/**
				 * Coraline Layout
				 */
				?>
				<tr valign="top" id="coraline-layouts"><th scope="row"><?php _e( 'Default Layout', 'coraline' ); ?></th>
					<td>
						<fieldset><legend class="screen-reader-text"><span><?php _e( 'Default Layout', 'coraline' ); ?></span></legend>
						<?php
							if ( ! isset( $checked ) )
								$checked = '';
							foreach ( coraline_layouts() as $option ) {
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
									<input type="radio" name="coraline_theme_options[theme_layout]" value="<?php echo esc_attr( $option['value'] ); ?>" <?php echo $checked; ?> />
									<span>
										<img src="<?php echo get_template_directory_uri(); ?>/images/<?php echo $option['value']; ?>.png"/>
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

				<?php
				/**
				 * Coraline Aside Category
				 */

				$selected_aside_category = ( isset( $options['aside_category'] ) ) ? $options['aside_category'] : null;
				if ( ! empty( $selected_aside_category ) ) :
				?>

				<tr valign="top"><th scope="row"><?php _e( 'Aside Category', 'coraline' ); ?></th>
					<td>
						<select name="coraline_theme_options[aside_category]">
							<option value="0"><?php _e( 'Select a category &hellip;', 'coraline' ); ?></option>
							<?php
								$p = '';
								$r = '';

								foreach ( get_categories( array( 'hide_empty' => 0 ) ) as $category ) {

									if ( $selected_aside_category == $category->cat_name ) // Make default first in list
										$p = "\n\t<option selected='selected' value='" . esc_attr( $category->cat_name ) . "'>$category->category_nicename</option>";
									else
										$r .= "\n\t<option value='" . esc_attr( $category->cat_name ) . "'>$category->category_nicename</option>";
								}
								echo $p . $r;
							?>
						</select>
						<label class="description" for="coraline_theme_options[aside_category]"><?php _e( 'Select a category to use for shorter aside posts', 'coraline' ); ?></label>
						<div class="update-msg"><p><?php _e( 'Note: Coraline now supports Post Formats! Read more at <a href="http://support.wordpress.com/posts/post-formats/">Support &raquo; Post Formats</a>.', 'coraline' ); ?></p></div>
					</td>
				</tr>
				<?php endif; ?>

				<?php
				/**
				 * Coraline Gallery Category
				 */
				$selected_gallery_category = ( isset( $options['gallery_category'] ) ) ? $options['gallery_category'] : null;
				if ( ! empty( $selected_gallery_category ) ) :
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Gallery Category', 'coraline' ); ?></th>
					<td>
						<select name="coraline_theme_options[gallery_category]">
							<option value="0"><?php _e( 'Select a category &hellip;', 'coraline' ); ?></option>
							<?php
								$p = '';
								$r = '';

								foreach ( get_categories( array( 'hide_empty' => 0 ) ) as $category ) {

									if ( $selected_gallery_category == $category->cat_name ) // Make default first in list
										$p = "\n\t<option selected='selected' value='" . esc_attr( $category->cat_name ) . "'>$category->category_nicename</option>";
									else
										$r .= "\n\t<option value='" . esc_attr( $category->cat_name ) . "'>$category->category_nicename</option>";
								}
								echo $p . $r;
							?>
						</select>
						<label class="description" for="coraline_theme_options[gallery_category]"><?php _e( 'Select a category to use for posts with image galleries', 'coraline' ); ?></label>
						<div class="update-msg"><p><?php _e( 'Note: Coraline now supports Post Formats! Read more at <a href="http://support.wordpress.com/posts/post-formats/">Support &raquo; Post Formats</a>.', 'coraline' ); ?></p></div>
					</td>
				</tr>
				<?php endif; ?>

			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Options', 'coraline' ); ?>" />
			</p>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function coraline_theme_options_validate( $input ) {

	// Our color scheme option must actually be in our array of color scheme options
	if ( ! array_key_exists( $input['color_scheme'], coraline_color_schemes() ) )
		$input['color_scheme'] = null;

	// Our radio option must actually be in our array of radio options
	if ( ! isset( $input['theme_layout'] ) )
		$input['theme_layout'] = null;
	if ( ! array_key_exists( $input['theme_layout'], coraline_layouts() ) )
		$input['theme_layout'] = null;

	// Our aside category option must actually be in our array of categories
	if ( isset( $input['aside_category'] ) && array_search( $input['aside_category'], get_categories() ) != 0 )
		$input['aside_category'] = null;

	// Our gallery category option must actually be in our array of categories
	if ( isset( $input['gallery_category'] ) && array_search( $input['gallery_category'], get_categories() ) != 0 )
		$input['gallery_category'] = null;

	return $input;
}

// adapted from http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/