<?php

class B2S_Curation_Save {

    public $data = null;

    public function __construct($data = array()) {
        $this->data = $data;
    }

    public function insertContent() {
        $post = array(
            'post_title' => sanitize_text_field($this->data['title']),
            'post_content' => $this->data['content'],
            'guid' => ((isset($this->data['url']) && !empty($this->data['url'])) ? esc_url($this->data['url']) : ''),
            'post_status' => 'private',
            'post_author' => $this->data['author_id'],
            'post_type' => 'b2s_ex_post',
            'post_category' => array(0)
        );
        $res = wp_insert_post($post, true);
        if((int) $res > 0 && isset($this->data['image_id']) && (int) $this->data['image_id'] > 0) {
            set_post_thumbnail($res, $this->data['image_id']);
        }
        return ($res > 0) ? (int) $res : false;
    }

    public function updateContent($source = '') {

        if ($source == "b2s_browser_extension") {
            $post = array(
                'ID' => $this->data['ID'],
                'post_title' => sanitize_text_field($this->data['title']),
                'post_content' => $this->data['content']
            );
            $res = wp_update_post($post, true);
            update_post_meta($this->data['ID'], 'b2s_original_url', trim(esc_url($this->data['url'])));
        }

        if (empty($source)) {
            $post = array(
                'ID' => $this->data['ID'],
                'post_title' => sanitize_text_field($this->data['title']),
                'post_content' => $this->data['content']
            );
            $res = wp_update_post($post, true);
            //wp_update_post don't update guid
            global $wpdb;
            $wpdb->update($wpdb->posts, array('guid' => ((isset($this->data['url']) && !empty($this->data['url'])) ? esc_url($this->data['url']) : '')), array('ID' => $this->data['ID']));
            if((int) $res > 0 && isset($this->data['image_id']) && (int) $this->data['image_id'] > 0) {
                set_post_thumbnail($res, $this->data['image_id']);
            }
        }
        return ($res > 0) ? (int) $res : false;
    }

}
