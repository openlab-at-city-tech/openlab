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
		$categories = esc_html( $attributes['category'] );
	}

	$post_type = isset( $attributes['postType'] ) ? esc_html( $attributes['postType'] ) : 'post';
	$tax_query = [];
	if ( ! empty( $attributes['tags'] ) && ($post_type === 'post' || $post_type === 'page') ){
        $tax_query = array(
				array(
					'taxonomy' => 'post_tag',
					'field' => 'name',
					'terms' => array_map( 'esc_html', $attributes['tags'] ),
					'operator' => 'IN',
				),
			);
	}

	$orderBy = empty($attributes['orderBy']) ? 'date' : esc_html( $attributes['orderBy'] );

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

    // if order by series order
	if ( $orderBy === 'series_order' ) {
		advgbSeriesOrderSort();
	}

	$args = array(
			'post_type' => $post_type,
            'numberposts' => empty( $attributes['numberOfPosts'] ) ? 8 : esc_html( $attributes['numberOfPosts'] ),
            'post_status' => 'publish',
            'order' => empty( $attributes['order'] ) ? 'desc' : esc_html( $attributes['order'] ),
            'orderby' => $orderBy,
            'suppress_filters' => false,
        );

    if( isset( $attributes['excludeCurrentPost'] ) && $attributes['excludeCurrentPost'] ) {
        $args['post__not_in'] = isset( $args['post__not_in'] ) ? array_merge( $args['post__not_in'], array( $post->ID ) ) : array( $post->ID );
    }

    if( isset( $attributes['offset'] ) && $attributes['offset'] ) {
        $args['offset'] = esc_html( $attributes['offset'] );
    }

    if(
        defined('ADVANCED_GUTENBERG_PRO')
        && isset( $attributes['includePosts'] )
        && ! empty( $attributes['includePosts'] )
    ) {
        // Pro
        $args['post__in'] = array_map( 'esc_html', $attributes['includePosts'] );
    } elseif(
        ( isset( $attributes['excludePosts'] ) && ! empty( $attributes['excludePosts'] ) )
        || ( isset( $attributes['exclude'] ) && ! empty( $attributes['exclude'] ) )
    ) {
        if( isset( $attributes['exclude'] ) && ! empty( $attributes['exclude'] ) ) {
            // Exclude posts, backward compatibility 2.13.1 and lower
            $exclude = array_map( 'esc_html', $attributes['exclude'] );
            $args['post__not_in'] = advgbGetPostIdsForTitles( $exclude, $post_type );
        } else {
            $args['post__not_in'] = array_map( 'esc_html', $attributes['excludePosts'] );
        }

    } else {
        // Nothing to do here
    }

	if( isset( $attributes['taxonomies'] ) && ! empty( $attributes['taxonomies'] ) ) {
		foreach( $attributes['taxonomies'] as $slug => $terms ) {
			if ( count( $terms ) > 0 ) {
				$tax_query[] = array(
						'taxonomy' => esc_html( $slug ),
						'field' => 'name',
						'terms' => array_map( 'esc_html', $terms ),
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
    $postView = isset( $attributes['postView'] ) && ! empty( $attributes['postView'] ) ? esc_html( $attributes['postView'] ) : 'grid';

    if (!empty($recent_posts)) {
        foreach ($recent_posts as $key=>$post) {
            $postThumbID         = get_post_thumbnail_id($post->ID);
            $outputImage         = advgbCheckElementDisplay( $attributes['displayFeaturedImage'], $attributes['displayFeaturedImageFor'], $key ) && ( $postThumbID || $attributes['enablePlaceholderImage'] );
            $displayImageVsOrder = getDisplayImageVsOrder( $attributes, $key );
            $postThumb           = '<img src="' . esc_url($rp_default_thumb['url']) . '" />';
            $postThumbCaption    = '';
            $postDate            = isset($attributes['displayDate']) && $attributes['displayDate'] ? 'created' : (isset($attributes['postDate']) ? esc_html( $attributes['postDate'] ) : 'hide');
            $postDateDisplay     = null;

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

            $postHtml .= '<article class="advgb-recent-post' . ( $outputImage ? '' : ' advgb-recent-post--no-image' ) . '">';

            if ( $outputImage && $displayImageVsOrder === 'ignore-order' ) {
                $postHtml .= sprintf(
                    '<div class="advgb-post-thumbnail"><a href="%1$s">%2$s%3$s</a></div>',
                    get_permalink($post->ID),
                    $postThumb,
                    $postThumbCaption
                );
            } elseif (
                ($postView === 'frontpage' && $attributes['frontpageStyle'] === 'headline')
                || ($postView === 'slider' && $attributes['sliderStyle'] === 'headline')
                 && $displayImageVsOrder === 'ignore-order'
            ) {
                $postHtml .= sprintf(
                    '<div class="advgb-post-thumbnail advgb-post-thumbnail-no-image"><a href="%1$s"></a></div>',
                    get_permalink($post->ID)
                );
            } else {
                // Nothing to do here
            }

            $postHtml .= '<div class="advgb-post-wrapper">';

            if ( $outputImage && $displayImageVsOrder === 'apply-order' ) {
                $postHtml .= sprintf(
                    '<div class="advgb-post-thumbnail"><a href="%1$s">%2$s%3$s</a></div>',
                    get_permalink($post->ID),
                    $postThumb,
                    $postThumbCaption
                );
            }

            $postHtml .= sprintf(
                '<h2 class="advgb-post-title"><a href="%1$s">%2$s</a></h2>',
                get_permalink($post->ID),
                get_the_title($post->ID)
            );

            if (isset($attributes['textAfterTitle']) && !empty($attributes['textAfterTitle'])) {
				$postHtml .= sprintf( '<div class="advgb-text-after-title">%s</div>', wp_kses_post( $attributes['textAfterTitle'] ) );
			}

            if(
                advgbCheckElementDisplay( $attributes['displayAuthor'], $attributes['displayAuthorFor'], $key )
                || advgbCheckElementDisplayStr( $postDate, $attributes['postDateFor'], $key )
                || ( !empty($postDateDisplay) )
                || (
                    $post_type === 'post'
                    && advgbCheckElementDisplay( $attributes['displayCommentCount'], $attributes['displayCommentCountFor'], $key )
                )
            ) {

                $postHtml .= '<div class="advgb-post-info">';

                if ( advgbCheckElementDisplay( $attributes['displayAuthor'], $attributes['displayAuthorFor'], $key ) ) {
    				$coauthors          = advgbGetCoauthors( array( 'id' => $post->ID ) );
    				$authorLinkNewTab   = isset($attributes['authorLinkNewTab']) && $attributes['authorLinkNewTab'] ? '_blank' : '_self';
                    if ( ! empty( $coauthors ) ) {
    					$index = 0;
    					foreach ( $coauthors as $coauthor ) {
    						$postHtml .= sprintf(
    							'<a href="%1$s" class="advgb-post-author" target="%3$s">%2$s</a>',
    							esc_url( $coauthor['link'] ),
    							esc_html( $coauthor['display_name'] ),
    							$authorLinkNewTab
    						);
    						if ( $index++ < count( $coauthors ) - 1 ) {
    							$postHtml .= '<span>, </span>';
    						}
    					}
    				} else {
    					$postHtml .= sprintf(
    						'<a href="%1$s" class="advgb-post-author" target="%3$s">%2$s</a>',
    						get_author_posts_url($post->post_author),
    						get_the_author_meta('display_name', $post->post_author),
    						$authorLinkNewTab
    					);
    				}
                }

                if ( advgbCheckElementDisplayStr( $postDate, $attributes['postDateFor'], $key ) ) {
                    $postDateFormat     = isset($attributes['postDateFormat']) ? $attributes['postDateFormat'] : '';
                    $displayTime        = isset($attributes['displayTime']) && $attributes['displayTime'];
                    $format             = $displayTime ? ( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) : get_option( 'date_format' );

                    if ( $postDateFormat === 'absolute' ) {
                        if ( $postDate === 'created' ) {
                            $postDateDisplay = esc_html__( 'Posted on', 'advanced-gutenberg') . ' ' . get_the_date( $format, $post->ID);
                        } else {
                            $postDateDisplay = esc_html__( 'Updated on', 'advanced-gutenberg') . ' ' . get_the_modified_date( $format, $post->ID);
                        }
                    } else {
                        // Relative date format
                        if ( $postDate === 'created' ) {
                            $postDateDisplay = esc_html__( 'Posted', 'advanced-gutenberg') . ' ' . human_time_diff( get_the_date( 'U', $post->ID ) ) . ' ' . esc_html__( 'ago', 'advanced-gutenberg');
                        } else {
                            $postDateDisplay = esc_html__( 'Updated', 'advanced-gutenberg') . ' ' .human_time_diff( get_the_modified_date( 'U', $post->ID ) ) . ' ' . esc_html__( 'ago', 'advanced-gutenberg');
                        }
                    }
                }

                if ( ! empty( $postDateDisplay ) ) {
                    $postHtml .= sprintf(
                        '<span class="advgb-post-datetime">%1$s</span>',
                        $postDateDisplay
                    );
                }

                if ( $post_type === 'post'
                    && advgbCheckElementDisplay( $attributes['displayCommentCount'], $attributes['displayCommentCountFor'], $key )
                ) {
                    $count = get_comments_number( $post );
                    $postHtml .= sprintf(
                        '<span class="advgb-post-comments"><span class="dashicons dashicons-admin-comments"></span>(%d)</span>',
                        $count
                    );
                }

                $postHtml .= '</div>'; // end advgb-post-info
            }

            if(
                advgbCheckElementDisplayStr( $attributes['showCategories'], $attributes['showCategoriesFor'], $key )
                || advgbCheckElementDisplayStr( $attributes['showTags'], $attributes['showTagsFor'], $key )
                || ( !in_array( $post_type, array( 'post', 'page' ), true ) && advgbCheckElementDisplayArr( $attributes['showCustomTaxList'], $attributes['showCustomTaxListFor'], $key ) )
            ) {
                $postHtml .= '<div class="advgb-post-tax-info">';

    			if ( advgbCheckElementDisplayStr( $attributes['showCategories'], $attributes['showCategoriesFor'], $key ) ) {
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

    			if ( advgbCheckElementDisplayStr( $attributes['showTags'], $attributes['showTagsFor'], $key ) ) {
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

    			if ( ! in_array( $post_type, array( 'post', 'page' ), true ) && advgbCheckElementDisplayArr( $attributes['showCustomTaxList'], $attributes['showCustomTaxListFor'], $key ) ) {
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
            }

            $postHtml .= '<div class="advgb-post-content">';

            if ( advgbCheckElementDisplay( $attributes['displayExcerpt'], $attributes['displayExcerptFor'], $key ) ) {
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

            if ( advgbCheckElementDisplay( $attributes['displayReadMore'], $attributes['displayReadMoreFor'], $key ) ) {
                $readMoreText = esc_html__('Read More', 'advanced-gutenberg');
                if (isset($attributes['readMoreLbl']) && $attributes['readMoreLbl']) {
                    $readMoreText = esc_html($attributes['readMoreLbl']);
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

    if ($postView === 'grid') {
        $blockClass = 'grid-view columns-' . esc_html($attributes['columns']);
    } elseif ($postView === 'list') {
        $blockClass = 'list-view';
        if($attributes['imagePosition'] !== 'left'){
            $blockClass .= ' image-' . esc_html($attributes['imagePosition']);
        }
    } elseif ($postView === 'slider') {
        $blockClass = 'slider-view';
        $blockClass .= ' style-' . esc_html($attributes['sliderStyle']);
		if ( isset( $attributes['sliderAutoplay'] ) && $attributes['sliderAutoplay'] ) {
	        $blockClass .= ' slider-autoplay';
		}
    } elseif ($postView === 'frontpage') {
        $blockClass = 'frontpage-view';
        $blockClass .= ' layout-' . esc_html($attributes['frontpageLayout']);
        $blockClass .= ' gap-' . esc_html($attributes['gap']);
        $blockClass .= ' style-' . esc_html($attributes['frontpageStyle']);
        (isset($attributes['frontpageLayoutT']) && $attributes['frontpageLayoutT']) ? $blockClass .= ' tbl-layout-' . esc_html($attributes['frontpageLayoutT']) : '';
        (isset($attributes['frontpageLayoutM']) && $attributes['frontpageLayoutM']) ? $blockClass .= ' mbl-layout-' . esc_html($attributes['frontpageLayoutM']) : '';
    } elseif ($postView === 'newspaper') {
        $blockClass = 'newspaper-view';
        $blockClass .= ' layout-' . esc_html($attributes['newspaperLayout']);
    } elseif ($postView === 'masonry') {
        $blockClass = 'masonry-view columns-' . esc_html($attributes['columns']) . ' tbl-columns-' . esc_html($attributes['columnsT']) . ' mbl-columns-' . esc_html($attributes['columnsM']) . ' gap-' . esc_html($attributes['gap']);
    }

    if (defined('ADVANCED_GUTENBERG_PRO') && isset($attributes['orderSections'])) {
        $blockClass .= ' sections-' . esc_html($attributes['orderSections']);
    }

    if (isset($attributes['className'])) {
        $blockClass .= ' ' . esc_html($attributes['className']);
    }

    if(isset($attributes['id'])){
        $blockClass .= ' ' . esc_html($attributes['id']);
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
            'id' => array(
                'type' => 'string',
            ),
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
            'imageOverlayColor' => array(
                'type' => 'string',
                'default' => '#000',
            ),
            'imageOpacity' => array(
                'type' => 'number',
                'default' => 1,
            ),
            'displayAuthor' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'displayAuthorFor' => array(
                'type' => 'string',
                'default' => 'all',
            ),
            'authorLinkNewTab' => array(
                'type' => 'boolean',
                'default' => true,
            ),
            'postDate' => array(
                'type' => 'string',
                'default' => 'hide',
            ),
            'postDateFor' => array(
                'type' => 'string',
                'default' => 'all',
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
            'displayExcerptFor' => array(
                'type' => 'string',
                'default' => 'all',
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
            'displayReadMoreFor' => array(
                'type' => 'string',
                'default' => 'all',
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
            'sliderAutoplaySpeed' => array(
                'type' => 'number',
                'default' => 3000,
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
            'showCategoriesFor' => array(
                'type' => 'string',
                'default' => 'all',
            ),
            'showTags' => array(
                'type' => 'string',
                'default' => 'hide',
            ),
            'showTagsFor' => array(
                'type' => 'string',
                'default' => 'all',
            ),
            'displayCommentCount' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'displayCommentCountFor' => array(
                'type' => 'string',
                'default' => 'all',
            ),
            'textAfterTitle' => array(
                'type' => 'string',
            ),
            'textBeforeReadmore' => array(
                'type' => 'string',
            ),
            'excludePosts' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'number'
                )
            ),
            'includePosts' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'number'
                )
            ),
            'offset' => array(
                'type' => 'number',
                'default' => 0,
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
            'showCustomTaxListFor' => array(
                'type' => 'string',
                'default' => 'all',
            ),
            'linkCustomTax' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'imagePosition' => array(
                'type' => 'string',
                'default' => 'left',
            ),
            'onlyFromCurrentUser' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'orderSections' => array(
                'type' => 'string',
                'default' => 'image-title-info-text',
            ),
			// deprecrated attributes...
            'exclude' => array(
                'type' => 'array',
                'items' => array(
                    'type' => 'string'
                )
            ),
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

    register_rest_field( 'post',
        'series_order',
        array(
            'get_callback'  => 'advgbGetSeriesOrder',
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

    register_rest_field( 'page',
        'featured_img',
        array(
            'get_callback'  => 'advgbGetFeaturedImage',
            'update_callback'   => null,
            'schema'            => null,
        )
    );

    register_rest_field( 'page',
        'series_order',
        array(
            'get_callback'  => 'advgbGetSeriesOrder',
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

        register_rest_field( $cpt,
            'series_order',
            array(
                'get_callback'  => 'advgbGetSeriesOrder',
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
	$query_params['orderby']['enum'][] = 'series_order';
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
	$query_params['orderby']['enum'][] = 'series_order';
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
	$query_params['orderby']['enum'][] = 'series_order';
	return $query_params;
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
        'created' => esc_html__( 'Posted on', 'advanced-gutenberg') . ' ' . get_the_date( $format, $post['id']),
        'modified' => esc_html__( 'Updated on', 'advanced-gutenberg') . ' ' . get_the_modified_date( $format, $post['id'])
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
        'created' => esc_html__( 'Posted on', 'advanced-gutenberg') . ' ' . get_the_date( $format, $post['id']),
        'modified' => esc_html__( 'Updated on', 'advanced-gutenberg') . ' ' . get_the_modified_date( $format, $post['id'])
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
 * Returns the Series order for each post
 *
 * @return int
 */
function advgbGetSeriesOrder( $post ) {
    
    // Series 2.11.4+ meta_key now uses _series_part_${id}
    if ( function_exists( 'publishpress_multi_series_supported' ) 
        && publishpress_multi_series_supported() ) {
        
        // Get the terms array from a post so later we can get the term id
        $terms = wp_get_post_terms( $post['id'], 'series' );

        if( count( $terms ) && $terms[0]->term_id ) {
            return (int) get_post_meta( $post['id'], '_series_part_' . $terms[0]->term_id, true );
        }
    } else {
        // Series Pro 2.11.3- and Series Free 2.11.2-
        return get_post_meta( $post['id'], '_series_part', true );
    }

    return null;
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
	if ( isset( $page['author'] ) ) {
		return array( 'author_link' => get_author_posts_url( $page['author'] ), 'display_name' => get_the_author_meta( 'display_name', $page['author'] ) );
	}
	return array( 'author_link' => '', 'display_name' => '' );
}

/**
 * Populate the correct arguments for filtering by author.
 *
 * The results depends on whether PublishPress Authors plugin is activated.
 *
 * @return array
 */
function advgbGetAuthorFilter( $args, $attributes, $post_type ) {
    // Get current logged in user
    if ( isset( $attributes['onlyFromCurrentUser'] ) && $attributes['onlyFromCurrentUser'] ) {
        if ( ! function_exists('get_multiple_authors') ){
            $args['author'] = advgbGetCurrentUserId();
        } elseif( advgbGetCurrentUserId() === 999999 ) {
            $args['author'] = advgbGetCurrentUserId();
        } else {
			advgbSetPPAuthorArgs( advgbGetCurrentUserId(), $args );
        }
    } else {
        // Get author attribute
        if ( isset( $attributes['author'] ) && ! empty( $attributes['author'] ) ) {
			// WooCommerce Products don't support multiple authors...
    		if (  $post_type === 'product' || ! function_exists('get_multiple_authors') ){
    			$args['author'] = esc_html( $attributes['author'] );
    		} else {
				advgbSetPPAuthorArgs( esc_html( $attributes['author'] ), $args );
			}
    	}
    }
	return $args;
}
add_filter( 'advgb_get_recent_posts_args', 'advgbGetAuthorFilter', 10, 3 );

/**
 * Populate the correct arguments in REST for filtering by author.
 *
 * The results depends on whether PublishPress Authors plugin is activated.
 *
 * @return array
 */
function advgbGetAuthorFilterREST( $args, $request ) {
	if ( isset( $request['author'] ) && ! empty( $request['author'] ) ) {
		// WooCommerce Products don't support multiple authors...
		if ( $args['post_type'] !== 'product' && function_exists('get_multiple_authors') ) {
			$author = $request['author'];
			$user_id = is_array( $author ) ? reset( $author ) : $author;
			advgbSetPPAuthorArgs( $user_id, $args );
		} else {
			unset( $args['author'] );
			$args['author__in'] = $request['author'];
		}
	}
	return $args;
}
add_filter( 'rest_post_query', 'advgbGetAuthorFilterREST', 10, 2 );
add_filter( 'rest_page_query', 'advgbGetAuthorFilterREST', 10, 2 );

/**
 * Populate the correct arguments in REST for filtering by series order.
 *
 * @return array
 */
function advgbSetSeriesOrderREST( $args, $request ) {
    $orderby = esc_html( $request['orderby'] );

    if ( isset( $orderby ) && $orderby === 'series_order' && isset( $request['series'] ) ) {
        $args['orderby'] = 'meta_value_num';

        // Series 2.11.4+ meta_key now uses _series_part_${id}
        if ( function_exists( 'publishpress_multi_series_supported' ) 
            && publishpress_multi_series_supported() ) {
            
            $ids = array_map( 'intval', $request['series'] );
            foreach( $ids as $id ) {
                $args['meta_key'][] = '_series_part_' . $id;
            }
        } else {
            // Series Pro 2.11.3- and Series Free 2.11.2-
            $args['meta_key'] = '_series_part';
        }

	}
	return $args;
}
add_filter( 'rest_post_query', 'advgbSetSeriesOrderREST', 10, 2 );
add_filter( 'rest_page_query', 'advgbSetSeriesOrderREST', 10, 2 );

/**
 * Sets the author args for the meta_query.
 */
function advgbSetPPAuthorArgs( $user_id, &$args ) {
	$author = advgbGetAuthorByID( $user_id );
	if ( $author ) {
		$args['meta_query'][] = array(
			'key' => 'ppma_authors_name',
			'value' => $author->__get( 'display_name' ),
			'compare' => 'LIKE',
		);
		unset( $args['author'] );
		unset( $args['author__in'] );
	}
}

/**
 * Populate the correct arguments in REST for sorting by author.
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

/**
 * Populate the correct arguments for filtering by author.
 *
 * If multiple authors are defined using PublishPress Authors plugin, use the first author in the list.
 */
function advgbMultipleAuthorSort() {
	if ( function_exists('get_multiple_authors') ){
		add_action('pre_get_posts', function( $query )  {
			if ( is_admin() || $query->query_vars['orderby'] !== 'author' ) {
				return $query;
			}

			$query->set('orderby', 'meta_value');
			$query->set('meta_key', 'ppma_authors_name');

			return $query;
		} );
	}
}

/**
 * Populate the correct arguments for filtering by series order.
 *
 */
function advgbSeriesOrderSort() {
	if ( class_exists('orgSeries') ){
		add_action('pre_get_posts', function( $query )  {
			if ( is_admin() || $query->query_vars['orderby'] !== 'series_order' ) {
				return $query;
			}
			$query->set('orderby', 'meta_value');

			// Series 2.11.4+ meta_key now uses _series_part_${id}
			if ( function_exists( 'publishpress_multi_series_supported' ) 
			    && publishpress_multi_series_supported() ) {
                
			    // We get the terms titles. The same stored in block's taxonomies->series attribute
			    // @TODO - Store and use term ids instead as attributes
			    $terms = $query->query_vars['tax_query'][0]['terms'];
			    $metakeys = [];
			    if( count( $terms ) ) {
			        foreach( $terms as $term ) {
			            // Get the term object and then use the id to get the meta_key 
			            $term_obj = get_term_by( 'name', $term, 'series' );
			            $metakeys[] = '_series_part_' . (int) $term_obj->term_id;
			        }
			        $query->set( 'meta_key', $metakeys );
			    }
			} else {
			    // Series Pro 2.11.3- and Series Free 2.11.2-
			    $query->set( 'meta_key', '_series_part' );
			}
            
			return $query;
		} );
	}
}

/**
 * Check if an element is enabled for each post when $display is a boolean
 *
 * @param string $element   Element to display
 * @param boolean $display  Display or not the element?
 * @param int $key          Index of the element
 *
 * @return boolean
 */
function advgbCheckElementDisplay( $element, $display, $key )  {
    if(
        isset( $element ) && $element
        && ( $display === 'all' || $key < $display )
    ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if an element is enabled for each post when $display is a string
 *
 * @param string $element   Element to display
 * @param boolean $display  Display or not the element?
 * @param int $key          Index of the element
 *
 * @return boolean
 */
function advgbCheckElementDisplayStr( $element, $display, $key )  {
    if(
        isset( $element ) && $element !== 'hide'
        && ( $display === 'all' || $key < $display )
    ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if an element is enabled for each post when $element is an array
 *
 * @param array $element    Element(s) to display
 * @param boolean $display  Display or not the element?
 * @param int $key          Index of the element
 *
 * @return boolean
 */
function advgbCheckElementDisplayArr( $element, $display, $key )  {
    if(
        isset( $element ) && ! empty( $element )
        && ( $display === 'all' || $key < $display )
    ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if Featured image is enable for each post
 *
 * Deprecated since 2.13.3
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
 * Skip images floating on left or right, and with headline style for each post
 *
 * @return boolean
 */
function getDisplayImageVsOrder( $attributes, $key )  {
    $postView = isset( $attributes['postView'] ) && ! empty( $attributes['postView'] ) ? esc_html( $attributes['postView'] ) : 'grid';
    if(
        (
            (
                isset($attributes['orderSections']) && $attributes['orderSections']
                && ( in_array( esc_html( $attributes['orderSections'] ), array( 'default', 'image-title-info-text' ) ) )
            ) || (
                (
                    $postView === 'frontpage' && esc_html( $attributes['frontpageStyle'] ) === 'headline'
                ) || (
                    $postView === 'slider' && esc_html( $attributes['sliderStyle'] ) === 'headline'
                ) || (
                    $postView === 'list'
                ) || (
                    (
                        $postView === 'newspaper'
                    ) && (
                        in_array( esc_html( $attributes['newspaperLayout'] ), array( 'np-2','np-3-1','np-3-2','np-3-3' ) )
                        || $key > 0
                    )
                )
            )
        ) || !defined('ADVANCED_GUTENBERG_PRO')
    ) {
        return 'ignore-order';
    } else {
        return 'apply-order';
    }
}

/**
 * Get id of current user
 *
 * @return integer
 */
function advgbGetCurrentUserId()  {
    return is_user_logged_in() ? get_current_user_id() : 999999; // 999999 means user is a guest :)
}

/**
 * Returns post ids corresponding to post titles.
 *
 * Only for backward compatibility 2.13.1 and lower
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
 * Returns all valid authors.
 *
 * If PublishPress Authors plugin is active, only those authors are returned.
 *
 * @return array
 */
function advgbGetAllAuthors( WP_REST_Request $request ) {
	$authors = array();

	if ( function_exists( 'multiple_authors_get_all_authors' ) ) {
		$coauthors = multiple_authors_get_all_authors();
		foreach ( $coauthors as $coauthor ) {
			$name = $coauthor->__get( 'display_name' );
			$authors[ $name ] = (object) array( 'name' => $name, 'id' => $coauthor->__get( 'ID' ) );
		}
	} else {
		$users = get_users( array( 'per_page' => -1, 'who' => 'authors', 'fields' => 'all' ) );
		foreach ( $users as $user ) {
			$author = $user->data;
			$author->id = $author->ID;
			$author->name = $author->display_name;
			$authors[ $author->name ] = $author;
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

/**
 * Fires all the relevant hooks for CPTs.
 */
function advgbInitializeHooksForCPTs() {
	foreach ( advgbGetCPTs() as $cpt ) {
		add_filter( "rest_{$cpt}_query", 'advgbGetAuthorFilterREST', 10, 2 );
		add_filter( "rest_{$cpt}_query", 'advgbMultipleAuthorSortREST', 10, 2 );
		add_filter( "rest_{$cpt}_collection_params", 'advgbAllowCPTQueryVars' );
        add_filter( "rest_{$cpt}_query", 'advgbSetSeriesOrderREST', 10, 2 );
	}
}
add_action( 'init', 'advgbInitializeHooksForCPTs' );
