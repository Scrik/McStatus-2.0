<?php
require 'inc/QuickDatabase/DB.class.php';
/**
    Die Tabelle 'services' 
    service | online | offline | currentStatus | currentDowntime | location
 */

abstract class McCSS {
    
    const BUTTON_STATUS     = 'BUTTON_STATUS';
    const BUTTON_CLASS      = 'BUTTON_CLASS';
    const BUTTON_UPTIME     = 'BUTTON_UPTIME';
    const BUTTON_TEXT       = 'BUTTON_TEXT';
    
    const BUTTON_STATUS_DEFAULT = 'BUTTON_STATUS_DEFAULT';
    const BUTTON_UPTIME_DEFAULT = 'BUTTON_UPTIME_DEFAULT';
    
    
}

abstract class McData {
    
    const UPTIME_MINUTES        = 'UPTIME_MINUTES';
    const UPTIME_CALCULATE      = 'UPTIME_CALCULATE';
    
    const DOWNTIME_MINUTES      = 'DOWNTIME_MINUTES';
    const DOWNTIME_CURRENT      = 'DOWNTIME_CURRENT';
    CONST DOWNTIME_CALCULATE    = 'DOWNTIME_CALCULATE';
    
    const LOCATION              = 'LOCATION';
    const STATUS                = 'STATUS';
    
}

class McService {
    
    private $_service;
    
    /**
     * 
     * @param String $service Die URL zum Dienst, wie z.B.: 'api.mojang.com'
     */
    public function __construct($service) {
        $this->_service = $service;
    }
    
    public function getService() {
        return $this->_service;
    }
    
    public function getContent($value) {

        switch($value) {
            
            case McCSS::BUTTON_STATUS:
                $buttonTextStatus = '<button id="button-%s" style="width: 120px;" curStat="%s" class="btn btn-%s" style="width: 120px;">%s</i></button>';
                $buttonStatus = $this->getData(McData::STATUS);
                return sprintf(
                        $buttonTextStatus, 
                        
                        $this->_service,
                        $buttonStatus,
                        $this->getContent(McCSS::BUTTON_CLASS),
                        sprintf($this->getContent(McCSS::BUTTON_TEXT), $this->getData(McData::DOWNTIME_CALCULATE))
                        );
                    
            case McCSS::BUTTON_CLASS:
                
                switch($this->getData(McData::STATUS)) {
                
                    case 'green': return 'success';
                    
                    case 'yellow': return 'warning';
                    
                    case 'red': return 'danger';
                    
                    default: return 'default';
                }
                
                break;
            
            case McCSS::BUTTON_TEXT:
                        
                switch($this->getData(McData::STATUS)) {
                
                    case 'green': return 'Online';
                    
                    case 'yellow': return 'Unstable';
                    
                    case 'red': return 'Offline(&plusmn;%sh)';
                    
                    default: return 'default';
                }
                
                break;
            
            case McCSS::BUTTON_UPTIME:
                    $uptime = $this->getData(McData::UPTIME_CALCULATE);
                    if ($uptime <= 100 && $uptime >= 98){ return '<button class="btn btn-success btn-sm" style="width: 70px;">' . $uptime . '%</button>';
                    } else if ($uptime <= 97.99 && $uptime >= 96){ return '<button class="btn btn-warning btn-sm" style="width: 70px;">' . $uptime . '%</button>';
                    } else if ($uptime <= 95.99 && $uptime >= 0) { return '<button class="btn btn-danger btn-sm" style="width: 70px;">' . $uptime . '%</button>';
                    } else { return '<button class="btn btn-default btn-sm" style="width: 70px;">Unknown...</button>'; }                  
                break;
                
            case McCSS::BUTTON_STATUS_DEFAULT: return '<button id="button-' . $this->_service . '" style="width: 120px;" curStat="default" class="btn btn-default">Calculate... <i class="fa fa-refresh fa-spin"></i></button>';        
            case McCSS::BUTTON_UPTIME_DEFAULT: return '<button class="btn btn-default btn-sm" style="width: 70px;">Unknown...</button>'; 

        }
        
       
    }
    
    public function getData($value) {

        switch($value) {
            
            /**
             * Gibt den Standort des Servers zurück.
             */
            case McData::LOCATION:
                return $this->fetchData()->location;
                
            
            /**
             * Gibt die Online-Zeit in Minuten zurück.
             */
            case McData::UPTIME_MINUTES:
                return $this->fetchData()->online;
            
            /**
             * Gibt die Uptime in Prozent zurück.
             * Rechnung: onlineZeit / (onlineZeit + offlineZeit) * 100
             */
            case McData::UPTIME_CALCULATE:
                // green / (green + red) * 100;
                $calculateDowntime  = $this->getData(McData::DOWNTIME_MINUTES);
                $calculateUptime    = $this->getData(McData::UPTIME_MINUTES);
                $calculatedUptime   = ($calculateUptime / ($calculateDowntime + $calculateUptime) * 100);
                return round($calculatedUptime, 2);
                
            /**
             * Gibt die Offline-Zeit in Minuten zurück.
             */
            case McData::DOWNTIME_MINUTES:
                return $this->fetchData()->offline;
            
            /**
             * Gibt die Zeit formatiert (hh:mm) zurück, falls der Server offline ist.
             */
            case McData::DOWNTIME_CALCULATE:              
                $downtimeMinutes = $this->getData(McData::DOWNTIME_CURRENT);
                $downHours      = floor($downtimeMinutes / 60);
                $downMinutes    = ($downtimeMinutes % 60);
    
                return sprintf("%02d:%02d", ($downHours == -1 ? 0 : $downHours), ($downMinutes == -1 ? 0 : $downMinutes));
            
            case McData::DOWNTIME_CURRENT:
                return $this->fetchData()->currentDowntime;
                
            /**
             * Gibt den Status des Dienstes zurück
             * Mögliche Werte: green (Online), yellow (Unstabil), red (Offline)
             */
            case McData::STATUS:
                return $this->fetchData()->currentStatus;
                
        }
        
    }
    
    private function fetchData() {
        return DB::getInstance()->select('services', null, array(
                    'service',
                    '=',
                    $this->_service
                ))->getResult()[0];
    }
    
}

?>