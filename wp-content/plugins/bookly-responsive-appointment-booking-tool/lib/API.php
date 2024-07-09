<?php
namespace Bookly\Lib;

abstract class API
{
    const API_URL = 'https://api.booking-wp-plugin.com';

    /**
     * Get info.
     *
     * @return array|false
     */
    public static function getInfo( $news_date )
    {
        $url = add_query_arg( array( 'site_url' => site_url(), 'news_date' => $news_date ), self::API_URL . '/1.1/info' );
        $response = wp_remote_get( $url, array(
            'sslverify' => false,
            'timeout' => 25,
        ) );

        if ( ! is_wp_error( $response ) && isset ( $response['body'] ) ) {

            return json_decode( $response['body'], true );
        }

        return false;
    }

    /**
     * Register subscriber.
     *
     * @param string $email
     * @return bool
     */
    public static function registerSubscriber( $email )
    {
        $response = wp_remote_post( self::API_URL . '/1.0/subscribers', array(
            'sslverify' => false,
            'timeout' => 25,
            'body' => array(
                'email' => $email,
                'site_url' => site_url(),
                'src' => Config::proActive() ? 'bookly_admin_pro' : 'bookly_admin_free',
            ),
        ) );
        $state = 'invalid';
        if ( ! is_wp_error( $response ) && isset ( $response['body'] ) ) {
            $json = json_decode( $response['body'], true );
            if ( isset ( $json['success'] ) ) {
                if ( $json['success'] ) {
                    $state = 'success';
                } else {
                    $state = $json['errors'][0];
                }
            }
        }

        return $state;
    }

