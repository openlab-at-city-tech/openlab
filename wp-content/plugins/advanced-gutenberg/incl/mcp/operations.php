<?php

declare(strict_types=1);

namespace PublishPress\Blocks\Mcp;

use PublishPress\Blocks\Configuration;
use PublishPress\Blocks\Utilities;
use WP_Block_Type_Registry;
use WP_Error;

\defined('ABSPATH') || exit;

/**
 * Typed Blocks settings and permissions operations for the Abilities API.
 *
 * @author PublishPress
 * @copyright Copyright (c) 2026, PublishPress
 * @license GPL v2 or later
 * @since 3.7.6
 */
class Operations
{
    /** Register actions without filtering by the current request identity. @since 3.7.6 */
    public static function registerAbilities()
    {
        $settings = self::settingsSchema();
        self::register(
            'get-plugin-settings',
            __('Get Blocks plugin settings', 'advanced-gutenberg'),
            __('Read Blocks Dashboard feature switches and non-secret Settings values, with labels and exact setting names. These switches enable whole features, not role-specific block permissions or individual block defaults.', 'advanced-gutenberg'),
            'getSettings',
            [],
            [],
            'canManageSettings'
        );
        self::register(
            'update-plugin-settings',
            __('Update Blocks plugin settings', 'advanced-gutenberg'),
            __('Change supplied Blocks Dashboard feature switches or non-secret Settings values. Unspecified fields are preserved. Use get-plugin-settings first to identify the setting. API keys and mail configuration are not supported.', 'advanced-gutenberg'),
            'updateSettings',
            ['settings' => ['type' => 'object', 'properties' => $settings, 'minProperties' => 1, 'additionalProperties' => false]],
            ['settings'],
            'canManageSettings',
            true
        );
        self::register(
            'get-block-permissions',
            __('Get role block permissions', 'advanced-gutenberg'),
            __('Read Block Permissions (Block Access): enabled and disabled editor blocks for one existing role. Returns known block names and role choices. This controls block availability, not publishing capabilities.', 'advanced-gutenberg'),
            'getPermissions',
            ['role' => ['type' => 'string', 'minLength' => 1]],
            ['role'],
            'canManagePermissions'
        );
        self::register(
            'update-block-permissions',
            __('Update role block permissions', 'advanced-gutenberg'),
            __('Enable or disable specified known editor blocks for one role on the Block Permissions screen. Preserves other roles and blocks. Unknown roles and blocks are rejected. This does not change WordPress role capabilities or existing content.', 'advanced-gutenberg'),
            'updatePermissions',
            [
                'role' => ['type' => 'string', 'minLength' => 1],
                'blocks' => ['type' => 'object', 'minProperties' => 1, 'maxProperties' => 200, 'additionalProperties' => ['type' => 'boolean']],
            ],
            ['role', 'blocks'],
            'canManagePermissions',
            true
        );
        self::register(
            'get-block-controls',
            __('Get Block Controls settings', 'advanced-gutenberg'),
            __('Read available Block Controls visibility rule types and their global enabled state. Includes per-block active/inactive lists. These are global controls; saved visibility presets use list-control-presets.', 'advanced-gutenberg'),
            'getControls',
            [],
            [],
            'canManageControls'
        );
        $controls = [];
        foreach (self::controlNames() as $name) {
            $controls[$name] = ['type' => 'boolean'];
        }
        self::register(
            'update-block-controls',
            __('Update Block Controls settings', 'advanced-gutenberg'),
            __('Enable or disable supplied global visibility rule types on Block Controls. Preserves unspecified controls and per-block lists. Does not change individual posts or saved preset definitions.', 'advanced-gutenberg'),
            'updateControls',
            ['controls' => ['type' => 'object', 'properties' => $controls, 'minProperties' => 1, 'additionalProperties' => false]],
            ['controls'],
            'canManageControls',
            true
        );
        self::register(
            'update-block-configuration',
            __('Update Extra Blocks defaults', 'advanced-gutenberg'),
            __('Change supplied default fields for one Extra Blocks type, preserving other defaults. First use get-block-configuration for exact block keys, field labels, types, choices and ranges. Affects newly inserted blocks only, not existing post content.', 'advanced-gutenberg'),
            'updateConfiguration',
            [
                'block' => ['type' => 'string', 'minLength' => 1],
                'settings' => ['type' => 'object', 'minProperties' => 1, 'maxProperties' => 200, 'additionalProperties' => ['type' => ['string', 'number', 'boolean']]],
            ],
            ['block', 'settings'],
            'canManageConfiguration',
            true
        );
    }

