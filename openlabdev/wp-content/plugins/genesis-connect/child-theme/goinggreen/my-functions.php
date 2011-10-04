<?php
/* 
 * make room for the BP nav
 */
function goinggreen_cust_after_header() { ?>
<div id="after-header">
    <img src="<?php bloginfo('stylesheet_directory'); ?>/images/after-header.png" />
</div><!-- end #after-header -->
<?php } 

function goinggreen_genesis_meta() {
	remove_action( 'genesis_after_header', 'goinggreen_round_header_graphic' );
	add_action( 'genesis_after_header', 'goinggreen_cust_after_header', 21 );
}
add_action( 'genesis_meta', 'goinggreen_genesis_meta' );
