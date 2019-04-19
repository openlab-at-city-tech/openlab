<?php
/*  FV Wordpress Flowplayer - HTML5 video player with Flash fallback    
    Copyright (C) 2013  Foliovision

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// player meta data instance with options that's stored in a DB
class FV_Player_Db_Player_Meta {

  private
    $id, // automatic ID for the meta data
    $is_valid = true, // used when loading meta data from DB to determine whether we've found it
    $id_player, // DB ID of the video to which this meta data belongs
    $meta_key, // arbitrary meta key
    $meta_value, // arbitrary meta value
    $DB_Instance = null;

  private static $db_table_name;

  /**
   * @param mixed $meta_value
   */
  public function setMetaValue( $meta_value ) {
    $this->meta_value = $meta_value;
  }

  /**
   * @return int
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return int
   */
  public function getIdPlayer() {
    return $this->id_player;
  }

  /**
   * @return string
   */
  public function getMetaKey() {
    return $this->meta_key;
  }

  /**
   * @return mixed
   */
  public function getMetaValue() {
    return $this->meta_value;
  }

  /**
   * @return bool
   */
  public function getIsValid() {
    return $this->is_valid;
  }

  /**
   * Initializes database name, including WP prefix
   * once WPDB class is initialized.
   *
   * @return string Returns the actual table name for this ORM class.
   */
  public static function init_db_name() {
    global $wpdb;

    self::$db_table_name = $wpdb->prefix.'fv_player_playermeta';
    return self::$db_table_name;
  }
  
  /**
   * Checks for DB tables existence and creates it as necessary.
   *
   * @param $wpdb The global WordPress database object.
   */
  private function initDB($wpdb) {
    global $fv_fp, $fv_wp_flowplayer_ver;

    self::init_db_name();

    if( !$fv_fp->_get_option('player_meta_model_db_checked') || $fv_fp->_get_option('player_meta_model_db_checked') != $fv_wp_flowplayer_ver ) {
      $sql = "
CREATE TABLE " . self::$db_table_name . " (
  id bigint(20) unsigned NOT NULL auto_increment,
  id_player bigint(20) unsigned NOT NULL default '0',
  meta_key varchar(255) NOT NULL,
  meta_value longtext NOT NULL,
  PRIMARY KEY  (id),
  KEY id_player (id_player),
  KEY meta_key (meta_key(191))
)" . $wpdb->get_charset_collate() . ";";
      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta( $sql );
      $fv_fp->_set_option('player_meta_model_db_checked', $fv_wp_flowplayer_ver);
    }
  }

  /**
   * Makes this meta data object linked to a record in database.
   * Used for player preview purposes.
   *
   * @param int $id The DB ID to which we'll link this meta data record.
   * @param bool $id_is_player If true, link to an actual player ID will be made.
   */
  public function link2db($id, $id_is_player = false) {
    if (!$id_is_player) {
      $this->id = (int) $id;
    } else {
      $this->id_player = (int) $id;
    }
  }

  /**
   * FV_Player_Db_Player_Meta constructor.
   *
   * @param int $id         ID of player meta data to load data from the DB for.
   * @param array $options  Options for a newly created player meta data that will be stored in a DB.
   * @param FV_Player_Db    $DB_Cache Instance of the DB shortcode global object that handles caching
   *                        of videos, players and their meta data.
   *
   * @throws Exception When no valid ID nor options are provided.
   */
  function __construct($id, $options = array(), $DB_Cache = null) {
    global $wpdb;

    if ($DB_Cache) {
      $this->DB_Instance = $DB_Cache;
    } else {
      global $FV_Player_Db;
      $this->DB_Instance = $DB_Cache = $FV_Player_Db;
    }

    $this->initDB($wpdb);
    $multiID = is_array($id);

    // check whether we're not trying to load data for a single player
    // rather than meta data by its own ID
    $load_for_player = false;
    $force_cache_update = false;

    if (is_array($options) && count($options) && isset($options['id_player']) && is_array($options['id_player'])) {
      $load_for_player = true;
      $multiID = true;
      $id = $options['id_player'];
      // reset this, so we don't try to create a new record below
      $options = array();
    }

    // if we've got options, fill them in instead of querying the DB,
    // since we're storing new player meta data into the DB in such case
    if (is_array($options) && count($options)) {
      foreach ($options as $key => $value) {
        if (property_exists($this, $key)) {
          if ($key !== 'id') {
            $this->$key = stripslashes($value);
          }
        } else {
          // generate warning
          trigger_error('Unknown property for new DB player meta data item: ' . $key);
        }
      }
    } else if ($multiID || (is_numeric($id) && $id > 0)) {
      /* @var $cache FV_Player_Db_Player_Meta[] */
      $cache = ($DB_Cache ? $DB_Cache->getPlayerMetaCache() : array());
      $all_cached = false;
      $some_meta_exist = false;

      // no options, load data from DB
      if ($multiID) {
        $query_ids = array();

        // make sure we have numeric IDs and that they're not cached yet
        $is_cached = false;
        foreach ($id as $id_key => $id_value) {
          if ($load_for_player) {
            $is_cached = isset($cache[$id_value]);
            $some_meta_exist = ($is_cached ? count($cache[$id_value]) : false);
          } else {
            // run through all the cached data and check
            // whether our meta data ID was not cached yet
            foreach ($cache as $player_meta) {
              if (isset($player_meta[$id_value])) {
                $is_cached = true;
                $some_meta_exist = (count($player_meta[$id_value]) ? true : false);
              }
            }
          }

          // select from DB if not cached yet
          if (!$is_cached) {
            $query_ids[ $id_key ] = (int) $id_value;
          }

          $id[$id_key] = (int) $id_value;
        }

        if (count($query_ids)) {
          // load multiple player metas via their IDs but a single query and return their values
          $meta_data = $wpdb->get_results( 'SELECT * FROM ' . self::$db_table_name . ' WHERE ' . ( $load_for_player ? 'id_player' : 'id' ) . ' IN(' . implode( ',', $query_ids ) . ')' );

          // run through all of the meta data and
          // fill the ones that were not found with blank arrays
          // for cache-filling purposes
          if (!is_array($meta_data)) {
            $meta_data = array();
          }

          foreach ($query_ids as $q_id) {
            $meta_found = false;
            foreach ($meta_data as $m_data) {
              if (($load_for_player && $m_data->id_player == $q_id) || (!$load_for_player && $m_data->id == $q_id)) {
                $meta_found = true;
                break;
              }
            }

            // if we have no meta data for the requested ID,
            // fill it with an empty array
            if (!$meta_found) {
              $force_cache_update = true;

              if ($load_for_player) {
                // for player, create an empty array with no meta
                $cache[$q_id] = array();
              } else {
                // for a single meta, initialize it with null value
                // on a 0-id player (which obviously cannot exist,
                // so we can use it for cache-checking purposes)
                $cache[0][$q_id] = null;
              }
            }
          }
        } else {
          $all_cached = true;
        }
      } else {
        $is_cached = false;
        if ($load_for_player) {
          $is_cached = isset($cache[$id]);
          $some_meta_exist = ($is_cached ? count($cache[$id]) : false);
        } else {
          // run through all the cached data and check
          // whether our meta data ID was not cached yet
          foreach ($cache as $player_id => $player_meta) {
            if (isset($player_meta[$id])) {
              $is_cached = true;
              $some_meta_exist = (count($player_meta[$id]) ? true : false);
            }
          }
        }

        if (!$is_cached) {
          // load a single player meta data record
          $meta_data = $wpdb->get_results( 'SELECT * FROM ' . self::$db_table_name . ' WHERE id = ' . intval($id) );
          
          // run through all of the meta data and
          // fill the ones that were not found with blank arrays
          // for cache-filling purposes
          if (!is_array($meta_data)) {
            $meta_data = array();
          }

          $meta_found = false;
          foreach ($meta_data as $m_data) {
            if (($load_for_player && $m_data->id_player == $id) || (!$load_for_player && $m_data->id == $id)) {
              $meta_found = true;
              break;
            }
          }

          // if we have no meta data for the requested ID,
          // fill it with an empty array
          if (!$meta_found) {
            $force_cache_update = true;

            if ($load_for_player) {
              // for player, create an empty array with no meta
              $cache[$id] = array();
            } else {
              // for a single meta, initialize it with null value
              // on a 0-id player (which obviously cannot exist,
              // so we can use it for cache-checking purposes)
              $cache[0][$id] = null;
            }
          }
        } else {
          $all_cached = true;
        }
      }

      if (isset($meta_data) && $meta_data && count($meta_data)) {
        // single ID, just populate our own data
        if (!$multiID) {
          // fill-in our internal variables, as they have the same name as DB fields (ORM baby!)
          foreach ( $meta_data as $key => $value ) {
            $this->$key = stripslashes($value);
          }

          // cache this meta in DB object
          if ($DB_Cache) {
            $cache[$this->id_player][$this->id] = $this;
          }
        } else {
          // multiple IDs, create new player meta objects for each of them except the first one,
          // for which we'll use this instance
          $first_done = false;
          foreach ($meta_data as $db_record) {
            if (!$first_done) {
              // fill-in our internal variables
              foreach ( $db_record as $key => $value ) {
                $this->$key = stripslashes($value);
              }

              $first_done = true;

              // cache this meta in DB object
              if ($DB_Cache) {
                $cache[$db_record->id_player][$this->id] = $this;
              }
            } else {
              // create a new player meta object and populate it with DB values
              $record_id = $db_record->id;
              // if we don't unset this, we'll get warnings
              unset($db_record->id);

              if ($DB_Cache && !$DB_Cache->isPlayerMetaCached($db_record->id_player, $record_id)) {
                $player_meta_object = new FV_Player_Db_Player_Meta( null, get_object_vars( $db_record ), $DB_Cache );
                $player_meta_object->link2db( $record_id );

                // cache this meta in DB object
                if ( $DB_Cache ) {
                  $cache[ $db_record->id_player ][ $record_id ] = $player_meta_object;
                }
              }
            }
          }
        }
      } else if ($all_cached && $some_meta_exist) {
        // fill the data for this class with data of the cached class
        if ($multiID) {
          $cached_meta = reset($id);
        } else {
          $cached_meta = $id;
        }

        // find the meta in cache and reassign $cached_meta
        foreach ($cache as $player_id => $player) {
          if ($load_for_player && $player_id == $cached_meta ) {
            // load first meta for the requested player
            $cached_meta = reset( $player );
            break;
          } else if (!$load_for_player && isset($player[$cached_meta])) {
            $cached_meta = $player[$cached_meta];
            break;
          }
        }

        // $cached_meta will remain numeric if there are no meta data in the database
        if ($cached_meta instanceof FV_Player_Db_Player_Meta) {
          foreach ( $cached_meta->getAllDataValues() as $key => $value ) {
            $this->$key = stripslashes($value);
          }
        }
      } else {
        $this->is_valid = false;
      }
    } else {
      throw new Exception('No options nor a valid ID was provided for DB player meta data item.');
    }

    // update cache, if changed
    if (isset($cache) && ($force_cache_update || !isset($all_cached) || !$all_cached)) {
      $this->DB_Instance->setPlayerMetaCache($cache);
    }
  }

  /**
   * Returns all options data for this player meta object.
   *
   * @return array Returns all options data for this player meta object.
   */
  public function getAllDataValues() {
    $data = array();
    foreach (get_object_vars($this) as $property => $value) {
      if ($property != 'is_valid' && $property != 'db_table_name' && $property != 'DB_Instance') {
        $data[$property] = $value;
      }
    }

    return $data;
  }

  /**
   * Stores new player meta data item or updates and existing one
   * in the database.
   *
   * @return bool|int Returns record ID if successful, false otherwise.
   */
  public function save() {
    global $wpdb;

    // prepare SQL
    $is_update   = ($this->id ? true : false);
    $sql         = ($is_update ? 'UPDATE' : 'INSERT INTO').' '.self::$db_table_name.' SET ';
    $data_keys   = array();
    $data_values = array();

    foreach (get_object_vars($this) as $property => $value) {
      if ($property != 'id' && $property != 'is_valid' && $property != 'db_table_name' && $property != 'DB_Instance') {
        $is_player_id = ($property == 'id_player');
        $data_keys[] = $property . ' = '.($is_player_id ? (int) $value : '%s');

        if (!$is_player_id) {
          $data_values[] = $value;
        }
      }
    }

    $sql .= implode(',', $data_keys);

    if ($is_update) {
      $sql .= ' WHERE id = ' . $this->id;
    }

    $wpdb->query( $wpdb->prepare( $sql, $data_values ));

    if (!$is_update) {
      $this->id = $wpdb->insert_id;
    }

    if (!$wpdb->last_error) {
      // add this meta into cache
      $cache = $this->DB_Instance->getPlayerMetaCache();
      $cache[$this->id_player][$this->id] = $this;
      $this->DB_Instance->setPlayerMetaCache($cache);

      return $this->id;
    } else {
      /*var_export($wpdb->last_error);
      var_export($wpdb->last_query);*/
      return false;
    }
  }

  /**
   * Prepares this class' properties for export
   * and returns them in an associative array.
   *
   * @return array Returns an associative array of this class' properties and their values.
   */
  public function export() {
    $export_data = array();
    foreach (get_object_vars($this) as $property => $value) {
      if ($property != 'id' && $property != 'id_player' && $property != 'is_valid' && $property != 'db_table_name' && $property != 'DB_Instance') {
        $export_data[$property] = $value;
      }
    }

    return $export_data;
  }


  /**
   * Removes meta data instance from the database.
   *
   * @return bool Returns true if the delete was successful, false otherwise.
   */
  public function delete() {
    // not a DB meta? no delete
    if (!$this->is_valid) {
      return false;
    }

    global $wpdb;

    $wpdb->delete(self::$db_table_name, array('id' => $this->id));

    if (!$wpdb->last_error) {
      // remove this meta from cache
      $cache = $this->DB_Instance->getPlayerMetaCache();
      if (isset($cache[$this->id_player][$this->id])) {
        unset($cache[$this->id_player][$this->id]);
        $this->DB_Instance->setPlayerMetaCache($cache);
      }

      return true;
    } else {
      /*var_export($wpdb->last_error);
      var_export($wpdb->last_query);*/
      return false;
    }
  }
}
