<?php
/*
Plugin Name: FeedWordPress
Plugin URI: http://feedwordpress.radgeek.com/
Description: simple and flexible Atom/RSS syndication for WordPress
Version: 2024.1119
Author: C. Johnson
Author URI: https://feedwordpress.radgeek.com/contact/
License: GPL
*/

/**
 * @package FeedWordPress
 * @version 2024.1119
 */

# This plugin uses code derived from:
# -	wp-rss-aggregate.php by Kellan Elliot-McCrea <kellan@protest.net>
# -	SimplePie feed parser by Ryan Parman, Geoffrey Sneddon, Ryan McCue, et al.
# -	MagpieRSS feed parser by Kellan Elliot-McCrea <kellan@protest.net>
# -	Ultra-Liberal Feed Finder by Mark Pilgrim <mark@diveintomark.org>
# -	WordPress Blog Tool and Publishing Platform <http://wordpress.org/>
# -	Github contributors @Flynsarmy, @BandonRandon, @david-robinson-practiceweb,
# 	@daidais, @thegreatmichael, @stedaniels, @alexiskulash, @quassy, @zoul0813,
# 	@timmmmyboy, @vobornik, @inanimatt, @tristanleboss, @martinburchell,
# 	@bigalownz, @oppiansteve, and @GwynethLlewelyn
# according to the terms of the GNU General Public License.

####################################################################################
## CONSTANTS & DEFAULTS ############################################################
####################################################################################

define ('FEEDWORDPRESS_VERSION', '2024.1119');
define ('FEEDWORDPRESS_AUTHOR_CONTACT', 'https://feedwordpress.radgeek.com/contact' );

if ( ! defined( 'FEEDWORDPRESS_BLEG' ) ) :
	define ( 'FEEDWORDPRESS_BLEG', true );
endif;

define( 'FEEDWORDPRESS_BLEG_BTC_pre_2020',  '15EsQ9QMZtLytsaVYZUaUCmrkSMaxZBTso' );
define( 'FEEDWORDPRESS_BLEG_BTC_2020',      '1NB1ebYVb68Har4WijmE8gKnZ47NptCqtB' ); // 2020.0201
define( 'FEEDWORDPRESS_BLEG_BTC', 	    '1HCDdeGcR66EPxkPT2rbdTd1ezh27pmjPR' ); // 2021.0713
define( 'FEEDWORDPRESS_BLEG_PAYPAL', 	    '22PAJZZCK5Z3W' );

// Defaults
define( 'DEFAULT_SYNDICATION_CATEGORY', 'Contributors' );
define( 'DEFAULT_UPDATE_PERIOD', 60 ); // value in minutes
define( 'FEEDWORDPRESS_DEFAULT_CHECKIN_INTERVAL', DEFAULT_UPDATE_PERIOD / 10 );

// Dependencies: modules packaged with FeedWordPress plugin.
/** @var string Path to parent directory */
$dir = dirname( __FILE__ );
require_once "{$dir}/externals/myphp/myphp.class.php";

/** @var bool|string Set to either true or 'yes' if debugging is set. */
$feedwordpress_debug = FeedWordPress::param( 'feedwordpress_debug', get_option( 'feedwordpress_debug' ) );

if ( is_string( $feedwordpress_debug ) ) :
	$feedwordpress_debug = ( $feedwordpress_debug == 'yes' );
endif;

define ( 'FEEDWORDPRESS_DEBUG', $feedwordpress_debug );
$feedwordpress_compatibility = true;
define ( 'FEEDWORDPRESS_COMPATIBILITY', $feedwordpress_compatibility );

define ( 'FEEDWORDPRESS_CAT_SEPARATOR_PATTERN', '/[:\n]/' );
define ( 'FEEDWORDPRESS_CAT_SEPARATOR', "\n" );

// define ('FEEDVALIDATOR_URI', 'http://feedvalidator.org/check.cgi');	// Link dead (gwyneth 20210617)
define ( 'FEEDVALIDATOR_URI', 'https://validator.w3.org/feed/check.cgi' );	// Falling back to the W3C validator link

define ( 'FEEDWORDPRESS_FRESHNESS_INTERVAL', 10 * 60 ); // Every ten minutes

define( 'FEEDWORDPRESS_BOILERPLATE_DEFAULT_HOOK_ORDER', 11000 ); // at the tail end of the filtering process

if ( FEEDWORDPRESS_DEBUG ) :
	// Help us to pick out errors, if any.
	ini_set( 'error_reporting', E_ALL & ~E_NOTICE );
	ini_set( 'display_errors', true );

	 // When testing we don't want cache issues to interfere. But this is
	 // a VERY BAD SETTING for a production server. Webmasters will eat your
	 // face for breakfast if you use it, and the baby Jesus will cry. So
	 // make sure FEEDWORDPRESS_DEBUG is FALSE for any site that will be
	 // used for more than testing purposes!
	define( 'FEEDWORDPRESS_CACHE_AGE', 1 );
	define( 'FEEDWORDPRESS_CACHE_LIFETIME', 1 );
	define( 'FEEDWORDPRESS_FETCH_TIMEOUT_DEFAULT', 60 );
else :
	// Hold onto data all day for conditional GET purposes,
	// but consider it stale after 1 min (requiring a conditional GET)
	define( 'FEEDWORDPRESS_CACHE_LIFETIME', 24 * 60 * 60 );	// aka one day.
	define( 'FEEDWORDPRESS_CACHE_AGE', 1 * 60 );
	define( 'FEEDWORDPRESS_FETCH_TIMEOUT_DEFAULT', 20 );
endif;

####################################################################################
## CORE DEPENDENCIES & PLUGIN MODULES ##############################################
####################################################################################

/** @var array<string> Dependencies: modules packaged with WordPress core */
$wpCoreDependencies = array(
	"class:SimplePie" => "class-simplepie",
	"class:WP_SimplePie_File" => "class-wp-simplepie-file",
	"class:WP_SimplePie_Sanitize_KSES" => "class-wp-simplepie-sanitize-kses",
	"function:wp_insert_user" => "registration",
);

// Ensure that we have SimplePie loaded up and ready to go
// along with the WordPress auxiliary classes.
$unmetCoreDependencies = array();
foreach ( $wpCoreDependencies as $unit => $fileSlug ) :
	list( $unitType, $unitName ) = explode( ":", $unit, 2 );

	$dependencyMet = ( ('class' == $unitType ) ? class_exists( $unitName ) : function_exists( $unitName ) );
	if ( ! $dependencyMet ) :
		$phpFileName = ABSPATH . WPINC . "/{$fileSlug}.php";
		if ( file_exists( $phpFileName ) ) :
			require_once $phpFileName;
		else :
			$unmetCoreDependencies[] = $unitName;
		endif;
	endif;
endforeach;

// Fallback garbage-pail module used in WP < 4.7 which may meet our dependencies
if ( count( $unmetCoreDependencies ) > 0 ) :
	require_once ABSPATH . WPINC . "/class-feed.php";
endif;

// Dependencies: modules packaged with FeedWordPress plugin
$dir = dirname( __FILE__ );
require_once "{$dir}/feedwordpressadminpage.class.php";
require_once "{$dir}/feedwordpresssettingsui.class.php";
require_once "{$dir}/feedwordpressdiagnostic.class.php";
require_once "{$dir}/admin-ui.php";
require_once "{$dir}/template-functions.php";
require_once "{$dir}/feedwordpresssyndicationpage.class.php";
require_once "{$dir}/compatability.php"; // Legacy API
require_once "{$dir}/syndicatedpost.class.php";
require_once "{$dir}/syndicatedlink.class.php";
require_once "{$dir}/feedwordpresshtml.class.php";
require_once "{$dir}/inspectpostmeta.class.php";
require_once "{$dir}/syndicationdataqueries.class.php";
require_once "{$dir}/extend/SimplePie/feedwordpie.class.php";
require_once "{$dir}/extend/SimplePie/feedwordpie_cache.class.php";
require_once "{$dir}/extend/SimplePie/feedwordpie_item.class.php";
require_once "{$dir}/extend/SimplePie/feedwordpie_file.class.php";
require_once "{$dir}/extend/SimplePie/feedwordpie_parser.class.php";
require_once "{$dir}/extend/SimplePie/feedwordpie_content_type_sniffer.class.php";
require_once "{$dir}/feedwordpressrpc.class.php";
require_once "{$dir}/feedwordpresshttpauthenticator.class.php";
require_once "{$dir}/feedwordpresslocalpost.class.php";

####################################################################################
## GLOBAL PARAMETERS ###############################################################
####################################################################################

// Get the path relative to the plugins directory in which FWP is stored
preg_match (
	'|'.preg_quote( WP_PLUGIN_DIR ) . '/(.+)$|',
	dirname( __FILE__ ),
	$ref
);

if ( isset( $ref[1] ) ) :
	$fwp_path = $ref[1];
else : // Something went wrong. Let's just guess.
	$fwp_path = 'feedwordpress';
endif;

####################################################################################
## FEEDWORDPRESS: INITIALIZE OBJECT AND FILTERS ####################################
####################################################################################

$feedwordpress = new FeedWordPress;
if ( ! $feedwordpress->needs_upgrade() ) : // only work if the conditions are safe!
	$feedwordpress->add_filters();

	# Inbound XML-RPC update methods
	$feedwordpressRPC = new FeedWordPressRPC;

	# Outbound XML-RPC ping reform
	remove_action( 'publish_post', 'generic_ping' ); // WP 1.5.x
	remove_action( 'do_pings', 'do_all_pings', 10, 1 ); // WP 2.1, 2.2
	remove_action( 'publish_post', '_publish_post_hook', 5, 1 ); // WP 2.3

	add_action( 'publish_post', 'fwp_publish_post_hook', 5, 1 );
	add_action( 'do_pings', 'fwp_do_pings', 10, 1 );
	add_action( 'feedwordpress_update', 'fwp_hold_pings' );
	add_action( 'feedwordpress_update_complete', 'fwp_release_pings' );

else :
	# Hook in the menus, which will just point to the upgrade interface
	add_action( 'admin_menu', array( $feedwordpress, 'add_pages' ) );
endif; // if ( !FeedWordPress::needs_upgrade())

register_deactivation_hook( __FILE__, 'feedwordpress_deactivate' );

/**
 * Hook to deactivate FeedWordPress.
 *
 * @return int|false @see wp_clear_scheduled_hook()
 */
function feedwordpress_deactivate() {
	wp_clear_scheduled_hook( 'fwp_scheduled_update_checkin' );
} /* feedwordpress_deactivate () */

################################################################################
## LOGGING FUNCTIONS: log status updates to error_log if you want it ###########
################################################################################

/**
 * Divides bytes into units of higher magnitude (e.g KB, MB, etc).
 *
 * @param  int|string $quantity Quantity in bytes to be displayed. Can be a string that only includes numeric digits.
 *
 * @return string Formatted string with quantity and unit.
 *
 * @deprecated use the WordPress built-in `size_format()` function instead! (gwyneth 20230918)
 */
function debug_out_human_readable_bytes( $quantity ) {
	if ( ! is_numeric( $quantity ) ) :
		return __( "(wrong quantity)" );
	endif;
	$quantity = intval( $quantity );
	$magnitude = 'B';
	/** @var array Two-letter abbreviations of the units in increasing magnitude. */
	$orders = array( 'KB', 'MB', 'GB', 'TB' );
	while ( ( $quantity > ( 1024 * 100 ) ) and ( count( $orders ) > 0 ) ) :
		$quantity = floor( $quantity / 1024 );
		$magnitude = array_shift( $orders );
	endwhile;
	return "{$quantity} {$magnitude}";
}

