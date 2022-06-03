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
    if( $activity->type === 'new_blog_post' && $group->status !== 'public' ) {
        $post_id = $activity->secondary_item_id;

        // Load the HTML of the email content
        $document = new DOMDocument();
        $document->loadHTML($content);

        // Get all images in the content
        $images = $document->getElementsByTagName('img');
        $images_length = $images->length;

        // DOMNodeList is getting reseted after removing an element,
        // so we always need to point to the next first item
        for( $i = 0; $i < $images_length; $i++ ) {
            $image = $images->item(0);

            // Skip this step if the image is from external source
            if( ! oleem_is_external_link( $image->getAttribute('src') ) ) {
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
                $private_link->setAttribute( 'href', '#' );
                $image->parentNode->replaceChild( $private_link, $image );
            }
        }

        // Update email's content with the latest changes
        $content = $document->saveHTML();
    }

    return $content;
}

/**
 * Utility function to check if the link is external
 * or coming from the same host
 * 
 */
function oleem_is_external_link( $url ) {
    // Get site URL
    $site_url = parse_url( get_site_url() );

    // Parse url
    $components = parse_url($url);

    // Check if the host of the provided link is same as the current website
    return ! empty( $components['host'] ) && strcasecmp( $components['host'], $site_url['host'] );
}
