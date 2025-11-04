<?php
/**
 * Title:404
 * Slug: patterns-docs/hidden-404
 * Inserter: no
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/patterns
 * @since      1.0.0
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"secondary","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-secondary-background-color has-background"><!-- wp:spacer {"height":"200px"} -->
<div style="height:200px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"style":{"spacing":{"blockGap":"0px"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group">
	
<!-- wp:heading {"textAlign":"left","level":1,"align":"wide","style":{"typography":{"fontSize":"8rem"}}} -->
<h1 class="wp-block-heading alignwide has-text-align-left" style="font-size:8rem"><?php esc_html_e( 'Ooops!', 'patterns-docs' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"left","style":{"spacing":{"margin":{"bottom":"15px"}}},"fontSize":"large"} -->
<p class="has-text-align-left has-large-font-size" style="margin-bottom:15px"><?php esc_html_e( 'This page could not be found.', 'patterns-docs' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"left","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}},"fontSize":"small"} -->
<p class="has-text-align-left has-small-font-size" style="margin-bottom:var(--wp--preset--spacing--30)"><?php esc_html_e( 'We can\'t find the page you\'re looking for. Check out our Help Center or head back to home', 'patterns-docs' ); ?>
</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-fill"} -->
<div class="wp-block-button is-style-fill"><a class="wp-block-button__link wp-element-button" href="#"><?php esc_html_e( 'Help Center', 'patterns-docs' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"backgroundColor":"accent","textColor":"base","className":"is-style-fill"} -->
<div class="wp-block-button is-style-fill"><a class="wp-block-button__link has-base-color has-accent-background-color has-text-color has-background wp-element-button" href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home', 'patterns-docs' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"200px"} -->
<div style="height:200px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group -->
