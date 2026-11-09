<?php


if ( !defined('ABSPATH' ) )
    exit();

/** Compatibility functions with WP core and various themes and plugins*/

/**
 * Remove '?fl_builder' query param from edit translation url (when clicking the admin bar button to enter the translation Editor)
 *
 * Otherwise after publishing out of BB and clicking TP admin bar button, it’s still showing the BB interface
 *
 * @param $url
 *
 * @return bool
 */
function trp_beaver_builder_compatibility( $url ){

    $url = remove_query_arg('fl_builder', $url );

    return esc_url ($url);

}
add_filter( 'trp_edit_translation_url', 'trp_beaver_builder_compatibility' );


/**
 * Mb Strings missing PHP library error notice
 */
function trp_mbstrings_notification(){
    echo '<div class="notice notice-error"><p>' . wp_kses( __( '<strong>TranslatePress</strong> requires <strong><a href="http://php.net/manual/en/book.mbstring.php">Multibyte String PHP library</a></strong>. Please contact your server administrator to install it on your server.','translatepress-multilingual' ), [ 'a' => [ 'href' => [] ], 'strong' => [] ] ) . '</p></div>';
}

function trp_missing_mbstrings_library( $allow_to_run ){
    if ( ! extension_loaded('mbstring') ) {
        add_action( 'admin_menu', 'trp_mbstrings_notification' );
        return false;
    }
    return $allow_to_run;
}
add_filter( 'trp_allow_tp_to_run', 'trp_missing_mbstrings_library' );

/**
 * robots.txt should never be handled by TranslatePress.
 *
 * Without this, the robots.txt file is processed by TranslatePress on secondary
 * languages: it becomes accessible (and translated) on language URLs such as
 * /es/robots.txt, and when "Use subdirectory for default language" is enabled the
 * default robots.txt gets redirected to the language slug URL. Since the resulting
 * file no longer matches the canonical one, this causes indexing issues.
 *
 * The check is based on the request URI (instead of is_robots()) because these
 * filters run on 'plugins_loaded', before the query is parsed and conditional tags
 * are available.
 *
 * @see https://app.clickup.com/t/qtc0c2
 *
 * @param string $url Optional URL to check. Defaults to the current request URI.
 * @return bool        Whether the current request targets a robots.txt file.
 */
function trp_is_robots_txt_request( $url = '' ){
    if ( empty( $url ) ) {
        $url = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore
    }
    if ( empty( $url ) || ! is_string( $url ) ) {
        return false;
    }
    // Only look at the path, ignoring any query string or fragment.
    $path = wp_parse_url( $url, PHP_URL_PATH );
    if ( empty( $path ) ) {
        return false;
    }
    return (bool) preg_match( '#(^|/)robots\.txt$#i', $path );
}

// Don't run TranslatePress (no translation, no output buffering) on robots.txt requests.
function trp_stop_running_on_robots_txt( $allow_to_run ){
    if ( trp_is_robots_txt_request() ) {
        return false;
    }
    return $allow_to_run;
}
add_filter( 'trp_allow_tp_to_run', 'trp_stop_running_on_robots_txt' );

// Don't redirect robots.txt to a language URL (e.g. /robots.txt -> /en/robots.txt).
function trp_stop_redirect_on_robots_txt( $allow_redirect, $needed_language, $current_page_url ){
    if ( trp_is_robots_txt_request( $current_page_url ) || trp_is_robots_txt_request() ) {
        return false;
    }
    return $allow_redirect;
}
add_filter( 'trp_allow_language_redirect', 'trp_stop_redirect_on_robots_txt', 10, 3 );

/**
 * Keep the canonical, unprefixed WordPress REST URL accessible when the default
 * language uses a subdirectory. REST requests must not redirect to themselves.
 */
function trp_stop_redirect_on_unprefixed_rest_request( $allow_redirect, $needed_language, $current_page_url ) {
    $trp           = TRP_Translate_Press::get_trp_instance();
    $url_converter = $trp->get_component( 'url_converter' );

    if ( $url_converter->is_unprefixed_rest_request_on_subdirectory_install() ) {
        return false;
    }

    return $allow_redirect;
}
add_filter( 'trp_allow_language_redirect', 'trp_stop_redirect_on_unprefixed_rest_request', 10, 3 );

/**
 * Don't have html inside menu title tags. Some themes just put in the title the content of the link without striping HTML
 */
add_filter( 'nav_menu_link_attributes', 'trp_remove_html_from_menu_title', 10, 3);
function trp_remove_html_from_menu_title( $atts, $item, $args ){
    if( isset( $atts['title'] ) )
        $atts['title'] = wp_strip_all_tags($atts['title']);

    return $atts;
}

/**
 * Rework wp_trim_words so we can trim Chinese, Japanese and Thai words since they are based on characters as words.
 *
 * @since 1.3.0
 *
 * @param string $text      Text to trim.
 * @param int    $num_words Number of words. Default 55.
 * @param string $more      Optional. What to append if $text needs to be trimmed. Default '&hellip;'.
 * @return string Trimmed text.
 */
function trp_wp_trim_words( $text, $num_words, $more, $original_text ) {
    if ( null === $more ) {
        $more = __( '&hellip;' );//phpcs:ignore
    }
    // what we receive is the short text in the filter
    $text = $original_text;
    $text = wp_strip_all_tags( $text );

    $trp = TRP_Translate_Press::get_trp_instance();
    $trp_settings = $trp->get_component( 'settings' );
    $settings = $trp_settings->get_settings();

    $default_language= $settings["default-language"];

    $char_is_word = false;
    foreach (array('ja', 'tw', 'zh') as $lang){
        if (strpos($default_language, $lang) !== false){
            $char_is_word = true;
        }
    }

    if ( $char_is_word && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
        $text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
        preg_match_all( '/./u', $text, $words_array );
        $words_array = array_slice( $words_array[0], 0, $num_words + 1 );
        $sep = '';
    } else {
        $words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
        $sep = ' ';
    }

    if ( count( $words_array ) > $num_words ) {
        array_pop( $words_array );
        $text = implode( $sep, $words_array );
        $text = $text . $more;
    } else {
        $text = implode( $sep, $words_array );
    }

    return $text;
}
add_filter('wp_trim_words', 'trp_wp_trim_words', 100, 4);


/**
 * Use home_url in the https://www.peepso.com/ ajax front-end url so strings come back translated.
 *
 * @since 1.3.1
 *
 * @param array $data   Peepso data
 * @return array
 */
add_filter( 'peepso_data', 'trp_use_home_url_in_peepso_ajax' );
function trp_use_home_url_in_peepso_ajax( $data ){
    if ( is_array( $data ) && isset( $data['ajaxurl_legacy'] ) ){
        $data['ajaxurl_legacy'] = home_url( '/peepsoajax/' );
    }
    return $data;
}

/**
 * Compatibility with Peepso urls having extra / due their link builder not considering home urls having trailing slashes
 */
add_filter('peepso_get_page', 'trp_remove_peepso_double_slash', 10, 2);
function trp_remove_peepso_double_slash( $page, $name){

    // avoid accidentally replacing // from http://
    $page = str_replace('http://', 'http:/', $page );
    $page = str_replace('https://', 'https:/', $page );

    $page = str_replace('//', '/', $page );

    // place it back
    $page = str_replace('https:/', 'https://', $page );
    $page = str_replace('http:/', 'http://', $page );

    return $page;
};

/**
 * Filter ginger_iframe_banner and ginger_text_banner to use shortcodes so our conditional lang shortcode works.
 *
 * @since 1.3.1
 *
 * @param string $content
 * @return string
 */

add_filter('ginger_iframe_banner', 'trp_do_shortcode', 999 );
add_filter('ginger_text_banner', 'trp_do_shortcode', 999 );
function trp_do_shortcode($content){
    return do_shortcode(stripcslashes($content));
}

/**
 * Compatibility with Woocommerce Print Products
 *
 * @param $bool
 * @param $output
 * @return bool|mixed
 */

add_filter( 'trp_skip_gettext_processing', 'trp_woo_strip_gettext_from_print_products' );

function trp_woo_strip_gettext_from_print_products( $bool ){
    if ( isset( $_REQUEST['print-products'] ) && $_REQUEST['print-products'] == 'pdf' && class_exists('\WooCommerce_Print_Products') ) {
        return true;
    }
    return $bool;
}


add_filter('trp_stop_translating_page', 'trp_woo_pdf_print_products', 10, 2);

function trp_woo_pdf_print_products( $bool, $output ){
    if ( isset( $_REQUEST['print-products'] ) && $_REQUEST['print-products'] == 'pdf' && class_exists('\WooCommerce_Print_Products') ) {
        return true;
    }
    return $bool;
}

/**
 * DK PDF compatibility
 *
 * The DK PDF plugin seems to not work at all. Even when TranslatePress is deactivated, there are critical errors and notices in debug.log
 */

add_filter( 'trp_skip_gettext_processing', 'trp_dk_pdf_strip_gettext_from_pdf' );

function trp_dk_pdf_strip_gettext_from_pdf( $bool ){

    if ( isset( $_GET['pdf'] ) && ( class_exists( 'DKPDF' ) || defined( 'DKPDF_VERSION' ) ) ){
        return true;
    }

    return $bool;
}


add_filter('trp_stop_translating_page', 'trp_do_not_translate_dk_pdf', 10, 2);
function trp_do_not_translate_dk_pdf($translate, $output){

    if ( isset( $_GET['pdf'] ) && ( class_exists( 'DKPDF' ) || defined( 'DKPDF_VERSION' ) ) ){
        return true;
    }

    return $translate;
}


/**
 * Compatibility with Invoices for WooCommerce
 * Do not translate url's like this as it brakes them because they are PDF's: https://ro.wordpress.org/plugins/woocommerce-pdf-invoices/
 */

add_filter( 'trp_skip_gettext_processing', 'trp_invoices_for_woocommerce_strip_gettext_from_pdf', 10, 4 );
function trp_invoices_for_woocommerce_strip_gettext_from_pdf( $bool, $translation, $text, $domain ){
    if ( !isset( $_GET['wc-ajax'] ) || $_GET['wc-ajax'] != 'checkout' ) {
        return $bool;
    }

    $domain = trim( $domain );
    $is_invoice_string = (
        $domain === 'woocommerce-pdf-invoice' ||
        ( $text == 'Cash on delivery' && $domain == 'woocommerce' )
    );

    if ( $is_invoice_string && class_exists( '\BEWPI_Invoice' ) ) {
        return true;
    }
    return $bool;
}

add_filter('trp_stop_translating_page', 'trp_do_not_translate_pdf_param', 10, 2);
function trp_do_not_translate_pdf_param($translate, $output){
    if ( isset( $_GET['bewpi_action'] ) && class_exists( '\BEWPI_Invoice' ) ){
        return true;
    }
    return $translate;
}

/**
 * Compatibility with WooCommerce PDF Invoices & Packing Slips
 * https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/
 *
 * @since 1.4.3
 *
 */
// fix attachment name in email
add_filter( 'wpo_wcpdf_filename', 'trp_woo_pdf_invoices_and_packing_slips_compatibility' );

// fix #trpgettext inside invoice pdf
add_filter( 'wpo_wcpdf_get_html', 'trp_woo_pdf_invoices_and_packing_slips_compatibility');
function trp_woo_pdf_invoices_and_packing_slips_compatibility($title){
    if ( class_exists( 'TRP_Translation_Manager' ) ) {
        return 	TRP_Translation_Manager::strip_gettext_tags($title);
    }
}

// fix font of pdf breaking because of str_get_html() call inside translate_page()
add_filter( 'trp_stop_translating_page', 'trp_woo_pdf_invoices_and_packing_slips_compatibility_dont_translate_pdf', 10, 2 );
function trp_woo_pdf_invoices_and_packing_slips_compatibility_dont_translate_pdf( $bool, $output ){
    if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'generate_wpo_wcpdf' ) {
        return true;
    }
    return $bool;
}

/**
 * Compatibility with WooCommerce PDF Invoices (woocommerce-ultimate-pdf-invoices)
 * https://www.welaunch.io/en/product/woocommerce-pdf-invoices/
 *
 * @since 1.4.3
 *
 */
add_filter( 'woocommerce_pdf_invoices_content', 'trp_woo_ultimate_pdf_invoices_compatibility');
add_filter( 'woocommerce_pdf_invoices_order_data', 'trp_woo_ultimate_pdf_invoices_data_compatibility');

function trp_woo_ultimate_pdf_invoices_compatibility($title){
    if ( class_exists( 'TRP_Translation_Manager' ) ) {
        return 	TRP_Translation_Manager::strip_gettext_tags($title);
    }
}

function trp_woo_ultimate_pdf_invoices_data_compatibility($data_array){
    if ( class_exists( 'TRP_Translation_Manager' ) ) {
        $data_array = array_map('TRP_Translation_Manager::strip_gettext_tags',$data_array );
    }
    return $data_array;
}

/**
 * Compatibility with WooCommerce PDF Catalog (woocommerce-pdf-catalog)
 * https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
 *
 * @since 2.2.7
 *
 */
add_filter( 'trp_stop_translating_page', 'trp_woocommerce_pdf_catalog_compatibility_dont_translate_pdf', 10, 2 );
function trp_woocommerce_pdf_catalog_compatibility_dont_translate_pdf( $bool, $output ){
	if ( isset( $_REQUEST['pdf-catalog'] ) ) {
		return true;
	}
	return $bool;
}

/**
 *  Compatibility with YITH WooCommerce
 */

add_filter( 'trp_skip_gettext_processing', 'trp_woo_strip_gettext_from_yith_pdf', 10, 4 );
function trp_woo_strip_gettext_from_yith_pdf( $bool, $translation, $text, $domain ){
    if ( !isset( $_GET['wc-ajax'] ) || $_GET['wc-ajax'] != 'checkout' ) {
        return $bool;
    }

    $domain = trim( $domain );
    $is_invoice_string = (
        $domain === 'yith-woocommerce-pdf-invoice' ||
        ( $text == 'N/A' && $domain == 'woocommerce' )
    );

    if ( $is_invoice_string && class_exists( 'YITH_Checkout_Addon' ) ) {
        return true;
    }
    return $bool;
}

add_filter( 'trp_stop_translating_page', 'trp_woo_pdf_invoices_compatibility_dont_translate_pdf', 10, 2 );
function trp_woo_pdf_invoices_compatibility_dont_translate_pdf( $bool, $output ){
    if ( isset( $_REQUEST['type'] ) && $_REQUEST['type'] == 'proforma' && class_exists( 'YITH_Checkout_Addon' ) ) {
        return true;
    }
    return $bool;

}

/**
 * Generic PDF output detection
 *
 * Stop translating when the output buffer contains raw PDF data, detected via the
 * mandatory "%PDF-" magic bytes (hex 25 50 44 46 2D) that begin every valid PDF per ISO 32000.
 *
 * Prevents TranslatePress from corrupting PDF responses generated by any plugin via
 * admin-ajax.php when the request originates from a translated frontend page.
 *
 * @since 2.8.6
 */
add_filter( 'trp_stop_translating_page', 'trp_do_not_translate_pdf_output', 1, 2 );
function trp_do_not_translate_pdf_output( $bool, $output ) {
    if ( is_string( $output ) && substr( $output, 0, 5 ) === '%PDF-' ) {
        return true;
    }
    return $bool;
}

/**
 * Compatibility with WooCommerce order notes
 *
 * When a new order is placed in secondary languages, in admin area WooCommerce->Orders->Edit Order, the right sidebar contains Order notes which can contain #trpst tags.
 *
 * @since 1.4.3
 */

// old orders
add_filter( 'woocommerce_get_order_note', 'trp_woo_notes_strip_trpst' );
// new orders
add_filter( 'woocommerce_new_order_note_data', 'trp_woo_notes_strip_trpst' );
function trp_woo_notes_strip_trpst( $note_array ){
    foreach ( $note_array as $item => $value ){
        $note_array[$item] = TRP_Translation_Manager::strip_gettext_tags( $value );
    }
    return $note_array;
}

/*
 * Compatibility with WooCommerce back-end display order shipping taxes
 */
add_filter('woocommerce_order_item_display_meta_key','trp_woo_data_strip_trpst');
add_filter('woocommerce_order_item_get_method_title','trp_woo_data_strip_trpst');
function trp_woo_data_strip_trpst( $data ){
    return TRP_Translation_Manager::strip_gettext_tags( $data );
}

/*
 * Always-on write-time stripping of TRP gettext markers from database writes.
 *
 * Prevents #!trpst#trp-gettext...#!trpen# markers from being persisted when
 * plugins pass __() results into data that gets saved (e.g. order item meta keys,
 * payment method titles, comments, options).
 *
 * @since 2.8.5
 */

// 1. WooCommerce Order Pre-Save — clean payment_method_title and customer_note before DB write
add_action( 'woocommerce_before_order_object_save', 'trp_woo_order_pre_save_strip_trpst', 10, 2 );
function trp_woo_order_pre_save_strip_trpst( $order, $data_store ) {
    if ( ! $order instanceof WC_Order ) {
        return;
    }

    $payment_method_title = $order->get_payment_method_title( 'edit' );
    if ( is_string( $payment_method_title ) && strpos( $payment_method_title, 'data-trpgettextoriginal=' ) !== false ) {
        $order->set_payment_method_title( TRP_Translation_Manager::strip_gettext_tags( $payment_method_title ) );
    }

    $customer_note = $order->get_customer_note( 'edit' );
    if ( is_string( $customer_note ) && strpos( $customer_note, 'data-trpgettextoriginal=' ) !== false ) {
        $order->set_customer_note( TRP_Translation_Manager::strip_gettext_tags( $customer_note ) );
    }
}

// 2. WooCommerce Order Item Pre-Save — clean item name, shipping method title, and all queued meta
add_action( 'woocommerce_before_order_item_object_save', 'trp_woo_order_item_pre_save_strip_trpst', 10, 2 );
function trp_woo_order_item_pre_save_strip_trpst( $item, $data_store ) {
    // Strip item name
    $name = $item->get_name( 'edit' );
    if ( is_string( $name ) && strpos( $name, 'data-trpgettextoriginal=' ) !== false ) {
        $item->set_name( TRP_Translation_Manager::strip_gettext_tags( $name ) );
    }

    // Strip shipping method title
    if ( $item instanceof WC_Order_Item_Shipping ) {
        $method_title = $item->get_method_title( 'edit' );
        if ( is_string( $method_title ) && strpos( $method_title, 'data-trpgettextoriginal=' ) !== false ) {
            $item->set_method_title( TRP_Translation_Manager::strip_gettext_tags( $method_title ) );
        }
    }

    // Strip queued meta data (not yet persisted — clean in memory before save_meta_data runs)
    $meta_data = $item->get_meta_data();
    foreach ( $meta_data as $meta ) {
        $changed = false;

        if ( is_string( $meta->key ) && strpos( $meta->key, 'data-trpgettextoriginal=' ) !== false ) {
            $meta->key = TRP_Translation_Manager::strip_gettext_tags( $meta->key );
            $changed = true;
        }

        if ( is_string( $meta->value ) && strpos( $meta->value, 'data-trpgettextoriginal=' ) !== false ) {
            $meta->value = TRP_Translation_Manager::strip_gettext_tags( $meta->value );
            $changed = true;
        }

        if ( is_array( $meta->value ) ) {
            $serialized = serialize( $meta->value );
            if ( strpos( $serialized, 'data-trpgettextoriginal=' ) !== false ) {
                array_walk_recursive( $meta->value, 'trp_array_walk_recursive_strip_gettext_tags' );
            }
        }
    }
}

// 3. Order Item Meta Post-Save Safety Net — catch direct add_metadata/update_metadata calls
add_action( 'added_order_item_meta', 'trp_woo_order_item_meta_post_save_strip_trpst', 10, 4 );
add_action( 'updated_order_item_meta', 'trp_woo_order_item_meta_post_save_strip_trpst', 10, 4 );
function trp_woo_order_item_meta_post_save_strip_trpst( $meta_id, $object_id, $meta_key, $meta_value ) {
    $key_contaminated   = is_string( $meta_key ) && strpos( $meta_key, 'data-trpgettextoriginal=' ) !== false;
    $value_contaminated = is_string( $meta_value ) && strpos( $meta_value, 'data-trpgettextoriginal=' ) !== false;

    if ( ! $key_contaminated && ! $value_contaminated ) {
        return;
    }

    global $wpdb;

    if ( $key_contaminated ) {
        $clean_key = TRP_Translation_Manager::strip_gettext_tags( $meta_key );
        $wpdb->update(
            $wpdb->prefix . 'woocommerce_order_itemmeta',
            array( 'meta_key' => $clean_key ),
            array( 'meta_id' => $meta_id ),
            array( '%s' ),
            array( '%d' )
        );
    }

    if ( $value_contaminated ) {
        $clean_value = TRP_Translation_Manager::strip_gettext_tags( $meta_value );
        // Remove our own hook to prevent recursion, then re-add
        remove_action( current_action(), 'trp_woo_order_item_meta_post_save_strip_trpst', 10 );
        update_metadata_by_mid( 'order_item', $meta_id, $clean_value );
        add_action( current_action(), 'trp_woo_order_item_meta_post_save_strip_trpst', 10, 4 );
    }
}

