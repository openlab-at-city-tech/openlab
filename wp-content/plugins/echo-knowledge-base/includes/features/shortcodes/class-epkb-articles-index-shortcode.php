<?php

/**
 * Shortcode - Lists all KB articles and groups them by Letter, just like an index page.
 *
 * @copyright   Copyright (c) 2018, Echo Plugins
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Articles_Index_Shortcode {

	public function __construct() {
		add_shortcode( 'epkb-articles-index-directory', array( $this, 'output_shortcode' ) );
	}

	/**
	 * Shortcode callback.
	 *
	 * @param array $attributes Shortcode attributes.
	 * @return string HTML output.
	 */
	public function output_shortcode( $attributes ) {
		return self::render_directory( $attributes );
	}

	/**
	 * Render the articles index directory. Shared between shortcode and block.
	 *
	 * @param array $attributes {
	 *     Rendering parameters.
	 *
	 *     @type string $title Title for the directory.
	 *     @type int    $kb_id Knowledge base ID.
	 *     @type bool   $is_block Whether called from block context (skips shortcode CSS enqueue).
	 * }
	 * @return string HTML output.
	 */
	public static function render_directory( $attributes ) {

		// Only enqueue shortcodes CSS when used as shortcode, not block
		if ( empty( $attributes['is_block'] ) ) {
			wp_enqueue_style( 'epkb-shortcodes' );
		}

		// allows to adjust the widget title
		$title = empty( $attributes['title'] ) ? '' : esc_html( wp_strip_all_tags( trim( $attributes['title'] ) ) );
		$title = ( empty( $title ) ? esc_html__( 'Indexed Articles', 'echo-knowledge-base' ) : esc_html( $title ) );

		// get add-on configuration
		$kb_id = empty( $attributes['kb_id'] ) ? EPKB_Utilities::get_eckb_kb_id() : $attributes['kb_id'];
		$kb_id = EPKB_Utilities::sanitize_int( $kb_id, EPKB_KB_Config_DB::DEFAULT_KB_ID );

		$indexed_articles_list = self::get_indexed_articles_list( $kb_id );
		if ( empty( $indexed_articles_list ) ) {
			ob_start(); ?>
			<div id="epkb-article-index-dir-container">
				<div class="epkb-aid__body-container"><?php
					echo esc_html__( 'Articles coming Soon', 'echo-knowledge-base' ); ?>
				</div>
			</div><?php
			return ob_get_clean();
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		$setting_names = EPKB_Core_Utilities::get_style_setting_name( $kb_config['kb_main_page_layout'] );
		$article_color_escaped = EPKB_Utilities::get_inline_style( 'color:: ' . $setting_names['article_font_color'], $kb_config );
		$icon_color_escaped = EPKB_Utilities::get_inline_style( 'color:: ' . $setting_names['article_icon_color'], $kb_config );
		$icon_class = 'ep_font_icon_document';

		// custom article list icon
		if ( EPKB_Utilities::is_elegant_layouts_enabled() && has_filter( 'eckb_article_list_icon_filter' ) ) {
			$result = apply_filters( 'eckb_article_list_icon_filter', 0, array( $kb_config['id'], $kb_config['kb_main_page_layout'] ) );
			if ( ! empty( $result['icon'] ) ) {
				$icon_class = $result['icon'];
			}
		}

		// DISPLAY INDEXED ARTICLES
		ob_start(); ?>
		<div id="epkb-article-index-dir-container">

            <div class="epkb-aid__header-container">
                <h2 class="epkb-aid__header__title" aria-label="<?php echo esc_attr( $title ); ?>"><?php 
					echo esc_html( $title ); ?>
				</h2>
            </div>

            <div class="epkb-aid__body-container">                <?php
	            foreach ( $indexed_articles_list as $indexed_result ) { ?>

    				
                    <section id="epkb-aid__section-<?php echo esc_attr( $indexed_result['index'] ); ?>" class="epkb-aid__section-container" role="contentinfo"
						aria-label="<?php
							// translators: %s is the letter for the article index section
							printf( esc_attr__( 'Article List for Letter %s', 'echo-knowledge-base' ), esc_html( $indexed_result['index'] ) ); ?>">

                        <div class="epkb-aid-section__header">
                            <div class="epkb-aid-section__header__title"><?php 
								echo esc_html( $indexed_result['index'] ); ?>
							</div>
                        </div>

                        <div class="epkb-aid-section__body">
                            <ul class="epkb-aid-section__body__list-container">  <?php
                                foreach ( $indexed_result['articles'] as $article_id => $article_title ) {

									if ( ! EPKB_Utilities::is_article_allowed_for_current_user( $article_id ) ) {
										continue;
									}

                                    $article_url = get_permalink( $article_id );
                                    if ( empty( $article_url ) || is_wp_error( $article_url ) ) {
                                        continue;
                                    }  ?>
                                    <li id="epkb-aid-article-<?php echo esc_attr( $article_id ); ?>" class="epkb-aid-list__item">
                                        <a href="<?php echo esc_url( $article_url ); ?>" <?php echo $article_color_escaped; ?>>
                                            <span class="epkb-aid-list__item__icon" <?php echo $icon_color_escaped; ?> >
                                                <span aria-hidden="true" class="epkbfa epkb-aid-article-icon <?php echo esc_attr( $icon_class ); ?>"></span>
                                            </span>
                                            <span class="epkb-aid-list__item__text"><?php 
												echo esc_html( $article_title ); ?>
											</span>
                                        </a>
                                    </li>  <?php
                                } ?>
                            </ul>
                        </div>

                    </section>                <?php
				} ?>
            </div>

		</div>  <?php

		return ob_get_clean();
	}

	/**
	 * Get sorted and indexed KB articles
	 *
	 * @param $kb_id
	 *
	 * @return array
	 */
	private static function get_indexed_articles_list( $kb_id ) {

		// name for non-alphabetic indexes
		$other_index_char = esc_html__( 'Other', 'echo-knowledge-base' );

		$articles_list = self::get_articles_list( $kb_id );

        // Sort results alphabetically excluding all special characters
		uasort( $articles_list, function ( $a, $b ) {

			// get first letter - if article starts with non-letter, then set to empty
			$is_first_a_letter = preg_match( '/[\p{L}]/u', mb_substr( trim( $a ), 0, 1 ) );
			$is_first_b_letter = preg_match( '/[\p{L}]/u', mb_substr( trim( $b ), 0, 1 ) );

			// CASE: if only one of the articles starts with letter, then it always has higher priority
			if ( empty( $is_first_a_letter ) && ! empty( $is_first_b_letter ) ) {
				return 1;
			}
			if ( ! empty( $is_first_a_letter ) && empty( $is_first_b_letter ) ) {
				return 0;
			}

			// CASE: if both articles start with letter or both articles start with non-letter character, then sort alphabetically by first letter
			$a = self::clean_string_for_alphabetically_sorting( $a );
			$b = self::clean_string_for_alphabetically_sorting( $b );

			if ( $a == $b ) {
				return 0;
			}

			return ( $a < $b ) ? -1 : 1;
		} );

		$indexed_articles_list = array();

		foreach ( $articles_list as $article_id => $article ) {

			// make sure we have any character in the article title after trim
			$article = trim( $article );
			if ( empty( $article ) ) {
				continue;
			}

			// get first letter; if no letters found, then set to default index
			$index_char = mb_substr( self::clean_string_for_alphabetically_sorting( $article ), 0, 1 );
			if ( empty( $index_char ) ) {
				$index_char = $other_index_char;
			}

			$index_key = array_search( $index_char, array_column( $indexed_articles_list , 'index') );
			if ( false === $index_key ) {
				$indexed_articles_list[] = array(
					'index'    => $index_char,
					'articles' => array()
				);
				$index_key = array_key_last( $indexed_articles_list );
			}

			$indexed_articles_list[$index_key]['articles'][$article_id] = $article;
		}

		// move 'Other' to the end
		$other_index_key = array_search( $other_index_char, array_column( $indexed_articles_list , 'index') );
        if ( false !== $other_index_key ) {
	        $indexed_articles_list[] = $indexed_articles_list[$other_index_key];
	        unset( $indexed_articles_list[$other_index_key] );
        }

		return $indexed_articles_list;
	}

	/**
	 * Get all KB articles
	 *
	 * @param $kb_id
	 *
	 * @return array
	 */
	private static function get_articles_list( $kb_id ) {

		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );

		$articles_list = array();
		foreach ( $articles_seq_data as $category_id => $category_articles ) {
			foreach ( $category_articles as $post_id => $article ) {
				if ( $post_id > 1 && ! empty( $article ) ) {
					$articles_list[$post_id] = $article;
				}
			}
		}

		return $articles_list;
	}

	/**
     * Clean string, remove all special characters, numbers from beginning, leave alphabetic letters only
     *
	 * @param $string
	 *
	 * @return string
	 */
    private static function clean_string_for_alphabetically_sorting( $string ) {

	    $string = mb_strtoupper( trim( $string ) );

	    // convert string to chars array
	    $chars = preg_split('//u', $string, -1 );
	    $chars = is_array( $chars ) ? $chars : array();

	    $result = '';
	    foreach ( $chars as $char ) {
		    // if alphabetic letter or numbers in the middle
		    if ( preg_match( '/[\p{L}]/u', $char ) || ( preg_match( '/[\p{N}]/u', $char ) && strlen( $result ) > 0 ) ) {
	            $result .= $char;
		    }
	    }

	    return $result;
    }
}
