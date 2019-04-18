<?php

//Fonts
$main_font = johannes_get_option( 'main_font', 'font' );
$h_font = johannes_get_option( 'h_font', 'font' );
$nav_font = johannes_get_option( 'nav_font', 'font' );
$button_font = johannes_get_option( 'button_font', 'font' );

//Font sizes
$font_size_p = number_format( absint( johannes_get_option( 'font_size_p' ) ) / 10,  1 );
$font_size_small = number_format( absint( johannes_get_option( 'font_size_small' ) ) / 10,  1 );
$font_size_nav = number_format( absint( johannes_get_option( 'font_size_nav' ) ) / 10,  1 );
$font_size_nav_ico = number_format( absint( johannes_get_option( 'font_size_nav_ico' ) ) / 10,  1 );
$font_size_section_title = number_format( absint( johannes_get_option( 'font_size_section_title' ) ) / 10,  1 );
$font_size_widget_title = number_format( absint( johannes_get_option( 'font_size_widget_title' ) ) / 10,  1 );
$font_size_punchline = number_format( absint( johannes_get_option( 'font_size_punchline' ) ) / 10,  1 );
$font_size_h1 = number_format( absint( johannes_get_option( 'font_size_h1' ) ) / 10,  1 );
$font_size_h2 = number_format( absint( johannes_get_option( 'font_size_h2' ) ) / 10,  1 );
$font_size_h3 = number_format( absint( johannes_get_option( 'font_size_h3' ) ) / 10,  1 );
$font_size_h4 = number_format( absint( johannes_get_option( 'font_size_h4' ) ) / 10,  1 );
$font_size_h5 = number_format( absint( johannes_get_option( 'font_size_h5' ) ) / 10,  1 );
$font_size_h6 = number_format( absint( johannes_get_option( 'font_size_h6' ) ) / 10,  1 );

//Colors
$color_header_top_bg = johannes_get_option('color_header_top_bg');
$color_header_top_txt = johannes_get_option('color_header_top_txt');
$color_header_top_acc = johannes_get_option('color_header_top_acc');

$color_header_middle_bg = johannes_get_option('color_header_middle_bg');
$color_header_middle_txt = johannes_get_option('color_header_middle_txt');
$color_header_middle_acc = johannes_get_option('color_header_middle_acc');
$color_header_middle_bg_multi = johannes_get_option('color_header_middle_bg_multi');

$color_header_bottom_bg = johannes_get_option('color_header_bottom_bg');
$color_header_bottom_txt = johannes_get_option('color_header_bottom_txt');
$color_header_bottom_acc = johannes_get_option('color_header_bottom_acc');

$color_header_sticky_bg = johannes_get_option('color_header_sticky_bg');
$color_header_sticky_txt = johannes_get_option('color_header_sticky_txt');
$color_header_sticky_acc = johannes_get_option('color_header_sticky_acc');

$color_bg = johannes_get_option('color_bg');
$color_h = johannes_get_option('color_h');
$color_txt = johannes_get_option('color_txt');
$color_acc = johannes_get_option('color_acc');
$color_meta = johannes_get_option('color_meta');
$color_bg_alt_1 = johannes_get_option('color_bg_alt_1');
$color_bg_alt_2 = johannes_get_option('color_bg_alt_2');

$color_footer_bg = johannes_get_option('color_footer_bg');
$color_footer_txt = johannes_get_option('color_footer_txt');
$color_footer_acc = johannes_get_option('color_footer_acc');
$color_footer_meta = johannes_get_option('color_footer_meta');

$color_dark_txt = johannes_is_color_light( $color_bg ) ? $color_txt : $color_bg;
$color_button_txt = johannes_is_color_light( $color_acc ) ? $color_dark_txt : '#fff';
$color_button_hover = johannes_is_color_light( $color_txt ) ? $color_dark_txt : '#fff';

//Other
$header_height = absint( johannes_get_option('header_height') );

//Grid vars
$grid = johannes_grid_vars();

?>

body{
	font-family: <?php echo wp_kses_post( $main_font['font-family'] ); ?>, Arial, sans-serif;
	font-weight: <?php echo esc_attr( $main_font['font-weight'] ); ?>;
	<?php if ( isset( $main_font['font-style'] ) && !empty( $main_font['font-style'] ) ):?>
	font-style: <?php echo esc_attr( $main_font['font-style'] ); ?>;
	<?php endif; ?>
	color: <?php echo esc_attr( $color_txt ); ?>;
	background: <?php echo esc_attr( $color_bg ); ?>;
}
.johannes-header{
	font-family: <?php echo wp_kses_post( $nav_font['font-family'] ); ?>, Arial, sans-serif;
	font-weight: <?php echo esc_attr( $nav_font['font-weight'] ); ?>;
	<?php if ( isset( $nav_font['font-style'] ) && !empty( $nav_font['font-style'] ) ):?>
	font-style: <?php echo esc_attr( $nav_font['font-style'] ); ?>;
	<?php endif; ?>
}

h1,
h2,
h3,
h4,
h5,
h6,
.h1,
.h2,
.h3,
.h4,
.h5,
.h6,
.h0,
.display-1,
.wp-block-cover .wp-block-cover-image-text, 
.wp-block-cover .wp-block-cover-text, 
.wp-block-cover h2, 
.wp-block-cover-image .wp-block-cover-image-text, 
.wp-block-cover-image .wp-block-cover-text, 
.wp-block-cover-image h2,
.entry-category a,
.single-md-content .entry-summary,
p.has-drop-cap:not(:focus)::first-letter,
.johannes_posts_widget .entry-header > a {
	font-family: <?php echo wp_kses_post( $h_font['font-family'] ); ?>, Arial, sans-serif;
	font-weight: <?php echo esc_attr( $h_font['font-weight'] ); ?>;
	<?php if ( isset( $h_font['font-style'] ) && !empty( $h_font['font-style'] ) ):?>
	font-style: <?php echo esc_attr( $h_font['font-style'] ); ?>;
	<?php endif; ?>
}
b,
strong,
.entry-tags a,
.entry-category a,
.entry-meta a{
	font-weight: <?php echo esc_attr( $h_font['font-weight'] ); ?>; 
}
.header-top{
	background: <?php echo esc_attr( $color_header_top_bg ); ?>;
	color: <?php echo esc_attr( $color_header_top_txt ); ?>;
}
.header-top nav > ul > li > a,
.header-top .johannes-menu-social a{
	color: <?php echo esc_attr( $color_header_top_txt ); ?>;		
}
.header-top nav > ul > li:hover > a,
.header-top nav > ul > li.current-menu-item > a,
.header-top .johannes-menu-social li:hover a{
	color: <?php echo esc_attr( $color_header_top_acc ); ?>;		
}


