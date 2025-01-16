const prevBtns = document.querySelectorAll(".nextgen-gallery-onboarding-btn-prev");
const nextBtns = document.querySelectorAll(".nextgen-gallery-onboarding-btn-next");
const progress = document.querySelector(".nextgen-gallery-onboarding-progress");
const formSteps = document.querySelectorAll(".nextgen-gallery-onboarding-form-step");
const progressSteps = document.querySelectorAll(".nextgen-gallery-onboarding-progress-step");

let formStepsNum = 0;


/* Event Listener for Next Button. */
nextBtns.forEach((btn) => {
	btn.addEventListener("click", () => {
		// Get data-next attribute from the button and set it as stepsNum.

		if(formStepsNum === 0){

			saveFormData();

		}else {
			let nextStep = btn.getAttribute("data-next");
			if(nextStep){
				formStepsNum = nextStep;
				updateFormSteps();
				updateProgressbar();
			}
		}

	});

});

/* Event Listener for Back Button. */
prevBtns.forEach((btn) => {
	btn.addEventListener("click", () => {
	// Get data-prev attribute from the button and set it as stepsNum.
	let prevStep = btn.getAttribute("data-prev");
	if(prevStep){
		formStepsNum = prevStep;
		updateFormSteps();
		updateProgressbar();
	}
	});
});

/* Updates Form Items */
function updateFormSteps() {
	formSteps.forEach((formStep) => {
		formStep.classList.contains("nextgen-gallery-onboarding-form-step-active") &&
		formStep.classList.remove("nextgen-gallery-onboarding-form-step-active")
	})
	formSteps[formStepsNum].classList.add("nextgen-gallery-onboarding-form-step-active");
	// Show selected plugins div only on step 2.
	if(formStepsNum === "2"){
		selectedPluginsdiv.style.display = "block";
	}else{
		selectedPluginsdiv.style.display = "none";
	}

 }

/* Updates Progress Bar */
function updateProgressbar() {

	progressSteps.forEach((progressStep, index) => {
		let spacer = progressStep.previousElementSibling;

		if(index <= formStepsNum){

			progressStep.classList.add('nextgen-gallery-onboarding-progress-step-active');
			spacer.style.borderColor = '#a0bc1a';

		} else {
			progressStep.classList.remove('nextgen-gallery-onboarding-progress-step-active')
			spacer.style.borderColor = '#DCDDE1'

		}
	})
	progress.style.width = ((formStepsNum) / (progressSteps.length - 1)) * 100 + "%";

}



// when nextgen-gallery-onboarding-back-to-welcome or nextgen-gallery-get-started-btn is clicked, show and hide the onboarding wizard pages.
const backToWelcomeBtn = document.querySelector("#nextgen-gallery-onboarding-back-to-welcome");
const getStartedBtn = document.querySelector("#nextgen-gallery-get-started-btn");

backToWelcomeBtn.addEventListener("click", () => {
	document.querySelector(".nextgen-gallery-onboarding-wizard-intro").style = {display: "flex"};
	document.querySelector('.nextgen-gallery-onboarding-wizard-wrapper').style = 'height: 100vh';
	document.querySelector(".nextgen-gallery-onboarding-wizard-pages").style.display = "none";
});

getStartedBtn.addEventListener("click", () => {
	document.querySelector(".nextgen-gallery-onboarding-wizard-intro").style.display = "none";
	document.querySelector('.nextgen-gallery-onboarding-wizard-wrapper').style = 'height: auto';
	document.querySelector(".nextgen-gallery-onboarding-wizard-pages").style = {display: "flex"};
});

// Disable click on no-clickable checkboxes with class no-clicks.

const noClicks = document.querySelectorAll(".no-clicks");
noClicks.forEach((noClick) => {
	noClick.addEventListener("click", (e) => {
		e.preventDefault();
		e.stopPropagation()
	});
});


// set height on page load.
document.querySelector('.nextgen-gallery-onboarding-wizard-wrapper').style = 'height: 85vh';

function isValidEmail(email) {
	let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	return emailRegex.test(email);
}

// Check if email_address is valid.
const email = document.querySelector("#email_address");
const saveOptIn = document.querySelector("#save-opt-in");
let emailError = document.querySelector('.nextgen-gallery-email-error');
emailError.innerHTML = "";

