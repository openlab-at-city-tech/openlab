<?php if (!defined('ABSPATH')) die('No direct access.'); 
$demo_options = array(
	array(
		'slug' => 'carousel',
		'label' => esc_html__( 'Carousel Slideshow', 'ml-slider' )
	),
	array(
		'slug' => 'withcaption',
		'label' => esc_html__( 'Carousel Slideshow with Captions', 'ml-slider' )
	)
);

// Allow to add new options
$demo_options = apply_filters( 'metaslider_quickstart_options', $demo_options );
?>
<div id="metaslider-ui">
<div class="metaslider-start mt-16">
	<div class="metaslider-welcome">
		<div class="welcome-panel-content items-center">
			<h2><?php esc_html_e('Thanks for using MetaSlider, the WordPress slideshow plugin', 'ml-slider'); ?></h2>
		</div>
		<div class="welcome-panel-content" style="min-height:270px;">
			<div class="ms-panel-container">
				<div class="">
					<div>
						<h3 class="ms-heading"><?php esc_html_e('Create a slideshow with your images', 'ml-slider'); ?></h3>
						<p><?php esc_html_e('Choose your own images to start a new slideshow.', 'ml-slider'); ?></p>
					</div>
					<div>
						<div id="plupload-upload-ui" class="hide-if-no-js">
							<div id="drag-drop-area">
								<div class="drag-drop-inside">
								<p class="drag-drop-info"><?php _e('Drop files to upload'); ?></p>
								<p><?php _ex('or', 'Uploader: Drop files to upload - or - Select Files', 'ml-slider'); ?></p>
								<p class="drag-drop-buttons">
									<input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files'); ?>" class="button" />
									<button id="quickstart-browse-button" class="button"><?php esc_html_e('Open Media Library', 'ml-slider'); ?></button>
								</p>
								
								</div>
							</div>
						</div>
						<div class="media-upload-form">
							<div id="media-items" class="hide-if-no-js"></div>
						</div>
						
					</div>
				</div>
				<div class="">

					<div>
						<h3 class="ms-heading"><?php esc_html_e('Import a demo slideshow', 'ml-slider'); ?></h3>
						<p>
							<?php 
								echo apply_filters( 'metaslider_quickstart_description', esc_html__('Use sample slides to quickly create a new slideshow.', 'ml-slider'));  //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</p>
					</div>

					<div class="try-gutenberg-action">
						<select id="sampleslider-options">
							<option value="<?php echo esc_url(wp_nonce_url(admin_url("admin-post.php?action=metaslider_create_slider"), "metaslider_create_slider")); ?>"><?php esc_html_e('Blank Slideshow', 'ml-slider'); ?></option>
							<option value="<?php echo esc_url(wp_nonce_url(admin_url("admin-post.php?action=metaslider_create_slider&metaslider_add_sample_slides"), "metaslider_create_slider")); ?>"><?php esc_html_e('Image Slideshow', 'ml-slider'); ?></option>
							<?php foreach ( $demo_options as $item ) : ?>
								<option value="<?php 
									echo esc_url( 
										wp_nonce_url( 
											admin_url( 
												'admin-post.php?action=metaslider_create_slider&metaslider_add_sample_slides=' . $item['slug'] ), 
												'metaslider_create_slider' 
											) 
										) ?>">
									<?php echo esc_html( $item['label'] ) ?>
								</option>
							<?php endforeach; ?>
						</select>
						<button id="sampleslider-btn" class="button button-primary"><?php esc_html_e('Create a Slideshow', 'ml-slider'); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<?php // TODO: I think after here maybe we can add images from their media library, or perhaps from an external image API