function debug_out_feedwordpress_footer() {
	if ( FeedWordPressDiagnostic::is_on('memory_usage') ) :
		if ( function_exists('memory_get_usage') ) :
			FeedWordPress::diagnostic( 'memory_usage', "Memory: Current usage: " . size_format( memory_get_usage() ) );
		endif;
		if ( function_exists('memory_get_peak_usage') ) :
			FeedWordPress::diagnostic('memory_usage', "Memory: Peak usage: " . size_format( memory_get_peak_usage() ) );
		endif;
	endif;
} /* debug_out_feedwordpress_footer() */

################################################################################
## FILTERS: syndication-aware handling of post data for templates and feeds ####
################################################################################

$feedwordpress_the_syndicated_content = null;
$feedwordpress_the_original_permalink = null;

function feedwordpress_preserve_syndicated_content ($text) {
	global $feedwordpress_the_syndicated_content;

	$p = new FeedWordPressLocalPost;
	if ( ! $p->is_exposed_to_formatting_filters()) :
		$feedwordpress_the_syndicated_content = $text;
	else :
		$feedwordpress_the_syndicated_content = null;
	endif;
	return $text;
}

function feedwordpress_restore_syndicated_content ($text) {
	global $feedwordpress_the_syndicated_content;

	if ( !is_null($feedwordpress_the_syndicated_content) ) :
		$text = $feedwordpress_the_syndicated_content;
	endif;

	return $text;
}

function feedwordpress_item_feed_data () {
	// In a post context....
	if (is_syndicated()) :
?>
<source>
	<title><?php print esc_html( get_syndication_source() ); ?></title>
	<link rel="alternate" type="text/html" href="<?php print esc_url( get_syndication_source_link() ); ?>" />
	<link rel="self" href="<?php print esc_url( get_syndication_feed() ); ?>" />
<?php
	$id = get_syndication_feed_guid();
	if ( strlen( $id ) > 0 ) :
?>
	<id><?php print esc_xml( $id ); ?></id>
<?php
	endif;
	$updated = get_feed_meta('feed/updated');
	if ( strlen( $updated ) > 0 ) : ?>
	<updated><?php print esc_xml( $updated ); ?></updated>
<?php
	endif;
?>
</source>
<?php
	endif;
}

/**
 * syndication_permalink: Allow WordPress to use the original remote URL of
 * syndicated posts as their permalink. Can be turned on or off by by setting in
 * Syndication => Posts & Links. Saves the old internal permalink in a global
 * variable for later use.
 *
 * @param string $permalink The internal permalink
 * @param mixed|null $post Post object
 * @param bool $leavename Unused
 * @param bool $sample Unused
 * @return string The new permalink. Same as the old if the post is not
 *	syndicated, or if FWP is set to use internal permalinks, or if the post
 *	was syndicated, but didn't have a proper permalink recorded.
 *
 * @uses SyndicatedLink::setting()
 * @uses get_syndication_permalink()
 * @uses get_syndication_feed_object()
 * @uses url_to_postid()
 * @global $id
 * @global $feedwordpress_the_original_permalink
 */
function syndication_permalink($permalink = '', $post = null, $leavename = false, $sample = false ) {
	global $id;
	global $feedwordpress_the_original_permalink;

	// Save the local permalink in case we need to retrieve it later.
	$feedwordpress_the_original_permalink = $permalink;

	if (is_object($post) and isset($post->ID) and !empty($post->ID)) :
		// Use the post ID we've been provided with.
		$postId = $post->ID;
	elseif (is_string($permalink) and strlen($permalink) > 0) :
		// Map this permalink to a post ID so we can get the correct
		// permalink even outside of the Post Loop. Props Björn.
		$postId = url_to_postid($permalink);
	else :
		// If the permalink string is empty but Post Loop context
		// provides an id.
		$postId = $id;
	endif;

	$munge = false;
	$link = get_syndication_feed_object($postId);
	if (is_object($link)) :
		$munge = ($link->setting('munge permalink', 'munge_permalink', 'yes') != 'no');
	endif;

	if ($munge):
		$uri = get_syndication_permalink($postId);
		$permalink = ((strlen($uri) > 0) ? $uri : $permalink);
	endif;
	return $permalink;
} /* function syndication_permalink () */

/**
 * syndication_permalink_escaped: Escape XML special characters in syndicated
 * permalinks when used in feed contexts and HTML display contexts.
 *
 * @param string $permalink
 * @return string
 *
 * @uses is_syndicated()
 * @uses FeedWordPress::munge_permalinks()
 *
 */
function syndication_permalink_escaped ($permalink) {
	/* FIXME: This should review link settings not just global settings */
	if (is_syndicated() and FeedWordPress::munge_permalinks()) :
		// This is a foreign link; WordPress can't vouch for its not
		// having any entities that need to be &-escaped. So we'll do
		// it here.
		$permalink = esc_html($permalink);
	endif;
	return $permalink;
} /* function syndication_permalink_escaped() */

/**
 * syndication_comments_feed_link: Escape XML special characters in comments
 * feed links
 *
 * @param string $link
 * @return string
 *
 * @uses is_syndicated()
 * @uses FeedWordPress::munge_permalinks()
 */
function syndication_comments_feed_link ($link) {
	global $feedwordpress_the_original_permalink;

	if (is_syndicated() and FeedWordPress::munge_permalinks()) :
		// If the source post provided a comment feed URL using
		// wfw:commentRss or atom:link/@rel="replies" we can make use of
		// that value here.
		$source = get_syndication_feed_object();
		$replacement = null;

		if (is_object($source) && $source->setting('munge comments feed links', 'munge_comments_feed_links', 'yes') != 'no') :
			$commentFeeds = get_post_custom_values('wfw:commentRSS');
			if (
				is_array($commentFeeds)
				and (count($commentFeeds) > 0)
				and (strlen($commentFeeds[0]) > 0)
			) :
				$replacement = $commentFeeds[0];

				// This is a foreign link; WordPress can't vouch for its not
				// having any entities that need to be &-escaped. So we'll do it
				// here.
				$replacement = esc_html($replacement);
			endif;
		endif;

		if (is_null($replacement)) :
			// Q: How can we get the proper feed format, since the
			// format is, stupidly, not passed to the filter?
			// A: Kludge kludge kludge kludge!
			$fancy_permalinks = ('' != get_option('permalink_structure'));
			if ($fancy_permalinks) :
				preg_match('|/feed(/([^/]+))?/?$|', $link, $ref);

				$format = (isset($ref[2]) ? $ref[2] : '');
				if (strlen($format) == 0) : $format = get_default_feed(); endif;

				$replacement = trailingslashit($feedwordpress_the_original_permalink) . 'feed';
				if ($format != get_default_feed()) :
					$replacement .= '/'.$format;
				endif;
				$replacement = user_trailingslashit($replacement, 'single_feed');
			else :
				// No fancy permalinks = no problem
				// WordPress doesn't call get_permalink() to
				// generate the comment feed URL, so the
				// comments feed link is never munged by FWP.
			endif;
		endif;

		if ( !is_null($replacement)) : $link = $replacement; endif;
	endif;
	return $link;
} /* function syndication_comments_feed_link() */

	require_once("{$dir}/feedwordpress.pings.functions.php");

	require_once("{$dir}/feedwordpress.wp-admin.post-edit.functions.php");

################################################################################
## class FeedWordPressBoilerplateReformatter ###################################
################################################################################

require_once("{$dir}/feedwordpressboilerplatereformatter.class.php");
require_once("{$dir}/feedwordpressboilerplatereformatter.shortcode.functions.php");

################################################################################
## class FeedWordPress #########################################################
################################################################################

// class FeedWordPress: handles feed updates and plugs in to the XML-RPC interface
class FeedWordPress {
	var $strip_attrs = array (
		      array('[a-z]+', 'style'),
		      array('[a-z]+', 'target'),
	);
	var $uri_attrs = array (
			array('a', 'href'),
			array('applet', 'codebase'),
			array('area', 'href'),
			array('blockquote', 'cite'),
			array('body', 'background'),
			array('del', 'cite'),
			array('form', 'action'),
			array('frame', 'longdesc'),
			array('frame', 'src'),
			array('iframe', 'longdesc'),
			array('iframe', 'src'),
			array('head', 'profile'),
			array('img', 'longdesc'),
			array('img', 'src'),
			array('img', 'usemap'),
			array('input', 'src'),
			array('input', 'usemap'),
			array('ins', 'cite'),
			array('link', 'href'),
			array('object', 'classid'),
			array('object', 'codebase'),
			array('object', 'data'),
			array('object', 'usemap'),
			array('q', 'cite'),
			array('script', 'src')
	);

	var $feeds = null;
	var $feedurls = null;
	var $httpauth = null;

	/**
	 * FeedWordPress::__construct (): Construct FeedWordPress singleton object
	 * and retrieves a list of feeds for later reference.
	 *
	 * @uses FeedWordPressHTTPAuthenticator
	 */
	public function __construct () {
		$this->feeds = array ();
		$this->feedurls  = array();
		$links = FeedWordPress::syndicated_links();

		if ($links): foreach ($links as $link):
			$id = intval($link->link_id);
			$url = $link->link_rss;

			// Store for later reference.
			$this->feeds[$id] = $id;
			if (strlen($url) > 0) :
				$this->feedurls[$url] = $id;
			endif;
		endforeach; endif;

		// System-related event hooks
		add_filter('cron_schedules', array($this, 'cron_schedules'), 10, 1);

		// FeedWordPress-related event hooks
		add_filter('feedwordpress_update_complete', array($this, 'process_retirements'), 1000, 1);

		$this->httpauth = new FeedWordPressHTTPAuthenticator;
	} /* FeedWordPress::__construct () */

