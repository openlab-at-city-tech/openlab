<?php
/**
 * The Header for the pop up template.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 */
 /* possible overrides:
<style>
#colophon, #branding, #main, #wrapper { width: 300px; }
#header {padding-top: 1px;}
#footer {margin-bottom: 1px;}
#main {padding: 0px 0 0 0;}
#wrapper {
margin-top: 0px; !important;
padding: 0 0px; !important;
}
</style>
*/
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php
    get_template_part('hdr','title');
?>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
    /* add relevant stuff from wvr-wphead.php - don't call wp_head() */
    /* this guy does ALL the work for generating theme look - it writes out the over-rides to the standard style.css */
    global $weaver_main_options, $weaver_cur_page_ID;


    $weaver_cur_page_ID = get_the_ID();	// we're on a page now, so set the post id for the rest of the session

    printf("\n<!-- This site is using %s %s subtheme: %s -->\n",WEAVER_THEMENAME, WEAVER_VERSION, weaver_getopt('ttw_subtheme'));
    // handle 3 stylesheet situations
    //	default: used weaver-style.css
    //	no weaver-style.css: when first installed, there will not be a weaver-style.css, so use inline instead
    //	force inline: user wants inline css

    if (weaver_get_per_page_value('weaver_popup_css')) { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<?php
    if (!weaver_use_inline_css(weaver_get_css_filename())) { // use file
	    $vers = weaver_getopt('ttw_style_version');
	    if (!$vers) $vers = '0';
	    else $vers = sprintf("%d",$vers);
		wp_register_style('weaver-style-sheet',weaver_get_css_url(),array(),$vres);
		wp_enqueue_style('weaver-style-sheet');
    } else { // generate inline CSS
	require_once('wvr-includes/wvr-generatecss.php'); 	// include only now at runtime.
	echo('<style type="text/css">'."\n");
	$output = weaver_f_open('php://output','w+');
	weaver_output_style($output);
	echo("</style> <!-- end of main options style section -->\n");
    }
    } else {
?>
<style type="text/css">
body, p, #wrapper, #main {
	background: transparent;
	border: 0;
	margin: 0;
	padding: 0;
	vertical-align: baseline;
}
</style>
<?php
    }


    if (!weaver_getopt('ttw_hide_metainfo'))
	echo(weaver_getopt('ttw_metainfo')."\n");

    $per_page_code = weaver_get_per_page_value('page-head-code');
    if (!empty($per_page_code)) {
	echo($per_page_code);
    }
    echo("\n<!-- End of Weaver options -->\n");
?>
</head>

<body <?php body_class(); ?>>
<?php if (!weaver_getopt_checked('ttw_header_first')) echo "<div id=\"wrapper\" class=\"pg-popup\">\n"; ?>

<?php
    $per_page_code = weaver_get_per_page_value('page-pre-header-code');	/* or on a per page basis! */
    if (!empty($per_page_code)) {
	echo(do_shortcode($per_page_code));
    } else {
    weaver_put_area('preheader');		/* here to allow total header replacement */
    }

    if (!weaver_is_checked_page_opt('ttw-hide-header')) { ?>
    <div id="header">
<?php
    /* ======== HEADER INSERT CODE ======== */
	$per_page_code = weaver_get_per_page_value('page-header-insert-code');	/* or on a per page basis! */
	if (!empty($per_page_code)) {
	    echo(do_shortcode($per_page_code));
	}
?>
    </div><!-- #header -->

    <?php
    }	/* end of hide whole header */
?>
<?php if (weaver_getopt_checked('ttw_header_first')) echo "<div id=\"wrapper\" class=\"pg-popup\">\n"; ?>

    <div id="main">
