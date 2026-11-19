<?php

/** Blocks feature action registration and schema helpers. @since 3.7.6 */

namespace PublishPress\Blocks\Mcp;

\defined('ABSPATH') || exit;

class Registry
{
    public static function object(array $properties = [], array $required = []): array
    {
        return ['type' => 'object', 'required' => $required, 'additionalProperties' => false]
            + ($properties ? ['properties' => $properties] : []);
    }

    public static function strings(): array
    {
        return ['type' => 'array', 'maxItems' => 200, 'uniqueItems' => true, 'items' => ['type' => 'string', 'maxLength' => 500]];
    }

    public static function ids(): array
    {
        return ['type' => 'array', 'maxItems' => 200, 'uniqueItems' => true, 'items' => ['type' => 'integer', 'minimum' => 1]];
    }

    public static function page(): array
    {
        return ['page' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10000, 'default' => 1],
            'per_page' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 20]];
    }

    public static function add(string $name, string $label, string $description, array $schema, array $callback, array $permission, bool $write = false, bool $idempotent = true)
    {
        wp_register_ability('publishpress-blocks/' . $name, [
            'label' => $label, 'description' => $description, 'category' => 'publishpress',
            'input_schema' => $schema, 'output_schema' => self::output($name),
            'execute_callback' => self::guard($callback, $schema),
            'permission_callback' => self::guard($permission, $schema, true),
            'meta' => ['public' => false, 'show_in_rest' => false, 'mcp' => ['public' => false],
                'annotations' => ['readonly' => !$write, 'destructive' => $write, 'idempotent' => $idempotent]],
        ]);
    }

    /** Validate types before callbacks can read identifiers or mutate settings. @since 3.7.7 */
    public static function guard(callable $callback, array $schema, bool $permission = false): callable
    {
        return static function ($values = []) use ($callback, $schema, $permission) {
            if (!self::matchesType($values, $schema)) {
                return $permission ? false : self::error(__('Use the exact input types shown in discovery.', 'advanced-gutenberg'));
            }
            return $callback($values);
        };
    }

    private static function output(string $name): array
    {
        $object = ['type' => 'object'];
        $integer = ['type' => 'integer'];
        $string = ['type' => 'string'];
        $list = ['type' => 'array', 'items' => $object];
        $properties = [
            'get-control-preset-fields' => ['rule_fields' => $object, 'controlSets_schema' => $object, 'visibility_schema' => $object, 'roles' => Registry::strings()],
            'save-control-preset' => ['id' => $string, 'presets' => $list, 'success' => ['type' => 'boolean'], 'message' => $string],
            'delete-control-preset' => ['success' => ['type' => 'boolean'], 'presets' => $list, 'message' => $string],
            'get-custom-style-fields' => ['fields' => $object, 'pro_active' => ['type' => 'boolean']],
            'save-custom-style' => ['style' => $object], 'delete-custom-style' => ['deleted' => $integer],
            'get-auto-insert-fields' => ['fields' => $object, 'post_types' => Registry::strings(), 'taxonomies' => Registry::strings(), 'pro_active' => ['type' => 'boolean']],
            'save-auto-insert-rule' => ['id' => $integer, 'status' => $string, 'settings' => $object], 'delete-auto-insert-rule' => ['trashed' => $integer],
            'scan-block-usage' => ['posts' => $list, 'page' => $integer, 'total_pages' => $integer],
            'get-block-type-fields' => ['name' => $string, 'title' => $string, 'attributes' => $object, 'supports' => $object, 'source' => $string],
            'get-post-blocks' => ['post_id' => $integer, 'hash' => $string, 'blocks' => $list, 'editor_preferences' => $object],
            'list-reusable-blocks' => ['blocks' => $list, 'page' => $integer, 'total_pages' => $integer],
            'save-reusable-block' => ['id' => $integer, 'status' => $string, 'hash' => $string], 'delete-reusable-block' => ['trashed' => $integer],
            'list-post-notes' => ['notes' => $list, 'page' => $integer, 'total_pages' => $integer], 'save-post-note' => ['note' => $object], 'delete-post-note' => ['trashed' => $integer],
            'get-feature-settings' => ['feature' => $string, 'settings' => $object, 'schema' => $object, 'roles' => Registry::strings(), 'pro_active' => ['type' => 'boolean']],
            'update-post-editor-settings' => ['post_id' => $integer, 'preferences' => $object],
            'list-form-submissions' => ['form' => $string, 'submissions' => ['type' => 'array', 'items' => ['type' => 'object', 'properties' => ['date' => $string, 'name' => $string, 'fname' => $string, 'lname' => $string, 'email' => $string, 'msg' => $string], 'additionalProperties' => false]], 'page' => $integer, 'total_pages' => $integer],
            'get-form-submission-counts' => ['contact' => $integer, 'newsletter' => $integer],
        ];
        $properties['list-features'] = ['features' => $list, 'pro_active' => ['type' => 'boolean']];
        $properties['bulk-update-block-permissions'] = ['results' => $list, 'dry_run' => ['type' => 'boolean']];
        $properties['bulk-update-feature-settings'] = $properties['bulk-update-block-permissions'];
        $properties['assign-block-style'] = $properties['get-post-blocks'];
        $properties['edit-post-block'] = $properties['get-post-blocks'];
        $properties['update-feature-settings'] = $properties['get-feature-settings'];
        return ['type' => 'object', 'properties' => $properties[$name], 'required' => array_keys($properties[$name]), 'additionalProperties' => false];
    }

    public static function error(string $message, int $status = 400): \WP_Error
    {
        return new \WP_Error('publishpress_blocks_invalid_operation', $message, ['status' => $status]);
    }

    public static function validate($value, array $schema)
    {
        if (!self::matchesType($value, $schema)) {
            return self::error(__('Use the exact field types shown in discovery.', 'advanced-gutenberg'));
        }
        return rest_validate_value_from_schema($value, $schema, 'input');
    }

    /** Reject scalar coercion and malformed nested lists before permission or write callbacks. @since 3.7.7 */
    public static function matchesType($value, array $schema, int $depth = 0): bool
    {
        if ($depth > 40) {
            return false;
        }
        foreach (['oneOf', 'anyOf'] as $keyword) {
            if (isset($schema[$keyword])) {
                foreach ($schema[$keyword] as $alternative) {
                    if (!is_wp_error(rest_validate_value_from_schema($value, $alternative))
                        && self::matchesType($value, $alternative, $depth + 1)) {
                        return true;
                    }
                }
                return false;
            }
        }
        $types = (array) ($schema['type'] ?? []);
        $actual = is_bool($value) ? 'boolean' : (is_int($value) ? 'integer' : (is_float($value) ? 'number'
            : (is_string($value) ? 'string' : ($value === null ? 'null' : (is_array($value) || is_object($value) ? 'object' : 'invalid')))));
        if (is_array($value) && in_array('array', $types, true) && (!$value || array_keys($value) === range(0, count($value) - 1))) {
            $actual = 'array';
        }
        if ($types && !in_array($actual, $types, true) && !($actual === 'integer' && in_array('number', $types, true))) {
            return false;
        }
        if (is_array($value) || is_object($value)) {
            foreach ((array) $value as $key => $child) {
                $field = $actual === 'array' ? ($schema['items'] ?? []) : ($schema['properties'][$key] ?? ($schema['additionalProperties'] ?? []));
                if ($field === false || !self::matchesType($child, is_array($field) ? $field : [], $depth + 1)) {
                    return false;
                }
            }
        }
        return true;
    }

}
