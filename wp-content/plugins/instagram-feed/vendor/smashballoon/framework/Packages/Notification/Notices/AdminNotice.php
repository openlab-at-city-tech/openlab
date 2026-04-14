<?php

/**
 * Admin notice class.
 *
 * @package Notices
 */
namespace InstagramFeed\Vendor\Smashballoon\Framework\Packages\Notification\Notices;

use function InstagramFeed\Vendor\Smashballoon\Framework\sb_get_template;
if (!defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
/**
 * Admin notice class.
 */
class AdminNotice extends Notice
{
    /**
     * Display notice.
     *
     * @return void
     */
    public function display()
    {
        $wrap_schema = $this->wrap_schema;
        $fields = $this->fields;
        // Display notice.
        foreach ($fields as $field => $value) {
            switch ($field) {
                case 'id':
                    $fields['id'] = NoticeFields::get_id($this->id);
                    break;
                case 'class':
                    $class = 'notice-' . $this->type . ' ' . $this->class;
                    if ($this->dismissible) {
                        $class .= ' is-dismissible';
                    }
                    $fields['class'] = NoticeFields::get_class($class);
                    break;
                case 'wrap_id':
                    $fields['wrap_id'] = NoticeFields::get_id($this->wrap_id);
                    break;
                case 'wrap_class':
                    $fields['wrap_class'] = NoticeFields::get_class($this->wrap_class);
                    break;
                case 'styles':
                    $fields['styles'] = NoticeFields::get_styles($this->styles);
                    break;
                case 'data':
                    $fields['data'] = NoticeFields::get_data($this->data);
                    break;
                case 'image':
                    $fields['image'] = NoticeFields::get_image($this->image);
                    break;
                case 'icon':
                    $fields['icon'] = NoticeFields::get_image($this->icon);
                    break;
                case 'title':
                    $fields['title'] = NoticeFields::get_title($this->title);
                    break;
                case 'message':
                    $fields['message'] = NoticeFields::get_content($this->message);
                    break;
                case 'buttons':
                    $fields['buttons'] = NoticeFields::get_buttons($this->buttons, $this->buttons_wrap_start, $this->buttons_wrap_end);
                    break;
                case 'dismiss':
                    $fields['dismiss'] = $this->dismissible ? NoticeFields::get_dismiss($this->dismiss) : '';
                    break;
                case 'navigation':
                    $fields['navigation'] = $this->nav ? NoticeFields::get_navigation($this->navigation) : '';
                    break;
                default:
                    $fields[$field] = $value;
                    break;
            }
        }
        // Replace fields in wrap schema.
        $notice = $this->replace_fields($wrap_schema, $fields);
        $this->print_notice($notice);
    }
    /**
     * Print notice.
     *
     * @param string $notice
     *
     * @return void
     */
    public function print_notice($notice)
    {
        ob_start();
        sb_get_template('Notification/templates/' . $this->type . '.php', ['notice' => $notice, 'type' => $this->type, 'id' => $this->id]);
        $notice_html = ob_get_clean();
        $notice_html = apply_filters('sb_' . $this->type . '_notice_markup', $notice_html);
        echo $notice_html;
    }
}
