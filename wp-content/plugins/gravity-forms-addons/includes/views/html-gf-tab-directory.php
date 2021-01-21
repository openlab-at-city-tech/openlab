<?php
/**
 * The template that contains 'Directory Columns' tab setting.
 *
 * @link       https://gravityview.co
 * @since      4.2
 *
 * @package    gravity-forms-addons
 * @subpackage gravity-forms-addons/includes/views
 */
?>

</ul>
</div>
<div id="gform_tab_directory">
	<ul>
		<li class="use_as_entry_link gf_directory_setting field_setting">
			<label for="field_use_as_entry_link">
				<?php esc_html_e( 'Use As Link to Single Entry', 'gravity-forms-addons' ); ?>
				<?php gform_tooltip( 'kws_gf_directory_use_as_link_to_single_entry' ); ?>
			</label>
			<label for="field_use_as_entry_link"><input type="checkbox" value="1" id="field_use_as_entry_link" /> <?php esc_html_e( 'Use this field as a link to single entry view', 'gravity-forms-addons' ); ?></label>
		</li>

		<li class="use_as_entry_link_value gf_directory_setting field_setting">
			<label>
				<?php esc_html_e( 'Single Entry Link Text', 'gravity-forms-addons' ); ?>
				<span class="howto"><?php esc_html_e( 'Note: it is a good idea to use required fields for links to single entries so there are no blank links.', 'gravity-forms-addons' ); ?></span>
			</label>

			<label><input type="radio" name="field_use_as_entry_link_value" id="field_use_as_entry_link_value" value="on" /> <?php esc_html_e( 'Use field values from entry', 'gravity-forms-addons' ); ?></label>
			<label><input type="radio" name="field_use_as_entry_link_value" id="field_use_as_entry_link_label" value="label" /> <?php printf( esc_html( 'Use the Field Label %s as link text', 'gravity-forms-addons' ), '<span id="entry_link_label_text"></span>' ); ?></label>
			<label><input type="radio" name="field_use_as_entry_link_value" id="field_use_as_entry_link_custom" value="custom" /> <?php esc_html_e( 'Use custom link text.', 'gravity-forms-addons' ); ?></label>
			<span class="hide-if-js" style="display:block;clear:both; margin-left:1.5em"><input type="text" class="widefat" id="field_use_as_entry_link_value_custom_text" value="" /><span class="howto"><?php printf( esc_html( '%s%%value%%%s will be replaced with each entry\'s value.', 'gravity-forms-addons' ), "<code class='code'>", '</code>' ); ?></span></span>
		</li>

		<li class="hide_in_directory_view only_visible_to_logged_in only_visible_to_logged_in_cap gf_directory_setting field_setting">
			<label for="hide_in_directory_view">
				<?php esc_html_e( 'Hide This Field in Directory View?', 'gravity-forms-addons' ); ?>
				<?php gform_tooltip( 'kws_gf_directory_hide_in_directory_view' ); ?>
			</label>
			<label><input type="checkbox" id="hide_in_directory_view" /> <?php esc_html_e( 'Hide this field in the directory view.', 'gravity-forms-addons' ); ?></label>

			<label>
				<input type="checkbox" id="only_visible_to_logged_in" /> <?php esc_html_e( 'Only visible to logged in users with the following role:', 'gravity-forms-addons' ); ?>
				<select id="only_visible_to_logged_in_cap">
					<option value="read"><?php esc_html_e( 'Any', 'gravity-forms-addons' ); ?></option>
					<option value="publish_posts"><?php esc_html_e( 'Author or higher', 'gravity-forms-addons' ); ?></option>
					<option value="delete_others_posts"><?php esc_html_e( 'Editor or higher', 'gravity-forms-addons' ); ?></option>
					<option value="manage_options"><?php esc_html_e( 'Administrator', 'gravity-forms-addons' ); ?></option>
				</select>
			</label>

		</li>

		<li class="hide_in_single_entry_view gf_directory_setting field_setting">
			<label for="hide_in_single_entry_view">
				<?php esc_html_e( 'Hide This Field in Single Entry View?', 'gravity-forms-addons' ); ?>
				<?php gform_tooltip( 'kws_gf_directory_hide_in_single_entry_view' ); ?>
			</label>
			<label><input type="checkbox" id="hide_in_single_entry_view" /> <?php esc_html_e( 'Hide this field in the single entry view.', 'gravity-forms-addons' ); ?></label>
		</li>
		
		<li class="use_field_as_search_filter gf_directory_setting field_setting">
			<label for="use_field_as_search_filter">
				<?php esc_html_e( 'Directory Search Field', 'gravity-forms-addons' ); ?>
				<?php gform_tooltip( 'kws_gf_directory_use_field_as_search_filter' ); ?>
			</label>
			<label for="use_field_as_search_filter"><input type="checkbox" id="use_field_as_search_filter" /> <?php esc_html_e( 'Use this field as a search filter', 'gravity-forms-addons' ); ?></label>
		</li>

