<?php

add_filter(
	'webwork_server_site_id',
	function() {
		return get_current_blog_id();
	}
);

add_filter(
	'webwork_client_site_base',
	function() {
		return trailingslashit( get_option( 'home' ) );

		$base = get_blog_option( 1, 'home' );
		if ( 'CT staging' === ENV_TYPE ) {
			return trailingslashit( $base ) . 'webwork-playground';
		} else {
			return trailingslashit( $base ) . 'ol-webwork';
		}
	}
);

add_filter(
	'webwork_server_site_base',
	function() {
		return trailingslashit( get_option( 'home' ) );

		$base = get_blog_option( 1, 'home' );
		if ( 'CT staging' === ENV_TYPE ) {
			return trailingslashit( $base );
		} else {
			return trailingslashit( $base ) . 'ol-webwork';
		}
	}
);

add_filter(
	'webwork_section_instructor_map',
	function( $map ) {
		return array(
			'MAT1275-F17-Antoine'          => 'wantoine@citytech.cuny.edu',
			'MAT1275-F17-Ferguson'         => 'rferguson@citytech.cuny.edu',
			'MAT1275-F17-Mujica'           => 'pmujica@citytech.cuny.edu',
			'MAT1275-F17-Poirier'          => 'kpoirier@citytech.cuny.edu',
			'MAT1275-F17-Saha'             => 'srsaha@citytech.cuny.edu',
			'MAT1275-F17-Sirelson'         => 'vsirelson@citytech.cuny.edu',
			'MAT1275EN-F17-Carley'         => 'hcarley@citytech.cuny.edu',
			'MAT1275EN-F17-Ganguli'        => 'sganguli@citytech.cuny.edu',
			'MAT1275EN-F17-Mingla'         => 'lmingla@citytech.cuny.edu',
			'MAT1275EN-F17-Parker'         => 'kparker@citytech.cuny.edu',
			'MAT1275-F17-Zeng-2pm'         => 'szeng@citytech.cuny.edu',
			'MAT1275-F17-Zeng-4pm'         => 'szeng@citytech.cuny.edu',
			'MAT1275-F17-Batyr-8am'        => 'obatyr@citytech.cuny.edu',
			'MAT1275-F17-Batyr-10am'       => 'obatyr@citytech.cuny.edu',

			'MAT1275-S18-Antoine'          => 'wantoine@citytech.cuny.edu',
			'MAT1275-S18-Ayoub'            => 'tayoub@citytech.cuny.edu',
			'MAT1275-S18-Chan'             => 'cchan@citytech.cuny.edu',
			'MAT1275-S18-Duvvuri'          => 'vduvvuri@citytech.cuny.edu',
			'MAT1275-S18-Ferguson'         => 'rferguson@citytech.cuny.edu',
			'MAT1275-S18-Ganguli'          => 'sganguli@citytech.cuny.edu',
			'MAT1275-S18-Ghezzi'           => 'lghezzi@citytech.cuny.edu',
			'MAT1275-S18-Lime'             => 'mlime@citytech.cuny.edu',
			'MAT1275-S18-Mingla-10am'      => 'lmingla@citytech.cuny.edu',
			'MAT1275-S18-Mingla-8am'       => 'lmingla@citytech.cuny.edu',
			'MAT1275-S18-Ovshey'           => 'novshey@citytech.cuny.edu',
			'MAT1275-S18-Poirier'          => 'kpoirier@citytech.cuny.edu',
			'MAT1275-S18-Rafeek'           => 'rrafeek@citytech.cuny.edu',
			'MAT1275-S18-Rahaman'          => 'lrahaman@citytech.cuny.edu',
			'MAT1275-S18-Rozenblyum'       => 'arozenblyum@citytech.cuny.edu',
			'MAT1275-S18-Sirelson'         => 'vsirelson@citytech.cuny.edu',
			'MAT1275-S18-Yeeda'            => 'vyeeda@citytech.cuny.edu',
			'MAT1275-S18-Yu'               => 'dmyu@citytech.cuny.edu',
			'MAT1275-S18-Zapata-1pm'       => 'gzapata@citytech.cuny.edu',
			'MAT1275-S18-Zapata-9am'       => 'gzapata@citytech.cuny.edu',
			'MAT1275-S18-Zeng'             => 'szeng@citytech.cuny.edu',
			'MAT1275EN-S18-Berglund'       => 'rberglund@citytech.cuny.edu',
			'MAT1275EN-S18-Daouki'         => 'sdaouki@citytech.cuny.edu',
			'MAT1275EN-S18-Kan-10am'       => 'bkan@citytech.cuny.edu',
			'MAT1275EN-S18-Kan-8am'        => 'bkan@citytech.cuny.edu',
			'MAT1275EN-S18-Parker'         => 'kparker@citytech.cuny.edu',

			'MAT1275-F18-Aqil'             => 'maqil@citytech.cuny.edu',
			'MAT1275-F18-Barthelemy'       => 'nbarthelemy@citytech.cuny.edu',
			'MAT1275-F18-Batyr-Fri'        => 'obatyr@citytech.cuny.edu',
			'MAT1275-F18-Batyr-WF'         => 'obatyr@citytech.cuny.edu',
			'MAT1275-F18-Beck'             => 'mbeck@citytech.cuny.edu',
			'MAT1275-F18-Berglund'         => 'rberglund@citytech.cuny.edu',
			'MAT1275-F18-Brenord'          => 'dbrenord@citytech.cuny.edu',
			'MAT1275-F18-Calinescu'        => 'ccalinescu@citytech.cuny.edu',
			'MAT1275-F18-DOrazio'          => 'ddorazio@citytech.cuny.edu',
			'MAT1275-F18-Duvvuri-10AM'     => 'vduvvuri@citytech.cuny.edu',
			'MAT1275-F18-Duvvuri-8AM'      => 'vduvvuri@citytech.cuny.edu',
			'MAT1275-F18-Edem'             => 'vedem@citytech.cuny.edu',
			'MAT1275-F18-Essien'           => 'sessien@citytech.cuny.edu',
			'MAT1275-F18-Frankel'          => 'rf26@nyu.edu',
			'MAT1275-F18-Goorova'          => 'lgoorova@citytech.cuny.edu',
			'MAT1275-F18-Grigorian'        => 'lgrigorian@citytech.cuny.edu',
			'MAT1275-F18-Gumeni'           => 'fgumeni@citytech.cuny.edu',
			'MAT1275-F18-Kan'              => 'bkan@citytech.cuny.edu',
			'MAT1275-F18-Kiefer'           => 'gkiefer@citytech.cuny.edu',
			'MAT1275-F18-Koca'             => 'ckoca@citytech.cuny.edu',
			'MAT1375-F18-Kostadinov'       => 'bkostadinov@citytech.cuny.edu',
			'MAT1275-F18-Kroll'            => 'jkroll@citytech.cuny.edu',
			'MAT1275-F18-Kushnir'          => 'rkushnir@citytech.cuny.edu',
			'MAT1275-F18-Lee'              => 'VILee@citytech.cuny.edu',
			'MAT1275-F18-Lime'             => 'mlime@citytech.cuny.edu',
			'MAT1275-F18-Mingla'           => 'lmingla@citytech.cuny.edu',
			'MAT1275-F18-Mujica'           => 'pmujica@citytech.cuny.edu',
			'MAT1275-F18-Mukhin'           => 'amukhin@citytech.cuny.edu',
			'MAT1275-F18-Ndengeyintwali'   => 'dndengeyintwali@citytech.cuny.edu',
			'MAT1275-F18-Ovshey'           => 'novshey@citytech.cuny.edu',
			'MAT1275-F18-Rafeek'           => 'rrafeek@citytech.cuny.edu',
			'MAT1275-F18-Saha'             => 'srsaha@citytech.cuny.edu',
			'MAT1275-F18-Shaver'           => 'sshaver@citytech.cuny.edu',
			'MAT1275-F18-Yeeda-12PM'       => 'vyeeda@citytech.cuny.edu',
			'MAT1275-F18-Yeeda-9AM'        => 'vyeeda@citytech.cuny.edu',
			'MAT1275-F18-Zeng'             => 'szeng@citytech.cuny.edu',
			'MAT1375-F18-Frankel'          => 'rf26@nyu.edu',
			'MAT1375-F18-Ghezzi'           => 'lghezzi@citytech.cuny.edu',
			'MAT1375-F18-Kan'              => 'bkan@citytech.cuny.edu',
			'MAT1375-F18-Koca'             => 'ckoca@citytech.cuny.edu',
			'MAT1375-F18-Masuda'           => 'amasuda@citytech.cuny.edu',
			'MAT1375-F18-Parker'           => 'kparker@citytech.cuny.edu',
			'MAT1375-F18-Poirier'          => 'kpoirier@citytech.cuny.edu',
			'MAT1375-F18-Shaver'           => 'sshaver@citytech.cuny.edu',
			'MAT1375-F18-Sirelson'         => 'vsirelson@citytech.cuny.edu',

			'MAT1275-S19-Ahmed'            => 'mahmed@citytech.cuny.edu',
			'MAT1275-S19-Aqil'             => 'maqil@citytech.cuny.edu',
			'MAT1275-S19-Barthelemy'       => 'nbarthelemy@citytech.cuny.edu',
			'MAT1275-S19-Bosso'            => 'kbosso@citytech.cuny.edu',
			'MAT1275-S19-Brenord'          => 'dbrenord@citytech.cuny.edu',
			'MAT1275-S19-Calinescu'        => 'ccalinescu@citytech.cuny.edu',
			'MAT1275-S19-Chan'             => 'echan@citytech.cuny.edu',
			'MAT1275-S19-Chan-D525'        => 'cchan@citytech.cuny.edu',
			'MAT1275-S19-Duvvuri'          => 'vduvvuri@citytech.cuny.edu',
			'MAT1275-S19-Edem'             => 'vedem@citytech.cuny.edu',
			'MAT1275-S19-Essien'           => 'sessien@citytech.cuny.edu',
			'MAT1275-S19-Helfand'          => 'ihelfand@citytech.cuny.edu',
			'MAT1275-S19-Isaacson'         => 'bisaacson@citytech.cuny.edu',
			'MAT1275-S19-Jeudy'            => 'ijeudy@citytech.cuny.edu',
			'MAT1275-S19-Kan'              => 'bkan@citytech.cuny.edu',
			'MAT1275-S19-Kiefer'           => 'gkiefer@citytech.cuny.edu',
			'MAT1275-S19-Lee'              => 'vilee@citytech.cuny.edu',
			'MAT1275-S19-Lime'             => 'mlime@citytech.cuny.edu',
			'MAT1275-S19-Mingla'           => 'lmingla@citytech.cuny.edu',
			'MAT1275-S19-Morrison'         => 'cmorrison@citytech.cuny.edu',
			'MAT1275-S19-Nehme'            => 'snehme@citytech.cuny.edu',
			'MAT1275-S19-Niezgoda-MW-12pm' => 'gniezgoda@citytech.cuny.edu',
			'MAT1275-S19-Niezgoda-MW-2pm'  => 'gniezgoda@citytech.cuny.edu',
			'MAT1275-S19-Rafeek'           => 'rrafeek@citytech.cuny.edu',
			'MAT1275-S19-Saha'             => 'srsaha@citytech.cuny.edu',
			'MAT1275-S19-Shaver'           => 'sshaver@citytech.cuny.edu',
			'MAT1275-S19-Shifa'            => 'sshifa@citytech.cuny.edu',
			'MAT1275-S19-Teano'            => 'eteano@citytech.cuny.edu',
			'MAT1275-S19-Traore'           => 'mtraore@citytech.cuny.edu',
			'MAT1275-S19-Verras'           => 'sverras@citytech.cuny.edu',
			'MAT1275-S19-Wharton'          => 'fwharton@citytech.cuny.edu',
			'MAT1275-S19-Yu'               => 'dmyu@citytech.cuny.edu',

			'MAT1375-S19-Batyr'            => 'obatyr@citytech.cuny.edu',
			'MAT1375-S19-Bonanome'         => 'mbonanome@citytech.cuny.edu',
			'MAT1375-S19-Calinescu'        => 'ccalinescu@citytech.cuny.edu',
			'MAT1375-S19-DOrazio'          => 'ddorazio@citytech.cuny.edu',
			'MAT1375-S19-Ganguli'          => 'sganguli@citytech.cuny.edu',
			'MAT1375-S19-Ghezzi'           => 'lghezzi@citytech.cuny.edu',
			'MAT1375-S19-Halleck'          => 'ehalleck@citytech.cuny.edu',
			'MAT1375-S19-Helfand'          => 'ihelfand@citytech.cuny.edu',
			'MAT1375-S19-Kan'              => 'bkan@citytech.cuny.edu',
			'MAT1375-S19-Masuda'           => 'amasuda@citytech.cuny.edu',
			'MAT1375-S19-Mingla'           => 'lmingla@citytech.cuny.edu',
			'MAT1375-S19-Poirier'          => 'kpoirier@citytech.cuny.edu',
			'MAT1375-S19-Sirelson'         => 'vsirelson@citytech.cuny.edu',
		);
	}
);

