<?php
defined('ABSPATH') || die;

/**
 * Extract text from html string
 *
 * @param string  $html   HTML string to extract
 * @param integer $length Length of the string will return
 *
 * @return string Text that has been extracted
 */
function advgbExtractHtml($html, $length)
{
    if (!trim($html)) {
        return '';
    }

    $html = <<<HTML
$html
HTML;

    $dom = new DOMDocument();

    libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

    $scripts = $dom->getElementsByTagName('script');
    $styles = $dom->getElementsByTagName('style');
    $remove = array();

    foreach ($scripts as $item) {
        $remove[] = $item;
    }

    foreach ($styles as $item) {
        $remove[] = $item;
    }

    foreach ($remove as $item) {
        $item->parentNode->removeChild($item);
    }

    $html = $dom->saveHTML();
    $text = strip_tags($html);
    $text = trim(preg_replace('/\s\s+/', ' ', $text));

    if (!$text) {
        return '';
    }

	$text = mb_convert_encoding($text, 'UTF-8','HTML-ENTITIES');
	$text = mb_substr($text, 0, $length, 'UTF-8');

    return $text;
}

/**
 * Render content for Content Display block
 *
 * @param array $attributes Attributes of the block
 *
 * @return null
 */
function advgbRenderBlockRecentPosts($attributes)
{
	global $post;

	$categories = empty($attributes['categories'])? array() :$attributes['categories'];
	if ( ! empty( $categories ) ) {
		$categories = array_column( $categories, 'id' );
	}
	if(isset($attributes['category']) && !empty($attributes['category'])){
		$categories = $attributes['category'];
	}

	$tax_query = [];
	if ( !empty($attributes['tags'] ) ){
		$tax_query = array(
				array(
					'taxonomy' => 'post_tag',
					'field' => 'name',
					'terms' => $attributes['tags'],
					'operator' => 'IN',
				),
			);
	}

	$orderBy = empty($attributes['orderBy'])?'date':$attributes['orderBy'];

	// 'id' in https://developer.wordpress.org/rest-api/reference/posts/#list-posts
	// BUT
	// 'ID' in https://developer.wordpress.org/reference/classes/wp_query/parse_query/
	if ( $orderBy === 'id' ) {
		$orderBy = 'ID';
	}

	// if multiple authors, use the first author in the list.
	if ( $orderBy === 'author' ) {
		advgbMultipleAuthorSort();
	}

	$post_type = isset($attributes['postType']) ? $attributes['postType'] : 'post';
	$args = array(
			'post_type' => $post_type,
            'numberposts' => empty($attributes['numberOfPosts'])?8:$attributes['numberOfPosts'],
            'post_status' => 'publish',
            'order' => empty($attributes['order'])?'desc':$attributes['order'],
            'orderby' => $orderBy,
            'suppress_filters' => false,
        );

    if( isset( $attributes['exclude'] ) && ! empty( $attributes['exclude'] ) ) {
        $args['post__not_in'] = advgbGetPostIdsForTitles( $attributes['exclude'], $post_type );
    }

    if( isset( $attributes['excludeCurrentPost'] ) && $attributes['excludeCurrentPost'] ) {
        $args['post__not_in'] = isset( $args['post__not_in'] ) ? array_merge( $args['post__not_in'], array( $post->ID ) ) : array( $post->ID );
    }

	if( isset( $attributes['taxonomies'] ) && ! empty( $attributes['taxonomies'] ) ) {
		foreach( $attributes['taxonomies'] as $slug => $terms ) {
			if ( count( $terms ) > 0 ) {
				$tax_query[] = array(
						'taxonomy' => $slug,
						'field' => 'name',
						'terms' => $terms,
						'include_children' => false,
						'operator' => 'IN',
				);
			}
		}
	}

	// use tax for anything but pages...
	if ( ! in_array( $post_type, array( 'page' ), true ) ) {
		$args = wp_parse_args( $args, array(
            'category__in' => is_array( $categories ) ? array_map( 'intval', $categories ) : $categories,
            'tax_query' => $tax_query,
		) );
	}

    $recent_posts = wp_get_recent_posts( apply_filters( 'advgb_get_recent_posts_args', $args, $attributes, $post_type ), OBJECT );

    $saved_settings    = get_option('advgb_settings');
    $default_thumb     = plugins_url('assets/blocks/recent-posts/recent-post-default.png', ADVANCED_GUTENBERG_PLUGIN);
    $rp_default_thumb  = isset($saved_settings['rp_default_thumb']) ? $saved_settings['rp_default_thumb'] : array('url' => $default_thumb, 'id' => 0);

    $postHtml = '';

    if (!empty($recent_posts)) {
        foreach ($recent_posts as $key=>$post) {
            $postThumbID = get_post_thumbnail_id($post->ID);
            $outputImage = advgbCheckImageStatus( $attributes, $key ) && ( $postThumbID || $attributes['enablePlaceholderImage'] );

            $postHtml .= '<article class="advgb-recent-post' . ( $outputImage ? '' : ' advgb-recent-post--no-image' ) . '">';

            if ( $outputImage ) {
                $postThumb = '<img src="' . $rp_default_thumb['url'] . '" />';
                $postThumbCaption = '';
                if ($postThumbID) {
                    $postThumb = wp_get_attachment_image($postThumbID, 'large');
                    if( get_the_post_thumbnail_caption( $post->ID ) && $attributes['displayFeaturedImageCaption']) {
                        $postThumbCaption = sprintf(
                            '<span class="advgb-post-caption">%1$s</span>',
                            get_the_post_thumbnail_caption( $post->ID )
                        );
                    } else {
                        $postThumbCaption = '';
                    }
                } else {
                    if ($rp_default_thumb['id']) {
                        $postThumb = wp_get_attachment_image($rp_default_thumb['id'], 'large');
                    }
                }

                $postHtml .= sprintf(
                    '<div class="advgb-post-thumbnail"><a href="%1$s">%2$s%3$s</a></div>',
                    get_permalink($post->ID),
                    $postThumb,
                    $postThumbCaption
                );
            } elseif ( ($attributes['postView'] === 'frontpage' && $attributes['frontpageStyle'] === 'headline') || ($attributes['postView'] === 'slider' && $attributes['sliderStyle'] === 'headline') ) {
                $postHtml .= sprintf(
                    '<div class="advgb-post-thumbnail advgb-post-thumbnail-no-image"><a href="%1$s"></a></div>',
                    get_permalink($post->ID)
                );
            } else {
                // Nothing to do here
            }

            $postHtml .= '<div class="advgb-post-wrapper">';

            $postHtml .= sprintf(
                '<h2 class="advgb-post-title"><a href="%1$s">%2$s</a></h2>',
                get_permalink($post->ID),
                get_the_title($post->ID)
            );

            if (isset($attributes['textAfterTitle']) && !empty($attributes['textAfterTitle'])) {
				$postHtml .= sprintf( '<div class="advgb-text-after-title">%s</div>', wp_kses_post( $attributes['textAfterTitle'] ) );
			}

            $postHtml .= '<div class="advgb-post-info">';

            if (isset($attributes['displayAuthor']) && $attributes['displayAuthor']) {
				$coauthors = advgbGetCoauthors( array( 'id' => $post->ID ) );
				if ( ! empty( $coauthors ) ) {
					$index = 0;
					foreach ( $coauthors as $coauthor ) {
						$postHtml .= sprintf(
							'<a href="%1$s" class="advgb-post-author" target="_blank">%2$s</a>',
							$coauthor['link'],
							$coauthor['display_name']
						);
						if ( $index++ < count( $coauthors ) - 1 ) {
							$postHtml .= '<span>, </span>';
						}
					}
				} else {
					$postHtml .= sprintf(
						'<a href="%1$s" class="advgb-post-author" target="_blank">%2$s</a>',
						get_author_posts_url($post->post_author),
						get_the_author_meta('display_name', $post->post_author)
					);
				}
            }

            $postDate = isset($attributes['displayDate']) && $attributes['displayDate'] ? 'created' : (isset($attributes['postDate']) ? $attributes['postDate'] : 'hide');
            $postDateFormat = isset($attributes['postDateFormat']) ? $attributes['postDateFormat'] : '';
            $displayTime = isset($attributes['displayTime']) && $attributes['displayTime'];
			$postDateDisplay = null;

            if ( $postDate !== 'hide' ) {
                $format = $displayTime ? ( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) : get_option( 'date_format' );

                if ( $postDateFormat === 'absolute' ) {
                    if ( $postDate === 'created' ) {
                        $postDateDisplay = __( 'Posted on', 'advanced-gutenberg') . ' ' . get_the_date( $format, $post->ID);
                    } else {
                        $postDateDisplay = __( 'Updated on', 'advanced-gutenberg') . ' ' . get_the_modified_date( $format, $post->ID);
                    }
                } else {
                    // Relative date format
                    if ( $postDate === 'created' ) {
                        $postDateDisplay = __( 'Posted', 'advanced-gutenberg') . ' ' . human_time_diff( get_the_date( 'U', $post->ID ) ) . ' ' . __( 'ago', 'advanced-gutenberg');
                    } else {
                        $postDateDisplay = __( 'Updated', 'advanced-gutenberg') . ' ' .human_time_diff( get_the_modified_date( 'U', $post->ID ) ) . ' ' . __( 'ago', 'advanced-gutenberg');
                    }
                }
            }

            if ( ! empty( $postDateDisplay ) ) {
                $postHtml .= sprintf(
                    '<span class="advgb-post-datetime">%1$s</span>',
                    $postDateDisplay
                );
            }

            if ($post_type === 'post' && isset($attributes['displayCommentCount']) && $attributes['displayCommentCount']) {
                $count = get_comments_number( $post );
                $postHtml .= sprintf(
                    '<span class="advgb-post-comments"><span class="dashicons dashicons-admin-comments"></span>(%d)</span>',
                    $count
                );
            }

            $postHtml .= '</div>'; // end advgb-post-info

            $postHtml .= '<div class="advgb-post-tax-info">';

			if ( isset( $attributes['showCategories'] ) && 'hide' !== $attributes['showCategories'] ) {
				$categories = get_the_category( $post->ID );
				if ( ! empty( $categories ) ) {
					$postHtml .= '<div class="advgb-post-tax advgb-post-category">';
					foreach ( $categories as $category ) {
						if ( 'link' === $attributes['showCategories'] ) {
							$postHtml .= sprintf( '<div><a class="advgb-post-tax-term" href="%s">%s</a></div>', esc_url( get_category_link( $category ) ), esc_html( $category->name ) );
						} else {
							$postHtml .= sprintf( '<div><span class="advgb-post-tax-term">%s</span></div>', esc_html( $category->name ) );
						}
					}
					$postHtml .= '</div>';
				}
			}

			if ( isset( $attributes['showTags'] ) && 'hide' !== $attributes['showTags'] ) {
				$tags = get_the_tags( $post->ID );
				if ( ! empty( $tags ) ) {
					$postHtml .= '<div class="advgb-post-tax advgb-post-tag">';
					foreach ( $tags as $tag ) {
						if ( 'link' === $attributes['showTags'] ) {
							$postHtml .= sprintf( '<div><a class="advgb-post-tax-term" href="%s">%s</a></div>', esc_url( get_tag_link( $tag ) ), esc_html( $tag->name ) );
						} else {
							$postHtml .= sprintf( '<div><span class="advgb-post-tax-term">%s</span></div>', esc_html( $tag->name ) );
						}
					}
					$postHtml .= '</div>';
				}
			}

			if ( ! in_array( $post_type, array( 'post', 'page' ), true ) && isset( $attributes['showCustomTaxList'] ) && ! empty( $attributes['showCustomTaxList'] ) ) {
				$info = advgbGetTaxonomyTerms( $post_type, $post->ID, true, false );
				if ( ! empty( $info ) ) {
					foreach ( $attributes['showCustomTaxList'] as $name ) {
						if ( ! isset( $info[ $name ] ) ) {
							// maybe the name changed?
							continue;
						}
						$props = $info[ $name ];
						$slug = $props['slug'];
						$postHtml .= "<div class='advgb-post-tax advgb-post-cpt advgb-post-${slug}'>";
						if ( isset( $attributes['linkCustomTax'] ) && $attributes['linkCustomTax'] ) {
							$postHtml .= implode( '', $props['linked'] );
						} else {
							$postHtml .= implode( '', $props['unlinked'] );
						}
						$postHtml .= '</div>';
					}
				}
			}

            $postHtml .= '</div>'; // end advgb-post-tax-info

            $postHtml .= '<div class="advgb-post-content">';

            if (isset($attributes['displayExcerpt']) && $attributes['displayExcerpt']) {
                $introText = $post->post_excerpt;

                if (isset($attributes['displayExcerpt']) && $attributes['postTextAsExcerpt']) {
                    if (!is_admin()) {
                        $postContent = get_post_field('post_content', $post->ID);
                        $postContent = strip_shortcodes($postContent);
                        $postContent = preg_replace('/<!--(.*?-->)/is', '', $postContent);
                        $introText   = advgbExtractHtml($postContent, $attributes['postTextExcerptLength']);
                    }
                }

                $postHtml .= sprintf(
                    '<div class="advgb-post-excerpt">%1$s</div>',
                    $introText
                );
            }

            if (isset($attributes['textBeforeReadmore']) && !empty($attributes['textBeforeReadmore'])) {
				$postHtml .= sprintf( '<div class="advgb-text-before-readmore">%s</div>', wp_kses_post( $attributes['textBeforeReadmore'] ) );
			}

            if (isset($attributes['displayReadMore']) && $attributes['displayReadMore']) {
                $readMoreText = __('Read More', 'advanced-gutenberg');
                if (isset($attributes['readMoreLbl']) && $attributes['readMoreLbl']) {
                    $readMoreText = $attributes['readMoreLbl'];
                }

                $postHtml .= sprintf(
                    '<div class="advgb-post-readmore"><a href="%1$s">%2$s</a></div>',
                    get_permalink($post->ID),
                    $readMoreText
                );
            }

            $postHtml .= '</div>'; // end advgb-post-content

            $postHtml .= '</div>'; // end advgb-post-wrapper

            $postHtml .= '</article>';
        }
    }

    $blockClass = '';

    if ($attributes['postView'] === 'grid') {
        $blockClass = 'grid-view columns-' . $attributes['columns'];
    } elseif ($attributes['postView'] === 'list') {
        $blockClass = 'list-view';
        if($attributes['imagePosition'] !== 'left'){
            $blockClass .= ' image-' . $attributes['imagePosition'];
        }
    } elseif ($attributes['postView'] === 'slider') {
        $blockClass = 'slider-view';
        $blockClass .= ' style-' . $attributes['sliderStyle'];
		if ( isset( $attributes['sliderAutoplay'] ) && $attributes['sliderAutoplay'] ) {
	        $blockClass .= ' slider-autoplay';
		}
    } elseif ($attributes['postView'] === 'frontpage') {
        $blockClass = 'frontpage-view';
        $blockClass .= ' layout-' . $attributes['frontpageLayout'];
        $blockClass .= ' gap-' . $attributes['gap'];
        $blockClass .= ' style-' . $attributes['frontpageStyle'];
        (isset($attributes['frontpageLayoutT']) && $attributes['frontpageLayoutT']) ? $blockClass .= ' tbl-layout-' . $attributes['frontpageLayoutT'] : '';
        (isset($attributes['frontpageLayoutM']) && $attributes['frontpageLayoutM']) ? $blockClass .= ' mbl-layout-' . $attributes['frontpageLayoutM'] : '';
    } elseif ($attributes['postView'] === 'newspaper') {
        $blockClass = 'newspaper-view';
        $blockClass .= ' layout-' . $attributes['newspaperLayout'];
    } elseif ($attributes['postView'] === 'masonry') {
        $blockClass = 'masonry-view columns-' . $attributes['columns'] . ' tbl-columns-' . $attributes['columnsT'] . ' mbl-columns-' . $attributes['columnsM'] . ' gap-' . $attributes['gap'];
    }

    if (isset($attributes['className'])) {
        $blockClass .= ' ' . $attributes['className'];
    }

    $blockHtml = sprintf(
        '<div class="advgb-recent-posts-block %2$s"><div class="advgb-recent-posts">%1$s</div></div>',
        $postHtml,
        esc_attr($blockClass)
    );

    return $blockHtml;
}

