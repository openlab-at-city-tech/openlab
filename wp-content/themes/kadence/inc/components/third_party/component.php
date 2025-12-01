<?php
/**
 * Kadence\Third_Party\Component class
 *
 * @package kadence
 */

namespace Kadence\Third_Party;

use Kadence\Component_Interface;
use function Kadence\kadence;
use function add_action;
use function add_filter;
use function get_template_part;

/**
 * Class for integrating with the block Third_Party.
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug(): string {
		return 'third_party';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		// WeDocs.
		remove_action( 'wedocs_before_main_content', 'wedocs_template_wrapper_start' );
		remove_action( 'wedocs_after_main_content', 'wedocs_template_wrapper_end' );
		add_action( 'wedocs_before_main_content', [ $this, 'output_content_wrapper' ] );
		add_action( 'wedocs_after_main_content', [ $this, 'output_content_wrapper_end' ] );
		add_action( 'kadence_gallery_post_before', [ $this, 'output_content_wrapper' ] );
		add_action( 'kadence_gallery_post_after', [ $this, 'output_content_wrapper_end' ] );
		add_action( 'kadence_gallery_post_before_content', [ $this, 'output_content_inner' ] );
		add_action( 'kadence_gallery_post_after_content', [ $this, 'output_content_inner_end' ] );
		add_filter( 'kadence_gallery_single_show_title', '__return_false' );
		add_action( 'kadence_gallery_album_before', [ $this, 'output_content_wrapper' ] );
		add_action( 'kadence_gallery_album_after', [ $this, 'output_content_wrapper_end' ] );
		add_action( 'kadence_gallery_album_before_content', [ $this, 'output_archive_content_inner' ] );
		add_action( 'kadence_gallery_album_after_content', [ $this, 'output_content_inner_end' ] );
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_content_inner() {
		if ( kadence()->show_feature_above() ) {
			get_template_part( 'template-parts/content/entry_thumbnail', get_post_type() );
		}
		?>
		<div class="entry-content-wrap">
		<?php
		if ( kadence()->show_in_content_title() ) {
			get_template_part( 'template-parts/content/entry_header', get_post_type() );
		}
		if ( kadence()->show_feature_below() ) {
			get_template_part( 'template-parts/content/entry_thumbnail', get_post_type() );
		}
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_content_inner_end() {
		?>
		</div>
		<?php
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_archive_content_inner() {
		/**
		 * Hook for anything before main content
		 */
		do_action( 'kadence_before_archive_content' );
		if ( kadence()->show_in_content_title() ) {
			get_template_part( 'template-parts/content/archive_header' );
		}
		?>
		<div id="archive-container" class="content-wrap">
		<?php
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_content_wrapper() {
		kadence()->print_styles( 'kadence-content' );
		/**
		 * Hook for Hero Section
		 */
		do_action( 'kadence_hero_header' );
		?>
		<div id="primary" class="content-area">
			<div class="content-container site-container">
				<div id="main" class="site-main">
					<?php
					/**
					 * Hook for anything before main content
					 */
					do_action( 'kadence_before_main_content' );
					?>
					<div class="content-wrap">
					<?php
	}
	/**
	 * Adds theme end output Wrapper.
	 */
	public function output_content_wrapper_end() {
		/**
		 * Hook for anything after main content
		 */
		do_action( 'kadence_after_main_content' );
		?>
			</div><!-- #main -->
			<?php
			get_sidebar();
			?>
			</div>
		</div><!-- #primary -->
		<?php
	}
}
