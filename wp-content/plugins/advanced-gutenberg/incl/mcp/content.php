<?php

/** Targeted post block, reusable block and Post Notes actions. @since 3.7.6 */

namespace PublishPress\Blocks\Mcp;

use PublishPress\Blocks\Utilities;

\defined('ABSPATH') || exit;

class Content
{
    public static function register()
    {
        Registry::add(
            'get-block-type-fields',
            __('Discover block attributes', 'advanced-gutenberg'),
            __('Read one registered or captured editor block type by exact name, including native attribute schemas, defaults, labels and supports. Includes core and Pro types available on this site. Open Block Permissions to refresh client-only schema snapshots.', 'advanced-gutenberg'),
            Registry::object(['name' => ['type' => 'string', 'minLength' => 1]], ['name']),
            [__CLASS__, 'blockFields'],
            [__CLASS__, 'canDiscover']
        );
        Registry::add(
            'scan-block-usage',
            __('Scan Block Usage', 'advanced-gutenberg'),
            __('Read fresh block usage counts from a bounded page of editable posts, including nested blocks. Returns IDs, post types and counts without post content. Optionally filter by a known block name. Totals cover this page only; continue all pages for a site inventory.', 'advanced-gutenberg'),
            Registry::object(array_merge(Registry::page(), ['post_type' => ['type' => 'string', 'default' => 'post'], 'name' => ['type' => 'string']])),
            [__CLASS__, 'usage'],
            [__CLASS__, 'canScan']
        );
        $post = ['post_id' => ['type' => 'integer', 'minimum' => 1]];
        Registry::add(
            'get-post-blocks',
            __('Read editable post blocks', 'advanced-gutenberg'),
            __('Read an editable post as serialized blocks with exact nested paths and a content hash. Includes content, attributes and saved Blocks editor preferences. Does not expand reusable references. Use the hash when editing to avoid overwriting newer content.', 'advanced-gutenberg'),
            Registry::object($post, ['post_id']),
            [__CLASS__, 'get'],
            [Abilities::class, 'canInspectPostBlocks']
        );
        $edit = array_merge($post, [
            'expected_hash' => ['type' => 'string', 'pattern' => '^[a-f0-9]{64}$'],
            'path' => ['type' => 'array', 'minItems' => 1, 'maxItems' => 30, 'items' => ['type' => 'integer', 'minimum' => 0]],
            'operation' => ['type' => 'string', 'enum' => ['replace', 'remove', 'insert_before', 'insert_after', 'set_visibility']],
            'block_content' => ['type' => 'string', 'maxLength' => 100000, 'description' => 'Exactly one complete serialized Gutenberg block, including its HTML when needed.'],
            'visibility' => Features::visibilitySchema(),
        ]);
        Registry::add(
            'edit-post-block',
            __('Edit one post block', 'advanced-gutenberg'),
            __('Replace, remove or insert one serialized block at an exact path from get-post-blocks. Alternatively set_visibility changes only native advgbBlockControls. Requires the last content hash. Preserves other blocks and post fields; native WordPress save sanitization and permissions apply.', 'advanced-gutenberg'),
            Registry::object($edit, ['post_id', 'expected_hash', 'path', 'operation']),
            [__CLASS__, 'edit'],
            [Abilities::class, 'canInspectPostBlocks'],
            true
        );
        Registry::add(
            'assign-block-style',
            __('Assign saved style to one post block', 'advanced-gutenberg'),
            __('Apply a saved Block Style ID to an exact block path from get-post-blocks, or remove its saved style. Updates native customStyle and identifyColor attributes and saved wrapper markup, preserving other classes and content. Requires the latest content hash and editable post permissions. Block Styles must be enabled.', 'advanced-gutenberg'),
            Registry::object(array_merge($post, [
                'expected_hash' => $edit['expected_hash'], 'path' => $edit['path'],
                'style_id' => ['type' => 'integer', 'minimum' => 1],
                'remove' => ['type' => 'boolean', 'default' => false],
            ]), ['post_id', 'expected_hash', 'path']),
            [__CLASS__, 'assignStyle'],
            [__CLASS__, 'canAssignStyle'],
            true
        );
        Registry::add(
            'list-reusable-blocks',
            __('Find reusable blocks and synced patterns', 'advanced-gutenberg'),
            __('Find editable reusable blocks (wp_block), with IDs, titles and status, for Auto Insert or editing. Read one with get-post-blocks. Results are paginated.', 'advanced-gutenberg'),
            Registry::object(array_merge(Registry::page(), ['search' => ['type' => 'string', 'maxLength' => 200]])),
            [__CLASS__, 'listReusable'],
            [__CLASS__, 'canReusable']
        );
        Registry::add(
            'save-reusable-block',
            __('Create or update reusable block', 'advanced-gutenberg'),
            __('Create a reusable block or synced pattern, or update a verified ID. New objects require title and serialized block content and default to draft. Existing content changes require expected_hash from get-post-blocks. Other fields are preserved.', 'advanced-gutenberg'),
            Registry::object(['id' => ['type' => 'integer', 'minimum' => 1], 'title' => ['type' => 'string', 'minLength' => 1],
                'content' => ['type' => 'string', 'maxLength' => 200000], 'status' => ['type' => 'string', 'enum' => ['draft', 'publish']],
            'expected_hash' => ['type' => 'string',
            'pattern' => '^[a-f0-9]{64}$']]),
            [__CLASS__, 'saveReusable'],
            [__CLASS__, 'canReusable'],
            true,
            false
        );
        Registry::add(
            'delete-reusable-block',
            __('Trash reusable block', 'advanced-gutenberg'),
            __('Trash a verified reusable block ID using native WordPress permissions. Existing references are not rewritten; check Auto Insert and post usage first.', 'advanced-gutenberg'),
            Registry::object(['id' => ['type' => 'integer', 'minimum' => 1]], ['id']),
            [__CLASS__, 'deleteReusable'],
            [__CLASS__, 'canReusable'],
            true
        );
        Registry::add(
            'list-post-notes',
            __('Read Post Notes', 'advanced-gutenberg'),
            __('Read native WordPress notes for an editable post, or omit post_id to list across editable posts with edit_others_posts and moderate_comments. Native REST permissions and per-post edit permissions apply. Omits author emails and IP addresses. Results are paginated.', 'advanced-gutenberg'),
            Registry::object(array_merge($post, Registry::page(), ['status' => ['type' => 'string', 'enum' => ['all', 'open', 'resolved']]])),
            [__CLASS__, 'notes'],
            [__CLASS__, 'canReadNotes']
        );
        Registry::add(
            'save-post-note',
            __('Create, edit or resolve Post Note', 'advanced-gutenberg'),
            __('Create a note on an editable post, edit its content, reply to a note or resolve/reopen a verified note. Requires native note permissions. New notes need content. Note identity and post must match.', 'advanced-gutenberg'),
            Registry::object(array_merge($post, ['id' => ['type' => 'integer', 'minimum' => 1], 'content' => ['type' => 'string', 'maxLength' => 20000],
                'parent' => ['type' => 'integer', 'minimum' => 1], 'resolution' => ['type' => 'string', 'enum' => ['resolved', 'reopen']]]), ['post_id']),
            [__CLASS__, 'saveNote'],
            [__CLASS__, 'canNotes'],
            true,
            false
        );
        Registry::add(
            'delete-post-note',
            __('Trash Post Note', 'advanced-gutenberg'),
            __('Trash a verified note belonging to the requested editable post. Native comment/note deletion permissions apply.', 'advanced-gutenberg'),
            Registry::object(array_merge($post, ['id' => ['type' => 'integer', 'minimum' => 1]]), ['post_id', 'id']),
            [__CLASS__, 'deleteNote'],
            [__CLASS__, 'canNotes'],
            true
        );
    }

