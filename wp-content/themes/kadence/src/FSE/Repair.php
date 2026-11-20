<?php
/**
 * Kadence\FSE\Repair class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use WP_Query;
use function Kadence\kadence;

/**
 * The checklist of what to rebuild in the Site Editor after Full Site Editing mode turns on.
 *
 * The header, footer and archives switch to block templates, so they're always listed, and
 * so is the blog page when the site has one. Each item notes what the classic site had there
 * that the block template doesn't. The list is built when the mode turns on, from the
 * settings as they are then, and stores codes, so its text follows each viewer's language.
 *
 * @since 1.6.0
 */
final class Repair {

	/**
	 * Option that stores the checklist.
	 *
	 * @since 1.6.0
	 */
	private const OPTION = 'kadence_fse_repair';

	/**
	 * Option that remembers whether a post turned the transparent header on for itself, as 'yes' or 'no'.
	 *
	 * @since 1.6.0
	 */
	private const TRANSPARENT_OPTION = 'kadence_fse_transparent_posts';

	/**
	 * Post meta that turns the transparent header on for one post.
	 *
	 * @since 1.6.0
	 */
	private const TRANSPARENT_META = '_kad_post_transparent';

	/**
	 * Option that marks the checklist finished.
	 *
	 * @since 1.6.0
	 */
	private const COMPLETED_OPTION = 'kadence_fse_repair_completed';

	/**
	 * Item types: a template or template part to open in the Site Editor, or a notice without one.
	 *
	 * @since 1.6.0
	 */
	private const TYPES = [ 'wp_template', 'wp_template_part', 'notice' ];

	/**
	 * Header builder items the block header has: the logo and the menu, and their overlay versions.
	 *
	 * @since 1.6.0
	 */
	private const HEADER_ITEMS = [ 'logo', 'navigation', 'mobile-logo', 'popup-toggle', 'mobile-navigation' ];

	/**
	 * Header builder items that are the WooCommerce cart, which the block header doesn't have.
	 *
	 * @since 1.6.0
	 */
	private const CART_ITEMS = [ 'cart', 'mobile-cart' ];

	/**
	 * Settings groups whose breadcrumb the block templates don't show, and the post type each needs.
	 *
	 * An empty post type is always registered.
	 *
	 * @since 1.6.0
	 */
	private const BREADCRUMB_GROUPS = [
		'page'            => 'page',
		'post'            => 'post',
		'post_archive'    => '',
		'product'         => 'product',
		'product_archive' => 'product',
		'ht_kb'           => 'ht_kb',
	];

	/**
	 * Notes that apply to every site, as notice slugs. They have no link, since there is nowhere to go.
	 *
	 * @since 1.6.0
	 */
	private const SITE_NOTES = [ 'note_placement', 'note_color_round_trip', 'note_width_round_trip', 'note_import_export', 'note_reset' ];

	/**
	 * The note for a site that had Optimized Group Block off, which FSE mode turns on.
	 *
	 * @since 1.6.0
	 */
	private const GROUP_NOTE = 'note_group_layout';

	/**
	 * The note for a site with published Kadence Pro Elements, which no block template runs.
	 *
	 * @since 1.6.0
	 */
	private const ELEMENTS_NOTE = 'note_elements';

	/**
	 * Footer builder items the block footer covers: the copyright text, and the widget areas, which are checked on their own.
	 *
	 * @since 1.6.0
	 */
	private const FOOTER_ITEMS = [ 'footer-html', 'footer-widget1', 'footer-widget2', 'footer-widget3', 'footer-widget4', 'footer-widget5', 'footer-widget6' ];

	/**
	 * Post archive settings the block archive and blog templates don't follow.
	 *
	 * @since 1.6.0
	 */
	private const ARCHIVE_SETTINGS = [
		'post_archive_title_layout',
		'post_archive_title_element_breadcrumb',
		'post_archive_title_element_description',
		'post_archive_layout',
		'post_archive_content_style',
		'post_archive_columns',
		'post_archive_item_image_placement',
		'post_archive_elements',
		'post_archive_element_feature',
		'post_archive_element_categories',
		'post_archive_element_title',
		'post_archive_element_meta',
		'post_archive_element_excerpt',
		'post_archive_element_readmore',
	];

	/**
	 * Kadence Pro Element placements in each part of the site, which the block templates never run.
	 *
	 * The values are what Pro stores, including its fixed placements, which print on the classic
	 * header, footer and wrapper hooks.
	 *
	 * @since 1.6.0
	 */
	private const ELEMENT_HOOKS = [
		'header'  => [
			'replace_header',
			'kadence_before_header',
			'kadence_after_header',
			'fixed_above_header',
			'fixed_above_trans_header',
			'fixed_on_header',
			'kadence_before_mobile_navigation_popup',
			'kadence_after_mobile_navigation_popup',
			'kadence_before_account_login_popup',
			'kadence_before_account_login_inner_popup',
			'kadence_after_account_login_inner_popup',
			'kadence_after_account_login_popup',
			'replace_login_modal',
		],
		'footer'  => [
			'replace_footer',
			'kadence_before_footer',
			'kadence_after_footer',
			'fixed_below_footer',
			'fixed_on_footer',
			'fixed_on_footer_scroll',
			'fixed_on_footer_scroll_space',
		],
		'archive' => [
			'replace_hero_header',
			'replace_archive_content',
			'replace_loop_content',
			'kadence_entry_archive_hero',
			'kadence_archive_before_entry_header',
			'kadence_archive_after_entry_header',
		],
	];

