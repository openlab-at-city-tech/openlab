<?php

$meta_boxes = array();

// Page meta box
$meta_boxes[] = array(
	'id' => 'rain-page-meta-box',
	'title' => 'Page settings',
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => 'Page subtitle',
			'desc' => '',
			'id' => 'rain_subtitle',
			'type' => 'text',
			'desc' => '',
			'std' => ''
		),
		array(
			'name' => 'Hide page header',
			'desc' => '',
			'id' => 'rain_hide_page_header',
			'type' => 'checkbox',
			'desc' => '',
			'std' => ''
		)
	)
);

// Portfolio meta box
$meta_boxes[] = array(
	'id' => 'rain-portfolio-meta-box',
	'title' => 'Portfolio settings',
	'pages' => array( 'page' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => 'Exclude categories',
			'id' => 'rain_exclude_categories',
			'type' => 'multicheck',
			'desc' => ''
		),
		array(
			'name' => 'Hide filter',
			'id' => 'rain_hide_filter',
			'type' => 'checkbox',
			'desc' => ''
		)
	)
);

// Project meta box
$meta_boxes[] = array(
	'id' => 'rain-project-meta-box',
	'title' => 'Project settings',
	'pages' => array( 'project' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => 'Project subtitle',
			'desc' => '',
			'id' => 'rain_subtitle',
			'type' => 'text',
			'desc' => '',
			'std' => ''
		),
		array(
			'name' => 'Hide page header',
			'desc' => '',
			'id' => 'rain_hide_page_header',
			'type' => 'checkbox',
			'desc' => '',
			'std' => ''
		),
		array(
			'name' => 'Custom Header Image',
			'desc' => '',
			'id' => 'rain_project_image',
			'type' => 'upload',
			'desc' => '',
			'std' => ''
		)
	)
);

foreach ( $meta_boxes as $meta_box ) {
	new ThemeRain_Meta_Box( $meta_box );
}

class ThemeRain_Meta_Box {

	// Create meta box based on given data
	function __construct( $meta_box ) {

		// Assign meta box values to local variables and add it's missed values
		$this->meta_box = $meta_box;

		// Add meta box
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		// Save post meta
		add_action( 'save_post', array( $this, 'save_meta_box' ), 1, 2 );
	}

	// Add meta box for multiple post types
	function add_meta_boxes() {

		foreach ( $this->meta_box['pages'] as $page ) {

			add_meta_box(
				$this->meta_box['id'],
				$this->meta_box['title'],
				array( $this, 'build_meta_box' ),
				$page,
				$this->meta_box['context'],
				$this->meta_box['priority']
			);
		}
	}

