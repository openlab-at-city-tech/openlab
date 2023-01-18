<?php

/**
 * Pagination for pages of topics (when viewing a forum)
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bbp_template_before_pagination_loop' ); ?>

<div class="bbp-pagination bbp-pagination-replies clearfix">
	<div class="row">
		<div class="col-xs-24">
            <div class="pagination-links">
                <?php bbp_topic_pagination_links(); ?>
            </div>
            <div class="bbp-pagination-count">
                <?php bbp_topic_pagination_count(); ?>
            </div>
		</div>
	</div>
</div>

<?php do_action( 'bbp_template_after_pagination_loop' ); ?>
