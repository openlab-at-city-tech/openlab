<?php 
	
	/* Font styles */
	
	$main_font = typology_get_font_option( 'main_font' );
	$h_font = typology_get_font_option( 'h_font' );
	$nav_font = typology_get_font_option( 'nav_font' );
	$font_size_p = number_format( absint( typology_get_option( 'font_size_p' ) )/10, 1 ); 
	$font_size_h1 = number_format( absint( typology_get_option( 'font_size_h1' ) )/10, 1 );
	$font_size_h2 = number_format( absint( typology_get_option( 'font_size_h2' ) )/10, 1 );
	$font_size_h3 = number_format( absint( typology_get_option( 'font_size_h3' ) )/10, 1 );
	$font_size_h4 = number_format( absint( typology_get_option( 'font_size_h4' ) )/10, 1 );
	$font_size_h5 = number_format( absint( typology_get_option( 'font_size_h5' ) )/10, 1 );
	$font_size_h6 = number_format( absint( typology_get_option( 'font_size_h6' ) )/10, 1 );
	$font_size_cover = number_format( absint( typology_get_option( 'font_size_cover' ) )/10, 1 );
	$font_size_small = number_format( absint( typology_get_option( 'font_size_small' ) )/10, 1 );
	$font_size_nav = number_format( absint( typology_get_option( 'font_size_nav' ) )/10, 1 );
	$font_size_meta = number_format( absint( typology_get_option( 'font_size_meta' ) )/10, 1 );
	$font_size_cover_dropcap = number_format( absint( typology_get_option( 'font_size_cover_dropcap' ) )/10, 1 );
	$font_size_dropcap = number_format( absint( typology_get_option( 'font_size_dropcap' ) )/10, 1 );
	$content_paragraph_width = absint( typology_get_option( 'content-paragraph-width' ) );


	/* Colors & stylings */

	$color_content_bg = esc_attr( typology_get_option('color_content_bg') );
	$color_body_bg = typology_get_option('style') == 'material' ? esc_attr( typology_get_option('color_body_bg') ) : $color_content_bg;
	
	$header_height = absint( typology_get_option('header_height') );
	$color_cover_bg = esc_attr( typology_get_option('color_header_bg') );
	$color_cover_txt = esc_attr( typology_get_option('color_header_txt') );
	$cover_bg_opacity = esc_attr( typology_get_option('cover_bg_opacity') );

	$color_content_h = esc_attr( typology_get_option('color_content_h') );
	$color_content_txt = esc_attr( typology_get_option('color_content_txt') );
	$color_content_acc = esc_attr( typology_get_option('color_content_acc') );
	$color_content_meta = esc_attr( typology_get_option('color_content_meta') );

	$color_footer_bg = esc_attr( typology_get_option('color_footer_bg') );
	$color_footer_txt = esc_attr( typology_get_option('color_footer_txt') );
	$color_footer_acc= esc_attr( typology_get_option('color_footer_acc') );


?>

/* Typography styles */

body,
blockquote:before, q:before{
  font-family: <?php echo wp_kses_post( $main_font['font-family'] ); ?>;
  font-weight: <?php echo esc_attr( $main_font['font-weight'] ); ?>;
  <?php if ( isset( $main_font['font-style'] ) && !empty( $main_font['font-style'] ) ):?>
    font-style: <?php echo esc_attr( $main_font['font-style'] ); ?>;
  <?php endif; ?>
  <?php if ( !empty( $main_font['letter-spacing'] ) ):?>
      letter-spacing: <?php echo esc_attr( $main_font['letter-spacing'] ); ?>;
  <?php endif; ?>
}

body,
.typology-action-button .sub-menu{
	color:<?php echo esc_attr( $color_content_txt ); ?>;
}

body{
	background:<?php echo esc_attr( $color_body_bg ); ?>;
	font-size: <?php echo esc_attr( $font_size_p ); ?>rem;
}
.typology-fake-bg{
	background:<?php echo esc_attr( $color_body_bg ); ?>;
}
.typology-sidebar,
.typology-section{
	background:<?php echo esc_attr( $color_content_bg ); ?>;
}


