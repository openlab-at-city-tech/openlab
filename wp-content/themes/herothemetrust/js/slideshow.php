<?php $slideshow_delay = of_get_option('ttrust_slideshow_delay'); ?>
<?php $autoPlay = ($slideshow_delay != "0") ? 1 : 0; ?>
<?php $slideshow_effect = of_get_option('ttrust_slideshow_effect'); ?>

<script type="text/javascript">
//<![CDATA[

jQuery(window).load(function() {			
	jQuery('.flexslider').flexslider({
		slideshowSpeed: <?php echo $slideshow_delay . '000'; ?>,  
		directionNav: true,
		slideshow: <?php echo $autoPlay; ?>,				 				
		animation: '<?php echo $slideshow_effect; ?>',
		animationLoop: true
	});  
});

//]]>
</script>