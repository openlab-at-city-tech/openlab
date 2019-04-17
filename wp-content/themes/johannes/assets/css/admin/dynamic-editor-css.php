<?php

//Fonts
$main_font = johannes_get_option( 'main_font', 'font' );
$h_font = johannes_get_option( 'h_font', 'font' );
$nav_font = johannes_get_option( 'nav_font', 'font' );
$button_font = johannes_get_option( 'button_font', 'font' );

//Font sizes
$font_size_p =  absint( johannes_get_option( 'font_size_p' ) );
$font_size_small = absint( johannes_get_option( 'font_size_small' ));
$font_size_h1 = absint( johannes_get_option( 'font_size_h1' ));
$font_size_h2 = absint( johannes_get_option( 'font_size_h2' ));
$font_size_h3 = absint( johannes_get_option( 'font_size_h3' ));
$font_size_h4 = absint( johannes_get_option( 'font_size_h4' ));
$font_size_h5 = absint( johannes_get_option( 'font_size_h5' ));
$font_size_h6 = absint( johannes_get_option( 'font_size_h6' ));

//Colors
$color_bg = johannes_get_option('color_bg');
$color_h = johannes_get_option('color_h');
$color_txt = johannes_get_option('color_txt');
$color_acc = johannes_get_option('color_acc');
$color_meta = johannes_get_option('color_meta');
$color_bg_alt_1 = johannes_get_option('color_bg_alt_1');
$color_bg_alt_2 = johannes_get_option('color_bg_alt_2');

$color_dark_text = johannes_is_color_light( $color_bg ) ? $color_txt : $color_bg;
$color_button_txt = johannes_is_color_light( $color_acc ) ? $color_dark_text : '#fff';
$color_button_hover = johannes_is_color_light( $color_txt ) ? $color_dark_text : '#fff';



//Grid vars
$grid = johannes_grid_vars();

?>

.edit-post-visual-editor.editor-styles-wrapper {
  line-height: 1.625;
	color: <?php echo esc_attr( $color_txt ); ?>;
	background: <?php echo esc_attr( $color_bg ); ?>;
}
.edit-post-visual-editor.editor-styles-wrapper{
	font-family: <?php echo wp_kses_post( $main_font['font-family'] ); ?>, Arial, sans-serif;
	font-weight: <?php echo esc_attr( $main_font['font-weight'] ); ?>;
	<?php if ( isset( $main_font['font-style'] ) && !empty( $main_font['font-style'] ) ):?>
	font-style: <?php echo esc_attr( $main_font['font-style'] ); ?>;
	<?php endif; ?>
}
.edit-post-visual-editor.editor-styles-wrapper p{
  line-height: 1.625;
}

.editor-styles-wrapper h1,
.editor-styles-wrapper.edit-post-visual-editor .editor-post-title__block .editor-post-title__input,
.editor-styles-wrapper h2,
.editor-styles-wrapper h3,
.editor-styles-wrapper h4,
.editor-styles-wrapper h5,
.editor-styles-wrapper h6,
.wp-block-cover .wp-block-cover-image-text, 
.wp-block-cover .wp-block-cover-text, 
.wp-block-cover h2, 
.wp-block-cover-image .wp-block-cover-image-text, 
.wp-block-cover-image .wp-block-cover-text, 
.wp-block-cover-image h2,
.entry-category a,
.single-md-content .entry-summary,
p.has-drop-cap:not(:focus)::first-letter,
.wp-block-heading h1,
.wp-block-heading h2,
.wp-block-heading h3,
.wp-block-heading h4,
.wp-block-heading h5,
.wp-block-heading h6 {
	font-family: <?php echo wp_kses_post( $h_font['font-family'] ); ?>, Arial, sans-serif;
	font-weight: <?php echo esc_attr( $h_font['font-weight'] ); ?>;
	<?php if ( isset( $h_font['font-style'] ) && !empty( $h_font['font-style'] ) ):?>
	font-style: <?php echo esc_attr( $h_font['font-style'] ); ?>;
	<?php endif; ?>
}
b,
strong{
	font-weight: <?php echo esc_attr( $h_font['font-weight'] ); ?>; 
}



