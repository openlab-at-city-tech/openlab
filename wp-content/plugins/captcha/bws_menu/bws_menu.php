<?php
/*
* Function for displaying BestWebSoft menu
* Version: 1.7.4
*/

if ( ! function_exists ( 'bws_admin_enqueue_scripts' ) )
	require_once( dirname( __FILE__ ) . '/bws_functions.php' );

if ( ! function_exists( 'bws_add_menu_render' ) ) {
	function bws_add_menu_render() {
		global $wpdb, $wp_version, $bws_plugin_info;
		$error = $message = $bwsmn_form_email = '';
		$bws_donate_link = 'http://bestwebsoft.com/donate/';

		if ( ! function_exists( 'is_plugin_active_for_network' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( function_exists( 'is_multisite' ) )
			$admin_url = ( ! is_multisite() ) ? admin_url( '/' ) : network_admin_url( '/' );
		else
			$admin_url = admin_url( '/' );

		$bws_plugins = array(
			'captcha/captcha.php' => array(
				'name'			=> 'Captcha',
				'description'	=> 'Plugin intended to prove that the visitor is a human being and not a spam robot.',
				'link'			=> 'http://bestwebsoft.com/products/captcha/?k=d678516c0990e781edfb6a6c874f0b8a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/captcha/download/?k=d678516c0990e781edfb6a6c874f0b8a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Captcha+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=captcha.php',
				'pro_version'	=> 'captcha-pro/captcha_pro.php',
				'purchase'		=> 'http://bestwebsoft.com/products/captcha/buy/?k=ff7d65e55e5e7f98f219be9ed711094e&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings'	=> 'admin.php?page=captcha_pro.php'
			),
			'contact-form-plugin/contact_form.php' => array(
				'name'			=> 'Contact Form',
				'description'	=> 'Add Contact Form to your WordPress website.',
				'link'			=> 'http://bestwebsoft.com/products/contact-form/?k=012327ef413e5b527883e031d43b088b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/contact-form/download/?k=012327ef413e5b527883e031d43b088b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Contact+Form+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=contact_form.php',
				'pro_version'	=> 'contact-form-pro/contact_form_pro.php',
				'purchase'		=> 'http://bestwebsoft.com/products/contact-form/buy/?k=773dc97bb3551975db0e32edca1a6d71&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings'	=> 'admin.php?page=contact_form_pro.php'
			),
			'facebook-button-plugin/facebook-button-plugin.php' => array(
				'name'			=> 'Facebook Like Button',
				'description'	=> 'Allows you to add the Follow and Like buttons the easiest way.',
				'link'			=> 'http://bestwebsoft.com/products/facebook-like-button/?k=05ec4f12327f55848335802581467d55&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/facebook-like-button/download/?k=05ec4f12327f55848335802581467d55&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Facebook+Like+Button+Plugin+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=facebook-button-plugin.php',
				'pro_version'	=> 'facebook-button-pro/facebook-button-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/facebook-like-button/buy/?k=8da168e60a831cfb3525417c333ad275&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings' 	=> 'admin.php?page=facebook-button-pro.php'
			),
			'twitter-plugin/twitter.php' => array(
				'name'			=> 'Twitter',
				'description'	=> 'Allows you to add the Twitter "Follow" and "Like" buttons the easiest way.',
				'link'			=> 'http://bestwebsoft.com/products/twitter/?k=f8cb514e25bd7ec4974d64435c5eb333&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/twitter/download/?k=f8cb514e25bd7ec4974d64435c5eb333&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Twitter+Plugin+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=twitter.php',
				'pro_version'	=> 'twitter-pro/twitter-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/twitter/buy/?k=63ecbf0cc9cebf060b5a3c9362299700&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings' 	=> 'admin.php?page=twitter-pro.php'
			),
			'portfolio/portfolio.php' => array(
				'name'			=> 'Portfolio',
				'description'	=> 'Allows you to create a page with the information about your past projects.',
				'link'			=> 'http://bestwebsoft.com/products/portfolio/?k=1249a890c5b7bba6bda3f528a94f768b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/portfolio/download/?k=1249a890c5b7bba6bda3f528a94f768b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Portfolio+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=portfolio.php',
				'pro_version'	=> 'portfolio-pro/portfolio-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/portfolio/buy/?k=2cc716026197d36538a414b728e49fdd&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings' 	=> 'admin.php?page=portfolio-pro.php'
			),
			'gallery-plugin/gallery-plugin.php' => array(
				'name'			=> 'Gallery',
				'description'	=> 'Allows you to implement a Gallery page into your website.',
				'link'			=> 'http://bestwebsoft.com/products/gallery/?k=2da21c0a64eec7ebf16337fa134c5f78&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/gallery/download/?k=2da21c0a64eec7ebf16337fa134c5f78&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Gallery+Plugin+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=gallery-plugin.php',
				'pro_version'	=> 'gallery-plugin-pro/gallery-plugin-pro.php',
				'purchase'		=> 'http://bestwebsoft.com/products/gallery/buy/?k=382e5ce7c96a6391f5ffa5e116b37fe0&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings'	=> 'admin.php?page=gallery-plugin-pro.php'
			),
			'adsense-plugin/adsense-plugin.php'=> array(
				'name'			=> 'Google AdSense',
				'description'	=> 'Allows Google AdSense implementation to your website.',
				'link'			=> 'http://bestwebsoft.com/products/google-adsense/?k=60e3979921e354feb0347e88e7d7b73d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/google-adsense/download/?k=60e3979921e354feb0347e88e7d7b73d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Adsense+Plugin+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=adsense-plugin.php',
				'pro_version'	=> 'adsense-pro/adsense-pro.php',
				'purchase'		=> 'http://bestwebsoft.com/products/google-adsense/buy/?k=c23889b293d62aa1ad2c96513405f0e1&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings'	=> 'admin.php?page=adsense-pro.php'
			),
			'custom-search-plugin/custom-search-plugin.php'=> array(
				'name'			=> 'Custom Search',
				'description'	=> 'Allows to extend your website search functionality by adding a custom post type.',
				'link'			=> 'http://bestwebsoft.com/products/custom-search/?k=933be8f3a8b8719d95d1079d15443e29&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/custom-search/download/?k=933be8f3a8b8719d95d1079d15443e29&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Custom+Search+plugin+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=custom_search.php',
				'pro_version'	=> 'custom-search-pro/custom-search-pro.php',
				'purchase'		=> 'http://bestwebsoft.com/products/custom-search/buy/?k=062b652ac6ac8ba863c9f30fc21d62c6&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings'	=> 'admin.php?page=custom_search_pro.php'
			),
			'quotes-and-tips/quotes-and-tips.php'=> array(
				'name'			=> 'Quotes and Tips',
				'description'	=> 'Allows you to implement quotes & tips block into your web site.',
				'link'			=> 'http://bestwebsoft.com/products/quotes-and-tips/?k=5738a4e85a798c4a5162240c6515098d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/quotes-and-tips/download/?k=5738a4e85a798c4a5162240c6515098d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Quotes+and+Tips+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=quotes-and-tips.php'
			),
			'google-sitemap-plugin/google-sitemap-plugin.php'=> array(
				'name'			=> 'Google Sitemap',
				'description'	=> 'Allows you to add sitemap file to Google Webmaster Tools.',
				'link'			=> 'http://bestwebsoft.com/products/google-sitemap/?k=5202b2f5ce2cf85daee5e5f79a51d806&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/google-sitemap/download/?k=5202b2f5ce2cf85daee5e5f79a51d806&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Google+sitemap+plugin+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=google-sitemap-plugin.php',
				'pro_version'	=> 'google-sitemap-pro/google-sitemap-pro.php',
				'purchase'		=> 'http://bestwebsoft.com/products/google-sitemap/buy/?k=7ea384a5cc36cb4c22741caa20dcd56d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings'	=> 'admin.php?page=google-sitemap-pro.php'
			),
			'updater/updater.php'=> array(
				'name'			=> 'Updater',
				'description'	=> 'Allows you to update plugins and WP core.',
				'link'			=> 'http://bestwebsoft.com/products/updater/?k=66f3ecd4c1912009d395c4bb30f779d1&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/updater/download/?k=66f3ecd4c1912009d395c4bb30f779d1&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=updater+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=updater-options',
				'pro_version'	=> 'updater-pro/updater_pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/updater/buy/?k=cf633acbefbdff78545347fe08a3aecb&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings' 	=> 'admin.php?page=updater-pro-options'
			),
			'custom-fields-search/custom-fields-search.php'=> array(
				'name'			=> 'Custom Fields Search',
				'description'	=> 'Allows you to add website search any existing custom fields.',
				'link'			=> 'http://bestwebsoft.com/products/custom-fields-search/?k=f3f8285bb069250c42c6ffac95ed3284&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/custom-fields-search/download/?k=f3f8285bb069250c42c6ffac95ed3284&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Custom+Fields+Search+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=custom_fields_search.php'
			),
			'google-one/google-plus-one.php' => array(
				'name'			=> 'Google +1',
				'description'	=> 'Allows you to see how many times your page has been liked on Google Search Engine as well as who has liked the article.',
				'link'			=> 'http://bestwebsoft.com/products/google-plus-one/?k=ce7a88837f0a857b3a2bb142f470853c&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/google-plus-one/download/?k=ce7a88837f0a857b3a2bb142f470853c&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Google+%2B1+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=google-plus-one.php',
				'pro_version'	=> 'google-one-pro/google-plus-one-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/google-plus-one/buy/?k=f4b0a62d155c9df9601a0531ad5bd832&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings' 	=> 'admin.php?page=google-plus-one-pro.php'
			),
			'relevant/related-posts-plugin.php' => array(
				'name'			=> 'Relevant - Related Posts',
				'description'	=> 'Allows you to display related posts with similar words in category, tags, title or by adding special meta key for posts.',
				'link'			=> 'http://bestwebsoft.com/products/related-posts/?k=73fb737037f7141e66415ec259f7e426&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/related-posts/download/?k=73fb737037f7141e66415ec259f7e426&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Related+Posts+Plugin+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=related-posts-plugin.php'
			),
			'contact-form-to-db/contact_form_to_db.php' => array(
				'name'			=> 'Contact Form to DB',
				'description'	=> 'Allows you to manage the messages that have been sent from your site.',
				'link'			=> 'http://bestwebsoft.com/products/contact-form-to-db/?k=ba3747d317c2692e4136ca096a8989d6&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/contact-form-to-db/download/?k=ba3747d317c2692e4136ca096a8989d6&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Contact+Form+to+DB+bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=cntctfrmtdb_settings',
				'pro_version'	=> 'contact-form-to-db-pro/contact_form_to_db_pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/contact-form-to-db/buy/?k=6ce5f4a9006ec906e4db643669246c6a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings' 	=> 'admin.php?page=cntctfrmtdbpr_settings'
			),
			'pdf-print/pdf-print.php' => array(
				'name'			=> 'PDF & Print',
				'description'	=> 'Allows you to create PDF and Print page with adding appropriate buttons to the content.',
				'link'			=> 'http://bestwebsoft.com/products/pdf-print/?k=bfefdfb522a4c0ff0141daa3f271840c&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/pdf-print/download/?k=bfefdfb522a4c0ff0141daa3f271840c&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=PDF+Print+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=pdf-print.php',
				'pro_version'	=> 'pdf-print-pro/pdf-print-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/pdf-print/buy/?k=fd43a0e659ddc170a9060027cbfdcc3a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings' 		=> 'admin.php?page=pdf-print-pro.php'
			),
			'donate-button/donate.php' => array(
				'name'			=> 'Donate',
				'description'	=> 'Makes it possible to place donation buttons of various payment systems on your web page.',
				'link'			=> 'http://bestwebsoft.com/products/donate/?k=a8b2e2a56914fb1765dd20297c26401b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/donate/download/?k=a8b2e2a56914fb1765dd20297c26401b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Donate+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=donate.php'
			),
			'post-to-csv/post-to-csv.php' => array(
				'name'			=> 'Post to CSV',
				'description'	=> 'The plugin allows to export posts of any types to a csv file.',
				'link'			=> 'http://bestwebsoft.com/products/post-to-csv/?k=653aa55518ae17409293a7a894268b8f&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/post-to-csv/download/?k=653aa55518ae17409293a7a894268b8f&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Post+To+CSV+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=post-to-csv.php'
			),
			'google-shortlink/google-shortlink.php' => array(
				'name'			=> 'Google Shortlink',
				'description'	=> 'Allows you to get short links from goo.gl servise without leaving your site.',
				'link'			=> 'http://bestwebsoft.com/products/google-shortlink/?k=afcf3eaed021bbbbeea1090e16bc22db&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/google-shortlink/download/?k=afcf3eaed021bbbbeea1090e16bc22db&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Google+Shortlink+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=gglshrtlnk_options'
			),
			'htaccess/htaccess.php' => array(
				'name'			=> 'Htaccess',
				'description'	=> 'Allows controlling access to your website using the directives Allow and Deny.',
				'link'			=> 'http://bestwebsoft.com/products/htaccess/?k=2b865fcd56a935d22c5c4f1bba52ed46&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/htaccess/download/?k=2b865fcd56a935d22c5c4f1bba52ed46&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Htaccess+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=htaccess.php',
				'pro_version'	=> 'htaccess-pro/htaccess-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/htaccess/buy/?k=59e9209a32864be534fda77d5e591c15&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings' 	=> 'admin.php?page=htaccess-pro.php'
			),
			'google-captcha/google-captcha.php' => array(
				'name'			=> 'Google Captcha (reCAPTCHA)',
				'description'	=> 'Plugin intended to prove that the visitor is a human being and not a spam robot.',
				'link'			=> 'http://bestwebsoft.com/products/google-captcha/?k=7b59fbe542acf950b29f3e020d5ad735&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/google-captcha/download/?k=7b59fbe542acf950b29f3e020d5ad735&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Google+Captcha+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=google-captcha.php',
				'pro_version'	=> 'google-captcha-pro/google-captcha-pro.php',
				'purchase'		=> 'http://bestwebsoft.com/products/google-captcha/buy/?k=773d30149acf1edc32e5c0766b96c134&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings'	=> 'admin.php?page=google-captcha-pro.php'
			),
			'sender/sender.php' => array(
				'name'			=> 'Sender',
				'description'	=> 'You can send mails to all users or to certain categories of users.',
				'link'			=> 'http://bestwebsoft.com/products/sender/?k=89c297d14ba85a8417a0f2fc05e089c7&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/sender/download/?k=89c297d14ba85a8417a0f2fc05e089c7&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Sender+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=sndr_settings',
				'pro_version'	=> 'sender-pro/sender-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/sender/buy/?k=dc5d1a87bdc8aeab2de40ffb99b38054&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings'	=> 'admin.php?page=sndrpr_settings'
			),
			'subscriber/subscriber.php' => array(
				'name'			=> 'Subscriber',
				'description'	=> 'This plugin allows you to subscribe users for newsletters from your website.',
				'link'			=> 'http://bestwebsoft.com/products/subscriber/?k=a4ecc1b7800bae7329fbe8b4b04e9c88&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/subscriber/download/?k=a4ecc1b7800bae7329fbe8b4b04e9c88&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Subscriber+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=sbscrbr_settings_page',
				'pro_version'	=> 'subscriber-pro/subscriber-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/subscriber/buy/?k=02dbb8b549925d9b74e70adc2a7282e4&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings' 	=> 'admin.php?page=sbscrbrpr_settings_page'
			),
			'contact-form-multi/contact-form-multi.php' => array(
				'name'			=> 'Contact Form Multi',
				'description'	=> 'Add-on to the Contact Form plugin that allows to create and implement multiple contact forms.',
				'link'			=> 'http://bestwebsoft.com/products/contact-form-multi/?k=83cdd9e72a9f4061122ad28a67293c72&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/contact-form-multi/download/?k=83cdd9e72a9f4061122ad28a67293c72&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Contact+Form+Multi+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> '',
				'pro_version'	=> 'contact-form-multi-pro/contact-form-multi-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/contact-form-multi/buy/?k=fde3a18581c143654f060c398b07e8ac&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings' 	=> ''
			),
			'bws-google-maps/bws-google-maps.php' => array(
				'name'			=> 'Google Maps',
				'description'	=> 'Easy to set up and insert Google Maps to your website.',
				'link'			=> 'http://bestwebsoft.com/products/bws-google-maps/?k=d8fac412d7359ebaa4ff53b46572f9f7&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/bws-google-maps/download/?k=d8fac412d7359ebaa4ff53b46572f9f7&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Google+Maps+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=bws-google-maps.php',
				'pro_version'	=> 'bws-google-maps-pro/bws-google-maps-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/bws-google-maps/buy/?k=117c3f9fc17f2c83ef430a8a9dc06f56&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings' 	=> 'admin.php?page=bws-google-maps-pro.php'
			),
			'bws-google-analytics/bws-google-analytics.php' => array(
				'name'			=> 'Google Analytics',
				'description'	=> 'Allows you to retrieve basic stats from Google Analytics account and add the tracking code to your blog.',
				'link'			=> 'http://bestwebsoft.com/products/bws-google-analytics/?k=261c74cad753fb279cdf5a5db63fbd43&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/bws-google-analytics/download/?k=261c74cad753fb279cdf5a5db63fbd43&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Google+Analytics+Bestwebsoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=bws-google-analytics.php',
				'pro_version'	=> 'bws-google-analytics-pro/bws-google-analytics-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/bws-google-analytics/buy/?k=83796e84fec3f70ecfcc8894a73a6c4a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings' 	=> 'admin.php?page=bws-google-analytics-pro.php'
			),
			'db-manager/db-manager.php' => array(
				'name'			=> 'DB Manager',
				'description'	=> 'Allows you to download the latest version of PhpMyadmin and Dumper and manage your site.',
				'link'			=> 'http://bestwebsoft.com/products/db-manager/?k=01ed9731780d87f85f5901064b7d76d8&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/db-manager/download/?k=01ed9731780d87f85f5901064b7d76d8&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> 'http://bestwebsoft.com/products/db-manager/download/?k=01ed9731780d87f85f5901064b7d76d8&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'settings'		=> 'admin.php?page=db-manager.php'
			),
			'user-role/user-role.php' => array(
				'name'			=> 'User Role',
				'description'	=> 'Allows to change wordpress user role capabilities.',
				'link'			=> 'http://bestwebsoft.com/products/user-role/?k=dfe2244835c6fbf601523964b3f34ccc&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/user-role/download/?k=dfe2244835c6fbf601523964b3f34ccc&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=User+Role+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=user-role.php',
				'pro_version'	=> 'user-role-pro/user-role-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/user-role/buy/?k=cfa9cea6613fb3d7c0a3622fa2faaf46&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings' 	=> 'admin.php?page=user-role-pro.php'
			),
			'email-queue/email-queue.php' => array(
				'name'			=> 'Email Queue',
				'description'	=> 'Allows to manage email massages sent by BestWebSoft plugins.',
				'link'			=> 'http://bestwebsoft.com/products/email-queue/?k=e345e1b6623f0dca119bc2d9433b130b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/email-queue/download/?k=e345e1b6623f0dca119bc2d9433b130b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Email+Queue+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=mlq_settings'
			),
			'limit-attempts/limit-attempts.php' => array(
				'name'			=> 'Limit Attempts',
				'description'	=> 'Allows you to limit rate of login attempts by the ip, and create whitelist and blacklist.',
				'link'			=> 'http://bestwebsoft.com/products/limit-attempts/?k=b14e1697ee4d008abcd4bd34d492573a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/limit-attempts/download/?k=b14e1697ee4d008abcd4bd34d492573a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&s=Limit+Attempts+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=limit-attempts.php',
				'pro_version'	=> 'limit-attempts-pro/limit-attempts-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/limit-attempts/buy/?k=9d42cdf22c7fce2c4b6b447e6a2856e0&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings'	=> 'admin.php?page=limit-attempts-pro.php'
			),
			'job-board/job-board.php' => array(
				'name'			=> 'Job Board',
				'description'	=> 'Allows to create a job-board page on your site.',
				'link'			=> 'http://bestwebsoft.com/products/job-board/?k=b0c504c9ce6edd6692e04222af3fed6f&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/job-board/download/?k=b0c504c9ce6edd6692e04222af3fed6f&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Job+board+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=job-board.php'
			),
			'multilanguage/multilanguage.php' => array(
				'name'			=> 'Multilanguage',
				'description'	=> 'Allows to create content on a Wordpress site in different languages.',
				'link'			=> 'http://bestwebsoft.com/products/multilanguage/?k=7d68c7bfec2486dc350c67fff57ad433&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/multilanguage/download/?k=7d68c7bfec2486dc350c67fff57ad433&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Multilanguage+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=mltlngg_settings',
				'pro_version'	=> 'multilanguage-pro/multilanguage-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/multilanguage/buy/?k=2d1121cd9a5ced583fc29eefd51bdf57&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings'	=> 'admin.php?page=mltlnggpr_settings'
			),
			'bws-popular-posts/bws-popular-posts.php' => array(
				'name'			=> 'Popular Posts',
				'description'	=> 'This plugin will help you can display the most popular posts on your blog in the widget.',
				'link'			=> 'http://bestwebsoft.com/products/popular-posts/?k=4d529f116d2b7f7df3a78018c383f975&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/popular-posts/download/?k=4d529f116d2b7f7df3a78018c383f975&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Popular+Posts+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=popular-posts.php'
			),
			'bws-testimonials/bws-testimonials.php' => array(
				'name'			=> 'Testimonials',
				'description'	=> 'Allows creating and displaying a Testimonial on your website.',
				'link'			=> 'http://bestwebsoft.com/products/testimonials/?k=3fe4bb89dc901c98e43a113e08f8db73&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/testimonials/download/?k=3fe4bb89dc901c98e43a113e08f8db73&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Testimonials+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=testimonials.php'
			),
			'bws-featured-posts/bws-featured-posts.php' => array(
				'name'			=> 'Featured Posts',
				'description'	=> 'Displays featured posts randomly on any website page.',
				'link'			=> 'http://bestwebsoft.com/products/featured-posts/?k=f0afb31185ba7c7d6d598528d69f6d97&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/featured-posts/download/?k=f0afb31185ba7c7d6d598528d69f6d97&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Featured+Posts+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=featured-posts.php'
			),
			'gallery-categories/gallery-categories.php' => array(
				'name'			=> 'Gallery Categories',
				'description'	=> 'Add-on for Gallery Plugin by BestWebSoft',
				'link'			=> 'http://bestwebsoft.com/products/gallery-categories/?k=7d68c7bfec2486dc350c67fff57ad433&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/gallery-categories/download/?k=7d68c7bfec2486dc350c67fff57ad433&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Gallery+Categories+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> ''
			),
			're-attacher/re-attacher.php' => array(
				'name'			=> 'Re-attacher',
				'description'	=> 'This plugin allows to attach, unattach or reattach media item in different post.',
				'link'			=> 'http://bestwebsoft.com/products/re-attacher/?k=4d529f116d2b7f7df3a78018c383f975&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/re-attacher/download/?k=4d529f116d2b7f7df3a78018c383f975&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Re-attacher+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=re-attacher.php'
			),
			'bws-smtp/bws-smtp.php' => array(
				'name'			=> 'SMTP',
				'description'	=> 'This plugin introduces an easy way to configure sending email messages via SMTP.',
				'link'			=> 'http://bestwebsoft.com/products/bws-smtp/?k=0546419f962704429ad2d9b88567752f&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/bws-smtp/download/?k=0546419f962704429ad2d9b88567752f&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=SMTP+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=bwssmtp_settings'
			),
			'promobar/promobar.php' => array(
				'name'			=> 'PromoBar',
				'description'	=> 'This plugin allows placing banners with any data on your website.',
				'link'			=> 'http://bestwebsoft.com/products/promobar/?k=619eac2232d9cfa382c4e678c3b14766&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/promobar/download/?k=619eac2232d9cfa382c4e678c3b14766&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=PromoBar+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=promobar.php',
				'pro_version'	=> 'promobar-pro/promobar-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/promobar/buy/?k=a9b09708502f12a1483532ba12fe2103&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings'	=> 'admin.php?page=promobar-pro.php'
			),
			'realty/realty.php' => array(
				'name'			=> 'Realty',
				'description'	=> 'A convenient plugin that adds Real Estate functionality.',
				'link'			=> 'http://bestwebsoft.com/products/realty/?k=d55de979dbbbb7af0b2ff1d7f43884fa&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/realty/download/?k=d55de979dbbbb7af0b2ff1d7f43884fa&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Realty+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=realty_settings',
				'pro_version'	=> 'realty-pro/realty-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/realty/buy/?k=c7791f0a72acfb36f564a614dbccb474&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings'	=> 'admin.php?page=realty_pro_settings'
			),
			'zendesk-help-center/zendesk-help-center.php' => array(
				'name'			=> 'Zendesk Help Center Backup',
				'description'	=> 'This plugin allows to backup Zendesk Help Center.',
				'link'			=> 'http://bestwebsoft.com/products/zendesk-help-center/?k=2a5fd2f4b2f4bde46f2ca44b8d15846d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/zendesk-help-center/download/?k=2a5fd2f4b2f4bde46f2ca44b8d15846d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Zendesk+Help+Center+Backup+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=zendesk_hc.php&tab=settings'
			),
			'social-buttons-pack/social-buttons-pack.php' => array(
				'name'			=> 'Social Buttons Pack',
				'description'	=> 'Add Social buttons to your WordPress website.',
				'link'			=> 'http://bestwebsoft.com/products/social-buttons-pack/?k=b6440fad9f54274429e536b0c61b42da&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/social-buttons-pack/download/?k=b6440fad9f54274429e536b0c61b42da&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Social+Buttons+Pack+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=social-buttons.php'
			),
			'pagination/pagination.php' => array(
				'name'			=> 'Pagination',
				'description'	=> 'Add pagination block to your WordPress website.',
				'link'			=> 'http://bestwebsoft.com/products/pagination/?k=22adb940256f149559ba8fedcd728ac8&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/pagination/download/?k=22adb940256f149559ba8fedcd728ac8&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Pagination+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=pagination.php'
			),
			'visitors-online/visitors-online.php' => array(
				'name'			=> 'Visitors Online',
				'description'	=> 'See how many users, guests and bots are online at the website.',
				'link'			=> 'http://bestwebsoft.com/products/visitors-online/?k=93c28013a4f830671b3bba9502ed5177&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/visitors-online/download/?k=93c28013a4f830671b3bba9502ed5177&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Visitors+online+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=visitors-online.php',
				'pro_version'	=> 'visitors-online-pro/visitors-online-pro.php',
				'purchase' 		=> 'http://bestwebsoft.com/products/visitors-online/buy/?k=f9a746075ff8a0a6cb192cb46526afd2&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'pro_settings'	=> 'admin.php?page=visitors-online-pro.php'
			),
			'profile-extra-fields/profile-extra-fields.php' => array(
				'name'			=> 'Profile Extra Fields',
				'description'	=> "Add additional fields on the user's profile page",
				'link'			=> 'http://bestwebsoft.com/products/profile-extra-fields/?k=fe3b6c3dbc80bd4b1cf9a27a2f339820&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/profile-extra-fields/download/?k=fe3b6c3dbc80bd4b1cf9a27a2f339820&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Profile+Extra+Fields+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=profile-extra-fields.php'
			),
			'error-log-viewer/error-log-viewer.php' => array(
				'name'			=> 'Error Log Viewer',
				'description'	=> "Work with log files and folders on the WordPress server",
				'link'			=> 'http://bestwebsoft.com/products/error-log-viewer/?k=da0de8bd2c7a0b2fea5df64d55a368b3&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'download'		=> 'http://bestwebsoft.com/products/error-log-viewer/download/?k=da0de8bd2c7a0b2fea5df64d55a368b3&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
				'wp_install'	=> $admin_url . 'plugin-install.php?tab=search&type=term&s=Error+Log+Viewer+BestWebSoft&plugin-search-input=Search+Plugins',
				'settings'		=> 'admin.php?page=rrrlgvwr.php&tab=settings'
			)		
		);

		$all_plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins' );
		$recommend_plugins = array_diff_key( $bws_plugins, $all_plugins );
		$bws_plugins_pro = array();

		foreach ( $bws_plugins as $key_plugin => $value_plugin ) {
			if ( ! isset( $all_plugins[ $key_plugin ] ) && isset( $bws_plugins[ $key_plugin ]['pro_version'] ) && isset( $all_plugins[ $bws_plugins[ $key_plugin ]['pro_version'] ] ) ) {
				unset( $recommend_plugins[ $key_plugin ] );
			}
		}

		foreach ( $all_plugins as $key_plugin => $value_plugin ) {
			if ( isset( $value_plugin['Author'] ) && $value_plugin['Author'] != "BestWebSoft" )
				unset( $all_plugins[ $key_plugin ] );
			elseif ( '-plus.php' == substr( $key_plugin, -9, 9 ) )
				unset( $all_plugins[ $key_plugin ] );
			else {
				foreach ( $bws_plugins as $key => $value ) {
					if ( isset( $value['pro_version'] ) && $value['pro_version'] == $key_plugin ) {
						$bws_plugins_pro[ $key_plugin ] = $key;
						unset( $all_plugins[ $key ] );
					}
				}
			}
		}

		if ( isset( $_GET['action'] ) && 'system_status' == $_GET['action'] ) {
			$all_plugins = get_plugins();
			$active_plugins = get_option( 'active_plugins' );
		    $mysql_info = $wpdb->get_results( "SHOW VARIABLES LIKE 'sql_mode'" );
		    if ( is_array( $mysql_info ) )
		    	$sql_mode = $mysql_info[0]->Value;
		    if ( empty( $sql_mode ) )
		    	$sql_mode = __( 'Not set', 'bestwebsoft' );

			$safe_mode = ( ini_get( 'safe_mode' ) ) ? __( 'On', 'bestwebsoft' ) : __( 'Off', 'bestwebsoft' );
			$allow_url_fopen = ( ini_get( 'allow_url_fopen' ) ) ? __( 'On', 'bestwebsoft' ) : __( 'Off', 'bestwebsoft' );
			$upload_max_filesize = ( ini_get( 'upload_max_filesize' ) )? ini_get( 'upload_max_filesize' ) : __( 'N/A', 'bestwebsoft' );
			$post_max_size = ( ini_get( 'post_max_size' ) ) ? ini_get( 'post_max_size' ) : __( 'N/A', 'bestwebsoft' );
			$max_execution_time = ( ini_get( 'max_execution_time' ) ) ? ini_get( 'max_execution_time' ) : __( 'N/A', 'bestwebsoft' );
			$memory_limit = ( ini_get( 'memory_limit' ) ) ? ini_get( 'memory_limit' ) : __( 'N/A', 'bestwebsoft' );
			$memory_usage = ( function_exists( 'memory_get_usage' ) ) ? round( memory_get_usage() / 1024 / 1024, 2 ) . __( ' Mb', 'bestwebsoft' ) : __( 'N/A', 'bestwebsoft' );
			$exif_read_data = ( is_callable( 'exif_read_data' ) ) ? __( 'Yes', 'bestwebsoft' ) . " ( V" . substr( phpversion( 'exif' ), 0,4 ) . ")" : __( 'No', 'bestwebsoft' );
			$iptcparse = ( is_callable( 'iptcparse' ) ) ? __( 'Yes', 'bestwebsoft' ) : __( 'No', 'bestwebsoft' );
			$xml_parser_create = ( is_callable( 'xml_parser_create' ) ) ? __( 'Yes', 'bestwebsoft' ) : __( 'No', 'bestwebsoft' );
			$theme = ( function_exists( 'wp_get_theme' ) ) ? wp_get_theme() : get_theme( get_current_theme() );

			if ( function_exists( 'is_multisite' ) ) {
				if ( is_multisite() )
					$multisite = __( 'Yes', 'bestwebsoft' );
				else
					$multisite = __( 'No', 'bestwebsoft' );
			} else
				$multisite = __( 'N/A', 'bestwebsoft' );

			$system_info = array(
				'system_info'		=> '',
				'active_plugins'	=> '',
				'inactive_plugins'	=> ''
			);
			$system_info['system_info'] = array(
		        __( 'Operating System', 'bestwebsoft' )				=> PHP_OS,
		        __( 'Server', 'bestwebsoft' )						=> $_SERVER["SERVER_SOFTWARE"],
		        __( 'Memory usage', 'bestwebsoft' )					=> $memory_usage,
		        __( 'MYSQL Version', 'bestwebsoft' )				=> $wpdb->get_var( "SELECT VERSION() AS version" ),
		        __( 'SQL Mode', 'bestwebsoft' )						=> $sql_mode,
		        __( 'PHP Version', 'bestwebsoft' )					=> PHP_VERSION,
		        __( 'PHP Safe Mode', 'bestwebsoft' )				=> $safe_mode,
		        __( 'PHP Allow URL fopen', 'bestwebsoft' )			=> $allow_url_fopen,
		        __( 'PHP Memory Limit', 'bestwebsoft' )				=> $memory_limit,
		        __( 'PHP Max Upload Size', 'bestwebsoft' )			=> $upload_max_filesize,
		        __( 'PHP Max Post Size', 'bestwebsoft' )			=> $post_max_size,
		        __( 'PHP Max Script Execute Time', 'bestwebsoft' )	=> $max_execution_time,
		        __( 'PHP Exif support', 'bestwebsoft' )				=> $exif_read_data,
		        __( 'PHP IPTC support', 'bestwebsoft' )				=> $iptcparse,
		        __( 'PHP XML support', 'bestwebsoft' )				=> $xml_parser_create,
				__( 'Site URL', 'bestwebsoft' )						=> get_option( 'siteurl' ),
				__( 'Home URL', 'bestwebsoft' )						=> home_url(),
				'$_SERVER[HTTP_HOST]'								=> $_SERVER['HTTP_HOST'],
				'$_SERVER[SERVER_NAME]'								=> $_SERVER['SERVER_NAME'],
				__( 'WordPress Version', 'bestwebsoft' )			=> $wp_version,
				__( 'WordPress DB Version', 'bestwebsoft' )			=> get_option( 'db_version' ),
				__( 'Multisite', 'bestwebsoft' )					=> $multisite,
				__( 'Active Theme', 'bestwebsoft' )					=> $theme['Name'] . ' ' . $theme['Version']
			);
			foreach ( $all_plugins as $path => $plugin ) {
				if ( is_plugin_active( $path ) )
					$system_info['active_plugins'][ $plugin['Name'] ] = $plugin['Version'];
				else
					$system_info['inactive_plugins'][ $plugin['Name'] ] = $plugin['Version'];
			}
		}

		if ( ( isset( $_REQUEST['bwsmn_form_submit'] ) && check_admin_referer( plugin_basename(__FILE__), 'bwsmn_nonce_submit' ) ) ||
			 ( isset( $_REQUEST['bwsmn_form_submit_custom_email'] ) && check_admin_referer( plugin_basename(__FILE__), 'bwsmn_nonce_submit_custom_email' ) ) ) {
			if ( isset( $_REQUEST['bwsmn_form_email'] ) ) {
				$bwsmn_form_email = esc_html( trim( $_REQUEST['bwsmn_form_email'] ) );
				if ( $bwsmn_form_email == "" || ! is_email( $bwsmn_form_email ) ) {
					$error = __( "Please enter a valid email address.", 'bestwebsoft' );
				} else {
					$email = $bwsmn_form_email;
					$bwsmn_form_email = '';
					$message = __( 'Email with system info is sent to ', 'bestwebsoft' ) . $email;
				}
			} else {
				$email = 'plugin_system_status@bestwebsoft.com';
				$message = __( 'Thank you for contacting us.', 'bestwebsoft' );
			}

			if ( $error == '' ) {
				$headers  = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\n";
				$headers .= 'From: ' . get_option( 'admin_email' );
				$message_text = '<html><head><title>System Info From ' . home_url() . '</title></head><body>
				<h4>Environment</h4>
				<table>';
				foreach ( $system_info['system_info'] as $key => $value ) {
					$message_text .= '<tr><td>'. $key .'</td><td>'. $value .'</td></tr>';
				}
				$message_text .= '</table>';
				if ( ! empty( $system_info['active_plugins'] ) ) {
					$message_text .= '<h4>Active Plugins</h4>
					<table>';
					foreach ( $system_info['active_plugins'] as $key => $value ) {
						$message_text .= '<tr><td scope="row">'. $key .'</td><td scope="row">'. $value .'</td></tr>';
					}
					$message_text .= '</table>';
				}
				if ( ! empty( $system_info['inactive_plugins'] ) ) {
					$message_text .= '<h4>Inactive Plugins</h4>
					<table>';
					foreach ( $system_info['inactive_plugins'] as $key => $value ) {
						$message_text .= '<tr><td scope="row">'. $key .'</td><td scope="row">'. $value .'</td></tr>';
					}
					$message_text .= '</table>';
				}
				$message_text .= '</body></html>';
				$result = wp_mail( $email, 'System Info From ' . home_url(), $message_text, $headers );
				if ( $result != true )
					$error = __( "Sorry, email message could not be delivered.", 'bestwebsoft' );
			}
		} ?>
		<div class="wrap">
			<div class="icon32 icon32-bws" id="icon-options-general"></div>
			<h2>
				<span class="bws_main title">BestWebSoft</span>
				<ul class="subsubsub bws_title_menu">
					<li><a href="<?php echo esc_url( 'http://support.bestwebsoft.com/home' ); ?>" target="_blank"><?php _e( 'Need help?', 'bestwebsoft' ); ?></a></li> |
					<li><a href="<?php echo esc_url( 'http://bestwebsoft.com/wp-login.php' ); ?>" target="_blank"><?php _e( 'Client area', 'bestwebsoft' ); ?></a></li>
					<li><a class="bws_system_status <?php if ( isset( $_GET['action'] ) && 'system_status' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=bws_plugins&amp;action=system_status"><?php _e( 'System status', 'bestwebsoft' ); ?></a></li>
				</ul>
				<div class="clear"></div>
			</h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( !isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=bws_plugins"><?php _e( 'Plugins', 'bestwebsoft' ); ?></a>
				<?php if ( $wp_version >= '3.4' ) { ?>
					<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'themes' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=bws_plugins&amp;action=themes"><?php _e( 'Themes', 'bestwebsoft' ); ?></a>
				<?php } ?>
			</h2>
			<?php if ( ! isset( $_GET['action'] ) ) { ?>
				<ul class="subsubsub">
					<li><a <?php if ( !isset( $_GET['sub'] ) ) echo 'class="current" '; ?>href="admin.php?page=bws_plugins"><?php _e( 'All', 'bestwebsoft' ); ?></a></li> |
					<li><a <?php if ( isset( $_GET['sub'] ) && 'installed' == $_GET['sub'] ) echo 'class="current" '; ?>href="admin.php?page=bws_plugins&amp;sub=installed"><?php _e( 'Installed', 'bestwebsoft' ); ?></a></li> |
					<li><a <?php if ( isset( $_GET['sub'] ) && 'recommended' == $_GET['sub'] ) echo 'class="current" '; ?>href="admin.php?page=bws_plugins&amp;sub=recommended"><?php _e( 'Recommended', 'bestwebsoft' ); ?></a></li>
				</ul>
				<div class="clear"></div>
				<?php if ( ( isset( $_GET['sub'] ) && 'installed' == $_GET['sub'] ) || !isset( $_GET['sub'] ) ) { ?>
					<h4 class="bws_installed"><?php _e( 'Installed plugins', 'bestwebsoft' ); ?></h4>
					<?php foreach ( $all_plugins as $key_plugin => $value_plugin ) {
						if ( isset( $bws_plugins_pro[ $key_plugin ] ) )
							$key_plugin = $bws_plugins_pro[ $key_plugin ];

						if ( isset( $bws_plugins[ $key_plugin ] ) ) {
							$key_plugin_explode = explode( '-plugin/', $key_plugin );
							if ( isset( $key_plugin_explode[1] ) )
								$icon = $key_plugin_explode[0];
							else {
								$key_plugin_explode = explode( '/', $key_plugin );
								$icon = $key_plugin_explode[0];
							}
						}

						if ( isset( $bws_plugins[ $key_plugin ]['pro_version'] ) && ( in_array( $bws_plugins[ $key_plugin ]['pro_version'], $active_plugins ) || is_plugin_active_for_network( $bws_plugins[ $key_plugin ]['pro_version'] ) ) ) { ?>
							<div class="bws_product_box bws_exist_overlay">
								<div class="bws_product">
									<div class="bws_product_title"><?php echo $value_plugin["Name"]; ?></div>
									<div class="bws_product_content">
										<div class="bws_product_icon">
											<div class="bws_product_icon_pro">PRO</div>
											<img src="<?php echo plugins_url( "icons/" , __FILE__ ) . $icon . '.png'; ?>"/>
										</div>
										<div class="bws_product_description"><?php echo $value_plugin["Description"]; ?></div>
									</div>
									<div class="clear"></div>
								</div>
								<div class="bws_product_links">
									<a href="<?php echo $bws_plugins[ $key_plugin ]["link"]; ?>" target="_blank"><?php _e( "Learn more", 'bestwebsoft' ); ?></a>
									<?php if ( '' != $bws_plugins[ $key_plugin ]["pro_settings"] ) { ?>
										<span> | </span>
										<a href="<?php echo $bws_plugins[ $key_plugin ]["pro_settings"]; ?>" target="_blank"><?php _e( "Settings", 'bestwebsoft' ); ?></a>
									<?php } ?>
								</div>
							</div>
						<?php } elseif ( isset( $bws_plugins[ $key_plugin ] ) && ( in_array( $key_plugin, $active_plugins ) || is_plugin_active_for_network( $key_plugin ) ) ) {
							if ( isset( $bws_plugins[ $key_plugin ]['pro_version'] ) && isset( $all_plugins[ $bws_plugins[ $key_plugin ]['pro_version'] ] ) ) { ?>
								<div class="bws_product_box bws_product_deactivated">
									<div class="bws_product">
										<div class="bws_product_title"><?php echo $value_plugin["Name"]; ?></div>
										<div class="bws_product_content">
											<div class="bws_product_icon">
												<div class="bws_product_icon_pro">PRO</div>
												<img src="<?php echo plugins_url( "icons/" , __FILE__ ) . $icon . '.png'; ?>"/>
											</div>
											<div class="bws_product_description"><?php echo $bws_plugins[ $key_plugin ]["description"]; ?></div>
										</div>
										<div class="clear"></div>
									</div>
									<div class="bws_product_links">
										<a href="<?php echo $bws_plugins[ $key_plugin ]["link"]; ?>" target="_blank"><?php _e( "Learn more", 'bestwebsoft' ); ?></a>
										<span> | </span>
										<a class="bws_activate" href="plugins.php" title="<?php _e( "Activate this plugin", 'bestwebsoft' ); ?>" target="_blank"><?php _e( "Activate", 'bestwebsoft' ); ?></a>
									</div>
								</div>
							<?php } else { ?>
								<div class="bws_product_box bws_product_free">
									<div class="bws_product">
										<div class="bws_product_title"><?php echo $value_plugin["Name"]; ?></div>
										<div class="bws_product_content">
											<div class="bws_product_icon">
												<img src="<?php echo plugins_url( "icons/" , __FILE__ ) . $icon . '.png'; ?>"/>
											</div>
											<div class="bws_product_description"><?php echo $bws_plugins[ $key_plugin ]["description"]; ?></div>
										</div>
										<?php if ( isset( $bws_plugins[ $key_plugin ]["purchase"] ) ) { ?>
											<a class="bws_product_button" href="<?php echo $bws_plugins[ $key_plugin ]["purchase"]; ?>" target="_blank">
												<?php _e( 'Go', 'bestwebsoft' );?> <strong>PRO</strong>
											</a>
										<?php } else { ?>
											<a class="bws_product_button bws_donate_button" href="<?php echo $bws_donate_link; ?>" target="_blank">
												<strong><?php _e( 'DONATE', 'bestwebsoft' );?></strong>
											</a>
										<?php } ?>
										<div class="clear"></div>
									</div>
									<div class="bws_product_links">
										<a href="<?php echo $bws_plugins[ $key_plugin ]["link"]; ?>" target="_blank"><?php _e( "Learn more", 'bestwebsoft' ); ?></a>
										<?php if ( '' != $bws_plugins[ $key_plugin ]["settings"] ) { ?>
											<span> | </span>
											<a href="<?php echo $bws_plugins[ $key_plugin ]["settings"]; ?>" target="_blank"><?php _e( "Settings", 'bestwebsoft' ); ?></a>
										<?php } ?>
									</div>
								</div>
							<?php }
						} elseif ( isset( $bws_plugins[ $key_plugin ] ) ) { ?>
							<div class="bws_product_box bws_product_deactivated bws_product_free">
								<div class="bws_product">
									<div class="bws_product_title"><?php echo $value_plugin["Name"]; ?></div>
									<div class="bws_product_content">
										<div class="bws_product_icon">
											<img src="<?php echo plugins_url( "icons/" , __FILE__ ) . $icon . '.png'; ?>"/>
										</div>
										<div class="bws_product_description"><?php echo $bws_plugins[ $key_plugin ]["description"]; ?></div>
									</div>
									<?php if ( isset( $bws_plugins[ $key_plugin ]["purchase"] ) ) { ?>
										<a class="bws_product_button" href="<?php echo $bws_plugins[ $key_plugin ]["purchase"]; ?>" target="_blank">
											<?php _e( 'Go', 'bestwebsoft' );?> <strong>PRO</strong>
										</a>
									<?php } else { ?>
										<a class="bws_product_button bws_donate_button" href="<?php echo $bws_donate_link; ?>" target="_blank">
											<strong><?php _e( 'DONATE', 'bestwebsoft' );?></strong>
										</a>
									<?php } ?>
									<div class="clear"></div>
								</div>
								<div class="bws_product_links">
									<a href="<?php echo $bws_plugins[ $key_plugin ]["link"]; ?>" target="_blank"><?php _e( "Learn more", 'bestwebsoft' ); ?></a>
									<span> | </span>
									<a class="bws_activate" href="plugins.php" title="<?php _e( "Activate this plugin", 'bestwebsoft' ); ?>" target="_blank"><?php _e( "Activate", 'bestwebsoft' ); ?></a>
								</div>
							</div>
						<?php }
					}
				} ?>
				<div class="clear"></div>
				<?php if ( ( isset( $_GET['sub'] ) && 'recommended' == $_GET['sub'] ) || !isset( $_GET['sub'] ) ) { ?>
					<h4 class="bws_recommended"><?php _e( 'Recommended plugins', 'bestwebsoft' ); ?></h4>
					<?php foreach ( $recommend_plugins as $key_plugin => $value_plugin ) {

						if ( isset( $bws_plugins[ $key_plugin ] ) ) {
							$key_plugin_explode = explode( '-plugin/', $key_plugin );
							if ( isset( $key_plugin_explode[1] ) )
								$icon = $key_plugin_explode[0];
							else {
								$key_plugin_explode = explode( '/', $key_plugin );
								$icon = $key_plugin_explode[0];
							}
						} ?>
						<div class="bws_product_box">
							<div class="bws_product">
								<div class="bws_product_title"><?php echo $value_plugin["name"]; ?></div>
								<div class="bws_product_content">
									<div class="bws_product_icon">
										<?php if ( isset( $bws_plugins[ $key_plugin ]['pro_version'] ) ) { ?>
											<div class="bws_product_icon_pro">PRO</div>
										<?php } ?>
										<img src="<?php echo plugins_url( "icons/" , __FILE__ ) . $icon . '.png'; ?>"/>
									</div>
									<div class="bws_product_description"><?php echo $bws_plugins[ $key_plugin ]["description"]; ?></div>
								</div>
								<?php if ( isset( $bws_plugins[ $key_plugin ]["pro_version"] ) ) { ?>
									<a class="bws_product_button" href="<?php echo $bws_plugins[ $key_plugin ]["purchase"]; ?>" target="_blank">
										<?php _e( 'Go', 'bestwebsoft' ); ?> <strong>PRO</strong>
									</a>
								<?php } else { ?>
									<a class="bws_product_button bws_donate_button" href="<?php echo $bws_donate_link; ?>" target="_blank">
										<strong><?php _e( 'DONATE', 'bestwebsoft' ); ?></strong>
									</a>
								<?php } ?>
							</div>
							<div class="clear"></div>
							<div class="bws_product_links">
								<a href="<?php echo $bws_plugins[ $key_plugin ]["link"]; ?>" target="_blank"><?php _e( "Learn more", 'bestwebsoft' ); ?></a>
								<span> | </span>
								<a href="<?php echo $bws_plugins[ $key_plugin ]["wp_install"]; ?>" target="_blank"><?php _e( "Install now", 'bestwebsoft' ); ?></a>
							</div>
						</div>
					<?php }
				} ?>
			<?php } elseif ( 'themes' == $_GET['action'] ) { ?>
				<div id="availablethemes">
					<?php global $tabs, $tab, $paged, $type, $theme_field_defaults;
					include( ABSPATH . 'wp-admin/includes/theme-install.php' );
					include( ABSPATH . 'wp-admin/includes/class-wp-themes-list-table.php' );
					include( ABSPATH . 'wp-admin/includes/class-wp-theme-install-list-table.php' );

					$theme_class = new WP_Theme_Install_List_Table();
					$paged = $theme_class->get_pagenum();
					$per_page = 36;
					$args = array( 'page' => $paged, 'per_page' => $per_page, 'fields' => $theme_field_defaults );
					$args['author'] = 'bestwebsoft';
					$args = apply_filters( 'install_themes_table_api_args_search', $args );
					$api = themes_api( 'query_themes', $args );

					if ( is_wp_error( $api ) )
						wp_die( $api->get_error_message() . '</p> <p><a href="#" onclick="document.location.reload(); return false;">' . __( 'Try again', 'bestwebsoft' ) . '</a>' );

					$theme_class->items = $api->themes;
					$theme_class->set_pagination_args( array(
						'total_items' => $api->info['results'],
						'per_page' => $per_page,
						'infinite_scroll' => true,
					) );
					$themes = $theme_class->items;
					if ( $wp_version < '3.9' ) {
						foreach ( $themes as $theme ) { ?>
							<div class="available-theme installable-theme"><?php
								global $themes_allowedtags;
								if ( empty( $theme ) )
									return;

								$name   = wp_kses( $theme->name,   $themes_allowedtags );
								$author = wp_kses( $theme->author, $themes_allowedtags );
								$preview_title = sprintf( __( 'Preview &#8220;%s&#8221;', 'bestwebsoft' ), $name );
								$preview_url   = add_query_arg( array(
									'tab'   => 'theme-information',
									'theme' => $theme->slug,
								), self_admin_url( 'theme-install.php' ) );

								$actions = array();

								$install_url = add_query_arg( array(
									'action' => 'install-theme',
									'theme'  => $theme->slug,
								), self_admin_url( 'update.php' ) );

								$update_url = add_query_arg( array(
									'action' => 'upgrade-theme',
									'theme'  => $theme->slug,
								), self_admin_url( 'update.php' ) );

								$status = 'install';
								$installed_theme = wp_get_theme( $theme->slug );
								if ( $installed_theme->exists() ) {
									if ( version_compare( $installed_theme->get('Version'), $theme->version, '=' ) )
										$status = 'latest_installed';
									elseif ( version_compare( $installed_theme->get('Version'), $theme->version, '>' ) )
										$status = 'newer_installed';
									else
										$status = 'update_available';
								}
								switch ( $status ) {
									default:
									case 'install':
										$actions[] = '<a class="install-now" href="' . esc_url( wp_nonce_url( $install_url, 'install-theme_' . $theme->slug ) ) . '" title="' . esc_attr( sprintf( __( 'Install %s', 'bestwebsoft' ), $name ) ) . '">' . __( 'Install Now', 'bestwebsoft' ) . '</a>';
										break;
									case 'update_available':
										$actions[] = '<a class="install-now" href="' . esc_url( wp_nonce_url( $update_url, 'upgrade-theme_' . $theme->slug ) ) . '" title="' . esc_attr( sprintf( __( 'Update to version %s', 'bestwebsoft' ), $theme->version ) ) . '">' . __( 'Update', 'bestwebsoft' ) . '</a>';
										break;
									case 'newer_installed':
									case 'latest_installed':
										$actions[] = '<span class="install-now" title="' . esc_attr__( 'This theme is already installed and is up to date' ) . '">' . _x( 'Installed', 'theme', 'bestwebsoft' ) . '</span>';
										break;
								}
								$actions[] = '<a class="install-theme-preview" href="' . esc_url( $preview_url ) . '" title="' . esc_attr( sprintf( __( 'Preview %s', 'bestwebsoft' ), $name ) ) . '">' . __( 'Preview', 'bestwebsoft' ) . '</a>';
								$actions = apply_filters( 'theme_install_actions', $actions, $theme ); ?>
								<a class="screenshot install-theme-preview" href="<?php echo esc_url( $preview_url ); ?>" title="<?php echo esc_attr( $preview_title ); ?>">
									<img src='<?php echo esc_url( $theme->screenshot_url ); ?>' width='150' />
								</a>
								<h3><?php echo $name; ?></h3>
								<div class="theme-author"><?php printf( __( 'By %s', 'bestwebsoft' ), $author ); ?></div>
								<div class="action-links">
									<ul>
										<?php foreach ( $actions as $action ): ?>
											<li><?php echo $action; ?></li>
										<?php endforeach; ?>
										<li class="hide-if-no-js"><a href="#" class="theme-detail"><?php _e( 'Details', 'bestwebsoft' ) ?></a></li>
									</ul>
								</div>
								<?php $theme_class->install_theme_info( $theme ); ?>
							</div>
						<?php }
						// end foreach $theme_names
						$theme_class->theme_installer();
					} else { ?>
						<div class="theme-browser">
							<div class="themes">
						<?php foreach ( $themes as $key => $theme ) {
							$installed_theme = wp_get_theme( $theme->slug );
							if ( $installed_theme->exists() )
								$theme->installed = true;
							else
								$theme->installed = false;
							?>
							<div class="theme" tabindex="0">
								<?php if ( $theme->screenshot_url ) { ?>
									<div class="theme-screenshot">
										<img src="<?php echo $theme->screenshot_url; ?>" alt="" />
									</div>
								<?php } else { ?>
									<div class="theme-screenshot blank"></div>
								<?php } ?>
								<div class="theme-author"><?php printf( __( 'By %s', 'bestwebsoft' ), $theme->author ); ?></div>
								<h3 class="theme-name"><?php echo $theme->name; ?></h3>
								<div class="theme-actions">
									<a class="button button-secondary preview install-theme-preview" href="theme-install.php?theme=<?php echo $theme->slug ?>"><?php esc_html_e( 'Learn More', 'bestwebsoft' ); ?></a>
								</div>
								<?php if ( $theme->installed ) { ?>
									<div class="theme-installed"><?php _e( 'Already Installed', 'bestwebsoft' ); ?></div>
								<?php } ?>
							</div>
						<?php } ?>
							<br class="clear" />
							</div>
						</div>
						<div class="theme-overlay"></div>
					<?php } ?>
				</div>
			<?php } elseif ( 'system_status' == $_GET['action'] ) {	?>
				<div class="updated fade" <?php if ( ! ( isset( $_REQUEST['bwsmn_form_submit'] ) || isset( $_REQUEST['bwsmn_form_submit_custom_email'] ) ) || $error != "" ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
				<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
				<h3><?php _e( 'System status', 'bestwebsoft' ); ?></h3>
				<div class="inside">
					<table class="bws_system_info">
						<thead><tr><th><?php _e( 'Environment', 'bestwebsoft' ); ?></th><td></td></tr></thead>
						<tbody>
						<?php foreach ( $system_info['system_info'] as $key => $value ) { ?>
							<tr>
								<td scope="row"><?php echo $key; ?></td>
								<td scope="row"><?php echo $value; ?></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					<table class="bws_system_info">
						<thead><tr><th><?php _e( 'Active Plugins', 'bestwebsoft' ); ?></th><th></th></tr></thead>
						<tbody>
						<?php if ( ! empty( $system_info['active_plugins'] ) ) {
							foreach ( $system_info['active_plugins'] as $key => $value ) { ?>
								<tr>
									<td scope="row"><?php echo $key; ?></td>
									<td scope="row"><?php echo $value; ?></td>
								</tr>
							<?php }
						} ?>
						</tbody>
					</table>
					<table class="bws_system_info">
						<thead><tr><th><?php _e( 'Inactive Plugins', 'bestwebsoft' ); ?></th><th></th></tr></thead>
						<tbody>
						<?php if ( ! empty( $system_info['inactive_plugins'] ) ) {
							foreach ( $system_info['inactive_plugins'] as $key => $value ) { ?>
								<tr>
									<td scope="row"><?php echo $key; ?></td>
									<td scope="row"><?php echo $value; ?></td>
								</tr>
							<?php }
						} ?>
						</tbody>
					</table>
					<div class="clear"></div>
					<form method="post" action="admin.php?page=bws_plugins&amp;action=system_status">
						<p>
							<input type="hidden" name="bwsmn_form_submit" value="submit" />
							<input type="submit" class="button-primary" value="<?php _e( 'Send to support', 'bestwebsoft' ) ?>" />
							<?php wp_nonce_field( plugin_basename(__FILE__), 'bwsmn_nonce_submit' ); ?>
						</p>
					</form>
					<form method="post" action="admin.php?page=bws_plugins&amp;action=system_status">
						<p>
							<input type="hidden" name="bwsmn_form_submit_custom_email" value="submit" />
							<input type="submit" class="button" value="<?php _e( 'Send to custom email &#187;', 'bestwebsoft' ) ?>" />
							<input type="text" maxlength="250" value="<?php echo $bwsmn_form_email; ?>" name="bwsmn_form_email" />
							<?php wp_nonce_field( plugin_basename(__FILE__), 'bwsmn_nonce_submit_custom_email' ); ?>
						</p>
					</form>
				</div>
			<?php } ?>
		</div>
	<?php }
}

if ( ! function_exists( 'bws_get_banner_array' ) ) {
	function bws_get_banner_array() {
		global $bstwbsftwppdtplgns_banner_array;
		$bstwbsftwppdtplgns_banner_array = array(
			array( 'gglcptch_hide_banner_on_plugin_page', 'google-captcha/google-captcha.php', '1.18' ),
			array( 'mltlngg_hide_banner_on_plugin_page', 'multilanguage/multilanguage.php', '1.1.1' ),
			array( 'adsns_hide_banner_on_plugin_page', 'adsense-plugin/adsense-plugin.php', '1.36' ),
			array( 'vstrsnln_hide_banner_on_plugin_page', 'visitors-online/visitors-online.php', '0.2' ),			
			array( 'cstmsrch_hide_banner_on_plugin_page', 'custom-search-plugin/custom-search-plugin.php', '1.28' ),
			array( 'prtfl_hide_banner_on_plugin_page', 'portfolio/portfolio.php', '2.33' ),
			array( 'rlt_hide_banner_on_plugin_page', 'realty/realty.php', '1.0.0' ),
			array( 'prmbr_hide_banner_on_plugin_page', 'promobar/promobar.php', '1.0.0' ),
			array( 'gglnltcs_hide_banner_on_plugin_page', 'bws-google-analytics/bws-google-analytics.php', '1.6.2' ),
			array( 'htccss_hide_banner_on_plugin_page', 'htaccess/htaccess.php', '1.6.3' ),
			array( 'sbscrbr_hide_banner_on_plugin_page', 'subscriber/subscriber.php', '1.1.8' ),
			array( 'lmtttmpts_hide_banner_on_plugin_page', 'limit-attempts/limit-attempts.php', '1.0.2' ),
			array( 'sndr_hide_banner_on_plugin_page', 'sender/sender.php', '0.5' ),
			array( 'srrl_hide_banner_on_plugin_page', 'user-role/user-role.php', '1.4' ),
			array( 'pdtr_hide_banner_on_plugin_page', 'updater/updater.php', '1.12' ),
			array( 'cntctfrmtdb_hide_banner_on_plugin_page', 'contact-form-to-db/contact_form_to_db.php', '1.2' ),
			array( 'cntctfrmmlt_hide_banner_on_plugin_page', 'contact-form-multi/contact-form-multi.php', '1.0.7' ),
			array( 'gglmps_hide_banner_on_plugin_page', 'bws-google-maps/bws-google-maps.php', '1.2' ),
			array( 'fcbkbttn_hide_banner_on_plugin_page', 'facebook-button-plugin/facebook-button-plugin.php', '2.29' ),
			array( 'twttr_hide_banner_on_plugin_page', 'twitter-plugin/twitter.php', '2.34' ),
			array( 'pdfprnt_hide_banner_on_plugin_page', 'pdf-print/pdf-print.php', '1.7.1' ),
			array( 'gglplsn_hide_banner_on_plugin_page', 'google-one/google-plus-one.php', '1.1.4' ),
			array( 'gglstmp_hide_banner_on_plugin_page', 'google-sitemap-plugin/google-sitemap-plugin.php', '2.8.4' ),
			array( 'cntctfrmpr_for_ctfrmtdb_hide_banner_on_plugin_page', 'contact-form-pro/contact_form_pro.php', '1.14' ),
			array( 'cntctfrm_hide_banner_on_plugin_page', 'contact-form-plugin/contact_form.php', '3.47' ),
			array( 'cptch_hide_banner_on_plugin_page', 'captcha/captcha.php', '3.8.4' ),
			array( 'gllr_hide_banner_on_plugin_page', 'gallery-plugin/gallery-plugin.php', '3.9.1' ),
			array( 'cntctfrm_for_ctfrmtdb_hide_banner_on_plugin_page', 'contact-form-plugin/contact_form.php', '3.62' )
		);
	}
}