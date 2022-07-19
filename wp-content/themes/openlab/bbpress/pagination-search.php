<?php

/**
 * Pagination for pages of topics (when viewing a forum)
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bbp_template_before_pagination_loop' ); ?>

<div class="bbp-pagination clearfix">
	<div class="row">
		<div class="col-sm-16 col-xs-24">
			<div class="bbp-pagination-row-flex">
				<div class="pagination-links">
					<?php bbp_forum_pagination_links(); ?>
				</div>
				<div class="bbp-pagination-count">
					<?php bbp_forum_pagination_count(); ?>
				</div>
			</div>
		</div>
		<div class="col-sm-8 col-xs-24">
			<?php bbp_get_template_part( 'form', 'search' ); ?>
		</div>
	</div>
</div>

<?php do_action( 'bbp_template_after_pagination_loop' ); ?>
