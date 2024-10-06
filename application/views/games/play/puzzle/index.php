<?php if ($puzzleContentType === 'gif') { ?>
    <link href="<?php echo asset_url('assets/css/image-puzzle.css'); ?>" rel="stylesheet" type="text/css">
    <script src="<?php echo asset_url('assets/js/puzzle/jquery-ui.js') ?>"></script>
    <script src="<?php echo asset_url('assets/js/puzzle/image-puzzle.js') ?>"></script>

    <ul id="canvas" class="sortable"  data-difficulty="<?php echo $puzzleGrid; ?>" data-img="<?= $puzzleImage; ?>"></ul>
<?php } else { ?>
    <script src="<?php echo asset_url('assets/js/newPuzzle/vendor/modernizr.js'); ?>" type="text/javascript"></script>
    <script src="<?php echo asset_url('assets/js/newPuzzle/puzzle.js'); ?>" type="text/javascript"></script>

    <canvas id="canvas" data-difficulty="<?php echo $puzzleGrid; ?>" data-img="<?= getImagePathSize($puzzleImage, 'puzzle_game_image')["image"]; ?>"></canvas>
<?php } ?>

<script type="text/javascript">
    function Game() {
        var game = new Puzzle(); 

        this.ready = async () => {
            return new Promise((resolve, reject) => {
                resolve(game.ready);
            });
        }

        this.start = async () => {
            return new Promise((resolve, reject) => {
                game.start();
                resolve(game.finished);
            });
        }

        this.end = () => {
            return game.end();
        }
    }
</script>