<?php

su_add_shortcode( array(
		'id' => 'posts',
		'callback' => 'su_shortcode_posts',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/posts.svg',
		'name' => __( 'Posts', 'shortcodes-ultimate' ),
		'type' => 'single',
		'group' => 'other',
		'article' => 'http://docs.getshortcodes.com/article/43-posts',
		'atts' => array(
			'template' => array(
				'default' => 'templates/default-loop.php', 'name' => __( 'Template', 'shortcodes-ultimate' ),
				'desc' => __( 'Relative path to the template file. Default templates placed in the plugin directory (templates folder). You can copy them under your theme directory and modify as you want. You can use following default templates that already available in the plugin directory:<br/><b%value>templates/default-loop.php</b> - posts loop<br/><b%value>templates/teaser-loop.php</b> - posts loop with thumbnail and title<br/><b%value>templates/single-post.php</b> - single post template<br/><b%value>templates/list-loop.php</b> - unordered list with posts titles', 'shortcodes-ultimate' )
			),
			'id' => array(
				'default' => '',
				'name' => __( 'Post ID\'s', 'shortcodes-ultimate' ),
				'desc' => __( 'Enter comma separated ID\'s of the posts that you want to show', 'shortcodes-ultimate' )
			),
			'posts_per_page' => array(
				'type' => 'number',
				'min' => -1,
				'max' => 10000,
				'step' => 1,
				'default' => get_option( 'posts_per_page' ),
				'name' => __( 'Posts per page', 'shortcodes-ultimate' ),
				'desc' => __( 'Specify number of posts that you want to show. Enter -1 to get all posts', 'shortcodes-ultimate' )
			),
			'post_type' => array(
				'type' => 'post_type',
				'multiple' => true,
				'values' => array(),
				'default' => 'post',
				'name' => __( 'Post types', 'shortcodes-ultimate' ),
				'desc' => __( 'Select post types. Hold Ctrl key to select multiple post types', 'shortcodes-ultimate' )
			),
			'taxonomy' => array(
				'type' => 'taxonomy',
				'values' => array(),
				'default' => 'category',
				'name' => __( 'Taxonomy', 'shortcodes-ultimate' ),
				'desc' => __( 'Select taxonomy to show posts from', 'shortcodes-ultimate' )
			),
			'tax_term' => array(
				'type' => 'term',
				'multiple' => true,
				'values' => array(),
				'default' => '',
				'name' => __( 'Terms', 'shortcodes-ultimate' ),
				'desc' => __( 'Select terms to show posts from', 'shortcodes-ultimate' )
			),
			'tax_operator' => array(
				'type' => 'select',
				'values' => array(
					'IN'     => __( 'IN - posts that have any of selected categories terms', 'shortcodes-ultimate' ),
					'NOT IN' => __( 'NOT IN - posts that is does not have any of selected terms', 'shortcodes-ultimate' ),
					'AND'    => __( 'AND - posts that have all selected terms', 'shortcodes-ultimate' ),
				),
				'default' => 'IN',
				'name' => __( 'Taxonomy term operator', 'shortcodes-ultimate' ),
				'desc' => __( 'Operator to test', 'shortcodes-ultimate' )
			),
			// 'author' => array(
			//  'type' => 'select',
			//  'multiple' => true,
			//  'values' => Su_Tools::get_users(),
			//  'default' => 'default',
			//  'name' => __( 'Authors', 'shortcodes-ultimate' ),
			//  'desc' => __( 'Choose the authors whose posts you want to show. Enter here comma-separated list of users (IDs). Example: 1,7,18', 'shortcodes-ultimate' )
			// ),
			'author' => array(
				'default' => '',
				'name' => __( 'Authors', 'shortcodes-ultimate' ),
				'desc' => __( 'Enter here comma-separated list of author\'s IDs. Example: 1,7,18', 'shortcodes-ultimate' )
			),
			'meta_key' => array(
				'default' => '',
				'name' => __( 'Meta key', 'shortcodes-ultimate' ),
				'desc' => __( 'Enter meta key name to show posts that have this key', 'shortcodes-ultimate' )
			),
			'offset' => array(
				'type' => 'number',
				'min' => 0,
				'max' => 10000,
				'step' => 1, 'default' => 0,
				'name' => __( 'Offset', 'shortcodes-ultimate' ),
				'desc' => __( 'Specify offset to start posts loop not from first post', 'shortcodes-ultimate' )
			),
			'order' => array(
				'type' => 'select',
				'values' => array(
					'desc' => __( 'Descending', 'shortcodes-ultimate' ),
					'asc' => __( 'Ascending', 'shortcodes-ultimate' )
				),
				'default' => 'DESC',
				'name' => __( 'Order', 'shortcodes-ultimate' ),
				'desc' => __( 'Posts order', 'shortcodes-ultimate' )
			),
			'orderby' => array(
				'type' => 'select',
				'values' => array(
					'none' => __( 'None', 'shortcodes-ultimate' ),
					'id' => __( 'Post ID', 'shortcodes-ultimate' ),
					'author' => __( 'Post author', 'shortcodes-ultimate' ),
					'title' => __( 'Post title', 'shortcodes-ultimate' ),
					'name' => __( 'Post slug', 'shortcodes-ultimate' ),
					'date' => __( 'Date', 'shortcodes-ultimate' ), 'modified' => __( 'Last modified date', 'shortcodes-ultimate' ),
					'parent' => __( 'Post parent', 'shortcodes-ultimate' ),
					'rand' => __( 'Random', 'shortcodes-ultimate' ), 'comment_count' => __( 'Comments number', 'shortcodes-ultimate' ),
					'menu_order' => __( 'Menu order', 'shortcodes-ultimate' ), 'meta_value' => __( 'Meta key values', 'shortcodes-ultimate' ),
				),
				'default' => 'date',
				'name' => __( 'Order by', 'shortcodes-ultimate' ),
				'desc' => __( 'Order posts by', 'shortcodes-ultimate' )
			),
			'post_parent' => array(
				'default' => '',
				'name' => __( 'Post parent', 'shortcodes-ultimate' ),
				'desc' => __( 'Show childrens of entered post (enter post ID)', 'shortcodes-ultimate' )
			),
			'post_status' => array(
				'type' => 'select',
				'values' => array(
					'publish' => __( 'Published', 'shortcodes-ultimate' ),
					'pending' => __( 'Pending', 'shortcodes-ultimate' ),
					'draft' => __( 'Draft', 'shortcodes-ultimate' ),
					'auto-draft' => __( 'Auto-draft', 'shortcodes-ultimate' ),
					'future' => __( 'Future post', 'shortcodes-ultimate' ),
					'private' => __( 'Private post', 'shortcodes-ultimate' ),
					'inherit' => __( 'Inherit', 'shortcodes-ultimate' ),
					'trash' => __( 'Trashed', 'shortcodes-ultimate' ),
					'any' => __( 'Any', 'shortcodes-ultimate' ),
				),
				'default' => 'publish',
				'name' => __( 'Post status', 'shortcodes-ultimate' ),
				'desc' => __( 'Show only posts with selected status', 'shortcodes-ultimate' )
			),
			'ignore_sticky_posts' => array(
				'type' => 'bool',
				'default' => 'no',
				'name' => __( 'Ignore sticky', 'shortcodes-ultimate' ),
				'desc' => __( 'Select Yes to ignore posts that is sticked', 'shortcodes-ultimate' )
			)
		),
		'desc' => __( 'Custom posts query with customizable template', 'shortcodes-ultimate' ),
		'icon' => 'th-list',
	) );

