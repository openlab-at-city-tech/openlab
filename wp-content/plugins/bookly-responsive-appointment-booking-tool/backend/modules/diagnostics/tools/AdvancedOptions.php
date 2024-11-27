<?php
namespace Bookly\Backend\Modules\Diagnostics\Tools;

use Bookly\Lib;

class AdvancedOptions extends Tool
{
    protected $slug = 'advanced-options';
    protected $hidden = true;
    protected $list;

    public $position = 40;

    protected $excluded_options = array(
        'bookly_cloud_account_products',
        'bookly_cloud_promotions',
        'bookly_setup_step',
        'bookly_pro_licensed_products'
    );

    protected $required = array(
        'bookly_cst_phone_default_country',
    );

    protected $white_list = array(
        'cron',
        'active_plugins'
    );

    public function __construct()
    {
        $this->title = 'Advanced options';
    }

    public function render()
    {
        $this->getList();
        $list = $this->getErrorsList();

        return self::renderTemplate( '_advanced_options', compact( 'list' ), false );
    }

    /**
     * @inheritDoc
     */
    public function hasError()
    {
        $this->getList();
        $list = $this->getErrorsList();

        return ! empty( $list );
    }

    /**
     * @return array
     */
    private function getList()
    {
        if ( $this->list === null ) {
            $this->list = array();
            foreach ( apply_filters( 'bookly_plugins', array() ) as $plugin ) {
                /** @var Lib\Base\Plugin $plugin */
                $installer_class = $plugin::getRootNamespace() . '\Lib\Installer';
                /** @var Lib\Base\Installer $installer */
                $installer = new $installer_class();
                foreach ( $installer->getOptions() as $option => $value ) {
                    $list_value = array( 'current' => maybe_serialize( get_option( $option, 'not-exists' ) ), 'default' => maybe_serialize( $value ) );
                    if ( ! $this->verifyOption( $option, $value ) ) {
                        $list_value['incorrect'] = true;
                    }
                    $this->list[ $option ] = $list_value;
                }
            }

        }

        return $this->list;
    }

    private function verifyOption( $option, $default )
    {
        if ( in_array( $option, $this->excluded_options, true ) ) {
            return true;
        }

        $wp_value = get_option( $option, 'not-exists' );
        if ( $wp_value === 'not-exists' ) {
            return false;
        }

        if ( $wp_value === '' && in_array( $option, $this->required, true ) ) {
            return false;
        }

        // Some special options check

        switch ( $option ) {
            case 'bookly_gen_time_slot_length':
                if ( ! ( $wp_value > 0 ) ) {
                    return false;
                }
                break;
            case 'bookly_paypal_enabled':
                if ( in_array( $wp_value, array( '0', 'ec', 'ps', 'checkout' ), true ) ) {
                    return true;
                }
                break;
        }

        if ( $default !== '' ) {
            if ( ( is_array( $default ) && ! is_array( $wp_value ) ) || ( ! is_array( $default ) && is_array( $wp_value ) ) ) {
                return false;
            }

            if ( is_string( $default ) && $this->isJson( $default ) && ! ( is_string( $wp_value ) && $this->isJson( $wp_value ) ) ) {
                return false;
            }

//            if ( is_numeric( $default ) && ! is_numeric( $wp_value ) ) {
//                return false;
//            }
        }

        return true;
    }

    /**
     * Reset option value to default
     *
     * @return void
     */
    public function setDefault()
    {
        $this->getList();

        $option = self::parameter( 'option' );
        if ( isset( $this->list[ $option ] ) ) {
            update_option( $option, maybe_unserialize( $this->list[ $option ]['default'] ) );
        }

        wp_send_json_success();
    }

    /**
     * Get option
     *
     * @return void
     */
    public function getOption()
    {
        $option = trim( self::parameter( 'option' ) );

        if ( ! $this->isOptionValid( $option ) ) {
            wp_send_json_error();
        }

        $this->getList();
        $result = array( 'current' => maybe_serialize( get_option( $option, 'not-exists' ) ), 'default' => null );

        if ( isset( $this->list[ $option ] ) ) {
            $result['default'] = $this->list[ $option ]['default'];
        }


        wp_send_json_success( $result );
    }

    /**
     * Set option
     *
     * @return void
     */
    public function setOption()
    {
        $option = trim( self::parameter( 'option' ) );

        if ( ! $this->isOptionValid( $option ) ) {
            wp_send_json_error();
        }
        $option_value = maybe_unserialize( self::parameter( 'value' ) );

        update_option( $option, $option_value );
        do_action( 'wpml_register_single_string', 'bookly', $option, $option_value );

        wp_send_json_success();
    }

    private function getErrorsList()
    {
        return array_filter( $this->list, static function( $val ) { return isset( $val['incorrect'] ) && $val['incorrect']; } );
    }

    private function isOptionValid( $option )
    {
        return in_array( $option, $this->white_list, true ) || strpos( $option, 'bookly_' ) === 0;
    }

    private function isJson( $string )
    {
        $json = json_decode( $string );

        return is_array( $json ) && json_last_error() === JSON_ERROR_NONE;
    }
}