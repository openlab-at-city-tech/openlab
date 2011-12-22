<?php
/* 
 * add a clear after the content
 */
function expose_after_bp_content() { ?>
	<div class="clear"></div>
<?php }
add_action( 'gconnect_after_content', 'expose_after_bp_content' );
