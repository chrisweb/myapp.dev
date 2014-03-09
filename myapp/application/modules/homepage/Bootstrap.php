<?php

class Homepage_Bootstrap extends Zend_Application_Module_Bootstrap
{

    public function __construct($application) {

        parent::__construct($application);
		
		$this->modulePath = APPLICATION_PATH.'/modules/'.strtolower($this->getModuleName()).'/';

	}
    
	protected function _initModuleCache()
	{
        
        Zend_Debug::dump($this->getModuleName(), '_initModuleCache: ');

        $cacheResource = $this->getPluginResource('cache');
        
        //Zend_Debug::dump($cacheResource, '$cacheResource: ');
        
        $defaultOptions = $cacheResource->getOptions();
        
        Zend_Debug::dump($defaultOptions, '$defaultOptions: ');
        
        $masterfilesOptions = $defaultOptions['masterfiles'];
        
        Zend_Debug::dump($masterfilesOptions, '$masterfilesOptions: ');
        
        $masterfilesOptions[] = $this->modulePath.'configs/routes.xml';
        
        $options = array(
            'masterfiles' => $masterfilesOptions
        );
        
        $cacheResource->setOptions($options);
        
        //Zend_Debug::dump($cacheResource, '$cacheResource: ');exit;
        
    }
    
	protected function _initModuleConfiguration()
	{
        
        //http://framework.zend.com/manual/1.8/en/zend.application.available-resources.html
        //Example 4.4. Configuring Modules
        
    }
	
	protected function _initModuleRoutes()
	{
        
        $routesPath = $this->modulePath.'configs/routes.xml';

        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

		$routesConfigObject = new Zend_Config_Xml($routesPath);

        $router->addConfig($routesConfigObject);
	
	}
    
	protected function _initModuleTranslations()
	{
        
        
        
    }
		
}