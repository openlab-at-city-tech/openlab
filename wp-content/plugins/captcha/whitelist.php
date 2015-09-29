<?php
/**
 * Display content of "Whitelist" tab on settings page
 * @subpackage Captcha
 * @since 4.1.4
 * @version 1.0.0
 */

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( ! class_exists( 'Cptch_Whitelist' ) ) {
	class Cptch_Whitelist extends WP_List_Table {
		var $basename;
		var $version;
		var $textdomain;
		/**
		* Constructor of class 
		*/
		function __construct( $plugin_basename, $plugin_version, $plugin_text_domain ) {
			$this->basename   = $plugin_basename;
			$this->version    = $plugin_version;
			$this->textdomain = $plugin_text_domain;
			parent::__construct( array(
				'singular'  => 'IP',
				'plural'    => 'IP',
				'ajax'      => true,
				)
			);
		}
		
		/**
		 * Display content
		 * @return void
		 */
		function display_content() {
			global $wp_version;
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
			$this->display_notices(); 
			$this->prepare_items(); ?>
			<p><strong><?php _e( 'For IP addresses from the whitelist CAPTCHA will not be displayed', $this->textdomain ); ?></strong></p>
			<form method="post" action="admin.php?page=captcha.php&amp;action=whitelist" style="margin: 10px 0;">
				<div style="margin: 10px 0;">
					<input type="text" maxlength="31" name="cptch_add_to_whitelist" />
					<input type="submit" class="button-secondary" value="<?php _e( 'Add IP to whitelist', $this->textdomain ) ?>" />
					<?php if ( isset( $my_ip ) ) { ?>
						<br />
						<label>
							<input type="checkbox" name="cptch_add_to_whitelist_my_ip" value="1" /> 
							<?php _e( 'My IP', $this->textdomain ); ?>									
							<input type="hidden" name="cptch_add_to_whitelist_my_ip_value" value="<?php echo $my_ip; ?>" />
						</label>
					<?php } ?>					
					<?php wp_nonce_field( $this->basename, 'captcha_nonce_name' ); ?>
				</div>
				<div style="margin: 10px 0;">
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed formats:", $this->textdomain ); ?><code>192.168.0.1</code></span><br />
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed diapason:", $this->textdomain ); ?>&nbsp;<code>0.0.0.0 - 255.255.255.255</code></span><br />
				</div>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<div class="bws_table_bg"></div>
						<table class="form-table bws_pro_version">
							<tr>
								<td valign="top"><?php _e( 'Reason', $this->textdomain ); ?>
									<input disabled type="text" style="margin: 10px 0;"/><br />
									<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed formats:", $this->textdomain ); ?>&nbsp;<code>192.168.0.1, 192.168.0., 192.168., 192., 192.168.0.1/8, 123.126.12.243-185.239.34.54</code></span><br />
									<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for IPs: a comma", $this->textdomain ); ?> (<code>,</code>), <?php _e( 'semicolon', $this->textdomain ); ?> (<code>;</code>), <?php _e( 'ordinary space, tab, new line or carriage return', $this->textdomain ); ?></span><br />
									<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for reasons: a comma", $this->textdomain ); ?> (<code>,</code>), <?php _e( 'semicolon', $this->textdomain ); ?> (<code>;</code>), <?php _e( 'tab, new line or carriage return', $this->textdomain ); ?></span>
								</td>
							</tr>
						</table>
					</div>
					<div class="bws_pro_version_tooltip">
						<span class="bws_info">
							<?php _e( 'Unlock premium options by upgrading to Pro version', $this->textdomain ); ?>
						</span>
						<a class="bws_button" href="http://bestwebsoft.com/products/captcha/buy/?k=9701bbd97e61e52baa79c58c3caacf6d&pn=75&v=<?php echo $this->version; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Captcha Pro"><?php _e( "Learn More", $this->textdomain ); ?></a>
						<div class="clear"></div>
					</div>
				</div>
			</form>
			<form method="post" action="admin.php?page=captcha.php&amp;action=whitelist">
				<?php $this->search_box( __( 'Search IP', $this->textdomain ), 'search_whitelisted_ip' );
				wp_nonce_field( $this->basename, 'captcha_nonce_name' ); ?>
			</form>
			<form method="post" action="admin.php?page=captcha.php&amp;action=whitelist">
				<?php $this->display(); 
				wp_nonce_field( $this->basename, 'captcha_nonce_name' ); ?>
			</form>
		<?php }

		/**
		* Function to prepare data before display 
		* @return void
		*/
		function prepare_items() {
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->items           = $this->get_content();
			$current_page          = $this->get_pagenum();
			$this->set_pagination_args( array(
					'total_items' => count( $this->items ),
					'per_page'    => 20,
				)
			);
		}
		/**
		* Function to show message if empty list
		* @return void
		*/
		function no_items() { 
			$label = isset( $_REQUEST['s'] ) ? __( 'Nothing found', $this->textdomain ) : __( 'No IP in whitelist', $this->textdomain ); ?>
			<p><?php echo $label; ?></p>
		<?php }

		function get_columns() {
			$columns = array(
				'cb'		=> '<input type="checkbox" />',
				'ip'		=> __( 'IP address', $this->textdomain ),
				'add_time'	=> __( 'Date added', $this->textdomain )
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
		 * Function to manage content of column with IP-adresses 
		 * @param     array     $item        The cuurrent letter data.
		 * @return    string                 
		 */
		function column_ip( $item ) {
			$actions = array(
				'remove' => '<a href="' . wp_nonce_url( sprintf( '?page=captcha.php&action=whitelist&cptch_remove=%s', $item['id'] ), 'cptch_nonce_remove_' . $item['id'] ) . '">' . __( 'Remove from whitelist', $this->textdomain ) . '</a>'
			);
			return sprintf('%1$s %2$s', $item['ip'], $this->row_actions( $actions ) );
		}
		/**
		 * List with bulk action for IP
		 * @return array   $actions   
		 */
		function get_bulk_actions() {
			/* adding bulk action */
			$actions = array(
				'cptch_remove'	=> __( 'Remove from whitelist', $this->textdomain ),
			);
			return $actions;
		}
		/**
		 * Get content for table
		 * @return  array  
		 */
		function get_content() {
			global $wpdb;
			$per_page = 20;
			$paged    = ( isset( $_REQUEST['paged'] ) && 1 < intval( $_REQUEST['paged'] ) ) ? $per_page * ( absint( intval( $_REQUEST['paged'] ) - 1 ) ) : 0;
			$order    = isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'ASC';
			if ( isset( $_GET['orderby'] ) && in_array( $_GET['orderby'], array_keys( $this->get_sortable_columns() ) ) && 'ip' != $_GET['orderby'] ) {
				switch ( $_GET['orderby'] ) {
					default:
						$order_by = $_GET['orderby'];
						break;
				}
			} else {
				$order_by = 'ip_from_int';
			}

			$sql_query = "SELECT * FROM `" . $wpdb->prefix . "cptch_whitelist` ";
			if ( isset( $_REQUEST['s'] ) ) {
				$ip = stripslashes( esc_html( trim( $_REQUEST['s'] ) ) );
				if ( '' != $ip ) {
					$ip_int = sprintf( '%u', ip2long( $ip ) );
					$query_where = 0 == $ip_int ? "`ip` LIKE '%" . $ip . "%'" : "( `ip_from_int` <= " . $ip_int . " AND `ip_to_int` >= " . $ip_int . " )";
					$sql_query .= "WHERE " . $query_where;
				}
			}
			$sql_query .= " ORDER BY " . $order_by . " " . $order . " LIMIT " . $per_page . " OFFSET " . $paged . ";";
			return $wpdb->get_results( $sql_query, ARRAY_A );
		}
		/**
		 * Handle necessary reqquests and display notices
		 * @return void
		 */
		function display_notices() {
			global $wpdb;
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
							$message = __( 'IP added in database successfully', $this->textdomain );
						else
							$error = __( 'Some errors occured', $this->textdomain );
					} else {
						$error = __( 'IP is allready in database', $this->textdomain );
					}
				} else {
					$error = __( 'Invalid IP. See allowed formats', $this->textdomain );
				}
			/* Remove IP from database */
			} elseif ( $bulk_action && check_admin_referer( $this->basename, 'captcha_nonce_name' ) ) {
				if ( ! empty( $_REQUEST['id'] ) ) {
					$list   = implode( ',', $_REQUEST['id'] );
					$result = $wpdb->query( "DELETE FROM `" . $wpdb->prefix . "cptch_whitelist` WHERE `id` IN (" . $list . ");" );
					if ( ! $wpdb->last_error )
						$message = sprintf( _n( "One IP was deleted successfully", "%s IPs were deleted successfully", $result, $this->textdomain ), $result );
					else
						$error = __( 'Some errors occured', $this->textdomain );
				}
			} elseif ( isset( $_GET['cptch_remove'] ) && check_admin_referer( 'cptch_nonce_remove_' . $_GET['cptch_remove'] ) ) {
				$result = $wpdb->query( "DELETE FROM `" . $wpdb->prefix . "cptch_whitelist` WHERE `id` = ". $_GET['cptch_remove'] . ";" );
				if ( ! $wpdb->last_error )
					$message = __( "One IP was deleted successfully", $this->textdomain );
				else
					$error = __( 'Some errors occured', $this->textdomain );
			} elseif ( isset( $_POST['cptch_add_to_whitelist'] ) && empty( $_POST['cptch_add_to_whitelist'] ) ) {
				$error = __( 'You have not entered any value', $this->textdomain );
			} elseif ( isset( $_REQUEST['s'] ) ) {
				if ( '' == $_REQUEST['s'] ) {
					$error = __( 'You have not enterd any value in to the search form', $this->textdomain );
				} else {
					$message = __( 'Search results for', $this->textdomain ) . '&nbsp;:&nbsp;' . $_REQUEST['s'];
				}
			}
			if ( ! empty( $message ) ) { ?>
				<div class="updated fade"><p><strong><?php echo $message; ?>.</strong></p></div>
			<?php }
			if ( ! empty( $error ) ) { ?>
				<div class="error"><p><strong><?php echo $error; ?>.</strong></p></div>
			<?php }
		}
	}
} ?>