=== KB Gradebook ===
Contributors: adamrbrown
Donate link: http://adambrown.info/b/widgets/donate/
Tags: education, gradebook, grades, school, teaching, gradesheet
Requires at least: 2.0
Tested up to: 2.5
Stable tag: trunk

Many instructors already use WordPress to run course websites. Now they can also use WordPress to let students securely check their grades online.

== Description ==

Students are frequently more concerned about their grades than they ought to be. Reduce some of their academic stress by letting them see what you've recorded for them in your grade book. If you're like me, you'll be surprised how frequently your students notice that you mis-recorded a grade. Includes an optional widget.

= How it Works =

You upload a CSV file (from Excel or some other spreadsheet) containing your students' grades. One column in the spreadsheet contains student (or parent) email addresses. The plugin generates a random password for each email address (or you can use WP logins; see FAQ). Students use this password with their email to check their grades. To update the grades, you just upload a new CSV.

After uploading a CSV, you type up a generic message that will be personalized for each student. For example, if one of the columns in your spreadsheet is called "midterm" and another is called "final," your message might be something like this:

> Dear [student], you got a [midterm] on the midterm and a [final] on your final.

Obviously, individual students would see their grades substituted in for `[midterm]` and `[final]`. If you don't feel like typing out a message, you don't have to; when a student logs in, she'll just see an automatically generated table showing her all the grades you have recorded for her.

After uploading a gradesheet, create a page containing `[KB Gradebook]` somewhere in the body. That's where your students will go to check their grades. There's also a sidebar widget included if that's your thing.

= Features =

* You can have several different courses at once if you want. Just give each one a unique name, like "Second period English" or "Econ 100, Fall 2007." It's okay if the same students are in more than one class.
* Since any decent spreadsheet program can export CSV files, it's very easy to use.

= Support =

If you post your support questions as comments below, I probably won't see them. If the FAQs don't answer your questions, you can post support questions at the [KB Gradebook plugin page](http://adambrown.info/b/widgets/category/kb-gradebook/) on my site.

== Installation ==

If you are upgrading from a 0.1+ to a 1.+ version, don't do it until you read the important comments at the top of the plugin file!

1. Upload `kb_gradebook.php` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to the new 'Manage => KB Gradebook' admin page. You'll find instructions for importing grades. Give it a go.
1. Create a new page called "Grade Viewer" or something. Add `[KB Gradebook]` somewhere in the body. Navigate to this new page. You should see a form for checking grades.

You can test that it worked by adding yourself to your gradesheet before uploading it (as if you were a student). Then, you can punch your own email address into the grade viewing page and see what happens.

If you like, you can add the KB Gradebook widget to your sidebar at this point. It will want to know the URL of the grade viewing page, so make sure you create that first.

= License =

This plugin is provided "as is" and without any warranty or expectation of function. I'll probably try to help you if you ask nicely, but I can't promise anything. You are welcome to use this plugin and modify it however you want, as long as you give credit where it is due.

If you are using this plugin for profit (i.e. you're using WP-MU and charging people to use this plugin), contact me and cut me in, eh?

An additional disclaimer: Your grade information is only as safe as your WordPress database. It remains your responsibility to decide whether using this plugin keeps your students' data adequately private. See the FAQs for more about security.

== Screenshots ==

You can see an example at the [KB Gradebook plugin page](http://adambrown.info/b/widgets/kb-gradebook/).

== Frequently Asked Questions ==

= Excel gives me weird warnings when I "Save As" as CSV =

That's because Excel is dumb. All the warnings are telling you is that you'll lose formatting--only text can go into a CSV--and that only the active sheet will be saved in the CSV. But all your formatting and multiple sheets will remain in the original Excel file. When you click "Save As," you create a copy of the original in CSV format. Just save the CSV to your desktop and delete it after uploading it.

= My students already have WP logins; they don't need randomly generated passwords =

Sweet. Open the plugin file and look for this...

`define('KBGV_WPUSERS', false );`

... and set it to true. Once you do that, the plugin will use WP's native authentication system rather than using its own randomly generated passwords.

= I had students add/drop my class. Do I need to delete the old class and start over? =

No. The plugin will notice the new students and generate passwords for them. The dropped students will remain in the passwords database, but they will no longer be able to log in and check their grades (since they won't show up in the gradesheet).

= I'm having trouble importing my gradesheet =

It works like a charm with my versions of Excel and Firefox, but I haven't tested other arrangements. Please let me know what error messages you're getting (if any), what spreadsheet program you're using (and version), what browser you're using (and version), and what platform you use (Windows/Mac/Linux). I don't guarantee that I can fix it, though.

= Some of the data gets cut off =

If you have more than 99,999 characters within a single row of your spreadsheet, then everything from the 100,000th character on will get cut off. To fix this, open the plugin file and increase this setting to something higher:

`define('KBGV_CSVCUTOFF', 99999);`

If you have PHP 5.0.4 or higher, set it to 0 to make line lengths infinite.

= Is my gradesheet secure? =

To protect my legal rear end, I'll say that it is your job to look at the code and decide whether your data will be secure; I make no guarantees. But I do use this software myself and am comfortable doing so. If the MySQL database that holds all your WordPress information is secure, then your gradesheet should be secure.

When students punch in their email address and password, they will see only their own grades on the next screen, not the entire gradebook. It is possible (but not likely) that something could go funny when you import your gradebook, and a student might end up seeing somebody else's grades instead of his own. To rule out this possibility, you will have the opportunity to check the imported gradesheet for errors before it becomes live on your site. (And even if this did happen, the student would probably have no idea whose grades he was looking at anyway.)

The most likely way that data might be insecure would be if a student viewed their grades and then left them up on the screen and walked away. In this case, somebody else might see them. So you may want to post a note on your grade viewing page reminding students to close the browser after checking their grades to help prevent this. (But frankly, it would be your students' fault for not protecting their own data.)

= I have a question that isn't addressed here. =

You may ask questions by posting a comment to the [KB Gradebook plugin page](http://adambrown.info/b/widgets/category/kb-gradebook/).