/* Header middle */
.header-middle,
.header-mobile{
	color: <?php echo esc_attr( $color_header_middle_txt ); ?>;	
	background: <?php echo esc_attr( $color_header_middle_bg ); ?>;	
}
.header-middle > .container {
	height: <?php echo esc_attr( $header_height ); ?>px;
}

.header-middle a,
.johannes-mega-menu .sub-menu li:hover a,
.header-mobile a{
	color: <?php echo esc_attr( $color_header_middle_txt ); ?>;	
}
.header-middle li:hover > a,
.header-middle .current-menu-item > a,
.header-middle .johannes-mega-menu .sub-menu li a:hover,
.header-middle .johannes-site-branding .site-title a:hover,
.header-mobile .site-title a,
.header-mobile a:hover{
	color: <?php echo esc_attr( $color_header_middle_acc ); ?>;	
}
.header-middle .johannes-site-branding .site-title a{
	color: <?php echo esc_attr( $color_header_middle_txt ); ?>;
}
.header-middle .sub-menu{
	background: <?php echo esc_attr( $color_header_middle_bg ); ?>;		
}
.johannes-cover-indent .header-middle .johannes-menu>li>a:hover,
.johannes-cover-indent .header-middle .johannes-menu-action a:hover{
	color: <?php echo esc_attr( $color_header_middle_acc ); ?>;		
}

/* Header sticky */
.header-sticky-main{
	color: <?php echo esc_attr( $color_header_middle_txt ); ?>;	
	background: <?php echo esc_attr( $color_header_middle_bg ); ?>;	
}

.header-sticky-main a,
.header-sticky-main .johannes-mega-menu .sub-menu li:hover a{
	color: <?php echo esc_attr( $color_header_middle_txt ); ?>;	
}
.header-sticky-main li:hover > a,
.header-sticky-main .current-menu-item > a,
.header-sticky-main .johannes-mega-menu .sub-menu li a:hover,
.header-sticky-main .johannes-site-branding .site-title a:hover{
	color: <?php echo esc_attr( $color_header_middle_acc ); ?>;	
}
.header-sticky-main .johannes-site-branding .site-title a{
	color: <?php echo esc_attr( $color_header_middle_txt ); ?>;	
}
.header-sticky-main .sub-menu{
	background: <?php echo esc_attr( $color_header_middle_bg ); ?>;		
}

.header-sticky-contextual{
	color: <?php echo esc_attr( $color_header_middle_bg ); ?>;	
	background: <?php echo esc_attr( $color_header_middle_txt ); ?>;	
}
.header-sticky-contextual,
.header-sticky-contextual a{
	font-family: <?php echo wp_kses_post( $main_font['font-family'] ); ?>, Arial, sans-serif;
	font-weight: <?php echo esc_attr( $main_font['font-weight'] ); ?>;
	<?php if ( isset( $main_font['font-style'] ) && !empty( $main_font['font-style'] ) ):?>
	font-style: <?php echo esc_attr( $main_font['font-style'] ); ?>;
	<?php endif; ?>
}
.header-sticky-contextual a{
	color: <?php echo esc_attr( $color_header_middle_bg ); ?>;	
}
.header-sticky-contextual .meta-comments:after{
	background: <?php echo esc_attr( $color_header_middle_bg ); ?>;		
}
.header-sticky-contextual .meks_ess a:hover{
	color: <?php echo esc_attr( $color_header_middle_acc ); ?>;	
	background: transparent;
}

/* Header bottom */
.header-bottom{
	color: <?php echo esc_attr( $color_header_bottom_txt ); ?>;	
	background: <?php echo esc_attr( $color_header_bottom_bg ); ?>;	
	border-top: 1px solid <?php echo esc_attr( johannes_hex_to_rgba( $color_header_bottom_txt, 0.1 ) ); ?>;
	border-bottom: 1px solid <?php echo esc_attr( johannes_hex_to_rgba( $color_header_bottom_txt, 0.1 ) ); ?>;
}
.johannes-header-bottom-boxed .header-bottom{
	background: transparent;	
	border: none;
}
.johannes-header-bottom-boxed .header-bottom-slots{
	background: <?php echo esc_attr( $color_header_bottom_bg ); ?>;	
	border-top: 1px solid <?php echo esc_attr( johannes_hex_to_rgba( $color_header_bottom_txt, 0.1 ) ); ?>;
	border-bottom: 1px solid <?php echo esc_attr( johannes_hex_to_rgba( $color_header_bottom_txt, 0.1 ) ); ?>;
}
.header-bottom-slots{
	height: 70px;
}
.header-bottom a,
.johannes-mega-menu .sub-menu li:hover a{
	color: <?php echo esc_attr( $color_header_bottom_txt ); ?>;	
}
.header-bottom li:hover > a,
.header-bottom .current-menu-item > a,
.header-bottom .johannes-mega-menu .sub-menu li a:hover,
.header-bottom .johannes-site-branding .site-title a:hover{
	color: <?php echo esc_attr( $color_header_bottom_acc ); ?>;	
}
.header-bottom .johannes-site-branding .site-title a{
	color: <?php echo esc_attr( $color_header_bottom_txt ); ?>;		
}
.header-bottom .sub-menu{
	background: <?php echo esc_attr( $color_header_bottom_bg ); ?>;		
}

.johannes-menu-action .search-form input[type=text]{
	background: <?php echo esc_attr( $color_bg ); ?>;			
}

.johannes-header-multicolor .header-middle .slot-l,
.johannes-header-multicolor .header-sticky .header-sticky-main .container > .slot-l,
.johannes-header-multicolor .header-mobile .slot-l,
.johannes-header-multicolor .slot-l .johannes-site-branding:after{
	background: <?php echo esc_attr( $color_header_middle_bg_multi ); ?>;
}



