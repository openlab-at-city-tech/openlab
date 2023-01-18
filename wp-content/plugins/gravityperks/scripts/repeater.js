/**
* jQuery Repeater
*
* Easily create a section of repeatable items.
*
*/

jQuery.fn.repeater = function(options) {

	var defaults = {
		template: '',
		limit: 5,
		items: [{}],
		saveEvents: 'blur change',
		saveElements: 'input, select',
		addImageSrc: '',
		removeImageSrc: '',
		callbacks: {
			save: function() { },
			beforeAdd: function() { },
			add: function() { },
			beforeAddNew: function() { },
			addNew: function() { },
			beforeRemove: function() { },
			remove: function() { },
			repeaterButtons: function() { return false; }
		}
	}

	this.options   = jQuery.extend( true, {}, defaults, options );
	this.elem      = jQuery( this );
	this.items     = this.options.items;
	this.callbacks = this.options.callbacks;
	this._template = this.options.template ? this.options.template : this.elem.next( '#' + this.elem.prop( 'id' ) + '-template' ).html();

	// add "repeater" namespace to all saveEvents
	var saveEvents = this.options.saveEvents.split( ' ' );

	var newSaveEvents = [];

	for (i in saveEvents) {
		newSaveEvents.push( saveEvents[i] + '.repeater' );
	}

	this.options.saveEvents = newSaveEvents.join( ' ' );

	this.init = function() {

		// if no template provided or in "storage", use current HTML
		if ( ! this._template) {
			this._template = this.elem.html();
		}

		// move template html into "storage"
		this.elem.after( '<div id="' + this.elem.prop( 'id' ) + '-template" style="display:none">' + this._template + '</div>' );

		this.elem.addClass( 'repeater' );
		this.elem.empty();

		for (i in this.items) {
			this.addItem( this.items[i], i );
		}

		var repeater = this;

		jQuery( this.elem ).off( 'click.repeater', 'a.add-item:not(".inactive")' );
		jQuery( this.elem ).on('click.repeater', 'a.add-item:not(".inactive")', function(event){
			repeater.addNewItem( this );
		});

		jQuery( this.elem ).off( 'click.repeater', 'a.remove-item' );
		jQuery( this.elem ).on('click.repeater', 'a.remove-item', function(event){
			repeater.removeItem( this )
		});

		jQuery( this.elem ).off( this.options.saveEvents, this.options.saveElements );
		jQuery( this.elem ).on(this.options.saveEvents, this.options.saveElements, function(){
			repeater.save();
		});

		return this;
	}

	this.addItem = function(item, index) {

		var itemHTML = this._template;

		for (var property in item) {

			itemHTML = itemHTML.replace( /{i}/g, index );

			var repeaterButtonHTML = this.callbacks.repeaterButtons( this, index ) ? this.callbacks.repeaterButtons( this, index ) : this.addRepeaterButtons( index );
			itemHTML               = itemHTML.replace( "{buttons}", repeaterButtonHTML );

			var re   = new RegExp( '{' + property + '}', 'g' );
			itemHTML = itemHTML.replace( re, item[property] );

		}

		var itemObj = jQuery( itemHTML ).addClass( 'item-' + index );

		this.callbacks.beforeAdd( this, itemObj, item );
		this.append( itemObj );
		this.callbacks.add( this, itemObj, item );
	}

	this.addRepeaterButtons = function(index) {

		var cssClass = this.items.length >= options.limit && options.limit !== 0 ? 'inactive' : '';

		var str = '<div class="repeater-buttons">';
		str    += '<a class="add-item ' + cssClass + '" data-index="' + index + '">';
		str    += '<img src="' + options.addImageSrc + '/images/add.png" alt="Add" /></a>';

		if (this.items.length > 1) {
			str += '<a class="remove-item" data-index="' + index + '"><img src="' + options.removeImageSrc + '/images/remove.png" alt="Remove" /></a>';
		}

		return str;
	}

	this.addNewItem = function(elem) {

		var index = jQuery( elem ).attr( 'data-index' );

		this.callbacks.beforeAddNew( this, index );
		this.items.splice( index + 1, 0, this.getBaseObject() );
		this.callbacks.addNew( this, index );

		this.refresh();

	}

	this.removeItem = function(elem) {

		var index = jQuery( elem ).attr( 'data-index' );

		this.callbacks.beforeRemove( this, index );
		// using delete (over splice) to maintain the correct indexes for
		// the items array when saving the data from the UI
		delete this.items[index];
		this.callbacks.remove( this, index );

		this.save();
		this.refresh();

	}

	this.refresh = function() {

		this.elem.empty();

		for (i in this.items) {
			this.addItem( this.items[i], i );
		}

	}

	this.save = function() {

		var keys = this.getDataKeys();
		var data = new Array();

		for (i = 0; i < this.items.length; i++) {

			if (typeof this.items[i] == 'undefined') {
				continue;
			}

			var item = {};

			for (j in keys) {
				var key = keys[j];
				var id  = '#' + key + '_' + i;

				item[key] = jQuery( this.elem ).find( id ).val();
			}

			data.push( item );
		}

		// save data to items
		this.items = data;

		// save data externally via callback
		this.callbacks.save( this, data );

	}

	/**
	* Loops through the current items array and retrieves the object properties of the
	* first valid item object. Originally this would simply pull the object keys from
	* the first index of the items array; however, when the first item has been
	* 'deleted' (see the save() method), it will be undefined.
	*/
	this.getDataKeys = function() {

		var keys = new Array();

		for (var i in this.items) {

			if (typeof this.items[i] == 'undefined') {
				continue;
			}

			for (var key in this.items[i]) {
				keys[keys.length] = key;
			}

			break;
		}

		return keys;
	}

	this.getBaseObject = function() {

		var item = {};
		var keys = this.getDataKeys();

		for (var i in keys) {
			item[keys[i]] = '';
		}

		return item;
	}

	return this.init( true );
};
