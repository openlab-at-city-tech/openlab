<?php
/**
 * Primary Sidebar
 * 
 * slug: cv-portfolio-blocks/primary-sidebar
 * title: Primary Sidebar
 * categories: cv-portfolio-blocks
 */

return array(
   'title'      =>__( 'Sidebar Section', 'cv-portfolio-blocks' ),
   'categories' => array( 'cv-portfolio-blocks' ),
   'content'    =>'<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"25px"},"blockGap":"20px","padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"border":{"width":"0px","style":"none","radius":{"topLeft":"40px","topRight":"10px","bottomLeft":"10px","bottomRight":"40px"}}},"backgroundColor":"accent","className":"sidebar-box"} -->
<div class="wp-block-group sidebar-box has-accent-background-color has-background" style="border-style:none;border-width:0px;border-top-left-radius:40px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:40px;margin-bottom:25px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":{"topLeft":"40px","topRight":"10px","bottomLeft":"10px","bottomRight":"40px"},"width":"2px"}},"borderColor":"accent","backgroundColor":"background","className":"has-shadow-dark","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-shadow-dark has-border-color has-accent-border-color has-background-background-color has-background" style="border-width:2px;border-top-left-radius:40px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:40px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"style":{"border":{"top":{"style":"none","width":"0px"},"right":{"style":"none","width":"0px"},"bottom":{"color":"var:preset|color|accent","width":"3px"},"left":{"style":"none","width":"0px"}},"dimensions":{"minHeight":"0px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="border-top-style:none;border-top-width:0px;border-right-style:none;border-right-width:0px;border-bottom-color:var(--wp--preset--color--accent);border-bottom-width:3px;border-left-style:none;border-left-width:0px;min-height:0px"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"0","right":"0"}}},"textColor":"black","fontSize":"content-heading"} -->
<h3 class="wp-block-heading has-black-color has-text-color has-content-heading-font-size" style="padding-top:var(--wp--preset--spacing--30);padding-right:0;padding-bottom:var(--wp--preset--spacing--30);padding-left:0;font-style:normal;font-weight:600">'. esc_html('Search','cv-portfolio-blocks') .'</h3>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:search {"label":"Search","showLabel":false,"width":100,"widthUnit":"%","buttonText":"Search","buttonUseIcon":true,"backgroundColor":"accent","fontSize":"tiny"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"25px"},"blockGap":"20px","padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"border":{"width":"0px","style":"none","radius":{"topLeft":"40px","topRight":"10px","bottomLeft":"10px","bottomRight":"40px"}}},"backgroundColor":"accent","className":"sidebar-box"} -->
<div class="wp-block-group sidebar-box has-accent-background-color has-background" style="border-style:none;border-width:0px;border-top-left-radius:40px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:40px;margin-bottom:25px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":{"topLeft":"40px","topRight":"10px","bottomLeft":"10px","bottomRight":"40px"},"width":"2px"}},"borderColor":"accent","backgroundColor":"background","className":"has-shadow-dark","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-shadow-dark has-border-color has-accent-border-color has-background-background-color has-background" style="border-width:2px;border-top-left-radius:40px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:40px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"style":{"border":{"top":{"style":"none","width":"0px"},"right":{"style":"none","width":"0px"},"bottom":{"color":"var:preset|color|accent","width":"3px"},"left":{"style":"none","width":"0px"}},"dimensions":{"minHeight":"0px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="border-top-style:none;border-top-width:0px;border-right-style:none;border-right-width:0px;border-bottom-color:var(--wp--preset--color--accent);border-bottom-width:3px;border-left-style:none;border-left-width:0px;min-height:0px"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"0","right":"0"}}},"textColor":"black","fontSize":"content-heading"} -->
<h3 class="wp-block-heading has-black-color has-text-color has-content-heading-font-size" style="padding-top:var(--wp--preset--spacing--30);padding-right:0;padding-bottom:var(--wp--preset--spacing--30);padding-left:0;font-style:normal;font-weight:600">'. esc_html('Latest Posts','cv-portfolio-blocks') .'</h3>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":19,"query":{"perPage":"3","pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
<div class="wp-block-query"><!-- wp:post-template -->
<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20","left":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"25%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:25%"><!-- wp:post-featured-image {"isLink":true,"width":""} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"75%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:75%"><!-- wp:post-title {"isLink":true,"style":{"typography":{"lineHeight":"2.5"}},"fontSize":"upper-heading"} /-->

