<?php
namespace Bookly\Backend\Modules\Diagnostics\Tests;

use Bookly\Lib;

class PluginsDirectories extends Test
{
    protected $slug = 'check-plugin-directories';
    public $error_type = 'error';

    public function __construct()
    {
        $this->title = __( 'Add-ons directories', 'bookly' );
        $this->description = __( 'Since Bookly has specific add-ons directories, their renaming may cause issues.', 'bookly' );
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $errors = array();
        /**
         * @var Lib\Base\Plugin $plugin
         */
        foreach ( apply_filters( 'bookly_plugins', array() ) as $plugin ) {
            $slug = strtolower( preg_replace( '([A-Z\d])', '-$0', $plugin::getRootNamespace() ) );
            if ( $slug === '-bookly' ) {
                $slug = 'bookly-responsive-appointment-booking-tool';
            } else {
                $slug = str_replace( '-bookly-', 'bookly-addon-', $slug );
            }

            if ( $slug !== $plugin::getSlug() ) {
                $errors[] = sprintf( '<br/><b>%s</b><br/>%s: <b>%s</b><br/>%s: <b>%s</b>', $plugin::getTitle(), __( 'Current directory name', 'bookly' ), $plugin::getSlug(), __( 'Expected directory name', 'bookly' ), $slug );
            }
        }

        if ( $errors ) {
            $this->addError( __( 'Some folders were renamed. Below you can find a list with current and correct names.', 'bookly' ) );
            foreach ( $errors as $error ) {
                $this->addError( $error );
            }
        }

        return empty( $this->errors );
    }
}