.johannes-cover-indent .johannes-cover{
    min-height: 450px;
}
.page.johannes-cover-indent .johannes-cover{
	min-height: 250px;
}
.single.johannes-cover-indent .johannes-cover {
    min-height: 350px;
}
/* Header responsive sizes */
@media (min-width: 900px) and (max-width: 1050px){
	.header-middle > .container {
    	height: 100px;
	}
	.header-bottom > .container {
    	height: 50px;
	}
}

.johannes-modal{
	background: <?php echo esc_attr( $color_bg ); ?>;	
}
.johannes-modal .johannes-menu-social li a:hover,
.meks_ess a:hover{
	background: <?php echo esc_attr( $color_txt ); ?>;		
}
.johannes-modal .johannes-menu-social li:hover a{
	color: <?php echo esc_attr( $color_button_hover ); ?>;
}
.johannes-modal .johannes-modal-close{
	color: <?php echo esc_attr( $color_txt ); ?>;	
}
.johannes-modal .johannes-modal-close:hover{
	color: <?php echo esc_attr( $color_acc ); ?>;	
}
.meks_ess a:hover{
	color: <?php echo esc_attr( $color_bg ); ?>;	
}

h1,
h2,
h3,
h4,
h5,
h6,
.h1,
.h2,
.h3,
.h4,
.h5,
.h6,
.h0,
.display-1,
.has-large-font-size {
	color: <?php echo esc_attr( $color_h ); ?>;	
}

.entry-title a,
a{
	color: <?php echo esc_attr( $color_txt ); ?>;	
}
.johannes-post .entry-title a{
	color: <?php echo esc_attr( $color_h ); ?>;		
}
.entry-content a:not([class*=button]),
.comment-content a:not([class*=button]){
	color: <?php echo esc_attr( $color_acc ); ?>;
}
.entry-content a:not([class*=button]):hover,
.comment-content a:not([class*=button]):hover{
	color: <?php echo esc_attr( $color_txt ); ?>;
}
.entry-title a:hover,
a:hover,
.entry-meta a,
.written-by a,
.johannes-overlay .entry-meta a:hover,
body .johannes-cover .section-bg+.container .johannes-breadcrumbs a:hover,
.johannes-cover .section-bg+.container .section-head a:not(.johannes-button):not(.cat-item):hover{
	color: <?php echo esc_attr( $color_acc ); ?>;	
}
.entry-meta,
.entry-content .entry-tags a,
.entry-content .fn a,
.comment-metadata,
.entry-content .comment-metadata a,
.written-by > span,
.johannes-breadcrumbs{
	color: <?php echo esc_attr( $color_meta ); ?>;
}
.entry-meta a:hover,
.written-by a:hover{
	color: <?php echo esc_attr( $color_txt ); ?>;
}
.entry-meta .meta-item + .meta-item:before{
	background:	<?php echo esc_attr( $color_txt ); ?>;	
}
.entry-format i{
	color: <?php echo esc_attr( $color_bg ); ?>;	
	background:	<?php echo esc_attr( $color_txt ); ?>;	
}


.category-pill .entry-category a{
  background-color: <?php echo esc_attr( $color_acc ); ?>;
  color: <?php echo esc_attr( $color_button_txt ); ?>;
}

.category-pill .entry-category a:hover{
  background-color: <?php echo esc_attr( $color_txt ); ?>;
  color: <?php echo esc_attr( $color_button_hover ); ?>;
}

.johannes-overlay.category-pill .entry-category a:hover,
.johannes-cover.category-pill .entry-category a:hover {
	background-color: #ffffff;
  color: <?php echo esc_attr( $color_dark_txt ); ?>;
}



.white-bg-alt-2 .johannes-bg-alt-2 .category-pill .entry-category a:hover,
.white-bg-alt-2 .johannes-bg-alt-2 .entry-format i{
	background-color: #ffffff;
  color: <?php echo esc_attr( $color_dark_txt ); ?>;
}

.media-shadow:after{
  background: <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.1 ) ); ?>;
}

.entry-content .entry-tags a:hover,
.entry-content .fn a:hover{
	color: <?php echo esc_attr( $color_acc ); ?>;	
}

.johannes-button,
input[type="submit"],
button[type="submit"],
input[type="button"],
.wp-block-button .wp-block-button__link,
.comment-reply-link,
#cancel-comment-reply-link,
.johannes-pagination a,
.johannes-pagination,
.meks-instagram-follow-link .meks-widget-cta,
.mks_autor_link_wrap a,
.mks_read_more a,
.category-pill .entry-category a{
	font-family: <?php echo wp_kses_post( $button_font['font-family'] ); ?>, Arial, sans-serif;
	font-weight: <?php echo esc_attr( $button_font['font-weight'] ); ?>;
	<?php if ( isset( $button_font['font-style'] ) && !empty( $button_font['font-style'] ) ):?>
	font-style: <?php echo esc_attr( $button_font['font-style'] ); ?>;
	<?php endif; ?>
}

.johannes-bg-alt-1,
.has-arrows .owl-nav,
.has-arrows .owl-stage-outer:after,
.media-shadow:after {
	background-color: <?php echo esc_attr( $color_bg_alt_1 ); ?>
}

.johannes-bg-alt-2 {
	background-color: <?php echo esc_attr( $color_bg_alt_2 ); ?>
}


.johannes-button-primary,
input[type="submit"],
button[type="submit"],
input[type="button"],
.johannes-pagination a{
  box-shadow: 0 10px 15px 0 <?php echo esc_attr( johannes_hex_to_rgba( $color_acc, 0.2 ) ); ?>;  
  background: <?php echo esc_attr( $color_acc ); ?>;	
  color: <?php echo esc_attr( $color_button_txt ); ?>;	
}

.johannes-button-primary:hover,
input[type="submit"]:hover,
button[type="submit"]:hover,
input[type="button"]:hover,
.johannes-pagination a:hover{
	box-shadow: 0 0 0 0 <?php echo esc_attr( johannes_hex_to_rgba( $color_acc, 0 ) ); ?>;
  color: <?php echo esc_attr( $color_button_txt ); ?>;
}
.johannes-button.disabled{	
	background: <?php echo esc_attr( $color_bg_alt_1 ); ?>;
	color: <?php echo esc_attr( $color_txt ); ?>; 
	box-shadow: none;  	
}

