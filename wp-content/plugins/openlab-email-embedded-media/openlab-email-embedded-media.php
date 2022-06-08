<?php
/**
 * Plugin Name: Openlab Email Embedded Media
 * Description: Handle embeded content in the email notifications sent by BPGES.
 * Author: OpenLab
 * Version: 1.0.0
*/

/**
 * Exclude images from the BPGES emails if they are coming from a private group.
 *
 */
add_filter( 'bp_ass_activity_notification_content', 'oleem_bpges_notification_content', 300, 4 );
function oleem_bpges_notification_content( $content, $activity, $action, $group ) {
    if( $activity->type === 'new_blog_post' ) {
        // Get post url
        $post_url = esc_url( $activity->primary_link );
        
        // Get site id by group id
        $site_id = openlab_get_site_id_by_group_id( $group->id );

        // Check if site is public
        $is_public = ( get_blog_option( $site_id, 'blog_public' ) >= 0 ) ? true : false; // 0 or 1 is public, negative is private

        // Remove images only for the posts of a non-public groups
        if( ! $is_public ) {
            $content = oleem_remove_private_images( $content, $post_url );
        }

        // Remove audio multimedia embeds from the email
        $content = oleem_remove_multimedia_embeds( $content, 'audio', $post_url );

        // Remove video multimedia embeds from the email
        $content = oleem_remove_multimedia_embeds( $content, 'video', $post_url );
    }

    return $content;
}

/**
 * Utility function to check if the link is from the
 * local WP network or an external
 *
 */
function oleem_is_network_link( $url ) {
    // Parse current site's url
    $site = parse_url( get_site_url() );

    // Parse provided url
    $url = parse_url( $url );
    print_r( $url );

    // WP Site object
    $site_object = get_site_by_path( $site['host'], '/' );

    // Compare hosts if site object exists
    if( $site_object ) {
        return $url['host'] === $site_object->domain;
    }

    return false;
}

/**
 * Remove private images from the content and change them
 * with a preview text and a link to the original blog post.
 *
 */
function oleem_remove_private_images( $content, $post_link = '#' ) {
    // Skip if content is missing
    if( empty( $content ) ) {
        return;
    }

    // Create new DOM document and load the content
    $document = new DOMDocument();
    $document->loadHTML($content, LIBXML_NOERROR);

    // Get all images in the content
    $images = $document->getElementsByTagName('img');

    // DOMNodeList is getting reseted after removing an element,
    // so we always need to point to the next first item
    for( $i = 0; $i < $images->length; $i++ ) {
        $image = $images->item(0);

        // If image is hosted on local WP network site
        if( oleem_is_network_link( $image->getAttribute('src') ) ) {
            if( $image->parentNode->tagName === 'a' ) {
                // Get <a> element
                $link = $image->parentNode;

                // Get <figure> element
                $figure = $image->parentNode->parentNode;
                
                // Make sure it's <figure>
                if( $figure->tagName === 'figure' ) {
                    // Move image to <figure>
                    $figure->appendChild($image);

                    // Remove <a> from within <figure>
                    $figure->removeChild($link);

                    // Remove <figcaption> if it's present within <figure>
                    if( $figure->hasChildNodes() ) {
                        foreach( $figure->childNodes as $figure_child ) {
                            if( $figure_child->tagName == 'figcaption' ) {
                                $figure->removeChild( $figure_child );
                            }
                        }
                    }
                }
            }

            // Change <img> with a preview text
            $private_link = $document->createElement( 'a', 'View this image by visiting the original post.' );
            $private_link->setAttribute( 'href', $post_link );
            $image->parentNode->replaceChild( $private_link, $image );
        }
    }

    // Return modified HTML
    return $document->saveHTML();
}

/**
 * Remove multimedia embeds (audio/video) from the content and change
 * it with a preview text with a link to the original blog post.
 *
 */
function oleem_remove_multimedia_embeds( $content, $media_type = '', $post_link = '#' ) {
    // Skip if media type and content is missing
    if( empty( $content ) || empty( $media_type ) ) {
        return;
    }

    // Create new DOM document and load the content
    $document = new DOMDocument();
    $document->loadHTML($content, LIBXML_NOERROR);

    // Find all elements with the specified tag name <media_type>
    $elements = $document->getElementsByTagName($media_type);

    // DOMNodeList is getting reseted after removing an element,
    // so we always need to point to the next first item
    for( $i = 0; $i < $elements->length; $i++ ) {
        $element = $elements->item(0);

        // If has <figure> as a parent
        if( $element->parentNode->tagName === 'figure' ) {

            // Get <figure> element
            $figure = $element->parentNode;

            // Remove <figcaption> if it's present within <figure>
            if( $figure->hasChildNodes() ) {
                foreach( $figure->childNodes as $figure_child ) {
                    if( $figure_child->tagName == 'figcaption' ) {
                        $figure->removeChild( $figure_child );
                    }
                }
            }
        }

        // Change <audio> with a preview text
        $private_link = $document->createElement( 'a', 'View this ' . $media_type . ' by visiting the original post.' );
        $private_link->setAttribute( 'href', $post_link );
        $element->parentNode->replaceChild( $private_link, $element );
    }

    // Return modified HTML
    return $document->saveHTML();
}
