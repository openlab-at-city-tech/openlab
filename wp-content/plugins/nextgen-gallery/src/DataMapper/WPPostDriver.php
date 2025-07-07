<?php

namespace Imagely\NGG\DataMapper;

use Imagely\NGG\Util\Serializable;

class WPPostDriver extends DriverBase {

	public $cache;
	public $primary_key_column = 'ID';
	public $query_args         = [];
	public $use_cache          = true;

	public static $post_table_columns = [];

	public $ID;
	public $post_parent;

	/**
	 * @throws \Exception
	 */
	public function __construct( $object_name = '' ) {
		if ( strlen( $object_name ) > 20 ) {
			throw new \Exception( 'The custom post name can be no longer than 20 characters long' );
		}
		parent::__construct( $object_name );
	}

	public function lookup_columns() {
		if ( empty( self::$post_table_columns ) ) {
			$columns = parent::lookup_columns();
			foreach ( $columns as $column ) {
				self::$post_table_columns[] = $column;
			}
		} else {
			foreach ( self::$post_table_columns as $column ) {
				$this->_table_columns[] = $column;
			}
		}
	}

	/**
	 * Gets the name of the table
	 *
	 * @global string $table_prefix
	 * @return string
	 */
	public function get_table_name() {
		global $table_prefix;
		return $table_prefix . 'posts';
	}

	/**
	 * Returns a list of querable table columns for posts
	 *
	 * @return array
	 */
	public function _get_querable_table_columns() {
		return [ 'name', 'author', 'date', 'title', 'modified', 'menu_order', 'parent', 'ID', 'rand', 'comment_count' ];
	}

	/**
	 * Specifies an order clause
	 *
	 * @param string $order_by
	 * @param string $direction
	 * @return WPPostDriver
	 */
	public function order_by( $order_by, $direction = 'ASC' ) {
		// Make an exception for the rand() method.
		$order_by = preg_replace( '/rand\(\s*\)/', 'rand', $order_by );

		if ( in_array( $order_by, $this->_get_querable_table_columns() ) ) {
			$this->query_args['orderby'] = $order_by;
		} else {
			// ordering by a meta value.
			$this->query_args['orderby']  = 'meta_value';
			$this->query_args['meta_key'] = $order_by;
		}

		$this->query_args['order'] = $direction;

		return $this;
	}

	/**
	 * Specifies a limit and optional offset
	 *
	 * @param int      $max
	 * @param int|bool $offset (optional)
	 * @return object
	 */
	public function limit( $max, $offset = false ) {
		if ( $max ) {
			$this->query_args['paged'] = true;
			if ( $offset ) {
				$this->query_args['offset'] = $offset;
			} else {
				unset( $this->query_args['offset'] );
			}

			$this->query_args['posts_per_page'] = $max;
		}

		return $this;
	}

	/**
	 * Specifies a list of columns to group by
	 *
	 * @param array|string $columns
	 * @return object
	 */
	public function group_by( $columns = [] ) {
		if ( ! isset( $this->query_args['group_by_columns'] ) ) {
			$this->query_args['group_by_columns'] = $columns;
		} else {
			$this->query_args['group_by_columns'] = array_merge(
				$this->query_args['group_by_columns'],
				$columns
			);
		}

		return $this;
	}

