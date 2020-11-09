<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Traits\Singleton;

/**
 * Menu Cache
 *
 * @author OnPoint Plugins
 */
class Cache {
	use Singleton;

	const CACHE_GROUP     = 'advanced-sidebar-menu';
	const CHILD_PAGES_KEY = 'child-pages';


	/**
	 * Actions.
	 */
	protected function hook() {
		add_action( 'save_post', [ $this, 'clear_cache_group' ] );
	}


	/**
	 * Clear all items in this cache group
	 *
	 * @return void
	 */
	public function clear_cache_group() {
		wp_cache_set( 'last_changed', microtime(), self::CACHE_GROUP . ':' . ADVANCED_SIDEBAR_BASIC_VERSION );
	}


	/**
	 * Get unique key for this group
	 *
	 * @return string
	 */
	public function get_cache_group() {
		$key = wp_cache_get_last_changed( self::CACHE_GROUP . ':' . ADVANCED_SIDEBAR_BASIC_VERSION );
		return self::CACHE_GROUP . ':' . ADVANCED_SIDEBAR_BASIC_VERSION . ':' . $key;
	}


	/**
	 * Retrieve a post's child pages from the cache.
	 * If no exist in the cache will return false.
	 *
	 * @param List_Pages $class - Full menu class with all properties set.
	 *
	 * @return array|false
	 */
	public function get_child_pages( $class ) {
		$key = $this->get_key_from_asm( $class );
		$all_child_pages = (array) wp_cache_get( self::CHILD_PAGES_KEY, $this->get_cache_group() );
		if ( isset( $all_child_pages[ $key ] ) ) {
			return $all_child_pages[ $key ];
		}
		return false;
	}


	/**
	 * Add a post and its children to the cache
	 * Uses a global key for all posts so this appends to an array
	 *
	 * @param List_Pages $class       - Full menu class with all properties set.
	 * @param array      $child_pages - List of child pages to store.
	 *
	 * @return void
	 */
	public function add_child_pages( $class, $child_pages ) {
		$key = $this->get_key_from_asm( $class );
		$all_child_pages = (array) wp_cache_get( self::CHILD_PAGES_KEY, $this->get_cache_group() );
		$all_child_pages[ $key ] = $child_pages;
		wp_cache_set( self::CHILD_PAGES_KEY, $all_child_pages, $this->get_cache_group() );
	}


	/**
	 * There are many possibilities for properties
	 * set to the object used for generations.
	 * To guarantee we have a unique id for the cache
	 * we serialize the whole object and hash it
	 *
	 * @param List_Pages $class - Full menu class with all properties set.
	 *
	 * @return string
	 */
	private function get_key_from_asm( $class ) {
		$string = serialize( $class ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		return md5( $string );
	}
}
