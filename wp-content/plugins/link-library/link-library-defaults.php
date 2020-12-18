<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/************************** Function called to create default settings or to reset them on user request **************************/
function ll_reset_options( $settings = 1, $layout = 'list', $setoptions = 'return' ) {

	if ( $layout == 'list' ) {
		$options['num_columns']       = 1;
		$options['showdescription']   = false;
		$options['shownotes']         = false;
		$options['beforenote']        = '<br />';
		$options['afternote']         = '';
		$options['beforeitem']        = '<li>';
		$options['afteritem']         = '</li>';
		$options['beforedesc']        = '';
		$options['afterdesc']         = '';
		$options['displayastable']    = 'false';
		$options['beforelink']        = '';
		$options['afterlink']         = '';
		$options['showcolumnheaders'] = false;
		$options['beforerss']         = '';
		$options['afterrss']          = '';
		$options['beforedate']        = '';
		$options['afterdate']         = '';
		$options['beforeimage']       = '';
		$options['afterimage']        = '';
		$options['beforeweblink']     = '';
		$options['afterweblink']      = '';
		$options['beforetelephone']   = '';
		$options['aftertelephone']    = '';
		$options['beforeemail']       = '';
		$options['afteremail']        = '';
		$options['beforelinkhits']    = '';
		$options['afterlinkhits']     = '';
		$options['columnheaderoverride'] = '';
	} elseif ( $layout == "table" ) {
		$options['num_columns']       = 3;
		$options['showdescription']   = true;
		$options['shownotes']         = true;
		$options['beforenote']        = '<td>';
		$options['afternote']         = '</td>';
		$options['beforeitem']        = '<tr>';
		$options['afteritem']         = '</tr>';
		$options['beforedesc']        = '<td>';
		$options['afterdesc']         = '</td>';
		$options['displayastable']    = 'true';
		$options['beforelink']        = '<td>';
		$options['afterlink']         = '</td>';
		$options['showcolumnheaders'] = true;
		$options['beforerss']         = '<td>';
		$options['afterrss']          = '</td>';
		$options['beforedate']        = '<td>';
		$options['afterdate']         = '</td>';
		$options['beforeimage']       = '<td>';
		$options['afterimage']        = '</td>';
		$options['beforeweblink']     = '<td>';
		$options['afterweblink']      = '</td>';
		$options['beforetelephone']   = '<td>';
		$options['aftertelephone']    = '</td>';
		$options['beforeemail']       = '<td>';
		$options['afteremail']        = '</td>';
		$options['beforelinkhits']    = '<td>';
		$options['afterlinkhits']     = '</td>';
		$options['columnheaderoverride'] = 'Application,Description,Similar to';
	}

	$options['order']                         = 'name';
	$options['hide_if_empty']                 = true;
	$options['table_width']                   = 100;
	$options['catanchor']                     = true;
	$options['flatlist']                      = 'table';
	$options['categorylist_cpt']              = null;
	$options['excludecategorylist_cpt']       = null;
	$options['showrating']                    = false;
	$options['showupdated']                   = false;
	$options['show_images']                   = false;
	$options['use_html_tags']                 = false;
	$options['show_rss']                      = false;
	$options['nofollow']                      = false;
	$options['catlistwrappers']               = 1;
	$options['beforecatlist1']                = '';
	$options['beforecatlist2']                = '';
	$options['beforecatlist3']                = '';
	$options['divorheader']                   = false;
	$options['catnameoutput']                 = 'linklistcatname';
	$options['show_rss_icon']                 = false;
	$options['linkaddfrequency']              = 0;
	$options['addbeforelink']                 = '';
	$options['addafterlink']                  = '';
	$options['linktarget']                    = '';
	$options['showcategorydescheaders']       = false;
	$options['showcategorydesclinks']         = false;
	$options['settingssetname']               = 'Default';
	$options['showadmineditlinks']            = true;
	$options['showonecatonly']                = false;
	$options['loadingicon']                   = '/icons/Ajax-loader.gif';
	$options['defaultsinglecat_cpt']          = '';
	$options['rsspreview']                    = false;
	$options['rsspreviewcount']               = 3;
	$options['rssfeedinline']                 = false;
	$options['rssfeedinlinecontent']          = false;
	$options['rssfeedinlinecount']            = 1;
	$options['rssfeedinlinedayspublished']    = 0;
	$options['rssfeedinlineskipempty']        = false;
	$options['direction']                     = 'ASC';
	$options['linkdirection']                 = 'ASC';
	$options['linkorder']                     = 'name';
	$options['pagination']                    = false;
	$options['linksperpage']                  = 5;
	$options['hidecategorynames']             = false;
	$options['showinvisible']                 = false;
	$options['showinvisibleadmin']            = false;
	$options['showdate']                      = false;
	$options['catdescpos']                    = 'right';
	$options['catlistdescpos']                = 'right';
	$options['showuserlinks']                 = false;
	$options['addnewlinkmsg']                 = __( 'Add new link', 'link-library' );
	$options['linknamelabel']                 = __( 'Link name', 'link-library' );
	$options['linkaddrlabel']                 = __( 'Link address', 'link-library' );
	$options['linkrsslabel']                  = __( 'Link RSS', 'link-library' );
	$options['linkcatlabel']                  = __( 'Link Category', 'link-library' );
	$options['linkdesclabel']                 = __( 'Link Description', 'link-library' );
	$options['linknoteslabel']                = __( 'Link Notes', 'link-library' );
	$options['addlinkbtnlabel']               = __( 'Add Link', 'link-library' );
	$options['newlinkmsg']                    = __( 'New link submitted.', 'link-library' );
	$options['moderatemsg']                   = __( 'It will appear in the list once moderated. Thank you.', 'link-library' );
	$options['rsspreviewwidth']               = 900;
	$options['rsspreviewheight']              = 700;
	$options['imagepos']                      = 'beforename';
	$options['imageclass']                    = '';
	$options['emailnewlink']                  = false;
	$options['emailsubmitter']                = false;
	$options['showaddlinkrss']                = 'hide';
	$options['showaddlinkdesc']               = 'hide';
	$options['showaddlinkcat']                = 'hide';
	$options['showaddlinknotes']              = 'hide';
	$options['usethumbshotsforimages']        = false;
	$options['uselocalimagesoverthumbshots']  = false;
	$options['addlinkreqlogin']               = false;
	$options['showcatlinkcount']              = false;
	$options['publishrssfeed']                = false;
	$options['numberofrssitems']              = 10;
	$options['rssfeedtitle']                  = __( 'Link Library-Generated RSS Feed', 'link-library' );
	$options['rssfeeddescription']            = __( 'Description of Link Library-Generated Feed', 'link-library' );
	$options['showonecatmode']                = 'AJAX';
	$options['paginationposition']            = 'AFTER';
	$options['addlinkcustomcat']              = 'hide';
	$options['linkcustomcatlabel']            = __( 'User-submitted category', 'link-library' );
	$options['linkcustomcatlistentry']        = __( 'User-submitted category (define below)', 'link-library' );
	$options['searchlabel']                   = 'Search';
	$options['dragndroporder']                = implode( ',', range( 1, 22 ) );
	$options['showname']                      = true;
	$options['cattargetaddress']              = '';
	$options['displayweblink']                = 'false';
	$options['sourceweblink']                 = 'primary';
	$options['showtelephone']                 = 'false';
	$options['sourcetelephone']               = 'primary';
	$options['showemail']                     = 'false';
	$options['showlinkhits']                  = false;
	$options['weblinklabel']                  = '';
	$options['telephonelabel']                = '';
	$options['emaillabel']                    = '';
	$options['showaddlinkreciprocal']         = 'hide';
	$options['linkreciprocallabel']           = __( 'Reciprocal Link', 'link-library' );
	$options['showaddlinksecondurl']          = 'hide';
	$options['linksecondurllabel']            = __( 'Secondary Address', 'link-library' );
	$options['showaddlinktelephone']          = 'hide';
	$options['linktelephonelabel']            = __( 'Telephone', 'link-library' );
	$options['showaddlinkemail']              = 'hide';
	$options['linkemaillabel']                = __( 'E-mail', 'link-library' );
	$options['emailcommand']                  = '';
	$options['sourceimage']                   = 'primary';
	$options['sourcename']                    = 'primary';
	$options['tooltipname']                   = 'notooltip';
	$options['enablerewrite']                 = false;
	$options['rewritepage']                   = '';
	$options['storelinksubmitter']            = false;
	$options['maxlinks']                      = '';
	$options['showcaptcha']                   = false;
	$options['beforelinkrating']              = '';
	$options['afterlinkrating']               = '';
	$options['linksubmitternamelabel']        = __( 'Submitter Name', 'link-library' );
	$options['showlinksubmittername']         = 'hide';
	$options['linksubmitteremaillabel']       = __( 'Submitter E-mail', 'link-library' );
	$options['showaddlinksubmitteremail']     = 'hide';
	$options['linksubmittercommentlabel']     = __( 'Submitter Comment', 'link-library' );
	$options['showlinksubmittercomment']      = 'hide';
	$options['addlinkcatlistoverride']        = '';
	$options['showlargedescription']          = false;
	$options['beforelargedescription']        = '';
	$options['afterlargedescription']         = '';
	$options['showcustomcaptcha']             = 'hide';
	$options['customcaptchaquestion']         = __( 'Is boiling water hot or cold?', 'link-library' );
	$options['customcaptchaanswer']           = __( 'hot', 'link-library' );
	$options['rssfeedaddress']                = '';
	$options['addlinknoaddress']              = false;
	$options['featuredfirst']                 = false;
	$options['showlinksonclick']              = false;
	$options['linklargedesclabel']            = __( 'Large Description', 'link-library' );
	$options['showuserlargedescription']      = 'hide';
	$options['usetextareaforusersubmitnotes'] = false;
	$options['showcatonsearchresults']        = false;
	$options['shownameifnoimage']             = false;
	$options['searchresultsaddress']          = '';
	$options['enable_link_popup']             = false;
	$options['link_popup_text']               = __( '%link_image%<br />Click through to visit %link_name%.', 'link-library' );
	$options['popup_width']                   = 300;
	$options['popup_height']                  = 400;
	$options['nocatonstartup']                = false;
	$options['linktitlecontent']              = 'linkname';
	$options['singlelinkid']                  = '';
	$options['combineresults']                = false;
	$options['showifreciprocalvalid']         = false;
	$options['cat_letter_filter']             = 'no';
	$options['cat_letter_filter_autoselect']  = true;
	$options['cat_letter_filter_showalloption'] = true;
	$options['beforefirstlink']                 = '';
	$options['afterlastlink']                   = '';
	$options['searchfieldtext']                 = __( 'Search...', 'link-library' );
	$options['searchnoresultstext']             = __( 'No links found matching your search criteria', 'link-library' );
	$options['catfilterlabel']                  = __( 'Category Filter', 'link-library' );
	$options['addlinkdefaultcat']               = 'nodefaultcat';
	$options['addlinkakismet']                  = false;
	$options['current_user_links']              = false;
	$options['showsubmittername']               = false;
	$options['beforesubmittername']             = '';
	$options['aftersubmittername']              = '';
	$options['onereciprocaldomain']             = false;
	$options['nooutputempty']                   = false;
	$options['showcatdesc']                     = false;
	$options['beforecatdesc']                   = '';
	$options['aftercatdesc']                    = '';
	$options['emailextracontent']               = '';
	$options['showparentcatname']               = false;
	$options['showparentcatdesc']               = false;
	$options['hidechildcatlinks']               = false;
	$options['childcatdepthlimit']              = 0;
	$options['hidechildcattop']                 = false;
	$options['catlinkspermalinksmode']          = false;
	$options['toppagetext']                     = '';
	$options['showbreadcrumbspermalinks']       = false;
	$options['showlinktags']                    = false;
	$options['beforelinktags']                  = '';
	$options['afterlinktags']                   = '';
	$options['showlinkprice']                   = false;
	$options['beforelinkprice']                 = '';
	$options['afterlinkprice']                  = '';
	$options['linkcurrency']                    = '$';
	$options['linkcurrencyplacement']           = 'before';
	$options['show0asfree']                     = true;
	$options['allowcolumnsorting']              = false;
	$options['extraquerystring']                = '';
	$options['updatedlabel']                    = __( 'New', 'link-library' );
	$options['showupdatedpos']                  = 'before';
	$options['showsearchreset']                 = false;
	$options['weblinktarget']                   = '';
	$options['linktagslabel']                   = __( 'Link Tags', 'link-library' );
	$options['showaddlinktags']                 = 'hide';
	$options['addlinktaglistoverride']          = '';
	$options['linkcustomtaglabel']              = '';
	$options['addlinkcustomtag']                = 'hide';
	$options['linkcustomtaglistentry']          = 'User-submitted tag (define below)';
	$options['showscheduledlinks']              = false;
	$options['maxlinkspercat']                  = '';
	$options['suppressnoreferrer']              = false;
	$options['linkaddrdefvalue']                = 'https://';
	$options['userlinkcatselectionlabel']       = __( 'Select a category', 'link-library' );
	$options['dropdownselectionprompt']         = false;
	$options['dropdownselectionprompttext']     = __( 'Select a category', 'link-library' );
	$options['showcatname']                     = false;
	$options['beforecatname']                   = '';
	$options['aftercatname']                    = '';
	$options['onelinkperdomain']                = '';
	$options['showupdatedtooltip']              = false;
	$options['linkimagelabel']                  = __( 'Link Image (jpg, jpeg, png)', 'link-library' ) ;
	$options['showaddlinkimage']                = 'hide';
	$options['datesource']                      = 'updateddate';
	$options['taglist_cpt']                     = '';
	$options['excludetaglist_cpt']              = '';
	$options['taglinks']                        = 'inactive';
	$options['catlistchildcatdepthlimit']       = '';
	$options['linknametooltip']                 = '';
	$options['linkaddrtooltip']                 = '';
	$options['linkrsstooltip']                  = '';
	$options['linkcattooltip']                  = '';
	$options['linkusercattooltip']              = '';
	$options['linktagtooltip']                  = '';
	$options['linkusertagtooltip']              = '';
	$options['linkdesctooltip']                 = '';
	$options['linknotestooltip']                = '';
	$options['linkimagetooltip']                = '';
	$options['linkreciptooltip']                = '';
	$options['linksecondtooltip']               = '';
	$options['linktelephonetooltip']            = '';
	$options['linkemailtooltip']                = '';
	$options['submitternametooltip']            = '';
	$options['submitteremailtooltip']           = '';
	$options['submittercommenttooltip']         = '';
	$options['largedesctooltip']                = '';
	$options['linkfilelabel']                   = '';
	$options['linkfiletooltip']                 = '';
	$options['showaddlinkfile']                 = 'hide';
	$options['linkfileallowedtypes']            = 'pdf,zip';
	$options['displaycustomurl1']               = 'false';
	$options['displaycustomurl2']               = 'false';
	$options['displaycustomurl3']               = 'false';
	$options['displaycustomurl4']               = 'false';
	$options['displaycustomurl5']               = 'false';
	$options['beforecustomurl1']                = '';
	$options['beforecustomurl2']                = '';
	$options['beforecustomurl3']                = '';
	$options['beforecustomurl4']                = '';
	$options['beforecustomurl5']                = '';
	$options['aftercustomurl1']                 = '';
	$options['aftercustomurl2']                 = '';
	$options['aftercustomurl3']                 = '';
	$options['aftercustomurl4']                 = '';
	$options['aftercustomurl5']                 = '';
	$options['labelcustomurl1']                 = '';
	$options['labelcustomurl2']                 = '';
	$options['labelcustomurl3']                 = '';
	$options['labelcustomurl4']                 = '';
	$options['labelcustomurl5']                 = '';
	$options['customurl1target']                = '';
	$options['customurl2target']                = '';
	$options['customurl3target']                = '';
	$options['customurl4target']                = '';
	$options['customurl5target']                = '';
	$options['searchtextinsearchbox']           = false;
	$options['showuservotes']                   = false;
	$options['beforeuservotes']                 = '';
	$options['afteruservotes']                  = '';
	$options['membersonlylinkvotes']            = false;
	$options['uservotelikelabel']               = 'Like';
	$options['searchfiltercats']                = false;
	$options['displaycustomtext1']               = 'false';
	$options['displaycustomtext2']               = 'false';
	$options['displaycustomtext3']               = 'false';
	$options['displaycustomtext4']               = 'false';
	$options['displaycustomtext5']               = 'false';
	$options['beforecustomtext1']                = '';
	$options['beforecustomtext2']                = '';
	$options['beforecustomtext3']                = '';
	$options['beforecustomtext4']                = '';
	$options['beforecustomtext5']                = '';
	$options['aftercustomtext1']                 = '';
	$options['aftercustomtext2']                 = '';
	$options['aftercustomtext3']                 = '';
	$options['aftercustomtext4']                 = '';
	$options['aftercustomtext5']                 = '';
	$options['displaycustomlist1']               = 'false';
	$options['displaycustomlist2']               = 'false';
	$options['displaycustomlist3']               = 'false';
	$options['displaycustomlist4']               = 'false';
	$options['displaycustomlist5']               = 'false';
	$options['beforecustomlist1']                = '';
	$options['beforecustomlist2']                = '';
	$options['beforecustomlist3']                = '';
	$options['beforecustomlist4']                = '';
	$options['beforecustomlist5']                = '';
	$options['aftercustomlist1']                 = '';
	$options['aftercustomlist2']                 = '';
	$options['aftercustomlist3']                 = '';
	$options['aftercustomlist4']                 = '';
	$options['aftercustomlist5']                 = '';
	$options['lazyloadimages']                   = false;

	if ( 'return_and_set' == $setoptions ) {
		$settingsname = 'LinkLibraryPP' . $settings;
		update_option( $settingsname, $options );
	}

	return $options;
}