    /** Register one explicitly scoped operation. @since 3.7.6 */
    private static function register($name, $label, $description, $method, array $properties, array $required, $permission, $write = false)
    {
        if ($write) {
            $properties['dry_run'] = ['type' => 'boolean', 'default' => false];
            $description .= ' ' . __('Use dry_run to validate and return proposed values without saving.', 'advanced-gutenberg');
        }
        wp_register_ability('publishpress-blocks/' . $name, [
            'label' => $label,
            'description' => $description,
            'category' => 'publishpress',
            'input_schema' => Registry::object($properties, $required),
            'output_schema' => self::outputSchema($method),
            'execute_callback' => Registry::guard([__CLASS__, $method], Registry::object($properties, $required)),
            'permission_callback' => Registry::guard([__CLASS__, $permission], Registry::object($properties, $required), true),
            'meta' => [
                'public' => false, 'show_in_rest' => false, 'mcp' => ['public' => false],
                'annotations' => ['readonly' => !$write, 'destructive' => $write, 'idempotent' => true],
            ],
        ]);
    }

    /** Describe result fields for clients and output validation. @since 3.7.6 */
    private static function outputSchema(string $method): array
    {
        $object = ['type' => 'object'];
        $strings = ['type' => 'array', 'items' => ['type' => 'string']];
        if (in_array($method, ['getSettings', 'updateSettings'], true)) {
            $properties = ['settings' => $object, 'fields' => $object];
        } elseif (in_array($method, ['getPermissions', 'updatePermissions'], true)) {
            $properties = ['role' => ['type' => 'string'], 'permissions' => $object, 'known_blocks' => $strings, 'roles' => $strings];
        } elseif (in_array($method, ['getControls', 'updateControls'], true)) {
            $properties = ['controls' => $object, 'active_blocks' => $strings, 'inactive_blocks' => $strings];
        } else {
            $properties = ['block' => ['type' => 'string'], 'settings' => $object];
        }
        return ['type' => 'object', 'properties' => $properties, 'required' => array_keys($properties), 'additionalProperties' => false];
    }

    /** Native Settings page and save capabilities. @since 3.7.6 */
    public static function canManageSettings()
    {
        return current_user_can('manage_options') && current_user_can('activate_plugins');
    }

    /** Retain the native Block Permissions screen/save gate. @since 3.7.6 */
    public static function canManagePermissions()
    {
        return self::canManageSettings() && current_user_can('administrator')
            && Utilities::settingIsEnabled('enable_block_access');
    }

    /** Retain the native Block Controls screen/save gates. @since 3.7.6 */
    public static function canManageControls()
    {
        return self::canManageSettings() && current_user_can('administrator')
            && Utilities::settingIsEnabled('block_controls');
    }

    /** Extra Blocks must be enabled. @since 3.7.6 */
    public static function canManageConfiguration()
    {
        return self::canManageSettings() && Utilities::settingIsEnabled('enable_advgb_blocks');
    }

    /** Explicit settings whitelist; credentials are never returned. @since 3.7.6 */
    private static function settingsSchema(): array
    {
        $labels = [
            'block_controls' => 'Dashboard: Block Controls visibility rules',
            'enable_block_access' => 'Dashboard: Block Permissions (Block Access)',
            'enable_block_usage' => 'Dashboard: Block Usage',
            'enable_custom_styles' => 'Dashboard: Block Styles',
            'enable_advgb_blocks' => 'Dashboard: Extra Blocks',
            'reusable_blocks' => 'Dashboard: Reusable Blocks',
            'auto_insert_blocks' => 'Dashboard: Auto Insert Blocks',
            'enable_post_notes' => 'Dashboard: Post Notes',
            'gallery_lightbox' => 'Settings > Legacy: gallery lightbox',
        ];
        if (defined('ADVANCED_GUTENBERG_PRO_LOADED')) {
            $labels['enable_core_blocks_features'] = 'Dashboard: Pro core block features';
        }
        $schema = [];
        foreach ($labels as $name => $description) {
            $schema[$name] = ['type' => 'boolean', 'description' => $description];
        }
        $schema['post_notes_block_toolbar'] = ['type' => 'boolean', 'description' => 'Settings > Post Notes: show the block toolbar button'];
        $schema['blocks_icon_color'] = ['type' => 'string', 'pattern' => '^#[a-fA-F0-9]{6}$', 'description' => 'Settings > Extra Blocks: block icon color'];
        $schema['gallery_lightbox_caption'] = ['type' => 'integer', 'enum' => [0, 1, 2], 'description' => 'Legacy gallery caption: 0 disabled, 1 bottom, 2 overlay.'];
        return $schema;
    }

