<?php
//Document post types...

//add_action( 'init', 'wds_register_post_types' );
function wds_register_post_types() {
	wds_register_post_type( 'Lab Document', 'Lab Documents', array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'author' ) );
	wds_register_tax( 'Lab Document Type', 'Lab Document Types', array( 'lab-documents' ) );
}


//Register Post Types
function wds_register_post_type( $type, $types, $supports ) {
	$type2  = strtolower( $type );
	$types2 = strtolower( $types );
	$slug   = str_replace( ' ', '-', $types2 );
	$labels = array(
		'name'               => _x( $types, 'post type general name' ),
		'singular_name'      => _x( $type, 'post type singular name' ),
		'add_new'            => _x( 'Add New', $type2 ),
		'add_new_item'       => __( 'Add New ' . $type ),
		'edit_item'          => __( 'Edit ' . $type ),
		'new_item'           => __( 'New ' . $type ),
		'view_item'          => __( 'View ' . $type ),
		'search_items'       => __( 'Search ' . $types ),
		'not_found'          => __( 'No ' . $types2 . ' found' ),
		'not_found_in_trash' => __( 'No ' . $types2 . ' found in Trash' ),
		'parent_item_colon'  => '',
		'menu_name'          => $types,
	);
	$args   = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => $slug ),
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => $supports,
	);
	register_post_type( $slug, $args );
}

//register taxonomy
function wds_register_tax( $type, $types, $arr_attached_type ) {
	$type2  = strtolower( $type );
	$types2 = strtolower( $types );
	$slug   = str_replace( ' ', '-', $types2 );
	$labels = array(
		'name'              => $types,
		'singular_name'     => $type,
		'search_items'      => 'Search ' . $types,
		'all_items'         => 'All ' . $types,
		'parent_item'       => 'Parent ' . $type,
		'parent_item_colon' => 'Parent ' . $type . ':',
		'edit_item'         => 'Edit ' . $type,
		'update_item'       => 'Update ' . $type,
		'add_new_item'      => 'Add New ' . $type,
		'new_item_name'     => 'New ' . $type . ' Name',
		'menu_name'         => $types,
	);

	register_taxonomy(
		$slug,
		$arr_attached_type,
		array(
			'hierarchical' => true,
			'labels'       => $labels,
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => array( 'slug' => $slug ),
		)
	);
}


//short form code
add_shortcode( 'lab-docs', 'wds_docs_shortcode' );
function wds_docs_shortcode( $atts ) {
	global $bp,$wpdb;
	if ( $_GET['uploaded'] == 'true' ) {
		echo "<div class='upload_message'>File Uploaded!</div>";
	}
	$return          = "<div class='wds-group-form digit-game-container'>";
	$return         .= "<div class='digit-game-padder'>";
	$return         .= "<form method='post' enctype='multipart/form-data'>";
		$return     .= "<div class='wds-group-form-item'>";
			$return .= '<label>Title</label>';
			// @phpstan-ignore-next-line
			$return .= "<input type='text' name='wds_doc_title' value='" . $title . "'>";
		$return     .= '</div>';

		$categories = get_categories( 'hide_empty=0&taxonomy=lab-document-types&' );
		$select     = '';
	foreach ( $categories as $category ) {
		if ( $category->cat_name ) {
			$option = '<option value="' . $category->slug . '"';
			$option .= '>';
			$option .= $category->cat_name;
			$option .= '</option>';
			$select .= $option;
		}
	}
	if ( $select ) {
		$return       .= "<div class='wds-group-form-item'>";
		  $return     .= '<label>Type</label>';
		  $return     .= "<select name='type'>";
			  $return .= $select;
		  $return     .= '</select>';
		$return       .= '</div>';
	}
		$return     .= "<div class='wds-group-form-item'>";
			$return .= '<label>Description</label>';
			// @phpstan-ignore-next-line
			$return .= "<textarea name='wds_doc_description' id='wds_doc_description'>" . $description . '</textarea>';
		$return     .= '</div>';
		$return     .= "<div class='wds-group-form-item'>";
			$return .= '<label>File</label>';
			$return .= "<input type='file' name='attachment'>";
		$return     .= '</div>';
		$return     .= "<div class='wds-group-form-buttons'>";
			$return .= "<input class='red-button' type='submit' name='upload_doc' id='upload_doc' value='Upload'>";
		$return     .= '</div>';
	$return         .= '</form>';
	$return         .= '</div>';
	$return         .= '</div>';
	return $return;
}


//form action
//add_action('init','wds_docs_action',50);
function wds_docs_action() {
	global $wpdb, $user_ID;
	//if(is_page('upload-documents')){
	if ( $_FILES ) {
		$title       = $_POST['wds_doc_title'];
		$description = $_POST['wds_doc_description'];
		if ( $title && $description ) {
			$args    = array(
				'post_title'   => $title,
				'post_content' => $description,
				'post_status'  => 'publish',
				'post_author'  => $user_ID,
				'post_type'    => 'lab-documents',
			);
			$post_id = wp_insert_post( $args );
			require_once ABSPATH . 'wp-admin' . '/includes/image.php';
			require_once ABSPATH . 'wp-admin' . '/includes/file.php';
			require_once ABSPATH . 'wp-admin' . '/includes/media.php';
			foreach ( $_FILES as $file => $array ) {
				$attachment = array(
					'post_title' => $title,
				);
				$attach_id  = media_handle_upload( $file, $post_id, $attachment );
			}
			$go = site_url() . '/upload-documents/?uploaded=true';
			wp_redirect( $go );
			exit();
		}
	}

	//}
}

