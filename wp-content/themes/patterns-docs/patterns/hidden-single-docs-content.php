<?php
/**
 * Title: Single Docs
 * Slug: patterns-docs/hidden-single-docs-content
 * Inserter: no
 * Categories: posts
 * Description: A layout that displays single post content with post navigation and comments.
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/patterns
 * @since      1.0.0
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":"0px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-right:0;padding-left:0">

	<!-- wp:post-title {"align":"full"} /-->
	<!-- wp:spacer {"height":"var:preset|spacing|20"} -->
	<div style="height:var(--wp--preset--spacing--20)" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->
	<!-- wp:separator {"align":"wide","className":"is-style-wide","style":{"color":{"background":"#f5f5f5"}}} -->
	<hr class="wp-block-separator alignwide has-text-color has-alpha-channel-opacity has-background is-style-wide" style="background-color:#f5f5f5;color:#f5f5f5"/>
	<!-- /wp:separator -->
	<!-- wp:spacer {"height":"var:preset|spacing|20"} -->
	<div style="height:var(--wp--preset--spacing--20)" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:post-content {"align":"full","className":"pwp-child-reset","layout":{"type":"default"}} /-->

	<!-- wp:template-part {"slug":"post-navigation-docs","area":"uncategorized","align":"full"} /-->

</div>
<!-- /wp:group -->