    /** Return effective defaults separately from field definitions. @since 3.7.6 */
    public static function getSettings(): array
    {
        $stored = (array) get_option('advgb_settings', []);
        $values = [];
        foreach (self::settingsSchema() as $name => $field) {
            if ($field['type'] === 'boolean') {
                $values[$name] = Utilities::settingIsEnabled($name);
            } elseif ($field['type'] === 'integer') {
                $values[$name] = isset($stored[$name]) ? (int) $stored[$name] : 1;
            } else {
                $values[$name] = isset($stored[$name]) ? (string) $stored[$name] : '#655997';
            }
        }
        return ['settings' => (object) $values, 'fields' => (object) self::settingsSchema()];
    }

    /** Persist only validated supplied fields. @since 3.7.6 */
    public static function updateSettings(array $input)
    {
        $schema = ['type' => 'object', 'properties' => self::settingsSchema(), 'additionalProperties' => false, 'minProperties' => 1];
        $valid = rest_validate_value_from_schema($input['settings'], $schema, 'settings');
        if (is_wp_error($valid)) {
            return $valid;
        }
        $stored = (array) get_option('advgb_settings', []);
        foreach ($input['settings'] as $name => $value) {
            $type = self::settingsSchema()[$name]['type'];
            if (($type === 'boolean' && !is_bool($value)) || ($type === 'integer' && !is_int($value)) || ($type === 'string' && !is_string($value))) {
                return self::invalid(__('Use the exact setting type returned by get-plugin-settings.', 'advanced-gutenberg'));
            }
        }
        foreach ($input['settings'] as $name => $value) {
            $stored[$name] = is_bool($value) ? (int) $value : (is_int($value) ? $value : sanitize_hex_color($value));
        }
        if (!empty($input['dry_run'])) {
            $result = self::getSettings();
            $result['settings'] = (object) array_merge((array) $result['settings'], $input['settings']);
            return $result;
        }
        update_option('advgb_settings', $stored);
        return self::getSettings();
    }

    /** Names shared with the native Block Controls form. @since 3.7.6 */
    private static function controlNames(): array
    {
        return ['schedule', 'user_role', 'device_type', 'device_width', 'archive', 'page', 'browser_device',
            'operating_system', 'cookie', 'user_meta', 'post_meta', 'query_string', 'capabilities', 'presets'];
    }

    /** Read global controls, never individual user metadata. @since 3.7.6 */
    public static function getControls(): array
    {
        $stored = (array) get_option('advgb_block_controls', []);
        $controls = [];
        foreach (self::controlNames() as $name) {
            $controls[$name] = isset($stored['controls'][$name]) ? (bool) $stored['controls'][$name] : true;
        }
        return [
            'controls' => (object) $controls,
            'active_blocks' => isset($stored['active_blocks']) ? (array) $stored['active_blocks'] : [],
            'inactive_blocks' => isset($stored['inactive_blocks']) ? (array) $stored['inactive_blocks'] : [],
        ];
    }

    /** Preserve controls omitted by the client. @since 3.7.6 */
    public static function updateControls(array $input)
    {
        foreach ($input['controls'] as $name => $value) {
            if (!in_array($name, self::controlNames(), true) || !is_bool($value)) {
                return self::invalid(__('Unknown control or non-boolean value.', 'advanced-gutenberg'));
            }
        }
        $stored = (array) get_option('advgb_block_controls', []);
        $stored['controls'] = array_merge(isset($stored['controls']) ? (array) $stored['controls'] : [], $input['controls']);
        if (!empty($input['dry_run'])) {
            $result = self::getControls();
            $result['controls'] = (object) array_merge((array) $result['controls'], $input['controls']);
            return $result;
        }
        update_option('advgb_block_controls', $stored, false);
        return self::getControls();
    }

    /** Merge server types and the native editor catalogue. @since 3.7.6 */
    public static function knownBlocks(): array
    {
        $names = array_keys(WP_Block_Type_Registry::get_instance()->get_all_registered());
        foreach ((array) get_option('advgb_blocks_list', []) as $block) {
            $block = (array) $block;
            if (!empty($block['name'])) {
                $names[] = $block['name'];
            }
        }
        return array_values(array_unique($names));
    }

    /** Read one role only and expose discovery without guessing block keys. @since 3.7.6 */
    public static function getPermissions(array $input)
    {
        if (!get_role($input['role'])) {
            return self::invalid(__('Unknown role.', 'advanced-gutenberg'));
        }
        $stored = (array) get_option('advgb_blocks_user_roles', []);
        return [
            'role' => $input['role'],
            'permissions' => (object) (isset($stored[$input['role']]) ? $stored[$input['role']] : ['active_blocks' => self::knownBlocks(), 'inactive_blocks' => []]),
            'known_blocks' => self::knownBlocks(),
            'roles' => array_keys(wp_roles()->roles),
        ];
    }

