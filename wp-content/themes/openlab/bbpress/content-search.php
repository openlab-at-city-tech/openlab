<?php

/**
 * Search Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

?>

	<?php bbp_breadcrumb(); ?>

	<?php bbp_set_query_name( bbp_get_search_rewrite_id() ); ?>

	<?php do_action( 'bbp_template_before_search' ); ?>

	<?php if ( bbp_has_search_results() ) : ?>

		<?php bbp_get_template_part( 'pagination', 'search' ); ?>

		<?php bbp_get_template_part( 'loop',       'search' ); ?>

		<?php bbp_get_template_part( 'pagination', 'search' ); ?>

        <div class="bbp-back-to-course-discussion">
			<?php /* Trick: use the buddypress string so it gets translated */ ?>
			<p><a class="btn btn-primary link-btn" href="<?php bp_group_permalink(); ?>forum/"><span class="fa fa-chevron-circle-left"></span> <?php _e( 'All Topics', 'openlab' ); ?></a></p>
		</div>
	<?php elseif ( bbp_get_search_terms() ) : ?>

		<?php bbp_get_template_part( 'feedback',   'no-search' ); ?>

	<?php else : ?>

		<?php bbp_get_template_part( 'form', 'search' ); ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_search_results' ); ?>
