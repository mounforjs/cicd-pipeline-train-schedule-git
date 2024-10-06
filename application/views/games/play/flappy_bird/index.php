<div id='game-container'>
</div>

<input type="hidden" name="attempts" value="<?= $attempt_count ?>" />

<script src="<?php echo asset_url('assets/js/flappybird/phaser.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/flappybird/game.js'); ?>"></script>

<script>
    function Game() {
        var game = new FlappyBird();
        
        this.ready = async () => {
            console.log(game);
            return new Promise((resolve, reject) => {
                resolve(game.ready);
            });
        }

        this.start = async () => {
            return new Promise((resolve, reject) => {
                resolve(game.finished);
            });
        }

        this.end = () => {
            return;
        }
    }
</script>