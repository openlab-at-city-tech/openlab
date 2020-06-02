<!DOCTYPE html>
<html>
    <head>
        <title><?php esc_html_e($page_title)?></title>
        <?php
        print_admin_styles();
        print_head_scripts();
        ?>
    </head>
	<body>
		<div id="attach_to_post_tabs">
            <div class='ui-tabs-icon'><img src='<?php esc_html_e($logo) ?>' class='attach_to_post_logo'>
				<ul>
		            <?php foreach ($tabs as $id => $tab_params): ?>
						<li>
							<a href='#<?php echo esc_attr($id)?>'>
								<?php esc_html_e($tab_params['title']) ?>
							</a>
						</li>
					<?php endforeach ?>
				</ul>
			</div>
			<?php reset($tabs); foreach ($tabs as $id => $tab_params): ?>
				<div class="main_menu_tab" id="<?php echo esc_attr($id) ?>">
					<?php echo $tab_params['content'] ?>
				</div>
			<?php endforeach ?>
		</div>

        <div id="adminmenu" style="display:none;" data-wp-responsive="true"></div>
		<?php wp_print_footer_scripts() ?>
	</body>
</html>
