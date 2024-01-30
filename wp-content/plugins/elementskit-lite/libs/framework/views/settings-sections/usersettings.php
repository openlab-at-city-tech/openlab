<?php

$user_data  = $this->utils->get_option( 'user_data', array() );
$pro_active = ( in_array( 'elementskit/elementskit.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) );

?>

<div class="ekit-admin-fields-container">
	<div class="ekit-admin-fields-container-fieldset-- xx">
		<div class="panel-group attr-accordion" id="accordion" role="tablist" aria-multiselectable="true">
			<!-------------------
				Mail Champ
			-------------------->
			<div class="attr-panel ekit_accordion_card">
				<div class="attr-panel-heading label-mail-chimp" role="tab" id="mail_chimp_data_headeing">
					<a class="attr-btn attr-collapsed" role="button" data-attr-toggle="collapse"
					   data-parent="#accordion"
					   href="#mail_chimp_data_control" aria-expanded="true"
					   aria-controls="mail_chimp_data_control">
						<span><?php esc_html_e( 'MailChimp Data', 'elementskit-lite' ); ?></span>
					</a>
				</div>


				<div id="mail_chimp_data_control" class="attr-panel-collapse attr-collapse attr-in" role="tabpanel"
					 aria-labelledby="mail_chimp_data_headeing">
					<div class="attr-panel-body">
						<div class="ekit-admin-user-data-separator"></div>
						<?php
						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[mail_chimp][token]',
								'label'       => esc_html__( 'Token', 'elementskit-lite' ),
								'placeholder' => '24550c8cb06076751a80274a52878-us20',
								'value'       => ( ! isset( $user_data['mail_chimp']['token'] ) ) ? '' : ( $user_data['mail_chimp']['token'] ),
							)
						);
						?>

					</div>
				</div>


			</div>

			<!-------------------
			Facebook Page Feed
			-------------------->
			<div class="attr-panel ekit_accordion_card">
				<div
						class="<?php echo esc_attr($this->utils->is_widget_active_class( 'facebook-feed', $pro_active )); ?>"
						data-attr-toggle="modal"
						data-target="#elementskit_go_pro_modal"
						role="tab" id="facebook_data_headeing">


					<a class="attr-btn" role="button" data-attr-toggle="collapse" data-parent="#accordion"

					   href="#fbp_feed_control_data"
					   aria-expanded="false" aria-controls="fbp_feed_control_data">
						<span><?php esc_html_e( 'Facebook Page Feed', 'elementskit-lite' ); ?></span>
					</a>
				</div>


				<div id="fbp_feed_control_data" class="attr-panel-collapse attr-collapse" role="tabpanel"
					 aria-labelledby="facebook_data_headeing">
					<div class="attr-panel-body">
						<div class="ekit-admin-user-data-separator"></div>

						<?php
						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[fb_feed][page_id]',
								'label'       => esc_html__( 'Facebook Page ID', 'elementskit-lite' ),
								'placeholder' => __( 'Facebook app id', 'elementskit-lite' ),
								'value'       => ( ! isset( $user_data['fb_feed']['page_id'] ) ) ? '' : ( $user_data['fb_feed']['page_id'] ),
							)
						);
						?>

						<?php

						$val = empty( $user_data['fb_feed']['pg_token'] ) ? '' : $user_data['fb_feed']['pg_token'];

						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[fb_feed][pg_token]',
								'label'       => esc_html__( 'Page Access Token', 'elementskit-lite' ),
								'placeholder' => 'S8LGISx9wBAOx5oUnxpOceDbyD01DYNNUwoz8jTHpm2ZB9RmK6jKwm',
								'value'       => ( ! isset( $user_data['fb_feed']['pg_token'] ) ) ? '' : ( $user_data['fb_feed']['pg_token'] ),
							)
						);

						$dbg    = '&app=105697909488869&sec=f64837dd6a129c23ab47bdfdc61cfe19'; //ElementsKit Plugin Review
						$dbg    = '&app=2577123062406162&sec=a4656a1cae5e33ff0c18ee38efaa47ac'; //ElementsKit Plugin page feed
						$scopes = '&scope=pages_show_list,pages_read_engagement,pages_manage_engagement,pages_read_user_content'; 
						?>

						<div class="ekit-admin-accordion-btn-group">
							<?php if ( did_action( 'elementskit/loaded' ) ) : ?>
								<a class="ekit-admin-access-token cache_clean_social_provider ekit-admin-accordion-btn"
								   data-provider="fb_page_feed" data-url_part="fb-feed">
									<?php echo esc_html__( 'Clear Cache', 'elementskit-lite' ); ?>
								</a>
							<?php endif; ?>

							<a class="ekit-admin-access-token ekit-admin-accordion-btn"
							   href="<?php echo esc_url('https://token.wpmet.com/social_token.php?provider=facebook&_for=page' . $dbg . $scopes); ?>"
							   target="_blank"> <?php echo esc_html__( 'Get access token', 'elementskit-lite' ); ?>
							</a>
						</div>
					</div>
				</div>

			</div>

			<!-------------------
			Facebook page review
			-------------------->

			<div class="attr-panel ekit_accordion_card">
				<div
						class="<?php echo esc_attr($this->utils->is_widget_active_class( 'facebook-review', $pro_active )); ?>"
						data-attr-toggle="modal"
						data-target="#elementskit_go_pro_modal"
						role="tab" id="facebook_data_headeing">

					<a class="attr-btn" role="button" data-attr-toggle="collapse" data-parent="#accordion"
					   href="#fbp_review_control_data"
					   aria-expanded="false" aria-controls="fbp_review_control_data">
						<span><?php esc_html_e( 'Facebook page review', 'elementskit-lite' ); ?></span>
					</a>

				</div>

				<div id="fbp_review_control_data" class="attr-panel-collapse attr-collapse" role="tabpanel"
					 aria-labelledby="facebook_data_headeing">
					<div class="attr-panel-body">
						<div class="ekit-admin-user-data-separator"></div>
						<?php
						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[fbp_review][pg_id]',
								'label'       => esc_html__( 'Page ID', 'elementskit-lite' ),
								'placeholder' => '109208590868891',
								'value'       => ( ! isset( $user_data['fbp_review']['pg_id'] ) ) ? '' : ( $user_data['fbp_review']['pg_id'] ),
							)
						);


						$val = ( empty( $user_data['fbp_review']['pg_token'] ) ) ? '' : ( $user_data['fbp_review']['pg_token'] );
						$btn = ( empty( $user_data['fbp_review']['pg_token'] ) ) ? esc_html__( 'Get access token', 'elementskit-lite' ) : esc_html__( 'Refresh access token', 'elementskit-lite' ); // escaped at line: 186

						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[fbp_review][pg_token]',
								'label'       => esc_html__( 'Page Token', 'elementskit-lite' ),
								'placeholder' => 'S8LGISx9wBAOx5oUnxpOceDbyD01DYNNUwoz8jTHpm2ZB9RmK6jKwm',
								'value'       => $val,
							)
						);

						/**
						 * App name : ElementsKit Plugin page feed
						 * App id : 2577123062406162
						 *
						 * Just empty the string when done debugging :D
						 *
						 */
						$dbg = '&app=944119176079880&sec=03b20cdd9cf6f1d4d6e03522dc9caa2a';
						$dbg = '';
						?>

						<div class="ekit-admin-accordion-btn-group">
							<?php if ( did_action( 'elementskit/loaded' ) ) : ?>
								<a class="ekit-admin-access-token cache_clean_social_provider ekit-admin-accordion-btn"
								   data-provider="fb_page_reviews" data-url_part="fb-pg-review">
									<?php echo esc_html__( 'Clear Cache', 'elementskit-lite' ); ?>
								</a>
							<?php endif; ?>

							<a class="ekit-admin-access-token ekit-admin-accordion-btn"
							   href="<?php echo esc_url('https://token.wpmet.com/social_token.php?provider=facebook&_for=page' . $dbg); ?>"
							   target="_blank">
								<?php echo esc_html( $btn ); ?>
							</a>
						</div>
					</div>
				</div>

			</div>

			<!-------------------
				yelp
			-------------------->
			<div class="attr-panel ekit_accordion_card">
				<div
						class="<?php echo esc_attr($this->utils->is_widget_active_class( 'yelp', $pro_active )); ?>"
						data-attr-toggle="modal"
						data-target="#elementskit_go_pro_modal"
						role="tab" id="yelp_data_headeing">

					<a class="attr-btn attr-collapsed" role="button" data-attr-toggle="collapse"
					   data-parent="#accordion"
					   href="#yelp_data_control" aria-expanded="false" aria-controls="yelp_data_control">
						<?php esc_html_e( 'Yelp Settings', 'elementskit-lite' ); ?>
					</a>
				</div>


				<div id="yelp_data_control" class="attr-panel-collapse attr-collapse" role="tabpanel"
					 aria-labelledby="yelp_data_headeing">
					<div class="attr-panel-body">
						<div class="ekit-admin-user-data-separator"></div>

						<?php
						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[yelp][page]',
								'label'       => esc_html__( 'Yelp Page', 'elementskit-lite' ),
								'placeholder' => 'awesome-cuisine-san-francisco',
								'value'       => ( ! isset( $user_data['yelp']['page'] ) ) ? '' : esc_html( $user_data['yelp']['page'] ),
							)
						);
						?>
					</div>
				</div>

			</div>

			<!-------------------
			facebook messenger
			-------------------->
			<div class="attr-panel ekit_accordion_card">
				<div
						data-attr-toggle="modal"
						data-target="#elementskit_go_pro_modal"
						class="<?php echo esc_attr($this->utils->is_widget_active_class( 'facebook-messenger', $pro_active )); ?>"
						role="tab" id="facebook_data_headeing">
					<a class="attr-btn" role="button" data-attr-toggle="collapse" data-parent="#accordion"
					   href="#fbm_control_data"
					   aria-expanded="false" aria-controls="fbm_control_data">
						<?php esc_html_e( 'Facebook Messenger', 'elementskit-lite' ); ?>
					</a>
				</div>

				<div id="fbm_control_data" class="attr-panel-collapse attr-collapse" role="tabpanel"
					 aria-labelledby="facebook_data_headeing">
					<div class="attr-panel-body">
						<div class="ekit-admin-user-data-separator"></div>
						<?php
						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[fbm_module][pg_id]',
								'label'       => esc_html__( 'Page ID', 'elementskit-lite' ),
								'placeholder' => '109208590868891',
								'value'       => ( ! isset( $user_data['fbm_module']['pg_id'] ) ) ? '' : esc_html( $user_data['fbm_module']['pg_id'] ),
							)
						);
						?>

						<?php
						$this->utils->input(
							array(
								'type'        => 'color',
								'name'        => 'user_data[fbm_module][txt_color]',
								'label'       => esc_html__( 'Color', 'elementskit-lite' ),
								'placeholder' => '#3b5998',
								'value'       => ( ! isset( $user_data['fbm_module']['txt_color'] ) ) ? '#3b5998' : esc_html( $user_data['fbm_module']['txt_color'] ),
							)
						);
						?>

						<?php
						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[fbm_module][l_in]',
								'label'       => esc_html__( 'Logged-in user greeting', 'elementskit-lite' ),
								'placeholder' => 'Hi! user',
								'value'       => ( ! isset( $user_data['fbm_module']['l_in'] ) ) ? 'Hi! user' : esc_html( $user_data['fbm_module']['l_in'] ),
							)
						);
						?>

						<?php
						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[fbm_module][l_out]',
								'label'       => esc_html__( 'Logged out user greeting', 'elementskit-lite' ),
								'placeholder' => 'Hi! guest',
								'value'       => ( ! isset( $user_data['fbm_module']['l_out'] ) ) ? 'Hi! guest' : esc_html( $user_data['fbm_module']['l_out'] ),
							)
						);
						?>

						<?php
						$this->utils->input(
							array(
								'type'    => 'switch',
								'name'    => 'user_data[fbm_module][is_open]',
								'label'   => esc_html__( 'Show Dialog Box', 'elementskit-lite' ),
								'value'   => '1',
								'options' => array(
									'checked' => ( isset( $user_data['fbm_module']['is_open'] ) ? true : false ),
								),
							)
						);
						?>

						<div><?php esc_html_e('Please add below domain as white listed in page advance messaging option', 'elementskit-lite'); ?> <a
									href="https://prnt.sc/u4zh96" target="_blank"><?php esc_html_e('how?', 'elementskit-lite'); ?></a></div>
						<div style="font-weight: bold;font-style: italic;color: blue;padding: 3px;"><?php echo esc_url(site_url()); ?></div>
					</div>
				</div>

			</div>

			<!-------------------
				dribble-feed
			-------------------->

			<div class="attr-panel ekit_accordion_card">
				<div
						class="<?php echo esc_attr($this->utils->is_widget_active_class( 'dribble-feed', $pro_active )); ?>"
						data-attr-toggle="modal"
						data-target="#elementskit_go_pro_modal"
						role="tab" id="dribble_data_headeing">
					<a class="attr-btn" role="button" data-attr-toggle="collapse" data-parent="#accordion"
					   href="#dribble_control_data"
					   aria-expanded="false" aria-controls="dribble_control_data">
						<?php esc_html_e( 'Dribbble User Data', 'elementskit-lite' ); ?>
					</a>
				</div>

				<div id="dribble_control_data"
					 class="attr-panel-collapse attr-collapse"
					 role="tabpanel"
					 aria-labelledby="dribble_data_headeing">
					<div class="attr-panel-body">
						<div class="ekit-admin-user-data-separator"></div>
						<?php

						$this->utils->input(
							array(
								'type'        => 'text',
								'disabled'    => '',
								'name'        => 'user_data[dribble][access_token]',
								'label'       => esc_html__( 'Access token', 'elementskit-lite' ),
								'placeholder' => 'Get a token....',
								'value'       => ( empty( $user_data['dribble']['access_token'] ) ) ? '' : esc_html( $user_data['dribble']['access_token'] ),
							)
						);

						?>


						<div class="ekit-admin-accordion-btn-group">

							<a href="https://token.wpmet.com/social_token.php?provider=dribbble"
							   class="ekit-admin-access-token ekit-admin-accordion-btn" target="_blank">
								<?php echo esc_html__( 'Get access token', 'elementskit-lite' ); ?>
							</a>

							<?php if ( did_action( 'elementskit/loaded' ) ) : ?>
								<a class="ekit-admin-access-token cache_clean_social_provider ekit-admin-accordion-btn"
								   data-provider="dribble_feed"
								   data-url_part="dribble">
									<?php echo esc_html__( 'Clear Cache', 'elementskit-lite' ); ?>
								</a>
							<?php endif; ?>

						</div>
					</div>
				</div>

			</div>

			<!-------------------
				twitter feed
			-------------------->
			<div class="attr-panel ekit_accordion_card">
				<div
						class="<?php echo esc_attr($this->utils->is_widget_active_class( 'twitter-feed', $pro_active )); ?>"
						data-attr-toggle="modal"
						data-target="#elementskit_go_pro_modal"
						role="tab" id="twetter_data_headeing">
					<a class="attr-btn attr-collapsed" role="button" data-attr-toggle="collapse"
					   data-parent="#accordion"
					   href="#twitter_data_control" aria-expanded="false" aria-controls="twitter_data_control">
						<?php esc_html_e( 'Twitter User Data', 'elementskit-lite' ); ?>
					</a>
				</div>

				<div id="twitter_data_control" class="attr-panel-collapse attr-collapse" role="tabpanel"
					 aria-labelledby="twetter_data_headeing">
					<div class="attr-panel-body">
						<div class="ekit-admin-user-data-separator"></div>
						<?php
						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[twitter][name]',
								'label'       => esc_html__( 'Twitter Username', 'elementskit-lite' ),
								'placeholder' => 'gameofthrones',
								'value'       => ( ! isset( $user_data['twitter']['name'] ) ) ? '' : esc_html( $user_data['twitter']['name'] ),

							)
						);
						?>
						<?php
						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[twitter][access_token]',
								'label'       => esc_html__( 'Access Token', 'elementskit-lite' ),
								'placeholder' => '97174059-g10REWwVvI0Mk02DhoXbqpEhh4zQg6SBIP2k8',
								// 'info' => esc_html__('Yuour', 'elementsKit-lite')
								'value'       => ( ! isset( $user_data['twitter']['access_token'] ) ) ? '' : esc_html( $user_data['twitter']['access_token'] ),
							)
						);
						?>

						<div class="ekit-admin-accordion-btn-group">
							<a class="ekit-admin-access-token ekit-admin-accordion-btn"
							   href="https://token.wpmet.com/index.php?provider=twitter" target="_blank">
								<?php echo esc_html__( 'Get Access Token', 'elementskit-lite' ); ?>
							</a>
						</div>
					</div>
				</div>

			</div>

			<!-------------------
				instagram-feed
			-------------------->
			<div class="attr-panel ekit_accordion_card">
				<div
						class="<?php echo esc_attr($this->utils->is_widget_active_class( 'instagram-feed', $pro_active )); ?>"
						data-attr-toggle="modal"
						data-target="#elementskit_go_pro_modal"
						role="tab" id="instagram_data_headeing">
					<a class="attr-btn attr-collapsed" role="button" data-attr-toggle="collapse"
					   data-parent="#accordion"
					   href="#instagram_data_control" aria-expanded="false" aria-controls="instagram_data_control">
						<?php esc_html_e( 'Instragram User Data', 'elementskit-lite' ); ?>
					</a>
				</div>

				<div id="instagram_data_control" class="attr-panel-collapse attr-collapse" role="tabpanel"
					 aria-labelledby="instagram_data_headeing">
					<div class="attr-panel-body">
						<div class="ekit-admin-user-data-separator"></div>

						<?php

						$user_id     = ( ! isset( $user_data['instragram']['user_id'] ) ) ? '' : esc_html( $user_data['instragram']['user_id'] );
						$insta_token = ( ! isset( $user_data['instragram']['token'] ) ) ? '' : esc_html( $user_data['instragram']['token'] );
						$insta_time  = ( ! isset( $user_data['instragram']['token_expire'] ) ) ? '' : intval( $user_data['instragram']['token_expire'] );
						$insta_gen   = ( ! isset( $user_data['instragram']['token_generated'] ) ) ? '' : gmdate('Y-m-d', strtotime($user_data['instragram']['token_generated']));

						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[instragram][user_id]',
								'label'       => esc_html__( 'User ID', 'elementskit-lite' ),
								'placeholder' => '',
								'value'       => $user_id,
							)
						);

						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[instragram][token]',
								'label'       => esc_html__( 'Access Token', 'elementskit-lite' ),
								'placeholder' => '',
								'value'       => $insta_token,
							)
						);

						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[instragram][token_expire]',
								'label'       => esc_html__( 'Token Expiry Time', 'elementskit-lite' ),
								'placeholder' => 'This is needed for automatically refreshing the token...',
								'value'       => $insta_time,
							)
						);

						$this->utils->input(
							array(
								'type'        => 'date',
								'name'        => 'user_data[instragram][token_generated]',
								'label'       => esc_html__( 'Token generation date', 'elementskit-lite' ),
								'placeholder' => 'This is needed for automatically refreshing the token...',
								'value'       => $insta_gen,
								'info'        => esc_html__( 'This is need to calculate the remaining time for token', 'elementskit-lite' ),
							)
						);

						?>


						<div class="ekit-admin-accordion-btn-group">
							<?php if ( did_action( 'elementskit/loaded' ) ) : ?>
								<a class="ekit-admin-access-token cache_clean_social_provider ekit-admin-accordion-btn"
								   data-provider="instagram_feed" data-url_part="instagram-feed">
									<?php echo esc_html__( 'Clear Cache', 'elementskit-lite' ); ?>
								</a>
							<?php endif; ?>

							<a href="https://token.wpmet.com/social_token.php?provider=instagram"
							   class="ekit-admin-access-token ekit-admin-accordion-btn" target="_blank">
								<?php echo esc_html__( 'Get access token', 'elementskit-lite' ); ?>
							</a>
						</div>
					</div>
				</div>

			</div>

			<!-------------------
				zoom
			-------------------->

			<div class="attr-panel ekit_accordion_card">
				<div
						class="<?php echo esc_attr($this->utils->is_widget_active_class( 'zoom', $pro_active )); ?>"
						data-attr-toggle="modal"
						data-target="#elementskit_go_pro_modal"
						role="tab" id="zoom_data_headeing">
					<a class="attr-btn attr-collapsed" role="button" data-attr-toggle="collapse"
					   data-parent="#accordion"
					   href="#zoom_data_control" aria-expanded="false" aria-controls="zoom_data_control">
						<?php esc_html_e( 'Zoom Data', 'elementskit-lite' ); ?>
					</a>
				</div>

				<div id="zoom_data_control" class="attr-panel-collapse attr-collapse" role="tabpanel"
					 aria-labelledby="zoom_data_headeing">
					<div class="attr-panel-body">
						<div class="ekit-admin-user-data-separator"></div>
						<?php
						if(method_exists('ElementsKit', 'version') && version_compare(\ElementsKit::version(), '3.5.0', '>')) {
							$this->utils->input(
								array(
									'type'        => 'text',
									'name'        => 'user_data[zoom][account_id]',
									'label'       => esc_html__( 'Account ID', 'elementskit-lite' ),
									'placeholder' => 'YhZDX-CdQ9KpttH2Ho7f0g',
									'value'       => ( ! isset( $user_data['zoom']['account_id'] ) ) ? '' : ( $user_data['zoom']['account_id'] ),
								)
							);

							$this->utils->input(
								array(
									'type'        => 'text',
									'name'        => 'user_data[zoom][client_id]',
									'label'       => esc_html__( 'Client ID', 'elementskit-lite' ),
									'placeholder' => 'dZMbttQeRUCaxzXtKoYZVQ',
									'value'       => ( ! isset( $user_data['zoom']['client_id'] ) ) ? '' : ( $user_data['zoom']['client_id'] ),
								)
							);

							$this->utils->input(
								array(
									'type'        => 'text',
									'name'        => 'user_data[zoom][client_secret]',
									'label'       => esc_html__( 'Client Secret', 'elementskit-lite' ),
									'placeholder' => '3Tlz4Dqd2hyf3q3bLRfn1GF6hr8tB2KR',
									'value'       => ( ! isset( $user_data['zoom']['client_secret'] ) ) ? '' : ( $user_data['zoom']['client_secret'] ),
								)
							);
						} else {
							$this->utils->input(
								array(
									'type'        => 'text',
									'name'        => 'user_data[zoom][api_key]',
									'label'       => esc_html__( 'Api key', 'elementskit-lite' ),
									'placeholder' => 'FmOUK_vdR-eepOExMhN7Kg',
									'value'       => ( ! isset( $user_data['zoom']['api_key'] ) ) ? '' : ( $user_data['zoom']['api_key'] ),
								)
							);

							$this->utils->input(
								array(
									'type'        => 'text',
									'name'        => 'user_data[zoom][secret_key]',
									'label'       => esc_html__( 'Secret Key', 'elementskit-lite' ),
									'placeholder' => 'OhDwAoNflUK6XkFB8ShCY5R7I8HxyWLMXR2SHK',
									'value'       => ( ! isset( $user_data['zoom']['secret_key'] ) ) ? '' : ( $user_data['zoom']['secret_key'] ),
								)
							);
						}
						?>
						<div class="ekit-admin-accordion-btn-group">
							<a href="https://token.wpmet.com/index.php?provider=zoom"
							   class="ekit-admin-access-token ekit-zoom-connection ekit-admin-accordion-btn"
							   target="_blank">
								<?php echo esc_html__( 'Check connection', 'elementskit-lite' ); ?>
							</a>
						</div>
					</div>
				</div>

			</div>

			<!-------------------
			   google-map
		   -------------------->

			<div class="attr-panel ekit_accordion_card">
				<div
						class="<?php echo esc_attr($this->utils->is_widget_active_class( 'google-map', $pro_active )); ?>"
						data-attr-toggle="modal"
						data-target="#elementskit_go_pro_modal"
						role="tab" id="google_map_data_heading">
					<a class="attr-btn attr-collapsed" role="button" data-attr-toggle="collapse"
					   data-parent="#accordion"
					   href="#google_map_data_control" aria-expanded="false"
					   aria-controls="google_map_data_control">
						<?php esc_html_e( 'Google Map', 'elementskit-lite' ); ?>
					</a>
				</div>

				<div id="google_map_data_control" class="attr-panel-collapse attr-collapse" role="tabpanel"
					 aria-labelledby="google_map_data_heading" aria-expanded='false'>
					<div class="attr-panel-body">
						<div class="ekit-admin-user-data-separator"></div>
						<?php
						$this->utils->input(
							array(
								'type'        => 'text',
								'name'        => 'user_data[google_map][api_key]',
								'label'       => esc_html__( 'Api Key', 'elementskit-lite' ),
								'placeholder' => 'AIzaSyA-10-OHpfss9XvUDWILmos62MnG_L4MYw',
								'value'       => ( ! isset( $user_data['google_map']['api_key'] ) ) ? '' : ( $user_data['google_map']['api_key'] ),
							)
						);
						?>

					</div>
				</div>

			</div>

            <!---------------------------------------
			   Google Sheet For Elementor Pro Form
		    ---------------------------------------->
            <div class="attr-panel ekit_accordion_card">
                <div class="<?php echo esc_attr( $this->utils->is_widget_active_class( 'google_sheet_for_elementor_pro_form', $pro_active ) ); ?>" data-attr-toggle="modal" data-target="#elementskit_go_pro_modal" role="tab" id="google_sheet_data_heading">
                    <a class="attr-btn attr-collapsed" role="button" data-attr-toggle="collapse"
                       data-parent="#accordion"
                       href="#google_sheet_data_control" aria-expanded="false"
                       aria-controls="google_sheet_data_control">
						<?php esc_html_e('Google Sheet For Elementor Pro Form', 'elementskit-lite'); ?>
                    </a>
                </div>

                <?php if(class_exists('\ElementsKit\Modules\Google_Sheet_Elementor_Pro_Form\Init')): ?>

                <div id="google_sheet_data_control" class="attr-panel-collapse attr-collapse" role="tabpanel"
                     aria-labelledby="google_sheet_data_heading" aria-expanded='false'>
                    <div class="attr-panel-body">
                        <div class="ekit-admin-user-data-separator"></div>
						<?php
						$this->utils->input([
                            'type'        => 'text',
                            'name'        => 'user_data[google][client_id]',
                            'label'       => esc_html__('Google Client Id', 'elementskit-lite'),
                            'placeholder' => '',
                            'value'       => (!isset($user_data['google']['client_id'])) ? '' : ($user_data['google']['client_id'])
						]);
						$this->utils->input([
                            'type'        => 'text',
                            'name'        => 'user_data[google][client_secret]',
                            'label'       => esc_html__('Google Client Secret', 'elementskit-lite'),
                            'placeholder' => '',
                            'value'       => (!isset($user_data['google']['client_secret'])) ? '' : ($user_data['google']['client_secret']),
						]);
						?>
                        <div>
                            <ol>
                                <li><?php 

								echo sprintf(
									'%1$s <a href="%2$s" target="_blank">%2$s</a> %3$s',
									esc_html__('Click', 'elementskit-lite'),
									esc_html('https://console.cloud.google.com'),
									esc_html__('and create App/Project On Google developer account', 'elementskit-lite')
								); ?>
								</li>
                                <li><?php esc_html_e('Must add the following URL to the "Valid OAuth redirect URIs" field:', 'elementskit-lite')?> <strong style="font-weight:700;"><?php echo esc_url( admin_url('admin.php?page=elementskit') )?></strong></li>
                                <li><?php esc_html_e('After getting the App ID & App Secret, put those information', 'elementskit-lite')?></li>
                                <li><?php esc_html_e('Click on "Save Changes"', 'elementskit-lite')?></li>
                                <li><?php esc_html_e('Click on "Generate Access Token"', 'elementskit-lite')?></li>
                            </ol>
                            <?php
                            $access_token = get_option( \ElementsKit\Modules\Google_Sheet_Elementor_Pro_Form\Google_Sheet::ACCESS_TOKEN_KEY );
                            $get_code_api = \ElementsKit\Modules\Google_Sheet_Elementor_Pro_Form\Init::get_code_url();

                            if($access_token):
                                echo '<p>'.esc_html__('Note:- After 200 days your token will be expired, before the expiration of your token,', 'elementskit-lite').' <a href="' . esc_url( $get_code_api ) . '">'. esc_html__('Generate a new access Token', 'elementskit-lite').'</a></p>';
                            else:
                            ?>
                           <a href="<?php echo esc_url( $get_code_api ) ?>"><?php esc_html_e('Generate Access Token', 'elementskit-lite'); ?></a>
                           <?php endif ?>
                        </div>
                    </div>
                </div>
                <?php endif ?>
            </div>

			<?php do_action('elementskit/admin/sections/userdata'); ?>

		</div>
	</div>
</div>
