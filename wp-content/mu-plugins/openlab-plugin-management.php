<?php

/**
 * This file is responsible for determining which plugins are shown to users
 * on the Plugins page of their individual sites
 */

function openlab_hide_plugins( $plugins ) {
	$network_admin_only = array();

	if ( defined( 'WP_CLI' ) ) {
		return $plugins;
	}

	$super_admin_only = array(
		'1-jquery-photo-gallery-slideshow-flash/wp-1pluginjquery.php',
		'accordion-shortcodes/accordion-shortcodes.php',
		'achievements/achievements.php',
		'ajax-thumbnail-rebuild/ajax-thumbnail-rebuild.php',
		'ambrosite-nextprevious-post-link-plus/ambrosite-post-link-plus.php',
		'an-gradebook/GradeBook.php',
		'ari-fancy-lightbox/ari-fancy-lightbox.php',
		'awesome-flickr-gallery-plugin/index.php',
		'badgeos/badgeos.php',
		'badgeos-badgestack-add-on/badgeos-badgestack.php',
		'bbpress/bbpress.php',
		'blog2social/blog2social.php',
		'bookly-responsive-appointment-booking-tool/main.php',
		'bp-customizable-group-categories/bp-customizable-group-categories.php',
		'bp-event-organiser/bp-event-organiser.php',
		'bp-reply-by-email/loader.php',
		'bu-learning-blocks/bu-learning-blocks.php', // #3149
		'bu-navigation/bu-navigation.php', // #3149
		'cac-featured-content/cac-featured-content.php',
		'cac-non-cuny-signup/loader.php',
		'cardboard/cardboard.php',
		'cubepoints/cubepoints.php',
		'distributor/distributor.php', // #3279
		'dk-pdf/dk-pdf.php',
		'download-media-library/download-media-library.php',
		'dw-question-answer/dw-question-answer.php',
		'dynamic-widgets/dynamic-widgets.php',
		'easy-table/easy-table.php',
		'edge-suite/edge-suite.php',
		'elasticpress/elasticpress.php',
		'elasticpress-buddypress/elasticpress-buddypress.php',
		'elasticpress-buddypress/elasticpress-rest.php',
		'embed-comment-images/eiic.php',
		'embed-google-map/embed_google_map.php',
		'enable-jquery-migrate-helper/enable-jquery-migrate-helper.php',
		'enhanced-tooltipglossary/enhanced-tooltipglossary.php', // #3301
		'enigma/enigma.php',
		'ewww-image-optimizer/ewww-image-optimizer.php',
		'featured-content-gallery/content-gallery.php',
		'fix-simplepie-errors/_fix-simplepie-errors.php',
		'google-document-embedder/gviewer.php',
		'google-maps-embed/cets_EmbedGmaps.php',
		'gp-media-library/gp-media-library.php',
		'grader/grader.php',
		'gravity-forms-addons/gravity-forms-addons.php',
		'gravityperks/gravityperks.php',
		'import-html-pages/html-import.php',
		'inline-comments/inline-comments.php',
		'kb-gradebook/kb-gradebook.php',
		'link-manager/link-manager.php',
		'mailchimp-for-wp/mailchimp-for-wp.php',
		'media-cleaner/media-cleaner.php',
		'openlab-modules/openlab-modules.php',
		'out-of-the-box/out-of-the-box.php',
		'p3-profiler/p3-profiler.php',
		'page-links-to/page-links-to.php',
		'pagemash/pagemash.php',
		'page-tagger/page-tagger.php',
		'papercite/papercite-wp-plugin.php',
		'pdf-embedder/pdf_embedder.php',
		'pdfembedder-premium/mobile_pdf_embedder.php',
		'post-gallery-widget/post-gallery.php',
		'query-monitor/query-monitor.php',
		'quiz-maker/quiz-maker.php',
		'rederly.php',
		'simple-drop-cap/simple-drop-cap.php',
		'simple-pull-quote/simple-pull-quote.php',
		'slideshare/slideshare.php',
		'social/social.php',
		'static-html-output-plugin/wp2static.php',
		'stout-google-calendar/stout-google-calendar.php',
		'sz-google/sz-google.php',
		'table-of-contents-plus/toc.php',
		'tako-movable-comments/tako.php',
		'taxonomy-terms-order/taxonomy-terms-order.php',
		'tinymce-comment-field/tinymce-comment-field.php',
		'titan-framework/titan-framework.php',
		'ufhealth-require-image-alt-tags/ufhealth-require-image-alt-tags.php',
		'watupro/watupro.php',
		'webwork/webwork.php',
		'webworkqa/webwork.php',
		'_webwork/webwork.php',
		'wp-accessibility/wp-accessibility',
		'wp-ajax-edit-comments/wp-ajax-edit-comments.php',
		'wp-dpla/wp-dpla.php',
		'wp-latex/wp-latex.php',
		'wp-link-status/wp-link-status.php',
		'wp-pro-quiz/wp-pro-quiz.php',
		'wp-post-to-pdf/wp-post-to-pdf.php',
		'wp-simile-timeline/timeline.php',
		'wp-simple-anchors-links/wp-simple-anchors-links.php',
		'wp-swfobject/wp-swfobject.php',
		'wp-twitter/wp-twitter.php',
		'wpbadgedisplay/wpbadgedisplay.php',
		'wpbadger/wpbadger.php',
		'wp-to-twitter/wp-to-twitter.php',
	);

	if ( ! is_super_admin() ) {
		foreach ( $plugins as $pkey => $plugin ) {
			if ( in_array( $pkey, $super_admin_only, true ) && ! is_plugin_active( $pkey ) ) {
				unset( $plugins[ $pkey ] );
			}
		}
	}

	if ( ! is_network_admin() ) {
		$network_admin_only = array(
			'achievements/loader.php',
			'bp-groupblog/loader.php',
			'buddypress-group-documents/index.php',
			'bp-include-non-member-comments/bp-include-non-member-comments.php',
			'bp-mpo-activity-filter/bp-mpo-activity-filter.php',
			'bp-system-report/bp-system-report.php',
			'buddypress/bp-loader.php',
			'buddypress-docs/loader.php',
			'buddypress-docs-in-group/loader.php',
			'buddypress-group-email-subscription/bp-activity-subscription.php',
			'bp-template-pack/loader.php',
			'staticpress/plugin.php',
			'cac-group-announcements/cac-group-announcements.php',
			'cubepoints-buddypress-integration/loader.php',
			'digressit/digressit.php',
			'forum-attachments-for-buddypress/forum-attachments-for-buddypress.php',
			'genesis-connect/genesis-connect.php',
			'genesis-connect-for-buddypress/genesis-connect.php',
			'invite-anyone/invite-anyone.php',
			'more-privacy-options/ds_wp3_private_blog.php',
			'ol-portfolio/ol-portfolio.php',
			'openlab-badges/openlab-badges.php',
			'openlab-grade-comments/openlab-grade-comments.php',
			'u-buddypress-forum-attachment/u-bp-forum-attachment.php',
			'wds-buddypress-docs/loader.php',
			'wds-citytech/wds-citytech.php',
		);

		foreach ( $plugins as $pkey => $plugin ) {
			if ( in_array( $pkey, $network_admin_only, true ) && ! is_plugin_active( $pkey ) ) {
				unset( $plugins[ $pkey ] );
			}
		}
	}

	$blog_specific_whitelist = array(
		'elementor/elementor.php' => [
			8100, // bmi - http://redmine.citytech.cuny.edu/issues/3100
		],
		'elementskit-lite/elementskit-lite.php' => [
			8100, // bmi - http://redmine.citytech.cuny.edu/issues/3100
		],
		'fixed-toc/fixed-toc.php'         => array(
			12249, // openlabguide - https://redmine.citytech.cuny.edu/issues/2562
		),
		'h5p/h5p.php'                     => array(
			11188, // bio-oer - https://redmine.citytech.cuny.edu/issues/2088
			11261, // openstax-bio - https://redmine.citytech.cuny.edu/issues/2088
			15333, // ssw-fall2020 - http://redmine.citytech.cuny.edu/issues/2847
		),
		'ml-slider/ml-slider.php' => [
			8100, // bmi - http://redmine.citytech.cuny.edu/issues/3100
		],
		'openlab-comd-gform.php'          => array(
			14428, // gracegallery - http://redmine.citytech.cuny.edu/issues/2692#change-18693
		),
		'ultimate-responsive-image-slider/ultimate-responsive-image-slider.php' => [
			8100, // bmi - http://redmine.citytech.cuny.edu/issues/3100
		],
	);

	foreach ( $blog_specific_whitelist as $plugin_file => $whitelisted_blogs ) {
		if ( ! in_array( get_current_blog_id(), $whitelisted_blogs, true ) && ! is_plugin_active( $plugin_file ) ) {
			unset( $plugins[ $plugin_file ] );
		}
	}

	$plugins = openlab_mu_group_type_plugin_handling( $plugins );

	return $plugins;
}
add_filter( 'all_plugins', 'openlab_hide_plugins' );

