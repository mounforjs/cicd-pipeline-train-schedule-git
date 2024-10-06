(function() {
    var check;
    const resetDelay = 10000;
    const warningTimeout = 30000;

    var alert = null;

    window.onload = function() {
        checkStatus(true);
    }

    const resetOnInteraction = () => {
        document.onclick = resetIdle;
        document.onkeydown = resetIdle;
        document.onwheel = resetIdle;
    }

    const clearListeners = () => {
        document.onclick = null;
        document.onkeydown = null;
        document.onwheel = null;
    }

    const resetIdle = (timeOffset=resetDelay) => {
        scheduleCheckStatus(true, timeOffset);
    }

    const scheduleCheckStatus = (xhr, delay) => {
        clearTimeout(check)
        check = setTimeout(function() {
            checkStatus(xhr);
        }, delay);
    }

    const fetchStatus = async (xhr) => {
        const response = await fetch(window.location.origin + "/checksession/", {
            method: 'GET',
            mode: 'same-origin',
            cache: 'no-cache',
            headers: (!xhr) ? {} : {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            referrerPolicy: 'no-referrer'
        });

        return response.json();
    }

    const checkStatus = (xhr=null) => {
        resetOnInteraction();

        var d1 = new Date();
        fetchStatus(xhr).then((data) => {
            var timeOffset = (d1 - new Date());

            if (data.redirect) {
                broadcaster.postMessage(JSON.stringify({"action" : "redirect", "timeOffset" : timeOffset}));
            } else {
                if (data.warning) {
                    broadcaster.postMessage(JSON.stringify({"action" : "warning", "timeOffset" : timeOffset}));
                } else if (data.invalid) {
                    broadcaster.postMessage(JSON.stringify({"action" : "invalid", "timeOffset" : timeOffset}));
                } else {
                    scheduleCheckStatus(false, warningTimeout + timeOffset);
                    broadcaster.postMessage(JSON.stringify({"action" : "confirm"}));
                }
            }
        });
    }

    const broadcaster = new BroadcastChannel("timeout");
    const receiver = new BroadcastChannel("timeout");
    receiver.onmessage = (event) => {
        var data = JSON.parse(event.data);
        switch (data.action) {
            case "confirm":
                if (alert) {
                    swal.close();
                    alert = null;
                }
                break;
            case "reset":
                if (alert) {
                    swal.close();
                    alert = null;
                }
                resetIdle(true, 0);
                break;
            case "warning":
                scheduleCheckStatus(false, warningTimeout + data.timeOffset);
                clearListeners();
                if (!alert) {
                    alert = showSweetTimeout("Are you still there?", $icon = 'warning', data.timeOffset, function(confirmed) {
                        broadcaster.postMessage(JSON.stringify({"action" : "reset"}));
                    });
                }
                break;
            case "invalid":
                if (!alert) {
                    alert = showSweetUserConfirm("Your session timed, please login again.", "Session expired.", 'warning', function() {
                        redirect();
                    });
                }
                break;
            case "redirect":
                redirect();
                break;
        }
    };

    const redirect = () => {
        broadcaster.postMessage(JSON.stringify({"action" : "confirm"}));
        window.onbeforeunload = null;
        window.location.reload();
    }
})();