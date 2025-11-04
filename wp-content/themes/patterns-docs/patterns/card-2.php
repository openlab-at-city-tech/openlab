<?php
/**
 * Title: Card 2
 * Slug: patterns-docs/card-2
 * Categories: featured
 * Description: A layout featuring an circular design text, title, and content, commonly used in repeated columns or as a standalone section.
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/patterns
 * @since      1.0.0
 */

?>
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"Card"},"style":{"border":{"style":"solid","width":"1px"},"spacing":{"blockGap":"0px"}},"borderColor":"quaternary","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color has-quaternary-border-color" style="border-style:solid;border-width:1px"><!-- wp:group {"metadata":{"name":"Card Header"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"secondary","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-secondary-background-color has-background" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading"><?php esc_html_e( 'Introduction', 'patterns-docs' ); ?></h4>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Card Body"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20","right":"var:preset|spacing|20"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)"><!-- wp:query {"queryId":5,"query":{"perPage":"5","pages":0,"offset":0,"postType":"post","order":"asc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"category":[3]}}} -->
<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"default"}} -->
<!-- wp:group {"style":{"spacing":{"blockGap":"8px"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:image {"width":"16px","sizeSlug":"full","linkDestination":"none","style":{"spacing":{"margin":{"top":"-8px"}}}} -->
<figure class="wp-block-image size-full is-resized" style="margin-top:-8px"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/icon-4.png" style="width:16px"/></figure>
<!-- /wp:image -->

<!-- wp:post-title {"level":6,"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"400","fontSize":"14px"}}} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline","style":{"spacing":{"padding":{"left":"18px","right":"18px","top":"6px","bottom":"6px"}},"typography":{"fontSize":"12px"},"border":{"width":"1px"}}} -->
<div class="wp-block-button has-custom-font-size is-style-outline" style="font-size:12px"><a class="wp-block-button__link wp-element-button" style="border-width:1px;padding-top:6px;padding-right:18px;padding-bottom:6px;padding-left:18px"><?php esc_html_e( 'View All', 'patterns-docs' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->