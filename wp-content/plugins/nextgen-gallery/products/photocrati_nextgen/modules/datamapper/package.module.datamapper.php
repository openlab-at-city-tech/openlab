<?php
/**
 * Class A_DataMapper_Factory
 * @mixin C_Component_Factory
 * @adapts I_Component_Factory
 */
class A_DataMapper_Factory extends Mixin
{
    function datamapper_model($mapper, $properties = array(), $context = FALSE)
    {
        return new C_DataMapper_Model($mapper, $properties = array(), $context);
    }
    function datamapper($object_name, $context = FALSE)
    {
        return new C_DataMapper($object_name, $context);
    }
    function custom_table_datamapper($object_name, $context = FALSE)
    {
        return new C_CustomTable_DataMapper_Driver($object_name, $context);
    }
    function custom_post_datamapper($object_name, $context = FALSE)
    {
        return new C_CustomPost_DataMapper_Driver($object_name, $context);
    }
}
/**
 * Class C_DataMapper_Driver_Base
 * @mixin Mixin_DataMapper_Driver_Base
 * @implements I_DataMapper_Driver
 */
class C_DataMapper_Driver_Base extends C_Component
{
    var $_object_name;
    var $_model_factory_method = FALSE;
    var $_columns = array();
    var $_table_columns = array();
    var $_serialized_columns = array();
    function define($object_name = FALSE, $context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_DataMapper_Driver_Base');
        $this->implement('I_DataMapper_Driver');
        $this->_object_name = $object_name;
    }
    function initialize()
    {
        parent::initialize();
        $this->_cache = array();
        if ($this->has_method('define_columns')) {
            $this->define_columns();
        }
        $this->lookup_columns();
    }
    /**
     * Gets the object name
     * @return string
     */
    function get_object_name()
    {
        return $this->_object_name;
    }
    /**
     * Gets the name of the table
     * @global string $table_prefix
     * @return string
     */
    function get_table_name()
    {
        global $table_prefix;
        global $wpdb;
        $prefix = $table_prefix;
        if ($wpdb != null && $wpdb->prefix != null) {
            $prefix = $wpdb->prefix;
        }
        return apply_filters('ngg_datamapper_table_name', $prefix . $this->_object_name, $this->_object_name);
    }
    /**
     * Looks up using SQL the columns existing in the database, result is cached
     */
    function lookup_columns()
    {
        // Avoid doing multiple SHOW COLUMNS if we can help it
        $key = C_Photocrati_Transient_Manager::create_key('col_in_' . $this->get_table_name(), 'columns');
        $this->_table_columns = C_Photocrati_Transient_Manager::fetch($key, FALSE);
        if (!$this->_table_columns) {
            $this->object->update_columns_cache();
        }
        return $this->_table_columns;
    }
    /**
     * Looks up using SQL the columns existing in the database
     */
    function update_columns_cache()
    {
        $key = C_Photocrati_Transient_Manager::create_key('col_in_' . $this->get_table_name(), 'columns');
        global $wpdb;
        $this->_table_columns = array();
        $sql = "SHOW COLUMNS FROM `{$this->get_table_name()}`";
        foreach ($wpdb->get_results($sql) as $row) {
            $this->_table_columns[] = $row->Field;
        }
        C_Photocrati_Transient_Manager::update($key, $this->_table_columns);
    }
    /**
     * Determines whether a column is present for the table
     * @param string $column_name
     * @return bool
     */
    function has_column($column_name)
    {
        if (empty($this->object->_table_columns)) {
            $this->object->lookup_columns();
        }
        return array_search($column_name, $this->object->_table_columns) !== FALSE;
    }
    /**
     * Sets the name of the factory method used to create a model for this entity
     * @param string $method_name
     */
    function set_model_factory_method($method_name)
    {
        $this->_model_factory_method = $method_name;
    }
    /**
     * Gets the name of the factory method used to create a model for this entity
     */
    function get_model_factory_method()
    {
        return $this->_model_factory_method;
    }
    /**
     * Gets the name of the primary key column
     * @return string
     */
    function get_primary_key_column()
    {
        return $this->_primary_key_column;
    }
    /**
     * Gets the class name of the driver used
     * @return string
     */
    function get_driver_class_name()
    {
        return get_called_class();
    }
    function cache($key, $results)
    {
        if ($this->object->_use_cache) {
            $this->_cache[$key] = $results;
        }
    }
    function get_from_cache($key, $default = NULL)
    {
        if ($this->object->_use_cache && isset($this->_cache[$key])) {
            return $this->_cache[$key];
        } else {
            return $default;
        }
    }
    function flush_query_cache()
    {
        $this->_cache = array();
    }
    /**
     * Used to clean column or table names in a SQL query
     * @param string $val
     * @return string
     */
    function _clean_column($val)
    {
        return str_replace(array(';', "'", '"', '`'), array(''), $val);
    }
    /**
     * Notes that a particular columns is serialized, and should be unserialized when converted to an entity
     * @param $column
     */
    function add_serialized_column($column)
    {
        $this->object->_serialized_columns[] = $column;
    }
    function unserialize_columns($object)
    {
        foreach ($this->object->_serialized_columns as $column) {
            if (isset($object->{$column}) && is_string($object->{$column})) {
                $object->{$column} = C_NextGen_Serializable::unserialize($object->{$column});
            }
        }
    }
    /**
     * Fetches the first row
     * @param array $conditions (optional)
     * @param object|bool $model (optional)
     * @return null|object
     */
    function find_first($conditions = array(), $model = FALSE)
    {
        $results = $this->object->select()->where_and($conditions)->limit(1, 0)->run_query();
        if ($results) {
            return $model ? $this->object->convert_to_model($results[0]) : $results[0];
        } else {
            return NULL;
        }
    }
    /**
     * Queries all rows
     * @param array $conditions (optional)
     * @param object|bool $model (optional)
     * @return array
     */
    function find_all($conditions = array(), $model = FALSE)
    {
        // Sometimes users will forget that the first parameter is conditions, and think it's $model instead
        if ($conditions === TRUE) {
            $conditions = array();
            $model = TRUE;
        }
        if ($conditions === FALSE) {
            $conditions = array();
        }
        $results = $this->object->select()->where_and($conditions)->run_query();
        if ($results && $model) {
            foreach ($results as &$r) {
                $r = $this->object->convert_to_model($r);
            }
        }
        return $results;
    }
    /**
     * Filters the query using conditions:
     * E.g.
     *		array("post_title = %s", "Foo")
     *		array(
     *			array("post_title = %s", "Foo"),
     *
     *		)
     * @param array $conditions (optional)
     * @return self
     */
    function where_and($conditions = array())
    {
        return $this->object->_where($conditions, 'AND');
    }
    /**
     * @param array $conditions (optional)
     * @return self
     */
    function where_or($conditions = array())
    {
        return $this->object->where($conditions, 'OR');
    }
    /**
     * @param array $conditions (optional)
     * @return self
     */
    function where($conditions = array())
    {
        return $this->object->_where($conditions, 'AND');
    }
    /** Parses the where clauses
     * They could look like the following:
     *
     * array(
     *  "post_id = 1"
     *  array("post_id = %d", 1),
     * )
     *
     * or simply "post_id = 1"
     * @param array|string $conditions
     * @param string $operator
     * @return ExtensibleObject
     */
    function _where($conditions, $operator)
    {
        $where_clauses = array();
        // If conditions is not an array, make it one
        if (!is_array($conditions)) {
            $conditions = array($conditions);
        } elseif (!empty($conditions) && !is_array($conditions[0])) {
            // Just a single condition was passed, but with a bind
            $conditions = array($conditions);
        }
        // Iterate through each condition
        foreach ($conditions as $condition) {
            if (is_string($condition)) {
                $clause = $this->object->_parse_where_clause($condition);
                if ($clause) {
                    $where_clauses[] = $clause;
                }
            } else {
                $clause = array_shift($condition);
                $clause = $this->object->_parse_where_clause($clause, $condition);
                if ($clause) {
                    $where_clauses[] = $clause;
                }
            }
        }
        // Add where clause to query
        if ($where_clauses) {
            $this->object->_add_where_clause($where_clauses, $operator);
        }
        return $this->object;
    }
    /**
     * Parses a where clause and returns an associative array
     * representing the query
     *
     * E.g. parse_where_clause("post_title = %s", "Foo Bar")
     *
     * @global wpdb $wpdb
     * @param string $condition
     * @return array
     */
    function _parse_where_clause($condition)
    {
        $column = '';
        $operator = '';
        $value = '';
        $numeric = TRUE;
        // Substitute any placeholders
        global $wpdb;
        $binds = func_get_args();
        $binds = isset($binds[1]) ? $binds[1] : array();
        // first argument is the condition
        foreach ($binds as &$bind) {
            // A bind could be an array, used for the 'IN' operator
            // or a simple scalar value. We need to convert arrays
            // into scalar values
            if (is_object($bind)) {
                $bind = (array) $bind;
            }
            if (is_array($bind) && !empty($bind)) {
                foreach ($bind as &$val) {
                    if (!is_numeric($val)) {
                        $val = '"' . addslashes($val) . '"';
                        $numeric = FALSE;
                    }
                }
                $bind = implode(',', $bind);
            } else {
                if (is_array($bind) && empty($bind)) {
                    $bind = 'NULL';
                } else {
                    if (!is_numeric($bind)) {
                        $numeric = FALSE;
                    }
                }
            }
        }
        if ($binds) {
            $condition = $wpdb->prepare($condition, $binds);
        }
        // Parse the where clause
        if (preg_match("/^[^\\s]+/", $condition, $match)) {
            $column = trim(array_shift($match));
            $condition = str_replace($column, '', $condition);
        }
        if (preg_match("/(NOT )?IN|(NOT )?LIKE|(NOT )?BETWEEN|[=!<>]+/i", $condition, $match)) {
            $operator = trim(array_shift($match));
            $condition = str_replace($operator, '', $condition);
            $operator = strtolower($operator);
            $value = trim($condition);
        }
        // Values will automatically be quoted, so remove them
        // If the value is part of an IN clause or BETWEEN clause and
        // has multiple values, we attempt to split the values apart into an
        // array and iterate over them individually
        if ($operator == 'in') {
            $values = preg_split("/'?\\s?(,)\\s?'?/i", $value);
        } elseif ($operator == 'between') {
            $values = preg_split("/'?\\s?(AND)\\s?'?/i", $value);
        }
        // If there's a single value, treat it as an array so that we
        // can still iterate
        if (empty($values)) {
            $values = array($value);
        }
        foreach ($values as $index => $value) {
            $value = preg_replace("/^(\\()?'/", '', $value);
            $value = preg_replace("/'(\\))?\$/", '', $value);
            $values[$index] = $value;
        }
        if (count($values) > 1) {
            $value = $values;
        }
        // Return the WP Query meta query parameters
        $retval = array('column' => $column, 'value' => $value, 'compare' => strtoupper($operator), 'type' => $numeric ? 'numeric' : 'string');
        return $retval;
    }
    function strip_slashes($stdObject_or_array_or_string)
    {
        /**
         * Some objects have properties that are recursive objects. To avoid this we have to keep track
         * of what objects we've already processed when we're running this method recursively
         */
        static $level = 0;
        static $processed_objects = array();
        $level++;
        $processed_objects[] = $stdObject_or_array_or_string;
        if (is_string($stdObject_or_array_or_string)) {
            $stdObject_or_array_or_string = str_replace("\\'", "'", str_replace('\\"', '"', str_replace("\\\\", "\\", $stdObject_or_array_or_string)));
        } elseif (is_object($stdObject_or_array_or_string) && !in_array($stdObject_or_array_or_string, $processed_objects)) {
            foreach (get_object_vars($stdObject_or_array_or_string) as $key => $val) {
                if ($val != $stdObject_or_array_or_string && $key != '_mapper') {
                    $stdObject_or_array_or_string->{$key} = $this->strip_slashes($val);
                }
            }
            $processed_objects[] = $stdObject_or_array_or_string;
        } elseif (is_array($stdObject_or_array_or_string)) {
            foreach ($stdObject_or_array_or_string as $key => $val) {
                if ($key != '_mixins') {
                    $stdObject_or_array_or_string[$key] = $this->strip_slashes($val);
                }
            }
        }
        $level--;
        if ($level == 0) {
            $processed_objects = array();
        }
        return $stdObject_or_array_or_string;
    }
    /**
     * Converts a stdObject entity to a model
     * @param object $stdObject
     * @param string|bool $context (optional)
     * @return object
     */
    function convert_to_model($stdObject, $context = FALSE)
    {
        // Create a factory
        $retval = NULL;
        try {
            $this->object->_convert_to_entity($stdObject);
        } catch (Exception $ex) {
            throw new E_InvalidEntityException($ex);
        }
        $retval = $this->object->create($stdObject, $context);
        return $retval;
    }
    /**
     * Determines whether an object is actually a model
     * @param mixed $obj
     * @return bool
     */
    function is_model($obj)
    {
        return is_subclass_of($obj, 'C_DataMapper_Model') or get_class($obj) == 'C_DataMapper_Model';
    }
    /**
     * If a field has no value, then use the default value.
     * @param stdClass|C_DataMapper_Model $object
     */
    function _set_default_value($object)
    {
        $array = NULL;
        $field = NULL;
        $default_value = NULL;
        // The first argument MUST be an object
        if (!is_object($object)) {
            throw new E_InvalidEntityException();
        }
        // This method has two signatures:
        // 1) _set_default_value($object, $field, $default_value)
        // 2) _set_default_value($object, $array_field, $field, $default_value)
        // Handle #1
        $args = func_get_args();
        if (count($args) == 4) {
            list($object, $array, $field, $default_value) = $args;
            if (!isset($object->{$array})) {
                $object->{$array} = array();
                $object->{$array}[$field] = NULL;
            } else {
                $arr =& $object->{$array};
                if (!isset($arr[$field])) {
                    $arr[$field] = NULL;
                }
            }
            $array =& $object->{$array};
            $value =& $array[$field];
            if ($value === '' or is_null($value)) {
                $value = $default_value;
            }
        } else {
            list($object, $field, $default_value) = $args;
            if (!isset($object->{$field})) {
                $object->{$field} = NULL;
            }
            $value = $object->{$field};
            if ($value === '' or is_null($value)) {
                $object->{$field} = $default_value;
            }
        }
    }
    function get_defined_column_names()
    {
        return array_keys($this->object->_columns);
    }
    function has_defined_column($name)
    {
        $columns = $this->object->_columns;
        return isset($columns[$name]);
    }
    function cast_columns($entity)
    {
        foreach ($this->object->_columns as $key => $properties) {
            $value = property_exists($entity, $key) ? $entity->{$key} : NULL;
            $default_value = $properties['default_value'];
            if (!is_null($value) && $value !== $default_value) {
                $column_type = $this->object->_columns[$key]['type'];
                if (preg_match("/varchar|text/i", $column_type)) {
                    if (!is_array($value) && !is_object($value)) {
                        $entity->{$key} = strval($value);
                    }
                } else {
                    if (preg_match("/decimal|numeric|double|float/i", $column_type)) {
                        $entity->{$key} = floatval($value);
                    } else {
                        if (preg_match("/int/i", $column_type)) {
                            $entity->{$key} = intval($value);
                        } else {
                            if (preg_match("/bool/i", $column_type)) {
                                $entity->{$key} = $value ? TRUE : FALSE;
                            }
                        }
                    }
                }
            } else {
                $entity->{$key} = $default_value;
            }
        }
        return $entity;
    }
}
/**
 * Provides instance methods for C_CustomPost_DataMapper_Driver
 * @mixin C_CustomPost_DataMapper_Driver
 */
