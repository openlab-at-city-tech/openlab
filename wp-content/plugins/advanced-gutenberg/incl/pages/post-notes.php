<?php
defined('ABSPATH') || die;

$current_user      = wp_get_current_user();
$can_edit_others   = current_user_can('edit_others_posts');

// "Notes per page" Screen Option (Settings > Screen Options, top right of this page)
$per_page = (int) get_user_meta($current_user->ID, 'advgb_post_notes_per_page', true);
if ($per_page < 1) {
    $per_page = 20;
}
$per_page = min(999, $per_page);

// Column visibility from Screen Options; '_title' (Post) is always shown
$advgb_screen  = get_current_screen();
$hidden_cols   = $advgb_screen ? get_hidden_columns($advgb_screen) : [];
$advgb_show_col = function ($key) use ($hidden_cols) {
    return ! in_array($key, $hidden_cols, true);
};

$current_page      = max(1, isset($_GET['paged']) ? absint($_GET['paged']) : 1);
$filter_status     = isset($_GET['note_status']) ? sanitize_text_field($_GET['note_status']) : '';
$filter_post_type  = isset($_GET['post_type_filter']) ? sanitize_text_field($_GET['post_type_filter']) : '';
$filter_search     = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$page_url          = admin_url('admin.php?page=advgb_post_notes');

// Build status arg
$status_arg = 'all';
if ($filter_status === 'open') {
    $status_arg = 'hold';
} elseif ($filter_status === 'resolved') {
    $status_arg = 'approve';
}

// Resolve which post IDs to restrict notes to
$post_id_limit = null; // null = no restriction (admins/editors who can edit others' posts)

if (! $can_edit_others) {
    // Authors/contributors: only their own posts
    $own_posts = get_posts([
        'author'         => $current_user->ID,
        'posts_per_page' => -1,
        'post_type'      => 'any',
        'post_status'    => 'any',
        'fields'         => 'ids',
    ]);
    $post_id_limit = ! empty($own_posts) ? $own_posts : [0];
}

// Post type filter: resolve to post IDs and intersect with existing limit
if ($filter_post_type) {
    $type_posts = get_posts([
        'posts_per_page' => -1,
        'post_type'      => $filter_post_type,
        'post_status'    => 'any',
        'fields'         => 'ids',
    ]);
    if (empty($type_posts)) {
        $post_id_limit = [0];
    } elseif ($post_id_limit === null) {
        $post_id_limit = $type_posts;
    } else {
        $post_id_limit = array_intersect($post_id_limit, $type_posts);
        if (empty($post_id_limit)) {
            $post_id_limit = [0];
        }
    }
}

// Post title search: resolve to post IDs and intersect
if ($filter_search) {
    $search_posts = get_posts([
        'posts_per_page' => -1,
        'post_type'      => 'any',
        'post_status'    => 'any',
        's'              => $filter_search,
        'fields'         => 'ids',
    ]);
    if (empty($search_posts)) {
        $post_id_limit = [0];
    } elseif ($post_id_limit === null) {
        $post_id_limit = $search_posts;
    } else {
        $post_id_limit = array_intersect($post_id_limit, $search_posts);
        if (empty($post_id_limit)) {
            $post_id_limit = [0];
        }
    }
}

// Base comment query args — parent=0 excludes internal resolution child comments WP may create
$base_args = [
    'type'    => 'note',
    'status'  => $status_arg,
    'parent'  => 0,
    'orderby' => 'comment_date',
    'order'   => 'DESC',
];
if ($post_id_limit !== null) {
    $base_args['post__in'] = array_values($post_id_limit);
}

// Total count
$count_args          = $base_args;
$count_args['count'] = true;
$total_notes         = get_comments($count_args);
$total_pages         = $total_notes > 0 ? (int) ceil($total_notes / $per_page) : 1;
$current_page        = min($current_page, $total_pages);

// Paginated results
$query_args           = $base_args;
$query_args['number'] = $per_page;
$query_args['offset'] = ($current_page - 1) * $per_page;
$notes                = get_comments($query_args);

