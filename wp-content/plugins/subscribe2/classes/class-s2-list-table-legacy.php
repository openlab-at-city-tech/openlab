<?php
/**
List Table class used in WordPress 4.2.x and below
*/
class S2_List_Table_Legacy extends WP_List_Table {
	function __construct() {
		global $status, $page;

		parent::__construct( array(
			'singular'	=> 'subscriber',
			'plural'	=> 'subscribers',
			'ajax'		=> false,
		) );
	}

	function column_default( $item, $column_name ) {
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

	function column_email( $item ) {
		global $current_tab;
		if ( 'registered' === $current_tab ) {
			$actions = array(
				'edit' => sprintf( '<a href="?page=%s&amp;id=%d">%s</a>', 's2', urlencode( $item['id'] ), __( 'Edit', 'subscribe2' ) ),
			);
			return sprintf( '%1$s %2$s', $item['email'], $this->row_actions( $actions ) );
		} else {
			global $mysubscribe2;
			if ( '0' === $mysubscribe2->is_public( $item['email'] ) ) {
				return sprintf( '<span style="color:#FF0000"><abbr title="' . $mysubscribe2->signup_ip( $item['email'] ) . '">%1$s</abbr></span>', $item['email'] );
			} else {
				return sprintf( '<abbr title="' . $mysubscribe2->signup_ip( $item['email'] ) . '">%1$s</abbr>', $item['email'] );
			}
		}
	}

	function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item['email'] );
	}

	function get_columns() {
		global $current_tab;
		if ( 'registered' === $current_tab ) {
			if ( is_multisite() ) {
				$columns = array(
					'email' => _x( 'Email', 'column name', 'subscribe2' ),
				);
			} else {
				$columns = array(
					'cb'		=> '<input type="checkbox" />',
					'email' => _x( 'Email', 'column name', 'subscribe2' ),
				);
			}
		} else {
			$columns = array(
				'cb'	=> '<input type="checkbox" />',
				'email'	=> _x( 'Email', 'column name', 'subscribe2' ),
				'date'	=> _x( 'Date', 'column name', 'subscribe2' ),
			);
		}
		return $columns;
	}

	function get_sortable_columns() {
		global $current_tab;
		if ( 'registered' === $current_tab ) {
			$sortable_columns = array(
				'email' => array( 'email', true ),
			);
		} else {
			$sortable_columns = array(
				'email'	=> array( 'email', true ),
				'date'	=> array( 'date', false ),
			);
		}
		return $sortable_columns;
	}

	function get_bulk_actions() {
		global $current_tab;
		if ( 'registered' === $current_tab ) {
			if ( is_multisite() ) {
				return array();
			} else {
				global $mysubscribe2;
				if ( 'never' === $mysubscribe2->subscribe2_options['email_freq'] ) {
					return array(
						'delete' => __( 'Delete', 'subscribe2' ),
						'subscribe' => __( 'Subscribe', 'subscribe2' ),
						'unsubscribe' => __( 'Unsubscribe', 'subscribe2' ),
						'format' => __( 'Change Email Format', 'subscribe2' ),
					);
				} else {
					return array(
						'delete' => __( 'Delete', 'subscribe2' ),
						'digest' => __( 'Change Digest Subscription', 'subscribe2' ),
					);
				}
			}
		} else {
			$actions = array(
				'delete'	=> __( 'Delete', 'subscribe2' ),
				'toggle'	=> __( 'Toggle', 'subscribe2' ),
			);
			return $actions;
		}
	}

	function process_bulk_action() {
		if ( in_array( $this->current_action(), array( 'delete', 'toggle', 'subscribe', 'unsubscribe', 'format', 'digest' ) ) ) {
			if ( ! isset( $_REQUEST['subscriber'] ) ) {
				echo '<div id="message" class="error"><p><strong>' . __( 'No users were selected.' , 'subscribe2' ) . '</strong></p></div>';
				return;
			}
		}
		if ( 'delete' === $this->current_action() ) {
			global $mysubscribe2, $current_user, $subscribers;
			$message = array();
			foreach ( $_REQUEST['subscriber'] as $address ) {
				if ( false !== $mysubscribe2->is_public( $address ) ) {
					$mysubscribe2->delete( $address );
					$key = array_search( $address, $subscribers );
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
							if ( in_array( $address, $data ) ) {
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
				$mysubscribe2->toggle( $address );
				if ( 'confirmed' === $_POST['what'] || 'unconfirmed' === $_POST['what'] ) {
					$key = array_search( $address, $subscribers );
					unset( $subscribers[ $key ] );
				}
			}
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'Status changed!', 'subscribe2' ) . '</strong></p></div>';
		}
		if ( 'subscribe' === $this->current_action() ) {
			global $mysubscribe2;
			$mysubscribe2->subscribe_registered_users( implode( ",\r\n", $_REQUEST['subscriber'] ), $_POST['category'] );
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'Registered Users Subscribed!', 'subscribe2' ) . '</strong></p></div>';
		}
		if ( 'unsubscribe' === $this->current_action() ) {
			global $mysubscribe2;
			$mysubscribe2->unsubscribe_registered_users( implode( ",\r\n", $_REQUEST['subscriber'] ), $_POST['category'] );
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'Registered Users Unsubscribed!', 'subscribe2' ) . '</strong></p></div>';
		}
		if ( 'format' === $this->current_action() ) {
			global $mysubscribe2;
			$mysubscribe2->format_change( implode( ",\r\n", $_REQUEST['subscriber'] ), $_POST['format'] );
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'Format updated for Selected Registered Users!', 'subscribe2' ) . '</strong></p></div>';
		}
		if ( 'digest' === $this->current_action() ) {
			global $mysubscribe2;
			$mysubscribe2->digest_change( implode( ",\r\n", $_REQUEST['subscriber'] ), $_POST['sub_category'] );
			echo '<div id="message" class="error"><p><strong>' . __( 'Digest Subscription updated for Selected Registered Users!', 'subscribe2' ) . '</strong></p></div>';
		}
	}

	function pagination( $which ) {
		if ( empty( $this->_pagination_args ) ) {
			return;
		}

		$total_items = intval( $this->_pagination_args['total_items'] );
		$total_pages = intval( $this->_pagination_args['total_pages'] );
		$infinite_scroll = false;
		if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
			$infinite_scroll = $this->_pagination_args['infinite_scroll'];
		}

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

		if ( isset( $what ) ) {
			$current_url = add_query_arg( array(
				'what' => $what,
			), $current_url );
		} elseif ( isset( $_REQUEST['what'] ) ) {
			$current_url = add_query_arg( array(
				'what' => $_REQUEST['what'],
			), $current_url );
		}

		if ( isset( $_POST['s'] ) ) {
			$current_url = add_query_arg( array(
				's' => $_POST['s'],
			), $current_url );
		}

		$page_links = array();

		$disable_first = $disable_last = '';
		if ( 1 === $current ) {
			$disable_first = ' disabled';
		}
		if ( $current === $total_pages ) {
			$disable_last = ' disabled';
		}

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'first-page' . $disable_first,
			esc_attr__( 'Go to the first page', 'subscribe2' ),
			remove_query_arg( 'paged', $current_url ),
			'&laquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'prev-page' . $disable_first,
			esc_attr__( 'Go to the previous page', 'subscribe2' ),
			add_query_arg( 'paged', max( 1, $current - 1 ), $current_url ),
			'&lsaquo;'
		);

		if ( 'bottom' === $which ) {
			$html_current_page = $current;
		} else {
			$html_current_page = sprintf( "<input class='current-page' title='%s' type='text' name='paged' value='%s' size='%d' />",
				esc_attr__( 'Current page', 'subscribe2' ),
				$current,
				strlen( $total_pages )
			);
		}

		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging', 'subscribe2' ), $html_current_page, $html_total_pages ) . '</span>';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'next-page' . $disable_last,
			esc_attr__( 'Go to the next page', 'subscribe2' ),
			add_query_arg( 'paged', min( $total_pages, $current + 1 ), $current_url ),
			'&rsaquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
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

	function prepare_items() {
		global $mysubscribe2, $subscribers, $current_tab;
		if ( is_int( $mysubscribe2->subscribe2_options['entries'] ) ) {
			$per_page = $mysubscribe2->subscribe2_options['entries'];
		} else {
			$per_page = 25;
		}

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		$data = array();
		if ( 'public' === $current_tab ) {
			foreach ( (array) $subscribers as $email ) {
				$data[] = array(
					'email' => $email,
					'date' => $mysubscribe2->signup_date( $email ),
				);
			}
		} else {
			foreach ( (array) $subscribers as $subscriber ) {
				$data[] = array(
					'email' => $subscriber['user_email'],
					'id' => $subscriber['ID'],
				);
			}
		}

		function usort_reorder( $a, $b ) {
			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'email';
			$order = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc';
			$result = strcasecmp( $a[ $orderby ], $b[ $orderby ] );
			return ( 'asc' === $order ) ? $result : -$result;
		}
		usort( $data, 'usort_reorder' );

		if ( isset( $_POST['what'] ) ) {
			$current_page = 1;
		} else {
			$current_page = $this->get_pagenum();
		}
		$total_items = count( $data );
		$data = array_slice( $data,( ($current_page -1 ) * $per_page ), $per_page );
		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items'	=> $total_items,
			'per_page'		=> $per_page,
			'total_pages'	=> ceil( $total_items / $per_page ),
		) );
	}
}
?>