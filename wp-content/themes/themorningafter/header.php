<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<?php global $woo_options; ?>

<title><?php woo_title(); ?></title>
<?php woo_meta(); ?>
    
<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" type="text/css" media="screen, projection" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php $GLOBALS['feedurl'] = $woo_options['woo_feed_url']; if ( ! empty( $feedurl ) ) { echo $feedurl; } else { echo get_bloginfo_rss( 'rss2_url' ); } ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	
<!--[if lt IE 7]>
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/ie.css" type="text/css" media="screen, projection">
<![endif]-->
    
<?php if ( is_singular() ) { wp_enqueue_script('comment-reply'); } ?>
    
<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

    <div class="container">
    
        <div id="header" class="column span-14">
        
            <div id="logo" class="column first">

                
        	<?php if ($woo_options['woo_texttitle'] == 'false' ) { $logo = $woo_options['woo_logo']; 
        			if ( ! empty( $logo ) ) { ?>
				<a href="<?php echo home_url( '/' ); ?>" title="<?php bloginfo( 'description' ); ?>">
        			<img src="<?php echo $logo; ?>" alt="<?php bloginfo( 'name' ); ?>" />
        		</a>
        	<?php }        	
        		} else { ?>
        	<div class="title">
        	<?php if( is_singular() ) { ?>
            	<span class="site-title"><a href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'name' ); ?></a></span>
        	<?php } else { ?>
            	<h1 class="site-title"><a href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
        	<?php } ?>
            	<div class="site-description desc"><?php bloginfo( 'description' ); ?></div>
			</div>
			<?php } ?> 
                    
            </div>
            
            <div id="search_menu" class="column span-6 border_left last push-0">
            
                <div id="search" class="column first">
                    <h3 class="mast4"><?php _e( 'Search', 'woothemes' ); ?></h3>
                    
                    <div id="search-form">                  
                        <form method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
                            
						<div><label for="s" class="none"><?php _e( 'Search for','woothemes' ); ?>:</label>
						<input type="text" name="s" id="s" class="search_input" value="<?php the_search_query(); ?>" />
						
						<label for="searchsubmit" class="none"><?php _e( 'Go','woothemes' ); ?></label>
						<input type="submit" id="searchsubmit" class="submit_input" value="Search" /></div>
                            
                        </form>
                    </div>
                </div>
                
				<ul id="menu">
				
					<?php
					
					$links = array( 'home', 'about', 'archives', 'subscribe', 'contact' );
					
					foreach ( $links as $curlink ) {
					
						$link = trim( $woo_options['woo_nav_'.$curlink] );
						
						if ( ( $link != '' ) && ( $link != '#' ) ) {
						
							echo '<li><span class="' . $curlink . '"><a href="' . $woo_options['woo_nav_'.$curlink] . '">'.__( ucfirst($curlink ),'woothemes' ) . '</a></span></li>'."\n";
						
						}
					
					}
					
					?>
				
				</ul>
            
            </div>
        
        </div>
        <div class="clear"></div>
       
		<?php // Only supports WordPress Menus
		if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'primary-menu' ) ) { ?>
			<div id="navigation" class="col-full">
				<?php
				wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav fl', 'theme_location' => 'primary-menu' ) );
				?>
				<ul class="rss fr">
        			<?php $email = $woo_options['woo_subscribe_email']; if ( $email ) { ?>
        			<li class="sub-email"><a href="<?php echo $email; ?>" target="_blank"><?php _e('Subcribe by Email', 'woothemes') ?></a></li>
        			<?php } ?>
        			<li class="sub-rss"><a href="<?php if ( $GLOBALS['feedurl'] ) { echo $GLOBALS['feedurl']; } else { echo get_bloginfo_rss( 'rss2_url' ); } ?>"><?php _e( 'Subscribe to RSS', 'woothemes' ); ?></a></li>
        		</ul>

			</div><!-- /#navigation -->
		<?php
		} 
        ?>