<?php
/**
 * Contains function(s) to generate a basic pagination widget
 */
class Mixin_NextGen_Basic_Pagination extends Mixin
{
    /**
     * Returns a formatted HTML string of a pagination widget
     *
     * @param mixed $selected_page
     * @param int $number_of_entities
     * @param int $entities_per_page
     * @param string|null $current_url (optional)
     * @return array Of data holding prev & next url locations and a formatted HTML string
     */
    public function create_pagination($selected_page, $number_of_entities, $entities_per_page = 0, $current_url = NULL)
    {
        $prev_symbol = apply_filters('ngg_prev_symbol', '&#9668;');
        $next_symbol = apply_filters('ngg_next_symbol', '&#9658;');
        if (empty($current_url)) {
            $current_url = $this->object->get_routed_url(TRUE);
            if (is_archive()) {
                $id = get_the_ID();
                if ($id == null) {
                    global $post;
                    $id = $post ? $post->ID : null;
                }
                if ($id != null && in_the_loop()) {
                    $current_url = get_permalink($id);
                }
            }
        }
        // Early exit
        $return = array('prev' => '', 'next' => '', 'output' => "<div class='ngg-clear'></div>");
        if ($entities_per_page <= 0 || $number_of_entities <= 0) {
            return $return;
        }
        // Construct array of page urls
        $ending_ellipsis = $starting_ellipsis = FALSE;
        $number_of_pages = ceil($number_of_entities / $entities_per_page);
        $pages = [];
        for ($i = 1; $i <= $number_of_pages; $i++) {
            if ($selected_page === $i) {
                $pages['current'] = "<span class='current'>{$i}</span>";
            } else {
                $link = esc_attr($this->object->set_param_for($current_url, 'nggpage', $i));
                $pages[$i] = "<a class='page-numbers' data-pageid='{$i}' href='{$link}'>{$i}</a>";
            }
        }
        $after = $this->array_slice_from('current', $pages);
        if (count($after) > 3) {
            $after = array_merge($this->array_take_from_start(2, $after), ["<span class='ellipsis'>...</span>"], $this->array_take_from_end(1, $after));
        }
        $before = $this->array_slice_to('current', $pages);
        if (count($before) > 3) {
            $before = array_merge($this->array_take_from_start(1, $before), ["<span class='ellipsis'>...</span>"], $this->array_take_from_end(2, $before));
            array_pop($before);
        }
        $pages = array_merge($before, $after);
        if ($pages && count($pages) > 1) {
            // Next page
            if ($selected_page + 1 <= $number_of_pages) {
                $next_page = $selected_page + 1;
                $link = $return['next'] = $this->object->set_param_for($current_url, 'nggpage', $next_page);
                $pages[] = "<a class='prev' href='{$link}' data-pageid={$next_page}>{$next_symbol}</a>";
            }
            // Prev page
            if ($selected_page - 1 > 0) {
                $prev_page = $selected_page - 1;
                $link = $return['next'] = $this->object->set_param_for($current_url, 'nggpage', $prev_page);
                array_unshift($pages, "<a class='next' href='{$link}' data-pageid={$prev_page}>{$prev_symbol}</a>");
            }
            $return['output'] = "<div class='ngg-navigation'>" . implode("\n", $pages) . "</div>";
        }
        return $return;
    }
    function array_slice_from($find_key, $arr)
    {
        $retval = [];
        reset($arr);
        foreach ($arr as $key => $value) {
            if ($key == $find_key || $retval) {
                $retval[$key] = $value;
            }
        }
        reset($arr);
        return $retval;
    }
    function array_slice_to($find_key, $arr)
    {
        $retval = [];
        reset($arr);
        foreach ($arr as $key => $value) {
            $retval[$key] = $value;
            if ($key == $find_key) {
                break;
            }
        }
        reset($arr);
        return $retval;
    }
    function array_take_from_start($number, $arr)
    {
        $retval = [];
        foreach ($arr as $key => $value) {
            if (count($retval) < $number) {
                $retval[$key] = $value;
            } else {
                break;
            }
        }
        return $retval;
    }
    function array_take_from_end($number, $arr)
    {
        return array_reverse($this->array_take_from_start($number, array_reverse($arr)));
    }
}