/**
 * Method for excluding plugins by group type
 * @param type $plugins
 * @return type
 */
function openlab_mu_group_type_plugin_handling( $plugins ) {
	global $wpdb;

	//first we convert the blog id to a group id
	$blog_id = get_current_blog_id();

	$group_id = openlab_get_group_id_by_blog_id( $blog_id );

	if ( $group_id ) {

		//then we get the group type and apply any necessary conditions
		$group_type = groups_get_groupmeta( $group_id, 'wds_group_type' );

		$course_only_plugins = array( 'openlab-gradebook/GradeBook.php' );

		if ( $group_type !== 'course' ) {
			foreach ( $plugins as $pkey => $plugin ) {
				if ( in_array( $pkey, $course_only_plugins ) ) {
					unset( $plugins[ $pkey ] );
					//deactive any legacy installs
					$plugin_dir  = WP_PLUGIN_DIR;
					$plugin_path = "$plugin_dir/$pkey";

					if ( is_plugin_active( $pkey ) ) {
						deactivate_plugins( $plugin_path );
					}
				}
			}
		}
	}

	return $plugins;
}

/**
 * License key for PDF Embedder Premium.
 */
function openlab_pdfemb_filter_license_key( $opt ) {
	if ( ! defined( 'OPENLAB_PDFEMB_LICENSE_KEY' ) ) {
		return $opt;
	}

	$opt['pdfemb_license_key'] = OPENLAB_PDFEMB_LICENSE_KEY;
	return $opt;
}
add_filter( 'option_pdfemb', 'openlab_pdfemb_filter_license_key' );
add_filter( 'default_option_pdfemb', 'openlab_pdfemb_filter_license_key' );

