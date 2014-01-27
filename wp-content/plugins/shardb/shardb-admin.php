<?php
/*
Plugin Name: SharDB site admin utilities
Plugin URI: http://wpmututorials.com/plugins/shardb/
Description: A Multi-database plugin for WordPress networks
Version: 2.7.7
Author: Ron Rennick
Author URI: http://ronandandrea.com/
Network: true
 
*/
/* Copyright:	(C) 2009 Ron Rennick, All rights reserved.  
	Contributions by Luke Poland copyright:	(C) 2009 Luke Poland, All rights reserved.
	
*/
function shardb_get_ds_part_from_blog_id( $blog_id ) {
	global $shardb_hash_length, $shardb_dataset, $shardb_num_db, $vip_db, $shardb_prefix, $enable_home_db, $db_ds_parts;
	
	if( !$enable_home_db && $blog_id < 2 && defined( 'MULTISITE' ) ) {
		$dataset = 'global'; 
		$partition = 0;
	} elseif( isset( $shardb_hash_length ) ) {
		$dataset = $shardb_dataset; 
		$hash = substr( md5( $blog_id ), 0, $shardb_hash_length );
		$partition = hexdec( $hash );
// VIP Blog Check.
// Added by: Luke Poland
		if ( is_array( $vip_db ) && array_key_exists( $blog_id, $vip_db ) ) {
			$ds_part = explode( '_', $db_ds_parts[$vip_db[$blog_id]] );
			$partition = array_pop( $ds_part );
			$hash = str_replace( $shardb_prefix, '', $vip_db[$blog_id] );
		}
// End VIP Addition
	} else { // to come - other sharding structures
		return false;
	}
	return compact( 'dataset', 'hash', 'partition' );
}

// show dataset/partition on site admin blogs screen
function shardb_blog_columns( $columns ) {
	if( class_exists( 'SharDB' ) )
		$columns[ 'shardb' ] = __( 'Dataset / Partition' );
	else
		remove_action( 'manage_blogs_custom_column', 'shardb_blog_field' );
	return $columns;
}
add_filter( 'wpmu_blogs_columns', 'shardb_blog_columns' );

function shardb_blog_field( $column, $blog_id ) {
	global $wpdb, $db_servers;
	
	if ( $column == 'shardb' ) {
		$ds_part = shardb_get_ds_part_from_blog_id( $blog_id );
		echo $ds_part[ 'dataset' ] . ' / ' . $db_servers[ $ds_part[ 'dataset' ] ][ $ds_part[ 'partition' ] ][ 0 ][ 'name' ];
	}
}
add_action( 'manage_sites_custom_column', 'shardb_blog_field', 10, 3 );

