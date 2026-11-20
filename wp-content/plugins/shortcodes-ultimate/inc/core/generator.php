<?php
// phpcs:ignoreFile
/**
 * Shortcode Generator
 */
class Su_Generator
{

	public function __construct()
	{
		add_action(
			'media_buttons',
			array(__CLASS__, 'button_classic_editor'),
			1000
		);
		add_action(
			'enqueue_block_editor_assets',
			array(__CLASS__, 'button_block_editor')
		);

		add_action('wp_footer', array(__CLASS__, 'popup'));
		add_action('admin_footer', array(__CLASS__, 'popup'));

		add_action('wp_ajax_su_generator_settings', array(__CLASS__, 'settings'));
		add_action('wp_ajax_su_generator_preview', array(__CLASS__, 'preview'));
		add_action('su/generator/actions', array(__CLASS__, 'presets'));

		add_action('wp_ajax_su_generator_get_icons', array(__CLASS__, 'ajax_get_icons'));
		add_action('wp_ajax_su_generator_get_terms', array(__CLASS__, 'ajax_get_terms'));
		add_action('wp_ajax_su_generator_get_taxonomies', array(__CLASS__, 'ajax_get_taxonomies'));
		add_action('wp_ajax_su_generator_search_posts', array(__CLASS__, 'ajax_search_posts'));
		add_action('wp_ajax_su_generator_search_users', array(__CLASS__, 'ajax_search_users'));
		add_action('wp_ajax_su_generator_add_preset', array(__CLASS__, 'ajax_add_preset'));
		add_action('wp_ajax_su_generator_remove_preset', array(__CLASS__, 'ajax_remove_preset'));
		add_action('wp_ajax_su_generator_get_preset', array(__CLASS__, 'ajax_get_preset'));
	}

	/**
	 * @deprecated 5.1.0 Replaced with Su_Generator::classic_editor_button()
	 */
	public static function button($args = array())
	{
		return self::button_html_editor($args);
	}
	public static function classic_editor_button($args = array())
	{
		return self::button_html_editor($args);
	}

	public static function button_html_editor($args = array())
	{

		if (!self::access_check()) {
			return;
		}

		self::enqueue_generator();

		$args = wp_parse_args(
			$args,
			array(
				'target' => '',
				'tag' => 'button',
				'text' => __('Insert shortcode', 'shortcodes-ultimate'),
				'class' => 'button',
				'icon' => true,
				'echo' => true,
				'shortcode' => '',
			)
		);

		if ($args['icon']) {

			$args['icon'] = '<svg style="vertical-align:middle;position:relative;top:-1px;opacity:.8;width:18px;height:18px" viewBox="0 0 20 20" width="18" height="18" aria-hidden="true"><path fill="currentcolor" d="M8.48 2.75v2.5H5.25v9.5h3.23v2.5H2.75V2.75h5.73zm9.27 14.5h-5.73v-2.5h3.23v-9.5h-3.23v-2.5h5.73v14.5z"/></svg>';

		}

		$onclick = sprintf(
			"SUG.App.insert('html',{editorID:'%s',shortcode:'%s'});return false;",
			esc_attr($args['target']),
			esc_attr($args['shortcode'])
		);

		$button = sprintf(
			'<%6$s
				type="button"
				href="javascript:;"
				class="su-generator-button %1$s"
				title="%2$s"
				onclick="%3$s"
			>%4$s %5$s</%6$s>',
			esc_attr($args['class']),
			esc_attr($args['text']),
			$onclick,
			$args['icon'],
			esc_html($args['text']),
			sanitize_key($args['tag'])
		);

		if ($args['echo']) {
			echo $button;
		}

		return $button;

	}

	public static function button_classic_editor($target)
	{

		if (!self::access_check()) {
			return;
		}

		self::enqueue_generator();

		$onclick = sprintf(
			"SUG.App.insert('classic',{editorID:'%s',shortcode:''});",
			esc_attr($target)
		);

		$icon = '<svg style="vertical-align:middle;position:relative;top:-1px;opacity:.8;width:18px;height:18px" viewBox="0 0 20 20" width="18" height="18" aria-hidden="true"><path fill="currentcolor" d="M8.48 2.75v2.5H5.25v9.5h3.23v2.5H2.75V2.75h5.73zm9.27 14.5h-5.73v-2.5h3.23v-9.5h-3.23v-2.5h5.73v14.5z"/></svg>';

		$button = sprintf(
			'<button
				type="button"
				class="su-generator-button button"
				title="%1$s"
				onclick="%2$s"
			>
				%3$s %1$s
			</button>',
			__('Insert shortcode', 'shortcodes-ultimate'),
			$onclick,
			$icon
		);

		echo $button;

	}

