(function() {
    const slug = location.pathname.split("/")[3];
    const game_session_id = document.querySelector("input[name='game_session_id']").value;

    window.onload = function() {
        history.pushState({}, "", window.location.origin + window.location.pathname);

        try {
            const game_session = new GameSession();
            game_session.start_session();
        } catch (e) {
            console.log(e);
            game_session.end_session();
        }
    }

    function GameSession() {
        var self = this;
        var check;

        const game = new Game();
        const countdown = new Countdown(4, document.querySelector('.count'), document.querySelector('#count-template'));
        const stopwatch = new Stopwatch(70, false);
        
        this.start_session = () => {
            game.ready().then(() => {
                return this.schedule_check_in(0);
            }).then(() => {
                return countdown.start();
            }).then(() => {
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({
                    'level_name': slug,
                    'event': 'level_start'
                });
                stopwatch.start();
                return game.start();
            }).catch((e) => {
                console.log(e);
                showSweetAlertForce("We ran into an error.", "Whoops!", "error");
            }).finally(() => {
                this.end_session();
            });
        }

        this.end_session = () => {
            var game_score = {};

            try {
                clearTimeout(check);
                stopwatch.stop();
                game_score = game.end();

                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({
                    'level_name': slug,
                    'success': 'true',
                    'event': 'level_end'
                });
            } catch (e) {
                console.log(e);
            } finally {
                showConfetti();
                var thisObj = this;
                setTimeout(function(){
                    showSweetUserConfirm("Click on Continue to see your score.", "Thanks for playing!", $icon='info', function(confirmed) {
                        if (confirmed) {
                            thisObj.submit(game_score);
                        }
                    });
                }, 5000);
            }
        }

        this.check_in = async () => {
            var data = JSON.stringify({
                game_session_id : game_session_id
            });

            const response = await fetch(window.location.origin + "/games/check_in/", {
                method: 'POST',
                body: data,
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                mode: 'same-origin',
                cache: 'no-cache',
                referrerPolicy: 'no-referrer'
            });

            return response.json();
        }

        this.schedule_check_in = (_delay) => {
            return new Promise((resolve, reject) => {
                clearTimeout(check);
                check = setTimeout(function() {
                    var d1 = new Date();
                    self.check_in().then((data) => {
                        if (!data || (data && data.status && data.status != "success")) {
                            reject();
                        } else {
                            resolve();
                            var timeOffset = (d1 - new Date());
            
                            self.schedule_check_in(5000 + timeOffset).catch((e) => {
                                self.end_session();
                            });
                        }
                    }).catch((e) => {
                        self.end_session();
                    });
                }, _delay);
            });
        }

        this.submit = (game_score) => {
            try {
                $.ajax({
                    type:"POST",
                    data: {
                        game_session_id : game_session_id,
                        ...game_score
                    },
                    dataType: "json",
                    url: window.location.origin + '/games/submit',
                    beforeSend: function () {
                        $('.container-fluid').html('<div class="d-flex justify-content-center"><div class="spinner-border text-success" role="status"><span class="sr-only">Loading...</span></div></div>');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            window.location.pathname = data.redirect;
                        } else {
                            showSweetAlertForce("Unable to submit score", "Whoops!", "error");
                            setTimeout(function() {
                                window.location.pathname = data.redirect;
                            }, 3000);
                        }
                    },
                    error: function(e) {
                        showSweetAlertForce("Unable to submit score", "Whoops!", "error");
                        setTimeout(function() {
                            window.location.pathname = data.redirect;
                        }, 3000);
                    }
                });
            } catch (e) {
                showSweetAlertForce("Unable to submit score", "Whoops!", "error");
                setTimeout(function() {
                    window.location.pathname = data.redirect;
                }, 3000);
            }
        }
    }
        
    function Stopwatch(increment, error) {
        var self = this;

        var start_time, timeout;
        this.increment = increment;

        this.start = function() {
            start_time = Date.now() + this.increment;
            timeout = setInterval(this.updateTimer, this.increment);
        }

        this.stop = function() {
            clearInterval(timeout);
        }

        this.pad = function(number, length) {
            var str = '' + number;
            while (str.length < length) {str = '0' + str;}
            return str;
        }

        this.formatTime = function(time) {
            var hour = Math.floor((time / (1000 * 60 * 60)) % 24), 
                min = Math.floor((time / (1000 * 60)) % 60),
                sec = Math.floor((time / 1000) % 60),
                ms = Math.floor((time % 1000) / 10);

            return (hour > 0 ? hour : "00") + ":" + (min > 0 ? this.pad(min, 2) : "00") + ":" + this.pad(sec, 2) + "." + (ms > 0 ? this.pad(ms, 2) : "00");
        }

        this.responseTime = function(time) {
            sec = parseInt(time / 100);
            hundredths = this.pad(time - (sec * 100), 2);
            return sec + '.' + hundredths;
        }

        this.updateTimer = function() {  
            total_time = Date.now() - start_time;
            $("#stopwatch").html(self.formatTime(total_time));
            $('#responseTime').val(self.responseTime(total_time));
        }
    }

    Countdown = function() {
        _(this).bindAll('update', 'executeAnimation', 'finishAnimation');
        this.setVars.apply(this, arguments);
    };

    Countdown.prototype = {
        duration: 1000,

        setVars: function(time, el, template) {
            this.max = time;
            this.time = time;
            this.el = el;
            this.template = _(template.innerHTML).template();
            this.delta = -1;
        },

        start: function() {
            var that = this;
            this.update();

            return new Promise((resolve, reject) => {
                (function waitFor(){
                    if (that.time <= 0) {
                        that.stop();
                        return resolve();
                    }
                    setTimeout(waitFor, 1);
                })();
            });
        },

        stop: function() {
            $('.countcontain').remove();
        },

        update: function() {
            this.checkTime();
            this.setSizes();

            this.setupAnimation();
            _(this.executeAnimation).delay(20);
            _(this.finishAnimation).delay(this.duration * 0.9);
            
            _(this.update).delay(this.duration);
        },

        checkTime: function() {
            this.time += this.delta;

            if (this.time === 0) this.delta = 0;
            if (this.time === this.max) this.delta = -1;

            this.delta === 1 ? this.toggleDirection('down', 'up') : this.toggleDirection('up', 'down');

            this.nextTime = this.time + this.delta;
        },

        toggleDirection: function(add, remove) {
            this.el.classList.add(add);
            this.el.classList.remove(remove);
        },

        setSizes: function() {
            this.currentSize = this.getSize(this.time);
            this.nextSize = this.getSize(this.nextTime);
        },

        getSize: function(time) {
            return time > 9 ? 'small' : '';
        },

        setupAnimation: function() {
            this.el.innerHTML = this.template(this);
            this.el.classList.remove('changed');
        },

        executeAnimation: function() {
            this.el.classList.add('changing');
        },

        finishAnimation: function() {
            this.el.classList.add('changed');
            this.el.classList.remove('changing');
        }
    };
})();