function shardb_migrate() {
	global $wpdb, $shardb_hash_length, $shardb_dataset, $shardb_num_db, $vip_db, $shardb_prefix, $enable_home_db;

	if ( ! current_user_can( 'manage_network' ) )
		wp_die( __( 'You do not have permission to access this page.' ) );

	if( empty( $shardb_hash_length ) || empty( $shardb_dataset ) || empty( $shardb_prefix ) ) {
		echo '<div class="error"><p><strong>' . __('Error:') . '</strong> ' . sprintf( __( 'You must configure your database settings by adding %s to your %s for the migration process to work', 'shardb' ), "require('./db-settings.php');", ABSPATH . 'wp-config.php' ) . '</p></div>';
		echo '</div>';
		include( ABSPATH . 'wp-admin/admin-footer.php' );
		die();
	}
	echo '<div class="wrap">';
	screen_icon();
	echo '<h2>' . __( 'SharDB Migration', 'shardb' ) . '</h2>';

	$action = isset( $_GET['action'] ) ? $_GET['action'] : 'show';
	$url = add_query_arg( array( 'page' => 'shardb_migrate' ), network_admin_url( 'settings.php' ) );
	$start_url = add_query_arg( array( 'action' => 'migrate' ), $url );
	$global_url = add_query_arg( array( 'action' => 'global' ), $url );

	switch ( $action ) {
		case 'migrate':
			$next = ( isset($_GET['next']) ) ? intval($_GET['next']) : 0;
			if( !$next && !$enable_home_db && defined( 'MULTISITE' ) )
				$next = 1;

			$sites = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs} ORDER BY blog_id ASC LIMIT {$next}, 5" );
			if ( empty( $sites ) ) {
				echo '<p>' . __( 'All done!' ) . '</p>';
				break;
			}
			$shards = array();
			echo '<ul>';
			$count = 0;
			foreach( $sites as $site ) {
				$count++;
				$siteurl = get_blog_option( $site, 'siteurl' );
				if( empty( $siteurl ) )
					continue;

				$errors = shardb_migrate_site_tables( $site, $siteurl, $wpdb, $shardb_prefix );
				if( !empty( $errors ) ) {
					foreach( $errors as $e )
						echo '<li><strong>' . $e . '</strong></li>';
					break;
				}
			}
			echo '</ul>';
			$next_url = add_query_arg( array( 'next' => $next + $count ), $start_url );

			if( empty( $errors ) ) {
			?><p><?php _e( 'If your browser doesn&#8217;t start loading the next page automatically, click this link:' ); ?> <a class="button" href="<?php echo $next_url ?>"><?php _e("Next Sites"); ?></a></p>
			<script type='text/javascript'>
			<!--
			function nextpage() {
				location.href = "<?php echo $next_url ?>";
			}
			setTimeout( "nextpage()", 1000 );
			//-->
			</script><?php
			} else
				echo '<p>' . __( 'Please review the messages above before continuing!', 'shardb' ) . '<a class="button" href="' . $next_url .'">' . __("Next Sites") . '</a></p>';
		break;
		case 'global':
			$errors = shardb_migrate_global_tables( $wpdb );
			if( !empty( $errors ) ) {
				foreach( $errors as $e )
					echo '<li><strong>' . $e . '</strong></li>';
				break;
			}
		case 'show':
		default:
			?><p><?php _e( 'You can migrate all the database tables on your network through this page. It works by calling the migrate script for each site automatically.', 'shardb' ); ?></p>
			<p><a class="button" href="<?php echo esc_url( $global_url ); ?>"><?php _e( 'Migrate Global Tables', 'shardb' ); ?></a>
			<a class="button" href="<?php echo esc_url( $start_url ); ?>"><?php _e( 'Migrate Sites', 'shardb' ); ?></a></p><?php
		break;
	}
	echo '</div>';
}
function add_shardb_migrate_page() {
	if( !class_exists( 'SharDB' ) && is_multisite() )
		add_submenu_page( 'settings.php', 'SharDB Migration', 'SharDB Migration', 'manage_network', 'shardb_migrate', 'shardb_migrate' );
}
add_action( 'network_admin_menu', 'add_shardb_migrate_page' );

