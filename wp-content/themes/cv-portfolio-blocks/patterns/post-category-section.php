<?php
/**
 * Post Category Section
 * 
 * slug: cv-portfolio-blocks/post-category-section
 * title: Post Category Section
 * categories: cv-portfolio-blocks
 */

return array(
    'title'      =>__( 'Post Category Section', 'cv-portfolio-blocks' ),
    'categories' => array( 'cv-portfolio-blocks' ),
    'content'    => '<!-- wp:spacer {"height":"50px"} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"layout":{"type":"constrained","contentSize":"80%"}} -->
<div id="about-section" class="wp-block-group"><!-- wp:columns {"verticalAlignment":"center","className":"wow fadeInUp"} -->
<div class="wp-block-columns are-vertically-aligned-center wow fadeInUp"><!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%"><!-- wp:image {"align":"right","id":17,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image alignright size-full"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/image.png" alt="" class="wp-image-17"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%"><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"textColor":"accent","fontSize":"normal","fontFamily":"poppins"} -->
<p class="has-accent-color has-text-color has-poppins-font-family has-normal-font-size" style="margin-top:0;margin-bottom:0"><strong>'. esc_html('ABOUT ME','cv-portfolio-blocks') .'</strong></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"0"}}},"textColor":"primary","fontSize":"large","fontFamily":"poppins"} -->
<h3 class="wp-block-heading has-primary-color has-text-color has-poppins-font-family has-large-font-size" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:0;font-style:normal;font-weight:700">'. esc_html('I am a UI/UX Designer Based in USA','cv-portfolio-blocks') .'</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"lineHeight":"2"},"color":{"text":"#1a1a1e"}},"fontSize":"upper-heading","fontFamily":"poppins"} -->
<p class="has-text-color has-poppins-font-family has-upper-heading-font-size" style="color:#1a1a1e;line-height:2">'. esc_html('UI Designer From USA, I Have rich Experience in ui design &amp; building and<br>customization lorem ipsum dummy text.','cv-portfolio-blocks') .'</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"lineHeight":"2"}},"fontSize":"upper-heading","fontFamily":"poppins"} -->
<p class="has-poppins-font-family has-upper-heading-font-size" style="line-height:2">'. esc_html('Lorem ipsum dolor sit amet, consectetur sed do eiusmod tencididunt ut before et dolore Lorem ipsum dolor sit amet, consectetur sed do eiusmod tencididunt ut before et dolore magnaa','cv-portfolio-blocks') .'</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"500","lineHeight":"2.5"}},"fontSize":"upper-heading","fontFamily":"poppins"} -->
<h3 class="wp-block-heading has-poppins-font-family has-upper-heading-font-size" style="font-style:normal;font-weight:500;line-height:2.5">'. esc_html('Age - 25 Years','cv-portfolio-blocks') .'<br>'. esc_html('Email - support@example.com','cv-portfolio-blocks') .'<br>'. esc_html('Freelancer - Available','cv-portfolio-blocks') .'</h3>
<!-- /wp:heading --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"50px"} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->',
);