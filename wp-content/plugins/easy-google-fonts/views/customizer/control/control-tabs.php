<?php 
/**
 * Font Control Tabs
 *
 * Ouputs the tabs used to navigate the settings
 * for each font control.
 * 
 * @package   Easy_Google_Fonts
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.4
 * 
 */
?>
<div class="egf-customizer-tabs">
	<ul>
		<?php foreach ( $this->tabs as $id => $tab ): ?>
			<li data-customize-tab='<?php echo esc_attr( $id ); ?>' class="<?php if ( $tab['selected'] ) : ?>selected<?php endif; ?> " tabindex='0'>
				<?php echo esc_html( $tab['label'] ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="egf-clear"></div>
</div>
