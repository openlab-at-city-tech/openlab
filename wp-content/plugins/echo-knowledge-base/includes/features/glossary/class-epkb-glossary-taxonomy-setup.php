<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register Glossary taxonomy
 */
class EPKB_Glossary_Taxonomy_Setup {

	const GLOSSARY_TAXONOMY = 'epkb_glossary';

	public function __construct() {
		add_action( 'init', array( $this, 'register_glossary_taxonomy' ), 10 );
		add_action( 'eckb_add_kb_submenu', array( 'EPKB_Glossary_Page', 'add_menu_item' ) );
	}

	public function register_glossary_taxonomy() {

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		if ( $kb_config['glossary_enable'] !== 'on' ) {
			return;
		}

		register_taxonomy( self::GLOSSARY_TAXONOMY, array(), array(
			'hierarchical'      => false,
			'public'            => false,
			'show_ui'           => false,
			'show_in_rest'      => false,
			'show_admin_column' => false,
			'query_var'         => false,
			'rewrite'           => false,
			'has_archive'       => false,
		) );

		register_term_meta( self::GLOSSARY_TAXONOMY, 'epkb_glossary_status', array(
			'type'    => 'string',
			'single'  => true,
			'default' => 'publish',
		) );

		register_term_meta( self::GLOSSARY_TAXONOMY, 'epkb_glossary_sort_key', array(
			'type'    => 'string',
			'single'  => true,
			'default' => '',
		) );
	}
}