const setEmailValid = () => {
	emailError.innerHTML = "";
	saveOptIn.disabled = false;
	saveOptIn.classList.remove("nextgen-gallery-disabled");
}

const setEmailInvalid = () => {
	emailError.innerHTML = "Please enter a valid email address.";
	saveOptIn.disabled = true;
	saveOptIn.classList.add("nextgen-gallery-disabled");
}

// If the user opt in for email, then check if the email is valid or not empty.

let email_opt_in = document.querySelector("#email_opt_in");

email_opt_in.addEventListener("change", (e) => {
	emailValidation();
});

let emailValidation = () => {

	// If email opt in is checked and email is invalid, show error message.
	if( ( email.value === "" || ! isValidEmail(email.value) ) && email_opt_in.checked ){
		setEmailInvalid();
	}
	// If email opt in is not checked and email is invalid, show error message.
	if( email.value !== "" && ! isValidEmail(email.value) && ! email_opt_in.checked ){
		setEmailInvalid();
	}
	// If email opt in is not checked and email is valid or empty, remove the error message.
	if( ( email.value !== "" && isValidEmail(email.value) ) || email.value === "" && ! email_opt_in.checked){
		setEmailValid();
	}
}

email.addEventListener("input", (e) => {
		emailValidation();
});

// Show others option when something else is selected for Drip.
// if user_type radio buttons are not checked disable the save button.
const userTypes = document.querySelectorAll("input[name='eow[_user_type]']");

userTypes.forEach((userType) => {
	userType.addEventListener("change", (e) => {
		if(e.target.value === "other"){
			document.querySelector("#others_div").style.display = "block";
			document.querySelector("#others").required = true;
		} else {
			document.querySelector("#others_div").style.display = "none";
			document.querySelector("#others").required = false;
		}

		if(e.target.value === "online-store"){
			document.querySelector("#ngg-pro-upsell").style.display = "block";
		} else {
			document.querySelector("#ngg-pro-upsell").style.display = "none";
		}

		let isAnyUserTypeChecked = Array.from(userTypes).some(radio => radio.checked);

		if(!isAnyUserTypeChecked){
			saveOptIn.disabled = true;
			saveOptIn.classList.add("nextgen-gallery-disabled");
		} else {
			saveOptIn.disabled = false;
			saveOptIn.classList.remove("nextgen-gallery-disabled");
		}
	});
});

function saveFormData() {

	// post form data via WP admin-ajax. nggOnboardingWizard.ajaxUrl,
	const form = document.querySelector("#nextgen-gallery-general");
	// Disable form submit.
	form.addEventListener("submit", async(e) => {
		e.preventDefault();
		e.stopPropagation();

		const formData = new FormData(form);

		formData.append("action", "save_onboarding_data");
		formData.append("nonce", nggOnboardingWizard.nonce);
		try{

			const response = await fetch(nggOnboardingWizard.ajaxUrl, {
				method: "POST",
				body: formData
			});
			const data = await response.json();
			if(data.success){
				formStepsNum = 1;
				updateFormSteps();
				updateProgressbar();
			} else {
				formStepsNum = 0;
				console.log("Error saving the data");
			}
		} catch(error){
			formStepsNum = 0;
			console.log("Error:", error);
		}

	});

}
// Get all the checkboxes with the class feature.
const features = document.querySelectorAll(".feature");
let selectedFeatures = [];
features.forEach((feature) => {
	feature.addEventListener("click", (e) => {
		if(e.target.checked){
			if(!selectedFeatures.includes(e.target.value)){
				selectedFeatures.push(e.target.value);
			}
			document.querySelector(`#${e.target.value}-desc`).style.display = "block";
		} else {
			selectedFeatures = selectedFeatures.filter((feature) => feature !== e.target.value);
			document.querySelector(`#${e.target.value}-desc`).style.display = "none";
		}
	});
});

