<?php
require 'res/McStatus.class.php';


if(isset($_GET['service']) && isset($_GET['request'])) {
    
    $mc = new McService($_GET['service']);
    
    switch($_GET['request']) {
        
        case 'status':
            echo $mc->getContent(McCSS::BUTTON_STATUS);
            break;
        
        case 'uptime':
            echo $mc->getContent(McCSS::BUTTON_UPTIME);
            break;
        
        case 'location':
            echo $mc->getData(McData::LOCATION);
            break;
        
    }
    
}

if(isset($_GET['time'])) {
    echo McStatus::getTime();
}

if(isset($_GET['list'])) {
    print_r(json_encode(McStatus::getServiceList()));
}

?>