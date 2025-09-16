<?php
/**
 * News Section
 * 
 * slug: cv-portfolio-blocks/news-section
 * title: News Section
 * categories: cv-portfolio-blocks
 */

    return array(
        'title'      =>__( 'News Section', 'cv-portfolio-blocks' ),
        'categories' => array( 'cv-portfolio-blocks' ),
        'content'    => '<!-- wp:spacer {"height":"30px"} -->
    <div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
    <!-- /wp:spacer -->

    <!-- wp:group {"className":"news-section wow bounceIn","style":{"spacing":{"padding":{"top":"0","bottom":"0"},"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained","contentSize":"80%"}} -->
    <div id="blog" class="wp-block-group news-section wow bounceIn" style="padding-top:0;padding-bottom:0"><!-- wp:paragraph {"align":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|accent"}}},"typography":{"fontStyle":"normal","fontWeight":"600"}},"textColor":"accent","fontSize":"medium","fontFamily":"poppins"} -->
    <p class="has-text-align-center has-accent-color has-text-color has-link-color has-poppins-font-family has-medium-font-size" style="font-style:normal;font-weight:600">'. esc_html__('News & Blogs','cv-portfolio-blocks').'</p>
    <!-- /wp:paragraph -->

    <!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"800","fontSize":"26px"},"elements":{"link":{"color":{"text":"var:preset|color|primary"}}}},"textColor":"primary","fontFamily":"poppins"} -->
    <h2 class="wp-block-heading has-text-align-center has-primary-color has-text-color has-link-color has-poppins-font-family" style="font-size:26px;font-style:normal;font-weight:800">'. esc_html__('Our Latest News & blogs','cv-portfolio-blocks').'</h2>
    <!-- /wp:heading -->

    <!-- wp:spacer {"height":"25px"} -->
    <div style="height:25px" aria-hidden="true" class="wp-block-spacer"></div>
    <!-- /wp:spacer -->

    <!-- wp:group {"layout":{"type":"constrained","contentSize":"100%"}} -->
    <div class="wp-block-group"><!-- wp:query {"queryId":15,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"parents":[],"format":[]},"metadata":{"categories":["posts"],"patternName":"core/query-standard-posts","name":"Standard"},"layout":{"type":"default"}} -->
    <div class="wp-block-query"><!-- wp:post-template {"className":"news-post-template","style":{"border":{"width":"0px","style":"none"}},"layout":{"type":"grid","columnCount":3,"minimumColumnWidth":null}} -->
    <!-- wp:group {"className":"news-image","layout":{"type":"constrained"}} -->
    <div class="wp-block-group news-image"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"auto","height":"300px","align":"wide"} /--></div>
    <!-- /wp:group -->

    <!-- wp:group {"className":"news-info","style":{"spacing":{"blockGap":"var:preset|spacing|30","margin":{"top":"0","bottom":"0"},"padding":{"right":"var:preset|spacing|30","left":"var:preset|spacing|30","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
    <div class="wp-block-group news-info" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
    <div class="wp-block-group"><!-- wp:post-author-name {"isLink":true} /-->

    <!-- wp:post-date {"format":"j/n/Y","isLink":true} /-->

    <!-- wp:comments {"style":{"spacing":{"padding":{"top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}}} -->
    <div class="wp-block-comments" style="margin-top:0;margin-bottom:0;padding-top:0;padding-bottom:0"><!-- wp:comments-title {"showPostTitle":false} /--></div>
    <!-- /wp:comments --></div>
    <!-- /wp:group -->

    <!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} /--></div>
    <!-- /wp:group -->
    <!-- /wp:post-template --></div>
    <!-- /wp:query --></div>
    <!-- /wp:group --></div>
    <!-- /wp:group -->

    <!-- wp:spacer {"height":"102px"} -->
    <div style="height:102px" aria-hidden="true" class="wp-block-spacer"></div>
    <!-- /wp:spacer -->',
    );