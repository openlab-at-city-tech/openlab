<?php
 /**
  * Title: 404 Header With Background
  * Slug: fse-freelancer-portfolio/404-header-with-background
  */
?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"}}},"className":"banner","layout":{"inherit":false}} -->
<div class="wp-block-group alignfull banner" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px"><!-- wp:cover {"url":"<?php echo get_parent_theme_file_uri( '/assets/images/banner.png' ); ?>","dimRatio":40,"isDark":false,"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"}}}} -->
<div class="wp-block-cover is-light" style="margin-bottom:var(--wp--preset--spacing--60)"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-40 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="<?php echo get_parent_theme_file_uri( '/assets/images/banner.png' ); ?>" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:group {"layout":{"inherit":true,"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:group {"className":"alignwide"} -->
<div class="wp-block-group alignwide"><!-- wp:post-title {"textAlign":"center","level":1} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover --></div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontSize":"300px","fontStyle":"normal","fontWeight":"600"},"spacing":{"margin":{"top":"0","bottom":"0","left":"0","right":"0"}}},"textColor":"secondary-bg-color"} -->
<h2 class="wp-block-heading has-text-align-center has-secondary-bg-color-color has-text-color" style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-size:300px;font-style:normal;font-weight:600">404</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"foreground","fontSize":"content-heading"} -->
<p class="has-text-align-center has-foreground-color has-text-color has-content-heading-font-size"><?php esc_html_e('Oops! That page cannot be found','fse-freelancer-portfolio'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"70px"} -->
<div style="height:70px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->