    public static function canScan(): bool
    {
        return current_user_can('manage_options') && Utilities::settingIsEnabled('enable_block_usage');
    }

    public static function usage(array $input)
    {
        $type = get_post_type_object($input['post_type'] ?? 'post');
        if (!$type || !$type->show_in_rest || !current_user_can($type->cap->edit_posts)) {
            return Registry::error(__('Choose an accessible REST-enabled post type.', 'advanced-gutenberg'));
        }
        if (isset($input['name']) && !in_array($input['name'], Operations::knownBlocks(), true)) {
            return Registry::error(__('Unknown block name.', 'advanced-gutenberg'));
        }
        $page = $input['page'] ?? 1;
        $query = new \WP_Query(['post_type' => $type->name, 'post_status' => 'any', 'posts_per_page' => $input['per_page'] ?? 20, 'paged' => $page, 'orderby' => 'ID', 'order' => 'ASC']);
        $items = [];
        foreach ($query->posts as $post) {
            if (!current_user_can('edit_post', $post->ID)) {
                continue;
            }
            $counts = [];
            self::countBlocks(parse_blocks($post->post_content), $counts);
            if (isset($input['name'])) {
                $counts = isset($counts[$input['name']]) ? [$input['name'] => $counts[$input['name']]] : [];
            }
            if ($counts) {
                $items[] = ['post_id' => $post->ID, 'post_type' => $post->post_type, 'counts' => (object) $counts];
            }
        }
        return ['posts' => $items, 'page' => $page, 'total_pages' => (int) $query->max_num_pages];
    }