h1, h2, h3, h4, h5, h6,
.h1, .h2, .h3, .h4, .h5, .h6,
.submit,
.mks_read_more a,
input[type="submit"],
input[type="button"],
a.mks_button,
.cover-letter,
.post-letter,
.woocommerce nav.woocommerce-pagination ul li span,
.woocommerce nav.woocommerce-pagination ul li a,
.woocommerce div.product .woocommerce-tabs ul.tabs li,
.typology-pagination a,
.typology-pagination span,
.comment-author .fn,
.post-date-month,
.typology-button-social,
.meks-instagram-follow-link a,
.mks_autor_link_wrap a,
.entry-pre-title,
.typology-button,
button,
.wp-block-cover .wp-block-cover-image-text, .wp-block-cover .wp-block-cover-text, 
.wp-block-cover h2, .wp-block-cover-image .wp-block-cover-image-text, 
.wp-block-cover-image .wp-block-cover-text, .wp-block-cover-image h2,
.wp-block-button__link,
body div.wpforms-container-full .wpforms-form input[type=submit], 
body div.wpforms-container-full .wpforms-form button[type=submit], 
body div.wpforms-container-full .wpforms-form .wpforms-page-button {
  font-family: <?php echo wp_kses_post( $h_font['font-family'] ); ?>;
  font-weight: <?php echo esc_attr( $h_font['font-weight'] ); ?>;
  <?php if ( isset( $h_font['font-style'] ) && !empty( $h_font['font-style'] ) ):?>
  font-style: <?php echo esc_attr( $h_font['font-style'] ); ?>;
  <?php endif; ?>
}
<?php if ( !empty( $h_font['letter-spacing'] ) ):?>
h1, h2, h3, h4, h5, h6,
.h1, .h2, .h3, .h4, .h5, .h6,
.wp-block-cover .wp-block-cover-image-text, .wp-block-cover .wp-block-cover-text, 
.wp-block-cover h2, .wp-block-cover-image .wp-block-cover-image-text, 
.wp-block-cover-image .wp-block-cover-text, .wp-block-cover-image h2{   
    letter-spacing: <?php echo esc_attr( $h_font['letter-spacing'] ); ?>;
}
<?php endif; ?>

.typology-header .typology-nav{
  font-family: <?php echo wp_kses_post( $nav_font['font-family'] ); ?>;
  font-weight: <?php echo esc_attr( $nav_font['font-weight'] ); ?>;
  <?php if ( isset( $nav_font['font-style'] ) && !empty( $nav_font['font-style'] ) ):?>
  font-style: <?php echo esc_attr( $nav_font['font-style'] ); ?>;
  <?php endif; ?>
  <?php if ( !empty( $nav_font['letter-spacing'] ) ):?>
      letter-spacing: <?php echo esc_attr( $nav_font['letter-spacing'] ); ?>;
  <?php endif; ?>
}
.typology-cover .entry-title,
.typology-cover h1 { 
	font-size: <?php echo esc_attr( $font_size_cover ); ?>rem;
}

h1, .h1 {
  font-size: <?php echo esc_attr( $font_size_h1 ); ?>rem;
}

h2, .h2 {
  font-size: <?php echo esc_attr( $font_size_h2 ); ?>rem;
}

h3, .h3 {
  font-size: <?php echo esc_attr( $font_size_h3 ); ?>rem;
}

h4, .h4 {
  font-size: <?php echo esc_attr( $font_size_h4 ); ?>rem;
}

h5, .h5,
.typology-layout-c.post-image-on .entry-title,
blockquote, q {
  font-size: <?php echo esc_attr( $font_size_h5 ); ?>rem;
}

