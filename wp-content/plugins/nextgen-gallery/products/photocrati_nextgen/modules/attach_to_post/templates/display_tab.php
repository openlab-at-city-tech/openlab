<div id='ngg_page_content'>

	<div id="errors">	
	</div>

	<div class="ngg_page_content_menu" class="ngg_advanced">

		<?php foreach($tabs as $tab): ?>
            <a href='javascript:void(0)' data-id='<?php esc_attr_e( $tab['id'] ) ?>'><?php esc_html_e( $tab['title']) ?></a>
        <?php endforeach ?>

	</div>

	<div class="ngg_page_content_main"">

		<?php foreach($tabs as $tab): ?>
            <div data-id='<?php esc_attr_e($tab['id']) ?>'>
                <h3 class="accordion_tab" id="<?php esc_html_e( $tab['id']) ?>"><?php esc_html_e( $tab['title']) ?></h3>
                <div id="<?php echo esc_attr($tab['id']) ?>_content"><?php echo $tab['content']; ?></div>
            </div>
        <?php endforeach ?>

        <?php if ( !defined('NGG_PRO_PLUGIN_VERSION') && !defined('NGG_PLUS_PLUGIN_VERSION') && !is_multisite() ) { ?>
	        <div class="ngg_igw_promo">
	        	<p><?php esc_html_e('Want Mosaic, Masonry, Tiled and other layouts?', 'nggallery'); ?></p>
	        	<p><a href="https://www.imagely.com/wordpress-gallery-plugin/nextgen-pro/?utm_source=ngg&utm_medium=ngguser&utm_campaign=igw" target="_blank"><?php esc_html_e('Get NextGEN Pro', 'nggallery'); ?></a></p>
	        	<p class="ngg_igw_coupon"><?php esc_html_e('Use ILOVENG for 30% off!', 'nggallery'); ?></p>
	        </div>
        <?php } ?> 
		<p class="wp-core-ui">
			<input type="button" class="button button-primary button-large ngg_display_tab_save" id="save_displayed_gallery" value="<?php if ($displayed_gallery->id()) { _e('Save Changes', 'nggallery'); } else { _e('Insert Gallery', 'nggallery'); } ?>"/>
		</p>
        <div class="ngg_igw_video">
        	<p class="ngg_igw_video_open button-primary"><?php esc_html_e('Need a quick tutorial?', 'nggallery'); ?></p>
        	<div class="ngg_igw_video_inner">
        		<span class="ngg_igw_video_close"><?php esc_html_e('Click to Close', 'nggallery'); ?></span>
        	</div>
        </div>
	</div>

</div>