	/**
	 * Adds a WP_Query where clause
	 *
	 * @param array  $where_clauses
	 * @param string $join
	 */
	public function add_where_clause( $where_clauses, $join ) {
		foreach ( $where_clauses as $clause ) {

			// Determine where what the where clause is comparing.
			switch ( $clause['column'] ) {
				case 'author':
				case 'author_id':
					$this->query_args['author'] = $clause['value'];
					break;
				case 'author_name':
					$this->query_args['author_name'] = $clause['value'];
					break;
				case 'cat':
				case 'cat_id':
				case 'category_id':
					switch ( $clause['compare'] ) {
						case '=':
						case 'BETWEEN';
						case 'IN';
							if ( ! isset( $this->query_args['category__in'] ) ) {
								$this->query_args['category__in'] = [];
							}
							$this->query_args['category__in'][] = $clause['value'];
							break;
						case '!=':
						case 'NOT BETWEEN';
						case 'NOT IN';
							if ( ! isset( $this->query_args['category__not_in'] ) ) {
								$this->query_args['category__not_in'] = [];
							}
							$this->query_args['category__not_in'][] = $clause['value'];
							break;
					}
					break;
				case 'category_name':
					$this->query_args['category_name'] = $clause['value'];
					break;
				case 'post_id':
				case $this->get_primary_key_column():
					switch ( $clause['compare'] ) {
						case '=':
						case 'IN';
						case 'BETWEEN';
							if ( ! isset( $this->query_args['post__in'] ) ) {
								$this->query_args['post__in'] = [];
							}
							$this->query_args['post__in'][] = $clause['value'];
							break;
						default:
							if ( ! isset( $this->query_args['post__not_in'] ) ) {
								$this->query_args['post__not_in'] = [];
							}
							$this->query_args['post__not_in'][] = $clause['value'];
							break;
					}
					break;
				case 'pagename':
				case 'postname':
				case 'page_name':
				case 'post_name':
					if ( $clause['compare'] == 'LIKE' ) {
						$this->query_args['page_name__like'] = $clause['value'];
					} elseif ( $clause['compare'] == '=' ) {
						$this->query_args['pagename'] = $clause['value'];
					} elseif ( $clause['compare'] == 'IN' ) {
						$this->query_args['page_name__in'] = $clause['value'];
					}
					break;
				case 'post_title':
					// Post title uses custom WHERE clause.
					if ( $clause['compare'] == 'LIKE' ) {
						$this->query_args['post_title__like'] = $clause['value'];
					} else {
						$this->query_args['post_title'] = $clause['value'];
					}
					break;
				default:
					// Must be metadata.
					$clause['key'] = $clause['column'];
					unset( $clause['column'] );

					// Convert values to array, when required.
					if ( in_array( $clause['compare'], [ 'IN', 'BETWEEN' ] ) ) {
						$clause['value'] = explode( ',', $clause['value'] );
						foreach ( $clause['value'] as &$val ) {
							if ( ! is_numeric( $val ) ) {

								// In the _parse_where_clause() method, we.
								// quote the strings and add slashes.
								$val = stripslashes( $val );
								$val = substr( $val, 1, strlen( $val ) - 2 );
							}
						}
					}

					if ( ! isset( $this->query_args['meta_query'] ) ) {
						$this->query_args['meta_query'] = [];
					}
					$this->query_args['meta_query'][] = $clause;
					break;
			}
		}

		// If any where clauses have been added, specify how the conditions.
		// will be conbined/joined.
		if ( isset( $this->query_args['meta_query'] ) ) {
			$this->query_args['meta_query']['relation'] = $join;
		}
	}

	/**
	 * Converts a post to an entity.
	 *
	 * @param \stdClass $post
	 * @param boolean   $model
	 * @return \stdClass
	 */
	public function convert_post_to_entity( $post, $model = false ) {
		$entity = new \stdClass();

		// Unserialize the post_content_filtered field.
		if ( is_string( $post->post_content_filtered ) ) {
			if ( ( $post_content = Serializable::unserialize( $post->post_content_filtered ) ) ) {
				foreach ( $post_content as $key => $value ) {
					$post->$key = $value;
				}
			}
		}

		// Unserialize the post content field.
		if ( is_string( $post->post_content ) ) {
			if ( ( $post_content = Serializable::unserialize( $post->post_content ) ) ) {
				foreach ( $post_content as $key => $value ) {
					$post->$key = $value;
				}
			}
		}

		// Copy post fields to entity.
		unset( $post->post_content );
		unset( $post->post_content_filtered );
		foreach ( $post as $key => $value ) {
			$entity->$key = $value;
		}

		$this->_convert_to_entity( $entity );

		return $model ? $this->convert_to_model( $entity ) : $entity;
	}

