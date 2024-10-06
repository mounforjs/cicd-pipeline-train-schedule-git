<!-- Content Start -->
<content class="content adminpage">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 p-4">
                <h1>Manage Distributions</h1>

                <div class="row mb-1">
                    <div class="col-sm-2 ml-auto">
                        <select id="filter" name="filter" class="form-control mb-2 ml-auto">
                            <option value="0">All</option> 
                            <option value="1">Processed</option>
                            <option value="2">Reviewable</option>
                            <option value="3">Approved</option>
                        </select>
                    </div>
                </div>

                <input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>"/>
                <table id="myAdvancedTable" class="table table-striped table-bordered thead-dark table-hover table-sm" data-type="distributions">
                <thead>
                    <tr>
                        <th>Ref Num.</th>
                        <th>Status</th>
                        <th>Review</th>
                        <th>Approved</th>
                        <th>Game</th>
                        <th>Creator</th>
                        <th>Creator Fundraiser</th>
                        <th>Winner</th>
                        <th>Winner Fundraiser</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($distributions as $key => $distribution) : ?>
                    <tr class="distribution" data-id="<?php echo $distribution->id; ?>">
                        <td><?php echo $distribution->ref_number; ?></td>
                        <td><?php echo ($distribution->status == 1) ? '<a class="btn btnSmall red">Pending</a>' : '<a class="btn btnSmall green">Processed</a>'; ?></td>
                        <td><?php echo ($distribution->review == 1) ? (isset($distribution->approved) ? '<a class="btn btnSmall green">Reviewed</a>' : '<a class="btn btnSmall red">Reviewable</a>') : '<a class="btn btnSmall">N/A</a>'; ?></td>
                        <td><?php echo ($distribution->approved == 1) ? '<a class="btn btnSmall green">Approved</a>' : ((isset($distribution->approved)) ? '<a class="btn btnSmall red">Not Approved</a>' : '<a class="btn btnSmall">N/A</a>'); ?></td>
                        <td><?php echo $distribution->game_id . " - " . $distribution->game_name; ?></td>
                        <td><?php echo $distribution->creator_id . " - " . $distribution->creator_name; ?></td>
                        <td><?php echo $distribution->creator_fundraise_id . " - " . $distribution->creator_charity_name; ?></td>
                        <td><?php echo $distribution->winner_id . " - " . $distribution->winner_name; ?></td>
                        <td><?php echo $distribution->winner_fundraise_id . " - " . $distribution->winner_charity_name; ?></td>
                    </tr>
                    <?php  endforeach; ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="distributionDetailModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Distribution Details</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <h3>Distribution Info: </h3>
                            </div>  

                            <div class="row overflow-auto">
                                <table id="distributionInfo" class="table table-striped table-bordered thead-dark table-hover table-sm">
                                    <thead>
                                        <tr>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    </tbody>     
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <h3>Game Info: </h3>
                            </div>  

                            <div class="row overflow-auto">
                                <table id="gameInfo" class="table table-striped table-bordered thead-dark table-hover table-sm">
                                    <thead>
                                        <tr>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    </tbody>     
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <h3>Creator Info: </h3>
                            </div>

                            <div class="row overflow-auto">
                                <table id="creatorInfo" class="table table-striped table-bordered thead-dark table-hover table-sm">
                                    <thead>
                                        <tr>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                    </tbody>       
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <h3>Winner Info: </h3>
                            </div>

                            <div class="row overflow-auto">
                                <table id="winnerInfo" class="table table-striped table-bordered thead-dark table-hover table-sm">
                                    <thead>
                                        <tr>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                    </tbody>    
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <h3>Shipping Info: </h3>
                            </div>

                            <div class="row overflow-auto">
                                <table id="shippingInfo" class="table table-striped table-bordered thead-dark table-hover table-sm">
                                    <thead>
                                        <tr>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                    </tbody>    
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <div class="col">
                        <div class="row" id="refundBtns">
                            <div class="col text-center">
                                <a class="refund btn btnSmall red h-100 disabled" data-type="nullify" disabled>Nullify Winner</a>
                            </div>
                            <div class="col text-center">
                                <a class="refund btn btnSmall red h-100 disabled" data-type="comp" disabled>Compensate Winner</a>
                            </div>
                            <div class="col text-center">
                                <a class="refund btn btnSmall red h-100 disabled" data-type="partial" disabled>Partial Refund</a>
                            </div>
                            <div class="col text-center">
                                <a class="refund btn red btnSmall h-100 disabled" data-type="complete" disabled>Complete Refund</a>
                            </div>
                        </div> 
                        <div class="row mt-4 d-none" id="refundTabs">
                            <div class="col-md-9 mx-auto" style="border: 1px solid; padding: 10px;">
                                <div class="col d-none" id="nullTab">
                                    <div class="row">
                                        <div class="col">
                                            <h3>Nullify Winner: </h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col my-auto">
                                            <p>Winner reward is given to creator's fundraiser. Winner receives nothing.</p>
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col my-auto">
                                            <label for="note">Note: </label>
                                            <textarea class="form-control" name="note" rows="2" style="resize: none;"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <a class="confirmRefund btn green pull-right" data-id="" data-game-id="">Confirm Refund</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col d-none" id="compTab">
                                    <div class="row">
                                        <div class="col">
                                            <h3>Compensate Winner: </h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col my-auto">
                                            <p>Winner is compensated with credit taken from the creator's share. Added onto existing reward.</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col my-auto">
                                            <label for="compensateValue">Amount: </label>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                                <input id="compensateValue" class="valid form-control" name="compensateValue" type="number" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col my-auto">
                                            <label for="note">Note: </label>
                                            <textarea class="form-control mb-2" name="note" rows="2" style="resize: none;"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <a class="confirmRefund btn green pull-right" data-id="" data-game-id="">Confirm Refund</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col d-none" id="partialTab">
                                    <div class="row">
                                        <div class="col">
                                            <h3>Partial Refund: </h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col my-auto">
                                            <p>Winner payments, taken out of total raised, are refunded and credited back. Applies to only this distribution, but alters the totals of other distributions.</p>
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col my-auto">
                                            <label for="note">Note: </label>
                                            <textarea class="form-control mb-2" name="note" rows="2" style="resize: none;"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <a class="confirmRefund btn green pull-right" data-id="" data-game-id="">Confirm Refund</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col d-none" id="completeTab">
                                    <div class="row">
                                        <div class="col">
                                            <h3>Complete Refund: </h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col my-auto">
                                            <p>All players are refunded any credits they spent playing the game. Applies to all distributions, not just this one.</p>
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col my-auto">
                                            <label for="note">Note: </label>
                                            <textarea class="form-control mb-2" name="note" rows="2" style="resize: none;"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <a class="confirmRefund btn green pull-right" data-id="" data-game-id="">Confirm Refund</a>
                                        </div> 
                                    </div>
                                </div>
                                
                            </div>
                        </div> 
                    </div> 
                </div>

            </div>
        </div>
    </div>



   <div id="divLoading"> </div>
</content>