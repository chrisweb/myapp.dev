<?php

class Myapp_Application_Resource_Cache extends Zend_Application_Resource_ResourceAbstract
{
    
    protected $_cache = null;
    
    /**
     * 
     * @return type
     */
    public function init()
    {
        
        return $this->getCache();
        
    }

    /**
     * 
     */
    public function getCache()
    {
        
        if (is_null($this->_cache)) {
            
            if (APC_SUPPORT) {
                
                $frontendOptions = array(
                    'automatic_serialization' => true,
                    'lifetime' => $cacheLifetime
                );
                
                $backendOptions = array();
                
                $configurationCache = Zend_Cache::factory('Core', 'Apc', $frontendOptions, $backendOptions);
                
            } else {
                
                $frontendOptions = array(
                    'master_files' => array(),
                    'automatic_serialization' => true,
                    'lifetime' => $cacheLifetime
                );
                
                $backendOptions = array('cache_dir' => $cacheDirectory);
                
                $configurationCache = Zend_Cache::factory('File', 'File', $frontendOptions, $backendOptions);
                
            }
            
        }
        
        return $this->_cache;
        
    }
    
}
