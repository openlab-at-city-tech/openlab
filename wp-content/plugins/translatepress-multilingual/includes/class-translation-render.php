<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Translation_Render
 *
 * Translates pages.
 */
class TRP_Translation_Render{
    protected $settings;
    protected $machine_translator;
    /* @var TRP_Query */
    protected $trp_query;
	/* @var TRP_Url_Converter */
    protected $url_converter;
    /* @var TRP_Translation_Manager */
	protected $translation_manager;
    protected $common_html_tags;

    /**
     * TRP_Translation_Render constructor.
     *
     * @param array $settings       Settings options.
     */
    public function __construct( $settings ){
        $this->settings = $settings;
        // apply_filters only once instead of everytime is_html() is used
        $this->common_html_tags = implode( '|', apply_filters('trp_common_html_tags', array( 'html', 'body', 'table', 'tbody', 'thead', 'th', 'td', 'tr', 'div', 'p', 'span', 'b', 'a', 'strong', 'center', 'br', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'img' ) ) );

    }

    /**
     * Start Output buffer to translate page.
     */
    public function start_output_buffer(){
        global $TRP_LANGUAGE;

        //when we check if is an ajax request in frontend we also set proper REQUEST variables and language global so we need to run this for every buffer
        $ajax_on_frontend = TRP_Gettext_Manager::is_ajax_on_frontend();//TODO refactor this function si it just checks and does not set variables

        if( ( is_admin() && !$ajax_on_frontend ) || trp_is_translation_editor( 'true' ) ){
            return;//we have two cases where we don't do anything: we are on the admin side and we are not in an ajax call or we are in the left side of the translation editor
        }
        else {
            global $trp_output_buffer_started;//use this global so we know that we started the output buffer. we can check it for instance when wrapping gettext
            mb_http_output("UTF-8");
            if ( $TRP_LANGUAGE == $this->settings['default-language'] && !trp_is_translation_editor() ) {
                // on default language when we are not in editor we just need to clear any trp tags that could still be present and handle links for special situation
                $chunk_size = ($this->handle_custom_links_for_default_language() ) ? 0 : 4096;
                $chunk_size = apply_filters("trp_output_buffer_chunk_size", $chunk_size);
                ob_start(array( $this, 'render_default_language' ), $chunk_size);
                $trp_output_buffer_started = true;
            } else {
                ob_start(array($this, 'translate_page'));//everywhere else translate the page
                $trp_output_buffer_started = true;
            }
        }
    }

    /**
     * Hide PHP errors and notices so they are not stored as translation strings when WP_DEBUG is on.
     *
     * Preserve an error log path already configured by WordPress or PHP.
     */
    public function trp_debug_mode_off(){
        if ( WP_DEBUG ) {
            ini_set( 'display_errors', 0 );
            ini_set( 'log_errors', 1 );

            $error_log = ini_get( 'error_log' );
            if ( $error_log === false || $error_log === '' ) {
                ini_set( 'error_log', WP_CONTENT_DIR . '/debug.log' );
            }
        }
    }

    /**
     * Forces the language to be the first non default one in the preview translation editor.
     * We're doing this because we need the ID's.
     * Otherwise we're just returning the global $TRP_LANGUAGE
     *
     * @return string       Language code.
     */
    protected function force_language_in_preview(){
        global $TRP_LANGUAGE;
        if ( in_array( $TRP_LANGUAGE, $this->settings['translation-languages'] ) ) {
            if ( $TRP_LANGUAGE == $this->settings['default-language']  ){
                // in the translation editor we need a different language then the default because we need string ID's.
                // so we're forcing it to the first translation language because if it's the default, we're just returning the $output
                if ( isset( $_REQUEST['trp-edit-translation'] ) && $_REQUEST['trp-edit-translation'] == 'preview' )  {
                    if( count( $this->settings['translation-languages'] ) > 1 ){
                        foreach ($this->settings['translation-languages'] as $language) {
                            if ($language != $TRP_LANGUAGE) {
                                // return the first language not default. only used for preview mode
                                return $language;
                            }
                        }
                    }
                    else{
                        return $TRP_LANGUAGE;
                    }
                }
            }else {
                return $TRP_LANGUAGE;
            }
        }
        return false;
    }

	/**
	 * Trim strings.
	 * This function is kept for backwards compatibility for earlier versions of SEO Pack Add-on
	 *
	 * @deprecated
	 * @param string $string      Raw string.
	 * @return string           Trimmed string.
	 */
	public function full_trim( $string ) {
		return trp_full_trim( $string );
	}

    /**
     * Preview mode string category name for give node type.
     *
     * @param string $current_node_type         Node type.
     * @return string                           Category name.
     */
    protected function get_node_type_category( $current_node_type ){
	    $trp = TRP_Translate_Press::get_trp_instance();
	    if ( ! $this->translation_manager ) {
		    $this->translation_manager = $trp->get_component( 'translation_manager' );
	    }
	    $string_groups = $this->translation_manager->string_groups();

        $node_type_categories = apply_filters( 'trp_node_type_categories', array(
            $string_groups['metainformation']   => array( 'meta_desc', 'page_title', 'meta_desc_img' ),
            $string_groups['images']            => array( 'image_src', 'picture_source_srcset', 'picture_image_src' ),
            $string_groups['videos']             => array( 'video_src', 'video_poster', 'video_source_src'),
            $string_groups['audios']             => array( 'audio_src', 'audio_source_src'),
        ));

        foreach( $node_type_categories as $category_name => $node_groups ){
            if ( in_array( $current_node_type, $node_groups ) ){
                return $category_name;
            }
        }

        return $string_groups['stringlist'];
    }

    /**
     * String description to be used in preview mode dropdown list of strings.
     *
     * @param object $current_node          Current node.
     * @return string                       Node description.
     */
    protected function get_node_description( $current_node ){
        $node_type_descriptions = apply_filters( 'trp_node_type_descriptions',
            array(
                array(
                    'type'          => 'meta_desc',
                    'attribute'     => 'name',
                    'value'         => 'description',
                    'description'   => esc_html__( 'Description', 'translatepress-multilingual' )
                ),
                array(
                    'type'          => 'meta_desc',
                    'attribute'     => 'property',
                    'value'         => 'article:section',
                    'description'   => esc_html__( 'Article Section', 'translatepress-multilingual' )
                ),
                array(
                    'type'          => 'meta_desc',
                    'attribute'     => 'property',
                    'value'         => 'article:tag',
                    'description'   => esc_html__( 'Article Tag', 'translatepress-multilingual' )
                ),
                array(
                    'type'          => 'meta_desc',
                    'attribute'     => 'property',
                    'value'         => 'og:title',
                    'description'   => esc_html__( 'OG Title', 'translatepress-multilingual' )
                ),
                array(
                    'type'          => 'meta_desc',
                    'attribute'     => 'property',
                    'value'         => 'og:site_name',
                    'description'   => esc_html__( 'OG Site Name', 'translatepress-multilingual' )
                ),
                array(
                    'type'          => 'meta_desc',
                    'attribute'     => 'property',
                    'value'         => 'og:description',
                    'description'   => esc_html__( 'OG Description', 'translatepress-multilingual' )
                ),
	            array(
		            'type'          => 'meta_desc',
		            'attribute'     => 'property',
		            'value'         => 'og:image:alt',
		            'description'   => esc_html__( 'OG Image Alt', 'translatepress-multilingual' )
	            ),
                array(
                    'type'          => 'meta_desc',
                    'attribute'     => 'name',
                    'value'         => 'twitter:title',
                    'description'   => esc_html__( 'Twitter Title', 'translatepress-multilingual' )
                ),
                array(
                    'type'          => 'meta_desc',
                    'attribute'     => 'name',
                    'value'         => 'twitter:description',
                    'description'   => esc_html__( 'Twitter Description', 'translatepress-multilingual' )
                ),
	            array(
		            'type'          => 'meta_desc',
		            'attribute'     => 'name',
		            'value'         => 'twitter:image:alt',
		            'description'   => esc_html__( 'Twitter Image Alt', 'translatepress-multilingual' )
	            ),
                array(
                    'type'          => 'page_title',
                    'description'   => esc_html__( 'Page Title', 'translatepress-multilingual' )
                ),
	            array(
		            'type'          => 'meta_desc',
		            'attribute'     => 'name',
		            'value'         => 'DC.Title',
		            'description'   => esc_html__( 'Dublin Core Title', 'translatepress-multilingual' )
	            ),
	            array(
		            'type'          => 'meta_desc',
		            'attribute'     => 'name',
		            'value'         => 'DC.Description',
		            'description'   => esc_html__( 'Dublin Core Description', 'translatepress-multilingual' )
	            ),
	            array(
		            'type'          => 'meta_desc_img',
		            'attribute'     => 'property',
		            'value'         => 'og:image',
		            'description'   => esc_html__( 'OG Image', 'translatepress-multilingual' )
	            ),
	            array(
		            'type'          => 'meta_desc_img',
		            'attribute'     => 'property',
		            'value'         => 'og:image:secure_url',
		            'description'   => esc_html__( 'OG Image Secure URL', 'translatepress-multilingual' )
	            ),
	            array(
		            'type'          => 'meta_desc_img',
		            'attribute'     => 'name',
		            'value'         => 'twitter:image',
		            'description'   => esc_html__( 'Twitter Image', 'translatepress-multilingual' )
	            ),

            ));

        foreach( $node_type_descriptions as $node_type_description ){
            if ( isset( $node_type_description['attribute'] )) {
                $attribute = $node_type_description['attribute'];
            }
            if ( $current_node['type'] == $node_type_description['type'] &&
                (
                    ( isset( $node_type_description['attribute'] ) && isset( $current_node['node']->$attribute ) && $current_node['node']->$attribute == $node_type_description['value'] ) ||
                    ( ! isset( $node_type_description['attribute'] ) )
                )
            ) {
                return $node_type_description['description'];
            }
        }

        return '';

    }

	/**
	 * Specific trim made for translation block string
	 *
	 * Problem especially for nbsp; which gets saved like that in DB. Then, in translation-render, the string arrives with nbsp; rendered to actual space character.
	 * Used before inserting in db, and when trying to match on translation-render.
     *
     * wp_strip_all_tags was moved before html_entity_decode functions because quotes (& #039;) within text
     * within html tag attributes would be decoded into a quote ' and made wp_strip_tags to cut more text based on the incorrect html.
     * Example of html that broke before this change: <div title='Voir les détails de l&#039;analyse de sécurité'></div>
	 *
	 * @param $string
	 *
	 * @return string
	 */
    public function trim_translation_block( $string ){
	    return preg_replace('/\s+/', ' ',   html_entity_decode( htmlspecialchars_decode( $this->strip_all_tags_tolerantly(trp_full_trim( $string )), ENT_QUOTES ) ) ) ;
    }

	/**
	 * wp_strip_all_tags() replacement that survives malformed markup.
	 *
	 * strip_tags() discards everything that follows an unterminated tag, so a single customer typo
	 * such as </span< in an Elementor widget emptied the rest of the page for trim_translation_block(),
	 * and every translation block below that typo stopped being recognized and split back into
	 * individual strings on each page load.
	 *
	 * Matches wp_strip_all_tags() on well-formed markup: script and style contents are dropped,
	 * comments are dropped, everything else between < and > is removed.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public function strip_all_tags_tolerantly( $string ){
		$stripped = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
		$stripped = preg_replace( '/<!--.*?-->/s', '', $stripped );
		// quote-aware so that a > inside an attribute value does not end the tag early
		$stripped = preg_replace( '/<[^>"\']*(?:(?:"[^"]*"|\'[^\']*\')[^>"\']*)*>/', '', $stripped );

		// any of the above returns null if the string defeats the backtrack limit
		return ( $stripped === null ) ? wp_strip_all_tags( $string ) : trim( $stripped );
	}

    /**
     * Recursive function that checks if a DOM node contains certain tags or not
     * @param $row
     * @param $tags
     * @return bool
     */
    public function check_children_for_tags( $row, $tags ){
        foreach ( $row->children as $child ) {
            if ( in_array( $child->tag, $tags ) ) {
                return true;
            } else {
                if ( $this->check_children_for_tags( $child, $tags ) ) {
                    return true;
                }
            }
        }
        return false;
    }

	/**
	 * Return translation block if matches any existing translation block from db
	 *
	 * Return null if not found
	 *
	 * @param $row
	 * @param $all_existing_translation_blocks
	 * @param $merge_rules
	 *
	 * @return bool
	 */
    public function find_translation_block( $row, $all_existing_translation_blocks, $merge_rules ){
    	if ( in_array( $row->tag, $merge_rules['top_parents'] ) ){
            //$row->innertext is very intensive on dom nodes that have a lot of children so we try here to eliminate as many as possible here
            // the ideea is that if a dom node contains any top parent tags for blocks it can't be a block itself so we skip it
            $skip = $this->check_children_for_tags( $row, $merge_rules['top_parents'] );
            if( !$skip ) {
                $trimmed_inner_text = $this->trim_translation_block($row->innertext);
                foreach ($all_existing_translation_blocks as $existing_translation_block) {
                    if ($existing_translation_block->trimmed_original == $trimmed_inner_text) {
                        return $existing_translation_block;
                    }
                }
            }
	    }
	    return null;
    }

    /**
     * Function that translates the content post title, site title and post content in oembed response
     *
     * @param $data
     * @param $post
     * @param $width
     * @param $height
     *
     * @return array
     */
    public function oembed_response_data($data, $post, $width, $height ){
        if ( !empty( $data )) {
            $translatable_items = apply_filters( 'trp_oembed_response_data_translatable_items', array('title', 'html', 'provider_name') );
            foreach( $translatable_items as $item ){
                if ( isset( $data[$item] ) ) {
                    $data[$item] = $this->translate_page( $data[$item] );
                }
            }
        }

        // Otherwise we incorrectly unescape the sequence to end CDATA from ']]&gt;' to ']]>' breaking the xml. It needs to stay escaped in oembed response data.
        remove_filter( 'trp_before_translate_content', array( $this, 'handle_cdata'), 1000);

        return $data;
    }

	/**
	 * Handle generic REST API translations using configurable rules
     * hooked on rest_pre_echo_response in class-translate-press.php
	 * @param array $result
	 * @param WP_REST_Server $server
	 * @param WP_REST_Request $request
	 * @return array
	 */
	public function handle_generic_rest_api_translations( $result, $server, $request ) {
        $trp = TRP_Translate_Press::get_trp_instance();
        $url_converter = $trp->get_component( 'url_converter' );
        $language = $url_converter->get_lang_from_url_string( $url_converter->cur_page_url() );

        if ( $language == $this->settings['default-language'] || $language == null ) {
            return $result; // exit early in default language.
        }

		// Get REST API translation configuration
		$translation_config = $this->get_rest_api_translation_config();
		
		// Check if this request matches any configured REST API paths
		$route = $request->get_route();
		$matching_config = $this->find_matching_rest_api_config( $route, $translation_config );
		
		if ( ! $matching_config ) {
			return $result; // No translation rules for this route
		}

		// Translate the REST API response data using the matching configuration
		if ( is_array( $result ) ) {
			$max_depth = apply_filters( 'trp_rest_api_translation_max_depth', 5 );
			$result = $this->translate_rest_api_data_recursive( $result, $matching_config, $language, 0, $max_depth );
		}

		return $result;
	}

	/**
	 * Get REST API translation configuration
	 * @return array
	 */
	private function get_rest_api_translation_config() {
		$default_config = array(
            'wp/v2/search' => array( 'title' ), // Search API
            'wp/v2/comments' => array( 'content' ), // Comments API
			'wc/store/' => array( 'name', 'description', 'short_description' ) // WooCommerce Store API
		);
		
		// Add all WordPress post types dynamically
		$post_types = get_post_types( array( 'public' => true, 'show_in_rest' => true ), 'objects' );
		foreach ( $post_types as $post_type ) {
			$rest_base = $post_type->rest_base ? $post_type->rest_base : $post_type->name;
			$default_config['wp/v2/' . $rest_base] = array( 'title', 'content', 'excerpt', 'name', 'description' );
		}
		
		// Add all taxonomies dynamically
		$taxonomies = get_taxonomies( array( 'public' => true, 'show_in_rest' => true ), 'objects' );
		foreach ( $taxonomies as $taxonomy ) {
			$rest_base = $taxonomy->rest_base ? $taxonomy->rest_base : $taxonomy->name;
			$default_config['wp/v2/' . $rest_base] = array( 'name', 'description' );
		}
		
		return apply_filters( 'trp_rest_api_translation_config', $default_config );
	}

