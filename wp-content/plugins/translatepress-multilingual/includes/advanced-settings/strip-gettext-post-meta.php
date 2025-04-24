<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'trp_register_advanced_settings', 'trp_register_strip_gettext_post_meta', 70 );
function trp_register_strip_gettext_post_meta( $settings_array ){
	$settings_array[] = array(
		'name'          => 'strip_gettext_post_meta',
		'type'          => 'checkbox',
		'label'         => esc_html__( 'Filter Gettext wrapping from post meta', 'translatepress-multilingual' ),
		'description'   => wp_kses( __( 'Filters gettext wrapping such as #!trpst#trp-gettext from all updated post meta. Does not affect previous post meta. <br/><strong>Database backup is recommended before switching on.</strong>', 'translatepress-multilingual' ), array( 'br' => array(), 'strong' => array()) ),
        'id'            => 'troubleshooting',
        'container'     => 'troubleshooting'
    );
	return $settings_array;
}

/**
 * Stripped gettext wrapping from wp_update_post_meta
 */
add_action( 'added_post_meta', 'trp_filter_trpgettext_from_updated_post_meta', 10, 4);
add_action( 'updated_postmeta', 'trp_filter_trpgettext_from_updated_post_meta', 10, 4);
function trp_filter_trpgettext_from_updated_post_meta($meta_id, $object_id, $meta_key, $meta_value){
	$option = get_option( 'trp_advanced_settings', true );
	if ( isset( $option['strip_gettext_post_meta'] ) && $option['strip_gettext_post_meta'] === 'yes' && class_exists( 'TRP_Translation_Manager' ) ){
		if ( is_serialized($meta_value) ){
			$unserialized_meta_value = unserialize($meta_value);
			$stripped_meta_value = trp_strip_gettext_array( $unserialized_meta_value );
			$stripped_meta_value = serialize( $stripped_meta_value );
		}else{
			$stripped_meta_value = trp_strip_gettext_array( $meta_value );
		}

		if ( $stripped_meta_value != $meta_value){
			remove_action('updated_postmeta','trp_filter_trpgettext_from_updated_post_meta' );
			update_post_meta( $object_id, $meta_key, $stripped_meta_value );
			add_action( 'updated_postmeta', 'trp_filter_trpgettext_from_updated_post_meta', 10, 4);
		}
	}
}

function trp_strip_gettext_array( $value ){
	if ( is_array( $value ) ){
		foreach( $value as $key => $item ){
			$value[$key] = trp_strip_gettext_array( $item );
		}
		return $value;
	}else{
		return TRP_Translation_Manager::strip_gettext_tags( $value );
	}
}