	public static function button_block_editor()
	{

		if (!self::access_check()) {
			return;
		}

		self::enqueue_generator();

		wp_enqueue_script(
			'shortcodes-ultimate-block-editor',
			plugins_url('includes/js/block-editor/index.js', SU_PLUGIN_FILE),
			array('wp-element', 'wp-components', 'wp-edit-post', 'wp-plugins', 'wp-blocks', 'wp-data'),
			SU_PLUGIN_VERSION,
			true
		);

		wp_enqueue_style(
			'shortcodes-ultimate-block-editor',
			plugins_url('includes/css/block-editor.css', SU_PLUGIN_FILE),
			array(),
			SU_PLUGIN_VERSION
		);

		wp_localize_script(
			'shortcodes-ultimate-block-editor',
			'SUBlockEditorL10n',
			array(
				'insertShortcode' => __('Insert shortcode', 'shortcodes-ultimate'),
				'livePreviewTitle' => __('Live Preview', 'shortcodes-ultimate'),
				'livePreviewDescription' => __('This is a Live Preview. Click this button to open the shortcode generator and try the plugin.', 'shortcodes-ultimate'),
			)
		);

		wp_localize_script(
			'shortcodes-ultimate-block-editor',
			'SUBlockEditorSettings',
			array(
				'supportedBlocks' => get_option('su_option_supported_blocks', array()),
				'showToolbarButton' => get_option('su_option_show_toolbar_button', 'on'),
				'showBlockControlsButton' => get_option('su_option_show_block_controls_button', 'on'),
				'isLivePreview' => su_is_live_preview_request(),
			)
		);

	}

	public static function enqueue_generator()
	{
		do_action('su/generator/enqueue');
		self::enqueue_assets();
	}

