<div id="footer" class="row">
    <div class="col-12">
        <br>
        <div class="container-fluid">
            <h3 class="whitetext">Goal: <?php echo $goal; ?></h3>

            <div class="container-fluid">
                <?php if ($credit_type != 'free') { ?>
                    <center><div class="gamewarning"><small><i class="fas fa-exclamation-triangle"></i> If you refresh or navigate away, your attempt will be forfeit.</small></div></center>
                <?php } ?>
                <center><a id="view-fullscreen"><i class="fas fa-expand"></i>&nbsp; VIEW FULLSCREEN</a><br><span class="esc">PRESS ESC TO EXIT</span></center>
            </div>
        </div>
    </div>
</div>