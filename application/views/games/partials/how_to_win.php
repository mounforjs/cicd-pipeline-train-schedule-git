<?php if ($type == '2048') { ?>
    <hr><h2 class="fs-title text-center">How to Win:</h2>
    <div class="row justify-content-center text-center pw-form">
       <div class="col-md-3 col-6">
          <label class="switch" id="charity-search">
          <input class="ab large-radio" type="radio" name="howToWin" id="timelimit_radio" value="timelimit" checked="checked">
          </label>
          <h4>
             Fastest to Winning Tile
             <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">Whoever has the fastest time to the winning tile wins.</span></div>
          </h4>
       </div>
       <div class="col-md-2 col-6">
          <label class="switch" id="project-search">
          <input class="ab large-radio" type="radio" name="howToWin" id="highest_radio" value="highest" >
          </label>
          <h4>
             Highest Score
             <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">Whoever achieves the highest score wins.</span></div>
          </h4>
       </div>
    </div>    
<?php } else if ($type == 'puzzle') { ?>
    <hr>
    <div class="row text-center pw-form">
        <!-- START Unique Element for Puzzle -->
        <div class="col-md-4">
            <h2 class="fs-title text-center">
                Puzzle Image Upload
            </h2>
            <div class="img-upload">
                <div class="img-edit">
                    <input accept=".png, .jpg, .jpeg" id="imageUpload1" type="file" class="commonImageUpload"
                    show-preview-on="puzzleImagePreview"
                    set-hidden-value="puzzleImage" />
                    <input type="hidden" name="puzzleImage" id="puzzleImage" class="puzzleImage" />

                    <label for="imageUpload1">
                    </label>
                </div>
                <div class="img-preview">
                    <img id="puzzleImagePreview" class="imagePreview" src="<?php $image = getImagePathSize("", "image_upload_placeholder"); echo $image["image"]; ?>" onerror="imgError(this, '<?= $image['fallback']; ?>');">
                </div>
            </div>
            <div id="puzzleImageError"></div>
        </div>
        <!-- END Unique Element for Puzzle -->
        <div class="col-md-8">
            <h2 class="fs-title text-center">
                How to Win:
            </h2>
            <br>
            <div class="row justify-content-center">
                <div class="col-md-4 col-6">
                    <!-- START Unique Element for Puzzle -->
                    <label class="switch" id="charity-search">
                    <input checked="" class="ab large-radio" id="time_limit_radio" name="howToWin" type="radio" value="timelimit" checked="checked">
                    </input>
                    </label>
                    <h4>
                        Fastest to solve wins
                        <div class="mytooltip">
                            <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="">
                            </i>
                            <span class="tooltiptext">
                            You may be the first to solve the puzzle, but that doesn't mean you are the fastest, unless everybody starts at the same time.
                            </span>
                        </div>
                    </h4>
                    <!-- END Unique Element for Puzzle -->
                </div>
                <div class="col-md-4 col-6">
                    <!-- START Unique Element for Puzzle -->
                    <label class="switch" id="project-search">
                    <input class="ab large-radio" id="steps_radio" name="howToWin" type="radio" value="steps">
                    </input>
                    </label>
                    <h4>
                        Fewest moves wins
                        <div class="mytooltip">
                            <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="">
                            </i>
                            <span class="tooltiptext">
                            Whoever takes the fewest moves wins. Time doesn't matter with this rule.
                            </span>
                        </div>
                    </h4>
                    <!-- END Unique Element for Puzzle -->
                </div>
            </div>
        </div>
    </div>
<?php } else if ($type == 'challenge') { ?>
    <hr><h2 class="fs-title text-center">How to Win: </h2>
    <div class="row justify-content-center text-center pw-form">
        <!-- START Unique Element for Challenge -->
        <div class="col-sm-auto">
            <label class="switch" id="charity-search">
                <input class="ab large-radio" id="optionsCheckboxes1" name="optionsCheckboxes" type="radio" value="1" checked="checked">
                </input>
            </label>
            <h4>
                Fastest 100% correct wins
                <div class="mytooltip">
                    <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="">
                    </i>
                    <span class="tooltiptext">
                        Whoever has the fastest time to the winning tile wins.
                    </span>
                </div>
            </h4>
        </div>
        <div class="col-sm-auto">
            <label class="switch" id="project-search">
                <input class="ab large-radio" id="optionsCheckboxes2" name="optionsCheckboxes" type="radio" value="2">
                </input>
            </label>
            <h4>
                Most right answers wins
                <div class="mytooltip">
                    <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="">
                    </i>
                    <span class="tooltiptext">
                        At the end of the game, the player with the most correct answers wins.
                    </span>
                </div>
            </h4>
        </div>
        <div class="col-sm-auto">
            <label class="switch" id="project-search">
                <input class="ab large-radio" id="optionsCheckboxes3" name="optionsCheckboxes" type="radio" value="3">
                </input>
            </label>
            <h4>
                The fastest + most right answers
                <div class="mytooltip">
                    <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="">
                    </i>
                    <span class="tooltiptext">
                        Combination of the other rules, so the player who finished fastest and with the most correct answers wins.
                    </span>
                </div>
            </h4>
        </div>
        <!-- END Unique Element for Challenge -->
    </div>
<?php } else if ($type == 'minecraft') { ?>

<?php } else { ?>
    
<?php } ?>