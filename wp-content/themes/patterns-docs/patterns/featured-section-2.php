<?php
/**
 * Title: Featured Section 2
 * Slug: patterns-docs/featured-section-2
 * Categories: featured
 * Description: A layout with an image in the left column and a title, content, and Card 2 pattern in the right column.
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/patterns
 * @since      1.0.0
 */

?>
<!-- wp:group {"metadata":{"name":"Featured Section 2"},"align":"full","style":{"color":{"gradient":"linear-gradient(180deg,rgb(218,246,252) 50%,rgb(255,255,255) 50%)"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background" style="background:linear-gradient(180deg,rgb(218,246,252) 50%,rgb(255,255,255) 50%)"><!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"33.33%","style":{"shadow":"var:preset|shadow|natural"}} -->
<div class="wp-block-column" style="box-shadow:var(--wp--preset--shadow--natural);flex-basis:33.33%">
    <!-- wp:pattern {"slug":"patterns-docs/card-1"} /-->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%","style":{"shadow":"var:preset|shadow|natural"}} -->
<div class="wp-block-column" style="box-shadow:var(--wp--preset--shadow--natural);flex-basis:33.33%">
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"Card"},"style":{"spacing":{"blockGap":"0px"}},"backgroundColor":"default","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-default-background-color has-background"><!-- wp:group {"metadata":{"name":"Card Body"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
<div class="wp-block-group"><!-- wp:image {"width":"100px","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/icon-2.png" style="width:100px"/></figure>
<!-- /wp:image -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Community Forums', 'patterns-docs' ); ?></h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php esc_html_e( 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium ', 'patterns-docs' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:button {"className":"is-style-outline has-x-small-font-size","style":{"spacing":{"padding":{"left":"18px","right":"18px","top":"8px","bottom":"8px"}},"border":{"width":"1px"}},"fontSize":"x-small"} -->
<div class="wp-block-button has-custom-font-size is-style-outline has-x-small-font-size"><a class="wp-block-button__link wp-element-button" style="border-width:1px;padding-top:8px;padding-right:18px;padding-bottom:8px;padding-left:18px"><?php esc_html_e( 'Read More', 'patterns-docs' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

</div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%","style":{"shadow":"var:preset|shadow|natural"}} -->
<div class="wp-block-column" style="box-shadow:var(--wp--preset--shadow--natural);flex-basis:33.33%">
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"Card"},"style":{"spacing":{"blockGap":"0px"}},"backgroundColor":"default","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-default-background-color has-background"><!-- wp:group {"metadata":{"name":"Card Body"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
<div class="wp-block-group"><!-- wp:image {"width":"100px","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/icon-3.png" style="width:100px"/></figure>
<!-- /wp:image -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Documentations', 'patterns-docs' ); ?></h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php esc_html_e( 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium ', 'patterns-docs' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:button {"className":"is-style-outline has-x-small-font-size","style":{"spacing":{"padding":{"left":"18px","right":"18px","top":"8px","bottom":"8px"}},"border":{"width":"1px"}},"fontSize":"x-small"} -->
<div class="wp-block-button has-custom-font-size is-style-outline has-x-small-font-size"><a class="wp-block-button__link wp-element-button" style="border-width:1px;padding-top:8px;padding-right:18px;padding-bottom:8px;padding-left:18px"><?php esc_html_e( 'Read More', 'patterns-docs' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

</div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->