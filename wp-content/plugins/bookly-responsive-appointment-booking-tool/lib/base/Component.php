<?php
namespace Bookly\Lib\Base;

use Bookly\Lib;

/**
 * Class Component
 *
 * @package Bookly\Lib\Base
 */
abstract class Component extends Cache
{
    /**
     * Array of reflection objects of child classes.
     *
     * @var \ReflectionClass[]
     */
    private static $reflections = array();

    private static $data = array();

    /******************************************************************************************************************
     * Public methods                                                                                                 *
     ******************************************************************************************************************/

    /**
     * Get admin page slug.
     *
     * @return string
     */
    public static function pageSlug()
    {
        return 'bookly-' . str_replace( '_', '-', basename( static::directory() ) );
    }

    /**
     * Render a template file.
     *
     * @param string $template
     * @param array $variables
     * @param bool $echo
     * @return void|string
     */
    public static function renderTemplate( $template, $variables = array(), $echo = true )
    {
        extract( array( 'self' => get_called_class() ) );
        extract( $variables );

        // Start output buffering.
        ob_start();
        ob_implicit_flush( 0 );

        include static::directory() . '/templates/' . $template . '.php';

        if ( ! $echo ) {
            return ob_get_clean();
        }

        echo ob_get_clean();
    }

    /******************************************************************************************************************
     * Protected methods                                                                                              *
     ******************************************************************************************************************/

    /**
     * Verify CSRF token.
     *
     * @param string $action
     * @return bool
     */
    protected static function csrfTokenValid( $action = null )
    {
        return isset( $_REQUEST['csrf_token'] ) && wp_verify_nonce( $_REQUEST['csrf_token'], 'bookly' ) == 1;
    }

    /**
     * Get path to component directory.
     *
     * @return string
     */
    protected static function directory()
    {
        return dirname( static::reflection()->getFileName() );
    }

    /**
     * Enqueue scripts with wp_enqueue_script.
     *
     * @param array $sources
     */
    protected static function enqueueScripts( array $sources )
    {
        static::registerGlobalAssets();
        static::_enqueue( 'scripts', $sources );
    }

    /**
     * Enqueue styles with wp_enqueue_style.
     *
     * @param array $sources
     */
    protected static function enqueueStyles( array $sources )
    {
        static::registerGlobalAssets();
        static::_enqueue( 'styles', $sources );
    }

    protected static function enqueueData( array $data, $handler = 'bookly-globals' )
    {
        foreach ( $data as $token ) {
            if ( ! in_array( $token, self::$data, true ) ) {
                $item = null;
                switch ( $token ) {
                    case 'casest':
                        $item = Lib\Config::getCaSeSt();
                        break;
                }
                $item = Lib\Proxy\Shared::prepareGlobalSetting( $item, $token );
                if ( $item !== null ) {
                    if ( is_scalar( $item ) && ! is_bool( $item ) ) {
                        $item = html_entity_decode( (string) $item, ENT_QUOTES, 'UTF-8' );
                    }

                    wp_add_inline_script( $handler, 'BooklyL10nGlobal[\'' . $token . '\']=' . wp_json_encode( $item ) . ';', 'before' );
                }
                self::$data[] = $token;
            }
        }
    }

    /**
     * Check if there is a parameter with given name in the request.
     *
     * @param string $name
     * @return bool
     */
    protected static function hasParameter( $name )
    {
        return self::getRequest()->has( $name );
    }

    /**
     * Get class reflection object.
     *
     * @return \ReflectionClass
     */
    protected static function reflection()
    {
        $class = get_called_class();
        if ( ! isset ( self::$reflections[ $class ] ) ) {
            self::$reflections[ $class ] = new \ReflectionClass( $class );
        }

        return self::$reflections[ $class ];
    }

    /**
     * Get request parameter by name.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    protected static function parameter( $name, $default = null )
    {
        return self::getRequest()->get( $name, $default );
    }

    /**
     * Get all request parameters.
     *
     * @return mixed
     */
    protected static function parameters()
    {
        return self::getRequest()->getAll();
    }

    /**
     * @return Lib\Utils\Collection
     */
    protected static function getRequest()
    {
        static $parameters;
        if ( $parameters === null ) {
            $parameters = isset( $_REQUEST['json_data'] ) ?
                array_map( function ( $value ) {
                    return $value !== '' ? $value : null;
                }, json_decode( stripslashes_deep( $_REQUEST['json_data'] ), true ) ?: array() ) :
                stripslashes_deep( $_REQUEST );
            if ( ! current_user_can( 'unfiltered_html' ) ) {
                $parameters = Lib\Utils\Common::arrayMapRecursive( function ( $value ) {
                    return is_string( $value ) ? Lib\Utils\Common::stripScripts( $value ) : $value;
                }, $parameters );
            }
            $parameters = new Lib\Utils\Collection( $parameters );
        }

        return $parameters;
    }

