<div class="sidebar-widget" id="group-member-portfolio-sidebar-widget">
    <h2 class="sidebar-header">Upcoming Events</h2>

    <div class="sidebar-block">

        <ul class="group-event-activity inline-element-list group-data-list sidebar-sublinks">
            <?php if (!empty($events)) : ?>
                <?php
                $_post = $GLOBALS['post'];
                foreach ($events as $post) {
                    ?>
                    <li class="bpeo-upcoming-event-<?php echo esc_attr($post->ID) ?>">
                        <a class="bpeo-upcoming-event-title" href="<?php echo esc_url(apply_filters('eventorganiser_calendar_event_link', get_permalink($post->ID), $post->ID)) ?>"><?php echo esc_html($post->post_title) ?></a><br />
                        <span class="bpeo-upcoming-event-date"><?php echo date('M j, Y', strtotime($post->StartDate)) ?></span>&nbsp;<span class="bpeo-upcoming-event-time"><?php echo date('g:ia', strtotime($post->StartTime)) ?></span>
                    </li>
                    <?php
                }
                $GLOBALS['post'] = $_post;
                ?>

            <?php else : // ! empty( $events ) ?>
                <li><?php _e('No upcoming events found.', 'bp-event-organiser') ?></li>
            <?php endif; // ! empty( $events )  ?>
        </ul>

    </div>
</div>
