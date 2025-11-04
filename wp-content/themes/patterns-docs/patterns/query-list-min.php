<?php
/**
 * Title: Query List Minimal
 * Slug: patterns-docs/query-list-min
 * Categories: query
 * Block Types: core/query
 * Description: Displays a query block in a list layout with minimal content.
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/patterns
 * @since      1.0.0
 */

?>
<!-- wp:query {"query":{"inherit":true,"postType":"post"},"align":"wide","layout":{"type":"constrained"}} -->
	<div class="wp-block-query alignwide">
		<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}},"layout":{"type":"default"}} -->
		<div class="wp-block-group alignfull" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">
			<!-- wp:post-template {"align":"wide","style":{"typography":{"textTransform":"none"}}} -->
			<!-- wp:group {"style":{"border":{"bottom":{"color":"#f5f5f5","width":"1px"},"top":{},"right":{},"left":{}}},"layout":{"type":"default"}} -->
			<div class="wp-block-group" style="border-bottom-color:#f5f5f5;border-bottom-width:1px">
				<!-- wp:group {"style":{"spacing":{"padding":{"top":"16px","right":"16px","bottom":"16px","left":"16px"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
				<div class="wp-block-group"
					style="padding-top:16px;padding-right:16px;padding-bottom:16px;padding-left:16px">
					<!-- wp:group {"style":{"spacing":{"blockGap":"4px"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
					<div class="wp-block-group">
						<!-- wp:post-date {"textAlign":"left","format":"M j, Y","style":{"spacing":{"margin":{"top":"0","right":"0","bottom":"0","left":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}},"typography":{"textTransform":"uppercase","fontSize":"13px"}},"textColor":"tertiary"} /-->
					</div>
					<!-- /wp:group -->

					<!-- wp:post-terms {"term":"category","prefix":" ","style":{"elements":{"link":{"color":{"text":"var:preset|color|tertiary"},":hover":{"color":{"text":"var:preset|color|primary"}}}},"typography":{"textTransform":"uppercase","fontSize":"13px"}},"textColor":"contrast"} /-->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"style":{"spacing":{"padding":{"top":"16px","bottom":"16px","right":"16px","left":"16px"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
			<div class="wp-block-group"
				style="padding-top:16px;padding-right:16px;padding-bottom:16px;padding-left:16px">
				<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"0","right":"0","bottom":"0","left":"0"}},"layout":{"selfStretch":"fit"},"typography":{"fontStyle":"normal","fontWeight":"500","lineHeight":"1.1","textTransform":"none","fontSize":"2rem"},"elements":{"link":{"color":{"text":"var:preset|color|contrast"},":hover":{"color":{"text":"var:preset|color|primary"}}}}},"textColor":"contrast"} /-->
			</div>
			<!-- /wp:group -->
			<!-- /wp:post-template -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|30","right":"16px","left":"16px"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
		<div class="wp-block-group alignwide"
			style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--80);padding-right:16px;padding-bottom:var(--wp--preset--spacing--30);padding-left:16px">

			<?php
			// Need to include from PHP since wp:pattern not working
			// <!-- wp:pattern {"slug":"patterns-docs/pagination"} /-->
			// <!-- wp:pattern {"slug":"patterns-docs/hidden-query-no-results"} /--> .
			require 'pagination.php';
			require 'hidden-query-no-results.php';
			?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:query -->
