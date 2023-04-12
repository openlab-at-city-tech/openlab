<?php
namespace Elementor;

use ElementsKit_Lite\ElementsKit_Widget_Mail_Chimp_Api;
use ElementsKit_Lite\Libs\Framework\Attr;

class ElementsKit_Widget_Mail_Chimp_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-mail-chimp';
    }

    static function get_title() {
        return esc_html__( 'MailChimp', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-mailchimp ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }
    static function get_keywords() {
        return ['ekit', 'email', 'mail chimp', 'mail', 'subscription'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'mail-chimp/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'mail-chimp/';
    }

    public function wp_init() {

	    require_once $this->get_dir() . 'mail-chimp-api.php';

	    new ElementsKit_Widget_Mail_Chimp_Api();
    }


	static function get_data(){
		$data = Attr::instance()->utils->get_option('user_data', []);

		$token = (isset($data['mail_chimp']) && !empty($data['mail_chimp']['token']) ) ? $data['mail_chimp']['token'] : '';

		$list = (isset($data['mail_chimp']) && !empty($data['mail_chimp']['list']) ) ? $data['mail_chimp']['list'] : '';

		return [
			'token' => $token,
			'list' => $list,
		];
	}


}
