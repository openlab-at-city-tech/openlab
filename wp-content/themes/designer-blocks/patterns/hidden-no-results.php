<?php
/**
 * Title: Hidden No Results Content
 * Slug: designer-blocks/hidden-no-results-content
 * Inserter: no
 */
?>
<!-- wp:paragraph -->
<p>
<?php echo esc_html_x( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'Message explaining that there are no results returned from a search', 'designer-blocks' ); ?>
</p>
<!-- /wp:paragraph -->

<!-- wp:search {"label":"<?php echo esc_html_x( 'Search', 'label', 'designer-blocks' ); ?>","placeholder":"<?php echo esc_attr_x( 'Search...', 'placeholder for search field', 'designer-blocks' ); ?>","showLabel":false,"buttonText":"<?php esc_attr_e( 'Search', 'designer-blocks' ); ?>","buttonUseIcon":true} /-->