	/**
	 * FeedWordPress::add_filters() connects FeedWordPress to WordPress lifecycle
	 * events by setting up action and filter hooks.
	 *
	 * @uses get_option()
	 * @uses add_filter()
	 * @uses add_action()
	 * @uses remove_filter()
	 */
	public function add_filters () {
		# Syndicated items are generally received in output-ready (X)HTML and
		# should not be folded, crumpled, mutilated, or spindled by WordPress
		# formatting filters. But we don't want to interfere with filters for
		# any locally-authored posts, either.
		#
		# What WordPress should really have is a way for upstream filters to
		# stop downstream filters from running at all. Since it doesn't, and
		# since a downstream filter can't access the original copy of the text
		# that is being filtered, what we will do here is (1) save a copy of the
		# original text upstream, before any other filters run, and then (2)
		# retrieve that copy downstream, after all the other filters run, *if*
		# this is a syndicated post

		add_filter('the_content', 'feedwordpress_preserve_syndicated_content', -10000);
		add_filter('the_content', 'feedwordpress_restore_syndicated_content', 10000);

		add_action('atom_entry', 'feedwordpress_item_feed_data');

		# Filter in original permalinks if the user wants that
		add_filter('post_link', 'syndication_permalink', /*priority=*/ 1, /*arguments=*/ 3);
		add_filter('post_type_link', 'syndication_permalink', /*priority=*/ 1, /*arguments=*/ 4);

		# When foreign URLs are used for permalinks in feeds or display
		# contexts, they need to be escaped properly.
		add_filter('the_permalink', 'syndication_permalink_escaped');
		add_filter('the_permalink_rss', 'syndication_permalink_escaped');

		add_filter('post_comments_feed_link', 'syndication_comments_feed_link');

		# WTF? By default, wp_insert_link runs incoming link_url and link_rss
		# URIs through default filters that include `wp_kses()`. But `wp_kses()`
		# just happens to escape any occurrence of & to &amp; -- which just
		# happens to fuck up any URI with a & to separate GET parameters.
		remove_filter('pre_link_rss', 'wp_filter_kses');
		remove_filter('pre_link_url', 'wp_filter_kses');

		# Boilerplate functionality: hook in to title, excerpt, and content to add boilerplate text
		$hookOrder = get_option('feedwordpress_boilerplate_hook_order', FEEDWORDPRESS_BOILERPLATE_DEFAULT_HOOK_ORDER);
		add_filter(
		/*hook=*/ 'the_title',
		/*function=*/ 'add_boilerplate_title',
		/*priority=*/ $hookOrder,
		/*arguments=*/ 2
		);
		add_filter(
		/*hook=*/ 'get_the_excerpt',
		/*function=*/ 'add_boilerplate_excerpt',
		/*priority=*/ $hookOrder,
		/*arguments=*/ 1
		);
		add_filter(
		/*hook=*/ 'the_content',
		/*function=*/ 'add_boilerplate_content',
		/*priority=*/ $hookOrder,
		/*arguments=*/ 1
		);
		add_filter(
		/*hook=*/ 'the_content_rss',
		/*function=*/ 'add_boilerplate_content',
		/*priority=*/ $hookOrder,
		/*arguments=*/ 1
		);

		# Admin menu
		add_action('admin_init', array($this, 'admin_init'));
		add_action('admin_menu', array($this, 'add_pages'));
		add_action('admin_notices', array($this, 'check_debug'));

		add_action('admin_menu', 'feedwordpress_add_post_edit_controls');
		add_action('save_post', 'feedwordpress_save_post_edit_controls');

		add_action('admin_footer', array($this, 'admin_footer'));

		add_action('syndicated_feed_error', array('FeedWordPressDiagnostic', 'feed_error'), 100, 3);

		add_action('wp_footer', 'debug_out_feedwordpress_footer', -100);
		add_action('admin_footer', 'debug_out_feedwordpress_footer', -100);

		# Cron-less auto-update. Hooray!
		$autoUpdateHook = $this->automatic_update_hook();
		if ( !is_null($autoUpdateHook)) :
			add_action($autoUpdateHook, array($this, 'auto_update'));
		endif;

		add_action('init', array($this, 'init'));
		add_action('wp_loaded', array($this, 'wp_loaded'));

		add_action('shutdown', array($this, 'email_diagnostic_log'));
		add_action('shutdown', array($this, 'feedwordpress_cleanup'));
		add_action('wp_dashboard_setup', array($this, 'dashboard_setup'));

		# Default sanitizers
		add_filter('syndicated_item_content', array('SyndicatedPost', 'resolve_relative_uris'), 0, 2);
		add_filter('syndicated_item_content', array('SyndicatedPost', 'sanitize_content'), 0, 2);

		add_action('plugins_loaded', array($this, 'admin_api'));
		add_action('all_admin_notices', array($this, 'all_admin_notices'));

		// Use the cache settings that we want, from a static method
		add_filter('wp_feed_cache_transient_lifetime', array(get_class($this), 'cache_lifetime'));

	} /* FeedWordPress::add_filters () */

	################################################################################
	## ADMIN MENU ADD-ONS: register Dashboard management pages #####################
	################################################################################

	/**
	 * FeedWordPress::add_pages(): set up WordPress admin interface pages thru
	 * hooking in Syndication menu and submenus
	 *
	 * @uses FeedWordPress::menu_cap()
	 * @uses FeedWordPress::path()
	 * @uses add_menu_page()
	 * @uses add_submenu_page()
	 * @uses do_action()
	 * @uses add_filter()
	 */
	public function add_pages()
	{
		$menu_cap = FeedWordPress::menu_cap();
		$settings_cap = FeedWordPress::menu_cap( /*sub=*/ true );
		$syndicationMenu = FeedWordPress::path( 'syndication.php' );

		add_menu_page(
			/* page_title */ 'Syndicated Sites',
			/* menu_title */ 'Syndication',
			/* capability */ $menu_cap,
			/* menu_slug  */ $syndicationMenu,
			/* function   */ NULL,
			/* icon_url   */ $this->plugin_dir_url( /* 'assets/images/feedwordpress-tiny.png' */ 'assets/images/icon.svg' )
			/* position   */
		);

		do_action( 'feedwordpress_admin_menu_pre_feeds', $menu_cap, $settings_cap );
		add_submenu_page(
			/* parent_slug */ $syndicationMenu,
			/* page_title  */ 'Syndicated Sites',
			/* menu_title  */ 'Syndicated Sites',
			/* capability  */ $settings_cap,
			/* menu_slug   */ $syndicationMenu,
			/* function    */
		);

		do_action('feedwordpress_admin_menu_pre_feeds', $menu_cap, $settings_cap);
		add_submenu_page(
			$syndicationMenu, 'Syndicated Feeds & Updates', 'Feeds & Updates',
			$settings_cap, FeedWordPress::path('feeds-page.php')
		);

		do_action('feedwordpress_admin_menu_pre_posts', $menu_cap, $settings_cap);
		add_submenu_page(
			$syndicationMenu, 'Syndicated Posts & Links', 'Posts & Links',
			$settings_cap, FeedWordPress::path('posts-page.php')
		);

		do_action('feedwordpress_admin_menu_pre_authors', $menu_cap, $settings_cap);
		add_submenu_page(
			$syndicationMenu, 'Syndicated Authors', 'Authors',
			$settings_cap, FeedWordPress::path('authors-page.php')
		);

		do_action('feedwordpress_admin_menu_pre_categories', $menu_cap, $settings_cap);
		add_submenu_page(
			$syndicationMenu, 'Categories & Tags', 'Categories & Tags',
			$settings_cap, FeedWordPress::path('categories-page.php')
		);

		do_action('feedwordpress_admin_menu_pre_performance', $menu_cap, $settings_cap);
		add_submenu_page(
			$syndicationMenu, 'FeedWordPress Performance', 'Performance',
			$settings_cap, FeedWordPress::path('performance-page.php')
		);

		do_action('feedwordpress_admin_menu_pre_diagnostics', $menu_cap, $settings_cap);
		add_submenu_page(
			$syndicationMenu, 'FeedWordPress Diagnostics', 'Diagnostics',
			$settings_cap, FeedWordPress::path('diagnostics-page.php')
		);

		add_filter('page_row_actions', array($this, 'row_actions'), 10, 2);
		add_filter('post_row_actions', array($this, 'row_actions'), 10, 2);
	} /* function FeedWordPress::add_pages () */

	public function check_debug() {
		// This is a horrible fucking kludge that I have to do because the
		// admin notice code is triggered before the code that updates the
		// setting.
		$feedwordpress_debug = FeedWordPress::param( 'feedwordpress_debug', get_option( 'feedwordpress_debug' ) );

		FeedWordPressSettingsUI::get_template_part( 'notice-debug-mode', $feedwordpress_debug, 'html' );
	} /* function FeedWordPress::check_debug () */

	/**
	 * FeedWordPress::subscribed (): Check whether a feed is currently in the
	 * subscription list for FeedWordPress.
	 *
	 * @param mixed $id Numeric ID of a WordPress link object or URL of a feed
	 * @return bool TRUE if currently subscribed; FALSE otherwise.
	 */
	public function subscribed ($id) {
		return (isset($this->feedurls[$id]) or isset($this->feeds[$id]));
	} /* FeedWordPress::subscribed () */

	/**
	 * FeedWordPress::subscription (): Get the SyndicatedLink object for a
	 * given URL or numeric ID, if we have either an active subscription to
	 * it; or a de-activated subscription.
	 *
	 * @param mixed $which Numeric ID for a WordPress link object or string URL for a feed
	 * @return mixed SyndicatedLink object if subscription is found; null if not
	 */
	public function subscription ($which) {
		$sub = null;

		if (is_string($which) and isset($this->feedurls[$which])) :
			$which = $this->feedurls[$which];
		endif;

		if (isset($this->feeds[$which])) :
			$sub = $this->feeds[$which];
		endif;

		// If it's not in the in-memory cache already, try to load it from DB.
		// This is necessary to fill requests for subscriptions that we don't
		// cache in memory, e.g. for deactivated feeds.
		if (is_null($sub)) :
			$sub = get_bookmark($which);
		endif;

		// Load 'er up if you haven't already.
		if ( !is_null($sub) and !($sub InstanceOf SyndicatedLink)) :
			$link = new SyndicatedLink($sub);
			$this->feeds[$which] = $link;
			$sub = $link;
		endif;

		return $sub;
	} /* FeedWordPress::subscription () */

	/** function update (): polls for updates on one or more Contributor feeds

	@desc
	# Arguments:

	- $uri (string): Either the URI of the feed to poll, the URI of the
	    (human-readable) website whose feed you want to poll, or null.

	    If $uri is null, then FeedWordPress will poll any feeds that are
	    ready for polling. It will not poll feeds that are marked as
	    "Invisible" Links (signifying that the subscription has been
	    de-activated), or feeds that are not yet stale according to their
	    TTL setting (which is either set in the feed, or else set
	    randomly within a window of 30 minutes - 2 hours).

	# Returns:

	-	Normally returns an associative array, with 'new' => the number
	    of new posts added during the update, and 'updated' => the number
	    of old posts that were updated during the update. If both numbers
	    are zero, there was no change since the last poll on that URI.

	-	Returns null if URI it was passed was not a URI that this
	    installation of FeedWordPress syndicates.

	# Effects:

	-	One or more feeds are polled for updates

	-   If the feed Link does not have a hardcoded name set, its Link
	    Name is synchronized with the feed's title element

	-   If the feed Link does not have a hardcoded URI set, its Link URI
	    is synchronized with the feed's human-readable link element

	-   If the feed Link does not have a hardcoded description set, its
	    Link Description is synchronized with the feed's description,
	    tagline, or subtitle element.

	-   The time of polling is recorded in the feed's settings, and the
	    TTL (time until the feed is next available for polling) is set
	    either from the feed (if it is supplied in the ttl or syndication
	    module elements) or else from a randomly-generated time window
	    (between 30 minutes and 2 hours).

	-   New posts from the polled feed are added to the WordPress store.

	-   Updates to existing posts since the last poll are mirrored in the
	    WordPress store.

	@param string|null $uri Either the URI of the feed to poll, the URI of the (human-readable) website whose feed you want to poll, or null.
	@param mixed|null $crash_ts Unknown purpose.
	@return array|null Associative array, with 'new' => # of new posts added during update, and 'updated' => # of old posts that were updated. If both are zero, there was no change since çast update.
	*/
	public function update( $uri = null, $crash_ts = null ) {
		if ( FeedWordPress::needs_upgrade() ) : // Will make duplicate posts if we don't hold off
			return null;
		endif;

		if ( ! is_null( $uri ) and $uri != '*' ) :
			$uri = trim( $uri );
		else : // Update all
			if ( $this->update_hooked ) :
				$diag = $this->update_hooked;
			else :
				$diag = 'Initiating a MANUAL check-in on the update schedule at ' . date( 'r', time() );
			endif;
			$this->diagnostic( 'update_schedule:check', $diag );

			update_option( 'feedwordpress_last_update_all', time() );
		endif;

		do_action( 'feedwordpress_update', $uri );

		if ( is_null( $crash_ts ) ) :
			$crash_ts = $this->crash_ts();
		endif;

		// Randomize order for load balancing purposes
		$feed_set = array_keys( $this->feeds );
		shuffle( $feed_set );

		$updateWindow = (int) get_option( 'feedwordpress_update_window', DEFAULT_UPDATE_PERIOD ) * 60 /* sec/min */;
		$interval = (int) get_option( 'feedwordpress_freshness', FEEDWORDPRESS_FRESHNESS_INTERVAL );
		$portion = max(
			(int) ceil( count( $feed_set ) / ( $updateWindow / $interval ) ),
			10
		);

		$max_polls = apply_filters(
			'feedwordpress_polls_per_update',
			get_option(
				'feedwordpress_polls_per_update', $portion
			),
			$uri
		);

		$feed_set = apply_filters( 'feedwordpress_update_feeds', $feed_set, $uri );

		// Loop through and check for new posts
		$delta = null;
		$remaining = $max_polls;
		foreach ( $feed_set as $feed_id)  :

			$feed = $this->subscription( $feed_id );
			// Try to catch a very unusual condition where the $feed comes as NULL (gwyneth 20230919)
			if ( ! empty( $feed ) ) :
				$this->diagnostic( 'update_schedule', "Feed " .  $feed_id . " returned an empty feed" );
			endif;

			// Has this process overstayed its welcome?
			if (
				// Over time limit?
				( ! is_null( $crash_ts ) and ( time() > $crash_ts ) )

				// Over feed count?
				or ( 0 == $remaining )
			) :
				break;
			endif;

			$pinged_that = ( is_null( $uri ) or ( $uri == '*' ) or in_array( $uri, array( $feed->uri(), $feed->homepage() ) ) );

			if ( ! is_null( $uri ) ) : // A site-specific ping always updates
				$timely = true;
			else :
				$timely = $feed->stale();
			endif;

			// If at least one feed was hit for updating...
			if ( $pinged_that and is_null( $delta ) ) :
				// ... don't return error condition
				$delta = array( 'new' => 0, 'updated' => 0, 'stored' => 0 );
			endif;

			if ( $pinged_that and $timely ) :
				$remaining = $remaining - 1;

				do_action( 'feedwordpress_check_feed', $feed->settings );
				$start_ts = time();
				$added = $feed->poll( $crash_ts );
				do_action( 'feedwordpress_check_feed_complete', $feed->settings, $added, time() - $start_ts );

				if ( is_array( $added ) ) : // Success
					foreach ( $added as $key => $count ) :
						$delta[ $key ] += $added[ $key ];
					endforeach;
				endif;
			endif;
		endforeach;

		do_action( 'feedwordpress_update_complete', $delta );
		return $delta;
	} /* FeedWordPress::update () */