h6, .h6 {
  font-size: <?php echo esc_attr( $font_size_h6 ); ?>rem;
}
.widget{
	font-size: <?php echo esc_attr( $font_size_small ); ?>rem;
}
.typology-header .typology-nav a{
	font-size: <?php echo esc_attr( $font_size_nav ); ?>rem;
}

.typology-layout-b .post-date-hidden,
.meta-item{
	font-size: <?php echo esc_attr( $font_size_meta ); ?>rem;
}

.post-letter {
	font-size: <?php echo esc_attr( $font_size_dropcap ); ?>rem;
}
.typology-layout-c .post-letter{
	height: <?php echo esc_attr( $font_size_dropcap ); ?>rem;	
}

.cover-letter {
	font-size: <?php echo esc_attr( $font_size_cover_dropcap ); ?>rem;
}


h1, h2, h3, h4, h5, h6,
.h1, .h2, .h3, .h4, .h5, .h6,
h1 a,
h2 a,
h3 a,
h4 a,
h5 a,
h6 a,
.post-date-month{
	color:<?php echo esc_attr( $color_content_h ); ?>;
}
.typology-single-sticky a{
	color:<?php echo esc_attr( $color_content_txt ); ?>;
}
.entry-title a:hover,
.typology-single-sticky a:hover{
		color:<?php echo esc_attr( $color_content_acc ); ?>;
}
.bypostauthor .comment-author:before,
#cancel-comment-reply-link:after{
	background:<?php echo esc_attr( $color_content_acc ); ?>;		
}

/* General styles */
a,
.widget .textwidget a,
.typology-layout-b .post-date-hidden{
	color: <?php echo esc_attr( $color_content_acc ); ?>;
}
.single .typology-section:first-child .section-content, .section-content-page, .section-content.section-content-a{
    max-width: <?php echo absint( $content_paragraph_width ); ?>px;
}


/* Header styles */

.typology-header{
	height:<?php echo absint( $header_height ); ?>px;
}

<?php if($header_height < 70): ?>
.typology-header.typology-header-sticky{
	height:<?php echo absint( $header_height ); ?>px;
}
<?php endif; ?>

.typology-header-sticky-on .typology-header{
	background:<?php echo esc_attr( $color_content_acc ); ?>;
}

.cover-letter{
	padding-top: <?php echo absint( $header_height ); ?>px;
}

.site-title a,
.typology-site-description{
	color: <?php echo esc_attr( $color_cover_txt ); ?>;	
}

.typology-header .typology-nav,
.typology-header .typology-nav > li > a{
	color: <?php echo esc_attr( $color_cover_txt ); ?>;
}

.typology-header .typology-nav .sub-menu a{
 	color:<?php echo esc_attr( $color_content_txt ); ?>;
}
.typology-header .typology-nav .sub-menu a:hover{
	color: <?php echo esc_attr( $color_content_acc ); ?>;
}
.typology-action-button .sub-menu ul a:before{
	background: <?php echo esc_attr( $color_content_acc ); ?>;	
}
.sub-menu .current-menu-item a{
	color:<?php echo esc_attr( $color_content_acc ); ?>;
}
.dot,
.typology-header .typology-nav .sub-menu{
	background:<?php echo esc_attr( $color_content_bg ); ?>;
}
.typology-header .typology-main-navigation .sub-menu .current-menu-ancestor > a,
.typology-header .typology-main-navigation .sub-menu .current-menu-item > a{
	color: <?php echo esc_attr( $color_content_acc ); ?>;
}

.typology-header-wide .slot-l{
	left: <?php echo absint( $header_height/2-20 ); ?>px;
}
.typology-header-wide .slot-r{
	right: <?php echo absint( $header_height/2-35 ); ?>px;
}

/* Post styles */
.meta-item,
.meta-item span,
.meta-item a,
.comment-metadata a{
	color: <?php echo esc_attr( $color_content_meta ); ?>;
}
.comment-meta .url,
.meta-item a:hover{
	color:<?php echo esc_attr( $color_content_h ); ?>;
}
.typology-post:after,
.section-title:after,
.typology-pagination:before{
	background:<?php echo typology_hex2rgba($color_content_h, 0.2); ?>;
}


