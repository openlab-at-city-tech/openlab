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
			'MAT1275-F17-Antoine'                    => 'wantoine@citytech.cuny.edu',
			'MAT1275-F17-Ferguson'                   => 'rferguson@citytech.cuny.edu',
			'MAT1275-F17-Mujica'                     => 'pmujica@citytech.cuny.edu',
			'MAT1275-F17-Poirier'                    => 'kpoirier@citytech.cuny.edu',
			'MAT1275-F17-Saha'                       => 'srsaha@citytech.cuny.edu',
			'MAT1275-F17-Sirelson'                   => 'vsirelson@citytech.cuny.edu',
			'MAT1275EN-F17-Carley'                   => 'hcarley@citytech.cuny.edu',
			'MAT1275EN-F17-Ganguli'                  => 'sganguli@citytech.cuny.edu',
			'MAT1275EN-F17-Mingla'                   => 'lmingla@citytech.cuny.edu',
			'MAT1275EN-F17-Parker'                   => 'kparker@citytech.cuny.edu',
			'MAT1275-F17-Zeng-2pm'                   => 'szeng@citytech.cuny.edu',
			'MAT1275-F17-Zeng-4pm'                   => 'szeng@citytech.cuny.edu',
			'MAT1275-F17-Batyr-8am'                  => 'obatyr@citytech.cuny.edu',
			'MAT1275-F17-Batyr-10am'                 => 'obatyr@citytech.cuny.edu',

			'MAT1275-S18-Antoine'                    => 'wantoine@citytech.cuny.edu',
			'MAT1275-S18-Ayoub'                      => 'tayoub@citytech.cuny.edu',
			'MAT1275-S18-Chan'                       => 'cchan@citytech.cuny.edu',
			'MAT1275-S18-Duvvuri'                    => 'vduvvuri@citytech.cuny.edu',
			'MAT1275-S18-Ferguson'                   => 'rferguson@citytech.cuny.edu',
			'MAT1275-S18-Ganguli'                    => 'sganguli@citytech.cuny.edu',
			'MAT1275-S18-Ghezzi'                     => 'lghezzi@citytech.cuny.edu',
			'MAT1275-S18-Lime'                       => 'mlime@citytech.cuny.edu',
			'MAT1275-S18-Mingla-10am'                => 'lmingla@citytech.cuny.edu',
			'MAT1275-S18-Mingla-8am'                 => 'lmingla@citytech.cuny.edu',
			'MAT1275-S18-Ovshey'                     => 'novshey@citytech.cuny.edu',
			'MAT1275-S18-Poirier'                    => 'kpoirier@citytech.cuny.edu',
			'MAT1275-S18-Rafeek'                     => 'rrafeek@citytech.cuny.edu',
			'MAT1275-S18-Rahaman'                    => 'lrahaman@citytech.cuny.edu',
			'MAT1275-S18-Rozenblyum'                 => 'arozenblyum@citytech.cuny.edu',
			'MAT1275-S18-Sirelson'                   => 'vsirelson@citytech.cuny.edu',
			'MAT1275-S18-Yeeda'                      => 'vyeeda@citytech.cuny.edu',
			'MAT1275-S18-Yu'                         => 'dmyu@citytech.cuny.edu',
			'MAT1275-S18-Zapata-1pm'                 => 'gzapata@citytech.cuny.edu',
			'MAT1275-S18-Zapata-9am'                 => 'gzapata@citytech.cuny.edu',
			'MAT1275-S18-Zeng'                       => 'szeng@citytech.cuny.edu',
			'MAT1275EN-S18-Berglund'                 => 'rberglund@citytech.cuny.edu',
			'MAT1275EN-S18-Daouki'                   => 'sdaouki@citytech.cuny.edu',
			'MAT1275EN-S18-Kan-10am'                 => 'bkan@citytech.cuny.edu',
			'MAT1275EN-S18-Kan-8am'                  => 'bkan@citytech.cuny.edu',
			'MAT1275EN-S18-Parker'                   => 'kparker@citytech.cuny.edu',

			'MAT1275-F18-Aqil'                       => 'maqil@citytech.cuny.edu',
			'MAT1275-F18-Barthelemy'                 => 'nbarthelemy@citytech.cuny.edu',
			'MAT1275-F18-Batyr-Fri'                  => 'obatyr@citytech.cuny.edu',
			'MAT1275-F18-Batyr-WF'                   => 'obatyr@citytech.cuny.edu',
			'MAT1275-F18-Beck'                       => 'mbeck@citytech.cuny.edu',
			'MAT1275-F18-Berglund'                   => 'rberglund@citytech.cuny.edu',
			'MAT1275-F18-Brenord'                    => 'dbrenord@citytech.cuny.edu',
			'MAT1275-F18-Calinescu'                  => 'ccalinescu@citytech.cuny.edu',
			'MAT1275-F18-DOrazio'                    => 'ddorazio@citytech.cuny.edu',
			'MAT1275-F18-Duvvuri-10AM'               => 'vduvvuri@citytech.cuny.edu',
			'MAT1275-F18-Duvvuri-8AM'                => 'vduvvuri@citytech.cuny.edu',
			'MAT1275-F18-Edem'                       => 'vedem@citytech.cuny.edu',
			'MAT1275-F18-Essien'                     => 'sessien@citytech.cuny.edu',
			'MAT1275-F18-Frankel'                    => 'rf26@nyu.edu',
			'MAT1275-F18-Goorova'                    => 'lgoorova@citytech.cuny.edu',
			'MAT1275-F18-Grigorian'                  => 'lgrigorian@citytech.cuny.edu',
			'MAT1275-F18-Gumeni'                     => 'fgumeni@citytech.cuny.edu',
			'MAT1275-F18-Kan'                        => 'bkan@citytech.cuny.edu',
			'MAT1275-F18-Kiefer'                     => 'gkiefer@citytech.cuny.edu',
			'MAT1275-F18-Koca'                       => 'ckoca@citytech.cuny.edu',
			'MAT1375-F18-Kostadinov'                 => 'bkostadinov@citytech.cuny.edu',
			'MAT1275-F18-Kroll'                      => 'jkroll@citytech.cuny.edu',
			'MAT1275-F18-Kushnir'                    => 'rkushnir@citytech.cuny.edu',
			'MAT1275-F18-Lee'                        => 'VILee@citytech.cuny.edu',
			'MAT1275-F18-Lime'                       => 'mlime@citytech.cuny.edu',
			'MAT1275-F18-Mingla'                     => 'lmingla@citytech.cuny.edu',
			'MAT1275-F18-Mujica'                     => 'pmujica@citytech.cuny.edu',
			'MAT1275-F18-Mukhin'                     => 'amukhin@citytech.cuny.edu',
			'MAT1275-F18-Ndengeyintwali'             => 'dndengeyintwali@citytech.cuny.edu',
			'MAT1275-F18-Ovshey'                     => 'novshey@citytech.cuny.edu',
			'MAT1275-F18-Rafeek'                     => 'rrafeek@citytech.cuny.edu',
			'MAT1275-F18-Saha'                       => 'srsaha@citytech.cuny.edu',
			'MAT1275-F18-Shaver'                     => 'sshaver@citytech.cuny.edu',
			'MAT1275-F18-Yeeda-12PM'                 => 'vyeeda@citytech.cuny.edu',
			'MAT1275-F18-Yeeda-9AM'                  => 'vyeeda@citytech.cuny.edu',
			'MAT1275-F18-Zeng'                       => 'szeng@citytech.cuny.edu',
			'MAT1375-F18-Frankel'                    => 'rf26@nyu.edu',
			'MAT1375-F18-Ghezzi'                     => 'lghezzi@citytech.cuny.edu',
			'MAT1375-F18-Kan'                        => 'bkan@citytech.cuny.edu',
			'MAT1375-F18-Koca'                       => 'ckoca@citytech.cuny.edu',
			'MAT1375-F18-Masuda'                     => 'amasuda@citytech.cuny.edu',
			'MAT1375-F18-Parker'                     => 'kparker@citytech.cuny.edu',
			'MAT1375-F18-Poirier'                    => 'kpoirier@citytech.cuny.edu',
			'MAT1375-F18-Shaver'                     => 'sshaver@citytech.cuny.edu',
			'MAT1375-F18-Sirelson'                   => 'vsirelson@citytech.cuny.edu',

			'MAT1275-S19-Ahmed'                      => 'mahmed@citytech.cuny.edu',
			'MAT1275-S19-Aqil'                       => 'maqil@citytech.cuny.edu',
			'MAT1275-S19-Barthelemy'                 => 'nbarthelemy@citytech.cuny.edu',
			'MAT1275-S19-Bosso'                      => 'kbosso@citytech.cuny.edu',
			'MAT1275-S19-Brenord'                    => 'dbrenord@citytech.cuny.edu',
			'MAT1275-S19-Calinescu'                  => 'ccalinescu@citytech.cuny.edu',
			'MAT1275-S19-Chan'                       => 'echan@citytech.cuny.edu',
			'MAT1275-S19-Chan-D525'                  => 'cchan@citytech.cuny.edu',
			'MAT1275-S19-Duvvuri'                    => 'vduvvuri@citytech.cuny.edu',
			'MAT1275-S19-Edem'                       => 'vedem@citytech.cuny.edu',
			'MAT1275-S19-Essien'                     => 'sessien@citytech.cuny.edu',
			'MAT1275-S19-Helfand'                    => 'ihelfand@citytech.cuny.edu',
			'MAT1275-S19-Isaacson'                   => 'bisaacson@citytech.cuny.edu',
			'MAT1275-S19-Jeudy'                      => 'ijeudy@citytech.cuny.edu',
			'MAT1275-S19-Kan'                        => 'bkan@citytech.cuny.edu',
			'MAT1275-S19-Kiefer'                     => 'gkiefer@citytech.cuny.edu',
			'MAT1275-S19-Lee'                        => 'vilee@citytech.cuny.edu',
			'MAT1275-S19-Lime'                       => 'mlime@citytech.cuny.edu',
			'MAT1275-S19-Mingla'                     => 'lmingla@citytech.cuny.edu',
			'MAT1275-S19-Morrison'                   => 'cmorrison@citytech.cuny.edu',
			'MAT1275-S19-Nehme'                      => 'snehme@citytech.cuny.edu',
			'MAT1275-S19-Niezgoda-MW-12pm'           => 'gniezgoda@citytech.cuny.edu',
			'MAT1275-S19-Niezgoda-MW-2pm'            => 'gniezgoda@citytech.cuny.edu',
			'MAT1275-S19-Rafeek'                     => 'rrafeek@citytech.cuny.edu',
			'MAT1275-S19-Saha'                       => 'srsaha@citytech.cuny.edu',
			'MAT1275-S19-Shaver'                     => 'sshaver@citytech.cuny.edu',
			'MAT1275-S19-Shifa'                      => 'sshifa@citytech.cuny.edu',
			'MAT1275-S19-Teano'                      => 'eteano@citytech.cuny.edu',
			'MAT1275-S19-Traore'                     => 'mtraore@citytech.cuny.edu',
			'MAT1275-S19-Verras'                     => 'sverras@citytech.cuny.edu',
			'MAT1275-S19-Wharton'                    => 'fwharton@citytech.cuny.edu',
			'MAT1275-S19-Yu'                         => 'dmyu@citytech.cuny.edu',

			'MAT1375-S19-Batyr'                      => 'obatyr@citytech.cuny.edu',
			'MAT1375-S19-Bonanome'                   => 'mbonanome@citytech.cuny.edu',
			'MAT1375-S19-Calinescu'                  => 'ccalinescu@citytech.cuny.edu',
			'MAT1375-S19-DOrazio'                    => 'ddorazio@citytech.cuny.edu',
			'MAT1375-S19-Ganguli'                    => 'sganguli@citytech.cuny.edu',
			'MAT1375-S19-Ghezzi'                     => 'lghezzi@citytech.cuny.edu',
			'MAT1375-S19-Halleck'                    => 'ehalleck@citytech.cuny.edu',
			'MAT1375-S19-Helfand'                    => 'ihelfand@citytech.cuny.edu',
			'MAT1375-S19-Kan'                        => 'bkan@citytech.cuny.edu',
			'MAT1375-S19-Masuda'                     => 'amasuda@citytech.cuny.edu',
			'MAT1375-S19-Mingla'                     => 'lmingla@citytech.cuny.edu',
			'MAT1375-S19-Poirier'                    => 'kpoirier@citytech.cuny.edu',
			'MAT1375-S19-Sirelson'                   => 'vsirelson@citytech.cuny.edu',

			'MAT1275CO-F19-Abdurakhmanova-D444'      => 'vabdurakhmanova@citytech.cuny.edu',
			'MAT1275CO-F19-Ahmed-E476'               => 'mahmed@citytech.cuny.edu',
			'MAT1275CO-F19-Aqil-D401'                => 'maqil@citytech.cuny.edu',
			'MAT1275CO-F19-Arjun-D467'               => 'jarjun@citytech.cuny.edu',
			'MAT1275CO-F19-Barthelemy-E475'          => 'nbarthelemy@citytech.cuny.edu',
			'MAT1275CO-F19-Beck-D454'                => 'mbeck@citytech.cuny.edu',
			'MAT1275CO-F19-Berglund-D402'            => 'rberglund@citytech.cuny.edu',
			'MAT1275CO-F19-Boakye-D458'              => 'aboakye@citytech.cuny.edu',
			'MAT1275CO-F19-Bosso-D414'               => 'kbosso@citytech.cuny.edu',
			'MAT1275CO-F19-Boukenken-D452'           => 'kboukenken@citytech.cuny.edu',
			'MAT1275CO-F19-Brenord-E473'             => 'dbrenord@citytech.cuny.edu',
			'MAT1275CO-F19-Capeless-D421'            => 'rreid@citytech.cuny.edu',
			'MAT1275CO-F19-Carley-D427'              => 'hcarley@citytech.cuny.edu',
			'MAT1275CO-F19-Chan-D425'                => 'echan@citytech.cuny.edu',
			'MAT1275CO-F19-Cinar-D445'               => 'Mukadder.Cinar@mail.citytech.cuny.edu',
			'MAT1275CO-F19-DOrazio-D407'             => 'ddorazio@citytech.cuny.edu',
			'MAT1275CO-F19-Douglas-D456'             => 'adouglas@citytech.cuny.edu',
			'MAT1275CO-F19-Essien-D418'              => 'sessien@citytech.cuny.edu',
			'MAT1275CO-F19-Ghosh-dastidar-D442'      => 'ughosh-dastidar@citytech.cuny.edu',
			'MAT1275CO-F19-Goorova-D459'             => 'lgoorova@citytech.cuny.edu',
			'MAT1275CO-F19-Greenstein-D412'          => 'jgreenstein@citytech.cuny.edu',
			'MAT1275CO-F19-Greenstein-D426'          => 'jgreenstein@citytech.cuny.edu',
			'MAT1275CO-F19-Gumeni-D417'              => 'fgumeni@citytech.cuny.edu',
			'MAT1275CO-F19-Halleck-D466'             => 'ehalleck@citytech.cuny.edu',
			'MAT1275CO-F19-Hellmann-D440'            => 'jhellmann@citytech.cuny.edu',
			'MAT1275CO-F19-Hill-D438'                => 'ehill@citytech.cuny.edu',
			'MAT1275CO-F19-Huang-D409'               => 'Jiehao.Luo@mail.citytech.cuny.edu',
			'MAT1275CO-F19-Huang-D448'               => 'whuang@citytech.cuny.edu',
			'MAT1275CO-F19-Jaramillo-Dominguez-CN31' => 'djaramillodominguez@citytech.cuny.edu',
			'MAT1275CO-F19-Jeudy-E484'               => 'ijeudy@citytech.cuny.edu',
			'MAT1275CO-F19-Kan-LC41'                 => 'bkan@citytech.cuny.edu',
			'MAT1275CO-F19-Khaknazarova-D406'        => 'zkhaknazarova@citytech.cuny.edu',
			'MAT1275CO-F19-Kiefer-D413'              => 'gkiefer@citytech.cuny.edu',
			'MAT1275CO-F19-Kroll-D453'               => 'jkroll@gradcenter.cuny.edu',
			'MAT1275CO-F19-Lee-D460'                 => 'vlee@citytech.cuny.edu',
			'MAT1275CO-F19-Li-E486'                  => 'sli@citytech.cuny.edu',
			'MAT1275CO-F19-Lime-D403'                => 'mlime@citytech.cuny.edu',
			'MAT1275CO-F19-Morrison-D428'            => 'cmorrison@citytech.cuny.edu',
			'MAT1275CO-F19-Ndengeyintwali-D450'      => 'dndengeyintwali@citytech.cuny.edu',
			'MAT1275CO-F19-Nehme-D462'               => 'snehme@citytech.cuny.edu',
			'MAT1275CO-F19-Niezgoda-D435'            => 'gniezgoda@citytech.cuny.edu',
			'MAT1275CO-F19-Ovshey-D464'              => 'novshey@citytech.cuny.edu',
			'MAT1275CO-F19-PerezFlores-CP25'         => 'EPerezFlores@citytech.cuny.edu',
			'MAT1275CO-F19-Philips-D411'             => 'aphilips@citytech.cuny.edu',
			'MAT1275CO-F19-Rafeek-D404'              => 'rrafeek@citytech.cuny.edu',
			'MAT1275CO-F19-Rocklin-D420'             => 'srocklin@citytech.cuny.edu',
			'MAT1275CO-F19-Rozenblyum-D434'          => 'arozenblyum@citytech.cuny.edu',
			'MAT1275CO-F19-Saint-Juste-E478'         => 'p.e.stjust@gmail.com',
			'MAT1275CO-F19-Shati'                    => 'fshati@citytech.cuny.edu',
			'MAT1275CO-F19-Shaver-D755'              => 'sshaver@citytech.cuny.edu',
			'MAT1275CO-F19-Tam-D437'                 => 'jtam@skidmore.edu',
			'MAT1275CO-F19-Teano-E480'               => 'eteano@citytech.cuny.edu',
			'MAT1275CO-F19-Vaughn-D408'              => 'ajvaughn@citytech.cuny.edu',
			'MAT1275CO-F19-Vaughn-D419'              => 'avaughn@citytech.cuny.edu',
			'MAT1275CO-F19-Yu-D430'                  => 'dmyu@citytech.cuny.edu',
			'MAT1275CO-F19-Zhu-W490'                 => 'dzhu@citytech.cuny.edu',
			'MAT1275-F19-Batyr-D534'                 => 'obatyr@citytech.cuny.edu',
			'MAT1275-F19-Chan-D529'                  => 'cchan@citytech.cuny.edu',
			'MAT1275-F19-Colucci-D505'               => 'wcolucci@citytech.cuny.edu',
			'MAT1275-F19-Colucci-D522'               => 'wcolucci@citytech.cuny.edu',
			'MAT1275-F19-DAnna-W541'                 => 'fdanna@citytech.cuny.edu',
			'MAT1275-F19-Edem-E535'                  => 'vedem@citytech.cuny.edu',
			'MAT1275-F19-Ghosh-dastidar-D540'        => 'ughosh-dastidar@citytech.cuny.edu',
			'MAT1275-F19-Ghosh-E542'                 => 'oghosh@citytech.cuny.edu',
			'MAT1275-F19-Kushnir-D506'               => 'rkushnir@citytech.cuny.edu',
			'MAT1275-F19-Liang-D544'                 => 'lliang@citytech.cuny.edu',
			'MAT1275-F19-Nadmi-D500'                 => 'mnadmi@citytech.cuny.edu',
			'MAT1275-F19-Nadmi-D515'                 => 'mnadmi@citytech.cuny.edu',
			'MAT1275-F19-Preiss-D501'                => 'mpreiss@citytech.cuny.edu',
			'MAT1275-F19-Rozenblyum-D521'            => 'arozenblyum@citytech.cuny.edu',
			'MAT1275-F19-Theodore-D536'              => 'ftheodore@citytech.cuny.edu',
			'MAT1275-F19-Valcourt-D547'              => 'yvalcourt@citytech.cuny.edu',
			'MAT1275-F19-Valcourt-E539'              => 'yvalcourt@citytech.cuny.edu',
			'MAT1375-F19-Bilsky-Bieniek-D565'        => 'cbilskybieniek@citytech.cuny.edu',
			'MAT1375-F19-Boukerrou-D562'             => 'kboukerrou@citytech.cuny.edu',
			'MAT1375-F19-Calinescu-Costeanu-D588'    => 'ncalinescucosteanu@citytech.cuny.edu',
			'MAT1375-F19-Camilien-D560'              => 'jcamilien@citytech.cuny.edu',
			'MAT1375-F19-Camilien-W568'              => 'jcamilien@citytech.cuny.edu',
			'MAT1375-F19-Cobb-D585'                  => 'pcobb@citytech.cuny.edu',
			'MAT1375-F19-Edem-D591'                  => 'vedem@citytech.cuny.edu',
			'MAT1375-F19-Feng-D568'                  => 'sfeng@citytech.cuny.edu',
			'MAT1375-F19-Grigorian-D592'             => 'lgrigorian@citytech.cuny.edu',
			'MAT1375-F19-Hill-D584'                  => 'ehill@citytech.cuny.edu',
			'MAT1375-F19-Hill-D589'                  => 'ehill@citytech.cuny.edu',
			'MAT1375-F19-Ishii-D596'                 => 'mishii@citytech.cuny.edu',
			'MAT1375-F19-Jiang-D569'                 => 'cjiang@citytech.cuny.edu',
			'MAT1375-F19-Kan-D572'                   => 'bkan@citytech.cuny.edu',
			'MAT1375-F19-Koca-D573'                  => 'ckoca@citytech.cuny.edu',
			'MAT1375-F19-Kushnir-D561'               => 'rkushnir@citytech.cuny.edu',
			'MAT1375-F19-Murray-E560'                => 'pmurray@citytech.cuny.edu',
			'MAT1375-F19-Poirier-D574'               => 'kpoirier@citytech.cuny.edu',
			'MAT1375-F19-Polinsky-D567'              => 'ipolinsky@citytech.cuny.edu',
			'MAT1375-F19-Polinsky-D576'              => 'ipolinsky@citytech.cuny.edu',
			'MAT1375-F19-Shifa-D564'                 => 'sshifa@citytech.cuny.edu',
			'MAT1375-F19-Sikri-D579'                 => 'ssikri@citytech.cuny.edu',
			'MAT1375-F19-Sun-D578'                   => 'jsun@citytech.cuny.edu',
			'MAT1375-F19-Tradler-D594'               => 'ttradler@citytech.cuny.edu',
			'MAT1375-F19-Uwa-E564'                   => 'auwa@citytech.cuny.edu',
			'MAT1375-F19-Victor-E562'                => 'tvictor@citytech.cuny.edu',
			'MAT1375-F19-Victor-E566'                => 'tvictor@citytech.cuny.edu',
			'MAT1375-F19-Yeeda-CP30'                 => 'vyeeda@citytech.cuny.edu',
			'MAT1475-F19-Antoine-D599'               => 'wantoine@citytech.cuny.edu',
			'MAT1475-F19-Bonanome-D606'              => 'mbonanome@citytech.cuny.edu',
			'MAT1475-F19-Ghezzi-D608'                => 'lghezzi@citytech.cuny.edu',
			'MAT1475-F19-Masuda-D607'                => 'amasuda@citytech.cuny.edu',
			'MAT1475-F19-Mingla-D619'                => 'lmingla@citytech.cuny.edu',
			'MAT1475H-F19-Parker'                    => 'kparker@citytech.cuny.edu',
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

/**
 * Author type label.
 */
add_filter(
	'webwork_author_type_label',
	function ( $label, $user_id ) {
		$account_type = xprofile_get_field_data( 'Account Type', $user_id );
		if ( $account_type ) {
			$label = $account_type;
		}
		return $label;
	},
	10,
	2
);

/**
 * Set admin to Faculty.
 */
add_filter(
	'webwork_user_is_admin',
	function( $is_admin ) {
		$account_type = xprofile_get_field_data( 'Account Type', get_current_user_id() );
		if ( $account_type && 'Faculty' === $account_type ) {
			$is_admin = true;
		}

		return $is_admin;
	}
);
