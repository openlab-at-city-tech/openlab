<?php

/*
Plugin Name: Gravity Forms Zapier Add-on
Plugin URI: https://www.gravityforms.com
Description: Integrates Gravity Forms with Zapier, allowing form submissions to be automatically sent to your configured Zaps.
Version: 3.2.1
Author: rocketgenius
Author URI: https://www.rocketgenius.com
License: GPL-2.0+
Text Domain: gravityformszapier
Domain Path: /languages

------------------------------------------------------------------------
Copyright 2009-2019 rocketgenius

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

defined( 'ABSPATH' ) || die();

add_action( 'init', array( 'GFZapier', 'init' ) );

class GFZapier {

	private static $slug = 'gravityformszapier';
	private static $path = 'gravityformszapier/zapier.php';
	private static $url = 'https://www.gravityforms.com';
	private static $version = '3.2.1';
	private static $min_gravityforms_version = '1.9.10';

	private static $_current_body = null;

	/**
	 * If true, maybe_delay_feed() checks will be bypassed allowing the feeds to be processed.
	 *
	 * @var bool
	 */
	protected static $_bypass_feed_delay = false;

	public static function init() {

		//supports logging
		add_filter( 'gform_logging_supported', array( 'GFZapier', 'set_logging_supported' ) );

		if ( basename( $_SERVER['PHP_SELF'] ) == 'plugins.php' ) {
			//loading translations
			load_plugin_textdomain( 'gravityformszapier', false, '/gravityformszapier/languages' );
			add_action( 'after_plugin_row_' . self::$path, array( 'GFZapier', 'plugin_row' ) );

			//force new remote request for version info on the plugin page
			self::flush_version_info();
		}

		if ( ! self::is_gravityforms_supported() ) {
			return;
		}

		//loading data lib
		require_once( self::get_base_path() . '/data.php' );

		if ( is_admin() ) {
			//loading translations
			load_plugin_textdomain( 'gravityformszapier', false, '/gravityformszapier/languages' );

			add_filter( 'transient_update_plugins', array( 'GFZapier', 'check_update' ) );
			add_filter( 'site_transient_update_plugins', array( 'GFZapier', 'check_update' ) );

			add_action( 'install_plugins_pre_plugin-information', array( 'GFZapier', 'display_changelog' ) );

			//add item to form settings menu in expand list
			add_action( 'gform_form_settings_menu', array( 'GFZapier', 'add_form_settings_menu' ), 50 );

			//add action so that when form is updated, data fields are sent to Zapier
			add_action( 'gform_after_save_form', array( 'GFZapier', 'send_form_updates' ), 10, 2 );

			// paypal standard plugin integration hooks
			if ( self::is_gravityforms_supported( '2.0-beta-2' ) ) {
				add_filter( 'gform_addon_feed_settings_fields', array(
					'GFZapier',
					'add_post_payment_actions',
				), 10, 2 );
			} else {
				add_action( 'gform_paypal_action_fields', array( 'GFZapier', 'add_paypal_settings' ), 10, 2 );
				add_filter( 'gform_paypal_save_config', array( 'GFZapier', 'save_paypal_settings' ) );
			}

			if ( GFForms::get( 'page' ) == 'gf_settings' ) {
				//add Zapier link to settings tabs on GF Main Settings page
				if ( self::has_access( 'gravityforms_zapier' ) ) {
					GFForms::add_settings_page( array(
						'name'      => self::$slug,
						'title'     => 'Zapier Settings',
						'tab_label' => 'Zapier',
						'handler'   => array(
							'GFZapier',
							'settings_page',
						),
						'',
					), self::get_base_url() . '/images/zapier_wordpress_icon_32.png' );
				}
			}

			if ( RGForms::get( 'subview' ) == 'gravityformszapier' ) {
				//add page Zapier link will go to
				add_action( 'gform_form_settings_page_gravityformszapier', array( 'GFZapier', 'zapier_page' ) );

				//loading upgrade lib
				if ( ! class_exists( 'GFZapierUpgrade' ) ) {
					require_once( 'plugin-upgrade.php' );
				}

				//loading Gravity Forms tooltips
				require_once( GFCommon::get_base_path() . '/tooltips.php' );
				add_filter( 'gform_tooltips', array( 'GFZapier', 'tooltips' ) );

			}

			// Gravity Forms 2.2+ System Status
			add_action( 'gform_post_upgrade', array( 'GFZapierData', 'post_gravityforms_upgrade' ), 10, 3 );
			add_filter( 'gform_system_report', array( 'GFZapier', 'system_report' ) );

			//runs the setup when version changes
			self::setup();

		} else {
			// ManageWP premium update filters
			add_filter( 'mwp_premium_update_notification', array( 'GFZapier', 'premium_update_push' ) );
			add_filter( 'mwp_premium_perform_update', array( 'GFZapier', 'premium_update' ) );
		}

		//integrating with Members plugin
		if ( function_exists( 'members_get_capabilities' ) ) {
			add_filter( 'members_get_capabilities', array( 'GFZapier', 'members_get_capabilities' ) );
		}

		add_action( 'gform_after_submission', array( 'GFZapier', 'send_form_data_to_zapier' ), 10, 2 );

		// handling paypal fulfillment.
		add_action( 'gform_paypal_fulfillment', array( 'GFZapier', 'paypal_fulfillment' ), 10, 4 );
		// handling fulfillment in other payment addons.
		add_action( 'gform_trigger_payment_delayed_feeds', array( 'GFZapier', 'action_trigger_payment_delayed_feeds' ), 10, 4 );
	}

	public static function add_form_settings_menu( $tabs ) {
		$tabs[] = array(
			'name'  => self::$slug,
			'label' => __( 'Zapier', 'gravityforms' ),
			'query' => array( 'zid' => null ),
		);

		return $tabs;
	}

	public static function zapier_page() {
		//see if there is a form id in the querystring
		$form_id = RGForms::get( 'id' );

		$zapier_id = rgempty( 'gform_zap_id' ) ? rgget( 'zid' ) : rgpost( 'gform_zap_id' );

		if ( ! empty( $zapier_id ) ) {
			$zapier_id = absint( $zapier_id );
		}


		if ( ! rgblank( $zapier_id ) ) {
			self::zapier_edit_page( $form_id, $zapier_id );
		} else {
			self::zapier_list_page( $form_id );
		}

		GFFormSettings::page_footer();

	}

	private static function zapier_list_page( $form_id ) {
		if ( rgpost( 'action' ) == 'delete' && check_admin_referer( 'gform_zapier_list_action', 'gform_zapier_list_action' ) ) {
			$zid = $_POST['action_argument'];
			if ( ! empty( $zid ) ) {
				GFZapierData::delete_feed( $zid );
				GFCommon::add_message( __( 'Zap deleted.', 'gravityformszapier' ) );
			}
		}

		GFFormSettings::page_header( __( 'Zapier', 'gravityformszapier' ) );

		?>
		<script type='text/javascript'>
			function DeleteZap(zid) {
				//set hidden fields
				jQuery('#action').val('delete');
				jQuery('#action_argument').val(zid);
				jQuery('#zapier_list_form')[0].submit();
			}
		</script>
		<style type='text/css'>
			a.limit-text {
				display: block;
				height: 18px;
				line-height: 18px;
				overflow: hidden;
				padding-right: 5px;
				color: #555;
				text-overflow: ellipsis;
				white-space: nowrap;
			}

			a.limit-text:hover {
				color: #555;
			}

			th.column-name {
				width: 30%;
			}

			th.column-type {
				width: 20%;
			}
		</style>

		<?php
		$add_new_url = add_query_arg( array( 'zid' => 0 ) );
		?>
		<h3>
			<span>
				<?php esc_html_e( 'Zapier Feeds', 'gravityforms' ) ?>
				<a id="add-new-zapier" class="add-new-h2"
				   href="<?php echo esc_url( $add_new_url ); ?>"><?php esc_html_e( 'Add New', 'gravityformszapier' ) ?></a>
			</span>
		</h3>

		<?php
		$zapier_table = new GFZapierTable( $form_id );
		$zapier_table->prepare_items();
		?>

		<form id="zapier_list_form" method="post">

			<?php $zapier_table->display(); ?>

			<input type="hidden" id="action" name="action" value="">
			<input id="action_argument" name="action_argument" type="hidden"/>

			<?php wp_nonce_field( 'gform_zapier_list_action', 'gform_zapier_list_action' ) ?>

		</form>
		<?php

	}

	private static function zapier_edit_page( $form_id, $zap_id ) {

		$zap = empty( $zap_id ) ? array() : GFZapierData::get_feed( $zap_id );

		$is_new_zap = empty( $zap_id ) || empty( $zap );

		$is_valid  = true;
		$is_update = false;

		$form = RGFormsModel::get_form_meta( $form_id );

		if ( rgpost( 'save' ) ) {

			check_admin_referer( 'gforms_save_zap', 'gforms_save_zap' );

			if ( rgar( $zap, 'url' ) != rgpost( 'gform_zapier_url' ) ) {
				$is_update = true;
			}

			$zap['name']      = sanitize_text_field( rgpost( 'gform_zapier_name' ) );
			$zap['url']       = esc_url_raw( rgpost( 'gform_zapier_url' ) );
			$zap['is_active'] = rgpost( 'gform_zapier_active' ) ? '1' : '0';

			//conditional
			$zap['meta']['zapier_conditional_enabled']  = rgpost( 'gf_zapier_conditional_enabled' ) ? '1' : '0';
			$zap['meta']['zapier_conditional_field_id'] = absint( rgpost( 'gf_zapier_conditional_field_id' ) );

			$posted_logic_operator = rgpost( 'gf_zapier_conditional_operator' );
			if ( ! in_array( $posted_logic_operator, array( 'is', 'isnot', '>', '<', 'contains', 'starts_with', 'ends_with' ) ) ) {
				$posted_logic_operator = 'is';
			}
			$zap['meta']['zapier_conditional_operator'] = $posted_logic_operator;
			$zap['meta']['zapier_conditional_value']    = wp_strip_all_tags( rgpost( 'gf_zapier_conditional_value' ) );
			$zap['meta']['adminLabels'] = rgpost( 'gform_zapier_admin_labels' ) ? '1' : '0';

			if ( empty( $zap['url'] ) || empty( $zap['name'] ) ) {
				$is_valid = false;
			}

			if ( $is_valid ) {
				$zap = apply_filters( 'gform_zap_before_save', apply_filters( "gform_zap_before_save_{$form['id']}", $zap, $form ), $form );

				$zap_id = GFZapierData::update_feed( $zap_id, $form_id, $zap['is_active'], $zap['name'], $zap['url'], $zap['meta'] );

				GFCommon::add_message( sprintf( __( 'Zap saved successfully. %sBack to list.%s', 'gravityformszapier' ), '<a href="' . esc_url( remove_query_arg( 'zid' ) ) . '">', '</a>' ) );

				if ( $is_new_zap || $is_update ) {
					//send field info to zap when new or url has changed
					$sent       = self::send_form_data_to_zapier( '', $form );
					$is_new_zap = false;
				}
			} else {
				GFCommon::add_error_message( __( 'Zap could not be updated. Please enter all required information below.', 'gravityformszapier' ) );
			}
		}

		GFFormSettings::page_header( __( 'Zapier', 'gravityformszapier' ) );

		$feed_active    = $is_new_zap ? 1 : rgar( $zap, 'is_active' );
		$admin_labels   = $is_new_zap ? 0 : rgars( $zap, 'meta/adminLabels' );
		$logic_operator = rgars( $zap, 'meta/zapier_conditional_operator' );

		?>
		<style type="text/css">
			a.limit-text {
				display: block;
				height: 18px;
				line-height: 18px;
				overflow: hidden;
				padding-right: 5px;
				color: #555;
				text-overflow: ellipsis;
				white-space: nowrap;
			}

			a.limit-text:hover {
				color: #555;
			}

			th.column-name {
				width: 30%;
			}

			th.column-type {
				width: 20%;
			}
		</style>
		<div style="<?php echo $is_new_zap ? 'display:block' : 'display:none' ?>">
			<?php echo sprintf( __( 'To create a new zap, you must have the Webhook URL. The Webhook URL may be found when you go to your %sZapier dashboard%s and create a new zap, or when you edit an existing zap. Once you have saved your new feed the form fields will be available for mapping on the Zapier site.', 'gravityformszapier' ), "<a href='https://zapier.com/app/dashboard' target='_blank'>", '</a>' ); ?>
		</div>
		<form method="post" id="gform_zapier_form">
			<?php wp_nonce_field( 'gforms_save_zap', 'gforms_save_zap' ) ?>
			<input type="hidden" id="gform_zap_id" name="gform_zap_id" value="<?php echo absint( $zap_id ) ?>"/>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="gform_zapier_name">
							<?php esc_html_e( 'Zap Name', 'gravityformszapier' ); ?><span class="gfield_required">*</span>
							<?php gform_tooltip( 'zapier_name' ) ?>
						</label>
					</th>
					<td>
						<input type="text" class="fieldwidth-2" name="gform_zapier_name" id="gform_zapier_name"
						       value="<?php echo esc_attr( rgar( $zap, 'name' ) ) ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="gform_zapier_url">
							<?php esc_html_e( 'Webhook URL', 'gravityformszapier' ); ?><span class="gfield_required">*</span>
							<?php gform_tooltip( 'zapier_url' ) ?>
						</label>
					</th>
					<td>
						<input type="text" class="fieldwidth-2" name="gform_zapier_url" id="gform_zapier_url"
						       value="<?php echo esc_url( rgar( $zap, 'url' ) ) ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="gform_zapier_active">
							<?php esc_html_e( 'Active', 'gravityformszapier' ); ?>
							<?php gform_tooltip( 'zapier_active' ) ?>
						</label>
					</th>
					<td>
						<input type="radio" id="form_active_yes"
						       name="gform_zapier_active" <?php checked( $feed_active, 1 ); ?> value="1"/>
						<label for="form_active_yes" class="inline"><?php esc_html_e( 'Yes', 'gravityformszapier' ) ?></label>
						<input type="radio" id="form_active_no"
						       name="gform_zapier_active" <?php checked( $feed_active, 0 ); ?> value="0"/>
						<label for="form_active_no" class="inline"><?php esc_html_e( 'No', 'gravityformszapier' ) ?></label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="gform_zapier_admin_labels">
							<?php esc_html_e( 'Use Admin Labels', 'gravityformszapier' ); ?>
							<?php gform_tooltip( 'zapier_labels' ) ?>
						</label>
					</th>
					<td>
						<input type="radio" id="admin_labels_yes"
						       name="gform_zapier_admin_labels" <?php checked( $admin_labels, 1 ); ?> value="1"/>
						<label for="admin_labels_yes" class="inline"><?php esc_html_e( 'Yes', 'gravityformszapier' ) ?></label>
						<input type="radio" id="admin_labels_no"
						       name="gform_zapier_admin_labels" <?php checked( $admin_labels, 0 ); ?> value="0"/>
						<label for="admin_labels_no" class="inline"><?php esc_html_e( 'No', 'gravityformszapier' ) ?></label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="gform_zapier_conditional_logic">
							<?php esc_html_e( 'Conditional Logic', 'gravityforms' ) ?>
							<?php gform_tooltip( 'zapier_conditional' ) ?>
						</label>
					</th>
					<td>
						<input type="checkbox" id="gf_zapier_conditional_enabled" name="gf_zapier_conditional_enabled"
						       value="1"
						       onclick="if(this.checked){jQuery('#gf_zapier_conditional_container').fadeIn('fast');} else{ jQuery('#gf_zapier_conditional_container').fadeOut('fast'); }" <?php checked( rgars( $zap, 'meta/zapier_conditional_enabled' ), 1 ); ?>/>
						<label for="gf_zapier_conditional_enable"><?php esc_html_e( 'Enable', 'gravityformszapier' ); ?></label>
						<br/>
						<div style="height:20px;">
							<div
								id="gf_zapier_conditional_container" <?php echo ! rgars( $zap, 'meta/zapier_conditional_enabled' ) ? "style='display:none'" : '' ?>>
								<div id="gf_zapier_conditional_fields" style="display:none;">
									<?php esc_html_e( 'Send to Zapier if ', 'gravityformszapier' ) ?>

									<select id="gf_zapier_conditional_field_id" name="gf_zapier_conditional_field_id"
									        class="optin_select"
									        onchange='jQuery("#gf_zapier_conditional_value_container").html(GetFieldValues(jQuery(this).val(), "", 20));'></select>
									<select id="gf_zapier_conditional_operator" name="gf_zapier_conditional_operator">
										<option
											value="is" <?php selected( $logic_operator, 'is' ); ?>><?php esc_html_e( 'is', 'gravityformszapier' ) ?></option>
										<option
											value="isnot" <?php selected( $logic_operator, 'isnot' ); ?>><?php esc_html_e( 'is not', 'gravityformszapier' ) ?></option>
										<option
											value=">" <?php selected( $logic_operator, '>' ); ?>><?php esc_html_e( 'greater than', 'gravityformszapier' ) ?></option>
										<option
											value="<" <?php selected( $logic_operator, '<' ); ?>><?php esc_html_e( 'less than', 'gravityformszapier' ) ?></option>
										<option
											value="contains" <?php selected( $logic_operator, 'contains' ); ?>><?php esc_html_e( 'contains', 'gravityformszapier' ) ?></option>
										<option
											value="starts_with" <?php selected( $logic_operator, 'starts_with' ); ?>><?php esc_html_e( 'starts with', 'gravityformszapier' ) ?></option>
										<option
											value="ends_with" <?php selected( $logic_operator, 'ends_with' ); ?>><?php esc_html_e( 'ends with', 'gravityformszapier' ) ?></option>
									</select>
									<div id="gf_zapier_conditional_value_container"
									     name="gf_zapier_conditional_value_container" style="display:inline;"></div>
								</div>
								<div id="gf_zapier_conditional_message" style="display:none">
									<?php esc_html_e( 'To create a condition, your form must have a field supported by conditional logic.', 'gravityformzapier' ) ?>
								</div>
							</div>
						</div>
					</td>
				</tr> <!-- / conditional logic -->
			</table>

			<p class="submit">
				<?php
				$button_label  = $is_new_zap ? __( 'Save Zapier Feed', 'gravityformszapier' ) : __( 'Update Zapier Feed', 'gravityformszapier' );
				$zapier_button = '<input class="button-primary" type="submit" value="' . $button_label . '" name="save"/>';
				/**
				 * A filter allowing for the modification of the save button for saving a Zapier Feed (A conditional).
				 *
				 * @param string $zapier_button The HTML rendered for the save button.
				 */
				echo apply_filters( 'gform_save_zapier_button', $zapier_button );
				?>
			</p>
		</form>
		<script type="text/javascript">
			// Conditional Functions

			// initialize form object
			form = <?php echo GFCommon::json_encode( $form )?> ;

			// initializing registration condition drop downs
			jQuery(document).ready(function () {
				var selectedField = <?php echo json_encode( absint( rgars( $zap, 'meta/zapier_conditional_field_id' ) ) ); ?>;
				var selectedValue = <?php echo json_encode( rgars( $zap, 'meta/zapier_conditional_value' ) )?>;
				SetCondition(selectedField, selectedValue);
			});

			function SetCondition(selectedField, selectedValue) {

				// load form fields
				jQuery('#gf_zapier_conditional_field_id').html(GetSelectableFields(selectedField, 20));
				var optinConditionField = jQuery('#gf_zapier_conditional_field_id').val();
				var checked = jQuery('#gf_zapier_conditional_enabled').attr('checked');

				if (optinConditionField) {
					jQuery('#gf_zapier_conditional_message').hide();
					jQuery('#gf_zapier_conditional_fields').show();
					jQuery('#gf_zapier_conditional_value_container').html(GetFieldValues(optinConditionField, selectedValue, 20));
					jQuery('#gf_zapier_conditional_value').val(selectedValue);
				}
				else {
					jQuery('#gf_zapier_conditional_message').show();
					jQuery('#gf_zapier_conditional_fields').hide();
				}

				if (!checked) jQuery('#gf_zapier_conditional_container').hide();

			}

			function GetFieldValues(fieldId, selectedValue, labelMaxCharacters) {
				if (!fieldId)
					return '';

				var str = '';
				var field = GetFieldById(fieldId);
				if (!field)
					return '';

				var isAnySelected = false;

				if (field['type'] == 'post_category' && field['displayAllCategories']) {
					str += '<?php $dd = wp_dropdown_categories( array(
						'class'        => 'optin_select',
						'orderby'      => 'name',
						'id'           => 'gf_zapier_conditional_value',
						'name'         => 'gf_zapier_conditional_value',
						'hierarchical' => true,
						'hide_empty'   => 0,
						'echo'         => false
					) ); echo str_replace( "\n", '', str_replace( "'", "\\'", $dd ) ); ?>';
				}
				else if (field.choices) {
					str += GetRuleValuesDropDown(field.choices, 'gf_zapier', 0, selectedValue, 'gf_zapier_conditional_value');
				}
				else {
					selectedValue = selectedValue ? selectedValue.replace(/'/g, "&#039;") : "";
					//create a text field for fields that don't have choices (i.e text, textarea, number, email, etc...)
					str += "<input type='text' placeholder='<?php esc_attr_e( 'Enter value', 'gravityforms' ); ?>' id='gf_zapier_conditional_value' name='gf_zapier_conditional_value' value='" + selectedValue.replace(/'/g, "&#039;") + "'>";
				}

				return str;
			}

			function GetFieldById(fieldId) {
				for (var i = 0; i < form.fields.length; i++) {
					if (form.fields[i].id == fieldId)
						return form.fields[i];
				}
				return null;
			}

			function TruncateMiddle(text, maxCharacters) {
				if (!text)
					return '';

				if (text.length <= maxCharacters)
					return text;
				var middle = parseInt(maxCharacters / 2);
				return text.substr(0, middle) + '...' + text.substr(text.length - middle, middle);
			}

			function GetSelectableFields(selectedFieldId, labelMaxCharacters) {
				var str = '', fieldLabel;
				var inputType;
				for (var i = 0; i < form.fields.length; i++) {
					fieldLabel = form.fields[i].adminLabel ? form.fields[i].adminLabel : form.fields[i].label;
					inputType = form.fields[i].inputType ? form.fields[i].inputType : form.fields[i].type;
					if (IsConditionalLogicField(form.fields[i])) {
						var selected = form.fields[i].id == selectedFieldId ? "selected='selected'" : '';
						str += "<option value='" + form.fields[i].id + "' " + selected + '>' + TruncateMiddle(fieldLabel, labelMaxCharacters) + '</option>';
					}
				}
				return str;
			}

			function IsConditionalLogicField(field) {
				var inputType = field.inputType ? field.inputType : field.type;
				var supported_fields = ['checkbox', 'radio', 'select', 'text', 'website', 'textarea', 'email', 'hidden', 'number', 'phone', 'multiselect', 'post_title', 'post_tags', 'post_custom_field', 'post_content', 'post_excerpt'];

				var index = jQuery.inArray(inputType, supported_fields);

				return index >= 0;
			}
		</script>

		<?php
	}

	public static function send_form_updates( $form, $is_new ) {
		self::send_form_data_to_zapier( '', $form );
	}

	public static function send_form_data_to_zapier( $entry = null, $form ) {
		//if there is an entry, then this is a form submission, get data out of entry to POST to Zapier
		//otherwise this is a dummy setup to give Zapier the field data, get the form fields and POST to Zapier with empty data
		if ( empty( $form ) && empty( $entry ) ) {
			self::log_debug( 'No form or entry was provided to send data to Zapier.' );

			return false;
		}

		//get zaps for form
		$form_id = $form['id'];
		$zaps    = GFZapierData::get_feed_by_form( $form_id, true );
		if ( empty( $zaps ) ) {
			self::log_debug( "There are no zaps configured for form id {$form_id}" );

			return false;
		}

		$is_entry   = ! empty( $entry );
		$is_delayed = $is_entry && self::maybe_delay_feed( $entry, $form );

		if ( $is_delayed ) {
			self::log_debug( 'Zapier Feed processing is delayed, not processing feed for entry #' . $entry['id'] );

			return false;
		}

		//do not send spam entries to zapier
		if ( $is_entry && $entry['status'] == 'spam' ) {
			self::log_debug( 'The entry is marked as spam, NOT sending to Zapier.' );

			return false;
		}

		$is_entry ? self::log_debug( 'Gathering entry data to send submission.' ) : self::log_debug( 'Gathering field data to send dummy submission.' );

		$retval = true;
		foreach ( $zaps as $zap ) {
			//checking to see if a condition was specified, and if so, met, otherwise don't send to zapier
			//only check this when there is an entry, simple form updates should go to zapier regardless of conditions existing
			if ( ! $is_entry || ( $is_entry && self::conditions_met( $form, $zap, $entry ) ) ) {
				if ( $is_entry ) {
					self::log_debug( 'No condition specified or a condition was specified and met, sending to Zapier' );
				}

				$retval = self::process_feed( $zap, $entry, $form );

			} else {
				self::log_debug( 'A condition was specified and not met, not sending to Zapier' );
				$retval = false;
			}
		}

		return $retval;
	}

	/**
	 * Determines if feed processing is delayed by the PayPal Standard Add-On.
	 *
	 * Also enables use of the gform_is_delayed_pre_process_feed filter.
	 *
	 * @param array $entry The Entry Object currently being processed.
	 * @param array $form The Form Object currently being processed.
	 *
	 * @since 2.0.2
	 * @since 3.1.5 Updated to support all payment addons.
	 *
	 * @return bool
	 */
	public static function maybe_delay_feed( $entry, $form ) {
		if ( self::$_bypass_feed_delay ) {
			return false;
		}

		$is_delayed = false;
		$slug       = self::$slug;

		// Backwards compatibility for GF core < 2.4.13.
		if ( ! method_exists( 'GFPaymentAddOn', 'maybe_delay_feed_processing' ) ) {
			// See if there is a paypal feed and zapier is set to be delayed until payment is received.
			if ( class_exists( 'GFPayPal' ) ) {
				$paypal_feeds = self::get_paypal_feeds( $form['id'] );
				// Loop through paypal feeds to get active one for this form submission, needed to see if add-on processing should be delayed.
				foreach ( $paypal_feeds as $paypal_feed ) {
					if ( $paypal_feed['is_active'] && self::is_feed_condition_met( $paypal_feed, $form, $entry ) ) {
						$active_paypal_feed = $paypal_feed;
						break;
					}
				}
				$is_fulfilled = rgar( $entry, 'is_fulfilled' );
				if ( ! empty( $active_paypal_feed ) && self::is_delayed( $active_paypal_feed ) && self::has_paypal_payment( $active_paypal_feed, $form, $entry ) && ! $is_fulfilled ) {
					$is_delayed = true;
				}
			}
		}

		/**
		 * Allow feed processing to be delayed.
		 *
		 * @param bool $is_delayed Is feed processing delayed?
		 * @param array $form The Form Object currently being processed.
		 * @param array $entry The Entry Object currently being processed.
		 * @param string $slug The Add-On slug e.g. gravityformszapier
		 */
		$is_delayed = apply_filters( 'gform_is_delayed_pre_process_feed', $is_delayed, $form, $entry, $slug );
		$is_delayed = apply_filters( 'gform_is_delayed_pre_process_feed_' . $form['id'], $is_delayed, $form, $entry, $slug );

		return $is_delayed;
	}

	public static function process_feed( $feed, $entry, $form ) {
		$body = self::get_body( $entry, $form, $feed );

		/**
		 * Allows the request body sent to zapier to be filtered
		 *
		 * @param array $body An associative array containing the request body that will be sent to Zapier.
		 * @param array $feed The Feed Object currently being processed.
		 * @param array $entry The Entry Object currently being processed.
		 * @param array $form The Form Object currently being processed.
		 *
		 * @since 3.1.1
		 */
		$body = gf_apply_filters( array( 'gform_zapier_request_body', $form['id'] ), $body, $feed, $entry, $form );

		$headers = array();
		if ( empty( $entry ) ) {
			$headers['X-Hook-Test'] = 'true';
		}

		$json_body = json_encode( $body );
		if ( empty( $body ) ) {
			self::log_debug( 'There is no field data to send to Zapier.' );

			return false;
		}

		self::log_debug( 'Posting to url: ' . $feed['url'] . ' data: ' . print_r( $body, true ) );

		$form_data = array( 'sslverify' => false, 'ssl' => true, 'body' => $json_body, 'headers' => $headers );
		$response  = wp_remote_post( $feed['url'], $form_data );

		if ( is_wp_error( $response ) ) {
			self::log_error( 'The following error occurred: ' . print_r( $response, true ) );

			return false;
		} else {
			self::log_debug( 'Successful response from Zap: ' . print_r( $response, true ) );

			if ( ! empty( $entry ) ) {
				self::log_debug( 'Marking entry #' . $entry['id'] . ' as fulfilled.' );
				gform_update_meta( $entry['id'], self::$slug . '_is_fulfilled', true );
			}

			return true;
		}
	}

	public static function get_body( $entry, $form, $feed = false ) {

		/**
		 * Determines if the Zapier add-on should use the body already stored.
		 *
		 * @since 2.1.1
		 *
		 * @param bool  true   If the current body should be used. Defaults to true.
		 * @param array $entry The Entry Object.
		 * @param array $form  The Form Object.
		 * @param array $feed  The Feed Object.
		 */
		if ( apply_filters( 'gform_zapier_use_stored_body', true, $entry, $form, $feed ) ) {
			$current_body = self::$_current_body;

			if ( is_array( $current_body ) ) {
				return $current_body;
			}
		}

		$adminLabels      = is_array( $feed ) ? rgars( $feed, 'meta/adminLabels' ) : false;
		$use_sample_value = empty( $entry );
		$body             = array();

		$body[ esc_html__( 'Form ID', 'gravityformszapier' ) ]    = rgar( $form, 'id' );
		$body[ esc_html__( 'Form Title', 'gravityformszapier' ) ] = rgar( $form, 'title' );

		$entry_properties = self::get_entry_properties();
		foreach ( $entry_properties as $property_key => $property_config ) {
			$key = self::get_body_key( $body, $property_config['label'] );

			if ( $use_sample_value ) {
				$value = $property_config['sample_value'];
			} else {
				$value = rgar( $entry, $property_key );
			}

			$body[ $key ] = $value;
		}

		$entry_meta = GFFormsModel::get_entry_meta( $form['id'] );
		foreach ( $entry_meta as $meta_key => $meta_config ) {
			$key = self::get_body_key( $body, $meta_config['label'] );

			if ( $use_sample_value ) {
				$body[ $key ] = rgar( $meta_config, 'is_numeric' ) ? rand( 0, 10 ) : 'Sample value';
			} else {
				$body[ $key ] = rgar( $entry, $meta_key );
			}
		}

		foreach ( $form['fields'] as $field ) {
			$input_type = GFFormsModel::get_input_type( $field );
			if ( $input_type == 'honeypot' || $field->displayOnly ) {
				//skip the honeypot and displayOnly fields
				continue;
			}

			if ( ! $use_sample_value ) {
				$field_value = GFFormsModel::get_lead_field_value( $entry, $field );
				$field_value = apply_filters( 'gform_zapier_field_value', $field_value, $form['id'], $field->id, $entry );
			} else {
				$field_value = self::get_sample_value( $field );
				$field_value = apply_filters( 'gform_zapier_sample_field_value', $field_value, $form['id'], $field->id );
			}

			$field_label = self::get_body_label( $adminLabels, $field );

			$inputs = $field instanceof GF_Field ? $field->get_entry_inputs() : rgar( $field, 'inputs' );

			if ( is_array( $inputs ) && ( is_array( $field_value ) || $use_sample_value ) ) {
				//handling multi-input fields

				$non_blank_items = array();

				//field has inputs, complex field like name, address and checkboxes. Get individual inputs
				foreach ( $inputs as $input ) {
					$input_label = self::get_body_label( $adminLabels, $field, $input['id'] );
					$key         = self::get_body_key( $body, $input_label );

					$field_id     = (string) $input['id'];
					$input_value  = rgar( $field_value, $field_id );
					$body[ $key ] = $input_value;

					if ( ! rgblank( $input_value ) ) {
						$non_blank_items[] = $input_value;
					}
				}

				//Also adding an item for the "whole" field, which will be a concatenation of the individual inputs
				switch ( $input_type ) {
					case 'checkbox' :
						//checkboxes will create a comma separated list of values
						$key          = self::get_body_key( $body, $field_label );
						$body[ $key ] = implode( ', ', $non_blank_items );
						break;

					case 'name' :
					case 'address' :
						//name and address will separate inputs by a single blank space
						$key          = self::get_body_key( $body, $field_label );
						$body[ $key ] = implode( ' ', $non_blank_items );
						break;

					case 'calculation':
					case 'hiddenproduct':
					case 'singleproduct':
						if ( $use_sample_value ) {
							$name     = rgar( $field_value, $field->id . '.1' );
							$price    = rgar( $field_value, $field->id . '.2' );
							$quantity = rgar( $field_value, $field->id . '.3' );

							$body['Products /'][] = array(
								'product_id'                 => $field->id,
								'product_name'               => $name,
								'product_quantity'           => $quantity,
								'product_price'              => $price,
								'product_price_with_options' => $price + 10 + 20,
								'product_subtotal'           => ( $price + 10 + 20 ) * $quantity,
								'product_options'            => 'Option 1, Option 2'
							);
						} else {
							// We get all product fields at once, so skipped if products has been set
							if ( isset( $body['Products /'] ) ) {
								break;
							}

							$body['Products /'] = self::get_products_array( $form, $entry );
						}
						break;
				}
			} else {
				$key = self::get_body_key( $body, $field_label );

				switch ( $input_type ) {
					case 'list' :

						if ( $field->enableColumns ) {

							// Keep for backwards compatibility
							$body[ $key ] = $field_value;

							// Add line-item support to list
							$body[ $key . ' /' ] = maybe_unserialize( $field_value );

						} else {

							$body[ $key ] = maybe_unserialize( $field_value );

						}

						break;

					default :
						if ( $field->type == 'product' ) {
							// Keep for backwards compatibility
							$body[ $key ] = $field_value;

							if ( $use_sample_value ) {
								list( $name, $price ) = explode( '|', $field_value );
								$quantity = rand( 1, 10 );

								$body['Products /'][] = array(
									'product_id'                 => $field->id,
									'product_name'               => $name,
									'product_quantity'           => $quantity,
									'product_price'              => $price,
									'product_price_with_options' => $price + 10 + 20,
									'product_subtotal'           => ( $price + 10 + 20 ) * $quantity,
									'product_options'            => 'Option 1, Option 2'
								);
							} else {
								// We get all product fields at once, so skipped if products has been set
								if ( isset( $body['Products /'] ) ) {
									break;
								}

								$body['Products /'] = self::get_products_array( $form, $entry );
							}
						} elseif ( $field->type == 'shipping' ) {
							// Keep old shipping value for backward compatibility.
							$body[ $key ] = rgblank( $field_value ) ? '' : $field_value;

							// Set shipping as a faux product
							if ( $use_sample_value ) {
								if ( $field->get_input_type() !== 'singleshipping' ) {
									list( $name, $price ) = explode( '|', $field_value );
									$name = 'Shipping (' . $name . ')';
								} else {
									$name  = 'Shipping';
									$price = $field_value;
								}
								$body['Products /'][] = array(
									'product_id'                 => $field->id,
									'product_name'               => $name,
									'product_quantity'           => 1,
									'product_price'              => $price,
									'product_price_with_options' => $price,
									'product_subtotal'           => $price,
									'product_options'            => '',
								);
							}
						} else {
							$body[ $key ] = rgblank( $field_value ) ? '' : $field_value;
						}
				}
			}
		}

		self::$_current_body = $body;

		return $body;
	}

	/**
	 * Retrieve a sample value for the current field.
	 *
	 * @param GF_Field $field The field properties.
	 *
	 * @return array|string
	 */
	public static function get_sample_value( $field ) {

		$default_value = 'Sample value';
		$always_text   = array( 'survey', 'quiz', 'poll' );
		$field_id      = absint( $field->id );
		$choice_type   = in_array( $field->type, $always_text ) || ! $field->enableChoiceValue ? 'text' : 'value';

		switch ( $field->get_input_type() ) {
			case 'address' :
				$value[ $field_id . '.1' ] = 'Bag End';
				$value[ $field_id . '.2' ] = 'Bagshot Row';
				$value[ $field_id . '.3' ] = 'Hobbiton';
				$value[ $field_id . '.4' ] = 'Shire';
				$value[ $field_id . '.5' ] = '1234';
				$value[ $field_id . '.6' ] = 'Middle Earth';
				break;

			case 'name' :
				$value[ $field_id . '.2' ] = 'Mr.';
				$value[ $field_id . '.3' ] = 'Bilbo';
				$value[ $field_id . '.4' ] = 'L.';
				$value[ $field_id . '.6' ] = 'Baggins';
				$value[ $field_id . '.8' ] = 'Ring-bearer';

				$inputs = $field->get_entry_inputs();
				if ( ! is_array( $inputs ) ) {
					$value = implode( ' ', $value );
				}

				break;

			case 'calculation' :
				$value[ $field_id . '.1' ] = $field->label;
				$value[ $field_id . '.2' ] = 10;
				$value[ $field_id . '.3' ] = 2;
				break;

			case 'checkbox' :
				$value = array();
				if ( is_array( $field->choices ) ) {
					$choice_number = 1;
					foreach ( $field->choices as $choice ) {
						if ( $choice_number % 10 == 0 ) {
							$choice_number ++;
						}

						$choice_value = rgar( $choice, $choice_type );
						if ( $field->enablePrice ) {
							$price = rgempty( 'price', $choice ) ? 0 : GFCommon::to_number( rgar( $choice, 'price' ) );
							$choice_value .= '|' . $price;
						}

						$input_id           = $field_id . '.' . $choice_number ++;
						$value[ $input_id ] = $choice_value;
					}
				}
				break;

			case 'creditcard' :
				$value[ $field_id . '.1' ] = str_repeat( 'X', 16 );
				$value[ $field_id . '.4' ] = 'Visa';
				break;

			case 'date' :
				$value = date( 'Y-m-d' );
				break;

			case 'email' :
				$value = 'test@domain.dev';
				break;

			case 'fileupload' :
			case 'signature' :
				$value = 'http://domain.dev/some_location/file.png';
				break;

			case 'list' :
				if ( ! $field->enableColumns ) {
					$max = 2;
				} else {
					$max = count( $field->choices ) * 2;
				}

				$value = array_fill( 0, $max, $default_value );
				$value = serialize( $field->create_list_array( $value ) );
				break;

			case 'multiselect' :
				$value = rgars( $field->choices, '0/' . $choice_type );
				if ( isset( $field->choices[1] ) ) {
					$value .= ',' . rgar( $field->choices[1], $choice_type );
				}
				break;

			case 'number' :
			case 'total' :
				$value = 100;
				break;

			case 'price' :
				$value = $field->label . '|10';
				break;

			case 'phone' :
				$value = '(999) 999-9999';
				break;

			case 'post_image' :
				$title       = $field->displayTitle ? 'The title' : '';
				$caption     = $field->displayCaption ? 'The caption' : '';
				$description = $field->displayDescription ? 'The description' : '';
				$value       = 'http://domain.dev/some_location/image.img|:|' . $title . '|:|' . $caption . '|:|' . $description;
				break;

			case 'hiddenproduct' :
			case 'singleproduct' :
				$value[ $field_id . '.1' ] = $field->label;
				$value[ $field_id . '.2' ] = empty( $field->basePrice ) ? 10 : GFCommon::to_number( $field->basePrice );
				$value[ $field_id . '.3' ] = 2;
				break;

			case 'singleshipping' :
				$value = empty( $field->basePrice ) ? 10 : GFCommon::to_number( $field->basePrice );
				break;

			case 'time' :
				$value = '10:30 am';
				break;

			case 'website' :
				$value = 'http://domain.dev';
				break;

			case 'likert' :
				if ( $field->gsurveyLikertEnableMultipleRows ) {
					$value = array();
					foreach ( $field->inputs as $input ) {
						$value[ $input['id'] ] = self::get_random_choice( $field->choices, $choice_type );
					}
				} else {
					$value = self::get_random_choice( $field->choices, $choice_type );
				}
				break;

			case 'rank' :
				$c       = 1;
				$value   = array();
				$choices = $field->choices;
				shuffle( $choices );
				foreach ( $choices as $choice ) {
					$value[] = $c ++ . '. ' . rgar( $choice, $choice_type );
				}
				$value = implode( ', ', $value );
				break;

			default :
				$inputs = $field->get_entry_inputs();

				if ( $inputs ) {
					$value = array();
					foreach ( $inputs as $input ) {
						$choices = rgar( $input, 'choices' );
						if ( is_array( $choices ) ) {
							$value[ $input['id'] ] = self::get_random_choice( $choices, $choice_type );
						} else {
							$value[ $input['id'] ] = $default_value;
						}
					}
				} elseif ( is_array( $field->choices ) && count( $field->choices ) > 0 ) {
					$value = self::get_random_choice( $field->choices, $choice_type, $field->enablePrice );
				} else {
					$value = $default_value;
				}
		}

		return $value;
	}

	/**
	 * Return a random choice.
	 *
	 * @param array $choices The choices.
	 * @param string $choice_type The choice property to return; text or value.
	 * @param bool $price_enabled Is the enablePrice property enabled for the field being processed.
	 *
	 * @return string
	 */
	public static function get_random_choice( $choices, $choice_type, $price_enabled = false ) {
		$key    = array_rand( $choices );
		$choice = $choices[ $key ];
		$value  = rgar( $choice, $choice_type );

		if ( $price_enabled ) {
			$price = rgempty( 'price', $choice ) ? 0 : GFCommon::to_number( rgar( $choice, 'price' ) );
			$value .= '|' . $price;
		}

		return $value;
	}

	/**
	 * Return the product fields in the entry as an array.
	 *
	 * @param array $form The Form Object.
	 * @param array $entry The Entry Object.
	 *
	 * @return array
	 */
	public static function get_products_array( $form, $entry ) {
		$product_info = GFCommon::get_product_fields( $form, $entry );
		$products     = array_values( $product_info['products'] );
		$product_ids  = array_keys( $product_info['products'] );
		foreach ( $products as $key => $product ) {
			$products[ $key ]['product_id']   = $product_ids[ $key ];
			$products[ $key ]['product_name'] = $product['name'];
			unset( $products[ $key ]['name'] );
			$products[ $key ]['product_quantity'] = intval( $product['quantity'] );
			unset( $products[ $key ]['quantity'] );

			// Change price to "product price" to be more clear when displaying in Zapier
			$products[ $key ]['product_price'] = GFCommon::to_number( $product['price'], $entry['currency'] );
			unset( $products[ $key ]['price'] );

			$options = rgar( $product, 'options' );
			// Add unit price
			$products[ $key ]['product_price_with_options'] = GFCommon::to_number( $product['price'], $entry['currency'] );
			if ( is_array( $options ) && ! empty( $options ) ) {
				foreach ( $options as $option ) {
					$products[ $key ]['product_price_with_options'] += GFCommon::to_number( $option['price'], $entry['currency'] );
				}
			}

			// Add subtotal to product array
			$products[ $key ]['product_subtotal'] = $products[ $key ]['product_price_with_options'] * $products[ $key ]['product_quantity'];

			// Turn options into product_options
			unset( $products[ $key ]['options'] );
			$products[ $key ]['product_options'] = ( empty( $options ) ) ? '' : implode( ', ', wp_list_pluck( $options, 'option_name' ) );
		}

		if ( ! empty( $product_info['shipping']['id'] ) ) {
			// Set shipping as a faux product
			$products[] = array(
				'product_id'                 => $product_info['shipping']['id'],
				'product_name'               => $product_info['shipping']['name'],
				'product_quantity'           => 1,
				'product_price'              => $product_info['shipping']['price'],
				'product_price_with_options' => $product_info['shipping']['price'],
				'product_subtotal'           => $product_info['shipping']['price'],
				'product_options'            => ''
			);
		}

		return apply_filters( 'gform_zapier_products', $products, $form, $entry );
	}

	/**
	 * Retrieve label to be sent to Zapier.
	 *
	 * @param bool $adminLabels Should the field adminLabel be used?
	 * @param GF_Field $field The field currently being processed.
	 * @param bool|int $input_id False or the input ID.
	 *
	 * @return string
	 */
	public static function get_body_label( $adminLabels, $field, $input_id = false ) {

		$label = $adminLabels && ! empty( $field->adminLabel ) ? $field->adminLabel : $field->label;

		if ( $input_id ) {
			$input = GFFormsModel::get_input( $field, $input_id );

			if ( ! is_null( $input ) ) {
				if ( $field->get_input_type() == 'checkbox' ) {
					$label = $input['label'];
				} else {
					$label .= ' (' . $input['label'] . ')';
				}
			}

		}

		if ( empty( $label ) ) {
			return $field->get_form_editor_field_title();
		}

		return $label;
	}

	/**
	 * Ensure the label (array key) is unique.
	 *
	 * @param array $body The data to be sent to Zapier.
	 * @param string $label The field or entry meta label.
	 *
	 * @return string
	 */
	public static function get_body_key( $body, $label ) {

		$count = 1;
		$key   = $label;

		while ( array_key_exists( $key, $body ) ) {
			$key = $label . ' - ' . $count;
			$count ++;
		}

		return $key;
	}

	/**
	 * Return the entry properties to be sent to Zapier.
	 *
	 * @return array
	 */
	public static function get_entry_properties() {
		return array(
			'id'             => array(
				'label'        => esc_html__( 'Entry ID', 'gravityforms' ),
				'sample_value' => 0,
			),
			'date_created'   => array(
				'label'        => esc_html__( 'Entry Date', 'gravityforms' ),
				'sample_value' => gmdate( 'Y-m-d H:i:s' ),
			),
			'ip'             => array(
				'label'        => esc_html__( 'User IP', 'gravityforms' ),
				'sample_value' => GFFormsModel::get_ip(),
			),
			'source_url'     => array(
				'label'        => esc_html__( 'Source Url', 'gravityforms' ),
				'sample_value' => RGFormsModel::get_current_page_url(),
			),
			'created_by'     => array(
				'label'        => esc_html__( 'Created By', 'gravityforms' ),
				'sample_value' => 1,
			),
			'transaction_id' => array(
				'label'        => esc_html__( 'Transaction Id', 'gravityforms' ),
				'sample_value' => '1234567890',
			),
			'payment_amount' => array(
				'label'        => esc_html__( 'Payment Amount', 'gravityforms' ),
				'sample_value' => 100,
			),
			'payment_date'   => array(
				'label'        => esc_html__( 'Payment Date', 'gravityforms' ),
				'sample_value' => gmdate( 'Y-m-d H:i:s' ),
			),
			'payment_status' => array(
				'label'        => esc_html__( 'Payment Status', 'gravityforms' ),
				'sample_value' => 'Paid',
			),
			'post_id'        => array(
				'label'        => esc_html__( 'Post Id', 'gravityforms' ),
				'sample_value' => 1,
			),
			'user_agent'     => array(
				'label'        => esc_html__( 'User Agent', 'gravityforms' ),
				'sample_value' => sanitize_text_field( substr( $_SERVER['HTTP_USER_AGENT'], 0, 250 ) ),
			),
		);
	}

	private static function is_gravityforms_supported( $min_gravityforms_version = '' ) {
		if ( class_exists( 'GFCommon' ) ) {
			if ( empty( $min_gravityforms_version ) ) {
				$min_gravityforms_version = self::$min_gravityforms_version;
			}

			$is_correct_version = version_compare( GFCommon::$version, $min_gravityforms_version, '>=' );

			return $is_correct_version;
		} else {
			return false;
		}
	}

	public static function settings_page() {
		if ( ! class_exists( 'GFZapierUpgrade' ) ) {
			require_once( 'plugin-upgrade.php' );
		}

		if ( rgpost( 'uninstall' ) ) {
			check_admin_referer( 'uninstall', 'gf_zapier_uninstall' );
			self::uninstall();

			?>
			<div class="updated fade"
			     style="padding:20px;"><?php echo sprintf( __( 'Gravity Forms Zapier Add-On has been successfully uninstalled. It can be re-activated from the %splugins page%s.', 'gravityformszapier' ), "<a href='plugins.php'>", '</a>' ) ?></div>
			<?php
			return;
		}
		?>
		<style>
			.valid_credentials {
				color: green;
			}

			.invalid_credentials {
				color: red;
			}
		</style>
		<p style="text-align: left;">
			<?php echo sprintf( __( 'Zapier is a service to which you may submit your form data so that information may be passed along to another online service. If you do not have a Zapier account, you may %ssign up for one here%s.', 'gravityformszapier' ), "<a href='https://zapier.com/app/signup' target='_blank'>", '</a>' ) ?>
		</p>
		<br/></br>
		<form action="" method="post">
			<?php wp_nonce_field( 'uninstall', 'gf_zapier_uninstall' ) ?>
			<?php if ( GFCommon::current_user_can_any( 'gravityforms_zapier_uninstall' ) ) { ?>
				<h3><?php esc_html_e( 'Uninstall Zapier Add-On', 'gravityformszapier' ) ?></h3>
				<div class="delete-alert alert_red">
					<h3><i class="fa fa-exclamation-triangle gf_invalid"></i> Warning</h3>

					<div class="gf_delete_notice">
						<strong>
							<?php esc_html_e( 'This operation deletes ALL Zapier feeds.', 'gravityformszapier' ) ?>
						</strong>
						<?php esc_html_e( 'If you continue, you will not be able to recover any Zapier data.', 'gravityformszapier' ) ?>
					</div>
					<input type="submit" name="uninstall" value="Uninstall Zapier Add-on" class="button"
					       onclick="return confirm('<?php echo esc_js( __( "Warning! ALL Zapier settings will be deleted. This cannot be undone. \'OK\' to delete, \'Cancel\' to stop", 'gravityformszapier' ) ) ?>');">
				</div>
			<?php } ?>
		</form>
		<?php
	}

	public static function premium_update_push( $premium_update ) {

		if ( ! function_exists( 'get_plugin_data' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}


		//loading upgrade lib
		if ( ! class_exists( 'GFZapierUpgrade' ) ) {
			require_once( 'plugin-upgrade.php' );
		}
		$update = GFZapierUpgrade::get_version_info( self::$slug, self::get_key(), self::$version );

		if ( $update['is_valid_key'] == true && version_compare( self::$version, $update['version'], '<' ) ) {
			$plugin_data                = get_plugin_data( __FILE__ );
			$plugin_data['type']        = 'plugin';
			$plugin_data['slug']        = self::$path;
			$plugin_data['new_version'] = isset( $update['version'] ) ? $update['version'] : false;
			$premium_update[]           = $plugin_data;
		}

		return $premium_update;
	}

	//Integration with ManageWP
	public static function premium_update( $premium_update ) {

		if ( ! function_exists( 'get_plugin_data' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		//loading upgrade lib
		if ( ! class_exists( 'GFZapierUpgrade' ) ) {
			require_once( 'plugin-upgrade.php' );
		}
		$update = GFZapierUpgrade::get_version_info( self::$slug, self::get_key(), self::$version );
		if ( $update['is_valid_key'] == true && version_compare( self::$version, $update['version'], '<' ) ) {
			$plugin_data         = get_plugin_data( __FILE__ );
			$plugin_data['slug'] = self::$path;
			$plugin_data['type'] = 'plugin';
			$plugin_data['url']  = isset( $update['url'] ) ? $update['url'] : false; // OR provide your own callback function for managing the update

			array_push( $premium_update, $plugin_data );
		}

		return $premium_update;
	}

	public static function flush_version_info() {
		if ( ! class_exists( 'GFZapierUpgrade' ) ) {
			require_once( 'plugin-upgrade.php' );
		}

		GFZapierUpgrade::set_version_info( false );
	}

	public static function plugin_row() {
		if ( ! self::is_gravityforms_supported() ) {
			$message = sprintf( __( 'Gravity Forms ' . self::$min_gravityforms_version . ' is required. Activate it now or %spurchase it today!%s' ), "<a href='http://www.gravityforms.com'>", '</a>' );
			GFZapierUpgrade::display_plugin_message( $message, true );
		} else {
			$version_info = GFZapierUpgrade::get_version_info( self::$slug, self::get_key(), self::$version );

			if ( ! $version_info['is_valid_key'] ) {
				$new_version = version_compare( self::$version, $version_info['version'], '<' ) ? __( 'There is a new version of Gravity Forms Zapier Add-On available.', 'gravityformszapier' ) . ' <a class="thickbox" title="Gravity Forms Zapier Add-On" href="plugin-install.php?tab=plugin-information&plugin=' . self::$slug . '&TB_iframe=true&width=640&height=808">' . sprintf( __( 'View version %s Details', 'gravityformszapier' ), $version_info['version'] ) . '</a>. ' : '';
				$message     = $new_version . sprintf( __( '%sRegister%s your copy of Gravity Forms to receive access to automatic upgrades and support. Need a license key? %sPurchase one now%s.', 'gravityformszapier' ), '<a href="admin.php?page=gf_settings">', '</a>', '<a href="http://www.gravityforms.com">', '</a>' ) . '</div></td>';
				GFZapierUpgrade::display_plugin_message( $message );
			}
		}
	}

	public static function add_permissions() {
		global $wp_roles;
		$wp_roles->add_cap( 'administrator', 'gravityforms_zapier' );
		$wp_roles->add_cap( 'administrator', 'gravityforms_zapier_uninstall' );
	}

	//Target of Member plugin filter. Provides the plugin with Gravity Forms lists of capabilities
	public static function members_get_capabilities( $caps ) {
		return array_merge( $caps, array( 'gravityforms_zapier', 'gravityforms_zapier_uninstall' ) );
	}

	public static function uninstall() {

		//loading data lib
		require_once( self::get_base_path() . '/data.php' );

		if ( ! GFZapier::has_access( 'gravityforms_zapier_uninstall' ) ) {
			die( __( "You don't have adequate permission to uninstall the Zapier Add-On.", 'gravityformszapier' ) );
		}

		//droping all tables
		GFZapierData::drop_tables();

		//removing options
		delete_option( 'gf_zapier_settings' );
		delete_option( 'gf_zapier_version' );

		//Deactivating plugin
		$plugin = 'gravityformszapier/zapier.php';
		deactivate_plugins( $plugin );
		update_option( 'recently_activated', array( $plugin => time() ) + (array) get_option( 'recently_activated' ) );
	}

	protected static function has_access( $required_permission ) {
		$has_members_plugin = function_exists( 'members_get_capabilities' );
		$has_access         = $has_members_plugin ? current_user_can( $required_permission ) : current_user_can( 'level_7' );
		if ( $has_access ) {
			return $has_members_plugin ? $required_permission : 'level_7';
		} else {
			return false;
		}
	}

	//Creates or updates database tables. Will only run when version changes
	private static function setup() {

		if ( get_option( 'gf_zapier_version' ) != self::$version ) {
			GFZapierData::update_table();
		}

		update_option( 'gf_zapier_version', self::$version );
	}

	//Adds feed tooltips to the list of tooltips
	public static function tooltips( $tooltips ) {
		$zapier_tooltips = array(
			'zapier_name'        => '<h6>' . __( 'Zap Name', 'gravityformszapier' ) . '</h6>' . __( 'This is a friendly name so you know what Zap is run when this form is submitted.', 'gravityformszapier' ),
			'zapier_url'         => '<h6>' . __( 'Webhook URL', 'gravityformszapier' ) . '</h6>' . __( 'This is the URL provided by Zapier when you created your Zap on their website. This is the location to which your form data will be submitted to Zapier for additional processing.', 'gravityformszapier' ),
			'zapier_active'      => '<h6>' . __( 'Active', 'gravityformszapier' ) . '</h6>' . __( 'Check this box if you want your form submissions to be sent to Zapier for processing.', 'gravityformszapier' ),
			'zapier_conditional' => '<h6>' . __( 'Conditional Logic', 'gravityformszapier' ) . '</h6>' . __( 'When Conditional Logic is enabled, submissions for this form will only be sent to Zapier when the condition is met. When disabled, all submissions for this form will be sent to Zapier.', 'gravityformszapier' ),
			'zapier_labels'      => '<h6>' . __( 'Use Admin Labels', 'gravityformszapier' ) . '</h6>' . __( 'By default the field labels will be sent to Zapier. Enable this option to send the field admin labels when available.', 'gravityformszapier' ),

		);

		return array_merge( $tooltips, $zapier_tooltips );
	}

	//Returns the url of the plugin's root folder
	protected static function get_base_url() {
		return plugins_url( null, __FILE__ );
	}

	//Returns the physical path of the plugin's root folder
	protected static function get_base_path() {
		$folder = basename( dirname( __FILE__ ) );

		return WP_PLUGIN_DIR . '/' . $folder;
	}

	public static function set_logging_supported( $plugins ) {
		$plugins[ self::$slug ] = 'Zapier';

		return $plugins;
	}

	//Displays current version details on Plugin's page
	public static function display_changelog() {
		if ( $_REQUEST['plugin'] != self::$slug ) {
			return;
		}

		//loading upgrade lib
		if ( ! class_exists( 'GFZapierUpgrade' ) ) {
			require_once( 'plugin-upgrade.php' );
		}

		GFZapierUpgrade::display_changelog( self::$slug, self::get_key(), self::$version );
	}

	public static function check_update( $update_plugins_option ) {
		if ( ! class_exists( 'GFZapierUpgrade' ) ) {
			require_once( 'plugin-upgrade.php' );
		}

		return GFZapierUpgrade::check_update( self::$path, self::$slug, self::$url, self::$slug, self::get_key(), self::$version, $update_plugins_option );
	}

	private static function get_key() {
		if ( self::is_gravityforms_supported() ) {
			return GFCommon::get_key();
		} else {
			return '';
		}
	}

	//Returns true if the current page is an Feed pages. Returns false if not
	private static function is_zapier_page() {
		$current_page = trim( strtolower( rgget( 'page' ) ) );
		$zapier_pages = array( 'gf_zapier' );

		return in_array( $current_page, $zapier_pages );
	}

	public static function conditions_met( $form, $zap, $entry ) {
		self::log_debug( __METHOD__ . '(): Evaluating conditional logic.' );

		$zap = $zap['meta'];

		if ( ! $zap['zapier_conditional_enabled'] ) {
			self::log_debug( __METHOD__ . '(): Conditional logic not enabled for this feed.' );

			return true;
		}

		$logic = array(
			'logicType' => 'all',
			'rules'     => array(
				array(
					'fieldId'  => rgar( $zap, 'zapier_conditional_field_id' ),
					'operator' => rgar( $zap, 'zapier_conditional_operator' ),
					'value'    => rgar( $zap, 'zapier_conditional_value' ),
				),
			)
		);

		$logic          = apply_filters( 'gform_zapier_feed_conditional_logic', $logic, $form, $zap );
		$is_value_match = GFCommon::evaluate_conditional_logic( $logic, $form, $entry );
		self::log_debug( __METHOD__ . '(): Result: ' . var_export( $is_value_match, 1 ) );

		return $is_value_match;
	}

	private static function log_error( $message ) {
		if ( class_exists( 'GFLogging' ) ) {
			GFLogging::include_logger();
			GFLogging::log_message( self::$slug, $message, KLogger::ERROR );
		}
	}

	private static function log_debug( $message ) {
		if ( class_exists( 'GFLogging' ) ) {
			GFLogging::include_logger();
			GFLogging::log_message( self::$slug, $message, KLogger::DEBUG );
		}
	}

	/**
	 * Add the Post Payment Actions setting to the PayPal feed.
	 *
	 * @param array   $feed_settings_fields The PayPal feed settings.
	 * @param GFAddOn $addon                The current instance of the add-on (i.e. GF_User_Registration, GFPayPal).
	 *
	 * @since 1.8.1
	 * @since 3.1.5 Support all payment addon.
	 *
	 * @return array
	 */
	public static function add_post_payment_actions( $feed_settings_fields, $addon ) {
		if ( ! $addon instanceof GFPaymentAddOn ) {
			return $feed_settings_fields;
		}

		$form_id = absint( rgget( 'id' ) );
		$feeds   = GFZapierData::get_feed_by_form( $form_id, true );
		if ( count( $feeds ) > 0 ) {
			$config = array();

			if ( method_exists( $addon, 'get_post_payment_actions_config' ) ) {
				$config = $addon->get_post_payment_actions_config( self::$slug );
			} elseif ( $addon instanceof GFPayPal ) {
				$config = array(
					'position' => 'after',
					'setting'  => 'options',
				);
			}

			if ( empty( $config ) ) {
				return $feed_settings_fields;
			}

			$choice = array(
				'label' => esc_html__( 'Send feed to Zapier only when payment is received.', 'gravityformszapier' ),
				'name'  => 'delay_gravityformszapier',
			);

			$field_name = 'post_payment_actions';
			$field      = $addon->get_field( $field_name, $feed_settings_fields );

			if ( ! $field ) {

				$fields = array(
					array(
						'name'    => $field_name,
						'label'   => esc_html__( 'Post Payment Actions', 'gravityformszapier' ),
						'type'    => 'checkbox',
						'choices' => array( $choice ),
						'tooltip' => '<h6>' . esc_html__( 'Post Payment Actions', 'gravityforms' ) . '</h6>' . esc_html__( 'Select which actions should only occur after payment has been received.', 'gravityformszapier' ),
					),
				);

				$setting = rgar( $config, 'setting', 'options' );
				if ( rgar( $config, 'position' ) === 'before' ) {
					$feed_settings_fields = $addon->add_field_before( $setting, $fields, $feed_settings_fields );
				} else {
					$feed_settings_fields = $addon->add_field_after( $setting, $fields, $feed_settings_fields );
				}
			} else {

				$field['choices'][]   = $choice;
				$feed_settings_fields = $addon->replace_field( $field_name, $field, $feed_settings_fields );

			}
		}

		return $feed_settings_fields;
	}

	/**
	 * Add PayPal delay setting for Gravity Forms < 2.0.
	 *
	 * @deprecated 1.8.2
	 * @todo Remove once $min_gravityforms_version reaches 2.0.
	 */
	public static function add_paypal_settings( $feed, $form ) {
		//this function was copied from the feed framework since this add-on has not yet been migrated
		$form_id   = rgar( $form, 'id' );
		$feed_meta = $feed['meta'];

		$addon_name  = 'gravityformszapier';
		$addon_feeds = array();
		$feeds       = GFZapierData::get_feeds( $form_id );
		if ( count( $feeds ) > 0 ) {
			$settings_style = '';
		} else {
			$settings_style = 'display:none;';
		}

		foreach ( $feeds as $feed ) {
			$addon_feeds[] = $feed['form_id'];
		}

		?>

		<li style="<?php echo $settings_style ?>" id="delay_<?php echo $addon_name; ?>_container">
			<input type="checkbox" name="paypal_delay_<?php echo $addon_name; ?>"
			       id="paypal_delay_<?php echo $addon_name; ?>"
			       value="1" <?php echo rgar( $feed_meta, "delay_$addon_name" ) ? "checked='checked'" : '' ?> />
			<label class="inline" for="paypal_delay_<?php echo $addon_name; ?>">
				<?php
				esc_html_e( 'Send feed to Zapier only when payment is received.', 'gravityformszapier' );
				?>
			</label>
		</li>

		<script type="text/javascript">
			jQuery(document).ready(function ($) {

				jQuery(document).bind('paypalFormSelected', function (event, form) {

					var addonFormIds = <?php echo json_encode( $addon_feeds ); ?>;
					var isApplicableFeed = false;

					if (jQuery.inArray(String(form.id), addonFormIds) != -1)
						isApplicableFeed = true;

					if (isApplicableFeed) {
						jQuery("#delay_<?php echo $addon_name; ?>_container").show();
					} else {
						jQuery("#delay_<?php echo $addon_name; ?>_container").hide();
					}

				});
			});
		</script>

		<?php
	}

	/**
	 * Save PayPal delay setting for Gravity Forms < 2.0.
	 *
	 * @deprecated 1.8.2
	 * @todo Remove once $min_gravityforms_version reaches 2.0.
	 */
	public static function save_paypal_settings( $feed ) {
		$feed['meta']['delay_gravityformszapier'] = rgpost( 'paypal_delay_gravityformszapier' );

		return $feed;
	}

	public static function get_paypal_feeds( $form_id = null ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'gf_addon_feed';
		$has_table  = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

		if ( ! $has_table ) {
			return array();
		}

		$form_filter = is_numeric( $form_id ) ? $wpdb->prepare( 'AND form_id=%d', absint( $form_id ) ) : '';

		$sql = $wpdb->prepare( "SELECT * FROM {$table_name}
                               WHERE addon_slug=%s {$form_filter}", 'gravityformspaypal' );

		$results = $wpdb->get_results( $sql, ARRAY_A );
		foreach ( $results as &$result ) {
			$result['meta'] = json_decode( $result['meta'], true );
		}

		return $results;
	}

	public static function is_feed_condition_met( $feed, $form, $entry ) {

		$feed_meta            = $feed['meta'];
		$is_condition_enabled = rgar( $feed_meta, 'feed_condition_conditional_logic' ) == true;
		$logic                = rgars( $feed_meta, 'feed_condition_conditional_logic_object/conditionalLogic' );

		if ( ! $is_condition_enabled || empty( $logic ) ) {
			return true;
		}

		return GFCommon::evaluate_conditional_logic( $logic, $form, $entry );
	}

	public static function is_delayed( $paypal_feed ) {
		//look for delay in paypal feed specific to zapier add-on
		$delay = rgar( $paypal_feed['meta'], 'delay_gravityformszapier' );

		return $delay;
	}

	public static function has_paypal_payment( $feed, $form, $entry ) {

		$products = GFCommon::get_product_fields( $form, $entry );

		$payment_field   = $feed['meta']['transactionType'] == 'product' ? $feed['meta']['paymentAmount'] : $feed['meta']['recurringAmount'];
		$setup_fee_field = rgar( $feed['meta'], 'setupFee_enabled' ) ? $feed['meta']['setupFee_product'] : false;
		$trial_field     = rgar( $feed['meta'], 'trial_enabled' ) ? rgars( $feed, 'meta/trial_product' ) : false;

		$amount       = 0;
		$line_items   = array();
		$discounts    = array();
		$fee_amount   = 0;
		$trial_amount = 0;
		foreach ( $products['products'] as $field_id => $product ) {

			$quantity      = $product['quantity'] ? $product['quantity'] : 1;
			$product_price = GFCommon::to_number( $product['price'] );

			$options = array();
			if ( is_array( rgar( $product, 'options' ) ) ) {
				foreach ( $product['options'] as $option ) {
					$options[] = $option['option_name'];
					$product_price += $option['price'];
				}
			}

			$is_trial_or_setup_fee = false;

			if ( ! empty( $trial_field ) && $trial_field == $field_id ) {

				$trial_amount          = $product_price * $quantity;
				$is_trial_or_setup_fee = true;

			} else if ( ! empty( $setup_fee_field ) && $setup_fee_field == $field_id ) {

				$fee_amount            = $product_price * $quantity;
				$is_trial_or_setup_fee = true;
			}

			//Do not add to line items if the payment field selected in the feed is not the current field.
			if ( is_numeric( $payment_field ) && $payment_field != $field_id ) {
				continue;
			}

			//Do not add to line items if the payment field is set to "Form Total" and the current field was used for trial or setup fee.
			if ( $is_trial_or_setup_fee && ! is_numeric( $payment_field ) ) {
				continue;
			}

			$amount += $product_price * $quantity;

		}


		if ( ! empty( $products['shipping']['name'] ) && ! is_numeric( $payment_field ) ) {
			$line_items[] = array(
				'id'          => '',
				'name'        => $products['shipping']['name'],
				'description' => '',
				'quantity'    => 1,
				'unit_price'  => GFCommon::to_number( $products['shipping']['price'] ),
				'is_shipping' => 1
			);
			$amount += $products['shipping']['price'];
		}

		return $amount > 0;
	}

	public static function paypal_fulfillment( $entry, $feed, $transaction_id, $amount ) {

		self::log_debug( 'Checking PayPal fulfillment for transaction ' . $transaction_id );
		$is_fulfilled = gform_get_meta( $entry['id'], self::$slug . '_is_fulfilled' );
		if ( $is_fulfilled || ! self::is_delayed( $feed ) ) {
			self::log_debug( 'Entry ' . $entry['id'] . ' is already fulfilled or feeds are not delayed. No action necessary.' );

			return false;
		}

		//get zaps for form
		$form_id = $entry['form_id'];
		$zaps    = GFZapierData::get_feed_by_form( $form_id, true );
		if ( ! empty( $zaps ) ) {
			self::log_debug( "Running PayPal Fulfillment for transaction {$transaction_id}" );
			$is_fulfilled = rgar( $entry, 'is_fulfilled' );
			if ( $is_fulfilled ) {
				self::log_debug( 'Payment has been completed, sending to Zapier' );
				$form = RGFormsModel::get_form_meta( $entry['form_id'] );
				self::send_form_data_to_zapier( $entry, $form );
			} else {
				self::log_debug( 'Payment not fulfilled, not running paypal fulfillment.' );
			}
		}
	}

	/**
	 * Triggers processing of feeds delayed by payment add-ons.
	 *
	 * @since 3.1.5
	 *
	 * @param string     $transaction_id The transaction or subscription ID.
	 * @param array      $payment_feed   The payment feed which originated the transaction.
	 * @param array      $entry          The entry currently being processed.
	 * @param null|array $form           The form currently being processed or null for the legacy PayPal integration.
	 */
	public static function action_trigger_payment_delayed_feeds( $transaction_id, $payment_feed, $entry, $form = null ) {
		self::log_debug( __METHOD__ . '(): Checking fulfillment for transaction ' . $transaction_id . ' for ' . $payment_feed['addon_slug'] );

		$is_fulfilled = gform_get_meta( $entry['id'], self::$slug . '_is_fulfilled' );
		if ( $is_fulfilled || ! self::is_delayed( $payment_feed ) ) {
			self::log_debug( __METHOD__ . '(): Entry ' . $entry['id'] . ' is already fulfilled or feeds are not delayed. No action necessary.' );

			return;
		}

		if ( is_null( $form ) ) {
			$form = GFFormsModel::get_form_meta( $entry['form_id'] );
		}

		self::$_bypass_feed_delay = true;
		self::send_form_data_to_zapier( $entry, $form );
	}
	//end of functions to use for PayPal delay.

	/**
	 * Include the add-on table in the Gravity Forms 2.2+ system report.
	 *
	 * @since 2.1.2
	 *
	 * @param array $system_report
	 *
	 * @return array
	 */
	public static function system_report( $system_report ) {

		foreach ( $system_report as &$section ) {
			if ( rgar( $section, 'title_export' ) === 'Gravity Forms Environment' && is_array( rgar( $section, 'tables' ) ) ) {
				foreach ( $section['tables'] as &$table ) {
					if ( rgar( $table, 'title_export' ) !== 'Database' ) {
						continue;
					}

					$table_name   = GFZapierData::get_zapier_table_name();
					$table_exists = GFCommon::table_exists( $table_name );

					$table['items'][] = array(
						'label'                     => $table_name,
						'value'                     => '',
						'is_valid'                  => $table_exists,
						'validation_message'        => $table_exists ? '' : __( 'Table does not exist', 'gravityformszapier' ),
						'validation_message_export' => $table_exists ? '' : 'Table does not exist',
					);

					return $system_report;
				}
			}
		}

		return $system_report;
	}

}

require_once( ABSPATH . '/wp-admin/includes/class-wp-list-table.php' );

class GFZapierTable extends WP_List_Table {
	private $_form_id;

	function __construct( $form_id ) {
		$this->_form_id = $form_id;

		$this->items = array();

		$this->_column_headers = array(
			array(
				'name' => __( 'Zap Name', 'gravityformszapier' ),
				'url'  => __( 'Webhook URL', 'gravityformszapier' )
			),
			array(),
			array(),
			'name',
		);

		parent::__construct();
	}

	function get_columns() {
		return $this->_column_headers[0];
	}

	function prepare_items() {
		//query db
		$zaps = GFZapierData::get_feed_by_form( $this->_form_id );

		$this->items = $zaps;
	}

	function display() {
		?>

		<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" cellspacing="0">
			<thead>
			<tr>
				<?php $this->print_column_headers(); ?>
			</tr>
			</thead>

			<tfoot>
			<tr>
				<?php $this->print_column_headers( false ); ?>
			</tr>
			</tfoot>

			<tbody id="the-list"<?php if ( $this->_args['singular'] ) {
				echo " class='list:{$this->_args['singular']}'";
			} ?>>

			<?php $this->display_rows_or_placeholder(); ?>

			</tbody>
		</table>

		<?php
	}

	function no_items() {
		$add_new_url = add_query_arg( array( 'zid' => 0 ) );
		$add_new_url = esc_url( $add_new_url );
		printf( __( "You currently don't have any Zapier Feeds, let's go %screate one%s", 'gravityformszapier' ), "<a href='{$add_new_url}'>", '</a>' );
	}

	function single_row( $item ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr id="zapier-' . $item['id'] . '" ' . $row_class . '>';
		echo $this->single_row_columns( $item );
		echo '</tr>';
	}

	function column_default( $item, $column ) {
		echo rgar( $item, $column );
	}

	function column_name( $item ) {
		$edit_url = add_query_arg( array( 'zid' => absint( $item['id'] ) ) );
		/**
		 * A filter to allow modification of Zapier Feed actions (Delete a feed and edit).
		 *
		 * @param array An array for the Edit and Delete actions for a Zapier Feed (Include all the HTML for each action link).
		 */
		$actions = apply_filters( 'gform_zapier_actions', array(
			'edit'   => '<a title="' . __( 'Edit this item', 'gravityformszapier' ) . '" href="' . esc_url( $edit_url ) . '">' . __( 'Edit', 'gravityformszapier' ) . '</a>',
			'delete' => '<a title="' . __( 'Delete this item', 'gravityformszapier' ) . '" class="submitdelete" onclick="javascript: if(confirm(\'' . __( 'WARNING: You are about to delete this Zapier feed.', 'gravityformszapier' ) . __( "\'Cancel\' to stop, \'OK\' to delete.", 'gravityforms' ) . '\')){ DeleteZap(\'' . esc_js( $item['id'] ) . '\'); }" style="cursor:pointer;">' . __( 'Delete', 'gravityformszapier' ) . '</a>'
		) );
		?>

		<strong><?php echo esc_html( rgar( $item, 'name' ) ); ?></strong>
		<div class="row-actions">

			<?php
			if ( is_array( $actions ) && ! empty( $actions ) ) {
				$keys     = array_keys( $actions );
				$last_key = array_pop( $keys );
				foreach ( $actions as $key => $html ) {
					$divider = $key == $last_key ? '' : ' | ';
					?>
					<span class="<?php echo $key; ?>">
                        <?php echo $html . $divider; ?>
                    </span>
					<?php
				}
			}
			?>

		</div>

		<?php
	}
}