/**
 * License key for out-of-the-box.
 */
add_filter(
	'pre_site_option_outofthebox_purchaseid',
	function( $pre ) {
		if ( defined( 'OUTOFTHEBOX_PURCHASEID' ) ) {
			$pre = OUTOFTHEBOX_PURCHASEID;
		}
		return $pre;
	}
);

/**
 * Load stylesheet for TablePress.
 */
function openlab_tablepress_stylesheet( $atts ) {
	wp_enqueue_style( 'openlab-tablepress', content_url( 'mu-plugins/css/tablepress.css' ) );
	return $atts;
}
add_filter( 'tablepress_shortcode_table_default_shortcode_atts', 'openlab_tablepress_stylesheet' );

/** Jetpack **/

/**
 * Remove entire modules from Jetpack
 *
 * @param  mixed $modules
 * @return array
 */
function openlab_jetpack_module_management( $modules ) {
	//remove Jetpack notes
	if ( isset( $modules['notes'] ) ) {
		unset( $modules['notes'] );
	}

	if ( isset( $modules['protect'] ) ) {
		unset( $modules['protect'] );
	}

	return $modules;
}
add_filter( 'jetpack_get_available_modules', 'openlab_jetpack_module_management' );

/**
 * Remove Jetpack adminbar hooks that cause styling and functionality conflicts
 * Hooks into wp_head to maximize hook coverage
 *
 * @return void
 */
function openlab_jetpack_adminbar_management() {
	//remove JetPack stats in adminbar
	if ( class_exists( 'Jetpack' ) ) {
		remove_action( 'admin_bar_menu', 'stats_admin_bar_menu', 100 );

		//remove JetPack likes in adminbar
		if ( class_exists( 'Jetpack_Likes' ) ) {
			openlab_remove_filters_for_anonymous_class( 'admin_bar_menu', 'Jetpack_Likes', 'admin_bar_likes', 60 );
		}
	}

}

add_action( 'wp_head', 'openlab_jetpack_adminbar_management', 9999 );

/**Utilities**/
/**
 * Allow to remove method for an hook when, it's a class method used and class don't have variable, but you know the class name
 * Courtesy of: https://github.com/herewithme/wp-filters-extras
 */
function openlab_remove_filters_for_anonymous_class( $hook_name = '', $class_name = '', $method_name = '', $priority = 0 ) {
	global $wp_filter;

	// Take only filters on right hook name and priority
	if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
		return false;
	}

	// Loop on filters registered
	foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
		// Test if filter is an array ! (always for class/method)
		if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
			// Test if object is a class, class and method is equal to param !
			if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) == $class_name && $filter_array['function'][1] == $method_name ) {
				// Test for WordPress >= 4.7 WP_Hook class (https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/)
				if ( is_a( $wp_filter[ $hook_name ], 'WP_Hook' ) ) {
					unset( $wp_filter[ $hook_name ]->callbacks[ $priority ][ $unique_id ] );
				} else {
					unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
				}
			}
		}
	}

	return false;
}

/**
 * Load JS for out-of-the-box accessibility fixes.
 */
add_action(
	'init',
	function() {
		if ( ! defined( 'OUTOFTHEBOX_VERSION' ) ) {
			return;
		}

		wp_enqueue_script( 'openlab-out-of-the-box-a11y', content_url( 'mu-plugins/js/openlab-out-of-the-box.js' ), [ 'jquery' ] );
	}
);

/**
 * Define wp-quicklatex cache directory location.
 */
define( 'WP_QUICKLATEX_CACHE_DIR', WP_CONTENT_DIR . '/uploads/ql-cache' );
define( 'WP_QUICKLATEX_CACHE_URL', content_url() . '/uploads/ql-cache' );