    /**
     * Send Net Promoter Score.
     *
     * @param integer $rate
     * @param string $msg
     * @param string $email
     * @return bool
     */
    public static function sendNps( $rate, $msg, $email )
    {
        $response = wp_remote_post( self::API_URL . '/1.0/nps', array(
            'sslverify' => false,
            'timeout' => 25,
            'body' => array(
                'rate' => $rate,
                'msg' => $msg,
                'email' => $email,
                'site_url' => site_url(),
            ),
        ) );

        if ( ! is_wp_error( $response ) && isset ( $response['body'] ) ) {
            $json = json_decode( $response['body'], true );
            if ( isset ( $json['success'] ) && $json['success'] ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Send statistics data.
     */
    public static function sendStats()
    {
        /** @global \wpdb $wpdb*/
        global $wpdb;

        $today = substr( current_time( 'mysql' ), 0, 10 );
        $ago_10days = date_create( $today )->modify( '-10 days' )->format( 'Y-m-d H:i:s' );
        $ago_30days = date_create( $today )->modify( '-30 days' )->format( 'Y-m-d H:i:s' );

        // Staff members.
        $staff = array( 'total' => 0, 'admins' => 0, 'non_admins' => 0 );
        /** @var \Bookly\Lib\Entities\Staff $staff_member */
        foreach ( Entities\Staff::query()->find() as $staff_member ) {
            ++$staff['total'];
            $wp_user_id = $staff_member->getWpUserId();
            if ( $wp_user_id && $user = get_user_by( 'id', $wp_user_id ) ) {
                if ( $user->has_cap( 'manage_options' ) ) {
                    ++$staff['admins'];
                } else {
                    ++$staff['non_admins'];
                }
            }
        }

        // Services.
        $services = array();

        $services['visible_simple'] = Entities\Service::query( 's' )
            ->where( 's.type', Entities\Service::TYPE_SIMPLE )
            ->whereNot( 's.visibility', Entities\Service::VISIBILITY_PRIVATE )
            ->count();

        // Max duration.
        $row = Entities\Service::query()->select( 'MAX(duration) AS max_duration' )->fetchRow();
        $services['max_duration'] = $row['max_duration'];

        // Max capacity.
        $row = Entities\Service::query( 's' )
            ->select( 'MAX(ss.capacity_max) AS max_capacity' )
            ->innerJoin( 'StaffService', 'ss', 'ss.service_id = s.id' )
            ->where( 's.type', Entities\Service::TYPE_SIMPLE )
            ->whereNot( 's.visibility', Entities\Service::VISIBILITY_PRIVATE )
            ->fetchRow();
        $services['max_capacity'] = $row['max_capacity'];

        // Services quantity.
        $services['quantity'] = Entities\Service::query()->count();

        // StaffServices.
        $staff_services = array(
            'total' => Entities\StaffService::query()->count(),
        );

        // Find active customers.
        $sql = $wpdb->prepare( '
             SELECT COUNT(customer_id) AS active_customers
               FROM ( SELECT DISTINCT(customer_id)
                        FROM `' . Entities\CustomerAppointment::getTableName() . '`
                       WHERE created_at >= %s
                     ) AS active',
            $ago_30days );
        $active_clients = (int) $wpdb->get_var( $sql );

        // Payments completed.
        $completed_payments = Entities\Payment::query()
            ->whereGt( 'created_at', $ago_30days )
            ->where( 'status', Entities\Payment::STATUS_COMPLETED )
            ->count();

        // Extras quantity.
        $extras_quantity = Config::serviceExtrasActive() && get_option( 'bookly_service_extras_enabled' ) ? count( Proxy\ServiceExtras::findAll() ) : null;

        // Cart Enabled.
        $cart_enabled = Config::cartActive() && get_option( 'bookly_cart_enabled' ) == 1;

        // History Data.
        $history = array();

        $history_schema = array( 'bookings_from_frontend' => 0, 'bookings_from_backend' => 0 );

        if ( Config::payLocallyEnabled() ) {
            $history_schema[ 'bookings_payment_' . Entities\Payment::TYPE_LOCAL ] = 0;
        }
        if ( Config::paypalEnabled() ) {
            $history_schema[ 'bookings_payment_' . Entities\Payment::TYPE_PAYPAL ] = 0;
        }
        if ( Config::stripeActive() && get_option( 'bookly_stripe_enabled' ) ) {
            $history_schema[ 'bookings_payment_' . Entities\Payment::TYPE_STRIPE ] = 0;
        }
        if ( Config::twoCheckoutActive() && get_option( 'bookly_2checkout_enabled' ) ) {
            $history_schema[ 'bookings_payment_' . Entities\Payment::TYPE_2CHECKOUT ] = 0;
        }
        if ( Config::authorizeNetActive() && get_option( 'bookly_authorize_net_enabled' ) ) {
            $history_schema[ 'bookings_payment_' . Entities\Payment::TYPE_AUTHORIZENET ] = 0;
        }
        if ( Config::paysonActive() && get_option( 'bookly_payson_enabled' ) ) {
            $history_schema[ 'bookings_payment_' . Entities\Payment::TYPE_PAYSON ] = 0;
        }
        if ( Config::mollieActive() && get_option( 'bookly_mollie_enabled' ) ) {
            $history_schema[ 'bookings_payment_' . Entities\Payment::TYPE_MOLLIE ] = 0;
        }
        if ( Config::payuLatamActive() && get_option( 'bookly_payu_latam_enabled' ) ) {
            $history_schema[ 'bookings_payment_' . Entities\Payment::TYPE_PAYULATAM ] = 0;
        }

        $history_schema['bookings_payment_coupon'] = Config::couponsActive() ? 0 : null;
        $history_schema[ 'bookings_payment_' . Entities\Payment::TYPE_WOOCOMMERCE ] = get_option( 'bookly_wc_enabled' ) ? 0 : null;

        if ( Config::serviceExtrasActive() && get_option( 'bookly_service_extras_enabled' ) ) {
            $history_schema['bookings_with_extras'] = 0;
            $history_schema['bookings_without_extras'] = 0;
        }

        if ( Config::couponsActive() && get_option( 'bookly_coupons_enabled' ) ) {
            $history_schema['bookings_with_coupon'] = 0;
            $history_schema['bookings_without_coupon'] = 0;
        }

        if ( Config::recurringAppointmentsActive() && get_option( 'bookly_recurring_appointments_enabled' ) ) {
            $history_schema['bookings_in_series'] = 0;
            $history_schema['bookings_not_in_series'] = 0;
        }

        if ( Config::depositPaymentsActive() ) {
            $history_schema['paid_deposit'] = 0;
            $history_schema['paid_in_full'] = 0;
            if ( $staff_services['total'] != 0 ) {
                $staff_services['deposit'] = round( Entities\StaffService::query( 'ss' )->whereNot( 'ss.deposit', '100%' )->whereRaw( 'ss.deposit != ss.price', array() )->count() / $staff_services['total'] * 100 );
            }
        }

        if ( Config::specialDaysActive() ) {
            $history_schema['special_days_changed'] = 0;
        }

        $period = new \DatePeriod( date_create( $ago_10days ), new \DateInterval( 'P1D' ), date_create( $today ) );

        foreach ( $period as $date ) {
            $history[ $date->format( 'Y-m-d' ) ] = $history_schema;
        }

        // Bookings With Coupons.
        if ( Config::couponsActive() && get_option( 'bookly_coupons_enabled' ) ) {
            $rows = Entities\CustomerAppointment::query( 'ca' )
                ->select( 'p.details, DATE_FORMAT(ca.created_at, \'%%Y-%%m-%%d\') AS cur_date' )
                ->innerJoin( 'Payment', 'p', 'ca.payment_id = p.id' )
                ->whereGte( 'created_at', $ago_10days )
                ->whereLt( 'created_at', $today )
                ->fetchArray();

            foreach ( $rows as $record ) {
                $details = json_decode( $record['details'], true );
                if ( $details['coupon'] ) {
                    $history[ $record['cur_date'] ]['bookings_with_coupon']++;
                } else {
                    $history[ $record['cur_date'] ]['bookings_without_coupon']++;
                }
            }
        }

        // Bookings Payment Methods.
        $rows = Entities\CustomerAppointment::query( 'ca' )
            ->select( 'COUNT(*) AS quantity, p.type, DATE_FORMAT(ca.created_at, \'%%Y-%%m-%%d\') AS cur_date' )
            ->innerJoin( 'Payment', 'p', 'ca.payment_id = p.id' )
            ->whereGte( 'created_at', $ago_10days )
            ->whereLt( 'created_at', $today )
            ->groupBy( 'p.type, cur_date' )
            ->fetchArray();

        foreach ( $rows as $record ) {
            $history[ $record['cur_date'] ][ 'bookings_payment_' . $record['type'] ] = (int) $record['quantity'];
        }

        // Bookings in Series.
        if ( Config::recurringAppointmentsActive() && get_option( 'bookly_recurring_appointments_enabled' ) ) {
            $rows = Entities\CustomerAppointment::query( 'ca' )
                ->select( 'series_id, IF(series_id IS NULL, COUNT(*), 1) AS in_series, DATE_FORMAT(created_at, \'%%Y-%%m-%%d\') AS cur_date' )
                ->whereGte( 'created_at', date_create( current_time( 'mysql' ) )->modify( '-10 days' )->format( 'Y-m-d' ) )
                ->whereLt( 'created_at', date_create( current_time( 'mysql' ) )->format( 'Y-m-d' ) )
                ->groupBy( 'series_id, cur_date' )
                ->fetchArray();

            foreach ( $rows as $record ) {
                if ( $record['series_id'] == null ) {
                    $history[ $record['cur_date'] ]['bookings_not_in_series'] = (int) $record['in_series'];
                } else {
                    $history[ $record['cur_date'] ]['bookings_in_series'] += 1;
                }
            }
        }

        // Frontend/Backend Bookings.
        $rows = Entities\CustomerAppointment::query()
            ->select( 'COUNT(*) AS quantity, created_from, DATE_FORMAT(created_at, \'%%Y-%%m-%%d\') AS cur_date' )
            ->whereGte( 'created_at', $ago_10days )
            ->whereLt( 'created_at', $today )
            ->groupBy( 'created_from, cur_date' )
            ->fetchArray();

        foreach ( $rows as $record ) {
            $history[ $record['cur_date'] ][ 'bookings_from_' . $record['created_from'] ] = (int) $record['quantity'];
        }

        // Statistic
        $rows = Entities\Stat::query( 's' )
            ->select( 'DATE_FORMAT(created_at, \'%%Y-%%m-%%d\') AS created_at, `name`, `value`' )
            ->whereGte( 'created_at', $ago_10days )
            ->whereLt( 'created_at', $today )
            ->fetchArray();
        foreach ( $rows as $record ) {
            $history[ $record['created_at'] ][ $record['name'] ] = $record['value'];
        }

        // Deposits Payments.
        if ( Config::depositPaymentsActive() ) {
            $rows = Entities\Payment::query()
                ->select( 'COUNT(*) AS quantity, paid_type, DATE_FORMAT(created_at, \'%%Y-%%m-%%d\') AS cur_date' )
                ->whereGte( 'created_at', $ago_10days )
                ->whereLt( 'created_at', $today )
                ->groupBy( 'paid_type, cur_date' )
                ->fetchArray();

            foreach ( $rows as $record ) {
                $history[ $record['cur_date'] ][ 'paid_' . $record['paid_type'] ] = (int) $record['quantity'];
            }
        }

        // Bookings with Extras.
        if ( Config::serviceExtrasActive() && get_option( 'bookly_service_extras_enabled' ) ) {
            $rows = Entities\CustomerAppointment::query()
                ->select( 'COUNT(*) AS quantity, IF(extras=\'[]\', 0, 1) AS with_extras, DATE_FORMAT(created_at, \'%%Y-%%m-%%d\') AS cur_date' )
                ->whereGte( 'created_at', $ago_10days )
                ->whereLt( 'created_at', $today )
                ->groupBy( 'with_extras, cur_date' )
                ->fetchArray();

            foreach ( $rows as $record ) {
                if ( $record['with_extras'] == 1 ) {
                    $history[ $record['cur_date'] ]['bookings_with_extras'] = (int) $record['quantity'];
                } else {
                    $history[ $record['cur_date'] ]['bookings_without_extras'] = (int) $record['quantity'];
                }
            }
        }

        // Send request.
        wp_remote_post( self::API_URL . '/1.5/stats', array(
            'sslverify' => false,
            'timeout' => 25,
            'body' => array(
                'site_url' => site_url(),
                'active_clients' => $active_clients,
                'admin_language' => get_option( 'bookly_admin_preferred_language' ),
                'wp_locale' => get_locale(),
                'company' => get_option( 'bookly_co_name' ),
                'industry' => get_option( 'bookly_co_industry' ),
                'size' => get_option( 'bookly_co_size' ),
                'email' => get_option( 'bookly_co_email' ),
                'completed_payments' => $completed_payments,
                'custom_fields_count' => count( Proxy\CustomFields::getAll( array() ) ?: array() ),
                'description' => get_bloginfo( 'description' ),
                'extras_quantity' => $extras_quantity,
                'cart_enabled' => $cart_enabled,
                'history' => $history,
                'php' => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION,
                'services' => $services,
                'staff' => $staff,
                'staff_services' => $staff_services,
                'title' => get_bloginfo( 'name' ),
                'source' => Config::proActive() ? 'bookly-pro' : 'bookly',
            ),
        ) );
    }

    /**
     * Get info.
     *
     * @return array|false
     */
    public static function getRequiredAddonsVersions( $bookly_version )
    {
        $url = add_query_arg( array( 'site_url' => site_url(), 'bookly_version' => $bookly_version ), self::API_URL . '/1.0/addons' );
        $response = wp_remote_get( $url, array(
            'sslverify' => false,
            'timeout' => 25,
        ) );

        if ( ! is_wp_error( $response ) && isset ( $response['body'] ) ) {

            return json_decode( $response['body'], true );
        }

        return false;
    }
}