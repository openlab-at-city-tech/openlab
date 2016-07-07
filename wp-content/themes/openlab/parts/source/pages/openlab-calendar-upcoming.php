<div id="openlabUpcomingEvents" class="calendar-wrapper action-events">
    <div id="item-body">
        <div class="submenu submenu-sitewide-calendar">
            <div class="submenu-text pull-left bold">Calendar:</div>
            <ul class="nav nav-inline">
                <?php foreach ($menu_items as $item): ?>
                    <li class="<?php echo $item['class'] ?>" id="<?php echo $item['slug'] ?>-groups-li"><a href="<?php echo $item['link'] ?>"><?php echo $item['name'] ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <h2><?php echo __('Upcoming Events', 'bp-event-organiser'); ?></h2>
        <?php if (!empty($events)) : ?>
            <ul class="bpeo-upcoming-events">
                <?php
                $_post = $GLOBALS['post'];
                foreach ($events as $post) {
                    ?>
                    <li class="bpeo-upcoming-event-<?php echo esc_attr($post->ID) ?>">
                        <div class="bpeo-upcoming-event-datetime">
                            <span class="bpeo-upcoming-event-date"><?php echo date('M j, Y', strtotime($post->StartDate)) ?></span> &middot;&nbsp; <span class="bpeo-upcoming-event-time"><?php echo date('g:ia', strtotime($post->StartTime)) ?></span>
                        </div>

                        <a class="bpeo-upcoming-event-title" href="<?php echo esc_url(apply_filters('eventorganiser_calendar_event_link', get_permalink($post->ID), $post->ID)) ?>"><?php echo esc_html($post->post_title) ?></a>

                    </li>
                    <?php
                }
                $GLOBALS['post'] = $_post;
                ?>
            </ul>
        <?php else : // ! empty( $events ) ?>
            <p><?php _e('No upcoming events found.', 'bp-event-organiser') ?></p>
        <?php endif; // ! empty( $events )  ?>
    </div>
</div>
