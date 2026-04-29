<?php
/**
 * Astra Learn Helper Class
 *
 * @package Astra
 * @since 4.12.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Astra_Learn class.
 *
 * @since 4.12.0
 */
class Astra_Learn {
	/**
	 * Get default learn chapters structure.
	 *
	 * Returns the complete structure of all available chapters and their steps.
	 * This serves as the source of truth for chapter definitions used across
	 * the theme for both frontend display and analytics validation.
	 *
	 * @return array Array of chapter objects with their steps.
	 * @since 4.12.0
	 */
	public static function get_chapters_structure() {
		$chapters = array(
			array(
				'id'          => 'brand-basics',
				'title'       => __( 'Brand Basics', 'astra' ),
				'description' => __( 'Make your website instantly recognizable and aligned with your brand identity.', 'astra' ),
				'url'         => 'https://wpastra.com/docs/style-guide/',
				'steps'       => array(
					array(
						'id'          => 'logo-tagline',
						'title'       => __( 'Add Logo, Tagline & Site Icon', 'astra' ),
						'description' => __( 'Help visitors identify your brand quickly by personalizing your core brand elements.', 'astra' ),
						'learn'       => array(
							'type'    => 'dialog',
							'content' => array(
								'type' => 'image',
								'data' => array(
									'src' => 'https://wpastra.com/wp-content/uploads/2025/12/astra-learn-logo-tagline.png',
									'alt' => __( 'Add Logo, Tagline & Site Icon in Astra', 'astra' ),
								),
							),
						),
						'action'      => array(
							'label'      => __( 'Add Branding', 'astra' ),
							'url'        => admin_url( 'customize.php?autofocus[section]=title_tagline' ),
							'isExternal' => true,
						),
						'completed'   => false,
					),
					array(
						'id'          => 'style-guide',
						'title'       => __( 'Update Brand Style Guide', 'astra' ),
						'description' => __( 'Bring consistency across your entire site by setting your brand colors, fonts, and design rules.', 'astra' ),
						'learn'       => array(
							'type'    => 'dialog',
							'content' => array(
								'type' => 'image',
								'data' => array(
									'src' => 'https://wpastra.com/wp-content/uploads/2025/12/astra-learn-style-guide.png',
									'alt' => __( 'Update Brand Style Guide in Astra', 'astra' ),
								),
							),
						),
						'action'      => array(
							'label'      => __( 'Update Style Guide', 'astra' ),
							'url'        => admin_url( 'customize.php?autofocus=astra-tour' ),
							'isExternal' => true,
						),
						'completed'   => false,
					),
				),
			),
			array(
				'id'          => 'navigation-header',
				'title'       => __( 'Navigation & Header', 'astra' ),
				'description' => __( 'Guide visitors effortlessly with a clear, modern, and intuitive header experience.', 'astra' ),
				'url'         => 'https://wpastra.com/docs/header-builder-options/',
				'steps'       => array(
					array(
						'id'          => 'header-layout',
						'title'       => __( 'Customize Header Layout', 'astra' ),
						'description' => __( 'Adjust your header structure: placement of logo, site title, buttons, menu and other elements', 'astra' ),
						'learn'       => array(
							'type'    => 'dialog',
							'content' => array(
								'type' => 'image',
								'data' => array(
									'src' => 'https://wpastra.com/wp-content/uploads/2025/12/astra-learn-navigation-header.png',
									'alt' => __( 'Customize Header Layout in Astra', 'astra' ),
								),
							),
						),
						'action'      => array(
							'label'      => __( 'Customize Header', 'astra' ),
							'url'        => admin_url( 'customize.php?autofocus[panel]=panel-header-builder-group' ),
							'isExternal' => true,
						),
						'completed'   => false,
					),
					array(
						'id'          => 'organize-menu',
						'title'       => __( 'Organize Your Menu', 'astra' ),
						'description' => __( 'Create a simple, logical menu so visitors can find what they need without friction.', 'astra' ),
						'learn'       => array(
							'type'    => 'dialog',
							'content' => array(
								'type' => 'image',
								'data' => array(
									'src' => 'https://wpastra.com/wp-content/uploads/2025/12/astra-learn-organize-menu.png',
									'alt' => __( 'Organize Your Menu in Astra', 'astra' ),
								),
							),
						),
						'action'      => array(
							'label'      => __( 'Configure Menu', 'astra' ),
							'url'        => admin_url( 'customize.php?autofocus[section]=section-hb-menu-1' ),
							'isExternal' => true,
						),
						'completed'   => false,
					),
					array(
						'id'          => 'mobile-header',
						'title'       => __( 'Set Up Your Mobile Header', 'astra' ),
						'description' => __( 'Optimize the header experience for small screens to ensure a seamless mobile journey.', 'astra' ),
						'learn'       => array(
							'type'    => 'dialog',
							'content' => array(
								'type' => 'image',
								'data' => array(
									'src' => 'https://wpastra.com/wp-content/uploads/2025/12/astra-learn-mobile-header.png',
									'alt' => __( 'Set Up Your Mobile Header in Astra', 'astra' ),
								),
							),
						),
						'action'      => array(
							'label'      => __( 'Configure Mobile Menu', 'astra' ),
							'url'        => admin_url( 'customize.php?autofocus[section]=section-header-mobile-menu&preview-device=mobile' ),
							'isExternal' => true,
						),
						'isPro'       => false,
						'completed'   => false,
					),
				),
			),
			array(
				'id'          => 'footer-customization',
				'title'       => __( 'Footer Customization', 'astra' ),
				'description' => __( 'Create a clean, modern footer that builds trust and improves browsing.', 'astra' ),
				'url'         => 'https://wpastra.com/docs/footer-builder/',
				'steps'       => array(
					array(
						'id'          => 'footer-layout',
						'title'       => __( 'Customize Footer Layout', 'astra' ),
						'description' => __( 'Add your social handles, links, contact info, copyrights, or widgets to create a professional closing section.', 'astra' ),
						'learn'       => array(
							'type'    => 'dialog',
							'content' => array(
								'type' => 'image',
								'data' => array(
									'src' => 'https://wpastra.com/wp-content/uploads/2025/12/astra-learn-footer-layout.png',
									'alt' => __( 'Customize Footer Layout in Astra', 'astra' ),
								),
							),
						),
						'action'      => array(
							'label'      => __( 'Customize Footer', 'astra' ),
							'url'        => admin_url( 'customize.php?autofocus[panel]=panel-footer-builder-group' ),
							'isExternal' => true,
						),
						'completed'   => false,
					),
				),
			),
			array(
				'id'          => 'page-layout-settings',
				'title'       => __( 'Page & Layout Settings', 'astra' ),
				'description' => __( 'Give your pages a clean, consistent visual flow that feels polished and professional.', 'astra' ),
				'url'         => 'https://wpastra.com/docs/page-layout-settings-guide/',
				'steps'       => array(
					array(
						'id'          => 'sidebar-layout',
						'title'       => __( 'Choose default sidebar layout and style', 'astra' ),
						'description' => __( 'Select left, right, or no sidebar depending on your content needs.', 'astra' ),
						'learn'       => array(
							'type'    => 'dialog',
							'content' => array(
								'type' => 'image',
								'data' => array(
									'src' => 'https://wpastra.com/wp-content/uploads/2025/12/astra-learn-sidebar-layout.png',
									'alt' => __( 'Customize Sidebar Layout in Astra', 'astra' ),
								),
							),
						),
						'action'      => array(
							'label'      => __( 'Configure Sidebar', 'astra' ),
							'url'        => admin_url( 'customize.php?autofocus[section]=section-sidebars' ),
							'isExternal' => true,
						),
						'completed'   => false,
					),
					array(
						'id'          => 'blog-layout',
						'title'       => __( 'Customize Blog Layout', 'astra' ),
						'description' => __( 'Choose how your posts appear - customize everything like layout, style, width and much more', 'astra' ),
						'learn'       => array(
							'type'    => 'dialog',
							'content' => array(
								'type' => 'image',
								'data' => array(
									'src' => 'https://wpastra.com/wp-content/uploads/2025/12/astra-learn-blog-layout.png',
									'alt' => __( 'Customize Blog Layout in Astra', 'astra' ),
								),
							),
						),
						'action'      => array(
							'label'      => __( 'Customize Blog', 'astra' ),
							'url'        => admin_url( 'customize.php?autofocus[section]=section-blog' ),
							'isExternal' => true,
						),
						'completed'   => false,
					),
					array(
						'id'          => 'single-page-layout',
						'title'       => __( 'Customize Single Page Layout', 'astra' ),
						'description' => __( 'Fine-tune individual pages for layout, style to suite your storytelling, SEO, and user experience', 'astra' ),
						'learn'       => array(
							'type'    => 'dialog',
							'content' => array(
								'type' => 'image',
								'data' => array(
									'src' => 'https://wpastra.com/wp-content/uploads/2025/12/astra-learn-single-page-layout.png',
									'alt' => __( 'Customize Single Page Layout in Astra', 'astra' ),
								),
							),
						),
						'action'      => array(
							'label'      => __( 'Customize Page', 'astra' ),
							'url'        => admin_url( 'customize.php?autofocus[section]=section-single-page' ),
							'isExternal' => true,
						),
						'completed'   => false,
					),
				),
			),
		);

		// Add WooCommerce chapter if WooCommerce is active.
		if ( class_exists( 'WooCommerce' ) ) {
			$chapters[] = array(
				'id'          => 'woocommerce-essentials',
				'title'       => __( 'WooCommerce Essentials', 'astra' ),
				'description' => __( 'Create a clean, trustworthy shopping experience to maximize your sales', 'astra' ),
				'url'         => 'https://wpastra.com/docs/woocommerce-integration-overview/',
				'steps'       => array(
					array(
						'id'          => 'shop-page',
						'title'       => __( 'Customize Shop Page', 'astra' ),
						'description' => __( 'Adjust product grid spacing, columns, and visual elements.', 'astra' ),
						'learn'       => array(
							'type'    => 'dialog',
							'content' => array(
								'type' => 'image',
								'data' => array(
									'src' => 'https://wpastra.com/wp-content/uploads/2025/12/astra-learn-shop-page.png',
									'alt' => __( 'Customize Shop Page in Astra', 'astra' ),
								),
							),
						),
						'action'      => array(
							'label'      => __( 'Customize Shop', 'astra' ),
							'url'        => admin_url( 'customize.php?autofocus[section]=woocommerce_product_catalog' ),
							'isExternal' => true,
						),
						'completed'   => false,
					),
					array(
						'id'          => 'product-page',
						'title'       => __( 'Customize Product Page', 'astra' ),
						'description' => __( 'Improve product presentation with better structure and clarity.', 'astra' ),
						'learn'       => array(
							'type'    => 'dialog',
							'content' => array(
								'type' => 'image',
								'data' => array(
									'src' => 'https://wpastra.com/wp-content/uploads/2025/12/astra-learn-product-page.png',
									'alt' => __( 'Customize Product Page in Astra', 'astra' ),
								),
							),
						),
						'action'      => array(
							'label'      => __( 'Customize Products', 'astra' ),
							'url'        => admin_url( 'customize.php?autofocus[section]=section-woo-shop-single' ),
							'isExternal' => true,
						),
						'completed'   => false,
					),
				),
			);
		}

		// Add Edit Your Homepage chapter as the last item.
		$homepage_id    = absint( get_option( 'page_on_front' ) );
		$homepage_url   = $homepage_id ? admin_url( 'post.php?post=' . $homepage_id . '&action=edit' ) : admin_url( 'options-reading.php' );
		$homepage_label = $homepage_id ? __( 'Edit Homepage', 'astra' ) : __( 'Set Homepage', 'astra' );

		$chapters[] = array(
			'id'          => 'edit-homepage',
			'title'       => __( 'Edit Your Homepage', 'astra' ),
			'description' => __( 'Add your own content and visuals to make your site feel authentic and trustworthy', 'astra' ),
			'url'         => 'https://wpastra.com/guides-and-tutorials/set-your-homepage/',
			'steps'       => array(
				array(
					'id'          => 'homepage-editor',
					'title'       => __( 'Edit Your Homepage', 'astra' ),
					'description' => __( 'Add your own content and visuals to make your site feel authentic and trustworthy', 'astra' ),
					'action'      => array(
						'label'      => $homepage_label,
						'url'        => $homepage_url,
						'isExternal' => true,
					),
					'completed'   => false,
				),
			),
		);

		/**
		 * Filter learn chapters structure.
		 *
		 * @param array $chapters Learn chapters data.
		 * @since 4.12.0
		 */
		return apply_filters( 'astra_learn_chapters', $chapters );
	}

