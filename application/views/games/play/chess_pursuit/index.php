<div id='game-container' class="row justify-content-center">
</div>

<input type="hidden" name="attempts" value="<?= $attempt_count ?>" />

<script src="<?php echo asset_url('assets/js/chesspursuit/chesspursuit.js'); ?>"></script>

<script>
    function Game() {
        var game = new ChessPursuit();

        this.ready = async () => {
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