class Mixin_CustomPost_DataMapper_Driver extends Mixin
{
    /**
     * Used to select which fields should be returned. NOT currently used by
     * this implementation of the datamapper driver
     * @param string $fields
     * @return C_DataMapper_Driver_Base
     */
    function select($fields = '*')
    {
        $this->object->_query_args = array('post_type' => $this->object->get_object_name(), 'paged' => FALSE, 'fields' => $fields, 'post_status' => 'any', 'datamapper' => TRUE, 'posts_per_page' => -1, 'is_select' => TRUE, 'is_delete' => FALSE);
        return $this->object;
    }
    /**
     * Destroys/deletes an entity from the database
     * @param object|C_DataMapper_Model $entity
     * @param bool $skip_trash (optional) Default = true
     * @return bool
     */
    function destroy($entity, $skip_trash = TRUE)
    {
        $retval = FALSE;
        $key = $this->object->get_primary_key_column();
        // Find the id of the entity
        if (is_object($entity) && isset($entity->{$key})) {
            $id = (int) $entity->{$key};
        } else {
            $id = (int) $entity;
        }
        // If we have an ID, then delete the post
        if (is_integer($id)) {
            // TODO: We assume that we can skip the trash. Is that correct?
            // FYI, Deletes postmeta as wells
            if (is_object(wp_delete_post($id, TRUE))) {
                $retval = TRUE;
            }
        }
        return $retval;
    }
    /**
     * Saves an entity to the database
     * @param object $entity
     * @return int Post ID
     */
    function _save_entity($entity)
    {
        $post = $this->object->_convert_entity_to_post($entity);
        $primary_key = $this->object->get_primary_key_column();
        // TODO: unsilence this. Wordpress 3.9-beta2 is generating an error that should be corrected before its
        // final release.
        if ($post_id = @wp_insert_post($post)) {
            $new_entity = $this->object->find($post_id, TRUE);
            if ($new_entity) {
                foreach ($new_entity->get_entity() as $key => $value) {
                    $entity->{$key} = $value;
                }
            }
            // Save properties as post meta
            $this->object->_flush_and_update_postmeta($post_id, $entity instanceof stdClass ? $entity : $entity->get_entity());
            $entity->{$primary_key} = $post_id;
            // Clean cache
            $this->object->_cache = array();
        }
        $entity->id_field = $primary_key;
        return $post_id;
    }
    /**
     * Starts a new DELETE statement
     */
    function delete()
    {
        $this->object->select();
        $this->object->_query_args['is_select'] = FALSE;
        $this->object->_query_args['is_delete'] = TRUE;
        return $this->object;
    }
    /**
     * Returns the title of the post. Used when post_title is not set
     * @param stdClass $entity
     * @return string
     */
    function get_post_title($entity)
    {
        return "Untitled {$this->object->get_object_name()}";
    }
    /**
     * Returns the excerpt of the post. Used when post_excerpt is not set
     * @param stdClass $entity
     * @return string
     */
    function get_post_excerpt($entity)
    {
        return '';
    }
}
/**
 * Class C_CustomTable_DataMapper_Driver
 * @mixin C_CustomTable_DataMapper_Driver_Mixin
 */