.johannes-button-secondary,
.comment-reply-link,
#cancel-comment-reply-link,
.meks-instagram-follow-link .meks-widget-cta,
.mks_autor_link_wrap a,
.mks_read_more a{
  box-shadow: inset 0 0px 0px 1px <?php echo esc_attr( $color_txt ); ?>;
  color: <?php echo esc_attr( $color_txt ); ?>;
  opacity: .5;
}
.johannes-button-secondary:hover,
.comment-reply-link:hover,
#cancel-comment-reply-link:hover,
.meks-instagram-follow-link .meks-widget-cta:hover,
.mks_autor_link_wrap a:hover,
.mks_read_more a:hover{
  box-shadow: inset 0 0px 0px 1px <?php echo esc_attr( $color_acc ); ?>; 
  opacity: 1;
  color: <?php echo esc_attr( $color_acc ); ?>;
}

.johannes-breadcrumbs a,
.johannes-action-close:hover,
.single-md-content .entry-summary span,
form label .required{
	color: <?php echo esc_attr( $color_acc ); ?>;
}
.johannes-breadcrumbs a:hover{
	color: <?php echo esc_attr( $color_txt ); ?>;
}

.section-title:after{
	background-color: <?php echo esc_attr( $color_acc ); ?>;
}


/* Blocks */
hr{
  background: <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.2 ) ); ?>;
}
.wp-block-preformatted,
.wp-block-verse,
pre,
code, kbd, pre, samp, address{
  background:<?php echo esc_attr( $color_bg_alt_1 ); ?>;
}
.entry-content ul li:before,
.wp-block-quote:before,
.comment-content ul li:before{
  color: <?php echo esc_attr( $color_txt ); ?>;
}
.wp-block-quote.is-large:before{
	color: <?php echo esc_attr( $color_acc ); ?>;	
}

.wp-block-table.is-style-stripes tr:nth-child(odd){
	background:<?php echo esc_attr( $color_bg_alt_1 ); ?>;
}

.wp-block-table.is-style-regular tbody tr,
.entry-content table tr,
.comment-content table tr{
	border-bottom: 1px solid <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.1 ) ); ?>;
}
.wp-block-pullquote:not(.is-style-solid-color){
	color: <?php echo esc_attr( $color_txt ); ?>;
	border-color: <?php echo esc_attr( $color_acc ); ?>;
}
.wp-block-pullquote{
	background: <?php echo esc_attr( $color_acc ); ?>;	
	color: <?php echo esc_attr( $color_bg ); ?>;	
}
.johannes-sidebar-none .wp-block-pullquote.alignfull.is-style-solid-color{
	box-shadow: -526px 0 0 <?php echo esc_attr( $color_acc ); ?>, -1052px 0 0 <?php echo esc_attr( $color_acc ); ?>,
	526px 0 0 <?php echo esc_attr( $color_acc ); ?>, 1052px 0 0 <?php echo esc_attr( $color_acc ); ?>;
}
.wp-block-button .wp-block-button__link{
	background: <?php echo esc_attr( $color_acc ); ?>;
	color: <?php echo esc_attr( $color_button_txt ); ?>;
  box-shadow: 0 10px 15px 0 <?php echo esc_attr( johannes_hex_to_rgba( $color_acc, 0.2 ) ); ?>;
}
.wp-block-button .wp-block-button__link:hover{
  box-shadow: 0 0 0 0 <?php echo esc_attr( johannes_hex_to_rgba( $color_acc, 0 ) ); ?>;  
}
.is-style-outline .wp-block-button__link {
  background: 0 0;
  color:<?php echo esc_attr( $color_acc ); ?>;
  border: 2px solid currentcolor;
}

.entry-content .is-style-solid-color a:not([class*=button]){
  color:<?php echo esc_attr( $color_bg ); ?>;	
}
.entry-content .is-style-solid-color a:not([class*=button]):hover{
  color:<?php echo esc_attr( $color_txt ); ?>;		
}


/* Forms */
input[type=color], input[type=date], input[type=datetime-local], input[type=datetime], 
input[type=email], input[type=month], input[type=number], input[type=password], input[type=range], 
input[type=search], input[type=tel], input[type=text], input[type=time], 
input[type=url], input[type=week], select, textarea{
  border: 1px solid <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.3 ) ); ?>;
}

.meks_ess{
  border-color: <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.1 ) ); ?>; 
}


/* Pagination */
.double-bounce1, .double-bounce2{
	background-color: <?php echo esc_attr( $color_acc ); ?>;	
}
.johannes-pagination .page-numbers.current,
.paginated-post-wrapper span{
	background: <?php echo esc_attr( $color_bg_alt_1 ); ?>;
	color: <?php echo esc_attr( $color_txt ); ?>;
}

/* Widgets */
.widget li{
	color: <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.8 ) ); ?>;	
}
.widget_calendar #today a{
	color: #fff;
}
.widget_calendar #today a{
	background: <?php echo esc_attr( $color_acc ); ?>;
}
.tagcloud a{
	border-color: <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.5 ) ); ?>;
	color: <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.8 ) ); ?>;
}
.tagcloud a:hover{
	color: <?php echo esc_attr( $color_acc ); ?>;
	border-color: <?php echo esc_attr( $color_acc ); ?>;
}
.rssSummary,
.widget p{
	color: <?php echo esc_attr( $color_txt ); ?>;	
}
.johannes-bg-alt-1 .count,
.johannes-bg-alt-1 li a,
.johannes-bg-alt-1 .johannes-accordion-nav{
	background-color: <?php echo esc_attr( $color_bg_alt_1 ); ?>;
}
.johannes-bg-alt-2 .count,
.johannes-bg-alt-2 li a,
.johannes-bg-alt-2 .johannes-accordion-nav,
.johannes-bg-alt-2 .cat-item .count, 
.johannes-bg-alt-2 .rss-date, 
.widget .johannes-bg-alt-2 .post-date, 
.widget .johannes-bg-alt-2 cite{
	background-color: <?php echo esc_attr( $color_bg_alt_2 ); ?>;
	color: #FFF;
}
.white-bg-alt-1 .widget .johannes-bg-alt-1 select option,
.white-bg-alt-2 .widget .johannes-bg-alt-2 select option{
	background: <?php echo esc_attr( $color_bg_alt_2 ); ?>;
}
.widget .johannes-bg-alt-2 li a:hover{
	color: <?php echo esc_attr( $color_acc ); ?>;
}
.widget_categories .johannes-bg-alt-1 ul li .dots:before,
.widget_archive .johannes-bg-alt-1 ul li .dots:before{
	color: <?php echo esc_attr( $color_txt ); ?>;	
}
.widget_categories .johannes-bg-alt-2 ul li .dots:before,
.widget_archive .johannes-bg-alt-2 ul li .dots:before{
	color: #FFF;	
}
.search-alt input[type=search], 
.search-alt input[type=text], 
.widget_search input[type=search], 
.widget_search input[type=text],
.mc-field-group input[type=email], 
.mc-field-group input[type=text]{
	border-bottom: 1px solid <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.2 ) ); ?>;
}
.johannes-sidebar-hidden{
	background: <?php echo esc_attr( $color_bg ); ?>;	
}


