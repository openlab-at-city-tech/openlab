<?php
namespace Bookly\Lib\Base;

use Bookly\Lib\PluginsUpdater;

abstract class Updater extends Schema
{
    protected $errors = array();

    /**
     * Run updates on 'plugins_loaded' hook.
     */
    public function run()
    {
        global $wpdb;

        $plugin_class = Plugin::getPluginFor( $this );
        $transient_name = $plugin_class::getPrefix() . 'updating_db';
        $lock = (int) get_transient( $transient_name );
        if ( $lock + 30 < time() ) {
            $version_option_name = $plugin_class::getPrefix() . 'db_version';
            $db_version = get_option( $version_option_name );
            $plugin_version = $plugin_class::getVersion();
            if ( $db_version !== false && version_compare( $plugin_version, $db_version, '>' ) ) {
                // Lock concurrent updates for 30 seconds.
                set_transient( $transient_name, time(), 30 );
                set_time_limit( 0 );

                $updates = array_filter(
                    get_class_methods( $this ),
                    function( $method ) { return strstr( $method, 'update_' ); }
                );
                usort( $updates, 'strnatcmp' );
                $updates_processed = array();
                foreach ( $updates as $method ) {
                    $version = str_replace( '_', '.', substr( $method, 7 ) );
                    if ( strnatcmp( $version, $db_version ) > 0 && strnatcmp( $version, $plugin_version ) <= 0 ) {
                        // Update the lock.
                        set_transient( $transient_name, time() );
                        // Do update.
                        try {
                            $updates_processed[ $method ] = false;
                            $errors_count = count( $this->errors );
                            $this->$method();
                            if ( $errors_count === count( $this->errors ) ) {
                                $updates_processed[ $method ] = true;
                            }
                        } catch ( \Error $e ) {
                            $this->errors[] = array(
                                'method' => get_class( $this ) . '::' . $method,
                                'message' => $e->getMessage(),
                                'file' => $e->getFile(),
                                'line' => $e->getLine(),
                            );
                        } catch ( \Exception $e ) {
                            $this->errors[] = array(
                                'method' => get_class( $this ) . '::' . $method,
                                'message' => $e->getMessage(),
                                'file' => $e->getFile(),
                                'line' => $e->getLine(),
                            );
                        }
                        update_option( $version_option_name, $version );
                    }
                }
                $logs_table = $this->getTableName( 'bookly_log' );
                // Log errors.
                if ( $this->errors ) {
                    try {
                        foreach ( $this->errors as $error ) {
                            $wpdb->insert( $logs_table, array(
                                'action' => 'error',
                                'target' => 'bookly-updater',
                                'author' => get_current_user_id(),
                                'details' => json_encode( $error ),
                                'ref' => isset( $error['method'] ) ? $error['method'] : '',
                                'created_at' => current_time( 'mysql' ),
                            ) );
                        }
                    } catch ( \Exception $e ) {
                    }
                }
                // Make sure db_version is set to plugin version (even though there were no updates).
                update_option( $version_option_name, $plugin_version );
                $wpdb->insert( $logs_table, array(
                    'action' => 'debug',
                    'target' => $plugin_class::getTitle() . ' ' . $db_version . ' - ' . $plugin_version,
                    'author' => get_current_user_id(),
                    'details' => $plugin_class::getSlug(),
                    'comment' => json_encode( $updates_processed ),
                    'ref' => '',
                    'created_at' => current_time( 'mysql' ),
                ) );
                try {
                    if ( $plugin_class::getSlug() === 'bookly-responsive-appointment-booking-tool' && class_exists( 'BooklyPro\Lib\Plugin', false ) ) {
                        PluginsUpdater::speedUpUpdate( array( 'bookly-addon-pro' ) );
                    } elseif ( $plugin_class::getSlug() === 'bookly-addon-pro' ) {
                        $slugs = array();
                        foreach ( get_option( 'active_plugins' ) as $path ) {
                            $dirname = dirname( $path );
                            if ( $dirname !== 'bookly-addon-pro' && strpos( $dirname, 'bookly-addon-' ) === 0 ) {
                                $slugs[] = $dirname;
                            }
                        }
                        $slugs && PluginsUpdater::speedUpUpdate( $slugs );
                    }
                } catch ( \Exception $e ) {
                }
            }
        }
    }