class C_CustomTable_DataMapper_Driver extends C_DataMapper_Driver_Base
{
    /**
     * The WordPress Database Connection
     * @var wpdb
     */
    var $_where_clauses = array();
    var $_order_clauses = array();
    var $_group_by_columns = array();
    var $_limit_clause = '';
    var $_select_clause = '';
    var $_delete_clause = '';
    public $_use_cache = TRUE;
    function define($object_name = FALSE, $context = FALSE)
    {
        parent::define($object_name, $context);
        $this->add_mixin('C_CustomTable_DataMapper_Driver_Mixin');
        $this->implement('I_CustomTable_DataMapper');
    }
    function initialize($object_name = FALSE)
    {
        parent::initialize();
        if (!isset($this->_primary_key_column)) {
            $this->_primary_key_column = $this->_lookup_primary_key_column();
        }
        $this->migrate();
    }
    /**
     * Returns the database connection object for WordPress
     * @global wpdb $wpdb
     * @return wpdb
     */
    function _wpdb()
    {
        global $wpdb;
        return $wpdb;
    }
    /**
     * Looks up the primary key column for this table
     */
    function _lookup_primary_key_column()
    {
        $key = $this->_wpdb()->get_row("SHOW INDEX FROM {$this->get_table_name()} WHERE Key_name='PRIMARY'", ARRAY_A);
        if (!$key) {
            throw new Exception("Please specify the primary key for {$this->get_table_name()}");
        }
        return $key['Column_name'];
    }
    /**
     * Gets the name of the primary key column
     * @return string
     */
    function get_primary_key_column()
    {
        return $this->object->_primary_key_column;
    }
    /**
     * Determines whether we're going to execute a SELECT statement
     * @return boolean
     */
    function is_select_statement()
    {
        return $this->object->_select_clause ? TRUE : FALSE;
    }
    /**
     * Determines if we're going to be executing a DELETE statement
     * @return bool
     */
    function is_delete_statement()
    {
        return $this->object->_delete_clause ? TRUE : FALSE;
    }
    /**
     * Orders the results of the query
     * This method may be used multiple of times to order by more than column
     * @param $order_by
     * @param $direction
     * @return object
     */
    function order_by($order_by, $direction = 'ASC')
    {
        // We treat the rand() function as an exception
        if (preg_match("/rand\\(\\s*\\)/", $order_by)) {
            $order = 'rand()';
        } else {
            $order_by = $this->object->_clean_column($order_by);
            // If the order by clause is a column, then it should be backticked
            if ($this->object->has_column($order_by)) {
                $order_by = "`{$order_by}`";
            }
            $direction = $this->object->_clean_column($direction);
            $order = "{$order_by} {$direction}";
        }
        $this->object->_order_clauses[] = $order;
        return $this->object;
    }
    /**
     * Specifies a limit and optional offset
     * @param integer $max
     * @param integer $offset
     * @return object
     */
    function limit($max, $offset = 0)
    {
        if ($offset) {
            $limit = $this->_wpdb()->prepare("LIMIT %d, %d", max(0, $offset), $max);
        } else {
            $limit = $this->_wpdb()->prepare("LIMIT %d", max(0, $max));
        }
        if ($limit) {
            $this->object->_limit_clause = $limit;
        }
        return $this->object;
    }
    /**
     * Specifics a group by clause for one or more columns
     * @param array|string $columns
     * @return object
     */
    function group_by($columns = array())
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }
        $this->object->_group_by_columns = array_merge($this->object->_group_by_columns, $columns);
        return $this->object;
    }
    /**
     * Adds a where clause to the driver
     * @param array $where_clauses
     * @param string $join
     */
    function _add_where_clause($where_clauses, $join)
    {
        $clauses = array();
        foreach ($where_clauses as $clause) {
            extract($clause);
            if ($this->object->has_column($column)) {
                $column = "`{$column}`";
            }
            if (!is_array($value)) {
                $value = array($value);
            }
            foreach ($value as $index => $v) {
                $v = $clause['type'] == 'numeric' ? $v : "'{$v}'";
                $value[$index] = $v;
            }
            if ($compare == 'BETWEEN') {
                $value = "{$value[0]} AND {$value[1]}";
            } else {
                $value = implode(', ', $value);
                if (strpos($compare, 'IN') !== FALSE) {
                    $value = "({$value})";
                }
            }
            $clauses[] = "{$column} {$compare} {$value}";
        }
        $this->object->_where_clauses[] = implode(" {$join} ", $clauses);
    }
    /**
     * Returns the total number of entities known
     * @return int
     */
    function count()
    {
        $retval = 0;
        $key = $this->object->get_primary_key_column();
        $results = $this->object->run_query("SELECT COUNT(`{$key}`) AS `{$key}` FROM `{$this->object->get_table_name()}`");
        if ($results && isset($results[0]->{$key})) {
            $retval = (int) $results[0]->{$key};
        }
        return $retval;
    }
    /**
     * Run the query
     * @param string|bool $sql (optional) run the specified SQL
     * @param object|bool $model (optional)
     * @param bool $no_entities (optional)
     * @return array
     */
    function run_query($sql = FALSE, $model = FALSE, $no_entities = FALSE)
    {
        $results = FALSE;
        $retval = array();
        // Or generate SQL query
        if (!$sql) {
            $sql = $this->object->get_generated_query($no_entities);
        }
        // If we have a SQL statement to execute, then heck, execute it!
        if ($sql) {
            if ($this->object->debug) {
                var_dump($sql);
            }
            // Try getting the result from cache first
            if ($this->is_select_statement() && $this->object->_use_cache) {
                $results = $this->object->get_from_cache($sql);
            }
        }
        if (!$results) {
            $this->_wpdb()->query($sql);
            $results = $this->_wpdb()->last_result;
            if ($this->is_select_statement()) {
                $this->object->cache($sql, $results);
            }
        }
        if ($results) {
            $retval = array();
            // For each row, create an entity, update it's properties, and add it to the result set
            if ($no_entities) {
                $retval = $results;
            } else {
                $id_field = $this->get_primary_key_column();
                foreach ($results as $row) {
                    if ($row) {
                        if (isset($row->{$id_field})) {
                            if ($model) {
                                $retval[] = $this->object->convert_to_model($row);
                            } else {
                                $retval[] = $this->object->_convert_to_entity($row);
                            }
                        }
                    }
                }
            }
        } elseif ($this->object->debug) {
            var_dump("No entities returned from query");
        }
        // Just a safety check
        if (!$retval) {
            $retval = array();
        }
        return $retval;
    }
    /**
     * Converts an entity to something suitable for inserting into a database column
     * @param object $entity
     * @return array
     */
    function _convert_to_table_data($entity)
    {
        $data = (array) $entity;
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->object->serialize($value);
            }
        }
        return $data;
    }
    /**
     * Fetches the last row
     * @param array $conditions
     * @return object
     */
    function find_last($conditions = array(), $model = FALSE)
    {
        $retval = NULL;
        // Get row number for the last row
        $this->select()->limit(1)->order_by('date', 'DESC');
        if ($conditions) {
            $this->where_and($conditions);
        }
        $results = $this->run_query();
        if ($results) {
            $retval = $model ? $this->object->convert_to_model($results[0]) : $results[0];
        }
        return $retval;
    }
    function get_column_names()
    {
        return array_keys($this->object->_columns);
    }
    /**
     * Migrates the schema of the database
     */
    function migrate()
    {
        if (!$this->object->_columns) {
            throw new E_ColumnsNotDefinedException("Columns not defined for {$this->get_table_name()}");
        }
        $added = FALSE;
        $removed = FALSE;
        // Add any missing columns
        foreach ($this->object->_columns as $key => $properties) {
            if (!in_array($key, $this->object->_table_columns)) {
                if ($this->object->_add_column($key, $properties['type'], $properties['default_value'])) {
                    $added = TRUE;
                }
            }
        }
        // Remove any columns not defined
        //		foreach ($this->object->_table_columns as $key) {
        //			if (!isset($this->object->_columns[$key])) {
        //				$this->object->_remove_column($key);
        //                $removed = TRUE;
        //			}
        //		}
        if ($added or $removed) {
            // var_dump($this->object->_table_columns);
            $this->object->lookup_columns();
            // var_dump($added, $removed);
        }
    }
    function _init()
    {
        $this->object->_where_clauses = array();
        $this->object->_order_clauses = array();
        $this->object->_group_by_columns = array();
        $this->object->_limit_clause = '';
        $this->object->_select_clause = '';
    }
}
/**
 * Provides instance methods for C_CustomTable_DataMapper_Driver
 * @mixin C_CustomTable_DataMapper_Driver
 */
