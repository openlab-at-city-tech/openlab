<?php
/**
 * Title: Query Docs
 * Slug: patterns-docs/query-docs
 * Categories: query
 * Block Types: core/query
 * Description: Display a query block in a list layout.
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/patterns
 * @since      1.0.0
 */

?>
<!-- wp:query {"query":{"inherit":false,"postType":"post","perPage":10},"align":"wide","layout":{"type":"constrained"}} -->
<div class="wp-block-query alignwide"><!-- wp:post-template {"align":"wide","style":{"spacing":{"blockGap":"15px"}},"layout":{"type":"default"}} -->
<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"0px"},"margin":{"top":"0px","bottom":"0px"}}}} -->
<div class="wp-block-columns" style="margin-top:0px;margin-bottom:0px"><!-- wp:column {"width":"100%","style":{"spacing":{"padding":{"top":"15px","bottom":"15px","left":"15px","right":"15px"}},"border":{"width":"1px","style":"solid"},"shadow":"var:preset|shadow|natural"},"borderColor":"quinary"} -->
<div class="wp-block-column has-border-color has-quinary-border-color" style="border-style:solid;border-width:1px;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;box-shadow:var(--wp--preset--shadow--natural);flex-basis:100%"><!-- wp:post-title {"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}},"fontSize":"small"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:post-template -->

<!-- wp:spacer {"height":"var:preset|spacing|40"} -->
<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<?php
	// Need to include from PHP since wp:pattern not working
	// <!-- wp:pattern {"slug":"patterns-docs/pagination"} /-->
	// <!-- wp:pattern {"slug":"patterns-docs/hidden-query-no-results"} /--> .
	require 'pagination.php';
	require 'hidden-query-no-results.php';
?>

</div>
<!-- /wp:query -->
