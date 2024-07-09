<?php
namespace Bookly\Backend\Modules\Diagnostics\Tools;

use Bookly\Lib;
use Bookly\Backend\Modules\Diagnostics\Schema;
use Bookly\Backend\Modules\Diagnostics\QueryBuilder;

class Database extends Tool
{
    protected $slug = 'database';
    protected $hidden = true;

    protected $troubles;
    protected $fixable = false;
    protected $error = 0;
    public $position = 20;

    public function __construct()
    {
        $this->title = 'Database tools';
    }

    public function render()
    {
        $this->processDB();

        return self::renderTemplate( '_database', array( 'fixable' => $this->fixable, 'troubles' => $this->troubles ), false );
    }

    public function hasError()
    {
        $this->processDB();

        return ! empty( $this->troubles );
    }

    public function fixDatabaseSchema()
    {
        $errors = array();
        $queries = 0;
        $schema = new Schema();
        /** @var Lib\Base\Plugin $plugin */
        foreach ( apply_filters( 'bookly_plugins', array() ) as $plugin ) {
            foreach ( $plugin::getEntityClasses() as $entity_class ) {
                $table_name = $entity_class::getTableName();
                $use_cache = true;
                if ( ! $schema->existsTable( $table_name ) ) {
                    $queries++;
                    $success = $this->executeSql( QueryBuilder::getCreateTable( $table_name ) );
                    if ( $success === true ) {
                        $use_cache = false;
                        Lib\Utils\Log::put( Lib\Utils\Log::ACTION_DEBUG, 'Schema', null, '', null, 'create table ' . $table_name );
                    } else {
                        $errors[] = sprintf( 'Can`t create table <b>%s</b>, Error:%s', $table_name, $success );
                    }
                }
                if ( $schema->existsTable( $table_name ) ) {
                    $table_structure = $schema->getTableStructure( $table_name, $use_cache );
                    $entity_schema = $entity_class::getSchema();

                    // Comparing model schema with real DB schema
                    foreach ( $entity_schema as $column => $data ) {
                        if ( array_key_exists( $column, $table_structure ) ) {
                            $expect = QueryBuilder::getColumnData( $table_name, $column );
                            $actual = $table_structure[ $column ];
                            unset( $expect['key'], $actual['key'], $actual['character_set'], $actual['collation'] );
                            if ( $expect && array_diff_assoc( $actual, $expect ) ) {
                                $sql = QueryBuilder::getChangeColumn( $table_name, $column );
                                if ( $table_structure[ $column ]['key'] == 'PRI' ) {
                                    $sql = str_replace( ' primary key', '', $sql );
                                }
                                $queries++;
                                $success = $this->executeSql( $sql );
                                if ( $success !== true ) {
                                    $errors[] = sprintf( 'Can`t change column <b>%s.%s</b>, Error:%s', $table_name, $column, $success );
                                } else {
                                    Lib\Utils\Log::put( Lib\Utils\Log::ACTION_DEBUG, 'Schema', null, $sql, 'differences:' . PHP_EOL . json_encode( array_diff_assoc( $actual, $expect ), JSON_PRETTY_PRINT ), 'change column ' . $table_name . '.' . $column );
                                }
                            }
                        } else {
                            $queries++;
                            $sql = QueryBuilder::getAddColumn( $table_name, $column );
                            $success = $this->executeSql( $sql );
                            if ( $success !== true ) {
                                if ( $this->error === 1118 ) {
                                    $queries++;
                                    if ( $this->executeSql( 'OPTIMIZE TABLE `' . $table_name . '`' ) ) {
                                        $queries++;
                                        $success = $this->executeSql( $sql );
                                    }
                                }
                                if ( $success !== true ) {
                                    $errors[] = sprintf( 'Can`t add column <b>%s.%s</b>, Error:%s', $table_name, $column, $success );
                                }
                            }
                            Lib\Utils\Log::put( Lib\Utils\Log::ACTION_DEBUG, 'Schema', null, $sql, null, 'add column ' . $table_name . '.' . $column );
                        }
                    }
                }
            }

            foreach ( $plugin::getEntityClasses() as $entity_class ) {
                $table_name = $entity_class::getTableName();
                if ( $schema->existsTable( $table_name ) ) {
                    $entity_constraints = $entity_class::getConstraints();
                    $table_constraints = $schema->getTableConstraints( $table_name );
                    // Comparing model constraints with real DB constraints
                    foreach ( $entity_constraints as $constraint ) {
                        $key = $constraint['column_name'] . $constraint['referenced_table_name'] . $constraint['referenced_column_name'];
                        if ( ! array_key_exists( $key, $table_constraints ) ) {
                            $query = QueryBuilder::getAddConstraint( $table_name, $constraint['column_name'], $constraint['referenced_table_name'], $constraint['referenced_column_name'] );
                            if ( $query !== '' ) {
                                $queries++;
                                $success = $this->executeSql( $query );
                                if ( $success !== true ) {
                                    $errors[] = sprintf( 'Can`t add constraint <b>%s.%s</b> REFERENCES `%s` (`%s`), Error:%s', $table_name, $constraint['column_name'], $constraint['referenced_table_name'], $constraint['referenced_column_name'], $success );
                                } else {
                                    Lib\Utils\Log::put( Lib\Utils\Log::ACTION_DEBUG, 'Schema', null, $query, null, 'add constraint ' . $table_name . '.' . $constraint['column_name'] . ' -> ' . $constraint['referenced_table_name'] . '.' . $constraint['referenced_column_name'] );
                                }
                            }
                        }
                    }

                    foreach ( $table_constraints as $constraint ) {
                        if ( $constraint['reference_exists'] === false ) {
                            $queries++;
                            $success = $this->executeSql( QueryBuilder::getDropForeignKey( $table_name, $constraint['constraint_name'] ) );
                            if ( $success !== true ) {
                                $errors[] = sprintf( 'Can`t drop foreign key <b>%s</b>, Error:%s', $constraint['constraint_name'], $success );
                            }
                        }
                    }
                }
            }
        }

        $message = ( $queries - count( $errors ) ) . ' queries completed successfully, with errors ' . count( $errors );
        $errors
            ? wp_send_json_error( compact( 'errors', 'message' ) )
            : wp_send_json_success( compact( 'message' ) );
    }

