<?php
/**
 * Footer Default
 * 
 * slug: cv-portfolio-blocks/footer-default
 * title: Footer Default
 * categories: cv-portfolio-blocks
 */

return array(
    'title'      =>__( 'Footer Default', 'cv-portfolio-blocks' ),
    'categories' => array( 'cv-portfolio-blocks' ),
    'content'    => '<!-- wp:group {"style":{"elements":{"link":{"color":{"text":"var:preset|color|fourground"}}}},"backgroundColor":"primary","textColor":"background","layout":{"type":"constrained","contentSize":"80%"}} -->
<div class="wp-block-group has-background-color has-primary-background-color has-text-color has-background has-link-color"><!-- wp:columns {"style":{"spacing":{"padding":{"top":"50px","bottom":"50px","right":"20px","left":"20px"}}},"className":"alignwide is-footer wow fadeInUp"} -->
<div class="wp-block-columns alignwide is-footer wow fadeInUp" style="padding-top:50px;padding-right:20px;padding-bottom:50px;padding-left:20px"><!-- wp:column {"style":{"spacing":{"blockGap":"20px"}}} -->
<div class="wp-block-column"><!-- wp:heading {"style":{"typography":{"fontSize":"22px"}},"textColor":"background"} -->
<h2 class="wp-block-heading has-background-color has-text-color" style="font-size:22px"><strong>'. esc_html('About Us','cv-portfolio-blocks') .'</strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"lineHeight":"2.2","fontStyle":"normal","fontWeight":"400"}},"className":"footer-about","fontSize":"medium","fontFamily":"poppins"} -->
<p class="footer-about has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:400;line-height:2.2">'. esc_html('Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.','cv-portfolio-blocks') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"style":{"typography":{"fontSize":"22px","fontStyle":"normal","fontWeight":"600"}},"textColor":"background"} -->
<h2 class="wp-block-heading has-background-color has-text-color" style="font-size:22px;font-style:normal;font-weight:600">'. esc_html('Quick Links','cv-portfolio-blocks') .'</h2>
<!-- /wp:heading -->

<!-- wp:navigation {"overlayMenu":"never","className":"footer-menu-box","layout":{"type":"flex","justifyContent":"left","orientation":"vertical"},"style":{"typography":{"fontStyle":"normal","fontWeight":"400"},"spacing":{"blockGap":"15px"}},"fontSize":"medium","fontFamily":"poppins"} -->
<!-- wp:navigation-link {"label":"Home","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"About","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Services","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Portfolio","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Blog","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- /wp:navigation --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"20px"}}} -->
<div class="wp-block-column"><!-- wp:heading {"style":{"typography":{"fontSize":"22px","fontStyle":"normal","fontWeight":"600"}},"textColor":"background"} -->
<h2 class="wp-block-heading has-background-color has-text-color" style="font-size:22px;font-style:normal;font-weight:600">'. esc_html('Hire Me','cv-portfolio-blocks') .'</h2>
<!-- /wp:heading -->

<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"},"style":{"typography":{"fontStyle":"normal","fontWeight":"400"},"spacing":{"blockGap":"15px"}},"fontSize":"medium","fontFamily":"poppins"} -->
<!-- wp:navigation-link {"label":"Freelancing","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"creative Process","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Design portfolio","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Design Education","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->
<!-- /wp:navigation --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"20px"}}} -->
<div class="wp-block-column"><!-- wp:heading {"style":{"typography":{"fontSize":"22px","fontStyle":"normal","fontWeight":"600"}},"textColor":"background"} -->
<h2 class="wp-block-heading has-background-color has-text-color" style="font-size:22px;font-style:normal;font-weight:600">'. esc_html('Contact Us','cv-portfolio-blocks') .'</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"left","style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"medium","fontFamily":"poppins"} -->
<p class="has-text-align-left has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:400"><span class="dashicons dashicons-email-alt"></span>  '. esc_html('support@example.com','cv-portfolio-blocks') .'</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"medium","fontFamily":"poppins"} -->
<p class="has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:400"><span class="dashicons dashicons-phone"></span>  +123 456 7890</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"medium","fontFamily":"poppins"} -->
<p class="has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:400"><span class="dashicons dashicons-admin-home"></span>  '. esc_html('123, Red Hills, Chicago,IL, USA','cv-portfolio-blocks') .'</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"backgroundColor":"accent","className":"footertext","layout":{"type":"constrained"}} -->
<div class="wp-block-group footertext has-accent-background-color has-background has-link-color"><!-- wp:paragraph {"align":"center","textColor":"background","className":"has-link-color","fontSize":"medium"} -->

<p class="has-text-align-center has-link-color has-background-color has-text-color has-medium-font-size"><a href="https://www.wpradiant.net/products/free-portfolio-wordpress-theme/">'. esc_html('Portfolio WordPress Theme ','cv-portfolio-blocks') .'</a> By <a href="https://www.wpradiant.net/">'. esc_html('WP Radiant','cv-portfolio-blocks') .'</a> | '. esc_html('Proudly powered by','cv-portfolio-blocks') .' <a href="https://wordpress.org/">'. esc_html('WordPress','cv-portfolio-blocks') .'</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->',
);