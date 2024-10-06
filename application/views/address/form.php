<form id="address-form" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Name</label>
                <div class="icon-addon addon-lg">
                    <input name="address_name" id="address_name" type="text" class="form-control" placeholder="Give your address a name" value="" required>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>Full Name</label>
                <div class="icon-addon addon-lg">
                    <input name="address_fullname" id="address_fullname" type="text" class="form-control" value="<?php echo $fname . " " . $lname; ?>" required>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>Address 1</label>
                <div class="icon-addon addon-lg">
                    <input name="address_1" id="address_1" type="text" class="form-control" placeholder="Street address or P.O. Box" value="" required>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>Address 2</label>
                <div class="icon-addon addon-lg">
                    <input name="address_2" id="address_2" type="text" class="form-control" placeholder="Apt, suite, unit, building, floor, etc." value="">
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>City</label>
                <div class="icon-addon addon-lg">
                    <input name="address_city" id="address_city" type="text" class="form-control" placeholder="City" value="" required>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>State</label>
                <div class="icon-addon addon-lg">
                    <select class="form-control" id="address_state" name="address_state" required>
                        <option value="" selected disabled></option>
                        <?php foreach (states() as $key => $state):?>
                            <option value="<?php echo $key?>"><?php echo $state; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>Zip</label>
                <div class="icon-addon addon-lg">
                    <input name="address_zip" id="address_zip" type="number" pattern="[0-9]{5}"class="form-control" placeholder="Zip" value="" required>
                </div>
            </div>
        </div>
    </div>
</form>