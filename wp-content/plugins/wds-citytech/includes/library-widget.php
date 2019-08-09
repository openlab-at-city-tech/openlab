<?php

/**
 * Functionality related to the Library widgets on sites and course profiles.
 */

add_action( 'widgets_init', 'openlab_register_library_tools_widget' );

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
		$setting    = 'course' === $group_type ? 'yes' : 'no';
	}

	return 'yes' === $setting;
}

/**
 * Renders the Library Settings section of the group admin.
 */
function openlab_group_library_settings() {
	$group_type_label = openlab_get_group_type_label(
		array(
			'case' => 'upper',
		)
	);

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
		<h2 class="sidebar-header">Library</h2>

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
 * Registers the Library Tools widget for WP sites.
 */
function openlab_register_library_tools_widget() {
	register_widget( 'OpenLab_Library_Tools_Widget' );
}

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
		<p><a class="bold" href="https://library.citytech.cuny.edu">Ursula C. Schwerin Library</a></p>
		<p>New York City College of Technology, C.U.N.Y</p>
		<p>300 Jay Street, Library&nbsp;Building - 4th Floor</p>

		<ul>
			<li><a href="https://library.citytech.cuny.edu/help/ask/index.php">Ask Us</a></li>
		</ul>
	</div>
	<?php
}

/**
 * Library Tools widget class.
 */
class OpenLab_Library_Tools_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'openlab-library-tools-widget',
			'Library Tools',
			array(
				'class' => 'openlab-library-tools-widget',
			)
		);
	}

	public function parse_settings( $settings ) {
		$merged = array_merge(
			array(
				'find_library_materials' => true,
				'library_information'    => true,
			),
			$settings
		);

		// boolval() available only on PHP 5.5+
		foreach ( $merged as &$m ) {
			$m = (bool) $m;
		}

		return $merged;
	}

	public function widget( $args, $instance ) {
		$settings = $this->parse_settings( $instance );

		?>

		<?php if ( $settings['find_library_materials'] ) : ?>
			<?php /* Divs with ids help with CSS specificity and theme overrides */ ?>
			<div id="openlab-library-find-widget-content">
				<?php echo str_replace( 'id="', 'id="find-', $args['before_widget'] ); ?>
				<?php echo $args['before_title']; ?>Find Library Materials<?php echo $args['after_title']; ?>

				<?php openlab_library_search_form(); ?>

				<?php echo $args['after_widget']; ?>
			</div>
		<?php endif; ?>

		<?php if ( $settings['library_information'] ) : ?>
			<div id="openlab-library-information-widget-content">
				<?php echo str_replace( 'id="', 'id="information-', $args['before_widget'] ); ?>
				<?php echo $args['before_title']; ?>Library Information<?php echo $args['after_title']; ?>

				<?php openlab_library_information(); ?>

				<?php echo $args['after_widget']; ?>
			</div>
		<?php endif; ?>

		<style type="text/css">
			.widget_openlab-library-tools-widget input[type="text"],
			.widget_openlab-library-tools-widget select {
				margin-bottom: .5rem;
			}

			#openlab-library-information-widget-content ul {
				list-style-type: disc;
				margin-top: .5rem;
				padding-left: 20px;
			}

			.library-search-advanced-link {
				font-size: .9rem;
				white-space: nowrap;
			}

			.openlab-library-information p {
				margin-bottom: 0;
			}
		</style>

		<?php

	}

	public function form( $instance ) {
		$settings = $this->parse_settings( $instance );

		?>

		<p>
			<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'find_library_materials' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'find_library_materials' ) ); ?>" value="1" <?php checked( $settings['find_library_materials'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'find_library_materials' ) ); ?>">Find Library Materials</label>
		</p>

		<p>
			<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'library_information' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'library_information' ) ); ?>" value="1" <?php checked( $settings['library_information'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'library_information' ) ); ?>">Library Information</label>
		</p>

		<?php wp_nonce_field( 'openlab_library_widget', 'openlab-library-widget-nonce', false ); ?>

		<?php
	}

	public function update( $new_instance, $old_instance ) {
		if ( empty( $_POST['openlab-library-widget-nonce'] ) ) {
			return $old_instance;
		}

		if ( ! wp_verify_nonce( $_POST['openlab-library-widget-nonce'], 'openlab_library_widget' ) ) {
			return $old_instance;
		}

		$passed = array(
			'find_library_materials' => ! empty( $new_instance['find_library_materials'] ),
			'library_information'    => ! empty( $new_instance['library_information'] ),
		);

		return $this->parse_settings( $passed );
	}
}
