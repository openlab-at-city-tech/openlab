<?php
class A_Marketing_AddGallery_MVC extends Mixin
{
    /**
     * @param string $medium
     * @return string
     */
    public function get_base_addgallery_block($medium)
    {
        $base = M_Marketing::get_big_hitters_block_base('addgalleryimages');
        $block = new C_Marketing_Block_Two_Columns($base['title'], $base['description'], $base['links'], $base['footer'], $medium, 'upgradetonextgenpro');
        return $block->render();
    }
    public function render_object()
    {
        $root_element = $this->call_parent('render_object');
        M_Marketing::enqueue_blocks_style();
        foreach ($root_element->find('admin_page.content_main_form', true) as $container) {
            /** @var C_MVC_View_Element $container */
            switch ($container->get_object()->context) {
                case 'upload_images':
                    $medium = 'addgalleryimages';
                    break;
                case 'import_media_library':
                    $medium = 'addgalleryimportmedia';
                    break;
                case 'import_folder':
                    $medium = 'addgalleryimportfolder';
                    break;
            }
            $container->append($this->get_base_addgallery_block($medium));
        }
        return $root_element;
    }
}
/**
 * Provides 'image animation' related marketing to NextGEN for when Pro|PLus is not active.
 *
 * @package NextGEN Gallery
 */
/**
 * Provides marketing for image animations features when Pro|Plus is not active.
 */
