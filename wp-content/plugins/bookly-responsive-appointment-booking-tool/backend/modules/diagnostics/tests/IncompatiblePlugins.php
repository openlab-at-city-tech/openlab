<?php

namespace Bookly\Backend\Modules\Diagnostics\Tests;

/**
 * Class IncompatiblePlugins
 *
 * @package Bookly\Backend\Modules\Diagnostics\Tests
 */
class IncompatiblePlugins extends Test
{
    protected $slug = 'check-incompatible-plugins';
    protected $plugins = array(
        'autoptimize',
        'wp-optimize',
        'wp-fastest-cache',
        'litespeed-cache',
    );

    public function __construct()
    {
        $this->title = __( 'Incompatible plugins', 'bookly' );
        $this->description = __( 'This test checks the plugins that may affect Bookly. For example, some caching and optimizing plugins may cause incorrect work of Bookly.', 'bookly' );
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $errors = array();
        foreach ( wp_get_active_and_valid_plugins() as $plugin ) {
            foreach ( $this->plugins as $incompatible ) {
                if ( strpos( $plugin, DIRECTORY_SEPARATOR . $incompatible . DIRECTORY_SEPARATOR ) !== false ) {
                    $data = get_plugin_data( $plugin );
                    $errors[] = sprintf( '<b>%s</b>', $data['Name'] );
                }
            }
        }

        if ( $errors ) {
            $this->addError( __( 'One or several incompatible plugins were detected. Please add pages with Bookly shortcodes to cache exceptions and Javascript-code minification.', 'bookly' ) . '<br>' );
            foreach ( $errors as $error ) {
                $this->addError( $error );
            }
        }

        return empty( $this->errors );
    }
}