.typology-layout-b .post-date-day,
.typology-outline-nav li a:hover,
.style-timeline .post-date-day{
	color:<?php echo esc_attr( $color_content_acc ); ?>;
}
.typology-layout-b .post-date:after,
blockquote:before,
q:before{
	background:<?php echo esc_attr( $color_content_acc ); ?>;	
}
.typology-sticky-c,
.typology-sticky-to-top span,
.sticky-author-date{
	color: <?php echo esc_attr( $color_content_meta ); ?>;
}

.typology-outline-nav li a{
	color: <?php echo esc_attr( $color_content_txt ); ?>;
}

.typology-post.typology-layout-b:before, .section-content-b .typology-ad-between-posts:before{
	background:<?php echo typology_hex2rgba($color_content_txt, 0.1); ?>;
}

/* Buttons styles */
.submit,
.mks_read_more a,
input[type="submit"],
input[type="button"],
a.mks_button,
.typology-button,
.submit,
.typology-button-social,
.page-template-template-authors .typology-author .typology-button-social,
.widget .mks_autor_link_wrap a,
.widget .meks-instagram-follow-link a,
.widget .mks_read_more a,
button,
body div.wpforms-container-full .wpforms-form input[type=submit], 
body div.wpforms-container-full .wpforms-form button[type=submit], 
body div.wpforms-container-full .wpforms-form .wpforms-page-button {
	color:<?php echo esc_attr( $color_content_bg ); ?>;
	background: <?php echo esc_attr( $color_content_acc ); ?>;
	border:1px solid <?php echo esc_attr( $color_content_acc ); ?>;
}
body div.wpforms-container-full .wpforms-form input[type=submit]:hover, 
body div.wpforms-container-full .wpforms-form input[type=submit]:focus, 
body div.wpforms-container-full .wpforms-form input[type=submit]:active, 
body div.wpforms-container-full .wpforms-form button[type=submit]:hover, 
body div.wpforms-container-full .wpforms-form button[type=submit]:focus, 
body div.wpforms-container-full .wpforms-form button[type=submit]:active, 
body div.wpforms-container-full .wpforms-form .wpforms-page-button:hover, 
body div.wpforms-container-full .wpforms-form .wpforms-page-button:active, 
body div.wpforms-container-full .wpforms-form .wpforms-page-button:focus {
	color:<?php echo esc_attr( $color_content_bg ); ?>;
	background: <?php echo esc_attr( $color_content_acc ); ?>;
	border:1px solid <?php echo esc_attr( $color_content_acc ); ?>;  
}
.page-template-template-authors .typology-author .typology-icon-social:hover {
	border:1px solid <?php echo esc_attr( $color_content_acc ); ?>;
}
.button-invert{
	color:<?php echo esc_attr( $color_content_acc ); ?>;
	background:transparent;
}
.widget .mks_autor_link_wrap a:hover,
.widget .meks-instagram-follow-link a:hover,
.widget .mks_read_more a:hover{
	color:<?php echo esc_attr( $color_content_bg ); ?>;
}


/* Cover styles */
.typology-cover{
	min-height: <?php echo absint( $header_height + 130 ); ?>px;
}
.typology-cover-empty{
	height:<?php echo absint( $header_height * 1.9 ); ?>px;
	min-height:<?php echo absint( $header_height * 1.9 ); ?>px;
}
.typology-fake-bg .typology-section:first-child {
	top: -<?php echo absint( ($header_height * 1.9) - $header_height ); ?>px;
}
.typology-flat .typology-cover-empty{
	height:<?php echo absint( $header_height ); ?>px;
}
.typology-flat .typology-cover{
	min-height:<?php echo absint( $header_height ); ?>px;
}

