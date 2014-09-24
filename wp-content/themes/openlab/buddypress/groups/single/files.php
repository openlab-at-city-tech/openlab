<?php
/**
 * Files page custom
 * Uses custom functionality derived from BuddyPress Group Documents
 */
?>

<div id="single-course-body" class="plugins">
    <div class="row"><div class="col-md-24">
            <div class="row">
                <div class="submenu col-sm-19">
                    <ul class="nav nav-inline">
                        <li class="current-menu-item"><a href=""><?php _e('Document List', 'bp-group-documents'); ?></a></li>
                    </ul>
                </div>
                <div class="group-count col-sm-5 pull-right"><?php echo openlab_get_files_count(); ?></div>
            </div>
            <?php openlab_bp_group_documents_display_content(); ?>
        </div>
    </div>
</div>

