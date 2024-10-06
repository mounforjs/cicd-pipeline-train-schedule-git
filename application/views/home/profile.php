<content class="content">
   <section>
	   <div class="container-lg mb-3">
    <div class="row">
        <div class="col-md-4 profilesidebar">
            <aside class="user-info-wrapper">
                <div class="user-cover" style="background-image: url(https://dg7ltaqbp10ai.cloudfront.net/fit-in/1000x600/bluewave5.jpg);">
                    <div class="info-label"><i class="fas fa-money-bill" aria-hidden="true"></i> <?php echo (!empty(getBalanceAsFloat())) ? '$' .getBalanceAsFloat() : '$0.00';?></div>
                </div>
                <div class="user-info">
                    <div class="user-avatar"><div class="avatar-upload text-center">
					 <div class="avatar-edit">
						 <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg"  name="profile_img_path" class="commonImageUpload" form-id="profile-form" preview-at="#profileImagePreview"/>
						 <label for="imageUpload"></label>
					 </div>
					 <div class="avatar-preview">
						 <img id="profileImagePreview" class="imagePreview" src="<?php $image = getImagePathSize($userData->profile_img_path, "profile_image"); echo $image["image"]; ?>" onerror="imgError(this, '<?= $image['fallback']; ?>')">
					 </div>
				 
					 </div></div>
                    <div class="user-data">
                        <h3><?php echo $userData->firstname;?> <?php echo $userData->lastname;?></h3><span><?php echo $userData->username;?></span>
                    </div>
                </div>
            </aside>
			<div class="profileinfo-wrap">
                        <ul class="nav nav-tabs" role="tablist" id="tabs">
                           <li class="nav-item <?php echo ($tab == 0) ? 'active' : ''; ?>">
                              <a class="nav-link <?php echo ($tab == 0) ? 'active' : ''; ?>" href="#about-info" role="tab" data-toggle="tab"><i class="fa fa-user" aria-hidden="true"></i> Profile</a>
                           </li>
                           <!-- <li class="nav-item <?php echo ($tab == 1) ? 'active' : ''; ?>">
                              <a class="nav-link <?php echo ($tab == 1) ? 'active' : ''; ?>" href="#profile-info" role="tab" data-toggle="tab"><i class="fa fa-globe" aria-hidden="true"></i> About</a>
                           </li> -->
                           <li class="nav-item <?php echo ($tab == 1) ? 'active' : ''; ?>">
                              <a class="nav-link <?php echo ($tab == 1) ? 'active' : ''; ?>" href="#fundraiser-info" role="tab" data-toggle="tab"><i class="fa fa-heart" aria-hidden="true"></i> Beneficiary</a>
                           </li>
                           <li class="nav-item <?php echo ($tab == 2) ? 'active' : ''; ?>">
                              <a class="nav-link <?php echo ($tab == 2) ? 'active' : ''; ?>" href="#address-info" role="tab" data-toggle="tab"><i class="fa fa-address-card" aria-hidden="true"></i> Addresses</a>
                           </li>
                           <li class="nav-item <?php echo ($tab == 3) ? 'active' : ''; ?>">
                              <a class="nav-link <?php echo ($tab == 3) ? 'active' : ''; ?>" href="#accounts" role="tab" data-toggle="tab"><i class="fa fa-address-book" aria-hidden="true"></i> Accounts</a>
                           </li>
                           <li class="nav-item <?php echo ($tab == 4) ? 'active' : ''; ?>">
                              <a class="nav-link <?php echo ($tab == 4) ? 'active' : ''; ?>" href="#prefs" role="tab" data-toggle="tab"><i class="fa fa-list-alt" aria-hidden="true"></i> Preferences</a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" data-toggle="modal" data-target="#w9modal"><i class="fa fa-file-text" aria-hidden="true"></i> W-9</a>
                           </li>
						 </ul>
			</div>
        </div>
        <div class="col-md-8">
			<div class="profileinfo-wrap">
                        <!-- Tab panes -->
                        <form id="profile-form" method="POST" enctype="multipart/form-data" onsubmit="return false;">
                           <div class="tab-content <?php echo ($tab == 0) ? '' : 'd-none'; ?>">
            
                              <div role="tabpanel" class="tab-pane <?php echo ($tab == 0) ? 'active show' : ''; ?>" id="about-info">
                                 <div class="row d-none d-lg-flex mb-2">           
                                    <div class="col-sm-12">      
                                       <button class="btn btnPrevious blue small pull-left" type="button"><i class="fa fa-arrow-left"></i> Previous</button>
                                       <button class="btn btnNext blue small pull-right" type="button">Next<i class="fa fa-arrow-right"></i></button>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-sm-6 col-sm-offset-1">
                                       <div class="form-group">
                                          <label>First Name <small>(required)</small></label>
                                          <input name="firstname" type="text" class="form-control" placeholder="Andrew..." value="<?php echo $userData->firstname;?>">
                                       </div>
                                    </div>
                                    <div class="col-sm-6 ">
                                       <div class="form-group">
                                          <label>Last Name <small>(required)</small></label>
                                          <input name="lastname" type="text" class="form-control" placeholder="Smith..." value="<?php echo $userData->lastname;?>">
                                       </div>
                                    </div>
                                    <div class="col-sm-6 col-sm-offset-1">
                                       <div class="form-group">
                                          <label>Country <small>(required)</small></label>
                                          <select name="country" class="form-control" id="user_country">
                                          <?php foreach (countries() as $key => $country):?>
                                          <option <?php echo ($userData->country==$key ? 'selected':''); ?> value="<?php echo $key?>"><?php echo $country?></option>
                                          <?php endforeach; ?>
                                          </select>
                                       </div>
                                    </div>
                                    <div class="col-sm-6">
                                       <div class="form-group">
                                          <label>Username <small>(required)</small></label>
                                          <div class="icon-addon addon-lg">
                                             <input name="username" type="text" class="form-control" placeholder="username.." value="<?php echo $userData->username;?>">
                                             <label for="email" class="fa fa-user abouticon" rel="tooltip" title="username"></label>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-sm-6 col-sm-offset-1">
                                       <div class="form-group">
                                          <label>Email <small>(required)</small></label>
                                          <div class="icon-addon addon-lg">
                                             <input name="updateEmail" id="updateEmail" type="email" class="form-control" placeholder="andrew@winwinlabs.org" value="<?php echo $userData->email;?>">
                                             <label for="email" class="fa fa-envelope abouticon" rel="tooltip" title="email"></label>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-sm-6">
                                       <div class="form-group">
                                          <label>Password <small>(required)</small></label>
                                          <div class="icon-addon addon-lg">
                                             <input type="password" class="form-control" id="password" name="password"  placeholder="Password.."  autocomplete="off" value="">
                                             <label for="password" class="fa fa-key abouticon" rel="tooltip" title="password"></label>
                                             <label for="password-toggle" class="fa fa-fw fa-eye-slash toggle-password abouticon2" toggle="#password"></label>
                                          </div>
                                       </div>
                                       <div id="popover-password" class="d-none">
                                          <p>Password Strength: <span id="result"> </span></p>
                                          <div class="progress">
                                             <div id="password-strength" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                             </div>
                                          </div>
                                          <ul class="list-unstyled">
                                             <li class=""><span class="low-upper-case"><i class="fa fa-times" aria-hidden="true"></i></span>&nbsp; 1 lowercase &amp; 1 uppercase</li>
                                             <li class=""><span class="one-number"><i class="fa fa-times" aria-hidden="true"></i></span> &nbsp;1 number (0-9)</li>
                                             <li class=""><span class="one-special-char"><i class="fa fa-times" aria-hidden="true"></i></span> &nbsp;1 Special Character (!@#$%^&*).</li>
                                             <li class=""><span class="eight-character"><i class="fa fa-times" aria-hidden="true"></i></span>&nbsp; Atleast 8 Character</li>
                                          </ul>
                                       </div>
                                    </div>
                                 </div>
								  <div class="col text-right"><input type='submit' class='btn orange <?php echo ($tab != 0) ? 'd-none' : ''; ?>' value='Update' id="update-btn" /></div>
                              </div>
                              <!-- <div role="tabpanel" class="tab-pane <?php echo ($tab == 1) ? 'active show' : ''; ?>" id="profile-info">
                                 <div class="row d-none d-lg-flex mb-2">           
                                    <div class="col-sm-12">      
                                       <button class="btn btnPrevious blue small pull-left" type="button"><i class="fa fa-arrow-left"></i> Previous</button>
                                       <button class="btn btnNext blue small pull-right" type="button">Next<i class="fa fa-arrow-right"></i></button>
                                    </div>
                                 </div>
                                 <div class="col-sm-12">
                                    <h5 class="info-text">This is used to match people with opportunities for mentoring and learning. <small>(fully optional)</small></h5>
                                    <div class="form-group">
                                       <label>What are your top 5 general areas of interest? <small>(comma separated)</small></label>
                                          <input type="text" name="interests" class="form-control tags longfield" value="<?php echo $userData->interests; ?>" data-role="tagsinput">
                                    </div>
                                    <div class="form-group">
                                       <label>What are currently your top 5 strongest skills? <small>(comma separated)</small></label>
                                       <input type="text" name="strengths" class="form-control tags longfield" value="<?php echo $userData->strengths; ?>" data-role="tagsinput">
                                    </div>
                                    <div class="form-group">
                                       <label>What do you hope to become proficient at within the next year? <small>(comma separated)</small></label>
                                    <input type="text" data-role="tagsinput" name="learn_areas" class="form-control tags longfield" value="<?php echo $userData->learn_areas; ?>">
                                    </div>
                                    <div class="form-group">
                                       <label>Select which of the following apply to you: <small>[Select all that apply]</small></label>
                                       <?php 
                                       if (!empty($userData->pathway)) {
                                          $userData->pathway = explode(',', $userData->pathway);
                                       }?>
                                       <div class="row">
                                          <div class="col-sm-4">
                                             <label class="inline"><input type="checkbox" <?php echo (in_array("High School", $userData->pathway) ? 'checked':''); ?> value="High School" name="pathway[]" class="user_status" > I am in high school</label><br>
                                             <label class="inline"><input type="checkbox" <?php echo (in_array("College", $userData->pathway) ? 'checked':''); ?> value="College" name="pathway[]" class="user_status" > I am in college</label><br>
                                             <label class="inline"><input type="checkbox" <?php echo (in_array("Trade School", $userData->pathway) ? 'checked':''); ?> value="Trade School" name="pathway[]" class="user_status" > I am in trade school </label>
                                          </div>
                                          <div class="col-sm-4"><label class="inline"><input type="checkbox" <?php echo (in_array("In a career", $userData->pathway) ? 'checked':''); ?> value="In a career" name="pathway[]" class="user_status" > I am in a career </label><br>
                                             <label class="inline"><input type="checkbox" <?php echo (in_array("Looking for a new career", $userData->pathway) ? 'checked':''); ?> value="Looking for a new career" name="pathway[]" class="user_status" > I am looking for a new career</label><br>
                                             <label class="inline"><input type="checkbox" <?php echo (in_array("Need a hobby!", $userData->pathway) ? 'checked':''); ?> value="Need a hobby!" name="pathway[]" class="user_status" > I need a new hobby</label>
                                          </div>
                                          <div class="col-sm-4"><label class="inline"><input type="checkbox" <?php echo (in_array("Retired", $userData->pathway) ? 'checked':''); ?> value="Retired" name="pathway[]" class="user_status" onClick="uncheck_unselect(this.checked);"> I am retired</label><br>
                                             <label class="inline"><input type="checkbox" <?php echo (in_array("None of your business", $userData->pathway) ? 'checked':''); ?> value="None of your business" name="pathway[]" class="unselect" > I choose not to answer</label>
                                          </div>
                                       </div>
                                       <div id="graduations" style="display: none;">
                                          <label>Year of Graduation <small>(required)</small></label>
                                          <select name="graduation" class="form-control">
                                          <?php
                                             $starting_year  =date('Y');
                                             $ending_year = date('Y', strtotime('+10 year'));
                                             $current_year = date('Y');
                                             for($starting_year; $starting_year <= $ending_year; $starting_year++) {
                                                echo '<option '.($userData->graduation==$starting_year ? 'selected':'').' value="'.$starting_year.'"';
                                                if ( $starting_year ==  $current_year ) {
                                                   echo ' selected="selected"';
                                                }
                                                echo ' >'.$starting_year.'</option>';
                                             }         
                                             ?>   
                                          </select>
                                       </div>
                                    </div>
                                    <fieldset>
                                       <h5>Internship and Technical</h5>
                                       <div class="form-group">
                                          <label>Are you interested in volunteering, internships, mentorships, or co-op opportunities with WinWinLabs?</label><br>
                                          <label class="radio-inline"><input type="radio" name="internship" value="1" <?php echo ($userData->internship=="1" ? 'checked':''); ?> >Yes</label>
                                          <label class="radio-inline"><input type="radio" name="internship" value="0" <?php echo ($userData->internship=="0" ? 'checked':''); ?> >No</label>
                                          <div class="d-none" id="intern-times">
                                             <br>
                                             <label>At what time of the year would you be interested in these? <small>(required)</small></label>
                                             <select name="intern_time" class="form-control">
                                                <option <?php echo ($userData->intern_time=="Other times too" ? 'selected':''); ?> value="Other times too">Year-round</option>
                                                <option <?php echo ($userData->intern_time=="Summer" ? 'selected':''); ?> value="Summer">Only summer</option>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="form-group">
                                          <br>
                                          <label>Do you, or have you ever, participated in any technical competitions?</label><br>
                                          
                                          <label class="radio-inline"><input <?php echo ($userData->is_first=="1" ? 'checked':''); ?> type="radio" name="is_first" value="1"> Yes</label>
                                          <label class="radio-inline"><input <?php echo ($userData->is_first=="0" ? 'checked':''); ?> type="radio" name="is_first" value="0"> No</label> 
                                       
                                          <div id="challenges" style="display: block;">
                                             <br>
                                             <div class="form-group">
                                                <label>In what roles? <small>(required)</small></label><span id="role-error"></span></br>
                                                <label class="radio-inline"><input <?php echo ($userData->status=="student" ? 'checked':''); ?> type="radio" name="status" value="student"> Student</label>
                                                <label class="radio-inline"><input <?php echo ($userData->status=="mentor" ? 'checked':''); ?> type="radio" name="status" value="mentor"> Mentor</label>
                                                <label class="radio-inline"><input <?php echo ($userData->status=="both" ? 'checked':''); ?> type="radio" name="status" value="both"> Both</label></br>

                                                <div class="form-group">
                                                   <br>
                                                   <label>Select technical competitions you participate(d) in:</label>
                                                   <?php 
                                                   if (!empty($userData->challenge)) {
                                                      $userData->challenge = explode(',', $userData->challenge);
                                                   }?>
                                                   <div class="row">
                                                      <div class="col-sm-6">
                                                         <label class="inline"><input type="checkbox" <?php echo (in_array("Bot Ball", $userData->challenge) ? 'checked':''); ?> value="Bot Ball" name="challenge[]" > Bot Ball</label><br>
                                                         <label class="inline"><input type="checkbox" <?php echo (in_array("Best Robotics", $userData->challenge) ? 'checked':''); ?> value="Best Robotics" name="challenge[]" > Best Robotics</label><br>
                                                         <label class="inline"><input type="checkbox" <?php echo (in_array("MATE ROV", $userData->challenge) ? 'checked':''); ?> value="MATE ROV" name="challenge[]" > MATE ROV</label><br>
                                                         <label class="inline"><input type="checkbox" <?php echo (in_array("RoboFest", $userData->challenge) ? 'checked':''); ?> value="RoboFest" name="challenge[]" > RoboFest</label>
                                                      </div>
                                                      <div class="col-sm-6"><label class="inline"><input type="checkbox" <?php echo (in_array("RoboGames", $userData->challenge) ? 'checked':''); ?> value="RoboGames" name="challenge[]"  > RoboGames</label><br>
                                                         <label class="inline"><input type="checkbox" <?php echo (in_array("Robo Olympiad", $userData->challenge) ? 'checked':''); ?> value="Robo Olympiad" name="challenge[]" > Robo Olympiad</label><br>
                                                         <label class="inline"><input type="checkbox" <?php echo (in_array("Robo Sumo", $userData->challenge) ? 'checked':''); ?> value="Robo Sumo" name="challenge[]" > Robo Sumo</label><br>
                                                         <label class="inline"><input type="checkbox" <?php echo (in_array("Other", $userData->challenge) ? 'checked':''); ?> value="Other" name="challenge[]" >Other</label>
                                                      </div>
                                                   </div>
                                                   <br>
                                                   <label class="inline">Please select if you are member of any <i>FIRST</i> Robotics Teams:</label> <br>
                                                   <label class="checkbox-inline">
                                                      <input type="checkbox" <?php echo (in_array("FRC", $userData->challenge) ? 'checked':''); ?> value="FRC" class="roboticCheckbox" name="challenge[]" id="frc"
                                                      onclick=" showTeam(this.checked, 'frc_teams', 'teamname'); " div-id="frc_teams" teamTextboxId="teamname">FRC
                                                   </label>
                                                   <label class="checkbox-inline pl-3">
                                                      <input type="checkbox" <?php echo (in_array("FTC", $userData->challenge) ? 'checked':''); ?> value="FTC" class="roboticCheckbox" id="ftc" name="challenge[]" onclick=" showTeam(this.checked, 'ftc_teams', 'teamnameftc'); " div-id="ftc_teams" teamTextboxId="teamnameftc">FTC
                                                   </label>
                                                   <label class="checkbox-inline pl-3">
                                                      <input type="checkbox" <?php echo (in_array("FLL", $userData->challenge) ? 'checked':''); ?> value="FLL" class="roboticCheckbox" name="challenge[]" id="fll" onclick=" showTeam(this.checked, 'fll_teams', 'teamnamefll'); " div-id="fll_teams" teamTextboxId="teamnamefll">FLL
                                                   </label>
                                                   <label class="checkbox-inline pl-3">
                                                      <input type="checkbox" <?php echo (in_array("JrFLL", $userData->challenge) ? 'checked':''); ?> value="JrFLL" class="roboticCheckbox" name="challenge[]" id="jrfll" onclick=" showTeam(this.checked, 'jrfll_teams', 'teamnamejrfll'); " div-id="jrfll_teams" teamTextboxId="teamnamejrfll">JrFLL
                                                   </label>
                                                </div>
                                                <div class="form-group d-none" id="frcteam">
                                                   <label>Team numbers <small>(comma separated and required)</small></label>
                                                   <div class="input-group" id="frc_teams">
                                                      <span class="input-group-addon">FRC:</span>
                                                      <input type="text" value="<?php echo $userData->frc_team_number; ?>" name="teamname" id="teamname" class="form-control tagsTeam"/>
                                                   </div>
                                                   <div class="hidden mb-2" id="team_info" style="font-weight: 900"></div>
                                                   <div class="input-group d-none" id="ftc_teams">
                                                      <span class="input-group-addon">FTC:</span>
                                                      <input type="text" value="<?php echo $userData->ftc_team_number; ?>" name="teamnameftc" id="teamnameftc" class="form-control tagsTeam" />
                                                   </div>
                                                   <div class="input-group d-none" id="fll_teams">
                                                      <span class="input-group-addon">FLL:</span>
                                                      <input type="text" value="<?php echo $userData->fll_team_number; ?>" name="teamnamefll" id="teamnamefll" class="form-control tagsTeam" />
                                                   </div>
                                                   <div class="input-group d-none" id="jrfll_teams" >
                                                      <span class="input-group-addon">JrFLL:</span>
                                                      <input type="text" value="<?php echo $userData->jrfll_team_number; ?>" name="teamnamejrfll" id="teamnamejrfll" class="form-control tagsTeam" />
                                                   </div>
                                                </div>

                                             </div>
                                          </div>
                                       </div>
                                    </fieldset>
                                    <hr>
                                    <div class="form-group">
                                       <label>Describe some of the goals you want to acheive in the next 1, 3, and 5 years?</label><p class="ml-1 pull-right"><span class ="char_count" id="rcharlifetime">500</span> Character(s) Remaining</p>
                                       <textarea class="form-control showTextBoxLength" placeholder="" rows="6" 
                                       id="lifetime_goals" spanId="rcharlifetime" maxlength="500" onkeyup="showTextBoxLength('lifetime_goals', 'rcharlifetime');"   
                                       name="lifetime_goals"><?php echo $userData->lifetime_goals; ?></textarea>
                                    </div>
                                    <div class="form-group">
                                       <label>Can you provide some ideas for how to enhance our system? </label><p class="ml-1 pull-right"><span class ="char_count" id="rcharwebappfeedback">500</span> Character(s) Remaining</p>
                                       <textarea class="form-control showTextBoxLength" placeholder="" 
                                       id="webapp_feedback" spanId="rcharwebappfeedback" maxlength="500" onkeyup="showTextBoxLength('webapp_feedback', 'rcharwebappfeedback');"
                                       rows="6" name="webapp_feedback"><?php echo $userData->webapp_feedback; ?></textarea>
                                    </div>

                                    <div class="form-group">
                                       <label>How do others describe you? </label> <p class="ml-1 pull-right"><span class ="char_count" id="rcharsUser">500</span> Character(s) Remaining</p>
                                       <textarea class="form-control showTextBoxLength" placeholder=""
                                       rows="6"  name="user_description" id="user_description" spanId="rcharsUser" maxlength="500" 
                                       onkeyup="showTextBoxLength('user_description', 'rcharsUser');"><?php echo $userData->user_description; ?></textarea>
                                    </div>
                                 </div>
                              </div> -->
                           </div>
                        </form>
                        <div class="tab-content <?php echo ($tab == 0) ? 'd-none' : ''; ?>">
                           <div role="tabpanel" class="tab-pane <?php echo ($tab == 1) ? 'active show' : ''; ?>" id="fundraiser-info">
                              <div class="row d-none d-lg-flex mb-2">           
                                 <div class="col-sm-12">      
                                    <button class="btn btnPrevious blue small pull-left" type="button"><i class="fa fa-arrow-left"></i> Previous</button>
                                    <button class="btn btnNext blue small pull-right" type="button">Next<i class="fa fa-arrow-right"></i></button>
                                 </div>
                              </div>
                              <?php if (isset($default_fundraise)  && !empty($default_fundraise) ) {?>
                                 <?php $this->load->view('fundraisers/partials/defaultFundraiser', array("default_fundraiser" => $default_fundraise)); ?>
                              <?php }?>
                           </div>
                                   
                           <div role="tabpanel" class="tab-pane <?php echo ($tab == 2) ? 'active show' : ''; ?>" id="address-info">
                              <div class="row d-none d-lg-flex mb-2">           
                                 <div class="col-sm-12">      
                                    <button class="btn btnPrevious blue small pull-left" type="button"><i class="fa fa-arrow-left"></i> Previous</button>
                                    <button class="btn btnNext blue small pull-right" type="button">Next<i class="fa fa-arrow-right"></i></button>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-12">
                                    <?php $this->load->view("address/index", array("fname" => $userData->firstname, "lname" => $userData->lastname, "addresses" => $userData->addresses)); ?>  
                                 </div>
                              </div>
                           </div>
                           <div role="tabpanel" class="tab-pane <?php echo ($tab == 3) ? 'active show' : ''; ?>" id="accounts">
                              <div class="row d-none d-lg-flex mb-2">           
                                 <div class="col-sm-12">      
                                    <button class="btn btnPrevious blue small pull-left" type="button"><i class="fa fa-arrow-left"></i> Previous</button>
                                    <button class="btn btnNext blue small pull-right" type="button">Next<i class="fa fa-arrow-right"></i></button>
                                 </div>
                              </div>
                              <?php for ($i = 0; $i < count($linked_accounts); $i++) { ?>
                                 <h2>Linked Accounts</h2>
                                 <div class="card text-black">
                                    <div class="card-body">
                                       <div class="row">
                                          <div class="col-sm-3">
                                             <div class="linkaccount" style="background-image:url('<?php echo $linked_accounts[$i]->img; ?>')"></div>
                                          </div>
                                          <div class="col-sm-8">
                                             <div class="row">
                                                <div class="col-sm-8">
                                                   <p><strong><?php echo ucfirst(str_replace("_", " ", $linked_accounts[$i]->name)); ?></strong></p>
                                                </div>
                                                <div class="col-sm-4">
                                                   <?php if (!isset($linked_accounts[$i]->linked)) { ?>
                                                      <?php if (!isset($linked_accounts[$i]->code)) { ?>
                                                         <button class="btn link pull-right" data-gametype="<?php echo $linked_accounts[$i]->name; ?>">Link</button>
                                                      <?php } else { ?>
                                                         <button class="btn linkpending pull-right disabled">Pending..</button>
                                                      <?php } ?>
                                                   <?php } else { ?>
                                                      <button class="btn unlink pull-right" data-gametype="<?php echo $linked_accounts[$i]->name; ?>">Unlink</button>
                                                   <?php } ?>
                                                </div>
                                             </div>
                                             
                                             <p class="card-text">
                                                Join us at minecraft.winwinlabs.org
                                             </p>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              <?php } ?>
                           </div>
                           
                           <div role="tabpanel" class="tab-pane <?php echo ($tab == 4) ? 'active show' : ''; ?>" id="prefs">
                              <div class="row d-none d-lg-flex">           
                                 <div class="col-sm-12">      
                                 <button class="btn btnPrevious blue small pull-left" type="button"><i class="fa fa-arrow-left"></i> Previous</button>
                                 <button class="btn btnNext blue small pull-right" type="button">Next<i class="fa fa-arrow-right"></i></button>
                                 </div>
                              </div>
                              
                              <?php $this->load->view("home/preferences"); ?>
                           </div>
                        </div>
                     </div>
        </div>
    </div>
</div>
</section>
   <div id="divLoading"> </div>
</content>