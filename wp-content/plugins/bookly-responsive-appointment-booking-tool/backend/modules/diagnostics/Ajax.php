<?php
namespace Bookly\Backend\Modules\Diagnostics;

use Bookly\Backend\Modules\Diagnostics\Tests\Test;
use Bookly\Backend\Modules\Diagnostics\Tools\Tool;
use Bookly\Lib;
use Bookly\Lib\Utils\Common;

class Ajax extends Lib\Base\Ajax
{
    protected static function permissions()
    {
        return array( 'diagnosticsAjax' => 'anonymous' );
    }

    public static function runDiagnosticsTest()
    {
        $class = self::getClassInstance( self::parameter( 'test' ), '\Bookly\Backend\Modules\Diagnostics\Tests\\' );
        if ( $class instanceof Test ) {
            if ( $class->execute() ) {
                wp_send_json_success();
            } else {
                wp_send_json_error( array( 'test' => $class->getSlug(), 'errors' => $class->getErrors() ) );
            }
        }
    }

    /**
     * Diagnostics ajax call: action=bookly_diagnostics_ajax&test=TestClassName&ajax=method
     */
    public static function diagnosticsAjax()
    {
        $method = self::parameter( 'ajax' );
        $class = self::getClassInstance( self::parameter( 'test' ), '\Bookly\Backend\Modules\Diagnostics\Tests\\' );
        if ( $class instanceof Test ) {
            if ( is_callable( array( $class, $method ) ) && ! in_array( $method, array( 'execute', 'run' ) ) ) {
                if ( in_array( $method, $class->ignore_csrf, false ) || parent::csrfTokenValid( __FUNCTION__ ) ) {
                    $class->$method( self::parameters() );
                }
            }
        } elseif ( ( $tool_name = self::parameter( 'tool' ) ) && Lib\Utils\Common::isCurrentUserAdmin() ) {
            $class = self::getClassInstance( $tool_name, '\Bookly\Backend\Modules\Diagnostics\Tools\\' );
            if ( $class instanceof Tool ) {
                if ( $method !== 'render' && method_exists( $class, $method ) && parent::csrfTokenValid( __FUNCTION__ ) ) {
                    $class->$method( self::parameters() );
                }
            }
        }
    }

    /**
     * Export database data.
     */
    public static function exportData()
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        $result = array();
        $schema = new Schema();
        $ignore = array();
        foreach ( self::parameter( 'ignore', array() ) as $section ) {
            switch ( $section ) {
                case 'appointments':
                    $ignore[] = 'bookly_appointments';
                    $ignore[] = 'bookly_customer_appointments';
                    break;
                case 'mailing queue':
                    $ignore[] = 'bookly_mailing_queue';
                    $ignore[] = 'bookly_notifications_queue';
                    break;
                case 'payments':
                    $ignore[] = 'bookly_payments';
                    break;
                case 'sessions':
                    $ignore[] = 'bookly_sessions';
                    break;
                case 'logs':
                    $ignore[] = 'bookly_log';
                    $ignore[] = 'bookly_email_log';
                    break;
                case 'files':
                    $ignore[] = 'bookly_files';
                    $ignore[] = 'bookly_customer_appointment_files';
                    break;
            }
        }
        foreach ( $ignore as &$i ) {
            $i = $wpdb->prefix . $i;
        }
        unset( $i );
        foreach ( apply_filters( 'bookly_plugins', array() ) as $plugin ) {
            /** @var Lib\Base\Plugin $plugin */
            $installer_class = $plugin::getRootNamespace() . '\Lib\Installer';
            /** @var Lib\Base\Installer $installer */
            $installer = new $installer_class();
            $result['plugins'][ $plugin::getBasename() ] = $plugin::getVersion();

            foreach ( $plugin::getEntityClasses() as $entity_class ) {
                $table_name = $entity_class::getTableName();
                if ( ! in_array( $table_name, $ignore ) ) {
                    $result['entities'][ $entity_class ] = array(
                        'fields' => array_keys( $schema->getTableStructure( $table_name ) ),
                        'values' => $wpdb->get_results( 'SELECT * FROM ' . $table_name, ARRAY_N ),
                    );
                }
            }
            $plugin_prefix = $plugin::getPrefix();
            $options_postfix = array( 'data_loaded', 'grace_start', 'db_version', 'installation_time' );
            foreach ( $options_postfix as $option ) {
                $option_name = $plugin_prefix . $option;
                $result['options'][ $option_name ] = get_option( $option_name );
            }

            $result['options'][ $plugin::getPurchaseCodeOption() ] = $plugin::getPurchaseCode();
            foreach ( $installer->getOptions() as $option_name => $option_value ) {
                $result['options'][ $option_name ] = get_option( $option_name );
            }
        }

