<div class="tab-pane fade <?php echo ($tab == 0) ? "show active" : ""; ?>" id="prizes" role="tabpanel" aria-labelledby="prizes-tab">

    <ul class="nav nav-tabs" id="prizeChartTabs" role="tablist">
        <?php if (getprofile()->creator_status == 'Yes') { ?>
        <li class="nav-item">
            <a class="nav-link <?php echo (isset($claimedPrizes->prizes) && count($claimedPrizes->prizes) > 0) ? "active" : ""; ?>" id="claimed-tab" data-toggle="tab" href="#claimed" role="tab" aria-controls="claimed" aria-selected="true">Awarded By You</a>
        </li>
        <?php } ?>

        <li class="nav-item">
            <a class="nav-link <?php echo (isset($claimedPrizes->prizes) && count($claimedPrizes->prizes) > 0 && getprofile()->creator_status == 'Yes') ? "" : "active"; ?>" id="claimable-tab" data-toggle="tab" href="#claimable" role="tab" aria-controls="claimable" aria-selected="false">Awarded To You</a>
        </li>
    </ul>
    <div class="tab-content" id="prizeChartContent">
        <?php if (getprofile()->creator_status == 'Yes') { ?>
        <div class="tab-pane fade <?php echo (isset($claimedPrizes->prizes) && count($claimedPrizes->prizes) > 0) ? "show active" : ""; ?>" id="claimed" role="tabpanel" aria-labelledby="claimed-tab">

            <div class="widgetstyle">

                <div class="row mb-1">
                    <div class="col-sm-2 ml-auto">
                        <select name="filter" class="form-control mb-2 ml-auto">
                            <option value="0" selected>All</option>
                            <option value="1">Pending</option>
                            <option value="2">Claimed</option>
                            <option value="3">Processed</option>
                            <option value="4">Received</option>
                            <option value="5">Not Received</option>
                            <option value="6">Under Review</option>
                            <option value="7">Completed</option>
                            <option value="8">Failed</option>
                        </select>
                    </div>
                </div>

                <input name="deferLoad" type="hidden" data-filtered="<?php echo $claimedPrizes->deferLoading["filtered"]; ?>" data-total="<?php echo $claimedPrizes->deferLoading["total"]; ?>"/>
                <table id="claimed-prize" class="table table-striped table-bordered" data-type="claimedPrizes">
                    <thead>
                        <tr>
                            <th>Game</th>
                            <th>Prize</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($claimedPrizes->prizes as $key => $prize) : ?>
                        <tr>
                            <td><h3><?php echo $prize->name; ?></h3></td>
                            <td><h3><?php echo $prize->prize_title; ?></h3></td>
                            <td>
                                <?php if ($prize->confirmed != 1) { ?>
                                    <?php if ($prize->review != 1) { ?>
                                        <a class="escrow-status idle red" href="<?php echo (asset_url("games/show/completed/". $prize->slug)); ?>">Pending...</a>
                                    <?php } else { ?>
                                        <?php if ($prize->approved == 1) { ?>
                                            <a class="escrow-status green">Approved</a>
                                        <?php } else if (isset($prize->approved) && $prize->approved == 0) { ?>
                                            <a class="escrow-status idle red">Disapproved</a>
                                        <?php } else { ?>
                                            <a class="escrow-status idle red">Under Review</a>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } else { ?>
                                    <?php if (!$prize->processed) { ?>
                                        <a class="shipinfo escrow-status orange" data-id="<?php echo $prize->id; ?>">Claimed!</a>
                                    <?php } else { ?>
                                        <?php if ($prize->received == 1) { ?>
                                            <a class="shipinfo escrow-status green" data-id="<?php echo $prize->id; ?>">Received!</a>
                                        <?php } else if (isset($prize->received) && $prize->received == 0) { ?>
                                            <a class="shipinfo escrow-status red" data-id="<?php echo $prize->id; ?>">Not Received!</a>
                                        <?php } else { ?>
                                            <a class="shipinfo escrow-status blue" data-id="<?php echo $prize->id; ?>">Processed!</a>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php  endforeach; ?>
                    </tbody>
                </table>

            </div>

        </div>
        <?php } ?>

        <div class="tab-pane fade <?php echo (isset($claimedPrizes->prizes) && count($claimedPrizes->prizes) > 0 && getprofile()->creator_status == 'Yes') ? "" : "show active"; ?>" id="claimable" role="tabpanel" aria-labelledby="claimable-tab">

            <div class="widgetstyle">

                <div class="row mb-1">
                    <div class="col-sm-2 ml-auto">
                        <select name="filter" class="form-control mb-2 ml-auto">
                            <option value="0" selected>All</option>
                            <option value="1">Claimable</option>
                            <option value="2">Pending</option>
                            <option value="3">Processed</option>
                            <option value="4">Received</option>
                            <option value="5">Not Received</option>
                            <option value="6">Under Review</option>
                            <option value="7">Completed</option>
                            <option value="8">Failed</option>
                        </select>
                    </div>
                </div>

                <input name="deferLoad" type="hidden" data-filtered="<?php echo $claimablePrizes->deferLoading["filtered"]; ?>" data-total="<?php echo $claimablePrizes->deferLoading["total"]; ?>"/>
                <table id="claimable-prize" class="table table-striped table-bordered" data-type="claimablePrizes">
                    <thead>
                        <tr>
                            <th>Game</th>
                            <th>Prize</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($claimablePrizes->prizes as $key => $prize) : ?>
                        <tr>
                            <td><h3><?php echo $prize->name; ?></h3></td>
                            <td><h3><?php echo $prize->prize_title; ?></h3></td>
                            <td>
                                <?php if (!$prize->confirmed) { ?>
                                    <a class="escrow-status green" href="<?php echo (asset_url("games/show/completed/". $prize->slug)); ?>"></i> Claim Prize!</a>
                                <?php } else { ?>
                                    <?php if (!$prize->processed) { ?>
                                        <a class="escrow-status idle red"></i> Pending..</a>
                                    <?php } else { ?>
                                        <?php if ($prize->received == 1) { ?>
                                            <a class="shipinfo escrow-status green" data-id="<?php echo $prize->id; ?>">Received!</a>
                                        <?php } else if (isset($prize->received) && $prize->received == 0) { ?>
                                            <a class="shipinfo escrow-status red" data-id="<?php echo $prize->id; ?>">Not Received!</a>
                                        <?php } else { ?>
                                            <a class="shipinfo escrow-status blue" data-id="<?php echo $prize->id; ?>"></i> Processed!</a>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php  endforeach; ?>
                    </tbody>
                </table>

            </div>

        </div>
    </div>

</div>