// 4. WordPress Comments Pre-Save — clean comment_content and comment_author
add_filter( 'preprocess_comment', 'trp_comment_pre_save_strip_trpst' );
function trp_comment_pre_save_strip_trpst( $commentdata ) {
    $fields = array( 'comment_content', 'comment_author' );
    foreach ( $fields as $field ) {
        if ( isset( $commentdata[ $field ] ) && is_string( $commentdata[ $field ] ) && strpos( $commentdata[ $field ], 'data-trpgettextoriginal=' ) !== false ) {
            $commentdata[ $field ] = TRP_Translation_Manager::strip_gettext_tags( $commentdata[ $field ] );
        }
    }
    return $commentdata;
}

// 5. WordPress Options Pre-Save — clean option values (skip TRP's own options)
add_filter( 'pre_update_option', 'trp_option_pre_save_strip_trpst', 10, 3 );
function trp_option_pre_save_strip_trpst( $value, $option, $old_value ) {
    // Skip TRP's own options
    if ( strpos( $option, 'trp_' ) === 0 ) {
        return $value;
    }

    if ( is_string( $value ) ) {
        if ( strpos( $value, 'data-trpgettextoriginal=' ) !== false ) {
            $value = TRP_Translation_Manager::strip_gettext_tags( $value );
        }
    } elseif ( is_array( $value ) ) {
        $serialized = serialize( $value );
        if ( strpos( $serialized, 'data-trpgettextoriginal=' ) !== false ) {
            array_walk_recursive( $value, 'trp_array_walk_recursive_strip_gettext_tags' );
        }
    }

    return $value;
}

/**
 * Compatibility with Modern Events Calendar (MEC).
 *
 * MEC reads price/total values out of DOM nodes and posts them back via AJAX,
 * which causes TRP gettext markers wrapping the rendered labels to end up in
 * post meta (e.g. mec_total_price = "85.00 #!trpst#trp-gettext...#!trpen#").
 * That breaks external integrations expecting numeric values.
 *
 * Exclude the MEC price/total selectors from frontend translation so the
 * markers don't reach AJAX submissions, and strip any markers that still slip
 * through into the affected post meta keys.
 */
add_filter( 'trp_no_translate_selectors', 'trp_mec_no_translate_selectors' );
function trp_mec_no_translate_selectors( $skip_selectors ) {
    $skip_selectors[] = '.mec-total-cost';
    $skip_selectors[] = '.mec-book-price-total';
    $skip_selectors[] = '#mec_total_price';
    return $skip_selectors;
}

add_action( 'added_post_meta', 'trp_mec_post_meta_strip_trpst', 10, 4 );
add_action( 'updated_postmeta', 'trp_mec_post_meta_strip_trpst', 10, 4 );
function trp_mec_post_meta_strip_trpst( $meta_id, $object_id, $meta_key, $meta_value ) {
    $mec_meta_keys = array( 'mec_total_price', 'mec_paid_amount', 'mec_gateway_label' );

    if ( ! in_array( $meta_key, $mec_meta_keys, true ) ) {
        return;
    }

    if ( ! is_string( $meta_value ) || strpos( $meta_value, 'data-trpgettextoriginal=' ) === false ) {
        return;
    }

    if ( ! class_exists( 'TRP_Translation_Manager' ) ) {
        return;
    }

    $clean_value = TRP_Translation_Manager::strip_gettext_tags( $meta_value );

    // Remove our own hooks to prevent recursion, then re-add
    remove_action( 'added_post_meta', 'trp_mec_post_meta_strip_trpst', 10 );
    remove_action( 'updated_postmeta', 'trp_mec_post_meta_strip_trpst', 10 );
    update_metadata_by_mid( 'post', $meta_id, $clean_value );
    add_action( 'added_post_meta', 'trp_mec_post_meta_strip_trpst', 10, 4 );
    add_action( 'updated_postmeta', 'trp_mec_post_meta_strip_trpst', 10, 4 );
}

/**
 * Compatibility with WooCommerce country list on checkout.
 *
 * Skip detection by translate-dom-changes of the list of countries
 *
 */
add_filter( 'trp_skip_selectors_from_dynamic_translation', 'trp_woo_skip_dynamic_translation' );
function trp_woo_skip_dynamic_translation( $skip_selectors ){
    if( class_exists( 'WooCommerce' ) ) {
        $add_skip_selectors = array( '#billing_country', '#shipping_country', '#billing_state', '#shipping_state', '#select2-billing_country-results',  '#select2-billing_state-results', '#select2-shipping_country-results', '#select2-shipping_state-results' );
        return array_merge( $skip_selectors, $add_skip_selectors );
    }
    return $skip_selectors;
}

/**
 * Prevent translation of names and addresses in WooCommerce emails.
 */
add_action( 'woocommerce_email_customer_details', 'trp_woo_prevent_address_from_translation_in_emails' );
function trp_woo_prevent_address_from_translation_in_emails(){
    add_filter( 'woocommerce_order_get_formatted_shipping_address', 'trp_woo_address_no_translate', 10, 3 );
    add_filter( 'woocommerce_order_get_formatted_billing_address', 'trp_woo_address_no_translate', 10, 3 );
}

function trp_woo_address_no_translate( $address, $raw_address, $order ){
    return empty( $address ) ? $address : '<span data-no-translation>' . $address . '</span>';
}

/**
 * Compatibility with WooCommerce product variation.
 *
 * Add span tag to woocommerce product variation name.
 *
 * Product variation name keep changes, but the prefix is the same. Wrap the prefix to allow translating that part separately.
 */
add_filter( 'woocommerce_product_variation_title', 'trp_woo_wrap_variation', 8, 4);
function trp_woo_wrap_variation($name, $product, $title_base, $title_suffix){
    $separator  = '<span> - </span>';
    return $title_suffix ? $title_base . $separator . $title_suffix : $title_base;
}

/**
 * Compatibility with WooCommerce Layered Nav widget (Filter Products by Attribute) in dropdown mode.
 *
 * The "Any %s" gettext wraps a dynamic taxonomy label. On secondary languages TRP switches the
 * locale, so the target .mo is active (e.g. French translates "Any %s" → "%s", Spanish →
 * "Cualquier %s"), producing a mixed-language label. The trp-gettext wrapper also causes
 * data-no-translation on the <option>, blocking visual editor translation.
 *
 * Fix: rebuild the label by reading "Any %s" directly from the default-language .mo file,
 * bypassing TRP's gettext filter and the switched locale. The result is a static, non-gettext
 * string in the default language (e.g. "Orice Color") that TRP can translate via the visual editor.
 */
add_filter( 'woocommerce_layered_nav_any_label', 'trp_woo_rebuild_layered_nav_any_label', 10, 3 );
function trp_woo_rebuild_layered_nav_any_label( $any_label, $taxonomy_label, $taxonomy ) {
    if ( ! class_exists( 'TRP_Translation_Manager' ) ) {
        return $any_label;
    }

    $taxonomy_label = TRP_Translation_Manager::strip_gettext_tags( $taxonomy_label );

    // Read "Any %s" from the default-language .mo file directly, reusing the same
    // MO-loading infrastructure as trp_x() but without the context separator (chr(4)).
    static $any_format = null;
    if ( $any_format === null ) {
        $any_format = 'Any %s'; // English fallback

        $trp          = TRP_Translate_Press::get_trp_instance();
        $trp_settings = $trp->get_component( 'settings' );
        $settings     = $trp_settings->get_settings();
        $default_lang = $settings['default-language'];

        $path = trp_find_translation_location_for_domain( 'woocommerce', $default_lang );
        if ( ! empty( $path ) ) {
            $cache_key = 'trp_x_woocommerce_' . $default_lang;
            $mo_file   = trp_cache_get( $cache_key );

            if ( false === $mo_file ) {
                $mo_file = new MO();
                $mo_file->import_from_file( $path );
                wp_cache_set( $cache_key, $mo_file );
            }

            if ( $mo_file && ! empty( $mo_file->entries['Any %s'] ) ) {
                $any_format = $mo_file->entries['Any %s']->translations[0];
            }
        }
    }

    return sprintf( $any_format, $taxonomy_label );
}

// trpgettext tags don't get escaped because they add <small> tags through a regex.
add_filter( 'qm/output/title', 'trp_qm_strip_gettext', 100);
function trp_qm_strip_gettext( $data ){
    if ( is_array( $data ) ) {
        foreach( $data as $key => $value ){
            $data[$key] = trp_qm_strip_gettext($value);
        }
    }else {
        // remove small tags
        $data = preg_replace('(<(\/)?small>)', '', $data);
        // strip gettext (not needed, they are just numbers shown in admin bar anyway)
        $data = TRP_Translation_Manager::strip_gettext_tags( $data );
        // add small tags back the same way they do it in the filter 'qm/output/title'
        $data = preg_replace( '#\s?([^0-9,\.]+)#', '<small>$1</small>', $data );
    }
    return $data;
}

/**
 * Compatibility with SeedProd Coming Soon
 *
 * Manually include the scripts and styles if do_action('enqueue_scripts') is not called
 */
add_filter( 'trp_translated_html', 'trp_force_include_scripts', 10, 4 );
function trp_force_include_scripts( $final_html, $TRP_LANGUAGE, $language_code, $preview_mode ){
    if ( $preview_mode ){
        $trp = TRP_Translate_Press::get_trp_instance();
        $translation_render = $trp->get_component( 'translation_render' );
        $trp_data = $translation_render->get_trp_data();

        $scripts_and_styles = apply_filters('trp_editor_missing_scripts_and_styles', array(
            'jquery'                        => "<script type='text/javascript' src='" . includes_url( '/js/jquery/jquery.js' ) . "'></script>",
            'trp-iframe-preview-script.js'  => "<script type='text/javascript' src='" . TRP_PLUGIN_URL . "assets/js/trp-iframe-preview-script.js'></script>",
            'trp-translate-dom-changes.js'  => "<script>trp_data = '" . addslashes(json_encode($trp_data) ) . "'; trp_data = JSON.parse(trp_data);</script><script type='text/javascript' src='" . TRP_PLUGIN_URL . "assets/js/trp-translate-dom-changes.js'></script>",
            'trp-preview-iframe-style-css'  => "<link rel='stylesheet' id='trp-preview-iframe-style-css'  href='" . TRP_PLUGIN_URL . "assets/css/trp-preview-iframe-style.css' type='text/css' media='all' />",
            'dashicons'                     => "<link rel='stylesheet' id='dashicons-css'  href='" . includes_url( '/css/dashicons.min.css' ) . "' type='text/css' media='all' />"
        ));

        $missing_script = '';
        foreach($scripts_and_styles as $key => $value ){
            if ( strpos( $final_html, $key ) === false ){
                $missing_script .= $value;
            }
        }

        if ( $missing_script !== '' ){
            $html = TranslatePress\str_get_html( $final_html, true, true, TRP_DEFAULT_TARGET_CHARSET, false, TRP_DEFAULT_BR_TEXT, TRP_DEFAULT_SPAN_TEXT );
            if ( $html === false ) {
                return $final_html;
            }

            $body = $html->find( 'body', 0 );
            if ( $body ) {
                $body->innertext = $body->innertext . $missing_script;
            }

            $final_html = $html->save();
        }
    }
    return $final_html;
}

/*
 * Compatibility with plugins sending Gettext strings in requests such as Cartflows
 *
 * Strip gettext wrappings from the requests made from http->post()
 */
// Strip of gettext wrappings all the values of the body request array
add_filter( 'http_request_args', 'trp_strip_trpst_from_requests', 10, 2 );
function trp_strip_trpst_from_requests($args, $url){
    if( is_array( $args['body'] ) ) {
        array_walk_recursive( $args['body'], 'trp_array_walk_recursive_strip_gettext_tags' );
    }else{
        $args['body'] = TRP_Translation_Manager::strip_gettext_tags( $args['body'] );
    }
    return $args;
}
function trp_array_walk_recursive_strip_gettext_tags( &$value ){
    $value = TRP_Translation_Manager::strip_gettext_tags( $value );
}

// Strip of gettext wrappings the customer_name and customer_email keys. Found in WC Stripe and Cartflows
add_filter( 'wc_stripe_payment_metadata', 'trp_strip_request_metadata_keys' );
function trp_strip_request_metadata_keys( $metadata ){
    foreach( $metadata as $key => $value ) {
        $stripped_key = TRP_Translation_Manager::strip_gettext_tags( $key );
        if ( $stripped_key != $key ) {
            $metadata[ $stripped_key ] = $value;
            unset( $metadata[ $key ] );
        }
    }
    return $metadata;
}

/**
 * Let Hummingbird start its page-cache buffer before TranslatePress.
 *
 * Both normally start at init priority 0, but TranslatePress registers first.
 * Buffers are processed in reverse order, so Hummingbird would cache the page
 * before it is translated and before #TRPLINKPROCESSED markers are removed.
 */
add_filter( 'trp_start_output_buffer_priority', 'trp_hummingbird_compatibility' );
function trp_hummingbird_compatibility( $priority ){
    if ( defined( 'WPHB_VERSION' ) && (int) $priority === 0 ) {
        return 1;
    }

    return $priority;
}

/**
 * Compatibility with NextGEN Gallery
 *
 * They start an output buffer at init -1 (before ours at init 0). They print footer scripts after we run translate_page,
 * resulting in outputting scripts that won't be stripped of trpst trp-gettext wrappings.
 * This includes WooCommerce Checkout scripts, resulting in trpst wrappings around form fields like Street Address.
 * Another issue is that translation editor is a blank page.
 *
 * We cannot move their hook to priority 1 because we do not have access to the object that gets hooked is not retrievable so we can't call remove_filter()
 * Also we cannot simply disable ngg using run_ngg_resource_manager hook because we would be disabling features of their plugin.
 *
 * So the only solution that works is to move our hook to -2.
 */
add_filter( 'trp_start_output_buffer_priority', 'trp_nextgen_compatibility' );
function trp_nextgen_compatibility( $priority ){
    if ( class_exists( 'C_Photocrati_Resource_Manager' ) ) {
        return '-2';
    }
    return $priority;
}

/**
 * Compatibility with NextGEN Gallery
 *
 * This plugin is adding wp_footer forcefully in a shutdown hook and appends it to "</body>" which bring up admin bar in translation editor.
 *
 * This filter prevents ngg from hooking the filters to alter the html.
 */
add_filter( 'run_ngg_resource_manager', 'trp_nextgen_disable_nextgen_in_translation_editor');
function trp_nextgen_disable_nextgen_in_translation_editor( $bool ){
    if ( isset( $_REQUEST['trp-edit-translation'] ) && sanitize_text_field( $_REQUEST['trp-edit-translation'] ) === 'true' ) {
        return false;
    }
    return $bool;
}

/**
 * Compatibility with WooCommerce added to cart message
 *
 * Makes sure title of product is translated.
 *
 * The title of product is added through sprintf %s of a Gettext.
 *
 */
add_filter( 'the_title', 'trp_woo_translate_product_title_added_to_cart', 10, 2 );
function trp_woo_translate_product_title_added_to_cart( ...$args ){
    // fix themes that don't implement the_title filter correctly. Works on PHP 5.6 >.
    // Implemented this because users we getting this error frequently.
    if( isset($args[0])){
        $title = $args[0];
    } else {
        $title = '';
    }


    if( class_exists( 'WooCommerce' ) ){
        if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);//set a limit if it is supported to improve performance
        }
        else{
            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }

        $list_of_functions = apply_filters( 'trp_woo_translate_title_before_translate_page', array( 'wc_add_to_cart_message' ) );
        if( !empty( $callstack_functions ) ) {
            foreach ( $callstack_functions as $callstack_function ) {
                if ( in_array( $callstack_function['function'], $list_of_functions ) ) {
                    $trp = TRP_Translate_Press::get_trp_instance();
                    $translation_render = $trp->get_component( 'translation_render' );
                    $title = $translation_render->translate_page($title);
                    break;
                }
            }
        }
    }
    return $title;
}

/**
 * Compatibility with WooCommerce "remove from cart" action
 *
 * In some cases (eg for Taiwanese) the product name and double quotes &ldquo &rdquo HTML entities
 * were translated/parsed wrongly.
 * We provide a fix by adding spaces between the quotes and product name
 *
 */

if( class_exists( 'WooCommerce' ) ) {
	add_filter( 'woocommerce_cart_item_removed_title', 'trp_woo_fix_product_remove_from_cart_notice', 10, 2 );

	function trp_woo_fix_product_remove_from_cart_notice($message, $cart_item){
		$product = wc_get_product( $cart_item['product_id'] );
		if ($product){
			$trp                = TRP_Translate_Press::get_trp_instance();
			$translation_render = $trp->get_component( 'translation_render' );
			$product_name       = $translation_render->translate_page( $product->get_name() );
			$message            = sprintf( _x( '&ldquo; %s &rdquo;', 'Item name in quotes', 'woocommerce' ), $product_name ); //phpcs:ignore
		}
		return $message;
	}
}

/**
 * Compatibility with WooTour plugin
 *
 * They replace spaces (" ") with \u0020, after we apply #trpst and because we don't strip them it breaks html
 */
add_action('init', 'trp_wootour_add_gettext_filter');
function trp_wootour_add_gettext_filter(){
    if ( class_exists( 'WooTour_Booking' ) ){
        add_filter('gettext', 'trp_wootour_exclude_gettext_strings', 1000, 3 );
    }
}
function trp_wootour_exclude_gettext_strings($translation, $text, $domain){
    if ( $domain == 'woo-tour' ){
        if ( in_array( $text, array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ) ) ){
            return TRP_Translation_Manager::strip_gettext_tags( $translation );
        }
    }
    return $translation;
}

/**
 * Compatibility: WooCommerce cart product name translation normalization
 *
 * WHAT THIS FIX DOES
 *  Normalizes the product name text inside the cart item link to exactly match `get_the_title( $product_id )`,
 *  so TranslatePress see the same character used in product titles everywhere.
 *
 *  By forcing the cart item name to the exact output of `get_the_title( $product_id )`, we ensure consistent characters.
 *
 * CONDITIONS
 *
 *  1) The cart is rendered with the [woocommerce_cart] shortcode (classic/cart template),
 *     NOT the Cart Block. With the Cart Block, this filter typically won't run on the same markup.
 *  2) The product name contains a '-' character (ASCII `-`, U+002D).
 *
 * You can reproduce by:
 *  - Using a simple product with `-` in its title, OR
 *  - Using a variable product where the variation title includes `-` (variations usually append attributes with `-`).
 *
 */
add_filter( 'woocommerce_cart_item_name', 'trp_woo_cart_item_name', 8, 3 );
function trp_woo_cart_item_name( $product_name, $cart_item, $cart_item_key ){
    if ( !strpos( $product_name, '</a>' ) )
        return $product_name;

    $product_id = $cart_item['variation_id'] ?? $cart_item['product_id'] ?? null;

    if ( !$product_id )
        return $product_name;

    $title = get_the_title( $product_id );

    if ( empty( $title ) )
        return $product_name;

    preg_match_all('~<a(.*?)href="([^"]+)"(.*?)>~', $product_name, $matches);

    if ( !isset( $matches[2][0] ) )
        return $product_name;

    $product_name = sprintf( '<a href="%s">%s</a>', esc_url( $matches[2][0] ), $title );

    return $product_name;
}

/**
 * Compatibility with WooCommerce PDF Invoices & Packing Slips
 *
 * Translate product name and variation (meta) in pdf invoices.
 */
add_filter( 'wpo_wcpdf_order_item_data', 'trp_woo_wcpdf_translate_product_name', 10, 3 );
function trp_woo_wcpdf_translate_product_name( $data, $order, $type ){
    if ( isset( $data['name'] ) ) {
        $trp = TRP_Translate_Press::get_trp_instance();
        $translation_render = $trp->get_component('translation_render');
        remove_filter( 'trp_stop_translating_page', 'trp_woo_pdf_invoices_and_packing_slips_compatibility_dont_translate_pdf', 10 );
        $data['name'] = $translation_render->translate_page($data['name']);
        if ( isset( $data['meta'] ) ) {
            $data['meta'] = $translation_render->translate_page($data['meta']);
        }
        add_filter( 'trp_stop_translating_page', 'trp_woo_pdf_invoices_and_packing_slips_compatibility_dont_translate_pdf', 10, 2 );
    }
    return $data;
}

/**
 * Compatibility with WooCommerce Checkout Add-Ons plugin
 *
 * Exclude name of "paid add-on" item from being run through gettext.
 *
 * No other filters were found. Advanced settings strip meta did not work.
 * It's being added through WC->add_fee and inserted directly in db in custom table.
 */
add_action( 'woocommerce_cart_calculate_fees', 'trp_woo_checkout_add_ons_filter_trpstr', 10, 2);
function trp_woo_checkout_add_ons_filter_trpstr(){
    if ( class_exists('WC_Checkout_Add_Ons_Frontend') ) {
        add_filter('trp_skip_gettext_processing', 'trp_woo_checkout_exclude_strings', 1000, 4);
    }
}

function trp_woo_checkout_exclude_strings( $return, $translation, $text, $domain) {
    if ( $domain === 'woocommerce-checkout-add-ons' ) {
        $add_ons = wc_checkout_add_ons()->get_add_ons();
        foreach ($add_ons as $add_on) {
            if ( $add_on->name === $text)
                return true;
        }
    }
    return $return;
}

/**
 * Compatibility with WooCommerce Fondy Payment gateway
 */
