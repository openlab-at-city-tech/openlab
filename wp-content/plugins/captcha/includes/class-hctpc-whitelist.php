<?php
/**
 * Display content of "Whitelist" tab on settings page
 * @package Captcha
 * @since   4.1.4
 * @version 1.0.2
 */

if ( ! class_exists( 'hctpc_Whitelist' ) ) {
	if ( ! class_exists( 'WP_List_Table' ) )
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

	class hctpc_Whitelist extends WP_List_Table {
		private
			$basename,
			$order_by,
			$per_page,
			$paged,
			$order,
			$s,
			$date_filter_options,
			$filter_by,
			$last_filtred_by;

		/**
		* Constructor of class
		*/
		function __construct( $plugin_basename ) {
			global $hctpc_options;

			parent::__construct( array(
				'singular'  => 'IP',
				'plural'    => 'IP',
				'ajax'      => true,
				)
			);
			$this->basename     = $plugin_basename;

			/**
			* options for filtring
			*/
			$this->date_filter_options = array(
				'all'    => __( 'All dates', 'captcha' ),
				'day'    => __( 'Last 24 hours', 'captcha' ),
				'week'   => __( 'Last week', 'captcha' ),
				'month'  => __( 'Last month', 'captcha' ),
				'year'   => __( 'Last year', 'captcha' )
			);

			/**
			* keep in mind what was the last filtring option to compare it
			* with the new filtring options and choose the differnt one
			*/
			if (
				! empty( $_POST['hctpc_last_filtred_by'] ) &&
				in_array( $_POST['hctpc_last_filtred_by'], array_keys( $this->date_filter_options ) )
			) {
				$this->last_filtred_by = $_POST['hctpc_last_filtred_by'];
			} else {
				$this->last_filtred_by = 'all';
			}

			if ( ! empty( $_POST['hctpc_date_filter'] ) )
				$filter_array = array_filter( array_unique( $_POST['hctpc_date_filter'] ), array( $this, 'get_date_filter_values' ) );

			/**
			* Due to the first element's key either be 0 or 1, $filter_array[ key( $filter_array ) ] should be used.
			* It gives the ability of taking the first element of the array
			*/
			$this->filter_by = ! empty( $filter_array ) ? $filter_array[ key( $filter_array ) ] : $this->last_filtred_by;
		}

		/**
		 * Display content
		 * @return void
		 */
		function display_content() { ?>
			<h1 class="wp-heading-inline"><?php _e( 'Captcha Whitelist', 'captcha' ); ?></h1>
			<form method="post" action="admin.php?page=captcha-whitelist.php" class="hctpc_whitelist_add_new" style="display: inline;">
				<button class="page-title-action" name="hctpc_show_whitelist_form" value="on"<?php echo ( isset( $_POST['hctpc_add_to_whitelist'] ) ) ? ' style="display: none;"' : ''; ?>><?php _e( 'Add New', 'captcha' ); ?></button>
			</form>
			<?php if ( isset( $_SERVER ) ) {
				$sever_vars = array( 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
				foreach ( $sever_vars as $var ) {
					if ( isset( $_SERVER[ $var ] ) && ! empty( $_SERVER[ $var ] ) ) {
						if ( filter_var( $_SERVER[ $var ], FILTER_VALIDATE_IP ) ) {
							$my_ip = $_SERVER[ $var ];
							break;
						} else { /* if proxy */
							$ip_array = explode( ',', $_SERVER[ $var ] );
							if ( is_array( $ip_array ) && ! empty( $ip_array ) && filter_var( $ip_array[0], FILTER_VALIDATE_IP ) ) {
								$my_ip = $ip_array[0];
								break;
							}
						}
					}
				}
			}

			$this->display_notices();
			$this->prepare_items(); ?>
			<form class="form-table hctpc_whitelist_form" method="post" action="admin.php?page=captcha-whitelist.php" style="margin: 10px 0;<?php echo ! ( isset( $_REQUEST['hctpc_show_whitelist_form'] ) || isset( $_REQUEST['hctpc_add_to_whitelist'] ) ) ? 'display: none;': ''; ?>">
				<div>
					<label><?php _e( 'IP to whitelist', 'captcha' ); ?></label>
					<br />
					<input type="text" maxlength="31" name="hctpc_add_to_whitelist" />
					<?php if ( isset( $my_ip ) ) { ?>
						<br />
						<label id="hctpc_add_my_ip">
							<input type="checkbox" name="hctpc_add_to_whitelist_my_ip" value="1" />
							<?php _e( 'My IP', 'captcha' ); ?>
							<input type="hidden" name="hctpc_add_to_whitelist_my_ip_value" value="<?php echo $my_ip; ?>" />
						</label>
					<?php } ?>
					<div>
						<span class="hctpc_info" style="line-height: 2;"><?php _e( "Allowed formats", 'captcha' ); ?>:&nbsp;<code>192.168.0.1</code></span>
						<br/>
						<span class="hctpc_info" style="line-height: 2;"><?php _e( "Allowed diapason", 'captcha' ); ?>:&nbsp;<code>0.0.0.0 - 255.255.255.255</code></span>
					</div>
					<p>
						<input type="submit" id="hctpc_add_to_whitelist_button" class="button-secondary" value="<?php _e( 'Add IP to whitelist', 'captcha' ) ?>" />
						<?php wp_nonce_field( $this->basename, 'hctpc_nonce_name' ); ?>
					</p>
				</div>				
			</form>
			<form id="hctpc_whitelist_search" method="post" action="admin.php?page=captcha-whitelist.php">
				<?php $this->search_box( __( 'Search item', 'captcha' ), 'search_whitelisted_ip' );
				wp_nonce_field( $this->basename, 'hctpc_nonce_name' ); ?>
			</form>
			<form id="hctpc_whitelist" method="post" action="admin.php?page=captcha-whitelist.php">
				<?php $this->display();
				wp_nonce_field( $this->basename, 'hctpc_nonce_name' ); ?>
			</form>
		<?php }

		/**
		* Function to prepare data before display
		* @return void
		*/
		function prepare_items() {
			
			if ( isset( $_GET['orderby'] ) && in_array( $_GET['orderby'], array_keys( $this->get_sortable_columns() ) ) ) {
				switch ( $_GET['orderby'] ) {
					case 'ip':
						$this->order_by = 'ip_from_int';
						break;
					case 'ip_from':
						$this->order_by = 'ip_from_int';
						break;
					case 'ip_to':
						$this->order_by = 'ip_to_int';
						break;
					default:
						$this->order_by = esc_sql( $_GET['orderby'] );
						break;
				}
			} else {
				$this->order_by = 'add_time';
			}
			$this->order       = isset( $_REQUEST['order'] ) && in_array( strtoupper( $_REQUEST['order'] ), array( 'ASC', 'DESC' ) ) ? $_REQUEST['order'] : '';
			$this->paged       = isset( $_REQUEST['paged'] ) && is_numeric( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : '';
			$this->s           = isset( $_REQUEST['s'] ) ? esc_html( trim( $_REQUEST['s'] ) ) : '';
			$this->per_page    = $this->get_items_per_page( 'hctpc_per_page', 20 );

			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->items           = $this->get_content();
			$current_page          = $this->get_pagenum();
			$this->set_pagination_args( array(
					'total_items' => $this->get_items_number(),
					'per_page'    => 20,
				)
			);
		}
		/**
		* Function to show message if empty list
		* @return void
		*/
		function no_items() {
			$label = isset( $_REQUEST['s'] ) ? __( 'Nothing found', 'captcha' ) : __( 'No IP in the whitelist', 'captcha' ); ?>
			<p><?php echo $label; ?></p>
		<?php }

		function get_columns() {
			$columns = array(
				'cb'      	=> '<input type="checkbox" />',
				'ip'      	=> __( 'IP Address', 'captcha' ),
				'add_time'  => __( 'Date Added', 'captcha' )
			);
			return $columns;
		}
		/**
		 * Get a list of sortable columns.
		 * @return array list of sortable columns
		 */
		function get_sortable_columns() {
			$sortable_columns = array(
				'ip'      => array( 'ip', true ),
				'add_time' => array( 'add_time', false )
			);
			return $sortable_columns;
		}
		/**
		 * Fires when the default column output is displayed for a single row.
		 * @param      string    $column_name      The custom column's name.
		 * @param      array     $item             The cuurrent letter data.
		 * @return    void
		 */
		function column_default( $item, $column_name ) {
			switch ( $column_name ) {
				case 'ip':
				case 'add_time':
					return $item[ $column_name ];
				default:
					/* Show whole array for bugfix */
					return print_r( $item, true );
			}
		}
		/**
		 * Function to manafe content of column with checboxes
		 * @param     array     $item        The cuurrent letter data.
		 * @return    string                  with html-structure of <input type=['checkbox']>
		 */
		function column_cb( $item ) {
			/* customize displaying cb collumn */
			return sprintf(
				'<input type="checkbox" name="id[]" value="%s" />', $item['id']
			);
		}
		/**
		 * Function to manafe content of column with IP-adresses
		 * @param     array     $item        The cuurrent letter data.
		 * @return    string                  with html-structure of <input type=['checkbox']>
		 */
		function column_ip( $item ) {
			$order_by = empty( $this->order_by ) ? '' : "&orderby={$this->order_by}";
			$order    = empty( $this->order )    ? '' : "&order={$this->order}";
			$paged    = empty( $this->paged )    ? '' : "&paged={$this->paged}";
			$s        = empty( $this->s )        ? '' : "&s={$this->s}";
			$url      = "?page=captcha-whitelist.php&hctpc_remove={$item['id']}{$order_by}{$order}{$paged}{$s}";
			$actions = array(
				'delete' => '<a href="' . wp_nonce_url( $url, "hctpc_nonce_remove_{$item['id']}" ) . '">' . __( 'Delete', 'captcha' ) . '</a>'
			);
			return sprintf('%1$s %2$s', $item['ip'], $this->row_actions( $actions ) );
		}
		/**
		 * Get content for table
		 * @return  array
		 */
		function get_content() {
			global $wpdb;

			if ( empty( $this->s ) ) {
				$where = '';
			} else {
				$ip_int = filter_var( $this->s, FILTER_VALIDATE_IP ) ? sprintf( '%u', ip2long( $this->s ) ) : 0;
				$where =
						0 == $ip_int
					?
						" WHERE `ip` LIKE '%{$this->s}%'"
					:
						" WHERE ( `ip_from_int` <= {$ip_int} AND `ip_to_int` >= {$ip_int} )";
			}

			/**
			* filter items by date if it is needed
			*/
			if ( 'all' != $this->filter_by ) {
				$now = time();
				$day = 60 * 60 * 24;

				/**
				* get the quantity of seconds in the day
				*/
				switch ( $this->filter_by ) {
					case 'day':
						$point = $now - $day;
						break;
					case 'week':
						$point = $now - $day * 7;
						break;
					case 'month':
						$point = $now - $day * 31;
						break;
					case 'year':
						$point = $now - $day * 365;
						break;
					default:
						break;
				}

				if( ! empty( $point ) ) {
					$point = date( 'Y-m-d h:i:s', $point );

					$where .= ! empty( $where ) ? ' &&' : 'WHERE';
					$where .= ' `add_time` > "' . $point . '"';
				}
			}

			$order_by = empty( $this->order_by ) ? '' : " ORDER BY `{$this->order_by}`";
			$order    = empty( $this->order )    ? '' : strtoupper( " {$this->order}" );
			$offset   = empty( $this->paged )    ? '' : " OFFSET " . ( $this->per_page * ( absint( $this->paged ) - 1 ) );

			return $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}hctpc_whitelist`{$where}{$order_by}{$order} LIMIT {$this->per_page}{$offset}", ARRAY_A );
		}

		/**
		 * Get number of all IPs which were added to database
		 * @since  1.1.4
		 * @param  void
		 * @return int    the number of IPs
		 */
		private function get_items_number() {
			global $wpdb;
			if ( empty( $this->s ) ) {
				$where = '';
			} else {
				$ip_int = filter_var( $this->s, FILTER_VALIDATE_IP ) ? sprintf( '%u', ip2long( $this->s ) ) : 0;
				$where =
						0 == $ip_int
					?
						" WHERE `ip` LIKE '%{$this->s}%'"
					:
						" WHERE ( `ip_from_int` <= {$ip_int} AND `ip_to_int` >= {$ip_int} )";
			}
			return absint( $wpdb->get_var( "SELECT COUNT(`id`) FROM `{$wpdb->prefix}hctpc_whitelist`{$where}" ) );
		}

		/**
		 * This function display's top- & bottom- filters
		 * @since 4.3.1
		 * @param string $which
		 * @return void
		 */
		function extra_tablenav( $which ) { ?>
			<select name="hctpc_date_filter[]">
				<?php foreach ( $this->date_filter_options as $key => $value) { ?>
					<option value="<?php echo $key;?>" <?php echo ( $key == $this->filter_by ) ? 'selected' : ''; ?>>
						<?php echo $value; ?>
					</option>
				<?php } ?>

			</select>

			<input type="hidden" name="hctpc_last_filtred_by" value="<?php echo $this->filter_by; ?>" />
			<input type="submit" class="button action" value="<?php _e( 'Filter', 'captcha' ); ?>" />
		<?php }

		/**
		 * Check if filtring option is valid and not same to last time used filtring option
		 * @see $this->extra_tablenav
		 * @param  string   filtring option
		 * @return array    filtred $_POST['hctpc_date_filter']
		 */
		private function get_date_filter_values( $item ) {
			return ( in_array( $item, array_keys( $this->date_filter_options ) ) && $this->last_filtred_by != $item );
		}

		/**
		 * Handle necessary reqquests and display notices
		 * @return void
		 */
		function display_notices() {
			global $wpdb, $hctpc_options;
			$error = $message = '';

			$bulk_action = isset( $_REQUEST['action'] ) && 'hctpc_remove' == $_REQUEST['action'] ? true : false;
			if ( ! $bulk_action )
				$bulk_action = isset( $_REQUEST['action2'] ) && 'hctpc_remove' == $_REQUEST['action2'] ? true : false;

			/* Add IP in to database */
			if ( isset( $_POST['hctpc_add_to_whitelist'] ) && ( ! empty( $_POST['hctpc_add_to_whitelist'] ) || isset( $_POST['hctpc_add_to_whitelist_my_ip'] ) ) && check_admin_referer( $this->basename, 'hctpc_nonce_name' ) ) {
				$add_ip = isset( $_POST['hctpc_add_to_whitelist_my_ip'] ) ? $_POST['hctpc_add_to_whitelist_my_ip_value'] : $_POST['hctpc_add_to_whitelist'];

				$valid_ip = filter_var( stripslashes( esc_html( trim( $add_ip ) ) ), FILTER_VALIDATE_IP );
				if ( $valid_ip ) {
					$ip_int = sprintf( '%u', ip2long( $valid_ip ) );
					$id = $wpdb->get_var( "SELECT `id` FROM " . $wpdb->prefix . "hctpc_whitelist WHERE ( `ip_from_int` <= " . $ip_int . " AND `ip_to_int` >= " . $ip_int . " ) OR `ip` LIKE '" . $valid_ip . "' LIMIT 1;" );
					/* check if IP already in database */
					if ( is_null( $id ) ) {
						$time         = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
						$wpdb->insert(
							$wpdb->prefix . "hctpc_whitelist",
							array(
								'ip'          => $valid_ip,
								'ip_from_int' => $ip_int,
								'ip_to_int'   => $ip_int,
								'add_time'    => $time
							)
						);
						if ( ! $wpdb->last_error )
							$message = __( 'IP added to the whitelist successfully.', 'captcha' );
						else
							$error = __( 'Some errors occurred.', 'captcha' );
					} else {
						$error = __( 'IP is already in the whitelist.', 'captcha' );
					}
				} else {
					$error = __( 'Invalid IP. See allowed formats.', 'captcha' );
				}
				if ( empty( $error ) ) {
					$hctpc_options['whitelist_is_empty'] = false;
					update_option( 'hctpc_options', $hctpc_options );
				}
			} elseif ( $bulk_action && check_admin_referer( $this->basename, 'hctpc_nonce_name' ) ) {
				if ( ! empty( $_REQUEST['id'] ) ) {
					$list   = implode( ',', $_REQUEST['id'] );
					$result = $wpdb->query( "DELETE FROM `" . $wpdb->prefix . "hctpc_whitelist` WHERE `id` IN (" . $list . ");" );
					if ( ! $wpdb->last_error ) {
						$message = sprintf( _n( "%s IP was deleted successfully", "%s IPs were deleted successfully", $result, 'captcha' ), $result );
						if ( ! is_null( $wpdb->get_var( "SELECT `id` FROM `{$wpdb->prefix}hctpc_whitelist` LIMIT 1" ) ) ) {
							$hctpc_options['whitelist_is_empty'] = false;
							update_option( 'hctpc_options', $hctpc_options );
						}
					} else {
						$error = __( 'Some errors occurred', 'captcha' );
					}
				}
			} elseif ( isset( $_GET['hctpc_remove'] ) && check_admin_referer( 'hctpc_nonce_remove_' . $_GET['hctpc_remove'] ) ) {
				$wpdb->delete( $wpdb->prefix . "hctpc_whitelist", array( 'id' => $_GET['hctpc_remove'] ) );
				if ( ! $wpdb->last_error ) {
					$message = __( "One IP was deleted successfully", 'captcha' );
					if( ! is_null( $wpdb->get_var( "SELECT `id` FROM `{$wpdb->prefix}hctpc_whitelist` LIMIT 1" ) ) ) {
						$hctpc_options['whitelist_is_empty'] = false;
						update_option( 'hctpc_options', $hctpc_options );
					}
				} else {
					$error = __( 'Some errors occurred', 'captcha' );
				}
			} elseif ( isset( $_POST['hctpc_add_to_whitelist'] ) && empty( $_POST['hctpc_add_to_whitelist'] ) ) {
				$error = __( 'You have not entered any value.', 'captcha' );
			} elseif ( isset( $_REQUEST['s'] ) ) {
				if ( '' == $_REQUEST['s'] ) {
					$error = __( 'You have not entered any value in to the search form.', 'captcha' );
				} else {
					$message = __( 'Search results for', 'captcha' ) . '&nbsp;:&nbsp;' . esc_html( $_REQUEST['s'] );
				}
			}
			if ( ! empty( $message ) ) { ?>
				<div class="updated fade inline"><p><strong><?php echo $message; ?></strong></p></div>
			<?php }
			if ( ! empty( $error ) ) { ?>
				<div class="error inline"><p><strong><?php echo $error; ?></strong></p></div>
			<?php }
		}
	}
}