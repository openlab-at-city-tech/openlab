<?php 
	
	/* Font styles */
	
	$main_font = typology_get_font_option( 'main_font' );
	$h_font = typology_get_font_option( 'h_font' );
    $nav_font = typology_get_font_option( 'nav_font' );
    
	$font_size_p = absint( typology_get_option( 'font_size_p' ) ); 
	$font_size_h1 = absint( typology_get_option( 'font_size_h1' ) );
	$font_size_h2 = absint( typology_get_option( 'font_size_h2' ) );
	$font_size_h3 = absint( typology_get_option( 'font_size_h3' ) );
	$font_size_h4 = absint( typology_get_option( 'font_size_h4' ) );
	$font_size_h5 = absint( typology_get_option( 'font_size_h5' ) );
	$font_size_h6 = absint( typology_get_option( 'font_size_h6' ) );
	$font_size_cover = absint( typology_get_option( 'font_size_cover' ) );
	$font_size_small = absint( typology_get_option( 'font_size_small' ) );
	$font_size_nav = absint( typology_get_option( 'font_size_nav' ) );
	$font_size_meta = absint( typology_get_option( 'font_size_meta' ) );
	$font_size_cover_dropcap = absint( typology_get_option( 'font_size_cover_dropcap' ) );
	$font_size_dropcap = absint( typology_get_option( 'font_size_dropcap' ) );
	$content_paragraph_width = absint( typology_get_option( 'content-paragraph-width' ) );


	/* Colors & stylings */

	$color_content_bg = esc_attr( typology_get_option('color_content_bg') );
	$color_body_bg = typology_get_option('style') == 'material' ? esc_attr( typology_get_option('color_body_bg') ) : $color_content_bg;
	
	$color_content_h = esc_attr( typology_get_option('color_content_h') );
	$color_content_txt = esc_attr( typology_get_option('color_content_txt') );
	$color_content_acc = esc_attr( typology_get_option('color_content_acc') );
	$color_content_meta = esc_attr( typology_get_option('color_content_meta') );



?>

/* Typography styles */

.edit-post-visual-editor,
body .editor-styles-wrapper,
.edit-post-visual-editor body .editor-styles-wrapper,
blockquote:before, 
q:before{
  font-family: <?php echo wp_kses_post( $main_font['font-family'] ); ?>;
  font-weight: <?php echo esc_attr( $main_font['font-weight'] ); ?>;
  <?php if ( isset( $main_font['font-style'] ) && !empty( $main_font['font-style'] ) ):?>
    font-style: <?php echo esc_attr( $main_font['font-style'] ); ?>;
  <?php endif; ?>
  <?php if ( !empty( $main_font['letter-spacing'] ) ):?>
      letter-spacing: <?php echo esc_attr( $main_font['letter-spacing'] ); ?>;
  <?php endif; ?>
}

.edit-post-visual-editor,
body .editor-styles-wrapper, .edit-post-visual-editor body .editor-styles-wrapper{
	color:<?php echo esc_attr( $color_content_txt ); ?>;
}

.edit-post-visual-editor 
body .editor-styles-wrapper{
	background:<?php echo esc_attr( $color_content_bg ); ?>;
	font-size: <?php echo esc_attr( $font_size_p ); ?>px;
}
body .editor-styles-wrapper,
body .editor-styles-wrapper p {
	font-size: <?php echo esc_attr( $font_size_p ); ?>px;
}
body .editor-styles-wrapper{
	background:<?php echo esc_attr( $color_content_bg ); ?>;	
}


