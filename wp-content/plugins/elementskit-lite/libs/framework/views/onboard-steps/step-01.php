<div class="ekit-admin-fields-container-fieldset">
	<?php
		$filter = \ElementsKit_Lite\Libs\Framework\Attr::instance()->utils->get_option( 'settings', array() );
		
		$this->utils->input(
			array(
				'type'        => 'radio',
				'name'        => 'settings[filter_widget_module]',
				'value'       => 'basic',
				'description' => esc_html__( 'General widgets will be activated to build your website. Best suited for lightweight-fast starter websites.', 'elementskit-lite' ),
				'label'       => esc_html__( 'Basic (Recommended)', 'elementskit-lite' ),
				'options'     => array(
					'checked' => ! empty( $filter['filter_widget_module'] ) ? $filter['filter_widget_module'] === 'basic' : true,
				),
			)
		);
		$this->utils->input(
			array(
				'type'        => 'radio',
				'name'        => 'settings[filter_widget_module]',
				'value'       => 'advanced',
				'description' => esc_html__( 'All the free dynamic widgets will be activated to increase flexibility & functionality to build your complex website in no-time.', 'elementskit-lite' ),
				'label'       => esc_html__( 'Advanced', 'elementskit-lite' ),
				'options'     => array(
					'checked' => ! empty( $filter['filter_widget_module'] ) ? $filter['filter_widget_module'] === 'advanced' : false,
				),
			)
		);
		$this->utils->input(
			array(
				'type'        => 'radio',
				'name'        => 'settings[filter_widget_module]',
				'value'       => 'custom',
				'class'       => 'ekit-onboard-custom-filter',
				'description' => esc_html__( 'You choose your website as per your need.', 'elementskit-lite' ),
				'label'       => esc_html__( 'Custom', 'elementskit-lite' ),
				'options'     => array(
					'checked' => ! empty( $filter['filter_widget_module'] ) ? $filter['filter_widget_module'] === 'custom' : false,
				),
			)
		);
		?>
</div>

<div class="ekit-onboard-section ekit-onboard-module">
	<h2 class="ekit-onboard-section-title"><?php echo esc_html__( 'Modules', 'elementskit-lite' ); ?></h2>
	<?php require self::get_dir() . 'views/settings-sections/modules.php'; ?>
</div>

<div class="ekit-onboard-section ekit-onboard-widget">
	<?php require self::get_dir() . 'views/settings-sections/widgets.php'; ?>
</div>

<div class="ekit-onboard-pagination">
	<a class="ekit-onboard-btn ekit-onboard-pagi-btn prev" href="#"><i class="icon icon-arrow-left"></i><?php echo esc_html__( 'Back', 'elementskit-lite' ); ?></a>
	<a class="ekit-onboard-btn ekit-onboard-pagi-btn next" href="#"><?php echo esc_html__( 'Next Step', 'elementskit-lite' ); ?></a>
</div>
