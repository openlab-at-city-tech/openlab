<?php 
namespace ElementsKit_Lite\Modules\Header_Footer;

defined( 'ABSPATH' ) || exit;

class Cpt_Hooks {
	public static $instance = null;

	public function __construct() {

		add_action( 'admin_init', array( $this, 'add_author_support_to_column' ), 10 );
		add_filter( 'manage_elementskit_template_posts_columns', array( $this, 'set_columns' ) );
		add_action( 'manage_elementskit_template_posts_custom_column', array( $this, 'render_column' ), 10, 2 );
		add_filter( 'parse_query', array( $this, 'query_filter' ) );
	}

	public function add_author_support_to_column() {
		add_post_type_support( 'elementskit_template', 'author' ); 
	}

	/**
	 * Set custom column for template list.
	 */
	public function set_columns( $columns ) {

		$date_column   = $columns['date'];
		$author_column = $columns['author'];

		unset( $columns['date'] );
		unset( $columns['author'] );

		$columns['type']      = esc_html__( 'Type', 'elementskit-lite' );
		$columns['condition'] = esc_html__( 'Conditions', 'elementskit-lite' );
		$columns['date']      = $date_column;
		$columns['author']    = $author_column;

		return $columns;
	}

	/**
	 * Render Column
	 *
	 * Enqueue js and css to frontend.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_column( $column, $post_id ) {
		switch ( $column ) {
			case 'type':
				$type   = get_post_meta( $post_id, 'elementskit_template_type', true );
				$active = get_post_meta( $post_id, 'elementskit_template_activation', true );
				
				$output = ucfirst( $type ) . ( ( $active == 'yes' ) 
				? ( '<span class="ekit-headerfooter-status ekit-headerfooter-status-active">' . esc_html__( 'Active', 'elementskit-lite' ) . '</span>' ) 
				: ( '<span class="ekit-headerfooter-status ekit-headerfooter-status-inactive">' . esc_html__( 'Inactive', 'elementskit-lite' ) . '</span>' ) );

				echo wp_kses($output, \ElementsKit_Lite\Utils::get_kses_array());

				break;
			case 'condition':
				$cond = array(
					'condition_a'           => get_post_meta( $post_id, 'elementskit_template_condition_a', true ),
					'condition_singular'    => get_post_meta( $post_id, 'elementskit_template_condition_singular', true ),
					'condition_singular_id' => get_post_meta( $post_id, 'elementskit_template_condition_singular_id', true ),
				);

				echo esc_html(ucwords(
					str_replace(
						'_',
						' ',
						$cond['condition_a']  
						. ( ( $cond['condition_a'] == 'singular' )
						? ( ( $cond['condition_singular'] != '' )
							? ( ' > ' . $cond['condition_singular'] 
							. ( ( $cond['condition_singular_id'] != '' )
								? ' > ' . $cond['condition_singular_id']
								: '' ) )
							: '' )
						: '' )
					)
				));

				break;
		}
	}
	

	public function query_filter( $query ) {
		global $pagenow;
		$current_page = isset( $_GET['post_type'] ) ? sanitize_text_field(wp_unslash($_GET['post_type'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We are using wordpress default query filter for managing menu items

		if ( is_admin() 
			&& 'elementskit_template' == $current_page 
			&& 'edit.php' == $pagenow   
			&& isset( $_GET['elementskit_type_filter'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We are using wordpress default query filter for managing menu items
			&& $_GET['elementskit_type_filter'] != ''    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We are using wordpress default query filter for managing menu items
			&& $_GET['elementskit_type_filter'] != 'all' // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We are using wordpress default query filter for managing menu items
		) {
			$type                              = sanitize_text_field(wp_unslash($_GET['elementskit_type_filter'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We are using wordpress default query filter for managing menu items
			$query->query_vars['meta_key']     = 'elementskit_template_type';
			$query->query_vars['meta_value']   = $type;
			$query->query_vars['meta_compare'] = '=';
		}
	}


	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