class A_Marketing_Animations_Form extends Mixin
{
    /**
     * Returns the title of this form.
     *
     * @return string
     */
    public function get_title() : string
    {
        return __('Image Animations', 'nggallery');
    }
    /**
     * Returns the marketing content as rendered HTML string.
     *
     * @return string
     */
    public function render() : string
    {
        $medium = 'otheroptions';
        $base = M_Marketing::get_big_hitters_block_base($medium);
        $base['campaign'] = 'imageanimation';
        // To convert these to links use the format:.
        // ['title' => 'Hello', 'https://imagely.com/' ].
        // Do not include the above period, it is part of the immutable PHP-CS rules.
        $base['links'] = [[__('14+ Different Animations for galleries', 'nextgen-gallery-pro'), __('Easily Sell with eCommerce', 'nextgen-gallery-pro'), __('Automated Gallery Displays', 'nextgen-gallery-pro'), __('Image Proofing', 'nextgen-gallery-pro')], [__('Lightboxes', 'nextgen-gallery-pro'), __('Digital Downloads', 'nextgen-gallery-pro'), __('Image Protection', 'nextgen-gallery-pro'), __('And much more!', 'nextgen-gallery-pro')]];
        $svg = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 26.9 25.65" style="enable-background:new 0 0 26.9 25.65" xml:space="preserve"><style type="text/css">.st0{clip-path:url(#SVGID_2_);} .st1{fill:#FFD401;}</style><g><g><defs><path id="SVGID_1_" d="M14.1,0.43l3.44,8.05l8.72,0.78c0.39,0.03,0.67,0.37,0.64,0.76c-0.02,0.19-0.1,0.35-0.24,0.47l0,0 l-6.6,5.76l1.95,8.54c0.09,0.38-0.15,0.75-0.53,0.84c-0.19,0.04-0.39,0-0.54-0.1l-7.5-4.48l-7.52,4.5 c-0.33,0.2-0.76,0.09-0.96-0.24c-0.1-0.16-0.12-0.35-0.08-0.52h0l1.95-8.54l-6.6-5.76c-0.29-0.25-0.32-0.7-0.07-0.99 C0.3,9.35,0.48,9.28,0.66,9.27l8.7-0.78l3.44-8.06c0.15-0.36,0.56-0.52,0.92-0.37C13.9,0.13,14.03,0.27,14.1,0.43L14.1,0.43 L14.1,0.43z"/></defs><clipPath id="SVGID_2_"><use xlink:href="#SVGID_1_" style="overflow:visible"/></clipPath><g class="st0"><defs><rect id="SVGID_3_" x="-0.08" y="-0.1" width="27.01" height="25.85"/></defs><clipPath id="SVGID_4_"><use xlink:href="#SVGID_3_" style="overflow:visible"/></clipPath><g style="clip-path:url(#SVGID_4_)"><image style="overflow:visible" width="64" height="57" xlink:href="data:image/jpeg;base64,/9j/4AAQSkZJRgABAgEAlgCWAAD/7AARRHVja3kAAQAEAAAAHgAA/+4AIUFkb2JlAGTAAAAAAQMA EAMCAwYAAAJIAAACsAAAA4b/2wCEABALCwsMCxAMDBAXDw0PFxsUEBAUGx8XFxcXFx8eFxoaGhoX Hh4jJSclIx4vLzMzLy9AQEBAQEBAQEBAQEBAQEABEQ8PERMRFRISFRQRFBEUGhQWFhQaJhoaHBoa JjAjHh4eHiMwKy4nJycuKzU1MDA1NUBAP0BAQEBAQEBAQEBAQP/CABEIADwAQwMBIgACEQEDEQH/ xACiAAADAQEBAQAAAAAAAAAAAAAABAUCAQMGAQACAwEAAAAAAAAAAAAAAAAAAwQFBgIQAAIABQME AwAAAAAAAAAAAAARAQIDBAUTJBUSIzMlMhQ0EQABAQYGAQUAAAAAAAAAAAABABAgEXGhMrECEkJy AzEhUSITFBIAAgADBAgHAQAAAAAAAAAAAQIAEAMhMaEiUXGBkbHRMtIRYRJykjNzQv/aAAwDAQAC EQMRAAAA+68SfRyaBPIrKBPAoektl3NMDQREp1Cbmp+jJAbruANsptSOKwGsrkJlKXnLHRkgu0ZA 00m2/iyBqqudKto0FikOkNyQ6Ak51h66IGkrP//aAAgBAgABBQC/v7ijccreHK3hbZG6nrmUhu0I s4bkycN2hFnDcmRkljc6chpyFrJLC4P/2gAIAQMAAQUApUpJpNCmaFMnoyQlLeHbQirDtltDtIRW h2y2mjCl1ROqJVmjpn//2gAIAQEAAQUAr14UIcjIcjIcjIcjIcjIcjIUbyWtOZH4MYxjGWH6DJfB jGMYzH/oMp42MYxjMdHcmV8bGMYxmNjujL+NjGMYzGR3RmPExjGMZi/1l/8AV6PUnqT1J6k9SepL L6Guf//aAAgBAgIGPwBqdNgFAX+QbxHWPiI6x8RFNGcFXcA5RcZPqXhOj+iyfUvCdH9FkxNRFsWw +rR5Ax9tPc/bH209z9sUiKiHOtgDdsv/2gAIAQMCBj8ABIti7GLsYJAuEhtm3tMhtm/tMhlJvu8O cdDYc46Gw5w+VhlOjnL/2gAIAQEBBj8ABIJiYeisKsKsKsKsKsK0DKQYRizJMviRZkmcHxIsyTOD 4kWdczg+JFnXyOD44lnXyOD44lmT9MdMfjp91vqt9Vvqt9Vvqt9UPz6vsgfPiDP/2Q==" transform="matrix(0.48 0 0 -0.48 -1.1399 26.7469)"/></g></g></g><path class="st1" d="M14.1,0.43l3.44,8.05l8.72,0.78c0.39,0.03,0.67,0.37,0.64,0.76c-0.02,0.19-0.1,0.35-0.24,0.47l0,0l-1.18,1.03 c-3.21,1.11-7.42,1.78-12.03,1.78c-4.61,0-8.83-0.67-12.03-1.78l-1.18-1.03c-0.29-0.25-0.32-0.7-0.07-0.99 C0.3,9.35,0.48,9.28,0.66,9.27l8.7-0.78l3.44-8.06c0.15-0.36,0.56-0.52,0.92-0.37C13.9,0.13,14.03,0.27,14.1,0.43L14.1,0.43 L14.1,0.43z"/></g></svg>';
        $base['title'] = __('Get NextGEN Pro and Unlock All the Powerful Features', 'nggallery');
        $base['description'] = [__('Thanks for using NextGEN Gallery. Upgrade to Pro and unlock all our features, like animations for each of your galleries.', 'nggallery'), sprintf(
            // translators: %s is an SVG of a star icon.
            __('We know that you will love NextGEN Pro. It has over 3,200+ five star ratings (%1$s%1$s%1$s%1$s%1$s) and is used by over 500k websites.<br/><br/><h3>Pro Features:</h3>', 'nggallery'),
            $svg,
            $svg,
            $svg,
            $svg,
            $svg
        )];
        $url = M_Marketing::get_utm_link('https://www.imagely.com/lite', 'otheroptions', 'imageanimation-learnmore');
        $base['footer'] = sprintf(__("<a href='%s'>Get NextGEN Pro today and unlock all the powerful features >></a><br/><br/><strong>Bonus:</strong> NextGEN users get <strong>50&percnt; off</strong> regular prices, automatically applied at checkout.", 'nextgen-gallery-pro'), $url);
        $block = new C_Marketing_Block_Two_Columns($base['title'], $base['description'], $base['links'], $base['footer'], $base['medium'], $base['campaign']);
        return $block->render();
    }
    /**
     * Enqueues necessary static resources such as JavaScript and CSS.
     *
     * @return null
     */
    public function enqueue_static_resources()
    {
        M_Marketing::enqueue_blocks_style();
        return $this->call_parent('enqueue_static_resources');
    }
}
/**
 * @mixin C_Form
 * @property C_MVC_Controller $object
 */
