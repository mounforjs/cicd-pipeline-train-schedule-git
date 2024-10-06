<!-- Content Start -->
<style>
    .edit{
    width: 100%;
    height: 25px;
    }
    .editMode{
    border: 1px solid black;
    }
</style>
<content class="content adminpage">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12 p-4">
                <h1>Manage Payment Method Keys</h1>

                <div class="carddivider"></div>

                <!-- <table id="myAdvancedTable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" >        </table> -->
                <div class="row">
                    <div class="table-responsive col-md-6">
                        <h2 class="sub-header">PAYPAL</h2>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="col-md-1">Server</th>
                                    <th class="col-md-2">Gateway ID</th>
                                    <th class="col-md-3">Gateway URL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="col-md-1">Development</td>
                                    <td class="col-md-2">
                                        <div contentEditable='true' class='edit' id='pDevId'> 
                                            <?php echo $paypalDevKeys[0]['method_id']; ?>
                                        </div>
                                    </td>
                                    <td class="col-md-3">
                                        <div contentEditable='true' class='edit' id='pDevUrl'> 
                                            <?php echo $paypalDevKeys[0]['method_url']; ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-md-2">Production</td>
                                    <td class="col-md-2">
                                        <div contentEditable='true' class='edit' id='pProdId'> 
                                            <?php echo $paypalProdKeys[0]['method_id']; ?>
                                        </div>
                                    </td>
                                    <td class="col-md-3">
                                        <div contentEditable='true' class='edit' id='pProdUrl'> 
                                            <?php echo $paypalProdKeys[0]['method_url']; ?>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="container">
                            <h4>Set Keys to:</h4>
                            <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" method = "<?php echo $paypalDevKeys[0]['name']; ?>" name="payPalKeyRadio" value="d" 
                                <?php 
                                    if (($server == 'dev' && $paypalDevKeys[0]['activeStatus'] == '1') || 
                                        ($server == 'prod' && $paypalProdKeys[0]['activeStatus'] == '0')) {
                                        echo 'checked';
                                    }
                                ?>>
                            <label class="form-check-label">
                            Development
                            </label>
                            </div>
                            <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" method = "<?php echo $paypalProdKeys[0]['name']; ?>" name="payPalKeyRadio" value="p"
                                <?php 
                                    if (($server == 'dev' && $paypalDevKeys[0]['activeStatus'] == '0') || 
                                        ($server == 'prod' && $paypalProdKeys[0]['activeStatus'] == '1')) {
                                        echo 'checked';
                                    }
                                ?>>
                            <label class="form-check-label">
                            Production
                            </label>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive col-md-6">
                        <h2 class="sub-header">STRIPE</h2>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="col-md-1">Server</th>
                                    <th class="col-md-2">Secret Key</th>
                                    <th class="col-md-3">Publishable Key</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="col-md-1">Development</td>
                                    <td class="col-md-2">
                                        <div contentEditable='true' class='edit' id='sDevSectKey'> 
                                            <?php echo $stripeDevKeys[0]['secret_key']; ?>
                                        </div> 
                                    </td>
                                    <td class="col-md-3">
                                        <div contentEditable='true' class='edit' id='sDevPubsKey'> 
                                            <?php echo $stripeDevKeys[0]['publishable_key']; ?>
                                        </div> 
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-md-2">Production</td>
                                    <td class="col-md-2">
                                        <div contentEditable='true' class='edit' id='sProdSectKey'> 
                                            <?php echo $stripeProdKeys[0]['secret_key']; ?>
                                        </div> 
                                    </td>
                                    <td class="col-md-3">
                                        <div contentEditable='true' class='edit' id='sProdPubsKey'> 
                                            <?php echo $stripeProdKeys[0]['publishable_key']; ?>
                                        </div> 
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="container">
                            <h4>Set Keys to:</h4>
                            <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" method = "<?php echo $stripeDevKeys[0]['name']; ?>" 
                                name="stripeKeyRadio" value="d" 
                                <?php 
                                    if (($server == 'dev' && $stripeDevKeys[0]['activeStatus'] == '1') || 
                                        ($server == 'prod' && $stripeProdKeys[0]['activeStatus'] == '0')) {
                                        echo 'checked';
                                    }
                                ?>>
                            <label class="form-check-label">
                            Development
                            </label>
                            </div>
                            <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" method = "<?php echo $stripeProdKeys[0]['name']; ?>" 
                                name="stripeKeyRadio" value="p" 
                                <?php 
                                    if (($server == 'dev' && $stripeDevKeys[0]['activeStatus'] == '0') || 
                                     ($server == 'prod' && $stripeProdKeys[0]['activeStatus'] == '1')) {
                                     echo 'checked';
                                    }
                                ?>>
                            <label class="form-check-label">
                            Production
                            </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</content>
<!-- Content End -->
<script>
    $(document).ready(function() {
        var $oldValue;
        var $editId;
        // Add Class
        $('.edit').click(function() {
            $(this).addClass('editMode');
            $oldValue = $(this).text().trim();
        });

        // Save data
        $(".edit").focusout(function() {
            $(this).removeClass("editMode");
            var value = $(this).text().trim();
            var id =  $(this).attr('id');

            $editId = '#'+id;

            if (value == $oldValue) {
                return false;
            }

            var title = 'Are you sure you want to change key value?';

            showSweetConfirm(text='', title, "warning", function(confirmed) {
                if (!confirmed) {
                    showSweetAlert('Key value has not been changed', 'Cancelled', 'warning');
                    $($editId).text($oldValue);
                    return false;
                } else {
                    $('#divLoading').addClass('show');
                    $.ajax({
                        method:"POST",
                        data:{keyval:value, keyId:id},
                        url: window.location.origin + '/admin/keyValUpdate',
                        success: function(result) {
                            if (result) {
                                showSweetAlert('Key value has been updated', 'Great');
                            } else {
                                showSweetAlert('Key value could not be updated', 'Oops', 'error');
                            }
                        }
                    });
                }     
            }); 
        })
  
        $('input[type=radio]').change(function() {
            var server = "<?php echo $server; ?>";
            var keyvalue = this.value;
            if (this.value == 'd') {
                var message = (server == 'dev') ? 'Sandbox keys?' : 'Sandbox keys on Production?';
                var title = 'Are you sure you want to test ' + message;
            } else if (this.value == 'p') {
                var message = (server == 'prod') ? 'Live keys?' : 'Live Keys on Development?';
                var title = 'Are you sure you want to test ' + message;
            }

            var method = $(this).attr('method');
            
            showSweetConfirm(text='', title, "warning", function(confirmed) {
                if (!confirmed) {
                    showSweetAlert('Key environment has not been changed', 'Cancelled', 'warning');
                    return false;
                } else {
                    $('#divLoading').addClass('show');
                    $.ajax({
                        method:"POST",
                        data:{switchVal: keyvalue, methodname: method},
                        url: window.location.origin + '/admin/keyValEnvSwitch',
                        success: function(result) {
                            if (result) {
                                showSweetAlert('Key environment has been changed', 'Great');
                            } else {
                                showSweetAlert('Key environment could not be changed', 'Cancelled', 'warning');
                            }
                        }
                    });
                }     
            }); 
        });

    });
</script>