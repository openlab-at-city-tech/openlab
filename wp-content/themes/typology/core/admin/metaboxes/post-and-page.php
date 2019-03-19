<?php

/**
 * Load page metaboxes
 *
 * Callback function for page metaboxes load
 * @since 1.5
 */
if (!function_exists('typology_load_metaboxes')) :
	function typology_load_metaboxes() {
		
		/* Display settings metabox */
		add_meta_box(
			'typology_display_settings',
			esc_html__('Display settings', 'typology'),
			'typology_display_settings_metabox',
			array('page', 'post'),
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
 * @since  1.5
 */

if ( !function_exists( 'typology_save_metaboxes' ) ) :
	function typology_save_metaboxes( $post_id, $post ) {
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;
		
		if ( !isset( $_POST['typology_post_metabox_nonce'] ) || !wp_verify_nonce( $_POST['typology_post_metabox_nonce'], 'typology_post_metabox_save' ) ) {
			return;
		}
		
		if ( ($post->post_type == 'post' || $post->post_type == 'page') && isset( $_POST['typology'] ) ) {
			$post_type = get_post_type_object( $post->post_type );
			if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
				return $post_id;
			
			$typology_meta = array();
			
			if( isset( $_POST['typology']['display_settings'] ) ){
				$typology_meta['display_settings'] = $_POST['typology']['display_settings'];
				
				if( $_POST['typology']['display_settings'] == 'custom' ){
					
					if( isset( $_POST['typology']['cover'] ) ){
						$typology_meta['cover'] = $_POST['typology']['cover'];
					}
					if( isset( $_POST['typology']['fimg'] ) ){
						$typology_meta['fimg'] = $_POST['typology']['fimg'];
					}
					
				}
				
			}
			
			if(!empty($typology_meta)){
				update_post_meta( $post_id, '_typology_meta', $typology_meta );
			} else {
				delete_post_meta( $post_id, '_typology_meta');
			}
			
		}
	}
endif;

/**
 * Layout metabox
 *
 * Callback function to create layout metabox
 *
 * @since  1.5
 */

if ( !function_exists( 'typology_display_settings_metabox' ) ) :
	function typology_display_settings_metabox( $object ) {
		wp_nonce_field( 'typology_post_metabox_save', 'typology_post_metabox_nonce' );
		
		if(function_exists('typology_get_' . $object->post_type . '_meta')){
			$meta = call_user_func('typology_get_' . $object->post_type . '_meta', $object->ID);
		}
		
		if(empty($meta)){
		    return false;
		}
		
		?>
		<label>
			<input type="radio" class="typology-display-settings"  name="typology[display_settings]" value="inherit" <?php checked($meta['display_settings'], 'inherit'); ?>>
			<?php esc_html_e( 'Inherit from theme options', 'typology' ); ?>
		</label>
		<br>
		<label>
			<input type="radio" class="typology-display-settings" name="typology[display_settings]" value="custom" <?php checked($meta['display_settings'], 'custom'); ?>>
			<?php esc_html_e( 'Customize', 'typology' ); ?>
		</label>
		
		<div class="typology-watch-for-changes" data-watch="typology-display-settings" data-hide-on-value="inherit">
			<label>
				<input type="hidden" name="typology[cover]" value="0">
				<h4><input type="checkbox" name="typology[cover]" value="1" <?php checked($meta['cover'], 1); ?>> <?php esc_html_e( 'Display cover', 'typology' ); ?></h4>
			</label>
		</div>
		
		<div class="typology-watch-for-changes" data-watch="typology-display-settings" data-hide-on-value="inherit">
			<h4><?php esc_html_e('Display featured image', 'typology') ?></h4>
			<label>
				<input type="radio" name="typology[fimg]" value="cover" <?php checked($meta['fimg'], 'cover'); ?>>
				<?php esc_html_e('As the post cover background', 'typology'); ?>
				<br>
			</label>
			<label>
				<input type="radio" name="typology[fimg]" value="content" <?php checked($meta['fimg'], 'content'); ?>>
				<?php esc_html_e('Inside the post content', 'typology'); ?>
				<br>
			</label>
			<label>
				<input type="radio" name="typology[fimg]" value="none" <?php checked($meta['fimg'], 'none'); ?>>
				<?php esc_html_e('Do not display', 'typology'); ?>
			</label>
		</div>
		<?php
	}
endif;

