<?php $slideshow_speed = of_get_option('ttrust_slideshow_speed').'000'; ?>
<?php $manual_advance = ($slideshow_speed == '0000') ? 'true' : 'false'; ?>
<?php $slideshow_effect = of_get_option('ttrust_slideshow_effect'); ?>
<script type="text/javascript">
jQuery(window).load(function() {
	jQuery('#slider').nivoSlider({		
			effect:'<?php echo $slideshow_effect; ?>', //Specify sets like: 'fold,fade,sliceDown'
			slices:12,			
			pauseTime: <?php echo $slideshow_speed; ?>, //Slide transition speed			
			captionOpacity:1, //Universal caption opacity
			manualAdvance:<?php echo $manual_advance; ?> //Force manual transitions
	});
});
</script>