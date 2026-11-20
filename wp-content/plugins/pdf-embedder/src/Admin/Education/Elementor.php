<?php

namespace PDFEmbedder\Admin\Education;

use Elementor\Elements_Manager;
use PDFEmbedder\Helpers\Assets;
use PDFEmbedder\Helpers\Links;

/**
 * Educational locked "PDF Embedder" widget in the Elementor editor panel.
 *
 * The premium plugin ships a real Elementor widget; free users have no way to
 * discover it exists. This class injects a promotion entry into Elementor's
 * editor config, which the panel renders as a locked card: crown badge, not
 * draggable or insertable, and pressing it opens Elementor's upgrade card with
 * our copy and pricing link. No widget type is ever registered, so nothing can
 * end up stored in post content.
 *
 * Booted from `Admin\Admin::init()` on `admin_init`, alongside the other
 * education classes; every hook used here fires later, inside the Elementor
 * editor. The `did_action( 'elementor/loaded' )` gate at the boot site keeps
 * the class from being autoloaded on sites without Elementor. Eligibility
 * (no premium, `manage_options`) is re-checked inside each callback rather
 * than once at boot, so the checks hold no matter when a callback fires.
 *
 * @since 5.0.2
 */
class Elementor {

	/**
	 * Promotion type slug.
	 *
	 * Elementor composes the panel press event name from it
	 * (`{type}-promotion:open`), so it must stay stable.
	 *
	 * @since 5.0.2
	 */
	public const TYPE = 'pdf-embedder';

	/**
	 * Custom widget category slug.
	 *
	 * Must match the category slug the premium plugin registers, so the locked
	 * card and the real premium widget occupy the same panel section before and
	 * after an upgrade.
	 *
	 * @since 5.0.2
	 */
	public const CATEGORY = 'pdf-embedder';