.typology-cover-empty,
.typology-cover,
.typology-header-sticky{
	<?php echo typology_get_cover_background_color(); ?>;	
}

.typology-cover-overlay:after{
	background: <?php echo typology_hex2rgba($color_cover_bg, $cover_bg_opacity); ?>;
}
.typology-sidebar-header{
	background:<?php echo esc_attr( $color_cover_bg ); ?>;	
}

.typology-cover,
.typology-cover .entry-title,
.typology-cover .entry-title a,
.typology-cover .meta-item,
.typology-cover .meta-item span,
.typology-cover .meta-item a,
.typology-cover h1,
.typology-cover h2,
.typology-cover h3{
	color: <?php echo esc_attr( $color_cover_txt ); ?>;
}


.typology-cover .typology-button{
	color: <?php echo esc_attr( $color_cover_bg ); ?>;
	background:<?php echo esc_attr( $color_cover_txt ); ?>;
	border:1px solid <?php echo esc_attr( $color_cover_txt ); ?>;
}
.typology-cover .button-invert{
	color: <?php echo esc_attr( $color_cover_txt ); ?>;
	background: transparent;
}
.typology-cover-slider .owl-dots .owl-dot span{
	background:<?php echo esc_attr( $color_cover_txt ); ?>;
}


/* Widgets */
.typology-outline-nav li:before,
.widget ul li:before{
	background:<?php echo esc_attr( $color_content_acc ); ?>;
}
.widget a{
	color:<?php echo esc_attr( $color_content_txt ); ?>;
}
.widget a:hover,
.widget_calendar table tbody td a,
.entry-tags a:hover,
.wp-block-tag-cloud a:hover{
	color:<?php echo esc_attr( $color_content_acc ); ?>;
}
.widget_calendar table tbody td a:hover,
.widget table td,
.entry-tags a,
.wp-block-tag-cloud a{
	color:<?php echo esc_attr( $color_content_txt ); ?>;
}
.widget table,
.widget table td,
.widget_calendar table thead th,
table,
td, th{
	border-color: <?php echo typology_hex2rgba($color_content_txt, 0.3); ?>;
}
.widget ul li,
.widget .recentcomments{
	color:<?php echo esc_attr( $color_content_txt ); ?>;	
}
.widget .post-date{
	color:<?php echo esc_attr( $color_content_meta ); ?>;
}
#today{
	background:<?php echo typology_hex2rgba($color_content_txt, 0.1); ?>;
}

/* Pagination styles */

.typology-pagination .current, .typology-pagination .infinite-scroll a, 
.typology-pagination .load-more a, 
.typology-pagination .nav-links .next, 
.typology-pagination .nav-links .prev, 
.typology-pagination .next a, 
.typology-pagination .prev a{
	color: <?php echo esc_attr( $color_content_bg ); ?>;
	background:<?php echo esc_attr( $color_content_h ); ?>;
}
.typology-pagination a, .typology-pagination span{
	color: <?php echo esc_attr( $color_content_h ); ?>;
	border:1px solid <?php echo esc_attr( $color_content_h ); ?>;
}

/* Footer styles */
.typology-footer{
	background:<?php echo esc_attr( $color_footer_bg ); ?>;
	color:<?php echo esc_attr( $color_footer_txt ); ?>;
}
.typology-footer h1,
.typology-footer h2,
.typology-footer h3,
.typology-footer h4,
.typology-footer h5,
.typology-footer h6,
.typology-footer .post-date-month{
	color:<?php echo esc_attr( $color_footer_txt ); ?>;
}

.typology-count{
	background: <?php echo esc_attr( $color_content_acc ); ?>;	
}

.typology-footer a, .typology-footer .widget .textwidget a{
	color: <?php echo esc_attr( $color_footer_acc ); ?>;	
}


/* Border styles */

input[type="text"], input[type="email"],input[type=search], input[type="url"], input[type="tel"], input[type="number"], input[type="date"], input[type="password"], textarea, select{
	border-color:<?php echo typology_hex2rgba($color_content_txt, 0.2); ?>;
}

