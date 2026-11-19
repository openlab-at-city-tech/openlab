<?php

/**
 * WordPress Abilities API integration for PublishPress Blocks.
 */

namespace PublishPress\Blocks\Mcp;

use PublishPress\Blocks\Configuration;
use PublishPress\Blocks\Utilities;
use stdClass;
use WP_Block_Type_Registry;
use WP_Error;
use WP_Query;

\defined('ABSPATH') || exit;

// WordPress 6.9 introduced the Abilities API. Keep older supported sites working.
if (!function_exists('wp_register_ability')) {
    return;
}

require_once __DIR__ . '/mcp/operations.php';
foreach (['registry', 'presets', 'styles', 'auto-insert', 'content', 'features', 'workflows'] as $module) {
    require_once __DIR__ . '/mcp/' . $module . '.php';
}

class Abilities
{
    public const CATEGORY = 'publishpress';

    public static function init()
    {
        add_action('wp_abilities_api_categories_init', [__CLASS__, 'registerCategory']);
        add_action('wp_abilities_api_init', [__CLASS__, 'registerAbilities']);
    }

    public static function registerCategory()
    {
        if (wp_has_ability_category(self::CATEGORY)) {
            return;
        }

        wp_register_ability_category(self::CATEGORY, [
            'label'       => __('PublishPress', 'advanced-gutenberg'),
            'description' => __('Actions provided by PublishPress plugins.', 'advanced-gutenberg'),
        ]);
    }

    public static function registerAbilities()
    {
        Operations::registerAbilities();
        Presets::register();
        Styles::register();
        AutoInsert::register();
        Content::register();
        Features::register();
        Workflows::register();

        wp_register_ability('publishpress-blocks/list-block-types', self::abilityArgs(
            __('List PublishPress Block Types', 'advanced-gutenberg'),
            __('Lists registered PublishPress Blocks block types and their editor metadata.', 'advanced-gutenberg'),
            self::emptyInputSchema(),
            self::arrayOutputSchema('blocks'),
            [__CLASS__, 'listBlockTypes'],
            [__CLASS__, 'canManageSettings']
        ));

        wp_register_ability('publishpress-blocks/get-block-configuration', self::abilityArgs(
            __('Get Block Configuration', 'advanced-gutenberg'),
            __('Gets Extra Blocks saved defaults and available fields, including labels, types, choices and ranges. Use these exact field names with update-block-configuration. Defaults affect new blocks; existing post blocks are unchanged.', 'advanced-gutenberg'),
            self::emptyInputSchema(),
            ['type' => 'object', 'properties' => ['configuration' => ['type' => 'object'], 'fields' => ['type' => 'object']]],
            [__CLASS__, 'getBlockConfiguration'],
            [Operations::class, 'canManageConfiguration']
        ));

        wp_register_ability('publishpress-blocks/list-control-presets', self::abilityArgs(
            __('List Block Control Presets', 'advanced-gutenberg'),
            __('Lists saved PublishPress Blocks visibility-control presets.', 'advanced-gutenberg'),
            self::emptyInputSchema(),
            self::arrayOutputSchema('presets'),
            [__CLASS__, 'listControlPresets'],
            [__CLASS__, 'canManageOptions']
        ));

        wp_register_ability('publishpress-blocks/list-custom-styles', self::abilityArgs(
            __('List Custom Block Styles', 'advanced-gutenberg'),
            __('Lists saved PublishPress Blocks custom styles.', 'advanced-gutenberg'),
            self::emptyInputSchema(),
            self::arrayOutputSchema('styles'),
            [__CLASS__, 'listCustomStyles'],
            [__CLASS__, 'canManageStyles']
        ));

        wp_register_ability('publishpress-blocks/list-auto-insert-rules', self::abilityArgs(
            __('List Auto Insert Rules', 'advanced-gutenberg'),
            __('Lists Auto Insert Block rules and their targeting metadata.', 'advanced-gutenberg'),
            self::paginationSchema(),
            self::arrayOutputSchema('rules'),
            [__CLASS__, 'listAutoInsertRules'],
            [__CLASS__, 'canManageAutoInsert']
        ));

        wp_register_ability('publishpress-blocks/inspect-post-blocks', self::abilityArgs(
            __('Inspect Post Blocks', 'advanced-gutenberg'),
            __('Returns a compact, ordered inventory of blocks in one editable post including attributes, but without rendered HTML. Does not expand synced patterns or reusable block references.', 'advanced-gutenberg'),
            [
                'type'                 => 'object',
                'properties'           => [
                    'post_id' => ['type' => 'integer', 'minimum' => 1],
                ],
                'required'             => ['post_id'],
                'additionalProperties' => false,
            ],
            self::arrayOutputSchema('blocks'),
            [__CLASS__, 'inspectPostBlocks'],
            [__CLASS__, 'canInspectPostBlocks']
        ));
    }

