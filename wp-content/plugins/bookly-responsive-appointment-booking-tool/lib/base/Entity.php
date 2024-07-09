<?php
namespace Bookly\Lib\Base;

use Bookly\Lib;

abstract class Entity extends Cache
{

    // Entity properties

    /**
     * Entity field id
     *
     * @var   int
     */
    protected $id;

    // Protected static properties

    /**
     * Reference to global database object.
     *
     * @var \wpdb
     */
    protected static $wpdb;

    /**
     * Name of table in database without WordPress prefix.
     * Must be defined in the child class.
     *
     * @static
     * @var string
     */
    protected static $table;

    /**
     * Schema of entity fields in database.
     * Must be defined in the child class as
     * array(
     *     '[FIELD_NAME]' => array(
     *         'format'    => '[FORMAT]',
     *         'default'   => '[DEFAULT_VALUE]',
     *         'reference' => '[ [entity], [namespace], [required] ]'
     * )
     *
     * @static
     * @var array
     */
    protected static $schema;

    /**
     * Is entity operations should be logged.
     */
    protected $loggable = false;

    // Private properties.

    /**
     * Name of table in database with WordPress prefix.
     *
     * @var string
     */
    private $table_name;

    /**
     * Values loaded from the database.
     *
     * @var boolean
     */
    private $loaded_values;

    // Public methods.

    /**
     * Constructor
     *
     * @param array $fields
     */
    public function __construct( $fields = array() )
    {
        if ( self::$wpdb === null ) {
            /** @var \wpdb $wpdb */
            global $wpdb;

            self::$wpdb = $wpdb;
        }

        $this->table_name = static::getTableName();

        $this->setFields( $fields );
    }

    /**
     * Load entity from database by ID.
     *
     * @param integer $id
     * @return boolean
     */
    public function load( $id )
    {
        return $this->loadBy( array( 'id' => $id ) );
    }

    /**
     * Load entity from database by field values.
     *
     * @param array $fields
     * @return bool
     */
    public function loadBy( array $fields )
    {
        // Prepare WHERE clause.
        $where = array();
        $values = array();
        foreach ( $fields as $field => $value ) {
            if ( $value === null ) {
                $where[] = sprintf( '`%s` IS NULL', $field );
            } else {
                $where[] = sprintf( '`%s` = %s', $field, static::$schema[ $field ]['format'] );
                $values[] = $value;
            }
        }

        $query = sprintf(
            'SELECT * FROM `%s` WHERE %s LIMIT 1',
            $this->table_name,
            implode( ' AND ', $where )
        );

        $row = self::$wpdb->get_row(
            empty ( $values ) ? $query : self::$wpdb->prepare( $query, $values )
        );

        if ( $row ) {
            $this->setFields( $row );
            $this->loaded_values = $this->getFields();
        } else {
            $this->loaded_values = null;
        }

        return $this->isLoaded();
    }

    /**
     * Check whether the entity was loaded from the database or not.
     *
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded_values !== null;
    }

    /**
     * Check if the entity operations should be logged.
     *
     * @return bool
     */
    public function isLoggable()
    {
        return $this->loggable === true;
    }

    /**
     * Set values to fields.
     * The method can be used to update only some fields.
     *
     * @param array|\stdClass $data
     * @param bool $overwrite_loaded_values
     * @return $this
     */
    public function setFields( $data, $overwrite_loaded_values = false )
    {
        if ( $data = (array) $data ) {
            foreach ( static::$schema as $field => $meta ) {
                if ( array_key_exists( $field, $data ) ) {
                    $this->{$field} = $data[ $field ];
                }
            }
        }

        // This parameter is used by \Bookly\Lib\Query.
        if ( $overwrite_loaded_values ) {
            $this->loaded_values = $this->getFields();
        }

        return $this;
    }

    /**
     * Get values of fields as array.
     *
     * @return array
     */
    public function getFields()
    {
        $data = array();
        foreach ( static::$schema as $field => $format ) {
            $data[ $field ] = $this->{$field};
        }

        return $data;
    }

    /**
     * Get modified fields with initial values.
     *
     * @return array
     */
    public function getModified()
    {
        return array_diff_assoc( $this->loaded_values ?: array(), $this->getFields() );
    }

