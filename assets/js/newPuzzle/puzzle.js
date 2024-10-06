function Puzzle() {
    /* PUZZLE */
    this.difficulty = document.getElementById("canvas").dataset.difficulty;
    this.hover_tint = '#009900';
    
    this._wrapper = $('#game-wrapper');
    this._canvas = $('#canvas');
    this._stage = this._canvas[0].getContext('2d');

    this._img;

    this.minWidth = 150;
    this.minHeight = 150;
        
    this._pieces;
    this._puzzleWidth;
    this._puzzleHeight;
    this._pieceWidth;
    this._pieceHeight;
    this._scaledPieceWidth;
    this._scaledPieceHeight;
    this._scaledPuzzleWidth;
    this._scaledPuzzleHeight;
    this._currentPiece;
    this._currentDropPiece;  

    this.start_time;
    this.end_time;
    this._stepsTaken = 0;

    this._mouse;
    this.touchSupported = Modernizr.touch;
    this.eventsMap  = {
        select: (this.touchSupported) ? "touchstart" : "click",
        down: (this.touchSupported) ? "touchstart" : "mousedown",
        up: (this.touchSupported) ? "touchend" : "mouseup",
        move: (this.touchSupported) ? "touchmove" : "mousemove"
    };

    this.promiseReadyResolve, this.promiseReadyReject;
    this.ready = new Promise((resolve, reject) => {
        this.promiseReadyResolve = resolve;
        this.promiseReadyReject = reject;
    })

    this.promiseEndResolve, this.promiseEndReject;
    this.finished = new Promise((resolve, reject) => {
        this.promiseEndResolve = resolve;
        this.promiseEndReject = reject;
    })

    var img = document.getElementById("canvas").dataset.img;
    this.loadImage(img);
};

Puzzle.prototype.start = function() {
    this.buildPieces();
    this.start_time = Date.now();
}

Puzzle.prototype.loadImage = function(img) {
    var self = this;

    this._img = new Image();
    this._img.onload = function () {

        var diff = this.height/this.width;
        if (this.height > this.width) {
            self.minWidth = self.minHeight / diff;
        } else {
            self.minHeight = self.minWidth * diff;
        }

        self.resize();
        window.addEventListener('resize', function(e) {
            self.resize();
        });
        
        self.promiseReadyResolve();
    };

    self._img.onerror = function() {
        self.promiseReadyReject();
    }

    this._img.src = img;
};

Puzzle.prototype.setCanvas = function() {
    this._canvas[0].width = this._scaledPuzzleWidth;
    this._canvas[0].height = this._scaledPuzzleHeight;
};

Puzzle.prototype.initPuzzle = function() {
    this._pieces = [];
    this._mouse = {x:0,y:0};
    this._currentPiece = null;
    this._currentDropPiece = null;
    this._stage.drawImage(this._img, 0, 0, this._img.width, this._img.height, 0, 0, this._scaledPuzzleWidth, this._scaledPuzzleHeight);
    $('body').removeClass('loading');
};

Puzzle.prototype.buildPieces = function() {
    var i;
    var piece;
    var oxPos = 0; var xPos = 0;
    var oyPos = 0; var yPos = 0;
    for (i = 0;i < this.difficulty * this.difficulty;i++) {
        piece = {};
        piece.ox = oxPos; piece.oy = oyPos; //original image dims - need to retain original image x,y coords for reference
        piece.sx = xPos; piece.sy = yPos; //scaled image dims
        
        this._pieces.push(piece);
        oxPos += this._pieceWidth; xPos += this._scaledPieceWidth;
        if(xPos >= this._scaledPuzzleWidth){
            oxPos = 0; xPos = 0;
            oyPos += this._pieceHeight; yPos += this._scaledPieceHeight;
        }
    }
    
    this.shufflePuzzle();
};

Puzzle.prototype.shufflePuzzle = function() {
    var self = this;

    this._pieces = this.shuffleArray(this._pieces);
    this._stage.clearRect(0,0,this._puzzleWidth,this._puzzleHeight);
    var i;
    var piece;
    var xPos = 0;
    var yPos = 0;
    for(i = 0;i < this._pieces.length;i++){
        piece = this._pieces[i];
        piece.xPos = xPos;
        piece.yPos = yPos;

        //replicated section of full sized image in scaled version of image
        this._stage.drawImage(this._img, piece.ox, piece.oy, this._pieceWidth, this._pieceHeight, xPos, yPos, this._scaledPieceWidth, this._scaledPieceHeight);
        this._stage.strokeRect(xPos, yPos, this._scaledPieceWidth,this._scaledPieceHeight);
        xPos += this._scaledPieceWidth;
        if(xPos >= this._scaledPuzzleWidth){
            xPos = 0;
            yPos += this._scaledPieceHeight;
        }
    }
    if (!this.touchSupported) {
        $('#canvas').on('mousedown', function(e) {
            self.onPuzzleClick(e.originalEvent);
        });
    } else {
        $('#canvas').on('touchstart', function(e) {
            self.onPuzzleClick(e.originalEvent); 
        });
    }
};

