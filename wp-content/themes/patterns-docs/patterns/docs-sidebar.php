<?php
/**
 * Title: Docs Sidebar
 * Slug: patterns-docs/docs-sidebar
 * Categories: posts
 * Block Types: core/template-part/docs-sidebar
 * Description: Display a collection of blocks for docs sidebar template part.
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/patterns
 * @since      1.0.0
 */

?>
<!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:search {"label":"Search","showLabel":false,"placeholder":"<?php esc_attr_e( 'Search documentations', 'patterns-docs' ); ?>","buttonText":"<?php esc_attr_e( 'Search', 'patterns-docs' ); ?>"} /-->

<!-- wp:categories {"showHierarchy":true,"showPostCounts":true} /--></div>
<!-- /wp:group -->
