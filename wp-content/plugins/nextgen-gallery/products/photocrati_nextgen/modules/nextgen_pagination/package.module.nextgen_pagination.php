<?php
/**
 * Contains function(s) to generate a basic pagination widget
 */
class Mixin_NextGen_Basic_Pagination extends Mixin
{
    /**
     * Returns a formatted HTML string of a pagination widget
     *
     * @param mixed $page
     * @param int $totalElement
     * @param int $maxElement
     * @param string|null $current_url (optional)
     * @return array Of data holding prev & next url locations and a formatted HTML string
     */
    public function create_pagination($page, $totalElement, $maxElement = 0, $current_url = NULL)
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
        $return = array('prev' => '', 'next' => '', 'output' => '');
        if ($maxElement <= 0) {
            $return['output'] = "<div class='ngg-clear'></div>";
            return $return;
        }
        $total = $totalElement;
        // create navigation
        if ($total > $maxElement) {
            $r = '';
            if (1 < $page) {
                $newpage = 1 == $page - 1 ? 1 : $page - 1;
                $return['prev'] = $this->object->set_param_for($current_url, 'nggpage', $newpage);
                $r .= '<a class="prev" data-pageid="' . $newpage . '" id="ngg-prev-' . $newpage . '" href="' . $return['prev'] . '">' . $prev_symbol . '</a>';
            }
            $total_pages = ceil($total / $maxElement);
            if ($total_pages > 1) {
                for ($page_num = 1; $page_num <= $total_pages; $page_num++) {
                    if ($page == $page_num) {
                        $r .= '<span class="current">' . $page_num . '</span>';
                    } else {
                        if ($page_num < 3 || $page_num >= $page - 3 && $page_num <= $page + 3 || $page_num > $total_pages - 3) {
                            $newpage = 1 == $page_num ? 1 : $page_num;
                            $link = $this->object->set_param_for($current_url, 'nggpage', $newpage);
                            $r .= '<a class="page-numbers" data-pageid="' . $newpage . '" href="' . $link . '">' . $page_num . '</a>';
                        }
                    }
                }
            }
            if ($page * $maxElement < $total || -1 == $total) {
                $newpage = $page + 1;
                $return['next'] = $this->object->set_param_for($current_url, 'nggpage', $newpage);
                $r .= '<a class="next" data-pageid="' . $newpage . '" id="ngg-next-' . $newpage . '" href="' . $return['next'] . '">' . $next_symbol . '</a>';
            }
            $return['output'] = "<div class='ngg-navigation'>{$r}</div>";
        } else {
            $return['output'] = "<div class='ngg-clear'></div>";
        }
        return $return;
    }
}