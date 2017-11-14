<?php

	/*
	 * Profile tab content
	 */
	 
	if ( ! defined( 'ABSPATH' ) ) { exit; }
	
	if ( isset( $_POST['action'] ) && $_POST['action'] == "edit" ) {
		// profile edit request
		
		require_once( GDE_PLUGIN_DIR . "libs/lib-profile.php" );
		gde_profile_form( $_POST['profile'] );
	}

