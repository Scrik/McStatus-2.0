var refreshtime = 30;

function loadService(service) {
    
    $.ajax({
        url: "handle.php?service=" + service + "&request=status"
    }).done(function(status) {
         document.getElementById("status-" + service).innerHTML = status;
    });
    
    $.ajax({
        url: "handle.php?service=" + service + "&request=uptime"
    }).done(function(uptime) {
         document.getElementById("uptime-" + service).innerHTML = uptime;
    });
 
     $.ajax({
        url: "handle.php?service=" + service + "&request=location"
    }).done(function(location) {
         document.getElementById("lang-" + service).innerHTML = location;
    });
    
}

function load() {
    var counter = refreshtime;

    var id = setInterval(function() {

        if (counter === refreshtime) {
            $.ajax({
                url: "handle.php?time"
            }).done(function(time) {
                document.getElementById("last-refresh").innerHTML = "<b>Last update: <i>" + time + "</i></b>";
            });
        }

        counter--;
        if (counter < 0) {
            setBar(refreshtime);
            
            //setNews();
            calculate();
            clearInterval(id);
            load();
        } else {
            setBar(counter);
        }
    }, 1000);
}

function setBar(progress) {
    document.getElementById("countdown-to-refresh").innerHTML = "Refreshing in " + progress.toString() + (progress.toString() === "1" ? " second" : " seconds") + ".";
    document.getElementById("countdown-bar").setAttribute("style", "width: " + (100 - ((100 / refreshtime) * progress)) + "%;");
}

function calculate() {
    
    $.ajax({
        url: "handle.php?list"
    }).done(function(list){
        var services = JSON.parse(list);
        
        for(var service in services) {
            loadService(service);
        }
        
    });
    
}

load();

