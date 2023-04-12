<div class="meta-header"><p>Ensure dates are entered in mm-dd-yyyy format and times in 12 hour format</p></div>
    <div class="eo-grid <?php echo ( $sche_once ? 'onetime' : 'reoccurence' ); ?>">

        <div class="eo-grid-row">
            <div class="eo-grid-4">
                <span class="eo-label" id="eo-start-datetime-label">
                    <?php esc_html_e('Start Date/Time:', 'eventorganiser'); ?>
                </span>
            </div>
            <div class="eo-grid-8 event-date" role="group" aria-labelledby="eo-start-datetime-label">

                <label for="eo-start-date" class="screen-reader-text"><?php esc_html_e('Start Date', 'eventorganiser'); ?></label>
                <input type="text" id="eo-start-date" aria-describedby="eo-start-date-desc" class="ui-widget-content ui-corner-all" name="eo_input[StartDate]" size="10" maxlength="10" value="<?php echo $start->format($php_format); ?>"/>
                <span id="eo-start-date-desc" class="screen-reader-text"><?php echo esc_html($date_desc); ?></span>

                <label for="eo-start-time" class="screen-reader-text"><?php esc_html_e('Start Time', 'eventorganiser'); ?></label>
                <?php
                printf(
                        '<input type="text" id="eo-start-time" aria-describedby="eo-start-time-desc" name="eo_input[StartTime]" class="eo_time ui-widget-content ui-corner-all" size="6" maxlength="8" value="%s"/>', eo_format_datetime($start, $time_format)
                );
                ?>
                <span id="eo-start-time-desc" class="screen-reader-text"><?php echo esc_html($time_desc); ?></span>
            </div>
        </div>

        <div class="eo-grid-row">
            <div class="eo-grid-4">
                <span class="eo-label" id="eo-end-datetime-label">
                    <?php esc_html_e('End Date/Time:', 'eventorganiser'); ?>
                </span>
            </div>
            <div class="eo-grid-8 event-date" role="group" aria-labelledby="eo-end-datetime-label">

                <label for="eo-end-date" class="screen-reader-text"><?php esc_html_e('End Date', 'eventorganiser'); ?></label>
                <input type="text" id="eo-end-date" aria-describedby="eo-end-date-desc" class="ui-widget-content ui-corner-all" name="eo_input[EndDate]" size="10" maxlength="10" value="<?php echo $end->format($php_format); ?>"/>

                <span id="eo-end-date-desc" class="screen-reader-text"><?php echo esc_html($date_desc); ?></span>
                <label for="eo-end-time" class="screen-reader-text"><?php esc_html_e('End Time', 'eventorganiser'); ?></label>
                <?php
                printf(
                        '<input type="text" id="eo-end-time" aria-describedby="eo-end-time-desc" name="eo_input[FinishTime]" class="eo_time ui-widget-content ui-corner-all" size="6" maxlength="8" value="%s"/>', eo_format_datetime($end, $time_format)
                );
                ?>
                <span id="eo-end-time-desc" class="screen-reader-text"><?php echo esc_html($time_desc); ?></span>

                <span>
                    <input type="checkbox" id="eo-all-day"  <?php checked($all_day); ?> name="eo_input[allday]" value="1"/>
                    <label for="eo-all-day">
                        <?php esc_html_e('All day', 'eventorganiser'); ?>
                    </label>
                </span>

            </div>
        </div>

        <div class="eo-grid-row event-date">
            <div class="eo-grid-4">
                <label for="eo-event-recurrence"><?php esc_html_e('Recurrence:', 'eventorganiser'); ?> </label>
            </div>
            <div class="eo-grid-8 event-date">
                <select id="eo-event-recurrence" name="eo_input[schedule]">
                    <?php foreach ($recurrence_schedules as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value) ?>" <?php selected($schedule, $value); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="eo-grid-row event-date reocurrence_row">
            <div class="eo-grid-4"></div>
            <div class="eo-grid-8 event-date">
                <div id="eo-recurrence-frequency-wrapper">
                    <?php esc_html_e('Repeat every', 'eventorganiser'); ?>
                    <label for="eo-recurrence-frequency" class="screen-reader-text"><?php esc_html_e('Recurrence frequency', 'eventorganiser'); ?></label>
                    <input type="number" id="eo-recurrence-frequency" class="ui-widget-content ui-corner-all" name="eo_input[event_frequency]"  min="1" max="365" maxlength="4" size="4" value="<?php echo intval($frequency); ?>" />
                    <span id="eo-recurrence-schedule-label"></span>
                </div>

                <div id="eo-day-of-week-repeat">

                    <span id="eo-days-of-week-label" class="screen-reader-text"><?php esc_html_e('Repeat on days of week:', 'eventorganiser'); ?></span>
                    <span class="eo-days-of-week-text"><?php esc_html_e('on', 'eventorganiser'); ?></span>
                    <ul class="eo-days-of-week" role="group" aria-labelledby="eo-days-of-week-label">
                        <?php
                        for ($i = 0; $i <= 6; $i++) :
                            $d = ($start_day + $i) % 7;
                            $ical_d = $ical_days[$d];
                            $day = $wp_locale->weekday_abbrev[$wp_locale->weekday[$d]];
                            $fullday = $wp_locale->weekday[$d];
                            $schedule_days = ( is_array($schedule_meta) ? $schedule_meta : array() );
                            ?>
                            <li>
                                <input type="checkbox" id="day-<?php echo esc_attr($day); ?>"  <?php checked(in_array($ical_d, $schedule_days), true); ?>  value="<?php echo esc_attr($ical_d) ?>" class="daysofweek" name="eo_input[days][]"/>
                                <label for="day-<?php echo esc_attr($day); ?>" > <abbr aria-label="<?php echo esc_attr($fullday); ?>"><?php echo esc_attr($day); ?></abbr></label>
                            </li>
                            <?php
                        endfor;
                        ?>
                    </ul>
                </div>

                <div id="eo-day-of-month-repeat">
                    <span id="eo-days-of-month-label" class="screen-reader-text"><?php esc_html_e('Select whether to repeat monthly by date or day:', 'eventorganiser'); ?></span>
                    <div class="eo-days-of-month" role="group" aria-labelledby="eo-days-of-month-label">
                        <label for="eo-by-month-day" >
                            <input type="radio" id="eo-by-month-day" name="eo_input[schedule_meta]" <?php checked($occurs_by, 'BYMONTHDAY'); ?> value="BYMONTHDAY=" />
                            <?php esc_html_e('date of month', 'eventorganiser'); ?>
                        </label>
                        <label for="eo-by-day" >
                            <input type="radio" id="eo-by-day" name="eo_input[schedule_meta]"  <?php checked('BYMONTHDAY' != $occurs_by, true); ?> value="BYDAY=" />
                            <?php esc_html_e('day of week', 'eventorganiser'); ?>
                        </label>
                    </div>
                </div>

                <div id="eo-schedule-last-date-wrapper" class="reoccurrence_label">
                    <?php esc_html_e('until', 'eventorganiser'); ?>
                    <label id="eo-repeat-until-label" for="eo-schedule-last-date" class="screen-reader-text"><?php esc_html_e('Repeat this event until:', 'eventorganiser'); ?></label>
                    <input class="ui-widget-content ui-corner-all" name="eo_input[schedule_end]" id="eo-schedule-last-date" size="10" maxlength="10" value="<?php echo $until->format($php_format); ?>"/>
                </div>

                <p id="eo-event-summary" role="status" aria-live="polite"></p>

            </div>
        </div>

        <div id="eo_occurrence_picker_row" class="eo-grid-row event-date">
            <div class="eo-grid-4">
                <?php esc_html_e('Include/Exclude occurrences:', 'eventorganiser'); ?>
            </div>
            <div class="eo-grid-8 event-date">
                <?php submit_button(__('Show dates', 'eventorganiser'), 'hide-if-no-js eo_occurrence_toggle button small', 'eo_date_toggle', false); ?>

                <div id="eo-occurrence-datepicker"></div>
                <input type="hidden" name="eo_input[include]" id="eo-occurrence-includes" value="<?php echo $include_str; ?>"/>
                <input type="hidden" name="eo_input[exclude]" id="eo-occurrence-excludes" value="<?php echo $exclude_str; ?>"/>

            </div>
        </div>

        <?php
        $tax = get_taxonomy('event-venue');
        if (taxonomy_exists('event-venue')) :
            ?>

            <!-- Add New Venue -->
            <div class="eo-grid-row eo-add-new-venue-custom">
                <div class="eo-grid-4">
                    <label for="eo_venue_name"><?php esc_html_e('Venue Name', 'eventorganiser'); ?></label>
                </div>
                <div class="eo-grid-8">
                    <input type="text" name="eo_venue[name]" id="eo_venue_name"  value="<?php echo $venue_stored_name ?>"/>
                </div>

                <?php
                foreach ($address_fields as $key => $label) {
                    printf(
                            '<div class="eo-grid-4">
						<label for="eo_venue_add-%2$s">%1$s</label>
					</div>
					<div class="eo-grid-8">
						<input type="text" name="eo_venue[%2$s]" class="eo_addressInput" id="eo_venue_add-%2$s"  value="%3$s"/>
					</div>', esc_html($label), esc_attr(trim($key, '_'))/* Keys are prefixed by '_' */, (isset($address[trim($key, '_')]) ? $address[trim($key, '_')] : '')
                    );
                }
                ?>

                <div class="eo-grid-4"></div>
            </div>

            <div class="eo-grid-row venue_row <?php
        if (!$venue_id) {
            echo 'novenue';
        }
                ?>">
                <div class="eo-grid-4"></div>
                <div class="eo-grid-8">
                    <div id="eventorganiser_venue_meta" style="display:none;">
                        <input type="hidden" id="eo_venue_Lat" name="eo_venue[latitude]" value="<?php esc_attr(eo_venue_lat($venue_id)); ?>" />
                        <input type="hidden" id="eo_venue_Lng" name="eo_venue[longtitude]" value="<?php esc_attr(eo_venue_lng($venue_id)); ?>" />
                    </div>

                    <div id="venuemap" class="ui-widget-content ui-corner-all gmap3"></div>
                    <div class="clear"></div>
                </div>
            </div>
        <?php endif; //endif venue's supported              ?>

    </div>
    <?php // create a custom nonce for submit verification later ?>
    <?php wp_nonce_field('eventorganiser_event_update_' . get_the_ID() . '_' . get_current_blog_id(), '_eononce');
