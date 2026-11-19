function nextgen_lightbox_filter_selector($, selector) {
	if (nextgen_lightbox_settings && nextgen_lightbox_settings.context) {
		var context = nextgen_lightbox_settings.context;

		// SECURITY (#1246): WordPress renders text smilies (":)") and emoji as <img> inside
		// whatever link wraps them, so a visitor comment link would match "a > img" and
		// become a lightbox trigger for the commenter's URL. They are never gallery images.
		var notSmiley = '.wp-smiley, img.emoji';

		if (context == 'all_images') {
			selector = selector.add($('a > img').not(notSmiley).parent());
		} else if (context == 'all_images_direct') {
			selector = selector.add(
				$('a[href] > img')
					.not(notSmiley)
					.parent()
					.filter(function () {
						var href = $(this).attr('href').toLowerCase();
						var ext = href.substring(href.length - 3);
						var ext2 = href.substring(href.length - 4);

						return (
							ext == 'jpg' ||
							ext == 'gif' ||
							ext == 'png' ||
							ext2 == 'tiff' ||
							ext2 == 'jpeg' ||
							ext2 == 'webp'
						);
					})
			);
		} else if (context == 'nextgen_and_wp_images') {
			selector = selector.add($('a > img[class*="wp-image-"]').parent());
		}

		selector = selector.not('.gallery_link');
		selector = selector.not('.use_imagebrowser_effect');
	}

	return selector;
}
