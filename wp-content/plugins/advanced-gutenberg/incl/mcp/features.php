<?php

/** Typed settings for Blocks admin and editor features. @since 3.7.6 */

namespace PublishPress\Blocks\Mcp;

use AdvancedGutenbergMain;
use PublishPress\Blocks\Utilities;

\defined('ABSPATH') || exit;

class Features
{
    public static function schemas(): array
    {
        $map = ['type' => 'object', 'minProperties' => 1, 'maxProperties' => 200, 'additionalProperties' => ['type' => 'boolean']];
        $legacy = [];
        foreach (AdvancedGutenbergMain::legacyBlocksMap() as $key => $label) {
            $legacy[$key] = ['type' => 'boolean', 'description' => $label];
        }
        return [
            'extra-blocks' => $map, 'controls-blocks' => $map,
            'legacy-blocks' => array_merge(Registry::object($legacy), ['minProperties' => 1]),
            'editor-features' => array_merge(Registry::object([
                'disable_block_adding' => ['type' => 'boolean', 'description' => 'Disable all block adding for this role.'],
                'disable_pattern_directory' => ['type' => 'boolean', 'description' => 'Disable Pattern Directory for this role.'],
                'disable_openverse' => ['type' => 'boolean', 'description' => 'Disable Openverse for this role.'],
            ]), ['minProperties' => 1]),
            'form-email' => array_merge(Registry::object([
                'contact_form_sender_name' => ['type' => 'string', 'maxLength' => 200],
                'contact_form_sender_email' => ['type' => 'string', 'anyOf' => [['type' => 'string', 'enum' => ['']], ['type' => 'string', 'format' => 'email']]],
                'contact_form_email_title' => ['type' => 'string', 'maxLength' => 300],
                'contact_form_email_receiver' => ['type' => 'string', 'anyOf' => [['type' => 'string', 'enum' => ['']], ['type' => 'string', 'format' => 'email']]],
            ]), ['minProperties' => 1]),
            'recaptcha' => array_merge(Registry::object([
                'recaptcha_enable' => ['type' => 'boolean'], 'recaptcha_language' => ['type' => 'string', 'maxLength' => 20],
                'recaptcha_theme' => ['type' => 'string', 'enum' => ['', 'light', 'dark', 'invisible']],
            ]), ['minProperties' => 1]),
            'content-display-thumbnail' => array_merge(Registry::object([
                'id' => ['type' => 'integer', 'minimum' => 0], 'url' => ['type' => 'string', 'anyOf' => [['type' => 'string', 'enum' => ['']], ['type' => 'string', 'format' => 'uri']]],
            ]), ['minProperties' => 1]),
        ];
    }

