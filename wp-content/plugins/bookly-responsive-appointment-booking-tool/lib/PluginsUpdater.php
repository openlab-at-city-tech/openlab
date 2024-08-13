<?php
namespace Bookly\Lib;

class PluginsUpdater
{
    public static function init()
    {
        if ( defined( 'DOING_AJAX' ) ) {
            // Update Bookly add-ons
            add_action( 'wp_ajax_bookly_update_plugin', array( __CLASS__, 'updateAddon' ), 10, 0 );
            // Check update add-ns
            add_action( 'wp_ajax_bookly_check_update', array( __CLASS__, 'getAddonsUpdatingData' ), 10, 0 );
            add_action( 'wp_ajax_nopriv_bookly_speed_up_update_addons', array( __CLASS__, 'speedUpUpdateAddons' ), 10, 0 );
        }
        // Modify updating data
        add_action( 'after_plugin_row', array( __CLASS__, 'renderAfterPluginRow' ), 10, 3 );
        // Reduce time of last check for updates of Bookly plugins to quicker identify new versions
        add_action( 'upgrader_process_complete', array( __CLASS__, 'reduceTimeOfLastCheck' ), 10, 2 );

        // Enqueue plugins.js on page plugins.php
        add_action( 'pre_current_active_plugins', function() {
            $scripts = wp_scripts();
            $version = Plugin::getVersion();
            $resources = plugins_url( 'backend/resources', Plugin::getMainFile() );

            $scripts->add( 'bookly-plugins-page', $resources . '/js/plugins.js', array( 'jquery' ), $version );
            $scripts->enqueue( 'bookly-plugins-page' );
            PluginsUpdater::renderModal();
        } );

        add_action( 'update_plugins_api.booking-wp-plugin.com', function( $update, $plugin_data, $plugin_file, $locales ) {
            if ( $plugin_data['Author'] === 'Nota-Info' ) {
                if ( ! is_array( $update ) ) {
                    $update = array();
                }
                $update['version'] = $plugin_data['Version'];
            }

            return $update;
        }, 10, 4 );
    }

    /**
     * @param string[] $slugs
     * @return void
     */
    public static function speedUpUpdate( $slugs )
    {
        self::resetLastCheckTime( $slugs );

        wp_remote_post(
            admin_url( 'admin-ajax.php' ),
            array(
                'timeout' => 5,
                'redirection' => false,
                'sslverify' => false,
                'body' => array(
                    'action' => 'bookly_speed_up_update_addons',
                    'signature' => self::getSignature(),
                    'slugs' => $slugs,
                ),
            ) );
    }

    /**
     * @return void
     */
    public static function speedUpUpdateAddons()
    {
        if ( isset( $_POST['signature'], $_POST['slugs'] ) && $_POST['signature'] === self::getSignature() ) {
            $bookly_plugins = apply_filters( 'bookly_plugins', array() );
            /** @var Base\Plugin[] $plugins */
            $plugins = array();
            foreach ( $_POST['slugs'] as $slug ) {
                if ( isset( $bookly_plugins[ $slug ] ) ) {
                    $plugins[ $slug ] = $bookly_plugins[ $slug ];
                }
            }
            // Required for calling wp_version_check
            add_action( 'wp_doing_cron', '__return_true', PHP_INT_MAX, 1 );
            wp_version_check( array(), true );
            $update_plugins = get_site_transient( 'update_plugins' );
            if ( isset( $update_plugins->response ) ) {
                $auto_update_plugins = get_option( 'auto_update_plugins' ) ?: array();
                /**@var \stdClass $data */
                foreach ( $update_plugins->response as $data ) {
                    if ( isset( $data->slug, $plugins[ $data->slug ] )
                        && in_array( $plugins[ $data->slug ]::getBasename(), $auto_update_plugins )
                    ) {
                        wp_maybe_auto_update();
                        wp_remote_post( admin_url( 'admin-ajax.php' ) );
                        break;
                    }
                }
            }
        } else {
            Utils\Log::put( Utils\Log::ACTION_DEBUG, 'Invalid request for speed up update addons', null, json_encode( $_POST, JSON_PRETTY_PRINT ) );
        }

        wp_send_json_success();
    }