function su_shortcode_posts( $atts = null, $content = null ) {

	$original_atts = $atts;

	// Parse attributes
	$atts = shortcode_atts( array(
			'template'            => 'templates/default-loop.php',
			'id'                  => false,
			'posts_per_page'      => get_option( 'posts_per_page' ),
			'post_type'           => 'post',
			'taxonomy'            => 'category',
			'tax_term'            => false,
			'tax_operator'        => 'IN',
			'author'              => '',
			'tag'                 => '',
			'meta_key'            => '',
			'offset'              => 0,
			'order'               => 'DESC',
			'orderby'             => 'date',
			'post_parent'         => false,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 'no'
		), $atts, 'posts' );

	$author = sanitize_text_field( $atts['author'] );
	$id = $atts['id']; // Sanitized later as an array of integers
	$ignore_sticky_posts = ( bool ) ( $atts['ignore_sticky_posts'] === 'yes' ) ? true : false;
	$meta_key = sanitize_text_field( $atts['meta_key'] );
	$offset = intval( $atts['offset'] );
	$order = sanitize_key( $atts['order'] );
	$orderby = sanitize_key( $atts['orderby'] );
	$post_parent = $atts['post_parent'];
	$post_status = $atts['post_status'];
	$post_type = sanitize_text_field( $atts['post_type'] );
	$posts_per_page = intval( $atts['posts_per_page'] );
	$tag = sanitize_text_field( $atts['tag'] );
	$tax_operator = $atts['tax_operator'];
	$tax_term = sanitize_text_field( $atts['tax_term'] );
	$taxonomy = sanitize_key( $atts['taxonomy'] );
	// Set up initial query for post
	$args = array(
		'category_name'  => '',
		'order'          => $order,
		'orderby'        => $orderby,
		'post_type'      => explode( ',', $post_type ),
		'posts_per_page' => $posts_per_page,
		'tag'            => $tag
	);
	// Ignore Sticky Posts
	if ( $ignore_sticky_posts ) $args['ignore_sticky_posts'] = true;
	// Meta key (for ordering)
	if ( !empty( $meta_key ) ) $args['meta_key'] = $meta_key;
	// If Post IDs
	if ( $id ) {
		$posts_in = array_map( 'intval', explode( ',', $id ) );
		$args['post__in'] = $posts_in;
	}
	// Post Author
	if ( !empty( $author ) ) $args['author'] = $author;
	// Offset
	if ( !empty( $offset ) ) $args['offset'] = $offset;
	// Post Status
	$post_status = explode( ', ', $post_status );
	$validated = array();
	$available = array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash', 'any' );
	foreach ( $post_status as $unvalidated ) {
		if ( in_array( $unvalidated, $available ) ) $validated[] = $unvalidated;
	}
	if ( !empty( $validated ) ) $args['post_status'] = $validated;
	// If taxonomy attributes, create a taxonomy query
	if ( !empty( $taxonomy ) && !empty( $tax_term ) ) {
		// Term string to array
		$tax_term = explode( ',', $tax_term );
		// Validate operator
		$tax_operator = str_replace( array( 0, 1, 2 ), array( 'IN', 'NOT IN', 'AND' ), $tax_operator );
		if ( !in_array( $tax_operator, array( 'IN', 'NOT IN', 'AND' ) ) ) $tax_operator = 'IN';
		$tax_args = array( 'tax_query' => array( array(
					'taxonomy' => $taxonomy,
					'field' => ( is_numeric( $tax_term[0] ) ) ? 'id' : 'slug',
					'terms' => $tax_term,
					'operator' => $tax_operator ) ) );
		// Check for multiple taxonomy queries
		$count = 2;
		$more_tax_queries = false;
		while (
			isset( $original_atts['taxonomy_' . $count] ) &&
			! empty( $original_atts['taxonomy_' . $count] ) &&
			isset( $original_atts['tax_' . $count . '_term'] ) &&
			! empty( $original_atts['tax_' . $count . '_term'] )
		) {
			// Sanitize values
			$more_tax_queries = true;
			$taxonomy = sanitize_key( $original_atts['taxonomy_' . $count] );
			$terms = explode( ', ', sanitize_text_field( $original_atts['tax_' . $count . '_term'] ) );
			$tax_operator = isset( $original_atts['tax_' . $count . '_operator'] ) ? $original_atts[
			'tax_' . $count . '_operator'] : 'IN';
			$tax_operator = in_array( $tax_operator, array( 'IN', 'NOT IN', 'AND' ) ) ? $tax_operator : 'IN';
			$tax_args['tax_query'][] = array( 'taxonomy' => $taxonomy,
				'field' => 'slug',
				'terms' => $terms,
				'operator' => $tax_operator );
			$count++;
		}
		if ( $more_tax_queries ) {

			$tax_relation = 'AND';

			if (
				isset( $original_atts['tax_relation'] ) &&
				in_array( $original_atts['tax_relation'], array( 'AND', 'OR' ) )
			) {
				$tax_relation = $original_atts['tax_relation'];
			}

			$args['tax_query']['relation'] = $tax_relation;

		}

		$args = array_merge( $args, $tax_args );

	}

	// If post parent attribute, set up parent
	if ( $post_parent ) {
		if ( 'current' == $post_parent ) {
			global $post;
			$post_parent = $post->ID;
		}
		$args['post_parent'] = intval( $post_parent );
	}
	// Save original posts
	global $posts;
	$original_posts = $posts;
	// Query posts
	$posts = new WP_Query( $args );
	// Buffer output
	ob_start();
	// Search for template in stylesheet directory
	if ( file_exists( STYLESHEETPATH . '/' . $atts['template'] ) ) load_template( STYLESHEETPATH . '/' . $atts['template'], false );
	// Search for template in theme directory
	elseif ( file_exists( TEMPLATEPATH . '/' . $atts['template'] ) ) load_template( TEMPLATEPATH . '/' . $atts['template'], false );
	// Search for template in plugin directory
	elseif ( file_exists( path_join( dirname( SU_PLUGIN_FILE ), $atts['template'] ) ) ) load_template( path_join( dirname( SU_PLUGIN_FILE ), $atts['template'] ), false );
	// Template not found
	else echo su_error_message( 'Posts', __( 'template not found', 'shortcodes-ultimate' ) );
	$output = ob_get_contents();
	ob_end_clean();
	// Return original posts
	$posts = $original_posts;
	// Reset the query
	wp_reset_postdata();
	su_query_asset( 'css', 'su-shortcodes' );
	return $output;
}
