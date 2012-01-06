function set_active_widget(instance_id) {
	self.IW_instance = instance_id;
}

function send_to_editor(h) {
	// ignore content returned from media uploader and use variables passed to window instead

	// store attachment id in hidden field
	jQuery( '#widget-'+self.IW_instance+'-image' ).val( self.IW_img_id );

	// display attachment preview
	jQuery( '#display-widget-'+self.IW_instance+'-image' ).html( self.IW_html );

	// change width & height fields in widget to match image
	jQuery( '#widget-'+self.IW_instance+'-width' ).val(jQuery( '#display-widget-'+self.IW_instance+'-image img').attr('width'));
	jQuery( '#widget-'+self.IW_instance+'-height' ).val(jQuery( '#display-widget-'+self.IW_instance+'-image img').attr('height'));

	// set alignment in widget
	jQuery( '#widget-'+self.IW_instance+'-align' ).val(self.IW_align);

	// set title in widget
	jQuery( '#widget-'+self.IW_instance+'-image_title' ).val(self.IW_title);

	// set caption in widget
	jQuery( '#widget-'+self.IW_instance+'-description' ).val(self.IW_caption);

	// set alt text in widget
	jQuery( '#widget-'+self.IW_instance+'-alt' ).val(self.IW_alt);

	// set link in widget
	jQuery( '#widget-'+self.IW_instance+'-link' ).val(self.IW_url);

	// close thickbox
	tb_remove();

	// change button text
	jQuery('#add_image-widget-'+self.IW_instance+'-image').html(jQuery('#add_image-widget-'+self.IW_instance+'-image').html().replace(/Add Image/g, 'Change Image'));
	
}


function changeImgWidth(instance) {
	var width = jQuery( '#widget-'+instance+'-width' ).val();
	var height = Math.round(width / imgRatio(instance));
	changeImgSize(instance,width,height);
}

function changeImgHeight(instance) {
	var height = jQuery( '#widget-'+instance+'-height' ).val();
	var width = Math.round(height * imgRatio(instance));
	changeImgSize(instance,width,height);
}

function imgRatio(instance) {
	var width_old = jQuery( '#display-widget-'+instance+'-image img').attr('width');
	var height_old = jQuery( '#display-widget-'+instance+'-image img').attr('height');
	var ratio =  width_old / height_old;
	return ratio;
}

function changeImgSize(instance,width,height) {
	if (isNaN(width) || width < 1) {
		jQuery( '#widget-'+instance+'-width' ).val('');
		width = 'none';
	} else {
		jQuery( '#widget-'+instance+'-width' ).val(width);
		width = width + 'px';
	}
	jQuery( '#display-widget-'+instance+'-image img' ).css({
		'width':width
	});

	if (isNaN(height) || height < 1) {
		jQuery( '#widget-'+instance+'-height' ).val('');
		height = 'none';
	} else {
		jQuery( '#widget-'+instance+'-height' ).val(height);
		height = height + 'px';
	}
	jQuery( '#display-widget-'+instance+'-image img' ).css({
		'height':height
	});
}

function changeImgAlign(instance) {
	var align = jQuery( '#widget-'+instance+'-align' ).val();
	jQuery( '#display-widget-'+instance+'-image img' ).attr(
		'class', (align == 'none' ? '' : 'align'+align)
	);
}

jQuery(document).ready(function() {
	jQuery("body").click(function(event) {
		if (jQuery(event.target).is('a.thickbox-cac-featured-content-widget')) {
			tb_show("Add an Image", event.target.href, false);
		}
	});
	// Modify thickbox link to fit window. Adapted from wp-admin\js\media-upload.dev.js.
	jQuery('a.thickbox-cac-featured-content-widget').each( function() {
		var href = jQuery(this).attr('href'), width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
		if ( ! href ) return;
		href = href.replace(/&width=[0-9]+/g, '');
		href = href.replace(/&height=[0-9]+/g, '');
		jQuery(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 ) );
	});
});