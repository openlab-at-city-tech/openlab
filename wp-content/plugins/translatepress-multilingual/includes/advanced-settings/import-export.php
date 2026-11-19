<?php

if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'trp_register_advanced_settings', 'trp_register_import_export_separator', 700 );
function trp_register_import_export_separator( $settings_array ){
    $settings_array[] = array(
        'name'  => 'import_export',
        'type'  => 'separator',
        'label' => esc_html__( 'Import & Export', 'translatepress-multilingual' ),
        'id'    => 'import_export',
        // The sub-tab opens the tool itself. The section below is only reached through ?tab=import_export.
        'url'   => admin_url( 'admin.php?page=trp_import_export' ),
    );
    return $settings_array;
}

add_filter( 'trp_possible_values_for_tab', 'trp_import_export_advanced_tab' );
function trp_import_export_advanced_tab( $tabs ){
    $tabs[] = 'import_export';
    return $tabs;
}

add_filter( 'trp_register_advanced_settings', 'trp_register_import_export_translations', 80 );
function trp_register_import_export_translations( $settings_array ){
    $settings_array[] = array(
        'name'      => 'import_export_translations_title',
        'type'      => 'container_title',
        'label'     => esc_html__( 'Import & Export Translations', 'translatepress-multilingual' ),
        'id'        => 'import_export',
        'container' => 'import_export_translations',
    );
    $settings_array[] = array(
        'name'        => 'import_export_translations',
        'type'        => 'text',
        'label'       => '',
        'description' => esc_html__( 'Move translations and settings to another site, e.g. from staging to production.', 'translatepress-multilingual' )
            . '<br><a class="trp-submit-btn trp-ie-open-tool" href="' . esc_url( admin_url( 'admin.php?page=trp_import_export' ) ) . '">' . esc_html__( 'Open Import & Export', 'translatepress-multilingual' ) . '</a>',
        'id'          => 'import_export',
        'container'   => 'import_export_translations',
    );
    return $settings_array;
}
