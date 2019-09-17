// Version 1.0 - original version
// Version 1.1 - Updated with function fixes and for WordPress 3.2 / jQuery 1.6
// Version 1.2 - Update to work when DISABLED is specified for changes in version 8.5
// Version 1.3 - Update for Subscribe2 9.0 to remove unecessary code now WordPress 3.3 is minimum requirement
// Version 1.4 - eslinted

jQuery( document ).ready(
	function() {

			// function to check or uncheck all when 'checkall' box it toggled
			jQuery( 'input[name="checkall"]' ).click(
				function() {
					var checkedStatus = this.checked;
					jQuery( 'input[class="' + this.value + '"]' ).each(
						function() {
							if ( false === jQuery( this ).prop( 'disabled' ) ) {
								this.checked = checkedStatus;
							}
						}
					);
				}
			);

			// function to check or uncheck 'checkall' box when individual boxes are toggled
			jQuery( 'input[class^="checkall"]' ).click(
				function() {
					var checkedStatus = true;
					jQuery( 'input[class="' + this.className + '"]' ).each(
						function() {
							if ( ( true === this.checked ) && ( true === checkedStatus ) ) {
								checkedStatus = true;
							} else {
								checkedStatus = false;
							}
							jQuery( 'input[value="' + this.className + '"]' )
							.prop( 'checked', checkedStatus );
						}
					);
				}
			);

			// function to check or uncheck 'checkall' box when page is loaded
			jQuery( 'input[class^="checkall"]' ).each(
				function() {
					var checkedStatus = true;
					if ( ( true === this.checked ) && ( true === checkedStatus ) ) {
						checkedStatus = true;
					} else {
						checkedStatus = false;
					}
					jQuery( 'input[value="' + this.className + '"]' )
					.prop( 'checked', checkedStatus );
				}
			);
	}
);
