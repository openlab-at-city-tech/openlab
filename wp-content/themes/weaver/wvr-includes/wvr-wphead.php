<?php
// This file is included from functions.php. It will be loaded only when the wp_head action is called from WordPress.

if ( ! function_exists( 'weaver_generate_wphead()' ) ) :	/* Allow child to override this */
function weaver_generate_wphead() {
    /* this guy does ALL the work for generating theme look - it writes out the over-rides to the standard style.css */
    global $weaver_main_options, $weaver_cur_page_ID;

    // if ( have_posts() )
    //   the_post();
    $weaver_cur_page_ID = get_the_ID();	// we're on a page now, so set the post id for the rest of the session
    // rewind_posts();

    printf("\n<!-- This site is using %s %s subtheme: %s -->\n",WEAVER_THEMENAME, WEAVER_VERSION, weaver_getopt('ttw_subtheme'));

    if (!weaver_getopt('ttw_hide_metainfo'))
	echo(weaver_getopt('ttw_metainfo')."\n");

    // handle 3 stylesheet situations
    //	default: used weaver-style.css
    //	no weaver-style.css: when first installed, there will not be a weaver-style.css, so use inline instead
    //	force inline: user wants inline css

    if (weaver_use_inline_css( weaver_get_css_filename() )) { // generate inline CSS
	if (!weaver_getopt('ttw_subtheme')) {
	    // It would be nice to use wp_enqueue_style, but it is too late at this point, so we
	    // will generate the code manually - this is a one-shot special case that happens ONLY
	    // when the theme preview option is being used from the themes admin page
	    echo '<link rel="stylesheet" type="text/css" media="all" href="' .
	    get_stylesheet_directory_uri() . '/subthemes/style-weaver-preview.css" />' . "\n";
	} else {
	    require_once('wvr-generatecss.php'); 	// include only now at runtime.
	    echo('<style type="text/css">'."\n");
	    $output = weaver_f_open('php://output','w+');
	    weaver_output_style($output);
	    echo("</style> <!-- end of main options style section -->\n");
	}

    } else {
	// file should have been added from header.php
    }

   /* now head options */
    echo(weaver_getopt('ttw_theme_head_opts'));
    echo(weaver_getopt('ttw_head_opts'));		/* let the user have the last word! */

    $per_page_code = weaver_get_per_page_value('page-head-code');
    if (!empty($per_page_code)) {
	echo($per_page_code);
    }
    weaver_fix_IE();
    do_action('wvrx_extended_wp_head'); 	/* call extended wp_head stuff */
    do_action('wvrx_plus_wp_head');		// future header plugin

    echo("\n<!-- End of Weaver options -->\n");

}
endif;

function weaver_fix_IE() {
    $add_PIE = (weaver_getopt('ttw_rounded_corners') || weaver_getopt('ttw_wrap_shadow')) && !weaver_getopt('ttw_hide_PIE');
    echo("\n");
?>
<!--[if lte IE 7]>
<style type="text/css" media="screen">
div.menu { display:inline !important;}
.menu-add, .menu-add-left {margin-top:-4px !important;}
</style>
<![endif]-->
<?php

    if ($add_PIE) { ?>
<!--[if lte IE 8]>
<style type="text/css" media="screen">
<?php  weaver_bake_PIE(); ?>
</style>
<![endif]-->
<?php
    }
}

function weaver_bake_PIE() {
/**
* Attach CSS3PIE behavior to elements
* Add elements here that need PIE applied
*/
   $pie_loc = get_template_directory_uri() . '/js/PIE/PIE.php';
// #primary, #secondary, #altleft, #altright, #ttw-top-widget, #ttw-bot-widget,
//  #access, #access2
    echo("#primary, #secondary, #altleft, #altright, #ttw-top-widget, #ttw-bot-widget,
  #ttw-site-top-widget, #ttw-site-bot-widget, #per-page-widget, #wrapper {
  behavior: url($pie_loc); position:relative; }\n");
}
?>
