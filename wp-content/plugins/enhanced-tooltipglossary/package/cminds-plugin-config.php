<?php

ob_start();
include plugin_dir_path(__FILE__) . 'views/plugin_compare_table.php';
$plugin_compare_table = ob_get_contents();
ob_end_clean();

$cminds_plugin_config = array(
	'plugin-is-pro'                 => false,
	'plugin-has-addons'             => true,
	'plugin-version'                => '4.2.3',
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