    private function processDB()
    {
        if ( $this->troubles === null ) {
            $troubles = array();
            if ( ! self::hasParameter( 'x' ) ) {
                /** @var Lib\Base\Plugin $plugin */
                foreach ( apply_filters( 'bookly_plugins', array() ) as $plugin ) {
                    foreach ( $plugin::getEntityClasses() as $entity_class ) {
                        $trouble = $this->getTroubles( $entity_class );
                        if ( $trouble ) {
                            $troubles[ $entity_class::getTableName() ] = $trouble;
                        }
                    }
                }
            }
            ksort( $troubles );

            $this->troubles = $troubles;
        }
    }

    /**
     * @param Lib\Base\Entity $entity_class
     * @return array
     */
    private function getTroubles( $entity_class )
    {
        $troubles = array();
        if ( ! $entity_class ) {
            return $troubles;
        }

        /** @var \wpdb $wpdb */
        global $wpdb;
        $schema = new Schema();
        $table_name = $entity_class::getTableName();
        if ( $schema->existsTable( $table_name ) ) {
            $table_structure = $schema->getTableStructure( $table_name );
            $table_constraints = $schema->getTableConstraints( $table_name );
            $entity_schema = $entity_class::getSchema();
            $entity_constraints = $entity_class::getConstraints();
            $valid_character_set = true;
            $valid_collation = true;
            // Comparing model schema with real DB schema
            foreach ( $entity_schema as $field => $data ) {
                if ( array_key_exists( $field, $table_structure ) ) {
                    $expect = QueryBuilder::getColumnData( $table_name, $field );
                    $actual = $table_structure[ $field ];
                    if ( $valid_character_set
                        && isset( $actual['character_set'] )
                        && $actual['character_set'] !== $wpdb->charset ) {
                        $valid_character_set = false;
                    }
                    if ( $valid_collation
                        && isset( $actual['collation'] )
                        && $actual['collation'] !== $wpdb->collate ) {
                        $valid_collation = false;
                    }

                    if ( isset( $actual['character_set'] ) ) {
                        $expect['character_set'] = $wpdb->charset;
                    } else {
                        unset ( $actual['character_set'] );
                    }
                    if ( isset( $actual['collation'] ) ) {
                        $expect['collation'] = $wpdb->collate;
                    } else {
                        unset ( $actual['collation'] );
                    }
                    unset( $expect['key'], $actual['key'] );
                    $diff = array_diff_assoc( $actual, $expect );
                    if ( isset( $diff['collation'] ) && ! isset( $diff['character_set'] ) ) {
                        // don't show collation without character_set
                        unset( $diff['collation'] );
                    }
                    if ( $expect && $diff ) {
                        $this->fixable = true;
                        $troubles['fields']['diff'][] = array( 'title' => $field, 'data' => array( 'diff' => array_keys( $diff ) ) );
                    }
                } else {
                    $this->fixable = true;
                    $troubles['fields']['missing'][] = array( 'title' => $field );
                }
                unset( $table_structure[ $field ] );
            }
            foreach ( $table_structure as $field => $data ) {
                $troubles['fields']['unknown'][] = array( 'title' => $field );
            }
            $exist_constraints = array();
            // Comparing model constraints with real DB constraints
            foreach ( $entity_constraints as $constraint ) {
                $key = $constraint['column_name'] . $constraint['referenced_table_name'] . $constraint['referenced_column_name'];
                if ( array_key_exists( $key, $table_constraints ) ) {
                    $exist_constraints[] = $key;
                } else {
                    $this->fixable = true;
                    $troubles['constraints']['missing'][] = array(
                        'title' => $constraint['column_name'] . ' - ' . $constraint['referenced_table_name'] . '.' . $constraint['referenced_column_name'],
                        'data' => array(
                            'column' => $constraint['column_name'],
                            'ref_table_name' => $constraint['referenced_table_name'],
                            'ref_column_name' => $constraint['referenced_column_name'],
                            'rules' => QueryBuilder::getConstraintRules( $table_name, $constraint['column_name'], $constraint['referenced_table_name'], $constraint['referenced_column_name'] ),
                        ),
                    );
                }
            }
            foreach ( $table_constraints as $constraint ) {
                $key = $constraint['column_name'] . $constraint['referenced_table_name'] . $constraint['referenced_column_name'];
                if ( ! in_array( $key, $exist_constraints ) ) {
                    $troubles['constraints']['unknown'][] = array(
                        'title' => $constraint['column_name'] . ' - ' . $constraint['referenced_table_name'] . '.' . $constraint['referenced_column_name'],
                        'data' => array(
                            'key' => $constraint['constraint_name'],
                        ),
                    );
                }
            }
            if ( ! $valid_character_set ) {
                $character = array( 'title' => 'character_set', 'data' => array( 'character_set' ) );
                if ( ! $valid_collation ) {
                    $character['data'][] = 'collation';
                    $this->fixable = true;
                }
                $troubles['tables']['character'][] = $character;
            }

        } else {
            $troubles['missing'] = true;
            $this->fixable = true;
        }

        return $troubles;
    }

