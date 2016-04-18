<?php

/**
 * Array definition for plugin options linked to the corresponding
 * module with which will develop the function getOptions()
 *
 * @package SZGoogle
 * @subpackage Options
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Definition array() with all the options connected to the
// module which must be called by an include (setoptions)

return array(
	'plus_page'                         => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'plus_profile'                      => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'plus_community'                    => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'plus_language'                     => array('N'=>'1','Y'=>'0','Z'=>'0','value' => '99'),
	'plus_widget_pr_enable'             => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_widget_pa_enable'             => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_widget_co_enable'             => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_widget_fl_enable'             => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_widget_size_portrait'         => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'plus_widget_size_landscape'        => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'plus_shortcode_pr_enable'          => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_shortcode_pa_enable'          => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_shortcode_co_enable'          => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_shortcode_fl_enable'          => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_shortcode_size_portrait'      => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'plus_shortcode_size_landscape'     => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'plus_button_enable_plusone'        => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_button_enable_sharing'        => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_button_enable_follow'         => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_button_enable_widget_plusone' => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_button_enable_widget_sharing' => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_button_enable_widget_follow'  => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_comments_gp_enable'           => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_comments_wp_enable'           => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_comments_ac_enable'           => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_comments_aw_enable'           => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_comments_wd_enable'           => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_comments_sh_enable'           => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_comments_dt_enable'           => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_comments_dt_day'              => array('N'=>'1','Y'=>'0','Z'=>'0','value' => '0'),
	'plus_comments_dt_month'            => array('N'=>'1','Y'=>'0','Z'=>'0','value' => '0'),
	'plus_comments_dt_year'             => array('N'=>'1','Y'=>'0','Z'=>'0','value' => '0'),
	'plus_comments_fixed_size'          => array('N'=>'0','Y'=>'0','Z'=>'1','value' => '' ),
	'plus_comments_title'               => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'plus_comments_css_class_1'         => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'plus_comments_css_class_2'         => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'plus_redirect_sign'                => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_redirect_plus'                => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_redirect_curl'                => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_redirect_sign_url'            => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'plus_redirect_plus_url'            => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'plus_redirect_curl_fix'            => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'plus_redirect_curl_url'            => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'plus_redirect_curl_dir'            => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'plus_redirect_flush'               => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_system_javascript'            => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_post_enable_widget'           => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_post_enable_shortcode'        => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '1'),
	'plus_enable_author'                => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_enable_publisher'             => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_enable_recommendations'       => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_usercontact_page'             => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_usercontact_community'        => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_usercontact_bestpost'         => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'plus_author_badge'                 => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
);