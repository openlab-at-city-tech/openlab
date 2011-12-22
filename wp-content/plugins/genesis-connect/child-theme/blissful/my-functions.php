<?php

function blissful_after_bp_content() { ?>
	<div class="clear"></div>
<?php }
add_action( 'gconnect_after_content', 'blissful_after_bp_content' );
