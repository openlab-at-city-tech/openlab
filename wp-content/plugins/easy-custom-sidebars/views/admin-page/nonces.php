<?php 
/**
 * Create Nonce Fields for Security
 *
 * This ensures that the request to modify sidebars
 * was an intentional request from the user. Used in
 * the Ajax Reequest for validation.     
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 */
wp_nonce_field( 'ecs_delete_sidebar_instance', 'ecs_delete_sidebar_instance_nonce' );
wp_nonce_field( 'ecs_edit_sidebar_instance', 'ecs_edit_sidebar_instance_nonce' );
wp_nonce_field( 'ecs_sidebar_quick_search', 'ecs_sidebar_quick_search_nonce' );
wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
wp_nonce_field( 'add-menu_item', 'menu-settings-column-nonce' );
wp_nonce_field( 'ecs_add_sidebar_item', 'ecs_sidebar_settings_column_nonce' );
