<?php
// see : http://wordpress.org/support/topic/plugin-nextgen-gallery-ngg-and-featured-image-issue?replies=14
/**
 * nggPostThumbnail - Class for adding the post thumbnail feature
 * 
 * @package NextGEN Gallery
 * @author Alex Rabe 
 * 
 * @version 1.0.2
 * @access internal
 */
class nggPostThumbnail {

	/**
	 * Main constructor - Add filter and action hooks
	 * 
	 */	
	function __construct() {
		
		add_filter( 'admin_post_thumbnail_html', array( $this, 'admin_post_thumbnail'), 10, 2 );
		add_action( 'wp_ajax_ngg_set_post_thumbnail', array( $this, 'ajax_set_post_thumbnail') );

		// Adding filter for the new post_thumbnail
		add_filter( 'post_thumbnail_html', array( $this, 'ngg_post_thumbnail'), 10, 5 );
	}

	/**
	 * Filter for the post meta box. look for a NGG image if the ID is "ngg-<imageID>"
	 * 
	 * @param string $content
	 * @return string html output
	 */
	function admin_post_thumbnail( $content, $post_id = null ) 
	{
    if ($post_id == null)
    {
			global $post;

		  if ( !is_object($post) )
		     return $content;
       
      $post_id = $post->ID;
    }
        
		$thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);

		// in the case it's a ngg image it return ngg-<imageID>
		if ( strpos($thumbnail_id, 'ngg-') === false) 
		{
			global $wp_version;
			
			if (version_compare($wp_version, '3.5', '>=') && $thumbnail_id <= 0)
			{
				$iframe_src = get_upload_iframe_src('image', $post_id);
				$iframe_src = remove_query_arg('TB_iframe', $iframe_src);
				$iframe_src = add_query_arg('tab', 'nextgen', $iframe_src);
				$iframe_src = add_query_arg('chromeless', '1', $iframe_src);
				$iframe_src = add_query_arg('TB_iframe', '1', $iframe_src);

	   		    $set_thumbnail_link = '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set NextGEN featured image', 'nggallery' ) . '" href="' . nextgen_esc_url( $iframe_src ) . '" id="set-ngg-post-thumbnail" class="thickbox">%s</a></p>';
			    $content .= sprintf($set_thumbnail_link, esc_html__( 'Set NextGEN featured image', 'nggallery' ));
			}
			
