<?php
/**
 * @package wp-content-aware-engine
 * @author Joachim Jensen <joachim@dev.institute>
 * @license GPLv3
 * @copyright 2023 by Joachim Jensen
 */

defined('ABSPATH') || exit;

/**
 *
 * BuddyPress Member Page Module
 * Requires BuddyPress 2.6+
 *
 * Detects if current content is:
 * a) a specific buddypress member page
 *
 */
class WPCAModule_bp_member extends WPCAModule_Base
{
    /**
     * @var string
     */
    protected $category = 'plugins';

    public function __construct()
    {
        $label = sprintf(
            __('%s Profile Sections'),
            defined('BP_PLATFORM_VERSION')
                ? __('BuddyBoss', 'buddyboss')
                : __('BuddyPress', 'buddypress')
        );
        parent::__construct('bp_member', $label);
        $this->default_value = 0;
        $this->placeholder = __('All Sections', WPCA_DOMAIN);
        $this->icon = 'dashicons-buddicons-buddypress-logo:#d84800';
        $this->query_name = 'cbp';
    }

    /**
     * @inheritDoc
     */
    public function can_enable()
    {
        return defined('BP_VERSION');
    }

    /**
     * @inheritDoc
     */
    public function initiate()
    {
        parent::initiate();
        add_filter(
            'wpca/module/static/in-context',
            [$this,'static_is_content']
        );
    }

    /**
     * @inheritDoc
     */
    protected function _get_content($args = [])
    {
        if (isset($args['paged']) && $args['paged'] > 1) {
            return [];
        }

        $bp = buddypress();
        $content = [];
        $is_search = isset($args['search']) && $args['search'];
        $is_include = !empty($args['include']);

        //BP <12.0 and Classic BP
        if (isset($bp->members->nav) && $bp->members->nav->get_item_nav() !== false) {
            foreach ((array) $bp->members->nav->get_item_nav() as $item) {
                $content[$item->slug] = [
                    'id'   => $item->slug,
                    'text' => strip_tags($item->name)
                ];
                if ($item->children) {
                    $level = $is_search ? 0 : 1;
                    foreach ($item->children as $child_item) {
                        $content[$item->slug . '-' . $child_item->slug] = [
                            'text'  => strip_tags($child_item->name),
                            'id'    => $item->slug . '-' . $child_item->slug,
                            'level' => $level
                        ];
                    }
                }
            }
        } elseif (function_exists('bp_get_component_navigations')) {
            foreach (bp_get_component_navigations() as $navs) {
                if (!isset($navs['main_nav']['rewrite_id']) || !$navs['main_nav']['rewrite_id']) {
                    continue;
                }
                $content[$navs['main_nav']['slug']] = [
                    'id'   => $navs['main_nav']['slug'],
                    'text' => strip_tags($navs['main_nav']['name'])
                ];
                if (isset($navs['sub_nav'])) {
                    $level = $is_search ? 0 : 1;
                    foreach ($navs['sub_nav'] as $sub_nav) {
                        $content[$sub_nav['parent_slug'] . '-' . $sub_nav['slug']] = [
                            'text' => $is_search || $is_include
                                ? strip_tags($navs['main_nav']['name'] . ': ' . $sub_nav['name'])
                                : strip_tags($sub_nav['name']),
                            'id'    => $sub_nav['parent_slug'] . '-' . $sub_nav['slug'],
                            'level' => $level
                        ];
                    }
                }
            }
        }

        if ($is_include) {
            $content = array_intersect_key($content, array_flip($args['include']));
        } elseif ($is_search) {
            $content = array_filter($content, function ($value) use ($args) {
                return mb_stripos($value['text'], $args['search']) !== false;
            });
        }

        return $content;
    }

    /**
     * @inheritDoc
     */
    public function in_context()
    {
        $bp = buddypress();
        return isset($bp->displayed_user->domain) && $bp->displayed_user->domain;
    }

    /**
     * @inheritDoc
     */
    public function get_context_data()
    {
        $bp = buddypress();
        $data = [$this->default_value];
        if (isset($bp->current_component)) {
            $data[] = $bp->current_component;
            if (isset($bp->current_action)) {
                $data[] = $bp->current_component . '-' . $bp->current_action;
            }
        }
        return $data;
    }

    /**
     * Avoid collision with content of static module
     * Somehow buddypress pages pass is_404()
     *
     * @since  1.0
     * @param  boolean $content
     * @return boolean
     */
    public function static_is_content($content)
    {
        //TODO: test if deprecated
        return $content && !$this->in_context();
    }
}
