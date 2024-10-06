<content class="content">
    <div id="question" class="container" @click='dynamicClick()'>
        <div class="col">
		    <div class="row mb-3">
                <h2> Browse Questions </h2>
                <a data-toggle="modal" data-target="#questionModal" @click='newQuestion()' class="btn btn-small ml-auto addQues">Add New</a>
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-sm-2 ml-auto">
                <select id="filter" name="filter" class="form-control mb-2 ml-auto">
                    <option value="0" selected>All</option>
                    <option value="1">Yours</option>
                </select>
            </div>
        </div>

        <input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>"/>
        <table class="table table-bordered" id="questionTable" data-type="questions">
            <thead>
                <tr>
                    <th><b>ID</b> <a href="?sort=asc"><i class="fa fa-sort"></i></a></th>
                    <th><b>Category</b></th>
                    <th><b>Type</b></th>
                    <th><b>Difficulty</b></th>
                    <th><b>Question / Statement</b></th>
                    <th><b>Status</b></th>
                </tr>
            </thead>
            
            <tbody>
                <?php foreach ($question as $key => $value) { ?>
                <tr>
                    <td><?php echo $value->id;?></td>
                    <td><?php echo $value->category_name;?></td>
                    <td><?php echo $value->type;?></td>
                    <td><?php echo $value->difficulty;?></td>
                    <td><?php echo $value->question;?></td>
                    <td>
                        <?php if (getprofile()->usertype == '2' || $value->editable) { ?>
                            <a class="editQues" @click="editQuestion()" data-toggle="modal" data-target="#questionModal" data-id="<?php echo $value->id;?>">Edit</a> |
                        <?php } ?>
                        <span> <?php echo (($value->status == 0) ? "Approval Requested" : (($value->status == 1) ? 'Approved' : 'Declined'));?></span>
                    </td>
                </tr>
                <?php }?>
            </tbody>
        </table>

        <?php $this->load->view("challenge/add_question"); ?>
    </div>
    <br>
</content>

<script src="https://unpkg.com/axios@0.21.1/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>

