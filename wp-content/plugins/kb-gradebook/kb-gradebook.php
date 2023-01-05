<?php
/*
Plugin Name: KB Gradebook
Description: Teachers, let your students check their grades online! Easily import grades from Excel or other spreadsheet software.
Author: Adam R. Brown
Version: 1.04
Plugin URI: http://adambrown.info/b/widgets/category/kb-gradebook/
Author URI: http://adambrown.info/
*/



/////////////////////////////////

//	ATTENTION: If you are UPGRADING, don't do it until you read the upgrade info in the 'DEV NOTES' below.

//	ATTENTION AGAIN: Before contacting me with FEATURE REQUESTS, read the "Feature Requests" info in the DEV NOTES.

////////////////////////////////





/* CHANGE LOG
	0.1	Original
	0.1.1	Allow more tags in grade message
	0.1.2	yet more tags allowed
	0.1.3	change id on widget's submit button to eliminate validation errors.
	1.0	Completely rewritten. Now with CSV importation! Woohoo! (Use v0.1.3 if this causes trouble for you.) The 0.1+ branch will no longer be maintained.
	1.01	very minor changes
	1.02	more
	1.03	option to use WP usernames and passwords instead (BETA)
	1.04	a fix for php 4
*/

// optional settings
define('KBGV_SLUG', '[KB Gradebook]'); // what will be placed in a page to trigger the plugin?
define('KBGV_CSVCUTOFF', 99999); // don't change unless you've read the FAQ

// setting this to TRUE activates a BETA feature of this plugin. Pre-beta, really. Please let me know if you use this and it works, since I haven't tested it much.
define('KBGV_WPUSERS', false ); // set to TRUE if all your students have a WP login at your blog AND their profile emails match the email column in your spreadsheet.



/* DEV NOTES

	IMPORTANT UPGRADE INFORMATION
	If you are upgrading from pre 1.0 to post 1.0, you need to read this paragraph:
		1.0 stores grade data in a different manner from pre-1.0. Any grade data that you uploaded on a pre-1.0 version of this plugin will be lost when you upgrade.
	Sorry. Since very few people use this plugin, I didn't want to bother with creating an updater to convert the data from the old format to the new one. Before you upgrade,
	However, your list of courses and your students' passwords will not be lost.

	IMPORTANT IMPORTANT IMPORTANT
	I wrote this for SMALL classes. If you've got a lot of students in a single spreadsheet, this might cause serious CPU usage problems. I would advise against
	using this if you have more than a few dozen students in a single spreadsheet. (For this plugin to scale for large classes, it would need to create its own tables
	rather than just saving a large array to the DB.)

	FEATURE REQUESTS?
	Unless you have money, I'm probably not going to do coding for you. I'm sorry. I made this for myself. I release it so others can use it if they want, but really, this is
	open source software--if you think something could be spiced up, just do it yourself. If you make really cool improvements, send them my way and maybe I'll integrate
	them into the code and add your name as a developer to the WP plugins repository.

	OPTION PREFIXES
	Moving from 0.1.3 to 1.0 entailed a complete rewrite, along with modifications to how we use options. The 0.1+ branch used kbgv_ as prefix for all options.
	Options that preserved their structure continue to use that prefix (kbgv_courses, kbgv_students). But options with a new structure now use kbgvNEW_ as
	a prefix.

	FUNCTION NAMESPACE
	Everything global in here starts with kbgv_ 	(stands for KB Grade Viewer)

	DATABASE STRUCTURE
	All data is stored via WP's options interface.
	* kbgv_courses			Contains an array of available courses (as array values; keys are meaningless)
	* kbgv_students			Contains an array of student emails and passwords. Emails are keys, passwords are values.
	* kbgv_ . md5(coursename)	deprecated in favor of...
	* kbgvNEW_ . md5(coursename)	Contains a multidimensional array of grade info for one of the courses in kbgv_courses. Keys are student emails (with a couple additional
						keys containing extra info), values are an array of the student's grades as imported from CSV.

	CODE FLOW - STUDENT VIEW
	* Student navigates to a PAGE (not post) that the teacher has set up containing our slug (you can set the slug with the setting at the top of this file).
		(Using the included widget makes this easier on the students.)
	* On first visit, student clicks "request my password" to have password emailed to her
	* On subsequent visits, student enters email and password to check grade information.
	* Note that the student is NOT LOGGED IN to anything. This is not a WP login being used, just a posted email and password.

	CODE FLOW - TEACHER VIEW
	* Teacher goes to Manage -> KB Gradebook
	* Teacher selects an existing course or creates a new one
	* Teacher uploads a CSV. CSV must conform to the guidelines listed on the upload page.
	* Plugin parses the CSV and saves to option kbgvNEW_ . md5(coursename)
	* Teacher writes a generic message. Individual students will see their grades filled into the message.

	INSTRUCTOR'S OPTIONS
	* Delete all classes and students (useful at end of year)
*/




