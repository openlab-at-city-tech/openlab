<?php
/*
Plugin Name: GradeBook
Plugin URI: https://wordpress.org/plugins/an-gradebook/
Description: A gradebook plugin for educators to create, maintain, and share grades.
Version: 6.5.3
Author: Aori Nevo
Author URI: http://www.aorinevo.com
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: an-gradebook
Domain Path: /languages
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AN_GRADEBOOK_VERSION', '6.5.3' );

require_once plugin_dir_path( __FILE__ ) . 'functions.php';
require_once plugin_dir_path( __FILE__ ) . 'Gradebook-Database.php';
require_once plugin_dir_path( __FILE__ ) . 'rest-api/class-rest-courses.php';
require_once plugin_dir_path( __FILE__ ) . 'rest-api/class-rest-assignments.php';
require_once plugin_dir_path( __FILE__ ) . 'rest-api/class-rest-students.php';
require_once plugin_dir_path( __FILE__ ) . 'rest-api/class-rest-cells.php';
require_once plugin_dir_path( __FILE__ ) . 'rest-api/class-rest-stats.php';
require_once plugin_dir_path( __FILE__ ) . 'rest-api/class-rest-student-view.php';

register_activation_hook( __FILE__, array( 'AN_GradeBook_Database', 'setup' ) );
add_action( 'plugins_loaded', array( 'AN_GradeBook_Database', 'maybe_setup' ) );

function an_gradebook_register_rest_routes() {
	$controllers = array(
		new AN_GradeBook_REST_Courses(),
		new AN_GradeBook_REST_Assignments(),
		new AN_GradeBook_REST_Students(),
		new AN_GradeBook_REST_Cells(),
		new AN_GradeBook_REST_Stats(),
		new AN_GradeBook_REST_Student_View(),
	);
	foreach ( $controllers as $controller ) {
		$controller->register_routes();
	}
}
add_action( 'rest_api_init', 'an_gradebook_register_rest_routes' );

function an_gradebook_load_textdomain() {
	load_plugin_textdomain( 'an-gradebook', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'an_gradebook_load_textdomain' );

function register_an_gradebook_menu_page() {
	add_menu_page(
		__( 'GradeBook', 'an-gradebook' ),
		__( 'GradeBook', 'an-gradebook' ),
		'read',
		'an_gradebook',
		'init_an_gradebook',
		'dashicons-book-alt',
		'6.12'
	);
}
add_action( 'admin_menu', 'register_an_gradebook_menu_page' );

function enqueue_an_gradebook_scripts( $hook ) {
	if ( 'toplevel_page_an_gradebook' !== $hook ) {
		return;
	}

	$asset_path = plugin_dir_path( __FILE__ ) . 'build/index.asset.php';
	if ( ! file_exists( $asset_path ) ) {
		return;
	}

	$asset = include $asset_path;

	wp_enqueue_script(
		'an-gradebook-react',
		plugins_url( 'build/index.js', __FILE__ ),
		$asset['dependencies'],
		$asset['version'],
		true
	);

	if ( file_exists( plugin_dir_path( __FILE__ ) . 'build/index.css' ) ) {
		wp_enqueue_style(
			'an-gradebook-react-style',
			plugins_url( 'build/index.css', __FILE__ ),
			array(),
			$asset['version']
		);
	}

	wp_localize_script( 'an-gradebook-react', 'anGradebookSettings', array(
		'restNonce' => esc_attr( wp_create_nonce( 'wp_rest' ) ),
		'restUrl'   => esc_url( rest_url( 'an-gradebook/v1/' ) ),
		'userRole'  => esc_attr(current_user_can( 'manage_options' )) ? 'instructor' : 'student',
	) );
}
add_action( 'admin_enqueue_scripts', 'enqueue_an_gradebook_scripts' );

function init_an_gradebook() {
	if ( ! current_user_can( 'read' ) ) {
		echo esc_html__( 'You do not have permissions to view this GradeBook.', 'an-gradebook' );
		return;
	}
	echo '<div id="an-gradebook-react-root"></div>';
}

function an_gradebook_my_delete_user( $user_id ) {
	global $wpdb;
	$table_gradebook  = an_gradebook_table( 'an_gradebook' );
	$table_assignment = an_gradebook_table( 'an_assignment' );
	$wpdb->delete( $table_gradebook, array( 'uid' => $user_id ) );
	$wpdb->delete( $table_assignment, array( 'uid' => $user_id ) );
}
add_action( 'delete_user', 'an_gradebook_my_delete_user' );
