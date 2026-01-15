<?php
/**
 * Title: Copyright
 * Slug: patterns-docs/copyright
 * Categories: footer
 * Block Types: core/template-part/footer
 * Description: Dynamic copyright text
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/patterns
 * @since      1.0.0
 */

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"5px"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
	<div class="wp-block-group alignwide"><!-- wp:paragraph {"style":{"typography":{"fontSize":"x-small"}}} -->
	<p class="has-x-small-font-size"><?php esc_html_e( 'All Rights Reserved', 'patterns-docs' ); ?></p>
	<!-- /wp:paragraph -->
	<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"patterns-docs/copyright","args":{"key":"copyright"}}}},"placeholder":"<?php esc_attr_e( 'Dynamic copyright text', 'patterns-docs' ); ?>","className":"has-x-small-font-size has-tertiary-color has-text-color"} -->
	<p class="has-x-small-font-size has-tertiary-color has-text-color"></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
