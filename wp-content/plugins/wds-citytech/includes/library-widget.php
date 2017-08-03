<?php

/**
 * Functionality related to the Library widgets on sites and course profiles.
 */

add_action( 'openlab_before_group_privacy_settings', 'openlab_group_library_settings' );

/**
 * Checks whether a group has the Library Tools feature enabled on the group profile.
 *
 * Defaults to true for Courses, otherwise defaults to false.
 *
 * @param int $group_id ID of the group.
 * @return bool
 */
function openlab_library_tools_are_enabled_for_group( $group_id ) {
	$setting = groups_get_groupmeta( $group_id, 'library_tools_enabled', true );

	// Courses default to 'yes'.
	if ( ! $setting ) {
		$group_type = openlab_get_group_type( $group_id );
		$setting = 'course' === $group_type ? 'yes' : 'no';
	}

	return 'yes' === $setting;
}

/**
 * Renders the Library Settings section of the group admin.
 */
function openlab_group_library_settings() {
	$group_type_label = openlab_get_group_type_label( array(
		'case' => 'upper',
	) );

	$setting = openlab_library_tools_are_enabled_for_group( bp_get_current_group_id() );

	?>
	<div class="panel panel-default">
		<div class="panel-heading">Library Settings</div>

		<div class="panel-body">
			<p>These settings enable or disable the library tools on your <?php echo esc_html( $group_type_label ); ?> profile.</p>

			<div class="checkbox">
				<label><input type="checkbox" name="group-show-library-tools" id="group-show-library-tools" value="1" <?php checked( $setting ); ?> /> Enable library tools</label>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Adds the library tools display to group sidebars.
 */
function openlab_group_library_tools_display() {
	if ( ! openlab_library_tools_are_enabled_for_group( bp_get_current_group_id() ) ) {
		return;
	}

	?>

	<div id="openlab-library-tools-sidebar-widget" class="sidebar-widget openlab-library-tools">
		<h2 class="sidebar-header"><a href="https://library.citytech.cuny.edu">Library <i class="fa fa-external-link-square"></i></a></h2>

		<div class="sidebar-block">
			<div class="sidebar-block-content">
				<h3 class="sidebar-block-header">Find Library Materials</h3>
				<?php openlab_library_search_form(); ?>
			</div>
		</div>

		<div class="sidebar-block">
			<div class="sidebar-block-content">
				<h3 class="sidebar-block-header">Library Information</h3>
				<?php openlab_library_information(); ?>
			</div>
		</div>
	</div>

	<?php
}
add_action( 'bp_group_options_nav', 'openlab_group_library_tools_display', 80 );

/**
 * Outputs the markup for the Library Search box.
 */
function openlab_library_search_form() {
	?>

<form action="https://library.citytech.cuny.edu/oneSearch.php" enctype="application/x-www-form-urlencoded; charset=utf-8" method="post" name="searchForm" role="search">

<input name="institution" type="hidden" value="NY" />
<input name="vid" type="hidden" value="ny" />
<input name="group" type="hidden" value="GUEST" />
<input name="onCampus" type="hidden" value="true" />
<input name="search_scope" type="hidden" value="everything" />
<input id="primoQuery" name="query" type="hidden" />
<input label= "search query" id="primoQueryTemp" class="focus form-control" name="queryTemp" type="text" placeholder="Find books, media, and more" aria-label="Input search query here"/>

<select name="selectStyle" class="form-control" aria-label="Search by Type">
<option label="Everything">Everything</option>
<option>Articles</option>
<option label="Print and eBooks">Books (Print + eBooks)</option>
<option label="Print Books">Books (Print)</option>
<option label="eBooks">eBooks</option>
<option label="Video, Audio and More">Media</option>
</select>

<div class="library-search-actions">
	<input alt="Search" class="btn btn-primary" id="submit" class="library-search-submit" title="Search books, articles &amp; more" type="submit" value="Search" />

	<a class="library-search-advanced-link" href="http://onesearch.cuny.edu/primo_library/libweb/action/search.do?vid=ny&mode=Advanced&ct=AdvancedSearch">Advanced Search</a>
</div>

</form>

	<?php
}

/**
 * Outputs the markup for the Library Information box.
 */
function openlab_library_information() {
	?>
	<div class="openlab-library-information">
		<a class="bold" href="https://library.citytech.cuny.edu">Ursula C. Schwerin Library</a><br />
		New York City College of Technology, C.U.N.Y<br />
		300 Jay Street, Atrium - 4th Floor<br />

		<ul>
			<li><a href="https://library.citytech.cuny.edu/help/ask/index.php">Ask Us</a></li>
			<li><a href="https://library.citytech.cuny.edu/">library.citytech.cuny.edu</a></li>
		</ul>
	</div>
	<?php
}
