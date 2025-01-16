<?php

/**
 * Title: Header Media
 * Slug: fse-freelancer-portfolio/main-banner
 */

?>

<!-- wp:cover {"url":"<?php echo get_parent_theme_file_uri( '/assets/images/slider.png' ); ?>","id":12,"dimRatio":0,"minHeight":600,"minHeightUnit":"px","isDark":false,"align":"full","style":{"border":{"radius":"0px"}},"className":"slide2"} -->
<div class="wp-block-cover alignfull is-light slide2" style="border-radius:0px;min-height:600px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-12" alt="" src="<?php echo get_parent_theme_file_uri( '/assets/images/slider.png' ); ?>" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:group {"layout":{"type":"constrained","contentSize":"90%"}} -->
<div class="wp-block-group"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:heading {"level":4,"style":{"typography":{"fontStyle":"normal","fontWeight":"500"},"spacing":{"margin":{"top":"0","bottom":"0"}},"elements":{"link":{"color":{"text":"#181719"}}},"color":{"text":"#181719"}},"fontSize":"extra-large","fontFamily":"poppins"} -->
<h4 class="wp-block-heading has-text-color has-link-color has-poppins-font-family has-extra-large-font-size" style="color:#181719;margin-top:0;margin-bottom:0;font-style:normal;font-weight:500"><?php esc_html_e('Hello, I am','fse-freelancer-portfolio'); ?></h4>
<!-- /wp:heading -->

<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"85px"},"color":{"text":"#181719"}},"fontFamily":"poppins"} -->
<h2 class="wp-block-heading has-text-color has-poppins-font-family" style="color:#181719;font-size:85px;font-style:normal;font-weight:700"><?php esc_html_e('Dana Smith','fse-freelancer-portfolio'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"color":{"text":"#828284"},"spacing":{"margin":{"top":"0","bottom":"0","left":"0","right":"0"}}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#828284;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0"><?php esc_html_e('Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.','fse-freelancer-portfolio'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"primary","textColor":"white","style":{"border":{"radius":"8px"},"spacing":{"padding":{"left":"var:preset|spacing|50","right":"var:preset|spacing|50","top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"fontSize":"small","fontFamily":"inter"} -->
<div class="wp-block-button has-custom-font-size has-inter-font-family has-small-font-size"><a class="wp-block-button__link has-white-color has-primary-background-color has-text-color has-background wp-element-button" style="border-radius:8px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--50)"><?php esc_html_e('Download CV','fse-freelancer-portfolio'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:social-links {"customIconColor":"#181719","iconColorValue":"#181719","openInNewTab":true,"size":"has-small-icon-size","style":{"spacing":{"margin":{"right":"0","left":"0","top":"var:preset|spacing|50","bottom":"0"}}},"className":"is-style-logos-only header-social","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
<ul class="wp-block-social-links has-small-icon-size has-icon-color is-style-logos-only header-social" style="margin-top:var(--wp--preset--spacing--50);margin-right:0;margin-bottom:0;margin-left:0"><!-- wp:social-link {"url":"@","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"dribbble"} /-->

<!-- wp:social-link {"url":"#","service":"twitter"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->