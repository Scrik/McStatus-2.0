var refreshtime = 30;

function load(service) {
    var s = service;
    var status = null;
    
    notify(s);
    
    /*check
     var button = document.getElementById("status-" + service);
     if(getStatus(service) == button) {
     return;
     }
     //end of check */

    $.ajax({
        url: "api.php?status=" + s
    }).done(function(html) {

        status = html;
        
        
        $.ajax({
            url: "api.php?location=" + s
        }).done(function(html) {
            document.getElementById("lang-" + s).innerHTML = html;
        });

        $.ajax({
            url: "api.php?a=" + status + "&service=" + s
        }).done(function(html) {
            if (status === "red") {

                $.ajax({
                    url: "api.php?d=" + s
                }).done(function(data) {

                    if(data == "00:00") {
                        document.getElementById("status-" + s).innerHTML = '<button id="button-' + s + '" curStat="yellow" style="width: 120px;" class="status-popover btn btn-warning" data-toggle="tooltip" title="May be experiencing issues.">Unstable <i class="fa fa-bolt"></i></button>';
                    } else {
                        document.getElementById("status-" + s).innerHTML = '<button id="button-' + s + '" curStat="red" style="width: 120px;" downTime="' + data + '" class="btn btn-danger">Offline (&plusmn;' + data + 'h) <i class="fa fa-exclamation"></i></button>';
                    }
                    
                    
                });
                
            } else {
                document.getElementById("status-" + s).innerHTML = html;
            }
        });

        $.ajax({
            url: "api.php?uptime=" + s
        }).done(function(html) {

            $.ajax({
                url: "api.php?c=" + html
            }).done(function(data) {
                document.getElementById("uptime-" + s).innerHTML = data;
            });

        });


    });
}

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

function setTime() {
    var time;
    $.ajax({
        url: "api.php?b"
    }).done(function(data) {
        time = data;
        document.getElementById("last-refresh").innerHTML = "<b>Last update: <i>" + time + "</i></b>";
    });
}

function getStatus(service) {
    var s = service;
    var status = null;
    var a = null;

    $.ajax({
        url: "api.php?status=" + s
    }).done(function(html) {
        status = html;

        $.ajax({
            url: "api.php?a=" + status + "&service=" + s
        }).done(function(html) {
            a = html;
        });

    });
    return a;
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
startRefresh();
calculateStatus();