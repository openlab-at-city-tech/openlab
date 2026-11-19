<?php
/**
 * Kadence\FSE\Templates class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use WP_Block_Template;

/**
 * Serves the block templates and template parts in fse/ to the Site Editor.
 *
 * Core only scans the theme's root templates/ and parts/ folders, which classic
 * sites must never have, so the files are added through core's template filters.
 *
 * @since 1.6.0
 */
final class Templates {

	/**
	 * Files found in fse/site-templates and fse/parts, keyed by template type, then slug.
	 *
	 * A child theme's files replace the parent theme's.
	 *
	 * @since 1.6.0
	 *
	 * @var array<string, array<string, string>>
	 */
	private $files = [];

	/**
	 * Whether a saved template lookup from pre_get_block_template is running.
	 *
	 * @since 1.6.0
	 *
	 * @var bool
	 */
	private $resolving = false;

	/**
	 * Adds the theme's templates to a template query.
	 *
	 * Saved templates already in the result win over the theme files, and saved
	 * copies of the theme's templates are marked as having a theme file.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Block_Template[] $query_result  Templates found so far.
	 * @param array               $query         The query arguments.
	 * @param string              $template_type wp_template or wp_template_part.
	 * @return WP_Block_Template[]
	 */
	public function add_templates( $query_result, $query, $template_type ) {
		$query_result = array_map( [ $this, 'mark_theme_file' ], $query_result );

		if ( ! empty( $query['wp_id'] ) || ( ! empty( $query['theme'] ) && get_stylesheet() !== $query['theme'] ) ) {
			return $query_result;
		}

		$taken = wp_list_pluck( $query_result, 'slug' );

		foreach ( $this->get_files( $template_type ) as $slug => $path ) {
			// PHP turns numeric keys like '404' into integers.
			$slug = (string) $slug;

			if ( in_array( $slug, $taken, true ) ) {
				continue;
			}

			if ( ! empty( $query['slug__in'] ) && ! in_array( $slug, (array) $query['slug__in'], true ) ) {
				continue;
			}

			if ( ! empty( $query['slug__not_in'] ) && in_array( $slug, (array) $query['slug__not_in'], true ) ) {
				continue;
			}

			if ( ! empty( $query['area'] ) && $this->get_area( $slug ) !== $query['area'] ) {
				continue;
			}

			$template = $this->build( $slug, $path, $template_type );

			// Like core, the post editor's template list only gets custom templates, not defaults like 404.
			if ( ! empty( $query['post_type'] ) && ! $template->is_custom ) {
				continue;
			}

			$query_result[] = $template;
		}

		return $query_result;
	}

	/**
	 * Resolves one of the theme's templates by ID when core finds no file for it.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Block_Template|null $block_template The template core found, if any.
	 * @param string                 $id             Template ID, as theme//slug.
	 * @param string                 $template_type  wp_template or wp_template_part.
	 * @return WP_Block_Template|null
	 */
	public function resolve_template( $block_template, $id, $template_type ) {
		if ( null !== $block_template || ! $this->owns( $id, $template_type ) ) {
			return $block_template;
		}

		$slug = explode( '//', $id, 2 )[1];

		return $this->build( $slug, $this->get_files( $template_type )[ $slug ], $template_type );
	}

	/**
	 * Looks up a saved copy of one of the theme's templates and marks it as having a theme file.
	 *
	 * Core returns a saved template before its get_block_template filter runs, so the
	 * Site Editor would offer Delete instead of Reset without this.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Block_Template|null $block_template A template another filter already returned.
	 * @param string                 $id             Template ID, as theme//slug.
	 * @param string                 $template_type  wp_template or wp_template_part.
	 * @return WP_Block_Template|null
	 */
	public function resolve_saved_template( $block_template, $id, $template_type ) {
		if ( null !== $block_template || $this->resolving || ! $this->owns( $id, $template_type ) ) {
			return $block_template;
		}

		$this->resolving = true;

		try {
			$template = get_block_template( $id, $template_type );
		} finally {
			$this->resolving = false;
		}

		return $template ? $this->mark_theme_file( $template ) : null;
	}

