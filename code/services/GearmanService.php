<?php
/**
 *
 * Used to interface with the gearman service
 * 
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class GearmanService
{
    
    const HANDLER_NAME = 'silverstripe_handler';
    
    public $host = 'localhost';
    public $port = '4730';
    
    public $name = null;
    
    public function __call($method, $args)
    {
        $client = new \Net\Gearman\Client();
        $client->addServer($this->host, $this->port);

        array_unshift($args, $method);
        array_unshift($args, Director::baseFolder());
        
        $client->doBackground(self::HANDLER_NAME, serialize($args));
    }
    
    /**
     * Send a job, with a particular type
     * 
     * @param string $type
     *				one of background|scheduled
     * @param string $method
     *				the name of the job, must have a worker defined for it in a GearmanHandler somewhere
     * @param array $args
     *				the method arguments
     */
    public function sendJob($type, $method, $args = array(), $timestamp = 0)
    {
        $client = new \Net\Gearman\Client();
        $client->addServer($this->host, $this->port);
        
        array_unshift($args, $method);
        array_unshift($args, Director::baseFolder());
        
        switch ($type) {
            case 'scheduled': {
                $client->doEpoch(self::HANDLER_NAME, serialize($args), $timestamp);
                break;
            };
            default: {
                $client->doBackground(self::HANDLER_NAME, serialize($args));
            }
        }
    }

    public function handleCall($args)
    {
        if (!count($args)) {
            return;
        }
        $workerImpl = ClassInfo::implementorsOf('GearmanHandler');

        $path = array_shift($args);
        $method = array_shift($args);
        
        foreach ($workerImpl as $type) {
            $obj = Injector::inst()->get($type);
            if ($obj->getName() == $method) {
                call_user_func_array(array($obj, $method), $args);
            }
        }
    }
}
