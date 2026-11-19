<?php

/** Feature discovery and validated role batches. @since 3.7.6 */

namespace PublishPress\Blocks\Mcp;

use PublishPress\Blocks\Utilities;

\defined('ABSPATH') || exit;

class Workflows
{
    public static function register()
    {
        Registry::add(
            'list-features',
            __('Discover Blocks features and actions', 'advanced-gutenberg'),
            __('List Blocks screens, enabled state, required capabilities, edition requirements, field schemas and related read/write actions. Distinguishes role permissions, global switches, new-block defaults and existing post edits. Discovery does not grant access to any action.', 'advanced-gutenberg'),
            Registry::object(),
            [__CLASS__, 'features'],
            [Operations::class, 'canManageSettings']
        );
        $roles = ['type' => 'array', 'minItems' => 1, 'maxItems' => 100, 'uniqueItems' => true,
            'items' => ['type' => 'string', 'minLength' => 1]];
        foreach (['permissions', 'editor-features'] as $kind) {
            $field = $kind === 'permissions' ? 'blocks' : 'settings';
            $values = $kind === 'permissions'
                ? ['type' => 'object', 'minProperties' => 1, 'maxProperties' => 200, 'additionalProperties' => ['type' => 'boolean']]
                : Features::schemas()['editor-features'];
            Registry::add(
                $kind === 'permissions' ? 'bulk-update-block-permissions' : 'bulk-update-feature-settings',
                $kind === 'permissions' ? __('Update block permissions for multiple roles', 'advanced-gutenberg') : __('Update Editor Features for multiple roles', 'advanced-gutenberg'),
                __('Apply the same typed changes to existing roles. Validates every role and field before one settings write, preserving other roles and fields. Use dry_run for proposed values without saving.', 'advanced-gutenberg'),
                Registry::object(['roles' => $roles, $field => $values, 'dry_run' => ['type' => 'boolean', 'default' => false]], ['roles', $field]),
                [__CLASS__, $kind === 'permissions' ? 'permissions' : 'editorFeatures'],
                [Operations::class, $kind === 'permissions' ? 'canManagePermissions' : 'canManageSettings'],
                true
            );
        }
    }

