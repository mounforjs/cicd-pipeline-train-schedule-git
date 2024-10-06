<nav class="navbar navbar-expand-sm editbar">
    <div class="container">
        <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#gameEditNav" aria-controls="gameEditNav" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-edit"></i>
        </button>
        <div class="collapse navbar-collapse" id="gameEditNav" >
            <ul class="navbar-nav ml-auto">
                <div class="row mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo asset_url('games/preview'); ?>/<?php echo $slug;?>"><i class="fa fa-eye"></i> Preview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo asset_url('games/edit'); ?>/<?php echo $slug;?>"><i class="fa fa-pencil"></i> Edit</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link deleteGameBtn" data-id = '<?php echo $slug;?>'><i class="fa fa-trash-o"></i> Delete</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link duplicateGameBtn" data-id = '<?php echo $id; ?>'><i class="fa fa-copy"></i> Duplicate</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link publishTimePicker"><i class="fa fa-file-video-o"></i> Publish</a>
                        <div class='m-2' id='timePickerDiv' style="display: none">
                            <div class="m-2">
                                <div class="my-2">
                                    <div class='input-group'>
                                        <span class="input-group-text"><span class="fa fa-calendar"></span></span>
                                        <input type='text' id='publishdate' class="form-control date" placeholder="Select date.."/>
                                    </div>
                                </div>
                                <div class>
                                    <select aria-invalid="false" aria-required="true" class="form-control time-zone-select valid" id="timeZone" name="timeZone"> </select>
                                    <div class="zone-time"></div>
                                </div>
                                <br>
                                <div class="row ml-auto">
                                    <!-- <a class="btn btn-sm" id='clear'>Reset Date</a> -->
                                    <button type="button" class="btn btn-sm" id="cancel_publish"></i>Cancel</button>
                                    <button type="button" class="btn btn-sm" id="publish_btn"></i>Publish</button>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link makeGameLiveBtn" ><i class="fa fa-file-video-o"></i> Make Live</a>
                    </li>
                </div>
            </ul>
        </div>
    </div>
</nav>