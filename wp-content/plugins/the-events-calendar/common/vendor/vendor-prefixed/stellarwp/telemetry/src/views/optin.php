<?php
/**
 * $args comes from load_template() in OptinTemplate.php
 *
 * @var array $args
 */

?>
<div id="optin-modal-<?php echo esc_attr( $args['plugin_slug'] ); ?>" class="stellarwp-telemetry stellarwp-telemetry-modal stellarwp-telemetry-modal--active" data-js="optin-modal">
	<section class="stellarwp-telemetry-modal__inner">
		<header>
			<img src="<?php echo esc_url( $args['plugin_logo'] ); ?>" width="<?php echo esc_attr( $args['plugin_logo_width'] ); ?>" height="<?php echo esc_attr( $args['plugin_logo_height'] ); ?>" alt="<?php echo esc_attr( $args['plugin_logo_alt'] ); ?>"/>
			<h1 class="stellarwp-telemetry__title">
				<?php echo esc_attr( $args['heading'] ); ?>
			</h1>
		</header>
		<main>
			<p>
				<?php echo esc_attr( $args['intro'] ); ?>
			</p>
			<ul class="stellarwp-telemetry-links">
				<li>
					<a href="<?php echo esc_url( $args['permissions_url'] ); ?>" target="_blank" class="stellarwp-telemetry-links__link">
						<?php echo esc_html__( 'What permissions are being granted?', 'stellarwp-telemetry' ); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo esc_url( $args['tos_url'] ); ?>" target="_blank" class="stellarwp-telemetry-links__link">
						<?php echo esc_html__( 'Terms of Service', 'stellarwp-telemetry' ); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo esc_url( $args['privacy_url'] ); ?>" target="_blank" class="stellarwp-telemetry-links__link">
						<?php echo esc_html__( 'Privacy Policy', 'stellarwp-telemetry' ); ?>
					</a>
				</li>
			</ul>
			<?php if ( $args['opted_in_plugins'] ) { ?>
				<div class="stellarwp-telemetry-other-plugins">
					<button class="stellarwp-telemetry-btn-text" data-js="other-plugins-toggle">
						<?php echo esc_html__( 'Other plugins you have opted in:', 'stellarwp-telemetry' ); ?>
					</button>
					<div class="stellarwp-telemetry-other-plugins__list">
						<ul>
							<?php foreach ( $args['opted_in_plugins'] as $opted_in_plugin ) { ?>
								<li>
									<?php echo esc_attr( $opted_in_plugin ); ?>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
			<?php } ?>
		</main>
		<footer>
			<form method="post" action="" data-js="optin-form">
				<input type="hidden" name="action" value="stellarwp-telemetry">
				<input type="hidden" name="stellar_slug" value="<?php echo esc_attr( $args['plugin_slug'] ); ?>">
				<input type="hidden" name="opt_in_text" value="<?php echo esc_attr( $args['intro'] ); ?>">
				<?php wp_nonce_field( 'stellarwp-telemetry' ); ?>
				<button class="stellarwp-telemetry-btn-primary" data-js="form-submit" type="submit" name="optin-agreed" value="true">
					<?php echo esc_html__( 'Allow & Continue', 'stellarwp-telemetry' ); ?>
				</button>
				<button data-js="close-modal" class="stellarwp-telemetry-btn-text stellarwp-telemetry-btn-text--skip" type="submit" name="optin-agreed" value="false">
					<?php echo esc_html__( 'Skip', 'stellarwp-telemetry' ); ?>
				</button>
			</form>
		</footer>
	</section>
</div>