    /**
     * Save entity to database.
     *
     * @return int|false
     */
    public function save()
    {
        // Prepare query data.
        $set = array();
        $values = array();
        foreach ( static::$schema as $field => $data ) {
            if ( $field == 'id' ) {
                continue;
            }
            $value = $this->{$field};
            if ( $value === null ) {
                if ( isset( static::$schema[ $field ]['sequent'] ) && static::$schema[ $field ]['sequent'] ) {
                    // Set greater than max value
                    $set[] = sprintf( '`%s` = %s', $field, static::$schema[ $field ]['format'] );
                    $max = (int) self::$wpdb->get_var( sprintf( 'SELECT MAX(`%s`) FROM `%s`', $field, $this->table_name ) );
                    $values[] = ++ $max;
                } else {
                    $set[] = sprintf( '`%s` = NULL', $field );
                }
            } else {
                $set[] = sprintf( '`%s` = %s', $field, static::$schema[ $field ]['format'] );
                $values[] = $value;
            }
        }
        // Run query.
        if ( $this->getId() ) {
            Lib\Utils\Log::fromBacktrace( $this );
            $res = self::$wpdb->query( self::$wpdb->prepare(
                sprintf(
                    'UPDATE `%s` SET %s WHERE `id` = %d',
                    $this->table_name,
                    implode( ', ', $set ),
                    $this->getId()
                ),
                $values
            ) );
        } else {
            $res = self::$wpdb->query( self::$wpdb->prepare(
                sprintf(
                    'INSERT INTO `%s` SET %s',
                    $this->table_name,
                    implode( ', ', $set )
                ),
                $values
            ) );
            if ( $res ) {
                $this->setId( self::$wpdb->insert_id );
                Lib\Utils\Log::fromBacktrace( $this, Lib\Utils\Log::ACTION_CREATE );
            }
        }

        if ( $res ) {
            // Update loaded values.
            $this->loaded_values = $this->getFields();
        }

        return $res;
    }

    /**
     * Delete entity from database.
     *
     * @return false|int
     */
    public function delete()
    {
        if ( $this->getId() ) {
            Lib\Utils\Log::fromBacktrace( $this, Lib\Utils\Log::ACTION_DELETE );
            static::deleteFromCache( $this->getId() );

            return self::$wpdb->delete( $this->table_name, array( 'id' => $this->getId() ), array( '%d' ) );
        }

        return false;
    }

    /**
     * Get table name.
     *
     * @static
     * @return string
     */
    public static function getTableName()
    {
        global $wpdb;

        return $wpdb->prefix . static::$table;
    }

    /**
     * Get schema.
     *
     * @static
     * @return array
     */
    public static function getSchema()
    {
        return static::$schema;
    }

    /**
     * Get table foreign key constraints
     *
     * @static
     * @return array
     */
    public static function getConstraints()
    {
        $constraints = array();
        foreach ( static::$schema as $field_name => $options ) {
            if ( array_key_exists( 'reference', $options ) ) {
                if ( array_key_exists( 'required', $options['reference'] ) ) {
                    $addon = str_replace( ' ', '', ucwords( str_replace( array( 'bookly-addon-', '-' ), array( '', ' ' ), $options['reference']['required'] ) ) );
                    // Check if required addon is active
                    $add_reference = Lib\Config::{lcfirst( $addon . 'Active' )}();
                } else {
                    $add_reference = true;
                }
                if ( $add_reference ) {
                    $ref_entity = $options['reference']['entity'];
                    if ( isset ( $options['reference']['namespace'] ) ) {
                        $ref_entity = $options['reference']['namespace'] . '\\' . $ref_entity;
                        if ( ! class_exists( $ref_entity ) ) {
                            continue;
                        }
                    } else {
                        $called_class = get_called_class();
                        $ref_entity = substr( $called_class, 0, strrpos( $called_class, '\\' ) ) . '\\' . $ref_entity;
                    }
                    $constraints[] = array(
                        'column_name' => $field_name,
                        'referenced_table_name' => call_user_func( array( $ref_entity, 'getTableName' ) ),
                        'referenced_column_name' => isset ( $options['reference']['field'] ) ? $options['reference']['field'] : 'id',
                    );
                }
            }
        }

        return $constraints;
    }

    /**
     * Create query for this entity.
     *
     * @param $alias
     * @return Lib\Query
     */
    public static function query( $alias = 'r' )
    {
        return new Lib\Query( get_called_class(), $alias );
    }

    /**
     * Find entity by id possibly using cache.
     *
     * @param $id
     * @param bool|true $use_cache
     * @return static|false
     */
    public static function find( $id, $use_cache = true )
    {
        $called_class = get_called_class();

        if ( $use_cache && $entity = static::getFromCache( $id ) ) {
            return $entity;
        }

        /** @var static $entity */
        $entity = new $called_class();
        if ( $entity->loadBy( array( 'id' => $id ) ) ) {
            if ( $use_cache ) {
                static::putInCache( $id, $entity );
            }

            return $entity;
        }

        return false;
    }

    /**
     * Gets id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets id
     *
     * @param int $id
     * @return $this
     */
    public function setId( $id )
    {
        $this->id = $id;

        return $this;
    }

    public function __call( $name, $arguments )
    {
        Lib\Utils\Log::put( Lib\Utils\Log::ACTION_ERROR, 'call unknown method', null, json_encode( $arguments ), null, get_called_class() . '::' . $name );
    }
}