	// Callback function to show fields in meta box
	function build_meta_box() {

		global $post;

		// Use nonce for verification
        echo '<input type="hidden" name="' . $this->meta_box['id'] . '_nonce" value="' . wp_create_nonce( $this->meta_box['id'] ) . '" />';

		echo '<table class="form-table">';

			foreach ( $this->meta_box['fields'] as $field ) {

				$meta = get_post_meta( $post->ID, $field['id'], $field['type'] != 'multicheck' );

				echo '<tr>';

					echo '<th><label for="' . $field['id'] . '">' . $field['name'] . '</label></th>';

					echo '<td>';
						switch ( $field['type'] ) {
							case 'text' :
								echo '<input type="text" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . ( $meta ? $meta : $field['std'] ) . '" class="widefat" />';
								break;

							case 'textarea' :
								echo '<textarea name="' . $field['id'] . '" id="' . $field['id'] . '" rows="4" cols="50" class="widefat">' . ( $meta ? $meta : $field['std'] ) . '</textarea>';
								break;

							case 'select' :
								echo '<select name="' . $field['id'] . '" id="' . $field['id'] . '">';
								foreach ( $field['options'] as $value => $option ) {
									echo '<option value="' . $value . '"' . ( $meta == $value ? ' selected="selected"' : '' ) . '>' . $option . '</option>';
								}
								echo '</select>';
								break;

							case 'radio' :
								foreach ( $field['options'] as $value => $option ) {
									echo '<label><input type="radio" name="' . $field['id'] . '" value="' . $value . '" ' . ( $meta == $value ? ' checked="checked"' : '' ) . ' />' . $option . '</label> ';
								}
								break;

							case 'checkbox' :
								echo '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '"' . ( $meta ? ' checked="checked"' : '' ) . ' /> ';
								break;

							case 'multicheck' :
								$taxonomies = get_categories( array( 'hide_empty' => false, 'taxonomy' => 'project-category' ) );

								if ( $taxonomies ) {
									foreach( $taxonomies as $taxonomy ) {
										echo '<label><input type="checkbox" name="' . $field['id'] . '[' . $taxonomy->term_id . ']" id="' . $taxonomy->slug . '" value="' . $taxonomy->term_id . '"' . ( in_array( $taxonomy->term_id, $meta ) ? ' checked="checked"' : '' ) . ' /> ' . $taxonomy->name . '</label>';
										echo '<br>';
									}
								} else {
									echo '<p>No categories found</p>';
								}
								break;

							case 'upload' :
								?>
								<script>
									jQuery( function( $ ) {
										var frame;

										$( '#<?php echo $field['id']; ?>_button' ).on( 'click', function( e ) {
											e.preventDefault();

											var options = {
												state: 'insert',
												frame: 'post'
											};

											frame = wp.media( options ).open();

											frame.menu.get( 'view' ).unset( 'gallery' );
											frame.menu.get( 'view' ).unset( 'featured-image' );

											frame.toolbar.get( 'view' ).set( {
												insert: {
													style: 'primary',
													text: 'Insert',

													click: function() {
														var models = frame.state().get( 'selection' ),
															url = models.first().attributes.url;

														$( '#<?php echo $field['id']; ?>' ).val( url ); 
														$( '#<?php echo $field['id']; ?>_image' ).attr( { src: url } );

														frame.close();
													}
												}
											} );
										} );
									} );
								</script>
								<?php

								echo '<input type="text" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . ( $meta ? $meta : '' ) . '" size="30" /> <button type="button" class="button button-secondary" id="' . $field['id'] . '_button">Browse</button><p><img style="max-width: 100%;" id="' . $field['id'] . '_image" src="' . ( $meta ? $meta : $field['std'] ) . '" /></p>';
								break;
						}

						if ( $field['desc'] ) echo '<p class="description">' . $field['desc'] . '</p>';
					echo '</td>';
				echo '</tr>';
			}
		echo '</table>';
	}

	// Save data from meta box
	function save_meta_box( $post_id, $post_object ) {

		global $pagenow;

		// Don't save if $_POST is empty
		if ( empty( $_POST ) || ( isset( $_POST['vc_inline'] ) && $_POST['vc_inline'] == true ) )
			return $post_id;

		// Don't save during quick edit
		if ( $pagenow == 'admin-ajax.php' )
			return $post_id;

		// Don't save during autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Don't save if viewing a revision
		if ( $post_object->post_type == 'revision' || $pagenow == 'revision.php' )
			return $post_id;

		// Verify nonce
		if ( isset( $_POST[ $this->meta_box['id'] . '_nonce'] ) && ! wp_verify_nonce( $_POST[ $this->meta_box['id'] . '_nonce'], $this->meta_box['id'] ) )
			return $post_id;

		// Check permissions
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		foreach ( $this->meta_box['fields'] as $field ) {

			$old = get_post_meta( $post_id, $field['id'], 'multicheck' != $field['type'] );
			$new = isset( $_POST[$field['id']] ) ? $_POST[$field['id']] : '';

			if ( $field['type'] == 'multicheck' ) {

				if ( empty( $new ) ) {
					$new = array();
				}

				$new_array = array_diff( $new, $old );
				$old_array = array_diff( $old, $new );

				foreach ( $new_array as $new_add ) {
					add_post_meta( $post_id, $field['id'], $new_add, false );
				}

				foreach ( $old_array as $old_delete ) {
					delete_post_meta( $post_id, $field['id'], $old_delete );
				}

			} else {

				if ( $new && $new != $old ) {
					update_post_meta( $post_id, $field['id'], $new );
				} elseif ( '' == $new && $old ) {
					delete_post_meta( $post_id, $field['id'], $old );
				}

			}
		}
	}
}