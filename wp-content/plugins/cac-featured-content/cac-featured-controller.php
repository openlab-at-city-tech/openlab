<?php
/**
 * This file is responsible for gathering all required variable values that
 * will be used in each of the specific view files depending on what type of
 * featured content has been chosen by the user.
 *
 * @author Dominic Giglio
 *
 */

// require our helper class so we can use its static methods
require_once 'cac-featured-helper.php';

// enqueue our default stylesheet
wp_enqueue_style( 'cfcw-default-styles', plugins_url( 'css/cfcw-default.css' , __FILE__ ) );

// instantiate our view class
$cfcw_view = new CAC_Featured_Content_View();

// common view template variables
$cfcw_view->content_type   = $params['featured_content_type'];
$cfcw_view->description    = $params['custom_description'];
$cfcw_view->display_images = $params['display_images'];
$cfcw_view->title_element  = $params['title_element'];
$cfcw_view->crop_length    = $params['crop_length'];
$cfcw_view->image_width    = $params['image_width'];
$cfcw_view->image_height   = $params['image_height'];
$cfcw_view->image_url      = $params['image_url'];
$cfcw_view->read_more      = $params['read_more'];
$cfcw_view->title          = $params['title'];

// blog specific view template variables
if ( $cfcw_view->content_type == 'blog' ) {

  $cfcw_view->blog = CAC_Featured_Content_Helper::get_blog_by_domain( $params['featured_blog'] );

  // if we don't have a valid blog bail now
  if ( ! $cfcw_view->blog ) {
    CAC_Featured_Content_Helper::error( __( 'Invalid Blog Name.', 'cac-featured-content' ) );
    return false;
  }

  // if there is a custom description, use it
  if ( $cfcw_view->description )
    $cfcw_view->blog->description = bp_create_excerpt( $cfcw_view->description, $cfcw_view->crop_length );
  else
    $cfcw_view->blog->description = bp_create_excerpt( get_blog_option( $cfcw_view->blog->blog_id, 'blogdescription' ), $cfcw_view->crop_length );

  // we only want to load images if the display images checkbox is checked
  if ( $cfcw_view->display_images ) {

    // if the admin entered a static url use it, otherwise get one from the blog
    if ( $cfcw_view->image_url )
      $cfcw_view->image_url = '<img src="' . $cfcw_view->image_url . '" alt="Thumbnail" class="avatar" width="' . $cfcw_view->image_width . '" height="' . $cfcw_view->image_height . '" />';
    else
      $cfcw_view->image_url = CAC_Featured_Content_Helper::get_image_from_blog( $cfcw_view->blog->blog_id, $cfcw_view->image_width, $cfcw_view->image_height );

    // if we still don't have an image, get an avatar
    if ( ! $cfcw_view->image_url ) {
      $cfcw_view->avatar = bp_core_fetch_avatar( array(
        'item_id' => cacfc_get_user_id_from_string( get_blog_option( $cfcw_view->blog->blog_id, 'admin_email' ) ),
        'width'   => $cfcw_view->image_width,
        'height'  => $cfcw_view->image_height,
        'type'    => 'full',
        'no_grav' => false ) );
    }
  }

}

// group specific view template variables
if ( $cfcw_view->content_type == 'group' ) {
  $cfcw_view->group = groups_get_group( array( 'group_id' => BP_Groups_Group::group_exists( $params['featured_group'] ) ) );

  // if we don't have a valid group bail now
  if ( ! $cfcw_view->group->id ) {
    CAC_Featured_Content_Helper::error( __( 'Invalid Group Name.', 'cac-featured-content' ) );
    return false;
  }

  // get the group permalink
  $cfcw_view->group->permalink = bp_get_group_permalink( $cfcw_view->group );

  // if there is a custom description, use it
  if ( $cfcw_view->description )
    $cfcw_view->group->description = bp_create_excerpt( $cfcw_view->description, $cfcw_view->crop_length );
  else
    $cfcw_view->group->description = bp_create_excerpt( $cfcw_view->group->description, $cfcw_view->crop_length );

  // we only want to load images if the display images checkbox is checked
  if ( $cfcw_view->display_images ) {

    // if the admin entered a static url use it
    if ( $cfcw_view->image_url )
      $cfcw_view->image_url = '<img src="' . $cfcw_view->image_url . '" alt="Thumbnail" class="avatar" width="' . $cfcw_view->image_width . '" height="' . $cfcw_view->image_height . '" />';

    // if we still don't have an image, get an avatar
    if ( ! $cfcw_view->image_url ) {
      $cfcw_view->avatar = bp_core_fetch_avatar( array(
        'item_id' => $cfcw_view->group->id,
        'width'   => $cfcw_view->image_width,
        'object'  => 'group',
        'height'  => $cfcw_view->image_height,
        'type'    => 'full',
        'no_grav' => false ) );
    }
  }
}

