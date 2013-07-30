=== Calendar ===
Contributors: KieranOShea
Donate link: http://www.kieranoshea.com
Tags: calendar, dates, times, events
Requires at least: 2.0
Tested up to: 3.5.1
Stable tag: 1.3.3

A simple but effective Calendar plugin for WordPress that allows you to 
manage your events and appointments and display them to the world.

== Description ==

A simple but effective Calendar plugin for WordPress that allows you to 
manage your events and appointments and display them to the world on your 
website.

Features:

*   Monthly view of events
*   Mouse-over details for each event
*   Events can have a timestamp (optional)
*   Events can display their author (optional)
*   Events can span more than one day
*   Multiple events per day possible
*   Events can repeat on a weekly, monthly (set numerical day), monthly (set textual day) or yearly basis
*   Repeats can occur indefinitely or a limited number of times
*   Easy to use events manager in admin dashboard
*   Sidebar function/Widget to show todays events
*   Sidebar function/Widget to show upcoming events
*   Lists of todays events can be displayed in posts or pages
*   Lists of upcoming events can be displayed in posts or pages
*   Comprehensive options panel for admin
*   Modifiable CSS using the options panel
*   Optional drop down boxes to quickly change month and year
*   User groups other than admin can be permitted to manage events
*   Events can be placed into categories
*   A calendar of events for just one of more categories can be displayed
*   Categories system can be switched on or off
*   Pop up javascript calendars help the choosing of dates
*   Events can be links pointing to a location of your choice
*   Full internationalisation is possible
*   Comaptible with WordPress MU

== Installation ==

The installation is extremely simple and straightforward. It only takes a second.

Installing:

1. Upload the whole calendar directory into your WordPress plugins directory.

2. Activate the plugin on your WordPress plugins page

3. Configure Calendar using the following pages in the admin panel:

   Calendar -> Manage Events

   Calendar -> Manage Categories

   Calendar -> Calendar Options

4. Edit or create a page on your blog which includes the text {CALENDAR} and visit 
   the page you have edited or created. You should see your calendar in action.

Upgrading from 1.2 or later:

1. Deactivate the plugin (you will not lose any events)

2. Remove your current calendar directory from the WordPress plugins directory

2. Upload the whole calendar directory into your WordPress plugins directory.

3. Activate the plugin on your WordPress plugins page

4. Configure Calendar using the following pages in the admin panel:

   Calendar -> Manage Events

   Calendar -> Manage Categories

   Calendar -> Calendar Options

5. Edit or create a page on your blog which includes the text {CALENDAR} and visit
   the page you have edited or created page. You should see your calendar in action.

Upgrading from 1.1:

1. Deactivate the plugin (you will not lose any events)

2. Remove the Rewrite rules from your .htaccess file that you added 
   when you first installed Calendar.

3. Delete plugins/calendar.php, wp-admin/edit-calendar.php, wp-calendar.php

4. Upload the whole calendar directory into your WordPress plugins directory.

5. Activate the plugin on your WordPress plugins page

6. Configure Calendar using the following pages in the admin panel:

   Calendar -> Manage Events

   Calendar -> Manage Categories

   Calendar -> Calendar Options

7. Edit or create a page on your blog which includes the text {CALENDAR} and visit
   the page you have edited or created page. You should see your calendar in action.

Uninstalling:

1. Deactivate the plugin on the plugins page of your blog dashboard

2. Delete the uploaded files for the plugin

3. Remove the text {CALENDAR} from the page you were using to show calendar, or delete that page

== Frequently Asked Questions ==

= Where are the frequently asked questions for Calendar? =

   They are located on [Kieran O'Shea's forum](http://www.kieranoshea.com/forum/viewtopic.php?f=13&t=10 "Kieran O'Shea's forum"). 
   Please note that you should check these before asking any support questions or thinking your calendar install isn't working 
   properly.

= Where can I get support for the plugin? =

   Support is only available on [Kieran O'Shea's forum](http://www.kieranoshea.com/forum/viewtopic.php?f=13&t=10 "Kieran O'Shea's forum"). 
   Regrettably e-mail support became too cumberome to manage and so now all support and bug report e-mails for calendar will be ignored. All 
   such queries will be answered promptly on the forums, although please make sure you search first before asking your question.

= Can I remove the link to your site? =
   
   Yes, you may do this, but please be aware that support will not be provided 
   to those who choose to remove the link. When you ask your support question 
   you will be asked for the URL to your blog and the presence of the link will 
   be checked before support will be provided.

== Screenshots ==

1. Calendar being used on a blog page

2. Widgets showing in the sidebar

3. The event management screen of calendar

4. The category management screen of calendar

5. The options screen of Calendar

== Changelog ==

= 1.3.3 =
*   Fixed XSS security issue (thanks to Charlie Eriksen via Secunia SVCRP for the report)

= 1.3.2 =
*   Ensured manage calendar JavaScript only loads on manage calendar page in admin panel
*   Switched to GPL compatible JavaScript date picker