/* Footer */
.johannes-footer{
	background: <?php echo esc_attr( $color_footer_bg ); ?>;
	color: <?php echo esc_attr( $color_footer_txt ); ?>;
}
.johannes-footer a,
.johannes-footer .widget-title{
	color: <?php echo esc_attr( $color_footer_txt ); ?>;	
}
.johannes-footer a:hover{
	color: <?php echo esc_attr( $color_footer_acc ); ?>;
}
.johannes-footer-widgets + .johannes-copyright{
  border-top: 1px solid <?php echo esc_attr( johannes_hex_to_rgba( $color_footer_txt, 0.1 ) ); ?>;
}
.johannes-footer .widget .count,
.johannes-footer .widget_categories li a,
.johannes-footer .widget_archive li a,
.johannes-footer .widget .johannes-accordion-nav{
	background-color: <?php echo esc_attr( $color_footer_bg ); ?>;
}
.footer-divider{
	border-top: 1px solid <?php echo esc_attr( johannes_hex_to_rgba( $color_footer_txt, 0.1 ) ); ?>;
}
.johannes-footer .rssSummary,
.johannes-footer .widget p{
	color: <?php echo esc_attr( $color_footer_txt ); ?>;
}

/* Additional */
.johannes-empty-message{
  background: <?php echo esc_attr( $color_bg_alt_1 ); ?>;
}

.error404 .h0{
  color: <?php echo esc_attr( $color_acc ); ?>;
}
.johannes-goto-top,
.johannes-goto-top:hover{
	background: <?php echo esc_attr( $color_txt ); ?>;	
	color: <?php echo esc_attr( $color_bg ); ?>;	
}

.johannes-ellipsis div{
	background: <?php echo esc_attr( $color_acc ); ?>;	
}
.white-bg-alt-2 .johannes-bg-alt-2 .section-subnav .johannes-button-secondary:hover{
	color: <?php echo esc_attr( $color_acc ); ?>;		
}
.section-subnav a{
	color: <?php echo esc_attr( $color_txt ); ?>;		
}
.johannes-cover .section-subnav a{
	color: <?php echo esc_attr( $color_button_txt ); ?>;		
}
.section-subnav a:hover{
	color: <?php echo esc_attr( $color_acc ); ?>;	
}

<?php


/* Apply image size options */
$image_sizes = johannes_get_image_sizes();

if ( !empty( $image_sizes ) ) {

	echo '@media(min-width: '.esc_attr( $grid['breakpoint']['md']).'px){'; 
    foreach ( $image_sizes as $id => $size ) {
    	if( isset($size['cover']) )  {      
        	echo '.size-'.$id .'{ height: '.absint($size['h'] * 0.9).'px !important;}';
    	}
    }
    echo '}';

	echo '@media(min-width: '.esc_attr( $grid['breakpoint']['lg']).'px){'; 
    foreach ( $image_sizes as $id => $size ) {
    	if( $size['h'] && $size['h'] < 5000)  {      
        	echo '.size-'.$id .'{ height: '.esc_attr($size['h']).'px !important;}';
    	}
    }
    echo '}';

}

/* Editor font sizes */
$font_sizes = johannes_get_editor_font_sizes();

if ( !empty( $font_sizes ) ) {
	echo '@media(min-width: '.esc_attr( $grid['breakpoint']['lg']).'px){'; 
    foreach ( $font_sizes as $id => $item ) {  
        	echo '.has-'. $item['slug'] .'-font-size{ font-size: '.number_format( $item['size'] / 10,  1 ) .'rem;}';
    }
    echo '}';
}

/* Editor colors */
$colors = johannes_get_editor_colors();

if ( !empty( $colors ) ) {
    foreach ( $colors as $id => $item ) {  
        	echo '.has-'. $item['slug'] .'-background-color{ background-color: ' . esc_attr($item['color']) .';}';
        	echo '.has-'. $item['slug'] .'-color{ color: ' . esc_attr($item['color']) .';}';
    }
}


$uppercase = johannes_get_option('uppercase');

if ( !empty( $uppercase ) ) {
    foreach ( $uppercase as $element ) {   
        echo esc_attr( $element ) .'{ text-transform: uppercase;}';
    }
}

?>

/* Mobile Font sizes */

body{
	font-size:<?php echo esc_attr( $font_size_p ); ?>rem;
}
.johannes-header{
	font-size:<?php echo esc_attr( $font_size_nav ); ?>rem;
}
.display-1{
	font-size:3rem;
}

h1, .h1{
	font-size:2.6rem;
}
h2, .h2{
	font-size:2.4rem;
}
h3, .h3{
	font-size:2.2rem;
}
h4, .h4,
.wp-block-cover .wp-block-cover-image-text,
.wp-block-cover .wp-block-cover-text,
.wp-block-cover h2,
.wp-block-cover-image .wp-block-cover-image-text,
.wp-block-cover-image .wp-block-cover-text,
.wp-block-cover-image h2{
	font-size:2rem;
}
h5, .h5{
	font-size:1.8rem;
}
h6, .h6  {
	font-size:1.6rem;
}
.entry-meta{
	font-size:1.2rem;	
}

.section-title {
	font-size:2.4rem;
}

.widget-title{
	font-size:<?php echo esc_attr( $font_size_widget_title ); ?>rem;
}
.mks_author_widget h3{
	font-size:<?php echo esc_attr( $font_size_widget_title + 0.2 ); ?>rem;	
}
.widget,
.johannes-breadcrumbs{
	font-size:<?php echo esc_attr( $font_size_small ); ?>rem;	
}

.wp-block-quote.is-large p, 
.wp-block-quote.is-style-large p{
	font-size:2.2rem;
}

