<?php
/*---------------------------------------*/
echo '<div id="postbox-container-1" class="postbox-container">';
echo '<div id="side-sortables" class="meta-box-sortables">';

/* class="postbox closed"
----------------------------------------*/
echo '<div class="postbox"><div class="handlediv" title="'.__('Click to toggle', $this->hook) .'"><br /></div><h3 class="hndle"><span>'. $this->pluginname . ' <small style="float: right">v'. $this->pluginversion . '</small></span></h3>';
echo '<div class="inside">';
echo '<a href="'. $this->sbar_homepage . '" target="_blank"><div id="logo"></div></a>';
echo '<a class="sm_button sm_autor" href="'. $this->sbar_homepage . '" target="_blank">' . __( 'Plugin Homepage', $this->hook ) . '</a>';
echo '<a class="sm_button sm_code" href="'. $this->sbar_supportpage . '" target="_blank">' . __( 'Suggest a Feature', $this->hook ) . '</a>';
echo '<a class="sm_button sm_bug" href="'. $this->sbar_supportpage . '" target="_blank">' . __( 'Report a Bug', $this->hook ) . '</a>';
echo '<a class="sm_button sm_lang" href="' . $this->sbar_glotpress . '" target="_blank">' . __( 'Help translating it', $this->hook ) . '</a>';
echo '</div></div>';

//----------------------------------------
echo '<div class="postbox"><div class="handlediv" title="'.__('Click to toggle', $this->hook) .'"><br /></div><h3 class="hndle"><span>'. __( 'Do you like this Plugin?', $this->hook ) . '</span></h3>';
echo '<div class="inside">'.__( 'Please help to support continued development of this plugin!', $this->hook );
echo '<div align="center"><strong style="font-size: 15px">' . __( 'DONATE', $this->hook ) . '</strong><br />';
echo '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=' . $this->sbar_paypalcode . '" target="_blank"><img src="'.plugins_url( 'images/h3_icons/paypal.png', dirname(__FILE__)).'" width="101" height="64" border="0"  alt=""/></a>';
echo '<a href="http://www.neteller.com/personal/send-money/" id="cl" target="_blank" title="fabrix@fabrix.net"><img src="'. plugins_url( 'images/h3_icons/neteller.png', dirname(__FILE__)).'" width="102" height="64" border="0" alt=""  style="margin-left: 25px" /></a></div>';
echo '<ul><li><a class="sm_button sm_star" href="http://wordpress.org/extend/plugins/wp-twitter" target="_blank">'. __( 'Rate the plugin 5 star on WordPress.org', $this->hook ) .'.</a></li>';
echo '<li><a class="sm_button sm_link" href="'. $this->sbar_homepage . '" target="_blank">'. __( 'Blog about it and link to the plugin page', $this->hook ) .'.</a></li></ul>';
echo '<div align="center"><a href="http://api.addthis.com/oexchange/0.8/forward/facebook/offer?url='. $this->sbar_homepage . '&amp;title='. $this->pluginname .'&amp;pubid=ra-52eb02b34be83059" data-width="850" data-height="500" rel="1" id="pop_1" class="newWindow" title="'. __( 'Share on', $this->hook ) .' Facebook"><img src="'. plugins_url( 'images/h3_icons/facebook.png', dirname(__FILE__)).'" width="32" height="32" border="0"  alt="*" style="margin-right: 15px" /></a>';
echo'<a href="http://api.addthis.com/oexchange/0.8/forward/google_plusone_share/offer?url='. $this->sbar_homepage .'&amp;pubid=ra-52eb02b34be83059" data-width="500" data-height="600" rel="1" id="pop_2" class="newWindow" title="'. __( 'Share on', $this->hook ) .' Google Plus"><img src="'. plugins_url( 'images/h3_icons/googleplus.png', dirname(__FILE__)).'" width="32" height="32" border="0" alt="*" style="margin-right: 15px" /></a>';
echo '<a href="http://api.addthis.com/oexchange/0.8/forward/twitter/offer?title=Plugin '. $this->pluginname . '&amp;url='. $this->sbar_homepage .'&amp;pubid=ra-52eb02b34be83059" data-width="500" data-height="690" rel="1" id="pop_5" class="newWindow" title="'. __( 'Share on', $this->hook ) .' Twitter"><img src="'. plugins_url( 'images/h3_icons/twitter.png', dirname(__FILE__)).'" width="32" height="32" border="0" alt="*" style="margin-right: 15px" /></a>';
echo '<a href="http://api.addthis.com/oexchange/0.8/offer?title='. $this->pluginname . '&amp;url='. $this->sbar_homepage .'&amp;pubid=ra-52eb02b34be83059" data-width="500" data-height="690" rel="1" id="pop_6" class="newWindow" title="'. __( 'Share on', $this->hook ) .' Addthis"><img src="'. plugins_url( 'images/h3_icons/addthis.png', dirname(__FILE__)).'" width="32" height="32" border="0" alt="*" /></a></div>';
echo '</div></div>';

//----------------------------------------
echo '<div class="postbox"><div class="handlediv" title="'.__('Click to toggle', $this->hook) .'"><br /></div><h3 class="hndle"><span>'. __( 'Translation', $this->hook ) . '</span></h3>';
echo '<div class="inside">';
if (WPLANG == '' || WPLANG == 'en' || WPLANG == 'en_US'  ){
echo '<strong>Would you like to help translating this plugin?</strong><br/> Contribute a translation using the GlotPress web interface - no technical knowledge required (<strong><a href="' . $this->sbar_glotpress . '" target="_blank">how to</a></strong>)';
} else {
echo '<span class="ico_button ico_button_'.WPLANG.'">' .sprintf(__('Translated by: <a href="%s">YOUR NAME</a>', $this->hook), __('http://YOUR-LINK.COM', $this->hook) ) . '</span>';
echo '<p>' . __( 'If you find any spelling error in this translation or would like to contribute', $this->hook ) . ', <a href="' . $this->sbar_glotpress . '" target="_blank">' . __( 'click here', $this->hook ) . '.</a></p>';
}
echo '</div></div>';

//----------------------------------------
echo '<div class="postbox"><div class="handlediv" title="'.__('Click to toggle', $this->hook) .'"><br /></div><h3 class="hndle"><span>'. __( 'Notices', $this->hook ) . '</span></h3>';
echo '<div class="inside">';

$rss = @fetch_feed( $this->sbar_rss );
     if ( is_object($rss) ) {
        if ( is_wp_error($rss) ) {
            echo 'Newsfeed could not be loaded.';
           } else {
echo '<ul>';
		foreach ( $rss->get_items(0, 5) as $item ) {
    		$link = $item->get_link();
    		while ( stristr($link, 'http') != $link )
    			$link = substr($link, 1);
    		$link = esc_url(strip_tags($link));
    		$title = esc_attr(strip_tags($item->get_title()));
    		if ( empty($title) )
    			$title = __('Untitled');
			$date = $item->get_date();
            $diff = '';
			if ( $date ) {
                $diff = human_time_diff( strtotime($date, time()) );
				if ( $date_stamp = strtotime( $date ) )
					$date =  date_i18n( get_option( 'date_format' ), $date_stamp );
				else
					$date = '';
			}
echo '<li style=" margin-top: -2px; margin-bottom: -2px"><a class="sm_button sm_bullet" title="'. $date .'" target="_blank" href="'. $link .'">'. $title.' <em class="none">'. $diff.'</em></a></li>';
    }
       echo'</ul>';
   } // if feed error
}
echo '</div></div>';
//----------------------------------------
echo '</div></div>';