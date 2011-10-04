<?php 

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Custom theme actions/functions
	- Add specific IE styling/hacks to HEAD
	- Add custom styling
- Custom hook definitions

-----------------------------------------------------------------------------------*/


// Add specific IE styling/hacks to HEAD
add_action('wp_head','woo_IE_head'); // Add specific IE styling/hacks to HEAD
function woo_IE_head() {
?>

<!--[if IE 6]>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/includes/js/pngfix.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/includes/js/menu.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri(); ?>/css/ie6.css" />
<![endif]-->	

<!--[if IE 7]>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri(); ?>/css/ie7.css" />
<![endif]-->

<!--[if IE 8]>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri(); ?>/css/ie8.css" />
<![endif]-->

<?php
}

// Add custom styling
add_action( 'woo_head','woo_custom_styling' );

function woo_custom_styling() {
	
	$output = '';
	// Get options
	$body_color = get_option( 'woo_body_color' );
	$body_img = get_option( 'woo_body_img' );
	$body_repeat = get_option( 'woo_body_repeat' );
	$body_position = get_option( 'woo_body_pos' );
	$link = get_option( 'woo_link_color' );
	$hover = get_option( 'woo_link_hover_color' );
	$button = get_option( 'woo_button_color' );
		
	// Add CSS to output
	if ( $body_color ) {
		$output .= 'body {background-color:'.$body_color.'}' . "\n";
	}
	if ( $body_img ) {
		$output .= 'body {background-image:url('.$body_img.')}' . "\n";
	}
	if ( $body_repeat && $body_position ) {
		$output .= 'body {background-repeat:'.$body_repeat.'}' . "\n";
	}
	if ( $body_img && $body_position ) {
		$output .= 'body {background-position:'.$body_position.'}' . "\n";
	}
	if ( $link ) {
		$output .= 'a:link, a:visited {color:'.$link.'}' . "\n";
	}
	if ( $hover ) {
		$output .= 'a:hover {color:'.$hover.'}' . "\n";
	}
	if ( $button ) {
		$output .= '.button, .reply a {background-color:'.$button.'}' . "\n";
	}
	
	// Output styles
	if ( isset( $output ) && ( $output != '' ) ) {
		$output = "<!-- Woo Custom Styling -->\n<style type=\"text/css\">\n" . $output . "</style>\n";
		echo $output;
	}
		
} 



/*-----------------------------------------------------------------------------------*/
/* Custom Hook definition */
/*-----------------------------------------------------------------------------------*/

// Add any custom hook definitions you want here
// function woo_hook_name() { do_action( 'woo_hook_name' ); }					

?>