<script>
    $(document).ready(function() {
        var config = {
            headers: {
                'Content-Type': 'application/json',
            }
        };

        var test = new Vue({
            el: '#question',
            data: {
                category: 1,
                type: 'multiple',
                difficulty: 'easy',
                trivia_question: "",
                correct_answer: "",
                incorrect_answer: "",
                booleanAnswer: '',

                status: 0,
                remark: "",
                remarkBlock: false,

                answerBlock: true,
                booleanBlock: false,
                inCorrectAnswerBlock: true,
                correctAnswerBlock: true,
                users: [],
                spinnerShow: null,

                edit: false,
                dirty: false
            },
            mounted() {
                var vue = this;

                vue.typeChange();
                
                $("content textarea").each(function(editor) {
                    var id = $(this).attr("id");
                    var content = $(this).text();

                    tinyMCEInitialize(id, content);
                });

                $("#correct_answer").selectize({
                    maxItems: 2,
                    delimiter: ",",
                    create: function (input) {
                        return {
                            value: input,
                            text: input,
                        };
                    },
                    onChange: function(value) {
                        vue.correct_answer = value;
                        vue.setDirty();
                    }
                });

                $("#incorrect_answer").selectize({
                    maxItems: 4,
                    delimiter: ",",
                    create: function (input) {
                        return {
                            value: input,
                            text: input,
                        };
                    },
                    onChange: function(value) {
                        vue.incorrect_answer = value;
                        vue.setDirty();
                    }
                });

                $("#filter").on("change", function() {
                    $("#questionTable").DataTable().clearPipeline().draw();
                });

                $(window).resize(function(e) {
                    $("#questionTable").DataTable().columns.adjust();
                });

                setInterval(function () {
                    $("#questionTable").DataTable().clearPipeline().draw(false);
                }, 120000);
            },
            methods: {
                dynamicClick() {
                    var target = $(event.target).closest("a");

                    if (target.hasClass('editQues')) {
                        this.editQuestion($(event.target).closest("tr"));
                    }
                },
                typeChange() {
                    var correctSelectize = $("#correct_answer")[0].selectize;

                    if (this.type == 'multiple') {
                        this.answerBlock = true
                        this.correctAnswerBlock = true
                        this.inCorrectAnswerBlock = true
                        this.booleanBlock = false

                        if (correctSelectize !== undefined) {
                            correctSelectize.settings.maxItems = 2;
                            for (var i = correctSelectize.items.length; i >= correctSelectize.settings.maxItems; i--) {
                                correctSelectize.removeItem(correctSelectize.items[i]);
                            }
                        }

                        $("#correct_answer").attr("placeholder", "Comma Separated: Max of 2");
                        $("#correct_answer").next().find("input").attr("placeholder", "Comma Separated: Max of 2");
                    } else if (this.type == 'boolean') {
                        this.answerBlock = false
                        this.correctAnswerBlock = false
                        this.inCorrectAnswerBlock = false
                        this.booleanBlock = true
                    } else if (this.type == 'review') {
                        this.answerBlock = false
                        this.correctAnswerBlock = false
                        this.inCorrectAnswerBlock = false
                        this.booleanBlock = false
                    } else if (this.type == 'one') {
                        this.answerBlock = true
                        this.correctAnswerBlock = true
                        this.inCorrectAnswerBlock = false
                        this.booleanBlock = false

                        if (correctSelectize !== undefined) {
                            correctSelectize.settings.maxItems = 1;
                            for (var i = correctSelectize.items.length; i >= correctSelectize.settings.maxItems; i--) {
                                correctSelectize.removeItem(correctSelectize.items[i]);
                            }
                        }

                        $("#correct_answer").attr("placeholder", "Comma Separated: Max of 1");
                        $("#correct_answer").next().find("input").attr("placeholder", "Comma Separated: Max of 1");
                    }
                },
                setDirty() {
                    if (this.trivia_question != "" || this.category != 1 || this.difficulty != "easy" || this.type != "multiple" || this.correct_answer != "" || this.incorrect_answer != "") {
                        this.dirty = true;
                    } else {
                        this.dirty = false;
                    }
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
                resetForm() {
                    $('#new_question').trigger("reset");

                    $("#new_question").data("id", "")
                    this.category = 1;
                    this.type = "multiple";
                    this.difficulty = "easy";
                    this.trivia_question = "";

                    this.correct_answer = "";
                    this.incorrect_answer = "";
                    $("#correct_answer")[0].selectize.clear();
                    $("#correct_answer")[0].selectize.clearOptions();
                    $("#incorrect_answer")[0].selectize.clear();
                    $("#incorrect_answer")[0].selectize.clearOptions();

                    this.booleanAnswer = "";
                    this.status = 0;
                    this.remark = "";

                    this.setDirty()
                    this.typeChange();
                },
                newQuestion() {
                    this.edit = false;

                    $('#questionModal .modal-title').text("New Question");
                    this.resetForm();
                },
                editQuestion(row) {
                    this.edit = true;
                    this.toggleLoader(true);

                    $('#questionModal .modal-title').text("Edit Question");
                    this.resetForm();

                    this.edit = true;
                    this.dirty = true;
                    
                    var table = $("#questionTable").DataTable();
                    var rowData = table.row(row).data();

                    var question = tinyMCE.get("trivia_question");
                    var remark = tinyMCE.get("remark");

                    axios.get((location.origin + '/challenge/getQuestion'), { params: { id: rowData.id } }).then(resp => {
                        var data = resp.data;

                        $('#questionModal #new_question').data("id", data.id);
                        this.category = data.category;
                        this.type = data.type;
                        this.difficulty = data.difficulty;
                        this.status = data.status;

                        this.typeChange();

                        if (question !== null) {
                            this.trivia_question = (data.question != null) ? data.question : "";
                            question.setContent(this.trivia_question);
                        }
                        
                        if (remark !== null) { 
                            this.remark = (data.remark != null) ? data.remark.replace(/<[^>]*>?/gm, '') : "";
                            remark.setContent(this.remark) 
                        };
                        
                        var correct = (data.correct_answer !== null) ? data.correct_answer.replace(/[^0-9a-zA-Z., ]/g,'') : "";
                        var incorrect = (data.incorrect_answer !== null) ? data.incorrect_answer.replace(/[^0-9a-zA-Z., ]/g,'') : "";

                        if (this.type == 'multiple') {
                            this.correct_answer = correct;
                            this.incorrect_answer = incorrect;

                            this.setSelectize();
                        } else if (this.type == 'boolean') {
                            if (data.booleanAnswer == null) {
                                if (correct == "True") {
                                    this.booleanAnswer = 1;
                                } else {
                                    this.booleanAnswer = 0;
                                }

                                $('input[name="booleanAnswer"][value="' + this.booleanAnswer + '"]').prop("checked", true);
                            } else {
                                this.booleanAnswer = data.booleanAnswer;
                                $('input[name="booleanAnswer"][value="' + data.booleanAnswer + '"]').prop("checked", true);
                            }
                        } else if (this.type == 'one') {
                            this.correct_answer = correct;

                            this.setSelectize();
                        }

                        $('#questionModal input[name="status"][value="' + data.status + '"]').prop("checked", true);

                        if (this.status == 2) {
                            this.adminRemark = true;
                        } else {
                            this.adminRemark = false;
                        }

                        this.toggleLoader(false);
                    }).catch(error => {
                        this.toggleLoader(false);
                    });
                },
                setSelectize() {
                    if (this.correct_answer != null) {
                        var corr = this.correct_answer.split(",");
                        var correctSelectize = $("#correct_answer")[0].selectize;
                        if (correctSelectize !== undefined) {
                            for (var i = 0; i < corr.length; i++) {
                                correctSelectize.addOption({value: corr[i], text: corr[i]});
                                correctSelectize.addItems(corr[i]);
                            }
                        }
                    }

                    if (this.incorrect_answer != null) {
                        var incorr = this.incorrect_answer.split(",");
                        var inCorrectSelectize = $("#incorrect_answer")[0].selectize;
                        if (inCorrectSelectize !== undefined) {
                            for (var i = 0; i < incorr.length; i++) {
                                inCorrectSelectize.addOption({value: incorr[i], text: incorr[i]});
                                inCorrectSelectize.addItems(incorr[i]);
                            }
                        }
                    }
                },
                async getQuestionData() {
                    var vue = this;

                    var data = new FormData();
                    await tinymce.activeEditor.uploadImages(function (success) {
                        vue.trivia_question = tinymce.get('trivia_question').getContent();

                        var remark = tinymce.get("remark");
                        if (remark !== null) { 
                            vue.remark = tinymce.get('remark').getContent();
                        };

                        data.append("id", $("#new_question").data("id"));
                        data.append("category", vue.category);
                        data.append("type", vue.type);
                        data.append("difficulty", vue.difficulty);
                        data.append("question", vue.trivia_question);
                        data.append("correct_answer", vue.correct_answer);
                        data.append("incorrect_answer", vue.incorrect_answer);
                        data.append("boolean_answer", vue.booleanAnswer);
                        data.append("status", vue.status);
                        data.append("remark", vue.remark.replace(/<[^>]*>?/gm, ''));
                    });

                    return data;
                },
                async addQuestion() {
                    var data = await this.getQuestionData();

                    if (!this.edit && this.trivia_question !== '') {
                        if (this.type == "multiple" && this.correct_answer === "" && this.incorrect_answer === "") {
                            return;
                        } else if (this.type == "one" && this.correct_answer === "") {
                            return;
                        }

                        axios.post((location.origin + "/challenge/question/add/"), data, config).then(resp => {
                            if (resp.data.status == "success") {
                                showSweetAlert("Your question was added!", "Success!", 'success');
                                $("#questionModal").modal("toggle");
                                this.resetForm();

                                $("#questionTable").DataTable().clearPipeline().draw(false);
                            } else {
                                showSweetAlert("We were unable to add your question!", "Uh oh!", 'error');
                            }
                        }).catch(error => {
                            showSweetAlert("We were unable to add your question!", "Uh oh!", 'error');
                        });
                    }
                },
                async updateQuestion() {
                    var data = await this.getQuestionData();

                    if (this.edit && this.trivia_question !== '') {
                        if (this.type == "multiple" && this.correct_answer === "" && this.incorrect_answer === "") {
                            return;
                        } else if (this.type == "one" && this.correct_answer === "") {
                            return;
                        }

                        axios.post(location.origin + "/challenge/question/edit/", data, config).then(resp => {
                            if (resp.data.status == "success") {
                                this.edit = false;

                                showSweetAlert("Your question was updated!", "Success!", 'success');
                                $("#questionModal").modal("toggle");
                                this.resetForm();

                                $("#questionTable").DataTable().clearPipeline().draw(false);
                            } else {
                                showSweetAlert("We were unable to update your question!", "Uh oh!", 'error');
                            }
                        }).catch(error => {
                            showSweetAlert("We were unable to update your question!", "Uh oh!", 'error');
                        });
                    }
                },
                toggleLoader(b) {
                    if (b) {
                        $('#questionModal .load').removeClass("d-none");
                        $('#questionModal .modal-content').css("pointer-events", "none");
                    } else {
                        $('#questionModal .load').addClass("d-none");
                        $('#questionModal .modal-content').css("pointer-events", "auto");
                    }
                }
            }
        });
    });
</script>
