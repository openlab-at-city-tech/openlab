		<!-- begin sidebar -->
		<div id="sidebar">
			<?php if(!is_home() || isset($_GET['paged'])): ?>
                <div id="top-content-internal">
                    <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Top Custom Content (Internal)') ) : ?>
                        <div class="widget">
                            <h2>SUBSCRIBE</h2>
                            <p>Custom contet area (You decide what you want here). To replace this text, go to "Widgets" page and start adding your own widgets to the "Top Custom Widget".</p>
                            <p>The top right corner is an effective spot to place your subscription forms, banner, or just about any action hat you desire users to take. </p>
                            <p><input type="text" class="textfield" name="name" value="Name" onfocus="clearDefault(this)" onblur="restoreDefault(this)" /><br /><input type="text" class="textfield" name="email" value="Primary email" onfocus="clearDefault(this)" onblur="restoreDefault(this)" /></p>
                            <p style="text-align:right"><input type="image" id="opt_submit" name="submit" value="Submit" src="<?php bloginfo('template_url')?>/images/<?php echo (get_skinDir()) ? get_skinDir().'/' : ''?>btn-signup.png" /></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div id="socialize-icons">
                <ul>
                    <?php if(strtolower(get_option('tbf2_icon_facebook')) !='hide'):?>
                    	<li id="icon-facebook"><a href="<?php echo (get_option('tbf2_icon_facebook')) ? get_option('tbf2_icon_facebook') : '#'?>" rel="nofollow" target="_blank">facebook</a></li>
					<?php endif; ?>
                    <?php if(strtolower(get_option('tbf2_icon_twitter')) !='hide'):?>	
	                    <li id="icon-twitter"><a href="<?php echo (get_option('tbf2_icon_twitter')) ? get_option('tbf2_icon_twitter') : '#'?>" rel="nofollow" target="_blank">twitter</a></li>
					<?php endif; ?>
                    <?php if(strtolower(get_option('tbf2_icon_rss')) !='hide'):?>                    	
                    	<li id="icon-rss"><a href="<?php echo (get_option('tbf2_icon_rss')) ? get_option('tbf2_icon_rss') : bloginfo('rss2_url')?>">RSS Feed</a></li>
					<?php endif; ?>	
                    <?php if(strtolower(get_option('tbf2_icon_youtube')) !='hide'):?>
                    	<li id="icon-youtube"><a href="<?php echo (get_option('tbf2_icon_youtube')) ? get_option('tbf2_icon_youtube') : '#'?>" rel="nofollow" target="_blank">YouTube</a></li>
                    <?php endif; ?>	
                </ul>
            </div>
            
        	  
			<ul>
			  <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar("Sidebar")) : //If no user selected widgets, display below ?>
              	<li id="recent-post-default" class="widget">
                	<h2><?php _e("RECENT POSTS"); ?></h2>
	                <ul>
						<?php wp_get_archives('title_li=&type=postbypost&limit=10'); ?>
                    </ul>
                </li>
                
                <li id="categories" class="widget">
					<h2><?php _e("CATEGORIES"); ?></h2>
					<ul>
					  <?php wp_list_categories('orderby=name&title_li=&depth=2'); ?>
					</ul>
				</li>
				<li id="archives" class="widget">
					<h2><?php _e("ARCHIVES"); ?></h2>
					<ul>
					  <?php wp_get_archives('type=monthly'); ?>	
					</ul>
				</li>
				<?php /* If this is the frontpage */ if ( is_home() || is_page() ) { ?>
                    <?php $args = array('title_before'=>'<h2>', 'title_after'=>'</h2>', 'class'=>'widget',); ?>
                    <?php wp_list_bookmarks($args); ?>
    
                    <li id="blogmeta" class="widget"><h2>Meta</h2>
                    <ul>
                        <?php wp_register(); ?>
                        <li><?php wp_loginout(); ?></li>
                        <li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
                        <li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
                        <li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
                        <?php wp_meta(); ?>
                    </ul>
                    </li>
                <?php } ?>
                
			  <?php endif; ?>	
              
              <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('250x? Side Banner Space') ) : ?>
              <?php endif; ?>
                	
			</ul>
		</div><!-- end sidebar -->