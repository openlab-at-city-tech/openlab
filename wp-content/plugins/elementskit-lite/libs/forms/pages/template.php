<?php
$pluginStatus = \ElementsKit_Lite\Libs\Framework\Classes\Plugin_Status::instance();
$plugin = $pluginStatus->get_status( 'metform/metform.php' );
?>

<div class="ekit-wrap notice">
	<div class="ekit-forms-wrapper ekit-forms-container">
		<!-- Hero -->
		<section class="ekit-forms-hero">
			<div class="ekit-badge">
				<span class="ekit-badge-text"><?php esc_html_e('NEW', 'elementskit-lite'); ?></span>
			</div>
			<div class="ekit-hero-copy">
				<h1 class="ekit-hero-title"><?php esc_html_e('Your Visual Form Builder For WordPress', 'elementskit-lite'); ?></h1>
				<p class="ekit-sub"><?php echo sprintf( esc_html__('Install & activate %s and visually build fully-functional forms with all the advanced features and highest flexibility.', 'elementskit-lite'), '<span>MetForm</span>' ); ?></p>

				<div class="ekit-hero-ctas">
					<a
						data-plugin_status="<?php echo esc_attr($plugin['status']); ?>"
						data-installation_url="<?php echo esc_url($plugin['installation_url']); ?>"
						data-activation_url="<?php echo esc_url($plugin['activation_url']); ?>"
						href="<?php echo $plugin['status'] == 'not_installed' ? esc_url($plugin['installation_url']) : esc_url($plugin['activation_url']); ?>"
						data-installing_text="<?php echo esc_attr__('Installing...', 'elementskit-lite'); ?>"
						data-activating_text="<?php echo esc_attr__('Activating...', 'elementskit-lite'); ?>"
						data-activated_text="<?php echo esc_attr__('Activated', 'elementskit-lite'); ?>"
						class="ekit-install-btn ekit-cta ekit-cta-primary ekit-form-btn">
							<?php echo esc_html($plugin['title']); ?>
					</a>
					<a href="https://wpmet.com/plugin/metform/" target="_blank" rel="noopener noreferrer" class="ekit-cta ekit-cta-outline">
						<?php esc_html_e('Learn More', 'elementskit-lite'); ?>
						<span class="ekit-external-link-icon" aria-hidden="true">
							<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
								<path d="M1 9L9 1" stroke="#13151D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M1 1H9V9" stroke="#13151D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</span>
					</a>
				</div>
			</div>

			<div class="ekit-hero-media">
				<!-- <iframe width="560" height="400" src="https://www.youtube.com/embed/8R4-Q14cu-w?si=NImN8i9XxDv1CCsN" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe> -->

				<iframe width="560" height="315" src="https://www.youtube.com/embed/zg1QIouKO_Q?si=7GlN214sDCBq6Wk3" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
			</div>

			<script>
			(function(){
				'use strict';

				try {
					// Install/Activate button behavior with error handling
					var installBtn = document.querySelector('.ekit-install-btn');

					if (installBtn) {
						installBtn.addEventListener('click', function(e){
							try {
								var original = installBtn.textContent.trim();
								var isInstalling = installBtn.classList.contains('installing');

								if (isInstalling) {
									e.preventDefault();
									return false;
								}

								installBtn.classList.add('installing');
								installBtn.textContent = '<?php echo esc_js( __( 'Installing...', 'elementskit-lite' ) ); ?>';
								installBtn.setAttribute('disabled', 'disabled');

								setTimeout(function(){
									if (installBtn) {
										installBtn.textContent = '<?php echo esc_js( __( 'Activating...', 'elementskit-lite' ) ); ?>';
									}
								}, 1200);

								setTimeout(function(){
									if (installBtn) {
										installBtn.textContent = '<?php echo esc_js( __( 'Activated', 'elementskit-lite' ) ); ?>';
										installBtn.classList.remove('installing');
										installBtn.classList.add('activated');
									}
								}, 2600);
							} catch (error) {
								console.error('MetForm template: Error handling install button:', error);
								if (installBtn) {
									installBtn.classList.remove('installing');
									installBtn.removeAttribute('disabled');
								}
							}
						});
					}

				} catch (error) {
					console.error('MetForm template: Initialization error:', error);
				}

			})();
			</script>
		</section>

		<section class="ekit-feature-sections">
			<article class="feature feature-contact">
				<div class="feature-content-box">
					<div class="feature-content">
						<h2><?php esc_html_e('Contact Form Builder', 'elementskit-lite'); ?></h2>
						<ul>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('40+ input styles for WordPress forms.', 'elementskit-lite'); ?>
							</li>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('GDPR compliant form-building.', 'elementskit-lite'); ?>
							</li>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('Confirmation email on form submission', 'elementskit-lite'); ?>
							</li>
						</ul>
					</div>
					<div class="feature-media">
						<div class="media-mock media-contact">
							<img src="<?php echo esc_url( plugins_url( '../assets/images/contact-form.png', __FILE__ ) ); ?>" alt="<?php esc_attr_e( 'Contact Form', 'elementskit-lite' ); ?>" />
						</div>
					</div>
				</div>
			</article>

			<article class="feature feature-conditional">
				<div class="feature-content-box">
					<div class="feature-content">
						<h2><?php esc_html_e('Conditional Logic Form', 'elementskit-lite'); ?></h2>
						<ul>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('Build smart forms with conditional logic', 'elementskit-lite'); ?>
							</li>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('Form fields appear dynamically for users.', 'elementskit-lite'); ?>
							</li>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('Conditional fields based on the user input.', 'elementskit-lite'); ?>
							</li>
						</ul>
					</div>
					<div class="feature-media">
						<div class="media-mock media-conditional">
							<img src="<?php echo esc_url( plugins_url( '../assets/images/conditional-form.png', __FILE__ ) ); ?>" alt="<?php esc_attr_e( 'Conditional Logic', 'elementskit-lite' ); ?>" />
						</div>
					</div>
				</div>
			</article>

			<article class="feature feature-multistep">
				<div class="feature-content-box">
					<div class="feature-content">
						<h2>
							<?php esc_html_e('Multi-step', 'elementskit-lite'); ?>
							<br>
							<?php esc_html_e('WordPress form', 'elementskit-lite'); ?>
						</h2>
						<ul>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('Convert long forms into multiple steps.', 'elementskit-lite'); ?>
							</li>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('More user-friendly and engaging forms.', 'elementskit-lite'); ?>
							</li>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('Nav bar/ progress bar for better attention.', 'elementskit-lite'); ?>
							</li>
						</ul>
					</div>
					<div class="feature-media">
						<div class="media-mock media-multistep">
							<img src="<?php echo esc_url( plugins_url( '../assets/images/multistep-form.png', __FILE__ ) ); ?>" alt="<?php esc_attr_e( 'Multi-Step Forms', 'elementskit-lite' ); ?>" />
						</div>
					</div>
				</div>
			</article>

			<article class="feature feature-advanced-integrations">
				<div class="feature-content-box">
					<div class="feature-content">
						<h2>
							<?php esc_html_e('18+ Advanced', 'elementskit-lite'); ?>
							<br>
							<?php esc_html_e('Integrations', 'elementskit-lite'); ?>
						</h2>
						<ul>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('reCAPTCHA spam protection for forms.', 'elementskit-lite'); ?>
							</li>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('Google Maps and Sheets integration.', 'elementskit-lite'); ?>
							</li>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('Support PayPal and Stripe payments.', 'elementskit-lite'); ?>
							</li>
						</ul>
					</div>
					<div class="feature-media">
						<div class="media-mock media-advanced-integrations">
							<img src="<?php echo esc_url( plugins_url( '../assets/images/advanced-integrations.png', __FILE__ ) ); ?>" alt="<?php esc_attr_e( 'Advanced Integrations', 'elementskit-lite' ); ?>" />
						</div>
					</div>
				</div>
			</article>

			<article class="feature feature-crm-integrations">
				<div class="feature-content-box">
					<div class="feature-content">
						<h2>
							<?php esc_html_e('CRM and Newsletter', 'elementskit-lite'); ?>
							<br>
							<?php esc_html_e('Integrations', 'elementskit-lite'); ?>
						</h2>
						<ul>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('Integrates with 7+ CRMs and Newsletters.', 'elementskit-lite'); ?>
							</li>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('HubSpot, Zoho, MailChimp,  integration', 'elementskit-lite'); ?>
							</li>
							<li>
								<span class="check-icon" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><rect width="20" height="20" fill="#3B67FE" rx="10"/>
										<path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.333 7.708 8.75 12.292l-2.083-2.084"/>
									</svg>
								</span>
								<?php esc_html_e('MailChimp, AWeber, ConvertKit, and more.', 'elementskit-lite'); ?>
							</li>
						</ul>
					</div>
					<div class="feature-media">
						<div class="media-mock media-crm-integrations">
							<img src="<?php echo esc_url( plugins_url( '../assets/images/crm-integrations.png', __FILE__ ) ); ?>" alt="<?php esc_attr_e( 'CRM Integrations', 'elementskit-lite' ); ?>" />
						</div>
					</div>
				</div>
			</article>
		</section>

		<!-- Info table: Free vs Pro -->
		<section class="ekit-info-table" aria-labelledby="metform-compare">
			<div class="ekit-info-title-wrap">
				<h3 id="metform-compare" class="ekit-info-title"><?php esc_html_e('MetForm Free vs Pro', 'elementskit-lite'); ?></h3>
				<p class="ekit-info-desc"><?php esc_html_e('Want full freedom while designing WordPress forms? Switch to PRO! Here is what you will be getting with MetForm Pro.', 'elementskit-lite'); ?></p>
			</div>

			<div class="info-table-wrap">
				<table class="info-table" role="table" aria-describedby="metform-compare">
					<thead>
						<tr>
							<th><?php esc_html_e('Features', 'elementskit-lite'); ?></th>
							<th><?php esc_html_e('Free', 'elementskit-lite'); ?></th>
							<th><?php esc_html_e('Pro', 'elementskit-lite'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							// Import icons from assets/images
							$check_icon_url = plugins_url( '../assets/images/check-icon.svg', __FILE__ );
							$cross_icon_url = plugins_url( '../assets/images/cross-icon.svg', __FILE__ );

							$check_icon = '<img src="' . esc_url($check_icon_url) . '" alt="' . esc_attr__('Available', 'elementskit-lite') . '" width="22" height="22" />';
							$cross_icon = '<img src="' . esc_url($cross_icon_url) . '" alt="' . esc_attr__('Not Available', 'elementskit-lite') . '" width="22" height="22" />';

							$info_rows = array(
								array('Form Templates','4+','40+'),
								array('Contact Form', $check_icon, $check_icon),
								array('Booking Form', $check_icon, $check_icon),
								array('Multi-Step Form', $cross_icon, $check_icon),
								array('Quiz Form', $cross_icon, $check_icon),
								array('Conditional Logic', $cross_icon, $check_icon),
								array('Calculations', $cross_icon, $check_icon),
								array('Field Validation', $cross_icon, $check_icon),
								array('Login / Register', $cross_icon, $check_icon),
								array('WooCommerce Checkout', $cross_icon, $check_icon),
								array('reCAPTCHA', $check_icon, $check_icon),
								array('GDPR Consent', $check_icon, $check_icon),
								array('Entry Restrictions', $check_icon, $check_icon),
								array('Google Map', $cross_icon, $check_icon),
								array('Google Sheet', $cross_icon, $check_icon),
								array('PayPal Payment', $cross_icon, $check_icon),
								array('Stripe Payment', $cross_icon, $check_icon),
								array('MailChimp', $check_icon, $check_icon),
								array('Hubspot', $check_icon, $check_icon),
								array('Zoho', $cross_icon, $check_icon),
								array('Aweber', $cross_icon, $check_icon),
								array('ConvertKit', $cross_icon, $check_icon),
							);

							foreach($info_rows as $r){
								$col1 = esc_html($r[0]);
								$col2 = (strpos($r[1], '<img') !== false) ? $r[1] : esc_html($r[1]);
								$col3 = (strpos($r[2], '<img') !== false) ? $r[2] : esc_html($r[2]);
								echo '<tr><td>'.$col1.'</td><td class="center">'.$col2.'</td><td class="center">'.$col3.'</td></tr>';
							}
						?>
					</tbody>
				</table>
			</div>
		</section>

		<!-- Newsletter / final CTA -->
		<section class="ekit-newslater">
			<div class="newslater-inner">
				<div class="newslater-copy">
					<h3 class="newslater-title">
						<?php esc_html_e('Switch to MetForm Pro and start creating', 'elementskit-lite'); ?>
						<br>
						<?php esc_html_e('your WordPress forms with', 'elementskit-lite'); ?>
						<span class="limitless-link"><?php esc_html_e('Limitless Options', 'elementskit-lite'); ?></span>.
					</h3>
				</div>
				<div class="newslater-cta">
					<a
						href="https://wpmet.com/plugin/metform/pricing/"
						target="_blank"
						class="ekit-premium-btn"
					>
						<?php echo esc_html__('Go Premium', 'elementskit-lite'); ?>
					</a>
				</div>
			</div>
		</section>
	</div>

	<div class="ekit-footer">
		<p>
			<?php echo sprintf( esc_html__('Thank you for creating with %s', 'elementskit-lite'), '<a href="https://wpmet.com/docs/category/elementskit/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'ElementsKit', 'elementskit-lite' ) . '</a>' ); ?>
		</p>
		<p class="ekit-version">
			<?php echo sprintf( esc_html__('Version 6.8.2', 'elementskit-lite') ); ?>
		</p>
	</div>



</div>
