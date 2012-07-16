<?php
/* Weaver:
  Top - Menu bar
*/
if (!weaver_getopt('ttw_hide_menu') && !weaver_is_checked_page_opt('ttw-hide-menus')) {
		    if (weaver_getopt('ttw_move_menu')) { 	/* TTW: move menu */ ?>
			<div id="nav-top-menu"><div id="access" role="navigation">
<?php			/* add html to menu left */
			$add_html = weaver_getopt('ttw_menu_addhtml-left');
			if (!empty($add_html)) {
			    echo('<div class="menu-add-left">');
			    echo(do_shortcode($add_html));
			    echo('</div>');
			}

			/*  NAVIGATION MENUS:  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.
			    The menu assiged to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */
			if (weaver_getopt('ttw_use_superfish'))
				wp_nav_menu( array( 'container_class' => 'menu', 'theme_location' => 'primary', 'menu_class' => 'sf-menu', 'fallback_cb' => 'weaver_page_menu' ) );
			else
				wp_nav_menu( array( 'container_class' => 'menu', 'theme_location' => 'primary' ) );

			/* add html/search to menu */
			$add_div = true;
			$add_enddiv = false;
			$add_html = weaver_getopt('ttw_menu_addhtml');

			if (!empty($add_html)) {
			    echo('<div class="menu-add">'); $add_div = false;
			    echo(do_shortcode($add_html));
			    $add_enddiv = true;
			}
                        if (weaver_getopt_plus('wvp_add_social_to_menu') > 0) {
                            if ($add_div) echo('<div class="menu-add">'); $add_div = false;
                            $val = weaver_getopt_plus('wvp_add_social_to_menu');
                            $width = $val * 28;
                            echo do_shortcode(sprintf('<div style="width:%spx; padding-right:4px;display:inline;">[weaver_social number=%d]</div>',
                                $width,$val));
                            $add_enddiv = true;
                        }
			if (weaver_getopt('ttw_menu_addsearch')) {
			    if ($add_div) echo('<div class="menu-add">'); $add_div = false;
			    if (function_exists('weaver_plus_search_form')) {
                                echo '<span style="padding-top:8px !important;padding-right:4px !important;display:inline-block;">';
                                echo weaver_plus_search_form('',120);
                                echo '</span>';
                            } else {
                                echo '<span style="padding-top:4px !important;padding-right:4px !important;display:inline-block;">';
                                get_search_form();
                                echo '</span>';
                            }
			    $add_enddiv = true;
			}
			if (weaver_getopt('ttw_menu_addlogin')) {
				if ($add_div) echo('<div class="menu-add">'); $add_div = false;
				wp_loginout();
				$add_enddiv = true;
			}
		        if ($add_enddiv) echo('</div>');
		        ?>
			</div></div><!-- #access -->
		    <?php
		    }  else {
                        if (has_nav_menu('secondary')) {
		    ?>
			<div id="nav-top-menu"><div id="access2" role="navigation">
		        <?php
			if (weaver_getopt('ttw_use_superfish'))
			    wp_nav_menu( array( 'container_class' => 'menu', 'theme_location' => 'secondary', 'fallback_cb' => '', 'menu_class' => 'sf-menu' ) );
			else
			    wp_nav_menu( array( 'container_class' => 'menu', 'theme_location' => 'secondary', 'fallback_cb' => '' ) );
			?>

			</div></div><!-- #access2 -->
		<?php
                        }
		    }  /* end move menu if-else */
		}  /* end hide menus */
?>