// post specific view template variables
if ( $cfcw_view->content_type == 'post' ) {

  if ( is_multisite() ) {
    $cfcw_view->blog = CAC_Featured_Content_Helper::get_blog_by_domain( $params['featured_blog'] );
    $cfcw_view->post = CAC_Featured_Content_Helper::get_post_by_slug( $params['featured_post'], $cfcw_view->blog->blog_id );
  } else {
    $cfcw_view->post = CAC_Featured_Content_Helper::get_post_by_slug( $params['featured_post'] );
  }

  // if we don't have a valid post bail now
  if ( ! $cfcw_view->post ) {
    CAC_Featured_Content_Helper::error( __( 'Invalid Post Slug.', 'cac-featured-content' ) );
    return false;
  }

  // if there is a custom description, use it
  if ( $cfcw_view->description )
    $cfcw_view->post->description = bp_create_excerpt( $cfcw_view->description, $cfcw_view->crop_length );
  else
    $cfcw_view->post->description = bp_create_excerpt( $cfcw_view->post->post_content, $cfcw_view->crop_length );

  // we only want to load images if the display images checkbox is checked
  if ( $cfcw_view->display_images ) {

    // if the admin entered a static url use it, otherwise get one from the post
    if ( $cfcw_view->image_url )
      $cfcw_view->image_url = '<img src="' . $cfcw_view->image_url . '" alt="Thumbnail" class="avatar" width="' . $cfcw_view->image_width . '" height="' . $cfcw_view->image_height . '" />';
    else
      $cfcw_view->image_url = CAC_Featured_Content_Helper::get_image_from_post( $cfcw_view->post->post_content, $cfcw_view->image_width, $cfcw_view->image_height );

    // if we still don't have an image, get an avatar
    if ( ! $cfcw_view->image_url ) {
      $cfcw_view->avatar = bp_core_fetch_avatar( array(
        'item_id' => $cfcw_view->post->post_author,
        'width'   => $cfcw_view->image_width,
        'height'  => $cfcw_view->image_height,
        'type'    => 'full',
        'no_grav' => false ) );
    }
  }

}

// member specific view template variables
if ( $cfcw_view->content_type == 'member' ) {
  $cfcw_view->member = bp_core_get_core_userdata( cacfc_get_user_id_from_string( $params['featured_member'] ) );

  // if we don't have a valid member bail now
  if ( ! $cfcw_view->member ) {
    CAC_Featured_Content_Helper::error( __( 'Invalid Member Username.', 'cac-featured-content' ) );
    return false;
  }

  $cfcw_view->member->user_link = bp_core_get_userlink( $cfcw_view->member->ID );
  $cfcw_view->member->last_activity = bp_get_last_activity( $cfcw_view->member->ID );

  // we only want to load images if the display images checkbox is checked
  if ( $cfcw_view->display_images ) {

    // if the admin entered a static url use it
    if ( $cfcw_view->image_url )
      $cfcw_view->image_url = '<img src="' . $cfcw_view->image_url . '" alt="Thumbnail" class="avatar" width="' . $cfcw_view->image_width . '" height="' . $cfcw_view->image_height . '" />';

    // if we still don't have an image, get an avatar
    if ( ! $cfcw_view->image_url ) {
      $cfcw_view->avatar = bp_core_fetch_avatar( array(
        'item_id' => $cfcw_view->member->ID,
        'width'   => $cfcw_view->image_width,
        'height'  => $cfcw_view->image_height,
        'type'    => 'full',
        'no_grav' => false ) );
    }

  }
}

// resource specific view template variables
if ( $cfcw_view->content_type == 'resource' ) {
  $cfcw_view->resource_title = $params['featured_resource_title'];
  $cfcw_view->resource_link  = $params['featured_resource_link'];
}

// Load the appropriate view file. We allow the file to be loaded from a theme, to override default markup.
$template_file = "cac-featured-{$cfcw_view->content_type}.php";
if ( ! $template = locate_template( 'cac-featured-content/' . $template_file ) ) {
	$template = dirname( __FILE__ ) . '/views/' . $template_file;
}

require $template;
