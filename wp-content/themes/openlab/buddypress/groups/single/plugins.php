<?php
/**
* Group plugins - includes files
*
*/

                global $bp, $wp_query; ?>

		<div id="single-course-body" class="plugins action-<?php echo $bp->current_action ?> component-<?php echo $bp->current_component ?><?php echo (openlab_eo_is_event_detail_screen() ? ' event-detail' : '') ?>">
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
                            
                            <?php elseif ($bp->current_component === 'events' || $bp->current_action === 'events'): ?>
                                
                                <div class="submenu-text pull-left bold">Calendar:</div>
                                <ul class="nav nav-inline">
                                    <?php bp_get_options_nav(buddypress()->groups->current_group->slug . '_events'); ?>
                                    <?php if (openlab_eo_is_event_detail_screen()): ?>
                                        <?php $event_obj = openlab_eo_get_single_event_query_obj(); ?>
                                        <?php if(isset($event_obj->post_title)): ?>
                                            <li id="single-event-name" class="current-menu-item"><span><?php echo $event_obj->post_title ?></span></li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </ul>

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