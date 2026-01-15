<?php
/**
 * Title: Landing
 * Slug: patterns-docs/landing
 * Template Types: front-page
 * Post Types: page
 * Description: A layout template for displaying the main landing front page.
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/patterns
 * @since      1.0.0
 */

?>
<!-- wp:template-part {"slug":"header-absolute","tagName":"header"} /-->
<!-- wp:group {"tagName":"main","metadata":{"name":"Main"},"align":"full","layout":{"type":"constrained"}} -->
<main class="wp-block-group alignfull">
	<!-- wp:pattern {"slug":"patterns-docs/featured-section-1"} /-->
	<!-- wp:pattern {"slug":"patterns-docs/featured-section-2"} /-->
	<!-- wp:pattern {"slug":"patterns-docs/featured-section-3"} /-->
	<!-- wp:pattern {"slug":"patterns-docs/featured-section-4"} /-->
</main>
<!-- /wp:group -->
<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
