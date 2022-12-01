<?php
// If a post password is required or no comments are given and comments/pings are closed, return.
if ( post_password_required() || ( !have_comments() && !comments_open() && !pings_open() ) ) {
    return;
} ?>
<section id="comments" class="comments">
  <?php if ( wp_count_comments($post->ID)->approved > 0 ) : ?>
    <div class="comments-number">
      <h2>
        <?php comments_number( esc_html__( 'Be First to Comment', 'period' ), esc_html__( 'One Comment', 'period' ), esc_html_x( '% Comments', 'noun: 5 comments', 'period' ) ); ?>
      </h2>
    </div>
  <?php endif; ?>
  <ol class="comment-list">
    <?php wp_list_comments( array( 'callback' => 'ct_period_customize_comments' ) ); ?>
  </ol>
  <?php if ( ( get_option( 'page_comments' ) == 1 ) && ( get_comment_pages_count() > 1 ) ) : ?>
    <nav class="comment-pagination">
      <p class="previous-comment"><?php previous_comments_link(); ?></p>
      <p class="next-comment"><?php next_comments_link(); ?></p>
    </nav>
  <?php endif;
  if ( comments_open() ) {
    comment_form( array(
      'title_reply_before' => '<div id="reply-title" class="comment-reply-title">',
      'title_reply_after'  => '</div>'
    ) );
  } elseif (!comments_open() && pings_open() && is_singular( 'post' ) ) { ?>
    <p class="comments-closed pings-open">
      <?php
      // translators: placeholder is link to the trackback URL
      echo wp_kses_post( sprintf( __( 'Comments are closed, but <a href="%s" title="Trackback URL for this post">trackbacks</a> and pingbacks are open.', 'period' ), esc_url( get_trackback_url() ) ) );
      ?>
    </p>
  <?php } elseif (!comments_open() && have_comments() ) { ?>
    <p class="comments-closed">
      <?php esc_html_e( 'Comments are closed.', 'period' ); ?>
    </p>
  <?php } ?>
</section>