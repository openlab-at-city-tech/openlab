<?php
namespace Bookly\Lib\Utils;

use Bookly\Lib;

abstract class Common extends Lib\Base\Cache
{
    /** @var string CSRF token */
    private static $csrf;

    /**
     * Get e-mails of WP & Bookly admins
     *
     * @return array
     */
    public static function getAdminEmails()
    {
        global $wpdb;
        static $emails = null;

        if ( $emails === null ) {
            // Add to filter capability manage_options or manage_bookly
            $meta_query = array(
                'relation' => 'OR',
                array( 'key' => $wpdb->prefix . 'capabilities', 'compare' => 'LIKE', 'value' => '"manage_options"', ),
                array( 'key' => $wpdb->prefix . 'capabilities', 'compare' => 'LIKE', 'value' => '"manage_bookly"', ),
            );
            $roles = new \WP_Roles();
            // Find roles with capabilities manage_options or manage_bookly
            foreach ( $roles->role_objects as $role ) {
                if ( $role->has_cap( 'manage_options' ) || $role->has_cap( 'manage_bookly' ) ) {
                    $meta_query[] = array( 'key' => $wpdb->prefix . 'capabilities', 'compare' => 'LIKE', 'value' => '"' . $role->name . '"', );
                }
            }

            $emails = array_map(
                function( $a ) { return $a->data->user_email; },
                get_users( compact( 'meta_query' ) )
            );
        }

        return $emails;
    }

    /**
     * @return string
     */
    public static function getCurrentPageURL()
    {
        if ( ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) {
            $url = 'https://';
        } else {
            $url = 'http://';
        }
        $url .= isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];

