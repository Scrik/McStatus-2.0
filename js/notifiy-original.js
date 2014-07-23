
            var notifi = document.getElementById('notifi');
            
            if(Notification.permission === 'granted') {
                notifi.innerHTML = 'Disable Notifications.';
            } else if(Notification.permission === 'default' || Notification.permission === 'denied') {
                notifi.innerHTML = 'Enabled Notifications.';
            }
            
            notifi.addEventListener('click', function(e) {
                e.preventDefault();

                if (!window.Notification) {
                    alert('Sorry, notifications are not supported.');
                } else {

                    Notification.requestPermission(function(p) {
                        
                        if(Notification.permission === 'granted') {
                            notifi.innerHTML = 'Disable Notifications.';
                        } else if(Notification.permission === 'default' || Notification.permission === 'denied') {
                            notifi.innerHTML = 'Enabled Notifications.';
                        }
                        
                        if (p === 'granted') {
                            new Notification('Notifications', {
                                body: 'The notifiication service is now enabled.',
                                icon: 'https://cdn1.iconfinder.com/data/icons/perqui/48/minecraft.png'
                            });
                        }

                    });


                }
            });