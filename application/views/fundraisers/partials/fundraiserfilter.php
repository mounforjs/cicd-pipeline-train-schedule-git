<div class="row">
   <div class="col">
      <div class="searchcardcontainer">
         <div class="filter-wrapper">
            <div class="toggle-filter-wrapper">
               <div class="row">
                  <div class="d-block d-sm-none col-12">
                     <div class="d-block d-sm-none col-10" style="padding-right:0;" id="mobile-searchbar-wrapper"> 
                     </div>
                     <div class="modal fade" id="myModal" role="dialog">
                        <div class="modal-dialog">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <h4 class="whitetext">
                                    <span class="glyphicon glyphicon-lock">
                                    </span>FILTER
                                 </h4>
                                 <button type="button" class="close" data-dismiss="modal">×
                                 </button>
                              </div>
                              <div class="modal-body">
                                 [MOBILE Filter forms go here]
                              </div>
                              <div class="modal-footer">
                                 <button type="submit" class="btn btn-danger btn-default pull-left" data-dismiss="modal">
                                 <span class="glyphicon glyphicon-remove">
                                 </span> SUBMIT
                                 </button>
                                 <button type="submit" class="btn btn-danger btn-default pull-left" data-dismiss="modal">
                                 <span class="glyphicon glyphicon-remove">
                                 </span> CANCEL
                                 </button>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row beneficiaryfilter">
               <div class="col-lg">
				   <p>Beneficiary Type:</p><div class="form-check form-check-inline">
                     <label class="form-check-label">
                        <input name="fundraise_type" type="radio" class="form-check-input fundraise_type" value="all" id="fundraiseAll" checked/>
                        <img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/17x17/allbeneficiaries.png"> All
                     </label>
                  </div>
                  <div class="form-check form-check-inline">
                     <label class="form-check-label charityfltr">
                        <input name="fundraise_type" type="radio" class="form-check-input fundraise_type" value="charity"/>
                        <i class="fas fa-hand-holding-heart" aria-hidden="true"></i> Charity
                     </label>
                  </div>
                  <div class="form-check form-check-inline">
                     <label class="form-check-label projectfltr">
                        <input name="fundraise_type" type="radio" class="form-check-input fundraise_type" value="project"/>
                        <i class="fas fa-lightbulb" aria-hidden="true"></i> Project
                     </label>
                  </div>
                  <div class="form-check form-check-inline">
                     <label class="form-check-label causefltr">
                        <input name="fundraise_type" type="radio" class="form-check-input fundraise_type" value="cause"/>
                        <i class="fa fa-globe" aria-hidden="true"></i> Cause
                     </label>
                  </div>
                  <div class="form-check form-check-inline">
                     <label class="form-check-label educationfltr">
                        <input name="fundraise_type" type="radio" class="form-check-input fundraise_type" value="education"/>
                        <i class="fas fa-graduation-cap"></i> Education
                     </label>
                  </div>
               </div>
               <div class="col-md">
                  <select placeholder="Search Beneficiaries" id="listoffundraisers" class="pt-2">
                     <?php foreach($search as $key => $fundraiser) { ?>
                        <option disabled selected value></option>
                        <option value="<?= $fundraiser["slug"]; ?>"><?= $fundraiser["name"]; ?></option>
                     <?php } ?>
                  </select>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
