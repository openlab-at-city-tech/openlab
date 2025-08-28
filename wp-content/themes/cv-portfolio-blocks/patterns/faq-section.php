<?php
/**
 * FAQ Section
 * 
 * slug: cv-portfolio-blocks/faq-section
 * title: FAQ Section
 * categories: cv-portfolio-blocks
 */

    return array(
        'title'      =>__( 'FAQ Section', 'cv-portfolio-blocks' ),
        'categories' => array( 'cv-portfolio-blocks' ),
        'content'    => '<!-- wp:group {"className":"faq-section","style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"backgroundColor":"accent","layout":{"type":"constrained","contentSize":"80%"}} -->
      <div class="wp-block-group faq-section has-accent-background-color has-background" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30)"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
      <div class="wp-block-columns"><!-- wp:column {"className":"faq-left wow bounceInUp center","style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
      <div class="wp-block-column faq-left wow bounceInUp center"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|accent"}}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"textColor":"accent","fontSize":"medium","fontFamily":"poppins"} -->
      <p class="has-background-color has-text-color has-link-color has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:500">'. esc_html__('Frequently Asked Questions','cv-portfolio-blocks').'</p>
      <!-- /wp:paragraph -->

      <!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"800","fontSize":"26px"},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"textColor":"background","fontFamily":"poppins"} -->
      <h2 class="wp-block-heading has-background-color has-text-color has-link-color has-poppins-font-family" style="font-size:26px;font-style:normal;font-weight:800">'. esc_html__('Have Any Questions For Us?','cv-portfolio-blocks').'</h2>
      <!-- /wp:heading -->

      <!-- wp:paragraph {"className":"short-para-text","style":{"typography":{"fontStyle":"normal","fontWeight":"400","lineHeight":"1.7"},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"textColor":"background","fontSize":"extra-small","fontFamily":"poppins"} -->
      <p class="short-para-text has-background-color has-text-color has-link-color has-poppins-font-family has-extra-small-font-size" style="font-style:normal;font-weight:400;line-height:1.7">'. esc_html__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s','cv-portfolio-blocks').'</p>
      <!-- /wp:paragraph -->

      <!-- wp:image {"id":6,"sizeSlug":"full","linkDestination":"none"} -->
      <figure class="wp-block-image size-full"><img src="'.esc_url(get_template_directory_uri()) .'/assets/images/FAQ.png" alt="" class="wp-image-6"/></figure>
      <!-- /wp:image --></div>
      <!-- /wp:column -->

      <!-- wp:column {"className":"faq-right wow bounceInDown center","style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
      <div class="wp-block-column faq-right wow bounceInDown center"><!-- wp:details {"showContent":true,"className":"faq-list"} -->
      <details class="wp-block-details faq-list" open><summary>'. esc_html__('What services does your agency offer?','cv-portfolio-blocks').'</summary><!-- wp:paragraph {"placeholder":"Type / to add a hidden block"} -->
      <p>'. esc_html__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.','cv-portfolio-blocks').'</p>
      <!-- /wp:paragraph --></details>
      <!-- /wp:details -->

      <!-- wp:details {"className":"faq-list"} -->
      <details class="wp-block-details faq-list"><summary>'. esc_html__('Who do you work with?','cv-portfolio-blocks').'</summary><!-- wp:paragraph {"placeholder":"Type / to add a hidden block"} -->
      <p>'. esc_html__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.','cv-portfolio-blocks').'</p>
      <!-- /wp:paragraph --></details>
      <!-- /wp:details -->

      <!-- wp:details {"className":"faq-list"} -->
      <details class="wp-block-details faq-list"><summary>'. esc_html__('Do you offer customized marketing packages?','cv-portfolio-blocks').'</summary><!-- wp:paragraph {"placeholder":"Type / to add a hidden block"} -->
      <p>'. esc_html__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.','cv-portfolio-blocks').'</p>
      <!-- /wp:paragraph --></details>
      <!-- /wp:details -->

      <!-- wp:details {"className":"faq-list"} -->
      <details class="wp-block-details faq-list"><summary>'. esc_html__('Can you help us improve our SEO rankings?','cv-portfolio-blocks').'</summary><!-- wp:paragraph {"placeholder":"Type / to add a hidden block"} -->
      <p>'. esc_html__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.','cv-portfolio-blocks').'</p>
      <!-- /wp:paragraph --></details>
      <!-- /wp:details -->

      <!-- wp:details {"className":"faq-list"} -->
      <details class="wp-block-details faq-list"><summary>'. esc_html__('Do you manage social media accounts?','cv-portfolio-blocks').'</summary><!-- wp:paragraph {"placeholder":"Type / to add a hidden block"} -->
      <p>'. esc_html__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.','cv-portfolio-blocks').'</p>
      <!-- /wp:paragraph --></details>
      <!-- /wp:details --></div>
      <!-- /wp:column --></div>
      <!-- /wp:columns --></div>
      <!-- /wp:group -->

      <!-- wp:spacer {"height":"80px"} -->
      <div style="height:80px" aria-hidden="true" class="wp-block-spacer"></div>
      <!-- /wp:spacer -->',
    );