<?php

namespace Bookly\Backend\Modules\Diagnostics\Tests;

use Bookly\Lib;

/**
 * Class PluginUpdates
 *
 * @package Bookly\Backend\Modules\Diagnostics\Tests
 */
class PluginUpdates extends Test
{
    protected $slug = 'bookly-plugin-updates';

    public function __construct()
    {
        $this->title = __( 'Bookly updates', 'bookly' );
        $this->description = __( 'Bookly updates bring new useful features and bugfixes. Make sure you are using the latest version of Bookly to get more possibilities and the highest quality.', 'bookly' );
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $plugins = get_site_transient( 'update_plugins' );
        $errors = false;
        if ( $plugins ) {
            foreach ( $plugins->response as $plugin => $data ) {
                if ( strpos( $plugin, 'bookly-' ) === 0 ) {
                    if ( ! $errors ) {
                        $this->addError( __( 'Some Bookly items are outdated. Please update the following items:', 'bookly' ) );
                        $this->addError( '' );
                        $errors = true;
                    }
                    $plugin_data = get_plugin_data( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin );
                    $this->addError( sprintf( '%s <b>(v%s - v%s)</b>', $plugin_data['Name'], $plugin_data['Version'], $data->new_version ) );
                }
            }
        }

        $addons_required = Lib\API::getRequiredAddonsVersions( Lib\Plugin::getVersion() );
        /** @var Lib\Base\Plugin[] $bookly_plugins */
        $bookly_plugins = apply_filters( 'bookly_plugins', array() );

        $title_shown = false;
        foreach ( $bookly_plugins as $slug => $plugin ) {
            if ( isset( $addons_required[ $slug ] ) && version_compare( $addons_required[ $slug ], $plugin::getVersion(), '>' ) ) {
                if ( ! $title_shown ) {
                    $this->addError( __( 'Please update the following add-ons for correct work of your version of Bookly:', 'bookly' ) );
                    $title_shown = true;
                }
                $this->addError( sprintf( '%s <b>(v%s - v%s)</b>', $plugin::getTitle(), $plugin::getVersion(), $addons_required[ $slug ] ) );
            }
        }

        return empty( $this->errors );
    }
}