function shardb_migrate_site_tables( $blog_id, $siteurl, &$source_object, $shard_prefix, $display = true ) {
	global $db_tables;
	static $shards = array();

	$errors = array();
	if( !is_callable( array( &$source_object, 'get_blog_prefix' ) ) ) {
		$errors[] = __( 'Source database object does not exist', 'shardb' );
		return $errors;
	}

	$ds_part = shardb_get_ds_part_from_blog_id( $blog_id );
	$db_name = $shard_prefix . $ds_part['hash'];
	if( empty( $shards[$db_name] ) )
		$shards[$db_name] = new wpdb( DB_USER, DB_PASSWORD, $db_name, DB_HOST );

	$target_object = &$shards[$db_name];

	$query = "SHOW TABLES LIKE '" . substr( $source_object->get_blog_prefix( $blog_id ), 0, -1 ) . "\\_%'";
	if( $display )
		echo "<li><h4>($blog_id) $siteurl</h4>";
	$site_tables = $source_object->get_col( $query );
	if( !empty( $site_tables ) ) {
		if( $display )
			echo "<ul>\n";
		$new_tables = $target_object->get_col( $query );
		$old_db_name = '';
		foreach( $site_tables as $t ) {
			if( $old_db_name ) {
				$db_name = $old_db_name;
				$old_db_name = '';
				$target_object = &$shards[$db_name];
			}
			if( $blog_id == 1 && defined( 'MULTISITE' ) ) {
				if( empty( $blog_tables ) ) {
					$blog_tables = $source_object->tables( 'blog', true, 1 );
					$global_tables = $source_object->tables( 'global', true, 1 );
				}
				if( !empty( $db_tables[$t] ) ) {
					$old_db_name = $db_name;
					$db_name = $db_tables[$t];
					if( empty( $shards[$db_name] ) )
						$shards[$db_name] = new wpdb( DB_USER, DB_PASSWORD, $db_name, DB_HOST );

					$target_object = &$shards[$db_name];
				} elseif( !in_array( $t, $blog_tables ) ) {
					if( $display && !in_array( $t, $global_tables ) && !preg_match( '/^' . $source_object->base_prefix . '([0-9]+)_/', $t ) )
						echo "<li>$t was <strong>not copied</strong></li>";
					continue;
				}
				$new_tables = $target_object->get_col( "SHOW TABLES LIKE '$t'" );
			}
			$msg = "<li>$t <strong>";
			if( !in_array( $t, $new_tables ) ) {
				$create = $source_object->get_row( "SHOW CREATE TABLE $t", ARRAY_N );
				if( !empty( $create[1] ) ) {
					$data = $source_object->get_results( "SELECT * FROM $t", ARRAY_A );
					$target_object->query( $create[1] );
					foreach( $data as $row )
						$target_object->insert( $t, $row );
					$msg .= 'copied';
				} else
					$msg .= 'was not copied';
				$prep = 'to';
			} else {
				$msg .= 'already exists';
				$prep = 'in';
			}
			if( $display )
				echo $msg . "</strong> $prep <strong>$db_name</strong></li>";
		}
		if( $display )
			echo '</ul>';
	}
	if( $display )
		echo '</li>';

	if( empty( $errors ) && $blog_id == 1 && defined( 'MULTISITE' ) )
		$errors[] = __( 'Please review the tables copied for the main site before proceeding', 'shardb' );

	return $errors;
}
function shardb_migrate_global_tables( &$source_object, $display = true ) {
	global $db_servers, $enable_home_db;

	if( empty( $db_servers['global'][0] ) )
		return array();
	
	$db_server = current( $db_servers['global'][0] );
	$column = 'Tables_in_' . $source_object->dbname;
	$query = "SHOW TABLES WHERE {$column} LIKE '{$source_object->base_prefix}%' AND SUBSTR({$column}," . ( strlen( $source_object->base_prefix ) + 1 ) . ',1) NOT BETWEEN 1 AND 9';
	$tables = $source_object->get_col( $query );
	
	$errors = array();
	if( !empty( $tables ) ) {
		$global = new wpdb( DB_USER, DB_PASSWORD, $db_server['name'], DB_HOST );
		$new_tables = $global->get_col( 'SHOW TABLES' );
		$blog_tables = array();
		if( $enable_home_db && defined( 'MULTISITE' ) )
			$blog_tables = $source_object->tables( 'blog', true, 1 );
			
		if( $display )
			echo "<h4>Global Tables</h4><ul>\n";
			
		foreach( $tables as $t ) {
			if( in_array( $t, $blog_tables ) )
				continue;
				
			$msg = "<li>$t <strong>";
			if( !in_array( $t, $new_tables ) ) {
				$create = $source_object->get_row( "SHOW CREATE TABLE $t", ARRAY_N );
				if( !empty( $create[1] ) ) {
					$data = $source_object->get_results( "SELECT * FROM $t", ARRAY_A );
					$global->query( $create[1] );
					foreach( $data as $row )
						$global->insert( $t, $row );
					$msg .= 'copied';
				} else
					$msg .= 'was not copied';
				$prep = 'to';
			} else {
				$msg .= 'already exists';
				$prep = 'in';
			}
			if( $display )
				echo $msg . "</strong> $prep <strong>{$db_server['name']}</strong></li>";
		}
		if( $display )
			echo '</ul>';
		
	}
	return $errors;
}
?>