/**
 * Register block Content Display
 *
 * @return void
 */
function advgbRegisterBlockRecentPosts()
{
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type('advgb/recent-posts', array(
        'attributes' => array(
            'postView' => array(
                'type' => 'string',
                'default' => 'grid',
            ),
            'order' => array(
                'type' => 'string',
                'default' => 'desc',
            ),
            'orderBy'  => array(
                'type' => 'string',
                'default' => 'date',
            ),
            'categories' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'object'
                )
            ),
            'tags' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'string'
                )
            ),
            'numberOfPosts' => array(
                'type' => 'number',
                'default' => 8,
            ),
            'columns' => array(
                'type' => 'number',
                'default' => 2,
            ),
            'columnsT' => array(
                'type' => 'number',
                'default' => 2,
            ),
            'columnsM' => array(
                'type' => 'number',
                'default' => 1,
            ),
            'displayFeaturedImage' => array(
                'type' => 'boolean',
                'default' => true,
            ),
            'displayFeaturedImageFor' => array(
                'type' => 'string',
                'default' => 'all',
            ),
            'displayFeaturedImageCaption' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'enablePlaceholderImage' => array(
                'type' => 'boolean',
                'default' => true,
            ),
            'displayAuthor' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'postDate' => array(
                'type' => 'string',
                'default' => 'hide',
            ),
            'postDateFormat' => array(
                'type' => 'string',
                'default' => 'absolute',
            ),
            'displayTime' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'displayExcerpt' => array(
                'type' => 'boolean',
                'default' => true,
            ),
            'postTextAsExcerpt' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'postTextExcerptLength' => array(
                'type' => 'number',
                'default' => 150,
            ),
            'displayReadMore' => array(
                'type' => 'boolean',
                'default' => true,
            ),
            'myToken' => array(
                'type' => 'number',
            ),
            'readMoreLbl' => array(
                'type' => 'string',
            ),
            'frontpageLayout' => array(
                'type' => 'string',
                'default' => '1-3',
            ),
            'frontpageLayoutT' => array(
                'type' => 'string',
            ),
            'frontpageLayoutM' => array(
                'type' => 'string',
            ),
            'gap' => array(
                'type' => 'number',
                'default' => 10,
            ),
            'frontpageStyle' => array(
                'type' => 'string',
                'default' => 'default',
            ),
            'sliderStyle' => array(
                'type' => 'string',
                'default' => 'default',
            ),
            'sliderAutoplay' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'newspaperLayout' => array(
                'type' => 'string',
                'default' => 'np-1-3',
            ),
            'changed' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'excludeCurrentPost' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'postType' => array(
                'type' => 'string',
            ),
            'showCategories' => array(
                'type' => 'string',
                'default' => 'hide',
            ),
            'showTags' => array(
                'type' => 'string',
                'default' => 'hide',
            ),
            'displayCommentCount' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'textAfterTitle' => array(
                'type' => 'string',
            ),
            'textBeforeReadmore' => array(
                'type' => 'string',
            ),
            'exclude' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'string'
                )
            ),
            'author' => array(
                'type' => 'string',
            ),
            'taxonomies' => array(
                'type' => 'object',
            ),
			'showCustomTaxList' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'string'
                )
            ),
            'linkCustomTax' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'imagePosition' => array(
                'type' => 'string',
                'default' => 'left',
            ),
			// deprecrated attributes...
            'displayDate' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'category' => array(
                'type' => 'string',
            ),
        ),
        'render_callback' => 'advgbRenderBlockRecentPosts',
    ));
}

