Survey.StylesManager.applyTheme("winter");

let json = {
	//showQuestionNumbers: "off",
	showNavigationButtons: false,
	goNextPageAutomatic: true,
	questions: [{
		type: "html",
		name: "info",
		html: '<p>' + CAC_Creative_Commons.text.intro + '</p><p>' + CAC_Creative_Commons.text.intro2 + '</p>'
	}, {
		type: "radiogroup",
		name: "publicDomain",
		description: CAC_Creative_Commons.questions.publicDomainDesc,
		title: CAC_Creative_Commons.questions.publicDomain,
		isRequired: true,
		choices: [
			{value: 'yes', text: CAC_Creative_Commons.answers.yes },
			{value: 'no', text: CAC_Creative_Commons.answers.no}
		],
	}, {
		type: "radiogroup",
		name: "derivative",
		title: CAC_Creative_Commons.questions.derivative,
		description: CAC_Creative_Commons.questions.derivativeDesc,
		visibleIf: "{publicDomain}='no'",
		isRequired: true,
		choices: [
			{value: 'yes', text: CAC_Creative_Commons.answers.yes },
			{value: 'no', text: CAC_Creative_Commons.answers.no},
			{value: 'share', text: CAC_Creative_Commons.answers.share}
		],
	}, {
		type: "radiogroup",
		name: "commercial",
		title: CAC_Creative_Commons.questions.commercial,
		description: CAC_Creative_Commons.questions.commercialDesc,
		visibleIf: "{publicDomain}='no'",
		isRequired: true,
		choices: [
			{value: 'yes', text: CAC_Creative_Commons.answers.yes },
			{value: 'no', text: CAC_Creative_Commons.answers.no}
		],
	}],
	completedHtml: '<h3 style="margin-bottom:1em">' + CAC_Creative_Commons.text.selected + '</h3><h4 style="margin-bottom:.5em">' + CAC_Creative_Commons.licenses.by + '</h4><a class="license-link" href="https://creativecommons.org/licenses/by/' + CAC_Creative_Commons.versions.current + '/" target="_blank"><img class="license-logo" src="https://creativecommons.org/wp-content/themes/creativecommons.org/images/chooser_cc.png" style="border-width:0" /></a><div id="free-culture" style="margin-top:1em"><p><a href="https://creativecommons.org/freeworks" target="_blank">' + CAC_Creative_Commons.text.freeCulture + '</a></p><img src="https://creativecommons.org/wp-content/themes/creativecommons.org/images/fc_approved_tiny.png" style="border-width:0" /></div><div id="not-free-culture" style="display:none;margin-top:1em"><p><a href="https://creativecommons.org/freeworks" target="_blank">' + CAC_Creative_Commons.text.notFreeCulture + '</a></p><img src="https://creativecommons.org/wp-content/themes/creativecommons.org/images/fc_dubious.png" style="border-width:0" /></div>'
};

window.survey = new Survey.Model(json);

survey.onComplete.add(function(result) {
	var logo = jQuery("img[data-license]"),
		link = jQuery("a[rel='license']"),
		currentLicense = logo.attr('data-license'),
		selectedContainer = jQuery("div.sv_completed_page"),
		selectedHeader = selectedContainer.find('h4'),
		selectedLogo = selectedContainer.find('img.license-logo'),
		selectedLink = selectedContainer.find('a.license-link'),
		freeCulture = selectedContainer.find( '#free-culture' ),
		notFreeCulture = selectedContainer.find( '#not-free-culture' ),
		newLicense;

	if( 'derivative' in result.data ){
		newLicense = CAC_Creative_Commons.chooser[result.data.derivative][result.data.commercial];
	} else {
		newLicense = 'zero';
	}

	// Data
	jQuery( '#cac-cc-license' ).val( newLicense );
	logo.attr( 'data-license', newLicense );

	// Logo
	logo.attr( 'src', logo.attr('src').replace( '/' + currentLicense + '/', '/' + newLicense + '/' ) );
	selectedLogo.attr( 'src', logo.attr('src').replace( '/' + currentLicense + '/', '/' + newLicense + '/' ) );

	// Link
	selectedLink.attr( 'href', selectedLink.attr('href').replace( '/' + currentLicense + '/', '/' + newLicense + '/' ) );
	if ( link.length ) {
		link.attr( 'href', link.attr('href').replace( '/' + currentLicense + '/', '/' + newLicense + '/' ) );
		if ( 0 === link.data( 'logo' ) ) {
			link.text( CAC_Creative_Commons.licenses[newLicense] );
		}
	}

	// Modal header
	selectedHeader.text( CAC_Creative_Commons.licenses[newLicense] );

	// CC0 requires switching the version number as well.
	if ( 'zero' === newLicense ) {
		// Logo
		logo.attr( 'src', logo.attr('src').replace( CAC_Creative_Commons.versions.current, CAC_Creative_Commons.versions.zero ) );
		selectedLogo.attr( 'src', logo.attr('src').replace( CAC_Creative_Commons.versions.current, CAC_Creative_Commons.versions.zero ) );

		// Link
		selectedLink.attr( 'href', selectedLink.attr('href').replace( CAC_Creative_Commons.versions.current, CAC_Creative_Commons.versions.zero ) );
		if ( link.length ) {
			link.attr( 'href', link.attr('href').replace( CAC_Creative_Commons.versions.current, CAC_Creative_Commons.versions.zero ) );
		}

	// Switching back to a regular Creative Commons license.
	} else if ( 'zero' !== newLicense && 'zero' === currentLicense ) {
		// Logo
		logo.attr( 'src', logo.attr('src').replace( CAC_Creative_Commons.versions.zero, CAC_Creative_Commons.versions.current ) );
		selectedLogo.attr( 'src', logo.attr('src').replace( CAC_Creative_Commons.versions.zero, CAC_Creative_Commons.versions.current ) );

		// Link
		selectedLink.attr( 'href', selectedLink.attr('href').replace( CAC_Creative_Commons.versions.zero, CAC_Creative_Commons.versions.current ) );
		if ( link.length ) {
			link.attr( 'href', link.attr('href').replace( CAC_Creative_Commons.versions.zero, CAC_Creative_Commons.versions.current ) );
		}
	}

	// Free Culture license
	if ( 'by' === newLicense || 'by-sa' === newLicense || 'zero' === newLicense ) {
		freeCulture.show();
		notFreeCulture.hide();
	} else {
		freeCulture.hide();
		notFreeCulture.show();
	}
});

/*
// Create showdown markdown converter
var converter = new showdown.Converter();
survey.onTextMarkdown.add(function(survey, options){
	var str = converter.makeHtml(options.text);

	// Remove root paragraphs <p></p>
	str = str.substring(3);
	str = str.substring(0, str.length - 4);

	options.html = str;
});
*/

jQuery(function(){
	var logo = jQuery("img[data-license]");

	jQuery("#cac-cc-survey").Survey({model: survey});

	jQuery( window ).on( 'tb_unload', function( event ) {
		survey.clear();
	});

	jQuery('#cac-cc-default-size').on('change', function() {
		var current = logo.attr( 'data-size' );
		logo.attr( 'data-size', this.value );
		logo.attr( 'src', logo.attr('src').replace( CAC_Creative_Commons.sizes[current], CAC_Creative_Commons.sizes[this.value] ) );
	});
});