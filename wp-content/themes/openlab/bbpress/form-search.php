<?php

/**
 * Search
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( bbp_allow_search() ) : ?>

	<div class="bbp-search-form">
		<form role="search" method="get" id="bbp-search-form" action="<?php echo esc_url( bp_get_group_permalink( groups_get_current_group() ) ); ?>forum/">
			<div>
				<label>
					<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'bbpress' ); ?></span>
					<input type="text" value="<?php bbp_search_terms(); ?>" name="bbp_search" id="bbp_search" />
				</label>
				<input class="button" type="submit" id="bbp_search_submit" value="<?php esc_attr_e( 'Search', 'bbpress' ); ?>" />
			</div>
		</form>
	</div>

<?php endif;
