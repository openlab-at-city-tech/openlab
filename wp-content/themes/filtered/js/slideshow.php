<?php $slideshow_delay = of_get_option('ttrust_slideshow_speed'); ?>
<?php $slideshow_delay = ($slideshow_delay != "") ? $slideshow_delay : '6'; ?>
<?php $slideshow_effect = of_get_option('ttrust_slideshow_effect'); ?>

<script type="text/javascript">
//<![CDATA[

jQuery(window).load(function() {			
	jQuery('.flexslider').flexslider({
		slideshowSpeed: <?php echo $slideshow_delay . '000'; ?>,  
		directionNav: true,					
		animation: '<?php echo $slideshow_effect; ?>'		
	});  
});

//]]>
</script>