<?php
/**
 * List Table class used in WordPress 4.2.x and below
 */
class S2_List_Table_Legacy extends WP_List_Table {
	private $date_format = '';
	private $time_format = '';

	public function __construct() {
		global $status, $page;

		parent::__construct(
			array(
				'singular' => 'subscriber',
				'plural'   => 'subscribers',
				'ajax'     => false,
			)
		);
		$this->date_format = get_option( 'date_format' );
		$this->time_format = get_option( 'time_format' );
	}

	public function column_default( $item, $column_name ) {
		global $current_tab;
		if ( 'registered' === $current_tab ) {
			switch ( $column_name ) {
				case 'email':
					return $item[ $column_name ];
			}
		} else {
			switch ( $column_name ) {
				case 'email':
				case 'date':
					return $item[ $column_name ];
			}
		}
	}

	public function column_email( $item ) {
		global $current_tab;
		if ( 'registered' === $current_tab ) {
			$actions = array(
				'edit' => sprintf( '<a href="?page=%s&amp;id=%d">%s</a>', 's2', rawurlencode( $item['id'] ), __( 'Edit', 'subscribe2' ) ),
			);
			return sprintf( '%1$s %2$s', $item['email'], $this->row_actions( $actions ) );
		} else {
			global $mysubscribe2;
			if ( '0' === $mysubscribe2->is_public( $item['email'] ) ) {
				return sprintf( '<span style="color:#FF0000"><abbr title="%2$s">%1$s</abbr></span>', $item['email'], $item['ip'] );
			} else {
				return sprintf( '<abbr title="%2$s">%1$s</abbr>', $item['email'], $item['ip'] );
			}
		}
	}