	/**
	 * Classic template parts that build an archive or the blog, through the kadence_archive hook.
	 *
	 * @since 1.6.0
	 */
	private const ARCHIVE_PARTS = [
		'template-parts/archive-title/*.php',
		'template-parts/content/archive*.php',
		'template-parts/content/entry.php',
		'template-parts/content/entry_loop_*.php',
		'template-parts/content/entry_summary.php',
		'template-parts/content/pagination.php',
	];

	/**
	 * Classic templates and template parts a child theme can replace, which the block templates never load.
	 *
	 * @since 1.6.0
	 */
	private const CHILD_TEMPLATES = [
		'header'     => [ 'header.php', 'template-parts/header/*.php' ],
		'footer'     => [ 'footer.php', 'template-parts/footer/*.php' ],
		'archive'    => [ 'archive.php', ...self::ARCHIVE_PARTS ],
		'front-page' => [ 'front-page.php', 'home.php', 'index.php', ...self::ARCHIVE_PARTS ],
		'home'       => [ 'home.php', 'index.php', ...self::ARCHIVE_PARTS ],
		'index'      => [ 'home.php', 'index.php', ...self::ARCHIVE_PARTS ],
	];

	/**
	 * Full Site Editing mode.
	 *
	 * @since 1.6.0
	 *
	 * @var Mode
	 */
	private Mode $mode;

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param Mode $mode Full Site Editing mode.
	 */
	public function __construct( Mode $mode ) {
		$this->mode = $mode;
	}

	/**
	 * Builds the checklist from the site's settings.
	 *
	 * @since 1.6.0
	 */
	public function build_checklist() {
		$archive    = $this->archive_details();
		$placements = $this->get_element_placements();
		$items      = [
			$this->item( 'wp_template_part', 'header', array_merge( $this->header_details(), $this->shared_details( 'header', 'header', $placements ) ) ),
			$this->item( 'wp_template_part', 'footer', array_merge( $this->footer_details(), $this->shared_details( 'footer', 'footer', $placements ) ) ),
			$this->item( 'wp_template', 'archive', array_merge( $archive, $this->shared_details( 'archive', 'archive', $placements ) ) ),
		];

		if ( 'posts' === get_option( 'show_on_front' ) || get_option( 'page_for_posts' ) ) {
			$blog    = $this->get_blog_template();
			$items[] = $this->item( 'wp_template', $blog, array_merge( $archive, $this->shared_details( $blog, 'archive', $placements ) ) );
		}

		foreach ( $this->get_disclosures()['notices'] as $notice ) {
			$items[] = [
				'type'     => 'notice',
				'slug'     => $notice,
				'required' => false,
				// The Group block note is a review step the site owner marks done. The rest need no action.
				'status'   => self::GROUP_NOTE === $notice ? 'todo' : 'info',
				'details'  => [],
			];
		}

		/**
		 * Filters the checklist of what to rebuild after Full Site Editing mode turns on.
		 *
		 * Each item has a type (wp_template, wp_template_part or notice), a slug, a required flag,
		 * a status (todo or done) and detail codes. A template item is done once the site has saved its
		 * template, and the site owner can mark any item done or to do. Store a notice that needs no
		 * action as info. Only a required item left to do keeps the checklist from finishing. Store codes rather
		 * than sentences, and add the text with the kadence_fse_repair_item filter, so it's translated
		 * for each viewer.
		 *
		 * @since 1.6.0
		 *
		 * @param array $items Checklist items.
		 */
		$items = apply_filters( 'kadence_fse_repair_items', $items );

		update_option( self::OPTION, array_values( (array) $items ), false );

		// A new checklist can finish again. Cleared after the new list is stored, so a read in between can't mark it finished.
		delete_option( self::COMPLETED_OPTION );
	}

	/**
	 * Marks a checklist item done or to do, as the site owner chose, which a saved template no longer changes.
	 *
	 * @since 1.6.0
	 *
	 * @param string $type Item type.
	 * @param string $slug Item slug.
	 * @param bool   $done Whether it's done.
	 * @return bool True when the item is on the checklist and has that status after the call.
	 */
	public function set_status( string $type, string $slug, bool $done ): bool {
		$stored = get_option( self::OPTION );

		if ( ! $this->mode->is_enabled() || ! is_array( $stored ) ) {
			return false;
		}

		foreach ( $stored as $key => $item ) {
			if ( ! $this->is_item( $item ) || $type !== $item['type'] || $slug !== $item['slug'] ) {
				continue;
			}

			// An information-only note has no status to set.
			if ( 'info' === ( $item['status'] ?? '' ) ) {
				return false;
			}

			$changed = array_merge(
				$item,
				[
					'status' => $done ? 'done' : 'todo',
					'manual' => true,
				]
			);

			// Core reports an unchanged value as a failed write.
			if ( $changed === $item ) {
				return true;
			}

			$stored[ $key ] = $changed;

			if ( ! update_option( self::OPTION, $stored, false ) ) {
				return false;
			}

			// Records the checklist finished, or clears that once an item is to do again.
			$this->get_checklist();

			return true;
		}

		return false;
	}

