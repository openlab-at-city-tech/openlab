<?php

namespace InstagramFeed\Integrations\Elementor;

use Elementor\Base_Data_Control;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class SBI_Feed_Elementor_Control
 *
 * @since 6.2.9
 */
class SBI_Feed_Elementor_Control extends Base_Data_Control
{
	/**
	 * Get control type.
	 *
	 * Retrieve the control type, in this case `sbi_feed`.
	 *
	 * @return string Control type.
	 * @since 6.2.9
	 * @access public
	 */
	public function get_type()
	{
		return 'sbi_feed_control';
	}

	/**
	 * Enqueue control scripts and styles.
	 *
	 * Used to register and enqueue custom scripts and styles.
	 *
	 * @since 6.2.9
	 * @access public
	 */
	public function enqueue()
	{
		wp_enqueue_style(
			'sb-elementor-style',
			SBI_PLUGIN_URL . 'admin/assets/css/sb-elementor.css',
			null,
			SBIVER
		);
	}

	/**
	 * Render control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template.
	 *
	 * @since 6.2.9
	 * @access public
	 */
	public function content_template()
	{
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
			<# if ( data.label ) {#>
			<label for="<?php echo $control_uid; ?>" class="elementor-control-title" style="font-weight: 700;">{{{
				data.label }}}</label>
			<# } #>
			<div class="elementor-control-input-wrapper elementor-control-unit-5">
				<select id="<?php echo $control_uid; ?>" data-setting="{{ data.name }}"
						onchange="jQuery(this).parents('.elementor-control-field').find('.link-sbi-builder').attr('href', '<?php echo admin_url('admin.php?page=sbi-feed-builder') ?>&feed_id='+jQuery(this).val())">
					<#
					var printOptions = function( options ) {
					_.each( options, function( option_title, option_value ) { #>
					<option value="{{ option_value }}">{{{ option_title }}}</option>
					<# } );
					};

					if ( data.groups ) {
					for ( var groupIndex in data.groups ) {
					var groupArgs = data.groups[ groupIndex ];
					if ( groupArgs.options ) { #>
					<optgroup label="{{ groupArgs.label }}">
						<# printOptions( groupArgs.options ) #>
					</optgroup>
					<# } else if ( _.isString( groupArgs ) ) { #>
					<option value="{{ groupIndex }}">{{{ groupArgs }}}</option>
					<# }
					}
					} else {
					printOptions( data.options );
					}
					#>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>

		<div style="font-weight: 700; color:#a73061; margin-top: 10px;">
			<# if( data.controlValue != undefined && data.controlValue != '0' ) { #>
			<a class="link-sbi-builder"
			   href="<?php echo admin_url('admin.php?page=sbi-feed-builder') ?>&feed_id={{data.controlValue}}"
			   target="_blank" rel="noopener"><?php echo __('Edit this Feed', 'instagram-feed'); ?></a>
			<span style="color:#aaa; display: inline-block; margin: 0 5px;">|</span>
			<# } #>
			<a href="<?php echo admin_url('admin.php?page=sbi-feed-builder') ?>" target="_blank"
			   rel="noopener"><?php echo __('Create New Feed', 'instagram-feed'); ?></a>
		</div>

		<?php
	}

	/**
	 * Get default settings.
	 *
	 * Retrieve the default settings of the control. Used to return the default
	 * settings while initializing the control.
	 *
	 * @return array Control default settings.
	 * @since 6.2.9
	 * @access protected
	 */
	protected function get_default_settings()
	{
		return [
			'label_block' => false,
		];
	}
}