<?php
add_filter( 'trp_register_advanced_settings', 'trp_register_remove_duplicate_entries_from_db', 530 );
function trp_register_remove_duplicate_entries_from_db( $settings_array ){
    $settings_array[] = array(
        'name'          => 'remove_duplicate_entries_from_db',
        'type'          => 'text',
        'label'         => esc_html__( 'Optimize TranslatePress database tables', 'translatepress-multilingual' ),
        'description'   => wp_kses_post( sprintf( __( 'Click <a href="%s">here</a> to remove duplicate rows from the database.', 'translatepress-multilingual' ), admin_url('admin.php?page=trp_remove_duplicate_rows') ) ),
    );
    return $settings_array;
}
