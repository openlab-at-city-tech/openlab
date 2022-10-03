<?php
defined('ABSPATH') || die;

// Check users permissions
if ( !current_user_can('administrator') ) {
    wp_die( esc_html__('You do not have permission to manage Block Access', 'advanced-gutenberg') );
}

// Render form
$this->advgbBlocksFeatureForm(
    __('Block Access', 'advanced-gutenberg'), // Name of the feature
    'advgb_access_nonce_field', // Nonce field name
    'advgb_block_access_save', // Save button field name
    'save_access', // Status param value from URL after saving/failing redirection
    'blocks_list_access' // Block list hidden field name
);
