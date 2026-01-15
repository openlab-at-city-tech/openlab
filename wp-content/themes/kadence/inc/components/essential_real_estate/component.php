<?php
/**
 * Kadence\Essential_Real_Estate\Component class
 *
 * @package kadence
 */

namespace Kadence\Essential_Real_Estate;

use Kadence\Component_Interface;
use function Kadence\kadence;
use function add_action;
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
		return 'essential_real_estate';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		// WeDocs.
		remove_action( 'ere_before_main_content', 'ere_output_content_wrapper_start' );
		remove_action( 'ere_after_main_content', 'ere_after_main_content' );
		remove_action( 'ere_sidebar_property', 'ere_sidebar_property' );
		add_action( 'ere_before_main_content', [ $this, 'output_content_wrapper' ] );
		add_action( 'ere_after_main_content', [ $this, 'output_content_wrapper_end' ] );
		// add_action( 'ere_before_main_content', array( $this, 'output_content_inner' ), 20 );
		// add_action( 'ere_after_main_content', array( $this, 'output_content_inner_end' ), 20 );
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_content_inner() {
		if ( is_archive() ) {
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
		} else {
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
		// /**
		// * Hook for Hero Section
		// */
		// do_action( 'kadence_hero_header' );
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
		?>
		</div>
		<?php
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
