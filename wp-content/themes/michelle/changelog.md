# Michelle Changelog

## 1.5.1, 20231114

### Updated
- Block editor post title styles
- Removing `experimental-` prefix from block editor theme setup

### Fixed
- Excerpt block double "read more" text

### File updates
	changelog.md
	readme.txt 
	style.css
	assets/scss/editor-style-blocks.scss
	includes/Entry/Summary.php
	includes/Setup/Editor.php


## 1.5.0, 20230814

### Updated
- Improved editor UI
- Enabled link color and border controls for editor
- Preventing issues with CSS minification tools
- Improving screen reader text styles in editor
- Improving pagination accessibility
- Updating and fixing minor CSS style issues
- Updating 3rd party scripts
- Localization

### Fixed
- Compatibility with WordPress 6.3
- Plugin recommendation script (TGMPA) PHP8 error
- Heading spacing styles
- Removing skip link to mobile menu toggle when mobile menu is disabled
- Compatibility with "Integration for WooCommerce" plugin

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/_custom-properties.scss
	assets/scss/blocks.scss
	assets/scss/comments.scss
	assets/scss/content.scss
	assets/scss/customize-controls.scss
	assets/scss/customize-preview.scss
	assets/scss/editor-style-blocks.scss
	assets/scss/editor-style-classic.scss
	assets/scss/global.scss
	assets/scss/welcome.scss
	assets/scss/_tools/_function-pow.scss
	assets/scss/_tools/_function-round.scss
	includes/Accessibility/Component.php
	includes/Content/Block.php
	includes/Loop/Pagination.php
	includes/Menu/Component.php
	includes/Setup/Editor.php
	languages/*.*
	vendor/a11y-menu/a11y-menu.dist.min.js
	vendor/a11y-menu/a11y-menu.js


## 1.4.1, 20221109

### Fixed
- Outline button padding
- Excerpt HTML in Query Loop block
- Mobile pagination previous/next button not displaying
- Full aligned blocks horizontal margin

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/blocks.scss
	assets/scss/global.scss
	includes/Content/Block.php


## 1.4.0, 20221102

### Fixed
- WordPress 6.1 compatibility

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/_custom-properties.scss
	assets/scss/blocks.scss
	assets/scss/editor-style-blocks.scss
	assets/scss/global.scss


## 1.3.11, 20221027

### Updated
- Improving HTML
- Code comments, formatting and organization
- Block editor font size names
- Improving styles
- Improving JavaScript
- Localization

### Fixed
- Preventing scrollbar width miscalculation on Android devices
- Preventing accessibility issue regarding skip link color contrast

### File updates
	changelog.md
	readme.txt
	style.css
	assets/js/customize-preview.js
	assets/scss/blocks.scss
	assets/scss/content.scss
	assets/scss/editor-style-blocks.scss
	assets/scss/global.scss
	includes/Assets/Editor.php
	includes/Customize/CSS_Variables.php
	includes/Setup/Editor.php
	includes/Tool/AMP.php
	includes/Tool/Google_Fonts.php
	languages/*.*
	templates/parts/component/entry-header-singular.php
	templates/parts/component/page-header-404.php
	templates/parts/component/page-header-archive.php
	templates/parts/component/page-header-none.php
	templates/parts/component/page-header-search.php
	templates/parts/component/page-header.php


## 1.3.10, 20220928

### Fixed
- Cover block padding
- Block editor styles

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/blocks.scss
	assets/scss/editor-style-blocks.scss


## 1.3.9, 20220927

### Fixed
- Gallery image caption styles
- Full aligned blocks horizontal padding
- Dotted separator alignment
- Spacing of lists with background
- Columns block spacing 

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/blocks.scss
	assets/scss/editor-style-blocks.scss
	assets/scss/global.scss


## 1.3.8, 20220926

### Updated
- Enabling "no gaps" block style for Group block
- Improving compatibility with WordPress 6.0 blocks functionality
- Improving accessibility

### Fixed
- Improving navigational menu accessibility
- WordPress 6.0 Columns block spacing and layout
- Wide and full aligned blocks spacing
- "No gaps" block style not working
- Left/right alignment issues
- Styling issues
- Block editor styles
- Site info privacy link separator spacing

### File updates
	changelog.md
	readme.txt
	style.css
	assets/js/editor-blocks.js
	assets/scss/blocks.scss
	assets/scss/editor-style-blocks.scss
	assets/scss/global.scss
	includes/Content/Block.php
	includes/Content/Block_Styles.php
	includes/Tool/AMP.php
	templates/parts/footer/site-info.php


## 1.3.7, 20220604

### Updated
- "Welcome" page improvements
- Removing obsolete `container_class` primary menu argument
- Improved accessibility
- Improved mobile device line height
- Improved styles
- Localization

### Fixed
- WordPress 6.0 align and width issues
- WordPress 6.0 large Quote styles
- Mobile navigation JavaScript issue
- Mega menu focus styles
- Cover block color contrast issue
- Primary menu `.current_page_parent` styles
- CSS quotes localization
- Editor styles

### File updates
	changelog.md
	readme.txt
	style.css
	assets/js/editor-blocks.js
	assets/js/modal-search.js
	assets/js/navigation-mobile.js
	assets/scss/_custom-properties.scss
	assets/scss/_extend.scss
	assets/scss/blocks.scss
	assets/scss/customize-controls.scss
	assets/scss/editor-style-blocks.scss
	assets/scss/global.scss
	assets/scss/welcome.scss
	includes/Assets/Scripts.php
	includes/Content/Block.php
	includes/Content/Starter.php
	includes/Header/Component.php
	includes/Menu/Component.php
	includes/Welcome/Component.php
	languages/*.*
	templates/parts/admin/welcome-a11y.php
	templates/parts/admin/welcome-demo.php
	templates/parts/admin/welcome-features.php
	templates/parts/admin/welcome-footer.php
	templates/parts/admin/welcome-guide.php
	templates/parts/admin/welcome-header.php
	templates/parts/admin/welcome-promo.php


## 1.3.6, 20220318

### Fixed
- Fixing WordPress 5.9 columns block gap
- Fixing quotation marks localization
- Fixing tablet stacking styles

### File updates
	changelog.md
	style.css
	assets/scss/blocks.scss
	assets/scss/content.scss
	assets/scss/global.scss
	templates/parts/content/content-none.php


## 1.3.5, 20220218

### Updated
- Improving styles

### Fixed
- WordPress 5.9 bugs
- Editor style bugs
- Filter hook name for disabling JavaScript

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/blocks.scss
	assets/scss/editor-style-blocks.scss
	assets/scss/global.scss
	includes/Assets/Factory.php


## 1.3.4, 20220201

### Fixed
- Columns block last column margin

### File updates
	changelog.md
	style.css
	assets/scss/blocks.scss


## 1.3.3, 20220201

### Fixed
- Loading Google Fonts in block pattern preview
- Welcome page styles
- Preventing featured image link PHP error
- Padding of wide-aligned block within wide-aligned block in editor

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/editor-style-blocks.scss
	includes/Assets/Editor.php
	includes/Entry/Media.php
	includes/Tool/Google_Fonts.php
	includes/Welcome/Component.php


## 1.3.2, 20220130

### Updated
- Improving WP5.9 compatibility
- Code formatting

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/blocks.scss
	assets/scss/editor-style-blocks.scss
	includes/Assets/Styles.php
	templates/parts/menu/menu-primary.php


## 1.3.1, 20220129

### Updated
- Improving WP5.9 global styles fix code

### Fixed
- Mobile screen search form display

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/global.scss
	includes/Assets/Styles.php


## 1.3.0, 20220128

### Added
- Option to disable sticky header on small screens

### Updated
- WordPress 5.9 compatibility
- Optimizing and improving styles
- Improving accessibility
- Not removing any custom color in editor
- Improved block patterns and styles
- Removing obsolete code
- Localization
- Documentation
- Demo content

### Fixed
- Style issues
- AMP compatibility

### File updates
	header.php
	readme.txt
	style.css
	assets/js/customize-controls.js
	assets/js/editor-blocks.js
	assets/js/modal-search.js
	assets/js/navigation-mobile.js
	assets/js/scroll.js
	assets/scss/_custom-properties.scss
	assets/scss/_extend.scss
	assets/scss/blocks.scss
	assets/scss/comments.scss
	assets/scss/content.scss
	assets/scss/customize-controls.scss
	assets/scss/customize-preview.scss
	assets/scss/editor-style-blocks.scss
	assets/scss/editor-style-classic.scss
	assets/scss/global.scss
	assets/scss/_tools/_function-encode-url.scss
	assets/scss/_tools/_function-important.scss
	assets/scss/_tools/_function-pow.scss
	assets/scss/_tools/_function-round.scss
	assets/scss/_tools/_function-str-replace.scss
	assets/scss/_tools/_mixin-gallery-caption.scss
	assets/scss/_tools/_mixin-media.scss
	assets/scss/_tools/_mixin-screen-reader-hiding.scss
	includes/Autoload.php
	includes/Accessibility/Component.php
	includes/Assets/Factory.php
	includes/Assets/Scripts.php
	includes/Assets/Styles.php
	includes/Content/Block.php
	includes/Content/Block_Area.php
	includes/Content/Block_Patterns.php
	includes/Content/Container.php
	includes/Content/Starter.php
	includes/Customize/Colors.php
	includes/Customize/CSS_Variables.php
	includes/Customize/Custom_Logo.php
	includes/Customize/Mod.php
	includes/Customize/Options.php
	includes/Customize/Preview.php
	includes/Entry/Media.php
	includes/Entry/Page_Template.php
	includes/Entry/Summary.php
	includes/Footer/Component.php
	includes/Footer/Container.php
	includes/Header/Body_Class.php
	includes/Header/Component.php
	includes/Header/Container.php
	includes/Loop/Featured_Posts.php
	includes/Loop/Pagination.php
	includes/Menu/Component.php
	includes/Plugin/One_Click_Demo_Import/Component.php
	includes/Setup/Editor.php
	includes/Tool/AMP.php
	includes/Tool/Google_Fonts.php
	includes/Tool/KSES.php
	includes/Tool/Page_Builder.php
	includes/Tool/Wrapper.php
	languages/*.*
	templates/parts/accessibility/menu-skip-links.php
	templates/parts/admin/content-starter-about.php
	templates/parts/admin/content-starter-faq.php
	templates/parts/admin/content-starter-home.php
	templates/parts/admin/content-starter-services.php
	templates/parts/admin/notice-welcome.php
	templates/parts/block/*.*
	templates/parts/content/content-attachment-image.php
	templates/parts/content/content-attachment.php
	templates/parts/content/content-full.php
	templates/parts/content/content.php
	templates/parts/loop/loop-featured-posts.php
	templates/parts/menu/menu-primary.php
	templates/parts/meta/entry-meta-item-edit.php
	templates/parts/meta/entry-meta-top.php
	templates/parts/plugin/content-ocdi-info.php


## 1.2.0, 20210609

### Added
- AMP compatibility (thanks to contribution of @jamesosborne)

### Updated
- A11y Menu script to version 1.1.0
- Improving primary menu attributes
- Removing obsolete `menu-toggle-skip-link` in primary menu
- Moving background image control into "Theme Options" section in customizer
- Styles
- Localization

### Fixed
- Overlaid header top margin on mobile screens
- RTL styles
- Making demo images overridable via child theme
- Elementor page builder inaccessible first row control buttons

### File updates
	changelog.md
	comments.php
	header.php
	readme.txt
	style.css
	assets/js/modal-search.js
	assets/js/navigation-mobile.js
	assets/scss/content.scss
	assets/scss/editor-style-blocks.scss
	assets/scss/global.scss
	includes/Accessibility/Component.php
	includes/Assets/Scripts.php
	includes/Content/Block_Area.php
	includes/Content/Block_Patterns.php
	includes/Content/Block_Styles.php
	includes/Content/Component.php
	includes/Content/Starter.php
	includes/Customize/CSS_Variables.php
	includes/Customize/Options.php
	includes/Customize/Preview.php
	includes/Entry/Component.php
	includes/Entry/Navigation.php
	includes/Header/Component.php
	includes/Menu/Component.php
	includes/Setup/Upgrade.php
	includes/Tool/AMP.php
	languages/*.*
	templates/parts/menu/menu-primary.php
	vendor/a11y-menu/*.*


## 1.1.2, 20210502

### Fixed
- Search form modal button padding when modal is open on large screens

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/global.scss


## 1.1.1, 20210428

### Fixed
- Search form modal button size when modal is open
- Overlaid header position on mobile screens when WordPress admin bar is displayed
- Mobile menu and search form toggle buttons spacing on mobile screens

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/global.scss


## 1.1.0, 20210421

### Updated
- Passing accessibility review and adding `accessibility-ready` tag

### Fixed
- Search button height on mobile screens

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/global.scss


## 1.0.13, 20210419

### Updated
- Moving focus to search field when search modal is open
- Allowing font size lower than 16px in theme options

### Fixed
- Accessibility: aria attribute values for mobile menu and search modal toggle

### File updates
	changelog.md
	readme.txt
	style.css
	assets/js/modal-search.js
	assets/js/navigation-mobile.js
	includes/Assets/Scripts.php
	includes/Customize/Options.php
	includes/Header/Component.php


## 1.0.12, 20210416

### Updated
- Theme options description
- Starter content menu
- Improved accessibility
- Improved site header styles
- Improved site branding styles
- Improved responsive navigation functionality and styles
- Improved search modal functionality and styles
- Localization

### Fixed
- Welcome page demo content links

### File updates
	changelog.md
	readme.txt
	style.css
	assets/js/modal-search.js
	assets/js/navigation-mobile.js
	assets/js/scroll.js
	assets/scss/blocks.scss
	assets/scss/comments.scss
	assets/scss/content.scss
	assets/scss/editor-style-blocks.scss
	assets/scss/global.scss
	includes/Accessibility/Component.php
	includes/Assets/Scripts.php
	includes/Content/Starter.php
	includes/Customize/CSS_Variables.php
	includes/Customize/Options.php
	includes/Customize/Styles.php
	includes/Header/Component.php
	includes/Loop/Component.php
	includes/Loop/Pagination.php
	languages/*.*
	templates/parts/accessibility/menu-skip-links.php
	templates/parts/admin/welcome-demo.php
	templates/parts/component/search-results-count.php
	templates/parts/menu/menu-primary.php


## 1.0.11, 20210405

### Updated
- Welcome page info

### Fixed
- Mobile screens site title and logo display

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/global.scss
	includes/Welcome/Component.php
	languages/*.*
	templates/parts/admin/welcome-accessibility.php
	templates/parts/admin/welcome-header.php
	templates/parts/admin/welcome-promo.php


## 1.0.10, 20210403

### Added
- New block patterns
- New block styles
- Allowing page templates to set custom body classes

### Updated
- Search results page title font size
- Localization

### Fixed
- Outlined button block padding
- Gallery block max width in editor
- Last child fullwidth block covering post meta on single post pages

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/blocks.scss
	assets/scss/content.scss
	assets/scss/editor-style-blocks.scss
	assets/scss/global.scss
	includes/Content/Block_Patterns.php
	includes/Content/Block_Styles.php
	includes/Entry/Page_Template.php
	languages/*.*
	templates/parts/block/pattern-cta-simple.php
	templates/parts/block/pattern-features-steps.php
	templates/parts/block/pattern-food-menu.php
	templates/parts/block/pattern-gallery-variable-with-description.php
	templates/parts/block/pattern-intro-2-columns-text.php
	templates/parts/block/pattern-text-2-columns-wider-heading.php
	templates/parts/block/pattern-text-extra-hierarchy.php
	templates/parts/block/pattern-text-large-lead.php


## 1.0.9, 20210330

### Fixed
- Post meta styles on single post page

### Updated
- Starter content

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/content.scss
	templates/parts/admin/content-starter-excerpt.php
	templates/parts/admin/content-starter-faq.php


## 1.0.8, 20210330

### Fixed
- Quote styles
- Entry meta in featured posts list styles
- Removing `<em>` from testimonial block pattern

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/blocks.scss
	assets/scss/content.scss
	assets/scss/global.scss
	templates/parts/block/pattern-testimonials-single-bg.php


## 1.0.7, 20210330

### Updated
- Quote styles
- Entry meta in posts list styles

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/blocks.scss
	assets/scss/content.scss
	assets/scss/global.scss
	includes/Customize/Options.php


## 1.0.6, 20210330

### Added
- Customize error 404 page content

### Updated
- Optimized fonts loading
- Posts list styles
- Site title styles
- Starter content
- Localization

### File updates
	404.php
	changelog.md
	readme.txt
	style.css
	assets/js/customize-controls.js
	assets/scss/blocks.scss
	assets/scss/content.scss
	assets/scss/editor-style-blocks.scss
	assets/scss/global.scss
	includes/Autoload.php
	includes/Content/Block_Area.php
	includes/Content/Component.php
	includes/Customize/Options.php
	includes/Footer/Component.php
	includes/Header/Body_Class.php
	includes/Tool/Google_Fonts.php
	languages/*.*
	templates/parts/admin/content-starter-about.php
	templates/parts/admin/content-starter-contact.php
	templates/parts/admin/content-starter-faq.php
	templates/parts/admin/content-starter-home.php
	templates/parts/admin/content-starter-services.php
	templates/parts/block/area-404.php
	templates/parts/block/area-footer.php
	templates/parts/content/content-404.php


## 1.0.5, 20210328

### Updated
- Changing default font to Inter and size to 19px
- Screenshot
- Removing TGMPA script and plugin suggestions
- Localization
- Styles
- Improving border color styles
- Starter content
- Block patterns

### File updates
	changelog.md
	readme.txt
	style.css
	assets/scss/blocks.scss
	assets/scss/comments.scss
	assets/scss/content.scss
	assets/scss/editor-style-blocks.scss
	assets/scss/editor-style-classic.scss
	assets/scss/global.scss
	includes/Autoload.php
	includes/Content/Starter.php
	includes/Customize/Options.php
	includes/Customize/RGBA.php
	includes/Plugin/Component.php
	languages/*.*
	templates/parts/block/pattern-gallery-captions.php
	templates/parts/block/pattern-team-2.php


## 1.0.4, 20210327

### Updated
- Theme URL
- Starter content

### File updates
	changelog.md
	readme.txt
	style.css
	includes/Content/Starter.php
	templates/parts/admin/content-starter-about.php
	templates/parts/admin/content-starter-contact.php
	templates/parts/admin/content-starter-excerpt.php
	templates/parts/admin/content-starter-faq.php
	templates/parts/admin/content-starter-home.php
	templates/parts/admin/content-starter-services.php


## 1.0.3, 20210326

### Updated
- Screenshot

### File updates
	changelog.md
	readme.txt
	style.css
	screenshot.jpg


## 1.0.2, 20210325

### Updated
- Removing `accessibility-ready` tag as a11y team is aware of this theme review

### File updates
	changelog.md
	readme.txt
	style.css


## 1.0.1, 20210325

### Updated
- Reintroducing `accessibility-ready` tag so the theme can undergo a11y review
- Starter theme images

### File updates
	changelog.md
	readme.txt
	style.css
	templates/parts/admin/content-starter-about.php
	templates/parts/admin/content-starter-contact.php
	templates/parts/admin/content-starter-faq.php
	templates/parts/admin/content-starter-home.php
	templates/parts/admin/content-starter-services.php

## 1.0.0, 20210322

- Initial release.
