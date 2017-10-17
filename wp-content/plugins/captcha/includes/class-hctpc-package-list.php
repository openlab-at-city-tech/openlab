<?php
/**
 * Manage list of upladed images packages
 * @package Captcha
 * @since 1.6.9
 */

if ( ! defined( 'ABSPATH' ) )
	die();

if ( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

if ( ! class_exists( 'hctpc_Package_List' ) ) {
	class hctpc_Package_List extends WP_List_Table {

		private
			$date_format, /* string, the date format, which is configured on the Admin Panel -> Settings -> General -> Date Format */
			$upload_dir,  /* string, the absolute path to the folder with images for CAPTCHA */
			$upload_url,  /* string, the URL of the folder with images for CAPTCHA */
			$to_delete,   /* array,  the list of packaeges, wich need to delete */
			$order_by,    /* srting, the column name, according to which to sort the data */
			$defaults,    /* array,  the list of default packages */
			$per_page,    /* int,    the number of packages on the page */
			$message,     /* string, the service message */
			$loader,      /* object, an instance of the hctpc_Package_Loader class */
			$paged,       /* int,    the number of the current page */
			$order,       /* string, 'ASC' or 'DESC' */
			$s;           /* string, the content of the search request */

		/**
		 * Constructor of class
		 * @param  void
		 * @return void
		 */
		function __construct() {
			parent::__construct( array(
				'singular'  => __( 'package', 'captcha' ),
				'plural'    => __( 'packages', 'captcha' ),
				'ajax'      => false,
				)
			);
		}

		/**
		 * Prepare data before the displaying
		 * @param  void
		 * @return void
		 */
		function prepare_items() {
			if ( is_multisite() ) {
				switch_to_blog( 1 );
				$upload_dir = wp_upload_dir();
				restore_current_blog();
			} else {
				$upload_dir = wp_upload_dir();
			}
			$this->upload_dir  = $upload_dir['basedir'] . '/captcha_images';
			$this->upload_url  = $upload_dir['baseurl'] . '/captcha_images';
			$this->order_by    = isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ? $_REQUEST['orderby'] : '';
			$this->order       = isset( $_REQUEST['order'] ) && in_array( strtoupper( $_REQUEST['order'] ), array( 'ASC', 'DESC' ) ) ? $_REQUEST['order'] : '';
			$this->paged       = isset( $_REQUEST['paged'] ) && is_numeric( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : '';
			$this->s           = isset( $_REQUEST['s'] ) ? esc_html( trim( $_REQUEST['s'] ) ) : '';
			$this->per_page    = $this->get_items_per_page( 'hctpc_per_page', 20 );
			$this->date_format = get_option( 'date_format' );

			if ( ! class_exists( 'hctpc_Package_Loader' ) )
				require_once( dirname( __FILE__ ) . '/class-hctpc-package-loader.php' );
			$this->loader = new hctpc_Package_Loader();

			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$primary               = 'name';
			$this->_column_headers = array( $columns, $hidden, $sortable, $primary );
			$this->items           = $this->get_packages();
			$this->set_pagination_args( array(
					'total_items' => $this->get_items_number(),
					'per_page'    => 20,
				)
			);
		}

		/**
		 * Display the content of the page
		 * @param  void
		 * @return void
		 */
		function display_content() {
			global $hctpc_options, $hctpc_plugin_info, $wp_version; ?>
			<h1 class="wp-heading-inline"><?php _e( 'Captcha Packages', 'captcha' ); ?></h1>
			<?php if ( $this->message ) { ?>
				<div class="updated fade below-h2"><p><?php echo $this->message; ?></p></div>
			<?php }
			$this->prepare_items();
			$this->loader->display(); ?>
			<form method="post" action="admin.php?page=captcha-packages.php" style="margin: 10px 0;">
				<?php $this->search_box( __( 'Search', 'captcha' ), 'hctpc_packages_search' );
				/* maint content displaing */
				$this->display(); ?>
				<input type="hidden" name="page" value="captcha-packages" />
			</form>
		<?php }

		/**
		 * Add necessary classes for the packages list
		 * @param  void
		 * @return array
		 */
		function get_table_classes() {
			return array( 'widefat', 'striped', 'hctpc_package_list' );
		}

		/**
		 * Show message the list is empty
		 * @param  void
		 * @return void
		 */
		function no_items() { ?>
			<p><?php _e( 'No packages found', 'captcha' ); ?></p>
		<?php }

		/**
		 * Get the list of table columns.
		 * @param  void
		 * @return array list of columns labels
		 */
		function get_columns() {
			return array(
				'name' => __( 'Package', 'captcha' ),
				'date' => __( 'Date Added', 'captcha' )
			);
		}

		/**
		 * Get the list of sortable columns.
		 * @param  void
		 * @return array list of sortable columns
		 */
		function get_sortable_columns() {
			return array(
				'name' => array( 'name', false ),
				'date' => array( 'date', false )
			);
		}

		/**
		 * Manage the content of the column "Package"
		 * @param     array     $item        The current package data.
		 * @return    string                 with the column content
		 */
		function column_name( $item ) {
			$styles = '';
			if ( ! empty( $item['settings'] ) ) {
				$settings = unserialize( $item['settings'] );
				if ( is_array( $settings ) ) {
					$styles = ' style="';
					foreach ( $settings as $propery => $value )
						$styles .= "{$propery}: {$value};";
					$styles .= '"';
				}
			}
			$title =
				"<div class=\"has-media-icon\">
					<span class=\"media-icon image-icon\">
						<img src=\"{$this->upload_url}/{$item['folder']}/{$item['image']}\" alt=\"{$item['name']}\"{$styles} />
					</span>
					{$item['name']}
				</div>";

			return $title;
		}

		/**
		 * Manage the content of the column 'Date'
		 * @param     array     $item        The cuurrent package data.
		 * @return    string                 with the column content
		 */
		function column_date( $item ) {
			return '0000-00-00 00:00:00' == $item['add_time'] ? '' : date_i18n( $this->date_format, strtotime( $item['add_time'] ) );
		}

		/**
		 * Get the list of loaded packages
		 * @param  void
		 * @return array  $items   the list with packages data
		 */
		private function get_packages() {
			global $wpdb;
			$where    = empty( $this->s )        ? '' : " WHERE `{$wpdb->base_prefix}hctpc_packages`.`name` LIKE '%{$this->s}%'";
			$order_by = empty( $this->order_by ) ? ' ORDER BY `add_time`' : " ORDER BY `{$this->order_by}`";
			$order    = empty( $this->order )    ? ' DESC' : strtoupper( " {$this->order}" );
			$offset   = empty( $this->paged )    ? '' : " OFFSET " . ( $this->per_page * ( absint( $this->paged ) - 1 ) );

			$items = $wpdb->get_results(
				"SELECT
					`{$wpdb->base_prefix}hctpc_packages`.`id`,
					`{$wpdb->base_prefix}hctpc_packages`.`name`,
					`{$wpdb->base_prefix}hctpc_packages`.`folder`,
					`{$wpdb->base_prefix}hctpc_packages`.`add_time`,
					`{$wpdb->base_prefix}hctpc_packages`.`settings`,
					`{$wpdb->base_prefix}hctpc_images`.`name` AS `image`
				FROM
					`{$wpdb->base_prefix}hctpc_packages`
				LEFT JOIN
					`{$wpdb->base_prefix}hctpc_images`
				ON
					`{$wpdb->base_prefix}hctpc_images`.`package_id`=`{$wpdb->base_prefix}hctpc_packages`.`id`
				{$where}
				GROUP BY `{$wpdb->base_prefix}hctpc_packages`.`id`
				{$order_by}
				{$order}
				LIMIT {$this->per_page}{$offset};",
				ARRAY_A
			);
			return $items;
		}
		/**
		 * Get number of all packages which were added to database
		 * @param  void
		 * @return int   the number of packages
		 */
		private function get_items_number() {
			global $wpdb;
			$where = empty( $this->s ) ? '' : " WHERE `name` LIKE '%{$this->s}%'";
			return absint( $wpdb->get_var( "SELECT COUNT(`id`) FROM `{$wpdb->prefix}hctpc_packages`{$where}" ) );
		}
	}
}
