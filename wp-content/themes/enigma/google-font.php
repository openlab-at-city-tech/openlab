<?php $wl_theme_options = weblizar_get_options(); ?>

<style>
.logo a, .logo p{
	font-family : <?php echo $wl_theme_options['main_heading_font'] ?> ;
}

.navbar-default .navbar-nav li a{
	font-family : <?php echo $wl_theme_options['menu_font'] ?> !important;
}

.carousel-text h1, .enigma_heading_title h3, .enigma_service_detail h3,
.enigma_home_portfolio_caption h3 a, .enigma_blog_thumb_wrapper h2 a,
.enigma_footer_widget_title, .enigma_header_breadcrum_title h1,
.enigma_fuul_blog_detail_padding h2 a, .enigma_fuul_blog_detail_padding h2,
.enigma_sidebar_widget_title h2{
	font-family : <?php echo $wl_theme_options['theme_title'] ?>;
}

.head-contact-info li a, .carousel-list li, .enigma_blog_read_btn,
.enigma_service_detail p, .enigma_blog_thumb_wrapper p, .enigma_blog_thumb_date li, .breadcrumb,
.breadcrumb li, .enigma_post_date span.date, .enigma_blog_comment a,
.enigma_fuul_blog_detail_padding p, #wblizar_nav, .enigma_comment_title h3,
.enigma_comment_detail_title, .enigma_comment_date, .enigma_comment_detail p, .reply,
.enigma_comment_form_section h2, .logged-in-as, .enigma_comment_form_section label, #enigma_send_button,
.enigma_blog_full p, .enigma_sidebar_link p a, .enigma_sidebar_widget ul li a, .enigma_footer_widget_column ul li a,
.enigma_footer_area p, .comment-author-link, .enigma_sidebar_widget ul li, .enigma_footer_widget_column .textwidget, .textwidget,
.enigma_callout_area p, .enigma_callout_area a, #searchform .form-control, .tagcloud a, #wp-calendar, 
.enigma_footer_widget_column .tagcloud a, .enigma_footer_widget_column ul#recentcomments li a, .enigma_footer_widget_column ul#recentcomments li{
	font-family : <?php echo $wl_theme_options['desc_font_all'] ?> ;
}
</style>