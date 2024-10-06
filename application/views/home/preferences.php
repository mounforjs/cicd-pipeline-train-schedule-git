<br>
<div class="card text-black">
    <div class="card-body">
    <form id="notification-prefs" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-sm-8">
                <h3>Notification</h3>
            </div>
            <div class="col-sm-4">
                <button class="btn small orange pull-right submit disabled">Update</button>
                <button class="btn small red pull-right reset d-none disabled">Reset</button>
            </div>
        </div>

        <hr class="mt-1">
        <br>

        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-sm-8">
                        <h4>Beneficiary:</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->beneficiary_approved ?>" id="noti_beneficiary_approved" <?= ($noti_preferences->beneficiary_approved) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_beneficiary_approved">Beneficiary approved</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (getprofile()->creator_status == "Yes") { ?>
        <br>
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-sm-8">
                        <h4>Creator:</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->game_live ?>" id="noti_game_live" <?= ($noti_preferences->game_live) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_game_live">Game made live</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->game_complete ?>" id="noti_game_complete" <?= ($noti_preferences->game_complete) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_game_complete">Game made completed</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->winners_selected ?>" id="noti_winners_selected" <?= ($noti_preferences->winners_selected) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_winners_selected">Game winners selected</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->winners_select_more ?>" id="noti_winners_select_more" <?= ($noti_preferences->winners_select_more) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_winners_select_more">Select more game winners</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->winners_reselected ?>" id="noti_winners_reselected" <?= ($noti_preferences->winners_reselected) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_winners_reselected">Game winners reselected</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->winners_reselect_portion ?>" id="noti_winners_reselect_portion" <?= ($noti_preferences->winners_reselect_portion) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_winners_reselect_portion">Game winner reselection (portion)</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->winners_reselect_failed ?>" id="noti_winners_reselect_failed" <?= ($noti_preferences->winners_reselect_failed) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_winners_reselect_failed">Game winner reselection (failed)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->winner_claimed_prize ?>" id="noti_winner_claimed_prize" <?= ($noti_preferences->winner_claimed_prize) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_winner_claimed_prize">Winner claimed prize</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->process_prize_reminder ?>" id="noti_process_prize_reminder" <?= ($noti_preferences->process_prize_reminder) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_process_prize_reminder">Process prize reminder</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->process_prize_failed ?>" id="noti_process_prize_failed" <?= ($noti_preferences->process_prize_failed) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_process_prize_failed">Process prize (failed)</label>
                        </div>
                    </div>
                    
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->winner_acknowledge_prize ?>" id="noti_winner_acknowledge_prize" <?= ($noti_preferences->winner_acknowledge_prize) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_winner_acknowledge_prize">Winner acknowledged prize (received/not received)</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->winner_acknowledge_prize_failed ?>" id="noti_winner_acknowledge_prize_failed" <?= ($noti_preferences->winner_acknowledge_prize_failed) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_winner_acknowledge_prize_failed">Winner did not acknowledge prize</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <br>
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-sm-8">
                        <h4>Player:</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->game_winner ?>" id="noti_game_winner" <?= ($noti_preferences->game_winner) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_game_winner">Game winner</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->game_participant ?>" id="noti_game_participant" <?= ($noti_preferences->game_participant) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_game_participant">Game participant</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->claim_prize_reminder ?>" id="noti_claim_prize_reminder" <?= ($noti_preferences->claim_prize_reminder) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_claim_prize_reminder">Claim prize reminder</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->claim_prize_failed ?>" id="noti_claim_prize_failed" <?= ($noti_preferences->claim_prize_failed) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_claim_prize_failed">Claim prize failed (reselected)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->prize_acknowledge_reminder ?>" id="noti_prize_acknowledge_reminder" <?= ($noti_preferences->prize_acknowledge_reminder) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_prize_acknowledge_reminder">Acknowledge prize reminder (received/not received)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-sm-8">
                        <h4>Transactions:</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->credits_awarded ?>" id="noti_credits_awarded" <?= ($noti_preferences->credits_awarded) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_credits_awarded">Credits awarded</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $noti_preferences->tokens_awarded ?>" id="noti_tokens_awarded" <?= ($noti_preferences->tokens_awarded) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="noti_tokens_awarded">Tokens awarded</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>

<br>
<div class="card text-black">
    <div class="card-body">
    <form id="email-prefs" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-sm-8">
                <h3>Email</h3>
            </div>
            <div class="col-sm-4">
                <button class="btn small orange pull-right submit disabled">Update</button>
                <button class="btn small red pull-right reset d-none disabled">Reset</button>
            </div>
        </div>

        <hr class="mt-1">
        <br>

        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-sm-8">
                        <h4>Beneficiary:</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->beneficiary_created ?>" id="email_beneficiary_created" <?= ($email_preferences->beneficiary_created) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_beneficiary_created">Beneficiary created</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->beneficiary_approved ?>" id="email_beneficiary_approved" <?= ($email_preferences->beneficiary_approved) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_beneficiary_approved">Beneficiary approved</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->beneficiary_edit_request ?>" id="email_beneficiary_edit_request" <?= ($email_preferences->beneficiary_edit_request) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_beneficiary_edit_request">Beneficiary edit requested</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (getprofile()->creator_status == "Yes") { ?>
        <br>
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-sm-8">
                        <h4>Creator:</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->game_live ?>" id="email_game_live" <?= ($email_preferences->game_live) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_game_live">Game made live</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->game_complete ?>" id="email_game_complete" <?= ($email_preferences->game_complete) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_game_complete">Game made completed</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->winners_selected ?>" id="email_winners_selected" <?= ($email_preferences->winners_selected) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_winners_selected">Game winners selected</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->winners_select_more ?>" id="email_winners_select_more" <?= ($email_preferences->winners_select_more) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_winners_select_more">Select more game winners</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->winners_reselected ?>" id="email_winners_reselected" <?= ($email_preferences->winners_reselected) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_winners_reselected">Game winners reselected</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->winners_reselect_portion ?>" id="email_winners_reselect_portion" <?= ($email_preferences->winners_reselect_portion) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_winners_reselect_portion">Game winner reselection (portion)</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->winners_reselect_failed ?>" id="email_winners_reselect_failed" <?= ($email_preferences->winners_reselect_failed) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_winners_reselect_failed">Game winner reselection (failed)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->winner_claimed_prize ?>" id="email_winner_claimed_prize" <?= ($email_preferences->winner_claimed_prize) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_prize_claim_pending">Winner claimed prize</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->process_prize_reminder ?>" id="email_process_prize_reminder" <?= ($email_preferences->process_prize_reminder) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_process_prize_reminder">Process prize reminder</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->process_prize_failed ?>" id="email_process_prize_failed" <?= ($email_preferences->process_prize_failed) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_process_prize_failed">Process prize (failed)</label>
                        </div>
                    </div>
                    
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->winner_acknowledge_prize ?>" id="email_winner_acknowledge_prize" <?= ($email_preferences->winner_acknowledge_prize) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_winner_acknowledge_prize">Winner acknowledged prize (received/not received)</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->winner_acknowledge_prize_failed ?>" id="email_winner_acknowledge_prize_failed" <?= ($email_preferences->winner_acknowledge_prize_failed) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_winner_acknowledge_prize_failed">Winner did not acknowledge prize</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <br>
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-sm-8">
                        <h4>Player:</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->game_winner ?>" id="email_game_winner" <?= ($email_preferences->game_winner) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_game_winner">Game winner</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->game_participant ?>" id="email_game_participant" <?= ($email_preferences->game_participant) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_game_participant">Game participant</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->claim_prize_reminder ?>" id="email_claim_prize_reminder" <?= ($email_preferences->claim_prize_reminder) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_claim_prize_reminder">Claim prize reminder</label>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->claim_prize_failed ?>" id="email_claim_prize_failed" <?= ($email_preferences->claim_prize_failed) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_claim_prize_failed">Claim prize failed (reselected)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->prize_acknowledge_reminder ?>" id="email_prize_acknowledge_reminder" <?= ($email_preferences->prize_acknowledge_reminder) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_prize_acknowledge_reminder">Acknowledge prize reminder (received/not received)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-sm-8">
                        <h4>Transactions:</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $email_preferences->credits_awarded ?>" id="email_credits_awarded" <?= ($email_preferences->credits_awarded) ? "checked='checked'" : "" ?>>
                            <label class="form-check-label" for="email_credits_awarded">Credits awarded</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>

<script src="<?php echo asset_url('assets/js/preferences.js'); ?>"></script>