    private static function abilityArgs($label, $description, $inputSchema, $outputSchema, $callback, $permissionCallback)
    {
        return [
            'label'               => $label,
            'description'         => $description,
            'category'            => self::CATEGORY,
            'input_schema'        => $inputSchema,
            'output_schema'       => $outputSchema,
            'execute_callback'    => $callback,
            'permission_callback' => $permissionCallback,
            'meta'                => [
                'public'       => false,
                'show_in_rest' => false,
                'mcp'          => ['public' => false],
                'annotations'  => [
                    'readonly'    => true,
                    'destructive' => false,
                    'idempotent'  => true,
                ],
            ],
        ];
    }

    private static function emptyInputSchema()
    {
        return [
            'type'                 => 'object',
            'properties'           => [], // An array: core's validator indexes properties as an array, so stdClass fatals when arguments are sent
            'additionalProperties' => false,
        ];
    }

    private static function arrayOutputSchema($property)
    {
        return [
            'type'       => 'object',
            'properties' => [
                $property => [
                    'type'  => 'array',
                    'items' => ['type' => 'object'],
                ],
            ],
        ];
    }

    public static function canManageStyles()
    {
        return self::canManageSettings() && Utilities::settingIsEnabled('enable_custom_styles');
    }

    public static function canManageAutoInsert()
    {
        return self::canManageOptions() && Utilities::settingIsEnabled('auto_insert_blocks');
    }

    public static function canManageSettings()
    {
        return current_user_can('manage_options') && current_user_can('activate_plugins');
    }

