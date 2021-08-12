<?php
/**
 * Helper class for WP_Document_Revisions that registers the recently revised widget.
 *
 * @since 3.2.2
 * @package WP_Document_Revisions
 */

/**
 * Recently revised documents widget.
 */
class WP_Document_Revisions_Recently_Revised_Widget extends WP_Widget {

	/**
	 * Default widget settings
	 *
	 * @var $defaults
	 */
	private $defaults = array(
		'numberposts' => 5,
		'post_status' => array(
			'publish' => true,
			'private' => false,
			'draft'   => false,
		),
		'show_author' => true,
	);

	/**
	 * Init widget and register.
	 */
	public function __construct() {
		parent::__construct( 'WP_Document_Revisions_Recently_Revised_Widget', __( 'Recently Revised Documents', 'wp-document-revisions' ) );

		// can't i18n outside of a function.
		$this->defaults['title'] = __( 'Recently Revised Documents', 'wp-document-revisions' );
	}


	/**
	 * Generate widget contents.
	 *
	 * @param Array  $args the widget arguments.
	 * @param Object $instance the WP Document Revisions instance.
	 */
	public function widget_gen( $args, $instance ) {

		global $wpdr;
		if ( ! $wpdr ) {
			$wpdr = new WP_Document_Revisions();
		}

		// enabled statuses are stored as status => bool, but we want an array of only activated statuses.
		$statuses = array_filter( (array) $instance['post_status'] );
		$statuses = array_keys( $statuses );

		$query = array(
			'orderby'     => 'modified',
			'order'       => 'DESC',
			'numberposts' => (int) $instance['numberposts'],
			'post_status' => $statuses,
			'perm'        => 'readable',
		);

		$documents = $wpdr->get_documents( $query );

		// no documents, don't bother.
		if ( ! $documents ) {
			return '';
		}

		// buffer output to return rather than echo directly.
		ob_start();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['before_widget'] . $args['before_title'] . esc_html( apply_filters( 'widget_title', $instance['title'] ) ) . $args['after_title'] . '<ul>';

		foreach ( $documents as $document ) {
			$link = ( current_user_can( 'edit_document', $document->ID ) ) ? add_query_arg(
				array(
					'post'   => $document->ID,
					'action' => 'edit',
				),
				admin_url( 'post.php' )
			) : get_permalink( $document->ID );
			// translators: %1$s is the time ago in words, %2$s is the author.
			$format_string = ( $instance['show_author'] ) ? __( '%1$s ago by %2$s', 'wp-document-revisions' ) : __( '%1$s ago', 'wp-document-revisions' );
			?>
			<li>
				<a href="<?php echo esc_attr( $link ); ?>"><?php echo esc_html( get_the_title( $document->ID ) ); ?></a><br />
				<?php printf( esc_html( $format_string ), esc_html( human_time_diff( strtotime( $document->post_modified_gmt ) ) ), esc_html( get_the_author_meta( 'display_name', $document->post_author ) ) ); ?>
			</li>
			<?php
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</ul>' . $args['after_widget'];

		// return buffer contents and remove it.
		return ob_get_clean();
	}

	/**
	 * Callback to display widget contents in classic widget.
	 *
	 * @param Array  $args the widget arguments.
	 * @param Object $instance the WP Document Revisions instance.
	 */
	public function widget( $args, $instance ) {
		$output = $this->widget_gen( $args, $instance );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $output;
	}

	/**
	 * Callback to display widget options form.
	 *
	 * @param Object $instance the WP Document Revisions instance.
	 */
	public function form( $instance ) {

		foreach ( $this->defaults as $key => $value ) {
			if ( ! isset( $instance[ $key ] ) ) {
				$instance[ $key ] = $value;
			}
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wp-document-revisions' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'numberposts' ) ); ?>"><?php esc_html_e( 'Number of Posts:', 'wp-document-revisions' ); ?></label><br />
			<input class="small-text" id="<?php echo esc_attr( $this->get_field_id( 'numberposts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'numberposts' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['numberposts'] ); ?>" />
		</p>
		<p>
			<?php esc_html_e( 'Posts to Show:', 'wp-document-revisions' ); ?><br />
			<?php foreach ( $instance['post_status'] as $status => $value ) : ?>
				<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'post_status_' . $status ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_status_' . $status ) ); ?>" type="text" <?php checked( $value ); ?> />
				<label for="<?php echo esc_attr( $this->get_field_name( 'post_status_' . $status ) ); ?>"><?php echo esc_html( ucwords( $status ) ); ?></label><br />
			<?php endforeach; ?>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_author' ) ); ?>"><?php esc_html_e( 'Display Document Author:', 'wp-document-revisions' ); ?></label><br />
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_author' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_author' ) ); ?>" <?php checked( $instance['show_author'] ); ?> /> <?php esc_html_e( 'Yes', 'wp-document-revisions' ); ?>
		</p>
		<?php
	}


