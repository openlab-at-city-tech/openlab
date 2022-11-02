<?php if (bp_docs_is_existing_doc()) : ?>

    <div class="doc-tabs">
        <ul>
            <li<?php if ('single' == bp_docs_current_view()) : ?> class="current"<?php endif ?>>
                <a href="<?php echo bp_docs_get_group_doc_permalink() ?>"><?php _e('Read', 'bp-docs') ?></a>
            </li>

            <?php if ( current_user_can( 'bp_docs_edit' ) ) : ?>
                <li<?php if ('edit' == bp_docs_current_view()) : ?> class="current"<?php endif ?>>
                    <a href="<?php echo bp_docs_get_group_doc_permalink() . '/' . BP_DOCS_EDIT_SLUG ?>"><?php _e('Edit', 'bp-docs') ?></a>
                </li>
            <?php endif ?>

            <?php do_action('bp_docs_header_tabs') ?>
        </ul>
    </div>

<?php endif ?>