    /**
     * Update add-on
     */
    public static function updateAddon()
    {
        if ( wp_verify_nonce( $_POST['csrf_token'], 'bookly' ) == 1 ) {
            if ( ! function_exists( 'plugins_api' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
            }

            if ( ! class_exists( 'Plugin_Upgrader', false ) ) {
                require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            }

            $slug = $_POST['slug'];
            $base_name = $slug . '/main.php';

            $current = get_site_transient( 'update_plugins' );
            if ( isset( $current->response[ $base_name ] ) ) {
                $upgrader = new \Plugin_Upgrader( new \Automatic_Upgrader_Skin() );
                $upgrader->init();
                $upgrader->run( array(
                    'package' => $current->response[ $base_name ]->package,
                    'destination' => WP_PLUGIN_DIR,
                    'clear_destination' => true,
                    'clear_working' => true,
                    'hook_extra' => array(
                        'plugin' => $base_name,
                        'type' => 'plugin',
                        'action' => 'update',
                    ),
                ) );

                if ( ! is_wp_error( $upgrader->result ) ) {
                    $update = array();
                    $plugins = apply_filters( 'bookly_plugins', array() );
                    $title = $plugins[ $slug ]::getTitle();
                    wp_send_json_success( compact( 'title', 'update' ) );
                }
            }
        }
        wp_send_json_error();
    }

    /**
     * Get updating data.
     */
    public static function getAddonsUpdatingData()
    {
        $update = array();
        if ( wp_verify_nonce( $_POST['csrf_token'], 'bookly' ) == 1 ) {
            $slug = $_POST['slug'];
            $bookly_plugins = apply_filters( 'bookly_plugins', array() );
            if ( $slug === 'bookly-responsive-appointment-booking-tool' ) {
                if ( array_key_exists( 'bookly-addon-pro', $bookly_plugins ) ) {
                    $update = self::getUpdates( array( 'bookly-addon-pro' => $bookly_plugins['bookly-addon-pro'] ) );
                }
            } elseif ( $slug === 'bookly-addon-pro' ) {
                $plugins = apply_filters( 'bookly_plugins', array() );
                $bookly_plugins = array();
                foreach ( $_POST['slugs'] as $slug ) {
                    if ( array_key_exists( $slug, $plugins ) ) {
                        $bookly_plugins[ $slug ] = $plugins[ $slug ];
                    }
                }
                $update = self::getUpdates( $bookly_plugins );
            }
        }
        $update
            ? wp_send_json_success( compact( 'update' ) )
            : wp_send_json_error();
    }

    /**
     * @param string $plugin_file
     * @param array $plugin_data
     * @param string $status
     */
    public static function renderAfterPluginRow( $plugin_file, $plugin_data, $status )
    {
        /** @var \Bookly\Lib\Base\Plugin[] $bookly_plugins */
        $bookly_plugins = apply_filters( 'bookly_plugins', array() );
        $slug = dirname( $plugin_file );
        unset( $bookly_plugins['bookly-responsive-appointment-booking-tool'] );
        if ( array_key_exists( $slug, $bookly_plugins ) ) {
            $plugin_class = $bookly_plugins[ $slug ];
            $bookly_update_plugins = get_site_transient( 'bookly_update_plugins' );
            $key = 'support_required';
            if ( isset( $bookly_update_plugins[ $slug ][ $key ]['last_version'] ) ) {
                $data = $bookly_update_plugins[ $slug ][ $key ];
                if ( version_compare( $data['last_version'], $plugin_data['Version'], '>' ) ) {
                    echo self::renderSupportInfo( $plugin_class );
                } else {
                    unset( $bookly_update_plugins[ $slug ][ $key ] );
                    set_site_transient( 'bookly_update_plugins', $bookly_update_plugins );
                }
            } elseif ( $plugin_class::getPurchaseCode() == '' && ! $plugin_class::embedded() ) {
                echo self::renderPurchaseCodeInfo();
            }
        }
    }

    /**
     * Reduce time of last check for updates of Bookly plugins to quicker identify new versions
     *
     * @param \WP_Upgrader $upgrader
     * @param array $data
     */
    public static function reduceTimeOfLastCheck( $upgrader, $data )
    {
        if ( isset( $data['action'], $data['type'] ) && $data['action'] === 'update' && $data['type'] === 'plugin' ) {
            $slugs = array();
            if ( isset( $data['plugins'], $data['bulk'] ) && $data['bulk'] ) {
                if ( in_array( 'bookly-addon-pro/main.php', $data['plugins'] ) ) {
                    $bookly_plugins = apply_filters( 'bookly_plugins', array() );
                    unset( $bookly_plugins['bookly-responsive-appointment-booking-tool'], $bookly_plugins['bookly-addon-pro'] );
                    $slugs = array_keys( $bookly_plugins );
                } elseif ( in_array( 'bookly-responsive-appointment-booking-tool/main.php', $data['plugins'] ) ) {
                    $slugs = array( 'bookly-addon-pro' );
                }
            }
            self::resetLastCheckTime( $slugs );
        }
    }

    /**
     * @param array $slugs
     * @return void
     */
    protected static function resetLastCheckTime( array $slugs )
    {
        foreach ( $slugs as $slug ) {
            $plugin_checked = get_option( 'external_updates-' . $slug );
            if ( $plugin_checked instanceof \stdClass && property_exists( $plugin_checked, 'lastCheck' ) ) {
                $current_last_check = $plugin_checked->lastCheck;
                $plugin_checked->lastCheck = time() - WEEK_IN_SECONDS;
                if ( abs( $current_last_check - $plugin_checked->lastCheck ) > 60 ) {
                    update_option( 'external_updates-' . $slug, $plugin_checked );
                }
            }
        }
    }

    /**
     * @param Plugin $plugin_class
     */
    protected static function getPluginUpdateInfo( $plugin_class, $data )
    {
        return sprintf(
            __( 'New version of %1$s is available. <a href="%2$s" %3$s>View version %4$s details</a> or <a href="#" %5$s %6$s>update now</a>.', 'bookly' ),
            $plugin_class::getTitle(),
            esc_url( self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_class::getSlug() . '&section=changelog&TB_iframe=true&width=600&height=800' ) ),
            sprintf(
                'class="thickbox open-plugin-details-modal" aria-label="%s"',
                esc_attr( sprintf( __( 'View %1$s version %2$s details', 'bookly' ), $plugin_class::getTitle(), $data->new_version ) )
            ),
            esc_attr( $data->new_version ),
            'data-update-bookly-plugin="' . $plugin_class::getSlug() . '"',
            sprintf(
                'class="update-link" aria-label="%s"',
                esc_attr( sprintf( __( 'Update %s now' ), $plugin_class::getTitle() ) )
            )
        );
    }

