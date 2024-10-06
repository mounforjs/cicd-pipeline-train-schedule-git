var url = location.toString();

function FlappyBird() {
	let bestContainer    = $("#scoreContainer");
	let attemptContainer  = $("#attemptContainer");

	var config = {
		type: Phaser.CANVAS,
		parent: 'game-container',
		width: 800,
		height: 600,
		physics: {
			default: 'arcade',
			arcade: {
				gravity: { y: 800 },
				debug: false
			}
		},
		scene: {
			preload: preload,
			create: create,
			update: update

		}
	};
	var game = new Phaser.Game(config);

	const assets = {
		obstacle: {
			pipe: {
				top: 'tubeTop',
				bottom: 'tubeBottom'
			}
		},
	}

	let gameOver;
	let gameStarted;

	let ground;
	let tubeGroup;
	let gapsGroup;
	let currentTube;
	let nextTubes;

	let startTime;
	let scoreText;
	let score;
	let maxScore = 0;
	let attemptsText;
	
	let attempts = $("input[name='attempts']").val();

	let gapsThrough = 0;

	let previousTubeTop;
	let previousTubeBottom;

	let logo;
	let welcomeMessage;
	let endMessage;

	var promiseReadyResolve, promiseReadyReject;
	this.ready = new Promise((resolve, reject) => {
		promiseReadyResolve = resolve;
		promiseReadyReject = reject;
	})

	var promiseEndResolve, promiseEndReject;
	this.finished = new Promise((resolve, reject) => {
		promiseEndResolve = resolve;
		promiseEndReject = reject;
	})

	function preload ()
	{
		this.load.image('sky', '/assets/js/flappybird/assets/sky.png');
		this.load.image('ground', '/assets/js/flappybird/assets/ground.png');

		this.load.image('bird','/assets/js/flappybird/assets/bird.png');

		this.load.image(assets.obstacle.pipe.top, '/assets/js/flappybird/assets/tubeTop.png');
		this.load.image(assets.obstacle.pipe.bottom, '/assets/js/flappybird/assets/tubeBottom.png');

		this.load.image('logo', '/assets/js/flappybird/assets/flappyBird.png');

		this.load.image('input', '/assets/js/flappybird/assets/input.png');
		this.load.image('endMessage', '/assets/js/flappybird/assets/endMsg.png');
	}

	function create ()
	{
		startTime = new Date().getTime();
		
		attempts --;

		this.add.image(400, 300, 'sky');

		logo = this.add.image(150, 100, 'logo');
		logo.setDepth(20);

		welcomeMessage = this.add.image(200, 225, 'input');
		welcomeMessage.setDepth(20);

		scoreText = this.add.text(5, 0, 'Points: ' + gapsThrough, { fontFamily: 'Georgia, "Goudy Bookletter 1911", Times, serif' });
		scoreText.setDepth(50);

		maxScoreText = this.add.text(100, 0, 'Max Score: ' + gapsThrough, { fontFamily: 'Georgia, "Goudy Bookletter 1911", Times, serif' });
		maxScoreText.setDepth(50);

		attemptsText = this.add.text(5, 20, 'Attempts: ' + attempts, { fontFamily: 'Georgia, "Goudy Bookletter 1911", Times, serif' });
		attemptsText.setDepth(50);

		endMessage = this.add.image(400, 300, 'endMessage');
		endMessage.setDepth(50);

		endMessage.setInteractive();
		endMessage.on('pointerdown', restartGame);

		endMessage.visible = false;

		ground = this.physics.add.sprite(800, 600, 'ground')
		ground.setCollideWorldBounds(true)
		ground.setDepth(10)

		gapsGroup = this.physics.add.group();
		tubeGroup = this.physics.add.group(); 

		const gameScene = game.scene.scenes[0];
		prepareGame(gameScene);
	}

	function update ()
	{
		if (gameOver || !gameStarted)
		{
			return
		}

		tubeGroup.children.iterate(function (child) {
			if (child == undefined)
				return

			if (child.x < -50)
				child.destroy();
			else
				child.setVelocityX(-375);
		});

		gapsGroup.children.iterate(function (child) {
			child.body.setVelocityX(-375)
		});

		nextTubes++
		if (nextTubes === 150) {
			makePipes(game.scene.scenes[0]);
			nextTubes = 0;
		}
	}

	function prepareGame(scene)
	{
		updateAttempts();
		
		gameOver = false;
		nextTubes = 0;

		currentTube = assets.obstacle.pipe;

		player = scene.physics.add.sprite(100, 200, 'bird');
		player.setScale(.8,.8);
		player.setCollideWorldBounds(true);
		player.body.allowGravity = false;

		scene.physics.add.collider(player, tubeGroup, birdHit, null, scene)
		scene.physics.add.overlap(player, gapsGroup, updateScore, null, scene)
		scene.physics.add.collider(player, ground, birdHit, null, scene);

		moveKey = scene.input.keyboard.addKey("W");
		moveKey.on('down', function (event) { moveBird(player); });  

		promiseReadyResolve();
	}

	function updateScore(_, gap)
	{
		gapsThrough++;
		gap.destroy();
		
		score = gapsThrough;
		scoreText.setText("Points: " + gapsThrough);

		if (score > maxScore){
			maxScore = score;
			maxScoreText.setText("Max Score: " + maxScore);
		}

		updateHTMLScore(score);
	}
	
	function makePipes(scene) {
		if (!gameStarted || gameOver) return;

		const pipeTopY = Phaser.Math.Between(300, 325);

		const gap = scene.add.line(850, pipeTopY, 0, 255, 0, 98);
		gapsGroup.add(gap);
		gap.body.allowGravity = false;
		gap.visible = true;

		pT = pipeTopY-200;
		pB = pipeTopY+400;

		let pipeTop;
		let pipeBottom;

		if ((previousTubeTop != null) && (previousTubeBottom != null)){
			pipeTop = tubeGroup.create(850, (pT + previousTubeTop) / 2, currentTube.top);
			pipeBottom = tubeGroup.create(850, (pB + previousTubeBottom) / 2, currentTube.bottom);
		}else{
			pipeTop = tubeGroup.create(850, pT, currentTube.top);
			pipeBottom = tubeGroup.create(850, pB, currentTube.bottom);
		}
		

		pipeTop.body.allowGravity = false;
		previousTubeTop = pT;

		pipeBottom.body.allowGravity = false;
		previousTubeBottom = pB;

	}

	function birdHit(player)
	{
		this.physics.pause();

		gameOver = true;
		gameStarted = false;

		if (attempts > 0)
		{
			endMessage.visible = true;
		} else {
			endGame();
		}

		if (score > maxScore){
			maxScore = score;
			maxScoreText.setText("Max Score: " + maxScore);
			updateHTMLBestScore(maxScore);
		}
	}

	function moveBird(player)
	{
		if (gameOver)
			return

		if (!gameStarted)
		{
			startGame(game.scene.scenes[0], player);
		}

		player.setVelocityY(-375);
	}

	function restartGame() {
		if (attempts == 0)
		{
			gameOver = true;
		} else {
			score = 0;
			gapsThrough = 0;
			scoreText.setText("Score: " + score);

			attempts --;
			attemptsText.setText("Attempts: " + attempts);
			
			updateAttempts();
			updateHTMLScore(score);

			tubeGroup.clear(true, true);
			gapsGroup.clear(true, true);

			previousTubeTop = null;
			previousTubeBottom = null;
			
			player.destroy();

			endMessage.visible = false;
			welcomeMessage.visible = true;

			const gameScene = game.scene.scenes[0]
			prepareGame(gameScene);

			makePipes(gameScene);

			gameScene.physics.resume();
		}
	}

	function endGame() {
		promiseEndResolve();

		scoreText.visible = false;
		attemptsText.visible = false;
		maxScoreText.setPosition(300, 300);
		maxScoreText.setFontSize(36);

		tubeGroup.clear(true, true);
		gapsGroup.clear(true, true);

		previousTubeTop = null;
		previousTubeBottom = null;
			
		player.destroy();

		var totalTime = (new Date().getTime() - startTime);
	}

	function updateHTMLScore(score) {
		this.score = score;

		if (score >= maxScore) {
			updateHTMLBestScore(score);
		}
	};

	function updateHTMLBestScore(bestScore) {
		bestContainer.text(bestScore);
	};
	
	function updateAttempts() {
		attemptContainer.text(attempts);
	}

	function startGame(scene, player)
	{
		gameStarted = true;

		welcomeMessage.visible = false;
		logo.visible = false;

		makePipes(scene);

		player.body.allowGravity = true;
	}
}