add_action('init', 'advgbRegisterBlockRecentPosts');

/**
 * Register additional fields returned in REST.
 *
 * @return void
 */
function advgbRegisterCustomFields() {
	// POST fields
    register_rest_field( 'post',
        'coauthors',
        array(
            'get_callback'  => 'advgbGetCoauthors',
            'update_callback'   => null,
            'schema'            => null,
        )
    );

    register_rest_field( 'post',
        'tax_additional',
        array(
            'get_callback'  => 'advgbGetAdditionalTaxInfo',
            'update_callback'   => null,
            'schema'            => null,
        )
    );

    register_rest_field( 'post',
        'comment_count',
        array(
            'get_callback'  => 'advgbGetComments',
            'update_callback'   => null,
            'schema'            => null,
        )
    );

    register_rest_field( 'post',
        'relative_dates',
        array(
            'get_callback'  => 'advgbGetRelativeDates',
            'update_callback'   => null,
            'schema'            => null,
        )
    );

    register_rest_field( 'post',
        'absolute_dates',
        array(
            'get_callback'  => 'advgbGetAbsoluteDates',
            'update_callback'   => null,
            'schema'            => null,
        )
    );

    register_rest_field( 'post',
        'absolute_dates_time',
        array(
            'get_callback'  => 'advgbGetAbsoluteDatesTime',
            'update_callback'   => null,
            'schema'            => null,
        )
    );

    register_rest_field( 'post',
        'featured_img_caption',
        array(
            'get_callback'  => 'advgbGetImageCaption',
            'update_callback'   => null,
            'schema'            => null,
        )
    );

	// PAGE fields
    register_rest_field( 'page',
        'coauthors',
        array(
            'get_callback'  => 'advgbGetCoauthors',
            'update_callback'   => null,
            'schema'            => null,
        )
    );

    register_rest_field( 'page',
        'author_meta',
        array(
            'get_callback'  => 'advgbGetAuthorMeta',
            'update_callback'   => null,
            'schema'            => null,
        )
    );

    register_rest_field( 'page',
        'relative_dates',
        array(
            'get_callback'  => 'advgbGetRelativeDates',
            'update_callback'   => null,
            'schema'            => null,
        )
    );
    register_rest_field( 'page',
        'absolute_dates',
        array(
            'get_callback'  => 'advgbGetAbsoluteDates',
            'update_callback'   => null,
            'schema'            => null,
        )
    );

    register_rest_field( 'page',
        'absolute_dates_time',
        array(
            'get_callback'  => 'advgbGetAbsoluteDatesTime',
            'update_callback'   => null,
            'schema'            => null,
        )
    );

    register_rest_field( 'page',
        'featured_img_caption',
        array(
            'get_callback'  => 'advgbGetImageCaption',
            'update_callback'   => null,
            'schema'            => null,
        )
    );

	// CPT fields
	foreach ( advgbGetCPTs() as $cpt ) {
		register_rest_field( $cpt, 'author' );

		register_rest_field( $cpt,
			'featured_img',
			array(
				'get_callback'  => 'advgbGetFeaturedImage',
				'update_callback'   => null,
				'schema'            => null,
			)
		);

		register_rest_field( $cpt,
			'coauthors',
			array(
				'get_callback'  => 'advgbGetCoauthors',
				'update_callback'   => null,
				'schema'            => null,
			)
		);

		register_rest_field( $cpt,
			'author_meta',
			array(
				'get_callback'  => 'advgbGetAuthorMeta',
				'update_callback'   => null,
				'schema'            => null,
			)
		);

		register_rest_field( $cpt,
			'relative_dates',
			array(
				'get_callback'  => 'advgbGetRelativeDates',
				'update_callback'   => null,
				'schema'            => null,
			)
		);

        register_rest_field( $cpt,
            'absolute_dates',
            array(
                'get_callback'  => 'advgbGetAbsoluteDates',
                'update_callback'   => null,
                'schema'            => null,
            )
        );

        register_rest_field( $cpt,
            'absolute_dates_time',
            array(
                'get_callback'  => 'advgbGetAbsoluteDatesTime',
                'update_callback'   => null,
                'schema'            => null,
            )
        );

		register_rest_field( $cpt,
			'featured_img_caption',
			array(
				'get_callback'  => 'advgbGetImageCaption',
				'update_callback'   => null,
				'schema'            => null,
			)
		);

		register_rest_field( $cpt,
			'tax_additional',
			array(
				'get_callback'  => 'advgbGetAdditionalTaxInfo',
				'update_callback'   => null,
				'schema'            => null,
			)
		);
	}

	// custom routes
	register_rest_route( 'advgb/v1', '/authors/', array(
		'methods' => 'GET',
		'callback' => 'advgbGetAllAuthors',
		'permission_callback' => function () {
			return current_user_can( 'edit_others_posts' );
		},
	) );

	register_rest_route( 'advgb/v1', '/exclude_post_types/', array(
		'methods' => 'GET',
		'callback' => 'advgbExcludePostTypes',
		'permission_callback' => function () {
			return current_user_can( 'edit_others_posts' );
		},
	) );

}
add_action( 'rest_api_init', 'advgbRegisterCustomFields' );