	/**
	 * Assign all hooks to proper places.
	 *
	 * @since 5.0.2
	 */
	public function hooks(): void {

		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
		add_filter( 'elementor/editor/localize_settings', [ $this, 'add_promotion_data' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_script' ] );
	}

	/**
	 * Whether the education entry should be shown to the current user.
	 *
	 * Premium replaces the locked card with the real widget, and the upsell is
	 * only actionable by users who could actually install the upgrade; Elementor
	 * gates its own widget promotions on the same capability.
	 *
	 * @since 5.0.2
	 */
	private function is_eligible(): bool {

		return ! pdf_embedder()->is_premium() && current_user_can( 'manage_options' );
	}

	/**
	 * Register our widget category in the Elementor editor panel.
	 *
	 * @since 5.0.2
	 *
	 * @param Elements_Manager $elements_manager Elementor elements manager.
	 */
	public function register_category( Elements_Manager $elements_manager ): void {

		if ( ! $this->is_eligible() ) {
			return;
		}

		$elements_manager->add_category(
			self::CATEGORY,
			[
				'title'       => esc_html__( 'PDF Embedder', 'pdf-embedder' ),
				'icon'        => 'eicon-document-file',
				// Elementor releases that predate the promotion entry consumed in
				// add_promotion_data() would leave this category with zero items;
				// never show an empty section there.
				'hideIfEmpty' => true,
			]
		);
	}

	/**
	 * Inject the locked widget entry into the Elementor editor config.
	 *
	 * The editor renders entries from this config array as non-editable panel
	 * cards; older Elementor releases ignore the key entirely, which degrades
	 * to the entry simply not appearing.
	 *
	 * @since 5.0.2
	 *
	 * @param array $settings Editor configuration.
	 *
	 * @return array
	 */
	public function add_promotion_data( $settings ): array {

		$settings = (array) $settings;

		if ( ! $this->is_eligible() ) {
			return $settings;
		}

		if ( ! isset( $settings['atomicWidgetPromotions'] ) ) {
			$settings['atomicWidgetPromotions'] = [];
		}

		$settings['atomicWidgetPromotions'][] = [
			'type'     => self::TYPE,
			'cardType' => 'atomic',
			'widgets'  => [
				[
					// Same slug as the premium widget: the locked card and the real
					// widget can never coexist, and reusing the name keeps panel
					// search and muscle memory identical after an upgrade.
					'name'       => 'pdf-embedder',
					'title'      => esc_html__( 'PDF Embedder', 'pdf-embedder' ),
					'icon'       => 'pdfemb-elementor-icon',
					// The editor runs JSON.parse() on this value, so it must be a
					// JSON-encoded string, not a PHP array.
					'categories' => wp_json_encode( [ self::CATEGORY ] ),
				],
			],
			'content'  => $this->get_promotion_content(),
		];

		return $settings;
	}

	/**
	 * Copy and pricing link for the upgrade card.
	 *
	 * @since 5.0.2
	 */
	private function get_promotion_content(): array {

		$cta_url = Links::get_utm_link(
			'https://wp-pdf.com/pricing/',
			'ElementorEditor',
			'Upgrade to Pro',
			'Locked Widget'
		);

		return [
			'title'         => esc_html__( 'PDF Embedder is a Premium widget', 'pdf-embedder' ),
			'content'       => esc_html__( 'Embed and customize PDFs directly in Elementor. Upgrade to unlock the widget. 50% OFF for PDF Embedder users, applied at checkout.', 'pdf-embedder' ),
			'ctaText'       => esc_html__( 'Upgrade to Pro', 'pdf-embedder' ),
			'widgetCtaUrl'  => $cta_url,
			// The card only reads the widget-level link today; the section-level
			// key is part of the config shape Elementor's own entries carry, so
			// keep both populated.
			'sectionCtaUrl' => $cta_url,
			// Exported at 2x (520px for a 260px slot) so the banner stays crisp
			// on retina displays. The plugin URL is fixed at boot time; force the
			// current request scheme to avoid mixed-content blocking on https.
			'image'         => esc_url_raw( set_url_scheme( Assets::url( 'img/edu/elementor-banner-520.png', false ) ) ),
		];
	}

	/**
	 * Enqueue the Elementor editor stylesheet that renders our widget icon.
	 *
	 * @since 5.0.2
	 */
	public function enqueue_editor_styles(): void {

		if ( ! $this->is_eligible() ) {
			return;
		}

		$handle = 'pdfemb-elementor-education';

		wp_enqueue_style(
			$handle,
			Assets::url( 'css/admin/pdfemb-elementor-education.css' ),
			[],
			Assets::ver()
		);

		// The plugin URL is fixed at boot time; force the current request scheme
		// so the SVG isn't blocked as mixed content on https sites.
		$icon_url = set_url_scheme( Assets::url( 'img/elementor-icon.svg', false ) );

		wp_add_inline_style(
			$handle,
			sprintf(
				':root{--pdfemb-elementor-icon-url:url(%s);}',
				wp_json_encode( $icon_url )
			)
		);
	}

	/**
	 * Enqueue the editor script: search keywords plus the upgrade dialog.
	 *
	 * The script adds search keywords to the locked panel card, because
	 * Elementor copies only name/title/icon/categories from promotion config
	 * entries and the panel search would otherwise match the title alone.
	 *
	 * It also answers the card's press event with the editor's legacy dialog in
	 * every Elementor state, intercepting ahead of Elementor's promotions app
	 * when that is loaded. Deferring to the app instead is not an option: with
	 * Elementor Pro installed but not license-connected, the app rewrites every
	 * third-party upgrade card's button into its own license-activation prompt,
	 * and with Pro licensed the app is absent and presses would go unanswered.
	 * One dialog everywhere keeps the upsell and its pricing link intact.
	 *
	 * @since 5.0.2
	 */
	public function enqueue_editor_script(): void {

		if ( ! $this->is_eligible() ) {
			return;
		}

		$handle = 'pdfemb-elementor-education';

		wp_enqueue_script(
			$handle,
			Assets::url( 'js/admin/pdfemb-elementor-education.js' ),
			[ 'elementor-editor' ],
			Assets::ver(),
			true
		);

		$content = $this->get_promotion_content();

		wp_localize_script(
			$handle,
			'pdfembElementorEdu',
			[
				'widget'   => 'pdf-embedder',
				// Same source list as the premium widget's get_keywords(), so
				// panel search behaves the same before and after an upgrade.
				'keywords' => [
					esc_html__( 'pdf', 'pdf-embedder' ),
					esc_html__( 'document', 'pdf-embedder' ),
					esc_html__( 'embed', 'pdf-embedder' ),
					esc_html__( 'viewer', 'pdf-embedder' ),
					esc_html__( 'pdf embedder', 'pdf-embedder' ),
				],
				'event'    => self::TYPE . '-promotion:open',
				'title'    => $content['title'],
				'content'  => $content['content'],
				'button'   => $content['ctaText'],
				'url'      => $content['widgetCtaUrl'],
				'docsUrl'  => Links::get_utm_link(
					'https://wp-pdf.com/docs/using-pdf-embedder-with-elementor-pages/',
					'ElementorEditor',
					'Documentation',
					'Locked Widget'
				),
				'docsText' => esc_html__( 'Documentation', 'pdf-embedder' ),
				'image'    => $content['image'],
			]
		);
	}
}
