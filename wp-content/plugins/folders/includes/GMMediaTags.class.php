<?php
/**
 * GMMediaTags class
 *
 * @package GMMediaTags
 * @author Giuseppe Mazzapica
 *
 */
class GMMediaTags {
	
	
	/**
	* Class version
	*
	* @since	0.1.0
	*
	* @access	protected
	*
	* @var	string
	*/
	protected static $version = '0.1.1';
	
	
	
	
	/**
	* Prevent register method from run more than one time. 
	*
	* @since	0.1.0
	*
	* @access	protected
	*
	* @var	bool
	*/
	protected static $done = false;
	
	
	
	
	/**
	 * Constructor. Doing nothing
	 *
	 * @since	0.1.0
	 *
	 * @access	public
	 * @return	null
	 *
	 */
	function __construct() {
		_doing_it_wrong( 'GMMediaTags::__construct', 'GMMediaTags Class is intented to be used statically.' );
	}
	

	
	
	/**
	 * Initialize the plugin. Run on 'after_setup_theme' hook
	 *
	 * @since	0.1.0
	 *
	 * @access	public
	 * @return	null
	 *
	 */
	static function init() {
		
		if ( ! defined('GMMEDIATAGSPATH') ) die();
		
		add_action('init', array(__CLASS__, 'register'), 999 );
		add_action('admin_init', array(__CLASS__, 'admin_init') );
		
	}
	
	
	static function register() {
		
		self::$done = true;
		
	}
	
	
	
	
	/**
	 * Add the action for backend. Run on 'admin_init' hook
	 *
	 * @since	0.1.0
	 *
	 * @access	public
	 * @return	null
	 *
	 */
	static function admin_init() {
		require( GMMEDIATAGSPATH . 'includes/GMMediaTagsAdmin.class.php');
		GMMediaTagsAdmin::init();
	}

}