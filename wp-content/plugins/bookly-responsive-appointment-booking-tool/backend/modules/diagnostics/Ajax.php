<?php

namespace Bookly\Backend\Modules\Diagnostics;

use Bookly\Backend\Modules\Diagnostics\Tests\Test;
use Bookly\Backend\Modules\Diagnostics\Tools\Tool;
use Bookly\Lib;


/**
 * Class Ajax
 *
 * @package Bookly\Backend\Modules\Diagnostics
 */
class Ajax extends Lib\Base\Ajax
{
    protected static function permissions()
    {
        return array( 'diagnosticsAjax' => 'anonymous' );
    }

    /**
     * Export database data.
     */
    public static function diagnosticsTestRun()
    {
        $test_name = self::parameter( 'test' );
        $class = '\Bookly\Backend\Modules\Diagnostics\Tests\\' . $test_name;
        if ( class_exists( $class ) ) {
            /** @var Test $test */
            $test = new $class();
            if ( $test->execute() ) {
                wp_send_json_success();
            } else {
                wp_send_json_error( array( 'test' => $test->getSlug(), 'errors' => $test->getErrors() ) );
            }
        }
    }

    /**
     * Diagnostics ajax call: action=bookly_diagnostics_ajax&test=TestClassName&ajax=method
     */
    public static function diagnosticsAjax()
    {
        $ajax = self::parameter( 'ajax' );
        if ( $test_name = self::parameter( 'test' ) ) {
            $class = '\Bookly\Backend\Modules\Diagnostics\Tests\\' . $test_name;
            if ( class_exists( $class ) ) {
                /** @var Test $test */
                $test = new $class();
                if ( is_callable( array( $test, $ajax ) ) && method_exists( $test, $ajax ) && ! in_array( $ajax, array( 'execute', 'run' ) ) ) {
                    if ( in_array( $ajax, $test->ignore_csrf, false ) || parent::csrfTokenValid( __FUNCTION__ ) ) {
                        $test->$ajax( self::parameters() );
                    }
                }
            }
        } elseif ( ( $tool_name = self::parameter( 'tool' ) ) && Lib\Utils\Common::isCurrentUserAdmin() ) {
            $class = '\Bookly\Backend\Modules\Diagnostics\Tools\\' . $tool_name;
            if ( class_exists( $class ) ) {
                /** @var Tool $tool */
                $tool = new $class();
                if ( $ajax !== 'render' && is_callable( array( $tool, $ajax ) ) && method_exists( $tool, $ajax ) ) {
                    if ( parent::csrfTokenValid( __FUNCTION__ ) ) {
                        $tool->$ajax( self::parameters() );
                    }
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
        foreach ( apply_filters( 'bookly_plugins', array() ) as $plugin ) {
            /** @var Lib\Base\Plugin $plugin */
            $installer_class = $plugin::getRootNamespace() . '\Lib\Installer';
            /** @var Lib\Base\Installer $installer */
            $installer = new $installer_class();
            $result['plugins'][ $plugin::getBasename() ] = $plugin::getVersion();

            foreach ( $plugin::getEntityClasses() as $entity_class ) {
                $table_name = $entity_class::getTableName();
                $result['entities'][ $entity_class ] = array(
                    'fields' => array_keys( $schema->getTableStructure( $table_name ) ),
                    'values' => $wpdb->get_results( 'SELECT * FROM ' . $table_name, ARRAY_N ),
                );
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

        header( 'Content-type: application/json' );
        header( 'Content-Disposition: attachment; filename=bookly_db_export_' . date( 'YmdHis' ) . '.json' );
        echo json_encode( $result );

        exit ( 0 );
    }

    /**
     * Import database data.
     */
    public static function importData()
    {
        /** @global \wpdb $wpdb */
        global $wpdb;
        $fs = Lib\Utils\Common::getFilesystem();

        if ( $_FILES['import']['name'] ) {
            $json = $fs->get_contents( $_FILES['import']['tmp_name'] );
            if ( $json !== false ) {
                $wpdb->query( 'SET FOREIGN_KEY_CHECKS = 0' );

                $data = json_decode( $json, true );
                /** @var Lib\Base\Plugin[] $bookly_plugins */
                $bookly_plugins = apply_filters( 'bookly_plugins', array() );
                /** @since Bookly 17.7 */
                if ( isset( $data['plugins'] ) ) {
                    foreach ( $bookly_plugins as $plugin ) {
                        if ( ! array_key_exists( $plugin::getBasename(), $data['plugins'] ) ) {
                            deactivate_plugins( $plugin::getBasename(), true, is_network_admin() );
                        }
                    }
                }
                foreach ( array_merge( array( 'bookly-responsive-appointment-booking-tool', 'bookly-addon-pro' ), array_keys( $bookly_plugins ) ) as $slug ) {
                    if ( ! array_key_exists( $slug, $bookly_plugins ) ) {
                        continue;
                    }
                    /** @var Lib\Base\Plugin $plugin */
                    $plugin = $bookly_plugins[ $slug ];
                    unset( $bookly_plugins[ $slug ] );
                    $installer_class = $plugin::getRootNamespace() . '\Lib\Installer';
                    /** @var Lib\Base\Installer $installer */
                    $installer = new $installer_class();

                    // Drop all data and options.
                    $installer->removeData();
                    $installer->dropTables();
                    $installer->createTables();

                    // Insert tables data.
                    foreach ( $plugin::getEntityClasses() as $entity_class ) {
                        if ( isset ( $data['entities'][ $entity_class ]['values'][0] ) ) {
                            $table_name = $entity_class::getTableName();
                            $query = sprintf(
                                'INSERT INTO `%s` (`%s`) VALUES (%%s)',
                                $table_name,
                                implode( '`,`', $data['entities'][ $entity_class ]['fields'] )
                            );
                            $placeholders = array();
                            $values = array();
                            $counter = 0;
                            foreach ( $data['entities'][ $entity_class ]['values'] as $row ) {
                                $params = array();
                                foreach ( $row as $value ) {
                                    if ( $value === null ) {
                                        $params[] = 'NULL';
                                    } else {
                                        $params[] = '%s';
                                        $values[] = $value;
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
                        add_option( $option_name, $data['options'][ $option_name ] );
                    }

                    $plugin_prefix = $plugin::getPrefix();
                    $options_postfix = array( 'data_loaded', 'grace_start', 'db_version' );
                    foreach ( $options_postfix as $option ) {
                        $option_name = $plugin_prefix . $option;
                        add_option( $option_name, $data['options'][ $option_name ] );
                    }
                }
            }
        }

        header( 'Location: ' . admin_url( 'admin.php?page=bookly-diagnostics&debug' ) );

        exit ( 0 );
    }

    protected static function csrfTokenValid( $action = null )
    {
        $excluded_actions = array(
            'diagnosticsAjax',
        );

        return in_array( $action, $excluded_actions ) || parent::csrfTokenValid( $action );
    }
}