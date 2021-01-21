<?php

class PRG_Post_Filter {

    public $type;
    protected $postFilter = '';
    protected $searchAuthorId;
    protected $searchPostType;
    protected $searchPostTitle;
    protected $postAuthor;

    function __construct($type, $title = "", $authorId = 0, $postType = "",$postStatus="") {  //type=all,publish,sched
        $this->type = $type;
        $this->searchPostTitle = $title;
        $this->searchAuthorId = (int)$authorId;
        $this->searchPostType = $postType;
        $this->searchPostStatus = $postStatus;
    }

    public function getAutorData() {
        global $wpdb;
        $sqlAuthors = "SELECT `ID`,`display_name` FROM `$wpdb->users`";
        $this->postAuthor = $wpdb->get_results($sqlAuthors);
    }

    private function getAutorHtml() {
        $autor = '<div class="form-group"><select name="prgSortPostAuthor" class="form-control b2s-select"><option value="">'. esc_html__('All Authors', 'blog2social').'</option>';
        foreach ($this->postAuthor as $var) {
            $selected = ($var->ID == (int) $this->searchAuthorId) ? 'selected' : '';
            $autor.='<option ' . $selected . ' value="' . esc_attr($var->ID) . '">' . esc_html($var->display_name) . '</option>';
        }
        $autor.='</select></div>';
        return $autor;
    }

    private function getPostStatusHtml() {
       $typeData = array(array('key' => 'publish', 'value' =>  __('published', 'blog2social')), array('key' => 'future', 'value' =>  __('scheduled', 'blog2social')));
       $type = '<div class="form-group"><select name="prgSortPostStatus" class="form-control b2s-select"><option value="">'. esc_html__('All Types', 'blog2social').'</option>';
        foreach ($typeData as $var) {
            $var = (object) $var;
            $selected = (!empty($this->searchPostStatus) && $var->key == $this->searchPostStatus) ? 'selected' : '';
            $type.='<option ' . $selected . ' value="' . esc_attr($var->key) . '">' . esc_html($var->value) . '</option>';
        }
        $type .='</select></div>';
        return $type;
    }
    
        private function getPostTypeHtml() {
        $type = '<div class="form-group"><select id="prgSortPostType" name="prgSortPostType" class="form-control b2s-select"><option value="">' . esc_html__('all post types', 'blog2social') . '</option>';
        $post_types = get_post_types(array('public' => true));
        if (is_array($post_types) && !empty($post_types)) {
            foreach ($post_types as $k => $v) {
                if ($v != 'attachment' && $v != 'nav_menu_item' && $v != 'revision') {
                    $selected = (!empty($this->searchPostType) && $v == $this->searchPostType) ? 'selected' : '';
                    //Bug: Converting json + PHP Extension
                      if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                            $v = mb_strlen($v,'UTF-8') > 27 ? mb_substr($v, 0, 27,'UTF-8') . '...' : $v;
                      }
                    $type .= '<option ' . $selected . ' value="' . esc_attr($v) . '">' . esc_html(ucfirst($v)) . '</option>';
                }
            }
        }
        $type .= '</select></div>';
        return $type;
    }
    
    public function getItemHtml() {
        $this->getAutorData();
        $this->postFilter .= '<div class="form-group">
                                    <input id="pref-search" name="prgSortPostTitle" maxlength="30" class="form-control input-sm" value="' . esc_attr((empty($this->searchPostTitle) ? '' : $this->searchPostTitle)) . '" placeholder="' . esc_attr((empty($this->searchPostTitle) ?  __('Search Title', 'blog2social') : '')) . '" type="text">
                             </div>';
        if (B2S_PLUGIN_ADMIN) {
            $this->postFilter .=$this->getAutorHtml();
        }
        if ($this->type == 'all') {
            $this->postFilter .= $this->getPostTypeHtml();
            $this->postFilter .= $this->getPostStatusHtml();      
        }
        
        $this->postFilter .='<div class="form-group">
                                    <button class="btn btn-primary btn-sm" type="submit">'. esc_html__('sort', 'blog2social').'</button>
                                    <a class="btn btn-primary btn-sm" href="admin.php?page=prg-post">'. esc_html__('reset', 'blog2social').'</a>
                             </div>';


        return $this->postFilter;
    }

}