body .editor-styles-wrapper h1, 
body .editor-styles-wrapper.edit-post-visual-editor .editor-post-title__block .editor-post-title__input,
body .editor-post-title .editor-post-title__input,
body .editor-styles-wrapper h2, 
body .editor-styles-wrapper h3, 
body .editor-styles-wrapper h4,
body .editor-styles-wrapper h5,
body .editor-styles-wrapper h6,
.wp-block-cover .wp-block-cover-image-text, .wp-block-cover .wp-block-cover-text, 
.wp-block-cover h2, .wp-block-cover-image .wp-block-cover-image-text, 
.wp-block-cover-image .wp-block-cover-text, .wp-block-cover-image h2,
.wp-block-button__link,
.wp-block-search__button{
  font-family: <?php echo wp_kses_post( $h_font['font-family'] ); ?>;
  font-weight: <?php echo esc_attr( $h_font['font-weight'] ); ?>;
  <?php if ( isset( $h_font['font-style'] ) && !empty( $h_font['font-style'] ) ):?>
  font-style: <?php echo esc_attr( $h_font['font-style'] ); ?>;
  <?php endif; ?>
}
<?php if ( !empty( $h_font['letter-spacing'] ) ):?>
body .editor-styles-wrapper h1, 
body .editor-styles-wrapper.edit-post-visual-editor .editor-post-title__block .editor-post-title__input,
body .editor-post-title .editor-post-title__input,
body .editor-styles-wrapper h2, 
body .editor-styles-wrapper h3, 
body .editor-styles-wrapper h4,
body .editor-styles-wrapper h5,
body .editor-styles-wrapper h6,
.wp-block-cover .wp-block-cover-image-text, .wp-block-cover .wp-block-cover-text, 
.wp-block-cover h2, .wp-block-cover-image .wp-block-cover-image-text, 
.wp-block-cover-image .wp-block-cover-text, .wp-block-cover-image h2{   
    letter-spacing: <?php echo esc_attr( $h_font['letter-spacing'] ); ?>;
}
<?php endif; ?>


body .editor-styles-wrapper h1,
body .editor-styles-wrapper.edit-post-visual-editor .editor-post-title__block .editor-post-title__input,
body .editor-post-title .editor-post-title__input {
  font-size: <?php echo esc_attr( $font_size_h1 ); ?>px;
}

body .editor-styles-wrapper h2{
  font-size: <?php echo esc_attr( $font_size_h2 ); ?>px;
}

body .editor-styles-wrapper h3{
  font-size: <?php echo esc_attr( $font_size_h3 ); ?>px;
}

body .editor-styles-wrapper h4{
  font-size: <?php echo esc_attr( $font_size_h4 ); ?>px;
}

body .editor-styles-wrapper h5,
blockquote, q {
  font-size: <?php echo esc_attr( $font_size_h5 ); ?>px;
}

body .editor-styles-wrapper h6{
  font-size: <?php echo esc_attr( $font_size_h6 ); ?>px;
}



.post-letter {
	font-size: <?php echo esc_attr( $font_size_dropcap ); ?>px;
}




body .editor-styles-wrapper h1, 
body .editor-styles-wrapper.edit-post-visual-editor .editor-post-title__block .editor-post-title__input,
body .editor-post-title .editor-post-title__input,
body .editor-styles-wrapper h2, 
body .editor-styles-wrapper h3, 
body .editor-styles-wrapper h4,
body .editor-styles-wrapper h5,
body .editor-styles-wrapper h6,
body .editor-styles-wrapper h1 a,
body .editor-styles-wrapper h2 a,
body .editor-styles-wrapper h3 a,
body .editor-styles-wrapper h4 a,
body .editor-styles-wrapper h5 a,
body .editor-styles-wrapper h6 a{
	color:<?php echo esc_attr( $color_content_h ); ?>;
}


/* General styles */

body .editor-styles-wrapper .wp-block a{
	color: <?php echo esc_attr( $color_content_acc ); ?>;
}

/* Buttons styles */

.mks_read_more a{
	color:<?php echo esc_attr( $color_content_bg ); ?>;
	background: <?php echo esc_attr( $color_content_acc ); ?>;
	border:1px solid <?php echo esc_attr( $color_content_acc ); ?>;
}

.wp-block-button__link{
	background: <?php echo esc_attr( $color_content_acc ); ?>;
}



body .editor-styles-wrapper .wp-block table.wp-block-table{
	border-color: <?php echo typology_hex2rgba($color_content_txt, 0.3); ?>;
}
body .editor-styles-wrapper .wp-block-table:not(.is-style-stripes) td, 
body .editor-styles-wrapper .wp-block-table:not(.is-style-stripes) th{
	border: 1px solid <?php echo typology_hex2rgba($color_content_txt, 0.3); ?>;
}