class C_CustomTable_DataMapper_Driver_Mixin extends Mixin
{
    /**
     * Selects which fields to collect from the table.
     * NOTE: Not protected from SQL injection - DO NOT let your users specify DB columns
     * @param string $fields
     * @return object
     */
    function select($fields = NULL)
    {
        // Create a fresh slate
        $this->object->_init();
        if (!$fields or $fields == '*') {
            $fields = $this->get_table_name() . '.*';
        }
        $this->object->_select_clause = "SELECT {$fields}";
        return $this->object;
    }
    /**
     * Start a delete statement
     */
    function delete()
    {
        // Create a fresh slate
        $this->object->_init();
        $this->object->_delete_clause = "DELETE";
        return $this->object;
    }
    /**
     * Stores the entity
     * @param object $entity
     * @return bool|object
     */
    function _save_entity($entity)
    {
        $retval = FALSE;
        unset($entity->id_field);
        $primary_key = $this->object->get_primary_key_column();
        if (isset($entity->{$primary_key}) && $entity->{$primary_key} > 0) {
            if ($this->object->_update($entity)) {
                $retval = intval($entity->{$primary_key});
            }
        } else {
            $retval = $this->object->_create($entity);
            if ($retval) {
                $new_entity = $this->object->find($retval);
                foreach ($new_entity as $key => $value) {
                    $entity->{$key} = $value;
                }
            }
        }
        $entity->id_field = $primary_key;
        // Clean cache
        if ($retval) {
            $this->object->_cache = array();
        }
        return $retval;
    }
    /**
     * Destroys/deletes an entity
     * @param object|C_DataMapper_Model|int $entity
     * @return boolean
     */
    function destroy($entity)
    {
        $retval = FALSE;
        $key = $this->object->get_primary_key_column();
        // Find the id of the entity
        if (is_object($entity) && isset($entity->{$key})) {
            $id = (int) $entity->{$key};
        } else {
            $id = (int) $entity;
        }
        // If we have an ID, then delete the post
        if (is_numeric($id)) {
            $sql = $this->object->_wpdb()->prepare("DELETE FROM `{$this->object->get_table_name()}` WHERE {$key} = %s", $id);
            $retval = $this->object->_wpdb()->query($sql);
        }
        return $retval;
    }
    /**
     * Creates a new record in the database
     * @param object $entity
     * @return boolean
     */
    function _create($entity)
    {
        $retval = FALSE;
        $id = $this->object->_wpdb()->insert($this->object->get_table_name(), $this->object->_convert_to_table_data($entity));
        if ($id) {
            $key = $this->object->get_primary_key_column();
            $retval = $entity->{$key} = intval($this->object->_wpdb()->insert_id);
        }
        return $retval;
    }
    /**
     * Updates a record in the database
     * @param object $entity
     * @return int|bool
     */
    function _update($entity)
    {
        $key = $this->object->get_primary_key_column();
        return $this->object->_wpdb()->update($this->object->get_table_name(), $this->object->_convert_to_table_data($entity), array($key => $entity->{$key}));
    }
    function _add_column($column_name, $datatype, $default_value = NULL)
    {
        $sql = "ALTER TABLE `{$this->get_table_name()}` ADD COLUMN `{$column_name}` {$datatype}";
        if ($default_value) {
            if (is_string($default_value)) {
                $default_value = str_replace("'", "\\'", $default_value);
            }
            $sql .= " NOT NULL DEFAULT " . (is_string($default_value) ? "'{$default_value}" : "{$default_value}");
        }
        $return = $this->object->_wpdb()->query($sql) ? TRUE : FALSE;
        $this->object->update_columns_cache();
        return $return;
    }
    function _remove_column($column_name)
    {
        $sql = "ALTER TABLE `{$this->get_table_name()}` DROP COLUMN `{$column_name}`";
        $return = $this->object->_wpdb()->query($sql) ? TRUE : FALSE;
        $this->object->update_columns_cache();
        return $return;
    }
    /**
     * Returns the generated SQL query to be executed
     * @param bool $no_entities Default = false
     * @return string
     */
    function get_generated_query($no_entities = FALSE)
    {
        $sql = array();
        if ($this->object->is_select_statement()) {
            $sql[] = $this->object->_select_clause;
        } elseif ($this->object->is_delete_statement()) {
            $sql[] = $this->object->_delete_clause;
        }
        $sql[] = 'FROM `' . $this->object->get_table_name() . '`';
        $where_clauses = array();
        foreach ($this->object->_where_clauses as $where) {
            $where_clauses[] = '(' . $where . ')';
        }
        if ($where_clauses) {
            $sql[] = 'WHERE ' . implode(' AND ', $where_clauses);
        }
        if ($this->object->is_select_statement()) {
            if ($this->object->_group_by_columns) {
                $sql[] = 'GROUP BY ' . implode(', ', $this->object->_group_by_columns);
            }
            if ($this->object->_order_clauses) {
                $sql[] = 'ORDER BY ' . implode(', ', $this->object->_order_clauses);
            }
            if ($this->object->_limit_clause) {
                $sql[] = $this->object->_limit_clause;
            }
        }
        return implode(' ', $sql);
    }
}
/**
 * Thrown when an entity does not exist
 */
