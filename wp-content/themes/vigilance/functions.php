<?php
//Set language folder and load textdomain
if (file_exists(STYLESHEETPATH . '/languages' ))
	$language_folder = (STYLESHEETPATH . '/languages' );
else
	$language_folder = (TEMPLATEPATH . '/languages' );
load_theme_textdomain( 'vigilance', $language_folder);

//Add support for post thumbnails
if ( function_exists( 'add_theme_support' ) )
	add_theme_support( 'post-thumbnails' );

//Redirect to theme options page on activation
if ( is_admin() && isset($_GET['activated'] ) && $pagenow ==	"themes.php" )
	wp_redirect( 'themes.php?page=vigilance-admin.php' );

// Required functions
if (is_file(STYLESHEETPATH . '/functions/sidebars.php' ))
	require_once(STYLESHEETPATH . '/functions/sidebars.php' );
else
	require_once(TEMPLATEPATH . '/functions/sidebars.php' );

if (is_file(STYLESHEETPATH . '/functions/comments.php' ))
	require_once(STYLESHEETPATH . '/functions/comments.php' );
else
	require_once(TEMPLATEPATH . '/functions/comments.php' );

if (is_file(STYLESHEETPATH . '/functions/vigilance-extend.php' ))
	require_once(STYLESHEETPATH . '/functions/vigilance-extend.php' );
else
	require_once(TEMPLATEPATH . '/functions/vigilance-extend.php' );
?>