add_action('init', 'trp_woo_fondy_payment_gateway_add_gettext_filter');
function trp_woo_fondy_payment_gateway_add_gettext_filter(){
    if ( class_exists( 'WC_fondy' ) ){
        add_filter('gettext', 'trp_woo_fondy_payment_gateway_exclude_gettext_strings', 1000, 3 );
    }
}

function trp_woo_fondy_payment_gateway_exclude_gettext_strings($translation, $text, $domain){
    if ( $domain == 'fondy-woocommerce-payment-gateway' && $text == 'Order: ' ){
        return TRP_Translation_Manager::strip_gettext_tags( $translation );
    }
    return $translation;
}

/**
 * Compatibility with Woocommerce Product Filters plugin, unknown author
 * This is NOT about the plugin made by WBM https://woobewoo.com/, nor by barn2.com
 *
 * They stop the buffering at priority -150 and that leaves #trpst style tags before we get to remove them
 *
 * The caveat to removing or adding a foreign filter is that it can be done via
 * a) static class call or b) through an object instance
 *
 * In this case we obtained access to global objects set by the WCPF plugin
 * and their public methods.
 *
 */

add_action( 'init', 'trp_woo_product_filters', 10 );

function trp_woo_product_filters(){
	if( isset( $GLOBALS['wcpf_plugin'] ) && class_exists( 'WooCommerce_Product_Filter_Plugin\Filters' ) ){
		$wcpf_plugin = $GLOBALS['wcpf_plugin'];
		$component_register = $wcpf_plugin->get_component_register();
		$filters = $component_register->get('Filters');
		$hook_manager = $filters->get_hook_manager();
		$hook_manager->remove_action( 'shutdown', 'end_of_buffering', -150 );
		$hook_manager->add_action( 'shutdown', 'end_of_buffering', 100 );
	}
}

/**
 * Compatibility with WooCommerce Product Filters by barn2
 * https://barn2.com/wordpress-plugins/woocommerce-product-filters/
 *
 * Set chunk size to 0 because the result of the wcf_fetch_data is HTML instead of JSON, causing errors in browser console
 */
if ( class_exists( 'Barn2\Plugin\WC_Filters\Plugin_Factory' ) ) {
    add_filter( "trp_output_buffer_chunk_size", "trp_set_chunk_size_to_zero", 10, 1 );
}
function trp_set_chunk_size_to_zero( $chunk_size ) {
    $chunk_size = 0;

    return $chunk_size;
}


/**
 * Compatibility with Elementor Popups Links
 *
 * The url is urlencoded so we add the language to it but we shouldn't.
 *
 */
add_filter('trp_skip_url_for_language', 'trp_skip_elementor_popup_action_from_url_converter', 10, 2);
function trp_skip_elementor_popup_action_from_url_converter($value, $url){
	if(strpos($url, '%23elementor-action') !== false){
		return true;
	}
	return $value;
}

/**
 * Strip gettext wrapping from get_the_date function parameter $d
 */
add_filter('get_the_date','trp_strip_gettext_from_get_the_date', 1, 3);
function trp_strip_gettext_from_get_the_date($the_date, $d = NULL, $post = NULL){
	if ( $d === NULL || $post === NULL ){
		return $the_date;
	}

    $d = TRP_Translation_Manager::strip_gettext_tags( $d );
    $post = get_post( $post );

    if ( ! $post ) {
        return false;
    }

    if ( '' == $d ) {
        $the_date = get_post_time( get_option( 'date_format' ), false, $post, true );
    } else {
        $the_date = get_post_time( $d, false, $post, true );
    }

    return $the_date;
}


/**
 * Compatibility with Affiliate Theme
 * It's adding parameters found in the filter forms automatically, braking the query.
 * TranslatePress adds the trp-form-language for other reasons. So we need to remove it in this case.
 * https://affiliatetheme.io
 *
 */
add_filter('at_set_product_filter_query', 'trp_remove_lang_param_from_query');
function trp_remove_lang_param_from_query($args){

	if ( isset( $args['meta_query'] ) && is_array( $args['meta_query']) ){
		foreach($args['meta_query'] as $key => $value){
			if ($value['key'] == 'trp-form-language'){
				unset( $args['meta_query'][$key] );
			}
		}
		$args['meta_query'] = array_values($args['meta_query']);
	}

	return $args;
}


/**
 * Set user prefered language to the language he was present on new user creation.
 * Only set it if an existing locale isn't set already, in case the registration comes from a form that sets the locale manually.
 *
 */
add_action( 'user_register', 'trp_add_user_prefered_language', 10 );
function trp_add_user_prefered_language($user_id) {
	global $TRP_LANGUAGE;
	if ( ! empty( $TRP_LANGUAGE ) ) {
		$user_locale = get_user_meta( $user_id, 'locale', true );
		if ( empty( $user_locale ) ) {
			update_user_meta( $user_id, 'locale', $TRP_LANGUAGE );
		}
	}
}

/*
 * Dflip Compatibility
 * With Secondary Language First, it deferes jquery and scripts don't load on the Elementor Editor.
 * Not sure exactly what's causing. I assume it's because Elementor loads with Ajax certain elements and that comes back broken somehow.
 */
add_action('wp_enqueue_scripts', 'trp_remove_dflip_defer_script', 9999);
function trp_remove_dflip_defer_script(){
	if(class_exists('DFlip')){
		$dflip_instance = DFlip::get_instance();
		remove_filter( 'script_loader_tag', array( $dflip_instance, 'add_defer_attribute' ), 10, 2 );
	}
}

/**
 * Ignore WooCommerce display_name gettext
 * _x( '%1$s %2$s', 'display name', 'woocommerce' ) || wordpress\wp-content\plugins\woocommerce\includes\class-wc-customer.php
 * _x( '%1$s %2$s', 'Display name based on first name and last name')   || wordpress\wp-includes\user.php
 * This will insert trpstr strings in the database. So just ignore it.
 *
 */
add_filter('trp_skip_gettext_processing', 'trp_exclude_woo_display_name_gettext', 2000, 4 );
function trp_exclude_woo_display_name_gettext ( $return, $translation, $text, $domain ){
	if($text == '%1$s %2$s' && $domain == 'woocommerce'){
		return true;
	}

	if($text == '%1$s %2$s' && $domain == 'default'){
		return true;
	}

	return $return;
}

/** Compatibility with superfly menu plugin.
 *
 *  Moving their script later so that dynamic translation detects their strings.
 */
add_action('wp_head','trp_superfly_change_menu_loading_hook', 5);
function trp_superfly_change_menu_loading_hook(){
    if ( remove_action ('wp_head', 'sf_dynamic') ){
        add_action ('wp_print_footer_scripts', 'sf_dynamic', 20);
    }
}

/**
 * Compatibility with Yoast SEO Canonical URL and Opengraph URL
 * Yoast places the canonical wrongly and it's not processed correctly.
 */
add_filter( 'wpseo_canonical', 'trp_wpseo_canonical_compat', 99999, 2);
function trp_wpseo_canonical_compat( $canonical, $presentation_class = null ){
    global $TRP_LANGUAGE;
    $trp           = TRP_Translate_Press::get_trp_instance();
    $url_converter = $trp->get_component( 'url_converter' );
    $canonical     = $url_converter->get_url_for_language( $TRP_LANGUAGE, $canonical, '' );

    return $canonical;
};

add_filter( 'wpseo_opengraph_url', 'trp_opengraph_url', 99999 );
function trp_opengraph_url( $url ) {
	global $TRP_LANGUAGE;
	$trp = TRP_Translate_Press::get_trp_instance();
	$url_converter = $trp->get_component( 'url_converter' );
	$url = $url_converter->get_url_for_language($TRP_LANGUAGE, $url, '');

	return $url;
}

/**
 * Compatibility with RankMath SEO Canonical URL and OpenGraph URL
 * RankMath places the canonical wrongly and it's not processed correctly.
 */
add_filter( 'rank_math/frontend/canonical', 'trp_rankmath_canonical_compat', 99999 );
function trp_rankmath_canonical_compat( $canonical ){
    global $TRP_LANGUAGE;
    $trp           = TRP_Translate_Press::get_trp_instance();
    $url_converter = $trp->get_component( 'url_converter' );
    $canonical     = $url_converter->get_url_for_language( $TRP_LANGUAGE, $canonical, '' );

    return $canonical;
}

add_filter( 'rank_math/opengraph/url', 'trp_rankmath_opengraph_url', 99999 );
function trp_rankmath_opengraph_url( $url ) {
    global $TRP_LANGUAGE;
    $trp = TRP_Translate_Press::get_trp_instance();
    $url_converter = $trp->get_component( 'url_converter' );
    $url = $url_converter->get_url_for_language( $TRP_LANGUAGE, $url, '' );

    return $url;
}

/**
 * Compatibility with Oxygen plugin
 *
 * Improves stylesheet loading time by disabling gettext and regular text detection for pages loaded with xlink=css
 */
add_action( 'trp_before_running_hooks', 'trp_oxygen_remove_gettext_hooks', 10, 1 );
function trp_oxygen_remove_gettext_hooks( $trp_loader ) {
    if ( isset( $_REQUEST['xlink'] ) && $_REQUEST['xlink'] === 'css' ) {
        $trp                 = TRP_Translate_Press::get_trp_instance();
        $gettext_manager = $trp->get_component( 'gettext_manager' );
        $translation_render = $trp->get_component( 'translation_render' );
        $trp_loader->remove_hook( 'init', 'create_gettext_translated_global', $gettext_manager );
        $trp_loader->remove_hook( 'init', 'initialize_gettext_processing', $gettext_manager );
        $trp_loader->remove_hook( 'shutdown', 'machine_translate_gettext', $gettext_manager );
        $trp_loader->remove_hook( 'init', 'start_output_buffer', $translation_render );
        $trp_loader->remove_hook( 'the_title', 'wrap_with_post_id', $translation_render );
        $trp_loader->remove_hook( 'the_content', 'wrap_with_post_id', $translation_render );
    }
}

/**
 * Compatibility with Oxygen plugin for search
 * Basically they use shortcodes to output content so we wrap the shortcode output for certain shortcodes
 */
if( function_exists('ct_is_show_builder') ) {
    add_filter('do_shortcode_tag', 'tp_oxygen_search_compatibility', 10, 4);
    function tp_oxygen_search_compatibility($output, $tag, $attr, $m){
    	// we're skiping the oxygen $tag as that one represents a dynamic shortcode based on custom fields. At times it contains images, links, numbers. Rarely we see actual content.
    	if( $tag === 'ct_headline' || $tag === 'ct_text_block' ) {
            global $post, $TRP_LANGUAGE;

            if (empty($post->ID))
                return $output;

            //we try to wrap only the actual content of the post
            if (!is_main_query())
                return $output;

            $trp = TRP_Translate_Press::get_trp_instance();
            $trp_settings = $trp->get_component( 'settings' );
            $settings = $trp_settings->get_settings();

            if ($TRP_LANGUAGE !== $settings['default-language']) {
                if (is_singular() && !empty($post->ID)) {
                    $output = "<trp-post-container data-trp-post-id='" . $post->ID . "'>" . $output . "</trp-post-container>";//changed " to ' to not break cases when the filter is applied inside an html attribute (title for example)
                }
            }
        }

        return $output;
    }

    /**
     * Disable TRP when the Oxygen Builder is being loaded
     */
    add_filter( 'trp_stop_translating_page', 'trp_oxygen_disable_trp_in_builder', 10, 2);
    function trp_oxygen_disable_trp_in_builder($bool, $output){

        if( defined( 'SHOW_CT_BUILDER' ) )
            return true;

        return $bool;
    }

    /**
     * Hide Floating Language Switcher when the Oxygen is shown
     */
    add_filter( 'trp_floating_ls_html', 'trp_page_builder_compatibility_disable_language_switcher' );
    function trp_page_builder_compatibility_disable_language_switcher( $html ){

        if( isset( $_GET['ct_builder'] ) && $_GET['ct_builder'] == 'true' )
            return '';

        return $html;

    }

}

if( function_exists( 'ct_is_show_builder' ) || defined( 'FL_BUILDER_VERSION' ) ){

    /**
     * Used to redirect Oxygen Builder front-end to the default language.
     * Hooked before TRP_Language_Switcher::redirect_to_correct_language() so we don't redirect twice
     */
    add_action( 'template_redirect', 'trp_page_builder_compatibility_redirect_to_default_language', 10 );
    function trp_page_builder_compatibility_redirect_to_default_language(){

        if( !is_admin() && ( ( isset( $_GET['ct_builder'] ) && $_GET['ct_builder'] == 'true' ) || isset( $_GET['fl_builder'] ) ) ){

            $trp           = TRP_Translate_Press::get_trp_instance();
            $url_converter = $trp->get_component('url_converter');
            $settings      = ( new TRP_Settings() )->get_settings();

            $current_url  = $url_converter->cur_page_url();
            $current_lang = $url_converter->get_lang_from_url_string( $current_url );

            if( ( $current_lang == null && $settings['add-subdirectory-to-default-language'] == 'yes' ) || ( $current_lang != null && $current_lang != $settings['default-language'] ) ){
                $link_to_redirect = $url_converter->get_url_for_language( $settings['default-language'], null, '' );

                if( $link_to_redirect != $current_url ){
                    wp_redirect( $link_to_redirect, 301 );
                    exit;
                }

            }

        }

    }

    /**
     * Disable automatic language redirect when the Oxygen or Beaver Builders are showing
     */
    add_filter( 'trp_ald_enqueue_redirecting_script', 'trp_ald_dont_redirect_inside_page_builders');
    function trp_ald_dont_redirect_inside_page_builders( $enqueue_redirecting_script ){
        if( ( isset( $_GET['ct_builder'] ) && $_GET['ct_builder'] == 'true' ) || isset( $_GET['fl_builder'] ) ){
            return false;
        }
        return $enqueue_redirecting_script;
    }
}

/**
 * Compatibility with Brizy editor
 */
add_filter( 'trp_enable_dynamic_translation', 'trp_brizy_disable_dynamic_translation' );
function trp_brizy_disable_dynamic_translation( $enable ){
    if ( isset( $_REQUEST['brizy-edit-iframe'] ) ){
        return false;
    }
    return $enable;
}

/**
 * Compatibility with Brizy PRO menu, the language switcher inside the menu does not work fully yet
 * Compatibility with Brizy assets loading with language slug in url (the 'process' function)
 */
if( defined( 'BRIZY_PRO_VERSION' ) || defined( 'BRIZY_VERSION' ) ){
    add_filter( 'trp_home_url', 'trp_brizy_menu_pro_compatibility', 10, 5 );
    function trp_brizy_menu_pro_compatibility( $new_url, $abs_home, $TRP_LANGUAGE, $path, $url ){
        if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);//set a limit if it is supported to improve performance
        }
        else{
            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }

        $list_of_functions = array( 'restoreSiteUrl', 'process' );
        if( !empty( $callstack_functions ) ) {
            foreach ( $callstack_functions as $callstack_function ) {
                if ( in_array( $callstack_function['function'], $list_of_functions ) ) {
                    return $url;
                }
            }
        }

        return $new_url;
    }
}


/**
 * Compatibility with Bricks Builder
 * Hide Floating Language Switcher (both v1 and v2) inside the Bricks builder editor.
 */
function trp_bricks_suppress_floater_in_builder( $html ) {
    if ( function_exists( 'bricks_is_builder_main' ) && bricks_is_builder_main() ) {
        return '';
    }
    return $html;
}
add_filter( 'trp_floater_ls_html_v2', 'trp_bricks_suppress_floater_in_builder' );
add_filter( 'trp_floating_ls_html',   'trp_bricks_suppress_floater_in_builder' );


/**
 * Compatibility with woocommerce-pdf-vouchers plugin, removed language from download link of the vouchers
 */
if( defined( 'WOO_VOU_PLUGIN_VERSION' ) ){
    add_filter( 'trp_home_url', 'trp_woocommerce_pdf_vouchers_download_file_compatibility', 10, 5 );
    function trp_woocommerce_pdf_vouchers_download_file_compatibility( $new_url, $abs_home, $TRP_LANGUAGE, $path, $url ){
        if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);//set a limit if it is supported to improve performance
        }
        else{
            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }

        $list_of_functions = array( 'get_item_download_url' );
        if( !empty( $callstack_functions ) ) {
            foreach ( $callstack_functions as $callstack_function ) {
                if ( in_array( $callstack_function['function'], $list_of_functions ) ) {
                    return $url;
                }
            }
        }

        return $new_url;
    }
}


/**
 * Compatibility with Advanced WooCommerce Search 1/2
 * Returns post ids where searched key matches translated version of post.
 */

add_filter( 'aws_search_results_products_ids', 'trp_aws_search_results_products_ids', 10, 2 );
function trp_aws_search_results_products_ids(  $posts_ids, $s ){
    // an empty search would match every translated string, so keep the Advanced Woo Search results like in the default language
    if ( '' === trim( (string) $s ) ){
        return $posts_ids;
    }
    global $TRP_LANGUAGE;
    $trp = TRP_Translate_Press::get_trp_instance();
    $trp_settings = $trp->get_component( 'settings' );
    $settings = $trp_settings->get_settings();

    if ( $TRP_LANGUAGE !== $settings['default-language'] ) {
        $trp_search = $trp->get_component( 'search' );
        $search_result_ids = $trp_search->get_post_ids_containing_search_term($s, null);

        if (!empty ( $search_result_ids) ) {
            return $search_result_ids;
        }
    }

    return $posts_ids;
}

/**
 * Compatibility with Advanced WooCommerce Search 2/2
 * Solves issue with caching results in a different language
 */
add_filter( 'wpml_current_language', 'trp_aws_current_language' );
function trp_aws_current_language( $lang ) {
    if ( class_exists( 'AWS_Main' ) ) {
        global $TRP_LANGUAGE;
        $lang = $TRP_LANGUAGE;
    }
    return $lang;
}
/**
 * Compatibility with Ivory Search plugin.
 *
 * Ivory Search registers pre_get_posts at priority 9999999 to inject `_is_includes`
 * (title/content/excerpt/custom_field/SKU/etc.) and a posts_search/posts_join pair
 * that builds the WHERE/JOIN clauses for those fields. TranslatePress's own
 * trp_search_filter runs at priority 99999999 and overwrites `post__in` with
 * translated content matches, which excludes everything Ivory's SQL would have
 * matched (e.g. SKU matches in WooCommerce).
 *
 * To keep both sets of matches, after TP has finished we run a sub WP_Query with
 * Ivory's `_is_includes` config plus `_is_settings[search_engine] = index`. The
 * 'index' flag lets Ivory's posts_search/posts_join filters process a non-main
 * query (see IS_Public::posts_search line check on $is_index_search). The IDs
 * returned by that sub-query are merged into the current `post__in`.
 */
add_action( 'pre_get_posts', 'trp_ivory_search_merge_results', 99999999 + 1 );
function trp_ivory_search_merge_results( $query ) {
    if ( ! class_exists( 'IS_Public' ) ) {
        return;
    }
    if ( ! empty( $GLOBALS['trp_ivory_compat_running'] ) ) {
        return;
    }
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
        return;
    }
    if ( empty( $query->query_vars['_is_includes'] ) ) {
        return;
    }

    global $TRP_LANGUAGE;
    $trp          = TRP_Translate_Press::get_trp_instance();
    $trp_settings = $trp->get_component( 'settings' );
    $settings     = $trp_settings->get_settings();
    if ( $TRP_LANGUAGE === $settings['default-language'] ) {
        return;
    }

    $search_term = $query->get( 's' );
    if ( $search_term === '' && ! empty( $_GET['s'] ) ) {
        $search_term = sanitize_text_field( wp_unslash( $_GET['s'] ) );
    }
    if ( $search_term === '' ) {
        return;
    }

    $is_settings = (array) $query->get( '_is_settings' );
    $is_settings['search_engine'] = 'index';

    $sub_args = array(
        's'              => $search_term,
        'fields'         => 'ids',
        'posts_per_page' => 200,
        'no_found_rows'  => true,
        '_is_includes'   => $query->get( '_is_includes' ),
        '_is_settings'   => $is_settings,
    );
    $is_excludes = $query->get( '_is_excludes' );
    if ( ! empty( $is_excludes ) ) {
        $sub_args['_is_excludes'] = $is_excludes;
    }
    $post_type = $query->get( 'post_type' );
    if ( ! empty( $post_type ) ) {
        $sub_args['post_type'] = $post_type;
    }
    $post_status = $query->get( 'post_status' );
    if ( ! empty( $post_status ) ) {
        $sub_args['post_status'] = $post_status;
    }

    $GLOBALS['trp_ivory_compat_running'] = true;
    $sub_query = new WP_Query( $sub_args );
    $ivory_ids = $sub_query->posts;
    unset( $GLOBALS['trp_ivory_compat_running'] );

    if ( empty( $ivory_ids ) ) {
        return;
    }

    $current_post_in = (array) $query->get( 'post__in' );
    $current_post_in = array_filter( $current_post_in, 'is_numeric' );

    $merged = array_values( array_unique( array_merge(
        array_map( 'absint', $current_post_in ),
        array_map( 'absint', $ivory_ids )
    ) ) );

    if ( ! empty( $merged ) ) {
        $query->set( 'post__in', $merged );
    }
}