<!-- wp:post-excerpt {"excerptLength":10,"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"medium"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"25px"},"blockGap":"20px","padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"border":{"width":"0px","style":"none","radius":{"topLeft":"40px","topRight":"10px","bottomLeft":"10px","bottomRight":"40px"}}},"backgroundColor":"accent","className":"sidebar-box"} -->
<div class="wp-block-group sidebar-box has-accent-background-color has-background" style="border-style:none;border-width:0px;border-top-left-radius:40px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:40px;margin-bottom:25px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":{"topLeft":"40px","topRight":"10px","bottomLeft":"10px","bottomRight":"40px"},"width":"2px"}},"borderColor":"accent","backgroundColor":"background","className":"has-shadow-dark","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-shadow-dark has-border-color has-accent-border-color has-background-background-color has-background" style="border-width:2px;border-top-left-radius:40px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:40px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"style":{"border":{"top":{"style":"none","width":"0px"},"right":{"style":"none","width":"0px"},"bottom":{"color":"var:preset|color|accent","width":"3px"},"left":{"style":"none","width":"0px"}},"dimensions":{"minHeight":"0px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="border-top-style:none;border-top-width:0px;border-right-style:none;border-right-width:0px;border-bottom-color:var(--wp--preset--color--accent);border-bottom-width:3px;border-left-style:none;border-left-width:0px;min-height:0px"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"0","right":"0"}}},"textColor":"black","fontSize":"content-heading"} -->
<h3 class="wp-block-heading has-black-color has-text-color has-content-heading-font-size" style="padding-top:var(--wp--preset--spacing--30);padding-right:0;padding-bottom:var(--wp--preset--spacing--30);padding-left:0;font-style:normal;font-weight:600">'. esc_html('Archieve','cv-portfolio-blocks') .'</h3>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:archives {"style":{"typography":{"lineHeight":"1.8"}},"fontSize":"upper-heading"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"25px"},"blockGap":"20px","padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"border":{"width":"0px","style":"none","radius":{"topLeft":"40px","topRight":"10px","bottomLeft":"10px","bottomRight":"40px"}}},"backgroundColor":"accent","className":"sidebar-box"} -->
<div class="wp-block-group sidebar-box has-accent-background-color has-background" style="border-style:none;border-width:0px;border-top-left-radius:40px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:40px;margin-bottom:25px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":{"topLeft":"40px","topRight":"10px","bottomLeft":"10px","bottomRight":"40px"},"width":"2px"}},"borderColor":"accent","backgroundColor":"background","className":"has-shadow-dark","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-shadow-dark has-border-color has-accent-border-color has-background-background-color has-background" style="border-width:2px;border-top-left-radius:40px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:40px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"style":{"border":{"top":{"style":"none","width":"0px"},"right":{"style":"none","width":"0px"},"bottom":{"color":"var:preset|color|accent","width":"3px"},"left":{"style":"none","width":"0px"}},"dimensions":{"minHeight":"0px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="border-top-style:none;border-top-width:0px;border-right-style:none;border-right-width:0px;border-bottom-color:var(--wp--preset--color--accent);border-bottom-width:3px;border-left-style:none;border-left-width:0px;min-height:0px"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"0","right":"0"}}},"textColor":"black","fontSize":"content-heading"} -->
<h3 class="wp-block-heading has-black-color has-text-color has-content-heading-font-size" style="padding-top:var(--wp--preset--spacing--30);padding-right:0;padding-bottom:var(--wp--preset--spacing--30);padding-left:0;font-style:normal;font-weight:600">'. esc_html('Category','cv-portfolio-blocks') .'</h3>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:categories {"style":{"typography":{"lineHeight":"1.8"}},"fontSize":"upper-heading"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"25px"},"blockGap":"20px","padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"border":{"width":"0px","style":"none","radius":{"topLeft":"40px","topRight":"10px","bottomLeft":"10px","bottomRight":"40px"}}},"backgroundColor":"accent","className":"sidebar-box"} -->
<div class="wp-block-group sidebar-box has-accent-background-color has-background" style="border-style:none;border-width:0px;border-top-left-radius:40px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:40px;margin-bottom:25px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":{"topLeft":"40px","topRight":"10px","bottomLeft":"10px","bottomRight":"40px"},"width":"2px"}},"borderColor":"accent","backgroundColor":"background","className":"has-shadow-dark","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-shadow-dark has-border-color has-accent-border-color has-background-background-color has-background" style="border-width:2px;border-top-left-radius:40px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:40px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"style":{"border":{"top":{"style":"none","width":"0px"},"right":{"style":"none","width":"0px"},"bottom":{"color":"var:preset|color|accent","width":"3px"},"left":{"style":"none","width":"0px"}},"dimensions":{"minHeight":"0px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="border-top-style:none;border-top-width:0px;border-right-style:none;border-right-width:0px;border-bottom-color:var(--wp--preset--color--accent);border-bottom-width:3px;border-left-style:none;border-left-width:0px;min-height:0px"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"0","right":"0"}}},"textColor":"black","fontSize":"content-heading"} -->
<h3 class="wp-block-heading has-black-color has-text-color has-content-heading-font-size" style="padding-top:var(--wp--preset--spacing--30);padding-right:0;padding-bottom:var(--wp--preset--spacing--30);padding-left:0;font-style:normal;font-weight:600">'. esc_html('Social','cv-portfolio-blocks') .'</h3>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:social-links {"style":{"spacing":{"blockGap":{"top":"20px","left":"18px"}}},"className":"is-style-default","layout":{"type":"flex","justifyContent":"space-between"}} -->
<ul class="wp-block-social-links is-style-default"><!-- wp:social-link {"url":"3","service":"twitter"} /-->

<!-- wp:social-link {"url":"#","service":"youtube"} /-->

<!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"linkedin"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->'
);