	/**
	 * Registers the patterns the templates reference, and the header and footer designs.
	 *
	 * Patterns hold the templates' translatable text, which a block template file cannot.
	 * The header and footer designs are offered wherever core offers designs for those
	 * parts: the inserter, the Site Editor's Patterns screen, and the part's Replace and
	 * Design panels.
	 *
	 * @since 1.6.0
	 */
	public function register_patterns() {
		$patterns = [
			'404'                => __( 'Page not found', 'kadence' ),
			'entry-meta'         => __( 'Post meta', 'kadence' ),
			'loop-entry'         => __( 'Post in a list', 'kadence' ),
			'post-navigation'    => __( 'Post navigation', 'kadence' ),
			'no-results'         => __( 'No posts found', 'kadence' ),
			'search-no-results'  => __( 'No search results', 'kadence' ),
			'navigation-overlay' => __( 'Navigation overlay', 'kadence' ),
		];

		foreach ( $patterns as $slug => $title ) {
			register_block_pattern(
				'kadence/' . $slug,
				[
					'title'    => $title,
					'inserter' => false,
					'filePath' => $this->find_file( 'patterns/' . $slug . '.php' ),
				]
			);
		}

		$part_patterns = [
			'header'          => __( 'Header', 'kadence' ),
			'header-centered' => __( 'Centered header', 'kadence' ),
			'header-button'   => __( 'Header with button', 'kadence' ),
			'header-top-bar'  => __( 'Header with top bar', 'kadence' ),
			'footer'          => __( 'Footer', 'kadence' ),
			'footer-simple'   => __( 'Simple footer', 'kadence' ),
			'footer-contact'  => __( 'Footer with links and contact', 'kadence' ),
			'footer-dark'     => __( 'Dark footer', 'kadence' ),
		];

		/**
		 * Filters the header and footer designs offered in the Site Editor.
		 *
		 * A child theme adds a design with a file in its fse/patterns/ folder.
		 *
		 * @since 1.6.0
		 *
		 * @param array<string, string> $part_patterns Design titles, keyed by file name without the extension.
		 *                                             The name starts with its template part area, such as header- or footer-,
		 *                                             and uses lowercase letters, numbers, dashes and underscores.
		 */
		$part_patterns = apply_filters( 'kadence_fse_part_patterns', $part_patterns );

		foreach ( (array) $part_patterns as $slug => $title ) {
			// Core needs a string title, and a slug that isn't a plain file name could load a file outside patterns/.
			if ( ! is_string( $title ) || ! is_string( $slug ) || sanitize_key( $slug ) !== $slug ) {
				continue;
			}

			$area = $this->get_area( $slug );
			$file = $this->find_file( 'patterns/' . $slug . '.php' );

			// A design needs an area to be offered in and a file to load.
			if ( WP_TEMPLATE_PART_AREA_UNCATEGORIZED === $area || ! file_exists( $file ) ) {
				continue;
			}

			register_block_pattern(
				'kadence/' . $slug,
				[
					'title'      => $title,
					'categories' => [ $area ],
					'blockTypes' => [ 'core/template-part/' . $area ],
					'filePath'   => $file,
				]
			);
		}
	}

	/**
	 * Registers the block styles the template parts use.
	 *
	 * Block gap is off in theme.json, so a grid block can't set its own gap and
	 * falls back to core's 0.5em. The footer's columns get the classic footer's
	 * 30px through a style instead. Like the classic footer's empty rows, the
	 * columns are hidden until one is filled. After that, only empty columns at
	 * the end are hidden, so they add no gaps when the columns stack, and an empty
	 * column before a filled one keeps its cell, as it does in the Site Editor.
	 * Only empty Groups count as columns, so the style never hides a Spacer or
	 * Separator in a Group it's applied to elsewhere.
	 *
	 * @since 1.6.0
	 */
	public function register_block_styles() {
		register_block_style(
			'core/group',
			[
				'name'         => 'kadence-footer-columns',
				'label'        => __( 'Footer columns', 'kadence' ),
				'inline_style' => '.wp-block-group.is-style-kadence-footer-columns{gap:30px}.wp-block-group.is-style-kadence-footer-columns:not(:has(>:not(.wp-block-group:empty))),.wp-block-group.is-style-kadence-footer-columns>.wp-block-group:empty:not(:has(~:not(.wp-block-group:empty))){display:none}',
			]
		);
	}

	/**
	 * Lets a site title word that can't fit break while the menu is a button.
	 *
	 * A long title with no spaces, like a domain name, otherwise keeps its full width, so on a
	 * phone it pushes the header wider than the screen and the menu button off it. The title's
	 * group may shrink, and only a word wider than the space left breaks, so other titles keep
	 * their line breaks and the logo still shrinks first. Core collapses the Navigation block's
	 * menu below a fixed 600px that theme.json's breakpoints don't reach, so the rule matches it.
	 *
	 * Every Site Title block shows the site's title, so the rule is only added when that title has
	 * 16 or more characters in a row that the browser can't break, roughly what a phone header
	 * fits. In some layouts the rule would let a group squeeze a shorter word mid-word.
	 *
	 * @since 1.6.0
	 */
	public function enqueue_block_css() {
		$title = html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		// Spaces, hyphens and dashes are break points; non-breaking spaces aren't.
		if ( ! preg_match( '/(?:[^\s\x{2010}\x{2013}\x{2014}-]|[\x{00A0}\x{2007}\x{202F}]){16,}/u', $title ) ) {
			return;
		}

		wp_register_style( 'kadence-fse-site-title', false, [], KADENCE_VERSION );
		wp_add_inline_style( 'kadence-fse-site-title', '@media (width < 600px){:where(.wp-block-group:has(> .wp-block-site-title)){min-width:0}:root :where(.wp-block-site-title){max-width:100%;overflow-wrap:break-word}}' );
		wp_enqueue_style( 'kadence-fse-site-title' );
	}

