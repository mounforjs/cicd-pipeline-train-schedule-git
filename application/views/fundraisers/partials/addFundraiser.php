<div class="col-sm-12" id="fundraiserbox" >
   <form enctype="multipart/form-data" id="fund-form" onsubmit="return false;" method="POST" class="mt-60">
      <div class="pw-form">
         <div class="row text-center fType m-auto">
            <div class="col-sm-3 col-6 fundraiser-type-selector-left fundraiser-type-selector">
               <label class="switch" id="charity-search">
               <input type="radio" class="abc fundraiser-type" name="fundraise_type" id="fundraise_type_charity" value="charity">
               <!-- <span class="slider round"></span> -->
               </label>
               <p class="fundraiser-type-name"><i class="fa fa-hand-holding-heart fund-hand-holding-heart" aria-hidden="true"></i> Non-Profit</p>
               <span class="fundraiser-type-description">NPR, Make a Wish <br>501(c)(3) only</span>
            </div>
            <div class="col-sm-3 col-6 fundraiser-type-selector-center-left fundraiser-type-selector">
               <label class="switch" id="project-search">
               <input type="radio" class="abc fundraiser-type" name="fundraise_type" id="fundraise_type_project" value="project" >
               <!-- <span class="slider round"></span> -->
               </label>
               <p class="fundraiser-type-name"><i class="fa fa-lightbulb fund-lightbulb" aria-hidden="true"></i> Project</p>
               <span class="fundraiser-type-description">Key Finding App, Aerial Photo</span><span class="fundraiser-large-expand fundraiser-type-description">graphy</span><span class="fundraiser-type-description"> Rocket</span>
            </div>
            <div class="col-sm-3 col-6 fundraiser-type-selector-center-right fundraiser-type-selector">
               <label class="switch" id="cause-search">
               <input type="radio" class="abc fundraiser-type" name="fundraise_type" id="fundraise_type_cause" value="cause" >
               <!-- <span class="slider round"></span> -->
               </label>
               <p class="fundraiser-type-name"><i class="fa fa-globe fund-globe" aria-hidden="true"></i> Cause</p>
               <span class="fundraiser-type-description">Our House is Flooded!, Capstone Research</span>
            </div>
            <div class="col-sm-3 col-6 fundraiser-type-selector-right fundraiser-type-selector">
               <label class="switch" id="education-search">
               <input type="radio" class="abc fundraiser-type" name="fundraise_type" id="fundraise_type_education" value="education" >
               <!-- <span class="slider round"></span> -->
               </label>
               <p class="fundraiser-type-name"><i class="fa fa-graduation-cap fund-graduation-cap" aria-hidden="true"></i> Education</p>
               <span class="fundraiser-type-description">Develop an HTML Game, Learn Solidworks</span>
            </div>
            <div class="col-sm-12 justify-content-left text-danger" id="fundraise-type-error"></div>
         </div>
         <br>
         <?php if (!isset($select_fundraiser) || !$select_fundraiser) { ?>
            <div class="row" id="fundraiserEditReason">
               <div class="col-sm-12">
                  <div class="form-group">
                     <label class="control-label">Please provide reason for editing your beneficiary:</label>
                     <p class="ml-1 pull-right"><span id="rchars">500</span> Character(s) Remaining</p>
                     <textarea rows='4' cols="100" name="reasonDescription" class="form-control " id="editReason"  maxlength="500" ></textarea>
                  </div>
               </div>
            </div>
         <?php } ?>
         
         <div class="row" id="charityCheck">
            <div class="col-sm-12">
               <div class="form-group text-center  d-none  decision-maker-check" id="authorize-charity" >
                  <label><strong>Are you a decision-maker within this 501(c)(3) non-profit who is responsible for fundraising?</strong> <small>(required)</small></label><span id="decision-maker-error"></span><br>
                  <label class="radio-inline"><input type="radio" name="is_non" value="1"/>Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
                  <label class="radio-inline"><input type="radio" name="is_non" value="0"/>No</label><br>
                  <span id="authorize-no"></span>
               </div>
            </div>
         </div>
         <div class="form-group" id="fundraise-form">
            <div class="row">
               <div class="col-sm-3 text-center">
                  <!-- Upload -->
                  <div class="img-upload">
                     <div class="img-edit">
                        <input type='file' id="fundraiseimageUpload" accept=".png, .jpg, .jpeg"  name="fundraise_img_path" class="commonImageUpload"
                           form-id="fund-form"
                           preview-at="#addFundraiserImagePreview"
                           />
                        <label for="fundraiseimageUpload"></label>
                     </div>
                     <div class="img-preview">
                        <img id="addFundraiserImagePreview" class="imagePreview" src="<?php $image = getImagePathSize("", "image_upload_placeholder"); echo $image["image"]; ?>" onerror="imgError(this, '<?= $image['fallback']; ?>');">
                     </div>
                  </div>
                  <br><br>
                  <!-- End Upload -->
               </div>
               <div class="col-sm-9 text-left">
                  <div class="form-group">
                     <label class="control-label">Beneficiary Name:</label>
                     <input name="form_charity_name" type="text" class="form-control" id="form_charity_name" value="" placeholder = "Must be unique on WinWinLabs">
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm-12">
                  <div class="form-group">
                     <label class="control-label">Beneficiary Description:</label>
                     <p class="ml-1 pull-right"><span id="rchars"> 500</span> characters maximum</p>
                     <textarea rows='4' cols="100" name="form_charity_desc" placeholder = "Please provide the relevant details, as concisely as possible" class="form-control summernote showTextBoxLength" id="form_charity_desc"  maxlength="500" onkeyup="showTextBoxLength('form_charity_desc', 'rchars');"></textarea>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm-12">
                  <div class="form-group">
                     <label class="control-label">Beneficiary Address:</label>
                     <input name="address" type="text" class="form-control" id="form_charity_address"  value="" placeholder = "For checks; if not, PayPal, Venmo, Stripe, Zelle, Google to use">
                  </div>
                  <div class="form-group">
                     <label class="control-label">Beneficiary URL:</label>
                     <input name="charity_url" type="text" class="form-control" id="charity_url"  value="" placeholder = "If you have a website or social site for more information">
                  </div>
                  <div class="form-group">
                     <label class="control-label">Beneficiary Contact Personnel:</label>
                     <input name="contact" type="text" class="form-control" id="form_charity_contact"  value="" placeholder = "If different than your WinWinLabs account">
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm-6">
                  <div class="form-group">
                     <label class="control-label">Beneficiary Phone:</label>
                     <input name="phone" type="tel" size="10" class="form-control" id="form_charity_phone" value="" placeholder = "Kept confidential, optional">
                  </div>
               </div>
               <div class="col-sm-6">
                  <div class="form-group label-floating" id="charity-parameters">
                     <label class="control-label">Beneficiary Tax ID:</label>
                     <input type="text" name="form_charity_tax" class="form-control" id="form_charity_tax" value="" >
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm-12">
                  <div class="form-group" id="authorize">
                     <label class="inline">
                     <input type="checkbox" name="fundraise_default_authorize" class="form-checkbox">
                     Would you like to make this your default beneficiary?
                     <span id="authorize-error"></span>
                     </label>
                  </div>
               </div>
            </div>


            <div class="row border-top justify-content-right text-right">
               <div class="col-sm-12 mt-2">
                  <button class="btn pull-left" id="cancel-new-beneficiary" type="button">Cancel</button>
                  <input type='submit' id="fund-submit" class='btn red' value='Submit'/>
                  <span id="fundraise_detail_slug" value=""></span>
               </div>
            </div>
         </div>
      </div>
   </form>
</div>