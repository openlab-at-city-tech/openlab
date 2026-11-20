<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct access.' );
}

/**
 * Local Video Slide. Builds a slide from one or more video files in the WordPress
 * media library, played through the bundled video.js player.
 *
 * Moved here from MetaSlider Slideshow Pro, which still extends this class for its
 * own External Video slide type and reuses MetaVideoHelper for Layer slides.
 *
 * @since 3.113.0
 */
class MetaLocalVideoSlide extends MetaSlide
{

    public $identifier = 'local_video'; // should be lowercase, one word (use underscores if needed)
    public $name = 'Local Video';
    public $slug = 'local-video';
    private $slide_settings;
    private $videoHelper;

    /**
     * Whether an instance has already registered this slide type's hooks. MetaSlider
     * Slideshow Pro 2.60.0 and older create their own instance of this class as well,
     * which would otherwise register every hook, script and media tab a second time.
     *
     * @var bool
     */
    private static $hooks_registered = false;


    /**
     * Register slide type
     */
    public function __construct()
    {
        $this->videoHelper = new MetaVideoHelper();

        add_action('init', function() {
            $this->name = __( 'Local Video', 'ml-slider' );
        });

        // Per-instance state is set up above; everything below is registered once globally
        if ( self::$hooks_registered ) {
            return;
        }
        self::$hooks_registered = true;

        if ( is_admin() ) {
            add_action( "wp_ajax_create_{$this->identifier}_slide", array( $this, 'ajax_create_slide' ) );
            add_action( "wp_ajax_duplicate_{$this->identifier}_slide", array( $this, 'ajax_duplicate_slide' ) );
            add_filter( 'media_view_strings', array( $this, 'custom_media_uploader_tabs' ), 10, 1 );
            add_action( 'metaslider_register_admin_scripts', array( $this, 'register_admin_scripts' ), 10, 1 );
            add_action( 'metaslider_register_admin_styles', array( $this, 'register_admin_styles' ), 10, 1 );
        }

        // Also serve MetaSlider Slideshow Pro's Layer slides, which reuse these video source endpoints
        add_action( 'wp_ajax_update_slide_video', array( $this, 'ajax_update_slide_video' ) );
        add_action( 'wp_ajax_add_video_source', array( $this, 'ajax_add_video_source' ) );
        add_action( 'wp_ajax_remove_video_source', array( $this, 'ajax_remove_video_source' ) );

        add_action( 'wp_ajax_update_slide_track', array( $this, 'ajax_update_slide_track' ) );
        add_action( 'wp_ajax_remove_slide_track', array( $this, 'ajax_remove_slide_track' ) );
        add_action( "metaslider_save_{$this->identifier}_slide", array( $this, 'save_slide' ), 5, 3 );
        add_filter( "metaslider_get_{$this->identifier}_slide", array( $this, 'get_slide' ), 10, 2 );

        add_action( 'wp_ajax_remove_cover', array( $this, 'ajax_remove_cover' ) );
    }

    /**
     * Creates a new media manager tab
     *
     * @param array $strings registered media manager tabs
     *
     * @return array
     */
    public function custom_media_uploader_tabs( $strings )
    {
        $strings['insertLocalVideo'] = __( 'Local Video', 'ml-slider' );
        return $strings;
    }

    /**
     * Registers and enqueues admin JavaScript
     */
    public function register_admin_scripts()
    {
        // Don't load in Quickstart page
        if ( isset( $_REQUEST['page'] ) && 'metaslider-start' != $_REQUEST['page'] ) {
            wp_enqueue_script(
                "metaslider-{$this->slug}-script",
                plugins_url( 'assets/local_video' . metaslider_asset_suffix() . '.js', __FILE__ ),
                array( 'metaslider-admin-script' ),
                METASLIDER_VERSION,
                true
            );

            // Nonce loaded through metaslider-local-video-script localized script
            wp_localize_script( 
                "metaslider-{$this->slug}-script", 
                "metaslider_{$this->identifier}", 
                array( 
                    'nonce' => wp_create_nonce( "metaslider_create_{$this->identifier}_nonce" ),
                    'update_slide_nonce'    => wp_create_nonce( "metaslider_update_{$this->identifier}_nonce" ),
                    'duplicate_slide_nonce'    => wp_create_nonce( "metaslider_duplicate_{$this->identifier}_nonce" ),
                    'update_video_text'     => esc_html__( 'Select replacement video', 'ml-slider' ),
                    'update_image_text'     => esc_html__( 'Select replacement cover', 'ml-slider' ),
                    'update_text_text'      => esc_html__( 'Select replacement text track', 'ml-slider' ),
                    'add_to_slideshow'      => esc_html__( 'Add to slideshow', 'ml-slider' ),
                    // Pro sets up a richer caption editor for this slide type, so ours stands down
                    'caption_editor'        => metaslider_pro_is_active() ? 0 : 1
                )
            );
        }
    }

