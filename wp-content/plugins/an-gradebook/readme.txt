=== AN_GradeBook ===
Contributors: anevo, jamarparris
Donate link: https://www.paypal.me/aorinevo
Tags: GradeBook, Course Management, Education, Grades
Requires at least: 5.0.0
Tested up to: 5.4.2
Stable tag: 5.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

AN_GradeBook allows educators to create, maintain, and share grades quickly and efficiently.

== Description ==

Check out the [plugin website](http://www.angradebook.com).

Administrators are able to

* Add/delete students
* Add/delete courses
* Add/delete assignments

**IMPORTANT:**

*Username*

Students added through the plugin, who are not already in the database, will have the user_login set to the first initial of their first name concatenated with their last name and a string of digits; all characters must be entered in lowercase.

*Password*

The password will be set to *password*.

*Note: If students are added using their user_login, then their username and password remains unchanged, provided that the respective information exists in the database.*

Students are able to:

* View their grades
* View pie charts and line graphs based on student and class performance

== Installation ==

1. Download and unzip in the plugins/ directory
2. Activate the plugin in the installed plugins page of the admin panel
3. A new admin item menu labeled GradeBook should now be present in the admin dashboard menu

== Screenshots ==

1. List of courses.
2. Gradebook view for a particular course.
3. Pie chart for a particular assignment.
4. Add student form.

== Credits ==

* plugin icon: https://www.iconfinder.com/icons/175285/edit_property_icon#size=256

== Changelog ==

Version 5.0.0:
* feat: Add support for WordPress 5.x
* docs: Add CONTRIBUTING.md
* docs: Rename readme.md to README.md

Version 4.0.11:

* Bug fix: Uncomment line 7 in Database.php.

Version 4.0.10:

* Bug fix: GradeBook not showing up for courses because the init js file fails to append #courses to the end of the url.

Version 4.0.9:

* More shift left/right fixes.

Version 4.0.8:

* Fixed shift left/right bug which unintentionally toggled the visibility of assignments.  The update prevents this from happening but for effected assignments, instuctors need to manually set the visibility again by editing the assignment and selecting the appropriate visibility.
* Fixed settings bug.  Select alternate roles to administer gradebook did not survive page reload.

Version 4.0.7:

* Fixed issue where database tables where not being added on install.

Version 4.0.6:

* Fixed main menu item href to contain correct url.

Version 4.0.5:

* Populate necessary models and collections before instantiating views.
* Fixed settings bug.
* Removed unnecessary dependancies for certain views.
* Added read.md for github.
* Update plugin icon credits.
* Removed ad hock implementation of promises and replaced it with native javascript implementation of promises.
* Previously set an-gradebook to load on admin_init but this caused conflicts with the version of backbone wp uses.  Now an-gradebook scripts are enqueued only on gradebook pages.


Version 4.0.4:

* Added ajax loading image when retrieving course list and gradebook.
* Quick fix for gradebook view, assignment headers were disappearing and new rows failed to render.  This was due to a call to a deprecated function.

Version 4.0.3:

* Added sorting for course list.  Sort by id, name, school, semester, and year.
* Simplified sorting code for gradebook view.
* New charts built with Chartjs.
* Fixed line chart to display student statistics for assignments relative to their order in their gradebook.
* Cleaned up lib directory

Version 4.0.2:

* Added sorting by user_login, first_name, and last_name.
* Added support for legacy web servers.  Some web servers do not handle PUT requests as anticipated.
* remove misc console.log statements.
* Added D3js library.  We are moving towards removing dependencies on google charts used in rendering student and assignment statistics.

Version 4.0.1:

* Plugin had two separate js files that handled the gradebook and settings separately.  The files are now combined into one file.
* Style page title and fix margin to be consistent with wpcontent styles.
* Fixed issue where student view of gradebook always used John for first name and Doe for last name.


Version 4.0:

* Added search when adding students to gradebook.  Automatically queries the database as you type and returns a list of users from which to select.
* Renamed plugin database tables to an_gradebook_cells, an_gradebook_assignments, an_gradebook_courses, an_gradebook_users.
* Security fix: added a role check when requesting line chart statistics. Otherwise, there’s a potential for a students grades to be exposed however, it would be unlikely that the user would know the link, user id, and gradebook id to successfully do this.
* Fixed margin-right to prevent gradebook from stretching all the way to the right of the browser window.
* When gradebook loads courses and students, a loading indicator is displayed.
* Only administrators, users with wordpress role set to administrator, are allowed to add courses.  Instructors, with the gradebook role set to instructor, can edit, delete and add students. There is also a student role in gradebook which is automatically assigned to a user added to a gradebook.
* Bug fix. Seems that the delete course bug wasn’t fixed in v3.5.7.  This occurs when a user deletes a selected gradebook.  Then the view doesn’t remove itself.
* Added settings page, where WordPress admin users can select roles that are allowed to administer AN_GradeBook. Users with those roles will be able to create new gradebooks.
* Reorganized files.  Main changes under the app directory.
* Added a router.
* The gradebook view now displays on a separate page, instead of below the course list.  To access the gradebook view, select view item from the dropdown menu.
* Dropped backwards compatibility up to v2.9

Version 3.5.7:

* Bug Fix: On course delete, gradebook would empty its views.  Reclicking the course, rerendered the gradebook correctly.
* Minified the app into two essential files, app-instructor-min.js and app-student-min.js
* Added a debugging toggle in GradeBook.php
* Slight change to views. Rounded corners are now sharp.

Version 3.5.6:

* Database upgrade: Users no longer in the database are removed from gradebooks. Deleting users through the Users tab, removes students from gradebooks.
* Use RequireJS to manage file loading for almost the entire app.  A couple of js files have to be loaded through php.  In particular, the css files for bootstrap and jquery-ui, the require.js file, and the dependent file app.js.
* Bug Fix: Adding a student that was already in the database, using their user_login, would add the student to the gradebook but the user_login cell would be empty.  If the page was refreshed, the user_login would display.


Version 3.5.5:

* Bug fix: Sort on first assignment column broken.
* General file management.

Version 3.5.4:

* New Feature: Choose which assignments are visible to your students by selecting the Students option in the edit assignment modal.

Version 3.5.3:

* Update and clean forms.
* Bug fix: Adding a student to an already filtered gradebook caused hidden assignment cells for that student to appear.
* Download cvs filename is derived from the course name and id.  For example, a Calculus I course with ID 19 will have the exported csv stored in a file named Calculus_I_19.csv


Version 3.5.2:

* Added details view on student side of the gradebook.  In particular, students can now view due dates.

Version 3.5.1:

* Fixed bug on student view where the gradebook would not display.

Version 3.5:

* The delete student modal was still rendered using old styling.  This was updated to the new styling.
* Added dropdown tools menu for courses, similar to the one for students.  This allowed us to remove the edit and delete buttons from the top of the GradeBook page.
* Fixed styling conflict both on the student view and instructor view with wordpress #adminmenuback.
* Added background-color: white to tables.
* New Feature: Export GradeBook to CSV.

Version 3.4:

* Added support for server requests of type x-http-method-override.
* Restyled using Bootstrap.

Version 3.3:

* Instructors now can add existing users in their WordPress database to the GradeBook by entering the user_login instead of the confusing and difficult to find user_id.  If the user_login exists in the database, the user is added to the GradeBook.  Otherwise, nothing happens.

Version 3.2:

* This update is mostly for the student view of grade-book.
* Code maintenance: Split up Gradebook_student.js into models and views.

Version 3.1:

* Code maintenance: Split up GradeBook.php into classes.
* Code maintenance: Removed unnecessary lines of code such as redundant wp_enqueue_script calls.
* Bug Fix: user_login was incorrectly set to the users second initial of their first name, lastname, and user_id concatenated together, in lowercase.
* Due to the bug fix, we removed the ID column of the gradebook and replaced it with a Login column.  This is the login name the user must use to log in.  The password is still set to password.
* Added a student menu button that handles edit, delete, and statistics views.

Version 3.0:

* Fixed assignment header bug where sorting a column caused the header cell to be the only cell to change color on hover on the first mouse enter.
* Feature: Added assignment ordering.  Newly added assignments are appended to the gradebook.  To move an assignment to the left or right, click on the assignment cell menu icon and choose from the shift options.
* Fixed assignment header display bug (Firefox).  Assignment headers would fail to display in Firefox.
* Cleaned up views.  Views were rarely removed, which ate up memory.  Now all unnecessary views are removed.
* Upgraded the database to handle ordering.
* Other performance and code enhancements.