			return $content;
		}
			
		// cut off the 'ngg-'
		$thumbnail_id = substr( $thumbnail_id, 4);

		return $this->_wp_post_thumbnail_html( $thumbnail_id );		
	}
	
	/**
	 * Filter for the post content
	 * 
	 * @param string $html
	 * @param int $post_id
	 * @param int $post_thumbnail_id
	 * @param string|array $size Optional. Image size.  Defaults to 'thumbnail'.
	 * @param string|array $attr Optional. Query string or array of attributes.
	 * @return string html output
	 */
	function ngg_post_thumbnail( $html, $post_id, $post_thumbnail_id, $size = 'post-thumbnail', $attr = '' ) {

		global $post, $_wp_additional_image_sizes;

		// in the case it's a ngg image it return ngg-<imageID>
		if ( strpos($post_thumbnail_id, 'ngg-') === false)
			return $html;

		// cut off the 'ngg-'
		$post_thumbnail_id = substr( $post_thumbnail_id, 4);

		// get the options
		$ngg_options = nggGallery::get_option('ngg_options');

		// get the image data
		$image = nggdb::find_image($post_thumbnail_id);

		if (!$image) 
			return $html;

		$img_src = false;		
		$class = 'wp-post-image ngg-image-' . $image->pid . ' ';
        
        if (is_array($size) || is_array($_wp_additional_image_sizes) && isset($_wp_additional_image_sizes[$size])) {		        	        		
			$class .= isset($attr['class']) ? esc_attr($attr['class']) : '';
		
			if( is_array($size)){
				//the parameters is given as an array rather than a predfined image
				$width = absint( $size[0] );
				$height = absint( $size[1] );
				if(isset($size[2]) && $size[2] === true) {
					$mode = 'crop';
				} else if(isset($size[2])){
					$mode = $size[2];
				} else {
					$mode = '';					
				}
			} else {
				$width = absint( $_wp_additional_image_sizes[$size]['width'] );
				$height = absint( $_wp_additional_image_sizes[$size]['height'] );
        $mode = ($_wp_additional_image_sizes[$size]['crop']) ? 'crop' : '';
			}

      // check fo cached picture
          if ( $post->post_status == 'publish' )
              $img_src = $image->cached_singlepic_file( $width, $height, $mode );                
  
			// if we didn't use a cached image then we take the on-the-fly mode 
		        if ($img_src ==  false) 
		        	$img_src = trailingslashit( home_url() ) . 'index.php?callback=image&amp;pid=' . $image->pid . '&amp;width=' . $width . '&amp;height=' . $height . '&amp;mode=crop';
                
		} else {
			$img_src = $image->thumbURL;
		}
		
		$alttext = isset($attr['alt']) ? $attr['alt'] : $image->alttext;
		$titletext = isset($attr['title']) ? $attr['title'] : $image->title;

		$html = '<img src="' . esc_attr($img_src) . '" alt="' . esc_attr($alttext) . '" title="' . esc_attr($titletext) .'" class="'.$class.'" />';

		return $html;
	}
	
	/**
	 * nggPostThumbnail::ajax_set_post_thumbnail()
	 * 
	 * @return void
	 */
	function ajax_set_post_thumbnail() 
	{
		// This function does the following:
		// 1) Check if the user is logged in and has permission to edit the post
		// 2) Get the thumbnail id from the POST request. The thumbnail id is actually the NGG image id
		// 3)]

		global $post_ID;

		// check for correct capability
		if ( !is_user_logged_in() )
			die( '-1' );

        if (!wp_verify_nonce($_POST['nonce'], 'ngg_set_post_thumbnails'))
            die('-1');

		// get the post id as global variable, otherwise the ajax_nonce failed later
		$post_ID = intval( $_POST['post_id'] );

		if ( !current_user_can( 'edit_post', $post_ID ) )
			die( '-1' );

		$thumbnail_id = intval( $_POST['thumbnail_id'] );

		// delete the image
		if ( $thumbnail_id == '-1' ) {
			delete_post_meta( $post_ID, '_thumbnail_id' );
			die('1');
		}

		if (($attachment_id = C_Gallery_Storage::get_instance()->set_post_thumbnail($post_ID, $thumbnail_id, TRUE))) {
			die(strval($attachment_id));
		}
		die(strval(0));
	}

	/**
	 * Output HTML for the post thumbnail meta-box.
	 *
	 * @see wp-admin\includes\post.php
	 * @param int $thumbnail_id ID of the image used for thumbnail
	 * @return string html output
	 */
	function _wp_post_thumbnail_html( $thumbnail_id = NULL ) {
	   
		global $_wp_additional_image_sizes, $post_ID;

	    $set_thumbnail_link = '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set featured image', 'nggallery' ) . '" href="' . nextgen_esc_url( get_upload_iframe_src('image') ) . '" id="set-post-thumbnail" class="thickbox">%s</a></p>';
	    $content = sprintf($set_thumbnail_link, esc_html__( 'Set featured image', 'nggallery' ));
		
        $image = nggdb::find_image($thumbnail_id);
        $img_src = false;

		// get the options
		$ngg_options = nggGallery::get_option('ngg_options');
        
		if ( $image ) {
            if ( is_array($_wp_additional_image_sizes) && isset($_wp_additional_image_sizes['post-thumbnail']) ){
                // Use post thumbnail settings if defined
     			$width = absint( $_wp_additional_image_sizes['post-thumbnail']['width'] );
    			$height = absint( $_wp_additional_image_sizes['post-thumbnail']['height'] );
                $mode = $_wp_additional_image_sizes['post-thumbnail']['crop'] ? 'crop' : '';
    		    // check fo cached picture
   		        $img_src = $image->cached_singlepic_file( $width, $height, $mode );                
            }

		    // if we didn't use a cached image then we take the on-the-fly mode 
		    if ( $img_src == false ) 
		        $img_src = trailingslashit( home_url() ) . 'index.php?callback=image&amp;pid=' . $image->pid . '&amp;width=' . $width . '&amp;height=' . $height . '&amp;mode=crop';
			
            $thumbnail_html = '<img width="266" src="'. $img_src . '" alt="'.$image->alttext.'" title="'.$image->alttext.'" />';
            
			if ( !empty( $thumbnail_html ) ) {
    			$ajax_nonce = wp_create_nonce( "set_post_thumbnail-$post_ID" );
    			$content = sprintf($set_thumbnail_link, $thumbnail_html);
    			$content .= '<p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail" onclick="WPRemoveThumbnail(\'' . $ajax_nonce . '\');return false;">' . esc_html__( 'Remove featured image' ) . '</a></p>';
			}
		}

		return $content;
	}	
	
}

$nggPostThumbnail = new nggPostThumbnail();
