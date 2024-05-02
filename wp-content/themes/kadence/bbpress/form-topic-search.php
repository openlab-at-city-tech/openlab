<?php
/**
 * Search 
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( bbp_allow_search() ) : ?>

	<div class="bbp-search-form">
		<form role="search" class="search-form" method="get" id="bbp-topic-search-form">
			<label for="ts">
				<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'kadence' ); ?></span>
				<input type="search" value="<?php bbp_search_terms(); ?>" placeholder="<?php esc_attr_e( 'Search ...', 'kadence' ); ?>" name="ts" id="ts" class="search-field" />
			</label>
			<input class="search-submit" type="submit" value="<?php esc_attr_e( 'Search', 'kadence' ); ?>" />
			<?php do_action( 'bbpress_end_form_search' ); ?>
		</form>
	</div>

<?php endif;