	/**
	 * Whether a template ID belongs to one of the theme's template files.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed  $id            Template ID, as theme//slug.
	 * @param string $template_type wp_template or wp_template_part.
	 * @return bool
	 */
	private function owns( $id, $template_type ): bool {
		if ( ! is_string( $id ) || false === strpos( $id, '//' ) ) {
			return false;
		}

		list( $theme, $slug ) = explode( '//', $id, 2 );

		return get_stylesheet() === $theme && isset( $this->get_files( $template_type )[ $slug ] );
	}

	/**
	 * Marks a saved copy of one of the theme's templates as having a theme file.
	 *
	 * Core only looks for theme files in templates/ and parts/, so it reports that
	 * saved copies of the fse/ files have none.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $template A template from a query.
	 * @return mixed
	 */
	private function mark_theme_file( $template ) {
		if ( $template instanceof WP_Block_Template && $this->is_saved_copy( $template ) ) {
			$template->has_theme_file = true;
		}

		return $template;
	}

	/**
	 * Whether a template is a saved copy of one of the theme's own files.
	 *
	 * An empty copy is left out, because core reads the file from templates/ for a
	 * logged-out visitor when a template has a theme file but no content of its own.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Block_Template $template A template from a query.
	 * @return bool
	 */
	private function is_saved_copy( WP_Block_Template $template ): bool {
		if ( 'custom' !== $template->source || $template->has_theme_file || empty( $template->content ) ) {
			return false;
		}

		return get_stylesheet() === $template->theme && isset( $this->get_files( $template->type )[ $template->slug ] );
	}

	/**
	 * Gets the theme's template files for a template type, keyed by slug.
	 *
	 * @since 1.6.0
	 *
	 * @param string $template_type wp_template or wp_template_part.
	 * @return array<string, string> File paths keyed by slug.
	 */
	private function get_files( $template_type ): array {
		if ( ! isset( $this->files[ $template_type ] ) ) {
			/*
			 * Not "templates": WordPress.org's Theme Check treats any path ending in templates/index.html
			 * as a block theme, then fails the upload for having none at the root.
			 */
			$folder = 'wp_template_part' === $template_type ? 'parts' : 'site-templates';
			$files  = [];

			// A child theme's files come last, so they replace the parent's and can add their own.
			foreach ( $this->get_directories() as $directory ) {
				$paths = glob( $directory . '/fse/' . $folder . '/*.html' );

				foreach ( $paths ? $paths : [] as $path ) {
					$files[ basename( $path, '.html' ) ] = $path;
				}
			}

			$this->files[ $template_type ] = $files;
		}

		return $this->files[ $template_type ];
	}

	/**
	 * Finds a file in fse/, letting a child theme replace the parent theme's copy.
	 *
	 * @since 1.6.0
	 *
	 * @param string $file File path inside fse/, like patterns/404.php.
	 * @return string The file to use.
	 */
	private function find_file( string $file ): string {
		$directories = array_reverse( $this->get_directories() );

		foreach ( $directories as $directory ) {
			if ( file_exists( $directory . '/fse/' . $file ) ) {
				return $directory . '/fse/' . $file;
			}
		}

		return get_template_directory() . '/fse/' . $file;
	}

	/**
	 * The theme directories that can hold fse/ files, parent first.
	 *
	 * @since 1.6.0
	 *
	 * @return string[]
	 */
	private function get_directories(): array {
		return array_unique( [ get_template_directory(), get_stylesheet_directory() ] );
	}

	/**
	 * Gets a template part's area from its slug.
	 *
	 * A slug that is a core area, or starts with one (header-centered), gets that area.
	 * Anything else is uncategorized, which is core's default.
	 *
	 * @since 1.6.0
	 *
	 * @param string $slug Template part slug.
	 * @return string
	 */
	private function get_area( string $slug ): string {
		foreach ( get_allowed_block_template_part_areas() as $area ) {
			if ( WP_TEMPLATE_PART_AREA_UNCATEGORIZED === $area['area'] ) {
				continue;
			}

			if ( $slug === $area['area'] || str_starts_with( $slug, $area['area'] . '-' ) ) {
				return $area['area'];
			}
		}

		return WP_TEMPLATE_PART_AREA_UNCATEGORIZED;
	}

	/**
	 * Builds a template from its file with core's own helper.
	 *
	 * @since 1.6.0
	 *
	 * @param string $slug          Template slug.
	 * @param string $path          Template file path.
	 * @param string $template_type wp_template or wp_template_part.
	 * @return WP_Block_Template
	 */
	private function build( string $slug, string $path, string $template_type ): WP_Block_Template {
		$file = [
			'slug'  => $slug,
			'path'  => $path,
			'theme' => get_stylesheet(),
			'type'  => $template_type,
		];

		if ( 'wp_template_part' === $template_type ) {
			$file['area']  = $this->get_area( $slug );
			$file['title'] = ucwords( str_replace( '-', ' ', $slug ) );
		}

		$template = _build_block_template_result_from_file( $file, $template_type );

		// Core finds the menu in an overlay part by walking the part's blocks, which doesn't look inside a pattern.
		if ( WP_TEMPLATE_PART_AREA_NAVIGATION_OVERLAY === ( $file['area'] ?? '' ) ) {
			$template->content = serialize_blocks( resolve_pattern_blocks( parse_blocks( $template->content ) ) );
		}

		return $template;
	}
}