/**
 * Returns the custom post types.
 *
 * @return array
 */
function advgbGetCPTs() {
	return get_post_types( array( '_builtin' => false, 'public' => true ) );
}

/**
 * Allow more orderBy values for posts.
 *
 * @return array
 */
function advgbAllowPostQueryVars( $query_params ) {
	$query_params['orderby']['enum'][] = 'rand';
	$query_params['orderby']['enum'][] = 'comment_count';
	return $query_params;
}
add_filter( 'rest_post_collection_params', 'advgbAllowPostQueryVars' );

/**
 * Allow more orderBy values for pages.
 *
 * @return array
 */
function advgbAllowPageQueryVars( $query_params ) {
	$query_params['orderby']['enum'][] = 'author';
	$query_params['orderby']['enum'][] = 'rand';
	return $query_params;
}
add_filter( 'rest_page_collection_params', 'advgbAllowPageQueryVars' );

/**
 * Allow more orderBy values for custom post types.
 *
 * @return array
 */
function advgbAllowCPTQueryVars( $query_params ) {
	$query_params['orderby']['enum'][] = 'author';
	return $query_params;
}

foreach ( advgbGetCPTs() as $cpt ) {
	add_filter( "rest_{$cpt}_collection_params", 'advgbAllowCPTQueryVars' );
}

