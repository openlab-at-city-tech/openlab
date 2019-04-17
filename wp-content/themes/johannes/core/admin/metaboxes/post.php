<?php
/**
 * Load post metaboxes
 *
 * Callback function for post metaboxes load
 *
 * @since  1.0
 */

if ( !function_exists( 'johannes_load_post_metaboxes' ) ) :
	function johannes_load_post_metaboxes() {

		add_meta_box(
			'johannes_post_display',
			esc_html__( 'Display Settings', 'johannes' ),
			'johannes_post_display_metabox',
			'post',
			'side',
			'default'
		);

	}
endif;


/**
 * Save post meta
 *
 * Callback function to save post meta data
 *
 * @since  1.0
 */

if ( !function_exists( 'johannes_save_post_metaboxes' ) ) :
	function johannes_save_post_metaboxes( $post_id, $post ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( !isset( $_POST['johannes_post_metabox_nonce'] ) || !wp_verify_nonce( $_POST['johannes_post_metabox_nonce'], 'johannes_post_metabox_save' ) ) {
			return;
		}

		if ( $post->post_type == 'post' && isset( $_POST['johannes'] ) ) {

			$post_type = get_post_type_object( $post->post_type );
			
			if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
				return $post_id;

			$meta = array();

			if ( isset( $_POST['johannes']['settings'] ) ) {

				$meta['settings'] = $_POST['johannes']['settings'];

				if ( $_POST['johannes']['settings'] == 'custom' ) {

					if ( isset( $_POST['johannes']['layout'] ) ) {
						$meta['layout'] = $_POST['johannes']['layout'];
					}

					if ( isset( $_POST['johannes']['sidebar'] ) ) {
						$meta['sidebar'] = $_POST['johannes']['sidebar'];
					}
				}
			}

			if ( !empty( $meta ) ) {
				update_post_meta( $post_id, '_johannes_meta', $meta );
			} else {
				delete_post_meta( $post_id, '_johannes_meta' );
			}

		}
	}
endif;

/**
 * Display metabox
 *
 * Callback function to create layout metabox
 *
 * @since  1.0
 */

if ( !function_exists( 'johannes_post_display_metabox' ) ) :
	function johannes_post_display_metabox( $object, $box ) {

		wp_nonce_field( 'johannes_post_metabox_save', 'johannes_post_metabox_nonce' );

		$meta = johannes_get_post_meta( $object->ID );
		$layouts = johannes_get_single_layouts( );
		$sidebar_layouts = johannes_get_sidebar_layouts( false, true );
		$sidebars = johannes_get_sidebars_list( );

?>
        <div class="johannes-opt-display">
			<label>
				<input type="radio" name="johannes[settings]" value="inherit" <?php checked( $meta['settings'], 'inherit' ); ?>>
				<?php esc_html_e( 'Inherit from theme options', 'johannes' ); ?>
			</label>
	        <br/>
			<label>
				<input type="radio" name="johannes[settings]" value="custom" <?php checked( $meta['settings'], 'custom' ); ?>>
				<?php esc_html_e( 'Customize', 'johannes' ); ?>
			</label>
		</div>

		<?php $class = $meta['settings'] == 'inherit' ? 'johannes-hidden' : ''; ?>
		<div class="johannes-opt-display-custom <?php echo esc_attr( $class ); ?>">
	        <h4><?php esc_html_e( 'Layout', 'johannes' ); ?></h4>
	        <ul class="johannes-img-select-wrap">
	            <?php foreach ( $layouts as $id => $layout ): ?>
	                <li>
	                    <img src="<?php echo esc_url( $layout['src'] ); ?>" title="<?php echo esc_attr( $layout['alt'] ); ?>" class="johannes-img-select <?php echo esc_attr( johannes_selected( $id, $meta['layout'], 'selected' ) ); ?>">
	                    <span><?php echo esc_html( $layout['alt'] ); ?></span>
	                    <input type="radio" class="johannes-hidden" name="johannes[layout]" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $meta['layout'] );?>/> </label>
	                </li>
	            <?php endforeach; ?>
	        </ul>

	        <h4><?php esc_html_e( 'Sidebar', 'johannes' ); ?></h4>

	        <ul class="johannes-img-select-wrap">
	            <?php foreach ( $sidebar_layouts as $id => $layout ): ?>
	                <li>
	                    <img src="<?php echo esc_url( $layout['src'] ); ?>" title="<?php echo esc_attr( $layout['alt'] ); ?>" class="johannes-img-select <?php echo esc_attr( johannes_selected( $id, $meta['sidebar']['position'], 'selected' ) ); ?>">
	                    <span><?php echo esc_html( $layout['alt'] ); ?></span>
	                    <input type="radio" class="johannes-hidden" name="johannes[sidebar][position]" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $meta['sidebar']['position'] );?>/> </label>
	                </li>
	            <?php endforeach; ?>
	        </ul>

	        <p>
	        	<select name="johannes[sidebar][classic]" class="widefat">
	                <?php foreach ( $sidebars as $id => $name ): ?>
	                    <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $meta['sidebar']['classic'] );?>><?php echo esc_html( $name ); ?></option>
	                <?php endforeach; ?>
	            </select>
	        </p>
	        <small class="howto"><?php esc_html_e( 'Choose standard sidebar to display', 'johannes' ); ?></small>

	        <p>
	        	<select name="johannes[sidebar][sticky]" class="widefat">
	                <?php foreach ( $sidebars as $id => $name ): ?>
	                    <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $meta['sidebar']['sticky'] );?>><?php echo esc_html( $name ); ?></option>
	                <?php endforeach; ?>
	            </select>
	        </p>
	        <small class="howto"><?php esc_html_e( 'Choose sticky sidebar to display', 'johannes' ); ?></small>

    	</div>

		<?php
	}
endif;
