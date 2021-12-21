<?php
class A_Marketing_AddGallery_MVC extends Mixin
{
    /**
     * @param string $medium
     * @return string
     */
    function get_base_addgallery_block($medium)
    {
        $base = M_Marketing::get_big_hitters_block_base('addgalleryimages');
        $block = new C_Marketing_Block_Two_Columns($base['title'], $base['description'], $base['links'], $base['footer'], $medium, 'upgradetonextgenpro');
        return $block->render();
    }
    function render_object()
    {
        $root_element = $this->call_parent('render_object');
        M_Marketing::enqueue_blocks_style();
        foreach ($root_element->find('admin_page.content_main_form', TRUE) as $container) {
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
 * @mixin C_Form
 * @property C_MVC_Controller $object
 */
class A_Marketing_Display_Settings_Form extends Mixin_Display_Type_Form
{
    function get_display_type_name()
    {
        return 'photocrati-marketing_fake_tile';
    }
    function get_title()
    {
        $context = $this->get_context();
        switch ($context) {
            case 'tile':
                return __('Pro Tiled', 'nggallery');
            case 'mosaic':
                return __('Pro Mosaic', 'nggallery');
            case 'masonry':
                return __('Pro Masonry', 'nggallery');
            default:
                return '';
        }
    }
    function _get_field_names()
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
        $footer = __('<strong>Bonus:</strong> NextGEN Gallery users get a discount of 20% off regular price.', 'nggallery');
        switch ($context) {
            case 'tile':
                $card = new C_Marketing_Block_Large(__('Use the Pro Tiled Gallery in NextGEN Pro', 'nggallery'), __('With this stunning display type, you can present your images large with no trouble. Choose the maximum width of the gallery, or let it automate. It will adjust incredibly on all devices.', 'nggallery'), $footer, 'https://www.imagely.com/wp-content/uploads/2020/06/tile.jpg', M_Marketing::get_utm_link('https://www.imagely.com/wordpress-gallery-plugin/pro-tiled-gallery/', 'gallerysettings', 'tiledgallery-demo'), __('View the Pro Tiled Demo', 'nggallery'), 'gallerysettings', 'tiledgallery');
                break;
            case 'mosaic':
                $card = new C_Marketing_Block_Large(__('Use the Mosaic Gallery in NextGEN Pro', 'nggallery'), __('With this stunning display type, you can present your images in a flexible grid. Choose the maximum height for your rows, and their margins, or use the default settings. It will adjust incredibly on all devices.', 'nggallery'), $footer, 'https://www.imagely.com/wp-content/uploads/2020/06/mosaic.jpg', M_Marketing::get_utm_link('https://www.imagely.com/wordpress-gallery-plugin/pro-mosaic-gallery/', 'gallerysettings', 'mosaicgallery-demo'), __('View the Mosaic Demo', 'nggallery'), 'gallerysettings', 'mosaicgallery');
                break;
            case 'masonry':
                $card = new C_Marketing_Block_Large(__('Use the Masonry Gallery in NextGEN Pro', 'nggallery'), __('With this stunning display type, you can present your images in a flexible grid. Choose the maximum width for your images, and their padding, or use the default settings. It will adjust incredibly on all devices.', 'nggallery'), $footer, 'https://www.imagely.com/wp-content/uploads/2020/06/masonry.jpg', M_Marketing::get_utm_link('https://www.imagely.com/wordpress-gallery-plugin/pro-masonry-gallery/', 'gallerysettings', 'masonrygallery-demo'), __('View the Masonry Demo', 'nggallery'), 'gallerysettings', 'masonrygallery');
                break;
            default:
                return '';
        }
        return $card->render();
    }
    function enqueue_static_resources()
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
    function _get_field_names()
    {
        $ret = $this->call_parent('_get_field_names');
        $ret[] = 'marketing_ecommerce_block';
        return $ret;
    }
    function get_upsell_popups()
    {
        $i18n = $this->get_i18n();
        $ecommerce = new C_Marketing_Block_Popup($i18n->ecommerce_and_print_lab, M_Marketing::get_i18n_fragment('feature_not_available', __('Ecommerce and Print Lab functionality', 'nggallery')), M_Marketing::get_i18n_fragment('lite_coupon'), 'fa-shopping-cart', 'gallerysettings', 'enableecommerce');
        $proofing = new C_Marketing_Block_Popup($i18n->proofing, M_Marketing::get_i18n_fragment('feature_not_available', __('proofing', 'nggallery')), M_Marketing::get_i18n_fragment('lite_coupon'), 'fa-star', 'gallerysettings', 'enableproofing');
        return ['ecommerce' => '<div class="ngg-marketing-popup">' . $ecommerce->render() . '</div>', 'proofing' => '<div class="ngg-marketing-popup">' . $proofing->render() . '</div>'];
    }
    function get_i18n()
    {
        $i18n = new stdClass();
        $i18n->requires_pro = __("Requires NextGEN Pro", "nggallery");
        $i18n->enable_proofing = __('Enable Proofing?', 'nggallery');
        $i18n->enable_ecommerce = __('Enable Ecommerce?', 'nggallery');
        $i18n->yes = __('Yes', 'nggallery');
        $i18n->no = __('No', 'nggallery');
        $i18n->ecommerce_and_print_lab = __("Ecommerce and Print Lab Integration");
        $i18n->proofing = __("Proofing");
        return $i18n;
    }
    function enqueue_static_resources()
    {
        wp_enqueue_style('jquery-modal');
        wp_enqueue_script('ngg_display_type_settings_marketing', M_Static_Assets::get_static_url('photocrati-marketing#display_type_settings.min.js'), ['jquery-modal'], NGG_SCRIPT_VERSION, TRUE);
        wp_localize_script('ngg_display_type_settings_marketing', 'ngg_display_type_settings_marketing', ['upsells' => $this->get_upsell_popups(), 'i18n' => (array) $this->get_i18n()]);
        return $this->call_parent('enqueue_static_resources');
    }
    function _render_marketing_ecommerce_block_field($display_type)
    {
        return $this->object->render_partial('photocrati-marketing#display_type_settings', ['display_type' => $display_type, 'i18n' => $this->get_i18n()], TRUE);
    }
}
class A_Marketing_IGW_Display_Type_Upsells extends Mixin
{
    function index_action($return = FALSE)
    {
        return $this->call_parent('index_action', $return);
    }
    function new_pro_display_type_upsell($id, $name, $title = '', $preview_mvc_path = NULL)
    {
        return ['ID' => $id, 'default_source' => 'galleries', 'entity_types' => ['image'], 'hidden_from_igw' => false, 'hidden_from_ui' => false, 'name' => $name, 'title' => $title, 'preview_image_url' => $preview_mvc_path ? $this->get_static_url($preview_mvc_path) : ''];
    }
    function get_pro_display_types()
    {
        return [$this->new_pro_display_type_upsell(-1, 'pro-tile', __("Pro Tile", 'nggallery'), 'photocrati-marketing#pro-tile-preview.jpg'), $this->new_pro_display_type_upsell(-2, 'pro-mosaic', __("Pro Mosaic", 'nggallery'), 'photocrati-marketing#pro-mosaic-preview.jpg'), $this->new_pro_display_type_upsell(-3, 'pro-masonry', __("Pro Masonry", 'nggallery'), 'photocrati-marketing#pro-masonry-preview.jpg'), $this->new_pro_display_type_upsell(-4, 'igw-promo')];
    }
    function get_marketing_cards()
    {
        $pro_tile = new C_Marketing_Block_Popup(__('Pro Tiled Gallery', 'nggallery'), M_Marketing::get_i18n_fragment('feature_not_available', __('the Pro Tiled Gallery', 'nggallery')), M_Marketing::get_i18n_fragment('lite_coupon'), $this->get_static_url('photocrati-marketing#pro-tile-preview.jpg'), 'igw', 'tiledgallery');
        $pro_masonry = new C_Marketing_Block_Popup(__('Pro Masonry Gallery', 'nggallery'), M_Marketing::get_i18n_fragment('feature_not_available', __('the Pro Masonry Gallery', 'nggallery')), M_Marketing::get_i18n_fragment('lite_coupon'), $this->get_static_url('photocrati-marketing#pro-masonry-preview.jpg'), 'igw', 'masonrygallery');
        $pro_mosaic = new C_Marketing_Block_Popup(__('Pro Mosaic Gallery', 'nggallery'), M_Marketing::get_i18n_fragment('feature_not_available', __('the Pro Mosaic Gallery', 'nggallery')), M_Marketing::get_i18n_fragment('lite_coupon'), $this->get_static_url('photocrati-marketing#pro-mosaic-preview.jpg'), 'igw', 'mosaicgallery');
        return ['pro-tile' => '<div>' . $pro_tile->render() . '</div>', 'pro-masonry' => '<div>' . $pro_masonry->render() . '</div>', 'pro-mosaic' => '<div>' . $pro_mosaic->render() . '</div>'];
    }
    function enqueue_display_tab_js()
    {
        $this->call_parent('enqueue_display_tab_js');
        $data = ['display_types' => $this->get_pro_display_types(), 'i18n' => ['get_pro' => __("Requires NextGEN Pro", 'nggallery')], 'templates' => $this->get_marketing_cards(), 'igw_promo' => $this->render_partial('photocrati-marketing#igw_promo', [], TRUE)];
        wp_enqueue_style('jquery-modal');
        wp_enqueue_script('igw_display_type_upsells', $this->get_static_url('photocrati-marketing#igw_display_type_upsells.min.js'), ['ngg_display_tab', 'jquery-modal'], NGG_SCRIPT_VERSION);
        wp_localize_script('igw_display_type_upsells', 'igw_display_type_upsells', $data);
        M_Marketing::enqueue_blocks_style();
        wp_add_inline_style('ngg_attach_to_post', ".display_type_preview:nth-child(5) {clear: both;} .ngg-marketing-block-display-type-settings label {color: darkgray !important;}");
        $this->mark_script('igw_display_type_upsells');
    }
}
class A_Marketing_Lightbox_Options_MVC extends Mixin
{
    function render_object()
    {
        $root_element = $this->call_parent('render_object');
        M_Marketing::enqueue_blocks_style();
        $block = new C_Marketing_Block_Large(__('Go big with the Pro Lightbox', 'nggallery'), __("The Pro Lightbox allows you to display images at full scale when opened. Your visitors will enjoy breathtaking views of your photos on any device. It's customizable, from colors to padding and more. Offer social sharing, deep linking, and individual image commenting. Turn your gallery lightbox view into a slideshow for your visitors. You can customize settings such as auto-playing and slideshow speed.", 'nggallery'), __('<strong>Bonus:</strong> NextGEN Gallery users get a discount of 20% off regular price', 'nggallery'), 'fa-expand', M_Marketing::get_utm_link('https://www.imagely.com/wordpress-gallery-plugin/pro-lightbox-demo/', 'otheroptions', 'prolightbox-demo'), __('View the Pro Lightbox Demo', 'nggallery'), 'otheroptions', 'prolightbox');
        foreach ($root_element->find('admin_page.other_options_lightbox_libraries', TRUE) as $container) {
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
    function get_title()
    {
        return __('Image Protection', 'nggallery');
    }
    function render()
    {
        $card = new C_Marketing_Block_Large(__('Protect your images', 'nggallery'), __('Image protection disables the ability for visitors to right-click or drag to download your images in both the gallery display and Pro Lightbox views. It gives you complete freedom to display your work without worry. You can also choose to protect all images sitewide, even outside of NextGEN Gallery.', 'nggallery'), __('<strong>Bonus:</strong> NextGEN Gallery users get a discount of 20% off regular price.', 'nggallery'), 'fa-lock-open', M_Marketing::get_utm_link('https://www.imagely.com/docs/turn-image-protection/', 'otheroptions', 'imageprotection-learnmore'), __('Learn more', 'nggallery'), 'otheroptions', 'imageprotection');
        return $card->render();
    }
    function enqueue_static_resources()
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
    public function render($return = TRUE)
    {
        $view = new C_MVC_View('photocrati-marketing#block-' . $this->template, ['block' => $this, 'link_text' => $this->link_text]);
        return $view->render($return);
    }
    public function get_upgrade_link()
    {
        return M_Marketing::get_utm_link('https://www.imagely.com/nextgen-gallery/', $this->medium, $this->campaign, $this->source);
    }
}
class C_Marketing_Block_Card extends C_Marketing_Block_Base
{
    public $title = '';
    public $thumb_url = '';
    public $description = '';
    public $icon = '';
    /**
     * @param string $title Card title
     * @param string $desc Card description
     * @param string $icon Icon found under static/icons/
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
        $this->icon = C_Router::get_instance()->get_static_url('photocrati-marketing#icons/' . $icon);
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
     * @param string $thumbnail_url Either a full HTTPS path or a FontAwesome icon (must begin with fa-)
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
     * @param string $thumbnail_url Either a full HTTPS path or a FontAwesome icon (must begin with fa-)
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
     *@var string $medium
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
     * @param string $title
     * @param string|string[] $description
     * @param array $links
     * @param string $footer
     * @param string $medium
     * @param string $campaign
     * @param string $src
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