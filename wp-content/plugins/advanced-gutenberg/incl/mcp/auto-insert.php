<?php

/** Auto Insert rule discovery and native post/meta persistence. @since 3.7.6 */

namespace PublishPress\Blocks\Mcp;

\defined('ABSPATH') || exit;

class AutoInsert
{
    public static function fields(): array
    {
        $fields = [
            'title' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 200],
            'status' => ['type' => 'string', 'enum' => ['draft', 'publish']],
            'block_id' => ['type' => 'integer', 'minimum' => 1, 'description' => 'Verified reusable block (wp_block) ID.'],
            'position' => ['type' => 'string', 'enum' => ['beginning', 'end']],
            'priority' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 10000],
            'post_types' => ['type' => 'array', 'items' => ['type' => 'string', 'enum' => ['post']], 'minItems' => 1, 'maxItems' => 100],
            'taxonomies' => ['type' => 'object', 'additionalProperties' => Registry::ids(), 'maxProperties' => 100],
        ];
        if (defined('ADVANCED_GUTENBERG_PRO_LOADED')) {
            $fields['position']['enum'] = ['beginning', 'end', 'after_paragraph', 'before_paragraph', 'after_heading', 'before_heading', 'after_block', 'before_block', 'after_specific_block', 'before_specific_block'];
            $fields['post_types']['items'] = ['type' => 'string'];
            $fields['position_value'] = ['type' => 'integer', 'minimum' => 1, 'maximum' => 10000];
            foreach (['authors', 'post_ids', 'exclude_post_ids'] as $name) {
                $fields[$name] = Registry::ids();
            }
            $fields['post_months'] = ['type' => 'array', 'maxItems' => 12, 'items' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 12]];
            $fields['post_years'] = ['type' => 'array', 'maxItems' => 100, 'items' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 9999]];
            $fields['excluded_blocks'] = Registry::strings();
            $fields['blocks'] = ['type' => 'object', 'maxProperties' => 200, 'additionalProperties' => ['type' => 'string']];
        }
        return $fields;
    }

    public static function register()
    {
        Registry::add(
            'get-auto-insert-fields',
            __('Discover Auto Insert fields', 'advanced-gutenberg'),
            __('Discover Auto Insert rule fields and Free/Pro positions, post types and taxonomies. Use list-reusable-blocks to choose a block ID. Specific block targeting and extra placement/author/date/post restrictions require Pro.', 'advanced-gutenberg'),
            Registry::object(),
            [__CLASS__, 'discover'],
            [Abilities::class, 'canManageAutoInsert']
        );
        $input = self::fields();
        $input['id'] = ['type' => 'integer', 'minimum' => 1];
        Registry::add(
            'save-auto-insert-rule',
            __('Create or update Auto Insert rule', 'advanced-gutenberg'),
            __('Create a draft Auto Insert rule or update one verified rule ID. Title and reusable block ID are required on create. Only supplied fields change. Setting status publish makes insertion active. Free/Pro targeting and object permissions are enforced.', 'advanced-gutenberg'),
            Registry::object($input),
            [__CLASS__, 'save'],
            [__CLASS__, 'canWrite'],
            true,
            false
        );
        Registry::add(
            'delete-auto-insert-rule',
            __('Trash Auto Insert rule', 'advanced-gutenberg'),
            __('Move a verified Auto Insert rule to Trash to stop its insertion. Does not delete the reusable block or change other rules.', 'advanced-gutenberg'),
            Registry::object(['id' => ['type' => 'integer', 'minimum' => 1]], ['id']),
            [__CLASS__, 'delete'],
            [__CLASS__, 'canDelete'],
            true
        );
    }

    public static function discover(): array
    {
        return ['fields' => (object) self::fields(), 'post_types' => array_values(get_post_types(['public' => true])),
            'taxonomies' => array_values(get_taxonomies(['public' => true])), 'pro_active' => defined('ADVANCED_GUTENBERG_PRO_LOADED')];
    }

    public static function canWrite(array $input = []): bool
    {
        if (!Abilities::canManageAutoInsert()) {
            return false;
        }
        $type = get_post_type_object('advgb_insert_block');
        if (!$type) {
            return false;
        }
        return isset($input['id']) ? current_user_can('edit_post', $input['id']) : current_user_can($type->cap->create_posts);
    }

    public static function canDelete(array $input = []): bool
    {
        return Abilities::canManageAutoInsert() && isset($input['id']) && current_user_can('delete_post', $input['id']);
    }

    public static function save(array $input)
    {
        if (isset($input['id']) && get_post_type($input['id']) !== 'advgb_insert_block') {
            return Registry::error(__('Auto Insert rule not found.', 'advanced-gutenberg'), 404);
        }
        if (!isset($input['id']) && (empty($input['title']) || empty($input['block_id']))) {
            return Registry::error(__('A title and reusable block ID are required.', 'advanced-gutenberg'));
        }
        $type = get_post_type_object('advgb_insert_block');
        if (isset($input['status']) && $input['status'] === 'publish' && !current_user_can($type->cap->publish_posts)) {
            return Registry::error(__('Cannot publish insertion rules.', 'advanced-gutenberg'), 403);
        }
        if (isset($input['block_id']) && (get_post_type($input['block_id']) !== 'wp_block' || !current_user_can('edit_post', $input['block_id']))) {
            return Registry::error(__('Choose an editable reusable block ID.', 'advanced-gutenberg'));
        }
        $effectiveStatus = $input['status'] ?? (isset($input['id']) ? get_post_status($input['id']) : 'draft');
        $blockId = $input['block_id'] ?? (isset($input['id']) ? (int) get_post_meta($input['id'], '_advgb_block_id', true) : 0);
        $block = get_post($blockId);
        if ($effectiveStatus === 'publish' && (!$block || $block->post_status !== 'publish' || $block->post_password !== '')) {
            return Registry::error(__('Publish the selected reusable block before activating its insertion rule.', 'advanced-gutenberg'));
        }
        foreach ($input['post_types'] ?? [] as $postType) {
            $object = get_post_type_object($postType);
            if (!$object || !$object->public || !current_user_can($object->cap->edit_posts)) {
                return Registry::error(__('Unknown or inaccessible target post type.', 'advanced-gutenberg'));
            }
        }
        foreach ($input['taxonomies'] ?? [] as $taxonomy => $ids) {
            $object = get_taxonomy($taxonomy);
            if (!$object || !current_user_can($object->cap->assign_terms) || (!defined('ADVANCED_GUTENBERG_PRO_LOADED') && !in_array($taxonomy, ['category', 'post_tag'], true))) {
                return Registry::error(__('Unknown, inaccessible or Pro-only taxonomy.', 'advanced-gutenberg'));
            }
            foreach ($ids as $id) {
                if (!term_exists($id, $taxonomy)) {
                    return Registry::error(__('Unknown term ID.', 'advanced-gutenberg'));
                }
            }
        }
        foreach ($input['authors'] ?? [] as $id) {
            if (!current_user_can('list_users') || !get_user_by('id', $id)) {
                return Registry::error(__('Unknown or inaccessible author ID.', 'advanced-gutenberg'));
            }
        }
        foreach (array_merge($input['post_ids'] ?? [], $input['exclude_post_ids'] ?? []) as $id) {
            if (!get_post($id) || !current_user_can('edit_post', $id)) {
                return Registry::error(__('Unknown or inaccessible target post ID.', 'advanced-gutenberg'));
            }
        }
        foreach (array_merge(array_keys($input['blocks'] ?? []), $input['excluded_blocks'] ?? []) as $name) {
            if (!in_array($name, Operations::knownBlocks(), true)) {
                return Registry::error(__('Unknown targeted block name.', 'advanced-gutenberg'));
            }
        }
        $post = ['post_type' => 'advgb_insert_block'];
        if (isset($input['id'])) {
            $post['ID'] = $input['id'];
        } else {
            $post['post_status'] = 'draft';
        }
        foreach (['title' => 'post_title', 'status' => 'post_status'] as $key => $field) {
            if (isset($input[$key])) {
                $post[$field] = sanitize_text_field($input[$key]);
            }
        }
        // Updates merge with the stored rule; only supplied fields change
        $id = isset($input['id']) ? wp_update_post(wp_slash($post), true) : wp_insert_post(wp_slash($post), true);
        if (is_wp_error($id)) {
            return $id;
        }
        $meta = $input;
        unset($meta['id'], $meta['title'], $meta['status']);
        if (!isset($input['id'])) {
            $meta = array_merge(['position' => 'beginning', 'post_types' => ['post'], 'priority' => 10], $meta);
        }
        if (isset($meta['blocks'])) {
            $meta['blocks'] = array_map('sanitize_text_field', (array) $meta['blocks']);
        }
        foreach ($meta as $key => $value) {
            update_post_meta($id, '_advgb_' . $key, $value);
        }
        return ['id' => $id, 'status' => get_post_status($id), 'settings' => (object) self::read($id)];
    }

    public static function read(int $id): array
    {
        $result = [];
        foreach (array_keys(self::fields()) as $key) {
            if (!in_array($key, ['title', 'status'], true)) {
                $result[$key] = get_post_meta($id, '_advgb_' . $key, true);
            }
        }
        return $result;
    }

    public static function delete(array $input)
    {
        if (!EMPTY_TRASH_DAYS) {
            return Registry::error(__('Trash is disabled on this site. Use WordPress to permanently delete this object.', 'advanced-gutenberg'));
        }
        if (get_post_type($input['id']) !== 'advgb_insert_block') {
            return Registry::error(__('Auto Insert rule not found.', 'advanced-gutenberg'), 404);
        }
        if (!wp_trash_post($input['id'])) {
            return Registry::error(__('Rule could not be trashed.', 'advanced-gutenberg'));
        }
        return ['trashed' => $input['id']];
    }
}