blockquote:after, blockquote:before, q:after, q:before{
    -webkit-box-shadow: 0 0 0 10px <?php echo esc_attr( $color_content_bg ); ?>;	
    box-shadow: 0 0 0 10px <?php echo esc_attr( $color_content_bg ); ?>;		
}

pre,
.entry-content #mc_embed_signup{
	background: <?php echo typology_hex2rgba($color_content_txt, 0.1); ?>;		
}


/* Content width*/

.edit-post-visual-editor .wp-block{
	max-width: 750px;
}
.post-type-page .edit-post-visual-editor .wp-block{
	max-width: 750px;
}
.edit-post-visual-editor .wp-block[data-align="wide"],
.post-type-page .edit-post-visual-editor .wp-block[data-align="wide"]{
	max-width: 1038px;
}
.edit-post-visual-editor .wp-block[data-align="full"],
.post-type-page .edit-post-visual-editor .wp-block[data-align="full"]{
	max-width: none;
}


.wp-block-image figcaption,
.wp-block-audio figcaption,
.wp-block-tag-cloud a{
  color: <?php echo esc_attr($color_content_txt); ?>;  
}

/* Blockquote*/
body .editor-styles-wrapper blockquote:before{
	background:<?php echo esc_attr( $color_content_acc ); ?>;	
}
.wp-block-quote:not(.is-large):not(.is-style-large){
	border-left: 1px solid rgba(0,0,0,.1);
}
.wp-block-quote.is-large, .wp-block-quote.is-style-large{
	border: 1px double rgba(0, 0, 0, 0.1);
}
.wp-block-pullquote:not(.is-style-solid-color){
	border-top:2px solid <?php echo esc_attr($color_content_txt); ?>;  
	border-bottom:2px solid <?php echo esc_attr($color_content_txt); ?>;  
}
.wp-block-pullquote.is-style-solid-color{
	background: <?php echo esc_attr( $color_content_acc ); ?>;
	color: <?php echo esc_attr($color_content_bg); ?>; 	
}

/* Code and preformated*/

.wp-block-code,
body .editor-styles-wrapper code,
body .editor-styles-wrapper pre,
body .editor-styles-wrapper pre h2{
	background: <?php echo typology_hex_to_rgba($color_content_txt, 0.05); ?>;
	color: <?php echo esc_attr( $color_content_txt ); ?>;
}
.wp-block-code .editor-plain-text{
  background: transparent;
}
.wp-block-separator{
	border-color: <?php echo typology_hex2rgba($color_content_txt, 0.3); ?>;
	border-bottom-width: 1px;	
}

.wp-block-calendar table th {
    border-bottom:1px solid <?php echo typology_hex2rgba($color_content_txt, 0.3); ?>;
		border-right:1px solid #e2e4e7;<?php echo typology_hex2rgba($color_content_txt, 0.3); ?>;
}
.wp-block .wp-block-search__input{
	border:1px solid <?php echo typology_hex2rgba($color_content_txt, 0.3); ?>;	
}
.wp-block .wp-block-search__button{
	background: <?php echo esc_attr( $color_content_acc ); ?>;
	color: <?php echo esc_attr($color_content_bg); ?>; 
}

<?php

/* Apply uppercase options */

$uppercase = typology_get_option( 'uppercase' );
if ( !empty( $uppercase ) ) {
  foreach ( $uppercase as $text_class => $val ) {
    if ( $val ){
      echo 'body .editor-styles-wrapper '. str_replace(', ',', body .editor-styles-wrapper ', $text_class .'{text-transform: uppercase;}' );
    } else {
      echo 'body .editor-styles-wrapper '. str_replace(', ',', body .editor-styles-wrapper ', $text_class .'{text-transform: none;}' );
    }
  }
}

?>

<?php if( $uppercase['h1, h2, h3, h4, h5, h6, .wp-block-cover-text, .wp-block-cover-image-text'] ): ?>
	
body .editor-styles-wrapper.edit-post-visual-editor .editor-post-title__block .editor-post-title__input,
body .editor-post-title .editor-post-title__input {
	text-transform:uppercase;
}

<?php endif; ?>



<?php if(!array_key_exists('.typology-button', $uppercase) || $uppercase['.typology-button'] ): ?>
	
.wp-block-button__link,
.wp-block-search__button{
	text-transform: uppercase;
}

<?php endif; ?>