blockquote:after, blockquote:before, q:after, q:before{
    -webkit-box-shadow: 0 0 0 10px <?php echo esc_attr( $color_content_bg ); ?>;	
    box-shadow: 0 0 0 10px <?php echo esc_attr( $color_content_bg ); ?>;		
}

pre,
.entry-content #mc_embed_signup{
	background: <?php echo typology_hex2rgba($color_content_txt, 0.1); ?>;		
}

/* Blocks styles */
.wp-block-button__link{
	background: <?php echo esc_attr( $color_content_acc ); ?>;
	color: <?php echo esc_attr($color_content_bg); ?>; 
}

.wp-block-image figcaption,
.wp-block-audio figcaption{
  color: <?php echo esc_attr($color_content_txt); ?>;  
}
.wp-block-pullquote:not(.is-style-solid-color) blockquote{
	border-top:2px solid <?php echo esc_attr($color_content_txt); ?>;  
	border-bottom:2px solid <?php echo esc_attr($color_content_txt); ?>;  
}

.wp-block-pullquote.is-style-solid-color{
	background: <?php echo esc_attr( $color_content_acc ); ?>;
	color: <?php echo esc_attr($color_content_bg); ?>; 	
}
.wp-block-separator{
	border-color: <?php echo typology_hex2rgba($color_content_txt, 0.3); ?>;	
}

<?php if( $color_footer_bg !=  $color_body_bg ): ?>
.typology-footer .container > .col-lg-4{
	margin-top:8rem;
}
<?php endif; ?>

body.wp-editor{
	background:<?php echo esc_attr( $color_content_bg ); ?>;
}

<?php

/* Editor font sizes */
$font_sizes = typology_get_editor_font_sizes();

if ( !empty( $font_sizes ) ) {
	
	foreach ( $font_sizes as $id => $item ) {  
		if( isset( $item['size-mobile'] ) ){
        	echo '.has-'. $item['slug'] .'-font-size{ font-size: '.number_format( $item['size-mobile'] / 10,  1 ) .'rem;}';
		}
    }

	echo '@media(min-width: 801px){'; 
    foreach ( $font_sizes as $id => $item ) {  
        	echo '.has-'. $item['slug'] .'-font-size{ font-size: '.number_format( $item['size'] / 10,  1 ) .'rem;}';
    }
    echo '}';
}

/* Editor colors */
$colors = typology_get_editor_colors();

if ( !empty( $colors ) ) {
    foreach ( $colors as $id => $item ) {  
        	echo '.has-'. $item['slug'] .'-background-color{ background-color: ' . esc_attr($item['color']) .';}';
        	echo '.has-'. $item['slug'] .'-color{ color: ' . esc_attr($item['color']) .';}';
    }
}

/* Apply uppercase options */

$uppercase = typology_get_option( 'uppercase' );
if ( !empty( $uppercase ) ) {
  foreach ( $uppercase as $text_class => $val ) {
    if ( $val ){
      echo esc_attr( $text_class.'{text-transform: uppercase;}' );
    } else {
      echo esc_attr( $text_class.'{text-transform: none;}' );
    }
  }
}

?>

<?php if(!array_key_exists('.typology-button', $uppercase) || $uppercase['.typology-button'] ): ?>
	
.submit,
.mks_read_more a,
input[type="submit"],
input[type="button"],
a.mks_button,
.typology-button,
.widget .mks_autor_link_wrap a,
.widget .meks-instagram-follow-link a,
.widget .mks_read_more a,
button,
.typology-button-social,
.wp-block-button__link,
body div.wpforms-container-full .wpforms-form input[type=submit], 
body div.wpforms-container-full .wpforms-form button[type=submit], 
body div.wpforms-container-full .wpforms-form .wpforms-page-button {
	text-transform: uppercase;
}

<?php endif; ?>