    private static function paginationSchema()
    {
        return [
            'type' => 'object',
            'properties' => [
                'page' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10000, 'default' => 1],
                'per_page' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 20],
            ],
            'additionalProperties' => false,
        ];
    }

    public static function canManageOptions()
    {
        return current_user_can('manage_options');
    }

    public static function canInspectPostBlocks($input = [])
    {
        $postId = isset($input['post_id']) ? absint($input['post_id']) : 0;
        $post   = $postId ? get_post($postId) : null;
        if (! $post) {
            return false;
        }

        // Only post types editable in the block editor through their REST controller
        $type = get_post_type_object($post->post_type);

        return $type && $type->show_in_rest && current_user_can('edit_post', $postId);
    }

    public static function listBlockTypes()
    {
        $registry = WP_Block_Type_Registry::get_instance();
        $blocks   = [];

        foreach ($registry->get_all_registered() as $name => $block) {
            if (strpos($name, 'advgb/') !== 0) {
                continue;
            }

            $blocks[] = [
                'name'        => $name,
                'title'       => isset($block->title) ? (string) $block->title : $name,
                'description' => isset($block->description) ? (string) $block->description : '',
                'category'    => isset($block->category) ? (string) $block->category : '',
                'attributes'  => (object) $block->get_attributes(),
                'supports'    => isset($block->supports) && is_array($block->supports) ? $block->supports : [],
            ];
        }

        $names = array_column($blocks, 'name');
        foreach ((array) get_option('advgb_blocks_list', []) as $stored) {
            $stored = (array) $stored;
            if (empty($stored['name']) || strpos($stored['name'], 'advgb/') !== 0 || in_array($stored['name'], $names, true)) {
                continue;
            }
            $blocks[] = [
                'name' => (string) $stored['name'],
                'title' => wp_strip_all_tags(isset($stored['title']) ? $stored['title'] : $stored['name']),
                'source' => 'editor_snapshot',
                'attributes' => (object) ($stored['attributes'] ?? []),
                'description' => (string) ($stored['description'] ?? ''),
            ];
        }

        return ['blocks' => $blocks];
    }

    public static function getBlockConfiguration()
    {
        $configuration = get_option('advgb_blocks_default_config', []);

        return [
            'configuration' => (object) (is_array($configuration) ? $configuration : []),
            'fields' => (object) Configuration::defaultConfig(),
        ];
    }

    public static function listControlPresets()
    {
        $storedPresets = get_option('advgb_block_control_presets', []);
        $presets       = [];

        foreach ((array) $storedPresets as $id => $preset) {
            if (!is_array($preset)) {
                continue;
            }

            $preset['id'] = (string) $id;
            $presets[]    = $preset;
        }

        return ['presets' => $presets];
    }

    public static function listCustomStyles()
    {
        $styles = Styles::stored();

        return ['styles' => is_array($styles) ? array_values($styles) : []];
    }

    public static function listAutoInsertRules($input = [])
    {
        $page = isset($input['page']) ? (int) $input['page'] : 1;
        $perPage = isset($input['per_page']) ? (int) $input['per_page'] : 20;
        $query = new WP_Query([
            'post_type'      => 'advgb_insert_block',
            'post_status'    => 'any',
            'posts_per_page' => $perPage,
            'paged' => $page,
            'orderby'        => 'ID',
            'order'          => 'ASC',
        ]);
        $result = [];

        foreach ($query->posts as $rule) {
            if (!current_user_can('edit_post', $rule->ID)) {
                continue;
            }
            $result[] = array_merge(AutoInsert::read((int) $rule->ID), [
                'id'                => (int) $rule->ID,
                'title'             => (string) $rule->post_title,
                'status'            => (string) $rule->post_status,
                'block_id'          => (int) get_post_meta($rule->ID, '_advgb_block_id', true),
                'position'          => (string) get_post_meta($rule->ID, '_advgb_position', true),
                'position_value'    => get_post_meta($rule->ID, '_advgb_position_value', true),
                'priority'          => (int) get_post_meta($rule->ID, '_advgb_priority', true),
                'post_types'        => (array) get_post_meta($rule->ID, '_advgb_post_types', true),
                'taxonomies'        => (array) get_post_meta($rule->ID, '_advgb_taxonomies', true),
                'authors'           => array_map('absint', (array) get_post_meta($rule->ID, '_advgb_authors', true)),
                'post_ids'          => array_map('absint', (array) get_post_meta($rule->ID, '_advgb_post_ids', true)),
                'exclude_post_ids'  => array_map('absint', (array) get_post_meta($rule->ID, '_advgb_exclude_post_ids', true)),
            ]);
        }

        return ['rules' => $result, 'page' => $page, 'total_pages' => (int) $query->max_num_pages];
    }

    public static function inspectPostBlocks($input = [])
    {
        $postId = isset($input['post_id']) ? absint($input['post_id']) : 0;
        $post   = $postId ? get_post($postId) : false;

        if (!$post) {
            return new WP_Error(
                'publishpress_blocks_post_not_found',
                __('Post not found.', 'advanced-gutenberg'),
                ['status' => 404]
            );
        }

        $blocks = [];
        self::collectBlocks(parse_blocks($post->post_content), $blocks);

        return ['blocks' => $blocks];
    }

    private static function collectBlocks($parsedBlocks, &$blocks, $depth = 0)
    {
        foreach ($parsedBlocks as $block) {
            if (empty($block['blockName'])) {
                if (!empty($block['innerBlocks'])) {
                    self::collectBlocks($block['innerBlocks'], $blocks, $depth);
                }
                continue;
            }

            $blocks[] = [
                'name'       => (string) $block['blockName'],
                'depth'      => (int) $depth,
                'attributes' => isset($block['attrs']) && is_array($block['attrs']) ? $block['attrs'] : [],
            ];

            if (!empty($block['innerBlocks'])) {
                self::collectBlocks($block['innerBlocks'], $blocks, $depth + 1);
            }
        }
    }
}

Abilities::init();