class E_EntityNotFoundException extends E_NggErrorException
{
}
class E_ColumnsNotDefinedException extends E_NggErrorException
{
}
/**
 * Thrown when an invalid data type is used as an entity, such as an associative
 * array which is not yet supported due to a problem with references and the
 * call_user_func_array() function.
 */
class E_InvalidEntityException extends E_NggErrorException
{
    function __construct($message_or_previous = FALSE, $code = 0, $previous = NULL)
    {
        // We don't know if we have been passed a message yet or not
        $message = FALSE;
        // Determine if the first parameter is a string or exception
        if ($message_or_previous) {
            if (is_string($message_or_previous)) {
                $message = $message_or_previous;
            } else {
                $previous = $message_or_previous;
            }
        }
        // If no message was provided, create a default message
        if (!$message) {
            $message = "Invalid data type used for entity. Please use stdClass\n\t\t\t\tor a subclass of C_DataMapper_Model. Arrays will be supported in\n\t\t\t\tthe future.";
        }
        parent::__construct($message, $code);
    }
}
/**
 * Class C_CustomPost_DataMapper_Driver
 * @mixin Mixin_CustomPost_DataMapper_Driver
 * @implements I_CustomPost_DataMapper
 */
class C_CustomPost_DataMapper_Driver extends C_DataMapper_Driver_Base
{
    var $_query_args = array();
    var $_primary_key_column = 'ID';
    static $_post_table_columns = array();
    public $_use_cache = TRUE;
    function define($object_name = FALSE, $context = FALSE)
    {
        if (strlen($object_name) > 20) {
            throw new Exception("The custom post name can be no longer than 20 characters long");
        }
        parent::define($object_name, $context);
        $this->add_mixin('Mixin_CustomPost_DataMapper_Driver');
        $this->implement('I_CustomPost_DataMapper');
    }
    function lookup_columns()
    {
        if (empty(self::$_post_table_columns)) {
            $columns = parent::lookup_columns();
            foreach ($columns as $column) {
                self::$_post_table_columns[] = $column;
            }
        } else {
            foreach (self::$_post_table_columns as $column) {
                $this->_table_columns[] = $column;
            }
        }
    }
    /**
     * Gets the name of the table
     * @global string $table_prefix
     * @return string
     */
    function get_table_name()
    {
        global $table_prefix;
        return $table_prefix . 'posts';
    }
    /**
     * Returns a list of querable table columns for posts
     * @return array
     */
    function _get_querable_table_columns()
    {
        return array('name', 'author', 'date', 'title', 'modified', 'menu_order', 'parent', 'ID', 'rand', 'comment_count');
    }
    /**
     * Specifies an order clause
     * @param string $order_by
     * @param string $direction
     * @return C_DataMapper_Driver_Base
     */
    function order_by($order_by, $direction = 'ASC')
    {
        // Make an exception for the rand() method
        $order_by = preg_replace("/rand\\(\\s*\\)/", 'rand', $order_by);
        if (in_array($order_by, $this->object->_get_querable_table_columns())) {
            $this->object->_query_args['orderby'] = $order_by;
        } else {
            // ordering by a meta value
            $this->object->_query_args['orderby'] = 'meta_value';
            $this->object->_query_args['meta_key'] = $order_by;
        }
        $this->object->_query_args['order'] = $direction;
        return $this->object;
    }
    /**
     * Specifies a limit and optional offset
     * @param int $max
     * @param int|bool $offset (optional)
     * @return object
     */
    function limit($max, $offset = FALSE)
    {
        if ($max) {
            $this->object->_query_args['paged'] = TRUE;
            if ($offset) {
                $this->object->_query_args['offset'] = $offset;
            } else {
                unset($this->object->_query_args['offset']);
            }
            $this->object->_query_args['posts_per_page'] = $max;
        }
        return $this->object;
    }
    /**
     * Specifies a list of columns to group by
     * @param array|string $columns
     * @return object
     */
    function group_by($columns = array())
    {
        if (!isset($this->object->_query_args['group_by_columns'])) {
            $this->object->_query_args['group_by_columns'] = $columns;
        } else {
            $this->object->_query_args['group_by_columns'] = array_merge($this->object->_query_args['group_by_columns'], $columns);
        }
        return $this->object;
    }
    /**
     * Adds a WP_Query where clause
     * @param array $where_clauses
     * @param string $join
     */
    function _add_where_clause($where_clauses, $join)
    {
        foreach ($where_clauses as $clause) {
            // $clause => array(
            // 'column' => 'ID',
            // 'value'  =>	1210,
            // 'compare' => '='
            // )
            // Determine where what the where clause is comparing
            switch ($clause['column']) {
                case 'author':
                case 'author_id':
                    $this->object->_query_args['author'] = $clause['value'];
                    break;
                case 'author_name':
                    $this->object->_query_args['author_name'] = $clause['value'];
                    break;
                case 'cat':
                case 'cat_id':
                case 'category_id':
                    switch ($clause['compare']) {
                        case '=':
                        case 'BETWEEN':
                        case 'IN':
                            if (!isset($this->object->_query_args['category__in'])) {
                                $this->object->_query_args['category__in'] = array();
                            }
                            $this->object->_query_args['category__in'][] = $clause['value'];
                            break;
                        case '!=':
                        case 'NOT BETWEEN':
                        case 'NOT IN':
                            if (!isset($this->object->_query_args['category__not_in'])) {
                                $this->object->_query_args['category__not_in'] = array();
                            }
                            $this->object->_query_args['category__not_in'][] = $clause['value'];
                            break;
                    }
                    break;
                case 'category_name':
                    $this->object->_query_args['category_name'] = $clause['value'];
                    break;
                case 'post_id':
                case $this->object->get_primary_key_column():
                    switch ($clause['compare']) {
                        case '=':
                        case 'IN':
                        case 'BETWEEN':
                            if (!isset($this->object->_query_args['post__in'])) {
                                $this->object->_query_args['post__in'] = array();
                            }
                            $this->object->_query_args['post__in'][] = $clause['value'];
                            break;
                        default:
                            if (!isset($this->object->_query_args['post__not_in'])) {
                                $this->object->_query_args['post__not_in'] = array();
                            }
                            $this->object->_query_args['post__not_in'][] = $clause['value'];
                            break;
                    }
                    break;
                case 'pagename':
                case 'postname':
                case 'page_name':
                case 'post_name':
                    if ($clause['compare'] == 'LIKE') {
                        $this->object->_query_args['page_name__like'] = $clause['value'];
                    } elseif ($clause['compare'] == '=') {
                        $this->object->_query_args['pagename'] = $clause['value'];
                    } elseif ($clause['compare'] == 'IN') {
                        $this->object->_query_args['page_name__in'] = $clause['value'];
                    }
                    break;
                case 'post_title':
                    // Post title uses custom WHERE clause
                    if ($clause['compare'] == 'LIKE') {
                        $this->object->_query_args['post_title__like'] = $clause['value'];
                    } else {
                        $this->object->_query_args['post_title'] = $clause['value'];
                    }
                    break;
                default:
                    // Must be metadata
                    $clause['key'] = $clause['column'];
                    unset($clause['column']);
                    // Convert values to array, when required
                    if (in_array($clause['compare'], array('IN', 'BETWEEN'))) {
                        $clause['value'] = explode(',', $clause['value']);
                        foreach ($clause['value'] as &$val) {
                            if (!is_numeric($val)) {
                                // In the _parse_where_clause() method, we
                                // quote the strings and add slashes
                                $val = stripslashes($val);
                                $val = substr($val, 1, strlen($val) - 2);
                            }
                        }
                    }
                    if (!isset($this->object->_query_args['meta_query'])) {
                        $this->object->_query_args['meta_query'] = array();
                    }
                    $this->object->_query_args['meta_query'][] = $clause;
                    break;
            }
        }
        // If any where clauses have been added, specify how the conditions
        // will be conbined/joined
        if (isset($this->object->_query_args['meta_query'])) {
            $this->object->_query_args['meta_query']['relation'] = $join;
        }
    }
    /**
     * Converts a post to an entity
     * @param \stdClass $post
     * @param boolean $model
     * @return \stdClass
     */
    function convert_post_to_entity($post, $model = FALSE)
    {
        $entity = new stdClass();
        // Unserialize the post_content_filtered field
        if (is_string($post->post_content_filtered)) {
            if ($post_content = $this->object->unserialize($post->post_content_filtered)) {
                foreach ($post_content as $key => $value) {
                    $post->{$key} = $value;
                }
            }
        }
        // Unserialize the post content field
        if (is_string($post->post_content)) {
            if ($post_content = $this->object->unserialize($post->post_content)) {
                foreach ($post_content as $key => $value) {
                    $post->{$key} = $value;
                }
            }
        }
        // Copy post fields to entity
        unset($post->post_content);
        unset($post->post_content_filtered);
        foreach ($post as $key => $value) {
            $entity->{$key} = $value;
        }
        $this->object->_convert_to_entity($entity);
        return $model ? $this->object->convert_to_model($entity) : $entity;
    }
    /**
     * Converts an entity to a post
     * @param object $entity
     * @return object
     */
    function _convert_entity_to_post($entity)
    {
        // Was a model passed instead of an entity?
        $post = $entity;
        if (!$entity instanceof stdClass) {
            $post = $entity->get_entity();
        }
        // Create the post content
        $post_content = clone $post;
        foreach ($this->object->_table_columns as $column) {
            unset($post_content->{$column});
        }
        unset($post->id_field);
        unset($post->post_content_filtered);
        unset($post->post_content);
        $post->post_content = $this->object->serialize($post_content);
        $post->post_content_filtered = $post->post_content;
        $post->post_type = $this->object->get_object_name();
        // Sometimes an entity can contain a data stored in an array or object
        // Those will be removed from the post, and serialized in the
        // post_content field
        foreach ($post as $key => $value) {
            if (in_array(strtolower(gettype($value)), array('object', 'array'))) {
                unset($post->{$key});
            }
        }
        // A post required a title
        if (!property_exists($post, 'post_title')) {
            $post->post_title = $this->object->get_post_title($post);
        }
        // A post also requires an excerpt
        if (!property_exists($post, 'post_excerpt')) {
            $post->post_excerpt = $this->object->get_post_excerpt($post);
        }
        return $post;
    }
    /**
     * Returns the WordPress database class
     * @global wpdb $wpdb
     * @return wpdb
     */
    function _wpdb()
    {
        global $wpdb;
        return $wpdb;
    }
    /**
     * Flush and update all postmeta for a particular post
     * @param int $post_id
     */
    function _flush_and_update_postmeta($post_id, $entity, $omit = array())
    {
        // We need to insert post meta data for each property
        // Unfortunately, that means flushing all existing postmeta
        // and then inserting new values. Depending on the number of
        // properties, this could be slow. So, we directly access the database
        /* @var $wpdb wpdb */
        global $wpdb;
        if (!is_array($omit)) {
            $omit = array($omit);
        }
        // By default, we omit creating meta values for columns in the posts table
        $omit = array_merge($omit, $this->object->_table_columns);
        // Delete the existing meta values
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE post_id = %s", $post_id));
        // Create query for new meta values
        $sql_parts = array();
        foreach ($entity as $key => $value) {
            if (in_array($key, $omit)) {
                continue;
            }
            if (is_array($value) or is_object($value)) {
                $value = $this->object->serialize($value);
            }
            $sql_parts[] = $wpdb->prepare("(%s, %s, %s)", $post_id, $key, $value);
        }
        $wpdb->query("INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) VALUES " . implode(',', $sql_parts));
    }
    /**
     * Determines whether the current statement is SELECT
     * @return boolean
     */
    function is_select_statement()
    {
        return isset($this->object->_query_args['is_select']) && $this->object->_query_args['is_select'];
    }
    /**
     * Determines whether the current statement is DELETE
     * @return bool
     */
    function is_delete_statement()
    {
        return isset($this->object->_query_args['is_delete']) && $this->object->_query_args['is_delete'];
    }
    /**
     * Runs the query
     * @param string|bool $sql (optional) Run the specified query
     * @param object|bool $model (optional)
     * @param bool $convert_to_entities (optional) Default = true
     * @return array
     */
    function run_query($sql = FALSE, $model = FALSE, $convert_to_entities = TRUE)
    {
        $retval = array();
        $results = array();
        // All of our custom fields are stored as post meta, but is also stored as a serialized
        // value in the post_content field. Because of this, we don't need to look up and cache the
        // post meta values
        $this->object->_query_args['update_post_meta_cache'] = FALSE;
        $this->object->_query_args['update_post_meta_cache'] = FALSE;
        $this->object->_query_args['no_found_posts'] = FALSE;
        // Don't cache any manual SQL query
        if ($sql) {
            $this->object->_query_args['cache_results'] = FALSE;
            $this->object->_query_args['custom_sql'] = $sql;
        }
        // If this is a select query, then try fetching the results from cache
        $cache_key = md5(json_encode($this->object->_query_args));
        if ($this->is_select_statement() && $this->object->_use_cache) {
            $results = $this->object->get_from_cache($cache_key);
        }
        // Execute the query
        if (!$results) {
            $query = new WP_Query(array('datamapper' => TRUE));
            if (isset($this->object->debug)) {
                $this->object->_query_args['debug'] = TRUE;
            }
            $query->query_vars = $this->object->_query_args;
            add_action('pre_get_posts', array(&$this, 'set_query_args'), PHP_INT_MAX - 1, 1);
            $results = $query->get_posts();
            // Cache the result
            if ($this->is_select_statement()) {
                $this->object->cache($cache_key, $results);
            }
            remove_action('pre_get_posts', array(&$this, 'set_query_args'), PHP_INT_MAX - 1);
        }
        // Convert the result
        if ($convert_to_entities) {
            foreach ($results as $row) {
                $retval[] = $this->object->convert_post_to_entity($row, $model);
            }
        } else {
            $retval = $results;
        }
        // Ensure that we return an empty array when there are no results
        if (!$retval) {
            $retval = array();
        }
        return $retval;
    }
    /**
     * Ensure that the query args are set. We need to do this in case a third-party
     * plugin overrides our query
     * @param $query
     */
    function set_query_args($query)
    {
        if ($query->get('datamapper')) {
            $query->query_vars = $this->object->_query_args;
        }
        $filter = isset($query->query_vars['suppress_filters']) ? $query->query_vars['suppress_filters'] : FALSE;
        $query->query_vars['suppress_filters'] = apply_filters('wpml_suppress_filters', $filter);
    }
    /**
     * Fetches the last row
     * @param array $conditions (optional)
     * @param object|bool $model (optional)
     * @return object
     */
    function find_last($conditions = array(), $model = FALSE)
    {
        $retval = NULL;
        // Get row number for the last row
        $table_name = $this->object->_clean_column($this->object->get_table_name());
        $object_name = $this->object->_clean_column($this->object->get_object_name());
        $sql = $this->_wpdb()->prepare("SELECT COUNT(*) FROM {$table_name} WHERE post_type = %s", $object_name);
        $count = $this->_wpdb()->get_var($sql);
        $offset = $count - 1;
        $this->select();
        if ($conditions) {
            $this->where_and($conditions);
        }
        if ($offset) {
            $this->limit(1, $offset);
        }
        $results = $this->run_query();
        if ($results) {
            $retval = $model ? $this->object->convert_to_model($results[0]) : $results[0];
        }
        return $retval;
    }
    /**
     * Returns the number of total records/entities that exist
     * @return int
     */
    function count()
    {
        $this->object->select($this->object->get_primary_key_column());
        $retval = $this->object->run_query(FALSE, FALSE, FALSE);
        return count($retval);
    }
}
/**
 * Provides instance methods for C_DataMapper_Driver_Base
 * @mixin C_DataMapper_Driver_Base
 */