    public static function register()
    {
        $selector = ['feature' => ['type' => 'string', 'enum' => array_keys(self::schemas())], 'role' => ['type' => 'string']];
        Registry::add(
            'get-feature-settings',
            __('Discover Blocks feature settings', 'advanced-gutenberg'),
            __('Read supported settings and exact schemas for Extra Blocks switches, per-block controls, legacy blocks, role Editor Features, form email, reCAPTCHA or Content Display thumbnail. editor-features requires role. API keys are never returned.', 'advanced-gutenberg'),
            Registry::object($selector, ['feature']),
            [__CLASS__, 'get'],
            [__CLASS__, 'canAccess']
        );
        $variants = [];
        foreach (self::schemas() as $feature => $schema) {
            $variants[] = Registry::object(
                ['feature' => ['type' => 'string', 'enum' => [$feature]], 'role' => ['type' => 'string'], 'settings' => $schema, 'dry_run' => ['type' => 'boolean', 'default' => false]],
                $feature === 'editor-features' ? ['feature', 'settings', 'role'] : ['feature', 'settings']
            );
        }
        Registry::add(
            'update-feature-settings',
            __('Update Blocks feature settings', 'advanced-gutenberg'),
            __('Change only supplied settings in one discovered feature. Use dry_run to validate and return proposed settings without saving. Preserves other roles and values, validates every field before saving and rejects unknown blocks or Pro-only blocks in Free. Does not accept API keys or arbitrary options.', 'advanced-gutenberg'),
            ['type' => 'object', 'oneOf' => $variants],
            [__CLASS__, 'update'],
            [__CLASS__, 'canAccess'],
            true
        );
        Registry::add(
            'update-post-editor-settings',
            __('Update post editor preferences', 'advanced-gutenberg'),
            __('Set one editable post editor width or columns visual guide. Empty strings inherit global settings. Other post metadata and content are preserved.', 'advanced-gutenberg'),
            Registry::object(['post_id' => ['type' => 'integer', 'minimum' => 1],
                'editor_width' => ['type' => 'string', 'enum' => ['', 'default', 'large', 'full']],
                'columns_visual_guide' => ['type' => 'string', 'enum' => ['', 'enable', 'disable']]], ['post_id']),
            [__CLASS__, 'postPreferences'],
            [Abilities::class, 'canInspectPostBlocks'],
            true
        );
        Registry::add(
            'list-form-submissions',
            __('Read saved form submissions', 'advanced-gutenberg'),
            __('Read saved Contact Form or Newsletter names, email addresses and messages for authorized export. Requires native settings/export capabilities and Extra Blocks enabled. Results are paginated; no email is sent.', 'advanced-gutenberg'),
            Registry::object(array_merge(Registry::page(), ['form' => ['type' => 'string', 'enum' => ['contact', 'newsletter']]]), ['form']),
            [__CLASS__, 'submissions'],
            [Operations::class, 'canManageConfiguration']
        );
        Registry::add(
            'get-form-submission-counts',
            __('Count saved form submissions', 'advanced-gutenberg'),
            __('Read counts of saved Contact Form and Newsletter submissions. Does not expose any submitted names, email addresses or message content.', 'advanced-gutenberg'),
            Registry::object(),
            [__CLASS__, 'submissionCounts'],
            [Operations::class, 'canManageConfiguration']
        );
    }

    public static function canAccess(array $input = []): bool
    {
        if (!Operations::canManageSettings()) {
            return false;
        }
        if (($input['feature'] ?? '') === 'controls-blocks') {
            return Operations::canManageControls();
        }
        return ($input['feature'] ?? '') !== 'extra-blocks' || Operations::canManageConfiguration();
    }

