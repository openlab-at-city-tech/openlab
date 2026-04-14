<?php

/**
 * Customizer Builder
 * Switcher Field Control
 *
 * @since 4.0
 */

namespace InstagramFeed\Builder\Controls;

if (!defined('ABSPATH')) {
	exit;
}

class SB_Switcher_Control extends SB_Controls_Base
{
	/**
	 * Get control type.
	 *
	 * Getting the Control Type
	 *
	 * @return string
	 * @since 4.0
	 * @access public
	 */
	public function get_type()
	{
		return 'switcher';
	}

	/**
	 * Output Control
	 *
	 * @return HTML
	 * @since 4.0
	 * @access public
	 */
	public function get_control_output($controlEditingTypeModel)
	{
		?>
		<div class="sb-control-switcher-ctn"
			 :data-active="<?php echo $controlEditingTypeModel ?>[control.id] == control.options.enabled"
			 @click.prevent.default="changeSwitcherSettingValue(control.id, control.options.enabled, control.options.disabled, control.ajaxAction ? control.ajaxAction : false)">
			<div class="sb-control-switcher sb-tr-2"></div>
			<div class="sb-control-label" v-if="control.label" :data-title="control.labelStrong ? 'true' : false">
				{{control.label}}
			</div>
		</div>
		<?php
	}
}