class Mixin_DataMapper_Driver_Base extends Mixin
{
    /**
     * Serializes the data
     *
     * @param mixed $value
     * @return string
     */
    function serialize($value)
    {
        return C_NextGen_Serializable::serialize($value);
    }
    /**
     * Unserializes data using our proprietary format
     *
     * @param string $value
     * @return mixed
     */
    function unserialize($value)
    {
        return C_NextGen_Serializable::unserialize($value);
    }
    /**
     * Finds a partiular entry by id
     * @param int|stdClass|C_DataMapper_Model $entity
     * @param object|bool $model (optional)
     * @return null|object
     */
    function find($entity, $model = FALSE)
    {
        $retval = NULL;
        // Get primary key of the entity
        $pkey = $this->object->get_primary_key_column();
        if (!is_numeric($entity)) {
            $entity = isset($entity->{$pkey}) ? intval($entity->{$pkey}) : FALSE;
        }
        // If we have an entity ID, then get the record
        if ($entity) {
            $results = $this->object->select()->where_and(array("{$pkey} = %d", $entity))->limit(1, 0)->run_query();
            if ($results) {
                $retval = $model ? $this->object->convert_to_model($results[0]) : $results[0];
            }
        }
        return $retval;
    }
    /**
     * Converts a stdObject to an Entity
     * @param object $stdObject
     * @return object
     */
    function _convert_to_entity($stdObject)
    {
        // Add name of the id_field to the entity, and convert
        // the ID to an integer
        $stdObject->id_field = $key = $this->object->get_primary_key_column();
        // Cast columns to their appropriate data type
        $this->cast_columns($stdObject);
        // Strip slashes
        $this->strip_slashes($stdObject);
        // Unserialize columns
        $this->unserialize_columns($stdObject);
        // Set defaults for this entity
        if (!$this->has_default_values($stdObject)) {
            $this->object->set_defaults($stdObject);
            $stdObject->__defaults_set = TRUE;
        }
        return $stdObject;
    }
    /**
     * Creates a new model
     * @param object|array $properties (optional)
     * @param string|bool $context (optional)
     * @return C_DataMapper_Model
     */
    function create($properties = array(), $context = FALSE)
    {
        $entity = $properties;
        $factory = C_Component_Factory::get_instance();
        if (!is_object($properties)) {
            $entity = new stdClass();
            foreach ($properties as $k => $v) {
                $entity->{$k} = $v;
            }
        }
        return $factory->create($this->object->get_model_factory_method(), $entity, $this->object, $context);
    }
    /**
     * Saves an entity
     * @param stdClass|C_DataMapper_Model $entity
     * @return bool|int Resulting ID or false upon failure
     */
    function save($entity)
    {
        $retval = FALSE;
        $model = $entity;
        $this->flush_query_cache();
        // Attempt to use something else, most likely an associative array
        // TODO: Support assocative arrays. The trick is to support references
        // with dynamic calls using __call() and call_user_func_array().
        if (is_array($entity)) {
            throw new E_InvalidEntityException();
        } elseif (!$this->object->is_model($entity)) {
            unset($entity->__defaults_set);
            $model = $this->object->convert_to_model($entity);
        }
        // Validate the model
        $model->validate();
        if ($model->is_valid()) {
            $saved_entity = $model->get_entity();
            unset($saved_entity->_errors);
            $retval = $this->object->_save_entity($saved_entity);
        }
        $this->flush_query_cache();
        // We always return the same type of entity that we given
        if (get_class($entity) == 'stdClass') {
            $model->get_entity();
        }
        return $retval;
    }
    /**
     * Gets validation errors for the entity
     * @param stdClass|C_DataMapper_Model $entity
     * @return array
     */
    function get_errors($entity)
    {
        $model = $entity;
        if (!$this->object->is_model($entity)) {
            $model = $this->object->convert_to_model($entity);
        }
        $model->validate();
        return $model->get_errors();
    }
    /**
     * Called to set defaults for the record/model/entity.
     * Subclasses and adapters should extend this method to provide their
     * implementation. The implementation should make use of the
     * _set_default_value() method
     * @param object $stdObject
     */
    function set_defaults($stdObject)
    {
    }
    function has_default_values($entity)
    {
        return isset($entity->__defaults_set) && $entity->__defaults_set == TRUE;
    }
    function define_column($name, $type, $default_value = NULL)
    {
        $this->object->_columns[$name] = array('type' => $type, 'default_value' => $default_value);
    }
}
/**
 * Class C_DataMapper_Model
 * @mixin Mixin_Validation
 * @mixin Mixin_DataMapper_Model_Instance_Methods
 * @mixin Mixin_DataMapper_Model_Validation
 * @implements I_DataMapper_Model
 */