/* WooCommerce styles */
<?php if ( typology_is_woocommerce_active() ): ?>
.woocommerce ul.products li.product .button,
.woocommerce ul.products li.product .added_to_cart,
body.woocommerce .button,
body.woocommerce-page .button,
.woocommerce .widget_shopping_cart_content .buttons .button,
.woocommerce div.product div.summary .single_add_to_cart_button,
.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover,
.woocommerce-cart .wc-proceed-to-checkout a.checkout-button,
.woocommerce-cart .wc-proceed-to-checkout a.checkout-button:hover,
.woocommerce-page #payment #place_order,
.woocommerce #review_form #respond .form-submit input,
.price, .amount,
.woocommerce .comment-reply-title{
	font-family: <?php echo wp_kses_post( $h_font['font-family'] ); ?>;
	font-weight: <?php echo esc_attr( $h_font['font-weight'] ); ?>;
	<?php if ( isset( $h_font['font-style'] ) && !empty( $h_font['font-style'] ) ):?>
	font-style: <?php echo esc_attr( $h_font['font-style'] ); ?>;
	<?php endif; ?> 	
}
.woocommerce ul.products li.product .button,
.woocommerce ul.products li.product .added_to_cart,
body.woocommerce .button,
body.woocommerce-page .button,
.woocommerce .widget_shopping_cart_content .buttons .button,
.woocommerce div.product div.summary .single_add_to_cart_button,
.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover,
.woocommerce-cart .wc-proceed-to-checkout a.checkout-button,
.woocommerce-cart .wc-proceed-to-checkout a.checkout-button:hover,
.woocommerce-page #payment #place_order,
.woocommerce #review_form #respond .form-submit input{
	color:<?php echo esc_attr( $color_content_bg ); ?>;
	background: <?php echo esc_attr( $color_content_acc ); ?>;
	border:1px solid <?php echo esc_attr( $color_content_acc ); ?>; 
}
.woocommerce div.product .woocommerce-tabs ul.tabs li.active a{
  border-bottom: 3px solid <?php echo esc_attr( $color_content_acc ); ?>;
}
.product-categories li,
.product-categories .children li {
  color:<?php echo esc_attr( $color_content_meta ); ?>;
}
.product-categories .children li {
  border-top: 1px solid <?php echo typology_hex2rgba( $color_content_txt, 0.1); ?>; 
}
.product-categories li{
   border-bottom: 1px solid <?php echo typology_hex2rgba( $color_content_txt, 0.1); ?>; 
}	

.woocommerce nav.woocommerce-pagination ul li a,
.woocommerce nav.woocommerce-pagination ul li span{
	color: <?php echo esc_attr( $color_content_bg ); ?>;
	background:<?php echo esc_attr( $color_content_h ); ?>;
}

.woocommerce nav.woocommerce-pagination ul li a,
.woocommerce nav.woocommerce-pagination ul li span{
	color: <?php echo esc_attr( $color_content_h ); ?>;
	border:1px solid <?php echo esc_attr( $color_content_h ); ?>;
	background: transparent;
}
.woocommerce nav.woocommerce-pagination ul li a:hover{
	color: <?php echo esc_attr( $color_content_h ); ?>;
}
.woocommerce nav.woocommerce-pagination ul li span.current{
	color: <?php echo esc_attr( $color_content_bg ); ?>;
	background:<?php echo esc_attr( $color_content_h ); ?>;	
}
.woocommerce .comment-reply-title:after{
	background:<?php echo typology_hex2rgba($color_content_h, 0.2); ?>;	
}

<?php endif; ?>

<?php if ( typology_is_co_authors_active() ): ?>
	.coauthors {
		display: inline;
	}
	.meta-author a {
		margin-left: 5px;
	}
	.typology-co-author-section .container {
		margin-bottom: 6rem;
	}
	.typology-co-author-section .container:last-child {
		margin-bottom: 0;
	}
	.typology-sticky-author {
		max-width: 450px;
	}

<?php endif; ?>