Puzzle.prototype.onPuzzleClick = function(e) {
    var self = this;

    if (!this.touchSupported) {
        this._mouse.x = e.pageX - this._canvas.offset().left;
        this._mouse.y = e.pageY - this._canvas.offset().top;
    } else {
        this._mouse.x = e.touches[0].pageX - this._canvas.offset().left;
        this._mouse.y = e.touches[0].pageY - this._canvas.offset().top;
    }
    
    this._currentPiece = this.checkPieceClicked();
    if(this._currentPiece != null){
        this._stage.clearRect(this._currentPiece.xPos,this._currentPiece.yPos,this._scaledPieceWidth,this._scaledPieceHeight);
        this._stage.save();
        this._stage.globalAlpha = .9;
        this._stage.drawImage(this._img, this._currentPiece.ox, this._currentPiece.oy, this._pieceWidth, this._pieceHeight, this._mouse.x - (this._scaledPieceWidth / 2), this._mouse.y - (this._scaledPieceHeight / 2), this._scaledPieceWidth, this._scaledPieceHeight);
        this._stage.restore();

        if (!this.touchSupported) {
            document.onmousemove = function(e) {
                self.updatePuzzle(e);
            }
            document.onmouseup = function(e) {
                self.pieceDropped(e);
            };
        } else {
            $('#canvas').bind( 'touchmove', function(e) {
                self.updatePuzzle(e.originalEvent);
            });
            
            $('#canvas').bind( 'touchend', function(e) {
                self.pieceDropped(e.originalEvent);
            });
        }
    }
};

Puzzle.prototype.checkPieceClicked = function() {
    var i; var piece;
    for (i = 0;i < this._pieces.length;i++) {
        piece = this._pieces[i];
        if (this._mouse.x < piece.xPos || this._mouse.x > (piece.xPos + this._scaledPieceWidth) || this._mouse.y < piece.yPos || this._mouse.y > (piece.yPos + this._scaledPieceHeight)){
            //PIECE NOT HIT
        } else {
            return piece;
        }
    }
    return null;
};

Puzzle.prototype.updatePuzzle = function(e) {
    if (e != null) {
        e.preventDefault();
        e.stopPropagation();

        if (!this.touchSupported) {
            this._mouse.x = e.pageX - this._canvas.offset().left;
            this._mouse.y = e.pageY - this._canvas.offset().top;
        } else {
            this._mouse.x = e.touches[0].pageX - this._canvas.offset().left;
            this._mouse.y = e.touches[0].pageY - this._canvas.offset().top;
        }
    }

    this._currentDropPiece = null;
    
    this._stage.clearRect(0,0,this._puzzleWidth,this._puzzleHeight);
    var i; var piece;
    for (i = 0;i < this._pieces.length;i++) {
        piece = this._pieces[i];
        if (piece == this._currentPiece) {
            continue;
        }

        this._stage.drawImage(this._img, piece.ox, piece.oy, this._pieceWidth, this._pieceHeight, piece.xPos, piece.yPos, this._scaledPieceWidth, this._scaledPieceHeight);
        this._stage.strokeRect(piece.xPos, piece.yPos, this._scaledPieceWidth,this._scaledPieceHeight);
        if (this._currentDropPiece == null && e != null) {
            if (this._mouse.x < piece.xPos || this._mouse.x > (piece.xPos + this._scaledPieceWidth) || this._mouse.y < piece.yPos || this._mouse.y > (piece.yPos + this._scaledPieceHeight)){
                //NOT OVER
            } else {
                this._currentDropPiece = piece;
                this._stage.save();
                this._stage.globalAlpha = .4;
                this._stage.fillStyle = this.hover_tint;
                this._stage.fillRect(this._currentDropPiece.xPos,this._currentDropPiece.yPos,this._scaledPieceWidth, this._scaledPieceHeight);
                this._stage.restore();
            }
        }
    }

    if (this._currentPiece != null) {
        this._stage.save();
        this._stage.globalAlpha = .6;
        this._stage.drawImage(this._img, this._currentPiece.ox, this._currentPiece.oy, this._pieceWidth, this._pieceHeight, this._mouse.x - (this._scaledPieceWidth / 2), this._mouse.y - (this._scaledPieceHeight / 2), this._scaledPieceWidth, this._scaledPieceHeight);
        this._stage.restore();
        this._stage.strokeRect( this._mouse.x - (this._scaledPieceWidth / 2), this._mouse.y - (this._scaledPieceHeight / 2), this._scaledPieceWidth,this._scaledPieceHeight);
    }
};