	/**
	 * Checks if we're over the update time limit.
	 *
	 * @todo is returning null advisable? (gwyneth 20230916)
	 *
	 * @param  int|null $default Default value, called when the corresponding FWP option is not set.
	 *
	 * @return int|null
	 */
	public function crash_ts( $default = null ) {
		$crash_dt = (int) get_option( 'feedwordpress_update_time_limit', 0 );
		if ( $crash_dt > 0 ) :
			$crash_ts = time() + $crash_dt;
		else :
			$crash_ts = $default;
		endif;
		return $crash_ts;
	} /* FeedWordPress::crash_ts () */


	/**
	 * Checks if we have a secret key set in the options; if not, generate one.
	 *
	 * @return string Secret key, either from options or auto-generated.
	 *
	 * @todo Only 6 characters? That is rather easy to guess... Also, uniqid() can return 13 or 23
	 * characters, what's the point of using MD5 in this case? (gwyneth 20230916)
	 */
	public function secret_key() {
		$secret = get_option( 'feedwordpress_secret_key', false );
		if ( ! $secret) : // Generate random key.
			$secret = substr( md5( uniqid( microtime() ) ), 0, 6 );
			update_option( 'feedwordpress_secret_key', $secret );
		endif;
		return $secret;
	} /* FeedWordPress::secret_key () */

	/**
	 * Returns true if we have set a secret FWP key.
	 *
	 * @return bool  True if we have a secret key, false otherwise
	 *
	 * @uses MyPHP::request()
	 */
	public function has_secret() {
		return ( MyPHP::request( 'feedwordpress_key' ) == $this->secret_key() );
	} /* FeedWordPress::has_secret () */

	var $update_hooked = null;

	/**
	 * Activates the hook for automatic plugin updates, if requested.
	 *
	 * @param  Array $params Parameters to send to the hook.
	 *
	 * @return string Represents the hook's name. There is a slight chance that this is null, though. (gwyneth 20230916)
	 *
	 * @uses wp_parse_args()
	 * @uses MyPHP::request()
	 */
	public function automatic_update_hook( $params = array() ) {
		$params = wp_parse_args( $params, array( // Defaults
			'setting only' => false,
		));
		$hook = get_option( 'feedwordpress_automatic_updates', null );
		$method = 'FeedWordPress option';

		// Allow for forced behavior in testing.
		if (
			! $params['setting only']
			and $this->has_secret()
			and MyPHP::request( 'automatic_update' )
		) :
			$hook = MyPHP::request( 'automatic_update') ;
			$method = 'URL parameter';
		endif;

		$exact = $hook; // Before munging

		if ( !! $hook) :
			if ( $hook == 'init' or $hook == 'wp_loaded' ) : // Re-map init to wp_loaded
				$hook = ( $params['setting only'] ? 'init' : 'wp_loaded' );

			// Constrain possible values. If it's not an init or wp_loaded, it's a shutdown
			else :
				$hook = 'shutdown';
			endif;
		endif;

		if ( $hook ) :
			$this->update_hooked = "Initiating an AUTOMATIC CHECK FOR UPDATES ON PAGE LOAD " . $hook . " due to " . $method . " = " . trim( $this->val( $exact ) );
		endif;

		return $hook;
	} /* FeedWordPress::automatic_update_hook () */

	public function last_update_all () {
		$last = get_option('feedwordpress_last_update_all');
		if ( $this->has_secret() and FeedWordPress::param('automatic_update') ) :
			$last = 1; // A long, long time ago.
		elseif ( $this->has_secret() and FeedWordPress::param('last_update_all') ) :
			$last = FeedWordPress::param( 'last_update_all' );
		endif;
		return $last;
	} /* FeedWordPress::last_update_all () */

	public function force_update_all () {
		return ($this->has_secret() and FeedWordPress::param( 'force_update_feeds' ));
	} /* FeedWordPress::force_update_all () */

	public function stale () {
		if ( !is_null($this->automatic_update_hook())) :
			// Do our best to avoid possible simultaneous
			// updates by getting up-to-the-minute settings.

			$last = $this->last_update_all();

			// If we haven't updated all yet, give it a time window
			if (false === $last) :
				$ret = false;
				update_option('feedwordpress_last_update_all', time());

			// Otherwise, check against freshness interval
			elseif (is_numeric($last)) : // Expect a timestamp
				$freshness = get_option('feedwordpress_freshness');
				if (false === $freshness) : // Use default
					$freshness = FEEDWORDPRESS_FRESHNESS_INTERVAL;
				endif;
				$ret = ( (time() - $last) > $freshness );

			// This should never happen.
			else :
				FeedWordPressDiagnostic::critical_bug('FeedWordPress::stale::last', $last, __LINE__, __FILE__);
			endif;

		else :
			$ret = false;
		endif;
		return $ret;
	} /* FeedWordPress::stale() */

	static function admin_init () {
		// WordPress 3.5+ compat: the WP devs are in the midst of removing Links from the WordPress core. Eventually we'll have to deal
		// with the possible disappearance of the wp_links table as a whole; but in the meantime, we just need to turn on the interface
		// element to avoid problems with user capabilities that presume the presence of the Links Manager in the admin UI.
		global $post_type;

		if ( !intval(get_option('link_manager_enabled', false))) :
			update_option('link_manager_enabled', true);
		endif;

		if (defined('FEEDWORDPRESS_PREPARE_TO_ZAP') and FEEDWORDPRESS_PREPARE_TO_ZAP) :
			$post_id = FEEDWORDPRESS_PREPARE_TO_ZAP;
			$sendback = wp_get_referer();
			if (
				! $sendback
				or strpos( $sendback, 'post.php' ) !== false
				or strpos( $sendback, 'post-new.php' ) !== false
			) :
				if ( 'attachment' == $post_type ) :		// where does this come from?? I put it as a global...(gwyneth 20230916)
					$sendback = admin_url( 'upload.php' );
				else :
					$sendback = admin_url( 'edit.php' );
					$sendback .= ( ! empty( $post_type ) ) ? '?post_type=' . $post_type : '';
				endif;
			else :
				$sendback = esc_url( remove_query_arg( array('trashed', 'untrashed', 'deleted', 'zapped', 'unzapped', 'ids'), $sendback ) );
			endif;

			// Make sure we have a post corresponding to this ID.
			$post = $post_type = $post_type_object = null;		// whatever $post_type_object might be, it's not being used! (gwyneth 20230920)
			if ( $post_id ) :
				$post = get_post( $post_id );
			endif;

			if ( $post ) :
				$post_type = $post->post_type;
			endif;
			$p = get_post( $post_id );

			if ( ! $post ) :
				wp_die( esc_html__( 'The item you are trying to zap no longer exists.' ) );
			endif;

			if ( ! current_user_can( 'delete_post', $post_id ) ) :
				wp_die( esc_html__( 'You are not allowed to zap this item.' ) );
			endif;

			if ( $user_id = wp_check_post_lock( $post_id ) ) :
				if ( is_numeric( $user_id ) and function_exists( 'get_userdata' ) ) :
					$user = get_userdata( (int) $user_id );
					wp_die( esc_html( sprintf( __( 'You cannot retire this item. %s is currently editing.' ), $user->display_name ) ) );
				else :
					wp_die( esc_html__( 'You cannot retire this item. Someone is currently editing.' ) );
				endif;
			endif;

			if (FeedWordPress::param( 'fwp_post_delete' ) == 'zap') :
				FeedWordPress::diagnostic('syndicated_posts', 'Zapping existing post # '.$p->ID.' "'.$p->post_title.'" due to user request.');

				$old_status = $post->post_status;

				set_post_field('post_status', 'fwpzapped', $post_id);
				wp_transition_post_status('fwpzapped', $old_status, $post);

				# Set up the post to have its content blanked on
				# next update if you do not undo the zapping.
				add_post_meta($post_id, '_feedwordpress_zapped_blank_me', 1, /*unique=*/ true);
				add_post_meta($post_id, '_feedwordpress_zapped_blank_old_status', $old_status, /*unique=*/ true);

				wp_redirect( esc_url_raw( add_query_arg( array('zapped' => 1, 'ids' => $post_id), $sendback ) ) );

			else :
				$old_status = get_post_meta($post_id, '_feedwordpress_zapped_blank_old_status', /*single=*/ true);

				set_post_field('post_status', $old_status, $post_id);
				wp_transition_post_status($old_status, 'fwpzapped', $post);

				# O.K., make sure this post does not get blanked
				delete_post_meta($post_id, '_feedwordpress_zapped_blank_me');
				delete_post_meta($post_id, '_feedwordpress_zapped_blank_old_status');

				wp_redirect( esc_url_raw( add_query_arg( array('unzapped' => 1, 'ids' => $post_id), $sendback ) ) );

			endif;

			// Intercept, don't pass on.
			exit;
		endif;
	} /* FeedWordPress::admin_init() */

	public function admin_api () {
		// This sucks, but WordPress doesn't give us any other way to
		// easily invoke a permanent-delete from a plugged in post
		// actions link. So we create a magic parameter, and when this
		// magic parameter is activated, the WordPress trashcan is
		// temporarily de-activated.

		if (FeedWordPress::param( 'fwp_post_delete' ) == 'nuke') :
			// Get post ID #
			$post_id = FeedWordPress::param( 'post' );
			if ( ! $post_id) :
				$post_id = FeedWordPress::param( 'post_ID' );
			endif;

			// Make sure we've got the right nonce and all that.
			check_admin_referer('delete-post_' . $post_id);

			// If so, disable the trashcan.
			define('EMPTY_TRASH_DAYS', 0);
		elseif ( FeedWordPress::param( 'fwp_post_delete' ) == 'zap' || FeedWordPress::param( 'fwp_post_delete' ) == 'unzap' ) :
			// Get post ID #
			$post_id = FeedWordPress::param( 'post' );
			if ( ! $post_id) :
				$post_id = FeedWordPress::param( 'post_ID' );
			endif;

			// Make sure we've got the right nonce and all that
			check_admin_referer('delete-post_' . $post_id);

			// If so, get ready to intercept the call a little
			// further down the line.
			define('FEEDWORDPRESS_PREPARE_TO_ZAP', $post_id);

		endif;

	} /* FeedWordPress::admin_api () */

