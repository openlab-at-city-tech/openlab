<?php
/*
* BestWebSoft product list
*/

$bws_plugins_category = array(
	'advertisement' 	=> array( 'name' => __( 'Advertisement', 'bestwebsoft' ) ),
	'content-and-media' => array( 'name' => __( 'Content & Media', 'bestwebsoft' ) ),
	'management' 		=> array( 'name' => __( 'Management', 'bestwebsoft' ) ),
	'recommended'		=> array( 'name' => __( 'Recommended', 'bestwebsoft' ) ),
	'security'			=> array( 'name' => __( 'Security', 'bestwebsoft' ) ),
	'site-stats'		=> array( 'name' => __( 'Site Stats', 'bestwebsoft' ) ),
	'social'			=> array( 'name' => __( 'Social', 'bestwebsoft' ) ),
	'utilities'			=> array( 'name' => __( 'Utilities', 'bestwebsoft' ) ),
	'other'				=> array( 'name' => __( 'Other', 'bestwebsoft' ) ),
);

$bws_plugins = array(
	'captcha/captcha.php' => array(
		'category'		=> array( 'security', 'recommended' ),
		'name'			=> 'Captcha',
		'description'	=> 'Protect WordPress website forms from spam entries by means of math logic.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/captcha/?k=d678516c0990e781edfb6a6c874f0b8a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=captcha.php',
		'pro_version'	=> 'captcha-pro/captcha_pro.php',
		'purchase'		=> 'https://bestwebsoft.com/products/wordpress/plugins/captcha/buy/?k=ff7d65e55e5e7f98f219be9ed711094e&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=captcha_pro.php'
	),
	'car-rental/car-rental.php' => array(
		'category'		=> array( 'content-and-media' ),
		'name'			=> 'Car Rental',
		'description'	=> 'Create your personal car rental/booking and reservation website.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/car-rental/?k=444cac6df9a0d3a9763ab4753d24941b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=car-rental-settings',
		'pro_version'	=> 'car-rental-pro/car-rental-pro.php',
		'purchase'		=> 'https://bestwebsoft.com/products/wordpress/plugins/car-rental/buy/?k=7643d4f0698252fa1159de078d22269c&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=car-rental-pro-settings'
	),
	'contact-form-plugin/contact_form.php' => array(
		'category'		=> array( 'other', 'recommended' ),
		'name'			=> 'Contact Form',
		'description'	=> 'Allow customers to reach you using secure contact form plugin any website must have.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/contact-form/?k=012327ef413e5b527883e031d43b088b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=contact_form.php',
		'pro_version'	=> 'contact-form-pro/contact_form_pro.php',
		'purchase'		=> 'https://bestwebsoft.com/products/wordpress/plugins/contact-form/buy/?k=773dc97bb3551975db0e32edca1a6d71&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=contact_form_pro.php'
	),
	'contact-form-multi/contact-form-multi.php' => array(
		'category'		=> array( 'other', 'recommended' ),
		'name'			=> 'Contact Form Multi',
		'description'	=> 'Add unlimited number of contact forms to WordPress website.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/contact-form-multi/?k=83cdd9e72a9f4061122ad28a67293c72&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> '',
		'pro_version'	=> 'contact-form-multi-pro/contact-form-multi-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/contact-form-multi/buy/?k=fde3a18581c143654f060c398b07e8ac&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 	=> ''
	),
	'contact-form-to-db/contact_form_to_db.php' => array(
		'category'		=> array( 'other', 'recommended' ),
		'name'			=> 'Contact Form to DB',
		'description'	=> 'Save and manage contact form messages. Never lose important data.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/contact-form-to-db/?k=ba3747d317c2692e4136ca096a8989d6&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=cntctfrmtdb_settings',
		'pro_version'	=> 'contact-form-to-db-pro/contact_form_to_db_pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/contact-form-to-db/buy/?k=6ce5f4a9006ec906e4db643669246c6a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 	=> 'admin.php?page=cntctfrmtdbpr_settings'
	),
	'custom-admin-page/custom-admin-page.php' => array(
		'category'		=> array( 'utilities' ),
		'name'			=> 'Custom Admin Page',
		'description'	=> 'Add unlimited custom pages to WordPress admin dashboard.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/custom-admin-page/?k=9ac03f16c25e845e8e055a221c3e1467&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=custom-admin-page.php'
	),
	'custom-fields-search/custom-fields-search.php' => array(
		'category'		=> array( 'content-and-media' ),
		'name'			=> 'Custom Fields Search',
		'description'	=> 'Add custom fields to WordPress website search results.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/custom-fields-search/?k=f3f8285bb069250c42c6ffac95ed3284&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=custom_fields_search.php'
	),
	'custom-search-plugin/custom-search-plugin.php' => array(
		'category'		=> array( 'content-and-media' ),
		'name'			=> 'Custom Search',
		'description'	=> 'Add custom post types to WordPress website search results.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/custom-search/?k=933be8f3a8b8719d95d1079d15443e29&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=custom_search.php',
		'pro_version'	=> 'custom-search-pro/custom-search-pro.php',
		'purchase'		=> 'https://bestwebsoft.com/products/wordpress/plugins/custom-search/buy/?k=062b652ac6ac8ba863c9f30fc21d62c6&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=custom_search_pro.php'
	),
	'db-manager/db-manager.php' => array(
		'category'		=> array( 'utilities' ),
		'name'			=> 'DB Manager',
		'description'	=> 'Manage database and file system right from your WordPress admin dashboard.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/db-manager/?k=01ed9731780d87f85f5901064b7d76d8&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'install_url'	=> 'https://bestwebsoft.com/products/wordpress/plugins/db-manager/download/?k=01ed9731780d87f85f5901064b7d76d8&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=db-manager.php',
		'icon'			=> bws_menu_url( 'icons/plugins/' ) . 'db-manager.png'
	),
	'donate-button/donate.php' => array(
		'category'		=> array( 'other' ),
		'name'			=> 'Donate',
		'description'	=> 'Add PayPal and 2CO donate buttons to receive charity payments.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/donate/?k=a8b2e2a56914fb1765dd20297c26401b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=donate.php'
	),
	'email-queue/email-queue.php' => array(
		'category'		=> array( 'other' ),
		'name'			=> 'Email Queue',
		'description'	=> 'Automatically add outgoing email messages to queue instead of sending them immediately.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/email-queue/?k=e345e1b6623f0dca119bc2d9433b130b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=mlq_settings'
	),
	'error-log-viewer/error-log-viewer.php' => array(
		'category'		=> array( 'utilities' ),
		'name'			=> 'Error Log Viewer',
		'description'	=> 'Get latest error log messages to diagnose website problems. Define and fix issues faster.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/error-log-viewer/?k=da0de8bd2c7a0b2fea5df64d55a368b3&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=rrrlgvwr.php&tab=settings'
	),
	'facebook-button-plugin/facebook-button-plugin.php' => array(
		'category'		=> array( 'social' ),
		'name'			=> 'Facebook Button',
		'description'	=> 'Add Facebook Like, Share and Profile buttons to WordPress posts, pages and widgets.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/facebook-like-button/?k=05ec4f12327f55848335802581467d55&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=facebook-button-plugin.php',
		'pro_version'	=> 'facebook-button-pro/facebook-button-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/facebook-like-button/buy/?k=8da168e60a831cfb3525417c333ad275&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 	=> 'admin.php?page=facebook-button-pro.php'
	),
	'bws-featured-posts/bws-featured-posts.php' => array(
		'category'		=> array( 'content-and-media' ),
		'name'			=> 'Featured Posts',
		'description'	=> 'Add featured posts to WordPress posts or widgets. Highlight important information.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/featured-posts/?k=f0afb31185ba7c7d6d598528d69f6d97&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=featured-posts.php'
	),
	'gallery-plugin/gallery-plugin.php' => array(
		'category'		=> array( 'content-and-media', 'recommended' ),
		'name'			=> 'Gallery',
		'description'	=> 'Add beautiful galleries, albums & images to your Wordpress website in few clicks.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/gallery/?k=2da21c0a64eec7ebf16337fa134c5f78&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=gallery-plugin.php',
		'pro_version'	=> 'gallery-plugin-pro/gallery-plugin-pro.php',
		'purchase'		=> 'https://bestwebsoft.com/products/wordpress/plugins/gallery/buy/?k=382e5ce7c96a6391f5ffa5e116b37fe0&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=gallery-plugin-pro.php'
	),
	'gallery-categories/gallery-categories.php' => array(
		'category'		=> array( 'content-and-media' ),
		'name'			=> 'Gallery Categories',
		'description'	=> 'Add unlimited gallery categories. Organize images to make the navigation through content easier.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/gallery-categories/?k=7d68c7bfec2486dc350c67fff57ad433&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> ''
	),
	'google-one/google-plus-one.php' => array(
		'category'		=> array( 'social' ),
		'name'			=> 'Google +1',
		'description'	=> 'Add Google +1 Share, Follow, Hangout buttons and profile badge to WordPress posts, pages and widgets.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/google-plus-one/?k=ce7a88837f0a857b3a2bb142f470853c&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=google-plus-one.php',
		'pro_version'	=> 'google-one-pro/google-plus-one-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/google-plus-one/buy/?k=f4b0a62d155c9df9601a0531ad5bd832&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 	=> 'admin.php?page=google-plus-one-pro.php'
	),
	'adsense-plugin/adsense-plugin.php' => array(
		'category'		=> array( 'advertisement' ),
		'name'			=> 'Google AdSense',
		'description'	=> 'Add Adsense ads to pages, posts, custom posts, search results, categories, tags, pages, and widgets.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/google-adsense/?k=60e3979921e354feb0347e88e7d7b73d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=adsense-plugin.php',
		'pro_version'	=> 'adsense-pro/adsense-pro.php',
		'purchase'		=> 'https://bestwebsoft.com/products/wordpress/plugins/google-adsense/buy/?k=c23889b293d62aa1ad2c96513405f0e1&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=adsense-pro.php'
	),
	'bws-google-analytics/bws-google-analytics.php' => array(
		'category'		=> array( 'site-stats' ),
		'name'			=> 'Google Analytics',
		'description'	=> 'Add Google Analytics code to WordPress website and track basic stats.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/bws-google-analytics/?k=261c74cad753fb279cdf5a5db63fbd43&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=bws-google-analytics.php',
		'pro_version'	=> 'bws-google-analytics-pro/bws-google-analytics-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/bws-google-analytics/buy/?k=83796e84fec3f70ecfcc8894a73a6c4a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 	=> 'admin.php?page=bws-google-analytics-pro.php'
	),
	'google-captcha/google-captcha.php' => array(
		'category'		=> array( 'security', 'recommended' ),
		'name'			=> 'Google Captcha (reCAPTCHA)',
		'description'	=> 'Protect WordPress website forms from spam entries with Google Captcha (reCaptcha).',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/google-captcha/?k=7b59fbe542acf950b29f3e020d5ad735&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=google-captcha.php',
		'pro_version'	=> 'google-captcha-pro/google-captcha-pro.php',
		'purchase'		=> 'https://bestwebsoft.com/products/wordpress/plugins/google-captcha/buy/?k=773d30149acf1edc32e5c0766b96c134&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=google-captcha-pro.php'
	),
	'bws-google-maps/bws-google-maps.php' => array(
		'category'		=> array( 'content-and-media' ),
		'name'			=> 'Google Maps',
		'description'	=> 'Add customized Google maps to WordPress posts, pages and widgets.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/bws-google-maps/?k=d8fac412d7359ebaa4ff53b46572f9f7&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=bws-google-maps.php',
		'pro_version'	=> 'bws-google-maps-pro/bws-google-maps-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/bws-google-maps/buy/?k=117c3f9fc17f2c83ef430a8a9dc06f56&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 	=> 'admin.php?page=bws-google-maps-pro.php'
	),
	'google-sitemap-plugin/google-sitemap-plugin.php' => array(
		'category'		=> array( 'other', 'recommended' ),
		'name'			=> 'Google Sitemap',
		'description'	=> 'Generate and add XML sitemap to WordPress website. Help search engines index your blog.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/google-sitemap/?k=5202b2f5ce2cf85daee5e5f79a51d806&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=google-sitemap-plugin.php',
		'pro_version'	=> 'google-sitemap-pro/google-sitemap-pro.php',
		'purchase'		=> 'https://bestwebsoft.com/products/wordpress/plugins/google-sitemap/buy/?k=7ea384a5cc36cb4c22741caa20dcd56d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=google-sitemap-pro.php'
	),
	'google-shortlink/google-shortlink.php' => array(
		'category'		=> array( 'other' ),
		'name'			=> 'Google Shortlink',
		'description'	=> 'Replace external WordPress website links with Google shortlinks and track click stats.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/google-shortlink/?k=afcf3eaed021bbbbeea1090e16bc22db&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=gglshrtlnk_options'
	),
	'htaccess/htaccess.php' => array(
		'category'		=> array( 'security' ),
		'name'			=> 'Htaccess',
		'description'	=> 'Protect WordPress website – allow and deny access for certain IP addresses, hostnames, etc.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/htaccess/?k=2b865fcd56a935d22c5c4f1bba52ed46&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=htaccess.php',
		'pro_version'	=> 'htaccess-pro/htaccess-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/htaccess/buy/?k=59e9209a32864be534fda77d5e591c15&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 	=> 'admin.php?page=htaccess-pro.php'
	),
	'job-board/job-board.php' => array(
		'category'		=> array( 'content-and-media' ),
		'name'			=> 'Job Board',
		'description'	=> 'Create your personal job board and listing WordPress website. Search jobs, submit CV/resumes, choose candidates.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/job-board/?k=b0c504c9ce6edd6692e04222af3fed6f&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=job-board.php'
	),
	'bws-latest-posts/bws-latest-posts.php' => array(
		'category'		=> array( 'content-and-media' ),
		'name'			=> 'Latest Posts',
		'description'	=> 'Add latest posts or latest posts for selected categories widgets to WordPress website.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/latest-posts/?k=ef4e125aadadd8d219140daa184a9399&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=latest-posts.php'
	),
	'limit-attempts/limit-attempts.php' => array(
		'category'		=> array( 'security', 'recommended' ),
		'name'			=> 'Limit Attempts',
		'description'	=> 'Protect WordPress website against brute force attacks. Limit rate of login attempts.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/limit-attempts/?k=b14e1697ee4d008abcd4bd34d492573a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=limit-attempts.php',
		'pro_version'	=> 'limit-attempts-pro/limit-attempts-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/limit-attempts/buy/?k=9d42cdf22c7fce2c4b6b447e6a2856e0&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=limit-attempts-pro.php'
	),
	'bws-linkedin/bws-linkedin.php' => array(
		'category'		=> array( 'social' ),
		'name'			=> 'LinkedIn',
		'description'	=> 'Add LinkedIn Share and Follow buttons to WordPress posts, pages and widgets. 5 plugins included – profile, insider, etc.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/linkedin/?k=d63c7319622ccc5f589dd2d545c1d77c&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=linkedin.php',
		'pro_version'	=> 'bws-linkedin-pro/bws-linkedin-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/linkedin/buy/?k=41dcc36192994408d24b103a02134567&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=linkedin-pro.php'
	),
	'multilanguage/multilanguage.php' => array(
		'category'		=> array( 'content-and-media', 'recommended' ),
		'name'			=> 'Multilanguage',
		'description'	=> 'Translate WordPress website content to other languages manually. Create multilingual pages, posts, widgets, menus, etc.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/multilanguage/?k=7d68c7bfec2486dc350c67fff57ad433&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=mltlngg_settings',
		'pro_version'	=> 'multilanguage-pro/multilanguage-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/multilanguage/buy/?k=2d1121cd9a5ced583fc29eefd51bdf57&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=mltlnggpr_settings'
	),
	'pagination/pagination.php' => array(
		'category'		=> array( 'content-and-media' ),
		'name'			=> 'Pagination',
		'description'	=> 'Add customizable pagination to WordPress website. Split long content to multiple pages for better navigation.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/pagination/?k=22adb940256f149559ba8fedcd728ac8&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=pagination.php',
		'pro_version'	=> 'pagination-pro/pagination-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/pagination/buy/?k=b87201d5a0505c621d0b14f4e8d4ccd6&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=pagination-pro.php'
	),
	'pdf-print/pdf-print.php' => array(
		'category'		=> array( 'other' ),
		'name'			=> 'PDF & Print',
		'description'	=> 'Generate PDF files and print WordPress posts/pages. Customize document header/footer styles and appearance.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/pdf-print/?k=bfefdfb522a4c0ff0141daa3f271840c&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=pdf-print.php',
		'pro_version'	=> 'pdf-print-pro/pdf-print-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/pdf-print/buy/?k=fd43a0e659ddc170a9060027cbfdcc3a&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 		=> 'admin.php?page=pdf-print-pro.php'
	),
	'bws-pinterest/bws-pinterest.php' => array(
		'category'		=> array( 'social' ),
		'name'			=> 'Pinterest',
		'description'	=> 'Add Pinterest Follow, Pin It buttons and profile widgets (Pin, Board, Profile) to WordPress posts, pages and widgets.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/pinterest/?k=504107b6213f247a67fe7ffb94e97c78&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=pinterest.php',
		'pro_version'	=> 'bws-pinterest-pro/bws-pinterest-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/pinterest/buy/?k=ab0069edd1914a3ca8f541bfd88bb0bb&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=pinterest-pro.php'
	),
	'bws-popular-posts/bws-popular-posts.php' => array(
		'category'		=> array( 'content-and-media', 'recommended' ),
		'name'			=> 'Popular Posts',
		'description'	=> 'Track views, comments and add most popular posts to Wordpress widgets.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/popular-posts/?k=4d529f116d2b7f7df3a78018c383f975&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=popular-posts.php'
	),
	'portfolio/portfolio.php' => array(
		'category'		=> array( 'content-and-media', 'recommended' ),
		'name'			=> 'Portfolio',
		'description'	=> 'Create your personal portfolio WordPress website. Manage and showcase past projects to get more clients.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/portfolio/?k=1249a890c5b7bba6bda3f528a94f768b&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=portfolio.php',
		'pro_version'	=> 'portfolio-pro/portfolio-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/portfolio/buy/?k=2cc716026197d36538a414b728e49fdd&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 	=> 'admin.php?page=portfolio-pro.php'
	),
	'post-to-csv/post-to-csv.php' => array(
		'category'		=> array( 'utilities' ),
		'name'			=> 'Post to CSV',
		'description'	=> 'Export WordPress posts to CSV file format easily. Configure data order.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/post-to-csv/?k=653aa55518ae17409293a7a894268b8f&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=post-to-csv.php'
	),
	'profile-extra-fields/profile-extra-fields.php' => array(
		'category'		=> array( 'management' ),
		'name'			=> 'Profile Extra Fields',
		'description'	=> 'Add extra fields to default WordPress user profile. The easiest way to create and manage additional custom values.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/profile-extra-fields/?k=fe3b6c3dbc80bd4b1cf9a27a2f339820&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=profile-extra-fields.php'
	),
	'promobar/promobar.php' => array(
		'category'		=> array( 'advertisement' ),
		'name'			=> 'PromoBar',
		'description'	=> 'Add and display HTML advertisement on WordPress website. Customize bar styles and appearance.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/promobar/?k=619eac2232d9cfa382c4e678c3b14766&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=promobar.php',
		'pro_version'	=> 'promobar-pro/promobar-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/promobar/buy/?k=a9b09708502f12a1483532ba12fe2103&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=promobar-pro.php'
	),
	'quotes-and-tips/quotes-and-tips.php' => array(
		'category'		=> array( 'content-and-media' ),
		'name'			=> 'Quotes and Tips',
		'description'	=> 'Add customizable quotes and tips blocks to WordPress posts, pages and widgets.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/quotes-and-tips/?k=5738a4e85a798c4a5162240c6515098d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=quotes-and-tips.php'
	),
	'rating-bws/rating-bws.php' => array(
		'category'		=> array( 'content-and-media' ),
		'name'			=> 'Rating',
		'description'	=> 'Add rating plugin to your WordPress website to receive feedback from your customers.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/rating/?k=c00e0824bb999735a3224616ef51f4c5&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=rating.php'
	),
	'realty/realty.php' => array(
		'category'		=> array( 'content-and-media' ),
		'name'			=> 'Realty',
		'description'	=> 'Create your personal real estate WordPress website. Sell, rent and buy properties. Add, search and browse listings easily.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/realty/?k=d55de979dbbbb7af0b2ff1d7f43884fa&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=realty_settings',
		'pro_version'	=> 'realty-pro/realty-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/realty/buy/?k=c7791f0a72acfb36f564a614dbccb474&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=realty_pro_settings'
	),
	're-attacher/re-attacher.php' => array(
		'category'		=> array( 'utilities' ),
		'name'			=> 'Re-attacher',
		'description'	=> 'Attach, unattach and re-attach media files quickly to WordPress posts and pages.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/re-attacher/?k=4d529f116d2b7f7df3a78018c383f975&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=re-attacher.php'
	),
	'relevant/related-posts-plugin.php' => array(
		'category'		=> array( 'content-and-media', 'recommended' ),
		'name'			=> 'Relevant - Related Posts',
		'description'	=> 'Add related posts to WordPress posts or widgets. Link your readers to relevant content.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/related-posts/?k=73fb737037f7141e66415ec259f7e426&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=related-posts-plugin.php'
	),
	'sender/sender.php' => array(
		'category'		=> array( 'social', 'recommended' ),
		'name'			=> 'Sender',
		'description'	=> 'Send bulk email messages to WordPress users. Custom templates, advanced settings and detailed reports.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/sender/?k=89c297d14ba85a8417a0f2fc05e089c7&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=sndr_settings',
		'pro_version'	=> 'sender-pro/sender-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/sender/buy/?k=dc5d1a87bdc8aeab2de40ffb99b38054&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=sndrpr_settings'
	),
	'bws-smtp/bws-smtp.php' => array(
		'category'		=> array( 'utilities' ),
		'name'			=> 'SMTP',
		'description'	=> 'Configure SMTP server to receive email messages from WordPress to Gmail, Yahoo, Hotmail and other services.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/smtp/?k=0546419f962704429ad2d9b88567752f&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=bwssmtp_settings'
	),
	'social-buttons-pack/social-buttons-pack.php' => array(
		'category'		=> array( 'social', 'recommended' ),
		'name'			=> 'Social Buttons Pack',
		'description'	=> 'Add social media buttons and widgets to WordPress posts, pages and widgets. FB, Twitter, G+1, Pinterest, LinkedIn.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/social-buttons-pack/?k=b6440fad9f54274429e536b0c61b42da&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=social-buttons.php',
		'pro_version'	=> 'social-buttons-pack-pro/social-buttons-pack-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/social-buttons-pack/buy/?k=e7059cacde0d275b224a5d995c9160fd&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=social-buttons.php'
	),
	'social-login-bws/social-login-bws.php' => array(
		'category'		=> array( 'social' ),
		'name'			=> 'Social Login',
		'description'	=> 'Add social media login, registration, and commenting to your WordPress website.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/social-login/?k=62817c9c94f24129e40894e1d9c3f49d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=social-login.php'
	),
	'subscriber/subscriber.php' => array(
		'category'		=> array( 'social', 'recommended' ),
		'name'			=> 'Subscriber',
		'description'	=> 'Add email newsletter sign up form to WordPress posts, pages and widgets. Collect data and subscribe your users.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/subscriber/?k=a4ecc1b7800bae7329fbe8b4b04e9c88&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=sbscrbr_settings_page',
		'pro_version'	=> 'subscriber-pro/subscriber-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/subscriber/buy/?k=02dbb8b549925d9b74e70adc2a7282e4&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 	=> 'admin.php?page=sbscrbrpr_settings_page'
	),
	'bws-testimonials/bws-testimonials.php' => array(
		'category'		=> array( 'content-and-media', 'recommended' ),
		'name'			=> 'Testimonials',
		'description'	=> 'Add testimonials and feedbacks from your customers to WordPress posts, pages and widgets.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/testimonials/?k=3fe4bb89dc901c98e43a113e08f8db73&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=testimonials.php'
	),
	'timesheet/timesheet.php' => array(
		'category'		=> array( 'management' ),
		'name'			=> 'Timesheet',
		'description'	=> 'Best timesheet plugin for WordPress. Track employee time, streamline attendance and generate reports.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/timesheet/?k=06a58bb78c17a43df01825925f05a5c1&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=timesheet_settings',
		'pro_version'	=> 'timesheet-pro/timesheet-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/timesheet/buy/?k=a448ce4cab0d365b7774c9bc3903b851&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 	=> 'admin.php?page=timesheet_pro_settings'
	),
	'twitter-plugin/twitter.php' => array(
		'category'		=> array( 'social' ),
		'name'			=> 'Twitter',
		'description'	=> 'Add Twitter Follow, Tweet, Hashtag, and Mention buttons to WordPress posts, pages and widgets.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/twitter/?k=f8cb514e25bd7ec4974d64435c5eb333&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=twitter.php',
		'pro_version'	=> 'twitter-pro/twitter-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/twitter/buy/?k=63ecbf0cc9cebf060b5a3c9362299700&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 	=> 'admin.php?page=twitter-pro.php'
	),
	'updater/updater.php' => array(
		'category'		=> array( 'utilities', 'recommended' ),
		'name'			=> 'Updater',
		'description'	=> 'Automatically check and update WordPress website core with all installed plugins and themes to the latest versions.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/updater/?k=66f3ecd4c1912009d395c4bb30f779d1&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=updater-options',
		'pro_version'	=> 'updater-pro/updater_pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/updater/buy/?k=cf633acbefbdff78545347fe08a3aecb&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 	=> 'admin.php?page=updater-pro-options'
	),
	'user-role/user-role.php' => array(
		'category'		=> array( 'utilities' ),
		'name'			=> 'User Role',
		'description'	=> 'Powerful user role management plugin for WordPress website. Create, edit, copy, and delete user roles.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/user-role/?k=dfe2244835c6fbf601523964b3f34ccc&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=user-role.php',
		'pro_version'	=> 'user-role-pro/user-role-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/user-role/buy/?k=cfa9cea6613fb3d7c0a3622fa2faaf46&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings' 	=> 'admin.php?page=user-role-pro.php'
	),
	'visitors-online/visitors-online.php' => array(
		'category'		=> array( 'site-stats' ),
		'name'			=> 'Visitors Online',
		'description'	=> 'Display live count of online visitors who are currently browsing your WordPress website.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/visitors-online/?k=93c28013a4f830671b3bba9502ed5177&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=visitors-online.php',
		'pro_version'	=> 'visitors-online-pro/visitors-online-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/visitors-online/buy/?k=f9a746075ff8a0a6cb192cb46526afd2&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=visitors-online-pro.php'
	),
	'zendesk-help-center/zendesk-help-center.php' => array(
		'category'		=> array( 'utilities' ),
		'name'			=> 'Zendesk Help Center',
		'description'	=> 'Backup and export Zendesk Help Center content automatically to your WordPress website database.',
		'link'			=> 'https://bestwebsoft.com/products/wordpress/plugins/zendesk-help-center/?k=2a5fd2f4b2f4bde46f2ca44b8d15846d&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'settings'		=> 'admin.php?page=zendesk_hc.php&action=settings',
		'pro_version'	=> 'zendesk-help-center-pro/zendesk-help-center-pro.php',
		'purchase' 		=> 'https://bestwebsoft.com/products/wordpress/plugins/zendesk-help-center/buy/?k=45199e4538b5befe4d9566868a61a3aa&pn=' . $bws_plugin_info["id"] . '&v=' . $bws_plugin_info["version"] . '&wp_v=' . $wp_version,
		'pro_settings'	=> 'admin.php?page=zendesk_hc_pro.php&tab=settings'
	)
);

$themes = array(
	(object) array(
		'name' 		=> 'Opening',
		'slug' 		=> 'opening',
		'href' 		=> 'https://bestwebsoft.com/products/wordpress/themes/opening-job-board-wordpress-theme/'
	),
	(object) array(
		'name' 		=> 'Real Estate',
		'slug' 		=> 'realestate',
		'href' 		=> 'https://bestwebsoft.com/products/wordpress/themes/real-estate-creative-wordpress-theme/'
	),
	(object) array(
		'name' 		=> 'Renty',
		'slug' 		=> 'renty',
		'href' 		=> 'https://bestwebsoft.com/products/wordpress/themes/renty-car-rental-booking-wordpress-theme/'
	),
	(object) array(
		'name' 		=> 'Unity',
		'slug' 		=> 'unity',
		'href' 		=> 'https://bestwebsoft.com/products/wordpress/themes/unity-multipurpose-wordpress-theme/'
	)
);