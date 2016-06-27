<?php
/**
 * A library to bring the WP admin post interface to the frontend.
 *
 * @author r-a-y
 */

if ( ! class_exists( 'WP_Frontend_Admin_Screen' ) ) :
/**
 * Core class.
 */
class WP_Frontend_Admin_Screen {
	/**
	 * Post type to edit / create.
	 *
	 * @var string
	 */
	public static $post_type = 'post';

	/**
	 * The queried WP_Post object.
	 *
	 * @var WP_Post
	 */
	public $queried_post;

	/**
	 * ID used for the textarea editor. Defaults to 'editor-content'.
	 *
	 * @var string
	 */
	public $editor_id = 'editor-content';

	/**
	 * URL to this directory. No trailing slash.
	 *
	 * @var string
	 */
	public $url = '';

	/**
	 * Holds all i10n strings.
	 *
	 * @var array
	 */
	public $strings = array();

	/**
	 * Constructor.
	 *
	 * @param array $args
	 */
	final public function __construct( $args = array() ) {
		// stupid PHP 5.2; would rather use late static bindings available in PHP 5.3
		// instead, use reflection so we can access child static properties
		$class = new ReflectionClass( $this );

		$this->args = array_merge( array(
			'post_type'      => $class->getStaticPropertyValue( 'post_type' ),
			'queried_post'   => '',
			'editor_id'      => $this->editor_id,
			'type'           => 'edit',
			'redirect_root'  => '',
		), $args );

		// set some properties
		self::$post_type    = sanitize_title( $this->args['post_type'] );
		$this->queried_post = $this->args['queried_post'];
		$this->editor_id    = $this->args['editor_id'];
		$this->url          = plugins_url( plugin_basename( dirname( __FILE__ ) ) );

		// clean up after ourselves
		unset( $this->args['post_type'], $this->args['queried_post'], $this->args['editor_id'] );

		// whee!
		$this->setup_strings();
		$this->setup_abstraction();

		if ( empty( $this->queried_post ) && false == $this->queried_post instanceof WP_Post ) {
			_doing_it_wrong( $class->getName(), "Please pass a valid post for the 'queried_post' parameter in the constructor", null );
		} else {
			$class = null;
			$this->screen();
		}
	}

	/**
	 * String setter method.
	 *
	 * Extended classes should set their strings in the strings() method.
	 */
	final protected function setup_strings() {
		$this->strings = array_merge( array(
			'created'           => __( 'Post successfully created', 'wp-frontend-admin-screen' ),
			'updated'           => __( 'Post updated', 'wp-frontend-admin-screen' ),
			'title_placeholder' => __( 'Enter title here' ),
			'word_count'        => __( 'Word count: %s' ),
			'last_edited_by'    => __( 'Last edited by %1$s on %2$s at %3$s' ),
			'last_edited_on'    => __( 'Last edited on %1$s at %2$s' ),
			'button_publish'    => __( 'Publish' ),
			'button_update'     => __( 'Update' ),
			'tag_delimiter'     => _x( ',', 'tag delimiter' ),
			'featured_image'    => __( 'Featured Image' ),
			'title_publish'     => __( 'Publish' ),
			'media_insert_into_post' => __( 'Insert into post' ),

			/* translators: If your word count is based on single characters (East Asian characters),
			   enter 'characters'. Otherwise, enter 'words'. Do not translate into your own language. */
			'words'             => _x( 'words', 'word count: words or characters?' )
		), $this->strings() );
	}

	/**
	 * Abstract some core WP admin functionality.
	 */
	final protected function setup_abstraction() {
		// override get_current_screen()
		$GLOBALS['current_screen'] = new WPFAS_Dummy_Screen( self::$post_type );

		// create a new post
		if ( empty( $this->queried_post ) && 'new' === $this->args['type'] ) {
			// try to grab an existing auto-draft
			// not sure why WP doesn't do this...
			$drafts = new WP_Query( array(
				'post_type'        => self::$post_type,
				'post_status'      => 'auto-draft',
				'author'           => get_current_user_id(),
				'suppress_filters' => true
			) );

			// use existing auto-draft
			if ( ! empty( $drafts->posts[0] ) ) {
				$this->queried_post = $drafts->posts[0];
				$drafts = null;

			// create a new draft
			} else {
				// require admin post abstraction functions
				require dirname( __FILE__ ) . '/abstraction-admin-post.php';

				// do something before the draft is inserted
				$this->before_insert( 'draft' );

				$this->queried_post = get_default_post_to_edit( self::$post_type, true );

				// Schedule purging of auto-draft posts
				if ( ! wp_next_scheduled( 'wp_scheduled_auto_draft_delete' ) ) {
					wp_schedule_event( time(), 'daily', 'wp_scheduled_auto_draft_delete' );
				}
			}
		}

	}