/**
 * Returns the relative dates of the post.
 *
 * @return array
 */
function advgbGetRelativeDates( $post ) {
	return array(
		'created' => __( 'Posted', 'advanced-gutenberg') . ' ' . human_time_diff( get_the_date( 'U', $post['id'] ) ) . ' ' . __( 'ago', 'advanced-gutenberg'),
		'modified' => __( 'Updated', 'advanced-gutenberg') . ' ' . human_time_diff( get_the_modified_date( 'U', $post['id'] ) ) . ' ' . __( 'ago', 'advanced-gutenberg')
	);
}

/**
 * Returns the absolute dates of the post.
 *
 * @return array
 */
function advgbGetAbsoluteDates( $post ) {
    $format = get_option( 'date_format' );
    return array(
        'created' => __( 'Posted on', 'advanced-gutenberg') . ' ' . get_the_date( $format, $post['id']),
        'modified' => __( 'Updated on', 'advanced-gutenberg') . ' ' . get_the_modified_date( $format, $post['id'])
    );
}

/**
 * Returns the absolute dates with time of the post.
 *
 * @return array
 */
function advgbGetAbsoluteDatesTime( $post ) {
    $format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
    return array(
        'created' => __( 'Posted on', 'advanced-gutenberg') . ' ' . get_the_date( $format, $post['id']),
        'modified' => __( 'Updated on', 'advanced-gutenberg') . ' ' . get_the_modified_date( $format, $post['id'])
    );
}