	/**
	 * Sanitizes options and saves.
	 *
	 * @param Object $new_instance the new instance.
	 * @param Object $old_instance the old instance.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance                = $old_instance;
		$instance['title']       = wp_strip_all_tags( $new_instance['title'] );
		$instance['numberposts'] = (int) $new_instance['numberposts'];
		$instance['show_author'] = (bool) $new_instance['show_author'];

		// merge post statuses into an array.
		foreach ( $this->defaults['post_status'] as $status => $value ) {
			$instance['post_status'][ $status ] = (bool) isset( $new_instance[ 'post_status_' . $status ] );
		}

		return $instance;
	}


	/**
	 * Register widget block.
	 *
	 * @since 3.3.0
	 */
	public function documents_widget_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active, e.g. Old WP version installed.
			return;
		}

		$dir      = dirname( __DIR__ );
		$suffix   = ( WP_DEBUG ) ? '.dev' : '';
		$index_js = 'js/wpdr-documents-widget' . $suffix . '.js';
		wp_register_script(
			'wpdr-documents-widget-editor',
			plugins_url( $index_js, __DIR__ ),
			array(
				'wp-blocks',
				'wp-element',
				'wp-block-editor',
				'wp-components',
				'wp-server-side-render',
				'wp-i18n',
			),
			filemtime( "$dir/$index_js" ),
			true
		);

		$index_css = 'css/wpdr-widget-editor-style.css';
		wp_register_style(
			'wpdr-documents-widget-editor-style',
			plugins_url( $index_css, __DIR__ ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( "$dir/$index_css" ) )
		);

		register_block_type(
			'wp-document-revisions/documents-widget',
			array(
				'editor_script'   => 'wpdr-documents-widget-editor',
				'editor_style'    => 'wpdr-documents-widget-editor-style',
				'render_callback' => array( $this, 'wpdr_documents_widget_display' ),
				'attributes'      => array(
					'header'            => array(
						'type' => 'string',
					),
					'numberposts'       => array(
						'type'    => 'number',
						'default' => 5,
					),
					'post_stat_publish' => array(
						'type' => 'boolean',
					),
					'post_stat_private' => array(
						'type' => 'boolean',
					),
					'post_stat_draft'   => array(
						'type' => 'boolean',
					),
					'show_author'       => array(
						'type'    => 'boolean',
						'default' => true,
					),
				),
			)
		);

		// set translations.
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wpdr-documents-widget-editor', 'wp-document-revisions' );
		}
	}


	/**
	 * Render widget block server side.
	 *
	 * @param array $atts block attributes coming from block.
	 * @since 3.3.0
	 */
	public function wpdr_documents_widget_display( $atts ) {
		// Create the two parameter sets.
		$args                    = array(
			'before_widget' => '',
			'before_title'  => '',
			'after_title'   => '',
			'after_widget'  => '',
		);
		$instance                = array();
		$instance['title']       = ( isset( $atts['header'] ) ? $atts['header'] : '' );
		$instance['numberposts'] = ( isset( $atts['numberposts'] ) ? (int) $atts['numberposts'] : 5 );
		$instance['show_author'] = ( isset( $atts['show_author'] ) ? (bool) $atts['show_author'] : true );
		$instance['post_status'] = array(  // temp.
			'publish' => ( isset( $atts['post_stat_publish'] ) ? (bool) $atts['post_stat_publish'] : true ),
			'private' => ( isset( $atts['post_stat_private'] ) ? (bool) $atts['post_stat_private'] : false ),
			'draft'   => ( isset( $atts['post_stat_draft'] ) ? (bool) $atts['post_stat_draft'] : false ),
		);

		$output = $this->widget_gen( $args, $instance );
		return $output;
	}
}


/**
 * Callback to register the recently revised widget.
 */
function wpdr_widgets_init() {
	global $wpdr_widget;

	register_widget( $wpdr_widget );
}

add_action( 'widgets_init', 'wpdr_widgets_init' );

/**
 * Callback to register the recently revised widget block.
 *
 * Call with low priority to let taxonomies be registered.
 */
function wpdr_widgets_block_init() {
	global $wpdr_widget;

	$wpdr_widget->documents_widget_block();
}

add_action( 'init', 'wpdr_widgets_block_init', 99 );
