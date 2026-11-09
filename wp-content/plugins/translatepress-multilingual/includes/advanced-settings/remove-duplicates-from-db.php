<?php

if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'trp_register_advanced_settings', 'trp_register_remove_duplicate_entries_from_db', 530 );
function trp_register_remove_duplicate_entries_from_db( $settings_array ){
    $gettext_optimization_pending = get_option( 'trp_updated_database_gettext_tables_optimization', 'yes' ) === 'no';
    $gettext_batch_task           = 'trp_gettext_tables_optimization_330';
    $gettext_batch_status         = get_option( $gettext_batch_task, 'is not set' );

    if ( $gettext_optimization_pending && $gettext_batch_status !== 'no' ) {
        if ( $gettext_batch_status === 'failed' ) {
            $action_url = wp_nonce_url(
                add_query_arg(
                    array(
                        'page'             => 'trp_advanced_page',
                        'tab'              => 'troubleshooting',
                        'trp_batch_action' => 'retry',
                        'trp_batch_task'   => $gettext_batch_task,
                    ),
                    admin_url( 'admin.php' )
                ),
                'trp_batch_task_action_' . $gettext_batch_task
            );
            $description = esc_html__( 'TranslatePress gettext database optimization failed.', 'translatepress-multilingual' ) . ' <a href="' . esc_url( $action_url ) . '">' . esc_html__( 'Retry', 'translatepress-multilingual' ) . '</a>';
        } else {
            $action_url = wp_nonce_url(
                add_query_arg(
                    array(
                        'page'                                  => 'trp_advanced_page',
                        'tab'                                   => 'troubleshooting',
                        'trp_start_gettext_tables_optimization' => '1',
                    ),
                    admin_url( 'admin.php' )
                ),
                'trp_start_gettext_tables_optimization'
            );
            $description = sprintf(
                wp_kses(
                    __( 'TranslatePress needs to optimize its gettext database tables. Back up the database, then <a href="%s">start the optimization</a>.', 'translatepress-multilingual' ),
                    array(
                        'a' => array(
                            'href' => array(),
                        ),
                    )
                ),
                esc_url( $action_url )
            );
        }

        $settings_array[] = array(
            'name'        => 'pending_gettext_database_optimization',
            'type'        => 'text',
            'label'       => esc_html__( 'Pending gettext database optimization', 'translatepress-multilingual' ),
            'description' => $description,
            'id'          => 'debug',
            'container'   => 'debug',
        );
    }

    $settings_array[] = array(
        'name'          => 'remove_duplicate_entries_from_db',
        'type'          => 'text',
        'label'         => esc_html__( 'Optimize TranslatePress database tables', 'translatepress-multilingual' ),
        'description'   => wp_kses_post( sprintf( __( '<a href="%s">Click here</a> to access the database optimization tool.', 'translatepress-multilingual' ), admin_url('admin.php?page=trp_remove_duplicate_rows') ) ) . '<br>' . esc_html__('It helps remove possible duplicate translations, clear unnecessary data and repair possible metadata issues.','translatepress-multilingual') . '<br>' . wp_kses_post(sprintf( __( '<a href="%s" target="_blank">Here</a> you can observe the last 5 SQL errors relevant to TranslatePress if they exist.', 'translatepress-multilingual' ), admin_url('admin.php?page=trp_error_manager') ) ),
        'id'            => 'debug',
        'container'     => 'debug'
    );
    return $settings_array;
}
