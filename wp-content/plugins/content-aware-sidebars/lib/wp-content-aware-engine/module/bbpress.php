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
 * bbPress Module
 * Requires bbPress 2.5+
 *
 * Detects if current content is:
 * a) any or specific bbpress user profile
 *
 */
class WPCAModule_bbpress extends WPCAModule_author
{
    /**
     * @var string
     */
    protected $category = 'plugins';

    public function __construct()
    {
        parent::__construct();
        $this->id = 'bb_profile';
        $this->name = __('bbPress User Profiles', WPCA_DOMAIN);
        $this->icon = 'dashicons-buddicons-bbpress-logo';
        $this->placeholder = __('All Profiles', WPCA_DOMAIN);
        $this->default_value = $this->id;
        $this->query_name = 'cbb';
    }

    /**
     * @inheritDoc
     */
    public function initiate()
    {
        parent::initiate();
        add_filter(
            'wpca/module/post_type/db-where',
            [$this,'add_forum_dependency']
        );
    }

    /**
     * @inheritDoc
     */
    public function can_enable()
    {
        return function_exists('bbp_get_version')
            && function_exists('bbp_is_single_user')
            && function_exists('bbp_get_displayed_user_id')
            && function_exists('bbp_get_forum_id');
    }

    /**
     * @inheritDoc
     */
    public function in_context()
    {
        return bbp_is_single_user();
    }

    /**
     * @inheritDoc
     */
    public function get_context_data()
    {
        $data = [$this->id];
        $data[] = bbp_get_displayed_user_id();
        return $data;
    }

    /**
     * Sidebars to be displayed with forums will also
     * be dislpayed with respective topics and replies
     *
     * @since  1.0
     * @param  string $where
     * @return string
     */
    public function add_forum_dependency($where)
    {
        if (is_singular(['topic','reply'])) {
            $data = [
                get_post_type(),
                get_the_ID(),
                'forum'
            ];
            $data[] = bbp_get_forum_id();
            $where = '(cp.meta_value IS NULL OR cp.meta_value IN(' . WPCACore::sql_prepare_in($data) . '))';
        }
        return $where;
    }
}