/**
 * Compatibility with thrive Arhitect plugin which does a "nice" little trick with remove_all_filters( 'template_include' ); so we need to stop that or else it will not load our translation editor
 */
add_filter('tcb_allow_landing_page_edit', 'trp_thrive_arhitect_compatibility');
add_filter('tcb_is_editor_page', 'trp_thrive_arhitect_compatibility');//this is for Thrive theme
function trp_thrive_arhitect_compatibility($bool)    {
    if (isset($_REQUEST['trp-edit-translation']))
        $bool = false;

    return $bool;
}
// do not redirect the URL's that are used inside Thrive Architect Editor
add_filter( 'trp_allow_language_redirect', 'trp_thrive_no_redirect_in_editor', 10, 3 );
function trp_thrive_no_redirect_in_editor( $allow_redirect, $needed_language, $current_page_url ){
	if ( strpos($current_page_url, 'tve=true&tcbf')!== false ){
		return false;
	}
	return $allow_redirect;
};
// skip the URL's that are used inside Thrive Architect Editor as they are stripped of parameters in certain cases and the editor isn't working.
add_filter('trp_skip_url_for_language', 'trp_thrive_skip_language_in_editor', 10, 2);
function trp_thrive_skip_language_in_editor($skip, $url){
	if ( strpos($url, 'tve=true&tcbf') !== false ){
		return true;
	}
	return $skip;
}

/**
 * Compatibility with the RECON gateway for woocommerce. We must not send the "trp-form-language" hidden field in the post request to the gateway
 */
if( class_exists('WC_Gateway_RECON') ) {
    add_filter('trp_form_inputs', 'trp_recon_gateway_compatibility', 10, 4);
    function trp_recon_gateway_compatibility($input, $trp_language, $slug, $row)
    {
        if (isset($row->attr['name']) && $row->attr['name'] === 'checkout') {
            $input = '';
        }
        return $input;
    }
}

/*
 * Add compatibility for tribe events that crash the JS for the input not having an error field assigned to it.
 * It expects a div with a class="error" like so: <div class="tribe-common-b3 tribe-tickets__form-field-description tribe-common-a11y-hidden error">Your first and last names are required</div>
 */
if (class_exists('Tribe__Tickets__Main')){
    add_filter( 'trp_form_inputs', 'trp_tribe_tickets_form_compatibility', 10, 4 );
}
function trp_tribe_tickets_form_compatibility( $input, $trp_language, $slug, $row ) {
    if ( isset( $row->attr['class'] ) ) {
        $classes = explode( ' ', $row->attr['class'] );
        foreach ( $classes as $class ) {
            if ( strpos( $class, 'tribe-tickets' ) !== false ) {
                $input = '';
                break;
            }
        }
    }
    return $input;
}

/**
 * Compatibility with Classified Listing plugin Search in secondary language
 */
// do not return inline autocomplete because when clicking the results, the input is filled with original title instead of translated
add_filter( 'rtcl_inline_search_autocomplete_args', 'trp_rtcl_autocomplete_search_results', 10, 2 );
function trp_rtcl_autocomplete_search_results( $args, $request ){
    global $TRP_LANGUAGE;
    $trp = TRP_Translate_Press::get_trp_instance();
    $trp_settings = $trp->get_component( 'settings' );
    $settings = $trp_settings->get_settings();

    if ( $TRP_LANGUAGE !== $settings['default-language'] ) {
        $args['post__in'] = array('1');
        return $args;
    }

    return $args;
}

// Otherwise trp-post-container is not added
add_action( 'wp_body_open', 'trp_overrule_main_query_condition', 10, 2 );
function trp_overrule_main_query_condition(){
    if ( class_exists('Rtcl') ) {
        add_filter( 'trp_wrap_with_post_id_overrule', '__return_false' );
    }
}

/**
 * Otherwise trp-post-container is stripped
 *
 * Applied this solution permanently. It's problematic with Elementor and WooCommerce too.
 */
add_filter( 'wp_kses_allowed_html', 'trp_prevent_kses_from_stripping_trp_post_container', 10, 2 );
function trp_prevent_kses_from_stripping_trp_post_container( $allowedposttags, $context  ) {
    if ( $context === 'post' ){
        $allowedposttags['trp-post-container'] = array( 'data-trp-post-id' => true );
    }
    return $allowedposttags;
}

// Filter search results to show secondary language results
add_action('rtcl_listing_query', 'trp_rtcl_search_results', 10, 2);
function trp_rtcl_search_results ($q, $t){
    if ( empty( $q->get('s')) ){
        return;
    }
    global $TRP_LANGUAGE;
    $trp = TRP_Translate_Press::get_trp_instance();
    $trp_settings = $trp->get_component( 'settings' );
    $settings = $trp_settings->get_settings();

    if ( $TRP_LANGUAGE !== $settings['default-language'] ) {
        $trp_search = $trp->get_component( 'search' );
        $search_result_ids = $trp_search->get_post_ids_containing_search_term($q->get('s'), null);
        $q->set('s', '');
        if ( empty($search_result_ids)){
            $search_result_ids = array(0);
        }
        $q->set('post__in', $search_result_ids );
    }
}

/*
 * Compatibility with AddToAny Share Buttons
 *
 * Skip detection by translate-dom-changes of the url change when hitting the share button
 *
 */
add_filter( 'trp_skip_selectors_from_dynamic_translation', 'trp_add_to_any_skip_dynamic_translation' );
function trp_add_to_any_skip_dynamic_translation( $skip_selectors ){
    if( function_exists( 'A2A_SHARE_SAVE_init' ) ) {
        $add_skip_selectors = array( '.addtoany_list' );
        return array_merge( $skip_selectors, $add_skip_selectors );
    }
    return $skip_selectors;
}

/*
 * Compatibility with Uncode theme menu on mobile
 *
 * Skip detection by translate-dom-changes of the url change when hitting the menu
 *
 */
add_filter( 'trp_skip_selectors_from_dynamic_translation', 'trp_uncode_skip_dynamic_translation' );
function trp_uncode_skip_dynamic_translation( $skip_selectors ){
    if( function_exists( 'uncode_setup' ) ) {
        $add_skip_selectors = array( '.menu-horizontal .menu-smart' );
        return array_merge( $skip_selectors, $add_skip_selectors );
    }
    return $skip_selectors;
}

/*
 * Compatibility with PDF Embedder Premium Secure
 *
 * Skips link processing if ?pdfemb-serveurl is in the url
 *
 */
// first we need to disable adding the language to home_url() because the plugin is using it to construct a url.
add_filter( 'trp_home_url', 'trp_skip_home_url_processing_for_pdfemb_server_url', 10, 5 );
function trp_skip_home_url_processing_for_pdfemb_server_url(  $new_url, $abs_home, $TRP_LANGUAGE, $path, $url ){
	if( class_exists( 'core_pdf_embedder' ) ){
		$callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

		$list_of_functions =  array( 'modify_pdfurl' ) ;
		if( !empty( $callstack_functions ) ) {
			foreach ( $callstack_functions as $callstack_function ) {
				if ( in_array( $callstack_function['function'], $list_of_functions ) ) {
					$new_url = $url;
					break;
				}
			}
		}
	}
	return $new_url;
}
// and after that we need to make sure we're not adding the language when we process the url's in the page.
add_filter( 'trp_skip_url_for_language', 'trp_skip_link_processing_for_pdfemb_server_url', 10, 2 );
function trp_skip_link_processing_for_pdfemb_server_url( $skip, $url ){
	if( strpos($url, '?pdfemb-serveurl') !== false ) {
		$skip = true;
	}
	return $skip;
}

/**
 * Add compatibility with blockquote tweet button in elementor
 * if the quote has one parameter it will be automatically translated, if not then you need to use conditional language shortcodes
 */
add_filter( 'wp_parse_str', 'trp_elementor_blockquote_translate_tweet_button' );
function trp_elementor_blockquote_translate_tweet_button( $array ){
    if( array_key_exists( 'text', $array ) ){

        if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);//set a limit if it is supported to improve performance
        }
        else{
            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }

        if( !empty( $callstack_functions ) ) {
            foreach ($callstack_functions as $callstack_function) {
                if ( $callstack_function['function'] === 'render_content' ) {
                    //enable conditional language shortcode
                    $array['text'] = do_shortcode( $array['text'] );

                    //try to eliminate the author from the text before we try to translate it
                    $tweet_link_text = $array['text'];
                    $tweet_link_text = explode( ' — ', $tweet_link_text );
                    if( count( $tweet_link_text ) > 1 ){
                        $quote_author = array_pop( $tweet_link_text );
                    }
                    $tweet_link_text = implode( ' — ', $tweet_link_text );

                    //try and translate the text
                    $trp = TRP_Translate_Press::get_trp_instance();
                    $translation_render = $trp->get_component( 'translation_render' );
                    $array['text'] = $translation_render->translate_page($tweet_link_text);

                    //add author if it was eliminated
                    if(!empty($quote_author) )
                        $array['text'] = $array['text'] . ' — ' .  $translation_render->translate_page($quote_author);

                    break;
                }
            }
        }

    }
    return $array;
}

/**
 * Add compatibility with blockquote tweet button in elementor pro that had the link broken, it doubled the language in the url
 */
if( function_exists('elementor_pro_load_plugin') ) {
    add_filter('trp_home_url', 'trp_elementor_blockquote_tweet_button_url', 10, 5);
    function trp_elementor_blockquote_tweet_button_url($new_url, $abs_home, $TRP_LANGUAGE, $path, $url)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);//set a limit if it is supported to improve performance
        } else {
            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }

        $list_of_functions = array('render');
        if (!empty($callstack_functions)) {
            foreach ($callstack_functions as $callstack_function) {
                if (in_array($callstack_function['function'], $list_of_functions) && isset($callstack_function['class']) && $callstack_function['class'] === 'ElementorPro\Modules\Blockquote\Widgets\Blockquote') {
                    return $url;
                }
            }
        }

        return $new_url;
    }
}

/**
 * Add compatibility with Elementor so we allow conditional shortcodes in post excerpt
 * this allows twitter button to have a translated text
 */
if( defined('ELEMENTOR_VERSION') ) {
    add_filter('the_post', 'trp_elementor_translate_tweet_button_excerpt');
    function trp_elementor_translate_tweet_button_excerpt($post){
        if (!empty($post->post_excerpt)) {
            $post->post_excerpt = do_shortcode($post->post_excerpt);
        }

        return $post;
    }
}

/**
 * Add current-menu-item css class to menu items in WP Nav Menu
 *
 * Don't add them to language switcher items.
 * Always adds them to secondary languages.
 * Add them to default language if Use subdirectory is set to Yes
 */
add_filter('wp_nav_menu_objects', 'trp_add_current_menu_item_css_class');
function trp_add_current_menu_item_css_class( $items ){
    global $TRP_LANGUAGE;
    $trp = TRP_Translate_Press::get_trp_instance();
    $url_converter = $trp->get_component('url_converter');
    $trp_settings = $trp->get_component( 'settings' );
    $settings = $trp_settings->get_settings();

    add_filter('pre_get_posts', 'trp_the_event_calendar_set_query_to_true', 2, 1);

    foreach( $items as $item ){
        if ( !( $TRP_LANGUAGE === $settings['default-language'] && isset( $settings['add-subdirectory-to-default-language']) && $settings['add-subdirectory-to-default-language'] !== 'yes'  ) &&
            !in_array( 'current-menu-item', $item->classes ) && !in_array( 'menu-item-object-language_switcher', $item->classes ) && ( !empty($item->url) && $item->url !== '#')
        ){
            $url_for_language = $url_converter->get_url_for_language( $TRP_LANGUAGE, $item->url );
            $url_for_language = strpos( $url_for_language, '#' ) ? substr( $url_for_language, 0, strpos( $url_for_language, '#' ) ) : $url_for_language;
            $cur_page_url = set_url_scheme( untrailingslashit( $url_converter->cur_page_url() ) );

            if ( untrailingslashit( $url_for_language ) == untrailingslashit( $cur_page_url ) ){
                $item->classes[] = 'current-menu-item';
            }
        }
        if(!in_array('current-language-menu-item', $item->classes) && in_array('menu-item-object-language_switcher', $item->classes)){
            $current_language = $url_converter->get_lang_from_url_string($item->url);

            if($current_language == null){
                $current_language = $settings['default-language'];
            }

            if($current_language == $TRP_LANGUAGE){
                $item->classes[] = 'current-language-menu-item';
            }
        }
    }

    remove_filter('pre_get_posts', 'trp_the_event_calendar_set_query_to_true', 2);
    return $items;
}

/**
 * Function needed to set tribe_suppress_query_filters to false in query in order to avoid errors with The Event Calendar
 *
 * @param $query
 * @return mixed
 */
function trp_the_event_calendar_set_query_to_true($query){
    $query->set('tribe_suppress_query_filters', false);

    return $query;
}

/**
 * Compatibility with xstore theme ajax search on other languages than english and when automatic translation was on
 * a class from the search form got translated
 */
if( function_exists('initial_ETC') ) {
    add_filter('trp_skip_gettext_processing', 'trp_exclude_xstore_search_class', 999, 4);
    function trp_exclude_xstore_search_class($return, $translation, $text, $domain){

        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);//set a limit if it is supported to improve performance
        } else {
            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }

        $list_of_functions = array();
        if (!empty($callstack_functions)) {
            foreach ($callstack_functions as $callstack_function) {
                $list_of_functions[] = $callstack_function['function'];
            }
        }

        if (in_array('esc_attr_e', $list_of_functions) && in_array('header_content_callback', $list_of_functions))
            return true;

        return $return;
    }
}

/**
 * Add compatibility with Business Directory Plugin that requires to update permalinks when we are on other languages or else it will throw a 404 error
 */
if( defined( 'WPBDP_PLUGIN_FILE' ) ) {
    add_filter('trp_prevent_permalink_update_on_other_languages', 'trp_prevent_permalink_update_on_other_languages');
    function trp_prevent_permalink_update_on_other_languages($bool){
        return false;
    }
}


/**
 * Exclude some problematic gettext strings from being translated
 */
add_filter('trp_skip_gettext_processing', 'trp_exclude_problematic_gettext_strings', 999, 4 );
function trp_exclude_problematic_gettext_strings ( $return, $translation, $text, $domain ){
    $exclude_strings = array(
        // some examples on how to use: (domain is optional)
        //array( 'string' => 'Some Text', 'domain' => 'some-domain' )
        //array( 'string' => 'Some Other Text' )
        array( 'string' => 'Ștefan Vodă' )//this is translated by Google Translate into german as "Fan Vod" and the quotes create problems
    );

    foreach( $exclude_strings as $string_details ){
        if( $text === $string_details['string'] ){
            if( empty( $string_details['domain'] ) )
                return true;
            else if( $domain === $string_details['domain'] )
                return true;
        }
    }

    return $return;
}


/**
 * Compatibility with WooCommerce API
 *
 * Particularly with Paypal and myPOS checkout
 *
 * When the IPN request comes do not translate anything outputted.
 * MyPOS expects "OK" string which does not have to be translated as regular string.
 * Paypal had trpst in the details sent.
 */
add_action( 'woocommerce_api_request', 'trp_woo_wc_api_handle_api_request', 1 );
function trp_woo_wc_api_handle_api_request( ){
    add_filter( 'trp_skip_gettext_processing', '__return_true' );
    add_filter( 'trp_stop_translating_page', '__return_true' );
}

/**
 * Compatibility with WooCommerce Min/Max Quantities that wrongly add data-quantity attribute two times on the link and our parser breaks this. The update of the parser to 1.9.1 should render this redundant
 */
if( class_exists('WC_Min_Max_Quantities') ) {
    add_filter('woocommerce_loop_add_to_cart_link', 'trp_check_duplicate_quantity_attribute_on_link', 99, 2);
    function trp_check_duplicate_quantity_attribute_on_link($html, $product){
        $occurrences = substr_count($html, " data-quantity=");
        if ($occurrences > 1) {
            $html = preg_replace('/(data-quantity="\d+"(?!.*data-quantity="\d+"))/', '', $html, 1);
        }

        return $html;
    }
}

/**
 * Add here compatibility with search plugins
 */
add_filter('trp_force_search', 'trp_force_search' );
function trp_force_search( $bool ){
    //force search in xstore theme ajax search
    if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'etheme_ajax_search' )
        $bool = true;

    //compatibility with WooCommerce Product Search plugin
    if( class_exists('WooCommerce_Product_Search_Service') ) {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'product_search')
            $bool = true;
    }

    return $bool;
}

/**
 * Compatibility with WooCommerce Product Search plugin
 * The only way I found is to hijack the cache in the get_post_ids_for_request() function from WooCommerce_Product_Search_Service class. It comes with a loss of performance
 */
if( class_exists('WooCommerce_Product_Search_Service') ) {

    add_filter('woocommerce_product_search_request_search_query', 'trp_woocommerce_product_search_compatibility');

    function trp_woocommerce_product_search_compatibility($search_query)
    {
        // an empty search would match every translated string, so let WooCommerce Product Search handle it like in the default language
        if ( '' === trim( (string) $search_query ) ) {
            return $search_query;
        }
        global $TRP_LANGUAGE;
        $trp = TRP_Translate_Press::get_trp_instance();
        $trp_settings = $trp->get_component('settings');
        $settings = $trp_settings->get_settings();

        if ($TRP_LANGUAGE !== $settings['default-language']) {
            $title = isset($_REQUEST[WooCommerce_Product_Search_Service::TITLE]) ? intval($_REQUEST[WooCommerce_Product_Search_Service::TITLE]) > 0 : WooCommerce_Product_Search_Service::DEFAULT_TITLE;
            $excerpt = isset($_REQUEST[WooCommerce_Product_Search_Service::EXCERPT]) ? intval($_REQUEST[WooCommerce_Product_Search_Service::EXCERPT]) > 0 : WooCommerce_Product_Search_Service::DEFAULT_EXCERPT;
            $content = isset($_REQUEST[WooCommerce_Product_Search_Service::CONTENT]) ? intval($_REQUEST[WooCommerce_Product_Search_Service::CONTENT]) > 0 : WooCommerce_Product_Search_Service::DEFAULT_CONTENT;
            $tags = isset($_REQUEST[WooCommerce_Product_Search_Service::TAGS]) ? intval($_REQUEST[WooCommerce_Product_Search_Service::TAGS]) > 0 : WooCommerce_Product_Search_Service::DEFAULT_TAGS;
            $sku = isset($_REQUEST[WooCommerce_Product_Search_Service::SKU]) ? intval($_REQUEST[WooCommerce_Product_Search_Service::SKU]) > 0 : WooCommerce_Product_Search_Service::DEFAULT_SKU;
            $categories = isset($_REQUEST[WooCommerce_Product_Search_Service::CATEGORIES]) ? intval($_REQUEST[WooCommerce_Product_Search_Service::CATEGORIES]) > 0 : WooCommerce_Product_Search_Service::DEFAULT_CATEGORIES;
            $attributes = isset($_REQUEST[WooCommerce_Product_Search_Service::ATTRIBUTES]) ? intval($_REQUEST[WooCommerce_Product_Search_Service::ATTRIBUTES]) > 0 : WooCommerce_Product_Search_Service::DEFAULT_ATTRIBUTES;
            $variations = isset($_REQUEST[WooCommerce_Product_Search_Service::VARIATIONS]) ? intval($_REQUEST[WooCommerce_Product_Search_Service::VARIATIONS]) > 0 : WooCommerce_Product_Search_Service::DEFAULT_VARIATIONS;

            $min_price = isset($_REQUEST[WooCommerce_Product_Search_Service::MIN_PRICE]) ? WooCommerce_Product_Search_Service::to_float($_REQUEST[WooCommerce_Product_Search_Service::MIN_PRICE]) : null;//phpcs:ignore
            $max_price = isset($_REQUEST[WooCommerce_Product_Search_Service::MAX_PRICE]) ? WooCommerce_Product_Search_Service::to_float($_REQUEST[WooCommerce_Product_Search_Service::MAX_PRICE]) : null;//phpcs:ignore
            if ($min_price !== null && $min_price <= 0) {
                $min_price = null;
            }
            if ($max_price !== null && $max_price <= 0) {
                $max_price = null;
            }
            if ($min_price !== null && $max_price !== null && $max_price < $min_price) {
                $max_price = null;
            }

            $on_sale = isset($_REQUEST[WooCommerce_Product_Search_Service::ON_SALE]) ? intval($_REQUEST[WooCommerce_Product_Search_Service::ON_SALE]) > 0 : WooCommerce_Product_Search_Service::DEFAULT_ON_SALE;

            //this is how they get the key in the method get_cache_key()
            $cache_key = md5(implode('-', array(
                'title' => $title,
                'excerpt' => $excerpt,
                'content' => $content,
                'tags' => $tags,
                'sku' => $sku,
                'categories' => $categories,
                'attributes' => $attributes,
                'variations' => $variations,

                'search_query' => $search_query,
                'min_price' => $min_price,
                'max_price' => $max_price,
                'on_sale' => $on_sale
            )));

            $trp_search = $trp->get_component('search');
            $include = $trp_search->get_post_ids_containing_search_term($search_query, null);
            wp_cache_set($cache_key, $include, WooCommerce_Product_Search_Service::POST_CACHE_GROUP, WooCommerce_Product_Search_Service::CACHE_LIFETIME);
        }

        return $search_query;
    }
}



