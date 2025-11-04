<?php
/**
 * Title: Section Title 1
 * Slug: patterns-docs/section-title-1
 * Categories: text, featured
 * Description: A layout featuring a title, content, and button group in centered alignment, commonly used for section titles in feature areas.
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/patterns
 * @since      1.0.0
 */

?>
<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"x-large"} -->
<h1 class="wp-block-heading has-text-align-center has-x-large-font-size"><?php esc_html_e( 'Hello, how can we assist you in finding what you are looking for?', 'patterns-docs' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php esc_html_e( 'Patterns Docs is a fully featured knowledge base theme for WordPress.', 'patterns-docs' ); ?></p>
<!-- /wp:paragraph -->
