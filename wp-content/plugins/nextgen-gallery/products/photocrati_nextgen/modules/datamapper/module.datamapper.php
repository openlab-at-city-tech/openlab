<?php
class M_DataMapper extends C_Base_Module
{
	function define($id = 'pope-module',
                    $name = 'Pope Module',
                    $description = '',
                    $version = '',
                    $uri = '',
                    $author = '',
                    $author_uri = '',
                    $context = FALSE)
	{
		parent::define(
			'photocrati-datamapper',
			'DataMapper',
			'Provides a database abstraction layer following the DataMapper pattern',
			'3.1.19',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
		);

		C_Photocrati_Installer::add_handler($this->module_id, 'C_Datamapper_Installer');
	}

	function _register_adapters()
	{
		$this->get_registry()->add_adapter('I_Component_Factory', 'A_DataMapper_Factory');
	}


	function _register_hooks()
	{
		add_filter('posts_request', array($this, 'set_custom_wp_query'), 50, 2);
		add_filter('posts_fields', array($this, 'set_custom_wp_query_fields'), 50, 2);
		add_filter('posts_where', array($this, 'set_custom_wp_query_where'), 50, 2);
        add_filter('posts_groupby', array($this, 'set_custom_wp_query_groupby'), 50, 2);
	}


	/**
	 * Sets a custom SQL query for the WP_Query class, when the Custom Post
	 * DataMapper implementation is used
	 * @param string $sql
	 * @param WP_Query $wp_query
	 * @return string
	 */
	function set_custom_wp_query($sql, $wp_query)
	{
		if ($wp_query->get('datamapper')) {

			// Set the custom query
			if (($custom_sql = $wp_query->get('custom_sql'))) {
				$sql = $custom_sql;
			}

			// Perhaps we're to initiate a delete query instead?
			elseif ($wp_query->get('is_delete')) {
				$sql = preg_replace("/^SELECT.*FROM/i", "DELETE FROM", $sql);
			}

			if ($wp_query->get('debug')) var_dump($sql);
		}
		
		return $sql;
	}

	/**
	 * Sets custom fields to select from the database
	 * @param string $fields
	 * @param WP_Query $wp_query
	 * @return string
	 */
	function set_custom_wp_query_fields($fields, $wp_query)
	{
		if ($wp_query->get('datamapper')) {
			if (($custom_fields = $wp_query->get('fields')) && $custom_fields != 'ids') {
				$fields = $custom_fields;
			}
		}

		return $fields;
	}


	/**
	 * Sets custom where clauses for a query
	 * @param string $where
	 * @param WP_Query $wp_query
	 * @return string
	 */
	function set_custom_wp_query_where($where, $wp_query)
	{
		if ($wp_query->get('datamapper')) {
			$this->add_post_title_where_clauses($where, $wp_query);
			$this->add_post_name_where_clauses($where, $wp_query);
		}

		return $where;
	}


    /**
     * Adds additional group by clauses to the SQL query
     * @param string $groupby
     * @param WP_Query $wp_query
     * @return string
     */
    function set_custom_wp_query_groupby($groupby, $wp_query)
    {
        $retval = $groupby;
        $group_by_columns = $wp_query->get('group_by_columns');
        if ($group_by_columns) {
            $retval = str_replace('GROUP BY', '', $retval);
            $columns = explode(',', $retval);
            foreach (array_reverse($columns) as $column) {
                array_unshift($group_by_columns, trim($column));
            }
            $retval = "GROUP BY ".implode(', ', $group_by_columns);
        }
        // Not all mysql servers allow access to create temporary tables which are used when doing GROUP BY
        // statements; this can potentially ruin basic queries. If no group_by_columns is set AND the query originates
        // within the datamapper we strip the "GROUP BY" clause entirely in this filter.
        else if ($wp_query->get('datamapper')) {
            $retval = '';
        }
        return $retval;
    }


	/**
	 * Formats the value of used in a WHERE IN
	 * SQL clause for use in the WP_Query where clause
	 * @param string|array $values
	 * @return string
	 */
	function format_where_in_value($values)
	{
		if (is_string($values) && strpos($values, ',') !== FALSE)
			$values = explode(", ", $values);
		elseif (!is_array($values))
			$values = array($values);

		// Quote the titles
		foreach ($values as $index => $value) {
			$values[$index] = "'{$value}'";
		}

		return implode(', ', $values);
	}


	/**
	 * Adds post_title to the where clause
	 * @param string $where
	 * @param WP_Query $wp_query
	 * @return string
	 */
	function add_post_title_where_clauses(&$where, &$wp_query)
	{
		global $wpdb;

		// Handle post_title query var
		if (($titles = $wp_query->get('post_title'))) {
			$titles = $this->format_where_in_value($titles);
			$where .= " AND {$wpdb->posts}.post_title IN ({$titles})";
		}

		// Handle post_title_like query var
		elseif (($value = $wp_query->get('post_title__like'))) {
			$where .= " AND {$wpdb->posts}.post_title LIKE '{$value}'";
		}

		return $where;
	}


	/**
	 * Adds post_name to the where clause
	 * @param string $where
	 * @param WP_Query $wp_query
	 */
	function add_post_name_where_clauses(&$where, &$wp_query)
	{
		global $wpdb;

		if (($name = $wp_query->get('page_name__like'))) {
			$where .= " AND {$wpdb->posts}.post_name LIKE '{$name}'";
		}
		elseif (($names = $wp_query->get('page_name__in'))) {
			$names = $this->format_where_in_value($names);
			$where .= " AND {$wpdb->posts}.post_name IN ({$names})";
		}
	}

    /**
     * Unserializes data
     *
     * @deprecated Used only by the Pro Lightbox
     * @param string $value
     * @return mixed
     */
	public static function unserialize($value)
	{
        return C_NextGen_Serializable::unserialize($value);
    }

    /**
     * Serializes the data
     *
     * @deprecated Used only by the Pro Lightbox
     * @param mixed $value
     * @return string
     */
    static function serialize($value)
    {
	    return C_NextGen_Serializable::serialize($value);
    }

    function get_type_list()
    {
        return array(
            'A_Datamapper_Factory' 		=> 'adapter.datamapper_factory.php',
            'C_Datamapper_Installer'	=> 'class.datamapper_installer.php',
            'C_Datamapper' 					=> 'class.datamapper.php',
            'C_Custompost_Datamapper_Driver' => 'class.custompost_datamapper_driver.php',
            'C_Customtable_Datamapper_Driver' => 'class.customtable_datamapper_driver.php',
            'C_Datamapper_Driver_Base'	=> 'class.datamapper_driver_base.php',
            'C_Datamapper_Model' 		=> 'class.datamapper_model.php',
            'M_Datamapper' 				=> 'module.datamapper.php'
        );
    }
}

class C_DataMapper_Installer
{
	function __construct()
	{
		$this->settings = C_NextGen_Settings::get_instance();
	}

	function install()
	{
		$this->settings->set_default_value('datamapper_driver', 'custom_post_datamapper');
	}
}
new M_DataMapper();