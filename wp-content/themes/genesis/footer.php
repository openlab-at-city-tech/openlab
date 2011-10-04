<?php
/**
 * WARNING: This file is part of the core Genesis framework. DO NOT edit
 * this file under any circumstances. Please do all modifications
 * in the form of a child theme.
 *
 * Handles the footer structure.
 *
 * @package Genesis
 */

genesis_structural_wrap( 'inner', '</div><!-- end .wrap -->' );
echo '</div><!-- end #inner -->';

do_action( 'genesis_before_footer' );
do_action( 'genesis_footer' );
do_action( 'genesis_after_footer' );
?>
</div><!-- end #wrap -->
<?php
	wp_footer(); // we need this for plugins
	do_action( 'genesis_after' );
?>
</body>
</html>