(function(){
	const validateEditSettingAgainstReadSetting = () => {
		const editSettings = document.querySelectorAll( '#doc-edit-settings input');
		if ( ! editSettings ) {
			return;
		}

		// Un-disable all edit settings. We will re-disable them if needed.
		editSettings.forEach( setting => {
			setting.disabled = false;
		} )

		const readSettingSelected = document.querySelector( 'input[name="doc[view_setting]"]:checked' );

		if ( ! readSettingSelected ) {
			return;
		}

		if ( 'admins' !== readSettingSelected.value ) {
			return;
		}

		const editSettingSelected = document.querySelector( 'input[name="doc[edit_setting]"]:checked' );

		if ( ! editSettingSelected ) {
			return;
		}

		// If the edit setting is not 'admins', set it to 'admins'.
		if ( 'admins' !== editSettingSelected.value ) {
			document.querySelector( '#doc-edit-setting-admins' ).checked = true;
		}

		// Disable other edit options.
		editSettings.forEach( setting => {
			if ( setting.value !== 'admins' ) {
				setting.disabled = true;
			}
		} );
	}

	document.addEventListener('DOMContentLoaded', function(){
		const readSettingRadios = document.querySelectorAll( 'input[name="doc[view_setting]"]' );
		readSettingRadios.forEach( radio => {
			radio.addEventListener( 'change', validateEditSettingAgainstReadSetting );
		} )

		validateEditSettingAgainstReadSetting();
	});
})();
