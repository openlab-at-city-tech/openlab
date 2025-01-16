<?php
/**
 * Post structure trait
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Border;
use LottaFramework\Customizer\Controls\CallToAction;
use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Filters;
use LottaFramework\Customizer\Controls\Layers;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Select;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Text;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Facades\AsyncCss;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! trait_exists( 'Kenta_Post_Structure' ) ) {

	/**
	 * Post structure functions
	 */
	trait Kenta_Post_Structure {

		use Kenta_Button_Controls;

		/**
		 * @return Layers
		 */
		protected function getPostElementsLayer( $id, $layer_id, $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'exclude'           => [],
				'selective-refresh' => [ null ],
				'selector'          => '',
				'value'             => [
					[ 'id' => 'categories', 'visible' => true ],
					[ 'id' => 'title', 'visible' => true ],
					[ 'id' => 'metas', 'visible' => true ],
					[ 'id' => 'thumbnail', 'visible' => true ],
					[ 'id' => 'excerpt', 'visible' => true ],
					[ 'id' => 'read-more', 'visible' => true ],
				],
				'title'             => [],
				'cats'              => [],
				'tags'              => [],
				'metas'             => [],
				'thumbnail'         => [],
				'read-more'         => [],
				'divider'           => [],
				'excerpt'           => [
					'length' => 20
				],
			] );

			$selective_refresh = $defaults['selective-refresh'];
			$layer             = ( new Layers( $id ) )->hideLabel()->setDefaultValue( $defaults['value'] );
			$layer->selectiveRefresh( ...$selective_refresh );

			$exclude = $defaults['exclude'];

			$layer_defaults = [ 'selective-refresh' => $selective_refresh, 'selector' => $defaults['selector'] ];

			if ( ! in_array( 'title', $exclude ) ) {
				$layer->addLayer( 'title', __( 'Title', 'kenta' ), $this->getTitleLayerControls( $layer_id, true, array_merge( $layer_defaults, $defaults['title'] ) ) );
			}
			if ( ! in_array( 'categories', $exclude ) ) {
				$layer->addLayer( 'categories', __( 'Categories', 'kenta' ), $this->getTaxonomyControls( $layer_id, '_cats', array_merge( $layer_defaults, $defaults['cats'] ) ) );
			}
			if ( ! in_array( 'tags', $exclude ) ) {
				$layer->addLayer( 'tags', __( 'Tags', 'kenta' ), $this->getTaxonomyControls( $layer_id, '_tags', array_merge( $layer_defaults, $defaults['tags'] ) ) );
			}
			if ( ! in_array( 'thumbnail', $exclude ) ) {
				$layer->addLayer( 'thumbnail', __( 'Thumbnail', 'kenta' ), $this->getThumbnailControls( $layer_id, array_merge( $layer_defaults, $defaults['thumbnail'] ) ) );
			}
			if ( ! in_array( 'excerpt', $exclude ) ) {
				$layer->addLayer( 'excerpt', __( 'Excerpt', 'kenta' ), $this->getExcerptControls( $layer_id, array_merge( $layer_defaults, $defaults['excerpt'] ) ) );
			}
			if ( ! in_array( 'read-more', $exclude ) ) {
				$layer->addLayer( 'read-more', __( 'Read More', 'kenta' ), $this->getReadMoreControls( $layer_id, array_merge( $layer_defaults, $defaults['read-more'] ) ) );
			}
			if ( ! in_array( 'metas', $exclude ) ) {
				$layer->addLayer( 'metas', __( 'Metas', 'kenta' ), $this->getMetasControls( $layer_id, array_merge( $layer_defaults, $defaults['metas'] ) ) );
			}
			if ( ! in_array( 'divider', $exclude ) ) {
				$layer->addLayer( 'divider', __( 'Divider', 'kenta' ), $this->getDividerControls( $layer_id, array_merge( $layer_defaults, $defaults['divider'] ) ) );
			}

			return $layer;
		}

		/**
		 * @return array
		 */
		protected function getTaxonomyControls( $id, $type = '', $defaults = [] ) {

			$defaults = wp_parse_args( $defaults, [
				'style'              => 'default',
				'text-initial'       => 'var(--kenta-primary-color)',
				'text-hover'         => 'var(--kenta-primary-active)',
				'badge-text-initial' => '#ffffff',
				'badge-text-hover'   => '#ffffff',
				'badge-bg-initial'   => 'var(--kenta-primary-color)',
				'badge-bg-hover'     => 'var(--kenta-primary-active)',
				'typography'         => [
					'family'     => 'inherit',
					'fontSize'   => '0.75rem',
					'variant'    => '700',
					'lineHeight' => '1.5'
				],
			] );

			$controls = [
				( new Select( 'kenta_' . $id . '_tax_style' . $type ) )
					->setLabel( __( 'Style', 'kenta' ) )
					->displayInline()
					->setDefaultValue( $defaults['style'] )
					->setChoices( [
						'default' => __( 'Default', 'kenta' ),
						'badge'   => __( 'Badge', 'kenta' ),
					] )
				,
			];

			return apply_filters( 'kenta_taxonomy_element_controls', $controls, $id, $type, $defaults );
		}

		/**
		 * @return array
		 */
		protected function getTitleLayerControls( $id, $link = true, $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'tag'        => 'h2',
				'typography' => [
					'family'     => 'inherit',
					'fontSize'   => [ 'desktop' => '1.25rem', 'tablet' => '1rem', 'mobile' => '1rem' ],
					'variant'    => '700',
					'lineHeight' => '1.5'
				],
				'initial'    => 'var(--kenta-accent-color)',
				'hover'      => 'var(--kenta-primary-color)',
			] );

			$controls = [
				( new Select( 'kenta_' . $id . '_title_tag' ) )
					->setLabel( __( 'Tag', 'kenta' ) )
					->selectiveRefresh( ...$defaults['selective-refresh'] )
					->setDefaultValue( $defaults['tag'] )
					->displayInline()
					->setChoices( [
						'h1' => __( 'H1', 'kenta' ),
						'h2' => __( 'H2', 'kenta' ),
						'h3' => __( 'H3', 'kenta' ),
						'h4' => __( 'H4', 'kenta' ),
						'h5' => __( 'H5', 'kenta' ),
						'h6' => __( 'H6', 'kenta' ),
					] )
				,
			];

			return apply_filters( 'kenta_title_element_controls', $controls, $id, $link, $defaults );
		}

		/**
		 * @return array
		 */
		protected function getThumbnailControls( $id, $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'full-width'    => 'yes',
				'fallback'      => 'yes',
				'height'        => '180px',
				'border-radius' => [
					'top'    => '0px',
					'right'  => '0px',
					'bottom' => '0px',
					'left'   => '0px',
					'linked' => true
				],
				'shadow'        => [
					'rgba(54,63,70,0.35)',
					'0px',
					'18px',
					'18px',
					'-15px',
				],
				'shadow-enable' => false
			] );

			$controls = [
				( new Select( 'kenta_' . $id . '_thumbnail_size' ) )
					->setLabel( __( 'Image Size', 'kenta' ) )
					->setDefaultValue( 'large' )
					->selectiveRefresh( ...$defaults['selective-refresh'] )
					->setChoices( kenta_image_size_options( false ) )
				,
				( new Slider( 'kenta_' . $id . '_thumbnail_height' ) )
					->setLabel( __( 'Height', 'kenta' ) )
					->asyncCss( $defaults['selector'] . ' .entry-thumbnail', [ 'height' => 'value' ] )
					->setUnits( [
						[ 'unit' => 'px', 'min' => 100, 'max' => 1000 ],
						[ 'unit' => '%', 'min' => 10, 'max' => 100 ],
					] )
					->setDefaultValue( $defaults['height'] )
				,
				( new Filters( 'kenta_' . $id . '_thumbnail_filter' ) )
					->setLabel( __( 'Css Filter', 'kenta' ) )
					->asyncCss( $defaults['selector'] . ' .entry-thumbnail', AsyncCss::filters() )
				,
				( new Toggle( 'kenta_' . $id . '_thumbnail_full_width' ) )
					->setLabel( __( 'Full Width', 'kenta' ) )
					->selectiveRefresh( ...$defaults['selective-refresh'] )
					->setDefaultValue( $defaults['full-width'] )
				,
				( new Toggle( 'kenta_' . $id . '_thumbnail_use_fallback' ) )
					->setLabel( __( 'Use Fallback Image', 'kenta' ) )
					->setDescription( __( 'If the current post does not have a featured image, then this image will be displayed.', 'kenta' ) )
					->selectiveRefresh( ...$defaults['selective-refresh'] )
					->setDefaultValue( $defaults['fallback'] )
				,
				( new CallToAction() )
					->setLabel( __( 'Edit Fallback Image', 'kenta' ) )
					->displayAsButton()
					->expandCustomize( 'kenta_single_post:kenta_post_featured_image' )
				,
			];

			return apply_filters( 'kenta_thumbnail_element_controls', $controls, $id, $defaults );
		}

		/**
		 * @return array
		 */
		protected function getExcerptControls( $id, $defaults = [] ) {
			$controls = [
				( new Radio( 'kenta_' . $id . '_excerpt_type' ) )
					->setDefaultValue( 'excerpt' )
					->selectiveRefresh( ...$defaults['selective-refresh'] )
					->hideLabel()
					->buttonsGroupView()
					->setChoices( [
						'excerpt' => __( 'Excerpt', 'kenta' ),
						'full'    => __( 'Full Post', 'kenta' ),
					] )
				,
				( new Condition() )
					->setCondition( [ 'kenta_' . $id . '_excerpt_type' => 'excerpt' ] )
					->setControls( [
						( new Slider( 'kenta_' . $id . '_excerpt_length' ) )
							->setLabel( __( 'Length', 'kenta' ) )
							->selectiveRefresh( ...$defaults['selective-refresh'] )
							->setMin( 10 )
							->setMax( 300 )
							->setDefaultUnit( false )
							->setDefaultValue( $defaults['length'] )
						,
						( new Separator() ),
						( new Text( 'kenta_' . $id . '_excerpt_more_text' ) )
							->setLabel( __( 'More Text', 'kenta' ) )
							->selectiveRefresh( ...$defaults['selective-refresh'] )
							->setDefaultValue( '...' )
						,
					] )
				,
			];

			return apply_filters( 'kenta_excerpt_element_controls', $controls, $id, $defaults );
		}

		/**
		 * @return array
		 */
		protected function getReadMoreControls( $id, $defaults ) {
			$defaults = wp_parse_args( $defaults, [
				'label'                => __( 'Read More', 'kenta' ),
				'show-arrow'           => 'yes',
				'arrow-dir'            => 'right',
				'arrow'                => [
					'library' => 'fa-solid',
					'value'   => 'fas fa-arrow-right',
				],
				'button-selector'      => $defaults['selector'] . ' .entry-read-more',
				'button-selective'     => $defaults['selective-refresh'],
				'button-css-selective' => 'kenta-global-selective-css',
			] );

			return array_merge(
				$this->getButtonContentControls( 'kenta_' . $id . '_read_more_', $defaults ),
				[ new Separator() ],
				$this->getButtonStyleControls( 'kenta_' . $id . '_read_more_', $defaults )
			);
		}

		/**
		 * @param $id
		 * @param array $defaults
		 *
		 * @return array
		 */
		protected function getMetasControls( $id, $defaults = [] ) {

			$defaults = wp_parse_args( $defaults, [
				'elements'   => [
					[ 'id' => 'byline', 'visible' => true ],
					[ 'id' => 'published', 'visible' => true ],
					[ 'id' => 'comments', 'visible' => true ],
				],
				'typography' => [
					'family'        => 'inherit',
					'fontSize'      => [ 'desktop' => '0.75rem', 'tablet' => '0.75rem', 'mobile' => '0.75rem' ],
					'variant'       => '400',
					'lineHeight'    => '1.5',
					'textTransform' => 'capitalize',
				],
				'initial'    => 'var(--kenta-accent-active)',
				'hover'      => 'var(--kenta-primary-color)',
				'style'      => 'default',
				'divider'    => 'divider-1',
			] );

			$controls = [
				( new Layers( 'kenta_' . $id . '_metas' ) )
					->hideLabel()
					->selectiveRefresh( ...$defaults['selective-refresh'] )
					->setDefaultValue( $defaults['elements'] )
					->addLayer( 'byline', __( 'Byline', 'kenta' ), apply_filters( 'kenta_byline_meta_controls', [], $id, $defaults ) )
					->addLayer( 'published', __( 'Published Date', 'kenta' ), apply_filters( 'kenta_published_meta_controls', [
						( new Toggle( 'kenta_' . $id . '_show_modified_date' ) )
							->setLabel( __( 'Show modified date', 'kenta' ) )
							->closeByDefault()
						,
						( new Text( 'kenta_' . $id . '_published_format' ) )
							->setLabel( __( 'Date Format', 'kenta' ) )
							->selectiveRefresh( ...$defaults['selective-refresh'] )
							->setDescription( sprintf(
							// translators: placeholder here means the actual URL.
								__( 'Date format %s instructions %s.', 'kenta' ),
								'<a href="https://wordpress.org/support/article/formatting-date-and-time/#format-string-examples" target="_blank">',
								'</a>'
							) )
							->setDefaultValue( 'M j, Y' )
						,
					], $id, $defaults ) )
					->addLayer( 'comments', __( 'Comments', 'kenta' ), apply_filters( 'kenta_comments_meta_controls', [], $id, $defaults ) )
				,
			];

			return apply_filters( 'kenta_metas_element_controls', $controls, $id, $defaults );
		}

		/**
		 * @return array
		 */
		protected function getDividerControls( $id, $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'full-width' => 'yes'
			] );

			return [
				( new Border( 'kenta_' . $id . '_divider' ) )
					->setLabel( __( 'Style', 'kenta' ) )
					->asyncCss( $defaults['selector'] . ' .entry-divider', AsyncCss::border( '--entry-divider' ) )
					->setDefaultBorder(
						1, 'solid', 'var(--kenta-base-300)'
					)
				,
				( new Toggle( 'kenta_' . $id . '_divider_full_width' ) )
					->setLabel( __( 'Full Width', 'kenta' ) )
					->selectiveRefresh( ...$defaults['selective-refresh'] )
					->setDefaultValue( $defaults['full-width'] )
				,
			];
		}
	}

}
