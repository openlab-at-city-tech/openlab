<?php
/**
 * About Us Section
 * 
 * slug: cv-portfolio-blocks/about-us-section
 * title: About Us Section
 * categories: cv-portfolio-blocks
 */

    return array(
        'title'      =>__( 'About Us Section', 'cv-portfolio-blocks' ),
        'categories' => array( 'cv-portfolio-blocks' ),
        'content'    => '<!-- wp:spacer {"height":"80px"} -->
        <div style="height:80px" aria-hidden="true" class="wp-block-spacer"></div>
        <!-- /wp:spacer -->

        <!-- wp:group {"className":"about-us-section","backgroundColor":"accent","layout":{"type":"constrained","contentSize":"80%"}} -->
        <div id="aboutus" class="wp-block-group about-us-section has-accent-background-color has-background"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
        <div class="wp-block-columns"><!-- wp:column {"className":"about-us-col01 wow bounceInDown center"} -->
        <div class="wp-block-column about-us-col01 wow bounceInDown center"><!-- wp:columns -->
        <div class="wp-block-columns"><!-- wp:column {"width":"50%"} -->
        <div class="wp-block-column" style="flex-basis:50%"><!-- wp:image {"id":20,"sizeSlug":"full","linkDestination":"none","className":"about-img01","style":{"border":{"radius":"10px"}}} -->
        <figure class="wp-block-image size-full has-custom-border about-img01"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/about01.png" alt="" class="wp-image-20" style="border-radius:10px"/></figure>
        <!-- /wp:image -->

        <!-- wp:image {"id":9,"sizeSlug":"full","linkDestination":"none","align":"right","className":"about-img02","style":{"border":{"radius":"10px"}}} -->
        <figure class="wp-block-image alignright size-full has-custom-border about-img02"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/about02.png" alt="" class="wp-image-9" style="border-radius:10px"/></figure>
        <!-- /wp:image --></div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:image {"id":10,"sizeSlug":"full","linkDestination":"none","className":"about-img03","style":{"border":{"radius":"10px"}}} -->
        <figure class="wp-block-image size-full has-custom-border about-img03"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/about03.png" alt="" class="wp-image-10" style="border-radius:10px"/></figure>
        <!-- /wp:image --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns --></div>
        <!-- /wp:column -->

        <!-- wp:column {"className":"about-us-col02 wow bounceInUp center","style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
        <div class="wp-block-column about-us-col02 wow bounceInUp center"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|accent"}}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"textColor":"accent","fontSize":"medium","fontFamily":"poppins"} -->
        <p class="has-background-color has-text-color has-link-color has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:600">'. esc_html__('About Us','cv-portfolio-blocks').'</p>
        <!-- /wp:paragraph -->

        <!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"800","fontSize":"28px","textTransform":"capitalize"},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"textColor":"background","fontFamily":"poppins"} -->
         <h2 class="wp-block-heading has-background-color has-text-color has-link-color has-poppins-font-family" style="font-size:28px;font-style:normal;font-weight:800;text-transform:capitalize">'. esc_html__('We want to give you the best services','cv-portfolio-blocks').'</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400","lineHeight":"1.8"},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"textColor":"background","fontSize":"extra-small","fontFamily":"poppins"} -->
         <p class="has-background-color has-text-color has-link-color has-poppins-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400;line-height:1.8">'. esc_html__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s','cv-portfolio-blocks').'</p>
        <!-- /wp:paragraph -->

        <!-- wp:columns {"className":"about-col02-list","style":{"spacing":{"margin":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|30"}}}} -->
        <div class="wp-block-columns about-col02-list" style="margin-top:var(--wp--preset--spacing--60);margin-bottom:var(--wp--preset--spacing--30)"><!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
        <div class="wp-block-column"><!-- wp:image {"id":12,"sizeSlug":"full","linkDestination":"none"} -->
        <figure class="wp-block-image size-full"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/about-icon01.png" alt="" class="wp-image-12"/></figure>
        <!-- /wp:image -->

        <!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"19px","textTransform":"capitalize"},"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"spacing":{"margin":{"top":"var:preset|spacing|40"}}},"textColor":"background","fontFamily":"poppins"} -->
         <h2 class="wp-block-heading has-background-color has-text-color has-link-color has-poppins-font-family" style="margin-top:var(--wp--preset--spacing--40);font-size:19px;font-style:normal;font-weight:700;text-transform:capitalize">'. esc_html__('Guaranteed Results','cv-portfolio-blocks').'</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400","lineHeight":"1.5"},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"textColor":"background","fontSize":"extra-small","fontFamily":"poppins"} -->
         <p class="has-background-color has-text-color has-link-color has-poppins-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400;line-height:1.5">'. esc_html__('Lorem Ipsum is simply dummy text of the printing and typesetting industry.','cv-portfolio-blocks').'</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column -->

        <!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
        <div class="wp-block-column"><!-- wp:image {"id":13,"sizeSlug":"full","linkDestination":"none"} -->
        <figure class="wp-block-image size-full"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/about-icon02.png" alt="" class="wp-image-13"/></figure>
        <!-- /wp:image -->

        <!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"19px","textTransform":"capitalize"},"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"spacing":{"margin":{"top":"var:preset|spacing|40"}}},"textColor":"background","fontFamily":"poppins"} -->
         <h2 class="wp-block-heading has-background-color has-text-color has-link-color has-poppins-font-family" style="margin-top:var(--wp--preset--spacing--40);font-size:19px;font-style:normal;font-weight:700;text-transform:capitalize">'. esc_html__('Quality Services','cv-portfolio-blocks').'</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400","lineHeight":"1.5"},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"textColor":"background","fontSize":"extra-small","fontFamily":"poppins"} -->
        <p class="has-background-color has-text-color has-link-color has-poppins-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400;line-height:1.5">'. esc_html__('Lorem Ipsum is simply dummy text of the printing and typesetting industry.','cv-portfolio-blocks').'</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns --></div>
        <!-- /wp:group -->

        <!-- wp:spacer {"height":"70px"} -->
        <div style="height:70px" aria-hidden="true" class="wp-block-spacer"></div>
        <!-- /wp:spacer -->',
    );