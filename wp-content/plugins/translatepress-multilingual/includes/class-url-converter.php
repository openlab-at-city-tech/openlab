<?php

/**
 * Class TRP_Url_Converter
 *
 * Manages urls of translated pages.
 */
class TRP_Url_Converter {

    protected $absolute_home;
    protected $settings;
    protected $admin_url;

    /**
     * TRP_Url_Converter constructor.
     *
     * @param array $settings       Settings option.
     */
    public function __construct( $settings ){
        $this->settings = $settings;
        //$admin_url is declared here because it was causing a conflict with Ultimate Dashboard since there was an action hooked on site_url
        $this->admin_url = strtolower( admin_url() );
    }

    /**
     * Add language code as a subdirectory after home url.
     *
     * Hooked to home_url.
     *
     * @param string $url               Given Url.
     * @param string $path              Given path.
     * @param string $orig_scheme       Scheme.
     * @param int $blog_id              Blog id.
     * @return string
     */
    public function add_language_to_home_url( $url, $path, $orig_scheme, $blog_id ){
        global $TRP_LANGUAGE;

        //if this is not set then don't do anything as this is an exception/error and $TRP_LANGUAGE should always be set
        if( empty( $TRP_LANGUAGE ) )
            return $url;

        if ( isset( $this->settings['add-subdirectory-to-default-language'] ) && $this->settings['add-subdirectory-to-default-language'] == 'no' && $TRP_LANGUAGE == $this->settings['default-language'] ) {
            return $url;
        }

        if( apply_filters( 'trp_add_language_to_home_url_check_for_admin', true, $url, $path ) &&
            ( is_customize_preview() || $this->is_admin_request()  || $this->is_sitemap_path( $path ) || $this->url_is_file( $path ) ) )
            return $url;

        $url_slug = $this->get_url_slug( $TRP_LANGUAGE );

        //if this is not set then don't do anything as this is an exception/error if we don't have an $url_slug we don't need to do anything
        if( empty( $url_slug ) )
            return $url;

        $abs_home = $this->get_abs_home();

        if ( trp_force_slash_at_end_of_link( $this->settings ) ) {
            $new_url = trailingslashit( trailingslashit( $abs_home ) . $url_slug );
        } else {
            $new_url = trailingslashit( $abs_home ) . $url_slug;
        }


        if ( ! empty( $path ) ){
            $new_url = trailingslashit($new_url) . ltrim( $path, '/');
        }

        return apply_filters( 'trp_home_url', $new_url, $abs_home, $TRP_LANGUAGE, $path, $url );
    }

