<?php
/**
 * Template library templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<script type="text/template" id="template-sydney-templateLibrary-header-logo">
	<img src="<?php echo esc_url( get_template_directory_uri() . '/images/logo.svg' ); ?>" alt="Main Logo">
	<h2 style="margin-left:10px;">Sydney Studio</h2>
</script>

<script type="text/template" id="template-sydney-templateLibrary-header-back">
	<i class="eicon-" aria-hidden="true"></i>
	<span><?php echo __( 'Back to Library', 'sydney' ); ?></span>
</script>

<script type="text/template" id="template-sydney-TemplateLibrary_header-menu">
	<# _.each( tabs, function( args, tab ) { var activeClass = args.active ? 'elementor-active' : ''; #>
		<div class="elementor-component-tab elementor-template-library-menu-item {{activeClass}}" data-tab="{{{ tab }}}">{{{ args.title }}}</div>
	<# } ); #>
</script>

<script type="text/template" id="template-sydney-templateLibrary-header-actions">
	<div id="sydney-templateLibrary-header-sync" class="elementor-templates-modal__header__item">
		<i class="eicon-sync" aria-hidden="true" title="<?php esc_attr_e( 'Sync Library', 'sydney' ); ?>"></i>
		<span class="elementor-screen-only"><?php esc_html_e( 'Sync Library', 'sydney' ); ?></span>
	</div>
</script>

<script type="text/template" id="template-sydney-templateLibrary-preview">
    <iframe></iframe>
</script>

<script type="text/template" id="template-sydney-templateLibrary-header-insert">
	<div id="elementor-template-library-header-preview-insert-wrapper" class="elementor-templates-modal__header__item">
		{{{ sydney.library.getModal().getTemplateActionButton( obj ) }}}
	</div>
</script>

<script type="text/template" id="template-sydney-templateLibrary-insert-button">
	<a class="elementor-template-library-template-action elementor-button sydney-templateLibrary-insert-button">
		<i class="eicon-file-download" aria-hidden="true"></i>
		<span class="elementor-button-title"><?php esc_html_e( 'Insert', 'sydney' ); ?></span>
	</a>
</script>

<script type="text/template" id="template-sydney-templateLibrary-pro-button">
	<a class="elementor-template-library-template-action elementor-button sydney-templateLibrary-pro-button" href="https://athemes.com/sydney-pricing/" target="_blank">
		<i class="eicon-external-link-square" aria-hidden="true"></i>
		<span class="elementor-button-title"><?php esc_html_e( 'Get Pro', 'sydney' ); ?></span>
	</a>
</script>

<script type="text/template" id="template-sydney-templateLibrary-loading">
	<div class="elementor-loader-wrapper">
		<div class="elementor-loader">
			<div class="elementor-loader-boxes">
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
			</div>
		</div>
		<div class="elementor-loading-title"><?php esc_html_e( 'Loading', 'sydney' ); ?></div>
	</div>
</script>

<script type="text/template" id="template-sydney-templateLibrary-templates">
	<div id="sydney-templateLibrary-toolbar">
		<div id="sydney-templateLibrary-toolbar-filter" class="sydney-templateLibrary-toolbar-filter">
			<select id="sydney-templateLibrary-filter-category" class="sydney-templateLibrary-filter-category">
				<option class="sydney-templateLibrary-category-filter-item active" value="" data-tag=""><?php esc_html_e( 'Filter', 'sydney' ); ?></option>

				<?php
					$cats = SydneyPro\Elementor\Template_Library_Source::get_library_data()['categories'];
					foreach ( $cats as $cat  ) : ?>
						<option class="sydney-templateLibrary-category-filter-item" value="<?php echo esc_html( $cat['slug'] ); ?>" data-tag="<?php echo $cat['slug']; ?>"><?php echo esc_html( $cat['name'] ); ?></option>
					<?php endforeach;
				?>
			</select>
		</div>

		<div id="sydney-templateLibrary-toolbar-search">
			<label for="sydney-templateLibrary-search" class="elementor-screen-only"><?php esc_html_e( 'Search Templates:', 'sydney' ); ?></label>
			<input id="sydney-templateLibrary-search" placeholder="<?php esc_attr_e( 'Search', 'sydney' ); ?>">
			<i class="eicon-search"></i>
		</div>
	</div>

	<div class="sydney-templateLibrary-templates-window">
		<div id="sydney-templateLibrary-templates-list"></div>
	</div>
</script>

<script type="text/template" id="template-sydney-templateLibrary-template">
	<div class="sydney-templateLibrary-template-body" id="sydney-template-{{ template_id }}">
		<div class="sydney-templateLibrary-template-preview">
			<i class="eicon-zoom-in-bold" aria-hidden="true"></i>
		</div>
		<img class="sydney-templateLibrary-template-thumbnail" src="{{ thumbnail }}">
		<div class="sydney-templateLibrary-template-title">
			<span>{{{ title }}}</span>
		</div>
	</div>
	{{{ sydney.library.getModal().proLabel( obj ) }}}
	<div class="sydney-templateLibrary-template-footer">
		{{{ sydney.library.getModal().getTemplateActionButton( obj ) }}}
		<a href="#" class="elementor-button sydney-templateLibrary-preview-button">
			<i class="eicon-device-desktop" aria-hidden="true"></i>
			<?php esc_html_e( 'Preview', 'sydney' ); ?>
		</a>
	</div>
</script>
