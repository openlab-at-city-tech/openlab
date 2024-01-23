<?php
namespace Bookly\Lib\Notifications;

abstract class WPML
{
    /** @var string|null */
    protected static $initial_lang;

    /**
     * Get current WPML lang.
     *
     * @return string|null
     */
    public static function getLang()
    {
        global $sitepress;

        if ( $sitepress instanceof \SitePress ) {
            return $sitepress->get_current_language();
        }

        return null;
    }

    /**
     * Switch WPML lang.
     *
     * @param string $lang
     * @return string|null
     */
    public static function switchLang( $lang )
    {
        global $sitepress;

        if ( $sitepress instanceof \SitePress ) {
            if ( $lang != $sitepress->get_current_language() ) {
                if ( self::$initial_lang === null ) {
                    self::$initial_lang = $sitepress->get_current_language();
                }
                $sitepress->switch_lang( $lang );
                // WPML Multilingual CMS 3.9.2 // 2018-02
                // Does not overload the date translation
                $GLOBALS['wp_locale'] = new \WP_Locale();
            }

            return $lang;
        }

        return null;
    }

    /**
     * Switch WPML to default lang.
     *
     * @return string|null
     */
    public static function switchToDefaultLang()
    {
        global $sitepress;

        if ( $sitepress instanceof \SitePress ) {
            return self::switchLang( $sitepress->get_default_language() );
        }

        return null;
    }

    /**
     * Restore WPML lang.
     */
    public static function restoreLang()
    {
        if ( self::$initial_lang !== null ) {
            self::switchLang( self::$initial_lang );
            self::$initial_lang = null;
        }
    }
}