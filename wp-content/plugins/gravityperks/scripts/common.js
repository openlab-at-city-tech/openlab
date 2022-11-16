if (typeof gperk == 'undefined') {
	var gperk = {};
}

gperk.is = function(variable, type) {
	return typeof variable == type;
}

gperk.isUndefined = function(variable) {
	return gperk.is( variable, 'undefined' );
}

gperk.isSet = function( variable ) {
	return gperk.is( variable, 'undefined' );
}

gperk.ajaxSpinner = function(elem, imageSrc) {

	var imageSrc = typeof imageSrc == 'undefined' ? gperk.gformBaseUrl + '/images/loading.gif' : imageSrc;

	this.elem  = elem;
	this.image = '<img src="' + imageSrc + '" />';

	this.init = function() {
		this.spinner = jQuery( this.image );
		jQuery( this.elem ).after( this.spinner );
		return this;
	}

	this.destroy = function() {
		jQuery( this.spinner ).remove();
	}

	return this.init();
}

gperk.applyFilter = function(filterName, value) {
	return typeof window[filterName] != 'undefined' ? window[filterName]( value ) : value;
}
