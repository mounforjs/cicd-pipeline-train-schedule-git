<div class="row mb-4">
    <div class="col-md-6 offset-md-3 text-center">
        <h3 class="text-decoration-underline font-weight-bold text-warning mb-1">
            <u><?php echo $name; ?></u>
        </h3>
    </div>
</div>
<div class="row justify-content-between statline mb-n3">
    <?php if ($type != 'challenge') { ?>
        <div class="col-auto order-1 order-sm-1 p-0 text-left">
            <span id="timer" class="d-block">
                <i class="fas fa-clock"></i> 
                <span id="stopwatch">00:00:00.00</span>
            </span>
        </div>
        <div class="col-sm order-3 order-sm-2 p-1">
            <center>
                <span id="rule-label">Rule</span>
                <p class="whitetext" id="rule">
                    <?php echo $gameRule; ?>
                </p>
            </center>
        </div>
        <div class="col-auto order-2 order-sm-3 p-0 text-right">
            <span id="score">
                <i class="fas fa-star"></i>
                <span class="score-container" id='scoreContainer'>
                    <?php echo ($type == 'puzzle') ? 'Steps: ' : '';?>0
                </span>
            </span>
        </div>
    <?php } else {?>
        <div class="col-auto order-1 order-sm-1 p-0 text-left">
            <span class="d-flex justify-content-center">
                <h4 class="text-warning"><u>Total Time</u></h4>
            </span>
            <span id="timer" class="d-block">
                <i class="fas fa-clock"></i> 
                <span id="stopwatch">00:00:00.00</span>
            </span>
        </div>
        <div class="col-5 order-3 order-sm-2 p-1 pl-4 text-right<?= ($credit_type != 'free') ? "" : "d-none"; ?>">
            <span class="d-flex justify-content-center">
                <h4 class="text-warning"><u>Rule</u></h4>
            </span>
            <p class="whitetext" id="rule">
                <?php echo $gameRule; ?>
            </p>
        </div>
    <?php }?>

    <input type="hidden" id="responseTime" value="" />
</div>