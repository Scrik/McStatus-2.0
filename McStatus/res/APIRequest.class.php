<?php

require_once 'McStatus.class.php';

class APIRequest {
    
     /*
      * [
      *     'minecraft.net' => [
      *         'uptime' (downtimeMinutes, uptimeMinutes, uptimePercent),
      *         'info' (location),
      *         'history' (list of downtimes)
      *     ]
      * ]
      * 
      * 
      */
    
    private $apiCode;
    private $services;
    
    public function __construct($apiCode = null, $services = null) {
        $this->apiCode = $apiCode;
        $this->services = $services;
        
        $count = DB::getInstance()->count('apiCodes', array(
            'code' => $this->apiCode
        ));
        
        if($count != 1) {
            self::printResult(array("Unknown API-Code please check the code or contact the admin \"yonas [at] krueger-jan [dot] de\ (without the spaces)!\"."));
            exit;
        }
    }
    /**
    Die Tabelle 'services' 
    service | online | offline | currentStatus | currentDowntime | location
    */
    public function generate() {
        
        $result = array();
        
        if(is_null($this->services)) {
            
            $query = DB::getInstance()->select('status');
            
            foreach($query->getResult() as $row) {
                
                $cache = array(
                    $row->service => [
                        'info' => [
                            'location' => $mc->getData(McData::LOCATION)
                        ],
                        'uptime' => [
                            'minutes' => [
                                'online' => intval($mc->getData(McData::UPTIME_MINUTES)),
                                'offline' => intval($mc->getData(McData::DOWNTIME_MINUTES))
                            ],
                            'percent' => $mc->getData(McData::UPTIME_CALCULATE),
                        ],
                        'history' => [
                            
                        ]
                    ]
                );
                
            }
            
        }
        
    }
    
    private static function printResult($result) {
        print_r(json_encode($result, JSON_PRETTY_PRINT));
    }
    
}

?>