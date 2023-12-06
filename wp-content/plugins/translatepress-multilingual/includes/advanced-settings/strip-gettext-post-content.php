<?php
add_filter( 'trp_register_advanced_settings', 'trp_register_strip_gettext_post_content', 60 );
function trp_register_strip_gettext_post_content( $settings_array ){
	$settings_array[] = array(
		'name'          => 'strip_gettext_post_content',
		'type'          => 'checkbox',
		'label'         => esc_html__( 'Filter Gettext wrapping from post content and title', 'translatepress-multilingual' ),
		'description'   => wp_kses( __( 'Filters gettext wrapping such as #!trpst#trp-gettext from all updated post content and post title. Does not affect previous post content. <br/><strong>Database backup is recommended before switching on.</strong>', 'translatepress-multilingual' ), array( 'br' => array(), 'strong' => array()) ),
        'id'            =>'troubleshooting',
	);
	return $settings_array;
}

/**
 * Strip gettext wrapping from post title and content.
 * They will be regular strings, written in the language they were submitted.
 * Filter called both for wp_insert_post and wp_update_post
 */
add_filter('wp_insert_post_data', 'trp_filter_trpgettext_from_post_content', 10, 2 );
function trp_filter_trpgettext_from_post_content($data, $postarr ){
	$option = get_option( 'trp_advanced_settings', true );
	if ( isset( $option['strip_gettext_post_content'] ) && $option['strip_gettext_post_content'] === 'yes' && class_exists( 'TRP_Translation_Manager' ) ){
		$data['post_content'] = TRP_Translation_Manager::strip_gettext_tags($data['post_content']);
		$data['post_title'] = TRP_Translation_Manager::strip_gettext_tags($data['post_title']);
	}
	return $data;
}