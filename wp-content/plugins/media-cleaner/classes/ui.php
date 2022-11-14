<?php

class Meow_WPMC_UI {

	private $core = null;

	private $foundTypes = array(
		"CONTENT" => "Found in content.",
		"CONTENT (ID)" => "Found in content (as an ID).",
		"CONTENT (URL)" => "Found in content (as an URL).",
		"THEME" => "Found in theme.",
		"THEME (ID)" => "Found in theme (as an ID).",
		"THEME (URL)" => "Found in theme (as an URL).",
		"PAGE BUILDER" => "Found in Page Builder.",
		"PAGE BUILDER (ID)" => "Found in Page Builder (as an ID).",
		"PAGE BUILDER (URL)" => "Found in Page Builder (as an URL).",
		"GALLERY" => "Found in gallery.",
		"PORTFOLIO (ID)" => "Found in a portfolio (as an ID).",
		"PORTFOLIO (URL)" => "Found in a portfolio (as an URL).",
		"META" => "Found in meta.",
		"META (ID)" => "Found in meta (as an ID).",
		"META (URL)" => "Found in meta (as an URL).",
		"META ACF (ID)" => "Found in meta (as an URL).",
		"META ACF (URL)" => "Found in meta (as an URL).",
		"WIDGET" => "Found in widget.",
		"ACF WIDGET (ID)" => "Found in ACF Widget (as an ID).",
		"ACF WIDGET (URL)" => "Found in ACF Widget (as an URL).",
		"ATTACHMENT (ID)" => "Found in Attachment (as an ID).",
		"SLIDER (ID)" => "Found in slider (as an ID).",
		"SLIDER (URL)" => "Found in slider (as an URL).",
		"CALENDAR (URL)" => "Found in calendar (as an URL).",
		"MENU (URL)" => "Found in menu (as an URL).",
		"SITE ICON" => "Found as a Site Icon."
	);

	function __construct( $core ) {
		$this->core = $core;
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_filter( 'media_row_actions', array( $this, 'media_row_actions' ), 10, 2 );
	}

	function admin_menu() {
		if ( !$this->core->can_access_features() ) {
      return;
    }
		add_media_page( 'Media Cleaner Dashboard', __( 'Cleaner', 'media-file-renamer' ), 'read', 
			'wpmc_dashboard', array( $this, 'cleaner_dashboard' ), 1 );
	}

	public function cleaner_dashboard() {
		wpmc_check_database();
		echo '<div id="wpmc-dashboard"></div>';
	}

	/*******************************************************************************
	 * METABOX FOR USAGE
	 ******************************************************************************/

	function add_metabox() {
		add_meta_box( 'mfrh_media_usage_box', 'Media Cleaner', array( $this, 'display_metabox' ), 'attachment', 'side', 'default' );
	}

	function display_metabox( $post ) {
		// Search the references to the ID
		$originType = $this->core->reference_exists( null, $post->ID );

		// Search the references to the files
		if ( !$originType ) {
			$originType = "";
			$paths = $this->core->get_paths_from_attachment( $post->ID );
			foreach ( $paths as $path ) {
				$originType = $this->core->reference_exists( $path, null );
				if ( $originType ) {
					break;
				}
			}
		}

		if ( $originType ) {
			$id = $originType;
			$originType = preg_replace( '/\s*\[.*\]/', '', $originType );
			$id = str_replace( $originType, '', $id );
			$id = trim( $id, '[' );
			echo "Used as: " . $originType . "<br />";
			if ( array_key_exists( $originType, $this->foundTypes ) ) {
				echo "Meaning: " . $this->foundTypes[ $originType ] . "<br />";
			}
			if ( !empty( $id ) ) {
				$id = trim( $id, ' []' );
				$post = get_post( $id );
				if ( $post ) {
					echo "Used in: <a href='" . get_permalink( $id ) . "' target='_blank'>" . $post->post_title . "</a>";
					echo " (<a href='" . get_edit_post_link( $id ) . "'>edit</a>)";
				}
			}
			return;
		}

		$issue = $this->core->get_issue_for_postId( $post->ID );
		if ( $issue ) {
			$this->core->echo_issue( $issue->issue );
			return;
		}
		
		echo "There is no information about this media in the Cleaner DB. It is either not in use, or the scan hasn't been ran.";
	}

	function media_row_actions( $actions, $post ) {
		wpmc_check_database();
		global $current_screen;
		if ( 'upload' != $current_screen->id )
		    return $actions;
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$res = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE postId = %d", $post->ID ) );
		if ( !empty( $res ) && isset( $actions['delete'] ) )
			$actions['delete'] = "<a href='?page=wpmc_dashboard'>" .
				__( 'Delete with Media Cleaner', 'media-cleaner' ) . "</a>";
		if ( !empty( $res ) && isset( $actions['trash'] ) )
			$actions['trash'] = "<a href='?page=wpmc_dashboard'>" .
				__( 'Trash with Media Cleaner', 'media-cleaner' ) . "</a>";
		if ( !empty( $res ) && isset( $actions['untrash'] ) ) {
			$actions['untrash'] = "<a href='?page=wpmc_dashboard>" .
				__( 'Restore with Media Cleaner', 'media-cleaner' ) . "</a>";
		}
		return $actions;
	}

}
