<?php

use LottaFramework\Customizer\Controls\Number;
use LottaFramework\Customizer\Controls\Section;
use LottaFramework\Customizer\Controls\Background;
use LottaFramework\Customizer\Controls\Select;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Controls\Text;
use LottaFramework\Facades\CZ;
use LottaFramework\Utils;

if ( ! class_exists( 'Kenta_Related_Posts_Extension' ) ) {

	/**
	 * Class for related posts extension
	 *
	 * @package Kenta
	 */
	class Kenta_Related_Posts_Extension {

		use Kenta_Article_Controls;
		use Kenta_Post_Card;

		/**
		 * Register hooks
		 */
		public function __construct() {
			add_filter( 'kenta_single_post_section_controls', [ $this, 'controls' ] );
			add_action( 'kenta_action_after_single_post', [ $this, 'render' ], 20 );
		}

		/**
		 * @param $controls
		 *
		 * @return mixed
		 */
		public function controls( $controls ) {
			$selective = [
				'.kenta-related-posts-container',
				[ $this, 'render' ],
				[
					'container_inclusive' => true
				]
			];

			$content_controls = apply_filters( 'kenta_filter_related_posts_content_controls', [
				( new Select( 'kenta_related_posts_criteria' ) )
					->setLabel( __( 'Related Criteria', 'kenta' ) )
					->selectiveRefresh( ...$selective )
					->setDefaultValue( 'category' )
					->setChoices( [
						'category' => __( 'Category', 'kenta' ),
						'tag'      => __( 'Tag', 'kenta' ),
						'author'   => __( 'Author', 'kenta' ),
					] )
				,
				( new Select( 'kenta_related_posts_sort' ) )
					->setLabel( __( 'Sort By', 'kenta' ) )
					->selectiveRefresh( ...$selective )
					->setDefaultValue( 'recent' )
					->setChoices( [
						'default' => __( 'Default', 'kenta' ),
						'recent'  => __( 'Recent', 'kenta' ),
						'random'  => __( 'Random', 'kenta' ),
						'comment' => __( 'Comment Count', 'kenta' ),
					] )
				,
				( new Number( 'kenta_related_posts_number' ) )
					->setLabel( __( 'Posts Count', 'kenta' ) )
					->selectiveRefresh( ...$selective )
					->setMin( 1 )
					->setMax( 20 )
					->setDefaultUnit( false )
					->setDefaultValue( 3 )
				,
				( new Separator() ),
				( new Text( 'kenta_related_posts_section_title' ) )
					->setLabel( __( 'Section Title', 'kenta' ) )
					->asyncText( '.kenta-related-posts-wrap .heading-content' )
					->setDefaultValue( __( 'Related Posts', 'kenta' ) )
				,
			] );

			$layout_controls = apply_filters( 'kenta_filter_related_posts_layout_controls', array_merge(
				[
					$this->getPostElementsLayer( 'kenta_related_posts_card_structure', 'related_posts', [
						'selective-refresh' => $selective,
						'selector'          => '.kenta-related-posts-wrap .card',
						'value'             => [
							[ 'id' => 'thumbnail', 'visible' => true ],
							[ 'id' => 'categories', 'visible' => false ],
							[ 'id' => 'title', 'visible' => true ],
							[ 'id' => 'excerpt', 'visible' => true ],
							[ 'id' => 'metas', 'visible' => true ],
							[ 'id' => 'divider', 'visible' => false ],
							[ 'id' => 'read-more', 'visible' => false ],
						],
						'thumbnail'         => [ 'full-width' => 'no', 'height' => '128px' ],
						'title'             => [
							'tag'        => 'h4',
							'typography' => [
								'family'     => 'inherit',
								'fontSize'   => [ 'desktop' => '1rem', 'tablet' => '1rem', 'mobile' => '1rem' ],
								'variant'    => '700',
								'lineHeight' => '1.5'
							],
							'initial'    => 'var(--kenta-accent-color)',
							'hover'      => 'var(--kenta-primary-color)',
						],
						'cats'              => [],
						'tags'              => [],
						'metas'             => [],
						'divider'           => [],
						'excerpt'           => [ 'length' => 10 ],
					] ),
					( new Separator() ),
					( new Slider( 'kenta_related_posts_grid_columns' ) )
						->setLabel( __( 'Grid Columns', 'kenta' ) )
						->enableResponsive()
						->setMin( 1 )
						->setMax( 4 )
						->setDefaultUnit( false )
						->setDefaultValue( [
							'desktop' => 3,
							'tablet'  => 2,
							'mobile'  => 1,
						] )
					,
					( new Slider( 'kenta_related_posts_grid_items_gap' ) )
						->setLabel( __( 'Items Gap', 'kenta' ) )
						->enableResponsive()
						->setMin( 0 )
						->setMax( 50 )
						->setDefaultUnit( 'px' )
						->setDefaultValue( '24px' )
					,
					( new Separator() )
				],
				$this->getCardContentControls( 'kenta_related_posts_', [
					'selector'          => '.kenta-related-posts-wrap .card',
					'content-spacing'   => '0px',
					'scroll-reveal'     => 'no',
					'thumbnail-spacing' => '12px',
				] )
			) );

			$style_controls = apply_filters( 'kenta_filter_related_posts_style_controls',
				$this->getCardStyleControls( 'kenta_related_posts_', [
					'preset'    => 'ghost',
					'selective' => 'kenta-global-selective-css',
				] )
			);

			$controls[] = ( new Section( 'kenta_post_related_posts' ) )
				->setLabel( __( 'Related Posts', 'kenta' ) )
				->enableSwitch()
				->setControls( [
					( new Tabs() )
						->setActiveTab( 'content' )
						->addTab( 'content', __( 'Content', 'kenta' ), $content_controls )
						->addTab( 'layout', __( 'Layout', 'kenta' ), $layout_controls )
						->addTab( 'style', __( 'Style', 'kenta' ), $style_controls )
					,
				] );

			return $controls;
		}

		/**
		 * Render related posts
		 */
		public function render() {
			$current = get_post();

			if ( ! CZ::checked( 'kenta_post_related_posts' ) || ! $current ) {
				return;
			}

			$args = [
				'post_type'           => $current->post_type,
				'ignore_sticky_posts' => 0,
				'post__not_in'        => array( get_the_ID() ),
				'posts_per_page'      => absint( CZ::get( 'kenta_related_posts_number' ) ),
			];

			$sort     = CZ::get( 'kenta_related_posts_sort' );
			$criteria = CZ::get( 'kenta_related_posts_criteria' );

			if ( $criteria === 'category' ) {
				$args['category__in'] = wp_get_post_categories( get_the_ID(), [ 'fields' => 'ids' ] );
			} elseif ( $criteria === 'tag' ) {
				$args['tag__in'] = wp_get_post_tags( get_the_ID(), [ 'fields' => 'ids' ] );
			} else if ( $criteria === 'author' ) {
				$args['author'] = isset( $current->post_author ) ? $current->post_author : 0;
			}

			if ( $sort !== 'default' ) {
				$orderby_map = [
					'random'  => 'rand',
					'recent'  => 'post_date',
					'comment' => 'comment_count'
				];

				if ( isset( $orderby_map[ $sort ] ) ) {
					$args['orderby'] = $orderby_map[ $sort ];
				}
			}

			$related_query = new \WP_Query( $args );

			if ( ! $related_query->have_posts() ) {
				return;
			}

			$attrs = [
				'class' => 'kenta-max-w-content has-global-padding mx-auto',
			];

			if ( is_customize_preview() ) {
				$attrs['class']                  = $attrs['class'] . ' kenta-related-posts-container';
				$attrs['data-shortcut']          = 'border';
				$attrs['data-shortcut-location'] = 'kenta_single_post:kenta_post_related_posts';
			}
			?>
            <div <?php \LottaFramework\Utils::print_attribute_string( $attrs ); ?>>
                <div class="kenta-related-posts-wrap kenta-heading kenta-heading-style-1">
                    <h3 class="heading-content uppercase my-gutter"><?php echo esc_html( CZ::get( 'kenta_related_posts_section_title' ) ) ?></h3>
                    <div class="flex flex-wrap kenta-related-posts-list">
						<?php while ( $related_query->have_posts() ): $related_query->the_post(); ?>
                            <div class="card-wrapper">
                                <article data-card-layout="archive-grid" class="<?php Utils::the_clsx(
									get_post_class( 'card overflow-hidden h-full', get_the_ID() ),
									[ 'kenta-scroll-reveal' => CZ::checked( 'kenta_related_posts_card_scroll_reveal' ) ]
								); ?>">
									<?php
									kenta_post_structure( 'related_posts', CZ::layers( 'kenta_related_posts_card_structure' ), CZ::layers( 'kenta_related_posts_metas' ), [
										'title_link'   => true,
										'title_tag'    => CZ::get( 'kenta_related_posts_title_tag' ),
										'excerpt_type' => CZ::get( 'kenta_related_posts_excerpt_type' ),
									] );
									?>
                                </article>
                            </div>
						<?php endwhile; ?>
						<?php wp_reset_postdata(); ?>
                    </div>
                </div>
            </div>
			<?php
		}
	}
}

new Kenta_Related_Posts_Extension();