// Post types for filter dropdown (public, excluding attachments)
$post_types = get_post_types(['public' => true], 'objects');
unset($post_types['attachment']);

/**
 * Render an initials avatar matching the WP 6.9 Notes editor style.
 * Color is deterministically derived from the author name so each
 * author always gets the same hue across page loads.
 */
function advgb_note_author_avatar($name, $email = '', $size = 32)
{
    // Extract up to two initials
    $parts    = array_filter(explode(' ', trim($name)));
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
    }
    if ($initials === '') {
        $initials = '?';
    }

    // Pick from a palette of visually distinct colors, deterministically by name
    $palette = [
        '#e74c3c', // red
        '#e67e22', // orange
        '#f39c12', // amber
        '#27ae60', // green
        '#16a085', // teal
        '#2980b9', // blue
        '#8e44ad', // purple
        '#c0392b', // dark red
        '#1abc9c', // mint
        '#2c3e50', // navy
        '#d35400', // burnt orange
        '#7f8c8d', // slate
    ];
    $bg_color = $palette[ abs(crc32($name)) % count($palette) ];
    $font_size = (int) ($size * 0.4);

    // Request avatar with default=404 so missing Gravatars don't load the mystery-man image.
    // The <img> overlays the initials; onerror hides it and lets the initials show through.
    $img_tag = '';
    if ($email) {
        $avatar_url = get_avatar_url($email, ['size' => $size * 2, 'default' => '404']);
        if ($avatar_url) {
            $img_tag = sprintf(
                '<img src="%s" width="%d" height="%d" alt="" '
                    . 'style="position:absolute;top:0;left:0;width:100%%;height:100%%;'
                    . 'border-radius:50%%;object-fit:cover;" '
                    . 'onerror="this.style.display=\'none\'">',
                esc_url($avatar_url),
                (int) $size,
                (int) $size
            );
        }
    }

    printf(
        '<span class="advgb-note-avatar" aria-hidden="true" style="'
            . 'position:relative;display:inline-flex;align-items:center;justify-content:center;'
            . 'width:%1$dpx;height:%1$dpx;border-radius:50%%;overflow:hidden;'
            . 'background:%2$s;color:#fff;font-size:%3$dpx;'
            . 'font-weight:600;flex-shrink:0;line-height:1;'
            . 'font-family:-apple-system,BlinkMacSystemFont,sans-serif;'
            . '">%4$s%5$s</span>',
        (int) $size,
        esc_attr($bg_color),
        (int) $font_size,
        esc_html($initials),
        wp_kses($img_tag, [
            'img' => [
                'src'     => true,
                'width'   => true,
                'height'  => true,
                'alt'     => true,
                'style'   => true,
                'onerror' => true,
            ],
        ])
    );
}

// Pagination helper
$pagination_args = [
    'base'               => add_query_arg('paged', '%#%', $page_url),
    'format'             => '',
    'current'            => $current_page,
    'total'              => $total_pages,
    'prev_text'          => '&laquo;',
    'next_text'          => '&raquo;',
    'before_page_number' => '',
];
if ($filter_status) {
    $pagination_args['base'] = add_query_arg('note_status', $filter_status, $pagination_args['base']);
}
if ($filter_post_type) {
    $pagination_args['base'] = add_query_arg('post_type_filter', $filter_post_type, $pagination_args['base']);
}
if ($filter_search) {
    $pagination_args['base'] = add_query_arg('s', $filter_search, $pagination_args['base']);
}
?>

