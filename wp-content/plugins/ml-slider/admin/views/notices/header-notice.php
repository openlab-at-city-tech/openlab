<?php if (!defined('ABSPATH')) {
    die('No direct access.');
} ?>

<div class="updraft-ad-container ml-discount-ad notice updated">
	<div class="updraft_notice_container">
		<div class="updraft_advert_content_left">
			<img src="<?php echo METASLIDER_BASE_URL.'admin/images/'.$image;?>" width="60" height="60" alt="<?php _e('Logo', 'ml-slider');?>" />
		</div>
		<div class="updraft_advert_content_right">
			<div class="ml-discount-ad-title">
                <?php
                echo $title;
                if ( !empty ( $button_link ) ) {
                    echo $this->get_button_link( $button_link, $button_meta );
                }
                ?>
            </div>
            <div class="updraft-advert-dismiss">
                <a class="underline text-blue-dark" href="#" onclick="jQuery('.updraft-ad-container').slideUp(); jQuery.post(ajaxurl, {action: 'notice_handler', ad_identifier: '<?php echo $dismiss_time;?>', _wpnonce: metaslider_notices_handle_notices_nonce });"><?php echo sprintf('%s', __('Dismiss', 'ml-slider')); echo ('' !== $hide_time) ? sprintf(' (%s)', $hide_time) : ''; ?></a>
            </div>
		</div>
	</div>
	<div class="clear"></div>
</div>