class C_DataMapper_Model extends C_Component
{
    var $_mapper;
    var $_stdObject;
    /**
     * Define the model
     */
    function define($mapper = NULL, $properties = array(), $context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Validation');
        $this->add_mixin('Mixin_DataMapper_Model_Instance_Methods');
        $this->add_mixin('Mixin_DataMapper_Model_Validation');
        $this->implement('I_DataMapper_Model');
    }
    /**
     * Creates a new entity for the specified mapper
     * @param C_DataMapper_Driver_Base $mapper (optional)
     * @param array|object|bool $properties (optional)
     */
    function initialize($mapper = NULL, $properties = FALSE)
    {
        $this->_mapper = $mapper;
        $this->_stdObject = $properties ? (object) $properties : new stdClass();
        parent::initialize();
        if (!$this->has_default_values()) {
            $this->set_defaults();
            $this->_stdObject->__defaults_set = TRUE;
        }
    }
    function jsonSerialize()
    {
        return $this->get_entity();
    }
    function has_default_values()
    {
        return isset($this->_stdObject->__defaults_set) && $this->_stdObject->__defaults_set == TRUE;
    }
    /**
     * Gets the data mapper for the entity
     * @return C_DataMapper_Driver_Base
     */
    function get_mapper()
    {
        return $this->_mapper;
    }
    /**
     * Gets a property of the model
     * @param string $property
     * @return mixed
     */
    function &__get($property)
    {
        if (isset($this->_stdObject->{$property})) {
            $retval =& $this->_stdObject->{$property};
            return $retval;
        } else {
            // We need to assign NULL to a variable first, since only
            // variables can be returned by reference
            $retval = NULL;
            return $retval;
        }
    }
    /**
     * Sets a property for the model
     * @param mixed $property
     * @param mixed $value
     * @return mixed $value
     */
    function &__set($property, $value)
    {
        $retval = $this->_stdObject->{$property} = $value;
        return $retval;
    }
    function __isset($property_name)
    {
        return isset($this->_stdObject->{$property_name});
    }
    /**
     * Saves the entity
     * @param array $updated_attributes
     * @return int|bool Object ID or false upon failure
     */
    function save($updated_attributes = array())
    {
        $this->update_attributes($updated_attributes);
        return $this->get_mapper()->save($this->get_entity());
    }
    /**
     * Updates the attributes for an object
     * @param array $array (optional)
     */
    function update_attributes($array = array())
    {
        foreach ($array as $key => $value) {
            $this->_stdObject->{$key} = $value;
        }
    }
    /**
     * Sets the default values for this model
     */
    function set_defaults()
    {
        $mapper = $this->get_mapper();
        if ($mapper->has_method('set_defaults')) {
            $mapper->set_defaults($this);
        }
    }
    /**
     * Destroys or deletes the entity
     */
    function destroy()
    {
        return $this->get_mapper()->destroy($this->_stdObject);
    }
    /**
     * Determines whether the object is new or existing
     * @return bool
     */
    function is_new()
    {
        return $this->id() ? FALSE : TRUE;
    }
    /**
     * Gets/sets the primary key
     * @param null|int|string $value (optional)
     * @return mixed
     */
    function id($value = NULL)
    {
        $key = $this->get_mapper()->get_primary_key_column();
        if ($value) {
            $this->__set($key, $value);
        }
        return $this->__get($key);
    }
}
/**
 * This mixin should be overwritten by other modules
 */
class Mixin_DataMapper_Model_Validation extends Mixin
{
    function validation()
    {
        return $this->object->is_valid();
    }
}
class Mixin_DataMapper_Model_Instance_Methods extends Mixin
{
    /**
     * Returns the associated entity
     */
    function &get_entity()
    {
        return $this->object->_stdObject;
    }
}