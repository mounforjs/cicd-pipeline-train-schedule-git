<content class="content">
    <div class="container">
        <div class="row">
            <div class="col-12 p-4">
                <h1>Game Details</h1>
                
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col">
                                        <span class="titleReview">Game Name:</span> <?php echo $game->name; ?></br>
                                        <span class="titleReview">Quiz Name:</span> <?php echo $game->Quiz_name; ?></br>
                                    </div>
                                    <div class="col">
                                        <?php if ($game->prize_title !== '') { ?>
                                            <span class="titleReview">Prize:</span> <?php echo $game->prize_title; ?></br>
                                        <?php } ?>
                                        
                                        <span class="titleReview"><?php echo ($game->credit_type == 'prize') ? 'Prize Value: ' : 'Value: '; ?></span> 
                                            <?php echo '$' . round_to_2dc($game->value_of_the_game); ?>
                                    </div>
                                    <div class="col">
                                        <span class="titleReview">Rules:</span> 
                                        <?php
                                            if ($game->Quiz_rules == 1) {
                                                echo "Fastest 100% correct wins";
                                            }
                                            if ($game->Quiz_rules == 2) {
                                                echo "Most right answers wins ";
                                            }
                                            if ($game->Quiz_rules == 3) {
                                                echo "The fastest + most right answers wins";
                                            }
                                        ?></br>
                                        <span class="titleReview">No. of Winners:</span><span id="total_winners"> <?php echo $game->winner_count; ?></span></br>
                                    </div>
                                    <div class="col">
                                        <span class="titleReview">Total Views:</span> <?php echo game_visits_count($game->id); ?></br>

                                        <span class="titleReview">Total Players:</span> <?php echo game_playes_count($game->id); ?></br>

                                        <?php
                                        $total_game_visits = game_visits_count($game->id);
                                        $total_game_plays = game_playes_count($game->id);
                                        $game_ratio = $total_game_visits !== 0 ? round(($total_game_plays / $total_game_visits) * 100, 2) : 0;
                                        if (is_nan($game_ratio) || is_infinite($game_ratio)) {
                                            $game_ratio = 0;
                                        }
                                        ?>
                                        <span class="titleReview">Performance:</span> <?php echo  $game_ratio . ' %'; ?>
                                    </div>
                                    <div class="col">
                                        <caption><b>Financial Distribution</b></caption>
                                        <table class="demo">

                                            <thead>
                                                <tr>
                                                    <th>Fundraise Goal</th>
                                                    <th>Charity %</th>
                                                    <th>Creator %</th>
                                                    <th>WinWinLabs %</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><?php echo '$' . round_to_2dc($game->fundraise_value); ?></td>
                                                    <td><?php echo $game->beneficiary_percentage; ?></td>
                                                    <td><?php echo $game->creator_percentage; ?></td>
                                                    <td><?php echo $game->wwl_percentage; ?></td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php if ((isset($game->declarable) && $game->declarable) || (isset($game->declared) && $game->declared)) { ?>
                                    <div class="row">
                                        <div class="col text-center">
                                            <?php if (isset($game->declared) && $game->declared) { ?>
                                                <input type="button" class="btn mt-4 green disabled" value="Winners Declared" disabled>
                                            <?php } else { ?>
                                                <input type="button" class="btn mt-4 <?php echo ($totalSelectedUsers < $game->winner_count) ? "disabled" : ""; ?>" value="Declare Winners" id="winner-btn" <?php echo ($totalSelectedUsers < $game->winner_count) ? "disabled" : ""; ?>>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="row mt-3">
                                    <div class="col">
                                        <a class="text-nowrap hidden text-center selectedWinners mb-3" data-toggle="collapse" href="#selectedWinners" role="button" aria-expanded="<?php ($totalSelectedUsers > 0) ? "true" : ""; ?>" aria-controls="selectedUsersTable"><span>Selected Winners - </span><span id="selectedUsers" ><?php echo $totalSelectedUsers; ?></span>/<span><?php echo $game->winner_count; ?></span> <i class="fas fa-chevron-down"></i></a>
                                        <div class="collapse <?php ($totalSelectedUsers > 0) ? "show" : ""; ?>" id="selectedWinners">
                                            <input id="selectedUsersCount" type="hidden" value="<?php echo $totalSelectedUsers; ?>"/>
                                            <input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading1["filtered"]; ?>" data-total="<?php echo $deferLoading1["total"]; ?>" />
                                            <table id="reviewSelectedUsers" class="table table-striped table-bordered" data-type="reviewSelectedUsers">
                                                <thead>
                                                    <tr>
                                                        <th>Final Rank</th>
                                                        <th>User</th>
                                                        <th>Grade</th>
                                                        <th>Notes</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php foreach ($selectedUsers as $key => $user) { ?>
                                                        <tr>
                                                            <td><?php echo $user->final_rank; ?></td>
                                                            <td class="player_name">
                                                                <?php echo $user->username; ?>
                                                            </td>
                                                            <td><?php echo isset($user->grade) ? $user->grade : "N/A"; ?></td>
                                                            <td ><?php echo isset($user->notes) ? $user->notes : "N/A"; ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col">
                    <input id="userAttemptData" type="hidden" data-user="" data-game="" data-quiz="<?php echo $game->quiz_id; ?>" />
                    <input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading2["filtered"]; ?>" data-total="<?php echo $deferLoading2["total"]; ?>" />
                    <table id="reviewUsers" class="table table-striped table-bordered" data-type="reviewUsers" data-propagate="reviewSelectedUsers">
                        <thead>
                            <tr>
                                <th>Final Rank</th>
                                <th>System Rank</th>
                                <th>Username</th>
                                <th>Grade</th>
                                <th>Notes</th>
                                <th>Attempts</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($attempt_data as $key => $attempt) { ?>
                                <tr class='<?php echo ($attempt->reselected) ? "reselected" : ($attempt->final_rank ? "selected" : ""); ?>'>
                                    <td <?php echo ((isset($game->selectable) && $game->selectable) && !$attempt->reselected) ? "contentEditable='true' class='edit'" : ""; ?> id="final_rank-<?php echo $key; ?>" name="final_rank">
                                        <?php echo ($attempt->reselected) ? "RESELECTED" : $attempt->final_rank; ?>
                                    </td>

                                    <td> <?php echo $key + 1; ?></td>

                                    <td class="player_name" id="username-<?php echo $key; ?>" data-user_id='<?php echo $attempt->user_id; ?>'>
                                        <?php echo $attempt->username; ?>
                                    </td>

                                    <td>
                                        <?php if ((isset($game->selectable) && $game->selectable) && !$attempt->reselected) { ?>
                                            <select class="grade_value" id="grade-<?php echo $key; ?>" name="grade">
                                                <option <?php if (isset($attempt->grade) && $attempt->grade == 'A') echo 'selected' ?> value="A">A</option>
                                                <option <?php if (isset($attempt->grade) && $attempt->grade == 'B') echo 'selected' ?> value="B">B</option>
                                                <option <?php if (isset($attempt->grade) && $attempt->grade == 'C') echo 'selected' ?> value="C">C</option>
                                                <option <?php if (isset($attempt->grade) && $attempt->grade == 'D') echo 'selected' ?> value="D">D</option>
                                                <option <?php if (isset($attempt->grade) && $attempt->grade == 'E') echo 'selected' ?> value="E">E</option>
                                            </select>
                                        <?php } else { 
                                            echo isset($attempt->grade) ? $attempt->grade : "N/A";
                                        } ?>
                                    </td>

                                    <td <?php echo ((isset($game->selectable) && $game->selectable) && !$attempt->reselected) ? "contentEditable='true' class='edit'" : ""; ?> id="notes-<?php echo $key; ?>" name="notes">
                                        <?php echo (!isset($attempt->notes) && !$game->selectable) ? "N/A" : $attempt->notes; ?>
                                    </td>

                                    <td>
                                        <button class='btn btn-sm showReviewBtn' type="button" data-toggle="modal" data-target="#userAttemptModal" data-user="<?php echo $attempt->user_id; ?>" data-game="<?php echo $game->id; ?>" data-attempts="<?php echo $attempt->userTotalAttempts; ?>">View <?php echo $attempt->userTotalAttempts; ?> Attempts</button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="modal" id="userAttemptModal" tabindex="-1" role="dialog" aria-labelledby="userAttemptModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <button type="button" class="close p-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="userAttemptsLoader" class="loader rounded-0" style="display: none;"><div class="imageLoader"></div></div>
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="row">
                                <span class="titleReview mr-2">User: </span><span id="modalUser"></span>
                            </div>
                            <div class="row">
                                <span class="titleReview mr-2">Notes: </span><span id="modalNotes"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="row">
                                <span class="titleReview mr-2">Final Rank: </span><span id="modalFRank"></span>
                            </div>
                            <div class="row">
                                <span class="titleReview mr-2">System Rank: </span><span id="modalSRank"></span>
                            </div>
                            <div class="row">
                                <span class="titleReview mr-2">Grade: </span><span id="modalGrade"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <select name="userAttemptsFilter" id="userAttemptsFilter" name="attempts" class="form-control mb-2 ml-auto w-auto">
                                <option selected value="1">Attempt 1</option>
                            </select>
                        </div>
                    </div>

                    <input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading3["filtered"]; ?>" data-total="<?php echo $deferLoading3["total"]; ?>" />
                    <table id="reviewUserAttempts" class="table table-striped table-bordered" style="width:100%" data-type="reviewUserAttempts">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Question</th>
                                <th scope="col">Answer</th>
                                <th scope="col">Time</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#userAttemptModal').on('shown.bs.modal', function(e) {
            var table = $('#reviewUserAttempts').DataTable();
            table.columns.adjust();
        });

        $(document).on('click', '.showReviewBtn',  function(e) {
            $('#userAttemptModal').modal('show');

            $("#userAttemptsLoader").show();

            var table = $('#reviewUserAttempts').DataTable();
            table.columns.adjust();
            table.search('');

            $("#userAttemptData").data("user", e.target.getAttribute("data-user"));
            $("#userAttemptData").data("game", e.target.getAttribute("data-game"));

            $("#winner-btn").prop('disabled', false);

            var attempts = $(e.target).data("attempts");
            $('#userAttemptsFilter').empty();
            for (var i = 0; i < attempts; i++) {
                $('#userAttemptsFilter').append($('<option>', { 
                    value: i + 1,
                    text : "Attempt " + (i + 1) 
                }));
            }

            populateAttemptInfo(e.target);

            table.clearPipeline().draw();
            table.columns.adjust();
        });

        $("#userAttemptsFilter").on('change', function(e) {
            $("#userAttemptsLoader").show();

            var table = $('#reviewUserAttempts').DataTable();
            table.search('');
            table.clearPipeline().draw();
        });

        function populateAttemptInfo(target) {
            var table = $('#reviewUsers').DataTable();
            var row = $(target).closest("tr");

            var gradeSelect = $(row).find(".grade_value");

            var data = table.rows( row ).data()[0];
            data.grade = (gradeSelect.length > 0) ? gradeSelect.first().val() : "N/A";
            data.userTotalAttempts = target.getAttribute("data-attempts"); 
            data.system_rank = table.row( row ).index()+1;
            data.final_rank = (data.final_rank === null) ? "" : ((parseInt(data.reselected) == 1) ? "RESELECTED"  : data.final_rank);
            data.notes = (data.notes === null) ? "" : data.notes;
            data.username = (data.username === null) ? "" : data.username;
            
            $("#modalUser").text(data.username);
            $("#modalNotes").text(data.notes);
            $("#modalGrade").text(data.grade);
            $("#modalFRank").text(data.final_rank);
            $("#modalSRank").text(data.system_rank);
        }

        $("#selectedWinners").on("shown.bs.collapse", function() {
            var table = $('#reviewSelectedUsers').DataTable();
            table.columns.adjust();
        });

        <?php if (isset($game->selectable) && $game->selectable) { ?>
            var $oldValue;
            var $editId;
            const game_id = <?php echo $game->id; ?>;
            const winner_count = <?php echo $game->winner_count; ?>;

            // Add Class
            $(document).on('click', '.edit', function(e) {
                $(this).addClass('editModeReview');
                $oldValue = $(this).text().trim();
            });

            // Save data
            $(document).on('focusout', '.edit', function(e) {
                $(this).removeClass("editModeReview");
                var value = $(this).text().trim();
                if (value == $oldValue) {
                    return false;
                }

                var user = $(this).parent().find(".player_name:first");
                var user_id = $(user).data("user_id");
                var name = $(this).attr('name');
                $editId = '#' + $(this).attr('id');

                if (name == "final_rank" && isNaN(parseInt(value)) && value != "") {
                    showSweetAlert("Value must be a number!", "Whoops!", "error");
                    if ($editId !== '') $($editId).text($oldValue);
                } else {
                    value = (name != "final_rank" && value.length > 10) ? '"' + value.substring(0, 10) + '..."' : value;
                    var title = 'Confirm change?';
                    var text = name + '" of ' + user.text();
                    text = ((value === "") ? 'Remove "' + text + '.' : 'Change "' + text + ' to ' + value + '.');

                    updateUserReviewAlert(text, title, value, user_id, name, $editId)
                }
            })

            $(document).on('change','.grade_value',function(){
                var value = $(this).val();
                var id = $(this).attr('id');
                var name = $(this).attr("name");

                var user = $(this).closest("tr").find(".player_name:first");
                var user_id = $(user).data("user_id");
                var title = 'Confirm change?';
                var text =  'Change "' + name + '" of ' + user.text() + ' to "' + value + '".';

                updateUserReviewAlert(text, title, value, user_id, name)
            });

            $(document).on('keypress', '[contenteditable="true"]', function(e) {
                var target = $(e.currentTarget);
                var pattern = /^([a-zA-Z0-9 _-]+)$/;
                if (target.attr("id").includes("notes") && !pattern.test(e.key)) {
                    e.preventDefault();
                } else if (!target.attr("id").includes("notes") && (isNaN(String.fromCharCode(e.which)) || e.which == 48 || parseInt(target.text() + e.key) > <?php echo game_playes_count($game->id); ?>)) {
                    e.preventDefault();
                }
            });

            function updateUserReviewAlert(text, title, value, id, name, editId = '') {
                const max = winner_count;
                if (name == "final_rank" && ($("#selectedUsersCount").val() == max && (value !== "" && $oldValue == ""))) {
                    showSweetAlert('You\'ve already selected ' + max + ((max == 1) ? ' winner.' : ' winners.'), "Whoops!", 'error');
                    if (editId !== '') $($editId).text($oldValue);
                    return;
                }
                var editables = $(".edit");
                showSweetConfirm(text, title, "warning", function(confirmed) {
                    if (!confirmed) {
                        showSweetAlert('"' + name.charAt(0).toUpperCase() + name.slice(1) + '" has not been changed.', 'Cancelled', 'warning');
                        if (editId !== '') $($editId).text($oldValue);
                        return;
                    } else {
                        $.ajax({
                            method: "POST",
                            data: {
                                rValue: value,
                                rUserId: id,
                                rName: name,
                                rGameId: game_id
                            },
                            url: window.location.origin + '/games/updateUserAttempt',
                            beforeSend: function() {
                                $('#divLoading').addClass('show');
                                editables.each(function(index) {
                                    $(this).removeClass('edit');
                                    $(this).attr('contenteditable', false);
                                });
                            },
                            success: function(result) {
                                result = JSON.parse(result);

                                if (result && result.status != "failed") {
                                    showSweetAlert(name + ' was updated.', 'Great!', 'success');
                                    $('#reviewUsers').DataTable().clearPipeline().draw(false);
                                    $('#reviewSelectedUsers').DataTable().one("draw.dt", function() {
                                        if (name == "final_rank") {
                                            var currentTotal = $("#selectedUsersCount").val();
                                            if (currentTotal == max) {
                                                $("#winner-btn").removeClass("disabled");
                                                $("#winner-btn").prop("disabled", false);
                                            } else {
                                                $("#winner-btn").addClass("disabled");
                                                $("#winner-btn").prop("disabled", true);
                                            }
                                        }
                                    });
                                } else {
                                    showSweetAlert(name + ' could not be updated at this time.', 'Whoops!', 'error');
                                }
                            },
                            error: function() {
                                showSweetAlert('We ran into an error, try again later.', 'Whoops!', 'error');
                                if (editId !== '') $($editId).text($oldValue);

                                editables.each(function(index) {
                                    $(this).addClass('edit');
                                    $(this).attr('contenteditable', true);
                                });
                            },
                            complete: function() {
                                $('#divLoading').removeClass('show');
                            }
                        });
                    }
                });
            }

            $("#winner-btn").on("click", function() {
                var selectedTotal = $("#selectedUsersCount").val();
                if (selectedTotal != winner_count) {
                    if (selectedTotal < winner_count) {
                        showSweetAlert("You need to select all winners before declaring!", "Whoops!", "error");
                    } else {
                        showSweetAlert("Too many winners selected!", "Whoops!", "error");
                    }
                    
                    $("#winner-btn").addClass("disabled");
                    $("#winner-btn").prop("disabled", true);
                    return;
                }

                showSweetConfirm("You cannot change winners once declared.", "Are you sure?", "warning", function(confirmed) {
                    if (!confirmed) {
                        return;
                    } else {
                        // declare winner ajax call
                        $.ajax({
                            type: "POST",
                            data: {
                                game_id: game_id
                            },
                            url: "<?php echo asset_url('cron/declareReviewGameWinner'); ?>",
                            beforeSend: function() {
                                $('#divLoading').addClass('show');
                                $("#winner-btn").addClass("disabled");
                                $("#winner-btn").prop('disabled', true);
                            },
                            success: function(data) {
                                data = JSON.parse(data);
                                if (data.status == 'success') {
                                    showSweetAlert(data.msg, "Congratulations!", "success")
                                    $("#winner-btn").val('Winners Declared');
                                    $("#winner-btn").addClass('green');

                                    $(".edit").attr('contenteditable', false);
                                    $(".edit").removeClass('edit');
                                    $(".grade_value").prop('disabled', true);

                                    setTimeout(function() {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    showSweetAlert(data.msg, "Whoops!", "error")
                                }
                            },
                            error: function(e) {
                                showSweetAlert('We ran into an error, try again later.', 'Whoops!', 'error');
                                if (editId !== '') $($editId).text($oldValue);
                                $("#winner-btn").removeClass("disabled");
                                $("#winner-btn").prop('disabled', false);
                            },
                            complete: function(e) {
                                $('#divLoading').removeClass('show');
                            }
                        });
                    }
                });
            });
        <?php } ?>
    </script>
    <div id="divLoading"> </div>
</content>