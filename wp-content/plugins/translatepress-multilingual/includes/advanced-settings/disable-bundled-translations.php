<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * Registers the "Disable bundled plugin translations" toggle on the Advanced Settings tab.
 *
 * When ON, TranslatePress will NOT short-circuit WordPress's textdomain loader to its own
 * bundled .mo / .l10n.php / .json files under `wp-content/plugins/translatepress/languages/`.
 * WordPress's default load order applies — translate.wordpress.org Language Packs at
 * `wp-content/languages/plugins/` win first, plugin folder is the fallback.
 *
 * Default OFF (use bundled). Provide this toggle so a user who prefers WP.org's community
 * pack, or who hits an edge case with the bundled files, can opt out without code changes.
 */
add_filter( 'trp_register_advanced_settings', 'trp_register_disable_bundled_translations', 35 );
function trp_register_disable_bundled_translations( $settings_array ) {
    $settings_array[] = array(
        'name'        => 'disable_bundled_translations',
        'type'        => 'checkbox',
        'label'       => esc_html__( 'Disable bundled plugin translations', 'translatepress-multilingual' ),
        'description' => wp_kses(
            __( 'By default, TranslatePress ships its own translation files in <code>wp-content/plugins/translatepress/languages/</code> and forces WordPress to use them instead of the language packs installed automatically from translate.wordpress.org.<br/>Enable this option to disable that override. WordPress will then load the community language pack from <code>wp-content/languages/plugins/</code> (if present) and fall back to the bundled files only when no pack is available.', 'translatepress-multilingual' ),
            array( 'br' => array(), 'code' => array() )
        ),
        'id'          => 'troubleshooting',
        'container'   => 'troubleshooting',
    );
    return $settings_array;
}

/**
 * Bridges the admin setting into the runtime filter `trp_use_bundled_translations` that
 * `class-translate-press.php::init_translation()` consults.
 *
 * Plugin code or other plugins can also hook the filter directly to disable bundled
 * translations programmatically without flipping the admin toggle. Example:
 *
 *     add_filter( 'trp_use_bundled_translations', '__return_false' );
 */
add_filter( 'trp_use_bundled_translations', 'trp_adst_disable_bundled_translations' );
function trp_adst_disable_bundled_translations( $enabled ) {
    $option = get_option( 'trp_advanced_settings', true );
    if ( isset( $option['disable_bundled_translations'] ) && $option['disable_bundled_translations'] === 'yes' ) {
        return false;
    }
    return $enabled;
}
