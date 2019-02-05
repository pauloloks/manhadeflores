<?php

class RastrearPedidoControllerCore extends FrontController
{
    public $php_self = 'rastrear-pedido';

    public $auth = false;
    public $authRedirection = 'history';
    public $ssl = true;

    /**
     * Initialize order detail controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    }

    public function displayAjax()
    {
        $this->display();
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();


        $this->setTemplate(_PS_THEME_DIR_.'rastrear-pedido.tpl');
    }

    public function setMedia()
    {
        if (Tools::getValue('ajax') != 'true') {
            parent::setMedia();
            $this->addCSS(_THEME_CSS_DIR_.'history.css');
            $this->addCSS(_THEME_CSS_DIR_.'addresses.css');
        }
    }
}