// stick everything in here
function kbgv_plugin_init(){

	///////////////////////////////////////////////
	// THE ACTION IS HERE
	///////////////////////////////////////////////
	function kbgv_admin_page(){		// for teachers to import their gradesheet from excel or whatever
		$postpath = set_url_scheme( get_settings('siteurl').'/wp-admin/edit.php?page=kb-gradebook.php' );
		$courselist = get_option('kbgv_courses');
		$args = array( 'postpath'=>$postpath, 'courselist'=>$courselist );


		if ($_GET['step']==2)
			$out = kbgv_uploadCSVPage($args);
		elseif( $_GET['step']==3 )
			$out = kbgv_writeMessagePage($args);
		elseif( $_GET['step']==4 )
			$out = kbgv_errorCheckPage($args);
		else
			$out = kbgv_selectCoursePage($args);
		if ($out[1])
			$alert = '<div id="message" class="updated fade">'.$out[1].'</div>';
		echo $alert.'<div class="wrap">'.$out[0].'</div>';
	}

	///////////////////////////////////////////////
	// ADMIN PAGES -- return an array. First value is HTML to display, second value is an alert/error (if any) to display
	///////////////////////////////////////////////
	function kbgv_selectCoursePage($args){
		extract($args);
		// are we deleting old student data?
		if ( ('delete'==$_GET['step']) && ('delete'==$_POST['delete_all']) ){
			if ( is_array( $courselist ) ){
				foreach( $courselist as $course ){
					delete_option( 'kbgvNEW_' . md5( $course ) );
					delete_option( 'kbgvNEW_' . md5( $course ) . '_temp' );
				}
			}
			delete_option('kbgv_courses');
			delete_option('kbgv_students');
			unset( $courselist );
			$alert .= '<p>All your old gradebook information has been successfully deleted.</p>';
		}elseif('delete'==$_GET['step']){
			$alert .= '<p>Nothing was deleted. You clicked "submit" but did not check the "delete all data" box first.</p>';
		}
		// display start page
		$out .= '
			<h2>KB Gradebook: Upload Grades</h2>
			<p>If you are familiar with KB Gradebook already, fill in the form below. If not, you should read the <a href="http://wordpress.org/extend/plugins/kb-gradebook/">quick overview of what KB Gradebook does</a> (and does not) do.</p>
			<form method="post" action="'. $postpath .'&amp;step=2">
			<table class="widefat">
		';
		if ( is_array( $courselist) ){
			$out .= '<tr><td><label for="existing_course">If you are updating grades for an existing course, please select it from this list. If you wish to create a new course, leave this field blank.</label></td>';
			$out .= '<td><select name="existing_course" id="existing_course" style="width:300px;">';
			$out .= "<option value=''> </option>\n";
			foreach( $courselist as $course )
				$out .= "<option>{$course}</option>\n";
			$out .= '</select></td></tr>';
		}
		$out .= '
			<tr>
				<td><label for="new_course">To create a new course, write a unique name for the course in the box.</label><br /><small>Examples: <i>Fall 2007</i>, <i>Economics 101</i> or <i>Second period English</i></small></td>
				<td><input type="text" style="width:300px;" name="new_course" id="new_course" /></td>
			</tr>
			<tr>
				<td><p class="submit"><input type="submit" value="Continue &raquo;" /></p></td>
				<td><input type="reset" value="Clear form" /></td>
			</tr>
			</table>
			</form>
		';
		if ( is_array( $courselist) ){
			$out .= '
				</div>

				<div class="wrap">
				<h2>New School Year? Delete Old Grades</h2>
				<p>To keep your database from getting overloaded (and slowed down) by old grade information, you should flush your database at the end of each academic year. When you update an existing course, your database does not grow significantly, but if you teach several courses each year, you should delete your old courses every so often.</p>
				<p>Deleting old grades will also delete old passwords. (New passwords will be generated for students who show up on a future grade sheet.) Even if you have the same students from year to year, deleting old passwords helps protect student privacy.</p>
				<form method="post" action="'. $postpath .'&amp;step=delete">
				<table><tr><td><input type="checkbox" name="delete_all" id="delete_all" value="delete" /> </td>
					<td><label for="delete_all">Check this box and submit this form to delete <b>all</b> your grade sheets and student data.</label></td></tr>
				</table>
				<p class="submit" style="width:420px;"><input type="submit" value="Delete &raquo;" /></p>
			';
		}
		return array( $out, $alert );
	}

	function kbgv_uploadCSVPage($args){
		extract( $args );
		// in case we've been sent back to this step by the next one, let's make sure we'll recognize the course name:
		if (''!=$_POST['class'] && ''==$_POST['existing_course'])
			$_POST['existing_course'] = $_POST['class'];

		// validate inputs:
		if (''==$_POST['existing_course'] && ''==$_POST['new_course']){
			$alert .= "<p>Error: You did not select a course.</p>";
			$args['alert'] = $alert;
			return kbgv_selectCoursePage($args);
		}
		// If an existing course was selected, let's validate that
		if ( '' != $_POST['existing_course'] ){
			$_POST['existing_course'] = kbgv_cleanup_coursename( $_POST['existing_course'] );
			if ( !in_array( $_POST['existing_course'], $courselist ) ){
				$alert .= "<p>Error: The selected course is not valid.</p>";
				$args['alert'] = $alert;
				return kbgv_selectCoursePage($args);
			}
			if ( '' != $_POST['new_course'] ){
				$alert .= '<p>Error: You selected an existing course but also typed in a new course name. Please leave one of these fields blank so that it is clear whether you are creating or modifying a course.</p>';
				$args['alert'] = $alert;
				return kbgv_selectCoursePage($args);
			}
			$coursename = $_POST['existing_course'];
		}
		// now let's validate if a new course was typed in:
		if ( '' != $_POST['new_course'] ){
			if ( is_array($courselist) && in_array( $_POST['new_course'], $courselist ) ){
				$alert .= '<p>Error: You are trying to create a course with the same name as one that already exists. If you are trying to modify an existing course, please select it from the dropdown menu.</p>';
				$args['alert'] = $alert;
				return kbgv_selectCoursePage($args);
			}
			$coursename = kbgv_cleanup_coursename( $_POST['new_course'] );
			$courselist[] = $coursename;
			if ( !kbgv_update_option('kbgv_courses', $courselist) ){
				$alert .= '<p>Error: Unable to write to database. Please try again later.</p>';
				$args['alert'] = $alert;
				return kbgv_selectCoursePage($args);
			}
		}
		// okay, we're good. Continue.
		$out .= '
			<h2>Step 2: Upload Grades</h2>
			<p>For your gradesheet to import properly, it must meet the following simple guidelines (<a href="http://spreadsheets.google.com/pub?key=p_bmzDs47XgZRwpYg4P6qOQ">view example</a>):</p>
			<ul>
				<li><strong>CSV files only</strong>. Every decent spreadsheet program can export data as CSV (<i>comma-separated values</i>). For example, in Excel, select "Save As" and then select file type "CSV (Comma Delimited)".</li>
				<li><strong>Use a header row</strong>. That is, each cell in the first row should contain a unique column name, like "Midterm," "Final," "Overall Grade," or "Email," that describes that column.</li>
				<li><strong>One student per row</strong>. This should be obvious.</li>
				<li><strong>Include email addresses</strong>. One of the columns must contain each student\'s email address. You will need to write this column\'s name below.</li>
			</ul>

			<form enctype="multipart/form-data" action="'. $postpath .'&amp;step=3" method="post">
				<!-- MAX_FILE_SIZE must precede the file input field -->
				<input type="hidden" name="MAX_FILE_SIZE" value="'.kbgv_getMaxFileSize().'" />
				<input type="hidden" id="class" name="class" value="'. $coursename .'" />
				<p><input name="csv" type="file" id="csv" size="60" /></p>
				<p><input name="email" type="text" id="email" style="width:300px;" /><label for="email"> What is the name of the column that contains emails?</label></p>
				<p class="submit" style="width:420px;"><input type="submit" value="Continue &raquo;" /></p>
			</form>
		';
		return array($out, $alert );
	}

	function kbgv_writeMessagePage($args){
		extract($args);
		// check inputs
		if (''==$_FILES['csv']['tmp_name']){
			$alert .= '<p>File did not upload propertly. Please try again.</p>';
			$prob = true;
		}
		if (''==trim($_POST['email'])){
			$alert .= '<p>Please give the name of the column that holds student email addresses.</p>';
			$prob = true;
		}
		if (''==$_POST['class']){
			$alert .= '<p>You left a required field blank.</p>';
			$prob = true;
		}
		if ($prob){
			$args['alert'] = $alert;
			return kbgv_uploadCSVPage($args);
		}
		$_POST['class'] = kbgv_cleanup_coursename( $_POST['class'] );
		if ( !in_array( $_POST['class'], $courselist) ){
			$alert .= "<p>You're trying to modify a course that doesn't exist. Since the course existed as of the last step, one of two things has happened. Either another user has deleted the course you're trying to modify, or you're trying to hack something.</p>";
			$args['alert'] = $alert;
			return kbgv_selectCoursePage($args);
		}
		// okay, done checking
		// returns an array on successful parsing, a string otherwise.
		$arr = kbgv_parseGradesheet( $_FILES['csv']['tmp_name'], $_POST['email'] );
		if (!is_array($arr)){
			$alert .= "<p>$arr</p>";
			$args['alert'] = $alert;
			return kbgv_uploadCSVPage($args);
		} // ELSE it is an array.

		// check whether any warnings came back (e.g. about bad email addresses):
		$alert .= $arr['alert'];
		unset($arr['alert']);

		$cols = $arr['cols']; 		// array of column cleannames=>rawnames
		// so now $arr just holds all the grade info, with emails as array keys
		$allStudents = $arr;
		unset( $allStudents['cols'] ); // the rest have a valid email as key
		$allStudents = array_keys( $allStudents ); // emails only
		$opt = 'kbgvNEW_'.md5($_POST['class']);
		if (!kbgv_update_option( $opt.'_temp', $arr) ){
			$alert .= '<p>Unable to write CSV data to database. Please try again in a couple minutes.</p>';
			$args['alert'] = $alert;
			return kbgv_uploadCSVPage($args);
		}

		// alright, we saved all the grade info, now we need to check whether to generate any new passwords
		$passwords = get_option( 'kbgv_students' ); // array: keys are emails, passwords are values
		if (is_array($passwords)){
			$existingStudents = array_keys( $passwords );
			$newStudents = array_diff( $allStudents, $existingStudents );
		}else{
			$newStudents = $allStudents;
		}
		if (is_array($newStudents) && count($newStudents)>0){
			// generate new passwords:
			foreach( $newStudents as $newStudent )
				$passwords[ $newStudent ] = kbgv_generate_one_password();
			if (!kbgv_update_option( 'kbgv_students', $passwords )){
				$alert .= '<p>Unable to write student emails to database. Please try again later.</p>';
				$args['alert'] = $alert;
				return kbgv_uploadCSVPage($args);
			}
		}
		$alert .= '<p>CSV data imported successfully.</p>';
		// all done. Proceed with the next step: Composing the message
		$out .= '
			<h2>Step 3: Compose a Message for your Students</h2>
			<div>
				<p>What would you like students to see when they check their grades? Type in a message below, inserting the codes below (exactly as shown) where you would like particular grades to appear. For example, if your spreadsheet has a column called "midterm," and you want each student\'s midterm grade to show up in that student\'s message, then type <code>[midterm]</code> in your message. Your spreadsheet contains the following fields (note that they column titles have been sanitized; type them as shown below):</p>
				<blockquote style="padding:4px;background:#cdf;border:solid 1px #abc;"><code>
		';
		$out .= '';
		foreach($cols as $clean=>$col){
			$out .= "[<strong><em>$clean</em></strong>] &nbsp; ";
		}
		$out .= '
				</code></blockquote>
				<p>Additional tags that you may use:</p>
				<ul>
					<li><code>[AUTOTABLE]</code> generates a table showing all the information about the particular student.</li>
					<li><code>[MDY]</code> returns <code>'. date('n-j-Y') .'</code>.</li>
					<li><code>[DMY]</code> returns <code>'. date('j-n-Y') .'</code>.</li>
					<li><code>[DATE]</code> returns <code>'. date('F jS') .'</code>.</li>
				</ul>
				<form method="post" action="'. $postpath .'&amp;step=4">
		';

		$defaultMsg = get_option($opt);
		if (is_array($defaultMsg) && array_key_exists('msg',$defaultMsg))
			$defaultMsg = $defaultMsg['msg'];
		else
			$defaultMsg = "Dear student,\n\nAs of [DATE], these are the grades I have recorded for you:\n\n[AUTOTABLE]\n\nPlease notify me promptly if you see any mistakes. Thanks!";
		// if user's original message was plain text, we would have added <p> and <br /> tags to it. We don't want to display HTML to a user who doesn't understand it. If we
		// see only those two tags, we'll assume that the plugin put them there, not the user, and strip them out.
		$strippedMsg = str_replace( array('<p>','</p>','<br />'), '', $defaultMsg );
		if ( strip_tags($defaultMsg) == $strippedMsg )
			$defaultMsg = $strippedMsg;

		// detect whether existing message is plain text or html
		if ($defaultMsg != strip_tags( $defaultMsg ))
			$checked = 'checked="checked"'; // there was HTML in there, apparently

		$out .= '
				<textarea id="msg" name="msg" rows="10" cols="70">'.$defaultMsg.'</textarea>
				<table>
					<tr>
						<td><input type="radio" name="text_or_html" id="text_or_html0" value="text" checked="checked" /> </td>
						<td><label for="text_or_html0">My message is plain text. Convert it to HTML (recommended).</label></td>
					</tr>
					<tr>
						<td><input type="radio" name="text_or_html" id="text_or_html1" value="html" '.$checked.' /> </td>
						<td><label for="text_or_html1">My message already contains HTML markup (advanced).</label><br /><small>Only tags allowed in posts may be used.</small></td>
					</tr>
				</table>
				<input type="hidden" id="class" name="class" value="'. $_POST['class'] .'" />
				<p class="submit" style="width:420px;"><input type="submit" value="Continue &raquo;" /></p>
				</form>
			</div>
		';
		return array( $out, $alert );
	}

	function kbgv_errorCheckPage($args){
		extract($args);
		// as usual, start by validating inputs
		$_POST['class'] = kbgv_cleanup_coursename( $_POST['class'] );
		if ( !( $_POST['class'] && $_POST['msg'] && $_POST['text_or_html'] ) ){	// verify form vars
			$alert .= "<p>You left a required form field blank. Please go back and check again.</p>";
			return array( $alert, '<h2>Error</h2>' );
		}
		if ( !in_array( $_POST['class'], $courselist) ){
			$alert .= "<p>You're trying to modify a course that doesn't exist. Since the course existed as of the last step, one of two things has happened. Either another user has deleted the course you're trying to modify, or you're trying to hack something.</p>";
			$args['alert'] = $alert;
			return kbgv_selectCoursePage($args);
		}
		$opt = 'kbgvNEW_'.md5($_POST['class']);
		$arr = get_option( $opt . '_temp' );
		if (!is_array($arr)){
			$alert .= "<p>There is no grade information available for this course. Since there was as of the last step, it's possible that another user is also working on your grades right now.</p>";
			$args['alert'] = $alert;
			return kbgv_selectCoursePage($args);
		}
		// done validating. Clean up the message.
		$msg = kbgv_cleanupMsg( $_POST['msg'], $_POST['text_or_html'] );
		// one final check
		if (''==$msg){
			$alert .= "<p>You did not enter in a valid message. Please go back and check again.</p>";
			return array( $alert, '<h2>Error</h2>' );
		}
		$arr['msg'] = $msg;
		if (!kbgv_update_option( $opt, $arr )){
			$alert .= "<p>Error sending information to the database. Maybe reloading will help.</p>";
			return array( $alert, '<h2>Error</h2>' );
		}
		// okay, we're good, it would appear. Delete our temp option:
		// delete_option( $opt . '_temp' ); // no, don't delete it. Otherwise, if they go "back" to adjust the message, they get an error.

		$alert .= '<p>Message saved successfully.</p>';

		$out .= '
			<h2>All Done!</h2>
			<p>';

		$numPreview = 5;
		if (is_numeric($numPreview))
			$out .= 'The table below shows how your message will appear to the <strong>first '.$numPreview.' students</strong> in your spreadsheet.';
		else
			$out .= 'The table below shows how your message will appear to your students.';

		$out .= ' If you want to change your message, just hit your browser\'s "back" button.</p>
			<h3>How do students check their grades?</h3>
			<ul>
				<li>Create a page (not post) containing <code>'.KBGV_SLUG.'</code> on a line by itself. Students can check their grades, get their password, and do everything they need to do at this page.</li>
				<li>Consider adding the KB Gradebook widget to your sidebar to make life easier for your students.</li>
			</ul>
			<h3>How do I update these grades later?</h3>
			<ul>
				<li>Easy. Come back and upload a new CSV.</li>
			</ul>
		';
		$out .= kbgv_previewMessages($arr, $numPreview);
		return array($out,$alert);
	}


	///////////////////////////////////////////////
	// UTILITY FUNCTIONS
	///////////////////////////////////////////////

	// check how large of a CSV upload to allow
	function kbgv_getMaxFileSize(){
		$val = ini_get('upload_max_filesize');
		$val = trim($val);
		// check whether we've used shorthand, like "2M", instead of raw bytes:
		$last = strtolower($val{strlen($val)-1});
	    switch($last) {
	        case 'g':
	            $val *= 1024;
	        case 'm':
	            $val *= 1024;
	        case 'k':
	            $val *= 1024;
	    }
		return $val - 1000; // reserve 1K for other post vars
	}

	// turns the CSV into a useful array, or returns a string error msg
	function kbgv_parseGradesheet( $file, $emailCol='email' ){
		$handle = fopen( $file, "r");
		// load CSV into multidimensional array
		while ( ($data=fgetcsv($handle, KBGV_CSVCUTOFF))  !== false )
			$arr[] = $data;
		fclose($handle);

		// check:
		if (!is_array( $arr ) || count($arr)<=1 ) // need at least 2 count--one row for column names, one row for student data
			return 'Sorry, but your CSV file has too few columns.';

		// first row in CSV is assumed to be headings
		$cols = $arr[0];
		array_shift( $arr );

		// must have at least 2 columns. One column is student email addresses, others hold grades.
		if (count($cols) < 2)
			return 'Sorry, but your CSV file has too few columns.';

		// let's make a sanitized version of $cols and do some error checking to ensure that each column has a unique name
		$cleanCols = array(); // will have same keys as $cols
		foreach( $cols as $col ){
			$colsList .= ', ' . $col; // used in error msgs
			$cleanCol = kbgv_sanitizeColumn($col);
			// we don't allow duplicate column names in $cleanCols (except for columns without a name, which we drop later)
			if (''!=$cleanCol && in_array($cleanCol,$cleanCols)){ // naming conflict with our sanitized name
				$i = 2;
				while (in_array($cleanCol.'-'.$i,$cleanCols))
					$i++;
				$cleanCol = $cleanCol.'-'.$i;
			}
			$cleanCols[] = $cleanCol;
		}
		unset( $cleanCol );

		// verify that $emailCol is valid. We check it against $cols, which allows duplicates, so the first instance of $emailCol gets used.
		$emailCol = kbgv_sanitizeColumn($emailCol);
		if (''==$emailCol)
			return 'Sorry, but you didn\'t specify a valid "email" column. Please specify which column in your spreadsheet holds student email addresses.';
		$emailKey = array_search($emailCol, $cleanCols);
		if (false===$emailKey)
			return 'Sorry, but you didn\'t specify a valid "email" column. Please specify which column in your spreadsheet holds student email addresses. You wrote <strong>'.$emailCol.'</strong>. Your spreadsheet contains the following columns: <br />' . substr( $colsList, 1 ) ;

		// let's restructure $arr to make it easier to work with by making each student's email address the array key for that row and using $cleanCols for keys within a row
		$out = array();
		$i = 0;
		$bad = 0;
		foreach( $arr as $student ){
			if (!is_array($student)) // shouldn't happen
				continue;
			if (count($student)<2) // happens if there's a blank row in the middle of the csv
				continue;
			$email = strtolower( $student[$emailKey] ); // make them all lowercase
			if (!kbgv_validateEmail( $email )){
				$bad++;
				continue;
			}
			if (array_key_exists($email,$out)) // if email occurs more than once, use the first instance only
				continue;
			foreach( $student as $k=>$v ){
				if (''==$cleanCols[$k]) // we're in a column without a name; skip it
					continue;
				$out[ $email ][ $cleanCols[$k] ] = $v; // rename $arr's keys to make them easier to use later
			}
		}

		if ($bad>0){
			$bad = "<strong>$bad</strong> of the email addresses in your spreadsheet were invalid.";
			$out['alert'] .= "<p>$bad</p>";
		}else{
			$bad = '';
		}

		// check:
		if (count($out)<1)
			return 'Sorry, but it looks like there is no grade information in your CSV. '.$bad;

		// let's add the column names (both raw and sanitized) to $out
		foreach( $cleanCols as $colNum=>$cleanCol ){
			if (''==$cleanCol) // omit columns without a name
				continue;
			$out['cols'][$cleanCol] = $cols[$colNum];
		}

		return $out;
	}

	// used in parsing CSV
	function kbgv_sanitizeColumn($col){
		return str_replace(' ', '_',   strtolower(trim($col))  );
	}

	// clean up posted course name
	function kbgv_cleanup_coursename($name){
		$name = wp_filter_nohtml_kses( stripslashes($name) );
		$name = htmlspecialchars(  $name  , ENT_QUOTES);
		return $name;
	}

	// clean up the msg that the teacher wants to display to students
	function kbgv_cleanupMsg($msg,$html){
		$msg = stripslashes( trim($msg) );
		if ('html'==$html)
			return stripslashes( wp_filter_post_kses( $msg ) ); // not sure why wp_filter_post_kses does an addslashes() to the string...
		// else: Let's clean up the text and make it into html
		$msg = htmlspecialchars( $msg, ENT_QUOTES );

		// if there's an [AUTOTABLE] in there, put it on its own line
		$msg = str_replace( '[AUTOTABLE]', "\n\n[AUTOTABLE]\n\n", $msg );

		// no more than two newlines in a row:
		$msg = str_replace("\r",'',$msg);
		$msg = preg_replace( "~[\n]{3,}~", "\n\n", $msg );

		// two newlines are a paragraph, one is a break
		$msg = "<p>$msg</p>";
		$msg = str_replace( "\n\n", '</p><p>', $msg );
		$msg = str_replace( "\n", "<br />\n", $msg );
		$msg = str_replace( '</p><p>', "</p>\n\n<p>", $msg ); // has to come after the <br /> replacement

		// don't put <p> tags around an autotable
		$msg = str_replace( '<p>[AUTOTABLE]</p>', '[AUTOTABLE]', $msg );

		// all done at last. Note that the only html we've added is <p> and <br />, which makes it easy for us to strip those tags back out
		// when teacher comes back another time to update the grades
		return $msg;
	}

	// generates random password for an individual student. Default length is 6 chars.
	function kbgv_generate_one_password($length=6){
		$password = "";	// start with a blank password
		$possible = "23456789bcdfghjkmnpqrstvwxyz";	// define possible characters. Omit 0, o, 1, and l to avoid confusion. Omit vowels to avoid offensive words.
		if ($length>=$possible)
			$length = $possible-1;
		$i = 0;	// set up a counter
		while ($i < $length) {	// add random characters to $password until $length is reached
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);	// pick a random character from the possible ones
			$possible = str_replace($char, '', $possible); // don't reuse characters
			$password .= $char;
			$i++;
		}
		return $password;
	}

	// for teacher's preview of how message will look
	function kbgv_previewMessages($arr,$length=false){
		$msg = $arr['msg'];
		$cols = $arr['cols'];
		unset($arr['cols']);
		unset($arr['msg']);
		$out .= "<table class='widefat'>\n\t<thead><tr>\n\t<th scope='col'>Email</th>\n\t<th scole='col'>Message</th>\n</tr></thead><tbody id=\"the-list\">\n"; // define header row
		$alt = 'alternate';
		if ($length)
			$arr = array_slice( $arr, 0, $length, true );
		foreach( $arr as $email => $student ){
			$out .= "\n\t<tr class='$alt'>\n\t\t<th scope='row'>$email</th><td>";
			$out .= kbgv_generateGradeMsg( $student, $msg, $cols );
			$out .= '</td></tr>';
			$alt = ('alternate'==$alt) ? 'notAlternate' : 'alternate';
		}
		$out .= '</tbody></table>';
		return $out;
	}

	// for displaying message to student. $grades is a row from $arr, $msg is $arr['msg']
	function kbgv_generateGradeMsg($grades,$msg,$cols){
		if (!is_array($grades))
			return '<p>Sorry, there appears to be a problem with the way your instructor has recorded your grades. Please let your instructor know about this problem.</p>';
		if (''==$msg)
			return '<p>Sorry, but there has been an error. If this error persists for more than a few minutes, please contact let your instructor know about this problem.</p>';
		$autotable = '<table>';
		foreach( $grades as $col=>$val ){
			$msg = str_replace( '['.$col.']', $val, $msg );
			$autotable .= '<tr><th>'.$cols[$col]."</th><td>$val</td></tr>";
		}
		$autotable .= '</table>';
		$msg = str_replace( '[AUTOTABLE]', $autotable, $msg );
		$msg = str_replace( array('[MDY]','[DMY]','[DATE]'), array(date('n-j-Y'), date('j-n-Y'), date('F jS')), $msg );
		return "<div class='kbGradebook'>$msg</div>";
	}

	// update_option returns false if no update is required. This hack makes it return false only if there's a problem.
	function kbgv_update_option($option, $value){
		$oldvalue = get_option($option);
		if ( $oldvalue == $value ){
			return true;
		}else{
			$update = update_option($option, $value);
			return $update;
		}
		return false;
	}

	// does what it sounds like it does
	function kbgv_validateEmail($email) {
		if (''==$email)
			return false;

		// First, we check that there's one @ symbol, and that the lengths are right
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
			return false;
		}
		// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
			return false;
			}
		}
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
					return false;
				}
			}
		}
		return true;
	}

	///////////////////////////////////////////////
	// FRONT-END FILTERING FUNCTIONS (STUDENT VIEW)
	///////////////////////////////////////////////

	// Our filter. Displays grade viewer to students.
	function kbgv_page_filter($content){
		if (!is_page())
			return $content;
		$slug = KBGV_SLUG;
		if (false===strpos($content, $slug))
			return $content;

		// alright, we're in business:

		$_POST['kbcourse'] = kbgv_cleanup_coursename( $_POST['kbcourse'] );

		// make life easier for everybody:
		$_POST['kbemail'] = strtolower( $_POST['kbemail'] );
		$_POST['kbpwd'] = strtolower( $_POST['kbpwd'] );

		$students = get_option('kbgv_students');

		$postpath = get_permalink();
		if ( strpos( $postpath, '?' ) )		// figure out how to append GETs to the current permalink
			$postpath .= "&amp;";
		else
			$postpath .= "?";

		if (KBGV_WPUSERS)
			$_GET['kbgv'] = 'wpUsers';

		switch( $_GET['kbgv'] ){
			case 'wpUsers':		// students all have a WP username/login, so the other CASEs aren't necessary.
				if (!KBGV_WPUSERS)
					$out .= '<h1>DIE HACKER DIE DIE!!!</h1>'; // somebody's up to no good
				else
					$out .= kbgv_studentsAreWpUsers( $postpath, $students );
				break;
			case 'reqpassword': // student wants password
				$out .= kbgv_reqPwdForm( $postpath );
				break;
			case 'sendpassword': // send pwd to student
				$out .= kbgv_sendPwd( $postpath, $students );
				break;
			case 'display':
				$out .= kbgv_showGrades( $postpath, $students );
				break;
			default:
				$out .= kbgv_showGradesForm( $postpath );
				break;
		}
		// done
		if (false!==strpos( $content, "<p>$slug</p>" ))
			return str_replace( "<p>$slug</p>", $out, $content );
		else
			return str_replace( $slug, $out, $content );
	}

	// helpers for kbgv_page_filter() follow:

	// we're not using our randomly generated passwords; we're using WP's usernames and passwords
	function kbgv_studentsAreWpUsers( $postpath, $students ){
		$login = get_bloginfo('url') . '/wp-login.php';
		if (!is_user_logged_in())
			return '<p><strong>Oops!</strong> You need to <a href="'.$login.'">log in</a> before you can check your grades.</p>';
		global $userdata;
		get_currentuserinfo();
		$e = strtolower($userdata->user_email);
		if (!is_array($students) || !array_key_exists( $e, $students ))
			return '<p>Sorry, but there is no grade information associated with your email address ('.$e.').</p>';
		// get course list
		$courses = get_option('kbgv_courses');
		if (!is_array($courses))
			return '<p>You cannot check your grade; your instructor has not uploaded any grade information yet.</p>';
		// do we need to prompt to select a course?
		if (count($courses)>1){
			if (''==$_POST['kbcourse']){ // student needs to select course
				$form = '
					<p>Which course would you like to view grades for?</p>
					<form method="post" action="' .$postpath .'">
					<fieldset>
					<table id="form">
					<tr><td>Courses: </td><td><select name="kbcourse" id="kbcourse">
				';
				foreach( $courses as $course ){
					$form .= "<option>{$course}</option>\n";
				}
				$form .= '</select></td></tr>
					<tr><td></td><td><input type="submit" value="Show me my grade &raquo;" /></td></tr>
					</table>
					</fieldset>
					</form>
				';
				return $form;
			}
			if (!in_array($_POST['kbcourse'], $courses)){
				return '<p>Sorry, but the selected course does not exist!</p>';
			}
			$course = $_POST['kbcourse'];
		}else{
			$course = $courses[0];
		}
		// load grades
		$grades = get_option( 'kbgvNEW_' . md5( $course ) );
		if (!is_array($grades) || ''==$grades['msg'])
			return '<p>Sorry, but there is no grade information for that course yet.</p>';
		// check student's grades
		if (!is_array( $grades[ $userdata->user_email ] ))
			return '<p>Sorry, but there is no grade information in <strong>'.$course.'</strong> for your email address.</p>';
		// display student's grades (finally!)
		$msg = $grades['msg'];
		$cols = $grades['cols'];
		$grades = $grades[ $userdata->user_email ];
		return kbgv_generateGradeMsg( $grades, $msg, $cols );
	}

	// student wants to request password by email
	function kbgv_reqPwdForm( $postpath ){
		$out .= '
			<p>This site randomly generates a password for each student. If you have not received your password yet, or if you have forgotten it, use this form to have your password emailed to you.</p>
			<form method="post" action="' .$postpath .'kbgv=sendpassword">
			<fieldset>
			<legend>Request a password</legend>
			<table id="form">
			<tr><td>Email address: </td><td><input type="text" name="kbemail" id="kbemail" /></td></tr>
			<tr><td> </td><td><input type="submit" value="Send me my password &raquo;" /></td></tr>
			</table>
			</fieldset>
			</form>
		';
		return $out;
	}
	// send password to student by email
	function kbgv_sendPwd( $postpath, $students ){
		// validate
		if (''==$_POST['kbemail'] || !kbgv_validateEmail($_POST['kbemail']))
			return '<p>Sorry, but you did not enter a valid email address.</p>';
		// check whether email exists
		if ( !is_array($students) || !array_key_exists( $_POST['kbemail'], $students) )
			return '<p>Sorry, but that email address does not exist in the database, so I cannot send you your password. Make sure you have entered your email address exactly as it would appear on the roster.</p>';
		// don't send if it's a demo
		if (false!==strpos( $_POST['kbemail'], 'example.com' ))
			return "<p>If this weren't a demo, your password would have been emailed to you. But since this is a demo, I'll just tell you that the password is <code>{$students[$_POST['kbemail']]}</code>.</p>";
		// prepare mail
		$subject = '[' . get_bloginfo() . '] Password for checking grades';
		$message = 'To check your grades online for '.get_bloginfo().', use this password: '.$students[$_POST['kbemail']].'. Please keep this password someplace safe.';
		// send
		if (wp_mail($_POST['kbemail'], $subject, $message))
			return '<p>An email has been sent with the information you requested. It may take several minutes to arrive. <a href="' . $postpath . 'kbgv=login">Return to login form</a>.</p>';
		// send failed
		return '<p>I\'m sorry, but the system is having a problem (the email function failed). Please wait a little while and try again. If this problem persists, please contact the site administrator.</p>';
	}
	// display grades to student
	function kbgv_showGrades( $postpath, $students ){
		// validate email/pwd
		if (!is_array( $students ) || ''==$_POST['kbemail'] || ''==$_POST['kbpwd'] || $students[ $_POST['kbemail'] ]!=$_POST['kbpwd'])
			return '<p>Sorry, but that email-password pair does not exist! Passwords are generated by this site; you can <a href="' . $postpath . 'kbgv=reqpassword">have your password sent to you by email</a> if you do not know it.</p><p>It is also possible that your instructor has not yet uploaded your grade information.</p>';
		// don't want student's grades to be show up if somebody hits "back" a few times and then reload. A VERY simple protection from that:
		$timediff = time() - $_GET['kbgv_t'];
		if ($timediff<0 || $timediff>600) // you get 10 minutes
			return '<p class="notice"><strong>Sorry, but the form has expired. Please try again.</strong></p>' . kbgv_showGradesForm( $postpath );
		// validate course
		$course = $_POST['kbcourse'];
		$courses = get_option('kbgv_courses');
		if (!in_array($course, $courses))
			return '<p>Sorry, but the selected course does not exist!</p>';
		// load grades
		$grades = get_option( 'kbgvNEW_' . md5( $course ) );
		if (!is_array($grades) || ''==$grades['msg'])
			return '<p>Sorry, but there is no grade information for that course yet.</p>';
		// check student's grades
		if (!is_array( $grades[ $_POST['kbemail'] ] ))
			return '<p>Sorry, but there is no grade information in <strong>'.$course.'</strong> for your email address. If this is unexpected, make sure you selected the correct course.</p>';
		// display student's grades (finally!)
		$msg = $grades['msg'];
		$cols = $grades['cols'];
		$grades = $grades[ $_POST['kbemail'] ];
		return kbgv_generateGradeMsg( $grades, $msg, $cols );
	}
	// student wants to submit pwd and email so he can see grades
	function kbgv_showGradesForm( $postpath ){
		$courses = get_option('kbgv_courses');
		if (!is_array($courses))
			return '<p>You cannot check your grade; your instructor has not uploaded any grade information yet.</p>';
		$out .= '
			<p>Use this form to check your grades.</p>
			<form method="post" action="' .$postpath .'kbgv=display&amp;kbgv_t='. time() .'">
			<fieldset>
			<table id="form">
			<tr><td>Email address: </td><td><input type="text" name="kbemail" id="kbemail" /></td></tr>
			<tr><td>Password: </td><td><input type="password" name="kbpwd" id="kbpwd" /></td></tr>
			<tr><td>Course: </td><td><select name="kbcourse" id="kbcourse">
		';
		foreach( $courses as $course ){
			$out .= "<option>{$course}</option>\n";
		}
		$out .= '</select></td></tr>
			<tr><td></td><td><input type="submit" value="Show me my grade &raquo;" /></td></tr>
			</table>
			</fieldset>
			</form>
			<p>Passwords were randomly generated by this site. If you haven\'t already received your password, or if you have forgotten it, use the <a href="' .$postpath .'kbgv=reqpassword">password request form</a> to have your password emailed to you.</p>
		';
		return $out;
	}

	///////////////////////////////////////////////
	// HOOKS
	///////////////////////////////////////////////
	function kbgv_admin_menu(){
		add_submenu_page('edit.php', __('Gradebook', 'kbgb'), __('KB Gradebook', 'kbgb'), 10, 'kb-gradebook.php', 'kbgv_admin_page');
	}
	add_action('admin_menu', 'kbgv_admin_menu');


}




