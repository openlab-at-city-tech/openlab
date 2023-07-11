<?php
/*
Plugin Name: Ultimate Category Excluder
Version: 1.7
Plugin URI: http://infolific.com/technology/software-worth-using/ultimate-category-excluder/
Description: Easily exclude categories from your front page, feeds, archives, and search results.
Author: Marios Alexandrou
Author URI: http://infolific.com/technology/
License: GPLv2 or later
Text Domain: ultimate-category-excluder
*/

/*
Copyright 2016 Marios Alexandrou

Copyright 2007 Michael Clark

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

add_action( 'admin_menu', 'ksuce_admin_menu' );
add_filter( 'pre_get_posts','ksuce_exclude_categories' );

// Monitor XMLRPC and REST queries to disable plugin functionality during API requests. This
// ensures that REST and XMLRPC clients see the complete content of the blog for admin purposes.
$ksuce_is_api_query = false;
add_action( 'xmlrpc_call', 'ksuce_detect_xmlrpc_api' );
add_action( 'rest_api_init', 'ksuce_detect_rest_api' );

function ksuce_detect_xmlrpc_api() {
	global $ksuce_is_api_query;
	$ksuce_is_api_query = true;
}

function ksuce_detect_rest_api() {
	global $ksuce_is_api_query;
	$ksuce_is_api_query = true;
}

// Include Ultimate Category Excluder language files
load_plugin_textdomain( 'ultimate-category-excluder', false, dirname(plugin_basename(__FILE__)) . '/languages' );

function ksuce_admin_menu() {
	add_options_page( __( 'Ultimate Category Excluder Options', 'ultimate-category-excluder'), __( 'Category Excluder', 'ultimate-category-excluder' ), "manage_options", basename(__FILE__), 'ksuce_options_page' );
}

function ksuce_options_page() {
	if( isset( $_POST[ 'ksuce' ] ) ) { 
        check_admin_referer( 'uce_form' );
        $message = ksuce_process(); 
    }

	$options = ksuce_get_options();

	if ( !is_array( $options['exclude_main'] ) ) {
		$options['exclude_main'] = array();
	}
	if ( !is_array( $options['exclude_feed'] ) ) {
		$options['exclude_feed'] = array();
	}
	if ( !is_array( $options['exclude_archives'] ) ) {
		$options['exclude_archives'] = array();
	}
	if ( !is_array( $options['exclude_search'] ) ) {
		$options['exclude_search'] = array();
	}
	?>
	<div class="wrap">
		<h2><?php _e( 'Ultimate Category Excluder Options', 'ultimate-category-excluder' ); ?></h2>
		<?php if ( isset( $message ) ) { echo $message; } ?>
		<p><?php _e( 'Use this page to select the categories you wish to exclude and where you would like to exclude them from.', 'ultimate-category-excluder' ); ?></p>
		<form action="options-general.php?page=ultimate-category-excluder.php" method="post">
        <?php wp_nonce_field( 'uce_form' ); ?>
		<table class="widefat">
		<thead>
			<tr>
				<th scope="col"><?php _e( 'Category', 'ultimate-category-excluder' ); ?></th>
				<th scope="col"><?php _e( 'Exclude from Front Page?', 'ultimate-category-excluder' ); ?></th>
				<th scope="col"><?php _e( 'Exclude from Feeds?', 'ultimate-category-excluder' ); ?></th>
				<th scope="col"><?php _e( 'Exclude from All Archives?', 'ultimate-category-excluder' ); ?></th>
				<th scope="col"><?php _e( 'Exclude from Search?', 'ultimate-category-excluder' ); ?></th>
			</tr>
		</thead>
		<tbody id="the-list">
	<?php
	$args=array(
		'hide_empty' => 0,
		'order' => 'ASC'
	);
	$cats = get_categories( $args );
	$alt = 0;
	foreach( $cats as $cat ) {
		?>
		<tr<?php if ( $alt == 1 ) { echo ' class="alternate"'; $alt = 0; } else { $alt = 1; } ?>>
			<th scope="row"><?php echo $cat->cat_name; //. ' (' . $cat->cat_ID . ')'; ?></th>
			<td><input type="checkbox" name="exclude_main[]" value="-<?php echo $cat->cat_ID ?>" <?php if ( in_array( '-' . $cat->cat_ID, $options['exclude_main'] ) ) { echo 'checked="true" '; } ?>/></td>
			<td><input type="checkbox" name="exclude_feed[]" value="-<?php echo $cat->cat_ID ?>" <?php if ( in_array( '-' . $cat->cat_ID, $options['exclude_feed'] ) ) { echo 'checked="true" '; } ?>/></td>
			<td><input type="checkbox" name="exclude_archives[]" value="-<?php echo $cat->cat_ID ?>" <?php if ( in_array( '-' . $cat->cat_ID, $options['exclude_archives'] ) ) { echo 'checked="true" '; } ?>/></td>
			<td><input type="checkbox" name="exclude_search[]" value="-<?php echo $cat->cat_ID ?>" <?php if ( in_array( '-' . $cat->cat_ID, $options['exclude_search'] ) ) { echo 'checked="true" '; } ?>/></td>
		</tr>			
	<?php } ?>
	</table>
	<p><input type="checkbox" name="disable_for_api" id="disable_for_api" <?php if ($options['disable_for_api'] == true) { echo 'checked="true" '; } ?>/><label for="disable-for-xmlrpc"><?php _e( 'Disable category exclusion for authenticated API requests', 'ultimate-category-excluder' ); ?></label></p>
	<p class="submit"><input type="submit" value="<?php _e('Update', 'ultimate-category-excluder'); ?>" /></p>
	<input type="hidden" name="ksuce" value="true" />
	</form>
	</div>

	<?php
	global $wpdb, $wp_version;
	echo "<h3>Support</h3>\n";
	echo "<p>Please report this information when requesting support.</p>";
	echo '<ul>';
	echo '<li>UCE version: 1.1</li>';
	echo '<li>PHP version: ' . PHP_VERSION . '</li>';
//	echo '<li>MySQL version: ' . mysqli_get_server_info( $wpdb->dbh ) . '</li>';
	echo '<li>WordPress version: ' . $wp_version . '</li>';
	$mbctheme = wp_get_theme();
	echo "<li>Theme: " . $mbctheme->Name . " is version " . $mbctheme->Version."</li>";
	$category_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE taxonomy = 'category'" );
	echo '<li>Number of categories is: ' . $category_count . '</li>';
	if( !defined( 'AUTH_KEY' ) ) { echo "<li>AUTH_KEY is empty. Please check your settings in your wp-config.php file. This doesn't affect UCE, but it will help make your WordPress sytem more secure.</li>"; }
	echo "</ul>";
}

function ksuce_process() {
	//echo '<pre>'; print_r( $_POST );
	if( !isset( $_POST[ 'exclude_main' ] ) )     { $_POST[ 'exclude_main' ] = array(); }
	if( !isset( $_POST[ 'exclude_feed' ] ) )     { $_POST[ 'exclude_feed' ] = array(); }
	if( !isset( $_POST[ 'exclude_archives' ] ) ) { $_POST[ 'exclude_archives' ] = array(); }
	if( !isset( $_POST[ 'exclude_search' ] ) )   { $_POST[ 'exclude_search' ] = array(); }
	if( !isset( $_POST[ 'disable_for_api' ] ) )   { $_POST[ 'disable_for_api' ] = false; }

	$options['exclude_main'] = $_POST[ 'exclude_main' ];
	$options['exclude_feed'] = $_POST[ 'exclude_feed' ];
	$options['exclude_archives'] = $_POST[ 'exclude_archives' ];
	$options['exclude_search'] = $_POST[ 'exclude_search' ];
	$options['disable_for_api'] = $_POST[ 'disable_for_api' ];

	update_option( 'ksuceExcludes', $options );
	$message = "<div class='updated'><p>" . ( __( 'Excludes successfully updated', 'ultimate-category-excluder' ) ) . "</p></div>";
	return $message;
}

function ksuce_get_options(){
	$defaults = array();
	$defaults['exclude_main'] = array();
	$defaults['exclude_feed'] = array();
	$defaults['exclude_archives'] = array();
	$defaults['exclude_search'] = array();
	$defaults['disable_for_api'] = true;

	$options = get_option( 'ksuceExcludes' );
	if ( !is_array( $options ) ){
		$options = $defaults;
		update_option( 'ksuceExcludes', $options );
	}

	// If the array exists but doesn't have a value for disable_for_api,
	// default to false to ensure existing users have to opt in to new behavior.
	if ( array_key_exists('disable_for_api', $options) == false ){
		$options['disable_for_api'] = false;
		update_option( 'ksuceExcludes', $options );
	}
	return $options;
}

function ksuce_exclude_categories( $query ) {
	$backtrace = debug_backtrace();
	$array2[0] = "";
	unset( $array2[0] );
	$options = ksuce_get_options();

	global $ksuce_is_api_query;
	//Don't modify the query if request is from authenticated API
	if ( ($options['disable_for_api'] == true) && $ksuce_is_api_query && is_user_logged_in() ) { return $query; }

	//wp_reset_query();
	//error_log( print_r( debug_backtrace(), true ) );
	//error_log( 'search for match: ' . in_array_recursive( 'WPSEO_Video_Sitemap', $backtrace ) );

	//Exclude calls from the Yoast SEO Video Sitemap plugin
	if ( $query->is_home && !in_array_recursive( 'WPSEO_Video_Sitemap', $backtrace ) ) {
		$mbccount = 0;
		foreach ( $options[ 'exclude_main' ] as $value ) {
			$array2[$mbccount] = $value; 
			$mbccount++;
		}
		$query->set('category__not_in', $array2);
	}

	if ( $query->is_feed ) {
		$mbccount = 0;
		foreach ( $options[ 'exclude_feed' ] as $value ) {
			$array2[$mbccount] = $value;
			$mbccount++;
		}
		$query->set( 'category__not_in', $array2 );
	}

	if ( !is_admin() && $query->is_search ) {
		$mbccount = 0;
		foreach ( $options[ 'exclude_search' ] as $value ) {
			$array2[$mbccount] = $value;
			$mbccount++;
		}
		$query->set('category__not_in', $array2);
	}

	if ( !is_admin() && $query->is_archive ) {
		$mbccount = 0;
		foreach ( $options[ 'exclude_archives' ] as $value ) {
			$array2[$mbccount] = $value;
			$mbccount++;
		}
		$query->set( 'category__not_in', $array2 );
	}
	
	return $query;
}

function in_array_recursive( $needle, $haystack ) {
	$found = false;

	foreach( $haystack as $item ) {
		if ( $item === $needle ) {
			$found = true;
			break;
		} elseif ( is_array( $item ) ) {
			$found = in_array_recursive( $needle, $item );
			if( $found ) {
				break;
			}
		}
	}

	return $found;
} 
?>