    protected static function renderPurchaseCodeInfo()
    {
        $wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );

        return '<tr class="plugin-update-tr active bookly-js-plugin">
                <td colspan="' . esc_attr( $wp_list_table->get_column_count() ) . '" class="plugin-update colspanchange">
                    <div class="update-message notice inline notice-error notice-alt">
                        <p>
                           ' . esc_html__( 'Important', 'bookly' ) . '!<br>
                           ' . sprintf( esc_html__( 'You haven\'t entered the purchase code which results in impossibility to see if there is a new version available. Enter your purchase code in Settings > %sPurchase Code%s.', 'bookly' ),
                '<a href="' . Utils\Common::escAdminUrl( \Bookly\Backend\Modules\Settings\Page::pageSlug(), array( 'tab' => 'purchase_code' ) ) . '">', '</a>' ) . '
                           </p>
                    </div>
                </td>
          </tr>';
    }

    /**
     * @param Plugin $bookly_plugin
     * @return string|void
     */
    protected static function renderSupportInfo( $bookly_plugin )
    {
        $wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
        $bookly_update_plugins = get_site_transient( 'bookly_update_plugins' );
        $key = 'support_required';
        $data = $bookly_update_plugins[ $bookly_plugin::getSlug() ][ $key ];
        if ( isset( $data['last_version'] ) && version_compare( $data['last_version'], $bookly_plugin::getVersion(), '>' ) ) {
            return '<tr class="plugin-update-tr active bookly-js-plugin">
                <td colspan="' . esc_attr( $wp_list_table->get_column_count() ) . '" class="plugin-update colspanchange">
                    <div class="update-message notice inline notice-error notice-alt">
                        <p>
                           ' . esc_html__( 'Important', 'bookly' ) . '!<br>
                           ' . esc_html__( 'Though, every new version is thoroughly tested to its highest quality before deploying, we can\'t guarantee that after update the plugin will work properly on all WordPress configurations and completely protect it from the influence of other plugins.', 'bookly' ) . '<br>
                           ' . sprintf( __( 'There is a small risk that some issues may appear as a result of updating the plugin. Please note that, according to %1$s Envato rules %2$s, we will be able to help you only if you have active item support period.', 'bookly' ),
                    '<a href="https://themeforest.net/page/item_support_policy" target="_blank">',
                    '</a>'
                ) . '<br>
                    ' . sprintf( __( 'You can renew support %1$s here %3$s or %2$s I\'ve already renewed support. %3$s', 'bookly' ),
                    '<a href="' . esc_url( array_key_exists( 'renew_support', $data ) ? $data['renew_support'] : 'https://codecanyon.net/user/nota-info' ) . '" target="_blank">',
                    '<a href="#" data-bookly-plugin="' . $bookly_plugin::getRootNamespace() . '">',
                    '</a>'
                ) . ' <span class="spinner" style="float: none; margin: -2px 0 0 2px"></span><br>
                        </p>
                    </div>
                </td>
          </tr>';
        } else {
            unset( $bookly_update_plugins[ $bookly_plugin::getSlug() ][ $key ] );
            set_site_transient( 'bookly_update_plugins', $bookly_update_plugins );
        }
    }

    /**
     * @param Base\Plugin[] $bookly_plugins
     * @return void
     */
    protected static function checkUpdates( $bookly_plugins )
    {
        if ( $bookly_plugins ) {
            $cookies = array();
            foreach ( $_COOKIE as $name => $value ) {
                $cookies[] = new \WP_Http_Cookie( compact( 'name', 'value' ) );
            }
            session_write_close();
            $current = get_site_transient( 'update_plugins' );
            foreach ( $bookly_plugins as $bookly_plugin ) {
                try {
                    $slug = $bookly_plugin::getSlug();
                    $base_name = $slug . '/main.php';
                    if ( ! isset( $current->response[ $base_name ] ) && $bookly_plugin::getPurchaseCode() != '' ) {
                        $url = html_entity_decode( wp_nonce_url(
                            add_query_arg(
                                array(
                                    'puc_check_for_updates' => 1,
                                    'puc_slug' => $slug,
                                ),
                                self_admin_url( 'plugins.php' )
                            ),
                            'puc_check_for_updates'
                        ) );
                        wp_remote_get( $url, compact( 'cookies' ) );
                    }
                } catch ( \Exception $e ) {
                }
            }
            wp_cache_flush();
        }
    }

    /**
     * @param Plugin[] $bookly_plugins
     * @return array
     */
    protected static function getUpdates( $bookly_plugins )
    {
        $update = array();
        if ( $bookly_plugins ) {
            self::checkUpdates( $bookly_plugins );
            $current = get_site_transient( 'update_plugins' );
            foreach ( $bookly_plugins as $bookly_plugin ) {
                $base_name = $bookly_plugin::getBasename();
                if ( isset( $current->response[ $base_name ] ) ) {
                    $update[] = array(
                        'icon' => isset( $current->response[ $base_name ]->icons['1x'] ) ? $current->response[ $base_name ]->icons['1x'] : null,
                        'details' => self::getPluginUpdateInfo( $bookly_plugin, $current->response[ $base_name ] ),
                        'support' => self::renderSupportInfo( $bookly_plugin ),
                    );
                }
            }
        }

        return $update;
    }

    protected static function renderModal()
    {
        echo '<style>
              .bookly-plugins-modal{ display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4); }
              .bookly-plugins-modal-content{ background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 50%; border-radius: 6px; }
              </style>
              <div class="bookly-plugins-modal" id="bookly-js-update-plugins-modal"><div class="bookly-plugins-modal-content"><div><b>' . esc_html__( 'Bookly updater', 'bookly' ) . '</b></div><p class="bookly-js-plugins-list"></p></div></div>';
        /** @var \Bookly\Lib\Base\Plugin[] $bookly_plugins */
        $bookly_plugins = apply_filters( 'bookly_plugins', array() );
        unset( $bookly_plugins['bookly-responsive-appointment-booking-tool'], $bookly_plugins['bookly-addon-pro'] );
        $auto_update_plugins = get_option( 'auto_update_plugins' ) ?: array();
        foreach ( $bookly_plugins as $slug => $plugin ) {
            if ( in_array( $plugin::getBasename(), $auto_update_plugins ) ) {
                unset( $bookly_plugins[ $slug ] );
            }
        }
        wp_localize_script( 'bookly-plugins-page', 'BooklyPluginsPageL10n', array(
            'csrfToken' => Utils\Common::getCsrfToken(),
            'deleteData' => get_option( 'bookly_gen_delete_data_on_uninstall', '1' ),
            'deletingInfo' => __( 'Please note that upon deleting this Bookly item, all data associated with it will be permanently deleted', 'bookly' ) . '. ' . __( 'To save data, please set "Don\'t delete" in Bookly Settings > General > Bookly data upon deleting Bookly items', 'bookly' ),
            'updated' => __( '%s updated', 'bookly' ) . '!',
            'addons' => array_keys( $bookly_plugins ),
            'wait' => __( 'Please wait, we are checking updates for {checked}/{total} Bookly add-ons', 'bookly' ),
            'noUpdatesAvailable' => __( 'No updates available', 'bookly' ),
        ) );
    }

    /**
     * @return string
     */
    protected static function getSignature()
    {
        return Utils\Common::xorEncrypt( get_option( 'bookly_installation_time' ) . '-' . get_option( 'bookly_co_name' ), 'bookly-automatically-update-request' );
    }
}