    /**
     * Execute array queries where the key is the table name.
     *
     * @param array $data key is table name
     */
    protected function alterTables( array $data )
    {
        foreach ( $data as $table => $queries ) {
            $table_name = $this->getTableName( $table );
            foreach ( $queries as $query ) {
                $this->query( sprintf( $query, $table_name ) );
            }
        }
    }

    /**
     * Rename tables
     *
     * @param array $tables
     * @return void
     */
    protected function renameTables( array $tables )
    {
        foreach ( $tables as $table => $new_table_name ) {
            $this->alterTables( array( $table => array( 'RENAME TABLE `%s` TO ' . $this->getTableName( $new_table_name ) ) ) );
        }
    }

    /**
     * Rename options.
     *
     * @param array $options
     */
    protected function renameOptions( array $options )
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        foreach ( $options as $old_name => $new_name ) {
            $this->query( $wpdb->prepare(
                'UPDATE `' . $wpdb->options . '` SET `option_name` = %s WHERE `option_name` = %s',
                $new_name,
                $old_name
            ) );
        }
    }

    /**
     * Rename user meta keys.
     *
     * @param array $meta
     */
    protected function renameUserMeta( array $meta )
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        foreach ( $meta as $old_name => $new_name ) {
            $this->query( $wpdb->prepare(
                'UPDATE `' . $wpdb->usermeta . '` SET `meta_key` = %s WHERE `meta_key` = %s',
                $new_name,
                $old_name
            ) );
        }
    }

    /**
     * Update user meta.
     *
     * @param array $meta
     */
    protected function updateUserMeta( array $meta )
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        foreach ( $meta as $meta_key => $value ) {
            $this->query( $wpdb->prepare(
                'UPDATE `' . $wpdb->usermeta . '` SET `meta_value` = %s WHERE `meta_key` = %s',
                $value,
                $meta_key
            ) );
        }
    }

    /**
     * Delete user meta.
     *
     * @param array $meta_names
     */
    protected function deleteUserMeta( array $meta_names )
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        $this->query( $wpdb->prepare( sprintf( 'DELETE FROM `' . $wpdb->usermeta . '` WHERE meta_key IN (%s)',
            implode( ', ', array_fill( 0, count( $meta_names ), '%s' ) ) ), $meta_names ) );
    }

    /**
     * Add options and register corresponding WPML strings.
     *
     * @param array $options
     */
    protected function addL10nOptions( array $options )
    {
        foreach ( $options as $option_name => $option_value ) {
            add_option( $option_name, $option_value );
            do_action( 'wpml_register_single_string', 'bookly', $option_name, $option_value );
        }
    }

    /**
     * This method allows one-time code execution,
     * at multiple calls to the same update_ * method, (for example, in case of timeout)
     *
     * @param string $token
     * @param callable $callable
     * @return string
     */
    protected function disposable( $token, $callable )
    {
        global $wpdb;

        $disposable_key = strtolower( strtok( __NAMESPACE__, '\\' ) ) . '_disposable_' . $token . '_completed';
        $completed = (int) get_option( $disposable_key );
        if ( $completed === 0 ) {
            $logs_table = $this->getTableName( 'bookly_log' );
            try {
                $wpdb->insert( $logs_table, array(
                    'action' => 'debug',
                    'target' => 'bookly-updater',
                    'author' => get_current_user_id(),
                    'details' => 'Disposable key: ' . $disposable_key,
                    'created_at' => current_time( 'mysql' ),
                ) );
            } catch ( \Exception $e ) {
            }
            $callable( $this );
            try {
                $wpdb->insert( $logs_table, array(
                    'action' => 'debug',
                    'target' => 'bookly-updater',
                    'author' => get_current_user_id(),
                    'details' => 'Disposable key: ' . $disposable_key . ' completed',
                    'created_at' => current_time( 'mysql' ),
                ) );
            } catch ( \Exception $e ) {
            }
            add_option( $disposable_key, '1' );
        }

        return $disposable_key;
    }

    /**
     * Rename WPML strings.
     *
     * @param array $strings
     * @param bool $rename_options
     */
    protected function renameL10nStrings( array $strings, $rename_options = true )
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        if ( $this->existsTable( 'icl_strings' ) ) {
            $wpml_strings_table = $this->getTableName( 'icl_strings' );
            // Check that `domain_name_context_md5` column exists.
            $exists = $this->query( $wpdb->prepare(
                'SELECT 1 FROM `INFORMATION_SCHEMA`.`COLUMNS`
                    WHERE `COLUMN_NAME`  = "domain_name_context_md5"
                      AND `TABLE_NAME`   = %s
                      AND `TABLE_SCHEMA` = SCHEMA()
                    LIMIT 1',
                $wpml_strings_table
            ) );
            if ( $exists ) {
                foreach ( $strings as $old_name => $new_name ) {
                    $this->query( $wpdb->prepare(
                        "UPDATE `$wpml_strings_table`
                          SET `name` = %s, `domain_name_context_md5` = MD5(CONCAT(`context`, %s, `gettext_context`))
                          WHERE `name` = %s",
                        $new_name,
                        $new_name,
                        $old_name
                    ) );
                }
            } else {
                foreach ( $strings as $old_name => $new_name ) {
                    $this->query( $wpdb->prepare(
                        "UPDATE `$wpml_strings_table` SET `name` = %s WHERE `name` = %s",
                        $new_name,
                        $old_name
                    ) );
                }
            }
        }

        if ( $rename_options ) {
            $this->renameOptions( $strings );
        }
    }

    /**
     * Upgrade character and collate for bookly tables.
     *
     * @param array $tables
     */
    protected function upgradeCharsetCollate( array $tables )
    {
        global $wpdb;
        // In WordPress 4.2, team upgraded wp tables to utf8mb4.
        if ( $wpdb->has_cap( 'collation' ) ) {
            // Bookly < 17.3 by default used CHARACTER SET = utf8 COLLATE = utf8_general_ci
            // mysql 5.5.3+ (2010) support utf8mb4
            // mysql 5.6+          support utf8mb4_520
            if ( $wpdb->charset ) {
                $query = sprintf( 'SELECT TABLE_NAME
                    FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_NAME IN (%s)
                     AND TABLE_SCHEMA = SCHEMA()
                     AND CHARACTER_SET_NAME IS NOT NULL
                     AND CHARACTER_SET_NAME != %%s',
                    implode( ', ', array_fill( 0, count( $tables ), '%s' ) )
                );
                $alter = 'ALTER TABLE `%s` CONVERT TO CHARACTER SET ' . $wpdb->charset;
                $params = array_map( array( $this, 'getTableName' ), $tables );
                $params[] = $wpdb->charset;
                if ( $wpdb->collate ) {
                    $query .= '
                     AND COLLATION_NAME IS NOT NULL
                     AND COLLATION_NAME != %s';
                    $params[] = $wpdb->collate;
                    $alter .= ' COLLATE ' . $wpdb->collate;
                }
                $query .= ' GROUP BY TABLE_NAME';
                $records = $wpdb->get_col( $wpdb->prepare( $query, $params ) );
                foreach ( $records as $table ) {
                    $this->query( sprintf( $alter, $table ) );
                }
            }
        }
    }

    /**
     * Add notifications.
     *
     * @param array $notifications
     */
    protected function addNotifications( $notifications )
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        $table = $this->getTableName( 'bookly_notifications' );
        foreach ( $notifications as $data ) {
            if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM `$table` WHERE `gateway` = %s AND `type` = %s", $data['gateway'], $data['type'] ) ) ) {
                do_action( 'wpml_register_single_string', 'bookly', $data['name'], $data['message'] );
                if ( $data['gateway'] == 'email' ) {
                    do_action( 'wpml_register_single_string', 'bookly', $data['name'] . '_subject', $data['subject'] );
                }
                $wpdb->insert( $table, $data );
            }
        }
    }

    /**
     * @param array $data
     * @return void
     */
    protected function createTables( $data )
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        $charset_collate = $wpdb->has_cap( 'collation' )
            ? $wpdb->get_charset_collate()
            : 'DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci';

        foreach ( $data as $table => $query ) {
            $this->query( sprintf( $query . ' ' . $charset_collate, $this->getTableName( $table ) ) );
        }
    }

    /**
     * WPDB query with error handling.
     *
     * @param $query
     * @return bool|int|\mysqli_result|resource|null
     */
    protected function query( $query )
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        $result = $wpdb->query( $query );
        if ( $wpdb->last_error ) {
            $method = '';
            try {
                $debug_backtrace = debug_backtrace();
                foreach ( $debug_backtrace as $trace ) {
                    if ( isset( $trace['function'] ) && strpos( $trace['function'], 'update_' ) === 0 ) {
                        $method = $trace['class'] . '::' . $trace['function'];
                        break;
                    }
                }
            } catch ( \Exception $e ) {
            }
            $this->errors[] = array(
                'method' => $method,
                'query' => $query,
                'error' => $wpdb->last_error,
            );
        }

        return $result;
    }
}