	/**
	 * Get count of incomplete chapters.
	 *
	 * A chapter is considered incomplete if it has at least one incomplete step.
	 *
	 * @param int $user_id Optional. User ID to get progress for. Defaults to current user.
	 * @return int Number of incomplete chapters.
	 * @since 4.12.2
	 */
	public static function get_incomplete_chapters_count( $user_id = 0 ) {
		$chapters         = self::get_learn_chapters( $user_id );
		$incomplete_count = 0;

		foreach ( $chapters as $chapter ) {
			if ( ! isset( $chapter['steps'] ) || ! is_array( $chapter['steps'] ) ) {
				continue;
			}

			foreach ( $chapter['steps'] as $step ) {
				if ( empty( $step['completed'] ) ) {
					$incomplete_count++;
					break;
				}
			}
		}

		return $incomplete_count;
	}

	/**
	 * Get learn chapters with user progress merged.
	 *
	 * @param int $user_id Optional. User ID to get progress for. Defaults to current user.
	 * @return array Chapters array with progress data merged.
	 * @since 4.12.0
	 */
	public static function get_learn_chapters( $user_id = 0 ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		// Get chapters structure.
		$chapters = self::get_chapters_structure();

		// Get saved progress from user meta.
		$saved_progress = get_user_meta( $user_id, 'astra_learn_progress', true );
		if ( ! is_array( $saved_progress ) ) {
			$saved_progress = array();
		}

		// Merge saved progress with chapters.
		foreach ( $chapters as &$chapter ) {
			// Validate chapter structure.
			if ( ! isset( $chapter['id'], $chapter['steps'] ) || ! is_array( $chapter['steps'] ) ) {
				continue;
			}

			$chapter_id = $chapter['id'];

			foreach ( $chapter['steps'] as &$step ) {
				if ( ! isset( $step['id'] ) ) {
					continue;
				}

				$step_id = $step['id'];
				if ( isset( $saved_progress[ $chapter_id ][ $step_id ] ) ) {
					$step['completed'] = $saved_progress[ $chapter_id ][ $step_id ];
				}
			}
		}

		return $chapters;
	}
}
