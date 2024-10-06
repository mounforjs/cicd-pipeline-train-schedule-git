$(document).ready(function() {

    $.ajax({
        type: "POST",
        url: window.location.origin + "/user/sessions"
    }).done(function(usession) {

        if (usession == 1) {
            var controller = new AbortController()
            var signal = controller.signal
            var interval;

            var dismissed = new Set();

            function fetch_notifications(delay=10000) {
                clearTimeout(interval);
                controller = new AbortController();

                interval = setTimeout(function() {
                    load_unseen_notification(true);
                }, delay);
            }

            async function get_unseen_notifications() {
                //prevent clicking until fetch returns
                $("#notification-panel").css("pointer-events", "none");

                const response = await fetch(window.location.origin + "/view/fetch", {
                    method: 'POST',
                    mode: 'same-origin',
                    cache: 'no-cache',
                    headers: {
                    'Content-Type': 'application/json'
                    },
                    referrerPolicy: 'no-referrer',
                    signal: signal,
                    body: JSON.stringify({
                        "dismissed": Array.from(dismissed)
                    })
                });

                return await response.json();
            }

            function load_unseen_notification(interval=false) {
                get_unseen_notifications().then(data => {
                    if (data) {
                        notifications = data.notification;

                        if ($(".notification-box").length <= 0) { //empty, add all
                            $('.panel-body').html(Object.values(data.notification).reverse().join(''));
                        } else {
                            //remove any that shouldnt be listed - dismissed after call or not in data
                            $(".notification-box").each(function(index) {
                                var id = $(this).find("a.dismiss-notification").data("id");

                                if (!notifications[id]) {
                                    $(this).remove();
                                    dismissed.delete(id);
                                }
                            });

                            var count = $(".notification-box").length;
                            var lastExist = -1;
                            for (const id in notifications) {
                                var exists = $(".notification-box a[data-id='" + id + "']");
                                //if notification already exists, update index of last known noti and replace to update
                                if (exists.length > 0) {
                                    lastExist = exists.index();
                                    
                                    if ($(exists).hasClass("read") || dismissed.has(id)) { 
                                        $(this).remove();
                                    } else { 
                                        $(exists).parent(".notification-box").replaceWith(notifications[id]);
                                    }
                                } else {
                                    //notification exists and should be prepended to top, or before last noti
                                    if (count <= 0) {
                                        $(notifications[id]).prependTo("#notification-panel .panel-body");
                                    } else {
                                        var lastNoti = $(".notification-box").eq(lastExist); lastNotiId = lastNoti.find("a.dismiss-notification").data("id");
                                        lastNotiId > id ? lastNoti.after(notifications[id]) : lastNoti.before(notifications[id]);
                                    }
                                }
                                
                            }
                        }

                        if (data.unseen_notification > 0) {
                            $('.-count').removeClass('d-none').html(data.unseen_notification);
                            $("#clearNotification").prop("disabled", false);
                            $("#clearNotification").removeClass("disabled");
                        } else {
                            $('.-count').addClass('d-none').html(data.unseen_notification);
                            $("#clearNotification").prop("disabled", true);
                            $("#clearNotification").addClass("disabled");
                        }

                        $("#notification-panel").css("pointer-events", "unset");
                    }

                    if (interval) { fetch_notifications(); }
                });
            }

            $(document).on('click', '.ringBell', function() {
                $(".notification-panel").toggle("slow");
                $('.ringBell').html('');
            });

            $(document).on('click', '#clearNotification', function() {
                $(".dismiss-notification").each((idx, elem) => {
                    elem.click();
                });
            });

            $(document).mouseup(function(e) {
                var container = $(".notification-panel");
                // If the target of the click isn't the container
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    container.hide();
                }
            });

            $(document).on('click', '.dismiss-notification', function() {
                var name = $(this).attr("class");

                var box = $(this).closest(".notification-box");
                var a = (name.includes("dismiss-notification")) ? $(this).find("a") : $(this).next().find("a");

                var id = $(a).attr('data-id');
                if ($(a).attr('stat-id') == 0) {
                    $(box).removeClass('unread');
                    $(box).find('i').removeClass('fa fa-envelope').addClass('fa fa-envelope-open-o');

                    //add to list and prevent any current and future queries
                    dismissed.add(parseInt(id));
                    controller.abort();

                    //reset queries
                    fetch_notifications(2000);
                }

            });

            load_unseen_notification(true);
        }

    });

});