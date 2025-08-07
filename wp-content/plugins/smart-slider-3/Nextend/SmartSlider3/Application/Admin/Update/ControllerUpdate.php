<?php


namespace Nextend\SmartSlider3\Application\Admin\Update;


use Joomla\CMS\Router\Route;
use Nextend\SmartSlider3\Application\Admin\AbstractControllerAdmin;

class ControllerUpdate extends AbstractControllerAdmin {

    public function actionUpdate() {
        if ($this->validateToken()) {
            header('LOCATION: ' . admin_url('update-core.php?force-check=1'));
            exit;
        }

        $this->redirectToSliders();
    }
}