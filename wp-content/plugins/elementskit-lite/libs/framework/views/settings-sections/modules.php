<?php
$modules_all    = \ElementsKit_Lite\Config\Module_List::instance()->get_list( 'optional' );
$modules_active = \ElementsKit_Lite\Config\Module_List::instance()->get_list( 'active' );
?>
<pre>
<?php 
$x = \ElementsKit_Lite\Libs\Framework\Attr::instance()->utils->get_option( 'module_list', array() );
// print_r($modules_active) ;
?>
</pre>
<!-- this blank input is for empty form submission -->
<input checked="checked" type="checkbox" value="_null" style="display:none" name="module_list[]" >

<div class="ekit-admin-fields-container">
	<span class="ekit-admin-fields-container-description"><?php esc_html_e( 'You can disable the modules you are not using on your site. That will disable all associated assets of those modules to improve your site loading speed.', 'elementskit-lite' ); ?></span>

	<div class="ekit-admin-fields-container-fieldset">
		<div class="attr-hidden" id="elementskit-template-admin-menu">
			<li><a href="edit.php?post_type=elementskit_template"><?php esc_html_e( 'Header Footer', 'elementskit-lite' ); ?></a></li>
		</div>
		<div class="attr-hidden" id="elementskit-template-widget-menu">
			<li><a href="edit.php?post_type=elementskit_widget"><?php esc_html_e( 'Widget Builder', 'elementskit-lite' ); ?></a></li>
		</div>
		<div class="attr-row">
			<?php 
			foreach ( $modules_all as $module => $module_config ) :
				if ( ! isset( $module_config['package'] ) ) {
					$module_config['package'] = ''; // for avoiding error when add module from theme
				}
				?>
			<div class="attr-col-md-6 attr-col-lg-4" <?php echo ( $module_config['package'] != 'pro-disabled' ? '' : 'data-attr-toggle="modal" data-target="#elementskit_go_pro_modal"' ); ?>>
				<?php
				$this->utils->input(
					array(
						'type'    => 'switch',
						'name'    => 'module_list[]',
						'value'   => $module,
						'class'   => 'ekit-content-type-' . $module_config['package'],
						'attr'    => ( $module_config['package'] != 'pro-disabled' ? array() : array( 'disabled' => 'disabled' ) ),
						'label'   => $module_config['title'],
						'options' => array(
							'checked' => ( isset( $modules_active[ $module ] ) ? true : false ),
						),
					)
				);
				?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
