<div id="address<?php echo $key; ?>" class="col-md-6<?php echo (isset($claim) && $claim) ? "" : ""; ?> pt-1 <?php echo (isset($claim) && $claim && $address->def) ? "selected": ""; ?>">
    <div class="addressContainer">
        <div class="card addresscard mb-3">
            <div class="card-header">
                <div class="row justify-content-between">
                    <div class="col">
                        <h3 class="addressName"><?php echo $address->name; ?></h3>
                    </div>
                    <div class="col-sm-auto">
                        <?php if ($address->def) { ?><span id="defaultAddress" class="defaultaddr"><i class="fas fa-check-circle"></i> Default</span><?php } ?>
                        <?php if (!isset($claim) || !$claim) { ?>
                        <button id="makeDefault<?php echo $key; ?>" data-id="<?php echo $address->id; ?>" type="button" class="btn small <?php echo ($address->def) ? "d-none": ""; ?>">Make Default</button>
                        <?php } else { ?>
                        <button id="selectAddress<?php echo $key; ?>" data-id="<?php echo $address->id; ?>" type="button" class="btn small green <?php echo ($address->def) ? "d-none" : ""; ?>">Select</button>
                        <?php } ?>
                        <div class="btn-group">
                            <button id="editAddress<?php echo $key; ?>" data-id="<?php echo $address->id; ?>" type="button" class="btn small orange"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                            <?php if (!isset($claim) || !$claim) { ?>
                            <button id="removeAddress<?php echo $key; ?>" data-id="<?php echo $address->id; ?>" type="button" class="btn small red"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="addressData" id="addressData<?php echo $key; ?>">
                    <p class="card-text">
                        <span id="<?php echo $key; ?>_address_fullname"><?php echo strtoupper($address->fullname);?></span><br>
                        <span id="<?php echo $key; ?>_address_1"><?php echo strtoupper($address->address_1);?></span> <span id="<?php echo $key; ?>_address_2"><?php echo strtoupper($address->address_2);?></span><br>
                        <span id="<?php echo $key; ?>_city"><?php echo strtoupper($address->city);?></span>, <span id="<?php echo $key; ?>_state"><?php echo strtoupper($address->state);?></span> <span id="<?php echo $key; ?>_zip"><?php echo strtoupper($address->zip);?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>