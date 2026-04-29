<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle bidirectional synchronization between KB Template setting and Page Template
 *
 * @copyright   Copyright (C) 2025, Echo Plugins
 */
class EPKB_Template_Sync {

	public function __construct() {
		// Hook into KB configuration updates to sync KB Template -> Page Template
		add_action( 'update_option', array( $this, 'sync_kb_template_to_page_template' ), 10, 3 );

		// Hook into page template updates to sync Page Template -> KB Template
		add_action( 'update_post_meta', array( $this, 'sync_page_template_to_kb_template' ), 10, 4 );
		add_action( 'add_post_meta', array( $this, 'sync_page_template_to_kb_template_add' ), 10, 3 );
		add_action( 'delete_post_meta', array( $this, 'sync_page_template_to_kb_template_delete' ), 10, 3 );
	}

	/**
	 * When KB Template changes, update the Page Template accordingly
	 *
	 * @param string $option_name
	 * @param mixed $old_value
	 * @param mixed $new_value
	 */
	public function sync_kb_template_to_page_template( $option_name, $old_value, $new_value ) {
		// Check if this is a KB configuration update
		if ( strpos( $option_name, EPKB_KB_Config_DB::KB_CONFIG_PREFIX ) !== 0 ) {
			return;
		}

		// Extract KB ID from option name
		$kb_id = str_replace( EPKB_KB_Config_DB::KB_CONFIG_PREFIX, '', $option_name );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			return;
		}

		// Check if templates_for_kb changed
		if ( ! is_array( $old_value ) || ! is_array( $new_value ) ) {
			return;
		}

		$old_template = isset( $old_value['templates_for_kb'] ) ? $old_value['templates_for_kb'] : '';
		$new_template = isset( $new_value['templates_for_kb'] ) ? $new_value['templates_for_kb'] : '';

		// If template setting hasn't changed, nothing to do
		if ( $old_template === $new_template ) {
			return;
		}

		// Get KB main pages
		if ( empty( $new_value['kb_main_pages'] ) || ! is_array( $new_value['kb_main_pages'] ) ) {
			return;
		}

		// Prevent recursion
		remove_action( 'update_post_meta', array( $this, 'sync_page_template_to_kb_template' ), 10 );
		remove_action( 'add_post_meta', array( $this, 'sync_page_template_to_kb_template_add' ), 10 );
		remove_action( 'delete_post_meta', array( $this, 'sync_page_template_to_kb_template_delete' ), 10 );

		// Update page template for all KB main pages
		foreach ( $new_value['kb_main_pages'] as $post_id => $post_title ) {
			$this->update_page_template_for_kb_template( $post_id, $new_template );
		}

