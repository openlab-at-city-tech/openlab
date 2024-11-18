<?php
/**
 * The template for displaying Navigation Back for KB Article.
 *
 * This template can be overridden by copying it to yourtheme/kb_templates/feature-navigation-back.php.
 *
 * HOWEVER, on occasion Echo Plugins will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		Echo Plugins
 */
/** @var WP_Post $article */
/** @var EPKB_KB_Config_DB $kb_config */

//$button_class1 = EPKB_Utilities::get_css_class('::section_box_shadow', $kb_config);

$button_style1_escaped = EPKB_Utilities::get_inline_style(
                'margin-top:: back_navigation_margin_top,
                 margin-right:: back_navigation_margin_right,
                 margin-bottom:: back_navigation_margin_bottom,
                 margin-left:: back_navigation_margin_left',
				 $kb_config );

$button_style2_escaped = EPKB_Utilities::get_inline_style(
	           'padding-top:: back_navigation_padding_top,
                      padding-right:: back_navigation_padding_right,
                      padding-bottom:: back_navigation_padding_bottom,
                      padding-left:: back_navigation_padding_left,
                      color:: back_navigation_text_color,
                      background-color:: back_navigation_bg_color,
                      typography:: back_navigation_typography,
                      border-radius:: back_navigation_border_radius,
                      border-style:: back_navigation_border,
                      border-width:: back_navigation_border_width,
                      border-color:: back_navigation_border_color', $kb_config );

echo '<div class="eckb-navigation-back  ' . //$kb_config['back_navigation_hover'] .  // what is the hover for? not in configuration specs
          '" ' . $button_style1_escaped . '>';

if ( $kb_config['back_navigation_mode'] == 'navigate_kb_main_page' ) {
	echo '<div class="eckb-navigation-button">';
    echo '<a tabindex="0" href="' . esc_url( EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config ) ) . '" ' . $button_style2_escaped . '>' . esc_html( $kb_config['back_navigation_text'] ) .  '</a>';//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo '</div>';
} else {
	
	if (  empty( $_REQUEST['epkb-editor-page-loaded'] ) ) {
		echo '<div tabindex="0" class="eckb-navigation-button" ' . $button_style2_escaped . ' onclick="history.go(-1);" >' . esc_html( $kb_config['back_navigation_text'] ) . '</div>';//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		echo '<div tabindex="0" class="eckb-navigation-button" ' . $button_style2_escaped . ' onclick="return false;" >' . esc_html( $kb_config['back_navigation_text'] ) . '</div>';//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

echo '</div>';
