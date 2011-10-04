<?php
/**
 * WARNING: These functions are no longer used by Genesis Core.
 * To reference a hook, use do_action( 'genesis_hook' );
 *
 * To attach functions to hook, use the same method as before:
 * add_action( 'genesis_hook', 'function_to_attach' );
 *
 * @package Genesis
 **/

function genesis_pre() { do_action('genesis_pre'); }
function genesis_pre_framework() { do_action('genesis_pre_framework'); }
function genesis_init() { do_action('genesis_init'); }

function genesis_doctype() { do_action('genesis_doctype'); }

function genesis_title() { do_action('genesis_title'); }

function genesis_meta() { do_action('genesis_meta'); }

function genesis_before() { do_action('genesis_before'); }
function genesis_after() { do_action('genesis_after'); }

function genesis_before_header() { do_action('genesis_before_header'); }
function genesis_header() { do_action('genesis_header'); }
function genesis_header_right() { do_action('genesis_header_right'); }
function genesis_after_header() { do_action('genesis_after_header'); }

function genesis_site_title() { do_action('genesis_site_title'); }
function genesis_site_description() { do_action('genesis_site_description'); }

function genesis_before_content_sidebar_wrap() { do_action('genesis_before_content_sidebar_wrap'); }	
function genesis_after_content_sidebar_wrap() { do_action('genesis_after_content_sidebar_wrap'); }

function genesis_before_content() { do_action('genesis_before_content'); }	
function genesis_after_content() { do_action('genesis_after_content'); }

function genesis_home() { do_action('genesis_home'); } /** optional for child theme home.php files **/

function genesis_before_loop() { do_action('genesis_before_loop'); }
function genesis_loop() { do_action('genesis_loop'); }
function genesis_after_loop() { do_action('genesis_after_loop'); }

function genesis_before_post() { do_action('genesis_before_post'); }
function genesis_after_post() { do_action('genesis_after_post'); }

function genesis_before_post_title() { do_action('genesis_before_post_title'); }
function genesis_post_title() { do_action('genesis_post_title'); }
function genesis_after_post_title() { do_action('genesis_after_post_title'); }

function genesis_before_post_content() { do_action('genesis_before_post_content'); }
function genesis_post_content() { do_action('genesis_post_content'); }
function genesis_after_post_content() { do_action('genesis_after_post_content'); }

function genesis_after_endwhile() { do_action('genesis_after_endwhile'); }
function genesis_loop_else() { do_action('genesis_loop_else'); }

function genesis_before_comments() { do_action('genesis_before_comments'); }
function genesis_comments() { do_action('genesis_comments'); }
function genesis_list_comments() { do_action('genesis_list_comments'); }
function genesis_after_comments() { do_action('genesis_after_comments'); }

function genesis_before_pings() { do_action('genesis_before_pings'); }
function genesis_pings() { do_action('genesis_pings'); }
function genesis_list_pings() { do_action('genesis_list_pings'); }
function genesis_after_pings() { do_action('genesis_after_pings'); }

function genesis_before_comment() { do_action('genesis_before_comment'); }
function genesis_after_comment() { do_action('genesis_after_comment'); }

function genesis_before_comment_form() { do_action('genesis_before_comment_form'); }
function genesis_comment_form() { do_action('genesis_comment_form'); }
function genesis_after_comment_form() { do_action('genesis_after_comment_form'); }

function genesis_before_sidebar_widget_area() { do_action('genesis_before_sidebar_widget_area'); }
function genesis_sidebar() { do_action('genesis_sidebar'); }
function genesis_after_sidebar_widget_area() { do_action('genesis_after_sidebar_widget_area'); }

function genesis_before_sidebar_alt_widget_area() { do_action('genesis_before_sidebar_alt_widget_area'); }
function genesis_sidebar_alt() { do_action('genesis_sidebar_alt'); }
function genesis_after_sidebar_alt_widget_area() { do_action('genesis_after_sidebar_alt_widget_area'); }

function genesis_before_footer() { do_action('genesis_before_footer'); }
function genesis_footer() { do_action('genesis_footer'); }
function genesis_after_footer() { do_action('genesis_after_footer'); }

/** Admin Hooks **/
function genesis_import_export_form() { do_action('genesis_import_export_form'); }