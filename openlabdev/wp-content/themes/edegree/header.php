<?php
global $wp_version, $shortname;;

//Get the custom css string
$body_css = get_BodyCSS();

//Set a class name for internal pages
$subclass = '';
if(!is_home() || isset($_GET['paged']))
	$subclass = 'internalpage';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title>
	<?php echo ($title = wp_title('&#8211;', false, 'right')) ? $title : ''; ?><?php echo ($description = get_bloginfo('description')) ? $description : bloginfo('name'); ?>
</title>

<meta name="author" content="<?php bloginfo('name'); ?>" />
<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/images/favicon.ico" type="image/x-icon" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_enqueue_script('jquery'); ?>
<?php if (is_singular()) wp_enqueue_script('comment-reply'); ?>
<?php wp_head(); ?>

<link href="<?php bloginfo('template_url'); ?>/style.css" type="text/css" rel="stylesheet" />
<?php if(get_skinDir()):?>
<link href="<?php bloginfo('template_url'); ?>/images/<?php echo get_skinDir();?>/style.css" type="text/css" rel="stylesheet" />
<?php endif;?>

<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/superfish.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/functions.js"></script>

<!--[if gte IE 5.5]>
<style type="text/css">.post img, .page img, .customhtml img {width: expression(this.width > 495 ? 495: true) }</style>
<![endif]-->
</head>

<body <?php echo (version_compare($wp_version, '2.8', '>=')) ? body_class($subclass) : 'class="'.$subclass .'"'?> <?php echo ($body_css) ? 'style="'.$body_css. '"' : ''?>>
<div id="bg" <?php echo (get_option('tbf2_header_image_file')) ? 'style="background-image:url('.get_option('tbf2_header_image_file'). ')"' : ''?>>
  <div id="wrapper">
    <div id="shadow">
     
        <div id="header">
          <h1 id="logo">
			<?php if (get_option('tbf2_logo_header') == "yes" && get_option('tbf2_logo')) { ?>
                    <a href="<?php bloginfo('url'); ?>/"><img src="<?php echo get_option('tbf2_logo'); ?>" title="<?php bloginfo('name'); ?> - 
					<?php bloginfo('description'); ?>" alt="<?php bloginfo('name'); ?> - <?php bloginfo('description'); ?>" /></a>
            <?php } else { //If no logo, show the blog title and tagline by default ?>
            	<a href="<?php bloginfo('url'); ?>" id="blogname" style="background:none;text-indent:0;width:auto"><span class="blod"><?php bloginfo('name'); ?></span> <?php bloginfo('description'); ?></a>
            <?php } ?>
          </h1>
          
			<?php //Search box 
                if(get_option('tbf2_search_header') == "yes" || ((isset($_GET['preview']) && isset($_GET['template'])) || $_SERVER['HTTP_HOST'] == 'wp-themes.com')) : ?>
                <?php include(TEMPLATEPATH.'/searchform.php'); ?>
            <?php endif; ?>          

        </div>
        
        <?php if (is_home() && !isset($_GET['paged'])) :?>
        <div id="featured-zone">            
        	<div id="slidespot">
            
				<?php if(function_exists('gallery_styles')) : //Slideshow plugin activated ?>
                        <?php include (ABSPATH . '/wp-content/plugins/featured-content-gallery/gallery.php'); ?>
                        
                <?php elseif(isset($_GET['preview']) && isset($_GET['template'])): //Theme is being previewed ?>
                		<img src="<?php bloginfo('template_url')?>/images/slidespot.jpg" alt="Install Featured Content Gallery Plugin to Replace This Image" />
                        
                <?php elseif(get_option('tbf2_custom_slidespot') == 'yes' && get_option('tbf2_custom_html_slidespot')): //Custom content is defined ?>
                        <?php echo get_option('tbf2_custom_html_slidespot');?>
                        
                <?php else: ?>
                		<img src="<?php bloginfo('template_url')?>/images/slidespot.jpg" alt="Install Featured Content Gallery Plugin to Replace This Image" />
                <?php endif;?>
                
            </div>
            
            <div id="featured-wiz">
                <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Top Custom Content (Home)") ) : ?>
                	<div class="widget">
                    	<h2>ABOUT US</h2>
                        <p>To replace this text, go to "Widgets" page and start adding your own widgets to the "Top Custom Widget".</p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas dolor nibh, feugiat ut fringilla eget, porttitor ac nisl. Phasellus non urna tellus. Suspendisse varius pharetra purus sit amet aliquet. Donec fermentum mi quis dolor fermentum euismod. </p>
                        <p>Vestibulum purus nunc, mollis pharetra aliquet ac, sodales ut risus.</p>
                        <p style="text-align:right"><img src="<?php bloginfo('template_url')?>/images/<?php echo (get_skinDir()) ? get_skinDir().'/' : ''?>btn-learn-more.png" alt="" /></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div id="container">
			<div id="<?php echo (!$subclass) ? 'container-shoulder' : 'container-shoulder-plain';?>">
            	<div id="left-col">