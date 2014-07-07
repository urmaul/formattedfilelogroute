<?php

class FormattedFileLogRoute extends CFileLogRoute
{
    /**
     * Log message format
     * @var string
     */
    public $format = "{time} [{level}] [{category}] {message}";
    
    /**
     * Default values array
     * @var array
     */
    public $defaults = array();
    
    /**
     * Static var values
     * @var array
     */
    protected $staticVars = array();
    
    /**
     * Static var names
     * @var array
     */
    protected $staticVarNames = array('ip', 'uri', 'ref', 'sref');
    
    public function init()
    {
        parent::init();
        
        $this->defaults += array(
            'ip'  => '[no_ip]',
            'uri' => '[no_uri]',
            'ref' => '[no_ref]',
        );
        
        foreach ($this->staticVarNames as $name)
            $this->addStaticVar($name);
    }
    
    /**
     * Returns formatted log message.
     * @see CFileLogRoute::formatLogMessage()
     * @param string $message
     * @param string $level
     * @param string $category
     * @param integer $time
     * @return string 
     */
    protected function formatLogMessage($message, $level, $category, $time)
    {
        $msg = explode("\n", $message);
        
        // Take trace to another var
        $trace = array();
        if(YII_DEBUG && YII_TRACE_LEVEL>0 && $level!==CLogger::LEVEL_PROFILE)
        {
            $count = 0;
            do {
                $line = array_pop($msg);

                if (strncmp($line, 'in ', 3)) {
                    array_push($msg, $line);
                    break;
                }
                
                array_push($trace, $line);
                
            } while (++$count < YII_TRACE_LEVEL);
        }
        
        $vars = $this->staticVars + array(
            'time'     => @date('Y/m/d H:i:s', $time),
            'level'    => $level,
            'category' => $category,
            'message'  => $message,
            'msg'      => implode("\n", $msg),
            'trace'    => implode("\n", $trace),
        );
        
        $str = $this->format;
        foreach ($vars as $key => $value) {
            $str = str_replace('{' . $key . '}', $value, $str);
        }
        
        return $str . "\n";
    }
    
    
    /**
     * Adds named static var to vars array if it is used in format
     * @param string $name 
     */
    protected function addStaticVar($name)
    {
        if (strpos($this->format, '{' . $name . '}') !== false) {
            $this->staticVars[$name] = $this->$name;
        }
    }
    
    
    /**
     * Returns named var "ip" - client IP
     * @return string 
     */
    protected function getIp()
    {
        return isset($_SERVER['REMOTE_ADDR'])
            ? $_SERVER['REMOTE_ADDR']
            : $this->defaults['ip'];
    }
    
    /**
     * Returns named var "uri" - current request uri
     * @return string
     */
    protected function getUri()
    {
        $uri = $this->defaults['uri'];
        
        $request = Yii::app()->getComponent('request');
        /* @var $request CHttpRequest */

        if (isset($request) && isset($_SERVER['SERVER_NAME'])) {
			try {
				$uri = $request->getRequestUri();
			} catch (CException $e) {
				// do nothing
			}
        }

        return $uri;
    }
    
    /**
     * Returns named var "ref" - request referer
     * @return string 
     */
    protected function getRef()
    {
        return isset($_SERVER['HTTP_REFERER'])
            ? $_SERVER['HTTP_REFERER']
            : $this->defaults['ref'];
    }
    
    /**
     * Returns named var "sref" - request referer with domain name removed
     * @return string 
     */
    protected function getSref()
    {
        $ref = $this->getRef();
        
        if ($ref !== $this->defaults['ref']) {
            $request = Yii::app()->getComponent('request');
            /* @var $request CHttpRequest */

            if (isset($request) && isset($_SERVER['SERVER_NAME'])) {
                $ref = str_replace($request->getHostInfo(), '', $ref);
            }
        }
        
        return $ref;
    }
}