    /**
     * Registers and enqueues admin Styles
     */
    public function register_admin_styles()
    {
        // Don't load in Quickstart page
        if ( isset( $_REQUEST['page'] ) && 'metaslider-start' != $_REQUEST['page'] ) {
            wp_enqueue_style(
                "metaslider-local-video-style",
                plugins_url( 'assets/style' . metaslider_asset_suffix() . '.css', __FILE__ ),
                false,
                METASLIDER_VERSION
            );

            $css = ".{$this->identifier}_slide video {
                object-fit: cover;
                height: 100%;
                width: 100%;
            }
            .{$this->identifier}_slide input.video_url {
                width: 100%;
                min-width: 200px;
            }
            .{$this->identifier}_slide input.video_url[readonly] {
                color: #50575e;
            }";
            wp_add_inline_style( 'metaslider-admin-styles', $css );
        }
    }

    /**
     * Extract the slide setings
     *
     * @param integer $id Slide ID
     */
    public function set_slide( $id )
    {
        parent::set_slide( $id );
        $this->slide_settings = get_post_meta( $id, 'ml-slider_settings', true );
    }

    /**
     * Create a new slide
     * @TODO - Do we really need $fields despite that child class MetaExternalVideoSlide::create_slide() requires it?
     * 
     * @param integer $slider_id Slider ID
     * 
     * @return int ID of the created slide
     */
    public function create_slide($slider_id, $fields, $video_id = false)
    {
        // Set the slideshow (that this slide belongs to)
        $this->set_slider( $slider_id );

        // Create a new post for a slide
        $new_slide_id = $this->insert_slide($video_id, 'local_video', $slider_id);

        // Save video id postmeta
        if(wp_attachment_is('video', $video_id)) {
            update_post_meta($new_slide_id, 'ml-slider_video_id', $video_id);
        }

        // Set the slide
        $this->set_slide($new_slide_id);

        // Tag the slide attachment to the slider tax category
        $this->tag_slide_to_slider();

        return $new_slide_id;
    }

    /**
     * Create a new local video slide.
     *
     * @since 3.114.0 Only creates slides in a slideshow the user can edit
     */
    public function ajax_create_slide()
    {
        if ( ! isset($_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), "metaslider_create_{$this->identifier}_nonce" ) ) {
            wp_send_json_error( esc_html__( 'Invalid nonce', 'ml-slider' ), 403 );
        }

        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
        if (! current_user_can($capability)) {
            wp_send_json_error(
                ['message' => __('Access denied', 'ml-slider')],
                403
            );
        }

        if ( ! isset( $_POST['slider_id'] ) || ! isset( $_POST['video_id'] ) ) {
            wp_send_json_error( esc_html__( 'Bad request', 'ml-slider' ), 400 );
        }

        $video_id   = intval( $_POST['video_id'] );
        $slider_id  = intval( $_POST['slider_id'] );
        metaslider_verify_slideshow_for_ajax( $slider_id );

        // Only allow mp4, webm and mov videos
        if( ! $this->video_type_is_allowed( $video_id ) ) {
            wp_send_json_error( 
                esc_html__( 'Not supported video format', 'ml-slider' ),
                422
            );
        }

        $fields = array(); // Placeholder due is required
        $new_slide_id = $this->create_slide($slider_id, $fields, $video_id);

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        $html = $this->get_admin_slide();

        $result = array(
            'slide_id' => $new_slide_id,
            'html' => $html
        );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array(
                'message' => $result->get_error_message()
            ), 409 );
        }
        
        wp_send_json_success ( $result, 200 );
    }

    /**
     * Check if video type is allowed (mp4, webm and mov)
     * 
     * @param integer $video_id Video ID from Media library
     * 
     * @return boolean
     */
    protected function video_type_is_allowed( $video_id )
    {
        $allowed = array(
            'video/quicktime',
            'video/mp4',
            'video/webm'
        );

        if( ! in_array( get_post_mime_type( $video_id ), $allowed ) ) {
            return false;
        }

        return true;
    }

    /**
     * Check if video type is allowed (mp4, webm and mov)
     * 
     * @deprecated since 2.35 - Use MetaVideoHelper->get_video_format() instead
     * 
     * @param integer $type Video mime type. e.g. 'video/mp4'
     * 
     * @return string|boolean The file format. e.g. 'mp4'
     */
    public function get_video_format( $type )
    {
        $allowed = array(
            'video/quicktime',
            'video/mp4',
            'video/webm'
        );

        if ( ! in_array( $type, $allowed ) ) {
            return false;
        }

        $format = explode ( '/', $type );

        // Let's make it clear is a mov video
        if ( $format[1] === 'quicktime' ) {
            return 'mov';
        }

        return $format[1];
    }

    /**
     * Check if text track is valid (txt and vtt)
     * 
     * @param integer $track_id Video ID from Media library
     * 
     * @return boolean
     */
    protected function track_is_allowed( $track_id )
    {
        $allowed = array(
            'text/plain',
            'text/vtt'
        );

        if ( ! in_array( get_post_mime_type( $track_id ), $allowed ) ) {
            return false;
        }

        return true;
    }

    /**
     * Return the admin slide HTML
     *
     * @return string|bool html
     */
    public function get_admin_slide()
    {
        // @since 2.46
        if ( ! is_admin() && ! defined( 'REST_REQUEST' ) && ! defined( 'DOING_AJAX' ) ) {
            return false;
        }
        
        $video_id = $this->get_video_id();

        ob_start();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->get_delete_button_html();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        if( method_exists( $this, 'get_duplicate_slide_button_html' ) ) {
            echo $this->get_duplicate_slide_button_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        if( method_exists( $this, 'get_hide_slide_button_html' ) ) {
            echo $this->get_hide_slide_button_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        do_action('metaslider-slide-edit-buttons', $this->identifier, $this->slide->ID);
        $edit_buttons = ob_get_clean();

        $row = "<tr id='slide-" . esc_attr( $this->slide->ID ) . "' class='slide {$this->identifier}_slide flex' data-slide-type='" . esc_attr($this->identifier) . "'>";
        $row .= "    <td class='col-1'>";
        $row .= "       <div class='metaslider-ui-controls ui-sortable-handle'>";
        $row .= "           <h4 class='slide-details'>";
        $row .= apply_filters( 'metaslider_slide_details', '', $this->slide->ID ); // @since 2.45
        $row .= esc_html__( 'Local Video Slide', 'ml-slider' ) . " | ID: ". esc_html( $this->slide->ID );
        $row .= "           </h4>";
        if ( metaslider_this_is_trash( $this->slide ) ) {
            $row .= '<div class="row-actions trash-btns">';
            $row .= "<span class='untrash'>{$this->get_undelete_button_html()}</span>";
            if( method_exists( $this, 'get_permanent_delete_button_html' ) ) {
                $row .= ' | ';
                $row .= "<span class='delete'>{$this->get_permanent_delete_button_html()}</span>";
            }
            $row .= '</div>';
        } else {
            $row .= $edit_buttons;
        }
        $row .= "       </div>";
        $row .= "    </td>";
        $row .= "    <td class='col-2'>";
        $row .= "       <div class='metaslider-ui-inner flex flex-col h-full'>";

        if ( method_exists( $this, 'get_admin_slide_tabs_html' ) ) {
            $row .= $this->get_admin_slide_tabs_html();
        } else {
            $row .= "<p>" . esc_html__( 'Please update to MetaSlider Slideshow to version 3.2 or above.', 'ml-slider' ) . "</p>";
        }

        $row .= "        <input type='hidden' name='attachment[" . esc_attr( $this->slide->ID ) . "][type]' value='local_video' />";
        $row .= "        <input type='hidden' name='attachment[" . esc_attr( $this->slide->ID ) . "][menu_order]' class='menu_order' value='" . esc_attr( $this->slide->menu_order ) . "' />";
        $row .= "        <input type='hidden' name='resize_slide_id' data-slide_id='" . esc_attr( $this->slide->ID ) . "' data-width='" . esc_attr( $this->settings['width'] ) . "' data-height='" . esc_attr( $this->settings['height'] ) . "' />";
        $row .= "       </div>";
        $row .= "    </td>";
        $row .= "</tr>";

        return $row;
    }

    /**
     * Build an array of tabs and their titles to use for the admin slide.
     */
    public function get_admin_tabs()
    {
        $path = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'tabs/';

        ob_start();
        include $path . 'general.php';
        $general_tab = ob_get_clean();

        ob_start();
        include $path . 'link.php';
        $link_tab = ob_get_clean();

        $cover_tab = $this->cover_tab_content();
        $track_tab = $this->track_tab_content();

        ob_start();
        include $path . 'caption.php';
        $caption_tab = ob_get_clean();

        $tabs = array(
            'general' => array(
                'title' => esc_html__( 'General', 'ml-slider' ),
                'content' => $general_tab
            ),
            'link' => array(
                'title' => esc_html__( 'Link', 'ml-slider' ),
                'content' => $link_tab
            ),
            'cover' => array(
                'title' => esc_html__( 'Cover', 'ml-slider' ),
                'content' => $cover_tab
            ),
            'track' => array(
                'title' => esc_html__( 'Text track', 'ml-slider' ),
                'content' => $track_tab
            ),
            'caption' => array(
                'title' => esc_html__( 'Caption', 'ml-slider' ),
                'content' => $caption_tab
            )
        );

        $global_settings = $this->get_global_settings();
        if (
            !isset($global_settings['mobileSettings']) ||
            (isset($global_settings['mobileSettings']) && true == $global_settings['mobileSettings'])
        ) {
            ob_start();
            include $path . 'mobile.php';
            $mobile_tab = ob_get_clean();

            $tabs['mobile'] = array(
                'title' => __("Device", "ml-slider"),
                'content' => $mobile_tab
            );
        }

        // Pro excludes local_video from its Custom thumbnail feature
        $tabs = $this->add_pro_upsell_tabs($tabs, array('thumbnail'));

        return apply_filters( 
            "metaslider_{$this->identifier}_slide_tabs", 
            $tabs, 
            $this->slide, 
            $this->slider, 
            $this->settings 
        );
    }

    /**
     * Get track tab content
     * 
     * @return html
     */
    public function track_tab_content()
    {
        // Track tab
        $track          = get_post_meta( $this->slide->ID, 'ml-slider_track', true );

        $track_label    = isset( $track['label'] ) ? $track['label'] : '';
        $track_kind     = isset( $track['kind'] ) ? $track['kind'] : 'captions';
        $track_lang     = isset( $track['lang'] ) ? $track['lang'] : '';

        // TXT or VTT url. e.g. 'http://lorem.any/path/to/file.vtt'
        $html   = $this->track_url_field();

        $html  .= '<div class="row">';
        $html  .= '     <div class="flex gap-4 mt-2">';

        // Language label. e.g. 'English'
        $html  .= '         <div style="width:145px">';
        $html  .= '             <div class="row mb-2">';
        $html  .= '                 <label class="tipsy-tooltip-top" title="' . 
                                        esc_attr__( 'Which language is the source file?', 'ml-slider' ) . '">' .
                                        __( 'Language label', 'ml-slider' ) . '</label>';
        $html  .= '             </div>';
        $html  .= '             <input type="text" name="attachment[' . 
                                        esc_attr( $this->slide->ID ) . '][track][label]"';
        $html  .=               ' aria-label="' . esc_attr__( 'Language label', 'ml-slider' ) . '"';
        $html  .=               ' placeholder="' . 
                                        esc_attr__( 'For example: English', 'ml-slider' ) . '"';
        $html  .=               ' value="' . esc_attr( $track_label ) . '">';
        $html  .= '         </div>';

        // Language kind
        $html  .= '         <div style="width:180px;">';
        $html  .= '             <div class="row mb-2">';
        $html  .= '                 <label class="tipsy-tooltip-top" title="' . 
                                        esc_attr__( 'Choose the type of text track', 'ml-slider' ) . '">' .
                                        __( 'Language kind', 'ml-slider' ) . '</label>';
        $html  .= '             </div>';
        $html  .= '             <select name="attachment[' . 
                                    esc_attr( $this->slide->ID ) . '][track][kind]" aria-label="' .
                                    esc_attr__( 'Language kind', 'ml-slider' ) . '">';
        $html  .= '                 <option value="captions"' . 
                                        ( $track_kind === 'captions' ? ' selected' : '' ) .'>' . 
                                        esc_html__( 'Captions', 'ml-slider' ) . '</option>';
        $html  .= '                 <option value="chapters"' . 
                                        ( $track_kind === 'chapters' ? ' selected' : '' ) .'>' . 
                                        esc_html__( 'Chapters', 'ml-slider' ) . '</option>';
        $html  .= '                 <option value="descriptions"' . 
                                        ( $track_kind === 'descriptions' ? ' selected' : '' ) .'>' . 
                                        esc_html__( 'Descriptions', 'ml-slider' ) . '</option>';
        /*$html  .= '             <option value="metadata"' . 
                                    ( $track_kind === 'metadata' ? ' selected' : '' ) .'>' . 
                                    esc_html__( 'Metadata', 'ml-slider' ) . '</option>';*/
        $html  .= '                 <option value="subtitles"' . 
                                        ( $track_kind === 'subtitles' ? ' selected' : '' ) .'>' . 
                                        esc_html__( 'Subtitles', 'ml-slider' ) . '</option>';
        $html  .= '             </select>';
        $html  .= '         </div>';

        // Language code. e.g. 'en'
        $html  .= '         <div style="width:120px;">';
        $html  .= '             <div class="row mb-2">';
        $html  .= '                 <label class="tipsy-tooltip-top" title="' . 
                                        esc_attr__( 'If the language is English, the code must be en', 'ml-slider' ) . '">' .
                                        __( 'Language code', 'ml-slider' )  . '</label>';
        $html  .= '             </div>';
        $html  .= '             <input style="width:120px;" type="text" name="attachment[' . 
                                        esc_attr( $this->slide->ID ) . '][track][lang]"';
        $html  .=               ' aria-label="' . esc_attr__( 'Language code', 'ml-slider' ) . '"';
        $html  .=               ' placeholder="' . 
                                        esc_attr__( 'For example: en', 'ml-slider' ) . '"';
        $html  .=               ' value="' . esc_attr( $track_lang ) . '" maxlength="2">';
        $html  .= '         </div>';

        $html  .= '     </div>';
        $html  .= '</div>';
        $html  .= '<div class="row mt-2 mb-0">';
        $html  .= ' <a class="button-link no-underline" 
                        href="https://www.metaslider.com/docs/video-captions-for-local-videos/" 
                        target="_blank">';
        $html  .=       esc_html__( 'Documentation', 'ml-slider' );
        $html  .=       ' <span class="dashicons dashicons-external"></span>';
        $html  .= ' </a>';
        $html  .= '</div>';

        return $html;
    }

    /**
     * Get cover tab content
     * 
     * @return html
     */
    public function cover_tab_content()
    {
        $cover_id   = $this->get_attachment_id();
        $thumb      = $this->get_intermediate_image_src( 240 );

        $html  = '<div class="local_video-cover relative">';
        $html .= $this->videoHelper->get_admin_cover_embed( 
                $this->slide->ID, 
                $cover_id, 
                $thumb,
                $this->identifier
            );
        $html .= '</div>';

        return $html;
    }

    /**
     * Get track URL field
     * 
     * @return html
     */
    protected function track_url_field()
    {
        $track      = get_post_meta( $this->slide->ID, 'ml-slider_track', true );
        $track_id   = isset( $track['id'] ) ? $track['id'] : '';
        $track_url  = $this->get_track( $track_id );

        $html   = '<div class="row mb-2">';
        $html  .= '    <label class="tipsy-tooltip-top" title="' . 
                        esc_attr__( 'File must be TXT or VTT format', 'ml-slider' ) . '">' .
                            __( 'Source', 'ml-slider' ) . '</label>';
        $html  .= '</div>';
        $html  .= '<div class="row">';
        $html  .= '     <div class="flex">';
        $html  .= '         <button data-button-text="' . 
                                esc_attr__( 'Update slide text track', 'ml-slider' ) . '" data-slide-id="' . 
                                esc_attr( $this->slide->ID ) . '" data-attachment-id="' . 
                                esc_attr( $track_id ) . '" class="update-text-track button button-secondary flex-1">';
        $html  .=               esc_html__( 'Browse', 'ml-slider' );
        $html  .= '         </button>';
        $html  .= '         <input class="track_url w-100 m-0 border-r-0 border-l-0" type="text" aria-label="' .
                                esc_attr__( 'Source', 'ml-slider' ) . '" value="' .
                                esc_url( $track_url ) . '" readonly>';
        $html  .= '         <button data-slide-id="' . 
                                esc_attr( $this->slide->ID ) . '" data-attachment-id="' . 
                                esc_attr( $track_id ) . '" class="remove-text-track button button-secondary ms-button-danger">';
        $html  .=               '<i><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></i>';
        $html  .= '         </button>';
        $html  .= '     </div>';
        $html  .= '</div>';

        return $html;
    }

    /**
     * Check if a URL ends with .vtt or .txt
     * 
     * @param string $url e.g. 'http://lorem.any/path/to/file.vtt'
     * 
     * @return bool
     */
    public function is_track_file( $url )
    {
        if ( ! empty( $url ) && filter_var( $url, FILTER_VALIDATE_URL ) ) {
            return (bool) preg_match('/\.(vtt|txt)$/i', $url);
        }
        return false;
    }

    /**
     * Save track data
     * 
     * @param array $fields Array of all the slide settings including regular settings and track data
     * 
     * @return array
     */
    public function save_track( $fields )
    {
        // Sanitize data
        foreach ( array( 'lang', 'label', 'kind' ) as $item ) {
            $fields['track'][$item] = sanitize_text_field( $fields['track'][$item] );
        }

        // We don't store the url for Local videos, but we do for External videos
        if ( isset( $fields['track']['url'] ) ) {
            $fields['track']['url'] = sanitize_url( $fields['track']['url'] );
        }

        // Make sure track id is not removed if exists in database. We use in Local videos only
        // @TODO - Check if we really use 'id'
        $data = get_post_meta( $this->slide->ID, 'ml-slider_track', true );
        if ( $data && isset( $data['id'] ) ) {
            $fields['track']['id'] = $data['id'];
        }

        $this->add_or_update_or_delete_meta( $this->slide->ID, 'track', $fields['track'] );
    }

    /**
     * Add track attributes to $attributes array
     * 
     * @param array $attributes The existing attributes including data-id, data-loop, data-autoplay, etc.
     * 
     * @return array
     */
    public function track_attributes( $attributes )
    {
        $track = get_post_meta( $this->slide->ID, 'ml-slider_track', true );

        // If slide doesn't have a track id, we stop the process here
        if ( ! isset( $track['id'] ) ) {
            return $attributes;
        }

        $track_url = $this->get_track( $track['id'] );

        $attributes['data-track-url']           = $track_url;
        $attributes['data-track-lang']          = ! empty( $track['lang'] ) 
                                                ? $track['lang']  : 'en';
        $attributes['data-track-label']         = ! empty( $track['label'] ) 
                                                ? $track['label'] : esc_attr__( 'English' );
        $attributes['data-track-kind']          = ! empty( $track['kind'] ) 
                                                ? $track['kind'] : esc_attr__( 'captions' );

        return $attributes;
    }

    /**
     * Add Link URL attributes to $attributes array
     * 
     * @param array $attributes The existing attributes including data-id, data-loop, data-autoplay, etc.
     * 
     * @return array
     */
    public function link_url_attributes( $attributes )
    {
        $url = get_post_meta( $this->slide->ID, 'ml-slider_url', true );

        // If slide doesn't have a Link URL, we stop the process here
        if ( ! $url ) {
            return $attributes;
        }

        $attributes['data-link-url']    = esc_url( $url );
        // Name the link after its host and path so links to pages on the same site stay distinguishable.
        $link_url   = esc_url_raw( $url );
        $link_parts = wp_parse_url( $link_url );
        $link_name  = ! empty( $link_parts['host'] )
            ? $link_parts['host'] . untrailingslashit( isset( $link_parts['path'] ) ? $link_parts['path'] : '' )
            : $link_url;
        $attributes['data-link-label']  = sprintf(
            /* translators: %s: the web address the slide links to, without https:// */
            __( 'Open link: %s', 'ml-slider' ),
            $link_name
        );
        $attributes['data-link-target'] = get_post_meta( $this->slide->ID, 'ml-slider_new_window', true ) ? '_blank' : '_self';

        // Preview is a srcdoc iframe - force new tab (also covers external_video).
        if ( isset( $_REQUEST['action'] ) && 'ms_get_preview' == $_REQUEST['action'] ) {
            $attributes['data-link-target'] = '_blank';
        }

        return $attributes;
    }

    /**
     * Save
     *
     * @param array $fields Slide fields
     */
    protected function save( $fields )
    {
        // Update the order
        wp_update_post(array(
            'ID' => $this->slide->ID,
            'menu_order' => $fields['menu_order']
        ));

        // Link URL settings
        $this->add_or_update_or_delete_meta( 
            $this->slide->ID, 
            'new_window', 
            isset( $fields['new_window'] ) && $fields['new_window'] === 'on' 
        );
        $this->add_or_update_or_delete_meta( $this->slide->ID, 'url', sanitize_url( $fields['url'] ) );

        // @since 2.40 - Sanitize HTML and save caption
        if (method_exists($this, 'cleanup_content_kses')) {
            $fields['caption'] = $this->cleanup_content_kses($fields['caption']);
        }
        $this->add_or_update_or_delete_meta($this->slide->ID, 'caption', $fields['caption']);

        // Save track data
        $this->save_track( $fields );

        // Set defaults for non existing fields
        foreach ( array( 'mute', 'controls', 'autoPlay', 'lazyLoad', 'loop', 'fit', 'fade' ) as $setting ) {
            if ( ! isset($fields['settings'][$setting] ) ) {
                $fields['settings'][$setting] = 'off';
            }
        }

        // Save all the settings fields serialized
        if ( isset( $fields['settings'] ) ) {
            $this->add_or_update_or_delete_meta( $this->slide->ID, 'settings', $fields['settings'] );
        }

        $this->add_or_update_or_delete_meta(
            $this->slide->ID,
            'hide_slide_smartphone',
            isset($fields['hide_slide_smartphone']) && $fields['hide_slide_smartphone'] === 'on'
        );

        $this->add_or_update_or_delete_meta(
            $this->slide->ID,
            'hide_slide_tablet',
            isset($fields['hide_slide_tablet']) && $fields['hide_slide_tablet'] === 'on'
        );

        $this->add_or_update_or_delete_meta(
            $this->slide->ID,
            'hide_slide_laptop',
            isset($fields['hide_slide_laptop']) && $fields['hide_slide_laptop'] === 'on'
        );

        $this->add_or_update_or_delete_meta(
            $this->slide->ID,
            'hide_slide_desktop',
            isset($fields['hide_slide_desktop']) && $fields['hide_slide_desktop'] === 'on'
        );

        // Is slide hidden?
        update_post_meta(
            $this->slide->ID,
            '_meta_slider_slide_is_hidden',
            isset( $fields['hide_slide'] ) && $fields['hide_slide'] === 'on'
        );
    }

    /**
     * Get the video for the slide
     * 
     * @deprecated since 2.35 - Use MetaVideoHelper->get_video() instead
     */
    protected function get_video( $id = false )
    {
        $video_id = $id ? $id : $this->get_video_id();

        if( $video_id && wp_attachment_is( 'video', $video_id ) ) {
            $video_url = wp_get_attachment_url( $video_id );
        }

        if ( isset( $video_url ) ) {
            return $video_url;
        }

        return '';
    }

    /**
     * Get the post id from a video
     * 
     * @deprecated since 2.35 - Use MetaVideoHelper->get_video_id() instead
     */
    protected function get_video_id()
    {
        $video_id = get_post_meta( $this->slide->ID, 'ml-slider_video_id', true );

        if ( isset( $video_id ) ) {
            return absint( $video_id );
        }

        return false;
    }

    /**
     * Get all the videos from a single slide 
     * aka. video slide have more than one source. e.g. mp4, mov, etc.
     * 
     * @deprecated since 2.35 - Use MetaVideoHelper->get_all_sources() instead
     * 
     * @param int $id Slide id
     * 
     * @return array
     */
    protected function get_all_sources( $slide_id = false )
    {
        if ( ! $slide_id ) {
            $slide_id = $this->slide->ID;
        }

        $data       = array();
        $video_ids  = get_post_meta( $slide_id, 'ml-slider_video_id' );

        if ( $video_ids !== false && is_array( $video_ids ) ) {
            
            foreach ( $video_ids as $id ) {
                $id     = absint( $id );
                $type   = get_post_mime_type( $id );
                $data[] = array(
                    'id' => $id,
                    'type' => $type,
                    'format' => $this->get_video_format( $type ),
                    'url' => $this->get_video( $id )
                );
            }
        }

        return $data;
    }

    /**
     * Get the text track for the slide
     * 
     * @param int $track_id The id of the text track file
     * 
     * @return string
     */
    protected function get_track( $track_id )
    {
        if ( $this->track_is_allowed( $track_id ) ) {
            $track_url = wp_get_attachment_url( $track_id );
        }

        if ( isset( $track_url ) ) {
            return $track_url;
        }

        return '';
    }

    /**
     * Ajax wrapper to update the slide video.
     * This works for Local video and Layer slides
     * 
     * @since 3.114.0 Only changes a slide and slideshow the user can edit
     *
     * @return String The status message and if success, the thumbnail link (JSON)
     */
    public function ajax_update_slide_video()
    {
        if ( ! isset( $_REQUEST['_wpnonce'] ) 
            || ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), "metaslider_update_{$this->identifier}_nonce") ) {
            wp_send_json_error( array(
                'message' => __( 'The security check failed. Please refresh the page and try again.', 'ml-slider' )
            ), 401 );
        }

        $capability = apply_filters( 'metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES );
        if ( ! current_user_can( $capability ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Access denied', 'ml-slider' )
                ],
                403
            );
        }

        if ( ! isset( $_POST['slide_id'] ) || ! isset($_POST['video_id']) || ! isset($_POST['prev_video_id']) || ! isset( $_POST['slider_id'] ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Bad request', 'ml-slider' ),
                ],
                400
            );
        }

        // Only allow mp4, webm and mov videos
        if( ! $this->video_type_is_allowed( absint( $_POST['video_id'] ) ) ) {
            wp_send_json_error( 
                [
                    'message' => esc_html__( 'Not supported video format', 'ml-slider' )
                ],
                422
            );
        }

        $slide_id  = absint( $_POST['slide_id'] );
        $slider_id = absint( $_POST['slider_id'] );
        metaslider_verify_slide_for_ajax( $slide_id );
        metaslider_verify_slideshow_for_ajax( $slider_id );

        $result = $this->update_slide_video(
            $slide_id,
            absint( $_POST['video_id'] ),
            $slider_id,
            absint( $_POST['prev_video_id'] )
        );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array(
                'message' => $result->get_error_message()
            ), 409 );
        }
        wp_send_json_success ( $result, 200 );
    }

    /**
     * Ajax wrapper to remove cover
     * 
     * @since 3.114.0 Only changes a slide the user can edit
     *
     * @return String
     */
    public function ajax_remove_cover()
    {
        if ( ! isset( $_REQUEST['_wpnonce'] ) 
            || ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), "metaslider_update_{$this->identifier}_nonce") ) {
            wp_send_json_error( array(
                'message' => __( 'The security check failed. Please refresh the page and try again.', 'ml-slider' )
            ), 401 );
        }

        $capability = apply_filters( 'metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES );
        if ( ! current_user_can( $capability ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Access denied', 'ml-slider' )
                ],
                403
            );
        }

        if ( ! isset( $_POST['slide_id'] ) || ! isset( $_POST['slide_type'] ) ) {
            wp_send_json_error(
                array(
                    'message' => __( 'Bad request', 'ml-slider' ),
                ),
                400
            );
        }
        
        $slide_id = absint( $_POST['slide_id'] );
        metaslider_verify_slide_for_ajax( $slide_id );
        $slide_type = sanitize_text_field( $_POST['slide_type'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- sanitize_text_field() strips the value regardless of slashes; no risk from the missing wp_unslash() here.
        delete_post_meta( $slide_id, '_thumbnail_id' );

        $result = array(
            'message'       => __( 'The cover was successfully removed.', 'ml-slider' ),
            'html_embed'    => $this->videoHelper->get_admin_cover_embed( $slide_id, 0, METASLIDER_ASSETS_URL . 'metaslider/placeholder-thumb.jpg', $slide_type )
        );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array(
                'message' => $result->get_error_message()
            ), 409 );
        }
        wp_send_json_success ( $result, 200 );
    }

    /**
     * Updates the slide meta value to a new video.
     *
     * @param int $slide_id         The id of the slide being updated
     * @param int $video_id         The id of the new video to use
     * @param int $slideshow_id     The id of the slideshow
     * @param int $prev_video_id    The previously saved video id
     *
     * @return array|WP_error The status message and if success, the thumbnail link
     */
    protected function update_slide_video( $slide_id, $video_id, $slideshow_id = null, $prev_video_id = false )
    {   
        /*
         * Verifies that the $video_id is an actual video
         */
        if ( ! wp_attachment_is( 'video', $video_id ) ) {
            return new WP_Error( 
                'update_failed', 
                __( 'The requested video does not exist. Please try again.', 'ml-slider' ), 
                array( 'status' => 409 )
            );
        }

        
        if ( ! $prev_video_id ) {
            return new WP_Error( 
                'update_failed', 
                __( 'There was an error updating the video. No previous video saved.', 'ml-slider' ), 
                array( 'status' => 409 ) 
            ); 
        }

        /* Updates database record and thumbnail if selection changed, 
         * assigns it to the slideshow, crops the image */
        update_post_meta( $slide_id, 'ml-slider_video_id', $video_id, $prev_video_id );

        // We need all the sources to refresh the video embed
        $sources = $this->videoHelper->get_all_sources( $slide_id );

        if ( $slideshow_id ) {
            $this->set_slider( $slideshow_id );

            return array(
                'message' => __( 'The video was successfully updated.', 'ml-slider' ),
                'video_url' => $this->get_video( $video_id ),
                'video_type' => get_post_mime_type( $video_id ),
                'html_embed' => $this->get_admin_video_embed( $sources )
            );
        }

        return new WP_Error( 
            'update_failed', 
            __( 'There was an error updating the video. Please try again', 'ml-slider' ), 
            array( 'status' => 409 ) 
        );
    }
    
    /**
     * Ajax wrapper to update the slide video.
     * This works for Local video and Layer slides
     * 
     * @since 3.114.0 Only changes a slide the user can edit
     *
     * @return String The status message and if success, the thumbnail link (JSON)
     */
    public function ajax_add_video_source()
    {
        if ( ! isset( $_REQUEST['_wpnonce'] ) 
            || ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), "metaslider_update_{$this->identifier}_nonce") ) {
            wp_send_json_error( array(
                'message' => __( 'The security check failed. Please refresh the page and try again.', 'ml-slider' )
            ), 401 );
        }

        $capability = apply_filters( 'metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES );
        if ( ! current_user_can( $capability ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Access denied', 'ml-slider' )
                ],
                403
            );
        }

        if ( ! isset( $_POST['slide_id'] ) 
            || ! isset($_POST['video_id']) 
            || ! isset( $_POST['slider_id'] ) 
            || ! isset( $_POST['slide_type'] ) 
        ) {
            wp_send_json_error(
                [
                    'message' => __( 'Bad request', 'ml-slider' ),
                ],
                400
            );
        }

        $slide_id   = absint( $_POST['slide_id'] );
        metaslider_verify_slide_for_ajax( $slide_id );
        $sources    = $this->videoHelper->get_all_sources( $slide_id );
        $video_id   = absint( $_POST['video_id'] );
        $slide_type = sanitize_text_field( $_POST['slide_type'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- sanitize_text_field() strips the value regardless of slashes; no risk from the missing wp_unslash() here.
        $type       = get_post_mime_type( $video_id );

        $existing_types = array();
        foreach ( $sources as $item ) {
            $existing_types[] = $item['type'];
        }

        // Check how many video sources have this slide so we don't allow to add more than 3
        if ( count( $sources ) > 2 ) {
            wp_send_json_error( 
                array(
                    'message' => esc_html__( 
                        "You can't add more sources to this video slide.", 
                        'ml-slider' 
                    )
                ),
                400
            );
        }

        /* Check the selected video uses a format not available in this slide.
         * e.g. The slide have a video in mp4 and webm, so the user can only add a new video in mov */
        if ( in_array( $type, $existing_types ) ) {
            wp_send_json_error( 
                array(
                    'failed_type' => $type,
                    'existing_types' => $existing_types,
                    'message' => esc_html__( 
                        "This slide already has a video in that format.", 
                        'ml-slider' 
                    )
                ),
                400
            );
        }

        // Only allow mp4, webm and mov videos
        if( ! $this->video_type_is_allowed( absint( $_POST['video_id'] ) ) ) {
            wp_send_json_error( 
                [
                    'message' => esc_html__( 'Not supported video format', 'ml-slider' )
                ],
                422
            );
        }

        $video              = array();
        $video['id']        = $video_id;
        $video['type']      = $type;
        $video['format']    = $this->get_video_format( $type );
        $video['url']       = $this->get_video( $video_id );
        
        // Add new record to database
        add_post_meta( $slide_id, 'ml-slider_video_id', $video_id );

        // We need all the sources to refresh the video embed
        $sources = $this->videoHelper->get_all_sources( $slide_id );

        $result = array(
            'message'       => __( 'The video source was successfully added.', 'ml-slider' ),
            'video' => $video,
            'html_row'  => $this->videoHelper->admin_video_source_row( $video, $slide_id, $slide_type ),
            'html_embed' => $this->get_admin_video_embed( $sources ),
            'count_sources' => count( $sources )
        );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( 
                array(
                    'message' => $result->get_error_message()
                ), 
                409 
            );
        }

        return wp_send_json_success ( 
            $result,
            200 
        );
    }

    /**
     * Video source HTML for admin screen
     * 
     * @deprecated since 2.35 - Use MetaVideoHelper->admin_video_source_row() instead
     * 
     * @param array $video  Array with video data including post id, url, format
     * @param int $slide_id Optional slide ID
     * 
     * @return html
     */
    public function admin_video_source_row( $video, $slide_id )
    {
        $row = '<div class="row">
            <div class="flex">
                <button data-slide-type="local_video" data-button-text="' . 
                    esc_attr__( 'Update slide video', 'ml-slider' ) . '" data-slide-id="' . 
                    esc_attr( $slide_id ) . '" data-attachment-id="' . 
                    esc_attr( $video['id'] ) . '" data-format="' . 
                    esc_attr( $video['format'] ) . '" class="update-video button button-secondary flex-1">' . 
                    esc_html__( 'Browse', 'ml-slider' ) . '
                </button>
                <input class="url video_url border-l-0 border-r-0 m-0" type="text" data-attachment-id="' . 
                    esc_attr( $video['id'] ) . '" data-format="' . 
                    esc_attr( $video['format'] ) . '" placeholder="' . 
                    esc_attr__( 'Video File', 'ml-slider' ) . '" value="' . 
                    esc_attr( $video['url'] ) . '" readonly />
                <button data-slide-id="' . 
                    esc_attr( $slide_id ) . '" data-attachment-id="' . 
                    esc_attr( $video['id'] ) . '" class="remove-video-source button button-secondary ms-button-danger">
                    <i>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </i>
                </button>
            </div>
        </div>';

        return $row;
    }

    /**
     * Ajax wrapper to remove the video source.
     * This works for Local video and Layer slides
     * 
     * @since 3.114.0 Only changes a slide the user can edit
     *
     * @return String The status message and if success, the JSON response
     */
    public function ajax_remove_video_source()
    {
        if ( ! isset( $_REQUEST['_wpnonce'] ) 
            || ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), "metaslider_update_{$this->identifier}_nonce") ) {
            wp_send_json_error( array(
                'message' => __( 'The security check failed. Please refresh the page and try again.', 'ml-slider' )
            ), 401 );
        }

        $capability = apply_filters( 'metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES );
        if ( ! current_user_can( $capability ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Access denied', 'ml-slider' )
                ],
                403
            );
        }

        if ( ! isset( $_POST['slide_id'] ) 
            || ! isset( $_POST['video_id'] ) 
            || ! isset( $_POST['slide_type'] ) 
        ) {
            wp_send_json_error(
                [
                    'message' => __( 'Bad request', 'ml-slider' ),
                ],
                400
            );
        }

        $slide_id   = absint( $_POST['slide_id'] );
        metaslider_verify_slide_for_ajax( $slide_id );
        $slide_type = sanitize_text_field( $_POST['slide_type'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- sanitize_text_field() strips the value regardless of slashes; no risk from the missing wp_unslash() here.

        // Check how many video sources have this slide so we don't allow to delete if we have only one
        $sources = $this->videoHelper->get_all_sources( $slide_id );

        // Only allow to html_overlay slides to delete all the video sources
        if (count($sources) === 1) {
            wp_send_json_error( 
                [
                    'message' => esc_html__( 'This slide requires at least one video source.', 'ml-slider' )
                ],
                409
            );
        }
        
        // Delete database record
        delete_post_meta( $slide_id, 'ml-slider_video_id', absint( $_POST['video_id'] ) );

        // Get sources again to update preview embed
        $sources = $this->videoHelper->get_all_sources( $slide_id );

        $result = array(
            'message'   => __( 'The video source was successfully removed.', 'ml-slider' ),
            'slide_id'  => $slide_id,
            'count_sources' => count( $sources ),
            'html_embed' => $this->get_admin_video_embed( $sources )
        );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array(
                'message' => $result->get_error_message()
            ), 409 );
        }
        wp_send_json_success ( $result, 200 );
    }

    /**
     * Ajax wrapper to update the slide text track.
     *
     * @since 3.114.0 Only changes a slide and slideshow the user can edit
     *
     * @return String The status message and if success, the thumbnail link (JSON)
     */
    public function ajax_update_slide_track()
    {
        if ( ! isset( $_REQUEST['_wpnonce'] ) 
            || ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), "metaslider_update_{$this->identifier}_nonce") ) {
            wp_send_json_error( array(
                'message' => __( 'The security check failed. Please refresh the page and try again.', 'ml-slider' )
            ), 401 );
        }

        $capability = apply_filters( 'metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES );
        if ( ! current_user_can( $capability ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Access denied', 'ml-slider' )
                ],
                403
            );
        }

        if ( ! isset( $_POST['slide_id'] ) || ! isset($_POST['track_id']) || ! isset( $_POST['slider_id'] ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Bad request', 'ml-slider' ),
                ],
                400
            );
        }

        // Only allow mp4, webm and mov videos
        if( ! $this->track_is_allowed( absint( $_POST['track_id'] ) ) ) {
            wp_send_json_error( 
                [
                    'message' => esc_html__( 'Not supported text track format', 'ml-slider' )
                ],
                422
            );
        }

        $slide_id  = absint( $_POST['slide_id'] );
        $slider_id = absint( $_POST['slider_id'] );
        metaslider_verify_slide_for_ajax( $slide_id );
        metaslider_verify_slideshow_for_ajax( $slider_id );

        $result = $this->update_slide_track(
            $slide_id,
            absint( $_POST['track_id'] ),
            $slider_id
        );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array(
                'message' => $result->get_error_message()
            ), 409 );
        }
        wp_send_json_success ( $result, 200 );
    }

    /**
     * Updates the slide meta value to a new video.
     *
     * @param int $slide_id     The id of the slide being updated
     * @param int $track_id     The id of the new video to use
     * @param int $slideshow_id The id of the slideshow
     *
     * @return array|WP_error The status message and if success, the JSON response
     */
    protected function update_slide_track( $slide_id, $track_id, $slideshow_id = null )
    {   
        /*
         * Verifies that the $track_id is a valid file
         */
        $mime_type = get_post_mime_type( $track_id );
        $supported_mime_types = array(
            'text/plain',
            'text/vtt'
        );
        if ( ! in_array( $mime_type, $supported_mime_types) ) {
            return new WP_Error( 
                'update_failed', 
                __( 'The requested text track does not exist. Please try again.', 'ml-slider' ), 
                array( 'status' => 409 )
            );
        }

        /*
         * Updates database record
         */
        $data = get_post_meta( $slide_id, 'ml-slider_track', true );
        if ( ! $data ) {
            $data = array();
        }

        $data['id'] = $track_id;
        
        $this->add_or_update_or_delete_meta( $slide_id, 'track', $data );

        if ( $slideshow_id ) {
            $this->set_slider( $slideshow_id );

            return array(
                'message'       => __( 'The text track was successfully updated.', 'ml-slider' ),
                'track_id'      => $track_id,
                'track_url'     => $this->get_track( $track_id ),
                'track_type'    => $mime_type
            );
        }

        return new WP_Error( 
            'update_failed', 
            __( 'There was an error updating the text track. Please try again', 'ml-slider' ), 
            array( 'status' => 409 ) 
        );
    }

    /**
     * Ajax wrapper to remove the slide text track.
     *
     * @since 3.114.0 Only changes a slide the user can edit
     *
     * @return String The status message and if success, the JSON response
     */
    public function ajax_remove_slide_track()
    {
        if ( ! isset( $_REQUEST['_wpnonce'] ) 
            || ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), "metaslider_update_{$this->identifier}_nonce") ) {
            wp_send_json_error( array(
                'message' => __( 'The security check failed. Please refresh the page and try again.', 'ml-slider' )
            ), 401 );
        }

        $capability = apply_filters( 'metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES );
        if ( ! current_user_can( $capability ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Access denied', 'ml-slider' )
                ],
                403
            );
        }

        if ( ! isset( $_POST['slide_id'] ) || ! isset($_POST['track_id']) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Bad request', 'ml-slider' ),
                ],
                400
            );
        }

        $slide_id = absint( $_POST['slide_id'] );
        metaslider_verify_slide_for_ajax( $slide_id );
        
        // Updates database record
        $data = get_post_meta( $slide_id, 'ml-slider_track', true );
        unset( $data['id'] );
        
        $this->add_or_update_or_delete_meta( $slide_id, 'track', $data );

        $result = array(
            'message'   => __( 'The text track was successfully removed.', 'ml-slider' ),
            'slide_id'  => $slide_id
        );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array(
                'message' => $result->get_error_message()
            ), 409 );
        }
        wp_send_json_success ( $result, 200 );
    }

    /**
     * Public slide html
     *
     * @return string
     */
    protected function get_public_slide()
    {
        wp_enqueue_script(
            'metaslider-videojs-script',
            METASLIDER_BASE_URL . 'modules/local_video/assets/video.js/video.min.js',
            array(),
            METASLIDER_VERSION,
            true
        );

        wp_enqueue_style(
            'metaslider-videojs-default-style',
            METASLIDER_BASE_URL . 'modules/local_video/assets/video.js/video-js.min.css',
            array(),
            METASLIDER_VERSION
        );

        add_action( 'metaslider_register_public_styles', array( $this, 'add_extra_styles' ), 10, 2 );

        $settings   = get_post_meta( $this->slider->ID, 'ml-slider_settings', true );
        $video_id   = get_post_meta( $this->slide->ID, 'ml-slider_video_id', true );
        $video_url  = $this->get_video( $video_id );
         
        if (! (int)$this->settings['height'] || ! (int)$this->settings['width']) {
            $ratio = 9 / 16 * 100;
        } else {
            $ratio = $this->settings['height'] / $this->settings['width'] * 100;
        }

        // Flexslider
        if ( $this->settings['type'] == 'flex' ) {
            add_filter( 'metaslider_flex_slider_parameters', array( $this, 'get_flex_slider_parameters' ), 10, 2 );

            return $this->get_flex_slider_markup( $video_id, $settings, $ratio );
        }
    }

    /**
     * Add inline styles used by videos
     */
    public function add_extra_styles()
    {
        $css  = ".metaslider .ms-{$this->slug} .play_button{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center}";
        $css .= ".metaslider .ms-{$this->slug} .play_button img{width:75px;cursor:pointer;opacity:0.8}";
        $css .= ".metaslider .ms-{$this->slug} .play_button img:hover{opacity:1}";
        $css .= ".metaslider .ms-{$this->slug} div.video-js{position:absolute;top:0;left:0}";
        $css .= ".metaslider .ms-{$this->slug} div.video-js.ms-fade-video{opacity:0;transition:opacity 0.3s ease-in-out}";

        $css = apply_filters( "metaslider_{$this->identifier}_inline_css", $css, $this->slide, $this->slider->ID );
        wp_add_inline_style( 'metaslider-public', $css );
    }

    /**
     * Return the slide HTML for flex slider
     *
     * @return string
     */
    private function get_flex_slider_markup()
    {
        $html  = $this->video_wrapper();

        $attributes = array(
            'class' => "slide-{$this->slide->ID} ms-{$this->slug} {$this->get_mobile_css_class($this->slide->ID)}",
            'style' => "display: none; width: 100%;",
            'data-slide-type' => $this->identifier,
            'data-date' => $this->slide->post_date
        );

        if ( $thumb_id = get_post_meta( $this->slide->ID, '_thumbnail_id', true ) ) {
            $attributes['data-filename'] = wp_basename( wp_get_attachment_url( $thumb_id ) );
        }

        // Custom delay
        $autoPlay = isset( $this->slide_settings['autoPlay'] ) ? (int)filter_var(
            $this->slide_settings['autoPlay'],
            FILTER_VALIDATE_BOOLEAN
        ) : 0;
        $is_delayed = (bool) get_post_meta( $this->slide->ID, '_meta_slider_slide_is_delayed', true );
        $delay_time = (int) get_post_meta( $this->slide->ID, '_meta_slider_slide_delayed_time', true );
        
        if ( filter_var( $this->settings['autoPlay'], FILTER_VALIDATE_BOOLEAN ) 
            && $is_delayed && $delay_time > 0 && ! $autoPlay 
        ) {
            $attributes['data-custom-delay'] = $delay_time;
        }

        $attributes = apply_filters(
            'metaslider_flex_slider_li_attributes',
            $attributes,
            $this->slide->ID,
            $this->slider->ID,
            $this->settings
        );

        $li = "<li";

        foreach ($attributes as $att => $val) {
            $li .= " " . $att . '="' . esc_attr($val) . '"';
        }

        $li .= ">" . $html . "</li>";

        $html = $li;

        return $html;
    }

    /**
     * Return the video wrapper with custom data attributes
     *
     * @return string
     */
    private function video_wrapper()
    {
        if ( ! (int) $this->settings['height'] || ! (int) $this->settings['width'] ) {
            $ratio = 9 / 16 * 100;
        } else {
            $ratio = $this->settings['height'] / $this->settings['width'] * 100;
        }

        // @since 2.40
        $caption = get_post_meta($this->slide->ID, 'ml-slider_caption', true);

        // @since 2.51 - Store the slide details
        $slide = array(
            'id' => $this->slide->ID,
            'caption' => html_entity_decode(do_shortcode($caption), ENT_NOQUOTES, 'UTF-8'),
            'caption_raw' => do_shortcode($caption)
        );

        // @since 2.51 - Remove unsafe html but let users that rely on this to override
        if ( apply_filters( 'metaslider_filter_unsafe_html', true, $slide, $this->slider->ID, $this->settings ) 
            && ! empty( $caption )  
            && function_exists( 'metaslider_filter_unsafe_html' ) ) {
            $caption = metaslider_filter_unsafe_html( $caption, $slide, $this->slider->ID, $this->settings );
        }

        $imageHelper = new MetaSliderImageHelper(
            $this->slide->ID,
            $this->image_cropped_size( 'width' ),
            $this->image_cropped_size( 'height' ),
            isset( $this->settings['smartCrop'] ) ? $this->settings['smartCrop'] : 'false'
        );
        
        $video_id       = $this->get_video_id();
        $poster         = $imageHelper->get_image_url();
        $controls       = ! isset( $this->slide_settings['controls'] ) || filter_var(
            $this->slide_settings['controls'],
            FILTER_VALIDATE_BOOLEAN
        ) ? 'true' : 'false';

        // Video sources
        $sources = $this->videoHelper->get_all_sources( $this->slide->ID );

        // Adjust array to match with videojs source object
        foreach ( $sources as &$item ) {
            $item['src'] = $item['url'];
            unset( $item['url'] );
            unset( $item['id'] );
            unset( $item['format'] );
        }

        // @since 2.41 - Add Fit video in container
        $fit_video_class = $this->setting_state('fit') ? ' ms-fit-video' : '';

        // @since 2.46 - Add fade efefct
        $fade_video_class = $this->setting_state('fade') ? ' ms-fade-video' : '';

        $attributes = array(
            'class'             => "{$this->slug} video-js{$fit_video_class}{$fade_video_class}",
            'data-id'           => $this->slide->ID, // Slide ID, not video ID!
            'data-lazy-load'    => $this->setting_state( 'lazyLoad' ) ? 'true' : 'false',
            'data-sources'      => htmlspecialchars( json_encode( $sources ), ENT_QUOTES, 'UTF-8' ),
            'data-controls'     => $controls,
            'data-loop'         => $this->setting_state( 'loop' ) ? 'true' : 'false',
            'data-poster'       => $poster,
            'data-autoplay'     => $this->setting_state( 'autoPlay' ) ? 'true' : 'false',
            'data-mute'         => $this->setting_state( 'mute' ) ? 'true' : 'false',
            'style'             => 'position:relative;height:0;' . sprintf( 'padding-bottom:%s%%', $ratio )
        );

        // Track settings
        $attributes = $this->track_attributes( $attributes );

        // Link URL settings
        $attributes = $this->link_url_attributes( $attributes );

        $html = $attrs = "";
        foreach ( $attributes as $att => $val ) {
            $attrs .= " " . $att . '="' . esc_attr( $val ) . '"';
        }

        $html .= "<div{$attrs}>";
        
        if( $this->setting_state( 'lazyLoad' ) ) {
            $attachment_id  = $this->get_attachment_id();
            $alt            = esc_attr( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );
            
            $html .= $this->temporary_video_image( $poster, $alt );
        }

        $html .= "</div>";

        if ($caption) {
            $html .= "<div class='caption-wrap'><div class='caption'>". do_shortcode($caption) ."</div></div>";
        }

        return $html;
    }

    /**
     * Return the thumb video embed for admin - Still in use for External video
     * 
     * @return string
     */
    public function get_admin_video_thumb( $url, $type )
    {
        $html = '';

        if( isset( $url ) && ! empty( $url ) && $type != '' ) {
            $html  = '<video loop muted';
            $html .= '   onmouseover="this.play()" onmouseout="this.pause()">';
            $html .= '    <source src="' . esc_url( $url ) . '"';
            $html .= '       type="' . esc_attr( $type ) . '"></source>';
            $html .= '</video>';
        } 

        return $html;
    }

    /**
     * Return the thumb video embed for admin 
     * 
     * @deprecated 2.40 - Use $this->videoHelper->get_admin_video_embed() instead
     * 
     * @param array $sources An array with video url, id and mime type
     * 
     * @return string
     */
    public function get_admin_video_embed( $sources )
    {
        $html = '';

        if ( is_array( $sources ) && count( $sources ) > 0 ) {
            $html  = '<video loop muted';
            $html .= '   onmouseover="this.play()" onmouseout="this.pause()">';

            foreach ( $sources as $video ) {
                $html .= '    <source src="' . esc_url( $video['url'] ) . '"';
                $html .= '       type="' . esc_attr( $video['type'] ) . '"></source>';
            }

            $html .= '</video>';
        } 

        return $html;
    }

    /**
     * Get the title of the video
     *
     * @param int|string $url - Image URL
     * @param int|string $alt - Image alt
     * @return string
     */
    public function temporary_video_image( $url, $alt )
    {
        $html = "";

        if( ! empty( $url ) ) {
            $attributes = array(
                'src' => $url,
                'alt' => $alt,
                'title' => '',
                'class' => 'msDefaultImage',
                'height' => $this->image_cropped_size( 'height' ),
                'width' => $this->image_cropped_size( 'width' )
            );

            $slide = array(
                'id' => $this->slide->ID
            );

            $attributes = apply_filters(
                'metaslider_flex_slider_image_attributes', 
                $attributes, 
                $slide, 
                $this->slider->ID
            );

            $html .= $this->build_image_tag( $attributes );
        }

        // Display button when controls are enabled
        if( ! isset( $this->slide_settings['controls'] ) || filter_var(
            $this->slide_settings['controls'],
            FILTER_VALIDATE_BOOLEAN
        ) ) {
            $html .= "<button class='vjs-big-play-button' type='button' title='Play Video' aria-disabled='false'>";
            $html .= "  <span class='vjs-icon-placeholder' aria-hidden='true'></span>";
            $html .= "  <span class='vjs-control-text' aria-live='polite'>Play Video</span>";
            $html .= "</button>";
        }

        return $html;
    }

    /**
     * A setting is enabled or not?
     * 
     * @deprecated since 2.30 - Use setting_state() instead.
     * 
     * @param string $setting The name of the slide setting. e.g. 'autoPlay'
     * 
     * @return boolean
     */
    private function get_setting_state( $setting )
    {
        $this->setting_state( $setting );
    }

    /**
     * A setting is enabled or not?
     * 
     * @param string $setting The name of the slide setting. e.g. 'autoPlay'
     * 
     * @return boolean
     */
    public function setting_state( $setting )
    {
        return isset( $this->slide_settings[$setting] ) && filter_var(
            $this->slide_settings[$setting],
            FILTER_VALIDATE_BOOLEAN
        ) ? true : false;
    }

    /**
     * Pause videos when the slide is changed
     *
     * @param array $options - current slideshow options
     * @param int $slider_id - current slideshow ID
     * @return array
     */
    public function get_flex_slider_parameters( $options, $slider_id )
    {
        $addActiveClass         = "";
        $loopContinuously_play  = "";
        $loopContinuously_pause = "";

        // @since 2.36 - Change animation CSS value when Carousel and Loop Endlessly are enabled
        if ( isset( $this->settings['carouselMode'] ) && $this->settings['carouselMode'] == 'true' 
            && isset( $this->settings['infiniteLoop'] ) && $this->settings['infiniteLoop'] == 'true'
        ) {
            $loopContinuously_play  = "$('#metaslider_{$slider_id} .slides').css('animation-play-state', 'paused');";
            $loopContinuously_pause = "$('#metaslider_{$slider_id} .slides').css('animation-play-state', '');";
        }

        // Add active slide class when carousel mode is enabled
        if ( 'true' == $this->settings['carouselMode'] ) {
            $addActiveClass = "$(slider).find('.slides > li').removeClass('flex-active-slide').eq(slider.currentSlide).addClass('flex-active-slide');";
        }

        /* This is for the slideshow autoplay on Flexslider's start param, not the video autoplay.
         * This will play the slideshow when the video is paused
         * 
         * firstSlideNoLoop means: stop on first slide after one loop
         * firstSlideNoLoop means: stop on last slide
         * 
         * @TODO - Minify this output
         */
        $autoplay = filter_var( $this->settings['autoPlay'], FILTER_VALIDATE_BOOLEAN ) 
            ? "player.on('ended', function() {
                $('#metaslider_{$slider_id} .slides > li').removeClass('video-playing');

                var lastSlideAndNoLoop = !slider.vars.animationLoop && slider.currentSlide === slider.count - 1;
                var firstSlideNoLoop = slider.currentSlide === 0 && typeof slider.vars.loopCount !== 'undefined' && slider.vars.loopCount > 0;
                if (firstSlideNoLoop || lastSlideAndNoLoop) {
                    return;
                }

                $('#metaslider_{$slider_id}').data('flexslider').manualPause = false;
                $('#metaslider_{$slider_id}').data('flexslider').flexslider('next');
                $('#metaslider_{$slider_id}').data('flexslider').flexslider('play');
			});
            player.on('pause', function() {
                $('#metaslider_{$slider_id} .slides > li').removeClass('video-playing');
                $('#metaslider_{$slider_id}').data('flexslider').manualPause = false;
                {$loopContinuously_pause}
			});" 
            // Slideshow Autoplay disabled
            : "player.on('ended', function() {
                $('#metaslider_{$slider_id} .slides > li').removeClass('video-playing');
			});
            player.on('pause', function() {
                $('#metaslider_{$slider_id} .slides > li').removeClass('video-playing');
			});";

        /* For slides with lazyLoad enabled, we need to detect touch devices (aka mobile)
         * through isTouch_{$this->identifier} variable to use 'touchstart' event once as trigger
         * due 'click' is detected until a second tap */
        $touchStarted = "if(eventType === 'touchstart' && !touchStarted) {
            touchStarted = true;
        } else if(eventType === 'touchstart' && touchStarted) {
            return;
        }";

        $playOneAtTheTime = filter_var( $this->settings['carouselMode'], FILTER_VALIDATE_BOOLEAN ) 
            ? "try {
            for (var id in videojs.players) {
                var p = videojs.players[id];
                if (!p || p === this) {
                    continue;
                }
                if (!p.paused()) {
                    p.pause();
                }
            }
        } catch(e) {
            console.log(e);
        }" : "";

        /* When the slideshow is loaded / first slide
         * To check text tracks in browser console: 
         * videojs.players.{player_id}.textTracks().tracks_ */
        $options['start'] = isset( $options['start'] ) ? $options['start'] : array();
        $options['start'] = array_merge($options['start'], array(
            "{$addActiveClass}
            var isTouch_{$this->identifier} = 'ontouchstart' in document.documentElement;

            $('#metaslider_{$slider_id} .{$this->slug}').each(function(instance) {
                var video = $(this);
                var id = 'ms_videojs_' + video.data('id') + '_' + instance;

                video.data('instance', instance);
                
                var active = video.parent('.flex-active-slide').length;
                var autoplay = video.data('autoplay') && active ? true : false;
                var lazyload = video.data('lazyLoad');
                var eventType = lazyload 
                            ? isTouch_{$this->identifier} ? 'touchstart' : 'click'
                            : 'metaslider/load-{$this->slug}';
                var track = video.data('track-url') && video.data('track-url').length ? video.data('track-url') : false;
                var crossorigin = video.data('track-crossorigin') ? ' crossorigin=\"anonymous\"' : '';
                var haveLink = video.data('link-url') ? true : false;
                var touchStarted = false;


                video.on(eventType, function(){
                    {$touchStarted}

                    if(typeof videojs.players[id] === 'undefined') {
                        $(this).append('<video playsinline class=\"video-js\" id=\"' + id + '\"' + crossorigin + '></video>');

                        var player = videojs(id, {
                            controls: $(this).data('controls'),
                            muted: $(this).data('mute'),
                            poster: $(this).data('poster'),
                            fill: true,
                            loop: $(this).data('loop'),
                            sources: $(this).data('sources'),
                            userActions: {
                                click: video.data('link-url') ? false : true
                            }
                        });

                        if (haveLink) {
                            var link = $('<a></a>').attr({
                                'href': video.data('link-url'),
                                'aria-label': video.data('link-label'),
                                'target': video.data('link-target')
                            });
                            video.wrap(link);
                        }

                        var trackOpts = track
                            ? {
                                src: $(this).data('track-url'),
                                kind: $(this).data('track-kind'),
                                label: $(this).data('track-label'),
                                language: $(this).data('track-lang'),
                                default: true,
                                mode: 'showing'
                            } : false;

                        if (active && autoplay) {
                            $('#metaslider_{$slider_id}').flexslider('pause');
                            player.options_.autoplay = autoplay;
                            player.muted(true);
                        }
                        
                        player.on('click', function() {
                            if (haveLink) {
                                player.pause();
                            }
                        });

                        player.on('loadedmetadata', function() {
                            if (trackOpts) player.addRemoteTextTrack(trackOpts);
                            if (eventType === 'click' || eventType === 'touchstart') player.play();
                        });
                        player.on('play', function() {
                            var active_fade = $('#metaslider_{$slider_id} .flex-active-slide .video-js.ms-fade-video');
                            if (active_fade.length) {
                                active_fade.css('opacity', 1);
                            }
                            
                            $('#metaslider_{$slider_id} .flex-active-slide').addClass('video-playing');
                            $('#metaslider_{$slider_id}').flexslider('pause');
                            $('#metaslider_{$slider_id}').data('flexslider').manualPause = true;
                            $('#metaslider_{$slider_id}').data('flexslider').manualPlay = false;
                            {$loopContinuously_play}
                            {$playOneAtTheTime}
                        });
                        {$autoplay}
                        player.ready(function () {
                            if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
                                player.on('touchend', function (e) {
                                    if (e.target.closest('.vjs-control-bar')) return;

                                    var link = player.el().closest('a');
                                    if (!link || !link.href) return;

                                    if (link.target === '_blank') {
                                        window.open(link.href, '_blank');
                                    } else {
                                        window.location.href = link.href;
                                    }
                                });
                            }
                        });
                    }
                });
                if (lazyload && autoplay) $(this).trigger(eventType);
                lazyload || $(this).trigger('metaslider/load-{$this->slug}');
            });"
        ) );

        /* Before a slide transitions
         * Access the player already created in start event globally with videojs.players[id] 
         * to not generate new video instances of the same video */
        $options['before'] = isset( $options['before'] ) ? $options['before'] : array();
        $options['before'] = array_merge( $options['before'], array(
            "$('#metaslider_{$slider_id} .flex-active-slide .{$this->slug}').each(function(i) {
                var id = 'ms_videojs_' + $(this).data('id') + '_' + $(this).data('instance');

                if(typeof videojs.players[id] === 'undefined') return;

                var player = videojs.players[id];
                if ($(this).data('mute')) player.muted(true);
                player.pause();

                if($(this).data('autoplay')) $(this).data('st', false);

                var active_fade = $('#metaslider_{$slider_id} .flex-active-slide .video-js.ms-fade-video');
                if (active_fade.length) {
                    active_fade.css('opacity', 0);
                }
            });"
        ) );

        /* After a slide transitions
         * Access the player already created in start event globally with videojs.players[id] 
         * to not generate new video instances of the same video
         * 
         * We use $(this).data('st') to autoplay videos only once
         * when slide is active and avoid triggering mute and autoplay 
         * when user decides to pause or unmute
         * https://github.com/MetaSlider/metaslider-pro/issues/265 */
        $options['after'] = isset( $options['after'] ) ? $options['after'] : array();
        $options['after'] = array_merge($options['after'], array(
            "{$addActiveClass}
            var isTouch_{$this->identifier} = 'ontouchstart' in document.documentElement;
            var eventType = isTouch_{$this->identifier} ? 'touchstart' : 'click';

            $('#metaslider_{$slider_id} .flex-active-slide .{$this->slug}').each(function(i) {
                var id = 'ms_videojs_' + $(this).data('id') + '_' + $(this).data('instance');
                
                if ($(this).data('autoplay') && !$(this).data('st')) {
                    $('#metaslider_{$slider_id}').flexslider('pause');
                    $(this).data('st', true);
                        
                    if ( $(this).data('lazyLoad') && typeof videojs.players[id] === 'undefined') {
                        $(this).trigger(eventType);
                    } else {
                        var player = videojs.players[id];
                        player.muted(true);
                        player.play();
                    }
                }
            });"
        ) );

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter( 'metaslider_flex_slider_parameters', array( $this, 'get_flex_slider_parameters' ) );
        return $options;
    }

    public function duplicate_slide($slideshow_id, $slide_id)
    {
        $old_slide = get_post($slide_id);
        if (!$old_slide) {
            return 0;
        }
        $title = $old_slide->post_title;
        $post_excerpt = '';
        if(isset($old_slide->post_excerpt)){
            $post_excerpt = $old_slide->post_excerpt;
        }
        $new_slide = [
            'post_title'  => $title,
            'post_name'   => sanitize_title($title),
            'post_status' => 'publish',
            'post_type'   => $old_slide->post_type,
            'post_excerpt'   => $post_excerpt
        ];
        $new_slide_id = wp_insert_post($new_slide);
        $slide_meta = get_post_custom($slide_id);
        foreach ($slide_meta as $key => $values) {
            foreach ($values as $value) {
                add_post_meta($new_slide_id, $key, maybe_unserialize($value));
            }
        }
        $taxonomies = get_post_taxonomies($slide_id);
        foreach ($taxonomies as $taxonomy) {
            $term_ids = wp_get_object_terms($slide_id, $taxonomy, ['fields' => 'ids']);
            wp_set_object_terms($new_slide_id, $term_ids, $taxonomy);
        }

        $this->set_slide($new_slide_id);
        $this->set_slider($slideshow_id);

        return array('slide_id' => $new_slide_id, 'html' => $this->get_admin_slide());
    }

    /**
     * Ajax wrapper for duplicate_slide().
     *
     * @since 3.114.0 Only duplicates a slide the user can edit, into a slideshow they can edit
     *
     * @return void
     */
    public function ajax_duplicate_slide()
    {
        if ( ! isset( $_POST['nonce'] ) 
            || ! wp_verify_nonce( sanitize_key( $_POST['nonce']), "metaslider_duplicate_{$this->identifier}_nonce" ) 
        ) {
            wp_send_json_error( esc_html__( 'Invalid nonce', 'ml-slider' ), 403 );
        }

        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
        if (! current_user_can($capability)) {
            wp_send_json_error(
                [
                    'message' => __('Access denied', 'ml-slider')
                ],
                403
            );
        }

        if (! isset($_POST['slide_id']) || ! isset($_POST['slider_id'])) {
            wp_send_json_error(
                [
                    'message' => __('Bad request', 'ml-slider'),
                ],
                400
            );
        }

        $slider_id = absint($_POST['slider_id']);
        $slide_id  = absint($_POST['slide_id']);
        metaslider_verify_slideshow_for_ajax($slider_id);
        metaslider_verify_slide_for_ajax($slide_id);

        $result = $this->duplicate_slide($slider_id, $slide_id);

        wp_send_json_success($result, 200);
    }
}
