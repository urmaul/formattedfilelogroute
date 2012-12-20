<?php

class FormattedFileLogRoute extends CFileLogRoute
{
    public $format = "{time} [{level}] [{category}] {message}";
    
    protected $vars = array();
        
    public function init()
    {
        parent::init();
        
        if (strpos($this->format, '{ip}') !== false) {
            $this->vars['ip'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '[no_ip]';
        }
        
        $this->addStaticVar('uri');
    }
    
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
        
        $vars = $this->vars + array(
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
    
    
    protected function addStaticVar($name)
    {
        if (strpos($this->format, '{' . $name . '}') !== false) {
            $this->vars[$name] = $this->$name;
        }
    }
    
    
    protected function getUri()
    {
        $uri = '[no_uri]';
        
        $request = Yii::app()->getComponent('request');
        /* @var $request CHttpRequest */
        
        if (isset($request)) {
            try {
                $uri = $request->getRequestUri();

            } catch (CException $exc) {
            }
        }

        return $uri;
    }
}