.johannes-site-branding .site-title.logo-img-none{
	font-size: 2.6rem;
}
.johannes-cover-indent .johannes-cover{
	margin-top: -70px;
}

.johannes-menu-social li a:after, 
.menu-social-container li a:after{
	font-size:<?php echo esc_attr( $font_size_nav_ico-0.8 ); ?>rem;	
}

.johannes-modal .johannes-menu-social li>a:after,
.johannes-menu-action .jf{
	font-size:<?php echo esc_attr( $font_size_nav_ico ); ?>rem;		
}



.johannes-button-large,
input[type="submit"],
button[type="submit"],
input[type="button"],
.johannes-pagination a,
.page-numbers.current,
.johannes-button-medium,
.meks-instagram-follow-link .meks-widget-cta,
.mks_autor_link_wrap a,
.mks_read_more a,
.wp-block-button .wp-block-button__link{
	font-size:<?php echo esc_attr( $font_size_small - 0.1 ); ?>rem;	
}
.johannes-button-small,
.comment-reply-link,
#cancel-comment-reply-link{
	font-size:<?php echo esc_attr( $font_size_small - 0.2 ); ?>rem;		
}
.category-pill .entry-category a,
.category-pill-small .entry-category a{
	font-size:<?php echo esc_attr( $font_size_small - 0.3 ); ?>rem;		
}



@media (min-width: <?php echo esc_attr($grid['breakpoint']['md']); ?>px){ 
.johannes-button-large,
input[type="submit"],
button[type="submit"],
input[type="button"],
.johannes-pagination a,
.page-numbers.current,
.wp-block-button .wp-block-button__link{
	font-size:<?php echo esc_attr( $font_size_small ); ?>rem;	
}
.category-pill .entry-category a{
	font-size:<?php echo esc_attr( $font_size_small ); ?>rem;		
}
.category-pill-small .entry-category a{
	font-size:<?php echo esc_attr( $font_size_small - 0.3 ); ?>rem;		
}
}

/* Specific Font sizes on mobile devices */
@media (max-width: <?php echo esc_attr($grid['breakpoint']['sm']); ?>px){
	.johannes-overlay .h1,
	.johannes-overlay .h2,
	.johannes-overlay .h3,
	.johannes-overlay .h4,
	.johannes-overlay .h5{
		font-size: 2.2rem;
	}


}
@media (max-width: <?php echo esc_attr($grid['breakpoint']['md']); ?>px){ 
	.johannes-layout-fa-d .h5{
		font-size: 2.4rem;
	}
	.johannes-layout-f.category-pill .entry-category a{
		background-color: transparent;
  		color: <?php echo esc_attr( $color_acc ); ?>;
	}
	.johannes-layout-c .h3,
	.johannes-layout-d .h5{
		font-size: 2.4rem;
	}
	.johannes-layout-f .h3{
		font-size: 1.8rem;
	}
}

@media (min-width: <?php echo esc_attr($grid['breakpoint']['md']); ?>px) and (max-width: 1050px){ 
.johannes-layout-fa-c .h2{
	font-size:<?php echo esc_attr( $font_size_h3 ); ?>rem;
}
.johannes-layout-fa-d .h5{
	font-size:<?php echo esc_attr( $font_size_h6 ); ?>rem;
}
.johannes-layout-fa-e .display-1,
.section-head-alt .display-1{
	font-size:<?php echo esc_attr( $font_size_h1 ); ?>rem;	
}

}

@media (max-width: 1050px){ 
	body.single-post .single-md-content{
		max-width: <?php echo johannes_size_by_col( johannes_get_option('single_width') ) + 30; ?>px;
		width: 100%;
	}
	body.page .single-md-content.col-lg-6,
	body.page .single-md-content.col-lg-6{
		flex: 0 0 100%
	}

	body.page .single-md-content{
		max-width: <?php echo johannes_size_by_col( johannes_get_option('page_width') ) + 30; ?>px;
		width: 100%;
	}
}


@media (min-width: <?php echo esc_attr($grid['breakpoint']['md']); ?>px) and (max-width: <?php echo esc_attr($grid['breakpoint']['lg']); ?>px){ 
.display-1{
	font-size:4.6rem;
}
h1, .h1{
	font-size:4rem;
}
h2, .h2,
.johannes-layout-fa-e .display-1,
.section-head-alt .display-1{
	font-size:3.2rem;
}
h3, .h3,
.johannes-layout-fa-c .h2,
.johannes-layout-fa-d .h5,
.johannes-layout-d .h5,
.johannes-layout-e .h2{
	font-size:2.8rem;
}
h4, .h4,
.wp-block-cover .wp-block-cover-image-text,
.wp-block-cover .wp-block-cover-text,
.wp-block-cover h2,
.wp-block-cover-image .wp-block-cover-image-text,
.wp-block-cover-image .wp-block-cover-text,
.wp-block-cover-image h2{
	font-size:2.4rem;
}
h5, .h5{
	font-size:2rem;
}
h6, .h6  {
	font-size:1.8rem;
}


.section-title {
	font-size:3.2rem;
}
.johannes-section.wa-layout .display-1{
	font-size: 3rem;
}
.johannes-layout-f .h3{
	font-size: 3.2rem
}
.johannes-site-branding .site-title.logo-img-none{
    font-size: 3rem;
}
}


