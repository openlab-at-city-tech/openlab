<?php
/* 
 * add a clear after the content
 */
function manhattan_after_bp_content() { ?>
	<div class="clear"></div>
<?php }
add_action( 'gconnect_after_content', 'manhattan_after_bp_content' );
