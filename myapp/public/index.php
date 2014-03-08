<?php

// Define path to the application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define path to the upload directory
define('UPLOAD_PATH', realpath(dirname(__FILE__).'/upload'));

// Define application environment
defined('APPLICATION_ENVIRONMENT') || define('APPLICATION_ENVIRONMENT', (getenv('APPLICATION_ENVIRONMENT') ? getenv('APPLICATION_ENVIRONMENT') : 'production'));

// check for apc support
define('APC_SUPPORT', extension_loaded('apc') && ini_get('apc.enabled'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

// create a autoloader instance
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

// put autoloader instance in registry to retrieve it in bootstrap
Zend_Registry::set('Autoloader', $autoloader);

// define some values needed by the cache
$cacheDirectory = APPLICATION_PATH.'/caches/';
$configurationPath = APPLICATION_PATH.'/configs/application.ini';
$configurationName = 'applicationconfiguration';
$cacheLifetime = 2678400;

// check if the cache directory exists, if not create it
if (!is_dir($cacheDirectory)) mkdir($cacheDirectory, 0755);

if (APC_SUPPORT) {
    
    // if no lifetime is defined default will be 3600
    $frontendOptions = array(
        'automatic_serialization' => true,
        'lifetime' => $cacheLifetime
    );
    
    $backendOptions = array();
    
    $configurationCache = Zend_Cache::factory('Core', 'Apc', $frontendOptions, $backendOptions);
    
} else {
    
    // if no lifetime is defined default will be 3600
    $frontendOptions = array(
        'master_files' => array($configurationPath),
        'automatic_serialization' => true,
        'lifetime' => $cacheLifetime
    );
    
    $backendOptions = array('cache_dir' => $cacheDirectory);
    
    $configurationCache = Zend_Cache::factory('File', 'File', $frontendOptions, $backendOptions);
    
}

if (!$configuration = $configurationCache->load($configurationName)) {
	$configuration = new Zend_Config_Ini($configurationPath, APPLICATION_ENVIRONMENT);
	$configurationCache->save($configuration, $configurationName);
}

// store configuration in registry
Zend_Registry::set('Configuration', $configuration);

// create an application instance
$application = new Zend_Application(
    APPLICATION_ENVIRONMENT,
    $configuration
);

// BOOTSTRAP
try {
   $application->bootstrap()->run();
} catch (Exception $exception) {
    echo '<html><body><center>'
       . 'An exception occurred while bootstrapping the application.';
       print_r($exception->getMessage());
    if (defined('APPLICATION_ENVIRONMENT')
        && APPLICATION_ENVIRONMENT != 'production'
    ) {
        echo '<br /><br />' . $exception->getMessage() . '<br />'
           . '<div align="left">Stack Trace:'
           . '<pre>' . $exception->getTraceAsString() . '</pre></div>';
    }
    echo '</center></body></html>';
    exit(1);
}