<div class="publishpress-admin wrap">
    <header>
        <h1 class="wp-heading-inline">
            <?php esc_html_e('Post Notes', 'advanced-gutenberg') ?>
        </h1>
    </header>

    <?php
    $this->screenDescription(
        __(
            'Find all the editorial notes for your posts in one place.',
            'advanced-gutenberg'
        )
    );
    ?>

    <div class="wrap">
        <form method="get" action="<?php echo esc_url(admin_url('admin.php')) ?>">
            <input type="hidden" name="page" value="advgb_post_notes">

            <div class="tablenav top">
                <div class="alignleft actions">

                    <label for="advgb-post-notes-post-type" class="screen-reader-text">
                        <?php esc_html_e('Filter by post type', 'advanced-gutenberg') ?>
                    </label>
                    <select id="advgb-post-notes-post-type" name="post_type_filter">
                        <option value=""><?php esc_html_e('All post types', 'advanced-gutenberg') ?></option>
                        <?php foreach ($post_types as $pt) : ?>
                            <option value="<?php echo esc_attr($pt->name) ?>"
                                <?php selected($filter_post_type, $pt->name) ?>>
                                <?php echo esc_html($pt->label) ?>
                            </option>
                        <?php endforeach ?>
                    </select>

                    <label for="advgb-post-notes-status" class="screen-reader-text">
                        <?php esc_html_e('Filter by note status', 'advanced-gutenberg') ?>
                    </label>
                    <select id="advgb-post-notes-status" name="note_status">
                        <option value=""><?php esc_html_e('All statuses', 'advanced-gutenberg') ?></option>
                        <option value="open" <?php selected($filter_status, 'open') ?>>
                            <?php esc_html_e('Open', 'advanced-gutenberg') ?>
                        </option>
                        <option value="resolved" <?php selected($filter_status, 'resolved') ?>>
                            <?php esc_html_e('Resolved', 'advanced-gutenberg') ?>
                        </option>
                    </select>

                    <label for="advgb-post-notes-search" class="screen-reader-text">
                        <?php esc_html_e('Search by post title', 'advanced-gutenberg') ?>
                    </label>
                    <input id="advgb-post-notes-search" type="search"
                           name="s"
                           value="<?php echo esc_attr($filter_search) ?>"
                           placeholder="<?php esc_attr_e('Search by post title…', 'advanced-gutenberg') ?>"
                           style="width:200px">

                    <?php submit_button(__('Filter', 'advanced-gutenberg'), 'action', '', false) ?>

                    <?php if ($filter_post_type || $filter_status || $filter_search) : ?>
                        <a href="<?php echo esc_url($page_url) ?>" class="button">
                            <?php esc_html_e('Reset', 'advanced-gutenberg') ?>
                        </a>
                    <?php endif ?>
                </div>

                <?php if ($total_notes > 0) : ?>
                <div class="tablenav-pages">
                    <span class="displaying-num">
                        <?php printf(
                            /* translators: %d: number of notes */
                            esc_html(_n('%d note', '%d notes', $total_notes, 'advanced-gutenberg')),
                            (int) $total_notes
                        ) ?>
                    </span>
                    <?php echo paginate_links($pagination_args) // phpcs:ignore WordPress.Security.EscapeOutput ?>
                </div>
                <?php endif ?>
            </div><!-- .tablenav.top -->

            <?php
            // Column count for the "no notes" placeholder row (Post is always shown)
            $advgb_visible_cols = 1;
            foreach (['post_type', 'note', 'author', 'date', 'status'] as $advgb_col_key) {
                if ($advgb_show_col($advgb_col_key)) {
                    $advgb_visible_cols++;
                }
            }
            ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" style="width:22%"><?php esc_html_e('Post', 'advanced-gutenberg') ?></th>
                        <?php if ($advgb_show_col('post_type')) : ?>
                            <th scope="col" style="width:10%"><?php esc_html_e('Post Type', 'advanced-gutenberg') ?></th>
                        <?php endif ?>
                        <?php if ($advgb_show_col('note')) : ?>
                            <th scope="col"><?php esc_html_e('Note', 'advanced-gutenberg') ?></th>
                        <?php endif ?>
                        <?php if ($advgb_show_col('author')) : ?>
                            <th scope="col" style="width:16%"><?php esc_html_e('Author', 'advanced-gutenberg') ?></th>
                        <?php endif ?>
                        <?php if ($advgb_show_col('date')) : ?>
                            <th scope="col" style="width:11%"><?php esc_html_e('Date', 'advanced-gutenberg') ?></th>
                        <?php endif ?>
                        <?php if ($advgb_show_col('status')) : ?>
                            <th scope="col" style="width:9%"><?php esc_html_e('Status', 'advanced-gutenberg') ?></th>
                        <?php endif ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($notes)) : ?>
                        <tr>
                            <td colspan="<?php echo (int) $advgb_visible_cols ?>">
                                <?php esc_html_e('No notes found.', 'advanced-gutenberg') ?>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($notes as $note) :
                            $post       = get_post($note->comment_post_ID);
                            $edit_url   = $post ? get_edit_post_link($post->ID) : '';
                            $post_title = $post ? ($post->post_title ?: __('(no title)', 'advanced-gutenberg')) : __('(deleted)', 'advanced-gutenberg');
                            $post_type  = $post ? get_post_type_object($post->post_type) : null;
                            $resolved   = ($note->comment_approved === '1');
                        ?>
                        <tr>
                            <td>
                                <?php if ($edit_url) : ?>
                                    <a href="<?php echo esc_url($edit_url) ?>">
                                        <?php echo esc_html($post_title) ?>
                                    </a>
                                <?php else : ?>
                                    <span><?php echo esc_html($post_title) ?></span>
                                <?php endif ?>
                            </td>
                            <?php if ($advgb_show_col('post_type')) : ?>
                                <td>
                                    <?php echo $post_type ? esc_html($post_type->labels->singular_name) : '&mdash;' ?>
                                </td>
                            <?php endif ?>
                            <?php if ($advgb_show_col('note')) : ?>
                                <td>
                                    <?php echo esc_html(wp_trim_words($note->comment_content, 20)) ?>
                                </td>
                            <?php endif ?>
                            <?php if ($advgb_show_col('author')) : ?>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <?php advgb_note_author_avatar($note->comment_author, $note->comment_author_email, 32) ?>
                                        <span><?php echo esc_html($note->comment_author) ?></span>
                                    </div>
                                </td>
                            <?php endif ?>
                            <?php if ($advgb_show_col('date')) : ?>
                                <td>
                                    <abbr title="<?php echo esc_attr($note->comment_date) ?>">
                                        <?php echo esc_html(
                                            date_i18n(get_option('date_format'), strtotime($note->comment_date))
                                        ) ?>
                                    </abbr>
                                </td>
                            <?php endif ?>
                            <?php if ($advgb_show_col('status')) : ?>
                                <td>
                                    <span class="advgb-note-status advgb-note-status--<?php echo $resolved ? 'resolved' : 'open' ?>">
                                        <?php echo $resolved
                                            ? esc_html__('Resolved', 'advanced-gutenberg')
                                            : esc_html__('Open', 'advanced-gutenberg') ?>
                                    </span>
                                </td>
                            <?php endif ?>
                        </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="col"><?php esc_html_e('Post', 'advanced-gutenberg') ?></th>
                        <?php if ($advgb_show_col('post_type')) : ?>
                            <th scope="col"><?php esc_html_e('Post Type', 'advanced-gutenberg') ?></th>
                        <?php endif ?>
                        <?php if ($advgb_show_col('note')) : ?>
                            <th scope="col"><?php esc_html_e('Note', 'advanced-gutenberg') ?></th>
                        <?php endif ?>
                        <?php if ($advgb_show_col('author')) : ?>
                            <th scope="col"><?php esc_html_e('Author', 'advanced-gutenberg') ?></th>
                        <?php endif ?>
                        <?php if ($advgb_show_col('date')) : ?>
                            <th scope="col"><?php esc_html_e('Date', 'advanced-gutenberg') ?></th>
                        <?php endif ?>
                        <?php if ($advgb_show_col('status')) : ?>
                            <th scope="col"><?php esc_html_e('Status', 'advanced-gutenberg') ?></th>
                        <?php endif ?>
                    </tr>
                </tfoot>
            </table>

            <?php if ($total_pages > 1) : ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <?php echo paginate_links($pagination_args) // phpcs:ignore WordPress.Security.EscapeOutput ?>
                </div>
            </div>
            <?php endif ?>

        </form>
    </div><!-- .wrap -->
</div><!-- .publishpress-admin.wrap -->

<style>
.advgb-note-status {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}
.advgb-note-status--open {
    background: #fff3cd;
    color: #856404;
}
.advgb-note-status--resolved {
    background: #d4edda;
    color: #155724;
}
</style>