class A_Marketing_Display_Settings_Form extends Mixin_Display_Type_Form
{
    public function get_display_type_name()
    {
        return 'photocrati-marketing_fake_tile';
    }
    public function get_title()
    {
        $context = $this->get_context();
        switch ($context) {
            case 'tile':
                return __('Pro Tile', 'nggallery');
            case 'mosaic':
                return __('Pro Mosaic', 'nggallery');
            case 'masonry':
                return __('Pro Masonry', 'nggallery');
            default:
                return '';
        }
    }
    public function _get_field_names()
    {
        return ['marketing_block'];
    }
    public function get_context()
    {
        return str_replace('photocrati-marketing_display_settings_', '', $this->object->context);
    }
    public function _render_marketing_block_field($thing)
    {
        $context = $this->get_context();
        $footer = __('<strong>Bonus:</strong> NextGEN Gallery users get a discount of 50% off regular price.', 'nggallery');
        switch ($context) {
            case 'tile':
                $card = new C_Marketing_Block_Large(__('Use the Pro Tile Gallery in NextGEN Pro', 'nggallery'), __('With this stunning display type, you can present your images large with no trouble. Choose the maximum width of the gallery, or let it automate. It will adjust incredibly on all devices.', 'nggallery'), $footer, 'https://www.imagely.com/wp-content/uploads/2020/06/tile.jpg', M_Marketing::get_utm_link('https://www.imagely.com/lite', 'gallerysettings', 'tiledgallery-demo'), __('View the Pro Tile Demo', 'nggallery'), 'gallerysettings', 'tiledgallery');
                break;
            case 'mosaic':
                $card = new C_Marketing_Block_Large(__('Use the Mosaic Gallery in NextGEN Pro', 'nggallery'), __('With this stunning display type, you can present your images in a flexible grid. Choose the maximum height for your rows, and their margins, or use the default settings. It will adjust incredibly on all devices.', 'nggallery'), $footer, 'https://www.imagely.com/wp-content/uploads/2020/06/mosaic.jpg', M_Marketing::get_utm_link('https://www.imagely.com/lite', 'gallerysettings', 'mosaicgallery-demo'), __('View the Mosaic Demo', 'nggallery'), 'gallerysettings', 'mosaicgallery');
                break;
            case 'masonry':
                $card = new C_Marketing_Block_Large(__('Use the Masonry Gallery in NextGEN Pro', 'nggallery'), __('With this stunning display type, you can present your images in a flexible grid. Choose the maximum width for your images, and their padding, or use the default settings. It will adjust incredibly on all devices.', 'nggallery'), $footer, 'https://www.imagely.com/wp-content/uploads/2020/06/masonry.jpg', M_Marketing::get_utm_link('https://www.imagely.com/lite', 'gallerysettings', 'masonrygallery-demo'), __('View the Masonry Demo', 'nggallery'), 'gallerysettings', 'masonrygallery');
                break;
            default:
                return '';
        }
        return $card->render();
    }
    public function enqueue_static_resources()
    {
        M_Marketing::enqueue_blocks_style();
        return $this->call_parent('enqueue_static_resources');
    }
}
/**
 * @property C_Form $object
 */
