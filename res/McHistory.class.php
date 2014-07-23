<?php
require 'McStatus.class.php';

abstract class HistoryCSS {
    
    const OPTIONS;
    const CONTENT;
    
}

class McHistory {
    
    public static function createHistory() {
        
        $history = array();

        foreach(McStatus::getServiceList() as $service => $info) {

            $mc = new McService($service);
            
            $info = [
                $mc->getService() => [
                    
                    'info' => [
                      'location' => $mc->getData(McData::LOCATION)  
                    ],
                    
                    'uptime' => [
                        'minutes' => [
                            'online'    => intval($mc->getData(McData::UPTIME_MINUTES)),
                            'offline'   => intval($mc->getData(McData::DOWNTIME_MINUTES)) 
                        ],
                        'percent' => $mc->getData(McData::UPTIME_CALCULATE),
                    ]
                    
                ]
            ];
            
            array_push($history, $info);
        }
        

        $date = date('m-Y');
        $count = DB::getInstance()->count('history', array(
            'date',
            '=',
            $date
        ));
        
        if($count == 0 && (date('d') == date('t'))) {
            DB::getInstance()->insert('history', array(
               'date' => $date,
               'historyString' => json_encode($history) 
            ));
            
            DB::getInstance()->databaseQuery("DELETE FROM `services`");
        }
        
    }
    
    public static function getOptions() {
        $query = DB::getInstance()->select('history');

        foreach($query->getResult() as $history) {

            echo "<option href=\"#tab-{$history->id}\" data-toggle=\"tab\">{$history->date}</option>";
            
        }

    }
    
}

?>