	/**
	 * Converts an entity to a post
	 *
	 * @param object $entity
	 * @return object
	 */
	public function _convert_entity_to_post( $entity ) {
		// Was a model passed instead of an entity?
		$post = $entity;

		// Create the post content.
		$post_content = clone $post;
		foreach ( $this->_table_columns as $column ) {
			unset( $post_content->$column );
		}
		unset( $post->id_field );
		unset( $post->post_content_filtered );
		unset( $post->post_content );
		$post->post_content          = Serializable::serialize( $post_content );
		$post->post_content_filtered = $post->post_content;
		$post->post_type             = $this->get_object_name();

		// Sometimes an entity can contain a data stored in an array or object
		// Those will be removed from the post, and serialized in the
		// post_content field.
		foreach ( $post as $key => $value ) {
			if ( in_array( strtolower( gettype( $value ) ), [ 'object', 'array' ] ) ) {
				unset( $post->$key );
			}
		}

		// A post required a title.
		if ( empty( $post->post_title ) ) {
			$post->post_title = $this->get_post_title( $post );
		}

		// A post also requires an excerpt.
		if ( empty( $post->post_excerpt ) ) {
			$post->post_excerpt = $this->get_post_excerpt( $post );
		}

		return $post;
	}

	/**
	 * Returns the WordPress database class
	 *
	 * @global \wpdb $wpdb
	 * @return \wpdb
	 */
	public function _wpdb() {
		global $wpdb;
		return $wpdb;
	}

