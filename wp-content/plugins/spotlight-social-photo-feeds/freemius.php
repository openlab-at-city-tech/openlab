<?php

if ( !function_exists( 'sliFreemius' ) ) {
    // Create a helper function for easy SDK access.
    function sliFreemius() {
        /* @var $sliFreemius Freemius */
        global $sliFreemius;
        if ( !isset( $sliFreemius ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';
            try {
                $sliFreemius = fs_dynamic_init( [
                    'id'               => '5975',
                    'slug'             => 'spotlight-social-photo-feeds',
                    'type'             => 'plugin',
                    'public_key'       => 'pk_c3d236fabdccc79ce32cd916ce2a2',
                    'is_premium'       => false,
                    'has_addons'       => false,
                    'has_paid_plans'   => true,
                    'is_org_compliant' => true,
                    'has_affiliation'  => 'selected',
                    'menu'             => [
                        'slug'        => 'spotlight-instagram',
                        'pricing'     => true,
                        'support'     => false,
                        'contact'     => true,
                        'affiliation' => false,
                    ],
                    'is_live'          => true,
                ] );
            } catch ( Freemius_Exception $e ) {
                // Do nothing
                return null;
            }
        }
        $sliFreemius->override_i18n( [
            'account'    => __( 'License', 'sl-insta' ),
            'contact-us' => __( 'Help', 'sl-insta' ),
        ] );
        return $sliFreemius;
    }

    // Init Freemius.
    sliFreemius();
    // Set plugin icon
    sliFreemius()->add_filter( 'plugin_icon', function () {
        return __DIR__ . '/ui/images/spotlight-icon.png';
    } );
    // Disable affiliate notice
    sliFreemius()->add_filter( 'show_affiliate_program_notice', '__return_false' );
    // Disable auto deactivation
    sliFreemius()->add_filter( 'deactivate_on_activation', '__return_false' );
    // Disable redirect on activation
    sliFreemius()->add_filter( 'redirect_on_activation', '__return_false' );
    // New Freemius beta pricing page
    sliFreemius()->add_filter( 'freemius_pricing_js_path', function () {
        return plugin_dir_path( SL_INSTA_FILE ) . '/ui/freemius-pricing/freemius-pricing.js';
    } );
    // Signal that SDK was initiated.
    do_action( 'sli_freemius_loaded' );
}