    /**
     * Check if this is a request at the backend.
     *
     * @return bool true if is admin request, otherwise false.
     */
    public function is_admin_request() {
        $current_url = $this->cur_page_url();

        // we can't use wp_get_referer() It looks like it creates an infinite loop because it calls home_url() and we're filtering that
        // array('http','https') is added because of a compatibility issue with Scriptless Social Sharing that created an infinite loop
        //because this function is hooked to 'locale' and reaches at a certain point a function hooked to 'kses_allowed_protocols'
        //Scriptless Social Sharing had a function hooked to the same filter and it created an infinit loop
        $referrer = '';
        if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
            $referrer = wp_unslash( esc_url_raw( $_REQUEST['_wp_http_referer'], array( 'http', 'https' ) ) );
        } else if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
            $referrer = wp_unslash( esc_url_raw( $_SERVER['HTTP_REFERER'], array( 'http', 'https' ) ) );
        }

        //consider an admin request a call to the rest api that came from the admin area
        if( false !== strpos( $current_url, '/wp-json/' ) && 0 === strpos( $referrer, $this->admin_url ) ){
            return true;
        }

        /**
         * Check if this is a admin request. If true, it
         * could also be a AJAX request from the frontend.
         */
        if ( 0 === strpos( $current_url, $this->admin_url ) ) {
            /**
             * Check if the user comes from a admin page.
             */
            if ( 0 === strpos( $referrer, $this->admin_url ) ) {
                return true;
            } else {
                if ( function_exists( 'wp_doing_ajax' ) ) {
                    return ! wp_doing_ajax();
                } else {
                    return ! ( defined( 'DOING_AJAX' ) && DOING_AJAX );
                }
            }
        } else {
            return false;
        }
    }

    /**
     * A function that is used inside the home_url filter to detect if the current link is a sitemap link
     * @param $path the path that is passed inside home_url
     * @return bool
     */
    public function is_sitemap_path( $path = '' ) {
        global $wp_current_filter;

        if( empty( $path ) || $path === '/' ){
            $path = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( $_SERVER['REQUEST_URI'] ) : '';
        }

        // Verify that this is a sitemap url and that it contains the .xml extension
        if( strpos($path, 'sitemap') !== false &&
            strpos($path, '.xml')    !== false &&
            // Bypass this check if we're on certain filters in order to be able to generate other language urls
            !in_array( 'wpseo_sitemap_url',     $wp_current_filter ) &&
            !in_array( 'seopress_sitemaps_url', $wp_current_filter ) &&
            !in_array( 'rank_math/sitemap/url', $wp_current_filter ) &&
            !in_array( 'aiosp_sitemap_data',    $wp_current_filter ) &&
            !in_array( 'aioseo_sitemap_terms',  $wp_current_filter ) &&
            !in_array( 'aioseo_sitemap_posts',  $wp_current_filter )
        ){
            return true;
        }

        // check if it's a stylesheet for xml. SEO Press uses it.
        if (strpos( $path, 'sitemap') !== false && strpos( $path, '.xsl') !== false ){
            return true;
        }

        return false;
    }

    /**
     * Add Hreflang entries for each language to Header.
     */
    public function add_hreflang_to_head() {

        // exclude hreflang for URL
        $default_language = $this->settings["default-language"];
        $original_url     = str_replace( '#TRPLINKPROCESSED', '', $this->get_url_for_language( $default_language ) );
        if ( apply_filters( 'trp-exclude-hreflang', false, $original_url ) ) {
            return;
        }

        $languages = $this->settings['publish-languages'];
        if ( isset( $_GET['trp-edit-translation'] ) && $_GET['trp-edit-translation'] == 'preview' ) {
            $languages = $this->settings['translation-languages'];
        }

        $region_independent_languages           = array();
        $hreflang_duplicates                    = array();
        $hreflang_duplicates_region_independent = array();
        foreach ( $languages as $language ) {
            if ( apply_filters( 'trp_add_country_hreflang_tags', true ) ) {
                $hreflang              = $this->strip_formality_from_language_code( $language ); // returns the language without formality
                // hreflang should have - instead of _ . For example: en-EN, not en_EN like the locale
                $hreflang              = str_replace( '_', '-', $hreflang );
                $hreflang              = apply_filters( 'trp_hreflang', $hreflang, $language );
                $hreflang_duplicates[] = $hreflang;
                echo '<link rel="alternate" hreflang="' . esc_attr( $hreflang ) . '" href="' . esc_url( $this->get_url_for_language( $language ) ) . '"/>' . "\n";
            }

            if ( apply_filters( 'trp_add_region_independent_hreflang_tags', true ) ) {
                $language_independent_hreflang = strtok( $language, '_' );
                $language_independent_hreflang = apply_filters( 'trp_hreflang', $language_independent_hreflang, $language );
                if ( !empty( $language_independent_hreflang ) && !in_array( $language_independent_hreflang, $region_independent_languages ) ) {
                    $region_independent_languages[]                      = $language_independent_hreflang;
                    $hreflang_duplicates_region_independent[ $language ] = '<link rel="alternate" hreflang="' . esc_attr( $language_independent_hreflang ) . '" href="' . esc_url( $this->get_url_for_language( $language ) ) . '"/>' . "\n";

                }
            }
        }

        foreach ( $languages as $language ) {
            $language_hreflang = strtok( $language, '_' );
            $language_hreflang = apply_filters( 'trp_hreflang', $language_hreflang, $language );
            if ( !in_array( $language_hreflang, $hreflang_duplicates ) ) {
                if ( isset( $hreflang_duplicates_region_independent[ $language ] ) ) {
                    echo $hreflang_duplicates_region_independent[ $language ]; /* phpcs:ignore */ /* escaped inside the array */
                }
            }
        }

        if ( !empty( $this->settings['trp_advanced_settings']['enable_hreflang_xdefault'] ) && $this->settings['trp_advanced_settings']['enable_hreflang_xdefault'] != 'disabled' && in_array( $this->settings['trp_advanced_settings']['enable_hreflang_xdefault'], $this->settings['translation-languages'] ) ) {
            $default_lang = $this->settings['trp_advanced_settings']['enable_hreflang_xdefault'];
            echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $this->get_url_for_language( $default_lang ) ) . '"/>' . "\n";
        }
    }

    /**
     * Strips formality from the language code - e.g. de_DE_formal => de_DE
     *
     * Otherwise, it would lead to unidentified hreflang values
     *
     * @param string $language language code
     * @return string
     */
    public function strip_formality_from_language_code( $language ){
        return str_replace( ['_formal', '_informal'], '', $language );
    }

    /**
     * Function that replace iso 639-2 and iso 639-3 with iso 639-1 because this is the official one used for hreflang.
     */
    public function replace_iso_2_with_iso_3_for_hreflang($hreflang, $language = null){

        $hreflang_iso_1 = apply_filters('trp_add_hreflang_correct_iso_code', array(
            'bel' => 'be'
        ));

        foreach ($hreflang_iso_1 as $iso_2 => $iso_1) {
            if ( $hreflang === $iso_2 ) {
                return $iso_1;
            }
        }

        return $hreflang;
    }

    /**
     * Function that changes the lang attribute in the html tag to the current language.
     *
     * @param string $output
     * @return string
     */
    public function change_lang_attr_in_html_tag( $output ){
        global $TRP_LANGUAGE;
        $lang = get_bloginfo('language');
        if ( $lang && !empty($TRP_LANGUAGE) ) {
            if ( apply_filters( 'trp_add_default_lang_tags', true ) ) {
                $output = str_replace( 'lang="' . $lang . '"', 'lang="' . str_replace( '_', '-', $TRP_LANGUAGE ) . '"', $output );
            }
            if ( apply_filters( 'trp_add_regional_lang_tags', true ) ) {
                $language = strtok($TRP_LANGUAGE, '_');
                $output = str_replace( 'lang="' . $lang . '"', 'lang="' . $language . '"', $output );

            }
        }

        return $output;
    }

    /**
     * @param $output
     * @return $output
     *
     * adds a new attribute in footer, tp_language_lang, for Automatic User Language Detection to rely on for finding the current language
     */
    public function add_tp_language_lang_attribute(){
        global $TRP_LANGUAGE;
        $html ='<template id="tp-language" data-tp-language="'. esc_attr($TRP_LANGUAGE) . '"></template>';
        echo $html; /* phpcs:ignore *///ignored because the html is constructed by us
    }

    /**
     * Returns language-specific url for given language.
     *
     * Defaults to current Url and current language.
     *
     * @param string $language Language code that we want to translate into.
     * @param string $url Url to encode.
     * @param string $trp_link_is_processed
     * @return string
     */

    public function get_url_for_language ( $language = null, $url = null, $trp_link_is_processed = '#TRPLINKPROCESSED') {
	    $debug = false;
	    // initializations
	    global $TRP_LANGUAGE;
        global  $trp_current_url_term_slug, $trp_current_url_taxonomy;//these are globals that we set on the 'request' filter in our SEO addon

	    if ( empty($url) ){
		    $url = $this->cur_page_url();
	    }

        $url = urldecode($url);

	    if(apply_filters('trp_skip_url_for_language', false, $url)){
		    return (string)$url;
	    }

        $hash = hash( 'md4', (string)$language . (string)$url . (string)$trp_link_is_processed . (string)$TRP_LANGUAGE );
        // set $new_url to false when debugging and developing new features
        $new_url = trp_cache_get('get_url_for_language_' . $hash, 'trp');
        if ( $new_url !== false ){
            return $new_url;
        }

        $trp_language_copy = $TRP_LANGUAGE;
        if ( empty( $language ) ) {
            $language = $TRP_LANGUAGE;
        }

        $url_obj = trp_cache_get('url_obj_' . hash('md4', $url), 'trp');
        if( $url_obj === false ){
            $url_obj = new \TranslatePress\Uri($url);
            wp_cache_set('url_obj_' . hash('md4', $url), $url_obj, 'trp' );
        }

        $abs_home_url_obj = trp_cache_get('url_obj_' . hash('md4',  $this->get_abs_home() ), 'trp');
        if( $abs_home_url_obj === false ){
            $abs_home_url_obj = new \TranslatePress\Uri( $this->get_abs_home() );
            wp_cache_set('url_obj_' . hash('md4', $this->get_abs_home()), $abs_home_url_obj, 'trp' );
        }

        if( $TRP_LANGUAGE == $this->settings['default-language'] ){
            $trp_link_is_processed = '';
        }

        // actual logic of the function

        if ( $this->is_sitemap_path('') ){
            trp_bulk_debug($debug, array('url' => $url, 'abort' => 'is file'));
            wp_cache_set('get_url_for_language_' . $hash, $url . $trp_link_is_processed, 'trp');
            return $url . $trp_link_is_processed; //abort for files
        }

        if ( $this->url_is_file($url) ){
            trp_bulk_debug($debug, array('url' => $url, 'abort' => 'is file'));
            wp_cache_set('get_url_for_language_' . $hash, $url . $trp_link_is_processed, 'trp');
            return $url . $trp_link_is_processed; //abort for files
        }

        if ( !$url_obj->isSchemeless() && $url_obj->getScheme() != 'http' && $url_obj->getScheme() != 'https' ){
            trp_bulk_debug($debug, array('url' => $url, 'abort' => "is different scheme ".$url_obj->getScheme()));
            wp_cache_set('get_url_for_language_' . $hash, $url . $trp_link_is_processed, 'trp');
            return $url . $trp_link_is_processed; // abort for non-http/https links
        }

        if ( $url_obj->isSchemeless() && !$url_obj->getPath() ){
            trp_bulk_debug($debug, array('url' => $url, 'abort' => "is anchor or has get params"));
            wp_cache_set('get_url_for_language_' . $hash, $url, 'trp');
            return $url; // abort for anchors or params only.
        }

        if ( $url_obj->getHost() && $abs_home_url_obj->getHost() && $url_obj->getHost() != $abs_home_url_obj->getHost() ){
            trp_bulk_debug($debug, array('url' => $url, 'abort' => "is external url "));
            wp_cache_set('get_url_for_language_' . $hash, $url, 'trp');
            return $url; // abort for external url's
        }

        if( $this->get_lang_from_url_string($url) === null && $this->settings['default-language'] === $language && $this->settings['add-subdirectory-to-default-language'] !== 'yes' ){
            trp_bulk_debug($debug, array('url' => $url, 'abort' => "URL already has the correct language added to it and default language has subdir"));
            wp_cache_set('get_url_for_language_' . $hash, $url, 'trp');
            return $url;
        }

        if( $this->get_lang_from_url_string($url) === $language ){
            trp_bulk_debug($debug, array('url' => $url, 'abort' => "URL already has the correct language added to it"));
            wp_cache_set('get_url_for_language_' . $hash, $url, 'trp');
            return $url;
        }

        // maybe find the post_id for the current URL
        $possible_post_id = trp_cache_get( 'possible_post_id_'. hash('md4', $url ), 'trp' );
        if ( $possible_post_id ){
            $post_id = $possible_post_id;
            trp_bulk_debug($debug, array('url' => $url, 'found post id' => $post_id, 'for language' => $TRP_LANGUAGE));
        } else {
            $post_id = url_to_postid( $url );
            wp_cache_set( 'possible_post_id_' . hash('md4', $url ), $post_id, 'trp' );
            if ( $post_id ) { trp_bulk_debug($debug, array('url' => $url, 'found post id' => $post_id, 'for default language' => $TRP_LANGUAGE)); }

            if ( $post_id == 0 ) {
                /* try again but this time switch to default language home_url
	            *  becasue url_to_postid() uses the global language setting to accurately retrieve a post ID
                */
                $TRP_LANGUAGE = $this->settings['default-language'];
                add_filter('trp_keep_permalinks_unchanged', '__return_true' );

                /* In order to accurately find the posst ID the passed URL to url_to_postid() needs to be accurate
	            * if the option add subdir to default language is on we need to add that to the URL
                */

                $possible_url = $url;
				if (isset ($this->settings['add-subdirectory-to-default-language']) && $this->settings['add-subdirectory-to-default-language'] === 'yes' && $this->get_lang_from_url_string( $url ) == null ){
					$possible_url = $this->add_language_to_home_url($url, $url_obj->getPath(), $url_obj->getScheme(), get_current_blog_id() );
				}
                $post_id = url_to_postid( $possible_url );
                wp_cache_set( 'possible_post_id_' . hash('md4', $possible_url ), $post_id, 'trp' );
                if($post_id){ trp_bulk_debug($debug, array('url' => $url, 'found post id' => $post_id, 'for default language' => $TRP_LANGUAGE)); }

                remove_filter('trp_keep_permalinks_unchanged', '__return_true' );
                $TRP_LANGUAGE = $trp_language_copy;
            }
        }

        $TRP_LANGUAGE = $this->get_lang_from_url_string( $url );

        if ($TRP_LANGUAGE == null){
            $TRP_LANGUAGE = $this->settings['default-language'];
        }

        $new_url_has_been_determined = false;

        if( $post_id ){

            /*
             * We need to find if the current URL (either passed as parameter or found via cur_page_url)
             * has extra arguments compared to it's permalink.
             * We need the permalink based on the language IN THE URL, not the one passed to this function,
             * as that represents the language to be translated into.
             *
             * WE ARE NOT USING \TranslatePress\Uri
             * due to URL's having extra path elements after the permalink slug. Using the class would strip those end points.
             *
             */

            $processed_permalink = get_permalink($post_id);

            $url_to_replace = ($url_obj->isSchemeless()) ? trailingslashit(trailingslashit( home_url() ) . ltrim($url, '/') ) : $url;
            $arguments = str_replace(untrailingslashit($processed_permalink), '', $url_to_replace );

            // if nothing was replaced, something was wrong, just use the normal permalink without any arguments.
            if( $arguments == $url_to_replace ) {
                $arguments = '';
                //try again, this time trying to correct url_to_replace to include subdirectory
                if (isset ($this->settings['add-subdirectory-to-default-language']) && $this->settings['add-subdirectory-to-default-language'] === 'yes' && $this->get_lang_from_url_string( $url_to_replace ) == null ) {
                    $possible_url_to_replace = $this->add_language_to_home_url( $url, ( empty( $url_obj->getQuery() ) ) ? (( empty( $url_obj->getFragment() ) ) ? $url_obj->getPath() : $url_obj->getPath() . '#' . $url_obj->getFragment()) : (( empty( $url_obj->getFragment() ) ) ? rtrim( $url_obj->getPath(), '/' ) . '/?' . $url_obj->getQuery() : rtrim( $url_obj->getPath(), '/' ) . '/?' . $url_obj->getQuery() . '#' . $url_obj->getFragment() ), $url_obj->getScheme(), get_current_blog_id() );
                    $arguments = str_replace( untrailingslashit( $processed_permalink ), '', $possible_url_to_replace );
                    if ( $arguments == $possible_url_to_replace ) {
                        $arguments = '';
                    }
                }
            }

            $TRP_LANGUAGE = $language;

            $new_url = trailingslashit( get_permalink($post_id) ) . ltrim($arguments, '/');
            trp_bulk_debug($debug, array('url' => $url, 'new url' => $new_url, 'found post id' => $post_id, 'url type' => 'based on permalink', 'for language' => $TRP_LANGUAGE));
            $TRP_LANGUAGE = $trp_language_copy;

            $new_url_has_been_determined = true;

        }

        if( isset( $trp_current_url_term_slug ) && isset($trp_current_url_taxonomy) && $new_url_has_been_determined === false){
            // check here if it is a term link
            $current_term_link = get_term_link( $trp_current_url_term_slug, $trp_current_url_taxonomy);
            if (!is_wp_error($current_term_link)){
                $language_to_replace = $TRP_LANGUAGE;
                $TRP_LANGUAGE = $language;
                $current_term_link= apply_filters( 'trp_get_url_for_language', $current_term_link, $url, $language_to_replace, $this->get_abs_home(), $this->get_lang_from_url_string($url), $this->get_url_slug( $language ) );
                $check_term_link = get_term_link($trp_current_url_term_slug, $trp_current_url_taxonomy);
                if (!is_wp_error($check_term_link) && strpos(urldecode( $url ), $current_term_link) === 0) {
                    $new_url = str_replace( $current_term_link, $check_term_link, urldecode( $url ) );
                    $new_url = apply_filters( 'trp_get_url_for_language', $new_url, $url, $language, $this->get_abs_home(), $this->get_lang_from_url_string($url), $this->get_url_slug( $language ) );
                    $new_url_has_been_determined = true;
                }
                $TRP_LANGUAGE = $trp_language_copy;
            }
        }

        /**
         * We try to look for a possible posts archive link that can be on the front page or another page in order to add pagination.
         */
        $url_stripped = $url;
        $posts_archive_link = get_post_type_archive_link('post');

        if( !empty($url_obj->getQuery()) ){
            $url_stripped = strtok($url_stripped, '?');
        }
        $url_stripped = rtrim($url_stripped, '/');

        $posts_archive_link = strtok($posts_archive_link, '?');
        $posts_archive_link = rtrim($this->maybe_add_pagination_to_blog_page($posts_archive_link), '/');

        if( is_home() && $url_stripped === $posts_archive_link && ( isset( $_SERVER['REQUEST_URI'] ) && strpos( esc_url_raw( $_SERVER['REQUEST_URI'] ), 'sitemap') === false && strpos( esc_url_raw( $_SERVER['REQUEST_URI'] ), '.xml') === false ) &&
        $new_url_has_been_determined === false)
        {//for some reason in yoast sitemap is_home() is true ..so we need to check if we are not in the sitemap itself
            $TRP_LANGUAGE = $language;
            if ( empty($url_obj->getQuery()) ) {
	            $new_url = $this->maybe_add_pagination_to_blog_page( trailingslashit(get_post_type_archive_link( 'post' ) ));
            } else {
	            $new_url = rtrim( $this->maybe_add_pagination_to_blog_page( get_post_type_archive_link( 'post' ) ), '/') . '/?' . $url_obj->getQuery();
            }
            $TRP_LANGUAGE = $trp_language_copy;

            $new_url_has_been_determined = true;
        }

        if ($new_url_has_been_determined === false){
            // we're just adding the new language to the url
            $new_url_obj = $url_obj;
            if ($abs_home_url_obj->getPath() == "/") {
                $abs_home_url_obj->setPath('');
            }
            if ($this->get_lang_from_url_string($url) === null) {
                // these are the custom url. They don't have language
                $abs_home_considered_path = trim(str_replace( $abs_home_url_obj->getPath() !== null ? $abs_home_url_obj->getPath() : '', '', $url_obj->getPath()), '/');
                $new_url_obj->setPath(trailingslashit(trailingslashit($abs_home_url_obj->getPath()) . trailingslashit($this->get_url_slug($language)) . $abs_home_considered_path));
                $new_url = $new_url_obj->getUri();

                trp_bulk_debug($debug, array('url' => $url, 'new url' => $new_url, 'lang' => $language, 'url type' => 'custom url without language parameter'));
            } else {
                // these have language param in them and we need to replace them with the new language
                $abs_home_considered_path = trim(str_replace($abs_home_url_obj->getPath() !== null ? $abs_home_url_obj->getPath() : '', '', $url_obj->getPath()), '/');
                $no_lang_orig_path = explode('/', $abs_home_considered_path);
                unset($no_lang_orig_path[0]);
                $no_lang_orig_path = implode('/', $no_lang_orig_path);

                if (!$this->get_url_slug($language)) {
                    $url_lang_slug = '';
                } else {
                    $url_lang_slug = trailingslashit($this->get_url_slug($language));
                }

                $new_url_obj->setPath(trailingslashit(trailingslashit($abs_home_url_obj->getPath()) . $url_lang_slug . ltrim($no_lang_orig_path, '/')));
                $new_url = $new_url_obj->getUri();

                trp_bulk_debug($debug, array('url' => $url, 'new url' => $new_url, 'lang' => $language, 'url type' => 'custom url with language', 'abs home path' => $abs_home_url_obj->getPath()));

                $new_url_has_been_determined = true;

            }
        }
        $TRP_LANGUAGE = $trp_language_copy;

        /* fix links for woocommerce on language switcher for product categories and product tags */
        if( class_exists( 'WooCommerce' ) ){
            $english_woocommerce_slugs = array('product-category', 'product-tag', 'product');
            foreach ($english_woocommerce_slugs as $english_woocommerce_slug){
                // current woo slugs are based on the localized default language OR the current language
                $current_slug = trp_get_transient( 'tp_'.$english_woocommerce_slug.'_'. $this->settings['default-language'] );
                if( $current_slug === false ){
                    $current_slug = trp_x( $english_woocommerce_slug, 'slug', 'woocommerce', $this->settings['default-language'] );
                    set_transient( 'tp_'.$english_woocommerce_slug.'_'. $this->settings['default-language'], $current_slug, 12 * HOUR_IN_SECONDS );
                }

                //only replace url here if we are in a default Woocommerce case, meaning the slug in Permalinks page is not changed manually by the user
                if( $this->trp_get_woocommerce_saved_permalink($english_woocommerce_slug) === $current_slug ) {

                    if (strpos($new_url, '/' . $current_slug . '/') === false) {
                        $current_slug = trp_get_transient('tp_' . $english_woocommerce_slug . '_' . $TRP_LANGUAGE);
                        if ($current_slug === false) {
                            $current_slug = trp_x($english_woocommerce_slug, 'slug', 'woocommerce', $TRP_LANGUAGE);
                            set_transient('tp_' . $english_woocommerce_slug . '_' . $TRP_LANGUAGE, $current_slug, 12 * HOUR_IN_SECONDS);
                        }
                    }

                    $translated_slug = trp_get_transient('tp_' . $english_woocommerce_slug . '_' . $language);
                    if ($translated_slug === false) {
                        $translated_slug = trp_x($english_woocommerce_slug, 'slug', 'woocommerce', $language);
                        set_transient('tp_' . $english_woocommerce_slug . '_' . $language, $translated_slug, 12 * HOUR_IN_SECONDS);
                    }
                    $new_url = str_replace('/' . $current_slug . '/', '/' . $translated_slug . '/', $new_url);
                }
            }
        }

        if ( empty( $new_url ) ) {
            $new_url = $url;
        }

	    $new_url = apply_filters( 'trp_get_url_for_language', $new_url, $url, $language, $this->get_abs_home(), $this->get_lang_from_url_string($url), $this->get_url_slug( $language ) );
        wp_cache_set('get_url_for_language_' . $hash, $new_url . $trp_link_is_processed, 'trp');
        return $new_url . $trp_link_is_processed ;

    }

    /**
     * Check is a url is an actual file on the server, in which case don't add a language param.
     *
     * @param string $url
     * @return bool
     */
    public function url_is_file( $url = null ){
        $trp = TRP_Translate_Press::get_trp_instance();
        $translation_render = $trp->get_component("translation_render");

        if ( empty( $url ) || $translation_render->is_external_link($url) ){
            $return = false;
        }else {
            if ( strpos( $url, 'wp-content/uploads' ) !== false ) {
                $return = true;
            }else {
                $path = trailingslashit( ABSPATH ) . str_replace( untrailingslashit( $this->get_abs_home() ), '', $url );

                if(apply_filters('trp_is_file', true, $path)) {
                    $return = is_file( $path );
                }else{
                    $return = true;
                }
            }
        }

        return apply_filters( 'trp_url_is_file', $return, $url, $this->get_abs_home() );
    }

    public function does_url_contains_array($return, $path){
        $elements_to_avoid = apply_filters( 'trp_elements_to_avoid_when_is_file_is_called', array("index.php", "/../"));
        foreach ($elements_to_avoid as $element){
            if( strpos($path, $element) !== false ){
                $return = false;
                return $return;
            }
        }

        return $return;
    }

	/**
	 * Check for a spacial type of URL. Currently includes mailto, tel, callto URL types.
	 *
	 * @param string $url
	 * @return bool
	 */
    public function url_is_extra( $url ){
	    $allowed = array( 'mailto', 'tel', 'callto' );
	    $parsed = parse_url($url);
	    if (is_array($parsed) && isset( $parsed['scheme'] )){
	        return in_array( $parsed['scheme'], $allowed );
	    } else {
	    	return false;
	    }
    }


    /**
     * Get language code slug to use in url.
     *
     * @param string $language_code         Full language code.
     * @param bool $accept_empty_return     Whether to take into account the add-subdirectory-to-default-language setting.
     * @return string                       Url slug.
     */
    public function get_url_slug( $language_code, $accept_empty_return = true ){
        $url_slug = $language_code;
        if( isset( $this->settings['url-slugs'][$language_code] ) ) {
            $url_slug = $this->settings['url-slugs'][$language_code];
        }

        if ( $accept_empty_return && isset( $this->settings['add-subdirectory-to-default-language'] ) && $this->settings['add-subdirectory-to-default-language'] == 'no' && $language_code == $this->settings['default-language'] ) {
            $url_slug = '';
        }

        return $url_slug;
    }

    /**
     * Return absolute home url as stored in database, unfiltered.
     *
     * @return string
     */
    public function get_abs_home() {
	    $this->absolute_home = trp_cache_get('get_abs_home', 'trp');
	    if ( $this->absolute_home !== false ){
		    return $this->absolute_home;
	    }

	    global $wpdb;

        // returns the unfiltered home_url by directly retrieving it from wp_options.
        $this->absolute_home = $this->absolute_home
            ? $this->absolute_home
            : ( ! is_multisite() && defined( 'WP_HOME' )
                ? WP_HOME
                : ( is_multisite() && ! is_main_site()
                    ? ( preg_match( '/^(https)/', get_option( 'home' ) ) === 1 ? 'https://'
                        : 'http://' ) . $wpdb->get_var( "	SELECT CONCAT(b.domain, b.path)
									FROM {$wpdb->blogs} b
									WHERE blog_id = {$wpdb->blogid}
									LIMIT 1" )

                    : $wpdb->get_var( "	SELECT option_value
									FROM {$wpdb->options}
									WHERE option_name = 'home'
									LIMIT 1" ) )
            );

        if( empty($this->absolute_home) ){
            $this->absolute_home = get_option("siteurl");
        }
        if ( apply_filters('trp_adjust_absolute_home_https_based_on_server_variable', true) ) {
            // always return absolute_home based on the http or https version of the current page request. This means no more redirects.
            if ( !empty( $_SERVER['HTTPS'] ) && strtolower( sanitize_text_field( $_SERVER['HTTPS'] ) ) != 'off' ) {
                $this->absolute_home = str_replace( 'http://', 'https://', $this->absolute_home );
            } else {
                $this->absolute_home = str_replace( 'https://', 'http://', $this->absolute_home );
            }
        }

        $this->absolute_home = apply_filters('trp_filter_absolute_home_result', $this->absolute_home);

        wp_cache_set( 'get_abs_home', $this->absolute_home, 'trp' );

        return $this->absolute_home;
    }

    /**
     * Return the language code from the url.
     *
     * Uses current url if none given.
     *
     * @param string $url       Url.
     * @return string           Language code.
     */
    public function get_lang_from_url_string( $url = null ) {
        if ( ! $url ){
            $url = $this->cur_page_url();
        }

        $language = trp_cache_get('url_language_' . hash('md4', $url) , 'trp' );
        if ( $language !== false ){
            return $language;
        }

        $url_obj = trp_cache_get('url_obj_' . hash('md4', $url), 'trp');
        if( $url_obj === false ){
            $url_obj = new \TranslatePress\Uri($url);
            wp_cache_set('url_obj_' . hash('md4', $url), $url_obj, 'trp' );
        }

        $abs_home_url_obj = trp_cache_get('url_obj_' . hash('md4',  $this->get_abs_home() ), 'trp');
        if( $abs_home_url_obj === false ){
            $abs_home_url_obj = new \TranslatePress\Uri( $this->get_abs_home() );
            wp_cache_set('url_obj_' . hash('md4', $this->get_abs_home()), $abs_home_url_obj, 'trp' );
        }

        if( $url_obj->getPath() ){
            if ($abs_home_url_obj->getPath() == "/"){
                $abs_home_url_obj->setPath('');
            }

            $abs_home = $abs_home_url_obj->getPath();

            //in some cases $abs_home_url_obj->getPath() can be null and this causes a PHP 8 notice
            if ($abs_home !== null) {
                $abs_home = $abs_home_url_obj->getPath();
            }else{
                $abs_home = '';
            }
            //we make sure that the path is the actual path and not a folder
            $possible_path = str_replace( $abs_home, '', $url_obj->getPath() );

            $lang = ltrim( $possible_path,'/' );
            $lang = explode('/', $lang);
            if( $lang == false ){
                wp_cache_set('url_language_' . hash('md4', $url), null, 'trp');
                return null;
            }
            // If we have a language in the URL, the first element of the array should be it.
            $lang = $lang[0];

            $lang = apply_filters( 'trp_get_lang_from_url_string', $lang, $url );

            // the lang slug != actual lang. So we need to do array_search so we don't end up with en instead of en_US
            if( isset($this->settings['url-slugs']) && in_array($lang, $this->settings['url-slugs']) ){
                $language = array_search($lang, $this->settings['url-slugs']);
                if ( in_array( $language, $this->settings['publish-languages'] ) ||
                    ( in_array( $language, $this->settings['translation-languages'] ) && current_user_can(apply_filters( 'trp_translating_capability', 'manage_options' )) ) ) {
                    wp_cache_set( 'url_language_' . hash( 'md4', $url ), $language, 'trp' );
                    return $language;
                }
            }
        }
        wp_cache_set('url_language_' . hash('md4', $url), null, 'trp');
        return null;
    }

    /**
     * Return current page url.
     * Always using $this->get_abs_home(), instead of home_url() since that one is filtered by TP
     * @return string
     */
    public function cur_page_url() {

        $req_uri = trp_cache_get('cur_page_url', 'trp');
        if ( $req_uri ){
            return $req_uri;
        }

        $req_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( $_SERVER['REQUEST_URI'] ) : '';

        //in some cases $this->get_abs_home() can be null and this causes a PHP 8 notice
        $abs_home = $this->get_abs_home();
        if ( $this->get_abs_home() !== null) {
            $abs_home = $this->get_abs_home();
        }else{
            $abs_home = '';
        }

        $abs_home_path_url = parse_url($abs_home, PHP_URL_PATH);
        $home_path = ($abs_home_path_url !== null )? trim($abs_home_path_url, '/') : '';

        $home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );

        // Trim path info from the end and the leading home path from the front.
        $req_uri = ltrim( $req_uri, '/' );
        $req_uri = preg_replace( $home_path_regex, '', $req_uri );
        $req_uri = trim( $abs_home, '/' ) . '/' . ltrim( $req_uri, '/' );


        if ( function_exists('apply_filters') ) $req_uri = apply_filters('trp_curpageurl', $req_uri);
        wp_cache_set('cur_page_url', $req_uri, 'trp');
        return $req_uri;
    }

    /**
     * we need to modify the permalinks structure for woocommerce when we switch languages
     * when woo registers post_types and taxonomies in the rewrite parameter of the function they change the slugs of the items (they are localized with _x )
     * we can't flush the permalinks on every page load so we filter the rewrite_rules option
     */
    public function woocommerce_filter_permalinks_on_other_languages( $rewrite_rules ){
        if( class_exists( 'WooCommerce' ) ){
            global $TRP_LANGUAGE;

            if( $TRP_LANGUAGE != $this->settings['default-language'] ){
                global $default_language_wc_permalink_structure; //we use a global because apparently you can't do switch to locale and restore multiple times. I should keep an eye on this
                /* get rewrite rules from original language */
                if( empty($default_language_wc_permalink_structure) ) {
                    $default_language_wc_permalink_structure = trp_get_transient( 'tp_default_language_wc_permalink_structure_'.$this->settings['default-language'] );
                    if( $default_language_wc_permalink_structure === false ) {
                        $default_language_wc_permalink_structure = array();
                        $default_language_wc_permalink_structure['product_rewrite_slug'] = trp_x('product', 'slug', 'woocommerce', $this->settings['default-language']);
                        $default_language_wc_permalink_structure['category_rewrite_slug'] = trp_x('product-category', 'slug', 'woocommerce', $this->settings['default-language']);
                        $default_language_wc_permalink_structure['tag_rewrite_slug'] = trp_x('product-tag', 'slug', 'woocommerce', $this->settings['default-language']);

                        set_transient('tp_default_language_wc_permalink_structure_' . $this->settings['default-language'], $default_language_wc_permalink_structure, 12 * HOUR_IN_SECONDS);
                    }
                }

                $current_language_permalink_structure = trp_get_transient( 'tp_current_language_wc_permalink_structure_'.$TRP_LANGUAGE );
                if( $current_language_permalink_structure === false ) {
                    //always generate the slugs for defaults on the current language
                    $current_language_permalink_structure = array();
                    $current_language_permalink_structure['product_rewrite_slug'] = trp_x('product', 'slug', 'woocommerce', $TRP_LANGUAGE);
                    $current_language_permalink_structure['category_rewrite_slug'] = trp_x('product-category', 'slug', 'woocommerce', $TRP_LANGUAGE);
                    $current_language_permalink_structure['tag_rewrite_slug'] = trp_x('product-tag', 'slug', 'woocommerce', $TRP_LANGUAGE);

                    set_transient( 'tp_current_language_wc_permalink_structure_'.$TRP_LANGUAGE, $current_language_permalink_structure, 12 * HOUR_IN_SECONDS );
                }


                $new_rewrite_rules = array();

                $search = array( '/^'.$default_language_wc_permalink_structure['product_rewrite_slug'].'\//', '/^'.$default_language_wc_permalink_structure['category_rewrite_slug'].'\//', '/^'.$default_language_wc_permalink_structure['tag_rewrite_slug'].'\//' );
                $replace = array( $current_language_permalink_structure['product_rewrite_slug'].'/', $current_language_permalink_structure['category_rewrite_slug'].'/', $current_language_permalink_structure['tag_rewrite_slug'].'/' );

                if( !empty( $rewrite_rules ) && is_array($rewrite_rules) ) {
                    foreach ($rewrite_rules as $rewrite_key => $rewrite_values) {
                        $new_rewrite_rules[preg_replace($search, $replace, $rewrite_key)] = preg_replace($search, $replace, $rewrite_values);
                    }
                }

            }
        }

        if( !empty($new_rewrite_rules) ) {
            return $new_rewrite_rules;
        }
        else
            return $rewrite_rules;
    }

    /* on frontend on other languages dinamically generate the woo permalink structure for the default slugs */
    function woocommerce_filter_permalink_option( $value ){
        global $TRP_LANGUAGE, $trp_wc_permalinks;

        //keep the unfiltered value in a global, we might need it later
        if( !isset( $trp_wc_permalinks ) )
            $trp_wc_permalinks = $value;

        if( $TRP_LANGUAGE != $this->settings['default-language'] ) {
            if( trim($value['product_base'], '/') === trp_x( 'product', 'slug', 'woocommerce', $this->settings['default-language'] ) ){
                $value['product_base'] = '';
                /* in ajax it seems the language is not set correctly and we get the slug for the original language if we leave it blank. detected in sober theme
                Will only do it for products for now as I am not 100% sure it won't impact other things */
                if( wp_doing_ajax() ){
                    $value['product_base'] = trp_x( 'product', 'slug', 'woocommerce', $TRP_LANGUAGE );
                }
            }else{
            	// if the custom base permalink starts with product, WooCommerce will translate it when on other languages
	            if ( substr( $value['product_base'], 0, strlen('/product/' ) ) === '/product/' ) {
		            $value['product_base'] = substr_replace( $value['product_base'], '/' . trp_x( 'product', 'slug', 'woocommerce', $TRP_LANGUAGE ) . '/', 0, strlen('/product/' ) );
	            }
            }

            if( trim($value['category_base'], '/') === trp_x( 'product-category', 'slug', 'woocommerce', $this->settings['default-language'] ) ){
                $value['category_base'] = '';
            }

            if( trim($value['tag_base'], '/') === trp_x( 'product-tag', 'slug', 'woocommerce', $this->settings['default-language'] ) ){
                $value['tag_base'] = '';
            }

        }

        return $value;
    }

    /**
     * Prevent the rewrite_rules option to change when we are not on the default language so we don't get translated data in the database
     * Basically update_option for rewrite_rules does nothing
     * @param $value
     * @param $old_value
     * @return mixed
     */
    function prevent_permalink_update_on_other_languages( $value, $old_value ){
        global $TRP_LANGUAGE;

        if( apply_filters( 'trp_keep_permalinks_unchanged', false ) ||
            ( isset($TRP_LANGUAGE) && $TRP_LANGUAGE != $this->settings['default-language'] && apply_filters( 'trp_prevent_permalink_update_on_other_languages', true ) ) )
        {
            $value = $old_value;
        }

        return $value;
    }


    /**
     * Function that deletes old woocommerce transients so the new one are generated correctly
     * @param $value
     * @return void
     */
    public function delete_woocommerce_transient_permalink($value){

        if( class_exists( 'WooCommerce' ) ) {
            $english_woocommerce_slugs = array( 'product-category', 'product-tag', 'product', 'default_language_wc_permalink_structure', 'current_language_wc_permalink_structure' );

            foreach ( $english_woocommerce_slugs as $english_woocommerce_slug ) {
                delete_transient( 'tp_' . $english_woocommerce_slug . '_' . $this->settings['default-language'] );
                foreach ( $this->settings['translation-languages'] as $language ) {
                    delete_transient( 'tp_' . $english_woocommerce_slug . '_' . $language );
                }
            }
        }
        return $value;
    }

    /**
     * Function that adds pagination to a blog page if it is necessary
     * @param $url
     * @return string
     */
    function maybe_add_pagination_to_blog_page( $url ){
        $pagenum = get_query_var( 'paged' );
        if( !empty( $pagenum ) ) {
            global $wp_rewrite;
            $url = trailingslashit( $url ) . user_trailingslashit($wp_rewrite->pagination_base . '/' . $pagenum, 'paged' );
        }
        return $url;
    }

    /**
     * Try to get the value that is displayed in the Permalinks settings
     * @param $english_woocommerce_slug
     * @return false|mixed|void
     */
    function trp_get_woocommerce_saved_permalink( $english_woocommerce_slug ){
        $wc_options = get_option('woocommerce_permalinks');
        switch($english_woocommerce_slug){
            case 'product-category':
                $option_index = 'category_base';
                break;
            case 'product-tag':
                $option_index = 'tag_base';
                break;
            case 'product':
                $option_index = 'product_base';
                break;
            default:
                $option_index = '';
        }

        if( !empty( $wc_options ) && !empty( $wc_options[$option_index] ) )
            return $wc_options[$option_index];
        elseif( empty( $wc_options[$option_index] ) ){//if it's the default from _x() it won't save in the db
            $current_slug = trp_get_transient( 'tp_'.$english_woocommerce_slug.'_'. $this->settings['default-language'] );
            if( $current_slug === false ){
                $current_slug = trp_x( $english_woocommerce_slug, 'slug', 'woocommerce', $this->settings['default-language'] );
                set_transient( 'tp_'.$english_woocommerce_slug.'_'. $this->settings['default-language'], $current_slug, 12 * HOUR_IN_SECONDS );
            }
            return $current_slug;
        }
        else
            return $english_woocommerce_slug;//always return something
    }

}
