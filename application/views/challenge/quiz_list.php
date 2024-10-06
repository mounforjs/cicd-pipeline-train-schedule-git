<content class="content">
    <div class="container" id="quiz" @click='dynamicClick()'>
        <div class="col">
            <div class="row mb-3">
                <h2 class="my-auto"> Browse Quizzes </h2> 
                <?php if ($quizConfig["createGame"]) { ?>
                    <a target="_blank" class="btn btn-small ml-auto newQuiz" href="<?php echo asset_url("challenge/quiz"); ?>">New Quiz</a>
                <?php } else { ?>
                    <a data-toggle="modal" data-target="#quizModal" @click='newQuiz()' class="btn btn-small ml-auto newQuiz">New Quiz</a>
                <?php } ?>
            </div>
        </div>
        
        <?php if ($quizConfig["createGame"]) { ?>
            <input id="selectedQuiz" name="selectedQuiz" type="hidden" value="<?php echo $game->quiz_id; ?>"/>
        <?php } ?>

        <div class="row mb-1">
            <div class="col-sm-2 ml-auto">
                <select id="filter" name="filter" class="form-control mb-2 ml-auto">
                    <option value="0" selected>All</option>
                    <option value="1">Yours</option>
                </select>
            </div>
        </div>
            
        <input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>"/>
        <table class="table table-striped table-bordered responsive nowrap <?php echo (($quizConfig["quizName"]) == "quizzes" ? "" : "selectHover"); ?>" id="quizTable" data-type="<?php echo $quizConfig["quizName"]; ?>">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Name</th>
                    <th>Difficulty</th>
                    <th># of Questions</th>
                    <?php if (!$quizConfig["createGame"]) { ?>
                        <th>Status</th>
                    <?php } ?>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($quiz as $key => $value) { ?>
                    <tr class="select<?php echo ($value->id == @$quiz_id) ? " selected" : ""; ?>">
                        <td><?php echo $value->id; ?></td>
                        <td><?php echo $value->category_name; ?></td>
                        <td><?php echo ucwords($value->name); ?></td>
                        <td><?php echo ucwords($value->difficulty); ?></td>
                        <td><?php echo count(explode(",", $value->questions)); ?></td>
                        <?php if (!$quizConfig["createGame"]) { ?>
                            <td><?php echo (($value->is_publish == 0) ? "Drafted" : (($value->status == 0) ? 'Approval Requested' : (($value->status == 1) ? 'Approved' : 'Declined')));?></td>
                        <?php } ?>
                        <td>
                            <?php if (!$quizConfig["createGame"]) { 
                                    if ($value->editable) { ?>
                                        <a class="editQuiz" data-toggle="modal" data-target="#quizModal" data-id="<?php echo $value->id; ?>">Edit</a>
                                    <?php } ?>
                            <?php } else { ?>
                                <input type="radio" name="quiz" value="<?php echo $value->id; ?>" data-publish="<?php echo $value->is_publish; ?>"  data-approved="<?php echo $value->status; ?>" <?php if ($value->id == $game->quiz_id) echo 'checked=checked'; ?>/>
                            <?php } ?>

                            <a class="btn small pull-right viewQues" data-toggle="modal" data-target="#myQuizQuestionModal" data-id="<?php echo $value->id; ?>"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="modal" id="myQuizQuestionModal">
            <div class="modal-dialog modal-lg">
                <!-- <div class="load d-none"><div class="imageLoader"></div></div> -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Quiz Question(s)</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Question</th>
                                    <th>Category</th>
                                    <th>Difficulty</th>
                                    <th>Options</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(q,index) in quizQuestions" :key="q">
                                    <td>{{ ++index }}</td>
                                    <td v-html="q.question"></td>
                                    <td>{{ q.category_name }}</td>
                                    <td>{{ q.difficulty }}</td>
                                    <td>
                                        {{ q.correct_answer }}
                                        {{ q.incorrect_answer }}
                                        {{ q.boolean_answer }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$quizConfig["createGame"]) { $this->load->view("challenge/add_quiz"); } ?>
    </div>
    <br>
</content>

<script src="https://unpkg.com/axios@0.21.1/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>

<script>
    $(document).ready(function() {
        const config = {
            headers: {
                'Content-Type': 'application/json',
            }
        };

        var firstApp = new Vue({
            el: '#quiz',
            data: {
                quizQuestions: [],
                addedQuestions: <?php echo isset($questions) ? json_encode($questions) : json_encode([]) ?>,

                incorrectAnswerBlock: true,
                correctAnswerBlock: true,

                users: [],
                spinnerShow: null,
                quiz_id: null,

                name: "",
                category: 1,
                count: 0,
                difficulty: "easy",
                is_publish: 0,
                is_approved: 0,
                question_required: 0,

                status: 0,
                remark: "",
                
                edit: false,
                dirty: false,

                randomCount: 1,
                randomCategory: [1],
                randomType: ["multiple"],
                randomDifficulty: ["easy"],
                randomCreatedBy: 0
            },
            mounted() {
                var vue = this;

                $("content textarea").each(function(editor) {
                    var id = $(this).attr("id");
                    var content = $(this).text();

                    tinyMCEInitialize(id, content);
                });

                $("#randomcategory").selectize({
                    maxItems: 5,
                    onChange: function(value) {
                        vue.randomCategory = value;
                    }
                });

                $("#randomtype").selectize({
                    maxItems: 4,
                    onChange: function(value) {
                        vue.randomType = value;
                    }
                });

                $("#randomdifficulty").selectize({
                    maxItems: 3,
                    onChange: function(value) {
                        vue.randomDifficulty = value;
                    }
                });

                $("#filter").on("change", function() {
                    $("#quizTable").DataTable().clearPipeline().draw();
                });

                $("#selectFilter").on("change", function() {
                    $("#selectQuestions").DataTable().clearPipeline().draw();
                });

                $('#selectQuestions').on( 'draw.dt', function () {
                    var table = $("#selectQuestions").DataTable();

                    vue.toggleRowSelect(table);
                });

                $("#quizModal").on('shown.bs.modal', function() {
                    var table = $("#selectQuestions").DataTable();
                    table.columns.adjust(); 
                });

                $(window).resize(function(e) {
                    $("#quizTable").DataTable().columns.adjust();
                    $("#selectQuestions").DataTable().columns.adjust();
                });

                setInterval(function () {
                    $("#quizTable").DataTable().clearPipeline().draw(false);;
                }, 120000);
            },
            methods: {
                getQuizQuestions(q_id) {
                    this.quizQuestions = []
                    axios.get(location.origin + "/challenge/getQuizQuestions", { params: { quiz: q_id } }).then(resp => {
                        this.spinnerShow = null;
                        this.quizQuestions = resp.data;
                    }).catch(error => {
                        console.log(error);
                    });
                },
                getQuizData() {
                    var data = new FormData();

                    if (tinyMCE.get("remark") !== null) { 
                        this.remark = tinymce.get('remark').getContent();
                    };

                    data.append("id", $("#new_quiz").data("id"));
                    data.append("category", this.category);
                    data.append("difficulty", this.difficulty);
                    data.append("name", this.name);
                    data.append("questions", this.addedQuestions.map(value => {
                        return value.id
                    }));
                    data.append("is_publish", this.is_publish);
                    data.append("status", this.status);
                    data.append("remark", this.remark.replace(/<[^>]*>?/gm, ''));

                    return data;
                },
                addQuiz() {
                    this.question_required = 0;
                    if (this.addedQuestions.length == 0) {
                        this.question_required = 1;
                    }

                    var data = this.getQuizData();

                    if (!this.edit && this.name !== '' && this.question_required === 0) {
                        axios.post(location.origin + "/challenge/quiz/add/", data, config).then(resp => {
                            if (resp.data.status == "success") {
                                showSweetAlert("Your quiz was added!", "Success!", 'success');
                                $("#quizModal").modal("toggle");
                                this.resetForm();

                                $("#quizTable").DataTable().clearPipeline().draw(false);
                            } else {
                                showSweetAlert("We were unable to add your quiz!", "Uh oh!", 'error');
                            }
                        }).catch(error => {
                            console.log(error);
                            showSweetAlert("We were unable to add your quiz!", "Uh oh!", 'error');
                        });
                    }
                },
                updateQuiz() {
                    this.question_required = 0;
                    if (this.addedQuestions.length == 0) {
                        this.question_required = 1;
                    }

                    var data = this.getQuizData();

                    if (this.edit && this.question_required === 0 && this.name !== '') {
                        axios.post(location.origin + "/challenge/quiz/edit/", data, config).then(resp => {
                            if (resp.data.status == "success") {
                                this.edit = false;

                                showSweetAlert("Your quiz was updated!", "Success!", 'success');
                                $("#quizModal").modal("toggle");
                                this.resetForm();

                                $("#quizTable").DataTable().clearPipeline().draw(false);
                            } else {
                                showSweetAlert("We were unable to update your quiz!", "Uh oh!", 'error');
                            }
                        }).catch(error => {
                            console.log(error);
                            showSweetAlert("We were unable to update your quiz!", "Uh oh!", 'error');
                        });
                    }
                },
                clearQuestions() {
                    var vue = this;

                    showSweetConfirm("Are you sure you want to remove all questions?", "Attention", $icon = 'info', function(confirmed) {
                        if (!confirmed) {
                            e.preventDefault();
                        } else {
                            vue.resetQuestions();
                        }
                    });
                },
                clearForm() {
                    var vue = this;

                    showSweetConfirm("Are you sure you want to clear?", "Attention", $icon = 'info', function(confirmed) {
                        if (!confirmed) {
                            e.preventDefault();
                        } else {
                            vue.resetForm();
                        }
                    });
                },
                resetQuestions() {
                    this.toggleRowSelect($("#selectQuestions").DataTable(), false);
                    this.addedQuestions = (this.addQuestions !== undefined) ? this.addedQuestions.splice(0) : [];
                    this.difficulty = "easy";

                    this.setDirty();
                },
                resetForm() {
                    $('#new_quiz').trigger("reset");
                    this.resetRandom();
                    this.resetQuestions();

                    $('#quizModal #new_quiz').data("id", "");
                    this.name = "";
                    this.category = 1;
                    this.difficulty = "easy";
                    this.is_publish = 0;
                    this.is_approved = 0;
                    this.question_required = 0;
                    this.status = 0;
                    this.remark = "";

                    if (tinyMCE.get("remark") !== null) { 
                        tinymce.get('remark').setContent("");
                    };

                    this.dirty = false;
                },
                resetRandom() {
                    this.randomCount = 1;
                    this.randomCategory = [1];
                    this.randomType = ["multiple"];
                    this.randomDifficulty = ["easy"];
                    this.randomCreatedBy = 0;

                    $("#randomcategory")[0].selectize.clear();
                    $("#randomtype")[0].selectize.clear();
                    $("#randomdifficulty")[0].selectize.clear();

                    $("#randomcategory")[0].selectize.addItems(1);
                    $("#randomtype")[0].selectize.addItems("multiple");
                    $("#randomdifficulty")[0].selectize.addItems("easy");
                },
                setDirty() {
                    if (this.name != "" || this.category != 1 || this.difficulty != "easy" || this.is_publish != 0 || this.addedQuestions.length > 0) {
                        this.dirty = true;
                    } else {
                        this.dirty = false;
                    }
                },
                newQuiz() {
                    if (this.edit) {
                        this.resetForm();
                    }

                    this.edit = false;
                    $('#quizModal .modal-title').text("New Quiz");
                },
                editQuiz(row) {
                    this.toggleLoader(true);
                    this.resetForm();

                    this.edit = true;
                    this.dirty = true;

                    $('#quizModal .modal-title').text("Edit Quiz");

                    var table = $("#quizTable").DataTable();
                    var rowData = table.row(row).data();

                    axios.get((location.origin + '/challenge/getQuiz'), { params: { id: rowData.id } }).then(resp => {
                        var data = resp.data;
                        var questions = data.questions;
                        
                        $('#quizModal #new_quiz').data("id", data.id);
                        this.name = data.name;
                        this.category = data.category;
                        this.difficulty = data.difficulty;
                        this.is_publish = data.is_publish;
                        this.status = data.status;
                        this.remark = ((data.remark !== null) ? data.remark : "");

                        for (var i = 0; i < questions.length; i++) {
                            this.addedQuestions.push({
                                id: questions[i].id,
                                type: questions[i].type,
                                difficulty: questions[i].difficulty,
                                question: questions[i].question,
                                boolean_answer: questions[i].boolean_answer,
                                correct_answer: questions[i].correct_answer,
                                incorrect_answer: questions[i].incorrect_answer,
                            });
                        }

                        if (tinyMCE.get("remark") !== null) { 
                            tinymce.get('remark').setContent(this.remark);
                        };

                        this.toggleRowSelect($("#selectQuestions").DataTable());

                        this.toggleLoader(false);
                    }).catch(error => {
                        this.toggleLoader(false);
                    });
                },
                toggleRowSelect(table, toggle=true) {
                    var vue = this;
                    table.rows().every(function(index, tableLoop, rowLoop) {
                        var exist = vue.addedQuestions.map(function(e) { return e.id; }).indexOf(this.data().id);
                        if (exist > -1) {
                            if (toggle) {
                                this.nodes().to$().addClass("selected");
                            } else {
                                this.nodes().to$().removeClass("selected");
                            }
                        }
                    });
                },
                orderQuestions(direction) {
                    var target = $(event.target).parent().find("input");
                    var id = target.val();

                    var index = this.addedQuestions.map(function(e) { return e.id; }).indexOf(id);

                    var dest = index + 1;
                    if (direction) { //up
                        dest = index - 1;
                    }

                    this.addedQuestions.splice(dest, 0, this.addedQuestions.splice(index, 1)[0]);
                },
                toggleDetails() {
                    var target = $(event.target);
                    var icon = $(target).find("i");

                    var details = $(target).parent().parent().find(".quesdetails");
                    if (details.hasClass("d-none")) {
                        icon.removeClass("fa-plus");
                        icon.removeClass("green");
                        icon.addClass("fa-minus");
                        icon.addClass("orange");

                        details.removeClass("d-none");
                    } else {
                        icon.removeClass("fa-minus");
                        icon.removeClass("orange");
                        icon.addClass("fa-plus");
                        icon.addClass("green");

                        details.addClass("d-none");
                    }
                },
                removeQuestion() {
                    var target = $(event.target).parent().find("input");
                    var id = target.val();

                    $(("#ques" + id)).removeClass("selected");

                    var index = this.addedQuestions.map(function(e) { return e.id; }).indexOf(id);
                    this.addedQuestions.splice(index, 1);

                    this.setQuizDifficulty();
                    this.setDirty();
                },
                toggleQuestion() {
                    var target = $(event.target).closest("tr");
                    var table = $("#selectQuestions").DataTable();
                    var row = table.row(target);

                    var q = row.data();
                    if (q === undefined) { return; }
                    
                    var index = this.addedQuestions.map(function(e) { return e.id; }).indexOf(q.id);

                    if (target.hasClass("selected")) {
                        target.removeClass("selected");

                        this.addedQuestions.splice(index, 1);
                    } else {
                        this.addedQuestions.push({
                            id: q.id,
                            type: q.type,
                            difficulty: q.difficulty,
                            question: q.question,
                            boolean_answer: q.boolean_answer,
                            correct_answer: q.correct_answer,
                            incorrect_answer: q.incorrect_answer,
                        });

                        target.addClass("selected");
                    }

                    this.setQuizDifficulty();
                    this.setDirty();
                },
                setQuizDifficulty() {
                    var easy = 0; var medium = 0; var hard = 0;
                    var count = 0;

                    this.addedQuestions.map(function(value, key) {
                        count++;
                        if (value.difficulty == 'easy') {
                            easy++;
                        } else if (value.difficulty == 'medium') {
                            medium++;
                        } else if (value.difficulty == 'hard') {
                            hard++;
                        }
                    });

                    var total = (3 * easy) + (6 * medium) + (10 * hard);
                    var weight = total / count;

                    if (weight > 7) {
                        this.difficulty = 'hard'
                    } else if (weight > 3 && weight <= 7) {
                        this.difficulty = 'medium'
                    } else {
                        this.difficulty = 'easy'
                    }
                },
                setCategory(e) {
                    var id = e.target.value;
                    var name = e.target.options[e.target.options.selectedIndex].text;
                    this.selected_category_text = name;
                },
                setRandomCount(e) {
                    this.randomCount = e.target.value;
                },
                getRandomQuestionData() {
                    var added = this.addedQuestions.map(function(item) {
                        return item.id;
                    });

                    var data = {
                        added: added,
                        difficulty: this.randomDifficulty,
                        type: this.randomType,
                        category: this.randomCategory,
                        count: this.randomCount,
                        userfilter: this.randomCreatedBy
                    };

                    return data;
                },
                toggleRandom() {
                    var random = $("#randomizeQues");

                    if (random.is(":visible")) {
                        random.addClass("d-none")
                    } else {
                        random.removeClass("d-none")
                    }
                },
                addRandomQuestions() {
                    if (this.randomCount < 0 || this.randomCount > 100) {
                        showSweetAlert("You must select a number between 1-100!", "Uh oh!", 'error');
                        return;
                    }

                    var data = this.getRandomQuestionData();

                    $("#randomizeQues").addClass("d-none");
                    this.toggleLoader(true);
                    axios.get(location.origin + "/challenge/getRandomQuestions/", { params: data }).then(resp => {
                        var data = resp.data;

                        if (data.length > 0) {
                            for(var i = 0; i < data.length; i++) {
                                var q = data[i];

                                this.addedQuestions.push({
                                    id: q.id,
                                    type: q.type,
                                    difficulty: q.difficulty,
                                    question: q.question,
                                    boolean_answer: q.boolean_answer,
                                    correct_answer: q.correct_answer,
                                    incorrect_answer: q.incorrect_answer,
                                });
                            }
                            
                            this.toggleRowSelect($("#selectQuestions").DataTable(), true);

                            this.setDirty();
                            this.setQuizDifficulty();

                            showSweetAlert("Your questions were added!", "Success!", 'success');
                        } else {
                            showSweetAlert("We couldn't get any questions with these parameters!", "Uh oh!", 'error');
                        }

                        this.toggleLoader(false);
                    }).catch(error => {
                        this.toggleLoader(false);
                        showSweetAlert("We were unable to add your questions!", "Uh oh!", 'error');
                    });
                },
                dynamicClick() {
                    var target = $(event.target).closest("a");
                    
                    if (target.hasClass('editQuiz')) {
                        this.editQuiz($(event.target).closest("tr"));
                    } else if (target.hasClass('viewQues')) {
                        var id = $(target).data("id");
                        this.getQuizQuestions(id);
                    }
                },
                toggleLoader(b) {
                    if (b) {
                        $('#quizModal .load').removeClass("d-none");
                        $('#quizModal .modal-content').css("pointer-events", "none");
                    } else {
                        $('#quizModal .load').addClass("d-none");
                        $('#quizModal .modal-content').css("pointer-events", "auto");
                    }
                }
            }
        });
    });
</script>