/**
 * Strip tags manually from a problematic string coming from the My Listing theme
 */
 add_action('init', 'trp_mylisting_hook_exclude_string' );
 function trp_mylisting_hook_exclude_string(){
     if( class_exists( 'MyListing\\App' ) ){
         add_filter('gettext_with_context', 'trp_mylisting_exclude_string', 101, 4 );
     }
 }

 function trp_mylisting_exclude_string( $translation, $text, $context, $domain ){

     if( $domain == 'my-listing' && $text == 'my-listings' )
         $translation = TRP_Translation_Manager::strip_gettext_tags( $translation );

     return $translation;

 }


/**
 * Compatibility with Google Site Kit plugin
 *
 * Problem was that Site Kit dashboard kept disconnecting, thinking the url must have changed.
 *
 * To replicate, set TP option "Add language to subdirectory" Yes and use Complianz plugin, wizard step 2,
 * to perform re-scan of cookies. This triggered the disconnect.
 */
add_filter('googlesitekit_canonical_home_url', 'trp_googlesitekit_compatibility_home_url' );
function trp_googlesitekit_compatibility_home_url( $url ) {
    $trp = TRP_Translate_Press::get_trp_instance();
    $url_converter = $trp->get_component('url_converter');
    return $url_converter->get_abs_home();
}


/**
 * Compatibility with WPEngine hosting
 *
 * Detect and handle query length limiting feature of WPEngine. Without this check, the query returns no results as if
 * there were no translations found. This results in duplicate row inserting and unnecessary automatic translation
 * usage.
 */
add_filter('trp_get_existing_translations', 'trp_wpengine_query_limit_check', 10, 3 );
function trp_wpengine_query_limit_check($dictionary, $prepared_query, $strings_array){
    if ( function_exists('is_wpe') && ( !defined ('WPE_GOVERNOR') || ( defined ('WPE_GOVERNOR') && WPE_GOVERNOR != false ) ) && strlen($prepared_query) >= 16000 ){
        $trp = TRP_Translate_Press::get_trp_instance();
        $trp_query = $trp->get_component( 'query' );
        $trp_query->maybe_record_automatic_translation_error(array( 'details' => esc_html__("Detected long query limitation on WPEngine hosting. Some large pages may appear untranslated. You can remove limitation by adding the following to your site’s wp-config.php: define( 'WPE_GOVERNOR', false ); ", 'translatepress-multilingual')), true );
        return false;
    }else{
        return $dictionary;
    }
}


/**
 * Compatibility with Dokan plugin
 *
 * Dates are run through gettext and the this breaks further functions because of wrappings
 */
if ( class_exists('WeDevs_Dokan')) {
    add_filter( 'trp_skip_gettext_processing', 'trp_exclude_dokan_date_strings', 20, 4 );
}
function trp_exclude_dokan_date_strings($return, $translation, $text, $domain) {
    $skip_text = array('Y/m/d g:i:s A', 'Y/m/d');
    if ($domain == 'dokan' && in_array( $text, $skip_text) ){
        return true;
    }
    return $return;
}

function trp_add_language_to_pms_wppb_restriction_redirect_url( $redirect_url ){

    global $TRP_LANGUAGE;

    $trp = TRP_Translate_Press::get_trp_instance();
    $url_converter = $trp->get_component('url_converter');

    return $url_converter->get_url_for_language( $TRP_LANGUAGE, $redirect_url, '' );

}

if( defined( 'PMS_VERSION' ) )
    add_filter( 'pms_restricted_post_redirect_url', 'trp_add_language_to_pms_wppb_restriction_redirect_url' );

if( function_exists( 'wppb_plugin_init' ) )
    add_filter( 'wppb_restricted_post_redirect_url', 'trp_add_language_to_pms_wppb_restriction_redirect_url' );

/**
 * Compatibility with wp-Typography
 * The $filters array is set to empty, so it does not affect the strings anymore in the function trp_remove_filters_wp_typography.
 * Then it is reset with a higher priority by calling the function process() inside the trp_add_filters_wp_typography function.
 */

if(class_exists('WP_Typography')) {
    add_action('plugins_loaded', 'trp_wp_typography');
}

function trp_wp_typography(){
    global $TRP_LANGUAGE;
    $trp = TRP_Translate_Press::get_trp_instance();
    $trp_settings = $trp->get_component('settings');
    $settings = $trp_settings->get_settings();

    if ($TRP_LANGUAGE !== $settings['default-language']) {
        add_filter( 'typo_content_filters', 'trp_remove_filters_wp_typography' );
        add_filter( 'trp_translated_html', 'trp_add_filters_wp_typography', 100000, 1 );
        add_filter('run_wptexturize', '__return_null', 11);
    }
}

function trp_remove_filters_wp_typography($filters){
    $filters = [];

    return $filters;
}



function trp_add_filters_wp_typography($final_html){
    $wpt= WP_Typography::get_instance();

    add_filter('run_wptexturize', '__return_false', 11);

    $final_html = $wpt->process($final_html, $is_title = false, $force_feed = false, null );

    return $final_html;

}


/*
 * Compatibility with All In One SEO Pack
 */

if(function_exists('aioseo')){

    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 15);//set a limit if it is supported to improve performance
    } else {
        $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
    }

    if (!empty($callstack_functions)) {
        foreach ( $callstack_functions as $callstack_function ) {
            if ( isset($callstack_function["object"]->{"callbacks"}) ) {
                foreach ($callstack_function["object"]->{"callbacks"}[10] as $key=>$value){
                    if(strpos($key, 'actionScheduler')){
                        if(array_key_exists('breadcrumbs_archiveFormat',  $callstack_function["object"]->{"callbacks"}[10][ $key ]["function"][0]->{"options"}->{"localized"}  )) {
                            add_action( 'trp_before_running_hooks', 'trp_AIOSEO_remove_gettext_hooks', 10, 1 );
                        }
                    }
                }
            }
        }
    }
}

function trp_AIOSEO_remove_gettext_hooks($trp_loader){
    $trp                = TRP_Translate_Press::get_trp_instance();
    $translation_render = $trp->get_component( 'translation_render' );
    $trp_loader->remove_hook( 'the_title', 'wrap_with_post_id', $translation_render );
}


/**
 * Compatibility with Elementor/Divi/WPBakery when "Use a subdirectory for the default language" is set to Yes
 * Making sure the page edited with Elementor/Divi/WPBakery appears in the default language instead of the first language from the Language list
 */
add_filter( 'trp_needed_language', 'trp_page_builders_compatibility_with_subdirectory_for_default_language', 10, 4 );
function trp_page_builders_compatibility_with_subdirectory_for_default_language( $needed_language, $lang_from_url, $settings, $trp) {
    if ( trp_is_page_builder_editor_request() ) {
        $needed_language = $settings['default-language'];
    }
    return $needed_language;
}

/**
 * Whether the current request is a page builder editor session (the builder shell or its preview iframe).
 *
 * The preview iframe of a page builder is a regular front-end request, so front-end output such as the
 * Automatic User Language Detection popup would otherwise show up on top of the editing canvas.
 *
 * @return bool
 */
function trp_is_page_builder_editor_request() {
    $is_builder_request = ( ( isset( $_GET['action'] ) && $_GET['action'] === 'elementor' ) || isset( $_GET['elementor-preview'] ) ) //Elementor
        || trp_divi_is_builder_request() //Divi 4 & 5. Divi 4 appended PageSpeed=off to the builder URL, Divi 5 no longer does, so don't rely on it
        || trp_is_breakdance_builder_request() //Breakdance builder shell (?breakdance=builder) and canvas iframe (breakdance_iframe=true)
        || ( ( isset( $_GET['vc_action'] ) && $_GET['vc_action'] === 'vc_inline' ) || ( isset( $_GET['vc_editable'] ) && $_GET['vc_editable'] === 'true' ) ); //WPBakery

    return apply_filters( 'trp_is_page_builder_editor_request', $is_builder_request );
}

/**
 * Whether the current request is a Divi Builder session.
 * et_fb=1  - Visual Builder (front-end)
 * et_bfb=1 - Backend Builder iframe
 */
function trp_divi_is_builder_request() {
    return ( isset( $_GET['et_fb'] ) && $_GET['et_fb'] === '1' ) || ( isset( $_GET['et_bfb'] ) && $_GET['et_bfb'] === '1' ); /* phpcs:ignore */
}

/**
 * Redirect Divi Builder sessions opened on a secondary language URL to the default language.
 *
 * The Divi 5 Visual Builder loads its content over the REST API. On a secondary language URL
 * the REST root advertised to the builder is language-prefixed and TranslatePress processes
 * the page and the REST responses, so the builder fails to load the post content.
 *
 * Hooked before TRP_Language_Switcher::redirect_to_correct_language() so we don't redirect twice.
 * The default language with "Use subdirectory for default language" enabled is left as is;
 * trp_needed_language resolves it, and on the default language TranslatePress leaves both the
 * page and the REST responses untouched.
 */
add_action( 'template_redirect', 'trp_divi_builder_redirect_to_default_language', 10 );
function trp_divi_builder_redirect_to_default_language() {
    if ( is_admin() || ! trp_divi_is_builder_request() || ! defined( 'ET_BUILDER_VERSION' ) ) {
        return;
    }

    $trp           = TRP_Translate_Press::get_trp_instance();
    $url_converter = $trp->get_component( 'url_converter' );
    $settings      = ( new TRP_Settings() )->get_settings();

    if ( ! $url_converter || empty( $settings['default-language'] ) ) {
        return;
    }

    $current_url  = $url_converter->cur_page_url();
    $current_lang = $url_converter->get_lang_from_url_string( $current_url );

    if ( $current_lang != null && $current_lang != $settings['default-language'] ) {
        $link_to_redirect = $url_converter->get_url_for_language( $settings['default-language'], null, '' );

        if ( $link_to_redirect != $current_url ) {
            wp_redirect( $link_to_redirect, 301 );
            exit;
        }
    }
}

/**
 * Disable the automatic language detection redirect script inside the Divi Builder.
 * Otherwise it would redirect the builder page back to the visitor's preferred language,
 * bouncing against the redirect to the default language above.
 */
add_filter( 'trp_ald_enqueue_redirecting_script', 'trp_divi_builder_disable_ald_redirect' );
function trp_divi_builder_disable_ald_redirect( $enqueue_redirecting_script ) {
    if ( trp_divi_is_builder_request() ) {
        return false;
    }
    return $enqueue_redirecting_script;
}

/**
 * Hide the floating language switcher inside the Divi Builder.
 */
add_filter( 'trp_floating_ls_html', 'trp_divi_builder_disable_language_switcher' );
add_filter( 'trp_floater_ls_html_v2', 'trp_divi_builder_disable_language_switcher' );
function trp_divi_builder_disable_language_switcher( $html ) {
    if ( trp_divi_is_builder_request() ) {
        return '';
    }
    return $html;
}


/**
 * Compatibility with Elementor when editing the static front page while "Use a subdirectory for the
 * default language" is enabled.
 *
 * Elementor builds the preview URL from get_permalink(). For the static front page that permalink is
 * the bare home URL (no ?page_id=), and TP does not add the language subdirectory on admin requests,
 * so the preview URL ends up as e.g. https://example.com/?elementor-preview=ID . On the front end that
 * bare-home request no longer resolves to the front page (it now lives under /<default-language>/), so
 * it returns a 404 and the Elementor editor hangs on the loading screen.
 *
 * Regular pages are not affected because their preview URL carries ?page_id=ID, which resolves fine.
 *
 * We fix it at the source by adding the default-language subdirectory to the front-page preview URL
 * (e.g. https://example.com/<default-language>/?elementor-preview=ID), which resolves correctly (200)
 * and lets the editor finish loading.
 */
add_filter( 'elementor/document/urls/preview', 'trp_elementor_front_page_preview_url_subdirectory', 10, 2 );
function trp_elementor_front_page_preview_url_subdirectory( $url, $document ) {

    $trp      = TRP_Translate_Press::get_trp_instance();
    $settings = $trp->get_component( 'settings' )->get_settings();

    // Only when the default language uses a subdirectory.
    if ( ( isset( $settings['add-subdirectory-to-default-language'] ) ? $settings['add-subdirectory-to-default-language'] : 'no' ) !== 'yes' ) {
        return $url;
    }

    // Only for the configured static front page.
    if ( get_option( 'show_on_front' ) !== 'page' ) {
        return $url;
    }

    $front_page_id = (int) get_option( 'page_on_front' );
    if ( $front_page_id === 0 || ! is_object( $document ) || (int) $document->get_main_id() !== $front_page_id ) {
        return $url;
    }

    $url_converter = $trp->get_component( 'url_converter' );

    // Pass an empty processed marker so the URL is not suffixed with #TRPLINKPROCESSED.
    return $url_converter->get_url_for_language( $settings['default-language'], $url, '' );
}


/**
 * Compatibility with Give WP plugin.
 *
 * When automatic translation is active and we are on secondary language, clicking the Donate button will not redirect you to the confirmation page.
 * This happens because "Give WP" expects an admin ajax request to return "success" but TP translates it in another language.
 */
add_filter( 'trp_stop_translating_page', 'trp_give_wp_compatibility', 10, 2 );
function trp_give_wp_compatibility( $bool, $output ){
    if ( isset( $_REQUEST['give_ajax'] ) && $_REQUEST['give_ajax'] == 'true' ) {
        return true;
    }
    return $bool;
}

/*
 * Divi is filtering the locale which is in turn accessed on every gettext call by TranslatePress. Together these two things slow down the site to 20+ seconds
 * The fix is to remove the Divi hook and replace it with another one that caches the result, so it's fast.
 * Ideally this is a fix Divi should do, however, it negatively impacts TP, so we're doing it for them.
 */
add_filter('locale', 'trp_remove_divi_locale_filter', 999999);
function trp_remove_divi_locale_filter($lang){
    remove_filter( 'locale', 'et_divi_maybe_change_frontend_locale' );
    return $lang;
}

/**
 * This function, checks if the Divi plugin is not installed first.
 * If it's not installed, it returns the original locale.
 * If it is installed, it will then access the theme options and check if the 'divi_disable_translations' is found in the cache.
 * If the value is not found in the cache, it retrieves it from the database using get_option('et_divi').
 * If the value retrieved from the database is also false, it sets it to 'not_set' in the cache.
 * Then, it checks if the value of theme_options is 'not_set'. If it is, it returns the input locale without making any changes.
 * If it is, it returns 'en_US', otherwise it returns the original locale.
**/
function trp_et_divi_maybe_change_frontend_locale( $locale ) {
    if ( !defined( 'ET_CORE_PATH' ) ) {
        return $locale;
    }
    $cache_key = 'et_divi_option';
    $theme_options = wp_cache_get( $cache_key );
    $option_name = 'divi_disable_translations';
    if (false === $theme_options) {
        $theme_options = get_option( 'et_divi' );
        if ( false === $theme_options ) {
            $theme_options = 'not_set';
        }
        wp_cache_set( $cache_key, $theme_options );
    }

    if ( 'not_set' === $theme_options ) {
        return $locale;
    }

    if ( !isset( $theme_options[ $option_name ] ) ) {
        return $locale;
    }

    if ( 'on' === $theme_options[ $option_name ] ) {
        return 'en_US';
    }

    return $locale;
}
add_filter( 'locale', 'trp_et_divi_maybe_change_frontend_locale' );

/*
 * Register old advanced settings if they are checked
 */
add_action('admin_init', 'trp_register_old_advanced_settings');

function trp_register_old_advanced_settings( $bool )
{

    $option = get_option('trp_advanced_settings', true);
    if (isset($option['fix_broken_html']) && $option['fix_broken_html'] === 'yes') {

        add_filter('trp_register_advanced_settings', 'trp_register_fix_broken_html', 50);
    }
}

add_filter('trp_ald_popup_options_array', 'trp_keep_no_popup_setting_for_redirect_directly', 10, 1);

function trp_keep_no_popup_setting_for_redirect_directly($array_popup_options){
    $option_ald = get_option('trp_ald_settings', true);
    if (isset($option_ald['popup_option']) && $option_ald['popup_option'] !== 'no_popup' && version_compare(TRP_IN_ALD_PLUGIN_VERSION, '1.1', '>=') ) {
        unset($array_popup_options['no_popup']);
    }
    return $array_popup_options;
}

/**
 * Prevent legacy sortable-languages hooks from running.
 *
 * The settings-page sortable initialization lives in trp-back-end-script.js.
 * Keep removing the old addon hook so outdated companion plugins do not enqueue
 * their legacy sortable script on top of the core implementation.
 */
add_action( 'trp_before_running_hooks', 'trpc_prevent_sortable_script_from_loading' );
function trpc_prevent_sortable_script_from_loading( $trp_loader ){
    if ( version_compare(TRP_PLUGIN_VERSION, '2.5.4', '>=' ) ) {
        $trp_loader->remove_hook( 'admin_enqueue_scripts', 'enqueue_sortable_language_script' );
        $trp_loader->remove_hook( 'admin_enqueue_scripts', 'enqueue_sortable_language_script_303' );
    }
}

add_filter('trp_advanced_tab_add_element', 'trp_compatibility_for_adl_127_version', 20);
function trp_compatibility_for_adl_127_version( $settings ){
    foreach ( $settings as $key => $setting ) {
        if ( $setting['name'] === 'automatic_user_language_detection' && !isset( $setting['id'] ) ) {
            $settings[$key]['id'] = 'ald_settings';
        }
    }
    return $settings;
}

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', TRP_PLUGIN_DIR . 'index.php', true );
    }
} );


add_filter( 'rank_math/analytics/get_translated_objects', 'trp_rank_math_get_translated_items', 10, 1 );
function trp_rank_math_get_translated_items( $post_id ) {
    if ( ! class_exists( 'TRP_Translate_Press' ) || !function_exists('trp_translate')) {
        return $post_id;
    }

    $trp                = TRP_Translate_Press::get_trp_instance();
    $url_converter      = $trp->get_component( 'url_converter' );
    $settings_component = $trp->get_component( 'settings' );
    $trp_settings       = $settings_component->get_settings();

    // Needed because adding language slug in urls is not performed by default in admin area.
    add_filter( 'trp_add_language_to_home_url_check_for_admin', '__return_false' );

    $permalink = get_permalink( $post_id );

    $translated_items = [];

    $languages = $trp_settings['publish-languages'];
    foreach ( $languages as $language ) {

        $url = esc_url( $url_converter->get_url_for_language( $language, $permalink, '' ) );

        /**
         * Google API and get_permalink sends URL Encoded strings so we need
         * to urldecode in order to get them to match with whats saved in DB.
         */
        $parse_url = wp_parse_url( urldecode( $url ) );
        if ( ! $parse_url ) {
            continue;
        }

        if ( empty( $parse_url['path'] ) ) {
            continue;
        }

        $title = get_the_title( $post_id );

        // Get translated title, if possible.
        if( $language != $trp_settings['default-language'] ){
            $title = trp_translate( $title, $language, false );
        }

        // Push translated URL into array.
        array_push(
            $translated_items,
            [
                'url'   => $parse_url['path'],
                'title' => $title,
            ]
        );
    }

    // Revert to default functionality.
    remove_filter( 'trp_add_language_to_home_url_check_for_admin', '__return_false' );

    return $translated_items;
}

/**
 *The manually translated slug being overwritten by automatic translation was caused by a conflict with the events calendar. In the function
 * include_slug_for_machine_translation(add-ons-advanced/seo-pack/includes/class-slug-manager.php, line 431), line 499 we have the line
 * $translated_base_slug = $this->get_translated_rewrite_base_slug( $post_type_string, $language_code, false );.Because the events calendar
 * adds a slug called ‘tribe_events’ this was registered as $post_type_string, and this slug does not exist amongst the existing post-type base
 * slugs saved in DB, so it was returning false and the slug was added to the translatable_information array so it was sent to automatic translation.
 * The client translated the slug for Portuguese in English so in line 502, $original_base_slug = $this->get_rewrite_base_slug( $post_type_object,
 * $post_type_string );, the translated slug was returned and passed through automatic translation which returned the slug in portugese and
 * overwrriten the human translated slug by the client.
 *
 * As a solution, if the events calendar is active, we use a filter of post type base slugs that should not be passed through automatic translation.
 */
if (class_exists("Tribe__Events__Adjacent_Events")) {
    add_filter('trp_filter_post_type_base_slugs_from_automatic_translation', 'trp_stop_automatic_translation_for_certain_post_type_base_slugs', 10, 2);
}

function trp_stop_automatic_translation_for_certain_post_type_base_slugs( $bool, $post_type_base_slug_to_avoid ) {

    $array_of_post_type_base_slugs_that_should_not_be_passed_through_automatic_translation = array("tribe_events");

    if (in_array($post_type_base_slug_to_avoid, $array_of_post_type_base_slugs_that_should_not_be_passed_through_automatic_translation)){
        $bool = false;
    }
    
    return $bool;
}

/**
 * Compatibility with Duplicate Page plugin
 */

if (class_exists("duplicate_page")) {

    add_action('save_post', 'trp_add_hook_for_delete', 10, 1);
}