/**
 * Returns the featured image caption
 *
 * @return string
 */
function advgbGetImageCaption( $post ) {
	return get_the_post_thumbnail_caption( $post['id'] );
}

/**
 * Returns the number of comments against the post;
 *
 * @return int
 */
function advgbGetComments( $post ) {
	return get_comments_number( $post['id'] );
}

/**
 * Populates the HTML corresponding to the categories and tags in case they need to be shown in the post.
 *
 * @return array
 */
function advgbGetAdditionalTaxInfo( $post ) {
	$info = array();

	$post_type = get_post_type( $post['id'] );
	if ( 'post' ===  $post_type ) {
		$categories = get_the_category( $post['id'] );
		if ( ! empty( $categories ) ) {
			$cats = array( 'linked' => array(), 'unlinked' => array() );
			foreach ( $categories as $category ) {
				$cats['linked'][] = sprintf( '<a href="%s" class="advgb-post-tax-term">%s</a>', esc_url( get_category_link( $category ) ), esc_html( $category->name ) );
				$cats['unlinked'][] = sprintf( '<span class="advgb-post-tax-term">%s</span>', esc_html( $category->name ) );
			}
			$info['categories'] = $cats;
		}

		$tags = get_the_tags( $post['id'] );
		if ( ! empty( $tags ) ) {
			$cats = array( 'linked' => array(), 'unlinked' => array() );
			foreach ( $tags as $tag ) {
				$cats['linked'][] = sprintf( '<a href="%s" class="advgb-post-tax-term">%s</a>', esc_url( get_tag_link( $category ) ), esc_html( $tag->name ) );
				$cats['unlinked'][] = sprintf( '<span class="advgb-post-tax-term">%s</span>', esc_html( $tag->name ) );
			}
			$info['tags'] = $cats;
		}
	} else {
		$info = advgbGetTaxonomyTerms( $post_type, $post['id'], false );
	}

	return $info;
}

