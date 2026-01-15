<?php
namespace PublishPress\Blocks\RestAPI;

class Presets
{

    public static function init()
    {
        add_action('rest_api_init', [__CLASS__, 'registerRoutes']);
    }

    public static function registerRoutes()
    {
        register_rest_route('advgb/v1', '/presets', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'getPresets'],
            'permission_callback' => [__CLASS__, 'checkPermissions']
        ]);

        register_rest_route('advgb/v1', '/presets', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'savePreset'],
            'permission_callback' => [__CLASS__, 'checkPermissions']
        ]);

        register_rest_route('advgb/v1', '/presets/(?P<id>[a-zA-Z0-9_-]+)', [
            'methods' => 'DELETE',
            'callback' => [__CLASS__, 'deletePreset'],
            'permission_callback' => [__CLASS__, 'checkPermissions']
        ]);

        register_rest_route('advgb/v1', '/sample-presets', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'createSamplePresetsEndpoint'],
            'permission_callback' => [__CLASS__, 'checkPermissions']
        ]);
    }

    public static function checkPermissions()
    {
        return current_user_can('manage_options');
    }

    public static function getPresets($request)
    {

        return rest_ensure_response(self::getPresetData());
    }

    public static function getPresetData()
    {
        $presets = (array) get_option('advgb_block_control_presets', []);

        $formatted_presets = [];
        foreach ($presets as $id => $preset) {
            $formatted_presets[] = array_merge($preset, ['id' => $id]);
        }

        return $formatted_presets;
    }

    public static function savePreset($request)
    {
        $data = $request->get_json_params();

        if (empty($data['title'])) {
            return new \WP_Error('missing_title', 'Preset title is required', ['status' => 400]);
        }

        $presets = get_option('advgb_block_control_presets', []);

        if (empty($data['id'])) {
            $data['id'] = 'preset_' . uniqid();
        }

        $preset_id = $data['id'];
        unset($data['id']);

        $preset_data = [
            'title' => sanitize_text_field($data['title']),
            'controlSets' => self::sanitizeControlSets($data['controlSets'] ?? []),
            'created' => current_time('mysql'),
            'modified' => current_time('mysql')
        ];

        if (isset($presets[$preset_id])) {
            $preset_data['created'] = $presets[$preset_id]['created'];
        }

        $presets[$preset_id] = $preset_data;
        update_option('advgb_block_control_presets', $presets);

        return rest_ensure_response([
            'id' => $preset_id,
            'presets' => self::getPresetData(),
            'success' => true,
            'message' => 'Preset saved successfully'
        ]);
    }

    public static function deletePreset($request)
    {
        $preset_id = $request->get_param('id');

        if (empty($preset_id)) {
            return new \WP_Error('missing_id', 'Preset ID is required', ['status' => 400]);
        }

        $presets = get_option('advgb_block_control_presets', []);

        if (!isset($presets[$preset_id])) {
            return new \WP_Error('preset_not_found', 'Preset not found', ['status' => 404]);
        }

        unset($presets[$preset_id]);

        update_option('advgb_block_control_presets', $presets);

        return rest_ensure_response([
            'success' => true,
            'presets' => self::getPresetData(),
            'message' => 'Preset deleted successfully'
        ]);
    }

    public static function createSamplePresetsEndpoint($request)
    {
        $created = self::createSamplePresets();
        $presets = self::getPresetData();

        if ($created) {
            return rest_ensure_response([
                'success' => true,
                'presets' => $presets,
                'message' => 'Sample presets created successfully'
            ]);
        } else {
            return rest_ensure_response([
                'success' => false,
                'presets' => $presets,
                'message' => 'Presets already exist'
            ], 200);
        }
    }

    private static function sanitizeControlSets($controlSets)
    {
        if (!is_array($controlSets)) {
            return [];
        }

        $sanitized = [];
        foreach ($controlSets as $controlSet) {
            if (!is_array($controlSet))
                continue;

            $sanitized_set = [
                'id' => sanitize_text_field($controlSet['id'] ?? uniqid()),
                'rules' => [],
                'expanded' => (bool) ($controlSet['expanded'] ?? true)
            ];

            if (isset($controlSet['rules']) && is_array($controlSet['rules'])) {
                foreach ($controlSet['rules'] as $rule) {
                    if (!is_array($rule))
                        continue;

                    $sanitized_rule = [
                        'id' => sanitize_text_field($rule['id'] ?? uniqid()),
                        'type' => sanitize_text_field($rule['type'] ?? ''),
                        'enabled' => (bool) ($rule['enabled'] ?? true),
                        'expanded' => (bool) ($rule['expanded'] ?? true)
                    ];

                    $sanitized_rule = array_merge($sanitized_rule, self::sanitizeRuleData($rule));

                    $sanitized_set['rules'][] = $sanitized_rule;
                }
            }

            $sanitized[] = $sanitized_set;
        }

        return $sanitized;
    }

    private static function sanitizeRuleData($rule)
    {
        $type = $rule['type'] ?? '';
        $sanitized = [];

        switch ($type) {
            case 'schedule':
                $sanitized['schedules'] = map_deep($rule['schedules'], 'sanitize_text_field') ?? [];
                break;

            case 'user_role':
                $sanitized['roles'] = array_map('sanitize_text_field', $rule['roles'] ?? []);
                $sanitized['approach'] = sanitize_text_field($rule['approach'] ?? 'include');
                break;

            case 'device_type':
                $sanitized['devices'] = array_map('sanitize_text_field', $rule['devices'] ?? []);
                break;

            case 'device_width':
                if (!empty($rule['min_width']) ) {
                    $sanitized['min_width'] = intval($rule['min_width']);
                }
                if (!empty($rule['max_width']) ) {
                    $sanitized['max_width'] = intval($rule['max_width']);
                }
                break;

            case 'browser_device':
                $sanitized['browsers'] = array_map('sanitize_text_field', $rule['browsers'] ?? []);
                $sanitized['approach'] = sanitize_text_field($rule['approach'] ?? 'include');
                break;

            case 'operating_system':
                $sanitized['systems'] = array_map('sanitize_text_field', $rule['systems'] ?? []);
                $sanitized['approach'] = sanitize_text_field($rule['approach'] ?? 'include');
                break;

            case 'cookie':
            case 'user_meta':
            case 'post_meta':
                $sanitized['key'] = sanitize_text_field($rule['key'] ?? '');
                $sanitized['condition'] = sanitize_text_field($rule['condition'] ?? '=');
                $sanitized['value'] = sanitize_text_field($rule['value'] ?? '');
                $sanitized['approach'] = sanitize_text_field($rule['approach'] ?? 'include');
                break;

            case 'query_string':
                $sanitized['queries'] = is_array($rule['queries']) ? array_map('sanitize_text_field', $rule['queries'] ?? []) : sanitize_textarea_field($rule['queries']);
                $sanitized['logic'] = sanitize_text_field($rule['logic'] ?? 'all');
                $sanitized['approach'] = sanitize_text_field($rule['approach'] ?? 'include');
                break;

            case 'capabilities':
                $sanitized['capabilities'] = array_map('sanitize_text_field', $rule['capabilities'] ?? []);
                $sanitized['approach'] = sanitize_text_field($rule['approach'] ?? 'include');
                break;

            case 'archive':
                $sanitized['taxonomies'] = array_map('sanitize_text_field', $rule['taxonomies'] ?? []);
                $sanitized['approach'] = sanitize_text_field($rule['approach'] ?? 'exclude');

            case 'pages':
                $sanitized['pages'] = array_map('sanitize_text_field', $rule['pages'] ?? []);
                $sanitized['approach'] = sanitize_text_field($rule['approach'] ?? 'exclude');
                break;
        }

        return $sanitized;
    }

    public static function createSamplePresets()
    {
        $presets = get_option('advgb_block_control_presets', []);

        // Only create samples if no presets exist
        if (!empty($presets)) {
            return false;
        }

        $sample_presets = [
            'sample_business_hours' => [
                'title' => 'Business Hours (9-5 Weekdays)',
                'controlSets' => [
                    [
                        'id' => 'set_1',
                        'expanded' => true,
                        'rules' => [
                            [
                                'id' => 'rule_1',
                                'type' => 'schedule',
                                'enabled' => true,
                                'expanded' => true,
                                'schedules' => [
                                    [
                                        'dateFrom' => null,
                                        'dateTo' => null,
                                        'recurring' => true,
                                        'days' => [1, 2, 3, 4, 5],
                                        'timeFrom' => '09:00:00',
                                        'timeTo' => '17:00:00',
                                        'timezone' => 'UTC'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'created' => current_time('mysql'),
                'modified' => current_time('mysql')
            ],
            'sample_utm_campaign' => [
                'title' => 'UTM Campaign Visitors',
                'controlSets' => [
                    [
                        'id' => 'set_1',
                        'rules' => [
                            [
                                'id' => 'rule_1',
                                'type' => 'query_string',
                                'enabled' => true,
                                'queries' => ['utm_campaign', 'utm_source'],
                                'logic' => 'any',
                                'approach' => 'include'
                            ]
                        ]
                    ]
                ],
                'created' => current_time('mysql'),
                'modified' => current_time('mysql')
            ],
            'sample_homepage_only' => [
                'title' => 'Homepage Only',
                'controlSets' => [
                    [
                        'id' => 'set_1',
                        'rules' => [
                            [
                                'id' => 'rule_1',
                                'type' => 'page',
                                'enabled' => true,
                                'pages' => ['home'],
                                'approach' => 'include'
                            ]
                        ]
                    ]
                ],
                'created' => current_time('mysql'),
                'modified' => current_time('mysql')
            ]
        ];

        // Advanced sample?? Should we keep only this or maintain current merge status
        $advanced_samples = [
            'sample_ab_testing' => [
                'title' => 'A/B Testing - Group A',
                'controlSets' => [
                    [
                        'id' => 'set_1',
                        'rules' => [
                            [
                                'id' => 'rule_1',
                                'type' => 'cookie',
                                'enabled' => true,
                                'name' => 'ab_test_group',
                                'condition' => '=',
                                'value' => 'group_a',
                                'approach' => 'include'
                            ]
                        ]
                    ]
                ],
                'created' => current_time('mysql'),
                'modified' => current_time('mysql')
            ],
            'sample_geo_targeting' => [
                'title' => 'US Visitors Only',
                'controlSets' => [
                    [
                        'id' => 'set_1',
                        'rules' => [
                            [
                                'id' => 'rule_1',
                                'type' => 'cookie',
                                'enabled' => true,
                                'name' => 'country',
                                'condition' => '=',
                                'value' => 'US',
                                'approach' => 'include'
                            ]
                        ]
                    ]
                ],
                'created' => current_time('mysql'),
                'modified' => current_time('mysql')
            ],
            'sample_complex_rule' => [
                'title' => 'Logged-in Chrome Users on Desktop',
                'controlSets' => [
                    [
                        'id' => 'set_1',
                        'rules' => [
                            [
                                'id' => 'rule_1',
                                'type' => 'user_role',
                                'enabled' => true,
                                'approach' => 'login',
                                'roles' => []
                            ],
                            [
                                'id' => 'rule_2',
                                'type' => 'browser_device',
                                'enabled' => true,
                                'browsers' => ['chrome'],
                                'approach' => 'include'
                            ],
                            [
                                'id' => 'rule_3',
                                'type' => 'device_type',
                                'enabled' => true,
                                'devices' => ['desktop']
                            ]
                        ]
                    ]
                ],
                'created' => current_time('mysql'),
                'modified' => current_time('mysql')
            ]
        ];

        $sample_presets = array_merge($sample_presets, $advanced_samples);

        update_option('advgb_block_control_presets', $sample_presets);
        return true;
    }


}

Presets::init();