	/**
	 * Find matching configuration for a REST API route
	 * @param string $route
	 * @param array $config
	 * @return array|false
	 */
	private function find_matching_rest_api_config( $route, $config ) {
		foreach ( $config as $pattern => $keys ) {
			if ( strpos( $route, $pattern ) !== false ) {
				return $keys;
			}
		}
		return false;
	}

	/**
	 * Recursively translate REST API data based on configuration
	 * @param array $data
	 * @param array $translatable_keys
	 * @param string $language
	 * @param int $current_depth
	 * @param int $max_depth
	 * @return array
	 */
	private function translate_rest_api_data_recursive( $data, $translatable_keys, $language, $current_depth = 0, $max_depth = 5 ) {
		if ( $current_depth >= $max_depth || ! is_array( $data ) ) {
			return $data;
		}

        $skip_shortcode_translation = apply_filters( 'trp_rest_api_skip_shortcode_translation', true, $data );

		foreach ( $translatable_keys as $field ) {
			// Check for direct field first
			if ( isset( $data[$field] ) && is_string( $data[$field] ) ) {
                // Skip shortcodes due to MT quota consumption
				if ( $this->rest_value_has_shortcode( $data[$field] ) && $skip_shortcode_translation ) {
					continue;
				}
				$data[$field] = $this->translate_page( $data[$field] );
			}
			// For title, content, excerpt - also check .rendered subfield
			elseif ( in_array( $field, array( 'title', 'content', 'excerpt' ) ) ) {
				if ( isset( $data[$field]['rendered'] ) && is_string( $data[$field]['rendered'] ) ) {
					if ( $this->rest_value_has_shortcode( $data[ $field]['rendered'] ) && $skip_shortcode_translation ) {
						continue;
					}
					$data[$field]['rendered'] = $this->translate_page( $data[$field]['rendered'] );
				}
			}
		}

		// Handle arrays and nested objects recursively
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$data[$key] = $this->translate_rest_api_data_recursive( $value, $translatable_keys, $language, $current_depth + 1, $max_depth );
			}
		}

		// Handle special case for slug translation if available
		if ( isset( $data['slug'] ) && is_string( $data['slug'] ) && class_exists( 'TRP_Slug_Query' ) ) {
			$trp_slug_query = new TRP_Slug_Query();
			$slug_array = array( $data['slug'] );
			$translated_slugs = $trp_slug_query->get_translated_slugs_from_original( $slug_array, $language );

			if ( !empty( $translated_slugs ) && isset( $translated_slugs[$data['slug']] ) ) {
				$data['slug'] = $translated_slugs[$data['slug']];
			}
		}

		return $data;
	}

	/**
	 * Check if a value contains shortcodes.
	 *
	 * @param string $value
	 * @return bool
	 */
	private function rest_value_has_shortcode( $value ) {
		if ( ! is_string( $value ) || strpos( $value, '[' ) === false ) {
			return false;
		}

		$pattern = get_shortcode_regex();
		if ( ! empty( $pattern ) && preg_match( '/' . $pattern . '/s', $value ) ) {
			return true;
		}

		return preg_match( '/\\[[^\\]]+\\]/', $value ) === 1;
	}

    /**
     * Finding translateable strings and replacing with translations.
     *
     * Method called for output buffer.
     *
     * @param string $output        Entire HTML page as string.
     * @return string               Translated HTML page.
     */
    public function translate_page( $output ){
    	if ( apply_filters( 'trp_stop_translating_page', false, $output ) ){
    		return $output;
	    }

        global $TRP_HDOM_QUOTE_DEFAULT;
        $TRP_HDOM_QUOTE_DEFAULT = apply_filters('trp_hdom_quote_default_double_quotes', '"');

    	global $trp_editor_notices;

        /* replace our special tags so we have valid html */
        $output = $this->replace_gettext_markers_with_html_tags( $output );

        $output = apply_filters('trp_before_translate_content', $output);

        /* remove unwanted tags. For example,we're removing script and style because they should not be translated and if they are large cause big performance issues */
        $excluded_tags = apply_filters('trp_excluded_tags_from_translation', array('script', 'style'));
        $output_with_excluded_tags_removed = $this->remove_tags_from_output($output, $excluded_tags); // $removed_tags = array('output' => '$output string', 'excluded_tags' => array());

        $output = apply_filters('trp_after_excluded_tags_from_translation', $output_with_excluded_tags_removed['output']);

        if ( $output == false || !is_string( $output ) || strlen( $output ) < 1 ) {
            return $output;
        }

        if ( ! $this->url_converter ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->url_converter = $trp->get_component('url_converter');
        }

        if( $this->url_converter->is_sitemap_path( $this->url_converter->cur_page_url( false )) ){
            return $output;
        }

        /* make sure we only translate on the rest_prepare_$post_type filter in REST requests and not the whole json */

        /* in certain cases $wp_rewrite is null, so it trows a fatal error. This is just a quick fix. The actual issue is probably in WordPress core
         * see taskid #2pjped
         */
        global $wp_rewrite;
        if( is_object($wp_rewrite) ) {
            if( strpos( $this->url_converter->cur_page_url( false ), get_rest_url() ) !== false
                && current_filter() !== 'oembed_response_data'
                && current_filter() !== 'rest_pre_echo_response'
                && current_filter() !== 'wp_mail'
            )
            {
                $trpremoved = $this->remove_trp_html_tags( $output );
                /* add back the excluded tags like script and style to the html */
                $trpremoved = $this->add_excluded_tags_after_translation( $trpremoved, $output_with_excluded_tags_removed['excluded_tags'] );
                return $trpremoved;
            }
        }

        /* don't do anything on xmlrpc.php  */
        if( strpos( $this->url_converter->cur_page_url( false ), 'xmlrpc.php' ) !== false ){
            $trpremoved = $this->remove_trp_html_tags( $output );
            /* add back the excluded tags like script and style to the html */
            $trpremoved = $this->add_excluded_tags_after_translation( $trpremoved, $output_with_excluded_tags_removed['excluded_tags'] );
            return $trpremoved;
        }

        global $TRP_LANGUAGE;
        $language_code = $this->force_language_in_preview();
        if ($language_code === false) {
            /* add back the excluded tags like script and style to the html */
            $output = $this->add_excluded_tags_after_translation( $output, $output_with_excluded_tags_removed['excluded_tags'] );
            /* strip leftover trp-gettext markers. Direct callers (wp_mail_filter, oembed,
               REST) hit this path when $TRP_LANGUAGE is the default language; the regular
               frontend output buffer handles default-language stripping via
               render_default_language() instead. */
            $output = $this->remove_trp_html_tags( $output );
            return $output;
        }
        if ( $language_code == $this->settings['default-language'] ){
        	// Don't translate regular strings (non-gettext) when we have no other translation languages except default language ( count( $this->settings['publish-languages'] ) > 1 )
        	$translate_normal_strings = false;
        }else{
	        $translate_normal_strings = true;
        }

        $translate_normal_strings = apply_filters( 'trp_translate_regular_strings', $translate_normal_strings );

	    $preview_mode = isset( $_REQUEST['trp-edit-translation'] ) && $_REQUEST['trp-edit-translation'] == 'preview';

        $json_array = json_decode( $output, true );
	    /* If we have a json response we need to parse it and only translate the nodes that contain html
	     *
	     * Removed is_ajax_on_frontend() check because we need to capture custom ajax events.
		 * Decided that if $output is json decodable it's a good enough check to handle it this way.
		 * We have necessary checks so that we don't get to this point when is_admin(), or when language is default.
	     */
	    if( $json_array && $json_array != $output ) {
		    /* if it's one of our own ajax calls don't do nothing */
            if ( ! empty( $_REQUEST['action'] ) && strpos( sanitize_text_field( $_REQUEST['action'] ), 'trp_' ) === 0 && $_REQUEST['action'] != 'trp_split_translation_block' ){
                /* add back the excluded tags like script and style to the html */
                $output = $this->add_excluded_tags_after_translation( $output, $output_with_excluded_tags_removed['excluded_tags'] );
                return $output;
            }

	        //check if we have a json response
	        if ( ! empty( $json_array ) ) {
	            if( is_array( $json_array ) ) {
                    array_walk_recursive($json_array, array($this, 'translate_json'));
                }else {
                    $json_array = $this->translate_page($json_array);
                }
	        }

	        return trp_safe_json_encode( $json_array );
        }

        /**
         * Tries to fix the HTML document. It is off by default. Use at own risk.
         * Solves the problem where a duplicate attribute inside a tag causes the plugin to remove the duplicated attribute and all the other attributes to the right of the it.
         */

        $output = apply_filters( 'trp_pre_translating_html', $output );

        $no_translate_attribute      = 'data-no-translation';
        $no_auto_translate_attribute = 'data-no-auto-translation';

        $translateable_strings = array();
        $translateable_strings_manual = array();
	    $skip_machine_translating_strings = array();
        $do_not_add_this_alug_to_dictionary_table = array();
        $nodes = array();
        $nodes_manual = array();

	    $trp = TRP_Translate_Press::get_trp_instance();
	    if ( ! $this->trp_query ) {
		    $this->trp_query = $trp->get_component( 'query' );
	    }
	    if ( ! $this->translation_manager ) {
		    $this->translation_manager = $trp->get_component( 'translation_manager' );
	    }

	    $html = TranslatePress\str_get_html($output, true, true, TRP_DEFAULT_TARGET_CHARSET, false, TRP_DEFAULT_BR_TEXT, TRP_DEFAULT_SPAN_TEXT);
	    if ( $html === false ){
            $trpremoved = $this->remove_trp_html_tags( $output );
            /* add back the excluded tags like script and style to the html */
            $trpremoved = $this->add_excluded_tags_after_translation( $trpremoved, $output_with_excluded_tags_removed['excluded_tags'] );
		    return $trpremoved;
	    }

	    $count_translation_blocks = 0;
	    if ( $translate_normal_strings ) {
		    $all_existing_translation_blocks = $this->trp_query->get_all_translation_blocks( $language_code );
		    // trim every translation block original now, to avoid over-calling trim function later
		    foreach ( $all_existing_translation_blocks as $key => $existing_tb ) {
			    $all_existing_translation_blocks[ $key ]->trimmed_original = $this->trim_translation_block( $all_existing_translation_blocks[ $key ]->original );
		    }

		    /* Try to find if there are any blocks in the output for translation.
		     * If the output is an actual html page, use only the innertext of body tag
		     * Else use the entire output (ex. the output is from JSON REST API content, or just a string)
		     */
		    $html_body = $html->find('body', 0 );
		    $output_to_translate = ( $html_body ) ?  $html_body->innertext : $output;


		    $trimmed_html_body = $this->trim_translation_block( $output_to_translate );
            foreach( $all_existing_translation_blocks as $key => $existing_translation_block ){
                if (  (empty($existing_translation_block->trimmed_original )) || (strpos( $trimmed_html_body, $existing_translation_block->trimmed_original ) === false )){
                    unset($all_existing_translation_blocks[$key] );//if it isn't present remove it, this way we don't look for them on pages that don't contain blocks
                }
            }
            $count_translation_blocks = count( $all_existing_translation_blocks );//see here how many remain on the current page

		    $merge_rules = $this->translation_manager->get_merge_rules();
	    }

        /**
         * When we are in the translation editor: Intercept the trp-gettext that was wrapped around all the gettext texts, grab the attribute data-trpgettextoriginal
         * which contains the original translation id and move it to the parent node if the parent node only contains that string then remove the  wrap trp-gettext, otherwise replace it with another tag.
         * Also set a no-translation attribute.
         * When we are in a live translation case: Intercept the trp-gettext that was wrapped around all the gettext texts, set a no-translation attribute to the parent node if the parent node only contains that string
         * then remove the  wrap trp-gettext, otherwise replace the wrap with another tag and do the same to it
         * We identified two cases: the wrapper trp-gettext can be as a node in the dome or ot can be inside a html attribute ( for example value )
         * and we need to treat them differently
         */

        /* store the nodes in arrays so we can sort the $trp_rows which contain trp-gettext nodes from the DOM according to the number of children and we process the simplest first */
        $trp_rows = array();
        $trp_attr_rows = array();
        foreach ( $html->find("*[!nuartrebuisaexiteatributulasta]") as $k => $row ){
            if( $row->hasAttribute('data-trpgettextoriginal') ){
                $trp_rows[count( $row->children )][] = $row;
            }
            else{
                if( $row->nodetype !== 5 && $row->nodetype !== 3 )//add all tags that are not root or text, text nodes can't have attributes
                    $trp_attr_rows[] = $row;

	            if ( $translate_normal_strings && $count_translation_blocks > 0 ) {
		            $translation_block = $this->find_translation_block( $row, $all_existing_translation_blocks, $merge_rules );
		            if ( $translation_block ) {
			            $existing_classes = $row->getAttribute( 'class' );
			            if ( $translation_block->block_type == 1 ) {
				            $found_inner_translation_block = false;
				            foreach ( $row->children() as $child ) {
					            if ( $this->find_translation_block( $child, array( $translation_block ), $merge_rules ) != null ) {
						            $found_inner_translation_block = true;
						            break;
					            }
				            }
				            if ( ! $found_inner_translation_block ) {
					            // make sure we find it later exactly the way it is in DB
					            $row->innertext = $translation_block->original;
					            $row->setAttribute( 'class', $existing_classes . ' translation-block' );
				            }
			            } else if ( $preview_mode && $translation_block->block_type == 2 && $translation_block->status != 0 ) {
				            // refactor to not do this for each
				            $row->setAttribute( 'data-trp-translate-id', $translation_block->id );
				            $row->setAttribute( 'data-trp-translate-id-deprecated', $translation_block->id );
				            $row->setAttribute( 'class', $existing_classes . 'trp-deprecated-tb' );
			            }
		            }
	            }

            }
        }

        /* sort them here ascending by key where the key is the number of children */
        /* here we add support for gettext inside gettext */
        ksort($trp_rows);
        foreach( $trp_rows as $level ){
            foreach( $level as $row ){
                /* never write this value back out unvalidated, see sanitize_gettext_original_id() */
                $original_gettext_translation_id = $this->sanitize_gettext_original_id( $row->getAttribute('data-trpgettextoriginal') );
                /* Parent node has no other children and no other innertext besides the current node */
                if( count( $row->parent()->children ) == 1 && $row->parent()->innertext == $row->outertext ){
                    $row->outertext = $row->innertext();
                    $row->parent()->setAttribute($no_translate_attribute, '');
	                $row->parent()->setAttribute('data-trp-gettext', '');
                    // we are in the editor
                    if (isset($_REQUEST['trp-edit-translation']) && $_REQUEST['trp-edit-translation'] == 'preview') {
                        //move up the data-trpgettextoriginal attribute
                        /* esc_attr because simple_html_dom::makeup() writes the stored value verbatim */
                        $row->parent()->setAttribute('data-trpgettextoriginal', esc_attr( $original_gettext_translation_id ));
                    }
                }
                else{
                    /* Setting this attribute using setAttribute function actually changes the $html object.
                    Important for not detecting this gettext as a regular string in the next lines using find()  */
                    $row->setAttribute($no_translate_attribute, '');

                    /* Changes made to outertext take place only after saving the html object to a string */
                    $row->outertext = '<trp-wrap class="trp-wrap" data-no-translation';
                    if (isset($_REQUEST['trp-edit-translation']) && $_REQUEST['trp-edit-translation'] == 'preview') {
                        $row->outertext .= ' data-trpgettextoriginal="'. esc_attr( $original_gettext_translation_id ) .'"';
                    }
                    $row->outertext .= '>'.$row->innertext().'</trp-wrap>';
                }
            }
        }

        foreach( $trp_attr_rows as $row ){
            $all_attributes = $row->getAllAttributes();
            if( !empty( $all_attributes ) ) {
                foreach ($all_attributes as $attr_name => $attr_value) {
                    if (strpos($attr_value, 'trp-gettext ') !== false) {
                        //if we have json content in the value of the attribute, we don't do anything. The trp-wrap will be removed later in the code
                        if (is_array($json_array = json_decode( html_entity_decode( $attr_value, ENT_QUOTES ), true ) ) ) {
                            continue;
                        }

                        // convert to a node
                        $node_from_value = TranslatePress\str_get_html(html_entity_decode(htmlspecialchars_decode($attr_value, ENT_QUOTES)), true, true, TRP_DEFAULT_TARGET_CHARSET, false, TRP_DEFAULT_BR_TEXT, TRP_DEFAULT_SPAN_TEXT);
	                    if ( $node_from_value === false ){
		                    continue;
	                    }
                        foreach ($node_from_value->find('trp-gettext') as $nfv_row) {
                            $nfv_row->outertext = $nfv_row->innertext();
	                        $saved_node_from_value = $node_from_value->save();

	                        // unwrapping the marker ( and decoding the value ) must not expose a script scheme that
	                        // wp_kses never saw, e.g. href="&lt;trp-gettext /?&gt;javascript:..." [CU-869f8bpxr]
	                        if ( $this->is_url_attribute( $attr_name ) && $this->has_unsafe_url_scheme( $saved_node_from_value ) ) {
		                        $saved_node_from_value = '';
	                        }

	                        // attributes of these tags are not handled well by the parser so don't escape them [see iss6264]
	                        if ( $row->tag != 'script' && $row->tag != 'style' ){
		                        $saved_node_from_value = esc_attr($saved_node_from_value);
	                        }

	                        $row->setAttribute($attr_name, $saved_node_from_value );
                            $row->setAttribute($no_translate_attribute . '-' . $attr_name, '');
                            // we are in the editor
                            if (isset($_REQUEST['trp-edit-translation']) && $_REQUEST['trp-edit-translation'] == 'preview') {
                                /* this node was rebuilt from a host attribute value that can carry reflected
                                   user input, so the id is untrusted here. See sanitize_gettext_original_id() */
                                $original_gettext_translation_id = $this->sanitize_gettext_original_id( $nfv_row->getAttribute('data-trpgettextoriginal') );
                                $row->setAttribute('data-trpgettextoriginal-' . $attr_name, esc_attr( $original_gettext_translation_id ));
                            }

                        }
                    }
                }
            }
        }


	    if ( ! $translate_normal_strings ) {
            /* save it as a string */
            $trpremoved = $html->save();
            /* perform preg replace on the remaining trp-gettext tags */
            $trpremoved = $this->remove_trp_html_tags($trpremoved );
            /* add back the excluded tags like script and style to the html */
            $trpremoved = $this->add_excluded_tags_after_translation( $trpremoved, $output_with_excluded_tags_removed['excluded_tags'] );
            return $trpremoved;
	    }

        $no_translate_selectors = apply_filters( 'trp_no_translate_selectors', array( '#wpadminbar' ), $TRP_LANGUAGE );
        $ignore_cdata = apply_filters('trp_ignore_cdata', true );
        $translate_encoded_html_as_string = apply_filters('trp_translate_encoded_html_as_string', false );
        $translate_encoded_html_as_html = apply_filters('trp_translate_encoded_html_as_html', true );
        // used for skipping minified scripts but can be used for anything
        $skip_strings_containing_key_terms = apply_filters('trp_skip_strings_containing_key_terms',
            array(
                array(
                    'terms'=> array( 'function', 'return', 'if', '==' ),
                    'operator' => 'and'
                )
            )
        );

        /*
         * process the types of strings we can currently have: no-translate, translation-block, text, input, textarea, etc.
         */

        foreach ( $no_translate_selectors as $no_translate_selector ){
            foreach ( $html->find( $no_translate_selector ) as $k => $row ){
                $row->setAttribute( $no_translate_attribute, '' );
            }
        }

        $no_auto_translate_selectors = apply_filters( 'trp_no_auto_translate_selectors', array( ), $TRP_LANGUAGE );
        foreach ( $no_auto_translate_selectors as $no_auto_translate_selector ){
            foreach ( $html->find( $no_auto_translate_selector ) as $k => $row ){
                $row->setAttribute( $no_auto_translate_attribute, '' );
            }
        }


        foreach ( $html->find('.translation-block') as $row ){
            $trimmed_string = trp_full_trim( $row->innertext );
            $parent = $row->parent();
            if( $trimmed_string!=""
                && $parent->tag!="script"
                && $parent->tag!="style"
                && $parent->tag != 'title'
                && strpos($row->outertext,'[vc_') === false
                && !$this->trp_is_numeric($trimmed_string)
                && !preg_match('/^\d+%$/',$trimmed_string)
                && $row->find_ancestor_tag( 'script' ) === null // sometimes the script/style has an html tree that gets detected, so script/style is not a direct parent
                && $row->find_ancestor_tag( 'style' ) === null
                && !$this->has_ancestor_attribute( $row, $no_translate_attribute ) )
            {
                $string_count = array_push( $translateable_strings, $trimmed_string );
                array_push( $nodes, array('node' => $row, 'type' => 'block'));

                if ( ! apply_filters( 'trp_allow_machine_translation_for_string', true, $trimmed_string, null, null, $row ) ){
                    array_push( $skip_machine_translating_strings, $trimmed_string );
                }

                //add data-trp-post-id attribute if needed
                $nodes = $this->maybe_add_post_id_in_node( $nodes, $row, $string_count );
            }
        }

        foreach ( $html->find('trptext') as $row ){
            $outertext = $row->outertext;
            $parent = $row->parent();
            $trimmed_string = trp_full_trim( $outertext );
            if( $trimmed_string!=""
                && $parent->tag!="script"
                && $parent->tag!="style"
                && $parent->tag != 'title'
                && $parent->tag != 'textarea' //explicitly exclude textarea strings
                && strpos($outertext,'[vc_') === false
                && !$this->trp_is_numeric($trimmed_string)
                && !preg_match('/^\d+%$/',$trimmed_string)
                && !$this->has_ancestor_attribute( $row, $no_translate_attribute )
                && !$this->has_ancestor_class( $row, 'translation-block')
                && $row->find_ancestor_tag( 'script' ) === null // sometimes the script/style has an html tree that gets detected, so script/style is not a direct parent
                && $row->find_ancestor_tag( 'style' ) === null
                && ( !$ignore_cdata || ( strpos($trimmed_string, '<![CDATA[') !== 0 && strpos($trimmed_string, '&lt;![CDATA[') !== 0  ) )
                && (strpos($trimmed_string, 'BEGIN:VCALENDAR') !== 0)
                && !$this->contains_substrings($trimmed_string, $skip_strings_containing_key_terms ) )
            {
                if ( !$translate_encoded_html_as_string ){
                    $is_html = false;
                    if ( $translate_encoded_html_as_html ){
                        if ( $this->is_html($trimmed_string) ){
                            // prevent potential infinite loops. Only call translate_page once recursively
                            add_filter( 'trp_translate_encoded_html_as_html', '__return_false' );

                            $row->outertext = str_replace( $trimmed_string, $this->translate_page( $trimmed_string ), $row->outertext );
                            remove_filter( 'trp_translate_encoded_html_as_html', '__return_false' );
                            $is_html = true;
                        }else {
                            $entity_decoded_trimmed_string = html_entity_decode( $trimmed_string );
                            if ( $this->is_html( $entity_decoded_trimmed_string ) ) {
                                // prevent potential infinite loops. Only call translate_page once recursively
                                add_filter( 'trp_translate_encoded_html_as_html', '__return_false' );

                                $row->outertext = str_replace( $trimmed_string, htmlentities( $this->translate_page( $entity_decoded_trimmed_string ) ), $row->outertext );
                                remove_filter( 'trp_translate_encoded_html_as_html', '__return_false' );
                                $is_html = true;
                            }
                        }
                    }
                    if ( $is_html ) {
                        continue;
                    }
                }

                // $translateable_strings array needs to be in sync in $nodes array
                $string_count = array_push( $translateable_strings, $trimmed_string );
                $node_type_to_push = ( in_array( $parent->tag, array( 'button', 'option' ) ) ) ? $parent->tag : 'text';
                array_push($nodes, array('node' => $row, 'type' => $node_type_to_push ));

                if ( ! apply_filters( 'trp_allow_machine_translation_for_string', true, $trimmed_string, null, null, $row ) ){
                    array_push( $skip_machine_translating_strings, $trimmed_string );
                }

                if ( $parent->tag == 'a' && ! apply_filters( 'trp_allow_machine_translation_for_url', true, $trimmed_string ) ){
                    array_push( $skip_machine_translating_strings, $trimmed_string );
                    array_push( $do_not_add_this_alug_to_dictionary_table, $trimmed_string );
                }

                //add data-trp-post-id attribute if needed
                $nodes = $this->maybe_add_post_id_in_node( $nodes, $row, $string_count );
            }

            $row = apply_filters( 'trp_process_other_text_nodes', $row );

        }

	    //set up general links variables
	    $home_url = home_url();

	    $node_accessors = $this->get_node_accessors();

	    /*
	     * Node types that carry a media URL (image/video/audio) rather than translatable text.
	     * These are diverted to the manual path below so that, outside preview mode, they are
	     * looked up but never inserted into the database on the front-end. This prevents DB bloat
	     * from dynamic/responsive media URLs. We key on the node type instead of the accessor
	     * because media URLs arrive through several accessors ('src', 'srcset', 'poster', and
	     * 'content' for og:image/twitter:image meta tags registered by the SEO Pack add-on) while
	     * the 'content' accessor is also shared with translatable text meta tags (meta_desc).
	     */
	    $media_url_node_types = apply_filters( 'trp_media_url_node_types', array(
		    'image_src', 'picture_image_src', 'picture_source_srcset',
		    'video_src', 'video_poster', 'video_source_src',
		    'audio_src', 'audio_source_src', 'meta_desc_img',
	    ) );

	    foreach( $node_accessors as $node_accessor_key => $node_accessor ){
	    	if ( isset( $node_accessor['selector'] ) ){
			    foreach ( $html->find( $node_accessor['selector'] ) as $k => $row ){
			    	$current_node_accessor_selector = $node_accessor['accessor'];
				    $trimmed_string = trp_full_trim( $row->$current_node_accessor_selector );
                    $translate_href = false;
			    	if ( $current_node_accessor_selector === 'href' ) {
					    $translate_href = ( $this->is_external_link( $trimmed_string, $home_url ) || $this->url_converter->url_is_file( $trimmed_string ) || $this->url_converter->url_is_extra($trimmed_string) );
					    $translate_href = apply_filters( 'trp_translate_this_href', $translate_href, $row, $TRP_LANGUAGE, $trimmed_string );
					    $trimmed_string = ( $translate_href ) ? $trimmed_string : '';
				    }
                    // outside preview mode we build the $translateable_strings_manual array for href
                    // the similar condition above needs to remain in place for backwords compatibility with the filter trp_translate_this_href
                    if ( $translate_href && !$preview_mode )
                    {
                        $translateable_strings_manual[] = html_entity_decode( $trimmed_string );
                        $nodes_manual[] = array('node' => $row, 'type' => $node_accessor_key);
                        // reset the string so it's excluded from $translateable_strings (no longer inserted in the database in front-end)
                        $trimmed_string = '';
                    }

                    // outside preview mode we build the $translateable_strings_manual array for media URLs
                    // (image/video/audio src, srcset, poster and og:image/twitter:image meta tags).
                    // Keyed on node type rather than accessor since media URLs arrive via 'src', 'srcset',
                    // 'poster' and 'content', while 'content' is also used by translatable text meta tags.
                    if ( in_array( $node_accessor_key, $media_url_node_types, true ) && !$preview_mode && $trimmed_string != ''){
                        $translateable_strings_manual[] = html_entity_decode( $trimmed_string );
                        $nodes_manual[] = array('node' => $row, 'type' => $node_accessor_key);
                        // reset the string so it's excluded from $translateable_strings (no longer inserted in the database in front-end)
                        $trimmed_string = '';
                    }

				    if( $trimmed_string!=""
				        && !$this->trp_is_numeric($trimmed_string)
				        && !preg_match('/^\d+%$/',$trimmed_string)
				        && !$this->has_ancestor_attribute( $row, $no_translate_attribute )
				        && !$this->has_ancestor_attribute( $row, $no_translate_attribute . '-' . $current_node_accessor_selector )
				        && !$this->has_ancestor_class( $row, 'translation-block')
				        && $row->tag != 'link'
                        && ( !$ignore_cdata || ( strpos($trimmed_string, '<![CDATA[') !== 0 && strpos($trimmed_string, '&lt;![CDATA[') !== 0  ) )
                        && (strpos($trimmed_string, 'BEGIN:VCALENDAR') !== 0 )
                        && !$this->contains_substrings($trimmed_string, $skip_strings_containing_key_terms ) )
				    {
                        $entity_decoded_trimmed_string = html_entity_decode( $trimmed_string );
                        if ( !$translate_encoded_html_as_string ){
                            if ( $translate_encoded_html_as_html ){
                                if ( $this->is_html($entity_decoded_trimmed_string) ){
                                    // prevent potential infinite loops. Only call translate_page once recursively
                                    add_filter( 'trp_translate_encoded_html_as_html', '__return_false' );

                                    $row->setAttribute( $current_node_accessor_selector, str_replace( $trimmed_string, esc_attr( htmlentities($this->translate_page( $entity_decoded_trimmed_string )) ), $row->$current_node_accessor_selector ) );
                                    remove_filter( 'trp_translate_encoded_html_as_html', '__return_false' );
                                    continue;
                                }
                            }
                        }

					    array_push( $translateable_strings, $entity_decoded_trimmed_string );
					    array_push( $nodes, array( 'node'=>$row, 'type' => $node_accessor_key ) );
					    if ( ! apply_filters( 'trp_allow_machine_translation_for_string', true, $entity_decoded_trimmed_string, $current_node_accessor_selector, $node_accessor, $row ) ){
					    	array_push( $skip_machine_translating_strings, $entity_decoded_trimmed_string );
					    }
				    }
			    }
		    }
	    }

        $translateable_information = array( 'translateable_strings' => $translateable_strings, 'nodes' => $nodes );
        $translateable_information = apply_filters( 'trp_translateable_strings', $translateable_information, $html, $no_translate_attribute, $TRP_LANGUAGE, $language_code, $this );
        $translateable_strings = $translateable_information['translateable_strings'];
        $nodes = $translateable_information['nodes'];

        if ( !empty( $translateable_information['nodes'] ) ) {
            foreach ( $translateable_information['nodes'] as $key => $node ) {

                if ( $node['type'] === 'post' || $node['type'] === 'term' || $node['type'] === 'taxonomy' || $node['type'] === 'post-type-base' || $node['type'] === 'other' ) {

                    if ( $node['skip_automatic_translation'] === true){

                        $skip_machine_translating_strings[] = $translateable_information['translateable_strings'][$key];

                    }

                }

            }
        }

        // serving translations, inserting strings in the database
        $translated_strings = $this->process_strings( $translateable_strings, $language_code, null, $skip_machine_translating_strings, $do_not_add_this_alug_to_dictionary_table );

        // serving translations for manual strings in the front-end: hrefs, src
        if ( !$preview_mode ){
            $translateable_information_manual = apply_filters( 'trp_translateable_strings_manual', array( 'translateable_strings_manual' => $translateable_strings_manual, 'nodes_manual' => $nodes_manual ), $html, $no_translate_attribute, $TRP_LANGUAGE, $language_code, $this );
            $translateable_strings_manual = $translateable_information_manual['translateable_strings_manual'];
            $nodes_manual = $translateable_information_manual['nodes_manual'];

            $translated_strings_manual_dictionary = $this->trp_query->get_existing_translations( array_values( $translateable_strings_manual ), $language_code );
            $translated_strings_manual = array();
            foreach ( $translateable_strings_manual as $i => $string_manual ) {
                if ( isset( $translated_strings_manual_dictionary[ $string_manual ]->translated ) && !empty( $translated_strings_manual_dictionary[ $string_manual ]->translated )) {
                    $translated_strings_manual[$i] = $translated_strings_manual_dictionary[ $string_manual ]->translated;
                }
            }

            foreach ( $nodes_manual as $i => $node_manual ) {
                if ( !isset( $translated_strings_manual[$i] ) || !isset( $node_accessors [$node_manual['type']] ) ){
                    continue;
                }
                $current_node_accessor = $node_accessors[$node_manual['type']];
                $accessor = $current_node_accessor[ 'accessor' ];
                if ( $current_node_accessor[ 'attribute' ] ){
                    $translateable_string_manual = $this->maybe_correct_translatable_string( $translateable_strings_manual[$i], $node_manual['node']->getAttribute( $accessor ) );
                    $node_manual['node']->setAttribute( $accessor, str_replace( $translateable_string_manual, esc_attr( $translated_strings_manual[$i] ), $node_manual['node']->getAttribute( $accessor ) ) );
                    do_action( 'trp_set_translation_for_attribute', $node_manual['node'], $accessor, $translated_strings_manual[$i] );
                }else{
                    $translateable_string_manual = $this->maybe_correct_translatable_string( $translateable_strings_manual[$i], $node_manual['node']->$accessor );
                    $node_manual['node']->$accessor = str_replace( $translateable_string_manual, trp_sanitize_string($translated_strings_manual[$i]), $node_manual['node']->$accessor );
                }
            }
            do_action('trp_translateable_information_manual', $translateable_information_manual, $translated_strings_manual, $language_code);
        }

        do_action('trp_translateable_information', $translateable_information, $translated_strings, $language_code);

        //check for post_id meta on original strings, and insert for non existing
        /*
         * - get only strings that have the post_id in the nodes from $translateable_information
         * - get the original id's for these string from the original table
         * - see which of these id's have the meta with the current post_id value and insert into the meta table the ones that don't
         *
         */

        if( !empty($translateable_information['nodes']) ){
            $strings_in_post_content = array();
            foreach ( $translateable_information['nodes'] as $i => $node ){
                if( !empty( $node['post_id'] ) ){
                    $strings_in_post_content['strings'][] = $translateable_information['translateable_strings'][$i];
                    $strings_in_post_content['post_ids'][] = $node['post_id'];
                }
            }
            if( !empty( $strings_in_post_content ) ){

                //try to do this only once a day to decrease query load
                $current_permalink = get_permalink();
                $set_meta_for_this_url = get_transient('processed_original_string_meta_post_id_for_' . hash('md4', $current_permalink));
                if( $set_meta_for_this_url === false ){

                    $original_string_ids = $this->trp_query->get_original_string_ids($strings_in_post_content['strings']);

                    if( !empty( $original_string_ids ) ){
                        //there is a correlation between the two arrays
                        $this->trp_query->set_original_string_meta_post_id( $original_string_ids, $strings_in_post_content['post_ids'] );
                    }

                    set_transient('processed_original_string_meta_post_id_for_' . hash('md4', $current_permalink), 'done', 60*60*24 );
                }
            }
        }

        if ( $preview_mode ) {
            $translated_string_ids = $this->trp_query->get_string_ids($translateable_strings, $language_code);
        }

        foreach ( $nodes as $i => $node ) {
            $translation_available = isset( $translated_strings[$i] );
            if ( ! ( $translation_available || $preview_mode ) || !isset( $node_accessors [$nodes[$i]['type']] )){
                continue;
            }
            $current_node_accessor = $node_accessors[ $nodes[$i]['type'] ];
            $accessor = $current_node_accessor[ 'accessor' ];
            if ( $translation_available && isset( $current_node_accessor ) && ! ( $preview_mode && ( $this->settings['default-language'] == $TRP_LANGUAGE ) ) ) {

                $translateable_string = $translateable_strings[$i];

                if ( $current_node_accessor[ 'attribute' ] ){
	                $translateable_string = $this->maybe_correct_translatable_string( $translateable_string, $nodes[$i]['node']->getAttribute( $accessor ) );
                    $nodes[$i]['node']->setAttribute( $accessor, str_replace( $translateable_string, esc_attr( $translated_strings[$i] ), $nodes[$i]['node']->getAttribute( $accessor ) ) );
                    do_action( 'trp_set_translation_for_attribute', $nodes[$i]['node'], $accessor, $translated_strings[$i] );
                }else{
	                $translateable_string = $this->maybe_correct_translatable_string( $translateable_string, $nodes[$i]['node']->$accessor );
                    $nodes[$i]['node']->$accessor = str_replace( $translateable_string, trp_sanitize_string($translated_strings[$i]), $nodes[$i]['node']->$accessor );
                }

            }
            if ( $preview_mode && !empty($translated_string_ids) ) {
                if ( $accessor == 'outertext' && $nodes[$i]['type'] != 'button' ) {
                    $outertext_details = '<translate-press data-trp-translate-id="' . $translated_string_ids[$translateable_strings[$i]]->id . '" data-trp-node-group="' . $this->get_node_type_category( $nodes[$i]['type'] ) . '"';
                    if ( $this->get_node_description( $nodes[$i] ) ) {
                        $outertext_details .= ' data-trp-node-description="' . $this->get_node_description($nodes[$i] ) . '"';
                    }
                    $outertext_details .= '>' . $nodes[$i]['node']->outertext . '</translate-press>';
                    $nodes[$i]['node']->outertext = $outertext_details;
                } else {
                    // button, option  can not be detected by the pencil, but the parent can.
                    if( $nodes[$i]['type'] == 'button' ||
                        $nodes[$i]['type'] == 'option' )
                    {
                        $nodes[$i]['node'] = $nodes[$i]['node']->parent();
                    }

                    // video without a src can't be detected. So when we detect a video > source tag
                    // we add the ID to the parent video tag as well
                    if( $nodes[$i]['type'] == 'video_source_src' ||
                        $nodes[$i]['type'] == 'audio_source_src' ||
                        $nodes[$i]['type'] == 'picture_source_srcset')
                    {
                        $parent = $nodes[$i]['node']->parent();
                        if (!array_key_exists('src', $parent->attr)){
                            $parent->setAttribute('data-trp-translate-id-' . $accessor, $translated_string_ids[ $translateable_strings[$i] ]->id );
                            $parent->setAttribute('data-trp-node-group-' . $accessor, $this->get_node_type_category( $nodes[$i]['type'] ) );
                        }
                    }

	                $nodes[$i]['node']->setAttribute('data-trp-translate-id-' . $accessor, $translated_string_ids[ $translateable_strings[$i] ]->id );
                    $nodes[$i]['node']->setAttribute('data-trp-node-group-' . $accessor, $this->get_node_type_category( $nodes[$i]['type'] ) );

                    if ( $this->get_node_description( $nodes[$i] ) ) {
                        $nodes[$i]['node']->setAttribute('data-trp-node-description-' . $accessor, $this->get_node_description($nodes[$i]));
                    }

                }
            }

        }


        // We need to save here in order to access the translated links too.
        $handle_custom_links_in_translation_blocks = $this->settings['force-language-to-custom-links'] == 'yes';
        if( apply_filters('tp_handle_custom_links_in_translation_blocks', $handle_custom_links_in_translation_blocks) ) {
            $html_string = $html->save();
            $html = TranslatePress\str_get_html($html_string, true, true, TRP_DEFAULT_TARGET_CHARSET, false, TRP_DEFAULT_BR_TEXT, TRP_DEFAULT_SPAN_TEXT);
            if ( $html === false ){
                /* add back the excluded tags like script and style to the html */
                $html_string = $this->add_excluded_tags_after_translation( $html_string, $output_with_excluded_tags_removed['excluded_tags'] );
                return $html_string;
            }
        }

        $html = $this->handle_custom_links_and_forms( $html );

	    // Append an html table containing the errors
	    $trp_editor_notices = apply_filters( 'trp_editor_notices', $trp_editor_notices );
	    if ( trp_is_translation_editor('preview') && $trp_editor_notices != '' ){
		    $body = $html->find('body', 0 );
            if ( $body ) {
                $body->innertext = '<div data-no-translation class="trp-editor-notices">' . $trp_editor_notices . "</div>" . $body->innertext;
            }
	    }
	    $final_html = $html->save();

        /* add back the excluded tags like script and style to the html */
        $final_html = $this->add_excluded_tags_after_translation( $final_html, $output_with_excluded_tags_removed['excluded_tags'] );

       /* perform preg replace on the remaining trp-gettext tags */
        $final_html = $this->remove_trp_html_tags( $final_html );

	    return apply_filters( 'trp_translated_html', $final_html, $TRP_LANGUAGE, $language_code, $preview_mode );
    }

    /**
     * Restore camelCase XML element names in syndication feeds.
     *
     * The HTML DOM parser used by translate_page() lowercases tag names. RSS 2.0
     * defines <pubDate> and <lastBuildDate> in camelCase, so the lowercased output
     * is not valid per the syndication spec and is rejected by some readers.
     */
    public function restore_feed_camelcase_tags( $final_html, $TRP_LANGUAGE = null, $language_code = null, $preview_mode = false ) {
        if ( ! is_feed() || ! is_string( $final_html ) ) {
            return $final_html;
        }

        $camelcase_tags = apply_filters( 'trp_feed_camelcase_tags', array( 'pubDate', 'lastBuildDate' ) );

        foreach ( $camelcase_tags as $tag ) {
            $lowercase = strtolower( $tag );
            $final_html = str_replace(
                array( '<' . $lowercase . '>', '</' . $lowercase . '>' ),
                array( '<' . $tag . '>',       '</' . $tag . '>' ),
                $final_html
            );
        }

        return $final_html;
    }


    public function handle_custom_links_and_forms( $html ){
        global $TRP_LANGUAGE;
        $preview_mode = isset( $_REQUEST['trp-edit-translation'] ) && $_REQUEST['trp-edit-translation'] == 'preview';
        $home_url = home_url();
        $admin_url = admin_url();
        $wp_login_url = wp_login_url();
	    $no_translate_attribute = 'data-no-translation';
        if ( ! $this->url_converter ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->url_converter = $trp->get_component('url_converter');
        }

        // force custom links to have the correct language
        foreach( $html->find('a[href!="#"]') as $a_href)  {
            $a_href->href = apply_filters( 'trp_href_from_translated_page', $a_href->href, $this->settings['default-language'] );

            if($a_href->href === true){
                $a_href->href = ''; // an empty href <a href>Link</a> causes href to be true instead of empty string. Exit early and do not use get_url_for_lang() on it.
                continue;
            }

            $url = trim($a_href->href);

            $url = $this->maybe_is_local_url($url, $home_url);

            $is_external_link = $this->is_external_link( $url, $home_url );
            $is_admin_link = $this->is_admin_link($url, $admin_url, $wp_login_url);

            if( $preview_mode && ! $is_external_link ){
                $a_href->setAttribute( 'data-trp-original-href', $url );
            }

            if (
            	( $TRP_LANGUAGE != $this->settings['default-language'] || $this->settings['add-subdirectory-to-default-language'] == 'yes' ) &&
                $this->settings['force-language-to-custom-links'] == 'yes' &&
	            !$is_external_link &&
                !$this->url_converter->url_is_file( $url ) &&
                ( $this->url_converter->get_lang_from_url_string( $url ) == null || ( isset ($this->settings['add-subdirectory-to-default-language']) && $this->settings['add-subdirectory-to-default-language'] === 'yes' && $this->url_converter->get_lang_from_url_string( $url ) === $this->settings['default-language'] ) ) &&
	            !$is_admin_link &&
                strpos($url, '#TRPLINKPROCESSED') === false &&
	            ( !$this->has_ancestor_attribute( $a_href, $no_translate_attribute ) || $this->has_ancestor_attribute($a_href, 'data-trp-gettext') ) // add language param to link if it's inside a gettext
            ){
                $a_href->href = apply_filters( 'trp_force_custom_links', $this->url_converter->get_url_for_language( $TRP_LANGUAGE, $url, '' ), $url, $TRP_LANGUAGE, $a_href );
                $url = $a_href->href;
            }

            if( $preview_mode && ( $is_external_link || $this->is_different_language( $url ) || $is_admin_link ) ) {
                $a_href->setAttribute( 'data-trp-unpreviewable', 'trp-unpreviewable' );
            }

            $a_href->href = $this->strip_link_processed_marker( $a_href->href );
        }

        // pass the current language in forms where the action does not contain the language
        // based on this we're filtering wp_redirect to include the proper URL when returning to the current page.
        foreach ( $html->find('form') as $k => $row ){
            $form_action      = $row->action;
            $is_admin_link    = $this->is_admin_link( $form_action, $admin_url, $wp_login_url );
            $skip_this_action = apply_filters( 'trp_skip_form_action', false, $form_action );

            if( !$is_admin_link && !$skip_this_action && !$this->is_external_link( $form_action, $home_url ) ) {
                $row->setAttribute( 'data-trp-original-action', $row->action );
                $row->innertext .= apply_filters( 'trp_form_inputs', '<input type="hidden" name="trp-form-language" value="' . $this->settings['url-slugs'][ $TRP_LANGUAGE ] . '"/>', $TRP_LANGUAGE, $this->settings['url-slugs'][ $TRP_LANGUAGE ], $row );

                $is_external_link = $this->is_external_link( $form_action, $home_url );

                if ( !empty( $form_action )
                    && $this->settings['force-language-to-custom-links'] == 'yes'
                    && !$is_external_link
                    && strpos( $form_action, '#TRPLINKPROCESSED' ) === false ) {
                    /* $form_action can have language slug in a secondary language but the path slugs in original language.
                     * By converting to default language first, it helps set the language slug to default language
                     * while keeping the path slugs unchanged (no language coincidences should appear because we check
                     * for uniqueness between secondary language translations and originals other than its own)
                     * Use filter trp_change_form_action to hardcode particular cases
                     */
                    $action_in_default_language = $this->url_converter->get_url_for_language( $this->settings['default-language'], $form_action, '' );
                    $action_in_current_language = $this->url_converter->get_url_for_language( $TRP_LANGUAGE, $action_in_default_language, '' );
                    $row->action                = apply_filters( 'trp_change_form_action', $action_in_current_language, $action_in_default_language, $TRP_LANGUAGE );
                }

                // this should happen regardless of whether we made changes above
                $row->action = $this->strip_link_processed_marker( esc_url( $row->action ) );
            }
        }

        foreach ( $html->find('link') as $link ) {
            if ( isset( $link->href ) ) {
                if ( isset( $link->rel ) && ( $link->rel == 'next' || $link->rel == 'prev' ) )
                    $link->href = $this->url_converter->get_url_for_language( $TRP_LANGUAGE, $link->href );

                $link->href = $this->strip_link_processed_marker( $link->href );
            }
        }

        return $html;
    }

    /**
     * Remove the internal #TRPLINKPROCESSED marker from a url attribute value and re-validate the result.
     *
     * The marker is normally a trailing fragment TRP appends to links it has already processed, but it can
     * also arrive embedded in attacker-controlled input ( e.g. a stored comment anchor href ) where it SPLITS
     * an otherwise-blocked scheme: "java#TRPLINKPROCESSEDscript:alert(1)" passes wp_kses / has_unsafe_url_scheme()
     * while split, then collapses to a live "javascript:alert(1)" once the marker is removed. We only neutralize
     * in that case ( stripping the marker turned a safe value into an unsafe one ) so that url schemes the site
     * itself placed, including a plain "javascript:void(0)", are left exactly as before. [CU-869ffp71k]
     *
     * @param string $value url attribute value that may still contain #TRPLINKPROCESSED
     * @return string the marker-free value, or '' when stripping the marker exposed an unsafe scheme
     */
    private function strip_link_processed_marker( $value ){
        $stripped = str_replace( '#TRPLINKPROCESSED', '', $value );
        if ( $stripped !== $value
            && $this->has_unsafe_url_scheme( $stripped )
            && ! $this->has_unsafe_url_scheme( $value ) ) {
            return '';
        }
        return $stripped;
    }

    public function is_first_language_not_default_language(){
        return ( isset( $this->settings['add-subdirectory-to-default-language'] ) &&
            $this->settings['add-subdirectory-to-default-language'] == 'yes' &&
            isset( $this->settings['publish-languages'][0] ) &&
            $this->settings['default-language'] != $this->settings['publish-languages'][0] );
    }

    /*
     * Adjust translatable string so that it must match the content of the node value
     *
     * We use str_replace method in order to preserve any existent spacing before or after the string.
     * If the encoding of the node is not the same as the translatable string then the string won't match so try applying htmlentities.
     * If that doesn't work either, just forget about any possible before and after spaces.
     *
     */
    public function maybe_correct_translatable_string( $translatable_string, $node_value ){
	    if ( strpos ( $node_value, $translatable_string ) === false ){
		    $translatable_string = htmlentities( $translatable_string );
		    if ( strpos ( $node_value, $translatable_string ) === false ){
			    $translatable_string = $node_value;
		    }
	    }
	    return $translatable_string;
    }

    public function maybe_add_post_id_in_node( $nodes, $row, $string_count ){
        $post_container_node = $this->has_ancestor_attribute( $row, 'data-trp-post-id' );
        if( $post_container_node && $post_container_node->attr['data-trp-post-id'] ) {
            $nodes[$string_count - 1]['post_id'] = $post_container_node->attr['data-trp-post-id'];
        }
        return $nodes;
    }

    /*
     * Update other image attributes (srcset) with the translated image
     *
     * Hooked to trp_set_translation_for_attribute
     */
    public function translate_image_srcset_attributes( $node, $accessor, $translated_string){
	    if( $accessor === 'src' ) {
			$srcset = $node->getAttribute( 'srcset' );
			$datasrcset = $node->getAttribute( 'data-srcset' );
		    if ( $srcset || $datasrcset ) {
			    $attachment_id = attachment_url_to_postid( $translated_string );
			    if ( $attachment_id ) {
				    $translated_srcset = '';
				    if ( function_exists( 'wp_get_attachment_image_srcset' ) ) {
				    	// get width of the image in order, to set the largest possible size for srcset
					    $meta_data = wp_get_attachment_metadata( $attachment_id );
					    $width = ( $meta_data && isset( $meta_data['width'] ) ) ? $meta_data['width'] : 'large';
					    $translated_srcset = wp_get_attachment_image_srcset( $attachment_id, $width );
				    }
				    if ( $srcset ){
					    $node->setAttribute( 'srcset', $translated_srcset );
				    }
				    if ( $datasrcset ){
					    $node->setAttribute( 'data-srcset', $translated_srcset );
				    }
			    } else {
				    $node->setAttribute( 'srcset', '' );
				    $node->setAttribute( 'data-srcset', '' );
			    }
		    }
		    if ( $node->getAttribute( 'data-src' ) ) {
			    $node->setAttribute( 'data-src', $translated_string );
		    }
	    }

    }

    /*
     * Do not automatically translate src and href attributes
     *
     * Hooked to trp_allow_machine_translation_for_string
     */
    public function allow_machine_translation_for_string( $allow, $entity_decoded_trimmed_string, $current_node_accessor_selector, $node_accessor ){
    	$skip_attributes = apply_filters( 'trp_skip_machine_translation_for_attr', array( 'href', 'src', 'poster', 'srcset' ) );
	    if ( in_array( $current_node_accessor_selector, $skip_attributes ) ){
	    	// do not machine translate href and src
	    	return false;
	    }
	    return $allow;
    }

    /*
     * Do not automatically translate html nodes with data-no-auto-translation attribute
     *
     * Hooked to trp_allow_machine_translation_for_string
     */
    function skip_automatic_translation_for_no_auto_translation_selector($allow, $entity_decoded_trimmed_string, $current_node_accessor_selector, $node_accessor, $row){
        $no_auto_translate_attribute = 'data-no-auto-translation';
        if ( $this->has_ancestor_attribute( $row, $no_auto_translate_attribute ) ||
            ( $current_node_accessor_selector !== null && $this->has_ancestor_attribute( $row, $no_auto_translate_attribute . '-' . $current_node_accessor_selector )) ){
            return false;
        }
        return $allow;
    }

    /*
     * Do not automatically translate numbers, emails and base64 images
     *
     * Hooked to trp_allow_machine_translation_for_string
     */
    public function skip_strings_that_cannot_be_auto_translated( $allow, $entity_decoded_trimmed_string, $current_node_accessor_selector, $node_accessor, $row ) {
        if ( is_numeric( $entity_decoded_trimmed_string ) ||
            $this->looks_like_email( $entity_decoded_trimmed_string ) ||
            ( strncmp( $entity_decoded_trimmed_string, 'data:image/', 11 ) === 0 && strpos( $entity_decoded_trimmed_string, ';base64,', 11 ) !== false )
        ) {
            $allow = false;
        }
        return $allow;
    }

    /**
     * Very fast is_email function. Not 100% strict, but good enough for deciding to not auto translate it
     *
     * @param $s
     * @return bool
     */
    public function looks_like_email( $s ) {
        $len = strlen( $s );

        // Length sanity (fast integer checks)
        if ( $len < 6 || $len > 254 ) {
            return false;
        }

        // Single @ check (cheaper than regex)
        if ( substr_count( $s, '@' ) !== 1 ) {
            return false;
        }

        $at = strpos( $s, '@' );

        // @ cannot be first or last
        if ( $at === 0 || $at === $len - 1 ) {
            return false;
        }

        // No spaces
        if ( strpos( $s, ' ' ) !== false ) {
            return false;
        }

        // Dot must exist after @ with at least one char in between
        $lastDot = strrpos( $s, '.' );
        if ( $lastDot === false || $lastDot < $at + 2 || $lastDot === $len - 1 ) {
            return false;
        }

        // Regex only for plausible candidates
        return preg_match( '/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $s ) === 1;
    }

    /**
     * Turn our internal gettext markers back into real html tags.
     *
     * Gettext strings are wrapped in #!trpst#trp-gettext ...#!trpen# instead of < and > so that the
     * wrapper survives the escaping functions themes and plugins apply to translated strings
     * ( esc_html, esc_attr, sanitize_text_field, ... ). Since the markers contain no html special
     * characters they also survive the escaping applied to *user input*, so replacing them
     * unconditionally allowed anyone to smuggle < and > into an already escaped page through a
     * query parameter ( e.g. ?s=#!trpst#img src=x onerror=alert(1)#!trpen# ) and inject html.
     *
     * That's why we only convert marker pairs that form one of the wrappers we generate ourselves in
     * TRP_Process_Gettext::process_gettext_strings(). Everything else stays harmless literal text.
     *
     * Both wrapper forms come out as "< + what was between the markers + >", so one pattern with one
     * capture group handles both and the buffer is only scanned once. Every quantifier is bounded on
     * purpose: an unbounded one here is reachable from ?s= and a PCRE failure would return null and
     * blank the whole page.
     *
     * Opening wrapper: #!trpst#trp-gettext data-trpgettextoriginal=123#!trpen#
     *   - the id is missing when the original was not in the database ( see strip_gettext_tags() ),
     *     hence \d{0,20} and not \d{1,20}
     *   - the separating space can be replaced by a literal backslash-u0020 escape by third party
     *     code ( see the WooTour compatibility ), so accept that form too
     * Closing wrapper: #!trpst#/trp-gettext#!trpen#
     *   - the slash arrives backslash escaped when the string went through json encoding; keep the
     *     escaping in the output so the json stays valid. remove_trp_html_tags() cleans up leftovers.
     *
     * @param string $string
     * @return string
     */
    public function replace_gettext_markers_with_html_tags( $string ){
        /* stripos and the 'i' modifier because the markers can be uppercased by code that normalizes
           the html it outputs. str_ireplace was used here before for the same reason. */
        if ( !is_string( $string ) || stripos( $string, '#!trpst#' ) === false ){
            return $string;
        }

        $replaced = preg_replace(
            '/#!trpst#(trp-gettext(?:(?:\s|\\\\{1,2}u0020){1,40}data-trpgettextoriginal=\d{0,20})?|\\\\{0,4}\/trp-gettext)#!trpen#/i',
            '<$1>',
            $string
        );

        /* preg_replace returns null when PCRE gives up ( backtrack or jit stack limit ). Never let that
           through: translate_page() would bail on the non-string and serve an empty document. Leaving
           the markers unconverted is the safe outcome, they render as inert text. */
        return is_string( $replaced ) ? $replaced : $string;
    }

    /**
     * function that removes any unwanted leftover <trp-gettext> tags
     *
     * Security ( CU-869eddnvm ): the opening-tag removals below used an unbounded inner match ( .*? ) that
     * could bridge across html attribute/tag delimiters. TP emits these wrappers as real tags, but the same
     * marker can also appear ENCODED ( percent-encoded, or html-entity escaped: %23%21trpst%23trp-gettext,
     * &lt;trp-gettext ) inside an attribute value that survived wp_kses, because the marker text contains no
     * html-special characters. A .*? there let a start marker inside one attribute ( href ) reach an end
     * marker inside another ( title ) and collapse everything between them, splicing the two attributes into
     * e.g. href="javascript:..." from an otherwise kses-clean, unauthenticated comment ( stored XSS ).
     *
     * The real-tag form ( <trp-gettext ...> ) is never attacker-reachable: wp_kses strips a literal <trp-*>
     * tag from user input, so it stays permissive ( bounded only by the real angle brackets ). Only the
     * encoded/entity form is attacker-reachable, so there the captured span excludes the raw attribute
     * delimiters "'<> and can no longer leave a single attribute value / text node. Legitimately escaped
     * wrappers carry &quot;/&#039; rather than raw quotes, so they are still removed.
     *
     * Security ( CU-869f8bpxr ): a marker can also sit entirely inside ONE url attribute value and act as an
     * obfuscation prefix: href="&lt;trp-gettext /?&gt;javascript:alert(1)". wp_kses_bad_protocol() skips
     * any value with "/?" before the first colon ( it looks like a relative url ), so it is stored verbatim
     * and is inert, but removing the marker leaves a live href="javascript:...". Constraining the marker shape
     * is not enough ( the attribute gettext path in translate_page() also html-decodes the value ), so url
     * attribute values that carry a marker are re-checked for a script scheme after the markers are removed.
     *
     * @param $string
     * @return string|string[]|null
     */
    function remove_trp_html_tags( $string ){
        if ( stripos( $string, 'trp-' ) !== false ) {
            $string = $this->neutralize_marker_hidden_url_schemes( $string );
        }

        return $this->strip_trp_markers( $string );
    }

    /**
     * Empty url attribute values ( href, src, action... ) that only get a script scheme once the trp markers
     * inside them are removed. Values with no marker are left alone; wp_kses already had its say on them.
     *
     * @param string $string
     * @return string
     */
    private function neutralize_marker_hidden_url_schemes( $string ){
        $url_attributes = array_map( function( $attr ){ return preg_quote( $attr, '/' ); }, $this->get_url_attributes() );

        // Capture the value with a possessive, delimiter-bounded class instead of a lazy .*? ( CU-1amndjnkyuu ).
        // A url attribute value never legitimately contains a raw < or > ( they would close the tag ) nor an
        // unescaped copy of its own quote, so the value is matched with [^"<>] / [^'<>] and *+ ( possessive, no
        // backtracking ). The old lazy .*?\2 could be forced to span ~1MB across many comments by a stray
        // opening quote whose closing quote sat far away; that defeated pcre.backtrack_limit, preg_replace_callback
        // returned null, the function failed open and strip_trp_markers() then collapsed a smuggled
        // java<marker>script: value into a live javascript: url. Bounding on < > keeps every candidate inside a
        // single tag ( at most one comment ), so the match stays linear and the backtrack limit is never reached.
        // A branch reset group ( ?| ) keeps $matches[2]/$matches[3] = opening-delimiter/value for both quote types,
        // and the quote can be json-escaped ( href=\"...\" ) when the html is inside a json response.
        $pattern = '/(?<=[\s"\'\/])((?:' . implode( '|', $url_attributes ) . ')\s*=\s*)(?|(\\\\{0,2}")([^"<>]*+)"|(\\\\{0,2}\')([^\'<>]*+)\')/is';

        $result = preg_replace_callback( $pattern, function( $matches ){
            if ( stripos( $matches[3], 'trp-' ) === false || !$this->has_unsafe_url_scheme( $this->strip_trp_markers( $matches[3] ) ) ) {
                return $matches[0];
            }
            return $matches[1] . $matches[2] . $matches[2];
        }, $string );

        // preg failure ( e.g. backtrack limit ) - keep the original string rather than blanking the page. The
        // bounded pattern above makes this unreachable for the comment-smuggling vector, so it stays a last resort.
        return ( $result === null ) ? $string : $result;
    }

    /**
     * @param string $attr_name
     * @return bool
     */
    private function is_url_attribute( $attr_name ){
        return in_array( strtolower( $attr_name ), $this->get_url_attributes(), true );
    }

    /**
     * Attributes a browser resolves as a url ( same list wp_kses runs wp_kses_bad_protocol() on ).
     *
     * @return array
     */
    private function get_url_attributes(){
        if ( function_exists( 'wp_kses_uri_attributes' ) ) {
            return wp_kses_uri_attributes();
        }
        return array( 'action', 'archive', 'background', 'cite', 'classid', 'codebase', 'data', 'formaction', 'href', 'icon', 'longdesc', 'manifest', 'poster', 'profile', 'src', 'usemap', 'xmlns' );
    }

    /**
     * Whether a url attribute value would be run by the browser under a scheme that is not in
     * wp_allowed_protocols() ( javascript:, data:, vbscript: ... ).
     *
     * Deliberately stricter than the browser: entities are decoded repeatedly ( a value can be decoded more
     * than once on its way out ), numeric references are also decoded without their ";", and every
     * whitespace / control character and backslash is dropped before the scheme is read.
     *
     * @param string $value
     * @return bool
     */
    private function has_unsafe_url_scheme( $value ){
        $decoded = (string) $value;
        for ( $i = 0; $i < 5; $i++ ) {
            $previous = $decoded;
            $decoded  = html_entity_decode( $decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
            $decoded  = preg_replace_callback( '/&#(x[0-9a-f]++|[0-9]++);?/i', function( $matches ){
                return html_entity_decode( '&#' . $matches[1] . ';', ENT_QUOTES | ENT_HTML5, 'UTF-8' );
            }, $decoded );
            if ( $decoded === $previous ) {
                break;
            }
        }
        $decoded = preg_replace( '/[\x00-\x20\x7f\\\\]+/', '', $decoded );

        if ( !preg_match( '/^([a-z][a-z0-9+.\-]*):/i', $decoded, $matches ) ) {
            return false;
        }

        return !in_array( strtolower( $matches[1] ), wp_allowed_protocols(), true );
    }

    /**
     * Remove every trp-gettext / trp-wrap / trp-post-container marker ( real, entity and percent-encoded forms ).
     *
     * @param string $string
     * @return string|string[]|null
     */
    private function strip_trp_markers( $string ){
        // trp-gettext opening tag: the real form ( <...> ) and the entity form ( &lt;...&gt; ) both carry an
        // unquoted attribute ( data-trpgettextoriginal=123 ), so a single delimiter-constrained match is safe.
        $string = preg_replace( '/(<|&lt;)trp-gettext ([^"\'<>]*?)(>|&gt;)/i', '', $string );
        $string = preg_replace( '/(<|&lt;)(\\\\)*\/trp-gettext(>|&gt;)/i', '', $string );

        // In case we have a gettext string which was run through rawurlencode(). See more details on iss6563
        $string = preg_replace( '/%23%21trpst%23trp-gettext([^"\'<>]*?)%23%21trpen%23/i', '', $string );
        $string = preg_replace( '/%23%21trpst%23%2Ftrp-gettext%23%21trpen%23/i', '', $string );
        $string = preg_replace( '/%23%21trpst%23%5C%2Ftrp-gettext%23%21trpen%23/i', '', $string );

        if (!isset($_REQUEST['trp-edit-translation']) || $_REQUEST['trp-edit-translation'] != 'preview') {
            // Real trp-wrap tag carries a double-quoted attribute ( class="trp-wrap" ), so the real form stays
            // permissive; only the attacker-reachable entity form is delimiter-constrained.
            $string = preg_replace('/<trp-wrap [^<>]*?>/i', '', $string);
            $string = preg_replace('/&lt;trp-wrap ([^"\'<>]*?)&gt;/i', '', $string);
            $string = preg_replace('/(<|&lt;)(\\\\)*\/trp-wrap(>|&gt;)/i', '', $string);
        }

        //remove post containers before outputting
        // Real trp-post-container carries a single-quoted attribute ( data-trp-post-id='123' ), so the real
        // form stays permissive; only the attacker-reachable entity form is delimiter-constrained.
        $string = preg_replace( '/<trp-post-container [^<>]*?>/i', '', $string );
        $string = preg_replace( '/&lt;trp-post-container ([^"\'<>]*?)&gt;/i', '', $string );
        $string = preg_replace( '/(<|&lt;)(\\\\)*\/trp-post-container(>|&gt;)/i', '', $string );

        return $string;
    }

    /**
     * Validate a data-trpgettextoriginal value before it is written back into the page.
     *
     * The value is always a row id from wp_trp_gettext_original_strings that TP itself placed in the
     * wrapper ( see TRP_Process_Gettext::process_gettext_strings() ), and it is legitimately empty when
     * the original was not in the database yet ( see strip_gettext_tags() ). Anything else means the
     * wrapper did not come from us.
     *
     * Security ( follow-up to the CVE-2026-17505 marker hardening ): the wrapper IS
     * attacker reachable, just not through the #!trpst# markers that replace_gettext_markers_with_html_tags()
     * now constrains to data-trpgettextoriginal=\d{0,20}. A literal
     *
     *     <trp-gettext data-trpgettextoriginal='x"><img src=x onerror=alert(1)>'>X</trp-gettext>
     *
     * in reflected input ( ?s= ) is escaped to entities by WordPress and lands inside a host attribute
     * ( title=, content=, value= ), and the trp_attr_rows loop in translate_page() deliberately
     * html_entity_decodes that attribute and re-parses it, resurrecting the tag as a real node. The id was
     * then written straight back out with setAttribute() / string concatenation. Neither escapes:
     * simple_html_dom::makeup() concatenates the stored value between quotes verbatim, so a double quote
     * in the id closed the attribute and injected live markup. Unauthenticated, because every preview
     * branch only tests $_REQUEST['trp-edit-translation'] with no capability check.
     *
     * Constraining the value to digits removes the breakout at the source; callers additionally esc_attr()
     * on output so the sink stays safe even if this ever loosens.
     *
     * @param mixed $id Raw attribute value as returned by simple_html_dom's getAttribute().
     * @return string Digits-only id, or an empty string when the value is not a usable id.
     */
    protected function sanitize_gettext_original_id( $id ){
        /* getAttribute() returns true for a valueless attribute and null/false for a removed one */
        if ( ! is_string( $id ) ){
            return '';
        }

        $id = trim( $id );

        return ( $id !== '' && ctype_digit( $id ) ) ? $id : '';
    }

    /**
     * Callback for the array_walk_recursive to translate json. It translates the values in the resulting json array if they contain html
     * @param $value
     */
    function translate_json (&$value) {
        //check if it a html text and translate
        $html_decoded_value = html_entity_decode( (string) $value );
        if ( $html_decoded_value != strip_tags( $html_decoded_value ) ) {

            $value = $this->translate_page( $value );
            /*the translate-press tag can appear on a gettext string without html and should not be left in the json
            as we don't know how it will be inserted into the page by js */
            $value = preg_replace( '/(<|&lt;)translate-press (.*?)(>|&gt;)/', '', $value );
            $value = preg_replace( '/(<|&lt;)(\\\\)*\/translate-press(>|&gt;)/', '', $value );
        }
    }

	/**
	 * Callback for the array_walk_recursive to process links inside json elements that might contain HTML. It processes the values in the resulting json array if they contain html
	 * @param $value
	 */
	function custom_links_and_forms_json (&$value) {
		//check if it a html text and translate
		$html_decoded_value = html_entity_decode( (string) $value );
		if ( $html_decoded_value != strip_tags( $html_decoded_value ) ) {
			$html = TranslatePress\str_get_html( $value, true, true, TRP_DEFAULT_TARGET_CHARSET, false, TRP_DEFAULT_BR_TEXT, TRP_DEFAULT_SPAN_TEXT );
			if( $html ) {
                $html = $this->handle_custom_links_and_forms($html);
                $value = $html->save();
            }
		}
	}

    public function handle_custom_links_for_default_language(){
        return ( $this->settings['force-language-to-custom-links'] == 'yes' &&
            $this->is_first_language_not_default_language() &&
            apply_filters('trp_handle_custom_links_and_forms_in_default_language', true ) );
    }

    /**
     * Function that should be called only on the default language and when we are not in the editor mode and it is designed as a fallback to clear
     * any trp gettext tags that we added and for some reason show up  although they should not
     * @param $output
     * @return mixed
     */
    public function render_default_language( $output ){
        if ( $this->handle_custom_links_for_default_language() && !apply_filters( 'trp_stop_translating_page', false, $output ) ) {

	        $json_array = json_decode( $output, true );
	        /* If we have a json response we need to parse it and only translate the nodes that contain html
			 *
			 * Removed is_ajax_on_frontend() check because we need to capture custom ajax events.
			 * Decided that if $output is json decodable it's a good enough check to handle it this way.
			 * We have necessary checks so that we don't get to this point when is_admin(), or when language is not default.
			 */
	        if( $json_array && $json_array != $output ) {
		        /* if it's one of our own ajax calls don't do nothing */
		        if ( ! empty( $_REQUEST['action'] ) && strpos( sanitize_text_field( $_REQUEST['action'] ), 'trp_' ) === 0 && $_REQUEST['action'] != 'trp_split_translation_block' ) {
			        return $output;
		        }

		        //check if we have a json response
		        if ( ! empty( $json_array ) ) {
			        if( is_array( $json_array ) ) {
				        array_walk_recursive($json_array, array($this, 'custom_links_and_forms_json'));
			        } else {

				        $html = TranslatePress\str_get_html( $json_array, true, true, TRP_DEFAULT_TARGET_CHARSET, false, TRP_DEFAULT_BR_TEXT, TRP_DEFAULT_SPAN_TEXT );
				        if( $html ) {
                            $html = $this->handle_custom_links_and_forms($html);
                            $json_array = $html->save();
                        }
			        }
		        }

		        return trp_safe_json_encode( $json_array );
	        }

            $html = TranslatePress\str_get_html( $output, true, true, TRP_DEFAULT_TARGET_CHARSET, false, TRP_DEFAULT_BR_TEXT, TRP_DEFAULT_SPAN_TEXT );
            if( $html ) {
                $html = $this->handle_custom_links_and_forms($html);
                $output = $html->save();
            }
        }

        return TRP_Translation_Manager::strip_gettext_tags($output);
    }

    /**
     * Whether given url links to an external domain.
     *
     * @param string $url           Url.
     * @param string $home_url      Optional home_url so we avoid calling the home_url() inside loops.
     * @return bool                 Whether given url links to an external domain.
     */
    public function is_external_link( $url, $home_url = '' ){
        // Abort if parameter URL is empty
        if( empty($url) ) {
            return false;
        }
        if ( strpos( $url, '#' ) === 0 || strpos( $url, '/' ) === 0){
            return false;
        }

        // Parse home URL and parameter URL
        $link_url = parse_url( $url );
        if( empty( $home_url ) )
            $home_url = home_url();
        $home_url_parsed = parse_url( $home_url );

        // Decide on target
        if( !isset ($link_url['host'] ) || $link_url['host'] == $home_url_parsed['host'] ) {
            // Is an internal link
            return false;

        } else {
            // Allow addons (like Multiple Domains) to recognize additional domains as internal
            return apply_filters( 'trp_is_external_link', true, $url, $home_url );
        }
    }
    /**
     * Checks to see if the user didn't incorrectly formated a url that's different from the home_url
     * Takes into account http, https, www and all the possible combinations between them.
     *
     * @param string $url           Url.
     * @param string $home_url      Optional home_url so we avoid calling the home_url() inside loops.
     * @return string               Correct URL that's the same structure as home_url
     */
    public function maybe_is_local_url( $url, $home_url='' ){

        if ( apply_filters('disable_maybe_is_local_url', false) ){
            return $url;
        }

        // Abort if parameter URL is empty
        if( empty($url) ) {
            return $url;
        }
        if ( strpos( $url, '#' ) === 0 || strpos( $url, '/' ) === 0){
            return $url;
        }

        // Parse home URL and parameter URL
        $link_url = parse_url( $url );
        if( empty( $home_url ) )
            $home_url = home_url();
        $home_url = parse_url( $home_url );

        // Decide on target
        if( !isset ($link_url['host'] ) || $link_url['host'] == $home_url['host'] || !isset ( $link_url['scheme'] ) ) {
            // Is an internal link
            return $url;
        } else {

            // test out possible local urls that the user might have mistyped
            $valid_local_prefix = array('http://', 'https://', 'http://www.', 'https://www.');
            foreach ($valid_local_prefix as $prefix){
                foreach ($valid_local_prefix as $replacement_prefix){
                    if( str_replace($prefix, $replacement_prefix, $link_url['scheme'] . '://' . $link_url['host']) == $home_url['scheme'] . '://' .$home_url['host'] ){
                        return str_replace($prefix, $replacement_prefix, $url);
                    }
                }
            }

            // Is an external link
            return $url;
        }
    }
    /**
     * Whether given url links to a different language than the current one.
     *
     * @param string $url           Url.
     * @return bool                 Whether given url links to a different language than the current one.
     */
    protected function is_different_language( $url ){
        global $TRP_LANGUAGE;
        if ( ! $this->url_converter ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->url_converter = $trp->get_component('url_converter');
        }
        $lang = $this->url_converter->get_lang_from_url_string( $url );
        if ( $lang == null ){
            $lang = $this->settings['default-language'];
        }
        if ( $lang == $TRP_LANGUAGE ){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Whether given url links to an admin page.
     *
     * @param string $url           Url.
     * @return bool                 Whether given url links to an admin page.
     *
     * It's always been private, do not make public in the future so we don't use it in one of the paid addons,
     * causing Fatal Errors for users who update the Paid but not the Free
     */
    public function is_admin_link( $url, $admin_url = '', $wp_login_url = '' ){

	    if( empty( $admin_url ) )
		    $admin_url = admin_url();

	    if( empty( $wp_login_url ) )
		    $wp_login_url = wp_login_url();

	    if ( strpos( $url, $admin_url ) !== false || strpos( $url, $wp_login_url ) !== false ){
		    $is_admin_link = true;
	    } else {
		    $is_admin_link = false;
	    }

	    return apply_filters('trp_is_admin_link', $is_admin_link, $url, $admin_url, $wp_login_url);

    }

    /**
     * Return translations for given strings in given language code.
     *
     * Also stores new strings, calls automatic translations and stores new translations.
     *
     * @param $translateable_strings
     * @param $language_code
     * @return array
     */
    public function process_strings( $translateable_strings, $language_code, $block_type = null, $skip_machine_translating_strings = array(), $do_not_add_this_alug_to_dictionary_table = array() ) {
        if ( !in_array( $language_code, $this->settings['translation-languages'] ) || $language_code === $this->settings['default-language'] ) {
            return array();
        }

        if ( !$this->machine_translator ) {
            $trp                      = TRP_Translate_Press::get_trp_instance();
            $this->machine_translator = $trp->get_component( 'machine_translator' );
        }

        $translated_strings            = array();
        $machine_translation_available = $this->machine_translator ? $this->machine_translator->is_available( array( $this->settings['default-language'], $language_code ) ) : false;

        $originals_without_translation_in_db_that_are_similar_with_already_translated_strings = array();

        if ( !$this->trp_query ) {
            $trp             = TRP_Translate_Press::get_trp_instance();
            $this->trp_query = $trp->get_component( 'query' );
        }

        // get existing translations
        $dictionary = $this->trp_query->get_existing_translations( array_values( $translateable_strings ), $language_code );
        if ( $dictionary === false ) {
            return array();
        }

        $new_strings                     = array();
        $machine_translatable_strings    = array();

        /**
         * The filter 'trp_add_similar_and_original_strings_to_db' becomes true only when TranslatePress Settings->Advanced Settings->Serve similar translations for strings that are almost identical
         * is set to 'Yes'
         * This filter appears in multiple places in this class since we use the function process_strings to make sure all the strings are inserted in the DataBase with their corresponding translation
         * The similar strings we reference are strings that are almost identical with strings that are in the DB and have translations.
         * We use those translated strings to translate the similar strings so the user or the translation engine does not have to do it anymore and we can
         * display them in frontend (for more information about how this process is done please look at the file tranlatepress\includes\advanced-settings\serve-similar-translation.php)
         *
         * In the code below we are populating the array '$originals_without_translation_in_db_that_are_similar_with_already_translated_strings' with original strings
         * from the page that do not have a translation, but are almost identical with strings that are already in DB with translation.
         * A similar string is determined by looking at its value in $dictionary (an array that contains all the strings on the page). The $dictionary has as keys
         * the strings mentioned before, which are objects containing more arguments, one of them being original. However, in the case of a similar original string
         * without translation, the argument 'original' is the almost identical string that has a translation stored in the DB.
         *
         * We collect these similar strings in an array so we can merge them later in new_strings in order to be introduced into the DB.
         */

        if ( apply_filters( 'trp_add_similar_and_original_strings_to_db', false ) ) {
            foreach ( array_keys( $dictionary ) as $value ) {
                if ( $value != $dictionary[ $value ]->original ) {
                    $originals_without_translation_in_db_that_are_similar_with_already_translated_strings[] = $value;
                }
            }
        }

        foreach ( $translateable_strings as $i => $string ) {
            if ( isset( $dictionary[ $string ]->translated ) && empty( $dictionary[ $string ]->translated ) ) {
                /* If we have an empty string with a status != NOT TRANSLATED, it's possible we are dealing with
                 * an intentional thing. After Automatic translation, a text can be translated with unallowed html
                 * thus being stored in DB as empty string with status = MACHINE TRANSLATED. By doing continue; we avoid
                 * re-autotranslating over and over again.
                 */
                continue;
            }
            // prevent accidentally machine translated strings from db such as for src to be displayed
            $skip_string = in_array( $string, $skip_machine_translating_strings );

            if ( isset( $dictionary[ $string ]->translated ) && $dictionary[ $string ]->status == $this->trp_query->get_constant_machine_translated() && $skip_string ) {
                continue;
            }
            //strings existing in database,
            if ( isset( $dictionary[ $string ]->translated ) ) {
                $translated_strings[ $i ] = $dictionary[ $string ]->translated;
            } elseif ( in_array( $string, $do_not_add_this_alug_to_dictionary_table )) {
                //do not add excluded links to dictionary
                continue;
            }else{

                $new_strings[ $i ] = $translateable_strings[ $i ];
                // if the string is not a url then allow machine translation for it

                if ( !$this->url_converter ){
                    $trp = TRP_Translate_Press::get_trp_instance();
                    $this->url_converter = $trp->get_component('url_converter');
                }

                if ( $machine_translation_available && !$skip_string && filter_var( $new_strings[ $i ], FILTER_VALIDATE_URL ) === false && !$this->url_converter->url_is_extra( $new_strings[ $i ] ) ) {
                    $machine_translatable_strings[ $i ] = $new_strings[ $i ];
                }
            }

            /**
             * Here we look through the $translateable_strings found and if they are also in the array $originals_without_translation_in_db_that_are_similar_with_already_translated_strings
             * then we assign them to new_strings to prepare them for being inserted into DB.
             */
            if ( apply_filters( 'trp_add_similar_and_original_strings_to_db', false ) ) {
                if ( in_array( $translateable_strings[ $i ], $originals_without_translation_in_db_that_are_similar_with_already_translated_strings ) ) {
                    $new_strings[ $i ] = $translateable_strings[ $i ];
                }
            }

        }

        $untranslated_list                                                    = $this->trp_query->get_untranslated_strings( $new_strings, $language_code );
        $update_strings                                                       = array();
        $unique_original_strings_with_machine_translations                    = array();

        // machine translate new strings in chunks, saving each chunk to the dictionary right away.
        // Saving per-chunk (instead of once at the end) means an aborted or overlapping page load
        // never re-sends, and re-bills, a chunk that was already translated and saved.
        $machine_strings = false;
        if ( $machine_translation_available ) {
            $machine_strings = array();

            // Request-wide time budget: once we pass the deadline we stop sending chunks and let the
            // page finish loading; the remaining strings are translated on future page loads. The
            // deadline is kept in a global so it is shared across the whole request - regular/DOM
            // strings (translated while the page renders) use up the budget before gettext strings
            // (translated on shutdown), so regular strings are prioritised.
            global $trp_machine_translation_deadline;
            if ( ! isset( $trp_machine_translation_deadline ) ) {
                $trp_machine_translation_deadline = microtime( true ) + apply_filters( 'trp_machine_translation_time_budget', 10 );
            }

            // Deduplicate before chunking so a string repeated across the page (e.g. "Read more") is
            // sent to the engine, and billed, only once. Downstream lookups are all keyed by the
            // original string, so the single translation is applied to every occurrence.
            $strings_to_machine_translate = array_unique( $machine_translatable_strings );

            foreach ( array_chunk( $strings_to_machine_translate, $this->machine_translator->get_chunk_size(), true ) as $strings_chunk ) {
                if ( microtime( true ) > $trp_machine_translation_deadline ) {
                    break;
                }

                $chunk_machine_strings = $this->machine_translator->translate( $strings_chunk, $language_code, $this->settings['default-language'] );
                if ( empty( $chunk_machine_strings ) ) {
                    continue;
                }
                $machine_strings += $chunk_machine_strings;

                // save this chunk to the dictionary before requesting the next one
                $chunk_originals        = array_keys( $chunk_machine_strings );
                $chunk_original_inserts = $this->trp_query->original_strings_sync( $language_code, $chunk_originals );
                $chunk_untranslated     = $this->trp_query->get_untranslated_strings( $chunk_originals, $language_code );
                $chunk_update_strings   = array();
                foreach ( $chunk_machine_strings as $original => $translated ) {
                    if ( ! isset( $chunk_original_inserts[ $original ] ) ) {
                        continue;
                    }
                    $id = ( isset( $chunk_untranslated[ $original ] ) ) ? $chunk_untranslated[ $original ]->id : NULL;
                    $chunk_update_strings[] = array(
                        'id'          => $id,
                        'original_id' => $chunk_original_inserts[ $original ]->id,
                        'original'    => trp_sanitize_string( $original, false ),
                        'translated'  => trp_sanitize_string( $translated ),
                        'status'      => $this->trp_query->get_constant_machine_translated() );
                }
                // keep a saved chunk's locks as recently translated markers; when the save is
                // skipped or fails, delete them so the strings can be retried right away
                $chunk_saved = false;
                if ( ! empty( $chunk_update_strings ) && apply_filters( 'trp_allow_string_saving', true, array(), $chunk_update_strings ) ) {
                    $chunk_saved = $this->trp_query->update_strings( $chunk_update_strings, $language_code, array( 'id', 'original', 'translated', 'status', 'original_id' ) );
                }
                $this->machine_translator->release_locks( $chunk_saved );
            }

            $unique_original_strings_with_machine_translations = array_keys( $machine_strings );
        }

        /**
         * If the option is activated,we use the below code so the variable $original_to_be_synced contains the array of strings that are to be sent to a machine translation
         * engine together with the similar one, so they have entries into the table the trp_original_strings
         */
        $originals_to_be_synced= array_merge( $unique_original_strings_with_machine_translations, $originals_without_translation_in_db_that_are_similar_with_already_translated_strings );

        $original_inserts = $this->trp_query->original_strings_sync( $language_code, $originals_to_be_synced );

        /* Machine-translated rows were saved per-chunk in the loop above, so they are intentionally
         * not added to $update_strings here. $update_strings below carries only the "similar strings"
         * rows. $machine_strings is still used further down to populate $translated_strings for
         * rendering this request. */

        // strings another request is translating right now must not be inserted as untranslated below: the lock holder inserts them when it saves
        $lock_skipped_strings = $this->machine_translator ? $this->machine_translator->get_lock_skipped_strings() : array();

        // update existing strings without translation if we have one now. also, do not insert duplicates for existing untranslated strings in db
        foreach( $new_strings as $i => $string ){

            if ( !isset($translated_strings[$i]) && isset( $machine_strings[$string] ) ) {
                $sanitized_machine_string = trp_sanitize_string( $machine_strings[$string] );
                if ( !empty( $sanitized_machine_string ) ){
                    // Unallowed HTML can be turned into empty string. Show original text instead
                    $translated_strings[$i] = $sanitized_machine_string;
                }
            }

            /**
             * In this code we add the original similar strings, that have now more arguments, including the translation taken from the similar string in
             * DB, to the $update_strings array, following now to update the field in the DB with all the information gathered.
             *
             * We check if the new_string is not already in the DB and if it is we unset it in order to avoid multiple entries in DB for thr same string.
             * The similar strings have status 3 in DB.
             */

            if ( apply_filters('trp_add_similar_and_original_strings_to_db', false) ) {

                if (isset($new_strings[$i]) && in_array($new_strings[$i],$originals_without_translation_in_db_that_are_similar_with_already_translated_strings ) ) {

                    $id = ( isset( $untranslated_list[$string] ) ) ? $untranslated_list[$string]->id : NULL;
                    array_push( $update_strings, array(
                        'id'          => $id,
                        'original'    => $new_strings[$i],
                        'translated'  => trp_sanitize_string( $dictionary[$string]->translated ),
                        'status'      => $this->trp_query->get_constant_similar_translated(),
                        'original_id' => $original_inserts[ $string ]->id) );

                    unset($new_strings[$i]);
                }

            }

            if ( isset( $untranslated_list[ $string ] ) || isset( $machine_strings[ $string ] ) || isset( $lock_skipped_strings[ $string ] ) ) {
                unset( $new_strings[ $i ] );
            }
        }


        // Allow filtering whether to save strings to database (for manual translation only mode)
        if ( apply_filters( 'trp_allow_string_saving', true, $new_strings, $update_strings ) ) {
            $this->trp_query->insert_strings( $new_strings, $language_code, $block_type );
            $this->trp_query->update_strings( $update_strings, $language_code, array( 'id','original', 'translated', 'status', 'original_id' ) );
        }

        return $translated_strings;
    }

    /**
     * Whether given node has ancestor with given attribute.
     *
     * @param object $node          Html Node.
     * @param string $attribute     Attribute to search for.
     * @return mixed                Whether given node has ancestor with given attribute.
     */
    public function has_ancestor_attribute($node,$attribute) {
        $currentNode = $node;
        if ( isset( $node->$attribute ) ){
            return $node;
        }
        while($currentNode->parent() && $currentNode->parent()->tag!="html") {
            if(isset($currentNode->parent()->$attribute))
                return $currentNode->parent();
            else
                $currentNode = $currentNode->parent();
        }
        return false;
    }

    /**
     * Whether given node has ancestor with given class.
     *
     * @param object $node           Html Node.
     * @param string $class     class to search for
     * @return bool                 Whether given node has ancestor with given class.
     */
    public function has_ancestor_class($node, $class) {
        $currentNode = $node;

        while($currentNode->parent() && $currentNode->parent()->tag!="html") {
            if(isset($currentNode->parent()->class) && strpos($currentNode->parent()->class, $class) !== false) {
                return true;
            } else {
                $currentNode = $currentNode->parent();
            }
        }
        return false;
    }

	/**
	 * Which attributes to translate and how to access them
	 *
	 * Nodes with "selector" attribute are automatically searched for in PHP translate_page and in JS translate-dom-changes
	 *
	 * @return array
	 */
    public function get_node_accessors(){
	    return apply_filters( 'trp_node_accessors', array(
		    'text' => array(
			    'accessor' => 'outertext',
			    'attribute' => false
		    ),
		    'block' => array(
			    'accessor' => 'innertext',
			    'attribute' => false
		    ),
		    'image_src' => array(
		    	'selector' => 'img[src]',
			    'accessor' => 'src',
			    'attribute' => true
		    ),
		    'submit' => array(
		    	'selector' => 'input[type=\'submit\'],input[type=\'button\'], input[type=\'reset\']',
			    'accessor' => 'value',
			    'attribute' => true
		    ),
		    'placeholder' => array(
			    'selector' => 'input[placeholder],textarea[placeholder]',
			    'accessor' => 'placeholder',
			    'attribute' => true
		    ),
		    'title' => array(
		    	'selector' => '[title]',
			    'accessor' => 'title',
			    'attribute' => true
		    ),
		    'a_href' => array(
		    	'selector' => 'a[href]',
			    'accessor' => 'href',
			    'attribute' => true
		    ),
		    'button' => array(
			    'accessor' => 'outertext',
			    'attribute' => false
		    ),
		    'option' => array(
			    'accessor' => 'innertext',
			    'attribute' => false
		    ),
            'aria_label' => array(
                'selector' => '[aria-label]',
                'accessor' => 'aria-label',
                'attribute' => true
            ),
            'video_src' => array(
                'selector' => 'video[src]',
                'accessor' => 'src',
                'attribute' => true
            ),
            'video_poster' => array(
                'selector' => 'video[poster]',
                'accessor' => 'poster',
                'attribute' => true
            ),
            'video_source_src' => array(
                'selector' => 'video source[src]',
                'accessor' => 'src',
                'attribute' => true
            ),
            'audio_src' => array(
                'selector' => 'audio[src]',
                'accessor' => 'src',
                'attribute' => true
            ),
            'audio_source_src' => array(
                'selector' => 'audio source[src]',
                'accessor' => 'src',
                'attribute' => true
            ),
            'picture_image_src' => array(
                'selector' => 'picture image[src]',
                'accessor' => 'src',
                'attribute' => true
            ),
            'picture_source_srcset' => array(
                'selector' => 'picture source[srcset]',
                'accessor' => 'srcset',
                'attribute' => true
            ),
	    ));
    }

    public function get_accessors_array( $prefix = '' ){
    	$accessor_array = array();
	    $node_accessors_array = $this->get_node_accessors();
	    foreach ( $node_accessors_array as $node_accessor ){
	    	if ( isset ( $node_accessor['accessor'] ) ){
			    $accessor_array[] = $prefix . $node_accessor['accessor'];
		    }
	    }

	    return array_values( array_unique( $accessor_array ) );
    }

    /*
     * Enqueue scripts on all pages
     */
	public function enqueue_scripts() {

		// so far only when woocommerce is active we need to enqueue this script on all pages
		if ( class_exists( 'WooCommerce' ) ){
			wp_enqueue_script('trp-frontend-compatibility', TRP_PLUGIN_URL . 'assets/js/trp-frontend-compatibility.js', array(), TRP_PLUGIN_VERSION );
		}

	}

	public function get_trp_data(){
		global $TRP_LANGUAGE;

		$trp = TRP_Translate_Press::get_trp_instance();
		if ( ! $this->translation_manager ) {
			$this->translation_manager = $trp->get_component( 'translation_manager' );
		}
		$nonces = $this->translation_manager->editor_nonces();

		$language_to_query = $TRP_LANGUAGE;
		if ( $TRP_LANGUAGE == $this->settings['default-language']  ) {
			foreach ($this->settings['translation-languages'] as $language) {
				if ( $language != $this->settings['default-language'] ) {
					$language_to_query = $language;
					break;
				}
			}
		}
		$language_to_query = ( count ( $this->settings['translation-languages'] ) < 2 ) ? '' : $language_to_query;

		return array(
            'trp_custom_ajax_url'                                  => apply_filters( 'trp_custom_ajax_url', TRP_PLUGIN_URL . 'includes/trp-ajax.php' ),
            'trp_wp_ajax_url'                                      => apply_filters( 'trp_wp_ajax_url', admin_url( 'admin-ajax.php' ) ),
            'trp_language_to_query'                                => $language_to_query,
            'trp_original_language'                                => $this->settings['default-language'],
            'trp_blog_id'                                          => get_current_blog_id(), // used by trp-ajax.php to query the subsite's tables on multisite
            'trp_current_language'                                 => $TRP_LANGUAGE,
            'trp_skip_selectors'                                   => apply_filters( 'trp_skip_selectors_from_dynamic_translation', array( '[data-no-translation]', '[data-no-dynamic-translation]', '[data-trp-translate-id-innertext]', 'script', 'style', 'head', 'trp-span', 'translate-press' ), $TRP_LANGUAGE, $this->settings ), // data-trp-translate-id-innertext refers to translation block and it shouldn't be detected
            'trp_base_selectors'                                   => $this->get_base_attribute_selectors(),
            'trp_attributes_selectors'                             => $this->get_node_accessors(),
            'trp_attributes_accessors'                             => $this->get_accessors_array(),
            'gettranslationsnonceregular'                          => $nonces['gettranslationsnonceregular'],
            'showdynamiccontentbeforetranslation'                  => apply_filters( 'trp_show_dynamic_content_before_translation', false ),
            'skip_strings_from_dynamic_translation'                => apply_filters( 'trp_skip_strings_from_dynamic_translation', array() ),
            'skip_strings_from_dynamic_translation_for_substrings' => apply_filters( 'trp_skip_strings_from_dynamic_translation_for_substrings', array( 'href' => array('amazon-adsystem', 'googleads', 'g.doubleclick') ) ),
            'duplicate_detections_allowed'                         => apply_filters( 'trp_duplicate_detections_allowed', 100 ),
            'trp_translate_numerals_opt'                           => isset ($this->settings["trp_advanced_settings"]["enable_numerals_translation"]) ? $this->settings["trp_advanced_settings"]["enable_numerals_translation"] : 'no',
            'trp_no_auto_translation_selectors'                    => apply_filters( 'trp_no_auto_translate_selectors', array( '[data-no-auto-translation]' ), $TRP_LANGUAGE )
		);
	}

    /**
     * Enqueue dynamic translation script.
     */
    public function enqueue_dynamic_translation(){
        $enable_dynamic_translation = apply_filters( 'trp_enable_dynamic_translation', true );
        if ( ! $enable_dynamic_translation ){
            return;
        }

        global $TRP_LANGUAGE;

        if ( $TRP_LANGUAGE != $this->settings['default-language'] || ( isset( $_REQUEST['trp-edit-translation'] ) && $_REQUEST['trp-edit-translation'] == 'preview' ) ) {
            $this->output_dynamic_translation_script();
        }
    }

    /**
     * If is_late_dom_html_plugin_active() returns true, echo script on shutdown hook priority 10
     *
     * Otherwise, enqueue script
     *
     * @see is_late_dom_html_plugin_active()
     *
     */
    public function output_dynamic_translation_script(){
        $script_src     = TRP_PLUGIN_URL . 'assets/js/trp-translate-dom-changes.js';
        $trp_data       = $this->get_trp_data();
        $trp_plugin_ver = TRP_PLUGIN_VERSION;

        $echo_scripts = function() use ( $script_src, $trp_data, $trp_plugin_ver ){
            echo '<script type="text/javascript" id="trp-dynamic-translator-js-extra"> var trp_data = ' . json_encode( $trp_data ) . ';</script>';
            echo '<script src="' . esc_url( $script_src  ) . '?ver=' . esc_attr($trp_plugin_ver) .'" id="trp-dynamic-translator-js"></script>';
        };

        if ( is_late_dom_html_plugin_active() ){
            add_action( 'shutdown', $echo_scripts );
            return;
        }

        wp_enqueue_script('trp-dynamic-translator', $script_src, array('jquery'), TRP_PLUGIN_VERSION, true );
        wp_localize_script('trp-dynamic-translator', 'trp_data', $trp_data );
    }

	/**
	 * Skip base selectors (data-trp-translate-id, data-trpgettextoriginal etc.)
	 *
	 * The base selectors (without any suffixes) are placed only if their children do not contain any nodes that are translatable
	 *
 	 * hooked to trp_skip_selectors_from_dynamic_translation
	 *
	 * @param $skip_selectors
	 *
	 * @return array
	 */
    public function skip_base_attributes_from_dynamic_translation( $skip_selectors ){
	    $base_attributes = $this->get_base_attribute_selectors();
	    $selectors_to_skip = array();
	    foreach( $base_attributes as $base_attribute ){
		    $selectors_to_skip[] = '[' . $base_attribute . ']';
	    }
	    return array_merge( $skip_selectors, $selectors_to_skip );
    }

	/*
	 * Get base attribute selectors
	 */
	public function get_base_attribute_selectors(){
		return apply_filters( 'trp_base_attribute_selectors', array( 'data-trp-translate-id', 'data-trpgettextoriginal', 'data-trp-post-slug' ) );
	}


    /**
     * Add a filter on the wp_mail function so we allow shortcode usage and run it through our translate function so it cleans it up nice and maybe even replace some strings
     * @param $args
     * @return array
     */
    public function wp_mail_filter( $args ) {
        if ( ! is_array( $args ) ) {
            return $args;
        }

        if ( empty( $args['to'] ) ) {
            return $args;
        }

        /* Skip full email translation in request contexts where TranslatePress does not wrap
           gettext strings (wp-login.php, wp-admin, xmlrpc, TP editor requests). There the
           email body carries no trp-gettext markers, so translate_page() would treat every
           line - including security URLs like the password-reset link - as a regular
           dynamic string and persist it to the dictionary, from where it can be disclosed.
           Whitelisted conditional shortcodes must still be evaluated because WooCommerce
           does not process them before wp_mail. See CU-869ehfvac and CU-869ekd2dw. */
        global $pagenow;
        if ( ! $this->url_converter ) {
            $trp                 = TRP_Translate_Press::get_trp_instance();
            $this->url_converter = $trp->get_component( 'url_converter' );
        }
        $is_order_email = TRP_Woocommerce_Emails::is_sending_order_email();
        $skip_email_translation = ! $is_order_email && (
            $pagenow === 'wp-login.php'
            || $pagenow === 'xmlrpc.php'
            || ( is_admin() && ! TRP_Gettext_Manager::is_ajax_on_frontend() )
            || $this->url_converter->is_admin_request()
        );

        global $TRP_LANGUAGE;

        $initial_language = $TRP_LANGUAGE;

        $recipient = $args['to'];

        // Normalize $recipient to a single email string (first recipient only - that's the main one)
        if ( is_array( $recipient ) ) {
            $first = reset( $recipient );
            $recipient = is_string( $first ) ? $first : '';
        }

        $recipient = (string) $recipient;

        // Keep only the first comma-separated entry if multiple are present in the string
        $recipient = trim( strtok( $recipient, ',' ) );

        $did_switch_language = false;

        // TranslatePress already selected the order email language in trp_woo_setup_locale().
        if ( ! $is_order_email && $recipient !== '' ) {
            $did_switch_language = trp_switch_to_preffered_language( $recipient );
        }

        $whitelisted_shortcodes = apply_filters(
            'trp_whitelisted_shortcodes_for_wp_mail',
            array( 'trp_language', 'language-include', 'language-exclude' )
        );

        if ( array_key_exists( 'subject', $args ) ) {
            $args['subject'] = trp_do_these_shortcodes( $args['subject'], $whitelisted_shortcodes );

            if ( ! $skip_email_translation ) {
                $args['subject'] = $this->translate_page( $args['subject'] );
            }
        }

        if ( array_key_exists( 'message', $args ) ) {
            $args['message'] = trp_do_these_shortcodes( $args['message'], $whitelisted_shortcodes );

            if ( ! $skip_email_translation ) {
                $args['message'] = $this->translate_page( $args['message'] );
            }
        }

        if ( $did_switch_language ) {
            trp_restore_language();
        } else {
            // No preferred-language switch happened, so restore only the request language.
            $TRP_LANGUAGE = $initial_language;
        }

        return $args;
    }

    /**
     * Filters the location redirect to add the preview parameter to the next page
     * @param $location
     * @param $status
     * @return string
     * @since 1.0.8
     */
    public function force_preview_on_url_redirect( $location, $status ){
        if( isset( $_REQUEST['trp-edit-translation'] ) && $_REQUEST['trp-edit-translation'] == 'preview' ){
            $location = add_query_arg( 'trp-edit-translation', 'preview', $location );
        }
        return $location;
    }

    /**
     * Filters the location redirect to add the current language based on the trp-form-language parameter
     * @param $location
     * @param $status
     * @return string
     * @since 1.1.2
     */
    public function force_language_on_form_url_redirect( $location, $status ){
        if( isset( $_REQUEST[ 'trp-form-language' ] ) && !empty($_REQUEST[ 'trp-form-language' ]) ){
            $form_language_slug = sanitize_text_field($_REQUEST[ 'trp-form-language' ]);
            $form_language = array_search($form_language_slug, $this->settings['url-slugs']);
            if ( ! $this->url_converter ) {
                $trp = TRP_Translate_Press::get_trp_instance();
                $this->url_converter = $trp->get_component('url_converter');
            }

            // Redirects do not pass through the HTML renderer that removes link markers.
            $location = $this->url_converter->get_url_for_language( $form_language, $location, '' );
        }
        return $location;
    }

    /**
     * Filters the output buffer of ajax calls that return json and adds the preview arg to urls
     * @param $output
     * @return string
     * @since 1.0.8
     */
    public function force_preview_on_url_in_ajax( $output ){
        if ( TRP_Gettext_Manager::is_ajax_on_frontend() && isset( $_REQUEST['trp-edit-translation'] ) && $_REQUEST['trp-edit-translation'] === 'preview' && $output != false ) {
            $result = json_decode($output, TRUE);
            if ( json_last_error() === JSON_ERROR_NONE) {
                if( !is_array( $result ) )//make sure we send an array as json_decode even with true parameter might not return one
                    $result = array($result);
                array_walk_recursive($result, array($this, 'callback_add_preview_arg'));
                $output = trp_safe_json_encode($result);
            } //endif
        } //endif
        return $output;
    }

    /**
     * Adds preview query arg to links that are url's. callback specifically for the array_walk_recursive function
     * @param $item
     * @param $key
     * @return string
     * @internal param $output
     * @since 1.0.8
     */
    function callback_add_preview_arg(&$item, $key){
        if ( filter_var($item, FILTER_VALIDATE_URL) !== FALSE ) {
            $item = add_query_arg( 'trp-edit-translation', 'preview', $item );
        }
    }

    /**
     * Filters the output buffer of ajax calls that return json and adds the preview arg to urls
     * @param $output
     * @return string
     * @since 1.1.2
     */
    public function force_form_language_on_url_in_ajax( $output ){
        if ( TRP_Gettext_Manager::is_ajax_on_frontend() && isset( $_REQUEST[ 'trp-form-language' ] ) && !empty( $_REQUEST[ 'trp-form-language' ] ) ) {
            $result = json_decode($output, TRUE);
            if ( is_array( $result ) && json_last_error() === JSON_ERROR_NONE) {
                array_walk_recursive($result, array($this, 'callback_add_language_to_url'));
                $output = trp_safe_json_encode($result);
            } //endif
        } //endif
        return $output;
    }

    /**
     * Adds preview query arg to links that are url's. callback specifically for the array_walk_recursive function
     * @param $item
     * @param $key
     * @return string
     * @internal param $output
     * @since 1.1.2
     */
    function callback_add_language_to_url(&$item, $key){
        if ( filter_var($item, FILTER_VALIDATE_URL) !== FALSE ) {
            $form_language_slug = isset( $_REQUEST[ 'trp-form-language' ] ) ? sanitize_text_field($_REQUEST[ 'trp-form-language' ]) : '';
            $form_language = array_search($form_language_slug, $this->settings['url-slugs']);
            if ( ! $this->url_converter ) {
                $trp = TRP_Translate_Press::get_trp_instance();
                $this->url_converter = $trp->get_component('url_converter');
            }

            $item = $this->url_converter->get_url_for_language( $form_language, $item );
	        $item  = str_replace('#TRPLINKPROCESSED', '', $item);
        }
    }

    /**
     * Function that reverses CDATA string replacement from the content because it breaks the renderer
     * @param $output
     * @return mixed
     */
    public function handle_cdata( $output ){
        $output = str_replace( ']]&gt;', ']]>', $output );
        return $output;
    }

    /**
     * Function always renders the default language wptexturize characters instead of the translated ones for secondary languages.
     * @param string
     * @param string
     * @param string
     * @param string
     * @return string
     */
    function fix_wptexturize_characters( $translated, $text, $context, $domain ){
        global $TRP_LANGUAGE;
        $trp = TRP_Translate_Press::get_trp_instance();
        $trp_settings = $trp->get_component( 'settings' );
        $settings = $trp_settings->get_settings();

        $default_language= $settings["default-language"];

        // it's reversed because the same string &#8217; is replaced differently based on context and we can't have the same key twice on an array
        $list_of_context_text = array(
            'opening curly double quote' => '&#8220;',
            'closing curly double quote' => '&#8221;',
            'apostrophe' => '&#8217;',
            'prime' => '&#8242;',
            'double prime' => '&#8243;',
            'opening curly single quote' => '&#8216;',
            'closing curly single quote' => '&#8217;',
            'en dash' => '&#8211;',
            'em dash' => '&#8212;',
            'Comma-separated list of words to texturize in your language' => "'tain't,'twere,'twas,'tis,'twill,'til,'bout,'nuff,'round,'cause,'em",
            'Comma-separated list of replacement words in your language' => '&#8217;tain&#8217;t,&#8217;twere,&#8217;twas,&#8217;tis,&#8217;twill,&#8217;til,&#8217;bout,&#8217;nuff,&#8217;round,&#8217;cause,&#8217;em'
        );

        if( $default_language != $TRP_LANGUAGE && array_key_exists($context, $list_of_context_text) && in_array($text, $list_of_context_text) ){
            return trp_x( $text, $context, '', $default_language );
        }

        return $translated;
    }

    /**
     * Function that wraps the post title and the post content in a custom trp wrap trp-post-container so we can know in
     * the function translate_page() if a string is part of the content of a post so we can store a meta that gives the string context
     * @param $content
     * @param null $id
     * @return string
     */
    function wrap_with_post_id( $content, $id = null ){
        global $post, $TRP_LANGUAGE, $wp_query;

        if( empty($post->ID) )
            return $content;

        //we try to wrap only the actual content of the post and not when the filters are executed in SEO plugins for example
        if( ( !$wp_query->in_the_loop || !is_main_query() ) && apply_filters( 'trp_wrap_with_post_id_overrule', true, $content, $id ) )
            return $content;

        //for the_tile filter we have an $id and we can compare it with the post we are on ..to avoid wrapping titles in menus for example
        if( !is_null( $id ) && $id !== $post->ID ){
            return $content;
        }

        if ( $TRP_LANGUAGE !== $this->settings['default-language'] ) {
            if ( is_singular() && !empty($post->ID)) {
                $content = "<trp-post-container data-trp-post-id='" . $post->ID . "'>" . $content . "</trp-post-container>";//changed " to ' to not break cases when the filter is applied inside an html attribute (title for example)
            }
        }

        return $content;
    }

	/**
	 * Function that wraps around the PHP's is_numeric function and adds an additional check,
	 * namely the option to translate numerals/numbers to be on.
	 * @param $str
	 * @return bool
	 */
    function trp_is_numeric($str){
    	if (is_numeric($str)){
    		if (isset($this->settings["trp_advanced_settings"]["enable_numerals_translation"]) && $this->settings["trp_advanced_settings"]["enable_numerals_translation"] === 'yes') {
	            return false;
		        } else {
			    return true;
		        }
    	}
    	else {
    		return false;
    	}
    }

    /**
     * Whether a text contains html tags.
     * Match an opening or closing tag among given html tags.
     * @param $string
     * @return bool
     */
    public function is_html( $string ){
        $pattern = '/<\/?(' . $this->common_html_tags . ')(\s[^>]*)?(\s?\/)?\>/';
        return preg_match($pattern, $string, $matches);
    }

    /**
     * Matches the conditions set by $key_term_arrays
     *
     * See $skip_strings_containing_key_terms for config
     *
     * @param $string
     * @param $key_terms_arrays
     * @return bool
     */
    public function contains_substrings($string, $key_terms_arrays){
        foreach( $key_terms_arrays as $key_terms ) {
            if ( !empty( $key_terms['operator'] ) && !empty( $key_terms['terms'] ) && is_array( $key_terms ) ) {
                if ( $key_terms['operator'] == 'or' ){
                    foreach ( $key_terms['terms'] as $term ) {
                        if ( stripos( $string, $term ) !== false ) {
                            return true;
                        }
                    }
                }
                if ( $key_terms['operator'] == 'and' ){
                    foreach ( $key_terms['terms'] as $array_key => $term ) {
                        if ( stripos( $string, $term ) !== false ) {
                            unset($key_terms['terms'][$array_key]);
                            if ( count ($key_terms['terms'] ) == 0 ){
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Searches for strings that are emails that go through antispambot() function in wp and saves them in the db not html encoded.
     * Hooks on the trp_translateable_strings filter defined in translate_page()
     *
     * @param $translateable_information
     * @param $html
     * @param $no_translate_attribute
     * @param $global_TRP_LANGUAGE
     * @param $language_code
     * @param $instance_TRP_Translation_Render
     * @return array
     */
    public function antispambot_infinite_detection_fix( $translateable_information, $html, $no_translate_attribute, $global_TRP_LANGUAGE, $language_code, $instance_TRP_Translation_Render)
    {
        if (!is_array($translateable_information['translateable_strings'])){
            return $translateable_information;
        }

        foreach ($translateable_information['translateable_strings'] as $key => $string){
            $translateable_information['translateable_strings'][$key] = is_email(html_entity_decode($string)) ? html_entity_decode($string) : $string;
        }
        return $translateable_information;
    }

    /**
     * Remove excluded tags before translation
     * and replaces them with <trp-replace-$index></trp-replace-$index>
     *
     * @param $output
     * @param $excluded_tags
     * @return array
     */
    private function remove_tags_from_output($output, $excluded_tags)
    {
        if (!is_string($output) || !is_array($excluded_tags)) {
            return array('output' => $output, 'excluded_tags' => array());
        }
        
        $trp_excluded_replacements = [];

        $index = 0;
        $result = '';
        $offset = 0;

        while (true) {
            $found = false;

            foreach ($excluded_tags as $tag) {
                $start = stripos($output, "<$tag", $offset);
                // We keep the first found tag - min($start_for_tag_script, $start_for_tag_style) where $start < $found['pos']
                if ($start !== false && ($found === false || $start < $found['pos'])) {
                    $found = [
                        'pos' => $start,
                        'tag' => $tag
                    ];
                }
            }

            if ($found === false) {
                // No more excluded tags found
                $result .= substr($output, $offset);
                break;
            }

            $start = $found['pos'];
            $tag   = $found['tag'];

            // Add everything before the tag
            $result .= substr($output, $offset, $start - $offset);

            // Find closing tag </tag>
            $end = stripos($output, "</$tag", $start);
            if ($end === false) {
                // malformed: no closing tag
                $result .= substr($output, $start);
                break;
            }

            // Move to closing ">"
            $close_pos = strpos($output, '>', $end);
            if ($close_pos === false) {
                // malformed: no ">"
                $result .= substr($output, $start);
                break;
            }

            /**
             * Preserve <script type="application/ld+json"> blocks,
             * so they remain in the DOM and can be processed by
             * translate_schema_data() via trp_process_other_text_nodes.
             */
            if ( $tag === 'script' ) {
                $open_tag_end = strpos( $output, '>', $start );
                if ( $open_tag_end === false ) {
                    // malformed: no ">" on opening tag
                    $result .= substr( $output, $start );
                    break;
                }

                // Opening <script ...> tag only
                $opening_tag_html = substr( $output, $start, $open_tag_end - $start + 1 );

                // If this is JSON-LD, keep the whole block untouched
                if ( stripos( $opening_tag_html, 'application/ld+json' ) !== false ) {
                    // Append the full <script>...</script> block
                    $result .= substr( $output, $start, $close_pos - $start + 1 );
                    $offset = $close_pos + 1;
                    // Do NOT record a replacement / placeholder for this one
                    continue;
                }
            }

            // Full <tag>...</tag>
            $tag_html = substr($output, $start, $close_pos - $start + 1);

            // Save replacement
            $trp_excluded_replacements[$index] = $tag_html;

            // Insert placeholder
            $result .= "<trp-replace-$index></trp-replace-$index>";

            // Advance offset
            $offset = $close_pos + 1;
            $index++;
        }

        return array('output' => $result, 'excluded_tags' => $trp_excluded_replacements);
    }

    private function add_excluded_tags_after_translation($final_html, $trp_excluded_replacements)
    {
        if (!is_string($final_html) || !is_array($trp_excluded_replacements) || empty($trp_excluded_replacements)) {
            return $final_html;
        }

        foreach ($trp_excluded_replacements as $index => $tag_html) {
            $placeholder = "<trp-replace-$index></trp-replace-$index>";
            $final_html = str_replace($placeholder, $tag_html, $final_html);
        }

        return $final_html;
    }
}
