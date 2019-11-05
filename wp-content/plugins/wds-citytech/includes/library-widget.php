<?php

/**
 * Functionality related to the Library widgets on sites and course profiles.
 */

add_action( 'widgets_init', 'openlab_register_library_widgets' );

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
function openlab_register_library_widgets() {
	register_widget( 'OpenLab_Library_Tools_Widget' );
	register_widget( 'OpenLab_Library_Subject_Guides_Widget' );
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

/**
 * Gets a list of Library Subject Guides.
 *
 * See http://libguides.citytech.cuny.edu/?b=t for the canonical list.
 */
function openlab_get_library_subject_guides() {
	return [
		// Course Guide
		'com-1330'                       => [
			'name' => 'COM 1330',
			'url'  => 'http://libguides.citytech.cuny.edu/Speech',
			'type' => 'course',
		],

		'construction-management'        => [
			'name' => 'Construction Management and Civil Engineering Technology',
			'url'  => 'http://libguides.citytech.cuny.edu/c.php?g=881500',
			'type' => 'course',
		],

		'cst-1101'                       => [
			'name' => 'CST 1101',
			'url'  => 'http://libguides.citytech.cuny.edu/cst1101',
			'type' => 'course',
		],

		'english-1101'                   => [
			'name' => 'English1101',
			'url'  => 'http://libguides.citytech.cuny.edu/eng1101',
			'type' => 'course',
		],

		'hmgt-1101'                      => [
			'name' => 'HMGT1101 Industry Research',
			'url'  => 'http://libguides.citytech.cuny.edu/hmgt1101',
			'type' => 'course',
		],

		// General Purpose.
		'arturss'                        => [
			'url'  => 'https://libguides.citytech.cuny.edu/advancedResearch',
			'name' => 'Advanced Research Techniques for Undergraduate Research Student Scholars',
			'type' => 'general',
		],

		'boost'                          => [
			'url'  => 'https://libguides.citytech.cuny.edu/boost',
			'name' => 'Boost Your Scholarly Profile!',
			'type' => 'general',
		],

		'citations'                      => [
			'url'  => 'https://libguides.citytech.cuny.edu/citations',
			'name' => 'Citation and Formatting Guides',
			'type' => 'general',
		],

		'cunyaw'                         => [
			'url'  => 'https://libguides.citytech.cuny.edu/cunyaw',
			'name' => 'CUNY Academic Works',
			'type' => 'general',
		],

		'esol'                           => [
			'url'  => 'https://libguides.citytech.cuny.edu/c.php?g=396279',
			'name' => 'ESOL & Applied Linguistics',
			'type' => 'general',
		],

		'fair-use'                       => [
			'url'  => 'https://libguides.citytech.cuny.edu/c.php?g=642462',
			'name' => 'Fair Use and Copyright',
			'type' => 'general',
		],

		'grad-school'                    => [
			'url'  => 'https://libguides.citytech.cuny.edu/Gradschool',
			'name' => 'Grad School Resources',
			'type' => 'general',
		],

		'guide-to-research'              => [
			'url'  => 'https://libguides.citytech.cuny.edu/intro',
			'name' => 'Guide to Research',
			'type' => 'general',
		],

		'open-access'                    => [
			'url'  => 'https://libguides.citytech.cuny.edu/openaccess',
			'name' => 'Open Access',
			'type' => 'general',
		],

		'oer'                            => [
			'url'  => 'https://libguides.citytech.cuny.edu/OER',
			'name' => 'Open Educational Resources (OER)',
			'type' => 'general',
		],

		'pacifica'                       => [
			'url'  => 'https://libguides.citytech.cuny.edu/pacifica',
			'name' => 'Pacifica Radio Archives',
			'type' => 'general',
		],

		'places'                         => [
			'url'  => 'https://libguides.citytech.cuny.edu/places',
			'name' => 'Place Based Research',
			'type' => 'general',
		],

		'prom'                           => [
			'url'  => 'https://libguides.citytech.cuny.edu/PROM',
			'name' => 'PROM Outreach Toolkit',
			'type' => 'general',
		],

		'pubqual'                        => [
			'url'  => 'https://libguides.citytech.cuny.edu/pubqual',
			'name' => 'Publication Quality, Evaluating Publishers, and Bibliometrics',
			'type' => 'general',
		],

		'sounds'                         => [
			'url'  => 'https://libguides.citytech.cuny.edu/sounds',
			'name' => 'Recorded Sounds for the Classroom',
			'type' => 'general',
		],

		'mcfp'                           => [
			'url'  => 'https://libguides.citytech.cuny.edu/mcfp',
			'name' => 'Resources for Academic Publishing',
			'type' => 'general',
		],

		'undocumented'                   => [
			'url'  => 'https://libguides.citytech.cuny.edu/undocumented',
			'name' => 'Resources for Undocumented Students',
			'type' => 'general',
		],

		'scholarly-communications'       => [
			'url'  => 'https://libguides.citytech.cuny.edu/scholarlycommunications',
			'name' => 'Scholarly Communications',
			'type' => 'general',
		],

		'stem'                           => [
			'url'  => 'https://libguides.citytech.cuny.edu/stem',
			'name' => 'STEM Study of Teaching and Learning Journals',
			'type' => 'general',
		],

		'student-scholarship'            => [
			'url'  => 'https://libguides.citytech.cuny.edu/student-scholarship',
			'name' => 'Undergraduate Research Student Posters: How to Submit to Academic Works',
			'type' => 'general',
		],

		'finding-info'                   => [
			'url'  => 'https://libguides.citytech.cuny.edu/findingInfo',
			'name' => 'Using Different Kinds of Information',
			'type' => 'general',
		],

		// Subject Guide.
		'african-american-studies'       => [
			'url'  => 'https://libguides.citytech.cuny.edu/african_american_studies',
			'name' => 'African American Studies',
			'type' => 'subject',
		],

		'archtech'                       => [
			'url'  => 'https://libguides.citytech.cuny.edu/archtech',
			'name' => 'Architectural Technology',
			'type' => 'subject',
		],

		'arthist'                        => [
			'url'  => 'https://libguides.citytech.cuny.edu/arthist',
			'name' => 'Art History',
			'type' => 'subject',
		],

		'biology'                        => [
			'url'  => 'https://libguides.citytech.cuny.edu/biology',
			'name' => 'Biology',
			'type' => 'subject',
		],

		'fashion'                        => [
			'url'  => 'https://libguides.citytech.cuny.edu/c.php?g=847871',
			'name' => 'Business & Technology of Fashion',
			'type' => 'subject',
		],

		'chemistry'                      => [
			'url'  => 'https://libguides.citytech.cuny.edu/chemistry',
			'name' => 'Chemistry',
			'type' => 'subject',
		],

		'comd'                           => [
			'url'  => 'https://libguides.citytech.cuny.edu/communication_design',
			'name' => 'Communication Design',
			'type' => 'subject',
		],

		'communications'                 => [
			'url'  => 'https://libguides.citytech.cuny.edu/c.php?g=846670',
			'name' => 'Communications',
			'type' => 'subject',
		],

		'computer-tech'                  => [
			'url'  => 'https://libguides.citytech.cuny.edu/computer_tech',
			'name' => 'Computer Engineering & Systems Technology',
			'type' => 'subject',
		],

		'dental-hygiene'                 => [
			'url'  => 'https://libguides.citytech.cuny.edu/dental_hygiene',
			'name' => 'Dental Hygiene',
			'type' => 'subject',
		],

		'economics'                      => [
			'url'  => 'https://libguides.citytech.cuny.edu/economics',
			'name' => 'Economics',
			'type' => 'subject',
		],

		'engineer'                       => [
			'url'  => 'https://libguides.citytech.cuny.edu/Engineer',
			'name' => 'Engineering',
			'type' => 'subject',
		],

		'english'                        => [
			'url'  => 'https://libguides.citytech.cuny.edu/eng',
			'name' => 'English',
			'type' => 'subject',
		],

		'entertainment-tech'             => [
			'url'  => 'https://libguides.citytech.cuny.edu/entertainment_tech',
			'name' => 'Entertainment Technology',
			'type' => 'subject',
		],

		'health-services-administration' => [
			'url'  => 'https://libguides.citytech.cuny.edu/healthservicesadmin',
			'name' => 'Health Services Administration',
			'type' => 'subject',
		],

		'hospitality'                    => [
			'url'  => 'https://libguides.citytech.cuny.edu/hospitality',
			'name' => 'Hospitality Management',
			'type' => 'subject',
		],

		'humanservices'                  => [
			'url'  => 'https://libguides.citytech.cuny.edu/humanservices',
			'name' => 'Human Services',
			'type' => 'subject',
		],

		'legal-studies'                  => [
			'url'  => 'https://libguides.citytech.cuny.edu/legalstudies',
			'name' => 'Law and Paralegal Studies',
			'type' => 'subject',
		],

		'mathematics'                    => [
			'url'  => 'https://libguides.citytech.cuny.edu/math',
			'name' => 'Mathematics',
			'type' => 'subject',
		],

		'nursing'                        => [
			'url'  => 'https://libguides.citytech.cuny.edu/nursing',
			'name' => 'Nursing',
			'type' => 'subject',
		],

		'philosophy'                     => [
			'url'  => 'https://libguides.citytech.cuny.edu/philosophy',
			'name' => 'Philosophy',
			'type' => 'subject',
		],

		'physics'                        => [
			'url'  => 'https://libguides.citytech.cuny.edu/physics',
			'name' => 'Physics',
			'type' => 'subject',
		],

		'polisci'                        => [
			'url'  => 'https://libguides.citytech.cuny.edu/polisci',
			'name' => 'Political Science',
			'type' => 'subject',
		],

		'radiotech'                      => [
			'url'  => 'https://libguides.citytech.cuny.edu/radiotech',
			'name' => 'Radiological Technology',
			'type' => 'subject',
		],

		'religion'                       => [
			'url'  => 'https://libguides.citytech.cuny.edu/religion',
			'name' => 'Religion',
			'type' => 'subject',
		],

		'dentistry'                      => [
			'url'  => 'https://libguides.citytech.cuny.edu/dentistry',
			'name' => 'Restorative Dentistry',
			'type' => 'subject',
		],

		'sociology'                      => [
			'url'  => 'https://libguides.citytech.cuny.edu/sociology',
			'name' => 'Sociology',
			'type' => 'subject',
		],

		'vision'                         => [
			'url'  => 'https://libguides.citytech.cuny.edu/vision',
			'name' => 'Vision Care Technology',
			'type' => 'subject',
		],

		'authors-rights'                 => [
			'url'  => 'https://libguides.citytech.cuny.edu/authorsRights',
			'name' => 'Author Rights',
			'type' => 'topic',
		],

		'market'                         => [
			'url'  => 'https://libguides.citytech.cuny.edu/market',
			'name' => 'Finding Consumer/Market and Company/Industry Information',
			'type' => 'topic',
		],
	];
}

/**
 * Library Subject Guides widget class.
 */
class OpenLab_Library_Subject_Guides_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'openlab-library-subject-guides-widget',
			'Library Subject Guides',
			array(
				'class'     => 'openlab-library-subject-guides-widget',
				'classname' => 'openlab-library-subject-guides-widget',
			)
		);
	}

	public function widget( $args, $instance ) {
		$checked = isset( $instance['selected_guides'] ) ? $instance['selected_guides'] : [];
		$guides  = openlab_get_library_subject_guides();

		$selected_guides = array_map(
			function( $guide ) use ( $guides ) {
				if ( ! isset( $guides[ $guide ] ) ) {
					return;
				}

				return sprintf(
					'<a href="%s">%s</a>',
					esc_attr( $guides[ $guide ]['url'] ),
					esc_html( $guides[ $guide ]['name'] )
				);
			},
			$checked
		);

		?>

		<?php /* Divs with ids help with CSS specificity and theme overrides */ ?>
		<div class="openlab-library-tools-widget" id="<?php echo esc_attr( $this->get_field_id( '' ) ); ?>">
			<?php echo $args['before_widget']; ?>
			<?php echo $args['before_title']; ?><?php echo count( $selected_guides ) > 1 ? 'Library Guides' : 'Library Guide'; ?><?php echo $args['after_title']; ?>

			<ul>
				<?php foreach ( $selected_guides as $selected_guide ) : ?>
					<li><?php echo $selected_guide; ?></li>
				<?php endforeach; ?>
			</ul>
			<?php echo $args['after_widget']; ?>
		</div>

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
		wp_enqueue_script( 'openlab-library-subject-guides-widget', plugins_url() . '/wds-citytech/assets/js/library-subject-guides-widget.js', [ 'jquery-ui-accordion' ] );
		wp_enqueue_style( 'openlab-library-subject-guides-widget', plugins_url() . '/wds-citytech/assets/css/library-subject-guides-widget.css' );

		$checked = isset( $instance['selected_guides'] ) ? $instance['selected_guides'] : [];

		$guides_by_type = [
			'course'  => [],
			'general' => [],
			'subject' => [],
			'topic'   => [],
		];

		foreach ( openlab_get_library_subject_guides() as $slug => $guide ) {
			$guide_type = $guide['type'];

			$guides_by_type[ $guide_type ][ $slug ] = $guide;
		}

		$types = [
			'course'  => 'Course Guide',
			'general' => 'General Purpose',
			'subject' => 'Subject Guide',
			'topic'   => 'Topic Guide',
		];

		?>

		<p>Select one or more Library Subject Guides.</p>

		<div class="library-subject-guide-selectors">
			<?php foreach ( $types as $type => $type_name ) : ?>
				<h4><?php echo esc_html( $type_name ); ?></h4>
				<div>
					<?php foreach ( $guides_by_type[ $type ] as $guide_slug => $guide ) : ?>
						<?php $guide_id = $this->get_field_id( $guide_slug ); ?>
						<input class="checkbox" type="checkbox" name="library-subject-guides[]" value="<?php echo esc_attr( $guide_slug ); ?>" id="<?php echo esc_attr( $guide_id ); ?>" <?php checked( in_array( $guide_slug, $checked, true ) ); ?>> <label for="<?php echo esc_attr( $guide_id ); ?>"><?php echo esc_html( $guide['name'] ); ?></label>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php wp_nonce_field( 'openlab_library_subject_guides', 'openlab-library-subject-guides-nonce', false ); ?>

		<?php
	}

	public function update( $new_instance, $old_instance ) {
		if ( empty( $_POST['openlab-library-subject-guides-nonce'] ) ) {
			return $old_instance;
		}

		if ( ! wp_verify_nonce( $_POST['openlab-library-subject-guides-nonce'], 'openlab_library_subject_guides' ) ) {
			return $old_instance;
		}

		if ( isset( $_POST['library-subject-guides'] ) ) {
			$new_instance['selected_guides'] = wp_unslash( $_POST['library-subject-guides'] );
		}

		return $new_instance;
	}
}