	public function all_admin_notices () {
		if (FeedWordPress::param( 'zapped' )) :
			$n = intval( FeedWordPress::param( 'zapped' ) );
?>
<div id="message" class="updated"><p><?php print esc_html( $n ); ?> syndicated item<?php print esc_html( $n != 1 ? 's' : '' ); ?> zapped. <strong>These items will not be re-syndicated.</strong> If this was a mistake, you must <strong>immediately</strong> Un-Zap them in the Zapped items section to avoid losing the data.</p></div>
<?php
		endif;
		if ( FeedWordPress::param( 'unzapped' ) ) :
			$n = intval( FeedWordPress::param( 'unzapped' ) );
?>
<div id="message" class="updated"><p><?php print esc_html( $n ); ?> syndicated item<?php print esc_html( $n != 1 ? 's' : '' ) ?> un-zapped and restored to normal.</p></div>
<?php
		endif;
	} /* FeedWordPress::all_admin_notices () */

	public function process_retirements ($delta) {
		update_option('feedwordpress_process_zaps', 1);

		return $delta;
	}

	public function feedwordpress_cleanup () {
		if (get_option('feedwordpress_process_zaps', null)) :
			$q = new WP_Query(array(
			'fields' => '_synfrom',
			'post_status' => 'fwpzapped',
			'ignore_sticky_posts' => true,
			'meta_key' => '_feedwordpress_zapped_blank_me',
			'meta_value' => 1,
			));

			if ($q->have_posts()) :
				foreach ($q->posts as $p) :

					$post_id = $p->ID;
					$revisions = wp_get_post_revisions($post_id, array("check_enabled" => false));

					# Now nuke the content of the post & its revisions
					set_post_field('post_content', '', $post_id);
					set_post_field('post_excerpt', '', $post_id);

					foreach ($revisions as $rev) :
						set_post_field('post_content', '', $rev->ID);
						set_post_field('post_excerpt', '', $rev->ID);
					endforeach;

					# Un-tag it for blanking.
					delete_post_meta($p->ID, '_feedwordpress_zapped_blank_me');

					# Don't remove old_status indicator. A later
					# update from the feed may cause us to once
					# again have some content so we can un-zap.

				endforeach;
			endif;

			$q = new WP_Query(array(
			'fields' => '_synfrom',
			'post_status' => 'fwpzapped',
			'ignore_sticky_posts' => true,
			'meta_key' => '_feedwordpress_zapped_blank_me',
			'meta_value' => 2,
			));

			if ($q->have_posts()) :
				foreach ($q->posts as $p) :
					update_post_meta($p->ID, '_feedwordpress_zapped_blank_me', 1);
				endforeach;
			endif;

			update_option('feedwordpress_process_zaps', 0);
		endif;
	} /* FeedWordPress::feedwordpress_cleanup () */

	public function init () {

		// If this is a FeedWordPress admin page, queue up scripts for AJAX
		// functions that FWP uses. If it is a display page or a non-FWP admin
		// page, don't.
		wp_register_style('feedwordpress-elements', $this->plugin_dir_url( 'assets/css/feedwordpress-elements.css') );
		if (FeedWordPressSettingsUI::is_admin()) :
			// For JavaScript that needs to be generated dynamically
			add_action('admin_print_scripts', array('FeedWordPressSettingsUI', 'admin_scripts'));

			// For CSS that needs to be generated dynamically.
			add_action('admin_print_styles', array('FeedWordPressSettingsUI', 'admin_styles'));

			wp_enqueue_style('dashboard');
			wp_enqueue_style('feedwordpress-elements');
		endif;

		// These are a special post statuses for hiding posts that have
		// expired from the feed or been marked for permanent zapping by
		// the FWP admin.
		register_post_status('fwpretired', array(
		'label' => _x('Retired', 'post'),
		'label_count' => _n_noop('Retired <span class="count">(%s)</span>', 'Retired <span class="count">(%s)</span>'),
		'exclude_from_search' => true,
		'public' => false,
		'publicly_queryable' => false,
		'show_in_admin_all_list' => false,
		'show_in_admin_status_list' => true,
		));
		register_post_status('fwpzapped',  array(
		'label' => _x('Zapped', 'post'),
		'label_count' => _n_noop('Zapped <span class="count">(%s)</span>', 'Retired <span class="count">(%s)</span>'),
		'exclude_from_search' => true,
		'public' => false,
		'publicly_queryable' => false,
		'show_in_admin_all_list' => false,
		'show_in_admin_status_list' => true,
		));
		add_action(
			/*hook=*/ 'template_redirect',
			/*function=*/ array($this, 'redirect_retired'),
			/*priority=*/ -100
		);

		add_action('wp_ajax_fwp_feeds', array($this, 'fwp_feeds'));
		add_action('wp_ajax_fwp_feedcontents', array($this, 'fwp_feedcontents'));
		add_action('wp_ajax_fwp_xpathtest', array($this, 'fwp_xpathtest'));

		// Prepare for future translations... (gwyneth 20210714)
		// add_action('init', array($this, 'load_textdomain');
	} /* FeedWordPress::init() */

	/**
	 * FeedWordPress::wp_loaded (): Once all plugin and theme modules have been
	 * loaded and initialized (by actions on the init hook, etc.), check the HTTP
	 * request to see if we need to perform any special FeedWordPress-related
	 * actions.
	 *
	 * @since 2016.0614
	 */
	public function wp_loaded () {
		$this->clear_cache_magic_url();
		$this->update_magic_url();
	} /* FeedWordPress::wp_loaded () */

	/**
	 * FeedWordPress::cron_schedules(): Filter hook to add WP-Cron schedules
	 * that plugins like FeedWordPress can use to carry out scheduled events.
	 *
	 * @param array $schedules An associative array containing the current set of cron schedules (hourly, daily, etc.)
	 * @return array The same array, with a new entry ('fwp_checkin_interval') added to the list.
	 *
	 * @since 2017.1021
	 */
	public function cron_schedules ($schedules) {
		$interval = FEEDWORDPRESS_DEFAULT_CHECKIN_INTERVAL*60 /*sec/min*/;

		// add 'fwp_checkin_interval' to the existing set
		$schedules['fwp_checkin_interval'] = array(
		'interval' => $interval,
		'display' => 'FeedWordPress update schedule check-in',
		);

		return $schedules;
	} /* FeedWordPress::cron_schedules () */

	public function fwp_feeds () {
		$feeds = array();
		$feed_ids = $this->feeds;

		foreach ($feed_ids as $id) :
			$sub = $this->subscription($id);
			$feeds[] = array(
			"id" => $id,
			"url" => $sub->uri(),
			"name" => $sub->name(/*fromFeed=*/ false),
			);
		endforeach;

		header("Content-Type: application/json");
		echo json_encode($feeds);
		exit;
	} /* FeedWordPress::fwp_feeds () */

	public function fwp_feedcontents () {
		$feed_id = FeedWordPress::param( 'feed_id' );

		// Let's load up some data from the feed . . .
		$feed = $this->subscription($feed_id);
		$posts = $feed->live_posts();

		if (is_wp_error($posts)) :
			header("HTTP/1.1 502 Bad Gateway");
			$result = $posts;
		else :
			$result = array();

			foreach ($posts as $post) :
				$p = new SyndicatedPost($post, $feed);

				$result[] = array(
					"post_title" => $p->entry->get_title(),
					"post_link" => $p->permalink(),
					"guid" => $p->guid(),
					"post_date" => $p->published(),
				);
			endforeach;
		endif;

		header("Content-Type: application/json");

		echo json_encode($result);

		// This is an AJAX request, so close it out thus.
		die;
	} /* FeedWordPress::fwp_feedcontents () */

	public function fwp_xpathtest () {
		$xpath   = FeedWordPress::param( 'xpath' );
		$feed_id = FeedWordPress::param( 'feed_id' );
		$post_id = FeedWordPress::param( 'post_id' );

		$expr = new FeedWordPressParsedPostMeta($xpath);

		// Let's load up some data from the feed . . .
		$feed = $this->subscription($feed_id);
		$posts = $feed->live_posts();

		if ( !is_wp_error($posts)) :
			if (strlen($post_id) == 0) :
				$post = $posts[0];
			else :
				$post = null;

				foreach ($posts as $p) :
					if ($p->get_id() == $post_id) :
						$post = $p;
					endif;
				endforeach;
			endif;

			$post = new SyndicatedPost($post, $feed);
			$meta = $expr->do_substitutions($post);

			$result = array(
			"post_title" => $post->entry->get_title(),
			"post_link" => $post->permalink(),
			"guid" => $post->guid(),
			"expression" => $xpath,
			"results" => $meta
			);
		else :
			$result = array(
			"expression" => $xpath,
			"feed_id" => $feed_id,
			"post_id" => $post_id,
			"results" => $posts
			);

			header("HTTP/1.1 503 Bad Gateway");
		endif;

		header("Content-Type: application/json");

		echo json_encode($result);

		// This is an AJAX request, so close it out thus.
		die;
	} /* FeedWordPress::fwp_xpathtest () */

	public function redirect_retired () {
		global $wp_query;
		if (is_singular()) :
			if (
				'fwpretired'==$wp_query->post->post_status
				or 'fwpzapped'==$wp_query->post->post_status
			) :
				do_action('feedwordpress_redirect_retired', $wp_query->post);

				if ( !($template = get_404_template())) :
					$template = get_index_template();
				endif;
				if ($template = apply_filters('template_include', $template)) :
					header("HTTP/1.1 410 Gone");
					include($template);
				endif;
				exit;
			endif;
		endif;
	} /* FeedWordPress::redirect_retired () */

	public function row_actions ($actions, $post) {
		if (is_syndicated($post->ID) && current_user_can('edit_post', $post->ID)) :
			$link = get_delete_post_link($post->ID, '', true);
			$eraseLink = MyPHP::url($link, array("fwp_post_delete" => "nuke"));

			$caption = apply_filters('feedwordpress_ui_erase_link_caption', __('Erase the record of this post (will be re-syndicated if it still appears on the feed).'));
			$linktext = apply_filters('feedwordpress_ui_erase_link_text', __('Erase/Resyndicate'));

			$retireClass = null;
			if ($post->post_status == 'fwpzapped') :
				if (count(get_post_meta($post->ID, '_feedwordpress_zapped_blank_me')) > 0) :
					$retireCap = 'Un-Zap this syndicated post (so it will appear on the site again)';
					$retireText = 'Un-Zap &amp; Restore';
					$retireLink = MyPHP::url($link, array("fwp_post_delete" => "unzap"));
				else :
					// No Un-Zap link for posts that have
					// been blanked. You'll just have to
					// Erase and hope you can resyndicate...
					$retireLink = null;
				endif;
			else :
				$retireCap = apply_filters('feedwordpress_ui_zap_link_caption', __('Zap this syndicated post (so it will not be re-syndicated if it still appears on the feed).'));
				$retireText = apply_filters('feedwordpress_ui_zap_link_text', __('Zap/Don&rsquo;t Resyndicate'));
				$retireLink = MyPHP::url($link, array("fwp_post_delete" => "zap"));
				$retireClass = 'submitdelete';
			endif;

			$keys = array_keys($actions);
			$links = array();
			foreach ($keys as $key) :
				$links[$key] = $actions[$key];

				if ('trash'==$key) :
					#$links[$key] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash (will NOT be re-syndicated)' ) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash' ) . "</a>";

					// Placeholder.
					if ( !is_null($retireLink)) :
						$links['zap trash'] = '';
					endif;
					$links['delete'] = '';
				endif;
			endforeach;

			if ( !is_null($retireLink)) :
				$links['zap trash'] = '<a class="'.esc_attr($retireClass).'" title="'.esc_attr(__($retireCap)).'" href="' . $retireLink . '">' . __($retireText) . '</a>';
			endif;
			$links['delete'] = '<a class="submitdelete" title="'.esc_attr(__($caption)).'" href="' . $eraseLink . '">' . __($linktext) . '</a>';

			$actions = $links;
		endif;
		return $actions;
	} /* FeedWordPress::row_actions () */

