<?php

//needed by university of leicster
//add_action('wp_print_styles', 'lightbubbles_wp_print_styles');
//add_action('wp_print_scripts', 'lightbubbles_wp_print_scripts' );

function lightbubbles_wp_print_styles(){
?>
<link rel="stylesheet" href="<?php echo get_digressit_media_uri('css/lightbubbles.css'); ?>" type="text/css" media="screen" />
<?php
}

function lightbubbles_wp_print_scripts(){	
	wp_enqueue_script('digressit.rounded_corners', get_digressit_media_uri('js/rounded_corners.inc.js'), 'jquery', false, true );
	wp_enqueue_script('digressit.lightbubbles', get_digressit_media_uri('js/digressit.lightbubbles.js'), 'jquery', false, true );
}



?>