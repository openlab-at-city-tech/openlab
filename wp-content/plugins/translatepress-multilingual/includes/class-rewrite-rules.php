<?php

/**
 * Class TRP_Rewrite_Rules
 *
 * Filters the .htaccess file to prevent language slug in URL
 *
 */
class TRP_Rewrite_Rules{

    protected $settings;

    public function __construct( $settings ){
        $this->settings = $settings;
    }

    /**
     * Remove language parameter from .htaccess in certain cases.
     *
     * Hooked to 'mod_rewrite_rules'
     *
     * @param string $htaccess_string
     *
     * @return string
     */
    public function trp_remove_language_param( $htaccess_string ) {

        $url_slugs = $this->settings['url-slugs'];

        foreach ( $url_slugs as $key => $value ) {
            if( $this->settings['add-subdirectory-to-default-language'] == 'no' && $key == $this->settings['default-language'] ){
                continue;
            }
            foreach ( array( '', 'index.php' ) as $base ) {
                $htaccess_string = str_replace(
                    '/' . $value . '/' . $base,
                    '/' . $base,
                    $htaccess_string
                );
            }
        }

        return $htaccess_string;
    }

}
