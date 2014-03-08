<?php

/**
 * MAIN APPLICATION BOOTSTRAP
 *
 * @author weber chris
 * @version 1.0
 *
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    /**
     * configuration variable
     *
     * @var Zend_Config_Ini
     */
    protected $_configuration;
    
    /**
     * frontcontroller variable
     *
     * @var Zend_Controller_Front
     */
    protected $_frontController;
    
    /**
     * AUTOLOADING
     *
     * @return Zend_Loader_Autoloader
     */
    protected function _initCore() {
        
        $autoloader = Zend_Registry::get('Autoloader');
        $autoloader->registerNamespace('Myapp_');
        
        if (APPLICATION_ENVIRONMENT != 'production') {
            $autoloader->suppressNotFoundWarnings(false);
        } else {
            $autoloader->suppressNotFoundWarnings(true);
        }
        
        return $autoloader;
        
    }
    
    /**
     * CONFIGURATION
     *
     * @return Zend_Config_Ini
     */
    protected function _initConfig() {

        $this->_configuration = Zend_Registry::get('Configuration');

        return $this->_configuration;

    }
    
    /**
     * FRONT CONTROLLER
     *
     * @return Zend_Controller_Front
     */
    protected function _initFrontControllerOutput() {

        $this->bootstrap('FrontController');
        $frontController = $this->getResource('FrontController');

        $response = new Zend_Controller_Response_Http;
        $response->setHeader('Content-Type', 'text/html; charset=UTF-8', true);
        $frontController->setResponse($response);

        $this->_frontController = $frontController;

        return $frontController;

    }
    
    /**
     * ERROR LOG
     *
     * to log errors in controllers use the logging action helper
     *
     * @return Zend_Log
     */
    protected function _initLogging() {

        $logsDir = APPLICATION_PATH.'/logs/';

        if (!is_dir($logsDir)) @mkdir($logsDir, 0755);

        // init error logger
        $logger = new Zend_Log();

        $writer = new Zend_Log_Writer_Stream($logsDir.'application.log');
        $logger->addWriter($writer);

        return $logger;

    }
    
    /**
     * VIEWS & LAYOUT
     *
     * @return Zend_View
     */
    protected function _initViewHelpers() {
		
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $view->setEncoding('UTF-8');
        $view->doctype('HTML5');
		
        $websiteTitle = $this->_configuration->website->title;
        $view   ->headTitle()->setSeparator(' - ')
                ->headTitle($websiteTitle);
		
        $view->addHelperPath(APPLICATION_PATH.'/layouts/helpers/', 'Application_Layouts_Helpers');
		
        return $view;
		
    }
    
    /**
     * DEFAULT ROUTES
     *
     * @return Zend_Controller_Router_Rewrite
     */
    protected function _initRoutes() {

        //ROUTING
        $this->_frontController->setParam('useDefaultControllerAlways', false);

        $router = $this->_frontController->getRouter();

        $router->removeDefaultRoutes();

        $this->_frontController->setRouter($router);

        return $router;

    }
    
}