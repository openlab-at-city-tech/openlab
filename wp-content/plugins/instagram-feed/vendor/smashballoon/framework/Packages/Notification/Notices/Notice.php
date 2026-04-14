<?php

/**
 * Notice class
 *
 * @package Notices
 */
namespace InstagramFeed\Vendor\Smashballoon\Framework\Packages\Notification\Notices;

use InstagramFeed\Vendor\Smashballoon\Framework\Packages\Notification\Notices\NoticeFields;
if (!defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
/**
 * Abstract Notice class.
 */
abstract class Notice
{
    /**
     * Notice type
     *
     * @var string
     */
    protected $type;
    /**
     * Notice message
     *
     * @var string
     */
    protected $message;
    /**
     * Notice title
     *
     * @var array
     */
    protected $title;
    /**
     * Notice icon
     *
     * @var string
     */
    protected $icon;
    /**
     * Notice image
     *
     * @var string
     */
    protected $image;
    /**
     * Notice class
     *
     * @var string
     */
    protected $class;
    /**
     * Notice id
     *
     * @var string
     */
    protected $id;
    /**
     * Notice wrap class
     *
     * @var string
     */
    protected $wrap_class;
    /**
     * Notice wrap id
     *
     * @var string
     */
    protected $wrap_id;
    /**
     * Notice data
     *
     * @var string
     */
    protected $data;
    /**
     * Notice dismissible
     *
     * @var boolean
     */
    protected $dismissible;
    /**
     * Notice dismiss
     *
     * @var string
     */
    protected $dismiss;
    /**
     * Notice navigation
     *
     * @var boolean
     */
    protected $nav;
    /**
     * Notice nav navigation
     *
     * @var string
     */
    protected $navigation;
    /**
     * Notice buttons
     *
     * @var array
     */
    protected $buttons;
    /**
     * Notice buttons wrap start
     *
     * @var string
     */
    protected $buttons_wrap_start;
    /**
     * Notice buttons wrap end
     *
     * @var string
     */
    protected $buttons_wrap_end;
    /**
     * Notice wrap schema
     *
     * @var string
     */
    protected $wrap_schema;
    /**
     * Notice styles
     *
     * @var string
     */
    protected $styles;
    /**
     * Notice fields
     *
     * @var array
     */
    protected $fields = ['wrap_class' => '', 'wrap_id' => '', 'id' => '', 'class' => '', 'data' => '', 'icon' => '', 'image' => '', 'title' => '', 'message' => '', 'buttons' => '', 'dismiss' => '', 'navigation' => '', 'styles' => ''];
    /**
     * Current screen.
     *
     * @var string
     */
    protected $screen;
    /**
     * Notice constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {
        $this->screen = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        $args = wp_parse_args($args, ['type' => 'error', 'message' => '', 'title' => '', 'icon' => '', 'image' => '', 'class' => '', 'id' => '', 'dismissible' => \false, 'dismiss' => '', 'buttons' => [], 'buttons_wrap_start' => '', 'buttons_wrap_end' => '', 'wrap_schema' => '<div {id} {class}>{icon}{title}{message}{buttons}</div>', 'nav' => \false, 'navigation' => '', 'wrap_class' => '', 'wrap_id' => '', 'data' => '', 'styles' => '']);
        $this->type = $args['type'];
        $this->message = $args['message'];
        $this->title = $args['title'];
        $this->icon = $args['icon'];
        $this->image = $args['image'];
        $this->class = $args['class'];
        $this->id = $args['id'];
        $this->wrap_class = $args['wrap_class'];
        $this->wrap_id = $args['wrap_id'];
        $this->data = $args['data'];
        $this->dismissible = $args['dismissible'];
        $this->dismiss = $args['dismiss'];
        $this->buttons = $args['buttons'];
        $this->buttons_wrap_start = $args['buttons_wrap_start'];
        $this->buttons_wrap_end = $args['buttons_wrap_end'];
        $this->wrap_schema = $args['wrap_schema'];
        $this->nav = $args['nav'];
        $this->navigation = $args['navigation'];
        $this->styles = $args['styles'];
        NoticeFields::set_screen($this->screen);
    }
    /**
     * Display notice
     *
     * @return void
     */
    abstract public function display();
    /**
     * Replace fields in notice.
     *
     * @param string $notice
     * @param array  $fields
     *
     * @return string
     */
    public function replace_fields($notice, $fields)
    {
        if (!empty($fields)) {
            foreach ($fields as $key => $value) {
                $notice = str_replace('{' . $key . '}', $value, $notice);
            }
            $notice = wp_kses($notice, NoticeFields::$allowed_tags);
        }
        return $notice;
    }
}
