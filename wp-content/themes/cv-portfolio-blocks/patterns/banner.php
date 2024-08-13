<?php
/**
 * Banner Section
 * 
 * slug: cv-portfolio-blocks/banner
 * title: Banner
 * categories: cv-portfolio-blocks
 */

return array(
    'title'      =>__( 'Banner', 'cv-portfolio-blocks' ),
    'categories' => array( 'cv-portfolio-blocks' ),
    'content'    => '<!-- wp:cover {"url":"'.esc_url(get_template_directory_uri()) .'/assets/images/banner.png","id":9,"dimRatio":0,"overlayColor":"black","minHeight":650,"minHeightUnit":"px","isDark":false,"tagName":"main","className":"wp-block-group alignfull"} -->
<main class="wp-block-cover is-light wp-block-group alignfull" style="min-height:650px"><span aria-hidden="true" class="wp-block-cover__background has-black-background-color has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-9" alt="" src="'.esc_url(get_template_directory_uri()) .'/assets/images/banner.png" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:group {"className":"wow fadeInUp","layout":{"type":"constrained","contentSize":"80%"}} -->
<div class="wp-block-group wow fadeInUp"><!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"slider-banner"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center slider-banner"><!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:paragraph {"style":{"color":{"text":"#81898b"}},"fontSize":"upper-heading","fontFamily":"poppins"} -->
<p class="has-text-color has-poppins-font-family has-upper-heading-font-size" style="color:#81898b">'. esc_html('WELCOME TO MY WORLD','cv-portfolio-blocks') .'</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"style":{"typography":{"fontSize":"40px","fontStyle":"normal","fontWeight":"700"}},"textColor":"primary","className":"is-slide-heading","fontFamily":"poppins"} -->
<h2 class="wp-block-heading is-slide-heading has-primary-color has-text-color has-poppins-font-family" style="font-size:40px;font-style:normal;font-weight:700">'. esc_html('Hi, Im Brad kane','cv-portfolio-blocks') .'</h2>
<!-- /wp:heading -->

<!-- wp:heading {"style":{"typography":{"fontSize":"40px","fontStyle":"normal","fontWeight":"700","lineHeight":"0.9"}},"textColor":"primary","className":"is-slide-heading","fontFamily":"poppins"} -->
<h2 class="wp-block-heading is-slide-heading has-primary-color has-text-color has-poppins-font-family" style="font-size:40px;font-style:normal;font-weight:700;line-height:0.9">'. esc_html('A Professional Ui Developer','cv-portfolio-blocks') .'</h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"accent","textColor":"background","style":{"border":{"radius":"5px"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"className":"theme-btn","fontSize":"medium","fontFamily":"poppins"} -->
<div class="wp-block-button has-custom-font-size theme-btn has-poppins-font-family has-medium-font-size"><a class="wp-block-button__link has-background-color has-accent-background-color has-text-color has-background wp-element-button" href="#" style="border-radius:5px;padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)">'. esc_html('CONTACT ME','cv-portfolio-blocks') .'</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%"></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div></main>
<!-- /wp:cover -->',
);