/**
 * Gets the taxonomy terms for a specific custom post.
 *
 * @return array
 */
function advgbGetTaxonomyTerms( $post_type, $post_id, $front_end = false, $use_tax_slug = true ) {
	$info = array();
	$taxonomies = get_object_taxonomies( $post_type, 'objects' );
	foreach( $taxonomies as $slug => $tax ) {
		$terms = get_the_terms( $post_id, $slug );
		$linked = $unlinked = array();
		if ( $terms ) {
			foreach( $terms as $term ) {
				if ( $front_end ) {
					$linked[] = sprintf( '<div><a href="%s" class="advgb-post-tax-term">%s</a></div>', esc_url( get_term_link( $term->slug, $slug ) ), esc_html( $term->name ) );
					$unlinked[] = sprintf( '<div><span class="advgb-post-tax-term">%s</span></div>', esc_html( $term->name ) );
				} else {
					$linked[] = sprintf( '<a href="%s" class="advgb-post-tax-term">%s</a>', esc_url( get_term_link( $term->slug, $slug ) ), esc_html( $term->name ) );
					$unlinked[] = sprintf( '<span class="advgb-post-tax-term">%s</span>', esc_html( $term->name ) );
				}
			}
		}
		$info[ $use_tax_slug ? $slug : html_entity_decode( $tax->label, ENT_QUOTES ) ] = array( 'linked' => $linked, 'unlinked' => $unlinked, 'slug' => $slug, 'name' => esc_html( $tax->label ) );
	}
	return $info;
}

/**
 * Get the coauthors from the PublishPress Authors plugin if it is activated.
 *
 * @return array
 */
function advgbGetCoauthors( $post ) {
	$coauthors = array();
	if ( function_exists('get_multiple_authors') ){
		$authors = get_multiple_authors( $post[ 'id' ] );
		foreach ($authors as $user) {
			$author = advgbGetAuthorByID( $user->ID );
			if ( $author ) {
				$coauthors[] = array( 'link' => $author->__get('link'), 'display_name' => $author->__get('name'));
			}
		}
	}
    return $coauthors;
}


/**
 * Populate the author_meta for pages/custom post types.
 *
 * @return array
 */
function advgbGetAuthorMeta( $page ) {
	return array( 'author_link' => get_author_posts_url( $page['author'] ), 'display_name' => get_the_author_meta( 'display_name', $page['author'] ) );
}

/**
 * Populate the correct arguments for filtering by author.
 *
 * The results depends on whether PublishPress Authors plugin is activated.
 *
 * @return array
 */
function advgbGetAuthorFilter( $args, $attributes, $post_type ) {
	if ( isset( $attributes['author'] ) && ! empty( $attributes['author'] ) ) {
		if ( ! function_exists('get_multiple_authors') ){
			$args['author'] = $attributes['author'];
		} else {
			$user_id = $attributes['author'];
			$author = advgbGetAuthorByID( $user_id );
			if ( $author ) {
				$args['meta_query'][] = array(
					'key' => 'ppma_authors_name',
					'value' => $author->__get( 'display_name' ),
					'compare' => 'LIKE',
				);
			}
		}
	}
	return $args;
}
add_filter( 'advgb_get_recent_posts_args', 'advgbGetAuthorFilter', 10, 3 );

/**
 * Populate the correct arguments for filtering by author.
 *
 * If multiple authors are defined using PublishPress Authors plugin, use the first author in the list.
 */
function advgbMultipleAuthorSort() {
	if ( function_exists('get_multiple_authors') ){
		add_action('pre_get_posts', function( $query )  {
			if ( is_admin() ) {
				return $query;
			}

			$query->set('orderby', 'meta_value');
			$query->set('meta_key', 'ppma_authors_name');

			return $query;

		} );
	}
}

/**
 * Populate the correct arguments in REST for sorting by author.
 *
 * The results depends on whether PublishPress Authors plugin is activated.
 *
 * @return array
 */
