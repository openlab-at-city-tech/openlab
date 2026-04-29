<?php if (!defined('ABSPATH')) die('No direct access.');

// Free quickstart demos
if ( ! class_exists( 'MetaSliderQuickstart' ) ) {
	wp_die(
		esc_html__( 
			'MetaSliderQuickstart class does not exists.', 
			'ml-slider' 
		)
	);
}

$msQuickstart = new MetaSliderQuickstart();
$demo_slideshows = $msQuickstart->quickstart_options();

$demo_slideshows = apply_filters( 'metaslider_quickstart_options_data', $demo_slideshows );
?>
<div id="metaslider-ui" class="metaslider">
<div class="metaslider-start mt-16" id="quickstart-screen">
	<div class="metaslider-welcome">

	<?php 
	if ( isset( $_GET['create-slideshow'] ) && ! empty( $_GET['slug'] ) ) : 
		$slug = sanitize_text_field( $_GET['slug'] );
		?>
		<div>
			<div class="quickstart_importing_loading">
				<span style="background-image: url(<?php echo esc_url( admin_url( '/images/loading.gif' ) ); ?>);">
					<?php esc_html_e( 'Creating slideshow... Please wait!', 'ml-slider' ); ?>
				</span>
				<div class="quickstart_progress_wrapper">
					<div class="quickstart_progress_bar"></div>
				</div>
			</div>
			<script type="text/javascript">
			jQuery(document).ready(function($){
				var progress = 0;
				var progressInterval;

				function ms_qs_start_progress() {
					progress = 0;
					progressInterval = setInterval(function () {
						if (progress < 90) {
							progress += Math.random() * 8;
							$('.quickstart_progress_bar').css('width', progress + '%');
						}
					}, 400);
				}

				function ms_qs_finish_progress() {
					clearInterval(progressInterval);
					$('.quickstart_progress_bar').css('width', '100%');
				}

				ms_qs_start_progress();
				var data = {
					action: 'quickstart_slideshow_v2',
					slug: '<?php echo esc_html( $slug ) ?>',
					_wpnonce: metaslider.quickstart_slideshow_nonce
				};

				$.ajax({
					url: metaslider.ajaxurl,
					data: data,
					type: 'GET',
					success: function (response) {
						ms_qs_finish_progress();
						console.log(response);
						if (response && response.slideshow_id) {
							$('.quickstart_importing_loading span').text('<?php 
								esc_html_e( 'Redirecting...', 'ml-slider' ) ?>');
							setTimeout(() => {
								window.location.href = '<?php 
									echo esc_url_raw(admin_url("admin.php?page=metaslider&id=")); 
							?>' + response.slideshow_id;
							}, 1000);
						} else {
							console.error('slideshow_id is missing!');
						}
					},
					error: function (xhr, status, error) {
						clearInterval(progressInterval);
						
						console.log('Error:', status);
						console.log('Message:', error);

						const response = JSON.parse(xhr.responseText);
						const message = response.data.message;
						$('.quickstart_importing_loading').text('Error: ' + message);
						$('.quickstart_progress_bar').css('width', '0%');
					}
				});

			});  
			</script>
		</div>
	<?php else: ?>
		<div>
			<div class="pb-5 pl-5 pr-5 pt-0">
				<?php 
				// @since 3.106
				do_action( 'metaslider_quickstart_ads' ); 
				?>
				<div class="wrap">
					<h1 class="wp-heading-inline pt-0">
						<?php esc_html_e( 'Quick Start', 'ml-slider' ) ?>
					</h1>
				</div>
				<div id="ms-qs-sections" class="mb-1">
					<a href="#" data-section="demo_group" class="mr-2 mb-2 inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md transition ease-in-out duration-150 md:w-auto md:text-lg bg-orange hover:bg-orange-darker active:bg-orange-darkest text-white">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 -ml-1 pr-1">
							<path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
						</svg> <?php esc_html_e( 'Start with a demo', 'ml-slider' ) ?>
					</a><a href="#" data-section="images_group" class="mr-2 mb-2 inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md transition ease-in-out duration-150 md:w-auto md:text-lg bg-orange hover:bg-orange-darker active:bg-orange-darkest text-white">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 -ml-1 pr-1">
							<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
						</svg> <?php esc_html_e( 'Use your own media', 'ml-slider' ) ?>
					</a><a href="<?php 
						echo esc_url( wp_nonce_url( 
							admin_url( 'admin-post.php?action=metaslider_create_slider' ), 
							'metaslider_create_slider' 
						) ) 
						?>" data-section="false" class="mr-2 mb-2 inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md transition ease-in-out duration-150 md:w-auto md:text-lg bg-orange hover:bg-orange-darker active:bg-orange-darkest text-white">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 -ml-1 pr-1">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
						</svg> <?php esc_html_e( 'Blank slideshow', 'ml-slider' ) ?>
					</a>
				</div>
				
				<div class="section-content-wrapper tabs-content-wrapper bg-white tabs-content-adjust">

					<!-- Demo -->
					<div id="demo_group" style="">
						<h3 class="ms-heading"><?php esc_html_e('Import a demo slideshow', 'ml-slider'); ?></h3>
						<p>
							<?php 
								echo apply_filters( 'metaslider_quickstart_description', esc_html__('Use sample slides to quickly create a new slideshow.', 'ml-slider'));  //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</p>
						<div class="tablenav top" id="ms-qs-filters">
							<form>
								<div class="alignleft actions mb-3">
									<select name="action" id="ms-qs-slide-type">
										<option value=""><?php esc_html_e( 'Slide Type', 'ml-slider' ) ?></option>
										<option value="image"><?php esc_html_e( 'Image', 'ml-slider' ) ?></option>
										<option value="vimeo"><?php esc_html_e( 'Vimeo', 'ml-slider' ) ?></option>
										<option value="youtube"><?php esc_html_e( 'YouTube', 'ml-slider' ) ?></option>
										<option value="tiktok"><?php esc_html_e( 'TikTok', 'ml-slider' ) ?></option>
										<option value="external"><?php esc_html_e( 'External Image', 'ml-slider' ) ?></option>
										<option value="external_video"><?php esc_html_e( 'External Video', 'ml-slider' ) ?></option>
										<option value="custom_html"><?php esc_html_e( 'Custom HTML', 'ml-slider' ) ?></option>
										<option value="woocommerce"><?php esc_html_e( 'WooCommerce', 'ml-slider' ) ?></option>
										<option value="post_feed"><?php esc_html_e( 'Post Feed', 'ml-slider' ) ?></option>
										<option value="html_overlay"><?php esc_html_e( 'Layer Slide', 'ml-slider' ) ?></option>
										<option value="local_video"><?php esc_html_e( 'Local Video', 'ml-slider' ) ?></option>
									</select>
								</div>
								<div class="alignleft actions mb-3">
									<select name="action" id="ms-qs-slide-features">
										<option value=""><?php esc_html_e( 'Features', 'ml-slider' ) ?></option>
										<option value="boxed"><?php esc_html_e( 'Boxed', 'ml-slider' ) ?></option>
										<option value="full-width"><?php esc_html_e( 'Full-width', 'ml-slider' ) ?></option>
										<option value="carousel"><?php esc_html_e( 'Carousel', 'ml-slider' ) ?></option>
										<option value="thumbnail-nav"><?php esc_html_e( 'Thumbnail Navigation', 'ml-slider' ) ?></option>
										<option value="video-caption"><?php esc_html_e( 'Video Caption', 'ml-slider' ) ?></option>
									</select>
								</div>
								<div class="alignleft actions mb-3">
									<select name="action" id="ms-qs-slide-animation">
										<option value=""><?php esc_html_e( 'Animation Type', 'ml-slider' ) ?></option>
										<option value="slide"><?php esc_html_e( 'Slide', 'ml-slider' ) ?></option>
										<option value="fade"><?php esc_html_e( 'Fade', 'ml-slider' ) ?></option>
										<option value="zooming"><?php esc_html_e( 'Zooming', 'ml-slider' ) ?></option>
										<option value="flip"><?php esc_html_e( 'Flip', 'ml-slider' ) ?></option>
										<option value="ken-burns"><?php esc_html_e( 'Ken Burns', 'ml-slider' ) ?></option>
										<option value="vertical"><?php esc_html_e( 'Vertical', 'ml-slider' ) ?></option>
									</select>
								</div>
								<div class="alignleft actions mb-3">
									<select name="action" id="ms-qs-slide-integration">
										<option value=""><?php esc_html_e( 'Integration', 'ml-slider' ) ?></option>
										<option value="woocommerce"><?php esc_html_e( 'WooCommerce', 'ml-slider' ) ?></option>
										<option value="tec"><?php esc_html_e( 'The Events Calendar', 'ml-slider' ) ?></option>
										<option value="posts"><?php esc_html_e( 'WordPress Posts', 'ml-slider' ) ?></option>
									</select>
								</div>
								<div class="alignleft actions mb-3">
									<select name="action" id="ms-qs-slide-price">
										<option value=""><?php esc_html_e( 'Free and Pro', 'ml-slider' ) ?></option>
										<option value="free"><?php esc_html_e( 'Free only', 'ml-slider' ) ?></option>
										<option value="pro"><?php esc_html_e( 'Pro only', 'ml-slider' ) ?></option>
									</select>
								</div>
								<div class="alignleft actions mb-3">
									<input type="button" id="ms-qs-slide-reset" class="button" value="<?php echo esc_attr( 'Reset Filters', 'ml-slider' ) ?>" />
								</div>
							</form>
						</div>

						<div style="display:none;" id="ms-quickstart-no-results" class="self-stretch text-center bg-gray-light mt-5 p-15 text-lg">
							<?php esc_html_e( 'No demos match your filters.', 'ml-slider' ) ?>
						</div>
						<ul class="ms-quickstart-selector flex flex-wrap m-0 w-full">
							<?php foreach ( $demo_slideshows as $item ) : 
								// Is this a Pro option placeholder? 
								// false = not a Pro option due Pro is installed and active
								$is_dummy = isset( $item['is_dummy'] ) ? $item['is_dummy'] : false;

								// Skip pro ads if Pro is active
								if ( $is_dummy && metaslider_pro_is_active() ) {
									continue;
								}

								$type_ 		 = ' data-type="' . htmlspecialchars( json_encode( $item['type'] ), ENT_QUOTES, 'UTF-8' ) . '"';
								$features    = ' data-features="' . htmlspecialchars( json_encode( $item['features'] ), ENT_QUOTES, 'UTF-8' ) . '"';
								$animation   = ' data-animation="' . htmlspecialchars( json_encode( $item['animation'] ), ENT_QUOTES, 'UTF-8' ) . '"';
								$integration = ' data-integration="' . htmlspecialchars( json_encode( $item['integration'] ), ENT_QUOTES, 'UTF-8' ) . '"';
								$price 		 = ' data-price="' . htmlspecialchars( $item['price'], ENT_QUOTES, 'UTF-8' ) . '"';
								$demo 		 = $item['demo'] ?? false;
								$image 		 = file_exists( METASLIDER_PATH . 'admin/images/quickstart/' . $item['slug'] . '.png' ) 
									? METASLIDER_ADMIN_URL . 'images/quickstart/' . $item['slug'] . '.png' 
									: METASLIDER_ADMIN_URL . 'images/quickstart/' . $item['slug'] . '.jpg';
								if ( $is_dummy ) {
									// Upgrade to pro
									$url = 'javascript:void(0);';
								} else {
									// Is a new demo
									$url = esc_url( admin_url( 
										'admin.php?page=metaslider-start&create-slideshow&slug=' . $item['slug']
									) );
								}
								?>
								<li class="cursor-pointer m-0 lg:w-3/12 md:w-4/12 sm:w-6/12 relative slug-<?php 
									echo esc_attr( $item['slug'] ) ?>"<?php 
									echo $type_ . $features . $animation . $integration . $price //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<span class="quickstart-labels">
										<?php 
										if ( isset( $item['type'] )
											&& is_array( $item['type'] )
											&& ! in_array( 'woocommerce', $item['type'] )
										) { 
											foreach ( $item['type'] as $slide_type ) {
											?>
												<span class="quickstart-type-label">
													<?php esc_html_e( $msQuickstart->slide_type_label( $slide_type ) ) ?>
												</span>
											<?php 
											}
										} 
										?>
										<?php if ( is_array( $item['integration'] ) && in_array( 'woocommerce', $item['integration'] ) ) { ?>
											<span class="quickstart-wc-label">WooCommerce</span>
										<?php } ?>
										<?php if ( is_array( $item['integration'] ) && in_array( 'tec', $item['integration'] ) ) { ?>
											<span class="quickstart-tec-label">The Events Calendar</span>
										<?php } ?>
										<?php if ( is_array( $item['integration'] ) && in_array( 'posts', $item['integration'] ) ) { ?>
											<span class="quickstart-posts-label"><?php 
												esc_html_e( 'WordPress Posts',  'ml-slider' ) ?></span>
										<?php } ?>
										<?php if ( $item['price'] == 'pro' ) { ?>
											<span class="quickstart-pro-label">Pro</span>
										<?php } ?>
									</span>

									<div class="quickstart-title-wrapper absolute grid items-center text-center p-2 block" style="width:calc(100% - 16px); min-height:40px; top: 8px; left: 8px; background:hsla(0,0%,100%,0.9); pointer-events: none">
										<div class="text-base text-black">
											<?php esc_html_e( $item['label'] ) ?>
										</div>
									</div>

									<div class="quickstart-confirm-wrapper absolute grid items-center text-center p-5 hidden" style="width:calc(100% - 16px); height:calc(100% - 16px); top: 8px; left: 8px; background:<?php
									echo $is_dummy !== false ? '#2271b1' : 'hsla(0,0%,100%,0.9)' ?>">
										<div class="quickstart-confirm-question">
											<?php if ( $is_dummy === false ) { ?>
												<div class="text-base mb-4 text-black">
													<?php
													echo sprintf(
														esc_html__( 'Create %s slideshow?', 'ml-slider' ),
														esc_html( $item['label'] )
													);
													?>
												</div>
												<button  data-btn-type="no" class="button button-secondary mr-2 shadow mb-2">
													<?php esc_html_e('Cancel', 'ml-slider') ?>
												</button>
												<button data-btn-type="yes" class="button button-primary shadow mb-2">
													<?php esc_html_e('Yes, please!', 'ml-slider') ?>
												</button>

												<?php if ( $demo ) { ?>
													<button data-btn-type="demo" data-href="<?php 
														echo esc_url( $demo) ?>" target="_blank" class="button button-primary border-transparent bg-orange hover:bg-orange-darker active:bg-orange-darkest shadow ml-2 mb-2">
														<?php esc_html_e('Demo', 'ml-slider') ?>
													</button>
												<?php } ?>

											<?php } else { ?>
												<h3 class="text-white mb-3 font-bold text-xl">
													<?php esc_html_e( 'Get MetaSlider Pro!', 'ml-slider') ?>
												</h3>
												<p class="text-white font-normal text-sm mb-3">
													<?php esc_html_e( 'Upgrade now to unlock this demo slideshow!', 'ml-slider') ?>
												</p>
												<a class="w-full inline-flex items-center justify-center px-5 py-2 border border-transparent rounded-md text-white bg-orange hover:bg-orange-darker active:bg-orange-darkest transition ease-in-out duration-150 md:w-auto text-base" href="https://www.metaslider.com/upgrade?utm_source=lite&utm_medium=banner&utm_campaign=pro" target="_blank">
													<?php esc_html_e( 'Upgrade now', 'ml-slider') ?>
													<span class="dashicons dashicons-external border-0"></span>
												</a>
											<?php } ?>
										</div>
									</div>
									<a href="<?php echo esc_url( $url ) ?>" class="block h-full">
										<img src="<?php 
											echo esc_url( $image ) ?>" alt="<?php
											echo esc_attr( $item['label'] ) ?>" class="object-cover h-full w-full" style="min-height:200px;">
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<!-- Images -->
					<div id="images_group" style="display:none;">
						<h3 class="ms-heading"><?php esc_html_e('Create a slideshow with your images', 'ml-slider'); ?></h3>
						<p><?php esc_html_e('Choose your own images to start a new slideshow.', 'ml-slider'); ?></p>
						<div>
							<div id="plupload-upload-ui" class="hide-if-no-js">
								<p id="loading-add-sample-slides-notice" style="display: none;">
									<span style="background-image: url(<?php echo esc_url(admin_url( '/images/loading.gif' )); ?>);">
										<span>
											<?php _e( 'Uploading images...', 'ml-slider' ); ?>
										</span>
									</span>
								</p>
								<div id="drag-drop-area" style="min-height:270px">
									<div class="drag-drop-inside" style="margin: 80px auto 0;width: 100%; text-align: center;">
										<div class="drag-drop-info">
											<p class="font-semibold text-base leading-7"><?php _e('Drop files to upload'); ?></p>
											<p class="font-semibold text-base leading-7"><?php _ex('or', 'Uploader: Drop files to upload - or - Select Files', 'ml-slider'); ?></p>
											<p class="drag-drop-buttons mt-5">
												<input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files'); ?>" class="button text-sm" />
												<button id="quickstart-browse-button" class="button text-sm"><?php esc_html_e('Open Media Library', 'ml-slider'); ?></button>
											</p>
										</div>
									</div>
								</div>
							</div>
							<div class="media-upload-form">
								<div id="media-items" class="hide-if-no-js"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	</div>
</div>
<?php // TODO: I think after here maybe we can add images from their media library, or perhaps from an external image API
