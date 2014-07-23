var refreshtime = 30;



function notify(service) {
     /**
      * 1) Den alten Status auslesen
      * 2) Den aktuellen Status mit dem alten Status vergleichen
      * 3) Wenn der alte Status "offline" war und der neue nun "online" ist, notification senden
      */
     
    $.ajax({
        url: 'api.php?status=' + service
    }).done(function(newStatus){
        var oldStatus = document.getElementById('button-' + service); //der alte Status
        
        if(oldStatus.getAttribute('curstat') == 'green' && newStatus == 'red') {
            
            var notify;
            
            if(Notification.permission === 'granted') {
                notify = new Notification(service + ' is now offline.', {
                    body: 'The service ' + service + ' is now offline. Please wait a while.',
                    icon: 'https://cdn1.iconfinder.com/data/icons/perqui/48/minecraft.png'
                });
            }
            
        }
        
        if(newStatus == 'green') {
            
            var notify;
            var downTime = oldStatus.getAttribute('downTime');;
            
            if(Notification.permission === 'granted' && oldStatus.getAttribute('curstat') == 'red') {
                notify = new Notification(service + ' is back online.', {
                    body: 'The service ' + service + ' is back online after \xB1' + downTime + 'h downtime. You can try playing now!',
                    icon: 'https://cdn1.iconfinder.com/data/icons/perqui/48/minecraft.png'	
            	});
                        
            }

        }

    });
}

function setNews() {
    $.ajax({
        url: "api.php?e"
    }).done(function(news) {
        
        if(!news) {
            document.getElementById("mojang-news").setAttribute("style", "visibility:hidden;");
            return;
        }
       
        //news = news.replace("<p>", "");
        //news = news.repalce("</p>", "");
       
        document.getElementById("mojang-news").setAttribute("style", "visibility:show;");
        document.getElementById("mojang-news").innerHTML = news;
    });
}




function startRefresh() {
    var lastUpdate = document.getElementById("last-refresh");
    var counter = refreshtime;

    var id = setInterval(function() {

        if (counter == refreshtime) {
            setTime();
        }

        counter--;
        if (counter < 0) {
            setProgressbar(refreshtime);
            
            setNews();
            calculateStatus();
            clearInterval(id);
            startRefresh();
        } else {
            setProgressbar(counter);
        }
    }, 1000);
}

function setProgressbar(progress) {
    document.getElementById("countdown-to-refresh").innerHTML = "Refreshing in " + progress.toString() + (progress.toString() == 1 ? " second" : " seconds") + ".";
    document.getElementById("countdown-bar").setAttribute("style", "width: " + (100 - ((100 / refreshtime) * progress)) + "%;");
}


function calculateStatus() {
    document.getElementById("last-refresh").innerHTML = "<b>Last update: <i>calculate current server status...</i> <i class=\"fa fa-refresh fa-spin\"></i></b>";
    var server = new Array(
            "minecraft.net",
            "skins.minecraft.net",
            "session.minecraft.net",
            "account.mojang.com",
            "auth.mojang.com",
            "authserver.mojang.com",
            "sessionserver.mojang.com");

    for (var i = server.length; i >= 0; i--) {
        load(server[i]);
    }
}

setNews();
//startRefresh();
//calculateStatus();