function trp_add_hook_for_delete( $post_id )
{
    global $trp_post_id_for_deleting_duplicate_posts_slugs_from_db;

    if (did_action('admin_action_dt_duplicate_post_as_draft')) {
        $trp_post_id_for_deleting_duplicate_posts_slugs_from_db = $post_id;
        add_action('shutdown', 'trp_delete_slug_translation_from_duplicated_pages' );
    }
}
function trp_delete_slug_translation_from_duplicated_pages() {
    global $trp_post_id_for_deleting_duplicate_posts_slugs_from_db;
    global $wpdb;

    $sql = $wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE post_id = %d AND (meta_key LIKE %s OR meta_key LIKE %s);", $trp_post_id_for_deleting_duplicate_posts_slugs_from_db, '%'. $wpdb->esc_like('trp_automatically_translated_slug') .'%', '%'. $wpdb->esc_like('trp_translated_slug') .'%');

    $wpdb->query( $sql );
    
    unset( $GLOBALS['trp_post_id_for_deleting_duplicate_posts_slugs_from_db'] );

}
/**
 * Exclude Query Monitor gettext strings from being processed
 */
if ( class_exists( 'QueryMonitor' ) ) {
    add_filter( 'trp_skip_gettext_processing', 'trp_exclude_query_monitor_strings', 10, 4 );
    add_filter( 'trp_no_translate_selectors', 'trp_exclude_query_monitor_selector' );
    add_filter( 'trp_skip_selectors_from_dynamic_translation', 'trp_exclude_query_monitor_selector' );
}
function trp_exclude_query_monitor_strings( $bool, $translation, $text, $domain ){
    if ( trim( $domain ) === 'query-monitor' ) return true;

    return $bool;
}

/**
 * Exclude Query Monitor selector from being translated
 */
function trp_exclude_query_monitor_selector( $skip_selectors ) {
    $skip_selectors[] = '#query-monitor-main';
    return $skip_selectors;
}


/**
 * Compatibility with Complianz plugin blocking trp_data script
 */
// Whitelisting inline script for Complianz
add_filter ( 'cmplz_service_category', 'trp_cmplz_whitelist_script', 10 , 3 );
function trp_cmplz_whitelist_script( $category, $total_match, $found ){
    if ( $found && false !== strpos( $total_match, 'trp-dynamic-translator-js-extra' ) ) {
        $category = 'functional'; // add cmplz-script for Marketing and cmplz-stats for Statistics
    }

    return $category;
}

/**
 * Compatibility with Fluent Forms
 * Do not Translate Fluent Forms ajax submit calls for uploaded media
 */
add_filter('trp_stop_translating_page', 'trp_do_not_translate_fluent_form_submit', 1000000, 2);
function trp_do_not_translate_fluent_form_submit($translate, $output){
    if ( isset( $_POST['action'] )  && $_POST['action'] == 'fluentform_file_upload'){
        return true;
    }

    return $translate;
}

/**
 * Do not Translate WooCommerce Bookings
 */
add_filter('trp_stop_translating_page', 'trp_do_not_translate_woo_bookings_cost_calculator', 10, 2);
function trp_do_not_translate_woo_bookings_cost_calculator($translate, $output){
    if ( isset( $_POST['action'] )  && $_POST['action'] == 'wc_bookings_calculate_costs'){
        return true;
    }
    return $translate;
}

add_action('init', 'trp_woo_bookings_gettext_filter');
function trp_woo_bookings_gettext_filter(){
    if ( isset( $_POST['action'] )  && $_POST['action'] == 'wc_bookings_calculate_costs'){
        add_filter('gettext', 'trp_woo_bookings_exclude_gettext_strings', 1000, 3 );
    }
}

function trp_woo_bookings_exclude_gettext_strings($translation, $text, $domain){
    if ( isset( $_POST['action'] )  && $_POST['action'] == 'wc_bookings_calculate_costs' ){
        return TRP_Translation_Manager::strip_gettext_tags( $translation );
    }
    return $translation;
}

/**
 * Add support for the content feed and excerpt feed so they get translated.
 * They can be manually translated from String Translation -> Regular.
 * For most contents, they will work with content the client already sees in the front-end.
 */
add_filter( 'the_excerpt_rss', 'trp_translate_the_excerpt_rss', 10, 1);
function trp_translate_the_excerpt_rss( $output ){
    $trp = TRP_Translate_Press::get_trp_instance();
    $translation_render = $trp->get_component( 'translation_render' );
    return $translation_render->translate_page($output);
};

add_filter( 'the_content_feed', 'trp_translate_the_content_feed', 10, 2);
function trp_translate_the_content_feed( $content, $feed_type ){
    $trp = TRP_Translate_Press::get_trp_instance();
    $translation_render = $trp->get_component( 'translation_render' );
    return $translation_render->translate_page($content);
};


// Add a filter to stop translating pages for PDF files associated with WP Job Board Pro
add_filter('trp_stop_translating_page', 'trp_block_wpjb_pro_pdf_translation', 10, 2);

/**
 * Blocks the translation of PDF files generated by WP Job Board Pro.
 *
 * This function checks if the current request is for a WP Job Board Pro AJAX call
 * related to PDF files. If so, it prevents these PDF files from being translated by TranslatePress.
 *
 * @param bool $bool The initial state determining if the page should be translated.
 * @param mixed $output The output or content potentially subject to translation.
 * @return bool True if the translation should be blocked for the current request, otherwise returns the original state.
 */
function trp_block_wpjb_pro_pdf_translation( $bool, $output ) {
    // Check if the current request is an AJAX call related to WP Job Board Pro PDF files
    if ( !empty( $_REQUEST['wjbp-ajax'] ) ) {
        // Block the translation of the PDF file by returning true
        return true;
    }
    // Return the initial state if the condition is not met
    return $bool;
}

// Compatibility with Brikk theme
if ( function_exists( 'brikk_utilities_load_textdomain' ) ) {
    add_filter( 'trp_skip_form_action', 'trp_exclude_brikk_theme_form_action', 10, 2 );
}
function trp_exclude_brikk_theme_form_action( $skip_this_action, $form_action ) {
    if ( $form_action == "1" || $form_action == "0" ) return true;

    return $skip_this_action;
}

/**
 * Compatibility with PWA plugin https://wordpress.org/plugins/pwa/
 */
add_filter('trp_stop_translating_page', 'trp_do_not_translate_service_worker_pages', 10, 2);
function trp_do_not_translate_service_worker_pages($translate, $output){

    if( isset( $_SERVER['REQUEST_URI'] ) )
        $request_uri = esc_url_raw( $_SERVER['REQUEST_URI'] );
    else
        $request_uri = '';

    if( strpos( $request_uri, 'wp.serviceworker' ) !== false ){
        return true;
    }

    return $translate;
}

if ( class_exists( 'WooCommerce' ) ){
    add_action('plugins_loaded', 'trp_check_if_woo_language_po_file_exists');
}

function trp_check_if_woo_language_po_file_exists() {

    $trp                 = TRP_Translate_Press::get_trp_instance();
    $trp_settings        = $trp->get_component( 'settings' );
    $settings            = $trp_settings->get_settings();

    if ( $settings['default-language'] != 'en_US' && !file_exists(WP_LANG_DIR . "/plugins/woocommerce-{$settings['default-language']}.po")){
        trp_download_woo_po_file_for_default_language( $settings['default-language'] );
    }
}

function trp_download_woo_po_file_for_default_language( $default_language ){

    $path_for_po_file_in_the_requested_language = trp_get_translation_woo_po_files_url( $default_language );

    if ( $path_for_po_file_in_the_requested_language ) {
        $language_pack_url = $path_for_po_file_in_the_requested_language;

        $save_to = WP_LANG_DIR . "/plugins";
        file_put_contents( "woocommerce-{$default_language}.zip", file_get_contents( $path_for_po_file_in_the_requested_language) );

        $zip = new ZipArchive;
        $res = $zip->open( "woocommerce-{$default_language}.zip");

        if ( $res === TRUE ) {
            $zip->extractTo( $save_to, array( "woocommerce-{$default_language}.po" ) );
            $zip->close();

            return true;
        }
    }
    return false;

}

function trp_get_translation_woo_po_files_url( $default_language ) {
    $translations_api_url = "https://api.wordpress.org/translations/plugins/1.0/?slug=woocommerce";
    $response = wp_remote_get($translations_api_url, array('timeout' => 300));

    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (empty($data['translations'])) {
        return false;
    }

    foreach ($data['translations'] as $translation) {
        if ($translation['language'] === $default_language) {
            return $translation['package'];
        }
    }

    return false;
}

/**
 * Hooked to trp_get_url_for_language, used in case the pro version of TP was not updated or legacy SEO Pack is in use
 *
 * @param $new_url
 * @param $url
 * @param $language
 * @return mixed|string|null
 */