///////////////////////////////////////////////
// WIDGET
///////////////////////////////////////////////
function kbgv_widget_init() {

	// prevent fatal errors
	if ( !function_exists('register_sidebar_widget') )
		return;

	function widget_kbgradeviewer($args) {

		extract($args);

		$options = get_option('widget_kbgradeviewer');
		$title = $options['title'];
		$action = $options['action'];

		$courses = get_option('kbgv_courses');

		if ( !is_array( $courses ) )	// no sense in displaying the widget if there isn't course data
			return;

		if ( '' == $action )	// require this.
			return;

		echo $before_widget . $before_title . $title . $after_title;
		echo '
				<form id="kbgradeviewer" action="'.$action.'?kbgv=display&amp;kbgv_t='.time().'" method="post">
				<table>
				<tr>
					<td>Email </td>
					<td><input type="text" name="kbemail" style="width:10em;" /></td>
				</tr>
				<tr>
					<td>Password </td>
					<td><input type="password" name="kbpwd" style="width:10em;" /></td>
				</tr>
				<tr>
					<td>Course </td>
					<td><select name="kbcourse" id="kbcourse" style="width:10em;">';
		foreach( $courses as $course){
			echo "<option>{$course}</option>";
		}
		echo '			</select></td>
				</tr>
				<tr>
					<td></td>
					<td><input value="View Grades &raquo;" type="submit" /></td>
				</tr>
				</table>
				</form>
				<small><a href="'.$action.'?kbgv=reqpassword">What is my password?</a></small>
			';
		echo $after_widget;
	}


	function widget_kbgradeviewer_control() {

		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_kbgradeviewer');
		if ( !is_array($options) )
			$options = array('title'=>__('Check Your Grade', 'widgets'), 'action'=>__('Complete URL to a WP page using grades template', 'widgets'));
		if ( $_POST['kbgradeviewer-submit'] ) {

			// Remember to sanitize and format use input appropriately.
			$options['title'] = strip_tags(stripslashes($_POST['kbgradeviewer-title']));
			$options['action'] = strip_tags(stripslashes($_POST['kbgradeviewer-action']));
			update_option('widget_kbgradeviewer', $options);
		}

		// Be sure you format your options to be valid HTML attributes.
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$action = htmlspecialchars($options['action'], ENT_QUOTES);

		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		echo '<p><label for="kbgradeviewer-title">' . __('Title:') . ' </label></p><p><input style="width: 500px;" id="kbgradeviewer-title" name="kbgradeviewer-title" type="text" value="'.$title.'" /></p>';
		echo '<p><label for="kbgradeviewer-action">URL of grade viewing page: </label></p><p><input style="width: 500px;" id="kbgradeviewer-action" name="kbgradeviewer-action" type="text" value="'.$action.'" /></p>';
		echo '<p><small>The grade viewing page can be any page that contains <code>'.KBGV_SLUG.'</code> in the body. The widget will not display unless a URL is provided.</small></p>';
		echo '<input type="hidden" id="kbgradeviewer-submit" name="kbgradeviewer-submit" value="1" />';
	}

	register_sidebar_widget(array('KB Gradebook', 'widgets'), 'widget_kbgradeviewer');

	register_widget_control(array('KB Gradebook', 'widgets'), 'widget_kbgradeviewer_control', 550, 200);
}
// end of widget stuff



///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////

kbgv_plugin_init();
add_filter('the_content','kbgv_page_filter');
add_action('widgets_init', 'kbgv_widget_init');

?>
