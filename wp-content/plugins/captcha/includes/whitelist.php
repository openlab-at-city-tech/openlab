<?php
/**
 * Display content of "Whitelist" tab on settings page
 * @subpackage Captcha by BestWebSoft
 * @since 4.1.4
 * @version 1.0.2
 */

if ( ! class_exists( 'Cptch_Whitelist' ) ) {
	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}

	class Cptch_Whitelist extends WP_List_Table {
		private
			$disable_list,
			$basename,
			$order_by,
			$per_page,
			$la_info,
			$paged,
			$order,
			$s;
		/**
		* Constructor of class
		*/
		function __construct( $plugin_basename, $limit_attempts_info ) {
			global $cptch_options;
			if ( empty( $cptch_options ) )
				$cptch_options = get_option( 'cptch_options' );
			parent::__construct( array(
				'singular'  => 'IP',
				'plural'    => 'IP',
				'ajax'      => true,
				)
			);
			$this->basename     = $plugin_basename;
			$this->la_info      = $limit_attempts_info;
			$this->display_notices();
			$this->disable_list = 1 == $cptch_options['use_limit_attempts_whitelist'];
		}

		/**
		 * Display content
		 * @return void
		 */
		function display_content() {
			global $wp_version, $cptch_options;
			if ( isset( $_SERVER ) ) {
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
			$this->prepare_items();
			$limit_attempts_info = $this->get_limit_attempts_info();
			$disabled = $limit_attempts_info['disabled'] ? ' disabled="disabled"' : '';
			if ( $limit_attempts_info['actived'] ) {
				$checked = $this->disable_list ? ' checked="checked"' : '';
				$hidden  = $this->disable_list;
			} else {
				$checked = '';
				$hidden  = false;
			} ?>
			<p><strong><?php _e( 'For IP addresses from the whitelist CAPTCHA will not be displayed', 'captcha' ); ?></strong></p>
			<?php if ( ! ( isset( $_REQUEST['cptch_show_whitelist_form'] ) || isset( $_REQUEST['cptch_add_to_whitelist'] ) ) ) { ?>
				<form method="post" action="admin.php?page=captcha.php&amp;action=whitelist" style="margin: 10px 0;">
					<table>
						<tr>
							<td>
								<label for="cptch_use_la_whitelist">
									<input type="checkbox" name="cptch_use_la_whitelist" value="1" id="cptch_use_la_whitelist"<?php echo $disabled . $checked;?>/>
									<?php echo $limit_attempts_info['label']; ?>
								</label>
								<div class="bws_help_box dashicons dashicons-editor-help cptch_thumb_block">
									<div class="bws_hidden_help_text" style="width: 200px;">
										<p><?php printf( __( 'With this option, CAPTCHA will not be displayed for IP-addresses from the whitelist of %s', 'captcha' ), $limit_attempts_info['name'] ); ?></p>
										<?php if ( ! empty( $limit_attempts_info['notice'] ) ) { ?>
											<p class="bws_info"><?php echo $limit_attempts_info['notice']; ?></p>
										<?php } ?>
									</div>
								</div>
							<td>
						</tr>
						<tr>
							<td class="cptch_whitelist_buttons">
								<div class="alignleft">
									<button class="button" name="cptch_show_whitelist_form" value="on"<?php echo $hidden ? ' style="display: none;"' : ''; ?>><?php _e( 'Add IP to whitelist', 'captcha' ); ?></button>
								</div>
								<div class="alignleft">
									<input type="submit" name="cptch_load_limit_attempts_whitelist" class="button" value="<?php _e( 'Load IP to whitelist', 'captcha' ); ?>" style="float: left;<?php echo $hidden ? 'display: none;' : ''; ?>" <?php echo $disabled; ?>/>
									<div class="bws_help_box dashicons dashicons-editor-help cptch_thumb_block"<?php echo $hidden ? ' style="display: none;"' : ''; ?>>
										<div class="bws_hidden_help_text" style="width: 200px;">
											<p><?php printf( __( 'By click on this button, all IP-addresses from the whitelist of %s will be loaded to the whitelist of %s', 'captcha' ), $limit_attempts_info['name'], 'Captcha by BestWebSoft' ); ?></p>
											<?php if ( ! empty( $limit_attempts_info['notice'] ) ) { ?>
												<p class="bws_info"><?php echo $limit_attempts_info['notice']; ?></p>
											<?php } ?>
										</div>
									</div>
								</div>
								<noscript>
									<div class="alignleft">
										<input type="submit" name="cptch_save_add_ip_form_button" class="button-primary" value="<?php _e( 'Save changes', 'captcha' ); ?>" />
									</div>
								</noscript>
								<?php wp_nonce_field( $this->basename, 'captcha_nonce_name' ); ?>
								<input type="hidden" name="cptch_save_add_ip_form" value="1"/>
							<td>
						</tr>
					</table>
				</form>
			<?php } ?>
			<form class="form-table cptch_whitelist_form" method="post" action="admin.php?page=captcha.php&amp;action=whitelist" style="margin: 10px 0;<?php echo ! ( isset( $_REQUEST['cptch_show_whitelist_form'] ) || isset( $_REQUEST['cptch_add_to_whitelist'] ) ) ? 'display: none;': ''; ?>">
				<div style="margin: 10px 0;">
					<input type="text" maxlength="31" name="cptch_add_to_whitelist" />
					<?php if ( isset( $my_ip ) ) { ?>
						<br />
						<label id="cptch_add_my_ip">
							<input type="checkbox" name="cptch_add_to_whitelist_my_ip" value="1" />
							<?php _e( 'My IP', 'captcha' ); ?>
							<input type="hidden" name="cptch_add_to_whitelist_my_ip_value" value="<?php echo $my_ip; ?>" />
						</label>
					<?php } ?>
					<br /><input type="submit" class="button-secondary" value="<?php _e( 'Add IP to whitelist', 'captcha' ) ?>" />
					<?php wp_nonce_field( $this->basename, 'captcha_nonce_name' ); ?>
				</div>
				<div style="margin: 10px 0;">
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed formats:", 'captcha' ); ?>&nbsp;<code>192.168.0.1</code></span><br/>
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed diapason:", 'captcha' ); ?>&nbsp;<code>0.0.0.0 - 255.255.255.255</code></span>
				</div>
				<?php cptch_pro_block( 'cptch_whitelist_banner' ); ?>
			</form>
			<?php if ( ! $hidden ) { ?>
				<form id="cptch_whitelist_search" method="post" action="admin.php?page=captcha.php&amp;action=whitelist">
					<?php $this->search_box( __( 'Search IP', 'captcha' ), 'search_whitelisted_ip' );
					wp_nonce_field( $this->basename, 'captcha_nonce_name' ); ?>
				</form>
				<form id="cptch_whitelist" method="post" action="admin.php?page=captcha.php&amp;action=whitelist">
					<?php $this->display();
					wp_nonce_field( $this->basename, 'captcha_nonce_name' ); ?>
				</form>
			<?php }
		}

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
			$this->per_page    = $this->get_items_per_page( 'cptch_per_page', 20 );


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
			$label = isset( $_REQUEST['s'] ) ? __( 'Nothing found', 'captcha' ) : __( 'No IP in whitelist', 'captcha' ); ?>
			<p><?php echo $label; ?></p>
		<?php }

		function get_columns() {
			$columns = array(
				'cb'      	=> '<input type="checkbox" />',
				'ip'      	=> __( 'IP address', 'captcha' ),
				'add_time'  => __( 'Date added', 'captcha' )
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
			$url      = "?page=captcha.php&action=whitelist&cptch_remove={$item['id']}{$order_by}{$order}{$paged}{$s}";
			$actions = array(
				'remove' => '<a href="' . wp_nonce_url( $url, "cptch_nonce_remove_{$item['id']}" ) . '">' . __( 'Remove from whitelist', 'captcha-pro' ) . '</a>'
			);
			return sprintf('%1$s %2$s', $item['ip'], $this->row_actions( $actions ) );
		}
		/**
		 * List with bulk action for IP
		 * @return array   $actions
		 */
		function get_bulk_actions() {
			return $this->disable_list ? array() : array( 'cptch_remove'=> __( 'Remove from whitelist', 'captcha' ) );
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
						" WHERE `ip` LIKE '%{$this->s}%' OR `ip_to` LIKE '%{$this->s}%' OR `ip_from` LIKE '%{$this->s}%'"
					:
						" WHERE ( `ip_from_int` <= {$ip_int} AND `ip_to_int` >= {$ip_int} )";
			}
			$order_by = empty( $this->order_by ) ? '' : " ORDER BY `{$this->order_by}`";
			$order    = empty( $this->order )    ? '' : strtoupper( " {$this->order}" );
			$offset   = empty( $this->paged )    ? '' : " OFFSET " . ( $this->per_page * ( absint( $this->paged ) - 1 ) );

			return $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}cptch_whitelist`{$where}{$order_by}{$order} LIMIT {$this->per_page}{$offset}", ARRAY_A );
		}

		/**
		 * Get number of all IPs which were added to database
		 * @since  1.6.9
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
						" WHERE `ip` LIKE '%{$this->s}%' OR `ip_to` LIKE '%{$this->s}%' OR `ip_from` LIKE '%{$this->s}%'"
					:
						" WHERE ( `ip_from_int` <= {$ip_int} AND `ip_to_int` >= {$ip_int} )";
			}
			return absint( $wpdb->get_var( "SELECT COUNT(`id`) FROM `{$wpdb->prefix}cptch_whitelist`{$where}" ) );
		}

		/**
		 * Handle necessary reqquests and display notices
		 * @return void
		 */
		function display_notices() {
			global $wpdb, $cptch_options;
			$error = $message = '';
			$bulk_action = isset( $_REQUEST['action'] ) && 'cptch_remove' == $_REQUEST['action'] ? true : false;
			if ( ! $bulk_action )
				$bulk_action = isset( $_REQUEST['action2'] ) && 'cptch_remove' == $_REQUEST['action2'] ? true : false;
			/* Add IP in to database */
			if ( isset( $_POST['cptch_add_to_whitelist'] ) && ( ! empty( $_POST['cptch_add_to_whitelist'] ) || isset( $_POST['cptch_add_to_whitelist_my_ip'] ) ) && check_admin_referer( $this->basename, 'captcha_nonce_name' ) ) {
				$add_ip = isset( $_POST['cptch_add_to_whitelist_my_ip'] ) ? $_POST['cptch_add_to_whitelist_my_ip_value'] : $_POST['cptch_add_to_whitelist'];

				$valid_ip = filter_var( stripslashes( esc_html( trim( $add_ip ) ) ), FILTER_VALIDATE_IP );
				if ( $valid_ip ) {
					$ip_int = sprintf( '%u', ip2long( $valid_ip ) );
					$id = $wpdb->get_var( "SELECT `id` FROM " . $wpdb->prefix . "cptch_whitelist WHERE ( `ip_from_int` <= " . $ip_int . " AND `ip_to_int` >= " . $ip_int . " ) OR `ip` LIKE '" . $valid_ip . "' LIMIT 1;" );
					/* check if IP already in database */
					if ( is_null( $id ) ) {
						$time         = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
						$wpdb->insert(
							$wpdb->prefix . "cptch_whitelist",
							array(
								'ip'          => $valid_ip,
								'ip_from_int' => $ip_int,
								'ip_to_int'   => $ip_int,
								'add_time'    => $time
							)
						);
						if ( ! $wpdb->last_error )
							$message = __( 'IP added to the whitelist successfully', 'captcha' );
						else
							$error = __( 'Some errors occured', 'captcha' );
					} else {
						$error = __( 'IP is already in the whitelist', 'captcha' );
					}
				} else {
					$error = __( 'Invalid IP. See allowed formats', 'captcha' );
				}
				if ( empty( $error ) ) {
					$cptch_options['whitelist_is_empty'] = false;
					update_option( 'cptch_options', $cptch_options );
				}
			} elseif ( $bulk_action && check_admin_referer( $this->basename, 'captcha_nonce_name' ) ) {
				if ( ! empty( $_REQUEST['id'] ) ) {
					$list   = implode( ',', $_REQUEST['id'] );
					$result = $wpdb->query( "DELETE FROM `" . $wpdb->prefix . "cptch_whitelist` WHERE `id` IN (" . $list . ");" );
					if ( ! $wpdb->last_error ) {
						$message = sprintf( _n( "%s IP was deleted successfully", "%s IPs were deleted successfully", $result, 'captcha' ), $result );
						if ( ! is_null( $wpdb->get_var( "SELECT `id` FROM `{$wpdb->prefix}cptch_whitelist` LIMIT 1" ) ) ) {
							$cptch_options['whitelist_is_empty'] = false;
							update_option( 'cptch_options', $cptch_options );
						}
					} else {
						$error = __( 'Some errors occured', 'captcha' );
					}
				}
			} elseif ( isset( $_GET['cptch_remove'] ) && check_admin_referer( 'cptch_nonce_remove_' . $_GET['cptch_remove'] ) ) {
				$wpdb->delete( $wpdb->prefix . "cptch_whitelist", array( 'id' => $_GET['cptch_remove'] ) );
				if ( ! $wpdb->last_error ) {
					$message = __( "One IP was deleted successfully", 'captcha' );
					if( ! is_null( $wpdb->get_var( "SELECT `id` FROM `{$wpdb->prefix}cptch_whitelist` LIMIT 1" ) ) ) {
						$cptch_options['whitelist_is_empty'] = false;
						update_option( 'cptch_options', $cptch_options );
					}
				} else {
					$error = __( 'Some errors occured', 'captcha' );
				}
			} elseif ( isset( $_POST['cptch_add_to_whitelist'] ) && empty( $_POST['cptch_add_to_whitelist'] ) ) {
				$error = __( 'You have not entered any value', 'captcha' );
			} elseif ( isset( $_REQUEST['s'] ) ) {
				if ( '' == $_REQUEST['s'] ) {
					$error = __( 'You have not entered any value in to the search form', 'captcha' );
				} else {
					$message = __( 'Search results for', 'captcha' ) . '&nbsp;:&nbsp;' . esc_html( $_REQUEST['s'] );
				}
			} elseif ( isset( $_POST['cptch_load_limit_attempts_whitelist'] ) && check_admin_referer( $this->basename, 'captcha_nonce_name' ) ) {
				/* copy data from the whitelist of LimitAttempts plugin */
				$time = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
				$result = $wpdb->query(
					"INSERT IGNORE INTO `{$wpdb->prefix}cptch_whitelist`
						( `ip`, `ip_from_int`, `ip_to_int`, `add_time` )
						( SELECT `ip`, `ip_from_int`, `ip_to_int`, '{$time}'
							FROM `{$wpdb->prefix}lmtttmpts_whitelist` );"
				);
				if ( $wpdb->last_error ) {
					$error = $wpdb->last_error;
				} else {
					$message = $result . '&nbsp;' . __( 'IP-address(es) successfully copied to the whitelist', 'captcha' );
					$cptch_options['whitelist_is_empty'] = false;
					update_option( 'cptch_options', $cptch_options );
				}
			} elseif( isset( $_POST['cptch_save_add_ip_form'] ) && check_admin_referer( $this->basename, 'captcha_nonce_name' ) ) {
				$cptch_options['use_limit_attempts_whitelist'] = isset( $_POST['cptch_use_la_whitelist'] ) ? 1 : 0;
				update_option( 'cptch_options', $cptch_options );
			}
			if ( ! empty( $message ) ) { ?>
				<div class="updated fade below-h2"><p><strong><?php echo $message; ?>.</strong></p></div>
			<?php }
			if ( ! empty( $error ) ) { ?>
				<div class="error below-h2"><p><strong><?php echo $error; ?>.</strong></p></div>
			<?php }
		}

		/*
		 * Get info about plugins Limit Attempts ( Free or Pro ) by BestWebSoft
		 */
		function get_limit_attempts_info() {
			global $wp_version, $cptch_plugin_info;
			if ( 'actived' == $this->la_info['status'] ) {
				$data = array(
					'actived'           => true,
					'name'             => $this->la_info['plugin_info']["Name"],
					'label'            => __( 'use', 'captcha' ) . '&nbsp;<a href="?page=' . $this->la_info['plugin_info']["TextDomain"] . '.php&action=whitelist">' . __( 'whitelist of', 'captcha' ) . '&nbsp;' . $this->la_info['plugin_info']["Name"] . '</a>',
					'notice'           => '',
					'disabled'         => false,
				);
			} elseif ( 'deactivated' == $this->la_info['status'] ) {
				$data = array(
					'actived'          => false,
					'name'             => $this->la_info['plugin_info']["Name"],
					'label'            => sprintf( __( 'use whitelist of %s', 'captcha' ), $this->la_info['plugin_info']["Name"] ),
					'notice'           => sprintf( __( 'you should %s to use this functionality', 'captcha' ), '<a href="plugins.php">' . __( 'activate', 'captcha' ) . '&nbsp;' . $this->la_info['plugin_info']["Name"] . '</a>' ),
					'disabled'         => true,
				);
			} elseif ( 'not_installed' == $this->la_info['status'] ) {
				$data = array(
					'actived'          => false,
					'name'             => 'Limit Attempts by BestWebSoft',
					'label'            => sprintf( __( 'use whitelist of %s', 'captcha' ), 'Limit Attempts by BestWebSoft' ),
					'notice'           => sprintf( __( 'you should install %s to use this functionality', 'captcha' ), '<a href="http://bestwebsoft.com/products/limit-attempts?k=d70b58e1739ab4857d675fed2213cedc&pn=75&v=' . $cptch_plugin_info["Version"] . '&wp_v=' . $wp_version . '" target="_blank">Limit Attempts by BestWebSoft</a>' ),
					'disabled'         => true,
				);
			}
			return $data;
		}
	}
} ?>