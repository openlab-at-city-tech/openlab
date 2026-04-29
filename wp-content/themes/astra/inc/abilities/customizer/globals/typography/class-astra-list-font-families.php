<?php
/**
 * List Font Families Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_List_Font_Families
 */
class Astra_List_Font_Families extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/list-font-family';
		$this->label       = __( 'List Available Font Families', 'astra' );
		$this->description = __( 'Lists the most popular and frequently used Google Font families available for use. Returns curated lists of trending fonts with categories and variants.', 'astra' );
		$this->category    = 'astra';
	}

	/**
	 * Get input schema.
	 *
	 * @return array
	 */
	public function get_input_schema() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'limit'        => array(
					'type'        => 'integer',
					'description' => 'Maximum number of fonts to return. Default is 20.',
					'default'     => 20,
					'minimum'     => 1,
					'maximum'     => 100,
				),
				'category'     => array(
					'type'        => 'string',
					'description' => 'Filter fonts by category.',
					'enum'        => array( 'all', 'sans-serif', 'serif', 'display', 'handwriting', 'monospace' ),
					'default'     => 'all',
				),
				'search'       => array(
					'type'        => 'string',
					'description' => 'Search for fonts by name.',
					'default'     => '',
				),
				'popular_only' => array(
					'type'        => 'boolean',
					'description' => 'Return only the most popular and commonly used fonts.',
					'default'     => true,
				),
			),
			'required'   => array(),
		);
	}

	/**
	 * Get output schema.
	 *
	 * @return array
	 */
	public function get_output_schema() {
		return $this->build_output_schema(
			array(
				'fonts'      => array(
					'type'        => 'array',
					'description' => 'List of font families.',
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'name'     => array(
								'type'        => 'string',
								'description' => 'Font family name.',
							),
							'category' => array(
								'type'        => 'string',
								'description' => 'Font category (sans-serif, serif, display, handwriting, monospace).',
							),
							'variants' => array(
								'type'        => 'array',
								'items'       => array( 'type' => 'string' ),
								'description' => 'Available font weight variants.',
							),
						),
					),
				),
				'total'      => array(
					'type'        => 'integer',
					'description' => 'Total number of fonts returned.',
				),
				'statistics' => array(
					'type'        => 'object',
					'description' => 'Font count statistics by category.',
				),
				'filters'    => array(
					'type'        => 'object',
					'description' => 'Applied filter values.',
				),
			)
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'list available font options',
			'show available fonts',
			'what fonts can I use',
			'list popular Google Fonts',
			'show font families',
			'list sans-serif fonts',
			'show serif fonts',
			'list monospace fonts',
			'search for fonts',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$limit        = isset( $args['limit'] ) ? absint( $args['limit'] ) : 20;
		$category     = isset( $args['category'] ) ? sanitize_text_field( $args['category'] ) : 'all';
		$search       = isset( $args['search'] ) ? sanitize_text_field( $args['search'] ) : '';
		$popular_only = isset( $args['popular_only'] ) ? (bool) $args['popular_only'] : true;

		$limit = min( $limit, 100 );

		if ( $popular_only ) {
			$fonts = $this->get_popular_fonts();
		} else {
			$fonts = $this->get_all_google_fonts();
		}

		if ( 'all' !== $category ) {
			$fonts = array_filter(
				$fonts,
				static function ( $font ) use ( $category ) {
					return isset( $font['category'] ) && $font['category'] === $category;
				}
			);
		}

		if ( ! empty( $search ) ) {
			$search_lower = strtolower( $search );
			$fonts        = array_filter(
				$fonts,
				static function ( $font ) use ( $search_lower ) {
					return strpos( strtolower( $font['name'] ), $search_lower ) !== false;
				}
			);
		}

		$fonts = array_slice( $fonts, 0, $limit );

		$category_stats = array();
		foreach ( $fonts as $font ) {
			$cat = $font['category'];
			if ( ! isset( $category_stats[ $cat ] ) ) {
				$category_stats[ $cat ] = 0;
			}
			$category_stats[ $cat ]++;
		}

		return Astra_Abilities_Response::success(
			/* translators: %d: number of font families */
			sprintf( __( 'Found %d font families.', 'astra' ), count( $fonts ) ),
			array(
				'fonts'      => array_values( $fonts ),
				'total'      => count( $fonts ),
				'statistics' => array(
					'by_category'  => $category_stats,
					'total_fonts'  => count( $fonts ),
					'popular_only' => $popular_only,
				),
				'filters'    => array(
					'category'     => $category,
					'search'       => $search,
					'limit'        => $limit,
					'popular_only' => $popular_only,
				),
			)
		);
	}

	/**
	 * Get the most popular Google Fonts.
	 *
	 * @return array Popular fonts array.
	 */
	private function get_popular_fonts() {
		return array(
			array(
				'name'     => 'Inter',
				'category' => 'sans-serif',
				'variants' => array( '100', '200', '300', '400', '500', '600', '700', '800', '900' ),
			),
			array(
				'name'     => 'Roboto',
				'category' => 'sans-serif',
				'variants' => array( '100', '300', '400', '500', '700', '900' ),
			),
			array(
				'name'     => 'Open Sans',
				'category' => 'sans-serif',
				'variants' => array( '300', '400', '500', '600', '700', '800' ),
			),
			array(
				'name'     => 'Lato',
				'category' => 'sans-serif',
				'variants' => array( '100', '300', '400', '700', '900' ),
			),
			array(
				'name'     => 'Montserrat',
				'category' => 'sans-serif',
				'variants' => array( '100', '200', '300', '400', '500', '600', '700', '800', '900' ),
			),
			array(
				'name'     => 'Poppins',
				'category' => 'sans-serif',
				'variants' => array( '100', '200', '300', '400', '500', '600', '700', '800', '900' ),
			),
			array(
				'name'     => 'Raleway',
				'category' => 'sans-serif',
				'variants' => array( '100', '200', '300', '400', '500', '600', '700', '800', '900' ),
			),
			array(
				'name'     => 'Nunito',
				'category' => 'sans-serif',
				'variants' => array( '200', '300', '400', '500', '600', '700', '800', '900' ),
			),
			array(
				'name'     => 'Ubuntu',
				'category' => 'sans-serif',
				'variants' => array( '300', '400', '500', '700' ),
			),
			array(
				'name'     => 'Playfair Display',
				'category' => 'serif',
				'variants' => array( '400', '500', '600', '700', '800', '900' ),
			),
			array(
				'name'     => 'Merriweather',
				'category' => 'serif',
				'variants' => array( '300', '400', '700', '900' ),
			),
			array(
				'name'     => 'PT Serif',
				'category' => 'serif',
				'variants' => array( '400', '700' ),
			),
			array(
				'name'     => 'Lora',
				'category' => 'serif',
				'variants' => array( '400', '500', '600', '700' ),
			),
			array(
				'name'     => 'Bebas Neue',
				'category' => 'sans-serif',
				'variants' => array( '400' ),
			),
			array(
				'name'     => 'Oswald',
				'category' => 'sans-serif',
				'variants' => array( '200', '300', '400', '500', '600', '700' ),
			),
			array(
				'name'     => 'Source Sans Pro',
				'category' => 'sans-serif',
				'variants' => array( '200', '300', '400', '600', '700', '900' ),
			),
			array(
				'name'     => 'Manrope',
				'category' => 'sans-serif',
				'variants' => array( '200', '300', '400', '500', '600', '700', '800' ),
			),
			array(
				'name'     => 'DM Sans',
				'category' => 'sans-serif',
				'variants' => array( '400', '500', '700' ),
			),
			array(
				'name'     => 'Work Sans',
				'category' => 'sans-serif',
				'variants' => array( '100', '200', '300', '400', '500', '600', '700', '800', '900' ),
			),
			array(
				'name'     => 'Fira Sans',
				'category' => 'sans-serif',
				'variants' => array( '100', '200', '300', '400', '500', '600', '700', '800', '900' ),
			),
			array(
				'name'     => 'Roboto Mono',
				'category' => 'monospace',
				'variants' => array( '100', '200', '300', '400', '500', '600', '700' ),
			),
			array(
				'name'     => 'IBM Plex Sans',
				'category' => 'sans-serif',
				'variants' => array( '100', '200', '300', '400', '500', '600', '700' ),
			),
			array(
				'name'     => 'Quicksand',
				'category' => 'sans-serif',
				'variants' => array( '300', '400', '500', '600', '700' ),
			),
			array(
				'name'     => 'Dancing Script',
				'category' => 'handwriting',
				'variants' => array( '400', '500', '600', '700' ),
			),
			array(
				'name'     => 'Pacifico',
				'category' => 'handwriting',
				'variants' => array( '400' ),
			),
		);
	}

	/**
	 * Get all available Google Fonts from Astra.
	 *
	 * @return array All Google Fonts.
	 */
	private function get_all_google_fonts() {
		if ( ! class_exists( 'Astra_Font_Families' ) ) {
			return $this->get_popular_fonts();
		}

		$fonts        = array();
		$google_fonts = Astra_Font_Families::get_google_fonts();

		foreach ( $google_fonts as $font_name => $font_data ) {
			$fonts[] = array(
				'name'     => $font_name,
				'category' => isset( $font_data[1] ) ? $font_data[1] : 'sans-serif',
				'variants' => isset( $font_data[0] ) ? $font_data[0] : array( '400' ),
			);
		}

		return $fonts;
	}
}

Astra_List_Font_Families::register();
