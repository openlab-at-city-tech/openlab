=== GradeBook ===
Contributors: anevo
Tags: gradebook, course management, education, grades, students
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 6.5.3
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A gradebook plugin for educators to create, maintain, and share grades quickly and efficiently.

== Description ==

GradeBook allows educators to manage courses, students, assignments, and grades directly from the WordPress dashboard.

**Instructor Features:**

* Create, edit, and delete courses
* Add, edit, and remove students (new users or existing WordPress users)
* Create, edit, delete, and reorder assignments
* Edit grade cells inline
* Filter assignments by category and toggle visibility
* Sort by assignment columns
* Export gradebook data to CSV
* View student and assignment statistics with interactive charts

**Student Features:**

* View enrolled courses and grades
* View assignment details including due dates
* View performance statistics with pie charts and line graphs

== Installation ==

1. Upload the `an-gradebook` folder to the `/wp-content/plugins/` directory, or install directly through the WordPress plugin screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. A new menu item labeled "GradeBook" will appear in the admin dashboard.

== Frequently Asked Questions ==

= How are student accounts created? =

When adding a student who is not already a WordPress user, a new user account is created automatically. The username is set to the first initial of their first name concatenated with their last name and a string of digits (all lowercase). A random password is generated.

= Can I add existing WordPress users as students? =

Yes. Enter the user's existing username (user_login) when adding a student. If the username exists in the database, the user is added to the gradebook without changing their credentials.

= How do students view their grades? =

Students log in to WordPress and navigate to the GradeBook menu item. They will see only their own grades for courses they are enrolled in.

== Screenshots ==

1. GradeBook with a few courses.
2. GradeBook with a course selected and corresponding students displayed.
3. Line chart for a particular student.
4. Pie chart for a particular assignment.
5. Add course modal.
6. Student view of GradeBook.
7. Student view of assignment details.

== Changelog ==

= 6.5.3 =
* version input on deploy workflow

= 6.5.2 =
* address early esc output

= 6.5.1 =
* apply late escaping at REST API output points

= 6.5.0 =
* add typescript, linting, kebab-case

= 6.4.1 =
* address security vuls

= 6.4.0 =
* replace backbonejs with react

= 6.3.0 =
* move assets to git version control

= 6.2.0 =
* manual deploy

= 6.1.0 =
* escape perl capture group refs in bump-version.sh
* replace sed with perl in update-readme-changelog.sh
* sed command json parsing issue
* sed command json parsing issue
* add semantic-release

= 4.0.0 =
* Compatibility update for WordPress 6.0+ and PHP 7.4–8.3.
* Security: Added nonce verification to all AJAX endpoints.
* Security: All database queries now use prepared statements.
* Security: All user inputs are sanitized and outputs are escaped.
* Security: Added capability checks to all admin endpoints.
* Database tables now use the WordPress table prefix for multisite compatibility.
* Replaced RequireJS module loading with WordPress script enqueue system.
* Uses WordPress-bundled jQuery, Backbone, and Underscore instead of bundled copies.
* Updated Google Charts integration to modern API.
* Added internationalization (i18n) support.
* Removed hardcoded default password for new student accounts; random passwords are now generated.
* Fixed PHP 8.x deprecation warnings and potential errors.
* Fixed undefined variable bugs in course and student creation.
* Meets WordPress.org plugin directory coding standards.

= 3.5.7 =
* Bug Fix: On course delete, gradebook would empty its views. Reclicking the course rerendered the gradebook correctly.
* Minified the app into two essential files.
* Added a debugging toggle in GradeBook.php.
* Slight change to views. Rounded corners are now sharp.

= 3.5.6 =
* Database upgrade: Users no longer in the database are removed from gradebooks.
* Use RequireJS to manage file loading for the app.
* Bug Fix: Adding a student already in the database using their user_login would show an empty user_login cell until page refresh.

= 3.5.5 =
* Bug fix: Sort on first assignment column broken.
* General file management.

= 3.5.4 =
* New Feature: Choose which assignments are visible to students via the edit assignment modal.

= 3.5.3 =
* Update and clean forms.
* Bug fix: Adding a student to a filtered gradebook caused hidden assignment cells to appear.
* CSV filename is derived from the course name and id.

= 3.5.2 =
* Added details view on student side. Students can now view due dates.

= 3.5.1 =
* Fixed bug on student view where the gradebook would not display.

= 3.5 =
* Updated delete student modal styling.
* Added dropdown tools menu for courses.
* Fixed styling conflict with WordPress #adminmenuback.
* New Feature: Export GradeBook to CSV.

= 3.4 =
* Added support for server requests of type x-http-method-override.
* Restyled using Bootstrap.

= 3.3 =
* Instructors can add existing WordPress users by entering their user_login.

= 3.2 =
* Student view improvements.
* Code maintenance: Split up Gradebook_student.js into models and views.

= 3.1 =
* Code maintenance: Split up GradeBook.php into classes.
* Bug Fix: user_login was incorrectly generated.
* Replaced ID column with Login column in the gradebook.
* Added student menu button for edit, delete, and statistics views.

= 3.0 =
* Feature: Added assignment ordering with shift left/right options.
* Fixed assignment header bugs in Firefox.
* Cleaned up views to properly remove unused views and free memory.
* Database upgrade to handle assignment ordering.

== Upgrade Notice ==

= 6.0.0 =
Breaking change: Legacy database migrations have been removed. If upgrading from a version older than 4.0.0, deactivate the plugin, delete the `an_gradebook_db_version` row from `wp_options`, then reactivate. This performs a fresh database setup. Existing data in pre-4.0 unprefixed tables will not be migrated automatically.

= 4.0.0 =
Major compatibility update. Database tables now use WordPress table prefixes. All AJAX endpoints are secured with nonces. Requires WordPress 6.0+ and PHP 7.4+.

== Credits ==

* Plugin icon: [IconFinder](https://www.iconfinder.com/icons/175285/edit_property_icon#size=256)