	/**
	 * The checklist as the dashboard shows it, empty while the mode is off.
	 *
	 * A template item is done once the site has saved its own copy of the template, and stays done
	 * if the template is reset. The site owner can mark any item done or to do, and their choice
	 * holds. Each read records whether every item is done. A site that turned the mode on before the
	 * checklist existed gets it built now.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	public function get_checklist(): array {
		if ( ! $this->mode->is_enabled() ) {
			return [];
		}

		if ( false === get_option( self::OPTION ) ) {
			$this->build_checklist();
		}

		$stored = (array) get_option( self::OPTION, [] );
		$items  = array_filter( array_filter( $stored, [ $this, 'is_item' ] ), [ $this, 'is_listed' ] );
		$saved  = $this->get_saved_copies( $items );
		$marked = false;

		// A saved template marks its item done, unless the site owner set the item. It's stored, so a reset doesn't undo it.
		foreach ( $items as $key => $item ) {
			$is_saved = isset( $saved[ $item['type'] . ':' . $item['slug'] ] );

			if ( $is_saved && empty( $item['manual'] ) && 'done' !== ( $item['status'] ?? '' ) ) {
				$items[ $key ]['status']  = 'done';
				$stored[ $key ]['status'] = 'done';
				$marked                   = true;
			}
		}

		if ( $marked ) {
			update_option( self::OPTION, $stored, false );
		}

		$checklist = [];

		foreach ( $items as $item ) {
			$entry = [
				'type'        => $item['type'],
				'slug'        => $item['slug'],
				'required'    => ! empty( $item['required'] ),
				'status'      => in_array( $item['status'] ?? '', [ 'done', 'info' ], true ) ? $item['status'] : 'todo',
				'label'       => $this->get_label( $item['slug'] ),
				'description' => $this->get_description( $item['slug'] ),
				'details'     => $this->get_detail_messages( $item ),
				'url'         => 'notice' === $item['type'] ? '' : $this->get_edit_url( $item['type'], $item['slug'] ),
			];

			/**
			 * Filters a checklist item as the dashboard shows it, so a plugin can add its item's
			 * label, description and detail sentences in the viewer's language.
			 *
			 * @since 1.6.0
			 *
			 * @param array $entry Item with type, slug, required, status, label, description, details and url.
			 * @param array $item  Item as stored, with its detail codes.
			 */
			$entry = apply_filters( 'kadence_fse_repair_item', $entry, $item );

