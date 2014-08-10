<?php
require_once 'inc/QuickDatabase/DB.class.php';
/**
    Die Tabelle 'services' 
    service | online | offline | currentStatus | currentDowntime | location
 */
class McStatus {
    
    private static $serviceList = [
        'minecraft.net' => [
            'Minecraft', 'Website'
            ],
        'session.minecraft.net' => [
            'Minecraft', 'Multiplayer Authentication'
            ],
        'skins.minecraft.net' => [
            'Minecraft', 'Skins Service'
            ],
        'textures.minecraft.net' => [
            'Minecraft', 'Textures'
            ],
        'account.mojang.com' => [
            'Mojang', 'Account System'
            ],
        'auth.mojang.com' => [
            'Mojang', 'Yggdrassil Login Service'
            ],
        'authserver.mojang.com' => [
            'Mojang', 'Authentication Service'
            ],
        'sessionserver.mojang.com' => [
            'Mojang', 'Session Service'
            ],
        'api.mojang.com' => [
            'Mojang', 'Public API'
            ]
    ];
    
    public static function renderTable() {
        
        foreach(self::$serviceList as $service => $info) {

            $mc = new McService($service);

                echo '<tr>';
                    echo '<td id="status-' . $service . '" style="width: 120px; text-align: left;">' . $mc->getContent(McCSS::BUTTON_STATUS) . '</td>';
                    echo '<td style="width: 150px; text-align: left;">' . $info[1] . '</td>';
                    echo '<td id="uptime-' . $service . '" style="width: 70px; text-align: left;">' . $mc->getContent(McCSS::BUTTON_UPTIME) . '</td>';
                    echo '<td style="width: 150px; text-align: left;" class="hidden-xs hidden-xss hidden-sm">' . $info[0] . ' Service</td>';
                    echo '<td style="width: 120px; text-align: left;" class="hidden-xs hidden-xss hidden-sm">' . $service . '</td>';
                    echo '<td id="lang-' . $service . '" style="width: 120px; text-align: left;" class="hidden-sm hidden-xs hidden-xxs">' . $mc->getData(McData::LOCATION) . '</td>';
                echo '</tr>';
        }
        
    }
    
    public static function updateTable() {
        
        foreach(self::$serviceList as $service => $v) {
            
            $mc = new McService($service);

            $count = DB::getInstance()->count('services', array('service', '=', $service));
            $status     = self::getServerStatus($service);
            $originalStatus = $status;
            $status     = ((is_null($status) || empty($status) || $status == 'yellow') ? 'green' : $status);
            $isOnline   = ($status == 'green');
            $location   = self::getLocation($service);
            
            if($count == 0) {
                
                DB::getInstance()->insert('services', array(
                    'service'           => $service,
                    'online'            => 0,
                    'offline'           => 0,
                    'currentStatus'     => $status,
                    'currentDowntime'   => '-1',
                    'location'          => $location
                ));
                
                continue;
            }
         

            DB::getInstance()->update('services', array(
                'online'            => ($isOnline ? ($mc->getData(McData::UPTIME_MINUTES) + 1) : ($mc->getData(McData::UPTIME_MINUTES))),
                'offline'           => (!($isOnline) ? ($mc->getData(McData::DOWNTIME_MINUTES) + 1) : ($mc->getData(McData::DOWNTIME_MINUTES))),
                'currentStatus'     => $status,
                'currentDowntime'   => (!($isOnline) ? ($mc->getData(McData::DOWNTIME_CURRENT) + 1) : (-1)),
                'location'          => $location
            ), array(
                'service',
                '=',
                $service
            ));
            
            if(!($isOnline) || ($originalStatus == 'yellow')) {
                DB::getInstance()->insert('log', array(
                    'service'   => $service,
                    'date'      => date('d.m.Y G:i (T)'),
                    'status'    => $status
                ));
            }
            
        }
        
    }
    
    public static function getServiceList() {
        return self::$serviceList;
    }
    
    public static function getTime() {
        return date('h:i:s A T');
    }
    
    private static function getLocation($service) {
        $a = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . gethostbyname($service)));
        $b = $a->geoplugin_city . ', ' . $a->geoplugin_countryName;
        return (trim($b) == "," ? "Ashburn, United States" : $b);
    }
    
    private static function getServerStatus($service) {
        return json_decode(file_get_contents("http://status.mojang.com/check?service=" . $service))->$service;
    }

}

?>