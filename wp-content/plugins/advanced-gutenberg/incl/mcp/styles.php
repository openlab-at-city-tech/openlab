<?php

/** Custom Block Styles CRUD using native CSS parsing and sanitization. @since 3.7.6 */

namespace PublishPress\Blocks\Mcp;

use AdvancedGutenbergBlockStyles as NativeStyles;

\defined('ABSPATH') || exit;

class Styles
{
    public static function register()
    {
        Registry::add(
            'get-custom-style-fields',
            __('Discover Block Styles fields', 'advanced-gutenberg'),
            __('Read native Block Styles field labels, choices and Pro requirements. CSS is scoped to the saved class name. Read list-custom-styles before updating an ID.', 'advanced-gutenberg'),
            Registry::object(),
            [__CLASS__, 'fields'],
            [Abilities::class, 'canManageStyles']
        );
        $schema = Registry::object([
            'id' => ['type' => 'integer', 'minimum' => 1],
            'title' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 200],
            'name' => ['type' => 'string', 'pattern' => '^[a-zA-Z_][a-zA-Z0-9_-]*$', 'maxLength' => 100],
            'css' => ['type' => 'string', 'maxLength' => 50000, 'description' => 'CSS declarations or native SCSS-style rules for this style, not HTML.'],
            'identifyColor' => ['type' => 'string', 'pattern' => '^#[a-fA-F0-9]{6}$'],
        ]);
        Registry::add(
            'save-custom-style',
            __('Create or update Block Style', 'advanced-gutenberg'),
            __('Create a named block style or update a verified ID. Unspecified fields and other styles are preserved. Native CSS parsing, sanitization and generated selectors are used. Changing a class name does not rewrite existing post classes.', 'advanced-gutenberg'),
            $schema,
            [__CLASS__, 'save'],
            [Abilities::class, 'canManageStyles'],
            true,
            false
        );
        Registry::add(
            'delete-custom-style',
            __('Delete Block Style', 'advanced-gutenberg'),
            __('Delete a verified style ID. This removes its saved CSS; it does not rewrite existing post classes.', 'advanced-gutenberg'),
            Registry::object(['id' => ['type' => 'integer', 'minimum' => 1]], ['id']),
            [__CLASS__, 'delete'],
            [Abilities::class, 'canManageStyles'],
            true
        );
    }

    public static function fields(): array
    {
        return ['fields' => (object) NativeStyles::get_style_fields(), 'pro_active' => defined('ADVANCED_GUTENBERG_PRO_LOADED')];
    }

    public static function stored(): array
    {
        return (array) get_option('advgb_custom_styles', NativeStyles::$default_custom_styles);
    }

    public static function save(array $input)
    {
        $styles = self::stored();
        $index = null;
        $maximum = 0;
        foreach ($styles as $key => $style) {
            $maximum = max($maximum, (int) $style['id']);
            if (isset($input['id']) && (int) $style['id'] === $input['id']) {
                $index = $key;
            }
            if (isset($input['name']) && ($style['name'] ?? '') === $input['name'] && (!isset($input['id']) || (int) $style['id'] !== $input['id'])) {
                return Registry::error(__('A style with this class name already exists.', 'advanced-gutenberg'));
            }
        }
        if (isset($input['id']) && $index === null) {
            return Registry::error(__('Style ID not found.', 'advanced-gutenberg'), 404);
        }
        if ($index === null && (empty($input['title']) || empty($input['name']))) {
            return Registry::error(__('Title and CSS class name are required to create a style.', 'advanced-gutenberg'));
        }
        $style = $index === null ? ['id' => $maximum + 1, 'css' => [], 'identifyColor' => '#000000', 'active_tab' => 'style-editor'] : $styles[$index];
        foreach (['title', 'name', 'identifyColor'] as $field) {
            if (isset($input[$field])) {
                $style[$field] = sanitize_text_field($input[$field]);
            }
        }
        if (empty($style['name'])) {
            return Registry::error(__('This saved style has no class name. Supply a valid name to repair it.', 'advanced-gutenberg'));
        }
        if (isset($input['css'])) {
            $style['css'] = NativeStyles::scss_to_array(NativeStyles::sanitize_css($input['css']), $style['name']);
        }
        $style['generated_css'] = NativeStyles::sanitize_css(NativeStyles::generate_final_css($style['css'], $style['name']));
        if ($index === null) {
            $styles[] = $style;
        } else {
            $styles[$index] = $style;
        }
        update_option('advgb_custom_styles', array_values($styles), false);
        return ['style' => (object) $style];
    }

    public static function delete(array $input)
    {
        $styles = self::stored();
        foreach ($styles as $key => $style) {
            if ((int) $style['id'] === $input['id']) {
                unset($styles[$key]);
                update_option('advgb_custom_styles', array_values($styles), false);
                return ['deleted' => $input['id']];
            }
        }
        return Registry::error(__('Style ID not found.', 'advanced-gutenberg'), 404);
    }
}
