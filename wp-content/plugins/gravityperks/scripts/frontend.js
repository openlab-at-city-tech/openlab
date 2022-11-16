if (typeof gperk == 'undefined') {
	var gperk = {};
}

gperk.getProductQuantity = function(formId, productFieldId) {

	var quantity;
	var quantityInput = jQuery( "#ginput_quantity_" + formId + "_" + productFieldId );

	if (quantityInput.length > 0) {
		quantity = ! gformIsNumber( quantityInput.val() ) ? 0 : quantityInput.val();
	} else {
		quantityElement = jQuery( ".gfield_quantity_" + formId + "_" + productFieldId );

		quantity = 1;
		if (quantityElement.find( "input" ).length > 0) {
			quantity = quantityElement.find( "input" ).val();
		} else if (quantityElement.find( "select" ).length > 0) {
			quantity = quantityElement.find( "select" ).val();
		}

		if ( ! gformIsNumber( quantity )) {
			quantity = 0
		}
	}
	quantity = parseFloat( quantity );

	//setting global variable if quantity is more than 0 (a product was selected). Will be used when calculating total
	if (quantity > 0) {
		_anyProductSelected = true;
	}

	return quantity;

}