function trp_get_url_for_language_backwards_compatibility( $new_url, $url, $language ){
    global $trp_current_url_term_slug, $trp_current_url_taxonomy;

    $trp = TRP_Translate_Press::get_trp_instance();
    $trp_settings = $trp->get_component( 'settings' );
    $settings = $trp_settings->get_settings();

    $upgrade = new TRP_Upgrade( $settings );

    if ( $upgrade->is_seo_pack_minimum_version_met() && ( !isset( $settings['trp_advanced_settings']['load_legacy_seo_pack'] ) || $settings['trp_advanced_settings']['load_legacy_seo_pack'] === 'no' ) ) return $new_url; // Abort -- New system can be used, process URL via get_slug_translated_url_for_language

    $url_converter = $trp->get_component( 'url_converter' );

    $debug = false;

    global $TRP_LANGUAGE;

    $trp_language_copy = $TRP_LANGUAGE;

    $url_obj          = trp_cache_get('url_obj_' . hash('md4', $url), 'trp');
    $abs_home_url_obj = trp_cache_get('url_obj_' . hash('md4',  $url_converter->get_abs_home() ), 'trp');

    $possible_post_id = trp_cache_get( 'possible_post_id_'. hash('md4', $url ), 'trp' );

    if ( $possible_post_id ){
        $post_id = $possible_post_id;
        trp_bulk_debug($debug, array('url' => $url, 'found post id' => $post_id, 'for language' => $TRP_LANGUAGE));
    }

    else {
        $post_id = url_to_postid( $url );
        wp_cache_set( 'possible_post_id_' . hash('md4', $url ), $post_id, 'trp' );
        if ( $post_id ) { trp_bulk_debug($debug, array('url' => $url, 'found post id' => $post_id, 'for default language' => $TRP_LANGUAGE)); }

        if ( $post_id == 0 ) {
            /* try again but this time switch to default language home_url
            *  becasue url_to_postid() uses the global language setting to accurately retrieve a post ID
            */
            $TRP_LANGUAGE = $settings['default-language'];
            add_filter('trp_keep_permalinks_unchanged', '__return_true' );

            /* In order to accurately find the post ID the passed URL to url_to_postid() needs to be accurate
            * if the option add subdir to default language is on we need to add that to the URL
            */

            $possible_url = $url;
            if (isset ($settings['add-subdirectory-to-default-language']) && $settings['add-subdirectory-to-default-language'] === 'yes' && $url_converter->get_lang_from_url_string( $url ) == null ){
                $possible_url = $url_converter->add_language_to_home_url($url, $url_obj->getPath(), $url_obj->getScheme(), get_current_blog_id() );
            }
            $post_id = url_to_postid( $possible_url );
            wp_cache_set( 'possible_post_id_' . hash('md4', $possible_url ), $post_id, 'trp' );
            if($post_id){ trp_bulk_debug($debug, array('url' => $url, 'found post id' => $post_id, 'for default language' => $TRP_LANGUAGE)); }

            remove_filter('trp_keep_permalinks_unchanged', '__return_true' );
            $TRP_LANGUAGE = $trp_language_copy;
        }
    }

    $TRP_LANGUAGE = $url_converter->get_lang_from_url_string( $url );

    if ($TRP_LANGUAGE == null){
        $TRP_LANGUAGE = $settings['default-language'];
    }

    $new_url_has_been_determined = false;

    if( $post_id ){

        /*
         * We need to find if the current URL (either passed as parameter or found via cur_page_url)
         * has extra arguments compared to its permalink.
         * We need the permalink based on the language IN THE URL, not the one passed to this function,
         * as that represents the language to be translated into.
         *
         * WE ARE NOT USING \TranslatePress\Uri
         * due to URL's having extra path elements after the permalink slug. Using the class would strip those end points.
         *
         */

        $processed_permalink = get_permalink($post_id);

        $url_to_replace = ( $url_obj->isSchemeless() ) ?
            ( $url_obj->hasAnchor() || $url_obj->hasQueryParam() ) ?
                trailingslashit( home_url() ) . ltrim($url, '/')
                :trailingslashit(trailingslashit( home_url() ) . ltrim($url, '/') )
            :$url;

        $arguments = str_replace(untrailingslashit($processed_permalink), '', $url_to_replace );

        // if nothing was replaced, something was wrong, just use the normal permalink without any arguments.
        if( $arguments == $url_to_replace ) {
            $arguments = '';
            //try again, this time trying to correct url_to_replace to include subdirectory
            if (isset ($settings['add-subdirectory-to-default-language']) && $settings['add-subdirectory-to-default-language'] === 'yes' && $url_converter->get_lang_from_url_string( $url_to_replace ) == null ) {
                $possible_url_to_replace = $url_converter->add_language_to_home_url( $url, ( empty( $url_obj->getQuery() ) ) ? (( empty( $url_obj->getFragment() ) ) ? $url_obj->getPath() : $url_obj->getPath() . '#' . $url_obj->getFragment()) : (( empty( $url_obj->getFragment() ) ) ? rtrim( $url_obj->getPath(), '/' ) . '/?' . $url_obj->getQuery() : rtrim( $url_obj->getPath(), '/' ) . '/?' . $url_obj->getQuery() . '#' . $url_obj->getFragment() ), $url_obj->getScheme(), get_current_blog_id() );
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

    if( isset( $trp_current_url_term_slug ) && isset($trp_current_url_taxonomy) && $new_url_has_been_determined === false ){
        // check here if it is a term link
        $current_term_link = get_term_link( $trp_current_url_term_slug, $trp_current_url_taxonomy);

        if (!is_wp_error($current_term_link)){
            $TRP_LANGUAGE = $language;
            $check_term_link = get_term_link($trp_current_url_term_slug, $trp_current_url_taxonomy);

            if ( !is_wp_error($check_term_link) && strpos(urldecode( $url ), $current_term_link) === 0 ) {
                $new_url = str_replace( $current_term_link, $check_term_link, urldecode( $url ) );
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
    $posts_archive_link = rtrim($url_converter->maybe_add_pagination_to_blog_page($posts_archive_link), '/');

    if( is_home() && $url_stripped === $posts_archive_link && ( isset( $_SERVER['REQUEST_URI'] ) && strpos( esc_url_raw( $_SERVER['REQUEST_URI'] ), 'sitemap') === false && strpos( esc_url_raw( $_SERVER['REQUEST_URI'] ), '.xml') === false ) &&
        $new_url_has_been_determined === false)
    {//for some reason in yoast sitemap is_home() is true ..so we need to check if we are not in the sitemap itself
        $TRP_LANGUAGE = $language;
        if ( empty($url_obj->getQuery()) ) {
            $new_url = $url_converter->maybe_add_pagination_to_blog_page( trailingslashit(get_post_type_archive_link( 'post' ) ));
        } else {
            $new_url = rtrim( $url_converter->maybe_add_pagination_to_blog_page( get_post_type_archive_link( 'post' ) ), '/') . '/?' . $url_obj->getQuery();
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
        if ($url_converter->get_lang_from_url_string($url) === null) {
            // these are the custom url. They don't have language
            $abs_home_considered_path = trim(str_replace( $abs_home_url_obj->getPath() !== null ? $abs_home_url_obj->getPath() : '', '', $url_obj->getPath()), '/');
            $new_url_obj->setPath(trailingslashit(trailingslashit(strval($abs_home_url_obj->getPath())) . trailingslashit($url_converter->get_url_slug($language)) . $abs_home_considered_path));
            $new_url = $new_url_obj->getUri();

            trp_bulk_debug($debug, array('url' => $url, 'new url' => $new_url, 'lang' => $language, 'url type' => 'custom url without language parameter'));
        } else {
            // these have language param in them and we need to replace them with the new language
            $abs_home_considered_path = trim(str_replace($abs_home_url_obj->getPath() !== null ? $abs_home_url_obj->getPath() : '', '', $url_obj->getPath()), '/');
            $no_lang_orig_path = explode('/', $abs_home_considered_path);
            unset($no_lang_orig_path[0]);
            $no_lang_orig_path = implode('/', $no_lang_orig_path);

            if (!$url_converter->get_url_slug($language)) {
                $url_lang_slug = '';
            } else {
                $url_lang_slug = trailingslashit($url_converter->get_url_slug($language));
            }

            $new_url_obj->setPath(trailingslashit(trailingslashit($abs_home_url_obj->getPath() !== null ? $abs_home_url_obj->getPath() : '') . $url_lang_slug . ltrim($no_lang_orig_path, '/')));
            $new_url = $new_url_obj->getUri();

            trp_bulk_debug($debug, array('url' => $url, 'new url' => $new_url, 'lang' => $language, 'url type' => 'custom url with language', 'abs home path' => $abs_home_url_obj->getPath()));

        }
    }
    $TRP_LANGUAGE = $trp_language_copy;

    return $new_url;
}
add_filter('trp_get_url_for_language', 'trp_get_url_for_language_backwards_compatibility', 10, 3 );

/**
 * Redirects to the translated version of the `redirect_url` based on the current language.
 *
 * @param string $redirect_url Original URL.
 */
function trp_compatibility_profile_builder_redirect( $redirect_url ) {
    $trp_instance = TRP_Translate_Press::get_trp_instance();

    global $TRP_LANGUAGE;

    $url_converter  = $trp_instance->get_component( 'url_converter' );

    return $url_converter->get_url_for_language( $TRP_LANGUAGE, $redirect_url, '' );
}

add_filter( 'wppb_register_redirect', 'trp_compatibility_profile_builder_redirect' );
add_filter( 'wppb_edit_profile_redirect', 'trp_compatibility_profile_builder_redirect' );

/**
 * Compatibility with WPBakery in edit mode. It adds parameters to the URL that get processed by get_url_for_language and should not.
 */
add_filter('trp_curpageurl', 'trp_wpbackery_compatibility_remove_params_curpageurl');
add_filter('trp_get_url_for_language', 'trp_wpbackery_compatibility_remove_params', 10, 3);

function trp_wpbackery_compatibility_remove_params($url, $language, $args) {
    return trp_wpbackery_compatibility_strip_params($url);
}

function trp_wpbackery_compatibility_remove_params_curpageurl($url) {
    return trp_wpbackery_compatibility_strip_params($url);
}

function trp_wpbackery_compatibility_strip_params($url) {
    // Only proceed if WPBakery is active
    if (!class_exists('Vc_Manager')) {
        return $url;
    }

    $params_to_remove = array('vc_editable', 'vc_post_id', '_vcnonce');

    // Use TranslatePress URI class for proper URL handling
    $uri = new \TranslatePress\Uri($url);
    
    // Return original URL if no query parameters exist
    if (!$uri->hasQueryParam()) {
        return $url;
    }

    $query_string = $uri->getQuery();
    
    // Check if any WPBakery params are present before processing
    $has_wpbakery_params = false;
    foreach ($params_to_remove as $param) {
        if (strpos($query_string, $param . '=') !== false) {
            $has_wpbakery_params = true;
            break;
        }
    }

    // Return original URL if no WPBakery params are found
    if (!$has_wpbakery_params) {
        return $url;
    }

    // Parse and clean query parameters
    $query = array();
    parse_str($query_string, $query);
    
    foreach ($params_to_remove as $param) {
        unset($query[$param]);
    }

    // Set the cleaned query back to the URI
    $new_query = http_build_query($query);
    $uri->setQuery($new_query);

    return $uri->getUri();
}

/**
 * Add compatibility fix for LiteSpeed Cache and it's ESI feature
 * https://docs.litespeedtech.com/lscache/lscwp/cache/#esi-tab
 * @param $url
 * @return mixed|string
 */
function trp_use_lightspeedcache_esi_referer($url){
    if( strpos($url, 'lsesi=') > 0 && !empty($_SERVER['ESI_REFERER']) ){
        return esc_url_raw($_SERVER['ESI_REFERER']);
    }

    return $url;
}
if (class_exists('LiteSpeed\ESI')) {
    add_filter('trp_curpageurl', 'trp_use_lightspeedcache_esi_referer');
}

/**
 * Filter the canonical URL generated by SEOPress to use the translated version
 * when viewing a non-default TranslatePress language.
 *
 * This function hooks into the `seopress_titles_canonical` filter and replaces
 * the `<link rel="canonical">` tag's href attribute with the language-specific
 * URL generated by TranslatePress.
 *
 * Safeguards:
 * - Leaves canonical untouched if:
 *   - No canonical tag is found
 *   - Current language is the default language
 *   - Canonical points to a different host
 *   - TranslatePress URL converter is unavailable
 *   - The current request is for the homepage (empty path)
 *
 * @since 2.10.4
 * @hooked seopress_titles_canonical - 10
 *
 * @param string $link_rel_canonical_html The original HTML for the canonical link tag as output by SEOPress.
 * @return string The modified HTML for the canonical link tag, or the original if no changes are needed.
 */
function trp_filter_seopress_titles_canonical( $link_rel_canonical_html ) {
    if ( false === stripos( $link_rel_canonical_html, 'rel="canonical"' ) )
        return $link_rel_canonical_html;

    global $TRP_LANGUAGE;

    $trp           = TRP_Translate_Press::get_trp_instance();
    $settings      = $trp->get_component( 'settings' )->get_settings();
    $url_converter = $trp->get_component( 'url_converter' );

    if ( $settings['default-language'] === $TRP_LANGUAGE )
        return $link_rel_canonical_html;

    if ( !method_exists( $url_converter, 'get_url_for_language' ) )
        return $link_rel_canonical_html;

    // Extract the current canonical URL from the link tag
    if ( !preg_match( '#<link\s+rel=["\']canonical["\']\s+href=["\']([^"\']+)#i', $link_rel_canonical_html, $m ) )
        return $link_rel_canonical_html;

    $canonical_url = html_entity_decode( $m[1], ENT_QUOTES ); // decode in case attrs were escaped

    // Convert the canonical URL to the current language (handles Multiple Domains subdomains)
    $new_url = $url_converter->get_url_for_language( $TRP_LANGUAGE, $canonical_url, '' );

    if ( empty( $new_url ) || $new_url === $canonical_url )
        return $link_rel_canonical_html;

    // Replace the href in the original <link rel="canonical"> tag (keep everything else as-is).
    $replacement_url_attr = esc_url( $new_url );
    $link_rel_canonical_html = preg_replace(
        '#(<link\s+rel=["\']canonical["\']\s+href=["\'])([^"\']+)(["\'])#i',
        '$1' . $replacement_url_attr . '$3',
        $link_rel_canonical_html,
        1
    );

    return $link_rel_canonical_html;
}
add_filter( 'seopress_titles_canonical', 'trp_filter_seopress_titles_canonical' , 10 );

/**
 * Filter the og:url meta tag generated by SEOPress to use the translated URL
 * including translated slugs when viewing a non-default TranslatePress language.
 *
 * This function hooks into the `seopress_social_og_url` filter and replaces
 * the og:url content attribute with the properly translated URL.
 *
 * @since 2.10.4
 * @hooked seopress_social_og_url - 10
 *
 * @param string $og_url_html The original HTML for the og:url meta tag as output by SEOPress.
 * @return string The modified HTML for the og:url meta tag, or the original if no changes are needed.
 */
function trp_filter_seopress_social_og_url( $og_url_html ) {
    if ( false === stripos( $og_url_html, 'og:url' ) )
        return $og_url_html;

    global $TRP_LANGUAGE;

    $trp           = TRP_Translate_Press::get_trp_instance();
    $settings      = $trp->get_component( 'settings' )->get_settings();
    $url_converter = $trp->get_component( 'url_converter' );

    if ( $settings['default-language'] === $TRP_LANGUAGE )
        return $og_url_html;

    if ( !method_exists( $url_converter, 'get_url_for_language' ) )
        return $og_url_html;

    // Extract the current og:url from the meta tag
    if ( !preg_match( '#<meta\s+property=["\']og:url["\']\s+content=["\']([^"\']+)#i', $og_url_html, $m ) )
        return $og_url_html;

    $og_url = html_entity_decode( $m[1], ENT_QUOTES );

    // Use the current page URL with translated slugs instead of the URL from SEOPress
    // SEOPress uses $wp->request which contains the rewritten (untranslated) slug
    $new_url = $url_converter->cur_page_url();

    if ( empty( $new_url ) || $new_url === $og_url )
        return $og_url_html;

    // Replace the content in the original og:url meta tag
    $replacement_url = esc_url( $new_url );
    $og_url_html = preg_replace(
        '#(<meta\s+property=["\']og:url["\']\s+content=["\'])([^"\']+)(["\'])#i',
        '$1' . $replacement_url . '$3',
        $og_url_html,
        1
    );

    return $og_url_html;
}
add_filter( 'seopress_social_og_url', 'trp_filter_seopress_social_og_url', 10 );

/**
 * Remove Breakdance's template override when TranslatePress editors are active.
 *
 * Breakdance overrides the template resolution process by hooking into
 * the `template_include` filter. This interferes with TranslatePress'
 * Translation Editor and String Translation Editor. To prevent conflicts,
 * this function removes Breakdance's filter when either of those editors
 * are active.
 *
 *
 * @since 2.10.4
 *
 * @hooked plugins_loaded - 20
 *
 * @return void
 */
function trp_breakdance_compat__remove_filter() {
    $is_editor  = isset( $_GET['trp-edit-translation'] ) && 'true' === sanitize_text_field( wp_unslash( $_GET['trp-edit-translation'] ) );
    $is_strings = isset( $_GET['trp-string-translation'] ) && 'true' === sanitize_text_field( wp_unslash( $_GET['trp-string-translation'] ) );

    // Only proceed if one of the TRP editors is active
    if ( !( $is_editor || $is_strings ) )
        return;

    // Only remove if Breakdance actually hooked its template override
    if ( has_filter( 'template_include', 'Breakdance\\ActionsFilters\\template_include' ) )
        remove_filter( 'template_include', 'Breakdance\\ActionsFilters\\template_include', 1000000 );
}
add_action( 'plugins_loaded', 'trp_breakdance_compat__remove_filter', 20 );

/**
 * Prepare translated search indexing for Breakdance product title elements.
 *
 * Breakdance renders its Product Title and dynamic Post Title elements outside the
 * WordPress main loop. TranslatePress normally avoids adding post container tags in
 * that context because title filters can also run in SEO plugins and other places
 * where HTML is not accepted. Without the container, however, the translated title
 * is not associated with the product ID and cannot be found by translated search.
 *
 * Arm an override only for the title filter triggered immediately by the relevant
 * Breakdance element. The override is removed at the end of that same filter call.
 *
 * @param object|string $element The Breakdance element being rendered.
 *
 * @return void
 */
function trp_breakdance_prepare_product_title_search_indexing( $element ) {
    if ( is_object( $element ) ) {
        $element_name = get_class( $element );
    } elseif ( is_string( $element ) ) {
        $element_name = $element;
    } else {
        return;
    }

    /**
     * Filter the Breakdance elements that render the current product title.
     *
     * @param string[] $product_title_elements Breakdance element class names.
     */
    $product_title_elements = (array) apply_filters( 'trp_breakdance_product_title_elements', array(
        'EssentialElements\\PostTitle',
        'EssentialElements\\WooProductTitle',
    ) );

    if ( ! in_array( $element_name, $product_title_elements, true ) || ! function_exists( 'is_product' ) || ! is_product() ) {
        return;
    }

    add_filter( 'trp_wrap_with_post_id_overrule', 'trp_breakdance_allow_current_product_title_wrapper', PHP_INT_MAX, 3 );
    add_filter( 'the_title', 'trp_breakdance_remove_product_title_wrapper_override', PHP_INT_MAX, 2 );
}
add_action( 'breakdance_render_element_template', 'trp_breakdance_prepare_product_title_search_indexing', 10, 1 );

/**
 * Allow the current product title to receive TranslatePress post context.
 *
 * @param bool     $overrule Whether post container tags should be skipped.
 * @param string   $content  The title being filtered.
 * @param int|null $post_id  The post ID supplied by the_title.
 *
 * @return bool
 */
function trp_breakdance_allow_current_product_title_wrapper( $overrule, $content, $post_id ) {
    if ( ! empty( $post_id ) && (int) $post_id === (int) get_queried_object_id() ) {
        return false;
    }

    return $overrule;
}

/**
 * Remove the one-use Breakdance product title indexing override.
 *
 * @param string   $title   The filtered title.
 * @param int|null $post_id The post ID supplied by the_title.
 *
 * @return string
 */
function trp_breakdance_remove_product_title_wrapper_override( $title, $post_id ) {
    remove_filter( 'trp_wrap_with_post_id_overrule', 'trp_breakdance_allow_current_product_title_wrapper', PHP_INT_MAX );
    remove_filter( 'the_title', 'trp_breakdance_remove_product_title_wrapper_override', PHP_INT_MAX );

    return $title;
}

/**
 * Detect whether the current request is loading the Breakdance Builder interface.
 */
function trp_is_breakdance_builder_request() {
    $template = isset( $_GET['breakdance'] ) ? sanitize_text_field( wp_unslash( $_GET['breakdance'] ) ) : '';

    $builder_templates = array( 'builder', 'templates', 'design_library', 'regenerate-cache', 'onboarding-app' );

    if ( in_array( $template, $builder_templates, true ) ) {
        return true;
    }

    if ( ! empty( $_GET['breakdance_iframe'] ) ) {
        return true;
    }

    return false;
}

/**
 * Make the Breakdance Builder follow the user profile language instead of the
 * site default that change_locale() forces on frontend requests.
 */
function trp_breakdance_builder_respect_user_locale( $locale ) {
    // Guard against infinite recursion: both is_user_logged_in() (via
    // wp_get_current_user() -> get_user_by() -> sanitize_user() ->
    // remove_accents() on multibyte usernames) and get_user_locale() (falls
    // back to get_locale() when the user has no profile language) re-fire
    // this very 'locale' filter. Without this guard that recurses until PHP
    // exhausts memory / segfaults.
    static $in_progress = false;
    if ( $in_progress ) {
        return $locale;
    }

    // Cheapest check first: on non-Breakdance-builder requests bail before
    // touching any user functions that could re-enter this filter.
    if ( ! trp_is_breakdance_builder_request() ) {
        return $locale;
    }

    $in_progress = true;

    // is_user_logged_in() is not yet available on the early load_default_textdomain() locale call.
    if ( ! function_exists( 'is_user_logged_in' ) || ! is_user_logged_in() ) {
        $in_progress = false;
        return $locale;
    }

    $user_locale = get_user_locale();
    $in_progress = false;

    return $user_locale;
}
// Priority 100000 so this runs after TRP_Languages::change_locale() (99999).
add_filter( 'locale', 'trp_breakdance_builder_respect_user_locale', 100000 );
add_filter( 'plugin_locale', 'trp_breakdance_builder_respect_user_locale', 100000 );

/**
 * Convert a URL of this site to its default-language version, collapsing repeated language slugs.
 *
 * Breakdance builds the "Edit Global Styles" (browse mode) admin bar link with
 * home_url( $_SERVER['REQUEST_URI'] ). On a secondary-language page the request URI already carries
 * the language slug and TRP_Url_Converter::add_language_to_home_url() prepends it once more, so the
 * browseModeOpenUrl and returnUrl parameters end up as https://example.com/en/en/page/ .
 * TRP_Url_Converter::get_url_for_language() replaces a single language slug, so drop the duplicated
 * leading slugs first and let it handle the remaining one (including translated slugs from SEO Pack).
 *
 * @param string $url Absolute URL of this site.
 *
 * @return string The URL in the default language.
 */
function trp_breakdance_url_to_default_language( $url ) {
    $trp           = TRP_Translate_Press::get_trp_instance();
    $url_converter = $trp->get_component( 'url_converter' );
    $settings      = $trp->get_component( 'settings' )->get_settings();

    if ( empty( $url ) || ! $url_converter || empty( $settings['default-language'] ) ) {
        return $url;
    }

    $language_slugs = array();
    foreach ( (array) $settings['translation-languages'] as $language_code ) {
        $language_slugs[] = $url_converter->get_url_slug( $language_code, false );
    }

    $url_obj  = new \TranslatePress\Uri( $url );
    $abs_home = wp_parse_url( $url_converter->get_abs_home() );

    // Only URLs of this site carry TranslatePress language slugs.
    if ( $url_obj->getHost() && ! empty( $abs_home['host'] ) && strcasecmp( strval( $url_obj->getHost() ), $abs_home['host'] ) !== 0 ) {
        return $url;
    }

    $original_path = strval( $url_obj->getPath() );
    $home_path     = isset( $abs_home['path'] ) ? trim( strval( $abs_home['path'] ), '/' ) : '';
    $path          = trim( $original_path, '/' );

    if ( $home_path !== '' && strpos( $path, $home_path ) === 0 ) {
        $path = trim( substr( $path, strlen( $home_path ) ), '/' );
    }

    $segments = ( $path === '' ) ? array() : explode( '/', $path );

    // A URL never legitimately starts with two language slugs. Keep the last one so
    // get_url_for_language() still knows which language the rest of the path is in.
    while ( count( $segments ) >= 2 && in_array( $segments[0], $language_slugs, true ) && in_array( $segments[1], $language_slugs, true ) ) {
        array_shift( $segments );
    }

    $new_path = '/' . ltrim( $home_path . '/' . implode( '/', $segments ), '/' );
    if ( $new_path !== '/' && substr( $original_path, -1 ) === '/' ) {
        $new_path .= '/';
    }
    $url_obj->setPath( $new_path );

    return $url_converter->get_url_for_language( $settings['default-language'], $url_obj->getUri(), '' );
}

/**
 * Redirect Breakdance Builder requests opened on a secondary language URL to the default language.
 *
 * Admin Bar → Breakdance → "Edit Global Styles" on a secondary-language page links to the builder on
 * that language ( https://example.com/en/?breakdance=builder&mode=browse&browseModeOpenUrl=... ), so the
 * builder canvas renders the translated page and edits made there are saved into the original
 * content, mixing languages. The browseModeOpenUrl and returnUrl parameters also carry a doubled
 * language slug ( /en/en/page/ ), which resolves to a 404 in the canvas and when leaving the builder.
 *
 * Applies to the builder shell (?breakdance=builder) and the canvas iframe (breakdance_iframe=true).
 * Hooked before TRP_Language_Switcher::redirect_to_correct_language() so we don't redirect twice.
 */
add_action( 'template_redirect', 'trp_breakdance_builder_redirect_to_default_language', 10 );
function trp_breakdance_builder_redirect_to_default_language() {
    if ( is_admin() || ! defined( '__BREAKDANCE_VERSION' ) || ! trp_is_breakdance_builder_request() ) {
        return;
    }

    $trp           = TRP_Translate_Press::get_trp_instance();
    $url_converter = $trp->get_component( 'url_converter' );
    if ( ! $url_converter ) {
        return;
    }

    $current_url  = $url_converter->cur_page_url();
    $redirect_url = trp_breakdance_url_to_default_language( $current_url );
    $redirect     = ( $redirect_url !== $current_url );

    // Browse mode: the page opened in the canvas and the page returned to when closing the builder.
    foreach ( array( 'browseModeOpenUrl', 'returnUrl' ) as $param ) {
        if ( empty( $_GET[ $param ] ) ) {
            continue;
        }
        $original_value  = esc_url_raw( wp_unslash( $_GET[ $param ] ) ); /* phpcs:ignore WordPress.Security.NonceVerification.Recommended */
        $converted_value = trp_breakdance_url_to_default_language( $original_value );
        if ( $converted_value !== $original_value ) {
            $redirect_url = add_query_arg( $param, rawurlencode( $converted_value ), $redirect_url );
            $redirect     = true;
        }
    }

    if ( $redirect ) {
        $status = apply_filters( 'trp_redirect_status', 302, 'breakdance_builder_redirect_to_default_language' );
        wp_safe_redirect( $redirect_url, $status );
        exit;
    }
}

/**
 * Disable the automatic language detection redirect script inside the Breakdance canvas.
 * Otherwise it would send the canvas back to the visitor's preferred language,
 * bouncing against the redirect to the default language above.
 */
add_filter( 'trp_ald_enqueue_redirecting_script', 'trp_breakdance_builder_disable_ald_redirect' );
function trp_breakdance_builder_disable_ald_redirect( $enqueue_redirecting_script ) {
    if ( trp_is_breakdance_builder_request() ) {
        return false;
    }
    return $enqueue_redirecting_script;
}

/**
 * Hide the floating language switcher inside the Breakdance canvas, so the canvas can't be switched
 * to a secondary language while editing.
 */
add_filter( 'trp_floating_ls_html', 'trp_breakdance_builder_disable_language_switcher' );
add_filter( 'trp_floater_ls_html_v2', 'trp_breakdance_builder_disable_language_switcher' );
function trp_breakdance_builder_disable_language_switcher( $html ) {
    if ( trp_is_breakdance_builder_request() ) {
        return '';
    }
    return $html;
}

/**
 * Remove Woodmart Layouts' template overrides when TranslatePress editors are active.
 *
 * Woodmart's Layouts module (woodmart_layout CPT) hooks `template_include` at
 * priority 20 via subclasses of XTS\Modules\Layouts\Layout_Type. Their override
 * echoes the entire page output and returns false from the filter. When TP's
 * own `template_include` filter (priority 99999) then returns the editor
 * partial, WordPress includes that partial too — producing two complete HTML
 * documents in the response and breaking the Translation Editor on any page
 * type with an assigned Woodmart Layout (single product, shop archive, etc.).
 *
 * Hooked on `init` priority 999, after Woodmart's `include_files` (priority 10)
 * has loaded the layout classes and registered their filters.
 *
 * @return void
 */
function trp_woodmart_compat__remove_layout_filters() {
    $is_editor  = isset( $_GET['trp-edit-translation'] ) && 'true' === sanitize_text_field( wp_unslash( $_GET['trp-edit-translation'] ) );
    $is_strings = isset( $_GET['trp-string-translation'] ) && 'true' === sanitize_text_field( wp_unslash( $_GET['trp-string-translation'] ) );

    if ( ! ( $is_editor || $is_strings ) )
        return;

    $layout_classes = array(
        'XTS\\Modules\\Layouts\\Single_Product',
        'XTS\\Modules\\Layouts\\Shop_Archive',
        'XTS\\Modules\\Layouts\\Single_Post',
        'XTS\\Modules\\Layouts\\Posts_Archive',
        'XTS\\Modules\\Layouts\\Checkout',
        'XTS\\Modules\\Layouts\\Cart',
        'XTS\\Modules\\Layouts\\My_Account',
        'XTS\\Modules\\Layouts\\Thank_You_Page',
    );

    foreach ( $layout_classes as $class ) {
        if ( class_exists( $class ) ) {
            $instance = $class::get_instance();
            remove_filter( 'template_include', array( $instance, 'override_template' ), 20 );
        }
    }
}
add_action( 'init', 'trp_woodmart_compat__remove_layout_filters', 999 );

/*
 * Add support for Simple Download Manager on certain hosts (not replicated locally)
 * Having TP installed will brake archives, an extra line gets added to the archive processing due to output buffer.
 * Do not translate url's like this as it brakes them because they are archives's: https://translatepress.ddev.site/ro/?sdm_process_download=1&download_id=95
 */
add_action( 'trp_before_running_hooks', 'trp_sdm_compat_remove_hooks_that_start_object_buffer', 10, 1);
function trp_sdm_compat_remove_hooks_that_start_object_buffer( $trp_loader ) {
    if ( isset( $_GET['sdm_process_download'] ) && isset( $_GET['download_id'] ) ) {
        add_filter( 'trp_skip_gettext_processing', '__return_true' );
        $trp                = TRP_Translate_Press::get_trp_instance();
        $translation_render = $trp->get_component( 'translation_render' );
        $trp_loader->remove_hook( 'init', 'start_output_buffer', $translation_render );
    }
}

/*
 * Disable gettext translation of the job manager slugs as they conflict with TranslatePress.
 */
add_filter( 'gettext_with_context', 'trp_ignore_wp_job_manager_slugs', 99, 4 );
function trp_ignore_wp_job_manager_slugs( $translation, $text, $context = null, $domain = null ) {
    static $targets = [ 'job', 'job-category', 'job-type', 'job-listings' ];

    if ( $domain == 'wp-job-manager' && in_array( $text, $targets, true ) ) {
        return $text; // Always return the original untranslated string
    }

    return $translation;
}

/**
 * Add trp-post-container wrapper to Divi module outputs
 * TP is not adding any trp-post-container except here.
 *
 * @param string $output The module HTML output
 * @param string $render_slug The module slug (e.g., 'et_pb_text', 'et_pb_post_title')
 * @param object $module The module object
 * @return string Modified output with trp-post-container wrapper
 */
add_filter('et_module_shortcode_output', 'trp_divi_wrap_module_with_post_id', 10, 3);

function trp_divi_wrap_module_with_post_id($output, $render_slug, $module) {
    global $post, $TRP_LANGUAGE;

    // Check if we have a valid post ID
    if (empty($post->ID)) {
        return $output;
    }

    // Get TranslatePress settings
    $trp = TRP_Translate_Press::get_trp_instance();
    $trp_settings = $trp->get_component('settings');
    $settings = $trp_settings->get_settings();

    // Only wrap on non-default language
    if ($TRP_LANGUAGE !== $settings['default-language']) {
        // Only wrap modules that typically contain translatable text content
        $modules_to_wrap = apply_filters('trp_divi_modules_to_wrap', array(
            'et_pb_text',
            'et_pb_post_title',
            'et_pb_post_content',
            'et_pb_blurb',
            'et_pb_cta',
            'et_pb_accordion',
            'et_pb_toggle',
            'et_pb_tabs',
            'et_pb_testimonial',
            'et_pb_pricing_tables',
            'et_pb_number_counter',
            'et_pb_countdown_timer'
        ));

        if (in_array($render_slug, $modules_to_wrap)) {
            $output = "<trp-post-container data-trp-post-id='" . $post->ID . "'>" . $output . "</trp-post-container>";
        }
    }

    return $output;
}

/**
 * Compatibility with redirect plugins when SEO Pack rewrites REQUEST_URI for translated slugs.
 */
add_action( 'plugins_loaded', 'trp_register_rank_math_seo_pack_compatibility', 20 );
function trp_register_rank_math_seo_pack_compatibility() {
    if ( ! defined( 'TRP_IN_SP_PLUGIN_VERSION' ) || ! class_exists( 'RankMath', false ) ) {
        return;
    }

    add_filter( 'rank_math/redirection/pre_search', 'trp_rank_math_use_original_request_uri_for_redirections', 9, 3 );
    add_filter( 'rank_math/redirection/add_redirect_header', 'trp_guard_rank_math_redirect', PHP_INT_MAX );
    add_filter( 'request', 'trp_rank_math_category_redirect_language', 9 );
}

function trp_rank_math_use_original_request_uri_for_redirections( $check, $uri, $full_uri ) {
    if ( ! is_null( $check ) ) {
        return $check;
    }

    $request_uri = trp_get_original_request_uri();
    if ( empty( $request_uri ) ) {
        return $check;
    }

    if ( ! class_exists( '\RankMath\Redirections\DB' ) || ! class_exists( '\RankMath\Redirections\Redirection' ) ) {
        return $check;
    }

    $full_uri    = str_replace( home_url( '/' ), '', $request_uri );
    $full_uri    = urldecode( $full_uri );
    $full_uri    = trim( \RankMath\Redirections\Redirection::strip_subdirectory( $full_uri ), '/' );

    if ( empty( $full_uri ) ) {
        return $check;
    }

    $uri_parts = explode( '?', $full_uri, 2 );
    $uri       = trim( $uri_parts[0], '/' );

    $redirection = \RankMath\Redirections\DB::match_redirections( $uri );
    if ( ! $redirection && $uri !== $full_uri ) {
        $redirection = \RankMath\Redirections\DB::match_redirections( $full_uri );
    }

    return $redirection ?: $check;
}

/**
 * Rank Math calls this filter immediately before wp_redirect(), after resolving its target.
 * Leave its header preference unchanged; only guard this redirect attempt.
 */
function trp_guard_rank_math_redirect( $add_header ) {
    if ( ! empty( trp_get_original_request_uri() ) ) {
        add_filter( 'wp_redirect', 'trp_prevent_rank_math_self_redirect', PHP_INT_MAX );
    }

    return $add_header;
}

/**
 * Cancel a Rank Math self-redirect using the visitor URL preserved before URI rewriting.
 */
function trp_prevent_rank_math_self_redirect( $location ) {
    remove_filter( 'wp_redirect', 'trp_prevent_rank_math_self_redirect', PHP_INT_MAX );

    if ( ! is_string( $location ) || $location === '' ) {
        return $location;
    }

    // A form submission can legitimately redirect to the same URL for a new GET request.
    if ( isset( $_SERVER['REQUEST_METHOD'] ) && ! in_array( $_SERVER['REQUEST_METHOD'], array( 'GET', 'HEAD' ), true ) ) {
        return $location;
    }

    // The translated-slug cache preserves the visitor URL before SEO Pack rewrites REQUEST_URI.
    $trp = TRP_Translate_Press::get_trp_instance();
    $current_url = $trp->get_component( 'url_converter' )->cur_page_url();
    $target = wp_parse_url( WP_Http::make_absolute_url( $location, $current_url ) );
    $current = wp_parse_url( $current_url );

    if ( ! is_array( $target ) || ! is_array( $current ) ) {
        return $location;
    }

    $normalize_url = static function ( $url ) {
        $url['scheme'] = strtolower( isset( $url['scheme'] ) ? $url['scheme'] : '' );
        $url['host'] = strtolower( isset( $url['host'] ) ? $url['host'] : '' );
        $url['port'] = isset( $url['port'] ) ? $url['port'] : ( $url['scheme'] === 'https' ? 443 : 80 );
        $url['path'] = isset( $url['path'] ) && $url['path'] !== '' ? $url['path'] : '/';
        ksort( $url );
        return $url;
    };

    return $normalize_url( $target ) === $normalize_url( $current ) ? false : $location;
}

/**
 * Rank Math strips the category base during request parsing, before its redirect module.
 */
function trp_rank_math_category_redirect_language( $query_vars ) {
    global $TRP_LANGUAGE;

    if ( ! isset( $query_vars['rank_math_category_redirect'] ) || ! is_string( $query_vars['rank_math_category_redirect'] ) || empty( $TRP_LANGUAGE ) ) {
        return $query_vars;
    }

    $trp = TRP_Translate_Press::get_trp_instance();
    $settings = $trp->get_component( 'settings' )->get_settings();
    if ( $TRP_LANGUAGE === $settings['default-language'] ) {
        return $query_vars;
    }

    $category_url = trailingslashit( get_option( 'home' ) ) . user_trailingslashit( $query_vars['rank_math_category_redirect'], 'category' );
    add_filter( 'wp_redirect', function ( $location ) use ( $category_url, $TRP_LANGUAGE ) {
        if ( $location !== $category_url ) {
            return $location;
        }

        $trp = TRP_Translate_Press::get_trp_instance();
        return $trp->get_component( 'url_converter' )->get_url_for_language( $TRP_LANGUAGE, $location, '' );
    } );

    return $query_vars;
}

/**
 * Register request URL filters for the Redirection plugin only when it is active.
 *
 * TP rewrites REQUEST_URI for translated slugs, while Redirection matches against request URL.
 * We provide the original request URI captured before TP rewrites it.
 */
function trp_register_redirection_original_request_uri_filters() {
    if ( ! class_exists( 'Redirection_Request' ) || ! function_exists( 'trp_get_original_request_uri' ) ) {
        return;
    }

    // Redirection_Request::get_request_url() uses this filter for the current request URI.
    add_filter( 'redirection_request_url', 'trp_get_original_request_uri', 1 );
    // Red_Url_Request applies this to source URL before decode/match.
    add_filter( 'redirection_url_source', 'trp_get_original_request_uri', 1 );
}
add_action( 'plugins_loaded', 'trp_register_redirection_original_request_uri_filters', 20 );

/**
 * Exclude TranslatePress secondary-language URLs from WP Rocket's Preload Links feature.
 *
 * WP Rocket's `<link rel="prefetch">` on hover otherwise warms the browser cache with the
 * alternate-language version of the page being hovered, and a single accidental click can
 * leave the visitor stuck on that other language (every internal link on that page is in
 * the new language too).
 *
 * Languages served from a separate domain (Multiple Domains add-on) are already skipped
 * by WP Rocket's same-origin check, so they're not included in the patterns.
 *
 * Pattern shape `/<slug>(?:/|$)` matches `/he`, `/he/`, `/he/anything` but not `/help` or
 * other slugs that share a prefix. Patterns are joined with `|` and used as a JS RegExp
 * (case-insensitive) inside preload-links.js.
 */
add_filter( 'rocket_preload_links_exclusions', 'trp_exclude_language_slugs_from_wp_rocket_preload_links' );
function trp_exclude_language_slugs_from_wp_rocket_preload_links( $excluded ) {
    if ( ! is_array( $excluded ) ) {
        $excluded = (array) $excluded;
    }

    if ( ! class_exists( 'TRP_Translate_Press' ) ) {
        return $excluded;
    }

    $trp           = TRP_Translate_Press::get_trp_instance();
    $settings_comp = $trp->get_component( 'settings' );
    if ( ! $settings_comp ) {
        return $excluded;
    }

    $settings = $settings_comp->get_settings();
    if ( empty( $settings['publish-languages'] ) || empty( $settings['url-slugs'] ) ) {
        return $excluded;
    }

    $default_language          = isset( $settings['default-language'] ) ? $settings['default-language'] : '';
    $default_uses_subdirectory = isset( $settings['add-subdirectory-to-default-language'] ) && $settings['add-subdirectory-to-default-language'] === 'yes';
    $multiple_domains          = isset( $settings['trp-multiple-domains'] ) && is_array( $settings['trp-multiple-domains'] ) ? $settings['trp-multiple-domains'] : array();

    foreach ( $settings['publish-languages'] as $language_code ) {
        if ( $language_code === $default_language && ! $default_uses_subdirectory ) {
            continue;
        }

        if ( ! empty( $multiple_domains[ $language_code ]['enabled'] ) ) {
            continue;
        }

        if ( empty( $settings['url-slugs'][ $language_code ] ) ) {
            continue;
        }

        $slug = trim( $settings['url-slugs'][ $language_code ], '/' );
        if ( $slug === '' ) {
            continue;
        }

        $excluded[] = '/' . preg_quote( $slug, '/' ) . '(?:/|$)';
    }

    return $excluded;
}

/**
 * Skip TranslatePress language prefixing on home_url() / get_permalink() while a
 * WP Rocket preload job is executing.
 *
 * WP Rocket's preload runs via Action Scheduler. When LoadInitialSitemap and
 * CrawlHomepage seed their URL list from home_url(), $TRP_LANGUAGE may still be
 * set to a secondary language (left over from an earlier render sharing the
 * runner process, or set by a referer-driven mutation in the AJAX/init path).
 * Without this skip the preload bot warms the wrong-language URLs.
 *
 * The receiving side of each preload fetch is intentionally not handled here —
 * resolve_language_context() picks the correct language from the URL itself, so
 * secondary-language pages preload with their proper `/<slug>/...` internal links.
 *
 * @param bool   $skip Whether to skip prefixing already requested by another filter.
 * @param string $url  Original URL passed to the home_url filter.
 * @param string $path Path component passed to the home_url filter.
 * @return bool
 */
add_filter( 'trp_skip_add_language_to_home_url', 'trp_skip_language_during_wp_rocket_preload', 10, 3 );
function trp_skip_language_during_wp_rocket_preload( $skip, $url, $path ) {
    if ( $skip ) {
        return $skip;
    }

    if ( ! defined( 'WP_ROCKET_VERSION' ) ) {
        return $skip;
    }

    if ( ! function_exists( 'doing_action' ) ) {
        return $skip;
    }

    $preload_hooks = array(
        'rocket_preload_job_load_initial_sitemap',
        'rocket_preload_job_parse_sitemap',
        'rocket_preload_job_preload_url',
        'rocket_preload_job_check_finished',
    );

    foreach ( $preload_hooks as $hook ) {
        if ( doing_action( $hook ) ) {
            return true;
        }
    }

    return $skip;
}

/**
 * Compatibility with plugins that serve a local Google Analytics file (e.g. CAOS / Host Analytics Locally).
 *
 * Those requests use the 'local_ga_js' GET parameter to output the analytics JS file. We must not run
 * TranslatePress translation on these responses, so stop translating the page when the parameter is present.
 * Particularly important because automatic translation got triggered.
 */
add_filter( 'trp_stop_translating_page', 'trp_local_ga_js_stop_translating_page' );
function trp_local_ga_js_stop_translating_page( $stop ){
    if ( isset( $_REQUEST['local_ga_js'] ) ){
        return true;
    }
    return $stop;
}

/**
 * Compatibility with WPS Hide Login.
 *
 * When "Use subdirectory for default language" is enabled, TranslatePress adds the language
 * subdirectory (e.g. /en/) to every home_url() call. WPS Hide Login builds its custom login URL
 * from home_url('/') and matches incoming requests against home_url( $login_slug, 'relative' ),
 * so the prefixed URL (e.g. /en/login) no longer matches the real login slug (/login) and the
 * login page returns a 404, locking users out. The login page is not a translatable frontend page
 * (it is the equivalent of wp-login.php), so it must always be reachable without a language prefix.
 *
 * This keeps the login URL unprefixed in both directions:
 *  - link generation: via the dedicated 'wps_hide_login_home_url' filter exposed by the plugin;
 *  - request matching/redirect: by skipping the language subdirectory for the login slug path.
 */
function trp_wps_hide_login_get_slug() {
    if ( ! defined( 'WPS_HIDE_LOGIN_VERSION' ) && ! class_exists( 'WPS\WPS_Hide_Login\Plugin' ) ) {
        return '';
    }
    $slug = get_option( 'whl_page' );
    if ( empty( $slug ) ) {
        $slug = 'login';
    }
    return $slug;
}

/**
 * Strip the TranslatePress language subdirectory from the home URL used to build the login URL.
 * Hooked to WPS Hide Login's own filter, which receives the (possibly prefixed) home_url('/').
 */
add_filter( 'wps_hide_login_home_url', 'trp_wps_hide_login_unprefixed_home_url' );
function trp_wps_hide_login_unprefixed_home_url( $url ) {
    $trp           = TRP_Translate_Press::get_trp_instance();
    $url_converter = $trp->get_component( 'url_converter' );
    if ( ! $url_converter ) {
        return $url;
    }

    $home = trailingslashit( $url_converter->get_abs_home() );

    // Preserve the scheme of the URL the plugin computed (e.g. https when forcing SSL on login).
    $scheme = wp_parse_url( $url, PHP_URL_SCHEME );
    if ( ! empty( $scheme ) ) {
        $home = set_url_scheme( $home, $scheme );
    }

    return $home;
}

/**
 * Do not add the language subdirectory to home_url() calls that point to the WPS Hide Login slug.
 * This keeps home_url( $login_slug, 'relative' ) === /login so the plugin still matches the request
 * and serves the login page instead of letting it fall through to a 404 / language redirect.
 */
add_filter( 'trp_skip_add_language_to_home_url', 'trp_wps_hide_login_skip_language_for_slug', 10, 3 );
function trp_wps_hide_login_skip_language_for_slug( $skip, $url, $path ) {
    if ( $skip ) {
        return $skip;
    }

    $slug = trp_wps_hide_login_get_slug();
    if ( $slug !== '' && trim( (string) $path, '/' ) === $slug ) {
        return true;
    }

    return $skip;
}

/**
 * Compatibility with Slider Revolution.
 *
 * Slider Revolution prints the data of a module as a JSON blob inside a <script> tag
 * ( SR7.JSON['...'] = {...} ) and builds part of the module from it on the client side.
 * The URL of a layer link action lives in that JSON whenever the layer is not rendered
 * as a plain <a href> in the markup: the jQuery link type ( which the editor forces on
 * row, column and group layers ), the slide link and every layer that the module only
 * renders on the client. TranslatePress rewrites URLs found in the HTML markup, not
 * inside scripts, so those links keep pointing to the default language and clicking
 * them sends the visitor back to the default language version of the page.
 *
 * Convert the internal URLs stored in the slider JSON to the current language.
 *
 * @since 3.3.5
 *
 * @param array $data Slider data that Slider Revolution is about to json_encode.
 * @return array
 */
add_filter( 'sr_load_slider_json', 'trp_slider_revolution_add_language_to_json_links' );
function trp_slider_revolution_add_language_to_json_links( $data ) {
    if ( ! is_array( $data ) || empty( $data['slides'] ) || ! is_array( $data['slides'] ) ) {
        return $data;
    }

    foreach ( $data['slides'] as $slide_key => $slide ) {

        // The whole slide can be a link ( Slide Options -> Link & SEO ).
        if ( ! empty( $slide['params']['seo']['link'] ) ) {
            $data['slides'][ $slide_key ]['params']['seo']['link'] = trp_slider_revolution_get_link_for_current_language( $slide['params']['seo']['link'] );
        }

        if ( empty( $slide['layers'] ) || ! is_array( $slide['layers'] ) ) {
            continue;
        }

        foreach ( $slide['layers'] as $layer_key => $layer ) {
            if ( ! is_array( $layer ) ) {
                continue;
            }

            // Version 7 layers keep the link of an <a> layer directly on the layer.
            if ( ! empty( $layer['href'] ) ) {
                $data['slides'][ $slide_key ]['layers'][ $layer_key ]['href'] = trp_slider_revolution_get_link_for_current_language( $layer['href'] );
            }

            if ( empty( $layer['actions']['action'] ) || ! is_array( $layer['actions']['action'] ) ) {
                continue;
            }

            foreach ( $layer['actions']['action'] as $action_key => $action ) {
                if ( ! is_array( $action ) || empty( $action['action'] ) ) {
                    continue;
                }

                if ( $action['action'] === 'link' ) {
                    $link_key = 'image_link';
                } elseif ( $action['action'] === 'menu' ) {
                    $link_key = 'menu_link';
                } else {
                    continue;
                }

                if ( empty( $action[ $link_key ] ) ) {
                    continue;
                }

                $data['slides'][ $slide_key ]['layers'][ $layer_key ]['actions']['action'][ $action_key ][ $link_key ] =
                    trp_slider_revolution_get_link_for_current_language( $action[ $link_key ] );
            }
        }
    }

    return $data;
}

/**
 * Compatibility with Slider Revolution.
 *
 * The legacy version 6 front-end does not use the JSON blob for the layer actions, it writes
 * them in a data-actions attribute instead. TranslatePress does not rewrite URLs found in
 * custom attributes, so the links of jQuery type layer actions need the same treatment there.
 *
 * @since 3.3.5
 *
 * @param array $events Actions that Slider Revolution is about to write in data-actions.
 * @return array
 */
add_filter( 'rs_action_output_layer_action', 'trp_slider_revolution_add_language_to_layer_actions' );
function trp_slider_revolution_add_language_to_layer_actions( $events ) {
    if ( ! is_array( $events ) ) {
        return $events;
    }

    foreach ( $events as $key => $event ) {
        if ( ! is_array( $event ) || empty( $event['url'] ) || empty( $event['a'] ) ) {
            continue;
        }

        if ( ! in_array( $event['a'], array( 'simplelink', 'menulink' ), true ) ) {
            continue;
        }

        $events[ $key ]['url'] = trp_slider_revolution_get_link_for_current_language( $event['url'] );
    }

    return $events;
}

/**
 * Compatibility with Slider Revolution.
 *
 * Add the current language to an internal URL stored by Slider Revolution, following the same
 * rules TranslatePress applies to the href of an <a> tag found in the markup of the page.
 *
 * @since 3.3.5
 *
 * @param string $url URL as stored by Slider Revolution.
 * @return string
 */
function trp_slider_revolution_get_link_for_current_language( $url ) {
    if ( ! is_string( $url ) ) {
        return $url;
    }

    $trimmed_url = trim( $url );

    // Skip empty values, anchors and non http(s) schemes such as mailto:, tel: or javascript:.
    if ( $trimmed_url === '' || strpos( $trimmed_url, '#' ) === 0 ) {
        return $url;
    }
    if ( preg_match( '/^[a-z][a-z0-9+.-]*:/i', $trimmed_url ) && stripos( $trimmed_url, 'http' ) !== 0 ) {
        return $url;
    }

    // Slider Revolution stores protocol relative URLs ( //example.com/page ) when the Protocol
    // option of the action is left on auto. Resolve them so that the host can be compared, then
    // put the URL back in the form it was stored in.
    $is_protocol_relative = ( strpos( $trimmed_url, '//' ) === 0 );
    if ( $is_protocol_relative ) {
        $trimmed_url = ( is_ssl() ? 'https:' : 'http:' ) . $trimmed_url;
    }

    // Anything that is neither absolute nor starting at the root of the site is relative to the
    // current path ( or a host name typed without a protocol ). Adding a language would break it.
    if ( ! preg_match( '#^https?://#i', $trimmed_url ) && strpos( $trimmed_url, '/' ) !== 0 ) {
        return $url;
    }

    global $TRP_LANGUAGE;

    $trp                 = TRP_Translate_Press::get_trp_instance();
    $settings            = $trp->get_component( 'settings' )->get_settings();
    $url_converter       = $trp->get_component( 'url_converter' );
    $translation_render  = $trp->get_component( 'translation_render' );

    if ( ! is_object( $url_converter ) || ! is_object( $translation_render ) ) {
        return $url;
    }

    $default_language        = $settings['default-language'];
    $subdirectory_on_default = ( isset( $settings['add-subdirectory-to-default-language'] ) && $settings['add-subdirectory-to-default-language'] === 'yes' );

    if ( $settings['force-language-to-custom-links'] !== 'yes' ) {
        return $url;
    }
    if ( $TRP_LANGUAGE === $default_language && ! $subdirectory_on_default ) {
        return $url;
    }

    $local_url         = $translation_render->maybe_is_local_url( $trimmed_url, home_url() );
    $language_from_url = $url_converter->get_lang_from_url_string( $local_url );

    if ( $translation_render->is_external_link( $local_url, home_url() ) ||
         $translation_render->is_admin_link( $local_url ) ||
         $url_converter->url_is_file( $local_url ) ||
         ( $language_from_url !== null && ! ( $subdirectory_on_default && $language_from_url === $default_language ) ) ) {
        return $url;
    }

    /**
     * Run the URL through the same filter that TRP_Translation_Render::handle_custom_links_and_forms()
     * applies to the href of an <a> tag, so that Slider Revolution links obey the "Do not translate
     * certain paths" setting and any other filter hooked to it.
     *
     * The fourth argument is the <a> node that the URL was found on. There is no such node here,
     * the URL comes from the slider data, so null is passed instead.
     */
    $new_url = apply_filters( 'trp_force_custom_links', $url_converter->get_url_for_language( $TRP_LANGUAGE, $local_url, '' ), $local_url, $TRP_LANGUAGE, null );

    if ( ! is_string( $new_url ) || $new_url === '' ) {
        return $url;
    }

    if ( $is_protocol_relative ) {
        $new_url = preg_replace( '#^https?://#i', '//', $new_url );
    }

    return $new_url;
}
