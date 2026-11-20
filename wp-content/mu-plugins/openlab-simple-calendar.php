<?php

/**
 * Customizations for google-calendar-events (Simple Calendar).
 */

/**
 * Treats every calendar as created before the "Powered by" cutoff.
 *
 * Simple Calendar 4.3.2 and later force the "Powered by Simple Calendar" credit on calendars
 * created after a cutoff date, and hide their opt-in checkbox, unless a Pro license is active.
 * Before the cutoff the credit is an option site admins choose per calendar.
 */
add_filter( 'simcal_is_powered_by_cutoff_calendar', '__return_false' );