	/**
	 * Do something before a post is inserted or saved into the DB.
	 *
	 * @param string $context The type of insert. Either 'draft' or ''. Default: ''.
	 */
	final protected function before_insert( $context = '' ) {
		// WPMU Sitewide Tags interferes with our library due to it including the
		// entire WP admin files causing redeclare fatal errors
		if ( function_exists( 'sitewide_tags_post' ) ) {
			remove_action( 'save_post', 'sitewide_tags_post', 10, 2 );
		}

		// regular save; use class method, handy for extended classes
		if ( 'draft' !== $context ) {
			$this->before_save();
		}
	}

	/**
	 * Screen handler.
	 */
	public function screen() {
		// for extended classes
		$this->before_screen();

		// update
		if ( $_POST ) {
			// require admin post functions
			if ( ! function_exists( 'get_default_post_to_edit' ) ) {
				require ABSPATH . 'wp-admin/includes/post.php';
			}

			// verify!
			check_admin_referer( 'update-post_' . $_POST['post_ID'] );

			// do something before saving
			// extended classes should extend the before_save() method to do stuff
			$this->before_insert();

			// rejig content for saving function
			// this is due to us changing the editor ID to avoid conflicts with themes
			$_POST['content'] = $_POST[$this->editor_id];
			$post_id = edit_post();

			// add BP message
			if ( function_exists( 'bp_core_add_message' ) ) {
				$message = '';
				if ( 'new' === $this->args['type'] && ! empty( $this->strings['created'] ) ) {
					$message = $this->strings['created'];
				} elseif ( 'edit' === $this->args['type'] && ! empty( $this->strings['updated'] ) ) {
					$message = $this->strings['updated'];
				}

				if ( ! empty( $message ) ) {
					bp_core_add_message( $message );
				}
			}

			// redirect
			if ( empty( $this->args['redirect_root'] ) ) {
				$url = add_query_arg(
					array( 'p' => $post_id ),
					get_home_url()
				);
			} else {
				// refetch post to grab slug
				$post = get_post( $post_id );

				$url = trailingslashit( $this->args['redirect_root'] ) . "{$post->post_name}/";
			}

			wp_safe_redirect( $url );
			die();

		// display necessities
		} else {
			// magic metabox abstraction code!
			require dirname( __FILE__ ) . '/abstraction-metabox.php';

			// enqueue editor scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );
			add_action( 'wp_footer',          array( $this, 'inline_js' ), 20 );
		}
	}

