<?php
/**
 * Multipage Admin Advanced.
 *
 * @package Multipage
 * @since 1.4
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main settings section description for the settings page.
 *
 * @since 1.4
 */
function mpp_admin_settings_callback_main_section() { }

/**
 * Pagination settings section description for the settings page.
 *
 * @since 1.4
 */
function mpp_admin_settings_callback_pagination_section() { }

/**
 * Table of contents settings section description for the settings page.
 *
 * @since 1.4
 */
function mpp_admin_settings_callback_toc_section() { }

/**
 * Show the excerpt on all the post pages.
 *
 * @since 1.4
 *
 */
function mpp_admin_settings_callback_excerpt_on_all_pages() {
?>

	<input id="excerpt-all-pages" name="mpp-excerpt-all-pages" type="checkbox" value="1" <?php checked( mpp_excerpt_all_pages( true ) ); ?> />
	<label for="excerpt-all-pages"><?php _e( 'Display the excerpt on all the post pages', 'sgr-nextpage-titles' ); ?></label>

<?php
}

/**
 * Hide the intro title.
 *
 * @since 1.4
 *
 */
function mpp_admin_settings_callback_hide_intro_title() {
?>

	<input id="hide-intro-title" name="mpp-hide-intro-title" type="checkbox" value="1" <?php checked( mpp_hide_intro_title( false ) ); ?> />
	<label for="hide-intro-title"><?php _e( 'Hide the default intro title', 'sgr-nextpage-titles' ); ?></label>

<?php
}

/**
 * Show the post comments on specific pages.
 *
 * @since 1.4
 *
 */
function mpp_admin_settings_callback_comments_on_page() {
	// Set the choices.
	$comments_choices = array(
		__( 'All pages — Display comments on all post pages', 'sgr-nextpage-titles' ) => 'all',
		__( 'First page — Display comments only on the first page', 'sgr-nextpage-titles' ) => 'first-page',
		__( 'Last page — Display comments only on the last page', 'sgr-nextpage-titles' ) => 'last-page' );
?>
	<fieldset id="comments-on-page">
		<legend class="screen-reader-text">
			<span><?php _e( 'Display comments on', 'sgr-nextpage-titles' ); ?></span>
		</legend>
		<?php foreach ( $comments_choices as $comments => $value) : ?>
		<label>
			<input type="radio" name="mpp-comments-on-page" value="<?php echo esc_html( $value ); ?>" <?php checked( $value, mpp_get_comments_on_page() ); ?> />
				<?php echo esc_html( $comments ); ?>
		</label>
		<br />
		<?php endforeach; ?>
	</fieldset>
	
<?php
}

/**
 * Display the navigation type.
 *
 * @since 1.4
 *
 */
function mpp_admin_settings_callback_continue_or_prev_next() {
	// Set the position choice values.
	$pagination_choices = array( 
		__( 'Continue or back to intro',	'sgr-nextpage-titles' )	=> 'continue',
		__( 'Next and previous',			'sgr-nextpage-titles' )	=> 'next-previous',
		__( 'Hidden',						'sgr-nextpage-titles' )	=> 'hidden'
	);
?>

	<select id="continue-or-prev-next" name="mpp-continue-or-prev-next" class="continue-or-prev-next">
		<?php foreach ( $pagination_choices as $pagination => $value) : ?>
			<option value="<?php echo $value; ?>" <?php selected( $value, mpp_get_continue_or_prev_next() ); ?>><?php echo esc_html( $pagination ); ?></option>
		<?php endforeach; ?>
	</select>

<?php
}

/**
 * Disable the TinyMCE buttons in order to preserve the compatibilty with older WP versions.
 *
 * @since 1.4
 *
 */
function mpp_admin_settings_callback_disable_standard_pagination() {
?>

	<input id="disable-standard-pagination" name="mpp-disable-standard-pagination" type="checkbox" value="1" <?php checked( mpp_disable_standard_pagination( true ) ); ?> />
	<label for="disable-standard-pagination"><?php _e( 'Disable the WordPress standard pagination', 'sgr-nextpage-titles' ); ?></label>
	<p id="mpp-disable-standard-pagination-description" class="description"><?php _e( 'WordPress, by default, displays a pagination with only numbers on standard multipage posts. Please uncheck this if you still want the standard pagination to display on multipage posts.', 'sgr-nextpage-titles' ); ?></p>

<?php
}

/**
 * Decide if the table of content must be visible only on the first page of a multipage post.
 *
 * @since 1.4
 *
 */
function mpp_admin_settings_callback_toc_only_on_the_first_page() {
?>

	<input id="toc-only-on-the-first-page" name="mpp-toc-only-on-the-first-page" type="checkbox" value="1" <?php checked( mpp_toc_only_on_the_first_page( false ) ); ?> />
	<label for="toc-only-on-the-first-page"><?php _e( 'Display the table of contents only on the first page of the post', 'sgr-nextpage-titles' ); ?></label>

<?php
}

/**
 * Set the Table of Contents position.
 *
 * @since 1.4
 *
 */
