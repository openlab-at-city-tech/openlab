<?php
/**
 * Title: Primary Header
 * Slug: patterns-docs/hidden-primary-header
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/patterns
 * @since      1.0.0
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)">

	<!-- wp:group {"align":"wide","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
	<div class="wp-block-group alignwide">	
		
		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"},"layout":{"selfStretch":"fill","flexSize":null}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
			<div class="wp-block-group"> 
				<!-- wp:pattern {"slug":"patterns-docs/site-identity"} /-->
				<!-- wp:pattern {"slug":"patterns-docs/navigation-menu"} /-->
			</div>
		<!-- /wp:group -->

		<!-- wp:buttons -->
		<div class="wp-block-buttons"><!-- wp:button {"style":{"spacing":{"padding":{"left":"var:preset|spacing|30","right":"var:preset|spacing|30","top":"var:preset|spacing|10","bottom":"var:preset|spacing|10"}}}} -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" style="padding-top:var(--wp--preset--spacing--10);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--10);padding-left:var(--wp--preset--spacing--30)"><?php esc_html_e( 'Get Started', 'patterns-docs' ); ?></a></div>
		<!-- /wp:button --></div>
		<!-- /wp:buttons -->
	

	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->
