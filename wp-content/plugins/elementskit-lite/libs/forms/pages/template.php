<?php
$pluginStatus = \ElementsKit_Lite\Libs\Framework\Classes\Plugin_Status::instance();
$plugin = $pluginStatus->get_status( 'metform/metform.php' );
?>

<div class="ekit-wrap notice">
	<div class="ekit-forms-wrapper">
		<img class="ekit-form-image" src="<?php echo esc_url(self::get_url() . 'assets/images/metform.svg');?>" alt="Metform">
		<h1><?php esc_html_e('Create forms — faster and easier than ever!', 'elementskit-lite')?></h1>
		<p><?php esc_html_e('Metform brings flexible form-building experience right inside your Elementor Editor','elementskit-lite')?> </br> <?php esc_html_e('So you can build stunning forms within minutes!', 'elementskit-lite')?></p>
		<a 
		data-plugin_status="<?php echo esc_attr($plugin['status']); ?>" 
		data-installation_url="<?php echo esc_url($plugin['installation_url']); ?>" 
		data-activation_url="<?php echo esc_url($plugin['activation_url']); ?>" 
		href="<?php echo $plugin['status'] == 'not_installed' ? esc_url($plugin['installation_url']) : esc_url($plugin['activation_url']); ?>" 
		data-installing_text="<?php echo esc_attr__('Installing...', 'elementskit-lite'); ?>" 
		data-activating_text="<?php echo esc_attr__('Activating...', 'elementskit-lite'); ?>" 
		data-activated_text="<?php echo esc_attr__('Activated', 'elementskit-lite'); ?>" 
		class="ekit-form-btn">
			<?php echo esc_html($plugin['title']); ?>
		</a>
	</div>
</div>
	