	/**
	 * Displays the edit interface.
	 *
	 * Heavily piggybacks off /wp-admin/edit-form-advanced.php.
	 *
	 * Currently, only supports taxonomy metaboxes.  Other metaboxes such as
	 * excerpt and thumbnail might be added later on.
	 */
	public function display() {
		// save $post global temporarily
		global $post;
		$_post = false;
		if ( ! empty( $post ) ) {
			$_post = $post;
		}

		$post        = $this->queried_post;
		$post_type   = get_post_type_object( self::$post_type );
		$can_publish = current_user_can( $post_type->cap->publish_posts );

		if ( 'edit' === $this->args['type'] ) {
			$title = $post_type->labels->edit_item;
		} else {
			$title = $post_type->labels->new_item;
		}

		$_wp_editor_expand = $_content_editor_dfw = false;

		// Do not use 'Auto Draft' as title
		if ( __( 'Auto Draft' ) === $post->post_title ) {
			$post->post_title = '';
		}

		// for extended classes
		$this->before_display();
	?>

		<h2 class="admin-page-title"><?php esc_html_e( $title  ); ?></h2>

		<div id="post-body">
		<form id="post" method="post" action="" name="post">
			<?php wp_nonce_field( 'update-post_' . $post->ID ); ?>
			<input type="hidden" id="post_ID" name="post_ID" value="<?php echo esc_attr( $post->ID ); ?>" />
			<input type="hidden" id="post_author" name="post_author" value="<?php echo esc_attr( empty( $post->post_author ) ? get_current_user_id() : $post->post_author ); ?>" />
			<input type="hidden" id="post_type" name="post_type" value="<?php echo esc_attr( $post->post_type ) ?>" />

			<div id="titlediv">
			<div id="titlewrap">
				<?php
				/**
				 * Filter the title field placeholder text.
				 *
				 * @param string  $text Placeholder text. Default 'Enter title here'.
				 * @param WP_Post $post Post object.
				 */
				$title_placeholder = apply_filters( 'enter_title_here', $this->strings['title_placeholder'], $post );
				?>
				<label class="screen-reader-text" id="title-prompt-text" for="title"><?php esc_html_e( $title_placeholder ); ?></label>
				<input type="text" name="post_title" size="30" value="<?php echo esc_attr( htmlspecialchars( $post->post_title ) ); ?>" id="title" spellcheck="true" autocomplete="off" />
			</div>
			</div>

			<?php do_action( 'edit_form_after_title', $post ); ?>

			<div id="postdivrich" class="postarea<?php if ( $_wp_editor_expand ) { echo ' wp-editor-expand'; } ?>">
				<?php // we have to change the ID element to something other than 'content' to prevent theme conflicts ?>
				<?php wp_editor( $post->post_content, $this->editor_id, array(
					'_content_editor_dfw' => $_content_editor_dfw,
					'drag_drop_upload' => true,
					'tabfocus_elements' => 'content-html,save-post',
					'editor_height' => 300,
					'tinymce' => array(
						'resize' => false,
						'wp_autoresize_on' => $_wp_editor_expand,
						'add_unload_trigger' => false,
					),
				) ); ?>

				<table id="post-status-info"><tbody><tr>
					<td id="wp-word-count"><?php printf( $this->strings['word_count'], '<span class="word-count">0</span>' ); ?></td>
					<td class="autosave-info">
					<span class="autosave-message">&nbsp;</span>
				<?php
					if ( 'auto-draft' != $post->post_status ) {
						echo '<span id="last-edit">';
						if ( $last_user = get_userdata( get_post_meta( $post->ID, '_edit_last', true ) ) ) {
							printf( $this->strings['last_edited_by'], esc_html( $last_user->display_name ), mysql2date( get_option( 'date_format' ), $post->post_modified ), mysql2date( get_option( 'time_format' ), $post->post_modified ) );
						} else {
							printf( $this->strings['last_edited_on'], mysql2date( get_option( 'date_format' ), $post->post_modified ), mysql2date( get_option( 'time_format' ), $post->post_modified ) );
						}
						echo '</span>';
					} ?>
					</td>
					<td id="content-resize-handle" class="hide-if-no-js"><br /></td>
				</tr></tbody></table>

				<?php if ( ! wp_is_mobile() && '' === $post->post_title ) : ?>
				<script type="text/javascript">
					try{document.post.title.focus();}catch(e){}
				</script>
				<?php endif; ?>

			</div>

	<?php
		// metabox time!
		// most of this is duplicated from edit-form-advanced.php

		// featured image metabox
		if ( current_theme_supports( 'post-thumbnails', $post->post_type ) && post_type_supports( $post->post_type, 'thumbnail' ) && current_user_can( 'upload_files' ) ) {
			add_meta_box( 'postimagediv', $this->strings['featured_image'], 'post_thumbnail_meta_box', self::$post_type, 'side', 'low');
		}

		// publish metabox
		add_meta_box( 'submitdiv', $this->strings['title_publish'], 'post_submit_meta_box', self::$post_type, 'side', 'low' );

		// taxonomy metaboxes
		foreach ( get_object_taxonomies( $post ) as $tax_name ) {
			$taxonomy = get_taxonomy( $tax_name );
			if ( ! $taxonomy->show_ui || false === $taxonomy->meta_box_cb ) {
				continue;
			}

			$label = $taxonomy->labels->name;

			if ( ! is_taxonomy_hierarchical( $tax_name ) ) {
				$tax_meta_box_id = 'tagsdiv-' . $tax_name;
			} else {
				$tax_meta_box_id = $tax_name . 'div';
			}

			add_meta_box( $tax_meta_box_id, $label, $taxonomy->meta_box_cb, self::$post_type, 'side', 'core', array( 'taxonomy' => $tax_name ) );
		}

		// plugin metabox registration
		do_action( 'add_meta_boxes', self::$post_type, $post );
		do_action( 'add_meta_boxes_' . self::$post_type, $post );
		do_action( 'do_meta_boxes', self::$post_type, 'normal', $post );
		do_action( 'do_meta_boxes', self::$post_type, 'side', $post );

		// render metaboxes
		do_meta_boxes( self::$post_type, 'normal', $post );
		do_meta_boxes( self::$post_type, 'side', $post );

		// revert $post global
		if ( ! empty( $_post ) ) {
			$post = $_post;
		}

		// submit button
	?>

			<div id="publishing-action">
			<span class="spinner"></span>
			<?php
			if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
				if ( $can_publish ) :
					if ( !empty($post->post_date_gmt) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) : ?>
					<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Schedule') ?>" />
					<?php submit_button( __( 'Schedule' ), 'primary button-large', 'publish', false ); ?>
			<?php	else : ?>
					<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Publish') ?>" />
					<?php submit_button( __( 'Publish' ), 'primary button-large', 'publish', false ); ?>
			<?php	endif;
				else : ?>
					<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Submit for Review') ?>" />
					<?php submit_button( __( 'Submit for Review' ), 'primary button-large', 'publish', false ); ?>
			<?php
				endif;
			} else { ?>
					<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update') ?>" />
					<input name="save" type="submit" class="button button-primary button-large" id="publish" value="<?php esc_attr_e( 'Update' ) ?>" />
			<?php
			} ?>
			</div>

