<?php

namespace TheLion\OutoftheBox\API\Dropbox\Store;

class DatabasePersistentDataStore implements PersistentDataStoreInterface {

  /**
   * Session Variable Prefix
   *
   * @var string
   */
  protected $prefix;

  /**
   * Create a new SessionPersistentDataStore instance
   *
   * @param string $prefix Session Variable Prefix
   */
  public function __construct($prefix = "outofthebox_csrf_token_") {
    $this->prefix = $prefix;
  }

  /**
   * Get a value from the store
   *
   * @param  string $key Data Key
   *
   * @return string
   */
  public function get($key) {
    if (get_transient($this->prefix . $key)) {
      return get_transient($this->prefix . $key);
    }

    return null;
  }

  /**
   * Set a value in the store
   * @param string $key   Data Key
   * @param string $value Data Value
   *
   * @return void
   */
  public function set($key, $value) {
    set_transient($this->prefix . $key, $value, HOUR_IN_SECONDS);
  }

  /**
   * Clear the key from the store
   *
   * @param $key Data Key
   *
   * @return void
   */
  public function clear($key) {
    delete_transient($this->prefix . $key);
  }

}
