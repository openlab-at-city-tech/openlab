<?php
ob_start();
include plugin_dir_path(__FILE__) . 'views/plugin_compare_table.php';
$plugin_compare_table = ob_get_contents();
ob_end_clean();
$cminds_plugin_config = array(
	'plugin-is-pro'                 => false,
	'plugin-has-addons'             => true,
	'plugin-version'                => '4.4.9',
	'plugin-addons'        => array(
		array(
			'title' => 'Footnotes Plugin',
			'description' => 'Add and manage footnotes, citations, and bibliography with this footnotes Plugin. Improve clarity and provide references.',
			'link' => 'https://wordpress.org/plugins/cm-footnotes/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPFootnotesS.png',
		),
		array(
			'title' => 'Search and Replace Plugin',
			'description' => 'Search and replace words, phrases, and HTML within your website posts and pages.',
			'link' => 'https://wordpress.org/plugins/cm-on-demand-search-and-replace/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPOnDemandSearchandReplaceS.png',
		),
		array(
			'title' => 'Video Lessons Manager',
			'description' => 'Create and display video lessons on your site by importing Vimeo videos. Organize content and track students with this efficient LMS plugin.',
			'link' => 'https://wordpress.org/plugins/cm-video-lesson-manager/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPVideoLessonsManagerS.png',
		),
		array(
			'title' => 'Popup Banners',
			'description' => 'Create and customize popups. Display messages, Call to actions, promotions, or announcements to engage visitors and boost interaction.',
			'link' => 'https://wordpress.org/plugins/cm-pop-up-banners/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPPopUpBannersS.png',
		),
		array(
			'title' => 'Header and Footer Plugin',
			'description' => 'Add custom CSS and JavaScript to headers and footers on your site with the header and footer plugin for enhanced control and design.',
			'link' => 'https://wordpress.org/plugins/cm-header-footer-script-loader/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPHeaderAndFooterScriptLoaderS.png',
		),
		array(
			'title' => 'Context Product Recommendations',
			'description' => 'Display recommended products on your website post or pages based on the content of the post.',
			'link' => 'https://wordpress.org/plugins/cm-context-related-product-recommendations/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPProductRecommendationsS.png',
		),
	),
	'plugin-specials'        => array(
		array(
			'title' => 'Questions and Answers Plugin',
			'description' => 'Experience a mobile-responsive discussion forum where members can post questions, answers, and comments, with integrated payment support.',
			'link' => 'https://www.cminds.com/cm-answer-store-page-content/?discount=CMINDS10',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPQuestionsAndAnswersS.png',
		),
		array(
			'title' => 'RSS Post Importer Plugin',
			'description' => 'Support importing and displaying external posts using RSS, Atom feeds and scraping tool to your WordPress site.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/rss-post-importer-plugin-wordpress-creativeminds/?discount=CMINDS10',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPRSSPostImporterS.png',
		),
		array(
			'title' => 'Invitation Code Content Access',
			'description' => 'Generate restricted access codes for specific content, pages, and files. Each code can have a limited number of uses and an expiration date.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/invitation-code-content-access-plugin-wordpress/?discount=CMINDS10',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPContentAccessInvitationCodeS.png',
		),
		array(
			'title' => 'Site Access and Content Restriction',
			'description' => 'A robust membership solution and content restriction plugin that supports role-based access to content on your WordPress website.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/membership-plugin-for-wordpress/?discount=CMINDS10',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPSiteRestrictionS.png',
		),
		array(
			'title' => 'Map Routes Manager',
			'description' => 'Draw map routes and generate a catalog of routes and trails with points of interest using Google maps.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/google-maps-routes-manager-plugin-for-wordpress-by-creativeminds/?discount=CMINDS10',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPMapRoutesManagerS.png',
		),
		array(
			'title' => 'Map Locations Manager',
			'description' => 'Efficiently manage map locations and enable location finding using Google Maps. Includes support for detailed location descriptions, images, and videos.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/multiple-locations-google-maps/?discount=CMINDS10',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPMapLocationsandStoreLocaterS.png',
		),
	),
	'plugin-bundles'        => array(
		array(
			'title' => '99+ Free Pass Plugins Suite',
			'description' => 'Get all CM 99+ WordPress plugins and addons. Includes unlimited updates and one year of priority support.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/cm-wordpress-plugins-yearly-membership/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPBundleWPSuiteS.png',
		),
		array(
			'title' => 'Essential Publishing Plugin Package',
			'description' => 'Enhance your WordPress publishing with a bundle of seven plugins that elevate content generation, presentation, and user engagement on your site.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/essential-wordpress-publishing-tools-bundle/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPBundlePublishingS.png',
		),
		array(
			'title' => 'Essential Content Marketing Tools',
			'description' => 'Enhance your WordPress content marketing with seven plugins for improved content generation, presentation, and user engagement.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/essential-wordpress-content-marketing-tools-bundle/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPBundleContentS.png',
		),
		array(
			'title' => 'Essential Security Plugins',
			'description' => 'Enhance your WordPress security with a bundle of five plugins that provide additional ways to protect your content and site from spammers, hackers, and exploiters.',
			'link' => 'https://www.cminds.com/wordpress-plugins-library/essential-wordpress-security-tools-plugin-bundle/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPBundleSecurityS.png',
		),
	),
	'plugin-services'        => array(
		array(
			'title' => 'WordPress Custom Hourly Support',
			'description' => 'Hire our expert WordPress developers on an hourly basis, offering a-la-carte service to craft your custom WordPress solution.',
			'link' => 'https://www.cminds.com/wordpress-services/wordpress-custom-hourly-support-package/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPServicesHourlySupportS.png',
		),
		array(
			'title' => 'Performance and Optimization Analysis',
			'description' => 'Receive a comprehensive review of your WordPress website with optimization suggestions to enhance its speed and performance.',
			'link' => 'https://www.cminds.com/wordpress-services/wordpress-performance-and-speed-optimization-analysis-service/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPServicesPerformanceS.png',
		),
		array(
			'title' => 'WordPress Plugin Installation',
			'description' => 'We offer professional installation and configuration of plugins or add-ons on your site, tailored to your specified requirements.',
			'link' => 'https://www.cminds.com/wordpress-services/plugin-installation-service-for-wordpress-by-creativeminds/',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPServicesExtensionInstallationS.png',
		),
		array(
			'title' => 'WordPress Consulting',
			'description' => 'Purchase consulting hours to receive assistance in designing or planning your WordPress solution. Our expert consultants are here to help bring your vision to life.',
			'link' => 'https://www.cminds.com/wordpress-services/consulting-planning-hourly-support-service-wordpress-creativeminds/#description',
			'image' => plugin_dir_url( __FILE__ ) . 'views/icons/WPServicesConsultingS.png',
		),
	),
	'plugin-abbrev'                 => 'cmtt',
	'plugin-affiliate'              => '',
	'plugin-redirect-after-install' => admin_url( 'admin.php?page=cmtt_settings' ),
	'plugin-campign'                => '?utm_source=glossary&utm_campaign=freeupgrade&upgrade=1',
	'plugin-show-guide'             => true,
	'plugin-show-upgrade'           => true,
	'plugin-show-upgrade-first'     => true,
	'plugin-guide-text'             => '<div style="display:block">
        <ol>
            <li>Go to <strong>"Add New"</strong> under the CM Tooltip Glossary menu</li>
            <li>Fill the <strong>"Title"</strong> of the glossary item and <strong>"Content"</strong></li>
            <li>Click <strong>"Publish" </strong> in the right column.</li>
            <li><strong>View</strong> this glossary item</li>
            <li>From the plugin settings click on the Link to the <strong>Glossary Index Page</strong></li>
            <li><strong>Troubleshooting:</strong> If you get a 404 error once viewing the glossary item,  make sure your WordPress permalinks are set and save them again to refresh</li>
            <li><strong>Troubleshooting:</strong> Make sure your site does not have any JavaScript error which might prevent tooltip from appearing</li>
        </ol>
    </div>',
	'plugin-guide-video-height'     => 240,
	'plugin-guide-videos'           => array(
		array( 'title' => 'Installation tutorial', 'video_id' => '157868636' ),
	),
	'plugin-upgrade-text'           => 'Use the most trusted WordPress knowledge base plugin to improve your site organization, SEO, and user experience all in one! Quickly build a dictionary, encyclopedia, wiki, online library, or glossary of terms with popup info boxes internally linking to dedicated term and index pages.',
	'plugin-upgrade-text-list'      => array(
		array( 'title' => 'Why you should upgrade', 'video_time' => '0:00' ),
		array( 'title' => 'Related Terms', 'video_time' => '0:03' ),
		array( 'title' => 'Multiple Glossaries', 'video_time' => '0:33' ),
		array( 'title' => 'Mobile Responsive Tooltips', 'video_time' => '1:59' ),
		array( 'title' => 'Index Page Templates', 'video_time' => '2:16' ),
		array( 'title' => 'Custom Fonts', 'video_time' => '2:43' ),
		array( 'title' => 'Images in Tooltips', 'video_time' => '3:26' ),
		array( 'title' => 'Video Tooltip', 'video_time' => '4:01' ),
		array( 'title' => 'Audio Tooltip', 'video_time' => '4:47' ),
		array( 'title' => 'Wikipedia Integration', 'video_time' => '5:27' ),
		array( 'title' => 'Merriam Webster and Glosbe Dictionary Integration', 'video_time' => '6:08' ),
	),
	'plugin-upgrade-video-height'   => 240,
	'plugin-upgrade-videos'         => array(
		array( 'title' => 'Glossary Introduction', 'video_id' => '266461556' ),
	),
	'plugin-file'                   => CMTT_PLUGIN_FILE,
	'plugin-dir-path'               => plugin_dir_path( CMTT_PLUGIN_FILE ),
	'plugin-dir-url'                => plugin_dir_url( CMTT_PLUGIN_FILE ),
	'plugin-basename'               => plugin_basename( CMTT_PLUGIN_FILE ),
	'plugin-icon'                   => '',
	'plugin-name'                   => CMTT_NAME,
	'plugin-license-name'           => CMTT_CANONICAL_NAME,
	'plugin-slug'                   => '',
	'plugin-short-slug'             => 'tooltip',
	'plugin-menu-item'              => CMTT_MENU_OPTION,
	'plugin-textdomain'             => CMTT_SLUG_NAME,
	'plugin-userguide-key'          => '2162-cm-tooltip-cmtg-free-version-tutorial',
	'plugin-video-tutorials-url'    => 'https://www.videolessonsplugin.com/video-lesson/lesson/tooltip-glossary-plugin/',
	'plugin-store-url'              => 'https://www.cminds.com/wordpress-plugins-library/tooltipglossary/',
	'plugin-support-url'            => 'https://wordpress.org/support/plugin/enhanced-tooltipglossary/',
	'plugin-review-url'             => 'https://wordpress.org/support/view/plugin-reviews/enhanced-tooltipglossary/',
	'plugin-changelog-url'          => CMTT_RELEASE_NOTES,
	'plugin-licensing-aliases'      => array( CMTT_LICENSE_NAME ),
	'plugin-compare-table'          => $plugin_compare_table,
);
?>