	public static function enqueue_assets()
	{

		wp_enqueue_media();

		su_query_asset(
			'css',
			array(
				'simpleslider',
				'farbtastic',
				'magnific-popup',
				'su-icons',
				'su-generator',
			)
		);

		su_query_asset(
			'js',
			array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-widget',
				'jquery-ui-mouse',
				'jquery-ui-sortable',
				'simpleslider',
				'farbtastic',
				'magnific-popup',
				'su-generator',
			)
		);

	}

	public static function normalize_shortcode($shortcode)
	{
		$shortcode = is_array($shortcode) ? $shortcode : array();

		foreach (array('name' => 'untitled-shortcode', 'desc' => '', 'group' => 'other', 'atts' => array()) as $key => $value) {
			if (!isset($shortcode[$key])) {
				$shortcode[$key] = $value;
			}
		}

		return $shortcode;
	}

	public static function get_choice_icon($shortcode_id, $shortcode)
	{
		$shortcode = self::normalize_shortcode($shortcode);

		if (!isset($shortcode['icon'])) {
			$shortcode['icon'] = 'puzzle-piece';
		}

		$svg_icon_path = 'admin/images/shortcodes/svgs/' . $shortcode_id . '.svg';
		$svg_file = su_get_plugin_path() . $svg_icon_path;
		$svg_url = su_get_plugin_url() . $svg_icon_path;

		if (file_exists($svg_file)) {
			$shortcode['icon'] = $svg_url . '?v=' . SU_PLUGIN_VERSION;
		}

		if (strpos($shortcode['icon'], '/') === false) {
			$shortcode['icon'] = 'icon:' . $shortcode['icon'];
		}

		return su_html_icon($shortcode['icon']);
	}

	/**
	 * Generator popup form
	 */
	public static function popup()
	{

		if (!did_action('su/generator/enqueue')) {
			return;
		}

		$tools = apply_filters('su/generator/tools', array(
			'<a href="' . admin_url('admin.php?page=shortcodes-ultimate-settings') . '" target="_blank" title="' . __('Settings', 'shortcodes-ultimate') . '">' . __('Plugin settings', 'shortcodes-ultimate') . '</a>',
			'<a href="https://getshortcodes.com/" target="_blank" title="' . __('Plugin homepage', 'shortcodes-ultimate') . '">' . __('Plugin homepage', 'shortcodes-ultimate') . '</a>',
		));

		if (!su_fs()->can_use_premium_code() && !su_has_all_active_addons()) {
			$tools[] = '<a href="' . esc_attr(su_get_utm_link('https://getshortcodes.com/pricing/', 'wp-dashboard', 'generator', 'badge')) . '" target="_blank" title="' . __('Upgrade to PRO', 'shortcodes-ultimate') . '" class="su-add-ons">&#9733; ' . __('Upgrade to PRO', 'shortcodes-ultimate') . '</a>';
		}
		?>
		<div id="su-generator-wrap" style="display:none">
			<div id="su-generator">
				<div class="su-generator-header">
					<!-- <div id="su-generator-tools"><?php echo implode(' <span></span> ', $tools); ?></div> -->
					<div class="su-generator-header-title">
						<?php _e('Insert Shortcode', 'shortcodes-ultimate'); ?>
					</div>
					<div id="su-generator-search-wrapper">
						<input type="text" name="su_generator_search" id="su-generator-search" value="" placeholder="<?php _e('Search for shortcodes', 'shortcodes-ultimate'); ?>" />
						<button type="button" id="su-generator-search-clear" title="<?php esc_attr_e('Clear search', 'shortcodes-ultimate'); ?>" aria-label="<?php esc_attr_e('Clear search', 'shortcodes-ultimate'); ?>">
							<i class="sui sui-times" aria-hidden="true"></i>
						</button>
					</div>
				</div>
				<!-- <p id="su-generator-search-pro-tip"><?php printf('<strong>%s:</strong> %s', __('Pro Tip', 'shortcodes-ultimate'), __('Hit enter to select highlighted shortcode, while searching', 'shortcodes-ultimate')) ?></p> -->
				<?php if (!su_fs()->can_use_premium_code() && !su_has_all_active_addons()): ?>
					<div class="su-generator-pro-nag">
						<?php
						// translators: %s is replaced with "Shortcodes Ultimate Pro link"
						printf(
							__('Unlock 15 additional shortcodes, 60+ styles, and create your own shortcodes with %s', 'shortcodes-ultimate'),
							sprintf(
								'<a href="%s" target="_blank">%s &rsaquo;</a>',
								esc_attr(su_get_utm_link('https://getshortcodes.com/pricing/', 'wp-dashboard', 'generator', 'pro-nag')),
								__('Shortcodes Ultimate Pro', 'shortcodes-ultimate')
							)
						);
						?>
						<a href="<?php echo esc_attr(su_get_utm_link('https://getshortcodes.com/pricing/', 'wp-dashboard', 'generator', 'pro-nag')) ?>" target="_blank" class="su-generator-pro-nag-block-link" tabindex="-1" aria-hidden="true"><?php _e('Shortcodes Ultimate Pro', 'shortcodes-ultimate') ?></a>
					</div>
				<?php endif; ?>
				<div id="su-generator-choices">
					<?php foreach (self::get_shortcodes_grouped() as $group_id => $group): ?>
						<div class="su-generator-choices-group">
							<div class="su-generator-choices-group-title">
								<?php echo esc_html($group['title']); ?>
							</div>
							<div class="su-generator-choices-group-items">
								<?php foreach ($group['shortcodes'] as $shortcode_id => $shortcode): ?>
									<?php $is_pro_choice = !su_fs()->can_use_premium_code() && isset($shortcode['is_pro']) && $shortcode['is_pro']; ?>
									<div class="su-generator-choice<?php echo $is_pro_choice ? ' su-generator-choice-is-pro' : ''; ?>" data-name="<?php echo esc_attr($shortcode['name']); ?>" data-shortcode="<?php echo esc_attr($shortcode_id); ?>" title="<?php echo esc_attr($shortcode['desc']); ?>" data-desc="<?php echo esc_attr($shortcode['desc']); ?>" data-group="<?php echo esc_attr($shortcode['group']); ?>">
										<?php echo self::get_choice_icon($shortcode_id, $shortcode); ?>
										<span><?php echo esc_html($shortcode['name']); ?></span>
										<?php if ($is_pro_choice): ?>
											<span class="su-generator-choice-pro-icon" aria-hidden="true">
												<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" focusable="false"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294zM5 21h14"/></svg>
											</span>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div id="su-generator-settings"></div>
				<input type="hidden" name="su-generator-selected" id="su-generator-selected" value="<?php echo plugins_url('', SU_PLUGIN_FILE); ?>" />
				<input type="hidden" name="su-generator-url" id="su-generator-url" value="<?php echo plugins_url('', SU_PLUGIN_FILE); ?>" />
				<input type="hidden" name="su-compatibility-mode-prefix" id="su-compatibility-mode-prefix" value="<?php echo su_get_shortcode_prefix(); ?>" />
				<input type="hidden" name="su-generator-option-skip" id="su-generator-option-skip" value="<?php echo esc_attr(get_option('su_option_skip', '')); ?>" />
				<?php wp_nonce_field('su_generator_preset', 'su_generator_presets_nonce'); ?>
				<?php wp_nonce_field('su_generator_preview', 'su_generator_preview_nonce'); ?>
				<div id="su-generator-result" style="display:none"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Normalize shortcode generator tabs.
	 */
	private static function get_generator_tabs($shortcode)
	{

		if (
			empty($shortcode['tabs']) ||
			!is_array($shortcode['tabs'])
		) {
			return array();
		}

		$tabs = array();

		foreach ($shortcode['tabs'] as $tab_id => $tab_info) {

			$tab_id = sanitize_key($tab_id);

			if (!$tab_id) {
				continue;
			}

			if (is_string($tab_info)) {
				$tab_info = array(
					'title' => $tab_info,
				);
			}

			if (!is_array($tab_info)) {
				continue;
			}

			$tabs[$tab_id] = wp_parse_args(
				$tab_info,
				array(
					'title' => $tab_id,
					'icon'  => '',
				)
			);

		}

		return $tabs;

	}

	private static function get_generator_tab_icon($tab)
	{

		if (empty($tab['icon'])) {
			return '';
		}

		$icon = $tab['icon'];

		if (strpos($icon, '/') === false && strpos($icon, 'icon:') !== 0) {
			$icon = 'icon:' . $icon;
		}

		return su_html_icon($icon);

	}

	private static function render_generator_tabs($tabs)
	{

		if (empty($tabs)) {
			return '';
		}

		$return = '<div class="su-generator-tabs" role="tablist">';
		$count = 0;

		foreach ($tabs as $tab_id => $tab) {

			$active = $count === 0;
			$tab_dom_id = 'su-generator-tab-' . sanitize_html_class($tab_id);
			$panel_dom_id = 'su-generator-tab-panel-' . sanitize_html_class($tab_id);
			$icon = self::get_generator_tab_icon($tab);

			$return .= '<button type="button" class="su-generator-tab' . ($active ? ' su-generator-tab-active' : '') . '" data-tab="' . esc_attr($tab_id) . '" id="' . esc_attr($tab_dom_id) . '" role="tab" aria-selected="' . ($active ? 'true' : 'false') . '" aria-controls="' . esc_attr($panel_dom_id) . '">';

			if ($icon) {
				$return .= '<span class="su-generator-tab-icon" aria-hidden="true">' . $icon . '</span>';
			}

			$return .= '<span class="su-generator-tab-title">' . esc_html($tab['title']) . '</span>';
			$return .= '</button>';

			$count++;

		}

		$return .= '</div>';

		return $return;

	}

	private static function render_generator_tab_panels($tabs, $panels)
	{

		if (empty($tabs)) {
			return '';
		}

		$return = '<div class="su-generator-tab-panels">';
		$count = 0;

		foreach ($tabs as $tab_id => $tab) {

			$active = $count === 0;
			$tab_dom_id = 'su-generator-tab-' . sanitize_html_class($tab_id);
			$panel_dom_id = 'su-generator-tab-panel-' . sanitize_html_class($tab_id);
			$content = isset($panels[$tab_id]) ? $panels[$tab_id] : '';

			$return .= '<div class="su-generator-tab-panel' . ($active ? ' su-generator-tab-panel-active' : '') . '" data-tab="' . esc_attr($tab_id) . '" id="' . esc_attr($panel_dom_id) . '" role="tabpanel" aria-labelledby="' . esc_attr($tab_dom_id) . '" aria-hidden="' . ($active ? 'false' : 'true') . '">';
			$return .= $content;
			$return .= '</div>';

			$count++;

		}

		$return .= '</div>';

		return $return;

	}

	private static function render_generator_attribute($attr_name, $attr_info, $skip)
	{

		if (isset($attr_info['hidden']) && $attr_info['hidden']) {
			return '';
		}

		// Prepare default value
		$default = (string) (isset($attr_info['default'])) ? $attr_info['default'] : '';
		$attr_info['name'] = (isset($attr_info['name'])) ? $attr_info['name'] : $attr_name;
		$depends = isset($attr_info['depends']) && is_array($attr_info['depends'])
			? ' data-depends="' . esc_attr(wp_json_encode($attr_info['depends'])) . '"'
			: '';
		$return = '<div class="su-generator-attr-container' . $skip . '" data-default="' . esc_attr($default) . '"' . $depends . '>';
		$return .= '<h5>' . $attr_info['name'] . '</h5>';
		// Create field types
		if (!isset($attr_info['type']) && isset($attr_info['values']) && is_array($attr_info['values']) && count($attr_info['values']))
			$attr_info['type'] = 'select';
		elseif (!isset($attr_info['type']))
			$attr_info['type'] = 'text';
		if (is_callable(array('Su_Generator_Views', $attr_info['type'])))
			$return .= call_user_func(array('Su_Generator_Views', $attr_info['type']), $attr_name, $attr_info);
		elseif (isset($attr_info['callback']) && is_callable($attr_info['callback']))
			$return .= call_user_func($attr_info['callback'], $attr_name, $attr_info);
		if (isset($attr_info['desc']))
			$return .= '<div class="su-generator-attr-desc">' . str_replace(array('<b%value>', '<b_>'), '<b class="su-generator-set-value" title="' . __('Click to set this value', 'shortcodes-ultimate') . '">', $attr_info['desc']) . '</div>';
		$return .= '</div>';

		return $return;

	}

	private static function render_generator_content_field($shortcode)
	{

		if (!isset($shortcode['content'])) {
			$shortcode['content'] = '';
		}

		if (is_array($shortcode['content'])) {
			$shortcode['content'] = self::get_shortcode_code($shortcode['content']);
		}

		return '<div class="su-generator-attr-container"><h5>' . __('Content', 'shortcodes-ultimate') . '</h5><textarea name="su-generator-content" id="su-generator-content" rows="5">' . esc_attr(str_replace(array('%prefix_', '__'), su_get_shortcode_prefix(), $shortcode['content'])) . '</textarea></div>';

	}

	/**
	 * Process AJAX request
	 */
	public static function settings()
	{
		self::access();
		// Param check
		if (empty($_REQUEST['shortcode']))
			wp_die(__('Shortcode not specified', 'shortcodes-ultimate'));
		// Request queried shortcode
		$shortcode = self::normalize_shortcode(su_get_shortcode(sanitize_key($_REQUEST['shortcode'])));
		// Call custom callback
		if (
			isset($shortcode['generator_callback']) &&
			is_callable($shortcode['generator_callback'])
		) {
			call_user_func($shortcode['generator_callback'], $shortcode);
			exit;
		}
		// Prepare skip-if-default option
		$skip = (get_option('su_option_skip') === 'on') ? ' su-generator-skip' : '';
		// Prepare actions
		$actions = apply_filters('su/generator/actions', array(
			'insert' => '<a href="javascript:void(0);" class="button button-primary button-large su-generator-insert"><i class="sui sui-check"></i> ' . __('Insert shortcode', 'shortcodes-ultimate') . '</a>',
			'copy'   => '<button type="button" class="button button-large su-generator-copy" data-label="' . esc_attr__( 'Copy shortcode', 'shortcodes-ultimate' ) . '" data-copied-label="' . esc_attr__( 'Copied', 'shortcodes-ultimate' ) . '" aria-label="' . esc_attr__( 'Copy shortcode', 'shortcodes-ultimate' ) . '"><i class="sui sui-copy" aria-hidden="true"></i><span class="su-generator-copy-label">' . esc_html__( 'Copy shortcode', 'shortcodes-ultimate' ) . '</span></button>',
			'reset'  => '<button type="button" class="button button-large su-generator-reset" aria-label="' . esc_attr__( 'Reset Settings', 'shortcodes-ultimate' ) . '"><i class="sui sui-undo" aria-hidden="true"></i>' . esc_html__( 'Reset Settings', 'shortcodes-ultimate' ) . '</button>',
		));
		$return = '<div class="su-generator-settings-body">';
		$return .= '<div class="su-generator-settings-fields">';
		$tabs = self::get_generator_tabs($shortcode);
		$first_tab = empty($tabs) ? '' : key($tabs);
		$tab_panels = array();

		foreach ($tabs as $tab_id => $tab) {
			$tab_panels[$tab_id] = '';
		}

		// Shortcode header
		$return .= '<div id="su-generator-breadcrumbs">';
		$return .= apply_filters('su/generator/breadcrumbs', '<a href="javascript:void(0);" class="su-generator-home" title="' . __('Click to return to the shortcodes list', 'shortcodes-ultimate') . '">' . __('All shortcodes', 'shortcodes-ultimate') . '</a> &rarr; <span>' . $shortcode['name'] . '</span> <small class="alignright">' . $shortcode['desc'] . '</small><div class="su-generator-clear"></div>');
		$return .= '</div>';
		// Shortcode note
		if (isset($shortcode['note'])) {
			$return .= '<div class="su-generator-note"><i class="sui sui-info-circle"></i><div class="su-generator-note-content">' . wpautop($shortcode['note']) . '</div></div>';
		}
		// Shortcode CTA
		if (isset($shortcode['generator_cta'])) {
			$return .= '<div class="su-generator-cta"><div class="su-generator-cta-content">' . $shortcode['generator_cta'] . '</div></div>';
		}
		// Shortcode has atts
		if (isset($shortcode['atts']) && count($shortcode['atts'])) {
			// Loop through shortcode parameters
			foreach ($shortcode['atts'] as $attr_name => $attr_info) {

				if (empty($tabs)) {
					$return .= self::render_generator_attribute($attr_name, $attr_info, $skip);
					continue;
				}

				$tab_id = isset($attr_info['tab']) ? sanitize_key($attr_info['tab']) : $first_tab;

				if (!isset($tab_panels[$tab_id])) {
					$tab_id = $first_tab;
				}

				$tab_panels[$tab_id] .= self::render_generator_attribute($attr_name, $attr_info, $skip);

			}
		}
		// Single shortcode (not closed)
		if ($shortcode['type'] == 'single')
			$return .= '<input type="hidden" name="su-generator-content" id="su-generator-content" value="false" />';
		// Wrapping shortcode
		else {

			if (empty($tabs)) {
				$return .= self::render_generator_content_field($shortcode);
			} else {
				$tab_panels[$first_tab] .= self::render_generator_content_field($shortcode);
			}

		}
		$return .= self::render_generator_tabs($tabs);
		$return .= self::render_generator_tab_panels($tabs, $tab_panels);
		$return .= '</div>';
		$return .= '<div class="su-generator-preview-panel"><div id="su-generator-preview"></div></div>';
		$return .= '</div>';
		$return .= '<div class="su-generator-actions su-generator-clearfix">' . implode(' ', array_values($actions)) . '</div>';
		echo $return;
		exit;
	}

	/**
	 * Process AJAX request and generate preview HTML
	 */
	public static function preview()
	{
		// Check nonce
		if (
			empty($_POST['nonce']) ||
			!wp_verify_nonce($_POST['nonce'], 'su_generator_preview')
		) {
			return;
		}
		// Check authentication
		self::access();
		// Output results
		do_action('su/generator/preview/before');
		$shortcode = wp_unslash($_POST['shortcode']);
		echo '<h5>' . __('Preview', 'shortcodes-ultimate') . '</h5>';
		echo apply_filters('su/generator/preview/output', do_shortcode($shortcode), $shortcode);
		echo '<div style="clear:both"></div>';
		do_action('su/generator/preview/after');
		die();
	}

	public static function access()
	{
		if (!self::access_check())
			wp_die(__('Access denied', 'shortcodes-ultimate'));
	}

	public static function access_check()
	{

		$required_capability = (string) get_option(
			'su_option_generator_access',
			'manage_options'
		);

		return current_user_can($required_capability);

	}

	public static function ajax_get_icons()
	{
		self::access();
		$icons = array();
		foreach (su_get_config('icons') as $icon) {
			$icons[] = '<i class="sui sui-' . $icon . '" title="' . $icon . '"></i>';
		}
		die(implode('', $icons));
	}

	public static function ajax_get_terms()
	{
		self::access();
		$args = array();
		if (isset($_REQUEST['tax']))
			$args['options'] = (array) self::get_terms(sanitize_key($_REQUEST['tax']));
		if (isset($_REQUEST['class']))
			$args['class'] = (string) sanitize_key($_REQUEST['class']);
		if (isset($_REQUEST['multiple']))
			$args['multiple'] = (bool) sanitize_key($_REQUEST['multiple']);
		if (isset($_REQUEST['size']))
			$args['size'] = (int) sanitize_key($_REQUEST['size']);
		if (isset($_REQUEST['noselect']))
			$args['noselect'] = (bool) sanitize_key($_REQUEST['noselect']);
		die(su_html_dropdown($args));
	}

	public static function ajax_get_taxonomies()
	{
		self::access();
		$args = array();
		$args['options'] = self::get_taxonomies();
		die(su_html_dropdown($args));
	}

	public static function ajax_search_posts()
	{
		self::access();

		$ids = array();

		if (isset($_REQUEST['ids'])) {
			$ids = is_array($_REQUEST['ids'])
				? $_REQUEST['ids']
				: explode(',', (string) wp_unslash($_REQUEST['ids']));

			$ids = array_filter(array_map('absint', $ids));
		}

		$args = array(
			'no_found_rows'          => true,
			'post_status'            => 'publish',
			'post_type'              => self::get_searchable_post_types(),
			'posts_per_page'         => 20,
			'suppress_filters'       => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		if (!empty($ids)) {
			$args['orderby'] = 'post__in';
			$args['post__in'] = $ids;
			$args['posts_per_page'] = count($ids);
		} else {
			$search = isset($_REQUEST['search'])
				? sanitize_text_field(wp_unslash($_REQUEST['search']))
				: '';

			if (strlen($search) < 2) {
				wp_send_json_success(array('results' => array()));
			}

			$args['s'] = $search;
		}

		$query = new WP_Query($args);
		$results = array();

		foreach ($query->posts as $post) {
			$results[] = self::format_post_search_result($post);
		}

		wp_send_json_success(array('results' => $results));
	}

	private static function get_searchable_post_types()
	{
		$post_types = array();

		foreach (get_post_types(array(), 'objects') as $post_type) {
			if (true === $post_type->show_ui) {
				$post_types[] = $post_type->name;
			}
		}

		return array_values(
			(array) apply_filters(
				'su/generator/search_posts/post_types',
				$post_types
			)
		);
	}

	private static function format_post_search_result($post)
	{
		$post_type = get_post_type_object($post->post_type);
		$post_type_label = $post_type ? $post_type->labels->singular_name : $post->post_type;
		$title = get_the_title($post);

		if ('' === $title) {
			$title = __('(no title)', 'shortcodes-ultimate');
		}

		return array(
			'value' => (string) $post->ID,
			'label' => sprintf(
				'%1$s (#%2$d, %3$s)',
				$title,
				$post->ID,
				$post_type_label
			),
		);
	}

	public static function ajax_search_users()
	{
		self::access();

		$ids = array();

		if (isset($_REQUEST['ids'])) {
			$ids = is_array($_REQUEST['ids'])
				? $_REQUEST['ids']
				: explode(',', (string) wp_unslash($_REQUEST['ids']));

			$ids = array_filter(array_map('absint', $ids));
		}

		$args = array(
			'fields' => 'all',
			'number' => 20,
		);

		if (!empty($ids)) {
			$args['include'] = $ids;
			$args['number'] = count($ids);
			$args['orderby'] = 'include';
		} else {
			$search = isset($_REQUEST['search'])
				? sanitize_text_field(wp_unslash($_REQUEST['search']))
				: '';

			if (strlen($search) < 2) {
				wp_send_json_success(array('results' => array()));
			}

			$args['search'] = '*' . $search . '*';
			$args['search_columns'] = array(
				'user_login',
				'user_nicename',
				'display_name',
				'user_email',
			);
		}

		$results = array();

		foreach (get_users($args) as $user) {
			$results[] = self::format_user_search_result($user);
		}

		wp_send_json_success(array('results' => $results));
	}

	private static function format_user_search_result($user)
	{
		$label = $user->display_name ? $user->display_name : $user->user_login;

		return array(
			'value' => (string) $user->ID,
			'label' => sprintf(
				'%1$s (%2$s, #%3$d)',
				$label,
				$user->user_login,
				$user->ID
			),
		);
	}

	public static function presets($actions)
	{
		ob_start();
		?>
		<div class="su-generator-presets alignright" data-shortcode="<?php echo sanitize_key($_REQUEST['shortcode']); ?>">
			<a href="javascript:void(0);" class="button button-large su-gp-button"><i class="sui sui-bars"></i> <?php _e('Presets', 'shortcodes-ultimate'); ?></a>
			<div class="su-gp-popup">
				<div class="su-gp-head">
					<a href="javascript:void(0);" class="button button-small button-primary su-gp-new"><?php _e('Save current settings as preset', 'shortcodes-ultimate'); ?></a>
				</div>
				<div class="su-gp-list">
					<?php self::presets_list(); ?>
				</div>
			</div>
		</div>
		<?php
		$actions['presets'] = ob_get_contents();
		ob_end_clean();
		return $actions;
	}

	public static function presets_list($shortcode = false)
	{
		// Shortcode isn't specified, try to get it from $_REQUEST
		if (!$shortcode)
			$shortcode = $_REQUEST['shortcode'];
		// Shortcode name is still doesn't exists, exit
		if (!$shortcode)
			return;
		// Shortcode has been specified, sanitize it
		$shortcode = sanitize_key($shortcode);
		// Get presets
		$presets = get_option('su_presets_' . $shortcode);
		// Presets has been found
		if (is_array($presets) && count($presets)) {
			// Print the presets
			foreach ($presets as $preset) {
				echo '<span data-id="' . $preset['id'] . '"><em>' . stripslashes($preset['name']) . '</em> <i class="sui sui-times"></i></span>';
			}
			// Hide default text
			echo sprintf('<b style="display:none">%s</b>', __('Presets not found', 'shortcodes-ultimate'));
		}
		// Presets doesn't found
		else
			echo sprintf('<b>%s</b>', __('Presets not found', 'shortcodes-ultimate'));
	}

	public static function ajax_add_preset()
	{
		self::access();
		// Check incoming data
		if (empty($_POST['id']))
			return;
		if (empty($_POST['name']))
			return;
		if (empty($_POST['settings']))
			return;
		if (empty($_POST['shortcode']))
			return;
		// Check Nonce
		if (
			empty($_POST['nonce']) ||
			!is_string($_POST['nonce']) ||
			!wp_verify_nonce($_POST['nonce'], 'su_generator_preset')
		) {
			return;
		}
		// Clean-up incoming data
		$id = sanitize_key($_POST['id']);
		$name = sanitize_text_field($_POST['name']);
		$shortcode = sanitize_key($_POST['shortcode']);
		// Validate and sanitize settings
		$settings = is_array($_POST['settings']) ? stripslashes_deep($_POST['settings']) : array();
		$settings = array_map('wp_kses_post', $settings);
		// Prepare option name
		$option = 'su_presets_' . $shortcode;
		// Get the existing presets
		$current = get_option($option);
		// Create array with new preset
		$new = array(
			'id' => $id,
			'name' => $name,
			'settings' => $settings,
		);
		// Add new array to the option value
		if (!is_array($current))
			$current = array();
		$current[$id] = $new;
		// Save updated option
		update_option($option, $current);
		// Clear cache
		delete_transient('su/generator/settings/' . $shortcode);
	}

	public static function ajax_remove_preset()
	{
		self::access();
		// Check incoming data
		if (empty($_POST['id']))
			return;
		if (empty($_POST['shortcode']))
			return;
		// Check Nonce
		if (
			empty($_POST['nonce']) ||
			!is_string($_POST['nonce']) ||
			!wp_verify_nonce($_POST['nonce'], 'su_generator_preset')
		) {
			return;
		}
		// Clean-up incoming data
		$id = sanitize_key($_POST['id']);
		$shortcode = sanitize_key($_POST['shortcode']);
		// Prepare option name
		$option = 'su_presets_' . $shortcode;
		// Get the existing presets
		$current = get_option($option);
		// Check that preset is exists
		if (!is_array($current) || empty($current[$id]))
			return;
		// Remove preset
		unset($current[$id]);
		// Save updated option
		update_option($option, $current);
		// Clear cache
		delete_transient('su/generator/settings/' . $shortcode);
	}

	public static function ajax_get_preset()
	{
		self::access();
		// Check incoming data
		if (empty($_GET['id']))
			return;
		if (empty($_GET['shortcode']))
			return;
		// Check Nonce
		if (
			empty($_GET['nonce']) ||
			!is_string($_GET['nonce']) ||
			!wp_verify_nonce($_GET['nonce'], 'su_generator_preset')
		) {
			return;
		}
		// Clean-up incoming data
		$id = sanitize_key($_GET['id']);
		$shortcode = sanitize_key($_GET['shortcode']);
		// Default data
		$data = array();
		// Get the existing presets
		$presets = get_option('su_presets_' . $shortcode);
		// Check that preset is exists
		if (is_array($presets) && isset($presets[$id]['settings']))
			$data = $presets[$id]['settings'];
		// Print results
		die(json_encode($data));
	}

	/**
	 * Helper function to create shortcode code with default settings.
	 *
	 * Example output: "[su_button color="#ff0000" ... ] Click me [/su_button]".
	 *
	 * @param mixed   $args Array with settings
	 * @since  5.0.0
	 * @return string      Shortcode code
	 */
	public static function get_shortcode_code($args)
	{

		$defaults = array(
			'id' => '',
			'number' => 1,
			'nested' => false,
		);

		// Accept shortcode ID as a string
		if (is_string($args)) {
			$args = array('id' => $args);
		}

		$args = wp_parse_args($args, $defaults);

		// Check shortcode ID
		if (empty($args['id'])) {
			return '';
		}

		// Get shortcode data
		$shortcode = self::normalize_shortcode(su_get_shortcode($args['id']));

		// Prepare shortcode prefix
		$prefix = get_option('su_option_prefix');

		// Prepare attributes container
		$attributes = '';

		// Loop through attributes
		foreach ($shortcode['atts'] as $attr_id => $attribute) {

			// Skip hidden attributes
			if (isset($attribute['hidden']) && $attribute['hidden']) {
				continue;
			}

			// Add attribute
			$attributes .= sprintf(' %s="%s"', esc_html($attr_id), esc_attr($attribute['default']));

		}

		// Create opening tag with attributes
		$output = "[{$prefix}{$args['id']}{$attributes}]";

		// Indent nested shortcodes
		if ($args['nested']) {
			$output = "\t" . $output;
		}

		// Insert shortcode content
		if (isset($shortcode['content'])) {

			if (is_string($shortcode['content'])) {
				$output .= $shortcode['content'];
			}

			// Create complex content
			else if (is_array($shortcode['content']) && $args['id'] !== $shortcode['content']['id']) {

				$shortcode['content']['nested'] = true;
				$output .= self::get_shortcode_code($shortcode['content']);

			}

		}

		// Add closing tag
		if (isset($shortcode['type']) && $shortcode['type'] === 'wrap') {
			$output .= "[/{$prefix}{$args['id']}]";
		}

		// Repeat shortcode
		if ($args['number'] > 1) {
			$output = implode("\n", array_fill(0, $args['number'], $output));
		}

		// Add line breaks around nested shortcodes
		if ($args['nested']) {
			$output = "\n{$output}\n";
		}

		return $output;

	}

	/**
	 * Helper function to check if all available addons were activated.
	 *
	 * @since  5.0.5
	 * @return boolean True if all addons active, False otherwise.
	 */
	public static function is_addons_active()
	{
		return false;
	}

	/**
	 * Get available shortcodes, skipping deprecated ones.
	 *
	 * @since  5.0.5
	 * @return array Available shortcodes data.
	 */
	public static function get_shortcodes()
	{

		$shortcodes = su_get_all_shortcodes();

		if (get_option('su_option_hide_deprecated')) {

			$shortcodes = array_filter(
				$shortcodes,
				array(__CLASS__, 'filter_deprecated_shortcodes')
			);

		}

		foreach ($shortcodes as $shortcode_id => $shortcode) {
			$shortcodes[$shortcode_id] = self::normalize_shortcode($shortcode);
		}

		return $shortcodes;

	}

	public static function get_shortcodes_grouped()
	{

		$result = [];
		$groups = su_get_groups();
		$shortcodes = self::get_shortcodes();

		foreach ($groups as $group => $group_title) {
			$group_shortcodes = array_filter($shortcodes, function ($shortcode) use ($group) {
				return $shortcode['group'] === $group;
			});

			if (count($group_shortcodes)) {
				$result[$group] = [
					'id' => $group,
					'title' => $group_title,
					'shortcodes' => $group_shortcodes,
				];
			}
		}

		return $result;
	}

	/**
	 * Filter shortcodes and skip deprecated ones.
	 *
	 * @since  5.0.5
	 * @param array   $shortcode A single shortcode data.
	 * @return boolean            False if shortcode deprecated, True otherwise.
	 */
	public static function filter_deprecated_shortcodes($shortcode)
	{
		return !isset($shortcode['deprecated']);
	}

	/**
	 * Get list of taxonomies as key-value pairs.
	 *
	 * @since  5.0.5
	 * @return array List of taxonomies.
	 */
	public static function get_taxonomies()
	{

		$taxes = array();

		foreach ((array) get_taxonomies('', 'objects') as $tax) {
			$taxes[$tax->name] = $tax->label;
		}

		return $taxes;

	}

	/**
	 * Get list of terms as key-value pairs.
	 *
	 * @since  5.0.5
	 * @return array List of terms.
	 */
	public static function get_terms($tax = 'category', $key = 'id')
	{

		$terms = array();

		if ($key === 'id') {

			foreach ((array) get_terms($tax, array('hide_empty' => false)) as $term) {
				$terms[$term->term_id] = $term->name;
			}

		} elseif ($key === 'slug') {

			foreach ((array) get_terms($tax, array('hide_empty' => false)) as $term) {
				$terms[$term->slug] = $term->name;
			}

		}

		return $terms;

	}

}

new Su_Generator;

class Shortcodes_Ultimate_Generator extends Su_Generator
{
	function __construct()
	{
		parent::__construct();
	}
}