			$checklist[] = [
				'type'        => $entry['type'],
				'slug'        => $entry['slug'],
				'required'    => (bool) ( $entry['required'] ?? false ),
				'status'      => in_array( $entry['status'] ?? '', [ 'done', 'info' ], true ) ? $entry['status'] : 'todo',
				'label'       => is_string( $entry['label'] ?? null ) ? $entry['label'] : $item['slug'],
				'description' => is_string( $entry['description'] ?? null ) ? $entry['description'] : '',
				'details'     => array_values( array_unique( array_filter( (array) ( $entry['details'] ?? [] ), 'is_string' ) ) ),
				'url'         => is_string( $entry['url'] ?? null ) ? $entry['url'] : '',
			];
		}

		if ( $this->is_complete( $checklist ) ) {
			$this->record_completion( array_values( $items ) );
		} elseif ( false !== get_option( self::COMPLETED_OPTION ) ) {
			// An item is to do again, so finishing again is announced again.
			delete_option( self::COMPLETED_OPTION );
		}

		return $checklist;
	}

	/**
	 * Whether a checklist has items and every required one is done. A note to review doesn't hold it back.
	 *
	 * @since 1.6.0
	 *
	 * @param array $checklist Checklist from get_checklist().
	 * @return bool
	 */
	public function is_complete( array $checklist ): bool {
		if ( [] === $checklist ) {
			return false;
		}

		foreach ( $checklist as $item ) {
			if ( ! empty( $item['required'] ) && 'todo' === ( $item['status'] ?? '' ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Whether a stored item has a slug and a known type.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $item Item as stored.
	 * @return bool
	 */
	private function is_item( $item ): bool {
		return is_array( $item )
			&& in_array( $item['type'] ?? '', self::TYPES, true )
			&& is_string( $item['slug'] ?? null )
			&& '' !== $item['slug'];
	}

	/**
	 * Whether a stored item still belongs on the checklist. Every known issue has shipped, so a stored one doesn't.
	 *
	 * @since 1.6.0
	 *
	 * @param array $item Item as stored.
	 * @return bool
	 */
	private function is_listed( array $item ): bool {
		return 'notice' !== $item['type'] || 0 !== strpos( $item['slug'], 'known_' );
	}

	/**
	 * Marks the checklist finished and announces it, once until an item is to do again.
	 *
	 * @since 1.6.0
	 *
	 * @param array $items Checklist items as stored, with their statuses.
	 */
	private function record_completion( array $items ): void {
		// Adding the same value again changes no row, so only one of two requests finishing at once gets true.
		if ( ! add_option( self::COMPLETED_OPTION, 1, '', false ) ) {
			return;
		}

		/**
		 * Fires when every item on the repair checklist is done.
		 *
		 * It fires again only if an item is marked to do and the checklist is finished again, and
		 * turning the mode on again starts a new checklist. It fires while the dashboard reads the
		 * checklist or marks an item, so a listener should be quick or queue its work.
		 *
		 * @since 1.6.0
		 *
		 * @param array $items Checklist items as stored, with their statuses.
		 */
		do_action( 'kadence_fse_repair_completed', $items );
	}

	/**
	 * A checklist item for one of the theme's templates.
	 *
	 * @since 1.6.0
	 *
	 * @param string   $type    wp_template or wp_template_part.
	 * @param string   $slug    Template slug.
	 * @param string[] $details Codes for what the classic site had that the block template doesn't.
	 * @return array
	 */
	private function item( string $type, string $slug, array $details ): array {
		return [
			'type'     => $type,
			'slug'     => $slug,
			'required' => true,
			'status'   => 'todo',
			'details'  => $details,
		];
	}

	/**
	 * What the classic header had that the block header doesn't.
	 *
	 * @since 1.6.0
	 *
	 * @return string[]
	 */
	private function header_details(): array {
		// The Kadence Header block replaces the header builder, as the classic header does.
		if ( $this->has_block_header() ) {
			$details = [ 'block_header' ];
		} else {
			$details = array_diff( $this->get_header_items(), self::HEADER_ITEMS, self::CART_ITEMS ) ? [ 'header_items' ] : [];
		}

		if ( $this->has_conditional_headers() ) {
			$details[] = 'conditional_header';
		}

		return array_merge( $details, $this->get_disclosures()['header'] );
	}

	/**
	 * What the site uses that Full Site Editing mode doesn't carry over, from its settings as they are now.
	 *
	 * Writes nothing, so it works before the mode is on: the opt-in dialog lists these, and the
	 * checklist stores them. Keys are the checklist items the details belong to, and notices.
	 *
	 * @since 1.6.0
	 *
	 * @return array{header: string[], archive: string[], notices: string[]}
	 */
	public function get_disclosures(): array {
		$header = [];

		// The Kadence Header block replaces the builder, so its cart isn't the one the site shows.
		if ( ! $this->has_block_header() && array_intersect( $this->get_header_items(), self::CART_ITEMS ) ) {
			$header[] = 'header_cart';
		}

		if ( 'no' !== kadence()->option( 'header_sticky' ) || 'no' !== kadence()->option( 'mobile_header_sticky' ) ) {
			$header[] = 'sticky_header';
		}

		if ( kadence()->option( 'transparent_header_enable' ) || $this->has_transparent_posts() ) {
			$header[] = 'transparent_header';
		}

		// The same read disable_json_optionally() makes, so a never-saved setting counts as off. FSE mode turns theme.json on without changing the stored value.
		$notices = get_theme_mod( 'theme_json_mode', false ) ? [] : [ self::GROUP_NOTE ];

		if ( $this->get_element_placements() ) {
			$notices[] = self::ELEMENTS_NOTE;
		}

		return [
			'header'  => $header,
			'archive' => $this->has_breadcrumbs() ? [ 'breadcrumbs' ] : [],
			'notices' => array_merge( $notices, self::SITE_NOTES ),
		];
	}

	/**
	 * What the opt-in dialog lists, as short sentences in the viewer's language.
	 *
	 * The same detection the checklist stores, so the two lists can't drift. The dialog keeps each line
	 * short and folds the features that don't apply into one line; the checklist carries the full wording.
	 *
	 * @since 1.6.0
	 *
	 * @return string[]
	 */
	public function get_disclosure_messages(): array {
		$disclosures = $this->get_disclosures();
		$features    = array_map( [ $this, 'get_feature_name' ], array_merge( $disclosures['header'], $disclosures['archive'] ) );
		$messages    = [];

		if ( $features ) {
			/* translators: %l: List of features, such as "sticky header, transparent header and breadcrumbs". */
			$messages[] = wp_sprintf( __( 'Not available in Full Site Editing mode: %l.', 'kadence' ), $features );
		}

		foreach ( $disclosures['notices'] as $notice ) {
			$messages[] = $this->get_short_note( $notice );
		}

		return array_values( array_unique( array_filter( $messages ) ) );
	}

	/**
	 * Whether the Kadence Header block replaces the header builder.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	private function has_block_header(): bool {
		return (bool) kadence()->option( 'blocks_header' ) && defined( 'KADENCE_BLOCKS_VERSION' ) && kadence()->option( 'blocks_header_id' );
	}

	/**
	 * Every item in the header builder, desktop and mobile.
	 *
	 * @since 1.6.0
	 *
	 * @return string[]
	 */
	private function get_header_items(): array {
		return array_merge( $this->flatten( (array) kadence()->option( 'header_desktop_items' ) ), $this->flatten( (array) kadence()->option( 'header_mobile_items' ) ) );
	}

	/**
	 * Whether a post or page turns the transparent header on for itself.
	 *
	 * Reads a flag that track_transparent_post() keeps, since the meta value isn't indexed and
	 * looking for it scans the site's postmeta. A site without the flag is looked at once, with
	 * a query for a single ID, and the answer is stored.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	private function has_transparent_posts(): bool {
		$flag = get_option( self::TRANSPARENT_OPTION );

		if ( ! in_array( $flag, [ 'yes', 'no' ], true ) ) {
			$posts = get_posts(
				[
					'post_type'              => 'any',
					'post_status'            => 'publish',
					'numberposts'            => 1,
					'fields'                 => 'ids',
					'no_found_rows'          => true,
					'update_post_term_cache' => false,
					'meta_key'               => self::TRANSPARENT_META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- one ID, once per site.
					'meta_value'             => 'enable', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- as above.
				]
			);
			$flag  = empty( $posts ) ? 'no' : 'yes';

			update_option( self::TRANSPARENT_OPTION, $flag );
		}

		return 'yes' === $flag;
	}

	/**
	 * Remembers that a post turned the transparent header on for itself.
	 *
	 * Hooked to added_post_meta and updated_post_meta. The flag never goes back to 'no' when a
	 * post is unpublished or changed: a stale yes only adds a line to the dialog and the checklist.
	 *
	 * @since 1.6.0
	 *
	 * @param int    $meta_id    Meta row ID, unused.
	 * @param int    $object_id  Post ID, unused.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 */
	public function track_transparent_post( $meta_id, $object_id, $meta_key, $meta_value ): void {
		if ( self::TRANSPARENT_META === $meta_key && 'enable' === $meta_value ) {
			update_option( self::TRANSPARENT_OPTION, 'yes' );
		}
	}

	/**
	 * Remembers a post that turned the transparent header on while it was a draft, once it's published.
	 *
	 * Hooked to transition_post_status, since publishing doesn't touch the meta.
	 *
	 * @since 1.6.0
	 *
	 * @param string   $new_status New post status.
	 * @param string   $old_status Old post status.
	 * @param \WP_Post $post       Post being published.
	 */
	public function track_published_post( $new_status, $old_status, $post ): void {
		if ( 'publish' === $new_status && 'publish' !== $old_status && 'enable' === get_post_meta( $post->ID, self::TRANSPARENT_META, true ) ) {
			update_option( self::TRANSPARENT_OPTION, 'yes' );
		}
	}

	/**
	 * Whether a breadcrumb is on in a title that shows it, for a post type the site has.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	private function has_breadcrumbs(): bool {
		foreach ( self::BREADCRUMB_GROUPS as $group => $post_type ) {
			if ( '' !== $post_type && ! post_type_exists( $post_type ) ) {
				continue;
			}

			$breadcrumb = kadence()->option( $group . '_title_element_breadcrumb' );

			if ( ! empty( $breadcrumb['enabled'] ) && in_array( 'breadcrumb', (array) kadence()->option( $group . '_title_elements' ), true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * What the classic footer had that the block footer doesn't.
	 *
	 * @since 1.6.0
	 *
	 * @return string[]
	 */
	private function footer_details(): array {
		$items   = $this->get_footer_items();
		$details = [];

		foreach ( range( 1, 6 ) as $area ) {
			if ( in_array( 'footer-widget' . $area, $items, true ) && is_active_sidebar( 'footer' . $area ) ) {
				$details[] = 'footer_widgets';
				break;
			}
		}

		if ( array_diff( $items, self::FOOTER_ITEMS ) ) {
			$details[] = 'footer_items';
		}

		if ( in_array( 'footer-html', $items, true ) && kadence()->option( 'footer_html_content' ) !== kadence()->default( 'footer_html_content' ) ) {
			$details[] = 'footer_html';
		}

		return $details;
	}

	/**
	 * What the classic post archives had that the block archive and blog templates don't.
	 *
	 * @since 1.6.0
	 *
	 * @return string[]
	 */
	private function archive_details(): array {
		foreach ( self::ARCHIVE_SETTINGS as $setting ) {
			$default = kadence()->default( $setting );
			$value   = kadence()->option( $setting );

			// A setting saved before the default gained keys only differs where it was changed. Lists compare as they are.
			if ( is_array( $default ) && is_array( $value ) && ! wp_is_numeric_array( $default ) ) {
				$value = array_replace( $default, $value );
			}

			// Loose, since a saved setting can hold '1' where the default is true.
			if ( $value != $default ) { // phpcs:ignore Universal.Operators.StrictComparisons.LooseNotEqual
				return array_merge( [ 'archive_layout' ], $this->get_disclosures()['archive'] );
			}
		}

		return $this->get_disclosures()['archive'];
	}

	/**
	 * Kadence Pro Elements and child theme templates that the block template doesn't run.
	 *
	 * @since 1.6.0
	 *
	 * @param string   $slug       Checklist item slug.
	 * @param string   $elements   Part of the site whose Element placements apply.
	 * @param string[] $placements Placements of the site's published Elements.
	 * @return string[]
	 */
	private function shared_details( string $slug, string $elements, array $placements ): array {
		$details = [];

		if ( array_intersect( self::ELEMENT_HOOKS[ $elements ], $placements ) ) {
			$details[] = 'element';
		}

		if ( $this->get_child_template( $slug ) ) {
			$details[] = 'child_template';
		}

		return $details;
	}

	/**
	 * Every item in the footer builder's columns that the classic footer shows.
	 *
	 * A row only shows as many columns as it's set to, so items left in the others never rendered.
	 *
	 * @since 1.6.0
	 *
	 * @return string[]
	 */
	private function get_footer_items(): array {
		$items = [];

		foreach ( (array) kadence()->option( 'footer_items' ) as $row => $columns ) {
			$count = absint( kadence()->option( 'footer_' . $row . '_columns' ) );

			foreach ( (array) $columns as $column => $column_items ) {
				if ( absint( substr( strrchr( (string) $column, '_' ), 1 ) ) <= $count ) {
					$items = array_merge( $items, $this->flatten( (array) $column_items ) );
				}
			}
		}

		return $items;
	}

	/**
	 * The strings in a builder's nested rows and zones.
	 *
	 * @since 1.6.0
	 *
	 * @param array $value Builder rows, zones or items.
	 * @return string[]
	 */
	private function flatten( array $value ): array {
		$items = [];

		foreach ( $value as $entry ) {
			if ( is_array( $entry ) ) {
				$items = array_merge( $items, $this->flatten( $entry ) );
			} elseif ( is_string( $entry ) && '' !== $entry ) {
				$items[] = $entry;
			}
		}

		return $items;
	}

	/**
	 * Whether Kadence Pro shows a different header on some pages.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	private function has_conditional_headers(): bool {
		$modules = json_decode( (string) get_option( 'kadence_pro_theme_config' ), true );

		if ( empty( $modules['conditional_headers'] ) ) {
			return false;
		}

		$headers = kadence()->option( 'conditional_headers' );

		foreach ( (array) ( $headers['items'] ?? [] ) as $header ) {
			if ( ! empty( $header['enabled'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * The placements of the site's published Kadence Pro Elements.
	 *
	 * Reads them once per build, with the same cap and order Kadence Pro uses when it loads Elements.
	 *
	 * @since 1.6.0
	 *
	 * @return string[]
	 */
	private function get_element_placements(): array {
		if ( ! post_type_exists( 'kadence_element' ) ) {
			return [];
		}

		$elements = get_posts(
			[
				'post_type'              => 'kadence_element',
				'post_status'            => 'publish',
				'numberposts'            => 533, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_numberposts -- Kadence Pro's own cap, and it loads them on every page.
				'orderby'                => 'menu_order',
				'order'                  => 'ASC',
				'fields'                 => 'ids',
				'update_post_term_cache' => false,
			]
		);

		// Loads the Elements' meta in one query, rather than one per Element.
		update_meta_cache( 'post', $elements );

		$placements = [];

		foreach ( $elements as $element ) {
			$placements[] = (string) get_post_meta( $element, '_kad_element_hook', true );
		}

		return array_values( array_unique( array_filter( $placements ) ) );
	}

	/**
	 * The child theme's own classic template or template part for an item, if it has one.
	 *
	 * @since 1.6.0
	 *
	 * @param string $slug Checklist item slug.
	 * @return string Path inside the child theme, or an empty string.
	 */
	private function get_child_template( string $slug ): string {
		if ( ! is_child_theme() ) {
			return '';
		}

		$directory = get_stylesheet_directory();

		foreach ( self::CHILD_TEMPLATES[ $slug ] ?? [] as $pattern ) {
			$files = glob( $directory . '/' . $pattern );

			if ( $files ) {
				return substr( $files[0], strlen( $directory ) + 1 );
			}
		}

		return '';
	}

	/**
	 * The template the blog page renders.
	 *
	 * Latest posts on the front page use front-page, then home. A separate posts page uses
	 * home. Each can be a theme file or a template saved in the Site Editor. Both fall back to
	 * index, since the theme ships neither.
	 *
	 * @since 1.6.0
	 *
	 * @return string Template slug.
	 */
	private function get_blog_template(): string {
		$candidates = 'posts' === get_option( 'show_on_front' ) ? [ 'front-page', 'home' ] : [ 'home' ];
		$templates  = [];

		foreach ( $candidates as $slug ) {
			$templates[] = [
				'type' => 'wp_template',
				'slug' => $slug,
			];
		}

		// A template saved in the Site Editor counts too, since WordPress renders it without a file.
		$saved = $this->get_saved_copies( $templates );

		foreach ( $candidates as $slug ) {
			if ( isset( $saved[ 'wp_template:' . $slug ] ) ) {
				return $slug;
			}

			foreach ( array_unique( [ get_stylesheet_directory(), get_template_directory() ] ) as $directory ) {
				if ( file_exists( $directory . '/fse/site-templates/' . $slug . '.html' ) ) {
					return $slug;
				}
			}
		}

		return 'index';
	}

	/**
	 * The templates in the checklist the site saved its own copy of in the Site Editor, in one query.
	 *
	 * Like the theme's template loader, an empty copy doesn't count.
	 *
	 * @since 1.6.0
	 *
	 * @param array $items Checklist items.
	 * @return array<string,int> Post IDs, keyed by type and slug.
	 */
	private function get_saved_copies( array $items ): array {
		$slugs = [];

		foreach ( $items as $item ) {
			if ( 'notice' !== $item['type'] ) {
				$slugs[] = $item['slug'];
			}
		}

		if ( ! $slugs ) {
			return [];
		}

		$query = new WP_Query(
			[
				'post_type'              => [ 'wp_template', 'wp_template_part' ],
				'post_status'            => 'publish',
				'post_name__in'          => array_values( array_unique( $slugs ) ),
				'posts_per_page'         => 100,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'tax_query'              => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- the dashboard's checklist only, scoped to template posts.
					[
						'taxonomy' => 'wp_theme',
						'field'    => 'name',
						'terms'    => get_stylesheet(),
					],
				],
			]
		);

		$saved = [];

		foreach ( $query->posts as $post ) {
			if ( '' !== trim( $post->post_content ) ) {
				$saved[ $post->post_type . ':' . $post->post_name ] = $post->ID;
			}
		}

		return $saved;
	}

	/**
	 * The name of one of the theme's items, or the slug for any other.
	 *
	 * @since 1.6.0
	 *
	 * @param string $slug Item slug.
	 * @return string
	 */
	private function get_label( string $slug ): string {
		$labels = [
			'header'                => __( 'Header', 'kadence' ),
			'footer'                => __( 'Footer', 'kadence' ),
			'archive'               => __( 'Archives', 'kadence' ),
			'front-page'            => __( 'Blog', 'kadence' ),
			'home'                  => __( 'Blog', 'kadence' ),
			'index'                 => __( 'Blog', 'kadence' ),
			self::GROUP_NOTE        => __( 'Group block layout', 'kadence' ),
			self::ELEMENTS_NOTE     => __( 'Kadence Elements', 'kadence' ),
			'note_placement'        => __( 'Title and featured image placement', 'kadence' ),
			'note_color_round_trip' => __( 'Colors after switching back', 'kadence' ),
			'note_width_round_trip' => __( 'Widths after switching back', 'kadence' ),
			'note_import_export'    => __( 'Customizer Import/Export', 'kadence' ),
			'note_reset'            => __( 'Customizer Reset', 'kadence' ),
		];

		return $labels[ $slug ] ?? $slug;
	}

	/**
	 * What one of the theme's items asks the site owner to do.
	 *
	 * @since 1.6.0
	 *
	 * @param string $slug Item slug.
	 * @return string
	 */
	private function get_description( string $slug ): string {
		$descriptions = [
			'header'     => __( 'Your header is now a block template part with your logo, site title and menu. Add anything else it needs in the Site Editor.', 'kadence' ),
			'footer'     => __( 'Your footer is now a block template part with columns to fill and a copyright line. Add your footer content in the Site Editor.', 'kadence' ),
			'archive'    => __( 'Your category, tag and date pages now use a block template that lists posts in a grid. Adjust it in the Site Editor.', 'kadence' ),
			'front-page' => __( 'Your front page now uses a block template that lists your latest posts. Adjust it in the Site Editor.', 'kadence' ),
			'home'       => __( 'Your blog page now uses a block template that lists your latest posts. Adjust it in the Site Editor.', 'kadence' ),
			'index'      => __( 'Your blog page now uses a block template that lists your latest posts in a grid. Adjust it in the Site Editor.', 'kadence' ),
		];

		$descriptions = array_merge(
			$descriptions,
			[
				self::ELEMENTS_NOTE     => __( 'Your Kadence Elements don’t show in Full Site Editing mode. They show again if you switch back.', 'kadence' ),
				self::GROUP_NOTE        => __( 'Your site had Enable Optimized Group Block off. Full Site Editing mode turns it on, so the Group block can space and lay out content differently. Review how your pages look.', 'kadence' ),
				'note_placement'        => __( 'Title and featured image placement follows the block templates, not the Customizer.', 'kadence' ),
				'note_color_round_trip' => __( 'Colors you save in the Site Editor stay after you turn the mode off, so they can differ from your Customizer palette.', 'kadence' ),
				'note_width_round_trip' => __( 'A Content or Wide width you save in the Site Editor stays after you turn the mode off. Block layouts can then use a Wide width that differs from your Customizer’s Content Max Width.', 'kadence' ),
				'note_import_export'    => __( 'Customizer Import/Export (from the Kadence Starter Templates plugin, not the theme) doesn’t support Full Site Editing templates yet. An export won’t include templates, template parts or Global Styles.', 'kadence' ),
				'note_reset'            => __( 'Customizer Reset (from the Kadence Starter Templates plugin, not the theme) doesn’t support Full Site Editing yet. It also resets the Site Editor’s default palette.', 'kadence' ),
			]
		);

		return $descriptions[ $slug ] ?? '';
	}

	/**
	 * The name of a feature Full Site Editing mode doesn't carry over, as the opt-in dialog lists it.
	 *
	 * @since 1.6.0
	 *
	 * @param string $slug Detail code.
	 * @return string
	 */
	private function get_feature_name( string $slug ): string {
		$names = [
			'sticky_header'      => __( 'sticky header', 'kadence' ),
			'transparent_header' => __( 'transparent header', 'kadence' ),
			'header_cart'        => __( 'header cart', 'kadence' ),
			'breadcrumbs'        => __( 'breadcrumbs', 'kadence' ),
		];

		return $names[ $slug ] ?? $slug;
	}

	/**
	 * A note as one short line for the opt-in dialog. Related notes share a line.
	 *
	 * @since 1.6.0
	 *
	 * @param string $slug Note slug.
	 * @return string
	 */
	private function get_short_note( string $slug ): string {
		$notes = [
			self::ELEMENTS_NOTE     => __( 'Kadence Elements rely on classic theme hooks, so they won’t display in Full Site Editing mode.', 'kadence' ),
			self::GROUP_NOTE        => __( 'Enable Optimized Group Block turns on, which can change spacing on your pages.', 'kadence' ),
			'note_placement'        => __( 'Title and featured image placement comes from the block templates.', 'kadence' ),
			'note_color_round_trip' => __( 'Colors and widths you save in the Site Editor stay if you switch back.', 'kadence' ),
			'note_width_round_trip' => __( 'Colors and widths you save in the Site Editor stay if you switch back.', 'kadence' ),
			'note_import_export'    => __( 'Customizer Import/Export and Reset in Kadence Starter Templates don’t support Full Site Editing yet.', 'kadence' ),
			'note_reset'            => __( 'Customizer Import/Export and Reset in Kadence Starter Templates don’t support Full Site Editing yet.', 'kadence' ),
		];

		return $notes[ $slug ] ?? $this->get_description( $slug );
	}

	/**
	 * The theme's detail codes as sentences.
	 *
	 * @since 1.6.0
	 *
	 * @param array $item Checklist item.
	 * @return string[]
	 */
	private function get_detail_messages( array $item ): array {
		$messages = [
			'header_items'       => __( 'Your classic header had more than a logo, site title and menu, such as a search, a button, social links or a second menu.', 'kadence' ),
			'block_header'       => __( 'Your header was built with the Kadence Header block. Add that block to the header.', 'kadence' ),
			'conditional_header' => __( 'Your classic site showed a different header on some pages. The Site Editor uses this header everywhere.', 'kadence' ),
			'footer_widgets'     => __( 'Your classic footer had widgets. Add their content to the footer columns.', 'kadence' ),
			'footer_items'       => __( 'Your classic footer had a menu, social links or other items.', 'kadence' ),
			'footer_html'        => __( 'Your classic footer had its own copyright text.', 'kadence' ),
			'archive_layout'     => __( 'Your classic post lists used layout settings from the Customizer, such as columns, a sidebar or which post details show.', 'kadence' ),
			'sticky_header'      => __( 'Your sticky header doesn’t apply in Full Site Editing mode.', 'kadence' ),
			'transparent_header' => __( 'Your transparent header doesn’t apply in Full Site Editing mode.', 'kadence' ),
			'header_cart'        => __( 'Your header cart doesn’t apply in Full Site Editing mode.', 'kadence' ),
			'breadcrumbs'        => __( 'Your breadcrumbs don’t apply in Full Site Editing mode.', 'kadence' ),
			'element'            => __( 'A Kadence Element added to or replaced this part of your site. Elements don’t show in Full Site Editing mode.', 'kadence' ),
		];

		$details = [];

		foreach ( (array) ( $item['details'] ?? [] ) as $detail ) {
			if ( 'child_template' === $detail ) {
				$file = $this->get_child_template( $item['slug'] );

				if ( $file ) {
					/* translators: %s: Template file name, such as header.php. */
					$details[] = sprintf( __( 'Your child theme has its own %s, which Full Site Editing mode doesn’t use.', 'kadence' ), $file );
				}
			} elseif ( is_string( $detail ) && isset( $messages[ $detail ] ) ) {
				$details[] = $messages[ $detail ];
			}
		}

		return $details;
	}

	/**
	 * Opens a template in the Site Editor's editing canvas.
	 *
	 * @since 1.6.0
	 *
	 * @param string $type wp_template or wp_template_part.
	 * @param string $slug Template slug.
	 * @return string
	 */
	private function get_edit_url( string $type, string $slug ): string {
		return add_query_arg(
			[
				'p'      => '/' . $type . '/' . rawurlencode( get_stylesheet() . '//' . $slug ),
				'canvas' => 'edit',
			],
			admin_url( 'site-editor.php' )
		);
	}
}