    private static function countBlocks(array $blocks, array &$counts)
    {
        foreach ($blocks as $block) {
            if ($block['blockName']) {
                $counts[$block['blockName']] = ($counts[$block['blockName']] ?? 0) + 1;
            }
            if ($block['innerBlocks']) {
                self::countBlocks($block['innerBlocks'], $counts);
            }
        }
    }

    public static function canDiscover(): bool
    {
        return current_user_can('edit_posts');
    }

    public static function blockFields(array $input)
    {
        $type = \WP_Block_Type_Registry::get_instance()->get_registered($input['name']);
        if ($type) {
            return ['name' => $type->name, 'title' => $type->title ?: $type->name, 'attributes' => (object) $type->get_attributes(), 'supports' => (object) $type->supports, 'source' => 'server'];
        }
        foreach ((array) get_option('advgb_blocks_list', []) as $block) {
            $block = (array) $block;
            if (($block['name'] ?? '') === $input['name']) {
                return ['name' => $block['name'], 'title' => $block['title'] ?? $block['name'], 'attributes' => (object) ($block['attributes'] ?? []), 'supports' => (object) ($block['supports'] ?? []), 'source' => 'editor_snapshot'];
            }
        }
        return Registry::error(__('Unknown block type. Refresh the native editor catalogue.', 'advanced-gutenberg'), 404);
    }

    public static function canReusable(): bool
    {
        $type = get_post_type_object('wp_block');
        return Utilities::settingIsEnabled('reusable_blocks') && $type && current_user_can($type->cap->edit_posts);
    }

    public static function canReadNotes(array $input = []): bool
    {
        if (!Utilities::settingIsEnabled('enable_post_notes') || !current_user_can('edit_posts')) {
            return false;
        }
        return isset($input['post_id']) ? Abilities::canInspectPostBlocks($input)
            : (current_user_can('edit_others_posts') && current_user_can('moderate_comments'));
    }

    public static function canNotes(array $input = []): bool
    {
        return Utilities::settingIsEnabled('enable_post_notes') && current_user_can('edit_posts') && Abilities::canInspectPostBlocks($input);
    }

    public static function get(array $input)
    {
        $post = get_post($input['post_id']);
        if (!$post) {
            return Registry::error(__('Post not found.', 'advanced-gutenberg'), 404);
        }
        if (strlen($post->post_content) > 200000) {
            return Registry::error(__('This post exceeds the 200 KB block editing limit. Use the WordPress editor.', 'advanced-gutenberg'));
        }
        $result = [];
        $budget = self::INVENTORY_MAX_BYTES;
        if (!self::inventory(parse_blocks($post->post_content), [], $result, $budget)) {
            return Registry::error(__('This post is too deeply nested or too large to list block by block. Use the WordPress editor.', 'advanced-gutenberg'));
        }
        return ['post_id' => $post->ID, 'hash' => hash('sha256', $post->post_content), 'blocks' => $result,
            'editor_preferences' => (object) ['editor_width' => get_post_meta($post->ID, 'advgb_blocks_editor_width', true),
                'columns_visual_guide' => get_post_meta($post->ID, 'advgb_blocks_columns_visual_guide', true)]];
    }

    /** Limits for the block inventory: nesting depth and total serialized output. @since 3.7.7 */
    const INVENTORY_MAX_DEPTH = 30;
    const INVENTORY_MAX_BYTES = 2000000;