    /**
     * Register bookly-globals so that other assets can use them as dependency
     */
    protected static function registerGlobalAssets()
    {
        global $sitepress;

        if ( ! ( wp_script_is( 'bookly-frontend-globals', 'registered' )
            || wp_script_is( 'bookly-backend-globals', 'registered' ) ) ) {
            Component::_register( 'scripts', array(
                'backend' => array(
                    'bootstrap/js/bootstrap.min.js' => array( 'jquery' ),
                    'js/datatables.min.js' => array( 'jquery' ),
                    'js/moment.min.js' => array(),
                    'js/daterangepicker.js' => array( 'bookly-moment.min.js', 'jquery' ),
                    'js/dropdown.js' => array( 'jquery' ),
                    'js/common.js' => array( 'jquery' ),
                    'js/select2.min.js' => array( 'jquery' ),
                ),
                'frontend' => array(
                    'js/spin.min.js' => array( 'jquery' ),
                    'js/ladda.min.js' => array( 'jquery' ),
                ),
                'alias' => array(
                    'bookly-globals' => array( 'bookly-spin.min.js' ),
                    'bookly-frontend-globals' => array( 'bookly-globals', 'bookly-spin.min.js', 'bookly-ladda.min.js', 'bookly-moment.min.js' ),
                    'bookly-backend-globals' => array( 'bookly-globals', 'bookly-bootstrap.min.js', 'bookly-datatables.min.js', 'bookly-daterangepicker.js', 'bookly-dropdown.js', 'bookly-select2.min.js', 'bookly-common.js', 'bookly-spin.min.js', 'bookly-ladda.min.js', ),
                ),
            ) );

            Component::_register( 'styles', array(
                'backend' => array( 'bootstrap/css/bootstrap.min.css', ),
                'frontend' => array( 'css/ladda.min.css', ),
                'alias' => array(
                    'bookly-frontend-globals' => array( 'bookly-ladda.min.css' ),
                    'bookly-backend-globals' => array( 'bookly-bootstrap.min.css', 'bookly-ladda.min.css' ),
                ),
            ) );
            $ajax_url = admin_url( 'admin-ajax.php' );
            wp_localize_script( 'bookly-globals', 'BooklyL10nGlobal', Lib\Proxy\Shared::prepareL10nGlobal( array(
                'csrf_token' => Lib\Utils\Common::getCsrfToken(),
                'ajax_url_backend' => $ajax_url,
                'ajax_url_frontend' => $sitepress instanceof \SitePress ? add_query_arg( array( 'lang' => $sitepress->get_current_language() ), $ajax_url ) : admin_url( 'admin-ajax.php' ),
                'mjsTimeFormat' => Lib\Utils\DateTime::convertFormat( 'time', Lib\Utils\DateTime::FORMAT_MOMENT_JS ),
                'datePicker' => Lib\Utils\DateTime::datePickerOptions(),
                'dateRange' => Lib\Utils\DateTime::dateRangeOptions(),
                'addons' => array(),
                'cloud_products' => get_option( 'bookly_cloud_account_products', array() ),
                'data' => (object) array(),
            ) ) );
        }
    }

    /******************************************************************************************************************
     * Private methods                                                                                                *
     ******************************************************************************************************************/

    /**
     * Register scripts or styles with wp_register_script/wp_register_style
     *
     * @param string $type
     * @param array $sources
     */
    private static function _register( $type, array $sources )
    {
        $func = $type == 'scripts' ? 'wp_register_script' : 'wp_register_style';
        static::_assets( $func, $sources );
    }

    /**
     * Enqueue scripts or styles with wp_enqueue_script/wp_enqueue_style
     *
     * @param string $type
     * @param array $sources
     */
    private static function _enqueue( $type, array $sources )
    {
        $func = $type == 'scripts' ? 'wp_enqueue_script' : 'wp_enqueue_style';
        static::_assets( $func, $sources );
    }

    /**
     * Process assets with given function
     *
     * @param callable $func
     * @param array $sources
     * array(
     *  resource_directory => array(
     *      file[ => deps],
     *      ...
     *  ),
     *  ...
     * )
     */
    private static function _assets( $func, array $sources )
    {
        $plugin_class = Lib\Base\Plugin::getPluginFor( get_called_class() );
        $assets_version = $plugin_class::getVersion();

        foreach ( $sources as $source => $files ) {
            switch ( $source ) {
                case 'alias':
                case 'wp':
                    $path = false;
                    break;
                case 'backend':
                    $path = $plugin_class::getDirectory() . '/backend/resources/path';
                    break;
                case 'frontend':
                    $path = $plugin_class::getDirectory() . '/frontend/resources/path';
                    break;
                case 'module':
                    $path = static::directory() . '/resources/path';
                    break;
                case 'bookly':
                    $path = Lib\Plugin::getDirectory() . '/path';
                    $assets_version = Lib\Plugin::getVersion();
                    break;
                default:
                    $path = $source . '/path';
            }

            foreach ( $files as $key => $value ) {
                $file = is_array( $value ) ? $key : $value;
                $deps = is_array( $value ) ? $value : array();
                if ( $path === false ) {
                    call_user_func( $func, $file, false, $deps, $assets_version );
                } else {
                    call_user_func( $func, 'bookly-' . basename( $file ), plugins_url( $file, $path ), $deps, $assets_version );
                }
            }
        }
    }
}