/* Desktop Font sizes */
@media (min-width: <?php echo esc_attr($grid['breakpoint']['lg']); ?>px){ 

body{
	font-size:<?php echo esc_attr( $font_size_p ); ?>rem;
}
.johannes-header{
	font-size:<?php echo esc_attr( $font_size_nav ); ?>rem;
}
.display-1{
	font-size:<?php echo esc_attr( $font_size_punchline ); ?>rem;
}
h1, .h1 {
	font-size:<?php echo esc_attr( $font_size_h1 ); ?>rem;
}

h2, .h2 {
	font-size:<?php echo esc_attr( $font_size_h2 ); ?>rem;
}
h3, .h3 {
	font-size:<?php echo esc_attr( $font_size_h3 ); ?>rem;
}
h4, .h4,
.wp-block-cover .wp-block-cover-image-text,
.wp-block-cover .wp-block-cover-text,
.wp-block-cover h2,
.wp-block-cover-image .wp-block-cover-image-text,
.wp-block-cover-image .wp-block-cover-text,
.wp-block-cover-image h2 {
	font-size:<?php echo esc_attr( $font_size_h4 ); ?>rem;
}
h5, .h5 {
	font-size:<?php echo esc_attr( $font_size_h5 ); ?>rem;
}

h6, .h6 {
	font-size:<?php echo esc_attr( $font_size_h6 ); ?>rem;
}
.widget-title{
	font-size:<?php echo esc_attr( $font_size_widget_title ); ?>rem;
}
.section-title{
	font-size:<?php echo esc_attr( $font_size_section_title ); ?>rem;	
}
.wp-block-quote.is-large p, 
.wp-block-quote.is-style-large p{
	font-size:2.6rem;
}
.johannes-section-instagram .h2{
	font-size: 3rem;
}
.johannes-site-branding .site-title.logo-img-none{
    font-size: 4rem;
}
.entry-meta{
	font-size:<?php echo esc_attr( $font_size_small ); ?>rem;	
}
.johannes-cover-indent .johannes-cover {
    margin-top: -<?php echo esc_attr( $header_height ); ?>px;
}
.johannes-cover-indent .johannes-cover .section-head{
    top: <?php echo esc_attr( $header_height /4 ); ?>px;
}
}



.section-description .search-alt input[type=text],
.search-alt input[type=text]{
	color: <?php echo esc_attr( $color_txt ); ?>;
}

::-webkit-input-placeholder {
	color: <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.5 ) ); ?>;
}
::-moz-placeholder {
	color: <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.5 ) ); ?>;
}
:-ms-input-placeholder {
	color: <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.5 ) ); ?>;
}
:-moz-placeholder{
	color: <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.5 ) ); ?>;
}
.section-description .search-alt input[type=text]::-webkit-input-placeholder {
	color: <?php echo esc_attr( $color_txt ); ?>;
}
.section-description .search-alt input[type=text]::-moz-placeholder {
	color: <?php echo esc_attr( $color_txt ); ?>;
}
.section-description .search-alt input[type=text]:-ms-input-placeholder {
	color: <?php echo esc_attr( $color_txt ); ?>;
}
.section-description .search-alt input[type=text]:-moz-placeholder{
	color: <?php echo esc_attr( $color_txt ); ?>;
}

.section-description .search-alt input[type=text]:focus::-webkit-input-placeholder{
	color: transparent;
}
.section-description .search-alt input[type=text]:focus::-moz-placeholder {
	color: transparent;
}
.section-description .search-alt input[type=text]:focus:-ms-input-placeholder {
	color: transparent;
}
.section-description .search-alt input[type=text]:focus:-moz-placeholder{
	color: transparent;
}

/* Woocommerce styles */
<?php if ( johannes_is_woocommerce_active() ): ?>
.johannes-header .johannes-cart-wrap a:hover{
	color: <?php echo esc_attr($color_header_middle_acc); ?>;	
}
.johannes-cart-count {
    background-color: <?php echo esc_attr($color_header_middle_acc); ?>;
    color: <?php echo esc_attr($color_header_middle_bg); ?>;
}


.woocommerce ul.products li.product .button, 
.woocommerce ul.products li.product .added_to_cart{
  box-shadow: 0 10px 15px 0 <?php echo esc_attr( johannes_hex_to_rgba( $color_acc, 0.2 ) ); ?>;  
  background: <?php echo esc_attr( $color_acc ); ?>;	
  color: <?php echo esc_attr( $color_button_txt ); ?>;
}
.woocommerce ul.products li.product .amount{
	color: <?php echo johannes_hex_to_rgba($color_h, 0.8); ?>;
}
.woocommerce ul.products li.product .button:hover{
  box-shadow: 0 0 0 0 <?php echo esc_attr( johannes_hex_to_rgba( $color_acc, 0 ) ); ?>;
  color: <?php echo esc_attr( $color_button_hover ); ?>;	
}
.woocommerce ul.products .woocommerce-loop-product__link{
  color: <?php echo esc_attr($color_txt); ?>;
}
.woocommerce ul.products .woocommerce-loop-product__link:hover{
  color: <?php echo esc_attr($color_acc); ?>;  
}
.woocommerce ul.products li.product .woocommerce-loop-category__title, 
.woocommerce ul.products li.product .woocommerce-loop-product__title,
.woocommerce ul.products li.product h3{
  font-size: <?php echo esc_attr($font_size_p); ?>rem;
}
.woocommerce div.product form.cart .button,
.woocommerce #respond input#submit, 
.woocommerce a.button, 
.woocommerce button.button, 
.woocommerce input.button,
.woocommerce #respond input#submit.alt, 
.woocommerce a.button.alt, 
.woocommerce button.button.alt, 
.woocommerce input.button.alt,
.woocommerce ul.products li.product .added_to_cart{
  box-shadow: 0 10px 15px 0 <?php echo esc_attr( johannes_hex_to_rgba( $color_acc, 0.2 ) ); ?>;  
  background: <?php echo esc_attr( $color_acc ); ?>;	
  color: <?php echo esc_attr( $color_bg ); ?>;
	font-family: <?php echo wp_kses_post( $button_font['font-family'] ); ?>, Arial, sans-serif;
	font-weight: <?php echo esc_attr( $button_font['font-weight'] ); ?>;
	<?php if ( isset( $button_font['font-style'] ) && !empty( $button_font['font-style'] ) ):?>
	font-style: <?php echo esc_attr( $button_font['font-style'] ); ?>;
	<?php endif; ?>
}
.woocommerce .button.wc-backward{
  box-shadow:none;
  background: <?php echo johannes_hex_to_hsla($color_acc, -15); ?>;
  color: <?php echo esc_attr($color_button_txt); ?>;  
}
.wc-tab,
.woocommerce div.product .woocommerce-tabs ul.tabs li{
	font-size: <?php echo esc_attr( $font_size_p ); ?>rem;
}
.woocommerce button.disabled,
.woocommerce button.alt:disabled{
	background-color: <?php echo esc_attr( $color_bg_alt_1 ); ?>	
}

.price,
.amount,
.woocommerce div.product p.price {
  color: <?php echo esc_attr( $color_txt ); ?>;
}

