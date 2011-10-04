<?php
/**
 * WARNING: This file is part of the core Genesis framework. DO NOT edit
 * this file under any circumstances. Please do all modifications
 * in the form of a child theme.
 *
 * Handles primary sidebar structure.
 *
 * @package Genesis
 */
?><div id="sidebar" class="sidebar widget-area">
<?php
	do_action( 'genesis_before_sidebar_widget_area' );
	do_action( 'genesis_sidebar' );
	do_action( 'genesis_after_sidebar_widget_area' );
?>
</div>