<?php

/**
 * Functions relating to the reports CPT screen.
 *
 * @author     Ben Roberts
 *
 */
class BP_Toolkit_Admin_Report_Screen {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.1.0
	 * @access   private
	 * @var      string $bp_toolkit The ID of this plugin.
	 */
	private $bp_toolkit;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.1.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $bp_toolkit The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    2.1.0
	 */
	public function __construct( $bp_toolkit, $version ) {

		$this->bp_toolkit = $bp_toolkit;
		$this->version    = $version;

	}


	/**
	 * Manage our hooks. Called from class-bp-toolkit.php.
	 *
	 * @since    2.1.0
	 *
	 */
	public function init() {

		add_filter( 'post_row_actions', array( $this, 'report_row_actions' ), 10, 2 );
		add_filter( 'manage_report_posts_columns', array( $this, 'set_report_columns' ) );
		add_filter( 'manage_report_posts_custom_column', array( $this, 'add_report_columns' ), 10, 2 );
		add_filter( 'post_class', array( $this, 'set_row_post_class' ), 10, 3 );
		add_filter( 'views_edit-report', array( $this, 'create_view' ), 10 );
		add_action( 'pre_get_posts', array( $this, 'filter_unread_posts' ) );
		add_action( 'wp_ajax_toggle_read', array( $this, 'toggle_read' ) );
		add_filter( 'bulk_actions-edit-report', array( $this, 'register_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-edit-report', array( $this, 'handle_bulk_actions' ), 10, 3 );
	}

	/**
	 * Build our report post type table.
	 *
	 * @param        $columns The columns passed to us from the filter.
	 *
	 * @since    2.0.0
	 *
	 */
	public function set_report_columns( $columns ) {

		$columns = array(
			'cb'       => '<input type="checkbox" />',
			// 'title' => __( 'Report Summary', 'bp-toolkit' ),
			'reporter' => __( 'Reporter', 'bp-toolkit' ),
			'activity' => __( 'Item', 'bp-toolkit' ),
			'type'     => __( 'Subject', 'bp-toolkit' ),
			'reported' => __( 'Author or User Reported', 'bp-toolkit' ),
			'content'  => __( 'Details', 'bp-toolkit' ),
			'total'    => __( 'Total Reports', 'bp-toolkit' )
		);

		return $columns;
	}

	/**
	 * Set our report post type table custom columns.
	 *
	 * @param        $column_name The custom column passed to us from the filter.
	 *
	 * @since    2.0.0
	 *
	 */
	public function add_report_columns( $column_name, $post_id ) {

		switch ( $column_name ) {

			case 'content' :
				echo get_the_excerpt( $post_id );
				break;

			case 'type' :
				echo strip_tags( ( get_the_term_list( $post_id, 'report-type', '', ', ' ) ) );
				break;

			case 'activity':
				switch ( get_post_meta( $post_id, '_bptk_activity_type', true ) ) {
					case 'member':
						echo '<div><span class="dashicons dashicons-admin-users"></span></div>';
						break;
					case 'comment':
						echo '<div><span class="dashicons dashicons-admin-comments"></span></div>';
						break;
					case 'activity':
						echo '<div><span class="dashicons dashicons-buddicons-activity"></span></div>';
						break;
					case 'activity-comment':
						echo '<div><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="1024px" height="1024px" viewBox="0 0 1024 1024" t="1569682881658" class="icon" version="1.1" p-id="8185"><defs><style type="text/css"/></defs><path d="M573 421c-23.1 0-41 17.9-41 40s17.9 40 41 40c21.1 0 39-17.9 39-40s-17.9-40-39-40zM293 421c-23.1 0-41 17.9-41 40s17.9 40 41 40c21.1 0 39-17.9 39-40s-17.9-40-39-40z" p-id="8186"/><path d="M894 345c-48.1-66-115.3-110.1-189-130v0.1c-17.1-19-36.4-36.5-58-52.1-163.7-119-393.5-82.7-513 81-96.3 133-92.2 311.9 6 439l0.8 132.6c0 3.2 0.5 6.4 1.5 9.4 5.3 16.9 23.3 26.2 40.1 20.9L309 806c33.5 11.9 68.1 18.7 102.5 20.6l-0.5 0.4c89.1 64.9 205.9 84.4 313 49l127.1 41.4c3.2 1 6.5 1.6 9.9 1.6 17.7 0 32-14.3 32-32V753c88.1-119.6 90.4-284.9 1-408zM323 735l-12-5-99 31-1-104-8-9c-84.6-103.2-90.2-251.9-11-361 96.4-132.2 281.2-161.4 413-66 132.2 96.1 161.5 280.6 66 412-80.1 109.9-223.5 150.5-348 102z m505-17l-8 10 1 104-98-33-12 5c-56 20.8-115.7 22.5-171 7l-0.2-0.1C613.7 788.2 680.7 742.2 729 676c76.4-105.3 88.8-237.6 44.4-350.4l0.6 0.4c23 16.5 44.1 37.1 62 62 72.6 99.6 68.5 235.2-8 330z" p-id="8187"/><path d="M433 421c-23.1 0-41 17.9-41 40s17.9 40 41 40c21.1 0 39-17.9 39-40s-17.9-40-39-40z" p-id="8188"/></svg></div>';
						break;
					case 'group':
						echo '<div><span class="dashicons dashicons-buddicons-groups"></span></div>';
						break;
					case 'message':
						echo '<div><span class="dashicons dashicons-buddicons-pm"></span></div>';
						break;
					case 'forum-topic':
						echo '<div><span class="dashicons dashicons-buddicons-topics"></span></div>';
						break;
					case 'forum-reply':
						echo '<div><span class="dashicons dashicons-buddicons-replies"></span></div>';
						break;
					case 'rtmedia':
						echo '<div><span class="dashicons dashicons-admin-media"></span></div>';
						break;
				}

				break;

			case 'reporter' :
				$user = get_user_by( 'ID', get_post_meta( $post_id, '_bptk_reported_by', true ) );
				echo '<a href="' . get_edit_post_link( $post_id ) . '"><div>' . bp_core_fetch_avatar( array( 'item_id' => $user->ID ) ) . '<div><span>' . $user->display_name . '</span><span>' . get_the_date( "F j, Y \\a\\t g:i a",
						$post_id ) . '</span></div></div></a>';
				break;

			case 'reported' :
				$user = get_user_by( 'ID', get_post_meta( $post_id, '_bptk_member_reported', true ) );

				$substantiated = ' | Upheld <span class="bptk-reports-count">(' . bptk_substantiated_reports_about_user( $user->ID ) . ')</span>';

				echo '<div>' . bp_core_fetch_avatar( array(
						'item_id' => get_post_meta( $post_id, '_bptk_member_reported', true )
					) ) . '<div><span>' . $user->display_name . '</span><span>Reports <span class="bptk-reports-count">(' . bptk_reports_about_user( $user->ID ) . ')</span>' . $substantiated . '</span></div></div>';
				break;

			case 'total' :
				if ( get_post_meta( $post_id, '_bptk_admin_created', true ) == 1 ) {
					$color = 'purple';
				} elseif ( get_post_meta( $post_id, 'is_upheld', true ) == 0 ) {
					$color = 'green';
				} elseif ( in_array( get_post_meta( $post_id, '_bptk_item_id', true ),
					bptk_get_moderated_list( get_post_meta( $post_id, '_bptk_activity_type', true ) ) ) ) {
					$color = '#cc3333';
				} else {
					$color = 'orange';
				}

				if ( get_post_meta( $post_id, '_bptk_admin_created', true ) == 1 ) {
					echo '<div style="background-color: ' . $color . ';">' . bptk_substantiated_reports_about_user( get_post_meta( $post_id,
							'_bptk_member_reported', true ) ) . '</div>';
				} else {
					echo '<div style="background-color: ' . $color . ';">' . bptk_reports_per_item( get_post_meta( $post_id,
							'_bptk_item_id', true ) ) . '</div>';
				}
				break;

		}
	}

	/**
	 * Register bulk actions.
	 *
	 * @since    2.1.0
	 *
	 */
	public function register_bulk_actions( $actions ) {

		$actions['mark_read']   = esc_html__( 'Mark as Read', 'bp-toolkit' );
		$actions['mark_unread'] = esc_html__( 'Mark as Unread', 'bp-toolkit' );

		return $actions;
	}

	/**
	 * Handle bulk actions.
	 *
	 * @since    2.1.0
	 *
	 */
	public function handle_bulk_actions( $redirect_to, $action_name, $post_ids ) {

		// Get rid of any lingering query args, so they don't clutter url bar or regenerate admin notices
		$redirect_to = remove_query_arg( array( 'mark_read', 'mark_unread' ), $redirect_to );

		if ( 'mark_read' === $action_name ) {
			foreach ( $post_ids as $post_id ) {
				update_post_meta( $post_id, 'is_read', 1 );
			}
			$redirect_to = add_query_arg( 'mark_read', count( $post_ids ), $redirect_to );

			return $redirect_to;
		} elseif ( 'mark_unread' === $action_name ) {
			foreach ( $post_ids as $post_id ) {
				update_post_meta( $post_id, 'is_read', 0 );
			}
			$redirect_to = add_query_arg( 'mark_unread', count( $post_ids ), $redirect_to );

			return $redirect_to;
		} else {
			return $redirect_to;
		}

	}

	/**
	 * Add a mark read/mark unread row action.
	 *
	 * @since    2.1.0
	 *
	 */
	public function report_row_actions( $actions, $post ) {
		// Check for your post type.
		if ( $post->post_type == 'report' ) {

			if ( metadata_exists( 'post', $post->ID, 'is_read' ) ) {

				/* Create Nonce */
				$toggle_read_nonce = wp_create_nonce( 'toggle_read_nonce' );


				$meta = get_post_meta( $post->ID, 'is_read', true );

				if ( $meta == 0 ) {
					$read = false;
				} elseif ( $meta == 1 ) {
					$read = true;
				}

				ob_start();

				?>

                <a class="bptk-report-row" data-nonce="<?php echo $toggle_read_nonce ?>"
                   data-report="<?php echo esc_attr( $post->ID ) ?>" id="mark_read_<?php echo esc_attr( $post->ID ); ?>"
                   aria-label="Mark this entry as read"
                   style="cursor: pointer; display:<?php echo $read ? 'none' : 'inline' ?>"><?php esc_html_e( 'Mark read',
						'bp-toolkit' ); ?></a>
                <a class="bptk-report-row" data-nonce="<?php echo $toggle_read_nonce ?>"
                   data-report="<?php echo esc_attr( $post->ID ) ?>"
                   id="mark_unread_<?php echo esc_attr( $post->ID ); ?>"
                   aria-label="<?php esc_attr_e( 'Mark this entry as unread', 'bp-toolkit' ); ?>"
                   style="cursor: pointer; display:<?php echo $read ? 'inline' : 'none' ?>"><?php esc_html_e( 'Mark unread',
						'bp-toolkit' ); ?></a>

				<?php

				$actions['mark_read'] = ob_get_clean();
			}
		}

		return $actions;
	}

	/**
	 * Style our report rows.
	 *
	 * @since    2.1.0
	 *
	 */
	public function set_row_post_class( $classes, $class, $post_id ) {

		// make sure we are in the dashboard
		if ( ! is_admin() ) {
			return $classes;
		}

		// verify which page we're on
		$screen = get_current_screen();

		if( ! is_object( $screen ) ) {
			return $classes;
		}

		if ( 'report' != $screen->post_type && 'edit' != $screen->base ) {
			return $classes;
		}

		// if < 2.1.0, no metadata will exist, so never show as unread (it probably will have been)
		if ( ! metadata_exists( 'post', $post_id, 'is_read' ) ) {
			return $classes;
		}

		//check if some meta field is set
		$is_read = get_post_meta( $post_id, 'is_read', true );
		if ( $is_read == 0 ) {
			$classes[] = 'report-unread'; //add a custom class to highlight this row in the table
		}

		// Return the array
		return $classes;
	}

	/**
	 * Marks a report as read or unread via AJAX.
	 *
	 * @since 3.0.0
	 */
	public function toggle_read() {

		check_ajax_referer( 'toggle_read_nonce', 'nonce' );

		if ( $_POST['status'] == "false" ) {
			update_post_meta( $_POST['report_id'], 'is_read', 1 );
			echo 'Report changed to read';
		} elseif ( $_POST['status'] == "true" ) {
			update_post_meta( $_POST['report_id'], 'is_read', 0 );
			echo 'Report changed to unread';
		}

		wp_die();
	}

	/**
	 * Add a view to users table to select unread reports.
	 *
	 * @since 2.1.0
	 */
	public function create_view( $views ) {

		$count = $this->count_unread_reports();

		$base_url = bp_get_admin_url( 'edit.php?post_type=report' );

		$url  = add_query_arg( 'unread', 'true', $base_url );
		$text = sprintf( _x( 'Unread %s', 'unread reports', 'bp-toolkit' ),
			'<span class="count">(<span id="unread_count">' . number_format_i18n( $count ) . '</span>)</span>' );

		$views['unread'] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $url ), $text );

		return $views;
	}

	/**
	 * Counts all unread reports.
	 *
	 * @since 2.1.0
	 */
	public function count_unread_reports() {

		$count = count( $this->get_unread_reports() );

		return $count;
	}

	/**
	 * Returns all unread reports.
	 *
	 * @since 2.1.0
	 */
	public function get_unread_reports() {

		$args           = array(
			'post_type'   => 'report',
			'numberposts' => - 1,
			'meta_query'  => array(
				array(
					'key'     => 'is_read',
					'value'   => '0',
					'compare' => '='
				)
			)
		);
		$unread_reports = get_posts( $args );

		return $unread_reports;


	}

	/**
	 * Filters the reports list to only show unread posts.
	 *
	 * @since 2.1.0
	 */
	public function filter_unread_posts( $query ) {

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( is_admin() && $query->is_main_query() && isset( $_REQUEST['unread'] ) && $screen->post_type == 'report' ) {
			$query->set( 'meta_key', 'is_read' );
			$query->set( 'meta_query', array(
				array(
					'key'     => 'is_read',
					'compare' => '=',
					'value'   => 0,
				)
			) );
		}
	}
}
