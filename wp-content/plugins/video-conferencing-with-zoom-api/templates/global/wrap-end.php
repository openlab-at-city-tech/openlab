<?php
/**
 * @author Deepen.
 * @created_on 11/26/19
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$template = video_conference_zoom_get_current_theme_slug();

switch ( $template ) {
	case 'Divi' :
		echo '</div></div></div>';
		break;
	case 'twentyten' :
		echo '</div></div>';
		break;
	case 'twentyeleven' :
		echo '</div>';
		get_sidebar( 'shop' );
		echo '</div>';
		break;
	case 'twentytwelve' :
		echo '</div></div>';
		break;
	case 'twentythirteen' :
		echo '</div></div>';
		break;
	case 'twentyfourteen' :
		echo '</div></div></div>';
		get_sidebar( 'content' );
		break;
	case 'twentyfifteen' :
		echo '</div></div>';
		break;
	case 'twentysixteen' :
		echo '</main></div>';
		break;
	case 'twentynineteen' :
		echo '</main></section>';
	default :
		echo '</main></div>';
		break;
}
