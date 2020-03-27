<?php
/*
	HDCommerce options and settings page
	Creates options page for global settings
*/

/* Create the option page
------------------------------------------------------- */


	if( current_user_can('edit_others_pages') ) { ?>
	<div id = "hdq_meta_forms">
		<div id = "hdq_wrapper">
			<div id="hdq_form_wrapper">
				<h1>HD Quiz Addons</h1>
				<div class = "hdq_tab hdq_tab_active">
					
				
				<p>
					If you are a developer and would like to include your addon plugin to this page, please message me <a href = "https://harmonicdesign.ca/submit-a-plugin?utm_source=HDQuiz&utm_medium=addonsPage">here</a>, or <a href = "https://harmonicdesign.ca/hd-quiz-addons?utm_source=HDQuiz&utm_medium=addonsPage">click here</a> to learn how to create your own addons.
				</p>
				<p>
					Plugins marked as "verified" are plugins that have either been developed by <a href = "https://harmonicdesign.ca?utm_source=HDQuiz&utm_medium=addonsPage">Harmonic Design</a>, or have been audited and approved by Harmonic Design.
				</p>
					
<div class="hdq_highlight">
						<p>
							<strong>NEW:</strong> Patreon contributors of $5+/m will recieve access to ALL first-party (developed by Harmonic Design) addons for FREE. Please visit the <a href="https://www.patreon.com/harmonic_design" target="_blank">patreon page</a> to help continued development. Every little bit helps, and I am fuelled by coffee ☕.
						NOTE: There are currently no paid addons available.</p>
					</div>					
					
					
				<div id = "hdq_addons">
					<?php

						// TODO! convert to ajax for faster initial page load
						$data = wp_remote_get("https://harmonicdesign.ca/addons/json/");
		
						if ( is_array( $data ) ) {
		
							$data = $data["body"];
							$data = stripslashes(html_entity_decode($data));
							$data = json_decode($data);

							
							if(!empty($data)){
								foreach($data as $value){
									$title = sanitize_text_field($value->title);
									$thumb = sanitize_text_field($value->thumb);
									$description = wp_kses_post($value->description);
									$url = sanitize_text_field($value->url);
									$author = sanitize_text_field($value->author);
									$price = sanitize_text_field($value->price);
									$verified = sanitize_text_field($value->verified);
									if ($price == 0){
										$price = "FREE";
									}
								?>
									<div class="hdq_addon_item">
										<div class="hdq_addon_item_image">
											<img src="<?php echo $thumb; ?>" alt="<?php echo $title; ?>">
										</div>
										<div class="hdq_addon_content">
											<h2>
												<?php 
													echo $title; if ($verified == "verified") {
														echo '<span class = "hdq_verified hdq_tooltip hdq_tooltip_question">verified<span class="hdq_tooltip_content"><span>This plugin has either been developed by the author of HD Quiz or has been audited by the developer.<br/><small>please note that Harmonic Design cannot guarantee that third party plugins for HD Quiz are risk free and secure after verification.</small></span></span></span>';

													} 
												?> <span class = "hdq_price"><?php echo $price; ?></span></h2>
											<h4 class = "hdq_addon_author">
												developed by: <?php echo $author; ?>
											</h4>

											<?php echo apply_filters( 'the_content', $description ); ?>
											<p style = "text-align:right">												
												<a href = "<?php echo $url; ?>?utm_source=HDQuiz&utm_medium=addonsPage" class = "hdq_button3 hdq_reverse">View Addon Page</a>
											</p>
										</div>
									</div>
								<?php } 
							} else {
									?>
					
										<p>Addons will appear here once available. There are currently two addons that are complete and have been submitted to the WordPress plugin team for review. They will appear here once verified. Please check back soon as both of these addons will be FREE!</p>
					
									<?php
								}
						} else {
							echo '<h2>There was an error loading the available addons. Please refresh this page to try again.</h2>';
						}
					?>


				</div>
			</div>
</div></div></div>

	<?php } ?>