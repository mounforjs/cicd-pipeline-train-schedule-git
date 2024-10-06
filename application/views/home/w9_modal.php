<div class="modal custom-modal fade" id="w9modal" tabindex="-1" role="dialog" aria-labelledby="w9modalform" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="w9modalform">W-9 Form</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <i>We are required to file a 1099 form with the IRS for all individuals and businesses
                earning over $599. We cannot authorize prize or cash distributions in excess of $599 until this form has been submitted.</i>
                <form id="w9-form" onsubmit="return false;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Full Name</label>
                                <!-- Full name is editable from original profile name and always required. Should we be adding this to the data since it won't always be the same as the profile name? -->
								<input id="fullname" name="fullname" type="text" class="form-control" value="<?php echo getprofile()->firstname ." ".getprofile()->lastname; ?>" >
                                <small>As shown on your income tax return</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Business/Disregarded Entity Name</label>
                                <!-- Bussiness name should be required and visible only if EIN is selected -->
								<input id="businessname" name="businessname" type="text" class="form-control" value="<?php echo $w9_data[0]->business_name; ?>" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group mb-0">
                                        <label>Taxpayer ID Number</label>
										<!-- Changed from a minimum of 3 characters to a min of 9 characters since both SSN and EIN are 9 characters I'm not sure if this effects anything on the backend -->
                                        <input id="taxpayerid" name="taxpayerid" type="text" class="form-control" placeholder="Taxpayer ID" value="<?php echo $w9_data[0]->taxpayer_id; ?>">
                                    </div>
                                </div>
                                <div class="col">
                                    <input id="ssn" name="taxpayidtype" type="radio" value="1" class="abc large-radio" <?php if ($w9_data[0]->ssn_or_ein = 1) {?> checked="checked" <?php } ?>><label class="switch">&nbsp; SSN</label>
                                </div>
                                <div class="col">
                                    <input id="ein" name="taxpayidtype" type="radio" value="0" class="abc large-radio" <?php if ($w9_data[0]->ssn_or_ein = 0) {?> checked="checked" <?php } ?>><label class="switch">&nbsp; EIN</label>
                                </div>
                            </div>
                            <small>Number and type must correspond with name given<br><br></small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Entity Type</label>
                                <!-- Individual/sole proprietor should be the only option if SSN is selected -->
								<select aria-invalid="false" class="form-control" name="taxclass" id="taxclass">
                                <?php if ($w9_data[0]->tax_classification) {?>
                                        <option value="<?php echo $w9_data[0]->tax_classification;?>" selected="selected"><?php echo $w9_data[0]->tax_classification;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Exempt Payee Code <small>(if any)</small></label>
                                <select class="form-control" id="exemptpayee" name="exemptpayee">
                                    <?php if ($w9_data[0]->exempt_payee_code) {?>
                                        <option value="<?php echo $w9_data[0]->exempt_payee_code;?>" selected="selected"><?php echo $w9_data[0]->exempt_payee_code;?></option>
                                    <?php } ?>
                                    <option value="NA">N/A</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                </select>
								<small><a href="https://www.irs.gov/pub/irs-pdf/fw9.pdf" target="_blank">Please see here for the description of each code</a></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Exemption from FATCA reporting code <small>(if any)</small></label>
                                <select class="form-control" id="fatcareporting" name="fatcareporting">
                                    <?php if ($w9_data[0]->fatca_report_code) {?>
                                        <option value="<?php echo $w9_data[0]->fatca_report_code;?>" selected="selected"><?php echo $w9_data[0]->fatca_report_code;?></option>
                                    <?php } ?>

                                    <option value="NA">N/A</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                    <option value="E">E</option>
                                    <option value="F">F</option>
                                    <option value="G">G</option>
                                    <option value="H">H</option>
                                    <option value="I">I</option>
                                    <option value="J">J</option>
                                    <option value="K">K</option>
                                    <option value="L">L</option>
                                    <option value="M">M</option>
                                </select>
								<small><a href="https://www.irs.gov/pub/irs-pdf/fw9.pdf" target="_blank">Please see here for the description of each code</a></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Address</label>
                                <input name="address" type="text" class="form-control" id="address" value="<?php echo $w9_data[0]->address; ?>" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>City</label>
                                <input name="city" type="text" class="form-control" id="city" value="<?php echo $w9_data[0]->city; ?>" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>State</label>
                                <select class="form-control" id="state" name="state">
                                <?php if ($w9_data[0]->state) {?>
                                        <option value="<?php echo $w9_data[0]->state;?>" selected="selected"><?php echo $w9_data[0]->state;?></option>
                                <?php } ?>
                                <?php foreach (states() as $key => $state):?>
                                    <option value="<?php echo $key?>">
                                        <?php echo $state; ?>
                                    </option>
                                <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Zip Code</label>
                                <input name="zipcode" type="text" class="form-control" id="zipcode" value="<?php echo $w9_data[0]->zipcode; ?>">
                            </div>
                        </div>
                        <div class="col-12">
                            <p><strong>Under penalties of perjury, I certify that:</strong></p>
                            <ol>
                                <li>The number shown on this form is my correct taxpayer identification number (or I am waiting for a number to be issued to me); and</li>
                                <li>I am not subject to backup withholding because; (a) I am exempt from backup withholding, or (b) I have not been notified by the Internal Revenue Service (IRS) that I am subject to backup withholding as a result of a failure to report all interest or dividends, or (c) the IRS has notified me that I am no longer subject to backup withholding; and</li>
                                <li>I am a U.S. citizen or other U.S. person; and</li>
                                <li>The FATCA code(s) entered on this form (if any) indicate that I am exempt from FATCA reporting is correct.</li>
                            </ol>
                            <p>The Internal Revenue Service does not require your consent to any provision of this document other than the certifications required to avoid backup withholding.</p>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Your Signature</label>
                                <input id="signature" name="signature" type="text" class="form-control">
                                <small>Typing your name acts as your signature</small>
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <input name="date" type="text" class="form-control" id="date" value="<?php echo date("m-d-Y"); ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <p><strong>Electronic Delivery</strong></p>
                                <input type="checkbox" <?php if ($w9_data[0]->electronic_copy == 1) {?> checked="checked" <?php } ?> 
                                aria-label="Only issue me electronic 1099 forms" name='ecopy' id="ecopy"
                                value='<?php echo $w9_data[0]->electronic_copy; ?>'>&nbsp; 
                                <label>Only issue me electronic 1099 forms</label><br>
                                <small><a href="<?php echo asset_url('disclosure'); ?>" target="_blank">Please read important disclosure information</a></small>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                        <input type="hidden" name="w9" value="<?php echo $w9_data[0]->id; ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
$('#ecopy').click(function () {
    if ($('input[name="ecopy"]').is(':checked')) {
        $(this).val('1');
    } else {
        $(this).val('0');
    }
})

mySsnVar = '<option value="Individual/sole proprietor">Individual/sole proprietor</option>';

$('#exemptpayee').append(mySsnVar);
$('#ein').prop('checked', false);
$("#ein").attr('disabled', true);
$('#taxclass').append(mySsnVar);

$('#businessname').on('keyup', function() {
    $('#ein').prop("checked", true);
    $("#ssn").attr('disabled', true);
    if ($(this).val().length === 0) {
        $("#ssn").attr('disabled', false);
        $("#ein").attr('disabled', true);
        $('#ssn').prop("checked", true);
        $('#ein').prop('checked', false);
        $("#exemptpayee").prop('disabled', true);
        $("#fatcareporting").prop('disabled', true);
        $('#taxclass').empty();
        $('#taxclass').append(mySsnVar);
    }
    else {
        var selectElem = $("#taxclass");
        var colors = { "C Corporation": "C Corporation", "S Corporation": "S Corporation", "Partnership": "Partnership",
            "Trust/estate": "Trust/estate", "LLC (Single Member)": "LLC (Single Member)", "LLC (C Corporation)": "LLC (C Corporation)",
            "LLC (S Corporation)": "LLC (S Corporation)", "LLC (Partnership)": "LLC (Partnership)", "Exempt Payee": "Exempt Payee" };

    
        // Iterate over object and add options to select
        selectElem.empty();
        $.each(colors, function(index, value) {
            if ( !$('#taxclass option[value="'+value+'"]').length ) {
            $("<option/>", {
                value: index,
                text: value
            }).appendTo(selectElem)};
        });
        
        $("#ein").attr('disabled', false);
        $("#ssn").attr('disabled', true);
        $('#ein').prop('checked', true);
        $('#ssn').prop('checked', false);
        $("#exemptpayee").prop('disabled', false);
        $("#fatcareporting").prop('disabled', false);
    }
});

$("form#w9-form").validate({
        rules: {
            taxpayerid: {
                required: true,
                taxid: true,
            },
            taxpayidtype:  {
                required: true,
            },
            taxclass:  {
                required: true,
            },
            exemptpayee:  {
                required: false,
            },
            fatcareporting:  {
                required: true,
            },
            address:  {
                required: true,
            },
            city:  {
                required: true,
            },
            state:  {
                required: true,
            },
            zipcode:  {
                required: true,
                digits: true
            },
            signature:  {
                required: true,
            },
        },
    
        submitHandler: function(form, event) {
            event.preventDefault();
        
            $.ajax({
                type: "POST",
                url: 'user/w9form',
                data: {
                    id: $("input[name='w9']").val(),
                    businessname: $("input[name='businessname']").val(),
                    taxpayerid: $("input[name='taxpayerid']").val(),
                    taxpayidtype: $("input[name='taxpayidtype']:checked").val(),
                    taxclass: $("#taxclass option:selected" ).text(),
                    exemptpayee: $("#exemptpayee option:selected").text(),
                    fatcareporting: $("#fatcareporting option:selected").text(),
                    address: $("input[name='address']").val(),
                    city: $("input[name='city']").val(),
                    state: $( "#state option:selected").text().replace(/\s+/g, ' ').trim(),
                    zipcode: $("input[name='zipcode']").val(),
                    signature: $("input[name='signature']").val(),
                    ecopy: $("input[type='checkbox'][name='ecopy']").val(),
                },
                success: function(data) {
                    if (data == '1') {
                        showSweetAlert("Your w9 form has been submitted.","Great", "success");
                        window.location.reload();
                    }
                }
            });
        }
        
    });
    $.validator.addMethod("taxid", function(value, element) {
	    return this.optional(element) || /^(\d{3})-?\d{2}-?\d{4}$/i.test(value) || /^(\d{2})-?\d{7}$/i.test(value)
    }, "Invalid Tax ID");
});
</script>