.editor-styles-wrapper h1,
.editor-styles-wrapper.edit-post-visual-editor .editor-post-title__block .editor-post-title__input,
.editor-styles-wrapper h2,
.editor-styles-wrapper h3,
.editor-styles-wrapper h4,
.editor-styles-wrapper h5,
.editor-styles-wrapper h6,
.has-large-font-size,
.wp-block-heading h1,
.wp-block-heading h2,
.wp-block-heading h3,
.wp-block-heading h4,
.wp-block-heading h5,
.wp-block-heading h6 {
	color: <?php echo esc_attr( $color_h ); ?>;	
}


.editor-rich-text__tinymce a,
.editor-writing-flow a,
.wp-block-freeform.block-library-rich-text__tinymce a{
	color: <?php echo esc_attr( $color_acc ); ?>;	
}


.wp-block-button .wp-block-button__link{
	font-family: <?php echo wp_kses_post( $button_font['font-family'] ); ?>, Arial, sans-serif;
	font-weight: <?php echo esc_attr( $button_font['font-weight'] ); ?>;
	<?php if ( isset( $button_font['font-style'] ) && !empty( $button_font['font-style'] ) ):?>
	font-style: <?php echo esc_attr( $button_font['font-style'] ); ?>;
	<?php endif; ?>
	font-size:<?php echo esc_attr( $font_size_small ); ?>px;
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
.entry-content table tr{
	border-bottom: 1px solid <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.1 ) ); ?>;
}
.editor-styles-wrapper .wp-block-table td, 
.editor-styles-wrapper .wp-block-table th{
	border-color: <?php echo esc_attr( johannes_hex_to_rgba( $color_txt, 0.1 ) ); ?>;
	border-left: 0;
  border-right: 0;
}
.wp-block-pullquote:not(.is-style-solid-color){
	color: <?php echo esc_attr( $color_txt ); ?>;
	border-color: <?php echo esc_attr( $color_acc ); ?>;
}
.wp-block-pullquote{
	background: <?php echo esc_attr( $color_acc ); ?>;	
	color: <?php echo esc_attr( $color_bg ); ?>;	
}
.wp-block-pullquote.alignfull.is-style-solid-color{
	box-shadow: -526px 0 0 <?php echo esc_attr( $color_acc ); ?>, -1052px 0 0 <?php echo esc_attr( $color_acc ); ?>,
	526px 0 0 <?php echo esc_attr( $color_acc ); ?>, 1052px 0 0 <?php echo esc_attr( $color_acc ); ?>;
}
.editor-styles-wrapper .wp-block-pullquote:not(.is-style-solid-color){
	border-top: 4px solid <?php echo esc_attr( $color_acc ); ?>;
	border-bottom: 4px solid <?php echo esc_attr( $color_acc ); ?>;
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

.is-style-solid-color a:not([class*=button]){
  color:<?php echo esc_attr( $color_bg ); ?>;	
}
.is-style-solid-color a:not([class*=button]):hover{
  color:<?php echo esc_attr( $color_txt ); ?>;		
}



<?php


/* Editor font sizes */
$font_sizes = johannes_get_editor_font_sizes();

if ( !empty( $font_sizes ) ) {
	echo '@media(min-width: '.esc_attr( $grid['breakpoint']['lg']).'px){'; 
    foreach ( $font_sizes as $id => $item ) {  
        	echo '.has-'. $item['slug'] .'-font-size{ font-size: '.$item['size'].'px;}';
    }
    echo '}';
}

?>

/* Mobile Font sizes */

.edit-post-visual-editor.editor-styles-wrapper {
	font-size:<?php echo esc_attr( $font_size_p ); ?>px;
}

.editor-styles-wrapper h1,
.editor-styles-wrapper.edit-post-visual-editor .editor-post-title__block .editor-post-title__input,
.wp-block-heading h1{
	font-size:26px;
}
.editor-styles-wrapper h2,
.wp-block-heading h2{
	font-size:24px;
}
.editor-styles-wrapper h3,
.wp-block-heading h3{
	font-size:22px;
}
.editor-styles-wrapper h4,
.wp-block-cover .wp-block-cover-image-text,
.wp-block-cover .wp-block-cover-text,
.wp-block-cover h2,
.wp-block-cover-image .wp-block-cover-image-text,
.wp-block-cover-image .wp-block-cover-text,
.wp-block-cover-image h2,
.wp-block-heading h4{
	font-size:20px;
}
.editor-styles-wrapper h5,
.wp-block-heading h5{
	font-size:18px;
}
.editor-styles-wrapper h6,
.wp-block-heading h6  {
	font-size:16px;
}
.wp-block-quote.is-large p, 
.wp-block-quote.is-style-large p{
	font-size:22px;
}

@media (min-width: <?php echo esc_attr($grid['breakpoint']['md']); ?>px) and (max-width: <?php echo esc_attr($grid['breakpoint']['lg']); ?>px){ 

.editor-styles-wrapper h1,
.editor-styles-wrapper.edit-post-visual-editor .editor-post-title__block .editor-post-title__input,
.wp-block-heading h1{
	font-size:40px;
}
.editor-styles-wrapper h2,
.wp-block-heading h2{
	font-size:32px;
}
.editor-styles-wrapper h3,
.wp-block-heading h3{
	font-size:28px;
}
.editor-styles-wrapper h4,
.wp-block-cover .wp-block-cover-image-text,
.wp-block-cover .wp-block-cover-text,
.wp-block-cover h2,
.wp-block-cover-image .wp-block-cover-image-text,
.wp-block-cover-image .wp-block-cover-text,
.wp-block-cover-image h2,
.wp-block-heading h4{
	font-size:24px;
}
.editor-styles-wrapper h5,
.wp-block-heading h5{
	font-size:20px;
}
.editor-styles-wrapper h6,
.wp-block-heading h6  {
	font-size:18px;
}
}


/* Desktop Font sizes */
@media (min-width: <?php echo esc_attr($grid['breakpoint']['lg']); ?>px){ 

.edit-post-visual-editor.editor-styles-wrapper{
	font-size:<?php echo esc_attr( $font_size_p ); ?>px;
}
.editor-styles-wrapper h1,
.editor-styles-wrapper.edit-post-visual-editor .editor-post-title__block .editor-post-title__input,
.wp-block-heading h1 {
	font-size:<?php echo esc_attr( $font_size_h1 ); ?>px;
}

.editor-styles-wrapper h2,
.wp-block-heading h2 {
	font-size:<?php echo esc_attr( $font_size_h2 ); ?>px;
}
.editor-styles-wrapper h3,
.wp-block-heading h3 {
	font-size:<?php echo esc_attr( $font_size_h3 ); ?>px;
}
.editor-styles-wrapper h4,
.wp-block-cover .wp-block-cover-image-text,
.wp-block-cover .wp-block-cover-text,
.wp-block-cover h2,
.wp-block-cover-image .wp-block-cover-image-text,
.wp-block-cover-image .wp-block-cover-text,
.wp-block-cover-image h2,
.wp-block-heading h4 {
	font-size:<?php echo esc_attr( $font_size_h4 ); ?>px;
}
.editor-styles-wrapper h5,
.wp-block-heading h5 {
	font-size:<?php echo esc_attr( $font_size_h5 ); ?>px;
}

.editor-styles-wrapper h6,
.wp-block-heading h6 {
	font-size:<?php echo esc_attr( $font_size_h6 ); ?>px;
}
.wp-block-quote.is-large p, 
.wp-block-quote.is-style-large p{
	font-size:26px;
}
}

/* Content width*/

.edit-post-visual-editor .wp-block{
	max-width: <?php echo johannes_size_by_col( johannes_get_option('single_width') ) + 30; ?>px;
}
.post-type-page .edit-post-visual-editor .wp-block{
	max-width: <?php echo johannes_size_by_col( johannes_get_option('page_width') ) + 30; ?>px;
}
.edit-post-visual-editor .wp-block[data-align="wide"],
.post-type-page .edit-post-visual-editor .wp-block[data-align="wide"]{
	max-width: <?php echo johannes_size_by_col( 12 ) + 30; ?>px;
}

.edit-post-visual-editor .wp-block[data-align="full"],
.post-type-page .edit-post-visual-editor .wp-block[data-align="full"]{
	max-width: none;
}