Puzzle.prototype.pieceDropped = function(e) {
    if (!this.touchSupported) {
        document.onmousemove = null;
        document.onmouseup = null;
    } else {
        $('#canvas').unbind('touchend'); 
    }

    if (this._currentDropPiece != null) {
        var tmp = {xPos:this._currentPiece.xPos,yPos:this._currentPiece.yPos};
        this._currentPiece.xPos = this._currentDropPiece.xPos;
        this._currentPiece.yPos = this._currentDropPiece.yPos;
        this._currentDropPiece.xPos = tmp.xPos;
        this._currentDropPiece.yPos = tmp.yPos;
        this._stepsTaken++;
    }

    $("#scoreContainer").text(this._stepsTaken);
    this.isComplete();
};

Puzzle.prototype.isComplete = function() {
    this._stage.clearRect(0,0,this._puzzleWidth,this._puzzleHeight);

    var gameWin = true;
    var i; var piece;
    for (i = 0;i < this._pieces.length;i++) {
        piece = this._pieces[i];
        this._stage.drawImage(this._img, piece.ox, piece.oy, this._pieceWidth, this._pieceHeight, piece.xPos, piece.yPos, this._scaledPieceWidth, this._scaledPieceHeight);
        this._stage.strokeRect(piece.xPos, piece.yPos, this._scaledPieceWidth,this._scaledPieceHeight);
        
        if (piece.xPos != piece.sx || piece.yPos != piece.sy) {
            gameWin = false;
        }
    }

    if (gameWin) {
        this.promiseEndResolve();
    }
};

Puzzle.prototype.end = function() {
    this.end_time = Date.now();
        
    //disable input
    document.onmousedown = null;
    document.onmousemove = null;
    document.onmouseup = null;

    this._canvas.unbind();

    this.initPuzzle();

    return {
        steps: this._stepsTaken,
        elapsed: this.end_time - this.start_time
    };
};

Puzzle.prototype.shuffleArray = function(o) {
    for(var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
    return o;
};

Puzzle.prototype.resize = function() {
    var aspect_ratio = this._img.height / this._img.width

    var offsetY = this._canvas[0].getBoundingClientRect().top + $("#footer").height() + parseInt(this._canvas.css("marginTop")) + parseInt(this._canvas.css("marginBottom"));
    var offsetX = parseInt(this._canvas.css("marginLeft")) + parseInt(this._canvas.css("marginRight"));

    var maxWidth =  Math.max(this._wrapper.outerWidth() - offsetX, this.minWidth);
    var maxHeight = Math.max(document.body.clientHeight - offsetY, this.minHeight);
    
    //clamp height and width by min.max
    if (this._img.height > this._img.width) {
        //image is taller than wide
        newHeight = Math.round(clamp(maxWidth * aspect_ratio, this.minHeight, maxHeight));
        newWidth = Math.round(clamp(newHeight / aspect_ratio, this.minWidth, maxWidth));
    } else {
        newWidth = clamp(maxWidth, this.minWidth, maxHeight / aspect_ratio);
        newHeight = clamp(maxWidth * aspect_ratio, this.minHeight, maxHeight);
    }

    //full size image details
    this._pieceWidth = Math.floor(this._img.width / this.difficulty);
    this._pieceHeight = Math.floor(this._img.height / this.difficulty);
    this._puzzleWidth = this._pieceWidth * this.difficulty;
    this._puzzleHeight = this._pieceHeight * this.difficulty;

    //scaled image details

    var originalScaleWidth = this._scaledPieceWidth;
    var originalScaleHeight = this._scaledPieceHeight;

    this._scaledPieceWidth = Math.floor(newWidth / this.difficulty);
    this._scaledPieceHeight = Math.floor(newHeight / this.difficulty);
    this._scaledPuzzleWidth = this._scaledPieceWidth * this.difficulty;
    this._scaledPuzzleHeight = this._scaledPieceHeight * this.difficulty;

    this.setCanvas();
    if (this._pieces && this._pieces.length > 0) {
        this._currentPiece = null;

        for (var i = 0; i < this._pieces.length; i++) {
            var piece = this._pieces[i];
            piece.xPos = (piece.xPos / originalScaleWidth) * this._scaledPieceWidth;
            piece.yPos = (piece.yPos / originalScaleHeight) * this._scaledPieceHeight;

            piece.sx = (piece.sx / originalScaleWidth) * this._scaledPieceWidth; 
            piece.sy = (piece.sy / originalScaleHeight) * this._scaledPieceHeight;
        }

        this.updatePuzzle(null);
    } else {
        this.initPuzzle();
    }
}

const clamp = (x, min, max) => {
    if (x <= min) {
        return min;
    } else if ( x >= max) {
        return max;
    } else {
        return x;
    }
}