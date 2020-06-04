	<?php

	$sfsi_show_via_shortcode  = isset($option9['sfsi_show_via_shortcode']) && !empty($option9['sfsi_show_via_shortcode']) ? $option9['sfsi_show_via_shortcode'] : "no";

	$checked 	 = '';
	$label_style = 'style="display:none;"';

	if ("yes" == $sfsi_show_via_shortcode) {
		$checked 	 = 'checked="true"';
		$label_style = 'style="display:block;"';
	}

	?>

	<li class="sfsi_show_via_shortcode">

		<div class="radio_section tb_4_ck" onclick="checkforinfoslction_checkbox(this);">
			<input name="sfsi_show_via_shortcode" <?php echo $checked; ?> type="checkbox" value="<?php echo $sfsi_show_via_shortcode; ?>" class="styled" />
		</div>

		<div class="sfsi_right_info">

			<p>
				<span class="sfsi_toglepstpgspn">Place via shortcode</span><br>

				<div class="kckslctn" <?php echo $label_style; ?>>

					<p>Please use the shortcode <b>[DISPLAY_ULTIMATE_SOCIAL_ICONS]</b> to place the icons anywhere you want.</p>

					<p>Or, place the icons directly into our (theme) codes by using <b>&lt;?php echo do_shortcode('[DISPLAY_ULTIMATE_SOCIAL_ICONS]'); ?&gt;</b></p>

					<p>Want to show icons <b>vertically</b> or <b>centralize the icons</b> in the shortcode container? Or need <b>different settings for mobile</b>? Check out the <a href="https://www.ultimatelysocial.com/usm-premium/" target="_blank"><b>Premium Plugin.</b></a></p>

				</div>
			</p>
		</div>
	</li>
	<li class="sfsi_show_via_onhover">
		<div class="radio_section tb_4_ck" onclick="checkforinfoslction_checkbox(this)" ;>
			<!-- <span class="checkbox" style="background-position:0px 0px!important;width:31px"></span> -->
			<input name="sfsi_show_theme_heade" type="checkbox" value="yes" class="styled" />
		</div>
		<div class="sfsi_right_info">
			<p style="display: inline-flex;">
				<span class="sfsi_toglepstpgspn" style="display:inline-block;float:left;">In your theme's header</span>
			</p>
			<div class="kckslctn" style="display: none;">
				<p>
					Placing icons in your theme's header can be tricky / technical as CSS & PHP know-how is required (as every theme is different, no "automatic" placement is possible).
				</p>
				<p>
					You can try via shortcode (see above), however if you don't want any hassle, check out our <a class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)"><span style="text-decoration: underline;cursor: pointer;color:#5A6570">Premium plugin</span></a> where - as part of our service - we can place the icons for you, making theme adjustments
					where needed. This ensures the perfect appearance (on all devices) for your icons. <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=theme_header_placement&utm_medium=link" style="cursor:pointer; color: #1a1d20 !important;border-bottom: 1px solid #12a252;text-decoration: none;font-weight: bold;" target="_blank">
						<b>Get it now</b> </a>
				</p>
			</div>
		</div>
	</li>