		// Re-add the hooks
		add_action( 'update_post_meta', array( $this, 'sync_page_template_to_kb_template' ), 10, 4 );
		add_action( 'add_post_meta', array( $this, 'sync_page_template_to_kb_template_add' ), 10, 3 );
		add_action( 'delete_post_meta', array( $this, 'sync_page_template_to_kb_template_delete' ), 10, 3 );
	}

	/**
	 * Update the WordPress page template based on KB template setting
	 *
	 * @param int $post_id
	 * @param string $kb_template
	 */
	private function update_page_template_for_kb_template( $post_id, $kb_template ) {
		// Skip if not a valid post
		$post = get_post( $post_id );
		if ( empty( $post ) || $post->post_type !== 'page' ) {
			return;
		}

		// Determine the page template based on KB template
		// KB Template = 'kb_templates' -> Page Template = 'kb-block-page-template'
		// KB Template = 'current_theme_templates' -> Page Template = 'default'
		if ( $kb_template === 'kb_templates' ) {
			// Check if KB Block Page Template is available
			if ( EPKB_Block_Utilities::is_kb_block_page_template_available() ) {
				update_post_meta( $post_id, '_wp_page_template', EPKB_Abstract_Block::EPKB_KB_BLOCK_PAGE_TEMPLATE );
			}
		} else if ( $kb_template === 'current_theme_templates' ) {
			// Set to default template (delete the meta to use theme default)
			delete_post_meta( $post_id, '_wp_page_template' );
		}
	}

	/**
	 * When Page Template changes, update the KB Template accordingly
	 *
	 * @param int $meta_id
	 * @param int $post_id
	 * @param string $meta_key
	 * @param mixed $meta_value
	 */
	public function sync_page_template_to_kb_template( $meta_id, $post_id, $meta_key, $meta_value ) {
		// Only process _wp_page_template changes
		if ( $meta_key !== '_wp_page_template' ) {
			return;
		}

		$this->handle_page_template_change( $post_id, $meta_value );
	}

	/**
	 * Handle when page template is added
	 *
	 * @param int $post_id
	 * @param string $meta_key
	 * @param mixed $meta_value
	 */
	public function sync_page_template_to_kb_template_add( $post_id, $meta_key, $meta_value ) {
		// Only process _wp_page_template changes
		if ( $meta_key !== '_wp_page_template' ) {
			return;
		}

		$this->handle_page_template_change( $post_id, $meta_value );
	}

	/**
	 * Handle when page template is deleted (reset to default)
	 *
	 * @param array $meta_ids
	 * @param int $post_id
	 * @param string $meta_key
	 */
	public function sync_page_template_to_kb_template_delete( $meta_ids, $post_id, $meta_key ) {
		// Only process _wp_page_template changes
		if ( $meta_key !== '_wp_page_template' ) {
			return;
		}

		// When template meta is deleted, it means using default template
		$this->handle_page_template_change( $post_id, 'default' );
	}

	/**
	 * Handle page template change and update KB configuration
	 *
	 * @param int $post_id
	 * @param string $page_template
	 */
	private function handle_page_template_change( $post_id, $page_template ) {
		// Find which KB this page belongs to
		$kb_id = $this->get_kb_id_for_main_page( $post_id );
		if ( empty( $kb_id ) ) {
			return;
		}

		// Get current KB configuration
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $kb_config ) ) {
			return;
		}

		// Determine the new KB template based on page template
		// Page Template = 'kb-block-page-template' -> KB Template = 'kb_templates'
		// Any other template -> KB Template = 'current_theme_templates'
		$new_kb_template = '';
		if ( $page_template === EPKB_Abstract_Block::EPKB_KB_BLOCK_PAGE_TEMPLATE ) {
			$new_kb_template = 'kb_templates';
		} else {
			$new_kb_template = 'current_theme_templates';
		}

		// If KB template is already set correctly, nothing to do
		if ( isset( $kb_config['templates_for_kb'] ) && $kb_config['templates_for_kb'] === $new_kb_template ) {
			return;
		}

		// Update KB configuration
		$kb_config['templates_for_kb'] = $new_kb_template;

		// Also update article title display based on template (matching existing logic)
		$kb_config['article_content_enable_article_title'] = ( $new_kb_template === 'current_theme_templates' ) ? 'off' : 'on';

		// Prevent recursion
		remove_action( 'update_option', array( $this, 'sync_kb_template_to_page_template' ), 10 );

		// Save the updated configuration
		epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_id, $kb_config );

		// Re-add the hook
		add_action( 'update_option', array( $this, 'sync_kb_template_to_page_template' ), 10, 3 );
	}

	/**
	 * Find which KB a main page belongs to
	 *
	 * @param int $post_id
	 * @return int|null KB ID or null if not found
	 */
	private function get_kb_id_for_main_page( $post_id ) {
		// Get all KB configurations
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs( true );

		foreach ( $all_kb_configs as $one_kb_config ) {
			if ( empty( $one_kb_config['kb_main_pages'] ) || ! is_array( $one_kb_config['kb_main_pages'] ) ) {
				continue;
			}

			// Check if this post is a KB main page
			if ( array_key_exists( $post_id, $one_kb_config['kb_main_pages'] ) ) {
				return $one_kb_config['id'];
			}
		}

		return null;
	}
}