<?php
/**
 * The Custom Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php
	get_template_part('hdr','title');
?>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	get_template_part('hdr','css');
?>
</head>

<body <?php body_class(); ?>>
<?php
    if (!weaver_getopt_checked('ttw_header_first'))	// put the header before the wrapper?
        echo "<div id=\"wrapper\" class=\"hfeed\">\n";

    $per_page_code = weaver_get_per_page_value('page-pre-header-code');	/* or on a per page basis! */
    if (!empty($per_page_code)) {
	echo(do_shortcode($per_page_code));
    } else {
    weaver_put_area('preheader');		/* here to allow total header replacement */
    }

    if (!weaver_is_checked_page_opt('ttw-hide-header')) { ?>
    <div id="header">
    <?php
    if (is_active_sidebar('header-widget-area')) { /* weaver header widget area */
	ob_start(); /* use output buffering */
	$success = dynamic_sidebar('header-widget-area');
	$content = ob_get_clean();
	if ($success) {
    ?>
    <div id="ttw-head-widget" class="ttw-head-widget-area" role="complementary" ><ul>
	<?php echo($content) ; ?>
    </ul></div> <!-- #ttw-header-widget -->
    <?php
        }  /* end if non-empty widgets */
    }

?>
	<div id="masthead">
<?php
		/* ======== SITE TITLE ======== */
		get_template_part('hdr','sitetitle');

		/* ======== TOP MENU ======== */
	        get_template_part('nav','top');

		/* ======== HEADER INSERT CODE ======== */
		echo("\n\t    ".'<div id="branding" role="banner">' . "\n");

		$per_page_code = weaver_get_per_page_value('page-header-insert-code');	/* or on a per page basis! */
		if (!empty($per_page_code)) {
			echo(do_shortcode($per_page_code));
		} elseif (weaver_getopt('ttw_custom_header_insert')) {	/* header insert defined? */
		    	echo (do_shortcode(weaver_getopt('ttw_custom_header_insert')));
		}

		/* The Dynamic Headers shows headers on a per page basis - will also optionally add site link */
		if (function_exists('show_media_header')) show_media_header();  /* **Dynamic Headers** built-in support for plugin */
		?>

	    </div><!-- #branding -->

	    <?php
	    /* ======== BOTTOM MENU ======== */
	    get_template_part('nav','bottom');
	    ?>

	</div><!-- #masthead -->
    </div><!-- #header -->

    <?php
    }	/* end of hide whole header */
    weaver_put_area('postheader');
    ?>
<?php if (weaver_getopt_checked('ttw_header_first')) echo "<div id=\"wrapper\" class=\"hfeed\">\n"; ?>

    <div id="main">
