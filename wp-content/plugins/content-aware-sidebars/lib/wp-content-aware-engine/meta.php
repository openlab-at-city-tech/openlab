<?php
/**
 * @package wp-content-aware-engine
 * @author Joachim Jensen <joachim@dev.institute>
 * @license GPLv3
 * @copyright 2023 by Joachim Jensen
 */

defined('ABSPATH') || exit;

if (!class_exists('WPCAMeta')) {
    /**
     * Post Meta
     */
    class WPCAMeta
    {
        /**
         * Id
         * @var string
         */
        private $id;

        /**
         * Title
         * @var string
         */
        private $title;

        /**
         * Description
         * @var string
         */
        private $description;

        /**
         * Default value
         * @var mixed
         */
        private $default_value;

        /**
         * Input type
         * @var string
         */
        private $input_type;

        /**
         * Input list
         * @var string
         */
        private $input_list;

        /**
         * Callback to sanitize data before save
         * @var func
         */
        private $sanitizer;

        /**
         * Constructor
         *
         * @since 1.0
         */
        public function __construct(
            $id,
            $title,
            $default_value = '',
            $input_type = 'text',
            $input_list = [],
            $description = '',
            $sanitizer = ''
        ) {
            $this->id = $id;
            $this->title = $title;
            $this->default_value = $default_value;
            $this->input_type = $input_type;
            $this->input_list = $input_list;
            $this->description = $description;
            $this->sanitizer = $sanitizer;
        }

        /**
         * Get meta id
         *
         * @since  1.0
         * @return string
         */
        public function get_id()
        {
            return $this->id;
        }

        /**
         * Get meta title
         *
         * @since  1.0
         * @return string
         */
        public function get_title()
        {
            return $this->title;
        }

        /**
         * Get meta input type
         *
         * @since  1.0
         * @return string
         */
        public function get_input_type()
        {
            return $this->input_type;
        }

        /**
         * Get meta input list
         *
         * @since  1.0
         * @return array
         */
        public function get_input_list()
        {
            return $this->input_list;
        }

        /**
         * Set meta input list
         *
         * @since 1.0
         * @param array  $input_list
         */
        public function set_input_list($input_list)
        {
            $this->input_list = $input_list;
        }

        /**
         * Get this meta data for a post
         *
         * @since  1.0
         * @param  int     $post_id
         * @param  boolean $default_fallback
         * @param  boolean $single
         * @return mixed
         */
        public function get_data($post_id, $default_fallback = false, $single = true)
        {
            $data = get_post_meta($post_id, WPCACore::PREFIX . $this->id, $single);
            if ($data == '' && $default_fallback) {
                $data = $this->default_value;
            }
            return $data;
        }

        /**
         * Update this meta data for a post
         *
         * @since  1.0
         * @param  int     $post_id
         * @param  string  $value
         * @return void
         */
        public function update($post_id, $value)
        {
            if ($this->input_type != 'multi') {
                update_post_meta($post_id, WPCACore::PREFIX . $this->id, $value);
            } else {
                add_post_meta($post_id, WPCACore::PREFIX . $this->id, $value);
            }
        }

        /**
         * Delete this meta data for a post
         *
         * @since  1.0
         * @param  int     $post_id
         * @param  string  $value
         * @return void
         */
        public function delete($post_id, $value = '')
        {
            delete_post_meta($post_id, WPCACore::PREFIX . $this->id, $value);
        }

        /**
         * Save data based on POST
         *
         * @since  4.2
         * @param int $post_id
         */
        public function save($post_id)
        {
            $value = isset($_POST[$this->id]) ? $_POST[$this->id] : '';
            if ($this->sanitizer && is_callable($this->sanitizer)) {
                $value = call_user_func($this->sanitizer, $value);
            }
            if ($this->input_type != 'multi') {
                //value can be 0 and valid
                if ($value != '') {
                    $this->update($post_id, $value);
                } elseif ($this->get_data($post_id, false, true) != '') {
                    $this->delete($post_id);
                }
            } else {
                $old = array_flip($this->get_data($post_id, false, false));
                if (is_array($value)) {
                    foreach ($value as $meta) {
                        if (isset($old[$meta])) {
                            unset($old[$meta]);
                        } else {
                            $this->update($post_id, $meta);
                        }
                    }
                }

                foreach ($old as $meta => $v) {
                    $this->delete($post_id, $meta);
                }
            }
        }

        /**
         * Get this meta data for a post
         * represented by entry in input list
         *
         * @since  1.0
         * @param  int  $post_id
         * @return mixed
         */
        public function get_list_data($post_id, $default_fallback = true)
        {
            $data = $this->get_data($post_id, $default_fallback);
            return isset($this->input_list[$data]) ? $this->input_list[$data] : null;
        }
    }
}
