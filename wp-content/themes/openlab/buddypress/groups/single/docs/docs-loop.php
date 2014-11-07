<?php include( apply_filters('bp_docs_header_template', bp_docs_locate_template('docs-header.php')) ) ?>

<div class="docs-info-header img-rounded">
    <div class="row">
        <div class="col-sm-24">
            <div class="doc-search-element pull-right align-right">
                <form action="" method="get" class="form-inline">
                    <div class="form-group">
                        <input class="form-control" name="s" value="<?php the_search_query() ?>">
                        <input class="btn btn-primary top-align" name="search_submit" type="submit" value="<?php _e('Search', 'bp-docs') ?>" />
                    </div>
                </form>
            </div>
            <?php bp_docs_info_header() ?>
        </div>
    </div>
</div>

<?php bp_docs_inline_toggle_js() ?>

<?php if (have_posts()) : ?>
<div class="info-panel panel panel-default doctable-panel">
    <table class="doctable table table-striped">

        <thead>
            <tr valign="bottom">
                <th scope="column" class="title-cell<?php bp_docs_is_current_orderby_class('title') ?>">
                    <a href="<?php bp_docs_order_by_link('title') ?>"><?php _e('Title', 'bp-docs'); ?></a>
                </th>

                <th scope="column" class="author-cell<?php bp_docs_is_current_orderby_class('author') ?>">
                    <a href="<?php bp_docs_order_by_link('author') ?>"><?php _e('Author', 'bp-docs'); ?></a>
                </th>

                <th scope="column" class="created-date-cell<?php bp_docs_is_current_orderby_class('created') ?>">
                    <a href="<?php bp_docs_order_by_link('created') ?>"><?php _e('Created', 'bp-docs'); ?></a>
                </th>

                <th scope="column" class="edited-date-cell<?php bp_docs_is_current_orderby_class('modified') ?>">
                    <a href="<?php bp_docs_order_by_link('modified') ?>"><?php _e('Last Edited', 'bp-docs'); ?></a>
                </th>

                <th scope="column" class="tags-cell hidden-xs"><?php _e( 'Tags', 'bp-docs' ); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php while (have_posts()) : the_post() ?>
                <tr>
                    <td class="title-cell">
                        <a href="<?php bp_docs_group_doc_permalink() ?>"><?php the_title() ?></a>

                        <?php the_excerpt() ?>

                        <div class="row-actions">					
                            <?php bp_docs_doc_action_links() ?>
                        </div>
                    </td>

                    <td class="author-cell">
                        <a href="<?php echo bp_core_get_user_domain(get_the_author_meta('ID')) ?>" title="<?php echo bp_core_get_user_displayname(get_the_author_meta('ID')) ?>"><?php echo bp_core_get_user_displayname(get_the_author_meta('ID')) ?></a>
                    </td>

                    <td class="date-cell created-date-cell"> 
                        <?php echo get_the_date() ?>
                    </td>

                    <td class="date-cell edited-date-cell"> 
                        <?php echo get_the_modified_date() ?>
                    </td>

                    <?php
                            $tags = wp_get_post_terms(get_the_ID(), $this->docs_tag_tax_name);
                            $tagtext = array();

                            foreach ($tags as $tag) {
                                $tagtext[] = bp_docs_get_tag_link(array('tag' => $tag->name));
                            }
                            ?>

                            <td class="tags-cell hidden-xs">
                                <?php echo implode(', ', $tagtext) ?>
                            </td>
		
                </tr>
            <?php endwhile ?>        
        </tbody>

    </table>
</div>

    <div id="bp-docs-pagination">
        <div id="bp-docs-pagination-count">
            <?php printf(__('Viewing %1$s-%2$s of %3$s docs', 'bp-docs'), bp_docs_get_current_docs_start(), bp_docs_get_current_docs_end(), bp_docs_get_total_docs_num()) ?>
        </div>

        <div id="bp-docs-paginate-links">
            <?php bp_docs_paginate_links() ?> 
        </div>
    </div>

<?php else: ?>
    <?php if (groups_is_user_member(get_current_user_id(),bp_get_group_id())): ?>
        <p class="no-docs"><?php printf(__('There are no docs for this view. Why not <a href="%s">create one</a>?', 'bp-docs'), bp_docs_get_item_docs_link() . BP_DOCS_CREATE_SLUG) ?></p>
    <?php else: ?>
        <p class="no-docs"><?php printf(__('There are no docs for this view.', 'bp-docs')) ?></p>
    <?php endif; ?>

<?php endif ?>