    public static function get(array $input)
    {
        $feature = $input['feature'];
        $schema = self::schemas()[$feature];
        $values = [];
        if ($feature === 'editor-features') {
            if (empty($input['role']) || !get_role($input['role'])) {
                return Registry::error(__('Choose an existing role.', 'advanced-gutenberg'));
            }
            $stored = (array) get_option('advgb_settings', []);
            foreach ($schema['properties'] as $key => $field) {
                $values[$key] = !empty($stored['block_features_settings'][$input['role']][$key]);
            }
        } elseif ($feature === 'extra-blocks' || $feature === 'controls-blocks') {
            $stored = (array) get_option($feature === 'extra-blocks' ? 'advgb_blocks_enabled' : 'advgb_block_controls', []);
            foreach (Operations::knownBlocks() as $name) {
                if ($feature === 'extra-blocks' && strpos($name, 'advgb/') !== 0) {
                    continue;
                }
                $values[$name] = $feature === 'extra-blocks' ? (!isset($stored[$name]) || (bool) $stored[$name]) : !in_array($name, $stored['inactive_blocks'] ?? [], true);
            }
        } elseif ($feature === 'legacy-blocks') {
            $values = AdvancedGutenbergMain::getLegacyBlocksState();
        } elseif ($feature === 'content-display-thumbnail') {
            $stored = (array) get_option('advgb_settings', []);
            $values = $stored['rp_default_thumb'] ?? ['id' => 0, 'url' => ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/blocks/recent-posts/recent-post-default.png'];
        } else {
            $stored = (array) get_option($feature === 'form-email' ? 'advgb_email_sender' : 'advgb_recaptcha_config', []);
            foreach ($schema['properties'] as $key => $field) {
                if (isset($stored[$key])) {
                    $values[$key] = $field['type'] === 'boolean' ? (bool) $stored[$key] : $stored[$key];
                }
            }
        }
        return ['feature' => $feature, 'settings' => (object) $values, 'schema' => $schema,
            'roles' => array_keys(wp_roles()->roles), 'pro_active' => defined('ADVANCED_GUTENBERG_PRO_LOADED')];
    }

    public static function update(array $input)
    {
        $current = self::get($input);
        if (is_wp_error($current)) {
            return $current;
        }
        $feature = $input['feature'];
        $values = $input['settings'];
        $valid = Registry::validate($values, $current['schema']);
        if (is_wp_error($valid)) {
            return $valid;
        }
        foreach ($values as $key => $value) {
            if (($current['schema']['properties'][$key]['type'] ?? 'boolean') === 'boolean' && !is_bool($value)) {
                return Registry::error(__('Use true or false for checkbox settings.', 'advanced-gutenberg'));
            }
        }
        if ($feature === 'extra-blocks' || $feature === 'controls-blocks') {
            foreach ($values as $name => $enabled) {
                if (!isset($current['settings']->$name) || (!defined('ADVANCED_GUTENBERG_PRO_LOADED') && in_array($name, array_column(Utilities::getProBlocks(), 'name'), true))) {
                    return Registry::error(__('Unknown or Pro-only block.', 'advanced-gutenberg'));
                }
            }
            if ($feature === 'extra-blocks') {
                if (empty($input['dry_run'])) {
                    update_option('advgb_blocks_enabled', array_merge((array) get_option('advgb_blocks_enabled', []), $values), false);
                }
            } else {
                $stored = (array) get_option('advgb_block_controls', []);
                foreach ($values as $name => $enabled) {
                    foreach (['active_blocks', 'inactive_blocks'] as $list) {
                        $stored[$list] = array_values(array_diff($stored[$list] ?? [], [$name]));
                    }
                    $stored[$enabled ? 'active_blocks' : 'inactive_blocks'][] = $name;
                }
                if (empty($input['dry_run'])) {
                    update_option('advgb_block_controls', $stored, false);
                }
            }
        } elseif ($feature === 'editor-features' || $feature === 'content-display-thumbnail') {
            $stored = (array) get_option('advgb_settings', []);
            if ($feature === 'editor-features') {
                $stored['block_features_settings'][$input['role']] = array_merge($stored['block_features_settings'][$input['role']] ?? [], $values);
            } else {
                if (!empty($values['id']) && (get_post_type($values['id']) !== 'attachment' || !current_user_can('edit_post', $values['id']))) {
                    return Registry::error(__('Choose an editable media attachment.', 'advanced-gutenberg'));
                }
                if (isset($values['url'])) {
                    $values['url'] = esc_url_raw($values['url']);
                }
                $stored['rp_default_thumb'] = array_merge($stored['rp_default_thumb'] ?? [], $values);
            }
            if (empty($input['dry_run'])) {
                update_option('advgb_settings', $stored);
            }
        } elseif ($feature === 'legacy-blocks') {
            if (empty($input['dry_run'])) {
                update_option('advgb_legacy_blocks', array_merge((array) get_option('advgb_legacy_blocks', []), $values), false);
            }
            if (empty($input['dry_run'])) {
                update_option('advgb_legacy_settings_migrated', 1, false);
            }
        } else {
            $option = $feature === 'form-email' ? 'advgb_email_sender' : 'advgb_recaptcha_config';
            $stored = (array) get_option($option, []);
            if ($feature === 'recaptcha' && !empty($values['recaptcha_enable']) && (empty($stored['recaptcha_site_key']) || empty($stored['recaptcha_secret_key']))) {
                return Registry::error(__('Configure reCAPTCHA keys in WordPress before enabling it.', 'advanced-gutenberg'));
            }
            foreach ($values as $key => $value) {
                $values[$key] = is_bool($value) ? $value : sanitize_text_field($value);
                $stored[$key] = is_bool($value) ? (int) $value : $values[$key];
            }
            if (empty($input['dry_run'])) {
                update_option($option, $stored);
            }
        }
        if (!empty($input['dry_run'])) {
            $current['settings'] = (object) array_merge((array) $current['settings'], $values);
            return $current;
        }
        return self::get($input);
    }

    public static function postPreferences(array $input)
    {
        $meta = [];
        foreach (['editor_width', 'columns_visual_guide'] as $name) {
            if (isset($input[$name])) {
                $meta['advgb_blocks_' . $name] = $input[$name];
            }
        }
        if (!$meta) {
            return Registry::error(__('Supply at least one editor preference.', 'advanced-gutenberg'));
        }
        $response = Content::updateNative(get_post($input['post_id']), ['meta' => $meta]);
        return is_wp_error($response) ? $response : ['post_id' => $input['post_id'], 'preferences' => (object) $meta];
    }

    public static function submissions(array $input): array
    {
        $stored = array_values((array) get_option($input['form'] === 'contact' ? 'advgb_contacts_saved' : 'advgb_newsletter_saved', []));
        $perPage = $input['per_page'] ?? 20;
        $page = $input['page'] ?? 1;
        $items = [];
        foreach (array_slice($stored, ($page - 1) * $perPage, $perPage) as $entry) {
            $entry = (array) $entry;
            $item = [];
            foreach ($input['form'] === 'contact' ? ['date', 'name', 'email', 'msg'] : ['date', 'fname', 'lname', 'email'] as $field) {
                if (isset($entry[$field]) && is_scalar($entry[$field])) {
                    $item[$field] = (string) $entry[$field];
                }
            }
            $items[] = (object) $item;
        }
        return ['form' => $input['form'], 'submissions' => $items, 'page' => $page,
            'total_pages' => (int) ceil(count($stored) / $perPage)];
    }

    public static function submissionCounts(): array
    {
        return ['contact' => count((array) get_option('advgb_contacts_saved', [])),
            'newsletter' => count((array) get_option('advgb_newsletter_saved', []))];
    }

    public static function visibilitySchema(): array
    {
        $variants = [];
        foreach (Presets::ruleSchemas() as $type => $fields) {
            $variants[] = Registry::object(array_merge(['control' => ['type' => 'string', 'enum' => [$type]], 'enabled' => ['type' => 'boolean'], 'id' => ['type' => 'string'], 'expanded' => ['type' => 'boolean']], $fields), ['control']);
        }
        $variants[] = Registry::object(['control' => ['type' => 'string', 'enum' => ['presets']], 'enabled' => ['type' => 'boolean'], 'id' => ['type' => 'string'], 'expanded' => ['type' => 'boolean'], 'selected' => Registry::strings(), 'logic' => ['type' => 'string', 'enum' => ['all', 'any']]], ['control', 'selected']);
        return ['type' => 'array', 'maxItems' => 30, 'items' => ['oneOf' => $variants]];
    }

    public static function validateVisibility(array $controls)
    {
        foreach ($controls as $control) {
            if (!isset($control['control'])) {
                return Registry::error(__('Each visibility rule needs a control type.', 'advanced-gutenberg'));
            }
            $type = $control['control'];
            if ($type === 'presets') {
                $schema = Registry::object(['control' => ['type' => 'string', 'enum' => ['presets']], 'enabled' => ['type' => 'boolean'], 'id' => ['type' => 'string'], 'expanded' => ['type' => 'boolean'],
                    'selected' => Registry::strings(), 'logic' => ['type' => 'string', 'enum' => ['all', 'any']]], ['control', 'selected']);
                $valid = Registry::validate($control, $schema);
                if (is_wp_error($valid)) {
                    return $valid;
                }
                $presets = (array) get_option('advgb_block_control_presets', []);
                foreach ($control['selected'] as $id) {
                    if (!isset($presets[$id])) {
                        return Registry::error(__('Unknown visibility preset ID.', 'advanced-gutenberg'));
                    }
                }
            } else {
                unset($control['control']);
                $control['type'] = $type;
                $valid = Presets::validateSets([['rules' => [$control]]]);
                if (is_wp_error($valid)) {
                    return $valid;
                }
            }
        }
        return true;
    }
}