// Save selected features to the database.
const saveFeaturesBtn = document.querySelector("#nextgen-gallery-save-features");
saveFeaturesBtn.addEventListener("click", () => {

	// if user has not selected any features, return.
	if(selectedFeatures.length === 0){
		return;
	}

	let requestData = {
		action: "save_selected_addons",
		addons:selectedFeatures,
		nonce: nggOnboardingWizard.nonce,
	};

	fetch(nggOnboardingWizard.ajaxUrl, {
		method:"post",
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: new URLSearchParams(requestData).toString(),
	}).then((response) => response.json())
		.then((data) => {
			if(data.success){
				// Show success message.
			}
		})
		.catch((error) => {
			console.error("Error:", error);
		});
	displaySelectedAddons();
});

// Get all the checkboxes with the class recommended.
const recommendedPlugins = document.querySelectorAll(".recommended");
let selectedRecommended = [];
let selectedPluginsdiv = document.querySelector(".selected-plugins-names");
let selectedPluginsNames =[];
recommendedPlugins.forEach((recommended) => {
	recommended.addEventListener("click", (e) => {
		if(e.target.checked){
			selectedRecommended.push(e.target.value);
			selectedPluginsNames.push(e.target.getAttribute("data-name"));
			document.querySelector(`#${e.target.value}-desc`).style.display = "block";
		} else {
			selectedRecommended = selectedRecommended.filter((recommended) => recommended !== e.target.value);
			selectedPluginsNames = selectedPluginsNames.filter((name) => name !== e.target.getAttribute("data-name"));
			document.querySelector(`#${e.target.value}-desc`).style.display = "none";
		}
		displaySelectedPlugins();
	});
});

// Display selected recommended plugins based on selection.
let displaySelectedPlugins = () => {
	selectedPluginsdiv.innerHTML = "";

	if(selectedPluginsNames.length === 0){
		// check if there are any selected recommended plugins.
		recommendedPlugins.forEach((recommended) => {
			// get the checked recommended plugins that are not in the selectedRecommended array.
			if(recommended.checked && !selectedRecommended.includes(recommended.value) && !nggOnboardingWizard.plugins_list.includes(recommended.value)){
				selectedPluginsNames.push(recommended.getAttribute("data-name"));
				document.querySelector(`#${recommended.value}-desc`).style.display = "block";
			}
		});
	}

	if(selectedPluginsNames.length > 0){
		selectedPluginsdiv.innerHTML = "The following plugins will be installed: ";
	}

	selectedPluginsNames.forEach((name) => {
		let plugin = document.createElement("span");
		plugin.innerHTML = `${name}`;
		selectedPluginsdiv.appendChild(plugin);
		// Append comma after each plugin name but not after the last plugin name.
		if(selectedPluginsNames.indexOf(name) !== selectedPluginsNames.length - 1){
			let comma = document.createElement("span");
			comma.innerHTML = ", ";
			selectedPluginsdiv.appendChild(comma);
		}
	});
}

displaySelectedPlugins();

// Install the selected recommended plugins.
const installBtn = document.querySelector("#nextgen-gallery-install-recommended");
installBtn.addEventListener("click", () => {

	if(selectedRecommended.length === 0){
		// check if there are any selected recommended plugins.
		recommendedPlugins.forEach((recommended) => {
			// get the checked recommended plugins that are not in the selectedRecommended array.
			if(recommended.checked && !selectedRecommended.includes(recommended.value)){
				selectedRecommended.push(recommended.value);
			}
		});
	}

	let requestData = {
		action: "install_recommended_plugins",
		plugins:selectedRecommended,
		nonce: nggOnboardingWizard.nonce,
	};

	fetch(nggOnboardingWizard.ajaxUrl, {
		method:"post",
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: new URLSearchParams(requestData).toString(),
	}).then((response) => response.json())
		.then((data) => {
			if(data.success){
				 // Show success message.
			}
		})
		.catch((error) => {
			console.error("Error:", error);
		});
});

// Insert selected addons to #selected-add-ons div.

