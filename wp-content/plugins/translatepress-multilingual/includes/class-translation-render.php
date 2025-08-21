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
     * Function to hide php errors and notice and instead log them in debug.log so we don't store the notice strings inside the db if WP_DEBUG is on
     */
    public function trp_debug_mode_off(){
        if ( WP_DEBUG ) {
            ini_set('display_errors', 0);
            ini_set('log_errors', 1);
            ini_set('error_log', WP_CONTENT_DIR . '/debug.log');
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
	    return preg_replace('/\s+/', ' ',   html_entity_decode( htmlspecialchars_decode( wp_strip_all_tags(trp_full_trim( $string )), ENT_QUOTES ) ) ) ;
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
     * Function that translates the content excerpt and post title in the REST API
     * @param $response
     * @return mixed
     */
    public function handle_rest_api_translations($response){
    	if ( isset( $response->data ) ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            $url_converter = $trp->get_component( 'url_converter' );
            $language = $url_converter->get_lang_from_url_string( $url_converter->cur_page_url() );

            if ( $language == $this->settings['default-language'] || $language == null) {
                return $response; // exit early in default language.
            }

            if ( isset( $response->data['name'] ) ){
                $response->data['name'] = $this->translate_page( $response->data['name'] );
            }
		    if (isset($response->data['title']['rendered'])) {
			    $response->data['title']['rendered'] = $this->translate_page( $response->data['title']['rendered'] );
		    }
		    if (isset($response->data['excerpt']['rendered'])) {
			    $response->data['excerpt']['rendered'] = $this->translate_page( $response->data['excerpt']['rendered'] );
		    }
		    if (isset($response->data['content']['rendered'])) {
			    $response->data['content']['rendered'] = $this->translate_page( $response->data['content']['rendered'] );
		    }
            if ( isset( $response->data['description'] ) ) {
			    $response->data['description'] = $this->translate_page( $response->data['description'] );
		    }
            if ( isset( $response->data['slug'] ) && class_exists( 'TRP_Slug_Query' ) ) {
                $trp_slug_query = new TRP_Slug_Query();
                $slug_array = array( $response->data['slug'] );
                $translated_slugs = $trp_slug_query->get_translated_slugs_from_original( $slug_array, $language );

                if ( !empty( $translated_slugs ) && isset( $translated_slugs[$response->data['slug']] ) ) {
                    $response->data['slug'] = $translated_slugs[$response->data['slug']];
                }
            }
	    }
        return $response;
    }

	/**
	 * Apply translation filters for REST API response
	 */
	public function add_callbacks_for_translating_rest_api(){
        $post_types = array_merge(["comment"], get_post_types(), get_taxonomies());
		foreach ( $post_types as $post_type ) {
			add_filter( 'rest_prepare_'. $post_type, array( $this, 'handle_rest_api_translations' ) );
		}
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
        $output = str_ireplace('#!trpst#', '<', $output);
        $output = str_ireplace('#!trpen#', '>', $output);

        $output = apply_filters('trp_before_translate_content', $output);

        if ( $output == false || !is_string( $output ) || strlen( $output ) < 1 ) {
            return $output;
        }

        if ( ! $this->url_converter ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->url_converter = $trp->get_component('url_converter');
        }


        /* make sure we only translate on the rest_prepare_$post_type filter in REST requests and not the whole json */

        /* in certain cases $wp_rewrite is null, so it trows a fatal error. This is just a quick fix. The actual issue is probably in WordPress core
         * see taskid #2pjped
         */
        global $wp_rewrite;
        if( is_object($wp_rewrite) ) {
            if( strpos( $this->url_converter->cur_page_url( false ), get_rest_url() ) !== false && strpos( current_filter(), 'rest_prepare_' ) !== 0 && current_filter() !== 'oembed_response_data' ){
                $trpremoved = $this->remove_trp_html_tags( $output );
                return $trpremoved;
            }
        }

        /* don't do anything on xmlrpc.php  */
        if( strpos( $this->url_converter->cur_page_url( false ), 'xmlrpc.php' ) !== false ){
            $trpremoved = $this->remove_trp_html_tags( $output );
            return $trpremoved;
        }

        global $TRP_LANGUAGE;
        $language_code = $this->force_language_in_preview();
        if ($language_code === false) {
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
            if ( ! empty( $_REQUEST['action'] ) && strpos( sanitize_text_field( $_REQUEST['action'] ), 'trp_' ) === 0 && $_REQUEST['action'] != 'trp_split_translation_block' )
		        return $output;

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
                $original_gettext_translation_id = $row->getAttribute('data-trpgettextoriginal');
                /* Parent node has no other children and no other innertext besides the current node */
                if( count( $row->parent()->children ) == 1 && $row->parent()->innertext == $row->outertext ){
                    $row->outertext = $row->innertext();
                    $row->parent()->setAttribute($no_translate_attribute, '');
	                $row->parent()->setAttribute('data-trp-gettext', '');
                    // we are in the editor
                    if (isset($_REQUEST['trp-edit-translation']) && $_REQUEST['trp-edit-translation'] == 'preview') {
                        //move up the data-trpgettextoriginal attribute
                        $row->parent()->setAttribute('data-trpgettextoriginal', $original_gettext_translation_id);
                    }
                }
                else{
                    /* Setting this attribute using setAttribute function actually changes the $html object.
                    Important for not detecting this gettext as a regular string in the next lines using find()  */
                    $row->setAttribute($no_translate_attribute, '');

                    /* Changes made to outertext take place only after saving the html object to a string */
                    $row->outertext = '<trp-wrap class="trp-wrap" data-no-translation';
                    if (isset($_REQUEST['trp-edit-translation']) && $_REQUEST['trp-edit-translation'] == 'preview') {
                        $row->outertext .= ' data-trpgettextoriginal="'. $original_gettext_translation_id .'"';
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

	                        // attributes of these tags are not handled well by the parser so don't escape them [see iss6264]
	                        if ( $row->tag != 'script' && $row->tag != 'style' ){
		                        $saved_node_from_value = esc_attr($saved_node_from_value);
	                        }

	                        $row->setAttribute($attr_name, $saved_node_from_value );
                            $row->setAttribute($no_translate_attribute . '-' . $attr_name, '');
                            // we are in the editor
                            if (isset($_REQUEST['trp-edit-translation']) && $_REQUEST['trp-edit-translation'] == 'preview') {
                                $original_gettext_translation_id = $nfv_row->getAttribute('data-trpgettextoriginal');
                                $row->setAttribute('data-trpgettextoriginal-' . $attr_name, $original_gettext_translation_id);
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

                    // outside preview mode we build the $translateable_strings_manual array for src
                    if ( $current_node_accessor_selector === 'src' && !$preview_mode && $trimmed_string != ''){
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
                if ( isset( $translated_strings_manual_dictionary[ $string_manual ]->translated ) ) {
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
                    $nodes[$i]['node']->$accessor = str_replace( $translateable_string_manual, trp_sanitize_string($translated_strings_manual[$i]), $node_manual['node']->$accessor );
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

       /* perform preg replace on the remaining trp-gettext tags */
        $final_html = $this->remove_trp_html_tags( $final_html );

	    return apply_filters( 'trp_translated_html', $final_html, $TRP_LANGUAGE, $language_code, $preview_mode );
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

            $a_href->href = str_replace('#TRPLINKPROCESSED', '', $a_href->href);
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
                $row->action = str_replace( '#TRPLINKPROCESSED', '', esc_url( $row->action ) );
            }
        }

        foreach ( $html->find('link') as $link ) {
            if ( isset( $link->href ) ) {
                if ( isset( $link->rel ) && ( $link->rel == 'next' || $link->rel == 'prev' ) )
                    $link->href = $this->url_converter->get_url_for_language( $TRP_LANGUAGE, $link->href );

                $link->href = str_replace('#TRPLINKPROCESSED', '', $link->href);
            }
        }

        return $html;
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


    /**
     * function that removes any unwanted leftover <trp-gettext> tags
     * @param $string
     * @return string|string[]|null
     */
    function remove_trp_html_tags( $string ){
        $string = preg_replace( '/(<|&lt;)trp-gettext (.*?)(>|&gt;)/i', '', $string );
        $string = preg_replace( '/(<|&lt;)(\\\\)*\/trp-gettext(>|&gt;)/i', '', $string );

        // In case we have a gettext string which was run through rawurlencode(). See more details on iss6563
        $string = preg_replace( '/%23%21trpst%23trp-gettext(.*?)%23%21trpen%23/i', '', $string );
        $string = preg_replace( '/%23%21trpst%23%2Ftrp-gettext%23%21trpen%23/i', '', $string );
        $string = preg_replace( '/%23%21trpst%23%5C%2Ftrp-gettext%23%21trpen%23/i', '', $string );

        if (!isset($_REQUEST['trp-edit-translation']) || $_REQUEST['trp-edit-translation'] != 'preview') {
            $string = preg_replace('/(<|&lt;)trp-wrap (.*?)(>|&gt;)/i', '', $string);
            $string = preg_replace('/(<|&lt;)(\\\\)*\/trp-wrap(>|&gt;)/i', '', $string);
        }

        //remove post containers before outputting
        $string = preg_replace( '/(<|&lt;)trp-post-container (.*?)(>|&gt;)/i', '', $string );
        $string = preg_replace( '/(<|&lt;)(\\\\)*\/trp-post-container(>|&gt;)/i', '', $string );

        return $string;
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
        $home_url = parse_url( $home_url );

        // Decide on target
        if( !isset ($link_url['host'] ) || $link_url['host'] == $home_url['host'] ) {
            // Is an internal link
            return false;

        } else {
            // Is an external link
            return true;
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

        // machine translate new strings
        if ( $machine_translation_available ) {
            $machine_strings                                                      = $this->machine_translator->translate( $machine_translatable_strings, $language_code, $this->settings['default-language'] );
            $unique_original_strings_with_machine_translations                    = array_keys( $machine_strings );
        }

        /**
         * If the option is activated,we use the below code so the variable $original_to_be_synced contains the array of strings that are to be sent to a machine translation
         * engine together with the similar one, so they have entries into the table the trp_original_strings
         */
        $originals_to_be_synced= array_merge( $unique_original_strings_with_machine_translations, $originals_without_translation_in_db_that_are_similar_with_already_translated_strings );

        $original_inserts = $this->trp_query->original_strings_sync( $language_code, $originals_to_be_synced );

        if ( $machine_translation_available ) {
            // insert unique machine translations into db. Only for strings newly discovered
            foreach ( $unique_original_strings_with_machine_translations as $string ) {
                $id = ( isset( $untranslated_list[ $string ] ) ) ? $untranslated_list[ $string ]->id : NULL;
                array_push( $update_strings, array(
                    'id'          => $id,
                    'original_id' => $original_inserts[ $string ]->id,
                    'original'    => trp_sanitize_string( $string, false ),
                    'translated'  => trp_sanitize_string( $machine_strings[ $string ] ),
                    'status'      => $this->trp_query->get_constant_machine_translated() ) );
            }
        }else{
            $machine_strings = false;
        }

        // update existing strings without translation if we have one now. also, do not insert duplicates for existing untranslated strings in db
        foreach( $new_strings as $i => $string ){

            if ( !isset($translated_strings[$i]) && isset( $machine_strings[$string] ) ) {
                $translated_strings[$i] = $machine_strings[$string];
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

            if ( isset( $untranslated_list[ $string ] ) || isset( $machine_strings[ $string ] ) ) {
                unset( $new_strings[ $i ] );
            }
        }


        $this->trp_query->insert_strings( $new_strings, $language_code, $block_type );
        $this->trp_query->update_strings( $update_strings, $language_code, array( 'id','original', 'translated', 'status', 'original_id' ) );

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
    public function wp_mail_filter( $args ){
        if (!is_array($args)){
            return $args;
        }

        if(array_key_exists('subject', $args)){
            $args['subject'] = $this->translate_page( do_shortcode( $args['subject'] ) );
        }

        if(array_key_exists('message', $args)){
            $args['message'] = $this->translate_page( do_shortcode( $args['message'] ) );
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

            $location = $this->url_converter->get_url_for_language( $form_language, $location );
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
        if( ( !$wp_query->in_the_loop || !is_main_query() ) && apply_filters('trp_wrap_with_post_id_overrule', true ) )
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
}