/**
 * Update the associated group's last_activity when new content is posted.
 */
function openlab_webwork_bump_group_on_activity( $post_id ) {
	// We do this weird parsing because the request comes from the API,
	// and we need to maintain compat with openlabdev.org.
	$client_site_url = apply_filters( 'webwork_client_site_base', '' );
	$parts           = parse_url( $client_site_url );
	$site            = get_site_by_path( $parts['host'], $parts['path'] );

	if ( ! $site ) {
		return;
	}

	$group_id = openlab_get_group_id_by_blog_id( $site->blog_id );
	if ( ! $group_id ) {
		return;
	}

	groups_update_groupmeta( $group_id, 'last_activity', bp_core_current_time() );
}
add_action( 'save_post_webwork_question', 'openlab_webwork_bump_group_on_activity' );
add_action( 'save_post_webwork_response', 'openlab_webwork_bump_group_on_activity' );

/**
 * Login message.
 */
add_filter(
	'webwork_login_redirect_message',
	function( $message ) {
		return 'You must log into the OpenLab in order to post a WeBWorK question.';
	}
);

/**
 * Intro text.
 */
add_filter(
	'webwork_intro_text',
	function( $text ) {
		$about_url = home_url( 'about' );
		return sprintf( 'You are viewing <a href="%s">WeBWorK on the OpenLab</a>. Here, you can ask questions and discuss WeBWorK homework problems, and also see what other students have been asking.', $about_url );
	}
);

/**
 * Sidebar intro text.
 */
add_filter(
	'webwork_sidebar_intro_text',
	function( $text ) {
		$help_url = home_url( 'help/explore-existing-questions-and-replies/#Filters' );
		return sprintf( 'Use the <a href="%s">filters</a> below to navigate the questions that have been posted. You can select questions by course, section, or a specific WeBWorK problem set.', $help_url );
	}
);

/**
 * Incomplete question text.
 */
add_filter(
	'webwork_incomplete_question_text',
	function() {
		return sprintf( 'This question does not contain enough detail for a useful response to be provided. Please review the <a href="%s">Ask Questions</a> page for guidance on how to phrase your question so that we may help you.', home_url( 'help/ask-questions' ) );
	}
);
