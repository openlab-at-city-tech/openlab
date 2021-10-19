<html>
	<head>
		<title><?php echo $og_title;?></title>
		<meta property="og:title" content="<?php echo $og_title;?>" />
		<meta property="og:image" content="<?php echo $target_image?>" />
		<meta property="og:description" content="<?php echo apply_filters('watupro_content', $og_description);?>" />
	</head>
	<body itemscope itemtype="http://schema.org/Product" style="display:none;">
		<h1 itemprop="name"><?php echo $og_title?></h1>
		<img itemprop="image" src="<?php echo $target_image?>" />
  <p itemprop="description"><?php echo apply_filters('watupro_content', $og_description);?></p>

		<?php if(!empty($_GET['return_to'])):?>
		<script type="text/javascript" >
		window.location = '<?php echo get_permalink($_GET['return_to']);?>';
		</script>
		<?php endif;?>
	</body>
</html>