    private static function inventory(array $blocks, array $prefix, array &$result, int &$budget): bool
    {
        if (count($prefix) >= self::INVENTORY_MAX_DEPTH) {
            return false;
        }
        foreach ($blocks as $index => $block) {
            $path = array_merge($prefix, [$index]);
            $type = isset($block['blockName']) ? \WP_Block_Type_Registry::get_instance()->get_registered($block['blockName']) : null;
            $serialized = serialize_block($block);
            $budget -= strlen($serialized);
            if ($budget < 0) {
                return false;
            }
            $result[] = ['path' => $path, 'name' => $block['blockName'], 'attributes' => (object) $block['attrs'],
                'attribute_schema' => $type ? (object) $type->get_attributes() : new \stdClass(), 'serialized_block' => $serialized];
            if ($block['innerBlocks'] && !self::inventory($block['innerBlocks'], $path, $result, $budget)) {
                return false;
            }
        }
        return true;
    }

    public static function edit(array $input)
    {
        $post = get_post($input['post_id']);
        if (!$post || hash('sha256', $post->post_content) !== $input['expected_hash']) {
            return Registry::error(__('Post changed. Read get-post-blocks again before editing.', 'advanced-gutenberg'), 409);
        }
        if (strlen($post->post_content) > 200000) {
            return Registry::error(__('This post exceeds the 200 KB block editing limit. Use the WordPress editor.', 'advanced-gutenberg'));
        }
        $fragment = null;
        if (in_array($input['operation'], ['replace', 'insert_before', 'insert_after'], true)) {
            if (empty($input['block_content'])) {
                return Registry::error(__('Supply one complete serialized block.', 'advanced-gutenberg'));
            }
            $parsed = array_values(array_filter(parse_blocks($input['block_content']), static function ($block) {
                return $block['blockName'] !== null || trim($block['innerHTML']) !== '';
            }));
            if (count($parsed) !== 1 || !$parsed[0]['blockName'] || !in_array($parsed[0]['blockName'], Operations::knownBlocks(), true)) {
                return Registry::error(__('Use exactly one known serialized block.', 'advanced-gutenberg'));
            }
            $valid = self::validateFragment($parsed);
            if (is_wp_error($valid)) {
                return $valid;
            }
            $fragment = $parsed[0];
        }
        if ($input['operation'] === 'set_visibility') {
            if (!Utilities::settingIsEnabled('block_controls') || !isset($input['visibility'])) {
                return Registry::error(__('Block Controls must be enabled and visibility supplied.', 'advanced-gutenberg'));
            }
            $valid = Features::validateVisibility($input['visibility']);
            if (is_wp_error($valid)) {
                return $valid;
            }
        }
        $blocks = parse_blocks($post->post_content);
        $valid = self::modify($blocks, $input['path'], $input, $fragment);
        if (is_wp_error($valid)) {
            return $valid;
        }
        $content = serialize_blocks($blocks);
        if (strlen($content) > 200000) {
            return Registry::error(__('The proposed edit exceeds the 200 KB block editing limit.', 'advanced-gutenberg'));
        }
        $result = self::updateNative($post, ['content' => $content]);
        return is_wp_error($result) ? $result : self::get($input);
    }

    public static function canAssignStyle(array $input = []): bool
    {
        return Utilities::settingIsEnabled('enable_custom_styles') && Abilities::canInspectPostBlocks($input);
    }

    public static function assignStyle(array $input)
    {
        $input['operation'] = 'set_style';
        return self::edit($input);
    }