        if ( self::parameter( 'safe', false ) ) {
            $result = self::makeSafe( $result );
        }

        header( 'Content-Disposition: attachment; filename=bookly_db_export_' . date( 'Ymd-Hi' ) . '_' . str_replace( '.', '_', parse_url( get_site_url(), PHP_URL_HOST ) ) . '.json' );
        wp_send_json( $result );
    }

    /**
     * Import database data.
     */
    public static function importData()
    {
        /** @global \wpdb $wpdb */
        global $wpdb;
        $fs = Lib\Utils\Common::getFilesystem();
        $errors = $info = array();

        if ( $_FILES['import']['name'] ) {
            $json = $fs->get_contents( $_FILES['import']['tmp_name'] );
            if ( $json !== false ) {

                $json_data = json_decode( $json, true );
                if ( self::parameter( 'safe', false ) ) {
                    $json_data = self::makeSafe( $json_data );
                }
                /** @var Lib\Base\Plugin[] $bookly_plugins */
                $bookly_plugins = apply_filters( 'bookly_plugins', array() );
                /** @since Bookly 17.7 */
                if ( isset( $json_data['plugins'] ) ) {
                    foreach ( $bookly_plugins as $slug => $plugin ) {
                        $basename = $plugin::getBasename();
                        if ( array_key_exists( $basename, $json_data['plugins'] ) ) {
                            $info[] = $plugin::getTitle() . ' v' . $json_data['plugins'][ $basename ] . ( version_compare( $plugin::getVersion(), $json_data['plugins'][ $basename ], '==' ) ? '' : ' ⚠️ code v' . $plugin::getVersion() );
                        } elseif ( array_key_exists( 'bookly-addon-pro/lib/addons/' . $basename, $json_data['plugins'] ) ) {
                            $basename_internal = 'bookly-addon-pro/lib/addons/' . $basename;
                            $info[] = $plugin::getTitle() . ' v' . $json_data['plugins'][ $basename_internal ] . ( version_compare( $plugin::getVersion(), $json_data['plugins'][ $basename_internal ], '==' ) ? '' : ' ⚠️ code v' . $plugin::getVersion() );
                        } else {
                            deactivate_plugins( $plugin::getBasename(), true, is_network_admin() );
                            unset( $bookly_plugins[ $slug ] );
                        }
                    }
                }
                $pi = array();
                foreach ( array_merge( array( 'bookly-responsive-appointment-booking-tool', 'bookly-addon-pro' ), array_keys( $bookly_plugins ) ) as $slug ) {
                    if ( ! array_key_exists( $slug, $bookly_plugins ) ) {
                        continue;
                    }
                    /** @var Lib\Base\Plugin $plugin */
                    $plugin = $bookly_plugins[ $slug ];
                    unset( $bookly_plugins[ $slug ] );
                    $installer_class = $plugin::getRootNamespace() . '\Lib\Installer';

                    // Updater has been blocked for 30 seconds.
                    set_transient( $plugin::getPrefix() . 'updating_db', time(), 30 );

                    /** @var Lib\Base\Installer $installer */
                    $installer = new $installer_class();
                    // Drop all data and options.
                    $installer->removeData();
                    $installer->dropTables();

                    $pi[] = array(
                        'plugin_class' => $plugin,
                        'installer' => $installer,
                    );
                }
                $wpdb->query( 'SET FOREIGN_KEY_CHECKS = 0' );
                foreach ( $pi as $objects ) {
                    $objects['installer']->createTables();
                }

                foreach ( $pi as $objects ) {
                    $plugin = $objects['plugin_class'];
                    $installer = $objects['installer'];
                    // Insert tables data.
                    foreach ( $plugin::getEntityClasses() as $entity_class ) {
                        if ( isset ( $json_data['entities'][ $entity_class ]['values'][0] ) ) {
                            $table_name = $entity_class::getTableName();

                            $unknown_values = array();
                            $query = self::getQuery( $table_name, $json_data['entities'][ $entity_class ]['fields'], $unknown_values );
                            if ( $unknown_values ) {
                                $errors[] = 'The dump for `' . $table_name . '` contains unknown columns: ' . implode( ', ', $unknown_values );
                            }
                            $placeholders = array();
                            $values = array();
                            $counter = 0;

                            foreach ( $json_data['entities'][ $entity_class ]['values'] as $row ) {
                                $params = array();
                                if ( $unknown_values ) {
                                    foreach ( $row as $id => $value ) {
                                        if ( ! array_key_exists( $id, $unknown_values ) ) {
                                            if ( $value === null ) {
                                                $params[] = 'NULL';
                                            } else {
                                                $params[] = '%s';
                                                $values[] = $value;
                                            }
                                        }
                                    }
                                } else {
                                    foreach ( $row as $value ) {
                                        if ( $value === null ) {
                                            $params[] = 'NULL';
                                        } else {
                                            $params[] = '%s';
                                            $values[] = $value;
                                        }
                                    }
                                }
                                $placeholders[] = implode( ',', $params );
                                if ( ++$counter > 50 ) {
                                    // Flush.
                                    $wpdb->query( $wpdb->prepare( sprintf( $query, implode( '),(', $placeholders ) ), $values ) );
                                    $placeholders = array();
                                    $values = array();
                                    $counter = 0;
                                }
                            }
                            if ( ! empty ( $placeholders ) ) {
                                $wpdb->query( $wpdb->prepare( sprintf( $query, implode( '),(', $placeholders ) ), $values ) );
                            }
                        }
                    }

                    // Insert options data.
                    foreach ( $installer->getOptions() as $option_name => $option_value ) {
                        if ( array_key_exists( $option_name, $json_data['options'] ) ) {
                            add_option( $option_name, $json_data['options'][ $option_name ] );
                        }
                    }

                    $plugin_prefix = $plugin::getPrefix();
                    $options_postfix = array( 'data_loaded', 'grace_start' );
                    foreach ( $options_postfix as $option ) {
                        $option_name = $plugin_prefix . $option;
                        add_option( $option_name, $json_data['options'][ $option_name ] );
                    }

                    $option_name = $plugin_prefix . 'db_version';
                    $min_version = version_compare( $json_data['options'][ $option_name ], $plugin::getVersion(), '>' )
                        ? $plugin::getVersion()
                        : $json_data['options'][ $option_name ];
                    add_option( $option_name, $min_version );
                }

                $wpdb->insert( $wpdb->prefix . 'bookly_log', array(
                    'action' => 'debug',
                    'target' => 'bookly-restore',
                    'author' => get_current_user_id(),
                    'details' => json_encode( $info ),
                    'comment' => 'Restore from ' . $_FILES['import']['name'],
                    'ref' => $_SERVER['REMOTE_ADDR'],
                    'created_at' => current_time( 'mysql' ),
                ) );
            }
        }

        $errors
            ? wp_send_json_error( array( 'message' => $errors ) )
            : wp_send_json_success( array( 'message' => implode( '<br>', $info ) ) );
    }

    /**
     * Make import/export data 'safe'
     *
     * @param array $data
     * @return array
     */
    protected static function makeSafe( $data )
    {
        $unsafe_options = array(
            'bookly_gc_client_id',
            'bookly_gc_client_secret',
            'bookly_oc_app_id',
            'bookly_oc_app_secret',
            'bookly_zoom_oauth_client_id',
            'bookly_zoom_oauth_client_secret',
            'bookly_smtp_host',
            'bookly_smtp_port',
            'bookly_smtp_user',
            'bookly_smtp_password',
            'bookly_cloud_token',
        );

        $unsafe_entities = array(
            'Bookly\Lib\Entities\Staff' => array(
                'google_data' => null,
                'outlook_data' => null,
                'zoom_authentication' => 'default',
                'zoom_oauth_token' => null,
            ),
        );

        foreach ( $unsafe_options as $option ) {
            if ( array_key_exists( $option, $data['options'] ) ) {
                $data['options'][ $option ] = '';
            }
        }

        $data['options']['bookly_email_gateway'] = 'wp';

        // Remove unsafe staff settings
        foreach ( $unsafe_entities as $entity => $entity_unsafe_values ) {
            if ( isset( $data['entities'][ $entity ] ) ) {
                foreach ( $entity_unsafe_values as $field => $default ) {
                    if ( ( $index = array_search( $field, $data['entities'][ $entity ]['fields'], true ) ) !== false ) {
                        foreach ( $data['entities'][ $entity ]['values'] as &$entity_field ) {
                            $entity_field[ $index ] = $default;
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Get logs.
     */
    public static function getLogs()
    {
        global $wpdb;

        $filter = self::parameter( 'filter' );
        $columns = Lib\Utils\Tables::filterColumns( self::parameter( 'columns' ), Lib\Utils\Tables::LOGS );
        $order = self::parameter( 'order', array() );

        $query = Lib\Entities\Log::query();

        // Filters.
        list ( $start, $end ) = explode( ' - ', $filter['created_at'], 2 );
        if ( $start !== 'any' ) {
            if ( strlen( $end ) === 10 ) {
                $end = date( 'Y-m-d', strtotime( '+1 day', strtotime( $end ) ) );
            }
            $query->whereBetween( 'created_at', $start, $end );
        }
        if ( isset( $filter['search'] ) && $filter['search'] !== '' ) {
            $query->whereRaw( 'target LIKE "%%%s%" OR details LIKE "%%%s%" OR target_id LIKE "%%%s%" OR ref LIKE "%%%s%" OR comment LIKE "%%%s%" OR author LIKE "%%%s%" OR id LIKE "%%%s%"', array_fill( 0, 7, $wpdb->esc_like( $filter['search'] ) ) );
        }
        if ( isset( $filter['target'] ) && $filter['target'] !== '' ) {
            $query->where( 'target_id', $filter['target'] );
        }
        if ( isset( $filter['action'] ) && $filter['action'] ) {
            $query->whereIn( 'action', $filter['action'] );
        } else {
            $query->whereIn( 'action', array(
                Lib\Utils\Log::ACTION_CREATE,
                Lib\Utils\Log::ACTION_DELETE,
                Lib\Utils\Log::ACTION_UPDATE,
                Lib\Utils\Log::ACTION_ERROR,
            ) );
        }

        $filtered = $query->count();

        if ( self::parameter( 'length' ) ) {
            $query->limit( self::parameter( 'length' ) )->offset( self::parameter( 'start' ) );
        }

        foreach ( $order as $sort_by ) {
            $field = str_replace( '.', '_', $columns[ $sort_by['column'] ]['data'] );
            $field = $field === 'created_at'
                ? 'id'
                : $field;
            $query->sortBy( $field )
                ->order( isset( $sort_by['dir'] ) && $sort_by['dir'] === 'desc' ? Lib\Query::ORDER_DESCENDING : Lib\Query::ORDER_ASCENDING );
        }

        $logs = $query->fetchArray();

        wp_send_json( array(
            'draw' => ( int ) self::parameter( 'draw' ),
            'recordsTotal' => count( $logs ),
            'recordsFiltered' => $filtered,
            'data' => $logs,
        ) );
    }

    /**
     * Delete all logs
     */
    public static function deleteLogs()
    {
        Lib\Entities\Log::query()
            ->delete()
            ->execute();

        wp_send_json_success();
    }

    /**
     * Set logs expire
     */
    public static function setLogsExpire()
    {
        update_option( 'bookly_logs_expire', self::parameter( 'expire', 30 ) );

        wp_send_json_success();
    }

    public static function importAppointments()
    {
        global $wpdb;

        $name = $_FILES['files']['name'][0];
        $parts = explode( '.', $name );
        $extension = end( $parts );
        $fs = Lib\Utils\Common::getFilesystem();
        if ( ! in_array( strtolower( $extension ), array( 'csv' ), false ) ) {
            $fs->delete( $_FILES['files']['tmp_name'][0], false, 'f' );
            wp_send_json_error( array( 'error' => Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_incorrect_file_type' ) ) );
        }
        $config = json_decode( self::parameter( 'config', '[]' ), true );
        $durations = json_decode( self::parameter( 'durations', '[]' ), true );
        $statuses = json_decode( self::parameter( 'statuses', '[]' ), true );
        $prices = json_decode( self::parameter( 'prices', '[]' ), true );

        $author_id = get_current_user_id();
        $author = $author_id ? ( trim( get_user_meta( $author_id, 'first_name', true ) . ' ' . get_user_meta( $author_id, 'last_name', true ) ) ?: get_user_meta( $author_id, 'nickname', true ) ) : '';

        $rollback_data = array(
            'date' => current_time( 'mysql' ),
            'staff' => array(),
            'services' => array(),
            'customers' => array(),
            'appointments' => array(),
            'orders' => array(),
            'payments' => array()
        );

        @ini_set( 'auto_detect_line_endings', true );
        if ( $fs->exists( $_FILES['files']['tmp_name'][0] ) ) {
            $rows = $fs->get_contents_array( $_FILES['files']['tmp_name'][0] );
            if ( $rows ) {
                $wpdb->insert( $wpdb->prefix . 'bookly_log', array(
                    'action' => 'debug',
                    'target' => 'bookly-import',
                    'author' => $author,
                    'details' => 'Start',
                    'comment' => 'Import from ' . $name,
                    'ref' => $_SERVER['REMOTE_ADDR'],
                    'created_at' => current_time( 'mysql' ),
                ) );

                $bookly_statuses = Lib\Entities\CustomerAppointment::getStatuses();
                foreach ( $rows as $row_number => $row ) {
                    $line = array_map( 'trim', str_getcsv( $row, $config['delimiter'], '"', "\\" ) );

                    // Start Date
                    if ( ! $start_date = strtotime( $line[ $config['start_date_column'] ] ) ) {
                        continue;
                    }
                    // End Date/Duration
                    if ( $config['end_date_column'] !== '-1' ) {
                        if ( $end_date = strtotime( $line[ $config['end_date_column'] ] ) ) {
                            // End Date
                            $duration = $end_date - $start_date;
                        } else {
                            // Duration
                            $duration = isset( $durations[ $line[ $config['end_date_column'] ] ] ) && $durations[ $line[ $config['end_date_column'] ] ] ? $durations[ $line[ $config['end_date_column'] ] ] * 60 : Lib\Config::getTimeSlotLength();
                        }
                    } else {
                        $duration = $config['duration_default'] ? $config['duration_default'] * 60 : Lib\Config::getTimeSlotLength();
                    }

                    // Staff
                    $staff = new Lib\Entities\Staff();
                    $staff_name = $config['staff_name_column'] !== '-1' ? $line[ $config['staff_name_column'] ] : $config['staff_name_default'];
                    if ( ! $staff->loadBy( array( 'full_name' => $staff_name ) ) ) {
                        $staff
                            ->setFullName( $staff_name )
                            ->save();
                        $rollback_data['staff'][] = $staff->getId();
                    }

                    // Price
                    $price = number_format( floatval( preg_replace( '/[^\d\.]+/', '', $config['price_column'] !== '-1' ? ( isset( $prices[ $line[ $config['price_column'] ] ] ) ? $prices[ $line[ $config['price_column'] ] ] : $line[ $config['price_column'] ] ) : $config['price_default'] ) ), 2, '.', '' );

                    // Service
                    $service = new Lib\Entities\Service();
                    $service_name = $config['service_name_column'] !== '-1' ? $line[ $config['service_name_column'] ] : $config['service_name_default'];
                    if ( ! $service->loadBy( array( 'title' => $service_name, 'duration' => $duration ) ) ) {
                        $service
                            ->setTitle( $service_name )
                            ->setPrice( $price )
                            ->setDuration( $duration )
                            ->save();
                        $rollback_data['services'][] = $service->getId();
                    }
                    // StaffService
                    $staff_service = new Lib\Entities\StaffService();
                    if ( ! $staff_service->loadBy( array( 'staff_id' => $staff->getId(), 'service_id' => $service->getId() ) ) ) {
                        $staff_service
                            ->setStaffId( $staff->getId() )
                            ->setServiceId( $service->getId() )
                            ->setPrice( $price )
                            ->save();
                    }
                    // Customer
                    $customer = new Lib\Entities\Customer();
                    $customer_data = array(
                        'full_name' => $config['client_name_column'] !== '-1' ? $line[ $config['client_name_column'] ] : $config['client_name_default'],
                    );
                    if ( $config['client_email_column'] !== '' ) {
                        $customer_data['email'] = $config['client_email_column'] !== '-1' ? $line[ $config['client_email_column'] ] : $config['client_email_default'];
                    }
                    if ( $config['client_phone_column'] !== '' ) {
                        $customer_data['phone'] = $config['client_phone_column'] !== '-1' ? $line[ $config['client_phone_column'] ] : $config['client_phone_default'];
                    }
                    if ( ! $customer->loadBy( $customer_data ) ) {
                        $customer
                            ->setFullName( $customer_data['full_name'] )
                            ->setEmail( isset( $customer_data['email'] ) ? $customer_data['email'] : '' )
                            ->setPhone( isset( $customer_data['phone'] ) ? $customer_data['phone'] : '' )
                            ->save();
                        $rollback_data['customers'][] = $customer->getId();
                    }
                    //Appointment
                    $appointment = new Lib\Entities\Appointment();
                    $appointment
                        ->setStaffId( $staff->getId() )
                        ->setServiceId( $service->getId() )
                        ->setCreatedFrom( 'import' )
                        ->setStartDate( date( 'Y-m-d H:i:s', $start_date ) )
                        ->setEndDate( date( 'Y-m-d H:i:s', $start_date + $duration ) )
                        ->save();
                    $rollback_data['appointments'][] = $appointment->getId();
                    // Status
                    if ( $config['status_column'] !== '-1' ) {
                        $_status = $line[ $config['status_column'] ];
                        $status = isset( $statuses[ $_status ] ) && in_array( $statuses[ $_status ], $bookly_statuses ) ? $statuses[ $_status ] : Lib\Config::getDefaultAppointmentStatus();
                    } else {
                        $status = $config['status_default'] ?: Lib\Config::getDefaultAppointmentStatus();
                    }

                    // Order
                    $order = new Lib\Entities\Order();
                    $order
                        ->setToken( Common::generateToken( get_class( $order ), 'token' ) )
                        ->save();
                    $rollback_data['orders'][] = $order->getId();

                    // Customer appointment
                    $ca = new Lib\Entities\CustomerAppointment();
                    $ca
                        ->setAppointmentId( $appointment->getId() )
                        ->setCustomerId( $customer->getId() )
                        ->setStatus( $status )
                        ->setCreatedAt( current_time( 'mysql' ) )
                        ->setOrderId( $order->getId() )
                        ->save();

                    // Payment
                    if ( $price > 0 ) {
                        $payment = new Lib\Entities\Payment();
                        $payment
                            ->setType( Lib\Entities\Payment::TYPE_LOCAL )
                            ->setOrderId( $order->getId() )
                            ->setTotal( $price )
                            ->setPaid( $price )
                            ->setTax( 0 )
                            ->save();
                        $rollback_data['payments'][] = $payment->getId();

                        $app_details = new Lib\DataHolders\Details\Appointment();
                        $app_details
                            ->setCa( $ca )
                            ->setData( array(
                                'service_price' => $price,
                                'service_tax' => 0,
                            ) )
                            ->setPrice( $price );

                        $details = $payment->getDetailsData();
                        $details
                            ->setCustomer( $customer )
                            ->addDetails( $app_details )
                            ->setData( array(
                                'from_backend' => true
                            ) );

                        $ca->setPaymentId( $payment->getId() )->save();
                        $payment->save();
                    }
                }
            }
        } else {
            wp_send_json_error();
        }

        $wpdb->insert( $wpdb->prefix . 'bookly_log', array(
            'action' => 'debug',
            'target' => 'bookly-import',
            'author' => $author,
            'details' => serialize( $rollback_data ),
            'comment' => 'Import from ' . $name,
            'ref' => $_SERVER['REMOTE_ADDR'],
            'created_at' => current_time( 'mysql' ),
        ) );

        foreach ( $rollback_data as $key => $value ) {
            if ( is_array( $rollback_data[ $key ] ) && count( $rollback_data[ $key ] ) > 0 ) {
                update_option( 'bookly_import_rollback_data', $rollback_data );
                break;
            }
        }

        wp_send_json_success( array(
            'date' => Lib\Utils\DateTime::formatDateTime( $rollback_data['date'] ),
            'staff' => count( $rollback_data['staff'] ),
            'services' => count( $rollback_data['services'] ),
            'customers' => count( $rollback_data['customers'] ),
            'appointments' => count( $rollback_data['appointments'] ),
        ) );
    }

    public static function rollbackAppointments()
    {
        global $wpdb;

        $rollback_data = get_option( 'bookly_import_rollback_data' );

        if ( $rollback_data ) {
            $author_id = get_current_user_id();
            $author = $author_id ? ( trim( get_user_meta( $author_id, 'first_name', true ) . ' ' . get_user_meta( $author_id, 'last_name', true ) ) ?: get_user_meta( $author_id, 'nickname', true ) ) : '';

            $wpdb->insert( $wpdb->prefix . 'bookly_log', array(
                'action' => 'debug',
                'target' => 'bookly-import',
                'author' => $author,
                'details' => json_encode( $rollback_data ),
                'comment' => 'Rollback',
                'ref' => $_SERVER['REMOTE_ADDR'],
                'created_at' => current_time( 'mysql' ),
            ) );

            if ( $appointments = $rollback_data['appointments'] ) {
                $wpdb->query( $wpdb->prepare( sprintf( 'DELETE FROM `' . Lib\Entities\Appointment::getTableName() . '` WHERE id IN (%s)',
                    implode( ', ', array_fill( 0, count( $appointments ), '%s' ) ) ), $appointments ) );
            }
            if ( $staff = $rollback_data['staff'] ) {
                $wpdb->query( $wpdb->prepare( sprintf( 'DELETE FROM `' . Lib\Entities\Staff::getTableName() . '` WHERE id IN (%s)',
                    implode( ', ', array_fill( 0, count( $staff ), '%s' ) ) ), $staff ) );
            }
            if ( $customers = $rollback_data['customers'] ) {
                $wpdb->query( $wpdb->prepare( sprintf( 'DELETE FROM `' . Lib\Entities\Customer::getTableName() . '` WHERE id IN (%s)',
                    implode( ', ', array_fill( 0, count( $customers ), '%s' ) ) ), $customers ) );
            }
            if ( $services = $rollback_data['services'] ) {
                $wpdb->query( $wpdb->prepare( sprintf( 'DELETE FROM `' . Lib\Entities\Service::getTableName() . '` WHERE id IN (%s)',
                    implode( ', ', array_fill( 0, count( $services ), '%s' ) ) ), $services ) );
            }
            if ( $payments = $rollback_data['payments'] ) {
                $wpdb->query( $wpdb->prepare( sprintf( 'DELETE FROM `' . Lib\Entities\Payment::getTableName() . '` WHERE id IN (%s)',
                    implode( ', ', array_fill( 0, count( $payments ), '%s' ) ) ), $payments ) );
            }
            if ( $orders = $rollback_data['orders'] ) {
                $wpdb->query( $wpdb->prepare( sprintf( 'DELETE FROM `' . Lib\Entities\Order::getTableName() . '` WHERE id IN (%s)',
                    implode( ', ', array_fill( 0, count( $orders ), '%s' ) ) ), $orders ) );
            }

            delete_option( 'bookly_import_rollback_data' );
        }

        wp_send_json_success();
    }

    protected static function csrfTokenValid( $action = null )
    {
        $excluded_actions = array(
            'diagnosticsAjax',
        );

        return in_array( $action, $excluded_actions ) || parent::csrfTokenValid( $action );
    }

    private static function getQuery( $table_name, $fields, &$unknown_values )
    {
        global $wpdb;
        $columns = $wpdb->get_col( $wpdb->prepare( 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = SCHEMA() AND TABLE_NAME = %s ORDER BY TABLE_NAME, ORDINAL_POSITION', $table_name ) );

        $exist_fields = array();
        foreach ( $fields as $id => $field ) {
            if ( in_array( $field, $columns ) ) {
                $exist_fields[] = $field;
            } else {
                $unknown_values[ $id ] = $field;
            }
        }

        return sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (%%s)',
            $table_name,
            implode( '`,`', $exist_fields )
        );
    }

    private static function getClassInstance( $class_name, $namespace )
    {
        $class = $namespace . $class_name;
        if ( class_exists( $class ) ) {
            return new $class();
        }

        return null;
    }
}