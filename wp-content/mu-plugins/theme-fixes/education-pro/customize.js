(function(wp, $){
	$(document).ready(function(){
		wp.customize.bind('ready',function() {
			var c = $('#customize-control-header_image .customizer-section-intro');
			c.html('To upload a header image from your computer, click “Add new image.” Your theme works best with one sized to at least <strong>2000 × 130</strong> pixels. You will have the option to crop your image during upload.');
		});
	});
}(wp, jQuery));