let tickSvg = `<svg class=nextgen-gallery-checkmark fill=none viewBox="0 0 14 11" xmlns=http://www.w3.org/2000/svg>
<path clip-rule=evenodd d="M10.8542 1.37147C11.44 0.785682 12.3897 0.785682 12.9755 1.37147C13.5613 1.95726 13.5613 2.907 12.9755 3.49279L6.04448 10.4238C5.74864 10.7196 5.35996 10.8661 4.97222 10.8631C4.58548 10.8653 4.19805 10.7189 3.90298 10.4238L1.0243 7.5451C0.438514 6.95931 0.438514 6.00956 1.0243 5.42378C1.61009 4.83799 2.55983 4.83799 3.14562 5.42378L4.97374 7.2519L10.8542 1.37147Z" fill=currentColor fill-rule=evenodd></path>
</svg>`;

let selectedAddons = document.querySelector("#selected-add-ons");
function displaySelectedAddons() {
	// Find all the selected-addon-item divs and remove them, to avoid duplicates.
	let selectedAddonItems = document.querySelectorAll(".selected-addon-item");
	selectedAddonItems.forEach((item) => {
		item.remove();
	});

	// Find all checkbox with class .feature and check if they are checked.
	let nggFeatures = document.querySelectorAll(".feature");

	nggFeatures.forEach((feature) => {
		if(feature.checked){
			const addon = document.createElement("div");
			addon.classList.add("nextgen-gallery-col", "col-sm-6", "col-xs-12", "nextgen-gallery-col", "text-xs-left","selected-addon-item");
			// Get data-name attribute from the checkbox by its name.
			let addonName = document.querySelector(`input[name="${feature.name}"]`).getAttribute("data-name");
			if(addonName !== "")
			{
				addon.innerHTML = `${tickSvg}${addonName}</div>`
				selectedAddons.appendChild(addon);
			}
		}
	});
}


// Verify license key.
const verifyBtn = document.querySelector(".nextgen-gallery-verify-submit");
const successMessage = document.querySelector("#license-key-message");
const installAddonsBtn = document.querySelector("#install-nextgen-gallery-addons-btn");
let loadingSpinner = document.querySelector(".nextgen-gallery-onboarding-spinner");
verifyBtn.addEventListener("click", (e) => {
	e.preventDefault();
	// Show spinner.
	loadingSpinner.style.visibility = "visible";
	verifyBtn.classList.add("nextgen-gallery-disabled");
	// disable the continue button.
	installAddonsBtn.disabled = true;
	installAddonsBtn.classList.add("nextgen-gallery-disabled");
	successMessage.classList.remove("nextgen-gallery-success", "nextgen-gallery-error");

	successMessage.innerHTML = "";

	let toggleButtonsVisibility = () => {
		loadingSpinner.style.visibility = "hidden";
		verifyBtn.disabled = false;
		installAddonsBtn.disabled = false;
		installAddonsBtn.classList.remove("nextgen-gallery-disabled");
		verifyBtn.classList.remove("nextgen-gallery-disabled");
	}

	let licenseKey = document.getElementById('nextgen-gallery-settings-key').value;

	if(licenseKey === ''){
		successMessage.classList.add("nextgen-gallery-error");
		successMessage.innerHTML = "Please enter your license key.";
		toggleButtonsVisibility();
		return;
	}

	let requestData = {
		action: 'ngg_plugin_verify_license_key',
		'nextgen-gallery-license-key': licenseKey,
		nonce: nggOnboardingWizard.nonce,
	};





	fetch(nggOnboardingWizard.ajaxUrl, {
		method:"post",
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: new URLSearchParams(requestData).toString(),
	}).then((response) => response.json())
		.then((data) => {
			if(data.success){
				successMessage.classList.add("nextgen-gallery-success");
				successMessage.innerHTML = data.data;
				toggleButtonsVisibility();
			}else{
				successMessage.classList.add("nextgen-gallery-error");
				successMessage.innerHTML = data.data ?? data.error;
				toggleButtonsVisibility();
			}
		})
		.catch((error) => {
			successMessage.classList.add("nextgen-gallery-error");
			successMessage.innerHTML = data.data;
			toggleButtonsVisibility();
			console.log("Error:", error);
		});
});

// Show body content once it is loaded.
let domReady = (cb) => {
	document.readyState === 'interactive' || document.readyState === 'complete'
		? cb()
		: document.addEventListener('DOMContentLoaded', cb);
};

domReady(() => {
	// Display body when DOM is loaded
	document.body.style.visibility = 'visible';
});


