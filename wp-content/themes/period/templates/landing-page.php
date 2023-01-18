<?php
/*
** Template Name: Landing Page
*/
?>
<!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>
	<?php wp_head(); ?>
</head>

<body id="<?php print get_stylesheet(); ?>" <?php body_class(); ?>>
  <a class="skip-content" href="#main"><?php esc_html_e( 'Press "Enter" to skip to content', 'period' ); ?></a>
  <div id="overflow-container" class="overflow-container">
    <div id="primary-container" class="primary-container">
      <div class="max-width">
        <section id="main" class="main" role="main">
          <div id="loop-container" class="loop-container">
            <?php
            if ( have_posts() ) :
              while ( have_posts() ) :
                the_post(); ?>
                <div <?php post_class(); ?>>
                  <article>
                    <div class="post-container">
                      <div class="post-content">
                        <?php the_content(); ?>
                      </div>
                    </div>
                  </article>
                </div>
              <?php endwhile;
            endif; ?>
          </div>
        </section> <!-- .main -->
      </div><!-- .max-width -->
    </div><!-- .primary-container -->
  </div><!-- .overflow-container -->
  <?php wp_footer(); ?>
</body>
</html>