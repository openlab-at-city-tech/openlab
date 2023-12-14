<?php
namespace Bookly\Backend\Modules\Diagnostics;

class Schema
{
    /** @var string MySQL | MariaDB | Percona | ? */
    protected $server;

    /**
     * Get table constraints
     *
     * @param string $table
     * @return array
     */
    public function getTableConstraints( $table )
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        static $tableConstraints = array();
        if ( empty( $tableConstraints ) ) {
            $records = $wpdb->get_results(
                'SELECT TABLE_NAME
                  , COLUMN_NAME
                  , CONSTRAINT_NAME
                  , REFERENCED_COLUMN_NAME
                  , REFERENCED_TABLE_NAME
               FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
              WHERE REFERENCED_TABLE_NAME IS NOT NULL
                AND CONSTRAINT_SCHEMA = SCHEMA()
                AND CONSTRAINT_NAME <> "PRIMARY";'
            );
            if ( $records ) {
                foreach ( $records as $row ) {
                    $constraint = array(
                        'column_name' => $row->COLUMN_NAME,
                        'referenced_table_name' => $row->REFERENCED_TABLE_NAME,
                        'referenced_column_name' => $row->REFERENCED_COLUMN_NAME,
                        'constraint_name' => $row->CONSTRAINT_NAME,
                        'reference_exists' => $this->existsColumn( $row->REFERENCED_TABLE_NAME, $row->REFERENCED_COLUMN_NAME ),
                    );
                    $key = $row->COLUMN_NAME . $row->REFERENCED_TABLE_NAME . $row->REFERENCED_COLUMN_NAME;
                    $tableConstraints[ $row->TABLE_NAME ][ $key ] = $constraint;
                }
            }
        }

        return array_key_exists( $table, $tableConstraints )
            ? $tableConstraints[ $table ]
            : array();
    }

    /**
     * Check exists table
     *
     * @param string $table
     * @return bool
     */
    public function existsTable( $table )
    {
        global $wpdb;

        return (bool) $wpdb->query(
            $wpdb->prepare(
                'SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = %s AND TABLE_SCHEMA = SCHEMA() LIMIT 1',
                $table
            )
        );
    }

    /**
     * Get table structure
     *
     * @param string $table
     * @param bool $use_cache
     * @return array
     */
    public function getTableStructure( $table,  $use_cache = true )
    {
        global $wpdb;

        static $tableStructure = array();
        if ( empty( $tableStructure ) || ! $use_cache ) {
            $results = $wpdb->get_results( $wpdb->prepare( 'SELECT TABLE_NAME, COLUMN_NAME, 
            CASE 
                WHEN DATA_TYPE IN( \'smallint\', \'int\', \'bigint\' ) THEN CONCAT( DATA_TYPE, IF(COLUMN_TYPE LIKE \'%%unsigned\', \' unsigned\', \'\'))
                ELSE COLUMN_TYPE
            END AS DATA_TYPE, 
            IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT, EXTRA, CHARACTER_SET_NAME, COLLATION_NAME
         FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = SCHEMA()
          AND TABLE_NAME LIKE %s
     ORDER BY TABLE_NAME, ORDINAL_POSITION', str_replace( '_', '\_', $wpdb->prefix . 'bookly_%' ) ), ARRAY_A );
            if ( $results ) {
                foreach ( $results as $row ) {
                    $tableStructure[ $row['TABLE_NAME'] ][ $row['COLUMN_NAME'] ] = $this->getColumnStructure( $row );
                }
            }
        }

        return array_key_exists( $table, $tableStructure )
            ? $tableStructure[ $table ]
            : array();
    }

    protected function getColumnStructure( $data )
    {
        switch ( $this->getServer() ) {
            case 'MariaDB':
                $default = trim( $data['COLUMN_DEFAULT'], '\'' );
                // MariaDB 10.3.22
                if ( strtolower( $default ) === 'null' ) {
                    $default = null;
                }

                $type = $data['DATA_TYPE'];
                // MariaDB 10.0.38
                if ( $type === 'mediumtext' ) {
                    $type = 'text';
                }
                break;
            case 'MySql':
            default:
                $default = $data['COLUMN_DEFAULT'];
                $type = $data['DATA_TYPE'];
                // 5.6.32-1+deb.sury.org~precise+0.1
                if ( $type === 'mediumtext' ) {
                    $type = 'text';
                }
        }

        return array(
            'type' => $type,
            'is_nullabe' => $data['IS_NULLABLE'] === 'YES' ? 1 : 0,
            'extra' => $data['EXTRA'],
            'default' => $default,
            'key' => $data['COLUMN_KEY'],
            'character_set' => $data['CHARACTER_SET_NAME'],
            'collation' => $data['COLLATION_NAME'],
        );
    }

    private function getServer()
    {
        if ( $this->server === null ) {
            global $wpdb;

            $this->server = 'MySql';
            $version = $wpdb->get_row( 'SELECT version() AS version', ARRAY_A );
            if ( strpos( $version['version'], 'MariaDB' ) !== false ) {
                $this->server = 'MariaDB';
            } elseif ( strpos( $version['version'], 'Percona' ) !== false ) {
                $this->server = 'Percona';
            }
        }

        return $this->server;
    }

    /**
     * Check exists column in table
     *
     * @param string $table
     * @param string $column_name
     * @return bool
     */
    protected function existsColumn( $table, $column_name )
    {
        global $wpdb;

        return (bool) $wpdb->query(
            $wpdb->prepare(
                'SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = %s AND COLUMN_NAME = %s AND TABLE_SCHEMA = SCHEMA() LIMIT 1',
                $table,
                $column_name
            )
        );
    }
}