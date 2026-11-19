<?php

/** Saved visibility presets using the native Blocks preset API. @since 3.7.6 */

namespace PublishPress\Blocks\Mcp;

use PublishPress\Blocks\RestAPI\Presets as NativePresets;

\defined('ABSPATH') || exit;

class Presets
{
    public static function register()
    {
        Registry::add(
            'get-control-preset-fields',
            __('Discover visibility preset fields', 'advanced-gutenberg'),
            __('Discover native visibility preset rule types, fields and choices. Rules within a set use AND; sets use OR. Read a saved preset with list-control-presets before changing it.', 'advanced-gutenberg'),
            Registry::object(),
            [__CLASS__, 'fields'],
            [NativePresets::class, 'checkPermissions']
        );
        Registry::add(
            'save-control-preset',
            __('Create or update visibility preset', 'advanced-gutenberg'),
            __('Create a named visibility preset or update an existing ID. Supplied controlSets replace that preset definition; omitted title or controlSets are preserved on update. This does not automatically attach the preset to post blocks.', 'advanced-gutenberg'),
            Registry::object(['id' => ['type' => 'string', 'pattern' => '^[a-zA-Z0-9_-]+$'], 'title' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 200],
            'controlSets' => self::setsSchema()]),
            [__CLASS__, 'save'],
            [NativePresets::class, 'checkPermissions'],
            true,
            false
        );
        Registry::add(
            'delete-control-preset',
            __('Delete visibility preset', 'advanced-gutenberg'),
            __('Delete a verified saved visibility preset ID. Existing post references are not rewritten. Read its usage before deleting.', 'advanced-gutenberg'),
            Registry::object(['id' => ['type' => 'string', 'pattern' => '^[a-zA-Z0-9_-]+$']], ['id']),
            [__CLASS__, 'delete'],
            [NativePresets::class, 'checkPermissions'],
            true
        );
    }

    public static function ruleSchemas(): array
    {
        $strings = Registry::strings();
        $approach = ['type' => 'string', 'enum' => ['include', 'exclude']];
        $schedule = Registry::object([
            'dateFrom' => ['type' => ['string', 'null']], 'dateTo' => ['type' => ['string', 'null']],
            'timeFrom' => ['type' => ['string', 'null']], 'timeTo' => ['type' => ['string', 'null']],
            'recurring' => ['type' => 'boolean'], 'days' => ['type' => 'array', 'items' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 7], 'maxItems' => 7],
            'timezone' => ['type' => 'string', 'maxLength' => 100],
        ]);
        return [
            'schedule' => ['schedules' => ['type' => 'array', 'maxItems' => 30, 'items' => $schedule]],
            'user_role' => ['roles' => $strings, 'approach' => ['type' => 'string', 'enum' => ['include', 'exclude', 'public', 'hidden', 'login', 'logout']]],
            'device_type' => ['devices' => ['type' => 'array', 'items' => ['type' => 'string', 'enum' => ['desktop', 'tablet', 'mobile']], 'maxItems' => 3]],
            'device_width' => ['min_width' => ['type' => 'integer', 'minimum' => 0], 'max_width' => ['type' => 'integer', 'minimum' => 0]],
            'browser_device' => ['browsers' => ['type' => 'array', 'maxItems' => 10, 'items' => ['type' => 'string', 'enum' => ['chrome', 'firefox', 'safari', 'edge', 'opera', 'internet explorer']]], 'approach' => $approach],
            'operating_system' => ['systems' => ['type' => 'array', 'maxItems' => 10, 'items' => ['type' => 'string', 'enum' => ['windows', 'mac', 'linux', 'android', 'ios', 'chrome os']]], 'approach' => $approach],
            'cookie' => self::comparisonFields('name'), 'user_meta' => self::comparisonFields(), 'post_meta' => self::comparisonFields(),
            'query_string' => ['queries' => $strings, 'logic' => ['type' => 'string', 'enum' => ['all', 'any']], 'approach' => $approach],
            'capabilities' => ['capabilities' => $strings, 'approach' => $approach],
            'archive' => ['taxonomies' => ['type' => 'array', 'maxItems' => 100, 'items' => Registry::object(['tax' => ['type' => 'string'], 'terms' => Registry::ids()], ['tax'])], 'approach' => $approach],
            'page' => ['pages' => ['type' => 'array', 'maxItems' => 5, 'items' => ['type' => 'string', 'enum' => ['home', 'blog', 'archive', 'search', 'page404']]], 'approach' => $approach],
        ];
    }

    private static function comparisonFields(string $key = 'key'): array
    {
        return [$key => ['type' => 'string', 'minLength' => 1], 'value' => ['type' => 'string'],
            'condition' => ['type' => 'string', 'enum' => ['=', '!=', '>', '<', '>=', '<=', 'contains', 'beginsWith', 'endsWith', 'doesNotContain', 'doesNotBeginWith', 'doesNotEndWith', 'null', 'notNull', 'in', 'notIn', 'between', 'notBetween']],
            'approach' => ['type' => 'string', 'enum' => ['include', 'exclude']]];
    }

    public static function setsSchema(): array
    {
        $variants = [];
        foreach (self::ruleSchemas() as $type => $fields) {
            $properties = array_merge(['id' => ['type' => 'string'], 'type' => ['type' => 'string', 'enum' => [$type]],
                'enabled' => ['type' => 'boolean'], 'expanded' => ['type' => 'boolean']], $fields);
            $variants[] = Registry::object($properties, ['type']);
        }
        return ['type' => 'array', 'maxItems' => 30, 'items' => Registry::object([
            'id' => ['type' => 'string'], 'expanded' => ['type' => 'boolean'],
            'rules' => ['type' => 'array', 'maxItems' => 30, 'items' => ['oneOf' => $variants]],
        ], ['rules'])];
    }

    public static function fields(): array
    {
        return ['rule_fields' => (object) self::ruleSchemas(), 'controlSets_schema' => self::setsSchema(), 'visibility_schema' => Features::visibilitySchema(), 'roles' => array_keys(wp_roles()->roles)];
    }

    public static function validateSets(array $sets)
    {
        $valid = Registry::validate($sets, self::setsSchema());
        if (is_wp_error($valid)) {
            return $valid;
        }
        foreach ($sets as $set) {
            foreach ($set['rules'] as $rule) {
                foreach ($rule['taxonomies'] ?? [] as $taxonomy) {
                    if (!get_taxonomy($taxonomy['tax'])) {
                        return Registry::error(__('Unknown archive taxonomy.', 'advanced-gutenberg'));
                    }
                    foreach ($taxonomy['terms'] ?? [] as $id) {
                        if (!term_exists($id, $taxonomy['tax'])) {
                            return Registry::error(__('Unknown archive term.', 'advanced-gutenberg'));
                        }
                    }
                }
                foreach (isset($rule['roles']) ? $rule['roles'] : [] as $role) {
                    if (!get_role($role)) {
                        return Registry::error(__('Unknown role in visibility rule.', 'advanced-gutenberg'));
                    }
                }
                foreach (isset($rule['schedules']) ? $rule['schedules'] : [] as $schedule) {
                    if (isset($schedule['timezone']) && !in_array($schedule['timezone'], \DateTimeZone::listIdentifiers(), true)) {
                        return Registry::error(__('Unknown timezone.', 'advanced-gutenberg'));
                    }
                    foreach (['dateFrom', 'dateTo', 'timeFrom', 'timeTo'] as $field) {
                        if (!empty($schedule[$field]) && strtotime($schedule[$field]) === false) {
                            return Registry::error(__('Invalid schedule date or time.', 'advanced-gutenberg'));
                        }
                    }
                }
                if (isset($rule['min_width'], $rule['max_width']) && $rule['max_width'] > 0 && $rule['min_width'] > $rule['max_width']) {
                    return Registry::error(__('Minimum width exceeds maximum width.', 'advanced-gutenberg'));
                }
            }
        }
        return true;
    }

    public static function save(array $input)
    {
        $stored = (array) get_option('advgb_block_control_presets', []);
        if (isset($input['id']) && !isset($stored[$input['id']])) {
            return Registry::error(__('Preset ID not found.', 'advanced-gutenberg'), 404);
        }
        $existing = isset($input['id']) ? $stored[$input['id']] : [];
        $data = array_merge($existing, $input);
        if (empty($data['title'])) {
            return Registry::error(__('A title is required to create a preset.', 'advanced-gutenberg'));
        }
        if (isset($input['controlSets'])) {
            $valid = self::validateSets($input['controlSets']);
            if (is_wp_error($valid)) {
                return $valid;
            }
        }
        // Native sanitizer supports every rule; use its current page identifier.
        $data['controlSets'] = isset($data['controlSets']) ? $data['controlSets'] : [];
        foreach ($data['controlSets'] as &$set) {
            foreach ($set['rules'] as &$rule) {
                $rule = self::normalizeRule($rule);
            }
            unset($rule);
        }
        unset($set);
        $request = new \WP_REST_Request('POST');
        $request->set_header('Content-Type', 'application/json');
        $request->set_body(wp_json_encode($data));
        $response = NativePresets::savePreset($request);
        return is_wp_error($response) ? $response : $response->get_data();
    }

    public static function normalizeRule(array $rule): array
    {
        $rule += ['enabled' => true, 'expanded' => true];
        foreach (self::ruleSchemas()[$rule['type']] as $key => $schema) {
            if ($schema['type'] === 'array' && !isset($rule[$key])) {
                $rule[$key] = [];
            }
        }
        if ($rule['type'] === 'schedule') {
            foreach ($rule['schedules'] as &$schedule) {
                $schedule += ['dateFrom' => null, 'dateTo' => null, 'timeFrom' => null, 'timeTo' => null, 'days' => [], 'recurring' => false, 'timezone' => wp_timezone_string()];
            }
            unset($schedule);
        }
        return $rule;
    }

    public static function delete(array $input)
    {
        $request = new \WP_REST_Request('DELETE');
        $request->set_param('id', $input['id']);
        $response = NativePresets::deletePreset($request);
        return is_wp_error($response) ? $response : $response->get_data();
    }
}