	/**
	 * Flush and update all postmeta for a particular post
	 *
	 * @param int $post_id
	 */
	public function _flush_and_update_postmeta( $post_id, $entity, $omit = [] ) {
		// We need to insert post meta data for each property
		// Unfortunately, that means flushing all existing postmeta
		// and then inserting new values. Depending on the number of
		// properties, this could be slow. So, we directly access the database.
		/* @var \wpdb $wpdb */
		global $wpdb;
		if ( ! is_array( $omit ) ) {
			$omit = [ $omit ];
		}

		// By default, we omit creating meta values for columns in the posts table.
		$omit = array_merge( $omit, $this->_table_columns );

		// Delete the existing meta values.
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE post_id = %s", $post_id ) );

		// Create query for new meta values.
		$sql_parts = [];
		foreach ( $entity as $key => $value ) {
			if ( in_array( $key, $omit ) ) {
				continue;
			}
			if ( is_array( $value ) or is_object( $value ) ) {
				$value = Serializable::serialize( $value );
			}
			$sql_parts[] = $wpdb->prepare( '(%s, %s, %s)', $post_id, $key, $value );
		}

		// The following $sql_parts is already sent through $wpdb->prepare() -- look directly above this line
		//
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) VALUES " . implode( ',', $sql_parts ) );
	}

	/**
	 * Determines whether the current statement is SELECT
	 *
	 * @return boolean
	 */
	public function is_select_statement() {
		return isset( $this->query_args['is_select'] ) && $this->query_args['is_select'];
	}

	/**
	 * Runs the query
	 *
	 * @param string|bool $sql (optional) Run the specified query
	 * @param object|bool $model (optional)
	 * @param bool        $convert_to_entities (optional) Default = true
	 * @return array
	 */
	public function run_query( $sql = false, $model = true, $convert_to_entities = true ) {
		$retval  = [];
		$results = [];

		// All of our custom fields are stored as post meta, but is also stored as a serialized value in the post_content
		// field. Because of this, we don't need to look up and cache the post meta values.
		$this->query_args['no_found_posts']         = false;
		$this->query_args['update_post_meta_cache'] = false;

		// Don't cache any manual SQL query.
		if ( $sql ) {
			$this->query_args['cache_results'] = false;
			$this->query_args['custom_sql']    = $sql;
		}

		// If this is a select query, then try fetching the results from cache.
		$cache_key = md5( json_encode( $this->query_args ) );
		if ( $this->is_select_statement() && $this->use_cache ) {
			$results = $this->get_from_cache( $cache_key );
		}

		// Execute the query.
		if ( ! $results ) {
			$query = new \WP_Query( [ 'datamapper' => true, 'fields' => '*' ] );
			if ( isset( $this->debug ) ) {
				$this->query_args['debug'] = true;
			}
			$query->query_vars = $this->query_args;

			add_action( 'pre_get_posts', [ &$this, 'set_query_args' ], PHP_INT_MAX - 1, 1 );

			$results = $query->get_posts();

			// Cache the result.
			if ( $this->is_select_statement() ) {
				$this->cache( $cache_key, $results );
			}

			remove_action( 'pre_get_posts', [ &$this, 'set_query_args' ], PHP_INT_MAX - 1 );
		}

		// Convert the result.
		if ( $convert_to_entities ) {
			foreach ( $results as $row ) {
				$retval[] = $this->convert_post_to_entity( $row, $model );
			}
		} else {
			$retval = $results;
		}

		// Ensure that we return an empty array when there are no results.
		if ( ! $retval ) {
			$retval = [];
		}

		return $retval;
	}

	/**
	 * Ensure that the query args are set. We need to do this in case a third-party
	 * plugin overrides our query.
	 *
	 * @param $query
	 */
	public function set_query_args( $query ) {
		if ( $query->get( 'datamapper' ) ) {
			$query->query_vars = $this->query_args;
		}
		$filter                                = isset( $query->query_vars['suppress_filters'] ) ? $query->query_vars['suppress_filters'] : false;
		$query->query_vars['suppress_filters'] = apply_filters( 'wpml_suppress_filters', $filter );
	}

	/**
	 * Returns the number of total records/entities that exist
	 *
	 * @return int
	 */
	public function count() {
		$this->select( $this->get_primary_key_column() );
		$retval = $this->run_query( false, false, false );

		return count( $retval );
	}

	/**
	 * Used to select which fields should be returned. NOT currently used by
	 * this implementation of the datamapper driver
	 *
	 * @param string $fields
	 * @return WPPostDriver
	 */
	public function select( $fields = '*' ) {
		$this->query_args = [
			'post_type'      => $this->get_object_name(),
			'paged'          => false,
			'fields'         => $fields,
			'post_status'    => 'any',
			'datamapper'     => true,
			'posts_per_page' => -1,
			'is_select'      => true,
			'is_delete'      => false,
		];

		return $this;
	}

	/**
	 * Destroys/deletes an entity from the database
	 *
	 * @param \stdClass|Model|int $entity
	 * @param bool                $skip_trash (optional) Default = true
	 * @return bool
	 */
	public function destroy( $entity, $skip_trash = true ) {
		$retval = false;

		$key = $this->get_primary_key_column();

		// Find the id of the entity.
		if ( is_object( $entity ) && isset( $entity->$key ) ) {
			$id = (int) $entity->$key;
		} else {
			$id = (int) $entity;
		}

		// If we have an ID, then delete the post.
		if ( is_integer( $id ) ) {
			// TODO: We assume that we can skip the trash. Is that correct?
			// FYI, Deletes postmeta as wells.
			if ( is_object( wp_delete_post( $id, true ) ) ) {
				$retval = true;
			}
		}

		return $retval;
	}

	/**
	 * Saves an entity to the database
	 *
	 * @param object $entity
	 * @return int Post ID
	 */
	public function save_entity( $entity ) {
		$post        = $this->_convert_entity_to_post( $entity );
		$primary_key = $this->get_primary_key_column();

		// Avoid pre_replace deprecation exception on sanitize_mime_type() by ensuring mime type is not null.
		if ( is_null( $post->post_mime_type ) ) {
			$post->post_mime_type = '';
		}

		// TODO: unsilence this. WordPress 3.9-beta2 is generating an error that should be corrected before its final release.
		if ( ( $post_id = wp_insert_post( (array) $post ) ) ) {
			$new_entity = $this->find( $post_id, true );
			if ( $new_entity ) {
				foreach ( get_object_vars( $new_entity ) as $key => $value ) {
					$entity->$key = $value;
				}
			}

			// Save properties as post meta.
			$this->_flush_and_update_postmeta( $post_id, $entity );

			$entity->$primary_key = $post_id;

			// Clean cache.
			$this->cache = [];
		}
		$entity->id_field = $primary_key;

		return $post_id;
	}

	/**
	 * Starts a new DELETE statement.
	 */
	public function delete() {
		$this->select();
		$this->query_args['is_select'] = false;
		$this->query_args['is_delete'] = true;
		return $this;
	}

	/**
	 * Returns the title of the post. Used when post_title is not set
	 *
	 * @param \stdClass $entity
	 * @return string
	 */
	public function get_post_title( $entity ) {
		return "Untitled {$this->get_object_name()}";
	}

	/**
	 * Returns the excerpt of the post. Used when post_excerpt is not set
	 *
	 * @param \stdClass $entity
	 * @return string
	 */
	public function get_post_excerpt( $entity ) {
		return '';
	}
}
