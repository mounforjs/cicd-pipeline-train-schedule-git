
! function(a) {
    function f(a, b) { if (!(a.originalEvent.touches.length > 1)) { a.preventDefault(); var c = a.originalEvent.changedTouches[0],
                d = document.createEvent("MouseEvents");
            d.initMouseEvent(b, !0, !0, window, 1, c.screenX, c.screenY, c.clientX, c.clientY, !1, !1, !1, !1, 0, null), a.target.dispatchEvent(d) } } if (a.support.touch = "ontouchend" in document, a.support.touch) { var e, b = a.ui.mouse.prototype,
            c = b._mouseInit,
            d = b._mouseDestroy;
        b._touchStart = function(a) { var b = this;!e && b._mouseCapture(a.originalEvent.changedTouches[0]) && (e = !0, b._touchMoved = !1, f(a, "mouseover"), f(a, "mousemove"), f(a, "mousedown")) }, b._touchMove = function(a) { e && (this._touchMoved = !0, f(a, "mousemove")) }, b._touchEnd = function(a) { e && (f(a, "mouseup"), f(a, "mouseout"), this._touchMoved || f(a, "click"), e = !1) }, b._mouseInit = function() { var b = this;
            b.element.bind({ touchstart: a.proxy(b, "_touchStart"), touchmove: a.proxy(b, "_touchMove"), touchend: a.proxy(b, "_touchEnd") }), c.call(b) }, b._mouseDestroy = function() { var b = this;
            b.element.unbind({ touchstart: a.proxy(b, "_touchStart"), touchmove: a.proxy(b, "_touchMove"), touchend: a.proxy(b, "_touchEnd") }), d.call(b) } } }(jQuery);

function Puzzle() {
    this.difficulty = document.getElementById("canvas").dataset.difficulty || 4;

    this._wrapper = $('#game-wrapper');
    this._canvas = $('#canvas');
    this._img;

    this.minWidth = 150;
    this.minHeight = 150;

    this.width; this._width;
    this.height; this._height;

    this.stepCount = 0;
    this.start_time = null;

    this.promiseStartResolve, this.promiseStartReject;
    this.ready = new Promise((resolve, reject) => {
        this.promiseStartResolve = resolve;
        this.promiseStartReject = reject;
    })

    this.promiseEndResolve, this.promiseEndReject;
    this.finished = new Promise((resolve, reject) => {
        this.promiseEndResolve = resolve;
        this.promiseEndReject = reject;
    })

    var img = document.getElementById("canvas").dataset.img;
    this.prepareGame(img);
}

Puzzle.prototype.prepareGame = function(img) {
    var self = this;

    this._img = new Image();
    this._img.onload = function() {
        self.width = this.width;
        self.height = this.height;

        var aspect_ratio = this.height/this.width;
        if (this.height > this.width) {
            self.minWidth = self.minHeight / aspect_ratio;
        } else {
            self.minHeight = self.minWidth * aspect_ratio;
        }

        self.resize();
        window.addEventListener('resize', function(e) {
            self.resize();
        });

        self._canvas.append(this);

        self.promiseStartResolve();
    };

    this._img.onerror = function() {
        self.promiseStartReject();
    }

    this._img.src = img;
}

Puzzle.prototype.start = function() {
    this.randomize();
    this.enableSwapping('#canvas li');
    this.start_time = Date.now();
}

Puzzle.prototype.randomize = function() {
    var percentage = (100 / (this.difficulty-1)).toFixed(8);
    this._canvas.empty();
    for (var i = 0; i < this.difficulty * this.difficulty; i++) {
        var xpos = (percentage * (i % this.difficulty)) + '%';
        var ypos = (percentage * Math.floor(i / this.difficulty)) + '%';
        var li = $('<li class="item" data-value="' + i + '"></li>').css({
            'background-image': 'url(' + this._img.src + ')',
            'background-size': (this.difficulty * 100) + '%',
            'background-position': xpos + ' ' + ypos,
            'width': this._width / this.difficulty,
            'height': (this._height / this.difficulty)
        });
        this._canvas.append(li);
    }
    
    var $elems = this._canvas.children();
    var $parents = $elems.parent();

    $parents.each(function () {
        $(this).children().sort(function () {
            return Math.round(Math.random()) - 0.5;
        }).remove().appendTo(this);
    });
}

Puzzle.prototype.enableSwapping = function(elem) {
    var self = this;

    setTimeout(function(){
        $(elem).draggable({
            containment: "#canvas",
            scroll: false,
            snap: '#droppable',
            snapMode: 'outer',
            revert: "invalid",
            helper: "clone"
        });
    }, 100);

    $(elem).droppable({
        drop: function (event, ui) {
            var $dragElem = $(ui.draggable).clone().replaceAll(this);
            $(this).replaceAll(ui.draggable);

            self.stepCount++;
            $('.score-container').text(self.stepCount);

            if (self.isComplete()) {
                self.promiseEndResolve();
            } else {
                self.enableSwapping(this);
                self.enableSwapping($dragElem);
            }
        }
    });
}

Puzzle.prototype.isComplete = function() {
    var pieces = $('#canvas > li').map(function (i, el) { return $(el).attr('data-value'); })
    for (var i = 0; i < pieces.length - 1; i++) {
        if (pieces[i] != i)
            return false;
    }
    return true;
}

Puzzle.prototype.end = function() {
    this.end_time = Date.now();
        
    //disable input
    document.onmousedown = null;
    document.onmousemove = null;
    document.onmouseup = null;

    document.touchstart = null;
    document.touchmove = null;
    document.touchend = null;

    this._canvas.empty();
    this._canvas.append(this._img);

    return {
        steps: this.stepCount,
        elapsed: this.end_time - this.start_time
    };
};

Puzzle.prototype.resize = function() {
    var self = this; 

    var aspect_ratio = this.height/this.width;

    var offsetY = this._canvas[0].getBoundingClientRect().top + $("#footer").height() + parseInt(this._canvas.css("marginTop")) + parseInt(this._canvas.css("marginBottom"));
    var offsetX = parseInt(this._canvas.css("marginLeft")) + parseInt(this._canvas.css("marginRight"));
    
    if (this.height > this.width) { //treat new width and height differently dependent on image dimensions
        var maxHeight = clamp(maxWidth * aspect_ratio, this.minHeight, document.body.clientHeight - offsetY);
        
        this._height = clamp(this.height, this.minHeight, maxHeight);
        this._width = clamp(this._height / aspect_ratio, this.minWidth, maxWidth);
    } else {
        var maxWidth =  Math.max(this._wrapper.outerWidth() - offsetX, this.minWidth);
        var maxHeight = Math.max(document.body.clientHeight - offsetY, this.minHeight);

        this._width = clamp(maxWidth, this.minWidth, maxHeight / aspect_ratio);
        this._height = clamp(maxWidth * aspect_ratio, this.minHeight, maxHeight);
    }
    
    this._img.width = this._width;
    this._img.height = this._height;

    this._canvas.height(this._height);
    this._canvas.width(this._width);

    $.each($('#canvas li'), function() {
        $(this).width(self._width / self.difficulty);
        $(this).height(self._height / self.difficulty);
    });
}

function clamp(x, min, max) {
	if (x <= min) {
		return min;
	} else if (x >= max) {
		return max;
	} else {
		return x;
	}
}