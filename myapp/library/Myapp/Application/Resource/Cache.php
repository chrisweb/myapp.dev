<?php

/**
 * http://mwop.net/blog/231-Creating-Re-Usable-Zend_Application-Resource-Plugins.html
 * http://framework.zend.com/manual/1.12/en/zend.application.theory-of-operation.html
 */
class Myapp_Application_Resource_Cache extends Zend_Application_Resource_ResourceAbstract
{
    
    protected $_cache = null;
    
    protected $_cacheLifetime = null; // null = infinite cache lifetime
    
    protected $_cacheDirectory = null;
    
    protected $_cacheMasterfiles = array();
    
    /**
     * 
     * cache resource plugin initialization
     * 
     * @return Zend_Cache_Core|null
     */
    public function init($options = array())
    {
        
        Zend_Debug::dump($options, '$options: ');
        Zend_Debug::dump($this->getOptions(), '$this->getOptions(): ');
        
        foreach ($this->getOptions() as $key => $value) {
            switch (strtolower($key)) {
                case 'directory':
                    $this->_cacheDirectory = $value;
                    break;
                case 'lifetime':
                    $this->_cacheLifetime = $value;
                    break;
                case 'masterfiles':
                    $this->_cacheMasterfiles = $value;
                    break;
            }
        }
        
    }

    /**
     * 
     * get a cache instance
     * 
     * @return Zend_Cache_Core|null
     */
    public function getCache()
    {
        
        Zend_Debug::dump($this->_cacheLifetime, '$this->_cacheLifetime: ');
        Zend_Debug::dump($this->_cacheDirectory, '$this->_cacheDirectory: ');
        
        if (is_null($this->_cache)) {
            
            if (APC_SUPPORT) {
                
                $frontendOptions = array(
                    'automatic_serialization' => true,
                    'lifetime' => $this->_cacheLifetime
                );
                
                $backendOptions = array();
                
                $this->_cache = Zend_Cache::factory('Core', 'Apc', $frontendOptions, $backendOptions);
                
            } else {
                
                if (is_null($this->_cacheDirectory)) {
                    
                    $this->_cacheDirectory = APPLICATION_PATH.'/caches/';
                    
                }
                
                if (!is_dir($this->_cacheDirectory)) mkdir($this->_cacheDirectory, 0755);
                
                $frontendOptions = array(
                    'master_files' => $this->_cacheMasterfiles,
                    'automatic_serialization' => true,
                    'lifetime' => $this->_cacheLifetime
                );
                
                $backendOptions = array('cache_dir' => $this->_cacheDirectory);
                
                $this->_cache = Zend_Cache::factory('File', 'File', $frontendOptions, $backendOptions);
                
            }
            
        }
        
        return $this->_cache;
        
    }
    
}