class A_Marketing_Display_Type_Settings_Form extends Mixin
{
    public function _get_field_names()
    {
        $ret = $this->call_parent('_get_field_names');
        $ret[] = 'marketing_ecommerce_block';
        return $ret;
    }
    public function get_upsell_popups()
    {
        $i18n = $this->get_i18n();
        $ecommerce = new C_Marketing_Block_Popup($i18n->ecommerce_and_print_lab, M_Marketing::get_i18n_fragment('feature_not_available', __('Ecommerce and Print Lab functionality', 'nggallery')), M_Marketing::get_i18n_fragment('lite_coupon'), 'fa-shopping-cart', 'gallerysettings', 'enableecommerce');
        $proofing = new C_Marketing_Block_Popup($i18n->proofing, M_Marketing::get_i18n_fragment('feature_not_available', __('proofing', 'nggallery')), M_Marketing::get_i18n_fragment('lite_coupon'), 'fa-star', 'gallerysettings', 'enableproofing');
        return ['ecommerce' => '<div class="ngg-marketing-popup">' . $ecommerce->render() . '</div>', 'proofing' => '<div class="ngg-marketing-popup">' . $proofing->render() . '</div>'];
    }
    public function get_i18n()
    {
        $i18n = new stdClass();
        $i18n->requires_pro = __('Requires NextGEN Pro', 'nggallery');
        $i18n->enable_proofing = __('Enable Proofing?', 'nggallery');
        $i18n->enable_ecommerce = __('Enable Ecommerce?', 'nggallery');
        $i18n->yes = __('Yes', 'nggallery');
        $i18n->no = __('No', 'nggallery');
        $i18n->ecommerce_and_print_lab = __('Ecommerce and Print Lab Integration');
        $i18n->proofing = __('Proofing');
        return $i18n;
    }
    public function enqueue_static_resources()
    {
        wp_enqueue_style('jquery-modal');
        wp_enqueue_script('ngg_display_type_settings_marketing', \Imagely\NGG\Display\StaticPopeAssets::get_url('photocrati-marketing#display_type_settings.js'), ['jquery-modal'], NGG_SCRIPT_VERSION, true);
        wp_localize_script('ngg_display_type_settings_marketing', 'ngg_display_type_settings_marketing', ['upsells' => $this->get_upsell_popups(), 'i18n' => (array) $this->get_i18n()]);
        return $this->call_parent('enqueue_static_resources');
    }
    public function _render_marketing_ecommerce_block_field($display_type)
    {
        return $this->object->render_partial('photocrati-marketing#display_type_settings', ['display_type' => $display_type, 'i18n' => $this->get_i18n()], true);
    }
}
class A_Marketing_Lightbox_Options_MVC extends Mixin
{
    public function render_object()
    {
        $root_element = $this->call_parent('render_object');
        M_Marketing::enqueue_blocks_style();
        $block = new C_Marketing_Block_Large(__('Go big with the Pro Lightbox', 'nggallery'), __("The Pro Lightbox allows you to display images at full scale when opened. Your visitors will enjoy breathtaking views of your photos on any device. It's customizable, from colors to padding and more. Offer social sharing, deep linking, and individual image commenting. Turn your gallery lightbox view into a slideshow for your visitors. You can customize settings such as auto-playing and slideshow speed.", 'nggallery'), __('<strong>Bonus:</strong> NextGEN Gallery users get a discount of 50% off regular price', 'nggallery'), 'fa-expand', M_Marketing::get_utm_link('https://www.imagely.com/lite', 'otheroptions', 'prolightbox-demo'), __('View the Pro Lightbox Demo', 'nggallery'), 'otheroptions', 'prolightbox');
        foreach ($root_element->find('admin_page.other_options_lightbox_libraries', true) as $container) {
            $container->append($block->render());
        }
        return $root_element;
    }
}
/**
 * @mixin C_Form
 * @property C_MVC_Controller $object
 */