    public function executeJob()
    {
        global $wpdb;
        $success = 'Fail';
        $class = null;
        list( $table_name, $fix, $trouble, $target ) = explode( '~', self::parameter( 'job' ) );
        if ( QueryBuilder::isBooklyTable( $table_name ) ) {
            foreach ( apply_filters( 'bookly_plugins', array() ) as $plugin ) {
                foreach ( $plugin::getEntityClasses() as $entity_class ) {
                    if ( $table_name == $entity_class::getTableName() ) {
                        $class = $entity_class;
                        break 2;
                    }
                }
            }
            $troubles = $this->getTroubles( $class );
            if ( isset( $troubles[ $fix ][ $trouble ] ) ) {
                foreach ( $troubles[ $fix ][ $trouble ] as $value ) {
                    if ( $value['title'] == $target ) {
                        switch ( $trouble ) {
                            case 'unknown':
                                if ( $fix === 'fields' ) {
                                    if ( ! QueryBuilder::getColumnData( $table_name, $target ) ) {
                                        $success = $this->dropColumn( $table_name, $target );
                                    }
                                } elseif ( $fix === 'constraints' ) {
                                    $success = $this->executeSql( sprintf( 'ALTER TABLE `%s` DROP FOREIGN KEY `%s`', $table_name, $value['data']['key'] ) );
                                }
                                break 2;
                            case 'missing':
                                if ( $fix === 'constraints' ) {
                                    $parts = explode( '~', self::parameter( 'job' ) );
                                    if ( count( $parts ) === 5 ) {
                                        switch ( $parts[4] ) {
                                            case 'delete':
                                                $this->executeSql( sprintf( 'DELETE FROM `%s` WHERE `%s` NOT IN ( SELECT `%s` FROM `%s` )',
                                                    $table_name, $value['data']['column'], $value['data']['ref_column_name'], $value['data']['ref_table_name']
                                                ) );
                                                break;
                                            case 'update':
                                                $this->executeSql( sprintf( 'UPDATE `%s` SET `%s` = NULL WHERE `%s` NOT IN ( SELECT `%s` FROM `%s` )',
                                                    $table_name, $value['data']['column'], $value['data']['column'], $value['data']['ref_column_name'], $value['data']['ref_table_name']
                                                ) );
                                                break;
                                            case 'custom':
                                                $rules = QueryBuilder::getConstraintRules( $table_name, $value['data']['column'], $value['data']['ref_table_name'], $value['data']['ref_column_name'] );
                                                $method = $rules['fix']['method'];
                                                try {
                                                    $missing = $wpdb->get_col( sprintf( 'SELECT `%1$s` FROM `%2$s` WHERE `%1$s` NOT IN (SELECT `%3$s` FROM `%4$s`)', $value['data']['column'], $table_name, $value['data']['ref_column_name'], $value['data']['ref_table_name'] ) );
                                                    $method( $table_name, $value['data']['column'], $value['data']['ref_table_name'], $value['data']['ref_column_name'], $wpdb, $missing );
                                                } catch ( \Exception $e ) {}
                                                break;
                                        }
                                    }
                                    $success = $this->executeSql( sprintf( 'ALTER TABLE `%s` ADD CONSTRAINT FOREIGN KEY (`%s`) REFERENCES `%s` (`%s`) ON DELETE %s ON UPDATE %s',
                                        $table_name, $value['data']['column'], $value['data']['ref_table_name'], $value['data']['ref_column_name'], $value['data']['rules']['DELETE_RULE'], $value['data']['rules']['UPDATE_RULE']
                                    ) );
                                }
                                break 2;
                            case 'character':
                                $query = 'ALTER TABLE `' . $table_name . '` ';
                                if ( in_array( 'character_set', $value['data'] ) ) {
                                    $query .= ' CONVERT TO CHARACTER SET ' . $wpdb->charset;
                                }
                                if ( in_array( 'collation', $value['data'] ) ) {
                                    $query .= ' COLLATE ' . $wpdb->collate;
                                }
                                $success = $this->executeSql( $query );
                                break 2;
                        }
                    }
                }
            }
        }
        if ( $success === true ) {
            wp_send_json_success( array( 'message' => 'Query completed successfully' ) );
        } else {
            wp_send_json_error( array( 'message' => $success ) );
        }
    }

    private function dropColumn( $table, $column )
    {
        global $wpdb;

        $get_foreign_keys = sprintf(
            'SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = SCHEMA() AND TABLE_NAME = "%s" AND COLUMN_NAME = "%s" AND REFERENCED_TABLE_NAME IS NOT NULL',
            $table,
            $column
        );
        $constraints = $wpdb->get_results( $wpdb->prepare( $get_foreign_keys, $column ) );
        foreach ( $constraints as $foreign_key ) {
            $wpdb->query( "ALTER TABLE `$table` DROP FOREIGN KEY `$foreign_key->CONSTRAINT_NAME`" );
        }

        $query = 'ALTER TABLE `' . $table . '` DROP COLUMN `' . $column . '`';

        return $this->executeSql( $query );
    }

    /**
     * @param string $sql
     * @return bool|string
     */
    private function executeSql( $sql )
    {
        global $wpdb;

        ob_start();
        $result = $wpdb->query( $sql );
        /** @var \mysqli $dd */
        $this->error = $wpdb->dbh->errno;
        ob_end_clean();

        return $result === false
            ? $wpdb->last_error
            : true;
    }
}