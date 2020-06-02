<div data-notification-name="<?php echo esc_attr($notice_name)?>" class="ngg_admin_notice <?php echo esc_attr($css_class)?>">
	<p><?php echo $html ?></p>
	<?php if ($show_dismiss_button): ?>
	<p><a class='dismiss' href="#"><?php esc_html_e(__('Dismiss', 'nggallery')) ?></a></p>
	<?php endif ?>
</div>