class A_Marketing_Other_Options_Form extends Mixin
{
    public function get_title()
    {
        return __('Image Protection', 'nggallery');
    }
    public function render()
    {
        $card = new C_Marketing_Block_Large(__('Protect your images', 'nggallery'), __('Image protection disables the ability for visitors to right-click or drag to download your images in both the gallery display and Pro Lightbox views. It gives you complete freedom to display your work without worry. You can also choose to protect all images sitewide, even outside of NextGEN Gallery.', 'nggallery'), __('<strong>Bonus:</strong> NextGEN Gallery users get a discount of 50% off regular price.', 'nggallery'), 'fa-lock-open', M_Marketing::get_utm_link('https://www.imagely.com/lite', 'otheroptions', 'imageprotection-learnmore'), __('Learn more', 'nggallery'), 'otheroptions', 'imageprotection');
        return $card->render();
    }
    public function enqueue_static_resources()
    {
        M_Marketing::enqueue_blocks_style();
        return $this->call_parent('enqueue_static_resources');
    }
}
abstract class C_Marketing_Block_Base
{
    public $source = '';
    public $medium = '';
    public $campaign = '';
    public $template = '';
    public $link_text = '';
    /**
     * @param string $template
     * @param string $medium
     * @param string $campaign
     * @param string $src
     * @return C_Marketing_Block_Base
     */
    public function __construct($template, $medium, $campaign, $src = 'ngg')
    {
        $this->template = $template;
        $this->source = $src;
        $this->medium = $medium;
        $this->campaign = $campaign;
        $this->link_text = __('Upgrade Now', 'nggallery');
        return $this;
    }
    public function render($return = true)
    {
        $view = new C_MVC_View('photocrati-marketing#block-' . $this->template, ['block' => $this, 'link_text' => $this->link_text]);
        return $view->render($return);
    }
    public function get_upgrade_link()
    {
        return M_Marketing::get_utm_link('https://www.imagely.com/lite', $this->medium, $this->campaign, $this->source);
    }
}
class C_Marketing_Block_Card extends C_Marketing_Block_Base
{
    public $title = '';
    public $thumb_url = '';
    public $description = '';
    public $icon = '';
    /**
     * @param string $title Card title.
     * @param string $desc Card description.
     * @param string $icon Icon found under static/icons/.
     * @param string $medium
     * @param string $campaign
     * @param string $src
     * @return C_Marketing_Block_Card
     */
    public function __construct($title, $desc, $icon, $medium, $campaign, $src = 'ngg')
    {
        parent::__construct('card', $medium, $campaign, $src);
        $this->title = $title;
        $this->description = $desc;
        $this->icon = \Imagely\NGG\Util\Router::get_instance()->get_static_url('photocrati-marketing#icons/' . $icon);
        return $this;
    }
}
class C_Marketing_Block_Large extends C_Marketing_Block_Base
{
    public $title = '';
    public $description = '';
    public $links = array();
    public $footer = '';
    public $thumbnail_url = '';
    public $demo_url = '';
    public $demo_text = '';
    /**
     * @param string $title
     * @param string $description
     * @param string $footer
     * @param string $thumbnail_url Either a full HTTPS path or a FontAwesome icon (must begin with fa-).
     * @param string $demo_url
     * @param string $demo_text
     * @param string $campaign
     * @param string $medium
     * @param string $src
     * @return C_Marketing_Block_Large
     */
    public function __construct($title, $description, $footer, $thumbnail_url, $demo_url, $demo_text, $medium, $campaign, $src = 'ngg')
    {
        parent::__construct('large', $medium, $campaign, $src);
        $this->title = $title;
        $this->description = $description;
        $this->footer = $footer;
        $this->thumbnail_url = $thumbnail_url;
        $this->demo_url = $demo_url;
        $this->demo_text = $demo_text;
        $this->link_text = __('Upgrade to NextGEN Pro', 'nggallery');
        return $this;
    }
}
class C_Marketing_Block_Popup extends C_Marketing_Block_Base
{
    public $title = '';
    public $description = '';
    public $links = array();
    public $footer = '';
    public $thumbnail_url = '';
    /**
     * @param string $title
     * @param string $description
     * @param string $footer
     * @param string $thumbnail_url Either a full HTTPS path or a FontAwesome icon (must begin with fa-).
     * @param string $demo_url
     * @param string $medium
     * @param string $campaign
     * @param string $src
     * @return C_Marketing_Block_Popup
     */
    public function __construct($title, $description, $footer, $thumbnail_url, $medium, $campaign, $src = 'ngg')
    {
        parent::__construct('popup', $medium, $campaign, $src);
        $this->title = $title;
        $this->description = $description;
        $this->footer = $footer;
        $this->thumbnail_url = $thumbnail_url;
        $this->link_text = __('Upgrade to NextGEN Pro', 'nggallery');
        return $this;
    }
}
class C_Marketing_Block_Single_Line extends C_Marketing_Block_Base
{
    public $title = '';
    public $source = '';
    public $medium = '';
    public $campaign = '';
    /**
     * @return C_Marketing_Block_Single_Line
     * @var string $medium
     * @var string $campaign
     * @var string $src (optional) Defaults to 'nggallery'
     * @var string $title
     */
    public function __construct($title, $medium, $campaign, $src = 'ngg')
    {
        parent::__construct('single-line', $medium, $campaign, $src);
        $this->title = $title;
        $this->source = $src;
        $this->medium = $medium;
        $this->campaign = $campaign;
        return $this;
    }
}
class C_Marketing_Block_Two_Columns extends C_Marketing_Block_Base
{
    public $title = '';
    public $description = '';
    public $links = array();
    public $footer = '';
    /**
     * @param string          $title
     * @param string|string[] $description
     * @param array           $links
     * @param string          $footer
     * @param string          $medium
     * @param string          $campaign
     * @param string          $src
     * @return C_Marketing_Block_Two_Columns
     */
    public function __construct($title, $description, $links, $footer, $medium, $campaign, $src = 'ngg')
    {
        parent::__construct('two-columns', $medium, $campaign, $src);
        $this->title = $title;
        $this->description = $description;
        $this->links = $links;
        $this->footer = $footer;
        return $this;
    }
}