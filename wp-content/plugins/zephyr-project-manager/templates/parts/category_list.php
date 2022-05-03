<?php

	/**
	* Template for displaying the category list
	*
	* @package ZephyrProjectManager
	*
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Categories;

	$category_count = Categories::get_category_total();
	$categories = Categories::get_categories();
?>

<?php foreach ($categories as $category) : ?>
	<div class="zpm_category_row" zpm-ripple="" data-ripple="rgba(0, 0, 0, 0.09)" data-category-id="<?php echo $category->id ?>">
		<span class="zpm_category_color" data-zpm-color="<?php echo $category->color; ?>" style="background:<?php echo $category->color; ?>"></span>
		<span class="zpm_category_name"><?php echo $category->name; ?></span>
		<?php echo ($category->description !== '') ? ' - <span class="zpm_category_description">' . $category->description . '</span>' : ''; ?>
		<span class="zpm_category_actions">
			<span class="zpm_delete_category" data-category-id="<?php echo $category->id ?>">
				<i class="zpm_delete_category_icon lnr lnr-cross"></i>
			</span>
		</span>
	</div>
<?php endforeach; ?>