// Function used to set general initial settings or reset them on user request
function ll_reset_gen_settings( $setoptions = 'return' ) {
	$genoptions['numberstylesets']             = 1;
	$genoptions['includescriptcss']            = '';
	$genoptions['debugmode']                   = false;
	$genoptions['schemaversion']               = '5.0';
	$genoptions['pagetitleprefix']             = '';
	$genoptions['pagetitlesuffix']             = '';
	$genoptions['thumbshotscid']               = '';
	$genoptions['emaillinksubmitter']          = false;
	$genoptions['suppressemailfooter']         = false;
	$genoptions['moderatorname']               = '';
	$genoptions['moderatoremail']              = '';
	$genoptions['approvalemailtitle']          = '';
	$genoptions['approvalemailbody']           = '';
	$genoptions['rejectedemailtitle']          = '';
	$genoptions['rejectedemailbody']           = '';
	$genoptions['moderationnotificationtitle'] = '';
	$genoptions['linksubmissionthankyouurl']   = '';
	$genoptions['usefirstpartsubmittername']   = '';
	$genoptions['recipcheckaddress']           = get_bloginfo( 'wpurl' );
	$genoptions['recipcheckdelete403']         = false;
	$genoptions['imagefilepath']               = 'absolute';
	$genoptions['catselectmethod']             = 'multiselectlist';
	$genoptions['hidedonation']                = false;
	$genoptions['updatechannel']               = 'standard';
	$genoptions['extraprotocols']              = '';
	$genoptions['fullstylesheet']              = '';
	$genoptions['thumbnailsize']               = '120x90';
	$genoptions['thumbnailgenerator']          = 'robothumb';
	$genoptions['survey2015']                  = false;
	$genoptions['addlinkakismet']              = false;
	$genoptions['rsscachedelay']               = 43200;
	$genoptions['single_link_layout']          = '[link_content]';
	$genoptions['captchagenerator']             = 'easycaptcha';
	$genoptions['recaptchasitekey']             = '';
	$genoptions['recaptchasecretkey']           = '';
	$genoptions['rolelevel']                    = 'Administrator';
	$genoptions['editlevel']                    = 'Administrator';
	$genoptions['cptslug']                      = 'links';
	$genoptions['publicly_queryable']           = false;
	$genoptions['exclude_from_search']          = false;
	$genoptions['defaultlinktarget']            = '_blank';
	$genoptions['bp_log_activity']              = false;
	$genoptions['bp_link_page_url']             = '';
	$genoptions['bp_link_settings']             = '';
	$genoptions['defaultprotocoladmin']         = 'http';
	$genoptions['shrinkthewebaccesskey']        = '';
	$genoptions['pagepeekersize']               = 'm';
	$genoptions['pagepeekerid']                 = '';
	$genoptions['stwthumbnailsize']             = '120x90';
	$genoptions['deletelocalfile']              = false;
	$genoptions['customurl1active']             = false;
	$genoptions['customurl2active']             = false;
	$genoptions['customurl3active']             = false;
	$genoptions['customurl4active']             = false;
	$genoptions['customurl5active']             = false;
	$genoptions['customurl1label']              = '';
	$genoptions['customurl2label']              = '';
	$genoptions['customurl3label']              = '';
	$genoptions['customurl4label']              = '';
	$genoptions['customurl5label']              = '';
	$genoptions['dismissll67update']            = '';
	$genoptions['customtext1active']             = false;
	$genoptions['customtext2active']             = false;
	$genoptions['customtext3active']             = false;
	$genoptions['customtext4active']             = false;
	$genoptions['customtext5active']             = false;
	$genoptions['customtext1label']              = '';
	$genoptions['customtext2label']              = '';
	$genoptions['customtext3label']              = '';
	$genoptions['customtext4label']              = '';
	$genoptions['customtext5label']              = '';
	$genoptions['customlist1active']             = false;
	$genoptions['customlist2active']             = false;
	$genoptions['customlist3active']             = false;
	$genoptions['customlist4active']             = false;
	$genoptions['customlist5active']             = false;
	$genoptions['customlist1label']              = '';
	$genoptions['customlist2label']              = '';
	$genoptions['customlist3label']              = '';
	$genoptions['customlist4label']              = '';
	$genoptions['customlist5label']              = '';
	$genoptions['customlist1values']             = '';
	$genoptions['customlist2values']             = '';
	$genoptions['customlist3values']             = '';
	$genoptions['customlist4values']             = '';
	$genoptions['customlist5values']             = '';
	$genoptions['customlist1html']               = '';
	$genoptions['customlist2html']               = '';
	$genoptions['customlist3html']               = '';
	$genoptions['customlist4html']               = '';
	$genoptions['customlist5html']               = '';

	if ( 'return_and_set' == $setoptions ) {
		$stylesheetlocation           = plugins_url( 'stylesheettemplate.css', __FILE__ );
		$genoptions['fullstylesheet'] = @file_get_contents( $stylesheetlocation );

		update_option( 'LinkLibraryGeneral', $genoptions );
	}

	return $genoptions;
}