	/**
	 * Sets up the FWP dashboard.
	 *
	 * @global $wp_meta_boxes
	 * @uses FeedWordPress::menu_cap
	 */
	public function dashboard_setup() {
		/** @var mixed User capability */
		$see_it = FeedWordPress::menu_cap();

		if ( current_user_can( $see_it ) ) :
			// Get the stylesheet
			wp_enqueue_style( 'feedwordpress-elements' );

			$widget_id   = 'feedwordpress_dashboard';
			$widget_name = __( 'Syndicated Sources' );
			$column      = 'side';
			$priority    = 'core';

			// I would love to use wp_add_dashboard_widget() here and save
			// myself some trouble. But WP 3 does not yet have any way to
			// push a dashboard widget onto the side, or to give it a default
			// location.
			add_meta_box(
				/*id=*/ $widget_id,
				/*title=*/ $widget_name,
				/*callback=*/ array( $this, 'dashboard' ),
				/*screen=*/ 'dashboard',
				/*context=*/ $column,
				/*priority=*/ $priority
			);
			/*control_callback= array($this, 'dashboard_control') */

			/* This is kind of rude, I know, but the dashboard widget isn't
			 * worth much if users don't know that it exists, and I don't
			 * know of any better way to reorder the boxen.
			 *
			 * Gleefully ripped off of codex.wordpress.org/Dashboard_Widgets_API
			 */

			/** @var mixed Globalizes the metaboxes array, this holds all the widgets for wp-admin. */
			global $wp_meta_boxes;

			/** @var mixed Get the regular dashboard widgets array
			 * (which has our new widget already but at the end). */
			$normal_dashboard = $wp_meta_boxes['dashboard'][$column][$priority];

			// Backup and delete our new dashbaord widget from the end of the array
			if ( isset( $normal_dashboard[ $widget_id ] ) ) :
				$backup = array();
				$backup[ $widget_id ] = $normal_dashboard[ $widget_id ];
				unset( $normal_dashboard[ $widget_id ] );

				/** @var array Merge the two arrays together so our widget is at the beginning. */
				$sorted_dashboard = array_merge( $backup, $normal_dashboard );

				// Save the sorted array back into the original €es
				$wp_meta_boxes['dashboard'][ $column ][ $priority ] = $sorted_dashboard;
			endif;
		endif;
	} /* FeedWordPress::dashboard_setup () */

	public function dashboard () {
		$syndicationPage = new FeedWordPressSyndicationPage(dirname(__FILE__).'/syndication.php');
		$syndicationPage->dashboard_box($syndicationPage);
	} /* FeedWordPress::dashboard () */

	public function user_can_richedit ($rich_edit) {

		$post = new FeedWordPressLocalPost;

		if ( ! $post->is_exposed_to_formatting_filters()) :
			// Disable visual editor and only allow operations
			// directly on HTML if post is bypassing fmt filters
			# $rich_edit = false;
		endif;

		return $rich_edit;

	} /* FeedWordPress::user_can_richedit () */

	public function clear_cache_magic_url () {
		if ($this->clear_cache_requested()) :
			$this->clear_cache();
		endif;
	} /* FeedWordPress::clear_cache_magic_url() */

	public function clear_cache_requested () {
		return FeedWordPress::param( 'clear_cache' );
	} /* FeedWordPress::clear_cache_requested() */

	public function update_magic_url () {
		global $wpdb;

		// Explicit update request in the HTTP request (e.g. from a cron job)
		if (self::update_requested()) :
			/*DBG*/ header("Content-Type: text/plain");
			$this->update_hooked = "Initiating a CRON JOB CHECK-IN ON UPDATE SCHEDULE due to URL parameter = " . trim( $this->val( FeedWordPress::param('update_feedwordpress' ) ) );

			$this->update($this->update_requested_url());

			if (FEEDWORDPRESS_DEBUG and is_array($wpdb->queries) and count($wpdb->queries) > 0) :
				$mysqlTime = 0.0;
				$byTime = array();
				foreach ($wpdb->queries as $query) :
					$time = $query[1] * 1000000.0;
					$mysqlTime += $query[1];
					if ( !isset($byTime[$time])) : $byTime[$time] = array(); endif;
					$byTime[$time][] = $query[0]. ' // STACK: ' . $query[2];
				endforeach;
				krsort($byTime);

				foreach ($byTime as $time => $querySet) :
					foreach ($querySet as $query) :
						printf( '[%s ms] %s', esc_html( sprintf( '%4.4f', $time/1000.0 ) ), esc_html( $query ) ) . "\n";
					endforeach;
				endforeach;
				echo esc_html( self::log_prefix()."$wpdb->num_queries queries. $mysqlTime seconds in MySQL. Total of " ); timer_stop(1); print " seconds.";
			endif;

			debug_out_feedwordpress_footer();

			// Magic URL should return nothing but a 200 OK header packet
			// when successful.
			exit;
		endif;
	} /* FeedWordPress::update_magic_url () */

	public static function update_requested () {
		return FeedWordPress::param( 'update_feedwordpress' );
	} /* FeedWordPress::update_requested() */

	public function update_requested_url () {
		$ret = null;

		$uf = FeedWordPress::update_requested();
		if (
			( '*' == $uf )
			|| ( preg_match( '|^[a-z]+://.*|i', $uf ) )
		) :
			$ret = $uf;
		endif;

		return $ret;
	} /* FeedWordPress::update_requested_url() */

	public function auto_update () {
		if ($this->stale()) :
			$this->update();
		endif;
	} /* FeedWordPress::auto_update () */

	public static function find_link ($uri, $field = 'link_rss') {
		global $wpdb;

		$unslashed = untrailingslashit($uri);
		$slashed = trailingslashit($uri);
		$link_id = $wpdb->get_var($wpdb->prepare("
		SELECT link_id FROM $wpdb->links WHERE $field IN ('%s', '%s')
		LIMIT 1", $unslashed, $slashed
		));

		return $link_id;
	} /* FeedWordPress::find_link () */

	/**
	 * FeedWordPress:syndicate_link(): add or update a feed subscription
	 *
	 * Add a new subscription to, or update an existing subscription in,
	 * FWP's list of subscribed feeds.
	 *
	 * Postcondition: If $rss is the URL of a feed not yet on FWP's list of
	 * subscribed feeds, then a new subscription will be added, using $name
	 * as its initial title and $uri as its initial homepage URL (normally
	 * these will be updated with new values taken from the feed, the first
	 * time the new feed is checked for syndicated content, unless feed
	 * settings prevent this). If $rss is the URL of a feed that is already
	 * on FWP's list of subscribed feeds, then that feed will be updated to
	 * use the title provided in $name and the homepage URL in $uri
	 *
	 * @param string $name The human-readable title of the feed (for example, "Rad Geek People's Daily")
	 * @param string $uri The URI for the human-readable homepage associated with the feed (for example, <http://radgeek.com/>)
	 * @param string $rss The URI for the feed itself (for example, <http://radgeek.com/feed/>)
	 *
	 * @return mixed Returns an int with the numeric ID of the new
	 *   subscription's wp_links record if successful or a WP_Error object
	 *   if wp_insert_link() failed.
	 *
	 * @uses FeedWordPress::link_category_id()
	 * @uses FeedWordPress::find_link()
	 * @uses is_wp_error()
	 * @uses wp_insert_link()
	 *
	 */
	public static function syndicate_link ($name, $uri, $rss) {
		// Get the category ID#
		$cat_id = FeedWordPress::link_category_id();
		if ( !is_wp_error($cat_id)) :
			$link_category = array($cat_id);
		else :
			$link_category = array();
		endif;

		// WordPress gets cranky if there's no homepage URI
		if ( !is_string($uri) or strlen($uri)<1) : $uri = $rss; endif;

		// Check if this feed URL is already being syndicated.
		$link_id = wp_insert_link(/*linkdata=*/ array(
		"link_id" => FeedWordPress::find_link($rss), // insert if nothing was found; else update
		"link_rss" => $rss,
		"link_name" => $name,
		"link_url" => $uri,
		"link_category" => $link_category,
		"link_visible" => 'Y', // reactivate if inactivated
		), /*wp_error=*/ true);

		return $link_id;
	} /* function FeedWordPress::syndicate_link() */

	static function syndicated_status ($what, $default) {
		$ret = get_option("feedwordpress_syndicated_{$what}_status");
		if ( ! $ret) :
			$ret = $default;
		endif;
		return $ret;
	} /* FeedWordPress::syndicated_status() */

	public static function on_unfamiliar ($what = 'author') {
		switch ($what) :
		case 'category' : $suffix = ':category'; break;
		case 'post_tag' : $suffix = ':post_tag'; break;
		default: $suffix = '';
		endswitch;

		return get_option('feedwordpress_unfamiliar_'.$what, 'create'.$suffix);
	} // function FeedWordPress::on_unfamiliar()

	public static function null_email_set () {
		$base = get_option('feedwordpress_null_email_set');

		if ($base===false) :
			$ret = array('noreply@blogger.com'); // default
		else :
			$ret = array_map('strtolower',
				array_map('trim', explode("\n", $base)));
		endif;
		$ret = apply_filters('syndicated_item_author_null_email_set', $ret);
		return $ret;

	} /* FeedWordPress::null_email_set () */

	public static function is_null_email( $email ) {
		$ret = in_array( strtolower( trim( $email ) ), FeedWordPress::null_email_set() );
		$ret = apply_filters( 'syndicated_item_author_is_null_email', $ret, $email );
		return $ret;
	} /* FeedWordPress::is_null_email () */

	public static function use_aggregator_source_data () {
		$ret = get_option('feedwordpress_use_aggregator_source_data');
		return apply_filters('syndicated_post_use_aggregator_source_data', ($ret=='yes'));
	} /* FeedWordPress::use_aggregator_source_data () */

	/**
	 * FeedWordPress::munge_permalinks: check whether or not FeedWordPress
	 * should rewrite permalinks for syndicated items to reflect their
	 * original location.
	 *
	 * @return bool TRUE if FeedWordPress SHOULD rewrite permalinks; FALSE otherwise
	 */
	static function munge_permalinks () {
		return (get_option('feedwordpress_munge_permalink', /*default=*/ 'yes') != 'no');
	} /* FeedWordPress::munge_permalinks() */

	public static function syndicated_links ($args = array()) {
		$contributors = FeedWordPress::link_category_id();
		if ( !is_wp_error($contributors)) :
			$links = get_bookmarks(array_merge(
				array("category" => $contributors),
				$args
			));
		else :
			$links = array();
		endif;

		return $links;
	} /* FeedWordPress::syndicated_links() */

	public static function link_category_id () {
		$cat_id = get_option('feedwordpress_cat_id');

		// If we don't yet have the category ID stored, search by name
		if ( ! $cat_id) :
			$cat_id = FeedWordPressCompatibility::link_category_id(DEFAULT_SYNDICATION_CATEGORY);

			if ($cat_id) :
				// We found it; let's stamp it.
				update_option('feedwordpress_cat_id', $cat_id);
			endif;

		// If we *do* have the category ID stored, verify that it exists
		else :
			$cat_id = FeedWordPressCompatibility::link_category_id((int) $cat_id, 'cat_id');
		endif;

		// If we could not find an appropriate link category,
		// make a new one for ourselves.
		if ( ! $cat_id) :
			$cat_id = FeedWordPressCompatibility::insert_link_category(DEFAULT_SYNDICATION_CATEGORY);
			if ( !is_wp_error($cat_id)) :
				// Stamp it
				update_option('feedwordpress_cat_id', $cat_id);
			endif;
		endif;

		return $cat_id;
	} /* FeedWordPress::link_category_id() */

