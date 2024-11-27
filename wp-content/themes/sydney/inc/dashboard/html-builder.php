<?php

/**
 * Tabs Nav Items
 * 
 * @package Dashboard
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$existing_parts = $this->get_template_parts();

$parts = array( 
	'header' 		=> __( 'Header', 'sydney' ),
	'page_title' 	=> __( 'Page Title', 'sydney' ),
	'content' 		=> __( 'Content', 'sydney' ),
	'footer' 		=> __( 'Footer', 'sydney' ),
);

//disabled links in free
$disabled = !$this->settings['has_pro'] ? 'style="pointer-events:none;"' : '';

?>
<div class="sydney-dashboard-row">
	<div class="sydney-dashboard-column">
		<div class="sydney-dashboard-card sydney-dashboard-card-top-spacing sydney-dashboard-card-tabs-divider">

		<div class="template-builder-wrapper">
			<h3><?php esc_html_e( 'Template Builder', 'sydney' ); ?></h3>
			
			<?php if ( !$this->settings['has_pro'] ) : ?>
				<p><?php echo '<strong>' . esc_html__( 'Please note: ', 'sydney' ) . '</strong>' . esc_html__( 'This feature is available only in Sydney Pro', 'sydney' ); ?></p>
			<?php endif; ?>

			<ol>
				<li><?php esc_html_e( 'Replace theme-built components like header, footer, etc. on specific pages or everywhere.', 'sydney' ); ?></li>
				<li><?php esc_html_e( 'Use the Global Template to create parts for all pages.', 'sydney' ); ?></li>
				<li><?php esc_html_e( 'You can create multiple templates and assign them to specific pages.', 'sydney' ); ?></li>
			</ol>
			<hr>
			<p class="tutorial-video"><span class="dashicons dashicons-editor-help"></span> <?php esc_html_e( 'Need help?', 'sydney' ); ?> <a target="_blank" href="https://youtu.be/MhKdxFeFOd8"><?php esc_html_e( 'Watch a quick tutorial video.', 'sydney' ); ?></a></p>
			<div id="template-builder" data-pro="<?php echo esc_attr( $this->settings['has_pro'] ? "true" : "false" ); ?>">
				<?php 
				$templates = array();
				$custom_templates = get_option( 'sydney_template_builder_data' );

				if ( !is_array( $custom_templates ) && empty( $custom_templates ) ) {
					$templates['global'] = array(
						'id' 			=> 'global',
						'template_name' => 'Global',
						'conditions' 	=> '',
						'header' 		=> '',
						'page_title' 	=> '',
						'content' 		=> '',
						'footer' 		=> '',
					);
				} else {
					$templates = $custom_templates;
				}

				if ( !empty( $templates ) ) : ?>
					<?php foreach ( $templates as $key => $template ) : ?>

						<?php            
						$conditions = ( isset( $template['conditions'] ) ) ? json_decode($template['conditions'], true ) : array();

						$labels = array();

						if ( ! empty( $conditions ) ) {
							foreach ( $conditions as $value ) {
								if ( ! empty( $value['id'] ) ) {
									$labels[ $value['id'] ] = $this->get_option_text($value);
								}
							}
						}

						$settings = array(
							'values' => $conditions,
							'labels' => $labels,
						);
						?>
						<div class="template-item" data-id="<?php echo esc_attr( $template['id'] ); ?>">

							<div class="template-name">
								
								<?php if ( 'global' !== $template['id'] ) : ?>
								<input type="text" name="template_name" value="<?php echo isset( $template['template_name'] ) ? esc_attr( $template['template_name'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Template Name', 'sydney' ); ?>">
								<?php else : ?>
								<h4 style="margin-top:12px;margin-bottom:0;"><?php echo esc_html__('Global Template', 'sydney'); ?></h4>
								<?php endif; ?>

								<div class="template-options" style="display: <?php echo ( isset( $template['id'] ) && 'global' == $template['id'] ) ? 'none' : 'block'; ?>">
									<div class="sydney-display-conditions-control" data-condition-settings="<?php echo esc_attr( json_encode( $settings ) ); ?>">
										<a href="#" title="<?php esc_attr_e( 'Display conditions', 'sydney' ); ?>" class="sydney-display-conditions-modal-toggle"><span class="dashicons dashicons-admin-generic"></span><span style="min-width:120px;margin-left:-60px;" class="tooltip"><?php esc_html_e( 'Display conditions', 'sydney' ); ?></span></a>
										<div class="sydney-display-conditions-modal">
										<!-- Modal content goes here -->
										</div>
										<input class="sydney-display-conditions-textarea" type="hidden" name="conditions" value="<?php echo isset( $template['conditions'] ) ? esc_attr( $template['conditions'] ) : ''; ?>">
									</div>
									<a href="#" title="<?php esc_attr_e( 'Duplicate', 'sydney' ); ?>" class="duplicate-template"><span class="dashicons dashicons-admin-page"></span><span class="tooltip"><?php esc_html_e( 'Duplicate', 'sydney' ); ?></span></a>
									<a href="#" title="<?php esc_attr_e( 'Delete', 'sydney' ); ?>" class="delete-template"><span class="dashicons dashicons-trash"></span><span class="tooltip"><?php esc_html_e( 'Delete', 'sydney' ); ?></span></a>
								</div>                    
							</div>

							<?php 
							foreach ( $parts as $type => $part ) : ?>
							<div class="template-part <?php echo esc_attr( $type ); ?>" data-page-builder="<?php echo !empty( $template[$type . '_builder'] ) ? $template[$type . '_builder'] : ''; ?>" data-part-type="<?php echo esc_attr( $type ); ?>" data-part-active="<?php echo !empty( $template[$type] ) ? 'active' : 'inactive'; ?>">
								<div class="template-part-inner">
									<span class="part-title">
										<span class="not-selected" style="display:<?php echo !empty( $template[$type] ) ? 'none' : 'block'; ?>;"><?php echo sprintf( __( 'Select %s', 'sydney' ), $part ); ?></span>
										<span class="selected" style="display:<?php echo !empty( $template[$type] ) ? 'block' : 'none'; ?>;"><span class="dashicons dashicons-yes-alt"></span><?php echo sprintf( __( '%s Selected', 'sydney' ), $part ); ?></span>
									</span>
									<div class="part-options" style="display:none;">
										<span class="select-existing"><?php esc_html_e( 'Select Existing', 'sydney' ); ?></span>
										<span class="select-page-builder"><?php esc_html_e( 'Create New', 'sydney' ); ?></span>
										<span class="edit-part"><?php esc_html_e( 'Edit', 'sydney' ); ?></span>
										<span class="reset"><?php esc_html_e( 'Reset', 'sydney' ); ?></span>
									</div>

									<input class="part-id" type="hidden" name="<?php echo esc_attr( $type ); ?>" value="<?php echo isset( $template[$type] ) ? esc_attr( $template[$type] ) : ''; ?>">
								</div>
								<div class="part-options-toggle">
									<span class="dashicons dashicons-ellipsis"></span>
								</div>
								<?php echo $this->existing_parts_select( $existing_parts ); ?>
								<div class="page-builder-wrapper" style="display:none;">
									<span class="page-builder-title"><?php esc_html_e( 'Choose your builder:', 'sydney' ); ?></span>
									<?php if ( class_exists( 'Elementor\Plugin' ) ) : ?>
									<span class="elementor"><span <?php echo $disabled; ?> class="create-new" data-page-builder="elementor"><?php sydney_get_svg_icon( 'icon-elementor', true ); ?><?php esc_html_e( 'Elementor', 'sydney' ); ?></span></span>
									<?php endif; ?>
									<span class="editor"><span <?php echo $disabled; ?> class="create-new" data-page-builder="editor"><?php sydney_get_svg_icon( 'icon-wordpress', true ); ?><?php esc_html_e( 'WordPress Editor', 'sydney' ); ?></span></span>
								</div>						
							</div>
							<?php endforeach; ?>
							<?php if ( !$this->settings['has_pro'] ) : ?>
								<div class="tb-upgrade-overlay">
									<div>
										<span class="dashicons dashicons-lock"></span>
										<h4><a href="<?php echo esc_url( $this->settings['upgrade_pro'] ); ?>"><?php esc_html_e( 'Upgrade to Sydney Pro', 'sydney' ); ?></a></h4>
									</div>
								</div>
							<?php endif; ?>
						</div>                
					<?php endforeach; ?>
				<?php endif; //check if templates is empty ?>

				<div class="add-new-template-wrapper">
					<span id="add-new-template"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e( 'Add Template', 'sydney' ); ?></span>
				</div>

			</div>
			
			<div class="buttons" style="display:flex;">
				<button class="button button-primary" id="save-templates" <?php echo $disabled; ?>><?php esc_html_e( 'Save', 'sydney' ); ?></button>
			</div>

			<div class="sydney-elementor-iframe-wrapper" style="display:none;">
				<iframe class="sydney-elementor-iframe"></iframe>
				<a href="#" class="sydney-editor-close"><?php sydney_get_svg_icon( 'icon-cancel', true ); ?></a>
			</div>			

			</div>
		</div>
	</div>
</div>