.woocommerce div.product form.cart .button:hover,
.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover,
.woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover{
	background: <?php echo esc_attr( $color_acc ); ?>;	
  box-shadow: 0 0 0 0 <?php echo esc_attr( johannes_hex_to_rgba( $color_acc, 0 ) ); ?>;
  color: <?php echo esc_attr( $color_button_hover ); ?>;
}
.woocommerce #respond input#submit, 
.woocommerce a.button, 
.woocommerce button.button, 
.woocommerce input.button, 
.woocommerce ul.products li.product .added_to_cart{
  color: <?php echo esc_attr($color_button_txt); ?>;  
}
.woocommerce .woocommerce-breadcrumb a:hover{
  color: <?php echo esc_attr($color_acc); ?>;
}
.woocommerce div.product .woocommerce-tabs ul.tabs li.active a {
    border-bottom: 3px solid <?php echo esc_attr($color_acc); ?>;
}
.woocommerce .woocommerce-breadcrumb,
.woocommerce .woocommerce-breadcrumb a{
  color: <?php echo esc_attr($color_meta); ?>;
}
body.woocommerce .johannes-entry ul.products li.product, body.woocommerce-page ul.products li.product{
  box-shadow:inset 0px 0px 0px 1px <?php echo johannes_hex_to_rgba($color_txt, 0.3); ?>;
}

.woocommerce div.product .woocommerce-tabs ul.tabs li.active a {
  border-bottom: 3px solid <?php echo esc_attr($color_acc); ?>;
}

body.woocommerce .johannes-entry ul.products li.product, body.woocommerce-page ul.products li.product{
  box-shadow:inset 0px 0px 0px 1px <?php echo johannes_hex_to_rgba($color_txt, 0.3); ?>;
}


body .woocommerce .woocommerce-error,
body .woocommerce .woocommerce-info, 
body .woocommerce .woocommerce-message{
   background-color: <?php echo esc_attr($color_bg_alt_1); ?>;
   color: <?php echo esc_attr($color_txt); ?>;
}
body .woocommerce-checkout #payment ul.payment_methods, 
body .woocommerce table.shop_table,
body .woocommerce table.shop_table td, 
body .woocommerce-cart .cart-collaterals .cart_totals tr td, 
body .woocommerce-cart .cart-collaterals .cart_totals tr th, 
body .woocommerce table.shop_table tbody th, 
body .woocommerce table.shop_table tfoot td, 
body .woocommerce table.shop_table tfoot th, 
body .woocommerce .order_details, 
body .woocommerce .cart-collaterals 
body .cross-sells, .woocommerce-page .cart-collaterals .cross-sells, 
body .woocommerce .cart-collaterals .cart_totals, 
body .woocommerce ul.order_details, 
body .woocommerce .shop_table.order_details tfoot th, 
body .woocommerce .shop_table.customer_details th, 
body .woocommerce-checkout #payment ul.payment_methods, 
body .woocommerce .col2-set.addresses .col-1, 
body .woocommerce .col2-set.addresses .col-2, 
body.woocommerce-cart table.cart td.actions .coupon .input-text,
body .woocommerce table.shop_table tbody:first-child tr:first-child th, 
body .woocommerce table.shop_table tbody:first-child tr:first-child td,
body .woocommerce ul.products,
body .woocommerce-product-search input[type=search]{
   border-color: <?php echo johannes_hex_to_rgba($color_txt, 0.1); ?>;
}
body .select2-container .select2-choice,
body .select2-container--default .select2-selection--single, 
body .select2-dropdown{
	border-color: <?php echo johannes_hex_to_rgba($color_txt, 0.3); ?>;	
}
body .select2-dropdown{
  background: <?php echo esc_attr($color_bg); ?>;
}
.select2-container--default .select2-results__option[aria-selected=true], 
.select2-container--default .select2-results__option[data-selected=true]{
	background-color: <?php echo esc_attr($color_acc); ?>;
  color: <?php echo esc_attr($color_bg); ?>; 
}
.woocommerce table.shop_table tfoot tr.order-total th{
    border-bottom: 1px solid <?php echo johannes_hex_to_rgba($color_txt, 0.3); ?>;
}
body.woocommerce div.product .woocommerce-tabs ul.tabs li a,
body.woocommerce-cart .cart-collaterals .cart_totals table th{
  color: <?php echo esc_attr($color_txt); ?>; 
}
body.woocommerce div.product .woocommerce-tabs ul.tabs li a:hover{
  color: <?php echo esc_attr($color_acc); ?>; 
}

.woocommerce nav.woocommerce-pagination ul li a,
.woocommerce nav.woocommerce-pagination ul li span{
  box-shadow: 0 10px 15px 0 <?php echo esc_attr( johannes_hex_to_rgba( $color_acc, 0.2 ) ); ?>;  
  background: <?php echo esc_attr( $color_acc ); ?>;	
  color: <?php echo esc_attr( $color_bg ); ?>;	
}
.woocommerce nav.woocommerce-pagination ul li a:hover{
	box-shadow: 0 0 0 0 <?php echo esc_attr( johannes_hex_to_rgba( $color_acc, 0 ) ); ?>;
	background: <?php echo esc_attr( $color_acc ); ?>;	
  color: <?php echo esc_attr( $color_bg ); ?>;	
}
.woocommerce nav.woocommerce-pagination ul li span.current{
	background: <?php echo esc_attr( $color_bg_alt_1 ); ?>;
	color: <?php echo esc_attr( $color_txt ); ?>;
}
.woocommerce .widget_price_filter .ui-slider .ui-slider-range{
	background:<?php echo johannes_hex_to_rgba($color_acc, 0.5); ?>;  	
}
.woocommerce .widget_price_filter .ui-slider .ui-slider-handle{
  background: <?php echo esc_attr($color_acc); ?>;
}


.woocommerce ul.product_list_widget li,
.woocommerce .widget_shopping_cart .cart_list li,
.woocommerce.widget_shopping_cart .cart_list li{
	border-bottom:1px solid <?php echo johannes_hex_to_rgba($color_bg, 0.1); ?>;  
}

.woocommerce-MyAccount-navigation ul{
	background: <?php echo esc_attr($color_bg_alt_1); ?>;	
}
body.woocommerce .widget_text .johannes-inverted .button:hover{
	background: <?php echo esc_attr($color_bg); ?>;	
}
.woocommerce-checkout #payment,
.woocommerce .col2-set.addresses .col-1,
.woocommerce .col2-set.addresses .col-2{
	background: <?php echo esc_attr($color_bg_alt_1); ?>;		
}

<?php endif; ?>