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
            'account'    => ( $sliFreemius->is_paying_or_trial() ? __( 'License', 'sl-insta' ) : __( 'Account', 'sl-insta' ) ),
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
    // Open Help inside the admin on Free too. Since SDK 2.9, Freemius turns Help into an external link that opens in a
    // new tab for free versions, so on Free I hide that link and add an admin page that renders the same contact form.
    if ( !sliFreemius()->is_premium() ) {
        sliFreemius()->add_filter(
            'is_submenu_visible',
            function ( $isVisible, $menuId ) {
                return ( $menuId === 'contact' ? false : $isVisible );
            },
            10,
            2
        );
        // Runs after Freemius adds its menu items, so Help can go right above Pricing as it does on the paid plans
        add_action( 'admin_menu', function () {
            global $submenu;
            // Until the user opts in or skips, Freemius replaces the menu with the opt-in page. Adding Help then would
            // make it the menu's only item, so WordPress would link "Instagram Feeds" to Help instead of the opt-in.
            if ( sliFreemius()->is_activation_mode() ) {
                return;
            }
            $parent = 'spotlight-instagram';
            $slugs = array_column( array_values( $submenu[$parent] ?? [] ), 2 );
            $position = array_search( $parent . '-pricing', $slugs, true );
            $label = __( 'Help', 'sl-insta' );
            $hook = add_submenu_page(
                $parent,
                sliFreemius()->get_plugin_name() . ' &ndash; ' . $label,
                '<span class="fs-submenu-item spotlight-social-photo-feeds contact">' . $label . '</span>',
                'manage_options',
                $parent . '-help',
                [sliFreemius(), '_contact_page_render'],
                ( $position === false ? null : $position )
            );
            if ( $hook ) {
                add_action( 'load-' . $hook, ['Freemius', '_clean_admin_content_section'] );
            }
        }, WP_FS__LOWEST_PRIORITY + 1 );
    }
    // Signal that SDK was initiated.
    do_action( 'sli_freemius_loaded' );
}