		</form>
		</div>

	<?php
	}

	/**
	 * Enqueues editor scripts and styles for the frontend.
	 */
	public function enqueue_editor_scripts() {
		// save $post global temporarily
		global $post, $wp_version;
		$_post = false;
		if ( ! empty( $post ) ) {
			$_post = $post;
		}

		// override the $post global so EO can use its functions
		$post      = $this->queried_post;
		$post_type = $this->queried_post->post_type;

		// frontend requires manually enqueuing some scripts due to is_admin() check
		// @see wp_default_scripts()
		$suffix = SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'word-count', admin_url( "js/word-count$suffix.js" ), array( 'jquery' ), false, 1 );
		wp_enqueue_script( 'postbox', admin_url( "js/postbox$suffix.js" ), array('jquery-ui-sortable'), false, 1 );
		wp_enqueue_script( 'post', admin_url( "js/post$suffix.js" ), array( 'suggest', 'wp-lists', 'postbox' ), false, 1 );
		wp_enqueue_script( 'link', admin_url( "js/link$suffix.js" ), array( 'wp-lists', 'postbox' ), false, 1 );
		//wp_enqueue_script( 'autosave' );

		// yay, different WP versions!
		if ( version_compare( $wp_version, '4.1.999') >= 0 ) {
			wp_enqueue_script( 'tags-box', admin_url( "js/tags-box$suffix.js" ), array( 'jquery', 'suggest' ), false, 1 );
		}

		if ( wp_is_mobile() ) {
			wp_enqueue_script( 'jquery-touch-punch' );
		}

		// post thumbnail requires a few assets
		if ( current_theme_supports( 'post-thumbnails', $post_type ) && post_type_supports( $post_type, 'thumbnail' ) ) {
			add_thickbox();
			add_filter( 'media_view_strings', array( $this, 'media_view_strings' ) );
			wp_enqueue_media( array( 'post' => $post->ID ) );
			remove_filter( 'media_view_strings', array( $this, 'media_view_strings' ) );
		}

		// localization
		wp_localize_script( 'tags-box', 'tagsBoxL10n', array(
			'tagDelimiter' => $this->strings['tag_delimiter'],
		) );
		wp_localize_script( 'word-count', 'wordCountL10n', array(
			'type' => 'characters' == $this->strings( 'words' ) ? 'c' : 'w',
		) );
		// might not be needed... keeping for now
		wp_localize_script( 'post', 'postL10n', array(
			'ok' => __('OK'),
			'cancel' => __('Cancel'),
			'publishOn' => __('Publish on:'),
			'publishOnFuture' =>  __('Schedule for:'),
			'publishOnPast' => __('Published on:'),
			/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
			'dateFormat' => __('%1$s %2$s, %3$s @ %4$s : %5$s'),
			'showcomm' => __('Show more comments'),
			'endcomm' => __('No more comments found.'),
			'publish' => __('Publish'),
			'schedule' => __('Schedule'),
			'update' => __('Update'),
			'savePending' => __('Save as Pending'),
			'saveDraft' => __('Save Draft'),
			'private' => __('Private'),
			'public' => __('Public'),
			'publicSticky' => __('Public, Sticky'),
			'password' => __('Password Protected'),
			'privatelyPublished' => __('Privately Published'),
			'published' => __('Published'),
			'saveAlert' => __('The changes you made will be lost if you navigate away from this page.'),
			'savingText' => __('Saving Draft&#8230;'),
			'comma' => $this->strings['tag_delimiter'],
		) );

		// editor-specific styles
		wp_enqueue_style( 'wp-frontend-admin-screen-edit', admin_url( 'css/edit.css' ) );
		wp_enqueue_style( 'wp-frontend-admin-screen', $this->url . '/frontend.css' );

		// for extended classes
		$this->enqueue_scripts();

		// revert $post global
		if ( ! empty( $_post ) ) {
			$post = $_post;
		}

		// set the 'pagenow' JS variable to emulate wp-admin area
		// @see /wp-admin/admin-header.php
	?>

