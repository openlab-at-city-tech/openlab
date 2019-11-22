<?php

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', 'dkpdfg_enqueue_styles', 15 );
add_action( 'wp_enqueue_scripts', 'dkpdfg_enqueue_scripts', 10 );
add_action( 'admin_enqueue_scripts', 'dkpdfg_admin_enqueue_scripts', 10, 1 );
add_action( 'admin_enqueue_scripts', 'dkpdfg_admin_enqueue_styles', 10, 1 );

/**
* enqueue front-end css
*/
function dkpdfg_enqueue_styles () {}

/**
* enqueue front-end js
*/
function dkpdfg_enqueue_scripts () {}

/**
* enqueue admin css
*/
function dkpdfg_admin_enqueue_styles ( $hook = '' ) {

	wp_register_style( 'selectize', plugins_url( 'dk-pdf-generator/assets/css/selectize.css' ), array(), '0.12.1' );
	wp_enqueue_style( 'selectize' );

	wp_register_style( 'selectize-default', plugins_url( 'dk-pdf-generator/assets/css/selectize.default.css' ), array(), '0.12.1' );
	wp_enqueue_style( 'selectize-default' );

	wp_register_style( 'dkpdfg-admin', plugins_url( 'dk-pdf-generator/assets/css/dkpdfg-admin.css' ), array(), DKPDFG_VERSION );
	wp_enqueue_style( 'dkpdfg-admin' );

}

/**
* enqueue admin js, create data to be passed into custom js
*/
function dkpdfg_admin_enqueue_scripts ( $hook = '' ) {
	
	wp_enqueue_script('jquery-ui-sortable');

	wp_register_script( 'microplugin', plugins_url( 'dk-pdf-generator/assets/js/microplugin.js' ), array(), '1.0' );
	wp_enqueue_script( 'microplugin' );

	wp_register_script( 'sifter', plugins_url( 'dk-pdf-generator/assets/js/sifter.js' ), array(), '1.0' );
	wp_enqueue_script( 'sifter' );

	wp_register_script( 'selectize', plugins_url( 'dk-pdf-generator/assets/js/selectize.min.js' ), array( 'jquery' ), '0.12.1' );
	wp_enqueue_script( 'selectize' );

	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

	wp_register_script( 'dkpdfg-admin', plugins_url( 'dk-pdf-generator/assets/js/dkpdfg-admin.js' ), array( 'jquery' ), DKPDFG_VERSION );
	wp_enqueue_script( 'dkpdfg-admin' );

    // create data to be passed into dkpdfg-admin.js
    $data = array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
    );

	wp_localize_script( 'dkpdfg-admin', 'dkpdfg_admin_data', $data );

	// settings
	/*
	wp_register_script( 'dkpdfg-settings-admin', plugins_url( 'dk-pdf-generator/assets/js/settings-admin.js' ), array( 'jquery' ), DKPDFG_VERSION );
	wp_enqueue_script( 'dkpdfg-settings-admin' );
	*/	
				
}


