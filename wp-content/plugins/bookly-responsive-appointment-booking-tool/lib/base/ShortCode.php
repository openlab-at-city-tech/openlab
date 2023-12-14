<?php
namespace Bookly\Lib\Base;

use Bookly\Lib;

abstract class ShortCode extends Component
{
    /** @var string */
    public static $code;
    /** @var array */
    protected static $print_only = array( 'bookly-search-form', 'bookly-staff-form', 'bookly-services-form' );

    /**
     * @return void
     */
    public static function init()
    {
        $code = static::$code;
        /** @var ShortCode $class */
        $class = get_called_class();
        // Register short code.
        add_shortcode( $code, function ( $attributes ) use ( $class, $code ) {
            $key = 'assets-' . $code;
            if ( ! $class::hasInCache( $key . '-styles' ) ) {
                $class::linkStyles();
                $class::putInCache( $key . '-styles', false );
            }
            if ( ! $class::hasInCache( $key . '-scripts' ) ) {
                $class::linkScripts();
                $class::putInCache( $key . '-scripts', false );
            }

            return $class::render( $attributes );
        } );

        // Assets.
        add_action( 'wp_enqueue_scripts', function () use ( $class ) {
            if ( ShortCode::needToIncludeAsset( $class, 'styles' ) ) {
                $class::linkStyles();
            }
        } );

        add_action( 'wp_enqueue_scripts', function () use ( $class ) {
            if ( ShortCode::needToIncludeAsset( $class, 'scripts' ) ) {
                $class::linkScripts();
            }
        } );
    }

    /**
     * @param ShortCode $class
     * @param string $type
     * @return bool
     */
    protected static function needToIncludeAsset( $class, $type )
    {
        $code = $class::$code;
        $key = 'assets-' . $code . '-' . $type;
        if ( ! $class::hasInCache( $key ) ) {
            if ( ( ! in_array( $code, self::$print_only, true ) && get_option( 'bookly_gen_link_assets_method' ) === 'enqueue' ) || Lib\Utils\Common::postsHaveShortCode( $code ) ) {
                $class::putInCache( $key, false );

                return true;
            }
        }

        return $class::getFromCache( $key );
    }
}