<script type="text/javascript">
	var pagenow = '<?php echo self::$post_type; ?>',
		typenow = '<?php echo self::$post_type; ?>';
</script>

	<?php
	}

	/**
	 * Fix various wp-admin JS looking for the wrong element.
	 *
	 * Currently supports word count on load.
	 */
	public function inline_js() {
	?>

	<script type="text/javascript">
	( function( $, window ) {
		if ( typeof(wpWordCount) != 'undefined' ) {
			$(document).trigger('wpcountwords', [ $('.wp-editor-area').val() ]);
		}
	}( jQuery, window ));
	</script>

	<?php
	}

	/**
	 * Override strings used in {@link wp_enqueue_media()}.
	 *
	 * @param  array $retval Current strings
	 * @return array
	 */
	public function media_view_strings( $retval ) {
		$retval['insertIntoPost'] = $this->strings['media_insert_into_post'];
		return $retval;
	}

	/** EXTENDABLE METHODS *************************************************/

	/**
	 * Set your strings in this class method.
	 *
	 * @see WP_Frontend_Admin_Screen::setup_strings() Keys of strings you can set.
	 *
	 * @return array
	 */
	protected function strings() {
		return array();
	}

	/**
	 * Override to do some logic here if needed.
	 *
	 * This runs inside the screen() method.  Handy if you need to include files.
	 */
	protected function before_screen() {}

	/**
	 * Override to do some logic here if needed.
	 *
	 * This runs inside the save portion of the screen() method.  Handy if you
	 * need to register some actions before saving.
	 */
	protected function before_save() {}

	/**
	 * Override to do some logic here if needed.
	 *
	 * This runs in the display() method.
	 */
	protected function before_display() {}

	/**
	 * Override to do some logic here if needed.
	 *
	 * This runs during the enqueue_editor_scripts() method.  Handy if you need to
	 * re-enqueue any scripts or styles for the frontend.
	 */
	protected function enqueue_scripts() {}
}

/**
 * Dummy class to override {@link get_current_screen()}.
 */
class WPFAS_Dummy_Screen {
	/**
	 * Constructor.
	 */
	public function __construct( $post_type = '' ) {
		$this->id   = $post_type;
		$this->base = 'post';
	}

	/**
	 * Bye bye, fatal error!
	 */
	public function in_admin() {}
}
endif;
