<?php
/**
 * Archive customizer section
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Background;
use LottaFramework\Customizer\Controls\Border;
use LottaFramework\Customizer\Controls\CallToAction;
use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Icons;
use LottaFramework\Customizer\Controls\ImageRadio;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Section;
use LottaFramework\Customizer\Controls\Select;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Spacing;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Controls\Text;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Customizer\Controls\Typography;
use LottaFramework\Customizer\Section as CustomizeSection;
use LottaFramework\Facades\AsyncCss;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Archive_Section' ) ) {

	class Kenta_Archive_Section extends CustomizeSection {

		use Kenta_Post_Card;
		use Kenta_Post_Structure;

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			return array_merge(
				$this->getArchiveLayoutControls(),
				[
					( new Section( 'kenta_archive_title' ) )
						->setLabel( __( 'Header', 'kenta' ) )
						->setControls( $this->getArchiveHeaderControls() )
					,

					( new Section( 'kenta_archive_card_section' ) )
						->setLabel( __( 'Post Card', 'kenta' ) )
						->setControls( $this->getCardControls() )
					,

					( new Section( 'kenta_archive_pagination_section' ) )
						->setLabel( __( 'Pagination', 'kenta' ) )
						->enableSwitch()
						->setControls( $this->getPaginationControls() )
					,

					( new Section( 'kenta_archive_sidebar_section' ) )
						->setLabel( __( 'Sidebar', 'kenta' ) )
						->enableSwitch( false )
						->keepMarginBelow()
						->setControls( [
							( new ImageRadio( 'kenta_archive_sidebar_layout' ) )
								->setLabel( __( 'Sidebar Layout', 'kenta' ) )
								->setDefaultValue( 'right-sidebar' )
								->setChoices( [
									'left-sidebar'  => [
										'title' => __( 'Left Sidebar', 'kenta' ),
										'src'   => kenta_image_url( 'archive-left-sidebar.png' ),
									],
									'right-sidebar' => [
										'title' => __( 'Right Sidebar', 'kenta' ),
										'src'   => kenta_image_url( 'archive-right-sidebar.png' ),
									],
								] )
							,
							( new CallToAction() )
								->setLabel( __( 'Customize Sidebar', 'kenta' ) )
								->displayAsButton()
								->expandCustomize( 'kenta_global:kenta_global_sidebar_section' )
							,
						] )
					,
					kenta_docs_control(
						__( '%sRead documentation for archive customize%s', 'kenta' ),
						'https://kentatheme.com/docs/kenta-theme/archive-theme-options/archive-layout/',
						'kenta_archive_layout_doc'
					)
				]
			);
		}

		/**
		 * @return array
		 */
		protected function getArchiveLayoutControls() {
			$controls = [
				( new Slider( 'kenta_archive_columns' ) )
					->setLabel( __( 'Card Columns', 'kenta' ) )
					->bindSelectiveRefresh( 'kenta-global-selective-css' )
					->setDefaultUnit( false )
					->setMin( 1 )
					->setMax( 6 )
					->enableResponsive()
					->setDefaultValue( [
						'desktop' => 3,
						'tablet'  => 2,
						'mobile'  => 1,
					] )
				,
				( new Slider( 'kenta_card_gap' ) )
					->setLabel( __( 'Card Gap', 'kenta' ) )
					->asyncCss( '.card-list', [ '--card-gap' => 'value' ] )
					->enableResponsive()
					->setDefaultUnit( 'px' )
					->setDefaultValue( '24px' )
				,
			];

			return apply_filters( 'kenta_archive_layout_controls', $controls );
		}

		/**
		 * @return array
		 */
		protected function getArchiveHeaderControls() {
			$controls = [
				( new Tabs() )
					->setActiveTab( 'content' )
					->addTab( 'content', __( 'Content', 'kenta' ), $this->getArchiveHeaderContentControls() )
					->addTab( 'style', __( 'Style', 'kenta' ), $this->getArchiveHeaderStyleControls() )
				,
			];

			return apply_filters( 'kenta_archive_header_controls', $controls );
		}

		protected function getArchiveHeaderContentControls() {
			$controls = [
				( new Toggle( 'kenta_disable_blogs_archive_header' ) )
					->setLabel( __( 'Disable Header On Blogs Home', 'kenta' ) )
					->selectiveRefresh( '.kenta-archive-header', 'kenta_show_archive_header', [
						'container_inclusive' => true,
					] )
					->openByDefault()
				,
			];

			if ( KENTA_WOOCOMMERCE_ACTIVE ) {
				$controls[] = ( new Toggle( 'kenta_disable_shop_archive_header' ) )
					->setLabel( __( 'Disable Header On Shop', 'kenta' ) )
					->selectiveRefresh( '.kenta-archive-header', 'kenta_show_archive_header', [
						'container_inclusive' => true,
					] )
					->closeByDefault();
			}

			if ( ! KENTA_CMP_PRO_ACTIVE ) {
				$controls[] = ( new Separator() );
				$controls[] = ( new Text( 'kenta_blogs_archive_header_title' ) )
					->setLabel( __( 'Blogs Page Title', 'kenta' ) )
					->selectiveRefresh( '.kenta-archive-header', 'kenta_show_archive_header', [
						'container_inclusive' => true,
					] )
					->setDefaultValue( __( 'Blogs', 'kenta' ) );
			}

			return apply_filters( 'kenta_archive_header_content_controls', $controls );
		}

		protected function getArchiveHeaderStyleControls() {
			$controls = [
				( new Radio( 'kenta_archive_header_alignment' ) )
					->setLabel( __( 'Alignment', 'kenta' ) )
					->asyncCss( '.kenta-archive-header', [ 'text-align' => 'value' ] )
					->buttonsGroupView()
					->setDefaultValue( 'center' )
					->setChoices( [
						'left'   => __( 'Left', 'kenta' ),
						'center' => __( 'Center', 'kenta' ),
						'right'  => __( 'Right', 'kenta' ),
					] )
				,
				( new Separator() ),
				( new Spacing( 'kenta_archive_header_padding' ) )
					->setLabel( __( 'Padding', 'kenta' ) )
					->asyncCss( '.kenta-archive-header .container', AsyncCss::dimensions( 'padding' ) )
					->setSpacing( [
						'top'    => '24px',
						'bottom' => '24px',
						'left'   => '0px',
						'right'  => '0px',
						'linked' => true,
					] )
				,
				( new Separator() ),
				( new Background( 'kenta_archive_header_background' ) )
					->setLabel( __( 'Background', 'kenta' ) )
					->asyncCss( '.kenta-archive-header', AsyncCss::background() )
					->setDefaultValue( [
						'type'  => 'color',
						'color' => 'var(--kenta-accent-active)',
					] )
				,
				( new Separator() ),
				( new Toggle( 'kenta_archive_header_has_overlay' ) )
					->setLabel( __( 'Enable Overlay', 'kenta' ) )
					->selectiveRefresh( '.kenta-archive-header', 'kenta_show_archive_header', [
						'container_inclusive' => true,
					] )
					->closeByDefault()
				,
				( new Condition() )
					->setCondition( [ 'kenta_archive_header_has_overlay' => 'yes' ] )
					->setControls( [
						( new Slider( 'kenta_archive_header_overlay_opacity' ) )
							->setLabel( __( 'Overlay Opacity', 'kenta' ) )
							->asyncCss( '.kenta-archive-header::after', [ 'opacity' => 'value' ] )
							->setMin( 0 )
							->setMax( 1 )
							->setDecimals( 2 )
							->setDefaultUnit( false )
							->setDefaultValue( 0.6 )
						,
						( new Background( 'kenta_archive_header_overlay' ) )
							->setLabel( __( 'Header Overlay', 'kenta' ) )
							->asyncCss( '.kenta-archive-header::after', AsyncCss::background() )
							->setDefaultValue( [
								'type'  => 'color',
								'color' => 'var(--kenta-accent-color)',
							] )
						,
					] ),
			];

			if ( ! KENTA_CMP_PRO_ACTIVE ) {
				$controls = array_merge(
					[
						( new ColorPicker( 'kenta_archive_title_color' ) )
							->setLabel( __( 'Title Color', 'kenta' ) )
							->asyncColors( '.kenta-archive-header .archive-title', [
								'initial' => 'color',
							] )
							->addColor( 'initial', __( 'Initial', 'kenta' ), 'var(--kenta-base-color)' )
						,
						( new ColorPicker( 'kenta_archive_description_color' ) )
							->setLabel( __( 'Description Color', 'kenta' ) )
							->asyncColors( '.kenta-archive-header .archive-description', [
								'initial' => 'color',
							] )
							->addColor( 'initial', __( 'Initial', 'kenta' ), 'var(--kenta-base-200)' )
						,
					],
					$controls
				);
			}

			return apply_filters( 'kenta_archive_header_style_controls', $controls );
		}

		/**
		 * @return array
		 */
		protected function getCardControls() {

			$content_controls = array_merge(
				array(
					$this->getPostElementsLayer( 'kenta_card_structure', 'entry', [
						'selective-refresh' => [ '.kenta-posts', 'kenta_render_posts_list' ],
						'selector'          => '.card',
						'value'             => [
							[ 'id' => 'thumbnail', 'visible' => true ],
							[ 'id' => 'categories', 'visible' => true ],
							[ 'id' => 'title', 'visible' => true ],
							[ 'id' => 'metas', 'visible' => true ],
							[ 'id' => 'excerpt', 'visible' => true ],
							[ 'id' => 'divider', 'visible' => true ],
							[ 'id' => 'read-more', 'visible' => true ],
						],
						'title'             => [],
						'cats'              => [],
						'tags'              => [],
						'metas'             => [],
						'divider'           => [],
					] ),
					( new Separator() )
				),
				$this->getCardContentControls( 'kenta_' )
			);

			$style_controls = $this->getCardStyleControls( 'kenta_', [
				'preset'    => 'plain',
				'selective' => 'kenta-global-selective-css',
			] );

			return array(
				kenta_docs_control(
					__( '%sRead documentation for post card options%s', 'kenta' ),
					'https://kentatheme.com/docs/kenta-theme/archive-theme-options/post-card/'
				),
				( new Tabs() )
					->setActiveTab( 'content' )
					->addTab( 'content', __( 'Content', 'kenta' ), apply_filters(
						'kenta_card_content_controls',
						$content_controls
					) )
					->addTab( 'style', __( 'Style', 'kenta' ), apply_filters(
						'kenta_card_style_controls',
						$style_controls,
						array( 'selector' => '.card' )
					) )
			);
		}

		/**
		 * @return array
		 */
		protected function getPaginationControls() {
			return [
				kenta_docs_control( __( '%sRead documentation for posts pagination%s', 'kenta' ), 'https://kentatheme.com/docs/kenta-theme/archive-theme-options/pagination/' ),
				( new Tabs() )
					->setActiveTab( 'general' )
					->addTab( 'general', __( 'General', 'kenta' ), $this->getPaginationGeneralControls() )
					->addTab( 'style', __( 'Style', 'kenta' ), $this->getPaginationStyleControls() )
				,
			];
		}

		/**
		 * @return array
		 */
		protected function getPaginationGeneralControls() {

			$pagination_type = [
				'numbered'        => __( 'Numbered', 'kenta' ),
				'prev-next'       => __( 'Prev & Next', 'kenta' ),
				'load-more'       => __( 'Load More', 'kenta' ),
				'infinite-scroll' => __( 'Infinite Scroll', 'kenta' ),
			];

			if ( ! KENTA_CMP_PRO_ACTIVE ) {
				$pagination_type['load-more']       = $pagination_type['load-more'] . ' (Pro Only)';
				$pagination_type['infinite-scroll'] = $pagination_type['infinite-scroll'] . ' (Pro Only)';
			}

			$controls = [
				( new Select( 'kenta_pagination_type' ) )
					->setLabel( __( 'Pagination Type', 'kenta' ) )
					->bindSelectiveRefresh( 'kenta-global-selective-css' )
					->selectiveRefresh( '.kenta-pagination', 'kenta_show_posts_pagination', [
						'container_inclusive' => true,
					] )
					->setDefaultValue( 'numbered' )
					->setChoices( $pagination_type )
				,
				( new ImageRadio( 'kenta_pagination_alignment' ) )
					->setLabel( __( 'Alignment', 'kenta' ) )
					->inlineChoices()
					->asyncCss( '.kenta-pagination', [ 'justify-content' => 'value' ] )
					->setDefaultValue( 'center' )
					->setChoices( [
						'flex-start' => [
							'src' => kenta_image( 'text-left' )
						],
						'center'     => [
							'src' => kenta_image( 'text-center' )
						],
						'flex-end'   => [
							'src' => kenta_image( 'text-right' )
						],
					] )
				,
				( new Separator() ),
				( new Condition() )
					->setCondition( [ 'kenta_pagination_type' => 'numbered' ] )
					->setControls( [
						( new Toggle( 'kenta_pagination_prev_next_button' ) )
							->setLabel( __( 'Previous & Next Buttons', 'kenta' ) )
							->openByDefault()
							->selectiveRefresh( '.kenta-pagination', 'kenta_show_posts_pagination', [
								'container_inclusive' => true,
							] )
						,
						( new Separator() ),
					] )
				,
				( new Condition() )
					->setCondition( [ 'kenta_pagination_type' => 'numbered|prev-next' ] )
					->setControls( [
						( new Radio( 'kenta_pagination_prev_next_type' ) )
							->setLabel( __( 'Previous & Next Type', 'kenta' ) )
							->setDefaultValue( 'icon' )
							->buttonsGroupView()
							->selectiveRefresh( '.kenta-pagination', 'kenta_show_posts_pagination', [
								'container_inclusive' => true,
							] )
							->setChoices( [
								'text' => __( 'Text', 'kenta' ),
								'icon' => __( 'Icon', 'kenta' ),
							] )
						,
						( new Condition() )
							->setCondition( [ 'kenta_pagination_prev_next_type' => 'icon' ] )
							->setControls( [
								( new Icons( 'kenta_pagination_prev_icon' ) )
									->setLabel( __( 'Previous Icon', 'kenta' ) )
									->selectiveRefresh( '.kenta-pagination', 'kenta_show_posts_pagination', [
										'container_inclusive' => true,
									] )
									->setDefaultValue( [
										'value'   => 'fas fa-arrow-left-long',
										'library' => 'fa-solid',
									] )
								,
								( new Icons( 'kenta_pagination_next_icon' ) )
									->setLabel( __( 'Next Icon', 'kenta' ) )
									->selectiveRefresh( '.kenta-pagination', 'kenta_show_posts_pagination', [
										'container_inclusive' => true,
									] )
									->setDefaultValue( [
										'value'   => 'fas fa-arrow-right-long',
										'library' => 'fa-solid',
									] )
								,
							] )
						,
						( new Condition() )
							->setCondition( [ 'kenta_pagination_prev_next_type' => 'text' ] )
							->setControls( [
								( new Text( 'kenta_pagination_prev_text' ) )
									->setLabel( __( 'Previous Text', 'kenta' ) )
									->selectiveRefresh( '.kenta-pagination', 'kenta_show_posts_pagination', [
										'container_inclusive' => true,
									] )
									->displayInline()
									->setDefaultValue( __( 'Prev', 'kenta' ) )
								,
								( new Text( 'kenta_pagination_next_text' ) )
									->setLabel( __( 'Next Text', 'kenta' ) )
									->selectiveRefresh( '.kenta-pagination', 'kenta_show_posts_pagination', [
										'container_inclusive' => true,
									] )
									->displayInline()
									->setDefaultValue( __( 'Next', 'kenta' ) )
								,
							] )
						,
						( new Separator() ),
						( new Toggle( 'kenta_pagination_disabled_button' ) )
							->setLabel( __( 'Show Disabled Buttons', 'kenta' ) )
							->selectiveRefresh( '.kenta-pagination', 'kenta_show_posts_pagination', [
								'container_inclusive' => true,
							] )
							->closeByDefault()
						,
						( new Toggle( 'kenta_pagination_scroll_reveal' ) )
							->setLabel( __( 'Enable Scroll Reveal', 'kenta' ) )
							->selectiveRefresh( '.kenta-pagination', 'kenta_show_posts_pagination', [
								'container_inclusive' => true,
							] )
							->openByDefault()
						,
					] )
				,
			];

			return apply_filters( 'kenta_pagination_general_controls', $controls );
		}

		/**
		 * @return array
		 */
		protected function getPaginationStyleControls() {
			$controls = [
				( new Typography( 'kenta_pagination_typography' ) )
					->setLabel( __( 'Typography', 'kenta' ) )
					->asyncCss( '.kenta-pagination', AsyncCss::typography() )
					->setDefaultValue( [
						'family'     => 'inherit',
						'fontSize'   => '0.875rem',
						'variant'    => '400',
						'lineHeight' => '1',
					] )
				,
				( new Separator() ),
				( new Condition() )
					->setCondition( [ 'kenta_pagination_type' => 'numbered|prev-next' ] )
					->setControls( [
						( new ColorPicker( 'kenta_pagination_button_color' ) )
							->setLabel( __( 'Text Color', 'kenta' ) )
							->asyncColors( '.kenta-pagination', [
								'initial' => '--kenta-pagination-initial-color',
								'active'  => '--kenta-pagination-active-color',
								'accent'  => '--kenta-pagination-accent-color',
							] )
							->addColor( 'initial', __( 'Text Initial', 'kenta' ), 'var(--kenta-accent-active)' )
							->addColor( 'active', __( 'Text Active', 'kenta' ), 'var(--kenta-base-color)' )
							->addColor( 'accent', __( 'Accent', 'kenta' ), 'var(--kenta-primary-color)' )
						,
						( new Border( 'kenta_pagination_button_border' ) )
							->setLabel( __( 'Border', 'kenta' ) )
							->displayInline()
							->asyncCss( '.kenta-pagination', AsyncCss::border( '--kenta-pagination-button-border' ) )
							->setDefaultBorder( 2, 'solid', 'var(--kenta-base-300)' )
						,
					] )
				,
				( new Condition() )
					->setCondition( [ 'kenta_pagination_type' => 'numbered|prev-next|load-more' ] )
					->setControls( [
						( new Slider( 'kenta_pagination_button_radius' ) )
							->setLabel( __( 'Radius', 'kenta' ) )
							->asyncCss( '.kenta-pagination', [ '--kenta-pagination-button-radius' => 'value' ] )
							->enableResponsive()
							->setDefaultUnit( 'px' )
							->setDefaultValue( '2px' )
							->setMin( 0 )
							->setMax( 100 )
						,
					] )
			];

			return apply_filters( 'kenta_pagination_style_controls', $controls );
		}
	}
}