        return $url . $_SERVER['REQUEST_URI'];
    }

    /**
     * @param bool $allow
     */
    public static function cancelAppointmentRedirect( $allow )
    {
        if ( $url = $allow ? get_option( 'bookly_url_cancel_page_url' ) : get_option( 'bookly_url_cancel_denied_page_url' ) ) {
            self::redirect( $url );
        }

        $url = home_url();
        if ( isset ( $_SERVER['HTTP_REFERER'] ) ) {
            if ( parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_HOST ) == parse_url( $url, PHP_URL_HOST ) ) {
                // Redirect back if user came from our site.
                $url = $_SERVER['HTTP_REFERER'];
            }
        }

        self::redirect( $url );
    }

    /**
     * Render redirection page
     *
     * @param string $url
     */
    public static function redirect( $url )
    {
        wp_redirect( $url );
        printf( '<!doctype html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="refresh" content="1;url=%s">
                    <script type="text/javascript">
                        window.location.href = %s;
                    </script>
                    <title>%s</title>
                </head>
                <body>
                %s
                </body>
                </html>',
            esc_attr( $url ),
            json_encode( $url ),
            __( 'Page Redirection', 'bookly' ),
            sprintf( __( 'If you are not redirected automatically, follow the <a href="%s">link</a>.', 'bookly' ), esc_attr( $url ) )
        );
        exit ( 0 );
    }

    /**
     * Escape params for admin.php?page
     *
     * @param $page_slug
     * @param array $params
     * @return string
     */
    public static function escAdminUrl( $page_slug, $params = array() )
    {
        $path = 'admin.php?page=' . $page_slug;
        if ( ( $query = build_query( $params ) ) != '' ) {
            $path .= '&' . $query;
        }

        return esc_url( admin_url( $path ) );
    }

    /**
     * Check whether any of the current posts in the loop contains given short code.
     *
     * @param string $short_code
     * @return bool
     */
    public static function postsHaveShortCode( $short_code )
    {
        $key = __FUNCTION__ . '-' . $short_code;
        if ( ! self::hasInCache( $key ) ) {
            /** @global \WP_Query $wp_query */
            global $wp_query;
            $result = false;
            if ( $wp_query && $wp_query->posts !== null ) {
                foreach ( $wp_query->posts as $post ) {
                    if ( has_shortcode( $post->post_content, $short_code ) || ( function_exists( 'parse_blocks' ) && self::hasBooklyShortCode( parse_blocks( $post->post_content ), $short_code ) ) ) {
                        $result = true;
                        break;
                    }
                    // Fusion builder
                    if ( strpos( $post->post_content, '[fusion' ) !== false ) {
                        $content = apply_filters( 'fusion_add_globals', $post->post_content, $post->guid );
                        if ( has_shortcode( apply_filters( 'fusion_add_globals', $post->post_content, $post->guid ), $short_code ) ) {
                            $result = true;
                            break;
                        }

                        try {
                            if ( preg_match_all( '/' . get_shortcode_regex( array( 'fusion_code' ) ) . '/s', $content, $matches ) ) {
                                foreach ( $matches[5] as $code ) {
                                    if ( has_shortcode( base64_decode( $code ), $short_code ) ) {
                                        $result = true;
                                        break 2;
                                    }
                                }
                            }
                        } catch ( \Exception $e ) {
                        }
                    }

                    try {
                        foreach ( get_post_meta( $post->ID ) ?: array() as $meta ) {
                            if ( is_string( $meta[0] ) && has_shortcode( $meta[0], $short_code ) ) {
                                $result = true;
                                break 2;
                            }
                        }
                    } catch ( \Exception $e ) {
                    }

                }
            }

            self::putInCache( $key, $result );
        }

        return self::getFromCache( $key );
    }

    /**
     * @param array $blocks
     * @param string $short_code
     * @return bool
     */
    private static function hasBooklyShortCode( $blocks, $short_code )
    {
        foreach ( $blocks as $block ) {
            if ( ! empty( $block['innerBlocks'] ) ) {
                return self::hasBooklyShortCode( $block['innerBlocks'], $short_code );
            }

            if ( $block['blockName'] === 'core/block' && ! empty( $block['attrs']['ref'] ) && has_shortcode( get_post( $block['attrs']['ref'] )->post_content, $short_code ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add utm_source, utm_medium, utm_campaign parameters to url
     *
     * @param $url
     * @param $campaign
     * @return string
     */
    public static function prepareUrlReferrers( $url, $campaign )
    {
        return add_query_arg(
            array(
                'utm_source' => 'bookly_admin',
                'utm_medium' => Lib\Config::proActive() ? 'pro_active' : 'pro_not_active',
                'utm_campaign' => $campaign,
            ),
            $url
        );
    }

    /**
     * Get option translated with WPML.
     *
     * @param $option_name
     * @return string
     */
    public static function getTranslatedOption( $option_name )
    {
        return self::getTranslatedString( $option_name, get_option( $option_name ) );
    }

    /**
     * Get string translated with WPML.
     *
     * @param             $name
     * @param string $original_value
     * @param null|string $language_code Return the translation in this language
     * @return string
     */
    public static function getTranslatedString( $name, $original_value = '', $language_code = null )
    {
        return apply_filters( 'wpml_translate_single_string', $original_value, 'bookly', $name, $language_code );
    }

    /**
     * Check whether the current user is administrator or not.
     *
     * @return bool
     */
    public static function isCurrentUserAdmin()
    {
        return current_user_can( 'manage_options' ) || current_user_can( 'manage_bookly' );
    }

    /**
     * Check whether the current user is supervisor or not.
     *
     * @return bool
     */
    public static function isCurrentUserSupervisor()
    {
        return self::isCurrentUserAdmin() || current_user_can( 'manage_bookly_appointments' );
    }

    /**
     * Check whether the current user is staff or not.
     *
     * @return bool
     */
    public static function isCurrentUserStaff()
    {
        return self::isCurrentUserAdmin()
            || Lib\Entities\Staff::query()->where( 'wp_user_id', get_current_user_id() )->count() > 0;
    }

    /**
     * Check whether the current user is customer or not.
     *
     * @return bool
     */
    public static function isCurrentUserCustomer()
    {
        return self::isCurrentUserSupervisor()
            || Lib\Entities\Customer::query()->where( 'wp_user_id', get_current_user_id() )->count() > 0
            || self::isCurrentUserStaff();
    }

    /**
     * Determine the current user time zone which may be the staff or WP time zone
     *
     * @return string
     */
    public static function getCurrentUserTimeZone()
    {
        if ( ! self::isCurrentUserSupervisor() ) {
            /** @var Lib\Entities\Staff $staff */
            $staff = Lib\Entities\Staff::query()->where( 'wp_user_id', get_current_user_id() )->findOne();
            if ( $staff ) {
                $staff_tz = $staff->getTimeZone();
                if ( $staff_tz ) {
                    return $staff_tz;
                }
            }
        }

        // Use WP time zone by default
        return Lib\Config::getWPTimeZone();
    }

    /**
     * Get required capability for view menu.
     *
     * @return string
     */
    public static function getRequiredCapability()
    {
        return current_user_can( 'manage_options' ) ? 'manage_options' : 'manage_bookly';
    }

    /**
     * @param int $duration
     * @return array
     */
    public static function getDurationSelectOptions( $duration )
    {
        $time_interval = get_option( 'bookly_gen_time_slot_length' );

        $options = array();

        for ( $j = $time_interval; $j <= 720; $j += $time_interval ) {

            if ( ( $duration / 60 > $j - $time_interval ) && ( $duration / 60 < $j ) ) {
                $options[] = array(
                    'value' => $duration,
                    'label' => DateTime::secondsToInterval( $duration ),
                    'selected' => 'selected',
                );
            }

            $options[] = array(
                'value' => $j * 60,
                'label' => DateTime::secondsToInterval( $j * 60 ),
                'selected' => selected( $duration, $j * 60, false ),
            );
        }

        for ( $j = 86400; $j <= 604800; $j += 86400 ) {
            $options[] = array(
                'value' => $j,
                'label' => DateTime::secondsToInterval( $j ),
                'selected' => selected( $duration, $j, false ),
            );
        }

        return $options;
    }

    /**
     * Get services grouped by categories for drop-down list.
     *
     * @param string $raw_where
     * @return array
     */
    public static function getServiceDataForDropDown( $raw_where = null )
    {
        $result = array();

        $query = Lib\Entities\Service::query( 's' )
            ->select( 'c.id AS category_id, c.name, s.id, s.title' )
            ->leftJoin( 'Category', 'c', 'c.id = s.category_id' )
            ->sortBy( 'COALESCE(c.position,99999), s.position' );
        if ( $raw_where !== null ) {
            $query->whereRaw( $raw_where, array() );
        }
        foreach ( $query->fetchArray() as $row ) {
            $category_id = (int) $row['category_id'];
            if ( ! isset ( $result[ $category_id ] ) ) {
                $result[ $category_id ] = array(
                    'name' => $category_id ? $row['name'] : __( 'Uncategorized', 'bookly' ),
                    'items' => array(),
                );
            }
            $result[ $category_id ]['items'][] = array(
                'id' => $row['id'],
                'title' => $row['title'],
            );
        }

        return $result;
    }

    /**
     * @param callable $func
     * @param array $arr
     * @return array
     */
    public static function arrayMapRecursive( callable $func, array $arr )
    {
        array_walk_recursive( $arr, function( &$v ) use ( $func ) {
            $v = $func( $v );
        } );

        return $arr;
    }

    /**
     * XOR encrypt/decrypt.
     *
     * @param string $str
     * @param string $password
     * @return string
     */
    private static function _xor( $str, $password = '' )
    {
        $len = strlen( $str );
        $gamma = '';
        $n = $len > 100 ? 8 : 2;
        while ( strlen( $gamma ) < $len ) {
            $gamma .= substr( pack( 'H*', sha1( $password . $gamma ) ), 0, $n );
        }

        return $str ^ $gamma;
    }

    /**
     * XOR encrypt with Base64 encode.
     *
     * @param string $str
     * @param string $password
     * @return string
     */
    public static function xorEncrypt( $str, $password = '' )
    {
        return base64_encode( self::_xor( $str, $password ) );
    }

    /**
     * XOR decrypt with Base64 decode.
     *
     * @param string $str
     * @param string $password
     * @return string
     */
    public static function xorDecrypt( $str, $password = '' )
    {
        return self::_xor( base64_decode( $str ), $password );
    }

    /**
     * Generate unique value for entity field.
     *
     * @param string $entity_class_name
     * @param string $token_field
     * @return string
     */
    public static function generateToken( $entity_class_name, $token_field )
    {
        /** @var Lib\Base\Entity $entity */
        $entity = new $entity_class_name();
        do {
            $token = md5( uniqid( time(), true ) );
        } while ( $entity->loadBy( array( $token_field => $token ) ) === true );

        return $token;
    }

    /**
     * Get CSRF token.
     *
     * @return string
     */
    public static function getCsrfToken()
    {
        if ( self::$csrf === null ) {
            self::$csrf = wp_create_nonce( 'bookly' );
        }

        return self::$csrf;
    }

    /**
     * Set nocache constants.
     *
     * @param bool $forcibly
     */
    public static function noCache( $forcibly = false )
    {
        if ( $forcibly || get_option( 'bookly_gen_prevent_caching' ) ) {
            if ( ! defined( 'DONOTCACHEPAGE' ) ) {
                define( 'DONOTCACHEPAGE', true );
            }
            if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
                define( 'DONOTCACHEOBJECT', true );
            }
            if ( ! defined( 'DONOTCACHEDB' ) ) {
                define( 'DONOTCACHEDB', true );
            }
        }
    }

    /**
     * Disable WP Emoji
     */
    public static function disableEmoji()
    {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'embed_head', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
    }

    /**
     * @return \WP_Filesystem_Direct
     */
    public static function getFilesystem()
    {
        global $wp_filesystem;

        require_once ABSPATH . 'wp-admin/includes/file.php';

        if ( ! $wp_filesystem ) {
            WP_Filesystem();
        }

        // Emulate WP_Filesystem to avoid FS_METHOD and filters overriding "direct" type
        if ( ! class_exists( 'WP_Filesystem_Direct', false ) ) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
        }

        return new \WP_Filesystem_Direct( null );
    }

    /**
     * Get sorted payment systems
     *
     * @return array
     */
    public static function getGateways()
    {
        $gateways = array();
        if ( Lib\Config::payLocallyEnabled() ) {
            $gateways[ Lib\Entities\Payment::TYPE_LOCAL ] = Lib\Entities\Payment::typeToString( Lib\Entities\Payment::TYPE_LOCAL );
        }

        if ( Lib\Config::stripeCloudEnabled() ) {
            $gateways[ Lib\Entities\Payment::TYPE_CLOUD_STRIPE ] = Lib\Entities\Payment::typeToString( Lib\Entities\Payment::TYPE_CLOUD_STRIPE );
        }

        foreach ( \Bookly\Backend\Modules\Appearance\Proxy\Shared::paymentGateways( array() ) as $type => $gateway ) {
            $gateways[ $type ] = $gateway['title'];
        }

        $order = Lib\Config::getGatewaysPreference();
        $payment_systems = array();

        if ( $order ) {
            foreach ( $order as $payment_system ) {
                if ( array_key_exists( $payment_system, $gateways ) ) {
                    $payment_systems[ $payment_system ] = $gateways[ $payment_system ];
                    unset( $gateways[ $payment_system ] );
                }
            }
        }

        return array_merge( $payment_systems, $gateways );
    }

    /**
     * Get common settings for Bookly calendar
     *
     * @return array
     */
    public static function getCalendarSettings()
    {
        $slot_length_minutes = get_option( 'bookly_gen_time_slot_length', '15' );
        $slot = new \DateInterval( 'PT' . $slot_length_minutes . 'M' );

        $hidden_days = array();
        $min_time = '00:00:00';
        $max_time = '24:00:00';
        $scroll_time = '08:00:00';
        // Find min and max business hours
        $min = $max = null;
        foreach ( Lib\Config::getBusinessHours() as $day => $bh ) {
            if ( $bh['start'] === null ) {
                if ( Lib\Config::showOnlyBusinessDaysInCalendar() ) {
                    $hidden_days[] = $day;
                }
                continue;
            }
            if ( $min === null || $bh['start'] < $min ) {
                $min = $bh['start'];
            }
            if ( $max === null || $bh['end'] > $max ) {
                $max = $bh['end'];
            }
        }
        if ( $min !== null ) {
            $scroll_time = $min;
            if ( Lib\Config::showOnlyBusinessHoursInCalendar() ) {
                $min_time = $min;
                $max_time = $max;
            } elseif ( $max > '24:00:00' ) {
                $min_time = DateTime::buildTimeString( DateTime::timeToSeconds( $max ) - DAY_IN_SECONDS );
                $max_time = $max;
            }
        }

        return array(
            'hiddenDays' => $hidden_days,
            'slotDuration' => $slot->format( '%H:%I:%S' ),
            'slotMinTime' => $min_time,
            'slotMaxTime' => $max_time,
            'scrollTime' => $scroll_time,
            'locale' => Lib\Config::getShortLocale(),
            'monthDayMaxEvents' => (int) ( get_option( 'bookly_cal_month_view_style' ) == 'minimalistic' ),
            'mjsTimeFormat' => DateTime::convertFormat( 'time', DateTime::FORMAT_MOMENT_JS ),
            'datePicker' => DateTime::datePickerOptions(),
            'dateRange' => DateTime::dateRangeOptions(),
            'today' => __( 'Today', 'bookly' ),
            'week' => __( 'Week', 'bookly' ),
            'day' => __( 'Day', 'bookly' ),
            'month' => __( 'Month', 'bookly' ),
            'list' => __( 'List', 'bookly' ),
            'allDay' => __( 'All day', 'bookly' ),
            'noEvents' => __( 'No appointments for selected period.', 'bookly' ),
            'more' => __( '+%d more', 'bookly' ),
            'timeline' => __( 'Timeline', 'bookly' ),
        );
    }

    /**
     * @return array
     */
    public static function getIndustries()
    {
        return array(
            __( 'Education', 'bookly' ) => array(
                '34' => __( 'Universities', 'bookly' ),
                '35' => __( 'Colleges', 'bookly' ),
                '36' => __( 'Schools', 'bookly' ),
                '37' => __( 'Libraries', 'bookly' ),
                '38' => __( 'Teaching', 'bookly' ),
                '39' => __( 'Tutoring lessons', 'bookly' ),
                '40' => __( 'Parent meetings', 'bookly' ),
                '41' => __( 'Services', 'bookly' ),
                '42' => __( 'Child care', 'bookly' ),
                '43' => __( 'Driving Schools', 'bookly' ),
                '44' => __( 'Driving Instructors', 'bookly' ),
                '45' => __( 'Other', 'bookly' ),
            ),
            __( 'Beauty and wellness', 'bookly' ) => array(
                '11' => __( 'Beauty salons', 'bookly' ),
                '12' => __( 'Hair salons', 'bookly' ),
                '13' => __( 'Nail salons', 'bookly' ),
                '14' => __( 'Eyelash extensions', 'bookly' ),
                '15' => __( 'Spa', 'bookly' ),
                '16' => __( 'Other', 'bookly' ),
            ),
            __( 'Events and entertainment', 'bookly' ) => array(
                '46' => __( 'Events (One time and Recurring)', 'bookly' ),
                '47' => __( 'Business events', 'bookly' ),
                '48' => __( 'Meeting rooms', 'bookly' ),
                '49' => __( 'Escape rooms', 'bookly' ),
                '50' => __( 'Art classes', 'bookly' ),
                '51' => __( 'Equipment rental', 'bookly' ),
                '52' => __( 'Photographers', 'bookly' ),
                '53' => __( 'Restaurants', 'bookly' ),
                '54' => __( 'Other', 'bookly' ),
            ),
            __( 'Medical', 'bookly' ) => array(
                '17' => __( 'Medical Clinics & Doctors', 'bookly' ),
                '18' => __( 'Dentists', 'bookly' ),
                '19' => __( 'Chiropractors', 'bookly' ),
                '20' => __( 'Acupuncture', 'bookly' ),
                '21' => __( 'Massage', 'bookly' ),
                '22' => __( 'Physiologists', 'bookly' ),
                '23' => __( 'Psychologists', 'bookly' ),
                '24' => __( 'Other', 'bookly' ),
            ),
            __( 'Officials', 'bookly' ) => array(
                '55' => __( 'City councils', 'bookly' ),
                '56' => __( 'Embassies and consulates', 'bookly' ),
                '57' => __( 'Attorneys', 'bookly' ),
                '58' => __( 'Legal services', 'bookly' ),
                '59' => __( 'Financial services', 'bookly' ),
                '60' => __( 'Interview scheduling', 'bookly' ),
                '61' => __( 'Call centers', 'bookly' ),
                '62' => __( 'Other', 'bookly' ),
            ),
            __( 'Personal meetings and services', 'bookly' ) => array(
                '25' => __( 'Consulting', 'bookly' ),
                '26' => __( 'Counselling', 'bookly' ),
                '27' => __( 'Coaching', 'bookly' ),
                '28' => __( 'Spiritual services', 'bookly' ),
                '29' => __( 'Design consultants', 'bookly' ),
                '30' => __( 'Cleaning', 'bookly' ),
                '31' => __( 'Household', 'bookly' ),
                '32' => __( 'Pet services', 'bookly' ),
                '33' => __( 'Other', 'bookly' ),
            ),
            __( 'Retailers', 'bookly' ) => array(
                '1' => __( 'Supermarket', 'bookly' ),
                '2' => __( 'Retail Finance', 'bookly' ),
                '3' => __( 'Other retailers', 'bookly' ),
            ),
            __( 'Sport', 'bookly' ) => array(
                '4' => __( 'Personal trainers', 'bookly' ),
                '5' => __( 'Gyms', 'bookly' ),
                '6' => __( 'Fitness classes', 'bookly' ),
                '7' => __( 'Yoga classes', 'bookly' ),
                '8' => __( 'Golf classes', 'bookly' ),
                '9' => __( 'Sport items renting', 'bookly' ),
                '10' => __( 'Other', 'bookly' ),
            ),
            __( 'Other', 'bookly' ) => array(
                '63' => __( 'Other', 'bookly' ),
            ),
        );
    }

    /**
     * Remove <script> tags from the given string
     *
     * @param string $html
     * @return string
     */
    public static function stripScripts( $html )
    {
        return preg_replace( '@<script[^>]*?>.*?</script>@si', '', $html );
    }

    /**
     * Prepare html for output (currently allow all tags)
     *
     * @param string $html
     * @return string
     */
    public static function html( $html )
    {
        // Currently, allow any HTML tags
        return $html;
    }

    /**
     * Prepare css for output
     *
     * @param string $css
     * @return string
     */
    public static function css( $css )
    {
        return trim( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $css ) );
    }

    /**
     * Update user meta only for blog users.
     *
     * @param string $meta_key
     * @param string $meta_value
     */
    public static function updateBlogUsersMeta( $meta_key, $meta_value, $blog_id = null )
    {
        global $wpdb;
        if ( is_multisite() ) {
            $prefix = $wpdb->get_blog_prefix( $blog_id );
            $query = 'UPDATE `' . $wpdb->usermeta . '` AS um
                   LEFT JOIN `' . $wpdb->usermeta . '` AS um2
                          ON (um2.user_id = um.user_id)
                         SET um.meta_value = %s 
                       WHERE um2.meta_key = %s
                         AND um.meta_key = %s';
            $wpdb->query( $wpdb->prepare( $query, $meta_value, $prefix . 'capabilities', $meta_key ) );
        } else {
            $wpdb->update( $wpdb->usermeta, compact( 'meta_value' ), compact( 'meta_key' ) );
        }
    }

    /**
     * @param $attachment_id
     * @param $size
     * @return string
     */
    public static function getAttachmentUrl( $attachment_id, $size = 'full' )
    {
        if ( $attachment_id && $img = wp_get_attachment_image_src( $attachment_id, $size ) ) {
            return $img[0];
        }

        return '';
    }

    /**
     * @param string $url
     * @param string $alt
     * @return string
     */
    public static function getImageTag( $url, $alt )
    {
        return $url
            ? sprintf( '<img src="%s" alt="%s" />', esc_attr( $url ), esc_attr( $alt ) )
            : '';
    }

    /**
     * @param int $response_code
     * @return void
     */
    public static function emptyResponse( $response_code )
    {
        if ( ! headers_sent() ) {
            header( 'Content-Type: text/html; charset=utf-8' );
            http_response_code( $response_code );
        }
        exit;
    }

    /**
     * @param Lib\Entities\Appointment $appointment
     * @return void
     */
    public static function syncWithCalendars( Lib\Entities\Appointment $appointment )
    {
        list( $sync, $gc, $oc ) = Lib\Config::syncCalendars();
        if ( $sync && $appointment->getStartDate() ) {
            $gc && Lib\Proxy\Pro::syncGoogleCalendarEvent( $appointment );
            $oc && Lib\Proxy\OutlookCalendar::syncEvent( $appointment );
        }
    }
}