	public function column_date( $item ) {
		global $current_tab;
		if ( 'registered' === $current_tab ) {
			return $item['date'];
		} else {
			$timestamp = strtotime( $item['date'] . ' ' . $item['time'] );
			return sprintf( '<abbr title="%2$s">%1$s</abbr>', date_i18n( $this->date_format, $timestamp ), date_i18n( $this->time_format, $timestamp ) );
		}
	}

	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item['email'] );
	}

	public function get_columns() {
		global $current_tab;
		if ( 'registered' === $current_tab ) {
			$columns = array(
				'cb'    => '<input type="checkbox" />',
				'email' => _x( 'Email', 'column name', 'subscribe2' ),
			);
		} else {
			$columns = array(
				'cb'    => '<input type="checkbox" />',
				'email' => _x( 'Email', 'column name', 'subscribe2' ),
				'date'  => _x( 'Date', 'column name', 'subscribe2' ),
			);
		}
		return $columns;
	}

	public function get_sortable_columns() {
		global $current_tab;
		if ( 'registered' === $current_tab ) {
			$sortable_columns = array(
				'email' => array( 'email', true ),
			);
		} else {
			$sortable_columns = array(
				'email' => array( 'email', true ),
				'date'  => array( 'date', false ),
			);
		}
		return $sortable_columns;
	}

	public function print_column_headers( $with_id = true ) {
		list( $columns, $hidden, $sortable ) = $this->get_column_info();

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$current_url = remove_query_arg( 'paged', $current_url );

		if ( isset( $_REQUEST['what'] ) ) {
			$current_url = add_query_arg(
				array(
					'what' => $_REQUEST['what'],
				),
				$current_url
			);
		}

		if ( isset( $_GET['orderby'] ) ) {
			$current_orderby = $_GET['orderby'];
		} else {
			$current_orderby = '';
		}

		if ( isset( $_GET['order'] ) && 'desc' === $_GET['order'] ) {
			$current_order = 'desc';
		} else {
			$current_order = 'asc';
		}

		if ( ! empty( $columns['cb'] ) ) {
			static $cb_counter = 1;
			$columns['cb']     = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All', 'subscribe2' ) . '</label>'
				. '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
			$cb_counter++;
		}

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );

			$style = '';
			if ( in_array( $column_key, $hidden, true ) ) {
				$style = 'display:none;';
			}

			$style = ' style="' . $style . '"';

			if ( 'cb' === $column_key ) {
				$class[] = 'check-column';
			}

			if ( isset( $sortable[ $column_key ] ) ) {
				list( $orderby, $desc_first ) = $sortable[ $column_key ];

				if ( $current_orderby === $orderby ) {
					$order   = 'asc' === $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order   = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
			}

			$id = $with_id ? "id='$column_key'" : '';

			if ( ! empty( $class ) ) {
				$class = "class='" . join( ' ', $class ) . "'";
			}

			echo "<th scope='col' $id $class $style>$column_display_name</th>";
		}
	}

	public function get_bulk_actions() {
		global $current_tab;
		if ( 'registered' === $current_tab ) {
			if ( is_multisite() ) {
				return array();
			} else {
				return array(
					'delete' => __( 'Delete', 'subscribe2' ),
				);
			}
		} else {
			$actions = array(
				'delete' => __( 'Delete', 'subscribe2' ),
				'toggle' => __( 'Toggle', 'subscribe2' ),
			);
			return $actions;
		}
	}

	public function process_bulk_action() {
		if ( in_array( $this->current_action(), array( 'delete', 'toggle' ), true ) ) {
			if ( ! isset( $_REQUEST['subscriber'] ) ) {
				echo '<div id="message" class="error"><p><strong>' . __( 'No users were selected.', 'subscribe2' ) . '</strong></p></div>';
				return;
			}
		}
		if ( 'delete' === $this->current_action() ) {
			global $mysubscribe2, $current_user, $subscribers;
			$message = array();
			foreach ( $_REQUEST['subscriber'] as $address ) {
				$address = trim( stripslashes( $address ) );
				if ( false !== $mysubscribe2->is_public( $address ) ) {
					$mysubscribe2->delete( $address );
					$key = array_search( $address, $subscribers, true );
					unset( $subscribers[ $key ] );
					$message['public_deleted'] = __( 'Address(es) deleted!', 'subscribe2' );
				} else {
					$user = get_user_by( 'email', $address );
					if ( ! current_user_can( 'delete_user', $user->ID ) || $user->ID === $current_user->ID ) {
						$message['reg_delete_error'] = __( 'Delete failed! You cannot delete some or all of these users.', 'subscribe2' );
						continue;
					} else {
						$message['reg_deleted'] = __( 'Registered user(s) deleted! Any posts made by these users were assigned to you.', 'subscribe2' );
						foreach ( $subscribers as $key => $data ) {
							if ( in_array( $address, $data, true ) ) {
								unset( $subscribers[ $key ] );
							}
						}
						wp_delete_user( $user->ID, $current_user->ID );
					}
				}
			}
			$final_message = implode( '<br /><br />', array_filter( $message ) );
			echo '<div id="message" class="updated fade"><p><strong>' . $final_message . '</strong></p></div>';
		}
		if ( 'toggle' === $this->current_action() ) {
			global $mysubscribe2, $current_user, $subscribers;
			$mysubscribe2->ip = $current_user->user_login;
			foreach ( $_REQUEST['subscriber'] as $address ) {
				$address = trim( stripslashes( $address ) );
				$mysubscribe2->toggle( $address );
				if ( 'confirmed' === $_POST['what'] || 'unconfirmed' === $_POST['what'] ) {
					$key = array_search( $address, $subscribers, true );
					unset( $subscribers[ $key ] );
				}
			}
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'Status changed!', 'subscribe2' ) . '</strong></p></div>';
		}
	}

	public function pagination( $which ) {
		if ( empty( $this->_pagination_args ) ) {
			return;
		}

		$total_items     = intval( $this->_pagination_args['total_items'] );
		$total_pages     = intval( $this->_pagination_args['total_pages'] );
		$infinite_scroll = false;
		if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
			$infinite_scroll = $this->_pagination_args['infinite_scroll'];
		}

		// Translators: Pagination
		$output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items, 'subscribe2' ), number_format_i18n( $total_items ) ) . '</span>';

		if ( isset( $_POST['what'] ) ) {
			$current = 1;
		} else {
			$current = intval( $this->get_pagenum() );
		}

		if ( version_compare( $GLOBALS['wp_version'], '3.5', '<' ) ) {
			$current_url = esc_url( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		} else {
			$current_url = esc_url( set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) );
		}

		$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

		if ( isset( $_REQUEST['what'] ) ) {
			$current_url = add_query_arg(
				array(
					'what' => $_REQUEST['what'],
				),
				$current_url
			);
		}

		if ( isset( $_POST['s'] ) ) {
			$current_url = add_query_arg(
				array(
					's' => $_POST['s'],
				),
				$current_url
			);
		}

		$page_links = array();

		$disable_first = '';
		$disable_last  = '';
		if ( 1 === $current ) {
			$disable_first = ' disabled';
		}
		if ( $current === $total_pages ) {
			$disable_last = ' disabled';
		}

		$page_links[] = sprintf(
			"<a class='%s' title='%s' href='%s'>%s</a>",
			'first-page' . $disable_first,
			esc_attr__( 'Go to the first page', 'subscribe2' ),
			remove_query_arg( 'paged', $current_url ),
			'&laquo;'
		);

		$page_links[] = sprintf(
			"<a class='%s' title='%s' href='%s'>%s</a>",
			'prev-page' . $disable_first,
			esc_attr__( 'Go to the previous page', 'subscribe2' ),
			add_query_arg( 'paged', max( 1, $current - 1 ), $current_url ),
			'&lsaquo;'
		);

		if ( 'bottom' === $which ) {
			$html_current_page = $current;
		} else {
			$html_current_page = sprintf(
				"<input class='current-page' title='%s' type='text' name='paged' value='%s' size='%d' />",
				esc_attr__( 'Current page', 'subscribe2' ),
				$current,
				strlen( $total_pages )
			);
		}

		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		// Translators: Pagination
		$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging', 'subscribe2' ), $html_current_page, $html_total_pages ) . '</span>';

		$page_links[] = sprintf(
			"<a class='%s' title='%s' href='%s'>%s</a>",
			'next-page' . $disable_last,
			esc_attr__( 'Go to the next page', 'subscribe2' ),
			add_query_arg( 'paged', min( $total_pages, $current + 1 ), $current_url ),
			'&rsaquo;'
		);

		$page_links[] = sprintf(
			"<a class='%s' title='%s' href='%s'>%s</a>",
			'last-page' . $disable_last,
			esc_attr__( 'Go to the last page', 'subscribe2' ),
			add_query_arg( 'paged', $total_pages, $current_url ),
			'&raquo;'
		);

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) ) {
			$pagination_links_class = ' hide-if-js';
		}
		$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages ) {
			$page_class = $total_pages < 2 ? ' one-page' : '';
		} else {
			$page_class = ' no-pages';
		}

		$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $this->_pagination;
	}

	public function prepare_items() {
		global $mysubscribe2, $subscribers, $current_tab;

		$user          = get_current_user_id();
		$screen        = get_current_screen();
		$screen_option = $screen->get_option( 'per_page', 'option' );
		$per_page      = get_user_meta( $user, $screen_option, true );
		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
		}

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		$data = array();
		if ( 'public' === $current_tab ) {
			foreach ( (array) $subscribers as $email ) {
				$data[] = array(
					'email' => $email,
					'date'  => $mysubscribe2->signup_date( $email ),
					'time'  => $mysubscribe2->signup_time( $email ),
					'ip'    => $mysubscribe2->signup_ip( $email ),
				);
			}
		} else {
			foreach ( (array) $subscribers as $subscriber ) {
				$data[] = array(
					'email' => $subscriber['user_email'],
					'id'    => $subscriber['ID'],
				);
			}
		}

		function usort_reorder( $a, $b ) {
			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'email';
			$order   = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc';
			$result  = strcasecmp( $a[ $orderby ], $b[ $orderby ] );
			return ( 'asc' === $order ) ? $result : -$result;
		}
		usort( $data, 'usort_reorder' );

		if ( isset( $_POST['what'] ) ) {
			$current_page = 1;
		} else {
			$current_page = $this->get_pagenum();
		}
		$total_items = count( $data );
		$data        = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		$this->items = $data;

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}
}