	# Upgrades and maintenance...
	static function needs_upgrade () {
		$fwp_db_version = get_option('feedwordpress_version', null);
		$ret = false; // innocent until proven guilty
		if (is_null($fwp_db_version) or ($fwp_db_version < FEEDWORDPRESS_VERSION)) :

			// This is an older version or a fresh install. Does it require a database
			// upgrade or database initialization?

			if (is_null($fwp_db_version)) :

				// Fresh install; brand it as ours. Or possibly a version of FWP
				// from before 0.96. But I'm no longer supporting upgrade paths
				// for versions from the previous decade. Sorry.
				update_option('feedwordpress_version', FEEDWORDPRESS_VERSION);

			elseif ($fwp_db_version < 2010.0814) :

				// Change in terminology.
				if (get_option('feedwordpress_unfamiliar_category', 'create')=='default') :
					update_option('feedwordpress_unfamiliar_category', 'null');
				endif;
				foreach (FeedWordPress::syndicated_links() as $link) :
					$sub = new SyndicatedLink($link);

					$remap_uf = array(
						'default' => 'null',
						'filter' => 'null',
						'create' => 'create:category',
						'tag' => 'create:post_tag'
					);
					if (isset($sub->settings['unfamiliar category'])) :
						if ($sub->settings['unfamiliar category']=='filter') :
							$sub->settings['match/filter'] = array('category');
						endif;
						foreach ($remap_uf as $from => $to) :
							if ($sub->settings['unfamiliar category']==$from) :
								$sub->settings['unfamiliar category'] = $to;
							endif;
						endforeach;
					endif;

					if (isset($sub->settings['add global categories'])) :
						$sub->settings['add/category'] = $sub->settings['add global categories'];
						unset($sub->settings['add global categories']);
					endif;

					$sub->save_settings(/*reload=*/ true);
				endforeach;
				update_option('feedwordpress_version', FEEDWORDPRESS_VERSION);

			else :

				// No upgrade needed. Just brand it with the new version.
				update_option('feedwordpress_version', FEEDWORDPRESS_VERSION);

			endif;

		endif;
		return $ret;
	} /* FeedWordPress::needs_upgrade () */

	static function upgrade_database ($from = null)
	{
		if (is_null($from) or $from <= 0.96) : $from = 0.96; endif;

		switch ($from) :
		case 0.96:
			// Dropping legacy upgrade code. If anyone is still
			// using 0.96 and just now decided to upgrade, well, I'm
			// sorry about that. You'll just have to cope with a few
			// duplicate posts.

			// Mark the upgrade as successful.
			update_option('feedwordpress_version', FEEDWORDPRESS_VERSION);
		endswitch;
		echo "<p>Upgrade complete. FeedWordPress is now ready to use again.</p>";
	} /* FeedWordPress::upgrade_database() */