    public static function features(): array
    {
        // This inventory describes domain workflows; Hub still discovers registered abilities.
        $map = [
            ['dashboard', __('Dashboard and Settings', 'advanced-gutenberg'), '', ['get-plugin-settings'], ['update-plugin-settings']],
            ['block-permissions', __('Block Permissions', 'advanced-gutenberg'), 'enable_block_access', ['get-block-permissions'], ['update-block-permissions', 'bulk-update-block-permissions']],
            ['block-controls', __('Block Controls', 'advanced-gutenberg'), 'block_controls', ['get-block-controls', 'get-control-preset-fields', 'list-control-presets'], ['update-block-controls', 'save-control-preset', 'delete-control-preset']],
            ['block-styles', __('Block Styles', 'advanced-gutenberg'), 'enable_custom_styles', ['get-custom-style-fields', 'list-custom-styles'], ['save-custom-style', 'delete-custom-style', 'assign-block-style']],
            ['block-usage', __('Block Usage', 'advanced-gutenberg'), 'enable_block_usage', ['scan-block-usage'], []],
            ['extra-defaults', __('Extra Blocks defaults for new blocks', 'advanced-gutenberg'), 'enable_advgb_blocks', ['get-block-configuration'], ['update-block-configuration']],
            ['reusable-blocks', __('Reusable Blocks and synced patterns', 'advanced-gutenberg'), 'reusable_blocks', ['list-reusable-blocks', 'get-post-blocks'], ['save-reusable-block', 'delete-reusable-block']],
            ['auto-insert', __('Auto Insert Blocks', 'advanced-gutenberg'), 'auto_insert_blocks', ['get-auto-insert-fields', 'list-auto-insert-rules'], ['save-auto-insert-rule', 'delete-auto-insert-rule']],
            ['post-notes', __('Post Notes', 'advanced-gutenberg'), 'enable_post_notes', ['list-post-notes'], ['save-post-note', 'delete-post-note']],
            ['post-blocks', __('Existing post blocks and editor preferences', 'advanced-gutenberg'), '', ['get-post-blocks', 'get-block-type-fields'], ['edit-post-block', 'update-post-editor-settings']],
            ['form-submissions', __('Saved form submissions', 'advanced-gutenberg'), 'enable_advgb_blocks', ['get-form-submission-counts', 'list-form-submissions'], []],
        ];
        foreach (Features::schemas() as $id => $schema) {
            $map[] = [$id, [
                'extra-blocks' => __('Extra Blocks availability', 'advanced-gutenberg'),
                'controls-blocks' => __('Per-block visibility controls', 'advanced-gutenberg'),
                'legacy-blocks' => __('Legacy Blocks', 'advanced-gutenberg'),
                'editor-features' => __('Role Editor Features', 'advanced-gutenberg'),
                'form-email' => __('Form email settings', 'advanced-gutenberg'),
                'recaptcha' => __('reCAPTCHA settings', 'advanced-gutenberg'),
                'content-display-thumbnail' => __('Content Display default thumbnail', 'advanced-gutenberg'),
            ][$id], $id === 'extra-blocks' ? 'enable_advgb_blocks' : ($id === 'controls-blocks' ? 'block_controls' : ''), ['get-feature-settings'],
                $id === 'editor-features' ? ['update-feature-settings', 'bulk-update-feature-settings'] : ['update-feature-settings']];
        }
        $items = [];
        foreach ($map as $entry) {
            list($id, $label, $switch, $reads, $writes) = $entry;
            $capabilities = ['manage_options', 'activate_plugins'];
            if (in_array($id, ['block-permissions', 'block-controls', 'controls-blocks'], true)) {
                $capabilities[] = 'administrator';
            } elseif (in_array($id, ['post-blocks', 'post-notes', 'reusable-blocks'], true)) {
                $capabilities = ['edit_posts', 'edit_post (target object)'];
            } elseif ($id === 'block-usage') {
                $capabilities = ['manage_options', 'edit_posts (target post type)', 'edit_post (each result)'];
            } elseif ($id === 'auto-insert') {
                $capabilities = ['manage_options', 'create/edit/publish/delete capabilities for the target rule'];
            }
            $schema = Features::schemas()[$id] ?? [];
            if ($id === 'dashboard') {
                $schema = (array) Operations::getSettings()['fields'];
            }
            $fields = [];
            if ($id === 'extra-defaults' && Operations::canManageConfiguration()) {
                $fields = (array) Abilities::getBlockConfiguration()['fields'];
            } elseif ($id === 'block-styles' && Abilities::canManageStyles()) {
                $fields = (array) Styles::fields()['fields'];
            } elseif ($id === 'auto-insert' && Abilities::canManageAutoInsert()) {
                $fields = (array) AutoInsert::discover()['fields'];
            } elseif ($id === 'block-controls' && Operations::canManageControls()) {
                $fields = Presets::fields();
            }
            $items[] = ['id' => $id, 'label' => $label, 'enabled' => !$switch || Utilities::settingIsEnabled($switch),
                'required_capabilities' => $capabilities, 'read_actions' => $reads, 'write_actions' => $writes,
                'schema' => (object) $schema, 'fields' => (object) $fields, 'edition' => 'free',
                'permission_note' => __('Each action also checks its native feature, edition and target-object permissions. Required capabilities can vary between reading, assignment and administration.', 'advanced-gutenberg'),
                'pro_fields' => in_array($id, ['auto-insert', 'block-styles'], true),
                'discovery' => $reads, 'role_required' => $id === 'editor-features'];
        }
        return ['features' => $items, 'pro_active' => defined('ADVANCED_GUTENBERG_PRO_LOADED')];
    }

    public static function permissions(array $input)
    {
        return self::roles($input, true);
    }

    public static function editorFeatures(array $input)
    {
        return self::roles($input, false);
    }

    private static function roles(array $input, bool $permissions)
    {
        $results = [];
        foreach ($input['roles'] as $role) {
            $change = $permissions ? ['role' => $role, 'blocks' => $input['blocks'], 'dry_run' => true]
                : ['feature' => 'editor-features', 'role' => $role, 'settings' => $input['settings'], 'dry_run' => true];
            $result = $permissions ? Operations::updatePermissions($change) : Features::update($change);
            if (is_wp_error($result)) {
                return $result;
            }
            $results[] = ['role' => $role, 'proposed' => $result];
        }
        if (empty($input['dry_run'])) {
            $option = $permissions ? 'advgb_blocks_user_roles' : 'advgb_settings';
            $stored = (array) get_option($option, []);
            foreach ($results as $result) {
                if ($permissions) {
                    $stored[$result['role']] = (array) $result['proposed']['permissions'];
                } else {
                    $stored['block_features_settings'][$result['role']] = array_merge(
                        $stored['block_features_settings'][$result['role']] ?? [],
                        $input['settings']
                    );
                }
            }
            // Keep each option's autoload setting: block permissions are not autoloaded, plugin settings are
            $permissions ? update_option($option, $stored, false) : update_option($option, $stored);
        }
        return ['results' => $results, 'dry_run' => !empty($input['dry_run'])];
    }
}
