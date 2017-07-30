<div class="action-events">
	<div id="item-body">
        <div class="submenu submenu-sitewide-calendar">
            <div class="submenu-text pull-left bold">Calendar:</div>
            <ul class="nav nav-inline">
                <?php foreach ($menu_items as $item): ?>
                    <li id="<?php echo $item['slug'] ?>-groups-li" class="<?php echo $item['class'] ?>"><a href="<?php echo $item['link'] ?>"><?php echo $item['name'] ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

		<div id="openlabCalendar" class="calendar-wrapper">
		    <?php echo eo_get_event_fullcalendar($args); ?>
		</div>

		<div id="bpeo-ical-download">
		    <h3><?php echo __('Subscribe', 'bp-event-organiser'); ?></h3>
		    <li><a class="bpeo-ical-link" href="<?php echo esc_url( $link ); ?>"><span class="icon"></span><?php echo __('Download iCalendar file (Public)', 'bp-event-organiser'); ?></a></li>
		</div>
	</div>
</div>
