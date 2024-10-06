<link href="<?php echo asset_url('assets/css/2048.css'); ?>" rel="stylesheet" type="text/css">

<div class="game-container">
    <div class="grid-container">
        <div class="grid-row">
            <div class="grid-cell"></div>
            <div class="grid-cell"></div>
            <div class="grid-cell"></div>
            <div class="grid-cell"></div>
        </div>
        <div class="grid-row">
            <div class="grid-cell"></div>
            <div class="grid-cell"></div>
            <div class="grid-cell"></div>
            <div class="grid-cell"></div>
        </div>
        <div class="grid-row">
            <div class="grid-cell"></div>
            <div class="grid-cell"></div>
            <div class="grid-cell"></div>
            <div class="grid-cell"></div>
        </div>
        <div class="grid-row">
            <div class="grid-cell"></div>
            <div class="grid-cell"></div>
            <div class="grid-cell"></div>
            <div class="grid-cell"></div>
        </div>
    </div>
    <div class="tile-container">
    </div>
</div>

<input type="hidden" name="tile" value="<?php echo $tile; ?>" />

<script src="<?php echo asset_url('assets/js/2048/bind_polyfill.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/2048/classlist_polyfill.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/2048/animframe_polyfill.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/2048/keyboard_input_manager.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/2048/html_actuator.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/2048/grid.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/2048/tile.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/2048/game_manager.js'); ?>"></script>

<script>
    function Game() {
        var game = new GameManager();

        this.ready = async () => {
            return new Promise((resolve, reject) => {
                resolve(game.ready);
            });
        }

        this.start = async () => {
            return new Promise((resolve, reject) => {
                requestAnimationFrame(() => {
                    game.start();
                    resolve(game.finished);
                });
            });
        }

        this.end = () => {
            return game.end();
        }
    }
</script>