	public static function has_guid_index () {
		global $wpdb;

		$found = false; // Guilty until proven innocent.

		$results = $wpdb->get_results("
		SHOW INDEXES FROM {$wpdb->posts}
		");
		if ($results) :
			foreach ($results as $index) :
				if (isset($index->Column_name)
				and ('guid' == $index->Column_name)) :
					$found = true;
				endif;
			endforeach;
		endif;
		return $found;
	} /* FeedWordPress::has_guid_index () */

	public static function create_guid_index () {
		global $wpdb;

		$wpdb->query("
		CREATE INDEX {$wpdb->posts}_guid_idx ON {$wpdb->posts}(guid)
		");
	} /* FeedWordPress::create_guid_index () */

	public static function remove_guid_index () {
		global $wpdb;

		$wpdb->query("
		DROP INDEX {$wpdb->posts}_guid_idx ON {$wpdb->posts}
		");
	}

	static function fetch_timeout () {
		return apply_filters(
			'feedwordpress_fetch_timeout',
			intval(get_option('feedwordpress_fetch_timeout', FEEDWORDPRESS_FETCH_TIMEOUT_DEFAULT))
		);
	}

	static function fetch ($url, $params = array()) {
		if (is_wp_error($url)) :
			// Let's bounce.
			return $url;
		endif;

		$force_feed = true; // Default

		// Allow user to change default feed-fetch timeout with a global setting.
		// Props Erigami Scholey-Fuller <http://www.piepalace.ca/blog/2010/11/feedwordpress-broke-my-heart.html>
		$timeout = FeedWordPress::fetch_timeout();

		if ( !is_array($params)) :
			$force_feed = $params;
		else : // Parameter array
			$args = wp_parse_args(array(
			'force_feed' => $force_feed,
			'timeout' => $timeout
			), $params);

			extract($args);
		endif;
		$timeout = intval($timeout);

		$pie_class = apply_filters('feedwordpress_simplepie_class', 'FeedWordPie');
		$cache_class = apply_filters('feedwordpress_cache_class', 'FeedWordPie_Cache');
		$file_class = apply_filters('feedwordpress_file_class', 'FeedWordPie_File');
		$parser_class = apply_filters('feedwordpress_parser_class', 'FeedWordPie_Parser');
		$item_class = apply_filters('feedwordpress_item_class', 'FeedWordPie_Item');
		$sniffer_class = apply_filters('feedwordpress_sniffer_class', 'FeedWordPie_Content_Type_Sniffer');

		$feed = new $pie_class;
		$feed->set_feed_url($url);
		$feed->registry->register('Cache', $cache_class);
		$feed->set_timeout($timeout);

		$feed->set_content_type_sniffer_class($sniffer_class);
		$feed->set_file_class($file_class);
		$feed->set_parser_class($parser_class);
		$feed->set_item_class($item_class);
		$feed->force_feed($force_feed);
		$feed->set_cache_duration(FeedWordPress::cache_duration($params));
		$feed->init();
		$feed->handle_content_type();

		if ($feed->error()) :
			$ret = new WP_Error('simplepie-error', $feed->error());
		else :
			$ret = $feed;
		endif;
		return $ret;
	} /* FeedWordPress::fetch () */

	public function clear_cache () {
		global $wpdb;

		// Just in case, clear out any old MagpieRSS cache records.
		$magpies = $wpdb->query("
		DELETE FROM {$wpdb->options}
		WHERE option_name LIKE 'rss_%' AND LENGTH(option_name) > 32
		");

		// The WordPress SimplePie module stores its cached feeds as
		// transient records in the options table. The data itself is
		// stored in `_transient_feed_{md5 of url}` and the last-modified
		// timestamp in `_transient_feed_mod_{md5 of url}`. Timeouts for
		// these records are stored in `_transient_timeout_feed_{md5}`.
		// Since the md5 is always 32 characters in length, the
		// option_name is always over 32 characters.
		$simplepies = $wpdb->query("
		DELETE FROM {$wpdb->options}
		WHERE option_name LIKE '_transient%_feed_%' AND LENGTH(option_name) > 32
		");
		$simplepies = (int) ($simplepies / 4); // Each transient has 4 rows: the data, the modified timestamp; and the timeouts for each

		return ($magpies + $simplepies);
	} /* FeedWordPress::clear_cache () */

	static public function cache_duration ($params = array()) {
		$params = wp_parse_args($params, array(
		"cache" => true,
		));

		$duration = null;
		if ( ! $params['cache']) :
			$duration = 0;
		elseif (defined('FEEDWORDPRESS_CACHE_AGE')) :
			$duration = FEEDWORDPRESS_CACHE_AGE;
		endif;
		return $duration;
	}

	static public function cache_lifetime ($duration) {
		// Check for explicit setting of a lifetime duration
		if (defined('FEEDWORDPRESS_CACHE_LIFETIME')) :
			$duration = FEEDWORDPRESS_CACHE_LIFETIME;

		// Fall back to the cache freshness duration
		elseif (defined('FEEDWORDPRESS_CACHE_AGE')) :
			$duration = FEEDWORDPRESS_CACHE_AGE;
		endif;

		// Fall back to WordPress default
		return $duration;
	} /* FeedWordPress::cache_lifetime () */

	# Utility functions for handling text settings
	static function get_field( $f, $setting = null ) {

		$ret = $f;
		if ( ! is_null( $setting ) ) :
			$ret = null;
			if ( array_key_exists( $setting, $f ) ) :
				$ret = $f[ $setting ];
			endif;
		endif;
		return $ret;

	} /* FeedWordPress::get_field () */

	static function negative ($f, $setting = null) {
		$nego = array ('n', 'no', 'f', 'false');
		$q = self::get_field( $f, $setting );
		return in_array( strtolower( trim( $q ) ), $nego );
	} /* FeedWordPress::negative () */

	static function affirmative ($f, $setting = null) {
		$affirmo = array ('y', 'yes', 't', 'true', 1);
		$q = self::get_field( $f, $setting );
		return in_array( strtolower( trim( $q ) ), $affirmo );
	} /* FeedWordPress::affirmative () */

	/**
	  * Internal debugging functions.
	  *
	  * @todo radgeek needs to document this better. What levels exist, and
	  * how/where are they defined? (gwyneth 20230919)
	  *
	  * @global $feedwordpress_admin_footer
	  */
	static function diagnostic( $level, $out, $persist = null, $since = null, $mostRecent = null ) {
		global $feedwordpress_admin_footer;

		$output = get_option( 'feedwordpress_diagnostics_output', array() );
		$dlog   = get_option( 'feedwordpress_diagnostics_log', array() );

		$diagnostic_nesting = count( explode( ":", $level ) );

		if (FeedWordPressDiagnostic::is_on($level)) :
			foreach ($output as $method) :
				switch ($method) :
				case 'echo' :
					if ( !( self::update_requested() || wp_doing_ajax() ) ) :
						echo "<div><pre><strong>Diag".esc_html( str_repeat('====', $diagnostic_nesting-1) ).'|</strong> '. esc_html( $out )."</pre></div>\n";
					endif;
					break;
				case 'echo_in_cronjob' :
					if (self::update_requested()) :
						echo esc_html( self::log_prefix() ) . ' ' . esc_html( $out ) . "\n";
					endif;
					break;
				case 'admin_footer' :
					$feedwordpress_admin_footer[] = $out;
					break;
				case 'error_log' :
					error_log(self::log_prefix() . ' ' . $out);
					break;
				case 'email' :

					if (is_null($persist)) :
						$sect = 'occurrent';
						$hook = (isset($dlog['mesg'][$sect]) ? count($dlog['mesg'][$sect]) : 0);
						$line = array("Time" => time(), "Message" => $out);
					else :
						$sect = 'persistent';
						$hook = md5($level."\n".$persist);
						$line = array("Since" => $since, "Message" => $out, "Most Recent" => $mostRecent);
					endif;

					if ( !isset($dlog['mesg'])) : $dlog['mesg'] = array(); endif;
					if ( !isset($dlog['mesg'][$sect])) : $dlog['mesg'][$sect] = array(); endif;

					$dlog['mesg'][$sect][$hook] = $line;
				endswitch;
			endforeach;
		endif;

		update_option( 'feedwordpress_diagnostics_log', $dlog );
	} /* FeedWordPress::diagnostic() */

	public function email_diagnostics_override () {
		return ( $this->has_secret() and ! ! FeedWordPress::param( 'feedwordpress_email_diagnostics' ) );
	} /* FeedWordPress::email_diagnostics_override() */

	public function has_emailed_diagnostics ($dlog) {
		$ret = false;
		if ($this->email_diagnostics_override()
		or (isset($dlog['schedule']) and isset($dlog['schedule']['last']))) :
			$ret = true;
		endif;
		return $ret;
	} /* FeedWordPress::has_emailed_diagnostics() */

	public function ready_to_email_diagnostics ($dlog) {
		$ret = false;
		if ($this->email_diagnostics_override()
		or (time() > ($dlog['schedule']['last'] + $dlog['schedule']['freq']))) :
			$ret = true;
		endif;
		return $ret;
	} /* FeedWordPress::ready_to_email_diagnostics() */

	/**
	 * Emails a diagnostic log to the WP administrator.
	 *
	 * @param  Array $params See @wp_parse_args()
	 */
	public function email_diagnostic_log( $params = array() ) {
		$params = wp_parse_args( $params, array(
			"force" => false,
		) );

		$dlog = get_option('feedwordpress_diagnostics_log', array());

		if ($this->has_emailed_diagnostics($dlog)) :
			if ($this->ready_to_email_diagnostics($dlog)) :
				// No news is good news; only send if
				// there are some messages to send.
				$body = null;
				if ( !isset($dlog['mesg'])) : $dlog['mesg'] = array(); endif;

				foreach ($dlog['mesg'] as $sect => $mesgs) :
					if (count($mesgs) > 0) :
						if (is_null($body)) : $body = ''; endif;

						$paradigm = reset($mesgs);
						$body .= "<h2>".ucfirst($sect)." issues</h2>\n"
							."<table>\n"
							."<thead><tr>\n";
						foreach ( $paradigm as $col => $value ) :
							$body .= '<th scope="col">'.$col."</th>\n";
						endforeach;
						$body .= "</tr></thead>\n"
							."<tbody>\n";

						foreach ($mesgs as $line) :
							$body .= "<tr>\n";
							foreach ($line as $col => $cell) :
								if (is_numeric($cell)) :
									$cell = date('j-M-y, h:i a', $cell);
								endif;
								$class = strtolower(preg_replace('/\s+/', '-', $col));
								$body .= "<td class=\"$class\">{$cell}</td>";
							endforeach;
							$body .= "</tr>\n";
						endforeach;

						$body .= "</tbody>\n</table>\n\n";
					endif;
				endforeach;

				$body = apply_filters('feedwordpress_diagnostic_email_body', $body, $dlog);
				if ( !is_null($body)) :
					$home = feedwordpress_display_url(get_bloginfo('url'));
					$subj = apply_filters('feedwordpress_diagnostic_email_subject', $home . " syndication issues", $dlog);
					$agent = 'FeedWordPress '.FEEDWORDPRESS_VERSION;
					$body = <<<EOMAIL
<html>
<head>
<title>$subj</title>
<style type="text/css">
	body { background-color: white; color: black; }
	table { width: 100%; border: 1px solid black; }
	table thead tr th { background-color: #ff7700; color: white; border-bottom: 1px solid black; }
	table thead tr { border-bottom: 1px solid black; }
	table tr { vertical-align: top; }
	table .since { width: 20%; }
	table .time { width: 20%; }
	table .most-recently { width: 20%; }
	table .message { width: auto; }
</style>
</head>
<body>
<h1>Syndication Issues encountered by $agent on $home</h1>
$body
</body>
</html>

EOMAIL;

					$ded = get_option('feedwordpress_diagnostics_email_destination', 'admins');

					// e-mail address
					if (preg_match('/^mailto:(.*)$/', $ded, $ref)) :
						$recipients = array($ref[1]);

					// userid
					elseif (preg_match('/^user:(.*)$/', $ded, $ref)) :
						if ( is_numeric( $ref[1] ) and function_exists( 'get_userdata' ) ) :
							$userdata = get_userdata( (int) $ref[1]) ;
							$recipients = array( $userdata->user_email );
						else :
							// get_userdata() might not have been loaded yet, so send
							// to admins instead. (gwyneth 20230901)
							$recipients = FeedWordPressDiagnostic::admin_emails();
						endif;

					// admins
					else :
						$recipients = FeedWordPressDiagnostic::admin_emails();
					endif;

					$mesgId = 'feedwordpress+'.time().'@'.$home;
					$parentId = get_option('feedwordpress_diagnostics_email_root_message_id', null);

					$head = array("Message-ID: <$mesgId>");
					if ( !is_null($parentId)) :
						// We've already sent off a diagnostic message in the past. Let's do some
						// magic to help with threading, in the hopes that all diagnostic messages
						// get threaded together.
						$head[] = "References: <$parentId>";
						$head[] = "In-Reply-To: <$parentId>";
						$subj = "Re: ".$subj;
					else :
						// This is the first of its kind. Let's mark it as such.
						update_option('feedwordpress_diagnostics_email_root_message_id', $mesgId);
					endif;
					$head = apply_filters('feedwordpress_diagnostic_email_headers', $head);

					foreach ($recipients as $email) :
						add_filter('wp_mail_content_type', array('FeedWordPress', 'allow_html_mail'));
						wp_mail($email, $subj, $body, $head);
						remove_filter('wp_mail_content_type', array('FeedWordPress', 'allow_html_mail'));
					endforeach;
				endif;

				// Clear the logs
				$dlog['mesg']['persistent'] = array();
				$dlog['mesg']['occurrent'] = array();

				// Set schedule for next update
				$dlog['schedule']['last'] = time();
			endif;
		else :
			$dlog['schedule'] = array(
				'freq' => 24 /*hr*/ * 60 /*min*/ * 60 /*s*/,
				'last' => time(),
			);
		endif;

		update_option( 'feedwordpress_diagnostics_log', $dlog );
	} /* FeedWordPress::email_diagnostic_log () */


	static function allow_html_mail() {
		return 'text/html';
	} /* FeedWordPress::allow_html_mail () */

	static function admin_footer () {
		global $feedwordpress_admin_footer;
		foreach ($feedwordpress_admin_footer as $line) :
			echo '<div><pre>' . esc_html( $line ) . '</pre></div>';
		endforeach;
	} /* FeedWordPress::admin_footer () */

	/**
	 * Returns the log prefix, optionally with a date.
	 *
	 * @param  boolean $date If true, a date timestamp is added to the prefix.
	 * @return string Generated log prefix.
	 */
	static function log_prefix ($date = false) {
		$home = get_bloginfo('url');
		$prefix = '['.feedwordpress_display_url($home).'] [feedwordpress] ';
		if ($date) :
			$prefix = "[".date('Y-m-d H:i:s')."]".$prefix;
		endif;
		return $prefix;
	} /* FeedWordPress::log_prefix () */

	/**
	 * Returns the menu capacity value.
	 *
	 * @param  boolean $sub If true, we're on a menu sublevel.
	 * @return mixed Returns the capacity.
	 */
	static function menu_cap ($sub = false) {
		if ($sub) :
			$cap = apply_filters('feedwordpress_menu_settings_capacity', 'manage_options');
		else :
			$cap = apply_filters('feedwordpress_menu_main_capacity', 'manage_links');
		endif;
		return $cap;
	} /* FeedWordPress::menu_cap () */

	public function plugin_dir_path ($path = '') {
		$dir = plugin_dir_path( __FILE__ );
		$file_path = "{$dir}{$path}";
		return apply_filters( "feedwordpress_plugin_dir_path", $file_path );
	} /* FeedWordPress::plugin_dir_path () */

	public function plugin_dir_url ($path = '') {
		$url_path = plugins_url( $path, __FILE__ );
		return apply_filters( "feedwordpress_plugin_dir_url", $url_path );
	} /* FeedWordPRess::plugin_dir_url () */

	static function path ($filename = '') {
		global $fwp_path;

		$path = $fwp_path;
		if (strlen($filename) > 0) :
			$path .= '/'.$filename;
		endif;

		return $path;
	} /* FeedWordPress::path () */

	// -- DEPRCATED UTILITY FUNCTIONS -------------------------------------
	// These are superceded by later code re-organization, (for example
	// MyPHP::param/post/get/request, or FeedWordPressDiagnostic methods),
	// but for the last several versions have been kept here for backward
	// compatibility with add-ons, older code, etc. Maybe someday they
	// will go away.
	// -------------------------------------------------------------------
	static function param( $key, $type = 'REQUEST', $default = null, $sanitizer = null ) {
		return self::sanitized_parameter( MyPHP::param( $key, $default, $type ), $sanitizer );
	} /* FeedWordPress::param () */

	static function post( $key, $default = null, $sanitizer = null ) {
		return self::sanitized_parameter( MyPHP::post( $key, $default ), $sanitizer );
	} /* FeedWordPress::post () */

	static function shallow_sanitize( $item, $sanitizer = null ) {
		if ( $sanitizer == 'raw' ) :
			$value = $item;
		elseif ( $sanitizer == 'textarea' ) :
			$value = sanitize_textarea_field( $item );
		else :
			$value = sanitize_text_field( $item );
		endif;
		return $value;
	}

	static function sanitized_parameter( $value, $sanitizer = null ) {
		if ( $sanitizer === 'raw' || is_scalar( $value ) ) :
			$ret = self::shallow_sanitize( $value, $sanitizer );
		elseif ( is_array( $value ) ) :
			$ret = array();
			foreach ( $value as $key => $item ) :
				$ret[ $key ] = self::sanitized_parameter( $item, $sanitizer );
			endforeach;
		else :
			$ret = null; // "Sanitized" objects or resources currently get nothing.
		endif;
		return $ret;
	}

	static function val ($v, $no_newlines = false) {
		return MyPHP::val($v, $no_newlines);
	} /* FeedWordPress::val () */

	static function critical_bug ($varname, $var, $line, $file = null) {
		FeedWordPressDiagnostic::critical_bug($varname, $var, $line, $file);
	} /* FeedWordPress::critical_bug () */

	static function noncritical_bug ($varname, $var, $line, $file = null) {
		FeedWordPressDiagnostic::noncritical_bug($varname, $var, $line, $file);
	} /* FeedWordPress::noncritical_bug () */

	static function diagnostic_on ($level) {
		return FeedWordPressDiagnostic::is_on($level);
	} /* FeedWordPress::diagnostic_on () */

} /* class FeedWordPress */

$feedwordpress_admin_footer = array();

// take your best guess at the realname and e-mail, given a string
define( 'FWP_REGEX_EMAIL_ADDY', '([^@"(<\s]+@[^"@(<\s]+\.[^"@(<\s]+)' );
define( 'FWP_REGEX_EMAIL_NAME', '("([^"]*)"|([^"<(]+\S))' );
define( 'FWP_REGEX_EMAIL_POSTFIX_NAME', '/^\s*' . FWP_REGEX_EMAIL_ADDY . '\s+\(' . FWP_REGEX_EMAIL_NAME . '\)\s*$/' );
define( 'FWP_REGEX_EMAIL_PREFIX_NAME', '/^\s*' . FWP_REGEX_EMAIL_NAME . '\s*<' . FWP_REGEX_EMAIL_ADDY . '>\s*$/' );
define( 'FWP_REGEX_EMAIL_JUST_ADDY', '/^\s*' . FWP_REGEX_EMAIL_ADDY . '\s*$/' );
define( 'FWP_REGEX_EMAIL_JUST_NAME', '/^\s*' . FWP_REGEX_EMAIL_NAME . '\s*$/' );

/**
 * Parses an email address that includes the real name as well.
 *
 * @param  string $email Email address to parse.
 *
 * @return array  Associative array with 'name' and 'email' as fields (each may be null).
 */
function parse_email_with_realname( $email ) {
	$ret = array(
		'name' => null,
		'email' => null
	);	// avoid uninitialized variables for return values, this will generate a notice/warning - and one day, an error! (gwyneth 20210714)

	if ( preg_match( FWP_REGEX_EMAIL_POSTFIX_NAME, $email, $matches ) ) :
		( $ret['name'] = $matches[3] ) || ( $ret['name'] = $matches[2] );
		$ret['email'] = $matches[1];
	elseif ( preg_match( FWP_REGEX_EMAIL_PREFIX_NAME, $email, $matches ) ) :
		( $ret['name'] = $matches[2] ) || ( $ret['name'] = $matches[3] );
		$ret['email'] = $matches[4];

	elseif ( preg_match( FWP_REGEX_EMAIL_JUST_ADDY, $email, $matches ) ) :
		$ret['name']  = null;
		$ret['email'] = $matches[1];
	elseif ( preg_match( FWP_REGEX_EMAIL_JUST_NAME, $email, $matches ) ) :
		$ret['email'] = null;
		( $ret['name'] = $matches[2] ) || ( $ret['name'] = $matches[3] );
	endif;
	return $ret;
}