function mpp_admin_settings_callback_toc_position() {
	// Set the position choice values.
	$position_choices = array( 
		__( 'above the post content, align left', 'sgr-nextpage-titles' )	=> 'top-left',
		__( 'above the post content, align right', 'sgr-nextpage-titles' )	=> 'top-right',
		__( 'above the post content', 'sgr-nextpage-titles' )				=> 'top',
		__( 'below the post content', 'sgr-nextpage-titles' )				=> 'bottom',
		__( 'hidden', 'sgr-nextpage-titles' )								=> 'hidden'
	);
?>

	<select id="toc-position" name="mpp-toc-position" class="toc-position">
		<?php foreach ( $position_choices as $position => $value) : ?>
			<option value="<?php echo $value; ?>" <?php selected( $value, mpp_get_toc_position() ); ?>><?php echo esc_html( $position ); ?></option>
		<?php endforeach; ?>
	</select>
	
<?php
}

/**
 * Set the page row labels.
 *
 * @since 1.4
 *
 */
function mpp_admin_settings_callback_toc_row_labels() {
	// Set the row labels.
	$label_choices = array(
		__( 'number — Display numbers before the subpage title', 'sgr-nextpage-titles' ) => 'number',
		__( 'page # — Display "Page #" before the subpage title', 'sgr-nextpage-titles' ) => 'page',
		__( 'hidden — Hide subpage labels, display only the title', 'sgr-nextpage-titles' ) => 'hidden'
	);
?>
	<fieldset id="row-labels">
		<legend class="screen-reader-text">
			<span><?php _e( 'Row labels', 'sgr-nextpage-titles' ); ?></span>
		</legend>
		<?php foreach ( $label_choices as $label => $value) : ?>
		<label>
			<input type="radio" name="mpp-toc-row-labels" value="<?php echo esc_html( $value ); ?>" <?php checked( $value, mpp_get_toc_row_labels() ); ?> />
				<?php echo esc_html( $label ); ?>
		</label>
		<br />
		<?php endforeach; ?>
	</fieldset>
	
<?php
}

/**
 * Decide if hide the table of contents header.
 *
 * @since 1.4
 *
 */
function mpp_admin_settings_callback_hide_toc_header() {
?>

	<input id="hide-toc-header" name="mpp-hide-toc-header" type="checkbox" value="1" <?php checked( mpp_hide_toc_header( false ) ); ?> />
	<label for="hide-toc-header"><?php _e( 'Hide the table of contents header', 'sgr-nextpage-titles' ); ?></label>
	
<?php
}

/**
 * Decide if the table of content must have a link for comments.
 *
 * @since 1.4
 *
 */
function mpp_admin_settings_callback_comments_toc_link() {
?>

	<input id="comments-toc-link" name="mpp-comments-toc-link" type="checkbox" value="1" <?php checked( mpp_comments_toc_link( false ) ); ?> />
	<label for="comments-toc-link"><?php _e( 'Add a link for the comments inside the table of contents', 'sgr-nextpage-titles' ); ?></label>
	<p id="mpp-comments-toc-link-description" class="description"><?php _e( 'If comments are enabled, this will display, inside the table of contents, a link for the comments list.', 'sgr-nextpage-titles' ); ?></p>

<?php
}

/** Settings Page *************************************************************/

/**
 * The main settings page
 *
 * @since 1.4
 *
 */
function mpp_admin_settings() {
	// We're saving our own options, until the WP Settings API is updated to work with Multisite.
	$form_action = add_query_arg( 'page', 'mpp-settings',  mpp_get_admin_url( 'options-general.php' ) );

	?>

	<div class="wrap">

		<h1><?php _e( 'Multipage Settings', 'sgr-nextpage-titles' ); ?> </h1>
		
		<h2 class="nav-tab-wrapper"><?php mpp_admin_tabs( __( 'Options', 'sgr-nextpage-titles' ) ); ?></h2>

		<form action="<?php echo esc_url( $form_action ) ?>" method="post">
		
			<?php settings_fields( 'multipage' ); ?>

			<?php do_settings_sections( 'multipage' ); ?>
		
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'sgr-nextpage-titles' ); ?>" />
			</p>
		</form>
		
	</div><!-- .wrap -->
	
<?php
}

/**
 * Save our settings.
 *
 * @since 1.4
 */
function mpp_admin_settings_save() {
	global $wp_settings_fields;

	if ( isset( $_GET['page'] ) && 'mpp-settings' == $_GET['page'] && !empty( $_POST['submit'] ) ) {
		check_admin_referer( 'multipage-options' );
		
		// Because many settings are saved with checkboxes, and thus will have no values
		// in the $_POST array when unchecked, we loop through the registered settings.
		if ( isset( $wp_settings_fields['multipage'] ) ) {
			foreach( (array) $wp_settings_fields['multipage'] as $section => $settings ) {
				foreach( $settings as $setting_name => $setting ) {
					$value = isset( $_POST[$setting_name] ) ? $_POST[$setting_name] : '';
					
					update_option( $setting_name, $value );
				}
			}
		}
		
		wp_safe_redirect( add_query_arg( array( 'page' => 'mpp-settings', 'updated' => 'true' ), mpp_get_admin_url( 'options-general.php' ) ) );
	}
}
add_action( 'admin_init', 'mpp_admin_settings_save', 100 );