    /** Validate all changes before writing, preserving other roles. @since 3.7.6 */
    public static function updatePermissions(array $input)
    {
        $current = self::getPermissions($input);
        if (is_wp_error($current)) {
            return $current;
        }
        foreach ($input['blocks'] as $name => $enabled) {
            if (!in_array($name, $current['known_blocks'], true) || !is_bool($enabled)) {
                return self::invalid(__('Unknown block or non-boolean value. Open Block Permissions in the editor to refresh its block catalogue.', 'advanced-gutenberg'));
            }
        }
        $stored = (array) get_option('advgb_blocks_user_roles', []);
        $role = isset($stored[$input['role']]) ? $stored[$input['role']] : [];
        $active = isset($role['active_blocks']) ? (array) $role['active_blocks'] : [];
        $inactive = isset($role['inactive_blocks']) ? (array) $role['inactive_blocks'] : [];
        // Blocks not previously configured are enabled by the native editor.
        $role['active_blocks'] = array_values(array_unique(array_merge($active, array_diff($current['known_blocks'], $inactive))));
        $role['inactive_blocks'] = $inactive;
        foreach ($input['blocks'] as $name => $enabled) {
            foreach (['active_blocks', 'inactive_blocks'] as $list) {
                $role[$list] = array_values(array_diff(isset($role[$list]) ? (array) $role[$list] : [], [$name]));
            }
            $role[$enabled ? 'active_blocks' : 'inactive_blocks'][] = $name;
        }
        $stored[$input['role']] = $role;
        if (!empty($input['dry_run'])) {
            $current['permissions'] = (object) $role;
            return $current;
        }
        update_option('advgb_blocks_user_roles', $stored, false);
        return self::getPermissions($input);
    }

    /** Validate native configuration fields before merging saved defaults. @since 3.7.6 */
    public static function updateConfiguration(array $input)
    {
        $definitions = Configuration::defaultConfig();
        if (!isset($definitions[$input['block']])) {
            return self::invalid(__('Unknown block configuration key. Use get-block-configuration first.', 'advanced-gutenberg'));
        }
        $fields = [];
        foreach ($definitions[$input['block']] as $group) {
            foreach ($group['settings'] as $field) {
                $fields[$field['name']] = $field;
            }
        }
        $values = [];
        foreach ($input['settings'] as $name => $value) {
            if (!isset($fields[$name])) {
                return self::invalid(sprintf(__('Unknown configuration field: %s', 'advanced-gutenberg'), $name));
            }
            $value = self::validateField($fields[$name], $value);
            if (is_wp_error($value)) {
                return $value;
            }
            $values[$name] = $value;
        }
        $stored = (array) get_option('advgb_blocks_default_config', []);
        $previous = isset($stored[$input['block']]) ? (array) $stored[$input['block']] : [];
        $values = array_merge($previous, $values);
        // Match the native Social Links form's flattened item fields.
        if ($input['block'] === 'advgb-social-links') {
            $items = [];
            foreach ($values as $name => $value) {
                if (strpos($name, '.') !== false) {
                    list($index, $property) = explode('.', $name, 2);
                    $items[$index][$property] = $value;
                }
            }
            if ($items) {
                $values['items'] = array_values($items);
            }
        }
        $stored[$input['block']] = $values;
        if (empty($input['dry_run'])) {
            update_option('advgb_blocks_default_config', $stored, false);
        }
        return ['block' => $input['block'], 'settings' => (object) $values];
    }

    /** Validate against native field types, choices and ranges. @since 3.7.6 */
    private static function validateField(array $field, $value)
    {
        if (!is_scalar($value)) {
            return self::invalid(__('Configuration values must be scalar.', 'advanced-gutenberg'));
        }
        if ($field['type'] === 'number') {
            if (!is_numeric($value) || (isset($field['min']) && $value < $field['min']) || (isset($field['max']) && $value > $field['max'])) {
                return self::invalid(__('Configuration number is outside its supported range.', 'advanced-gutenberg'));
            }
        }
        if ($field['type'] === 'checkbox' && !in_array($value, [true, false, 0, 1, '0', '1'], true)) {
            return self::invalid(__('Checkbox values must be boolean or 0/1.', 'advanced-gutenberg'));
        }
        if ($field['type'] === 'select' && !in_array((string) $value, array_map('strval', array_column($field['options'], 'value')), true)) {
            return self::invalid(__('Unknown configuration choice.', 'advanced-gutenberg'));
        }
        if ($field['type'] === 'checkbox') {
            return $value ? 1 : 0;
        }
        $value = sanitize_text_field((string) $value);
        return is_numeric($value) && !in_array($field['name'], ['lat', 'lng'], true) ? (float) $value : $value;
    }

    /** A consistent actionable validation error. @since 3.7.6 */
    private static function invalid(string $message): WP_Error
    {
        return new WP_Error('publishpress_blocks_invalid_input', $message, ['status' => 400]);
    }
}
