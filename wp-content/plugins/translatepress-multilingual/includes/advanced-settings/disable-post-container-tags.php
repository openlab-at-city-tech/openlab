<?php

/** Post title */
add_filter( 'trp_register_advanced_settings', 'trp_register_disable_post_container_tags_for_post_title', 510 );
function trp_register_disable_post_container_tags_for_post_title( $settings_array ){
	$settings_array[] = array(
		'name'          => 'disable_post_container_tags_for_post_title',
		'type'          => 'checkbox',
		'label'         => esc_html__( 'Disable post container tags for post title', 'translatepress-multilingual' ),
		'description'   => wp_kses( __( 'It disables search indexing the post title in translated languages.<br/>Useful when the title of the post doesn\'t allow HTML thus breaking the page.', 'translatepress-multilingual' ), array( 'br' => array() ) ),
        'id'            =>'debug',
	);
	return $settings_array;
}

add_filter( 'trp_before_running_hooks', 'trp_remove_hooks_to_disable_post_title_search_wraps' );
function trp_remove_hooks_to_disable_post_title_search_wraps( $trp_loader ){
    $option = get_option( 'trp_advanced_settings', true );
    if ( isset( $option['disable_post_container_tags_for_post_title'] ) && $option['disable_post_container_tags_for_post_title'] === 'yes' ) {
        $trp                = TRP_Translate_Press::get_trp_instance();
        $translation_render = $trp->get_component( 'translation_render' );
        $trp_loader->remove_hook( 'the_title', 'wrap_with_post_id', $translation_render );
    }
}


/** Post content */
add_filter( 'trp_register_advanced_settings', 'trp_register_disable_post_container_tags_for_post_content', 520 );
function trp_register_disable_post_container_tags_for_post_content( $settings_array ){
    $settings_array[] = array(
        'name'          => 'disable_post_container_tags_for_post_content',
        'type'          => 'checkbox',
        'label'         => esc_html__( 'Disable post container tags for post content', 'translatepress-multilingual' ),
        'description'   => wp_kses( __( 'It disables search indexing the post content in translated languages.<br/>Useful when the content of the post doesn\'t allow HTML thus breaking the page.', 'translatepress-multilingual' ), array( 'br' => array() ) ),
        'id'            =>'debug',
    );
    return $settings_array;
}

add_filter( 'trp_before_running_hooks', 'trp_remove_hooks_to_disable_post_content_search_wraps' );
function trp_remove_hooks_to_disable_post_content_search_wraps( $trp_loader ){
    $option = get_option( 'trp_advanced_settings', true );
    if ( isset( $option['disable_post_container_tags_for_post_content'] ) && $option['disable_post_container_tags_for_post_content'] === 'yes' ) {
        $trp                = TRP_Translate_Press::get_trp_instance();
        $translation_render = $trp->get_component( 'translation_render' );
        $trp_loader->remove_hook( 'the_content', 'wrap_with_post_id', $translation_render );
	    remove_action( 'do_shortcode_tag', 'tp_oxygen_search_compatibility', 10, 4 );
    }
}