    private static function styleBlock(array &$block, array $input)
    {
        if (!$block['blockName'] || (!defined('ADVANCED_GUTENBERG_PRO_LOADED') && in_array($block['blockName'], array_column(Utilities::getProBlocks(), 'name'), true))) {
            return Registry::error(__('Unknown or unavailable Pro block type.', 'advanced-gutenberg'));
        }
        $definition = self::blockFields(['name' => $block['blockName']]);
        if (is_wp_error($definition)) {
            return $definition;
        }
        if (isset($definition['supports']->customStyle) && !$definition['supports']->customStyle) {
            return Registry::error(__('This block does not support custom styles.', 'advanced-gutenberg'));
        }
        $style = null;
        if (empty($input['remove'])) {
            foreach (Styles::stored() as $candidate) {
                if ((int) $candidate['id'] === ($input['style_id'] ?? 0)) {
                    $style = $candidate;
                    break;
                }
            }
            if (!$style || empty($style['name']) || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_-]*$/', $style['name'])) {
                return Registry::error(__('Choose an existing style with a valid class name.', 'advanced-gutenberg'));
            }
        }
        $previous = $block['attrs']['customStyle'] ?? '';
        // Match the native save-content filter, without replacing unrelated block markup.
        foreach ($block['innerContent'] as $index => $html) {
            if (!is_string($html) || trim($html) === '') {
                continue;
            }
            $processor = new \WP_HTML_Tag_Processor($html);
            if (!$processor->next_tag()) {
                return Registry::error(__('This block has no supported HTML wrapper for style assignment.', 'advanced-gutenberg'));
            }
            if ($previous !== '') {
                $processor->remove_class($previous);
            }
            if ($style) {
                $processor->add_class($style['name']);
            }
            $block['innerContent'][$index] = $processor->get_updated_html();
            break;
        }
        if ($style) {
            $block['attrs']['customStyle'] = $style['name'];
            $block['attrs']['identifyColor'] = $style['identifyColor'] ?? '#000000';
        } else {
            unset($block['attrs']['customStyle'], $block['attrs']['identifyColor']);
        }
        $block['innerHTML'] = implode('', array_filter($block['innerContent'], 'is_string'));
        return true;
    }

    private static function validateFragment(array $blocks)
    {
        $pro = array_column(Utilities::getProBlocks(), 'name');
        foreach ($blocks as $block) {
            if ($block['blockName'] && (!in_array($block['blockName'], Operations::knownBlocks(), true) || (!defined('ADVANCED_GUTENBERG_PRO_LOADED') && in_array($block['blockName'], $pro, true)))) {
                return Registry::error(__('Unknown or unavailable Pro block type.', 'advanced-gutenberg'));
            }
            if ($block['innerBlocks']) {
                $valid = self::validateFragment($block['innerBlocks']);
                if (is_wp_error($valid)) {
                    return $valid;
                }
            }
        }
        return true;
    }

    private static function modify(array &$blocks, array $path, array $input, $fragment)
    {
        $index = array_shift($path);
        if (!isset($blocks[$index])) {
            return Registry::error(__('Block path does not exist. Read get-post-blocks again.', 'advanced-gutenberg'));
        }
        if ($path) {
            $before = count($blocks[$index]['innerBlocks']);
            $target = $path[0];
            $valid = self::modify($blocks[$index]['innerBlocks'], $path, $input, $fragment);
            if (is_wp_error($valid)) {
                return $valid;
            }
            $after = count($blocks[$index]['innerBlocks']);
            if ($after !== $before) {
                $seen = 0;
                foreach ($blocks[$index]['innerContent'] as $slot => $chunk) {
                    if ($chunk !== null) {
                        continue;
                    }
                    if ($seen++ === $target) {
                        if ($after < $before) {
                            array_splice($blocks[$index]['innerContent'], $slot, 1);
                        } else {
                            array_splice($blocks[$index]['innerContent'], $slot, 0, [null]);
                        }
                        break;
                    }
                }
            }
            return true;
        }
        if ($input['operation'] === 'set_style') {
            return self::styleBlock($blocks[$index], $input);
        }
        if ($input['operation'] === 'set_visibility') {
            $visibility = [];
            foreach ($input['visibility'] as $control) {
                if ($control['control'] === 'presets') {
                    $control += ['enabled' => true, 'logic' => 'any'];
                } else {
                    $control['type'] = $control['control'];
                    $control = Presets::normalizeRule($control);
                    unset($control['type']);
                }
                $visibility[] = $control;
            }
            $blocks[$index]['attrs']['advgbBlockControls'] = $visibility;
        } elseif ($input['operation'] === 'replace') {
            $blocks[$index] = $fragment;
        } elseif ($input['operation'] === 'remove') {
            array_splice($blocks, $index, 1);
        } else {
            array_splice($blocks, $index + ($input['operation'] === 'insert_after' ? 1 : 0), 0, [$fragment]);
        }
        return true;
    }

    public static function updateNative($post, array $data)
    {
        $type = get_post_type_object($post->post_type);
        if (!$type || !$type->show_in_rest) {
            return Registry::error(__('Post type is not editable through its native REST controller.', 'advanced-gutenberg'));
        }
        $controller = $type->get_rest_controller();
        if (!$controller instanceof \WP_REST_Posts_Controller) {
            return Registry::error(__('Unsupported native post controller.', 'advanced-gutenberg'));
        }
        $request = new \WP_REST_Request('POST');
        $request->set_param('id', $post->ID);
        foreach ($data as $key => $value) {
            $request->set_param($key, $value);
        }
        $allowed = $controller->update_item_permissions_check($request);
        if (is_wp_error($allowed) || !$allowed) {
            return is_wp_error($allowed) ? $allowed : Registry::error(__('Cannot edit this post.', 'advanced-gutenberg'), 403);
        }
        return $controller->update_item($request);
    }

    public static function listReusable(array $input): array
    {
        $page = $input['page'] ?? 1;
        $query = new \WP_Query(['post_type' => 'wp_block', 'post_status' => ['publish', 'draft', 'pending', 'private'],
            's' => $input['search'] ?? '', 'posts_per_page' => $input['per_page'] ?? 20, 'paged' => $page, 'orderby' => 'ID', 'order' => 'ASC']);
        $items = [];
        foreach ($query->posts as $post) {
            if (current_user_can('edit_post', $post->ID)) {
                $items[] = ['id' => $post->ID, 'title' => $post->post_title, 'status' => $post->post_status];
            }
        }
        return ['blocks' => $items, 'page' => $page, 'total_pages' => (int) $query->max_num_pages];
    }

    public static function saveReusable(array $input)
    {
        if (isset($input['content'])) {
            $valid = self::validateFragment(parse_blocks($input['content']));
            if (is_wp_error($valid)) {
                return $valid;
            }
        }
        if (isset($input['id'])) {
            $post = get_post($input['id']);
            if (!$post || $post->post_type !== 'wp_block' || !current_user_can('edit_post', $post->ID)) {
                return Registry::error(__('Reusable block not found or not editable.', 'advanced-gutenberg'), 403);
            }
            if (isset($input['content']) && (!isset($input['expected_hash']) || hash('sha256', $post->post_content) !== $input['expected_hash'])) {
                return Registry::error(__('Read get-post-blocks and supply the latest expected_hash.', 'advanced-gutenberg'), 409);
            }
            $data = array_intersect_key($input, array_flip(['title', 'content', 'status']));
            $response = self::updateNative($post, $data);
        } else {
            if (empty($input['title']) || empty($input['content'])) {
                return Registry::error(__('Title and serialized content are required.', 'advanced-gutenberg'));
            }
            $request = new \WP_REST_Request('POST');
            foreach (array_intersect_key($input, array_flip(['title', 'content', 'status'])) as $field => $value) {
                $request->set_param($field, $value);
            }
            if (!isset($input['status'])) {
                $request->set_param('status', 'draft');
            }
            $controller = get_post_type_object('wp_block')->get_rest_controller();
            $allowed = $controller->create_item_permissions_check($request);
            if (is_wp_error($allowed) || !$allowed) {
                return is_wp_error($allowed) ? $allowed : Registry::error(__('Cannot create reusable blocks.', 'advanced-gutenberg'), 403);
            }
            $response = $controller->create_item($request);
        }
        if (is_wp_error($response)) {
            return $response;
        }
        $data = $response->get_data();
        return ['id' => $data['id'], 'status' => $data['status'], 'hash' => hash('sha256', get_post_field('post_content', $data['id']))];
    }

    public static function deleteReusable(array $input)
    {
        if (!EMPTY_TRASH_DAYS) {
            return Registry::error(__('Trash is disabled on this site. Use WordPress to permanently delete this object.', 'advanced-gutenberg'));
        }
        if (get_post_type($input['id']) !== 'wp_block' || !current_user_can('delete_post', $input['id'])) {
            return Registry::error(__('Cannot delete this reusable block.', 'advanced-gutenberg'), 403);
        }
        return wp_trash_post($input['id']) ? ['trashed' => $input['id']] : Registry::error(__('Could not trash reusable block.', 'advanced-gutenberg'));
    }

    private static function nativeNotes(string $method, string $suffix, array $data)
    {
        $request = new \WP_REST_Request($method, '/wp/v2/comments' . $suffix);
        foreach ($data as $key => $value) {
            $request->set_param($key, $value);
        }
        $response = rest_do_request($request);
        if ($response->is_error()) {
            return $response->as_error();
        }
        return $response;
    }

    public static function notes(array $input)
    {
        $response = self::nativeNotes('GET', '', ['post' => isset($input['post_id']) ? [$input['post_id']] : [], 'type' => 'note', 'context' => 'edit', 'status' => ['all' => 'all', 'open' => 'hold', 'resolved' => 'approve'][$input['status'] ?? 'all'],
            'page' => $input['page'] ?? 1, 'per_page' => $input['per_page'] ?? 20]);
        if (is_wp_error($response)) {
            return $response;
        }
        $items = [];
        foreach ($response->get_data() as $note) {
            if (!current_user_can('edit_post', $note['post'])) {
                continue;
            }
            $items[] = array_intersect_key($note, array_flip(['id', 'post', 'parent', 'content', 'status', 'meta', 'author', 'date']));
        }
        $headers = $response->get_headers();
        return ['notes' => $items, 'page' => $input['page'] ?? 1, 'total_pages' => (int) ($headers['X-WP-TotalPages'] ?? 0)];
    }

    public static function saveNote(array $input)
    {
        foreach (['id', 'parent'] as $field) {
            if (isset($input[$field])) {
                $comment = get_comment($input[$field]);
                if (!$comment || $comment->comment_type !== 'note' || (int) $comment->comment_post_ID !== $input['post_id']) {
                    return Registry::error(__('Note ID and post do not match.', 'advanced-gutenberg'));
                }
            }
        }
        if (!isset($input['id']) && empty($input['content']) && !isset($input['resolution'])) {
            return Registry::error(__('A new note needs content.', 'advanced-gutenberg'));
        }
        $data = ['post' => $input['post_id'], 'type' => 'note'];
        if (!isset($input['id'])) {
            $data['status'] = 'hold';
        }
        foreach (['content', 'parent'] as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }
        if (isset($input['resolution'])) {
            if (!isset($input['id']) || isset($input['parent'])) {
                return Registry::error(__('Resolve or reopen an existing note ID.', 'advanced-gutenberg'));
            }
            $previousStatus = get_comment($input['id'])->comment_approved;
            $status = $input['resolution'] === 'resolved' ? 'approved' : 'hold';
            $response = self::nativeNotes('POST', '/' . $input['id'], ['status' => $status]);
            if (is_wp_error($response)) {
                return $response;
            }
            $data['status'] = $status;
            $data['parent'] = $input['id'];
            $data['content'] = $input['content'] ?? '';
            $data['meta'] = ['_wp_note_status' => $input['resolution']];
            $response = self::nativeNotes('POST', '', $data);
            if (is_wp_error($response)) {
                // Compensate only this operation's status change; retain the original REST failure.
                $restored = wp_update_comment(['comment_ID' => $input['id'], 'comment_approved' => $previousStatus], true);
                if (is_wp_error($restored) || !$restored) {
                    return Registry::error(__('The note status changed, but its audit entry and status rollback failed. Read the note before retrying.', 'advanced-gutenberg'), 500);
                }
            }
        } else {
            $response = self::nativeNotes('POST', isset($input['id']) ? '/' . $input['id'] : '', $data);
        }
        if (is_wp_error($response)) {
            return $response;
        }
        $note = $response->get_data();
        return ['note' => (object) array_intersect_key($note, array_flip(['id', 'post', 'parent', 'content', 'status', 'meta']))];
    }

    public static function deleteNote(array $input)
    {
        $note = get_comment($input['id']);
        if (!$note || $note->comment_type !== 'note' || (int) $note->comment_post_ID !== $input['post_id']) {
            return Registry::error(__('Note ID and post do not match.', 'advanced-gutenberg'));
        }
        $response = self::nativeNotes('DELETE', '/' . $input['id'], ['force' => false]);
        return is_wp_error($response) ? $response : ['trashed' => $input['id']];
    }
}
