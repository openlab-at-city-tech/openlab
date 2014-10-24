<?php
/**
* Group plugins - includes files
*
*/

                global $bp; ?>

		<div id="single-course-body" class="plugins">
                    <div class="row"><div class="col-md-24">
                        <div class="submenu">
			<?php if ( $bp->current_action == 'invite-anyone' || $bp->current_action == 'notifications' ) : ?>
                    
                                <ul class="nav nav-inline">
                                    <?php openlab_group_membership_tabs(); ?>
                                </ul>
                            
                        <?php elseif ($bp->current_action == 'docs'): ?>
                            
                                <ul class="nav nav-inline">
                                    <?php openlab_docs_tabs(); ?>
                                </ul>
                            
                            <?php elseif ($bp->current_action == 'files'): ?>

                                <div class="row">
                                    <div class="submenu col-sm-17">
                                        <ul class="nav nav-inline">
                                            <li class="current-menu-item"><a href=""><?php _e('Document List', 'bp-group-documents'); ?></a></li>
                                        </ul>
                                    </div>
                                    <div class="group-count col-sm-7 pull-right"><?php echo openlab_get_files_count(); ?></div>
                                </div>

                            <?php else: ?>
                                <ul class="nav nav-inline">
                                        <?php do_action( 'bp_group_plugin_options_nav' ); ?>
                                </ul>
			<?php endif; ?>
                        </div>
                        </div></div>

			<div id="item-body">
                            
                                <?php do_action( 'bp_before_group_plugin_template' ); ?>

				<?php do_action( 'bp_template_content' ); ?>

				<?php do_action( 'bp_after_group_plugin_template' ); ?>
			</div><!-- #item-body -->
		</div>