function ll_modify_layout( $settings, $newlayout ) {
	$settingsname = 'LinkLibraryPP' . $settings;
	$options      = get_option( $settingsname );

	if ( 1 == $newlayout || 2 == $newlayout) {
		// Layout 1: Simple Unordered List
		// Layout 2: Unordered list with link descriptions
		$options['displayastable']    = 'false';
		$options['showcolumnheaders'] = false;
		$options['columnheaderoverride'] = '';
		$options['linkaddfrequency']     = 0;

		$options['beforefirstlink']   = '';
		$options['afterlastlink']     = '';

		$options['beforeitem']        = '<li>';
		$options['afteritem']         = '</li>';

		$options['showname']          = true;
		$options['beforelink']        = '';
		$options['afterlink']         = '';

		if ( 1 == $newlayout ) {
			$options['showdescription']   = false;
		} else {
			$options['showdescription']   = true;
		}

		$options['beforedesc']        = '';
		$options['afterdesc']         = '';

		$options['shownotes']         = false;
		$options['beforenote']        = '';
		$options['afternote']         = '';

		$options['show_images']         = false;
		$options['beforeimage']       = '';
		$options['afterimage']        = '';

		$options['showdate']          = false;
		$options['beforedate']        = '';
		$options['afterdate']         = '';

		$options['showlargedescription']    = false;
		$options['beforelargedescription']  = '';
		$options['afterlargedescription']   = '';

		$options['displayweblink']    = 'false';
		$options['beforeweblink']     = '';
		$options['afterweblink']      = '';

		$options['showtelephone']     = 'false';
		$options['beforetelephone']   = '';
		$options['aftertelephone']    = '';

		$options['showemail']         = 'false';
		$options['beforeemail']       = '';
		$options['afteremail']        = '';

		$options['showlinkhits']      = false;
		$options['beforelinkhits']    = '';
		$options['afterlinkhits']     = '';

		$options['showrating']        = false;
		$options['beforelinkrating']  = '';
		$options['afterlinkrating']   = '';

		$options['showsubmittername']   = false;
		$options['beforesubmittername'] = '';
		$options['aftersubmittername']  = '';

		$options['showcatdesc']      = false;
		$options['beforecatdesc']    = '';
		$options['aftercatdesc']     = '';

		$options['showlinktags']      = false;
		$options['beforelinktags']    = '';
		$options['afterlinktags']     = '';

		$options['showlinkprice']      = false;
		$options['beforelinkprice']    = '';
		$options['afterlinkprice']     = '';

		$options['showcatname']      = false;
		$options['beforecatname']    = '';
		$options['aftercatname']     = '';

		$options['beforerss']         = '';
		$options['afterrss']          = '';

		$options['addbeforelink']   = '';
		$options['addafterlink']    = '';
	} elseif ( 3 == $newlayout ) {

		$options['displayastable']    = 'true';
		$options['showcolumnheaders'] = true;
		$options['columnheaderoverride'] = 'Link,Description';
		$options['linkaddfrequency']     = 0;

		$options['beforefirstlink']   = '';
		$options['afterlastlink']     = '';

		$options['beforeitem']        = '<tr>';
		$options['afteritem']         = '</tr>';

		$options['showname']          = true;
		$options['beforelink']        = '<td>';
		$options['afterlink']         = '</td>';

		$options['showdescription']   = true;

		$options['beforedesc']        = '<td>';
		$options['afterdesc']         = '</td>';

		$options['shownotes']         = false;
		$options['beforenote']        = '<td>';
		$options['afternote']         = '</td>';

		$options['show_images']         = false;
		$options['beforeimage']       = '<td>';
		$options['afterimage']        = '</td>';

		$options['showdate']          = false;
		$options['beforedate']        = '<td>';
		$options['afterdate']         = '</td>';

		$options['showlargedescription']    = false;
		$options['beforelargedescription']  = '<td>';
		$options['afterlargedescription']   = '</td>';

		$options['displayweblink']    = 'false';
		$options['beforeweblink']     = '<td>';
		$options['afterweblink']      = '</td>';

		$options['showtelephone']     = 'false';
		$options['beforetelephone']   = '<td>';
		$options['aftertelephone']    = '</td>';

		$options['showemail']         = 'false';
		$options['beforeemail']       = '<td>';
		$options['afteremail']        = '</td>';

		$options['showlinkhits']      = false;
		$options['beforelinkhits']    = '<td>';
		$options['afterlinkhits']     = '</td>';

		$options['showrating']        = false;
		$options['beforelinkrating']  = '<td>';
		$options['afterlinkrating']   = '</td>';

		$options['showsubmittername']   = false;
		$options['beforesubmittername'] = '<td>';
		$options['aftersubmittername']  = '</td>';

		$options['showcatdesc']      = false;
		$options['beforecatdesc']    = '<td>';
		$options['aftercatdesc']     = '</td>';

		$options['showlinktags']      = false;
		$options['beforelinktags']    = '<td>';
		$options['afterlinktags']     = '</td>';

		$options['showlinkprice']      = false;
		$options['beforelinkprice']    = '<td>';
		$options['afterlinkprice']     = '</td>';

		$options['showcatname']      = false;
		$options['beforecatname']    = '<td>';
		$options['aftercatname']     = '</td>';

		$options['beforerss']         = '<td>';
		$options['afterrss']          = '</td>';

		$options['addbeforelink']   = '';
		$options['addafterlink']    = '';
	} elseif ( 4 == $newlayout || 5 == $newlayout ) {

		$options['displayastable']    = 'true';
		$options['showcolumnheaders'] = false;
		$options['columnheaderoverride'] = 'Link,Description';
		$options['linkaddfrequency']     = 2;

		$options['beforefirstlink']   = '';
		$options['afterlastlink']     = '';

		$options['beforeitem']        = '';
		$options['afteritem']         = '';

		$options['showname']          = true;

		if ( 4 == $newlayout ) {
			$options['beforelink'] = '';
		} elseif ( 5 == $newlayout ) {
			$options['beforelink'] = '<td>';
		}
		$options['afterlink']         = '<br />';

		$options['showdescription']   = true;
		$options['beforedesc']        = '';
		$options['afterdesc']         = '<br />';

		$options['shownotes']         = false;
		$options['beforenote']        = '';
		$options['afternote']         = '<br />';

		$options['show_images']         = true;

		if ( 4 == $newlayout ) {
			$options['beforeimage']       = "<td style='width:50%'>";
			$options['afterimage']        = '<br />';
		} elseif ( 5 == $newlayout ) {
			$options['beforeimage']       = "<td style='width:100px'>";
			$options['afterimage']        = '</td>';
		}

		$options['showdate']          = false;
		$options['beforedate']        = '';
		$options['afterdate']         = '<br />';

		$options['showlargedescription']    = false;
		$options['beforelargedescription']  = '';
		$options['afterlargedescription']   = '<br />';

		$options['displayweblink']    = 'false';
		$options['beforeweblink']     = '';
		$options['afterweblink']      = '<br />';

		$options['showtelephone']     = 'false';
		$options['beforetelephone']   = '';
		$options['aftertelephone']    = '<br />';

		$options['showemail']         = 'false';
		$options['beforeemail']       = '';
		$options['afteremail']        = '<br />';

		$options['showlinkhits']      = false;
		$options['beforelinkhits']    = '';
		$options['afterlinkhits']     = '<br />';

		$options['showrating']        = false;
		$options['beforelinkrating']  = '';
		$options['afterlinkrating']   = '<br />';

		$options['showsubmittername']   = false;
		$options['beforesubmittername'] = '';
		$options['aftersubmittername']  = '<br />';

		$options['showcatdesc']      = false;
		$options['beforecatdesc']    = '';
		$options['aftercatdesc']     = '<br />';

		$options['showlinktags']      = false;
		$options['beforelinktags']    = '';
		$options['afterlinktags']     = '<br />';

		$options['showlinkprice']      = false;
		$options['beforelinkprice']    = '';
		$options['afterlinkprice']     = '<br />';

		$options['showcatname']      = false;
		$options['beforecatname']    = '';
		$options['aftercatname']     = '<br />';

		$options['beforerss']         = '';
		$options['afterrss']          = '<br />';

		$options['addbeforelink']   = '<tr>';
		$options['addafterlink']    = '</tr>';
	}

	update_option( $settingsname, $options );

	return $options;
}
