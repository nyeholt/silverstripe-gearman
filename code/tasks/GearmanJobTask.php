<?php

/**
 * Description of GearmanJobTask
 *
 * @author marcus
 */
class GearmanJobTask extends BuildTask
{
    public function run($request)
    {
        if (PHP_SAPI != 'cli') {
            throw new Exception("Please only run this task from the command line");
        }
        $data = $request->getVar('gearman_data');
        
        if (!strlen($data)) {
            echo "Error";
            return;
        }
        
        $ser = trim(base64_decode($data));
        if (!$ser || $ser{0} != 'a') {
            echo "Invalid data: $ser";
            return;
        }
        
        $data = unserialize($ser);
        
        if (count($data) < 2) {
            echo "Not enough parameters";
            return;
        }
        
        $handler = Injector::inst()->get('GearmanService');
        $handler->handleCall($data);
    }
}
