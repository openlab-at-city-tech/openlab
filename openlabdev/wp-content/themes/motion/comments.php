<?php // Do not delete these lines
// thanks to Jeremy at http://clarktech.no-ip.com for the tips

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
  die ( 'Please do not load this page directly. Thanks!' );


if ( function_exists( 'post_password_required' ) ) {
    // If the post is password protected
  if ( post_password_required() ) {
    echo '<p class="nocomments">This post is password protected. Enter the password to view comments.</p>';
    return;
  }
} else {
  if (!empty($post->post_password)) { // if there's a password
    if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) { // and it doesn't match the cookie  ?>
      <p class="nocomments">This post is password protected. Enter the password to view comments.</p>
      <?php return;
    }
  }
}

//WP 2.7 Comment Loop
if ( have_comments() ) : ?>

  <?php if ( ! empty($comments_by_type['comment']) ) :
  $count = count($comments_by_type['comment']);
  ($count !== 1) ? $txt = "Comments: " : $txt = "Comment:"; ?>
  <h3><?php echo $count . " " . $txt; ?></h3>
  <ul class="commentlist">
    <?php wp_list_comments( 'type=comment&callback=motiontheme_comment' ); ?>
  </ul>
  <?php endif; ?>

  <div id="navigation">
    <div class="alignleft"><?php previous_comments_link(); ?></div>
    <div class="alignright"><?php next_comments_link(); ?></div>
  </div><!-- /navigation -->

  <?php 
  
  if ( ! empty($comments_by_type['pings']) ) :
    $countp = count($comments_by_type['pings']);
    ($countp !== 1) ? $txtp = "Trackbacks / Pingbacks for this entry:" : $txtp = "Trackback or Pingback for this entry:"; ?>
    <h3 id="trackbacktitle"><?php echo $countp . " " . $txtp; ?></h3>
    <ul class="trackback">
      <?php wp_list_comments( 'type=pings&callback=motiontheme_ping' ); ?>
    </ul>
  <?php endif; ?>

<?php endif; ?>

<?php 
  if (comments_open( $post->ID )){ comment_form(); }
?>