function advgbGetAuthorFilterREST( $args, $request ) {
	if ( isset( $request['author'] ) && ! empty( $request['author'] ) && function_exists('get_multiple_authors') ) {
			$author = $request['author'];
			$user_id = reset( $author );
			$author = advgbGetAuthorByID( $user_id );
			if ( $author ) {
				$args['meta_key'] = 'ppma_authors_name';
				$args['meta_value'] = $author->__get( 'display_name' );
				$args['meta_compare'] = 'LIKE';
				unset( $args['author'] );
				unset( $args['author__in'] );
			}
	}
	return $args;
}
add_filter( 'rest_post_query', 'advgbGetAuthorFilterREST', 10, 2 );
add_filter( 'rest_page_query', 'advgbGetAuthorFilterREST', 10, 2 );

foreach ( advgbGetCPTs() as $cpt ) {
	add_filter( "rest_{$cpt}_query", 'advgbGetAuthorFilterREST', 10, 2 );
}

/**
 * Populate the correct arguments in REST for filtering by author.
 *
 * The results depends on whether PublishPress Authors plugin is activated.
 *
 * @return array
 */
function advgbMultipleAuthorSortREST( $args, $request ) {
	if ( isset( $request['orderby'] ) && 'author' === $request['orderby'] && function_exists('get_multiple_authors') ) {
		$args['meta_key'] = 'ppma_authors_name';
		$args['orderby'] = 'meta_value';
	}
	return $args;
}
add_filter( 'rest_post_query', 'advgbMultipleAuthorSortREST', 10, 2 );

foreach ( advgbGetCPTs() as $cpt ) {
	add_filter( "rest_{$cpt}_query", 'advgbMultipleAuthorSortREST', 10, 2 );
}

/**
 * Check if Featured image is enable for each post
 *
 * @return boolean
 */
function advgbCheckImageStatus( $attributes, $key )  {
    if(
        isset($attributes['displayFeaturedImage']) && $attributes['displayFeaturedImage']
        && ($attributes['displayFeaturedImageFor'] === 'all'
        || $key < $attributes['displayFeaturedImageFor'])
    ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Returns post ids corresponding to post titles.
 *
 * @return array
 */
function advgbGetPostIdsForTitles( $titles, $post_type ) {
	global $wpdb;
	if ( ! empty( $titles ) ) {
		// don't use post_name__in here because the title may be different from the slug
		$placeholders = implode( ',', array_fill(0, count($titles), '%s') );
		$params = $titles;
		$params[] = $post_type;
		$query = $wpdb->prepare( "SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_title IN ($placeholders) AND post_type = '%s'", $params);
		return $wpdb->get_col( $query );
	}
	return array();
}

/**
 * Returns all valid authors (including those defined by PublishPress Authors plugin).
 *
 * @return array
 */
function advgbGetAllAuthors( WP_REST_Request $request ) {
	$authors = array();
	$users = get_users( array( 'per_page' => -1, 'who' => 'authors', 'fields' => 'all' ) );
	foreach ( $users as $user ) {
		$author = $user->data;
		$author->id = $author->ID;
		$author->name = $author->display_name;
		$authors[ $author->name ] = $author;
	}

	if ( function_exists( 'multiple_authors_get_all_authors' ) ) {
		$coauthors = multiple_authors_get_all_authors();
		foreach ( $coauthors as $coauthor ) {
			$name = $coauthor->__get( 'display_name' );
			if ( ! array_key_exists( $name, $authors ) ) {
				$authors[ $name ] = (object) array( 'name' => $name, 'id' => $coauthor->__get( 'ID' ) );
			}
		}
	}
	return array_values( $authors );
}

/**
 * Wrapper method to fetch an author on the basis of it's ID.
 *
 * This ID can either be the WP_User ID (positive integer) or guest author ID (negative integer).
 *
 * @return Author|false
 */
function advgbGetAuthorByID( $id ) {
	$author = false;
	if ( method_exists( 'MultipleAuthors\Classes\Objects\Author', 'get_by_id' ) ) {
		$author = MultipleAuthors\Classes\Objects\Author::get_by_id( $id );
	} else {
		if ( intval( $id ) > -1 ) {
			$author = MultipleAuthors\Classes\Objects\Author::get_by_user_id( $id );
		} else {
			$author = MultipleAuthors\Classes\Objects\Author::get_by_term_id( $id );
		}
	}
	return $author;
}

/**
 * Returns the featured image URL.
 *
 * @return string
 */
function advgbGetFeaturedImage( $post ) {
	return get_the_post_thumbnail_url( $post[ 'id' ] );
}


/**
 * Returns all the post types that need to be excluded.
 *
 * @return array
 */
function advgbExcludePostTypes( WP_REST_Request $request ) {
	// allow users to add more
	return apply_filters( 'advgb_exclude_post_types', array( 'attachment', 'web-story' ) );
}
