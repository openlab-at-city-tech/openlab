<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct access.' );
}

/**
 * Class with reusable methods for Local videos. Also used by MetaSlider Slideshow Pro's
 * Layer and External Video slide types.
 *
 * @since 3.113.0
 */
class MetaVideoHelper
{

    /**
     * Get all the videos from a single slide 
     * aka. video slide have more than one source. e.g. mp4, mov, etc.
     * 
     * @param int $slide_id
     * 
     * @return array
     */
    public function get_all_sources( $slide_id )
    {
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
     * Return the thumb video embed for admin 
     * 
     * @param array $sources An array with video url, id and mime type
     * 
     * @return string
     */
    public function get_admin_video_embed( $sources )
    {
        $html = '';

        if ( is_array( $sources ) && count( $sources ) > 0 ) {
            $html  = '<video muted controls>';

            foreach ( $sources as $video ) {
                $html .= '    <source src="' . esc_url( $video['url'] ) . '"';
                $html .= '       type="' . esc_attr( $video['type'] ) . '"></source>';
            }

            $html .= '</video>';
        } 

        return $html;
    }

    /**
     * Return the thumb cover embed for admin 
     * 
     * @param array $sources An array with video url, id and mime type
     * 
     * @return string
     */
    public function get_admin_cover_embed( $slide_id, $cover_id, $thumb, $slide_type = 'local_video' )
    {
        $is_placeholder_thumb = $thumb == METASLIDER_ASSETS_URL . 'metaslider/placeholder-thumb.jpg' ? true : false;

        $html = '';

        if ( ! $is_placeholder_thumb ) :
            $html .= '<button data-slide-id="' . esc_attr( $slide_id ) . '" 
                data-slide-type="' . esc_attr( $slide_type ) . '" 
                class="remove-cover-image button button-secondary ms-button-danger">
                <i>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line> <line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </i>
            </button>';
        endif;
        
        $html .= '<button class="update-cover-image"
            data-button-text="' . esc_attr__( 'Update cover image', 'ml-slider' ) . '"
            data-slide-id="' . esc_attr( $slide_id ) . '"
            data-slide-type="' . esc_attr( $slide_type ) . '"
            data-attachment-id="' . esc_attr( $cover_id ) . '"';
            
            if ( ! empty( $thumb ) ) :
                $html .= 'title="' . esc_attr__( 'Update cover image', 'ml-slider' ) . '"
                style="background-image: url(' . esc_url( $thumb ) . ')"';
            endif;

        $html .= '>';

            if ( $is_placeholder_thumb ) :
                $html .= esc_html__( 'Set cover image', 'ml-slider' );
            endif;

        $html .= '</button>';

        return $html;
    }

    /**
     * Check if video type is allowed (mp4, webm and mov)
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
     * Video source HTML for admin screen
     * 
     * @param array $video  Array with video data including post id, url, format
     * @param int $slide_id
     * @param string $slide_type e.g. 'layer_slide'
     * 
     * @return html
     */
    public function admin_video_source_row( $video, $slide_id, $slide_type = 'local_video' )
    {
        $row = '<div class="row">
            <div class="flex">
                <button data-slide-type="' . esc_attr( $slide_type ) . '" data-button-text="' . 
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
                    esc_attr( $video['id'] ) . '" data-slide-type="' . 
                    esc_attr( $slide_type ) . '" class="remove-video-source button button-secondary ms-button-danger">
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
     * Get the video for the slide
     * 
     * @param int $video_id Video media id
     * 
     * @return string
     */
    public function get_video( $video_id )
    {
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
     * @param int $slide_id
     * 
     * @return int|bool
     */
    public function get_video_id( $slide_id )
    {
        $video_id = get_post_meta( $slide_id, 'ml-slider_video_id', true );

        if ( isset( $video_id ) ) {
            return absint( $video_id );
        }

        return false;
    }
}
