<div id="questionModal" class="modal" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="load d-none"><div class="imageLoader"></div></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">New Question</h3> 
                <button type="button" data-dismiss="modal" class="close">×</button>
            </div> 

            <div class="modal-body">
                <form class="row" id="new_question" data-id="" name="quesForm" @change="setDirty()">
                    <div class="col-lg-4">
                        <label class="control-label">Category:</label>
                        <select id="trivia_category" name="trivia_category" class="form-control" v-model="category">
                            <?php foreach ($category as $cat) { ?>
                                <option value="<?php echo $cat->id; ?>" <?php if (isset($question) and $question->category == $cat->id) { echo 'selected';} ?>><?php echo $cat->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-4" style="margin-bottom: 15px">
                        <label class="control-label">Type:</label>
                        <select id="trivia_type" name="trivia_type" class="form-control" v-model="type" @change="typeChange()">
                            <option value="multiple">Multiple Choice</option>
                            <option value="boolean">True or False</option>
                            <option value="review">Review</option>
                            <option value="one">Enter One</option>
                        </select>
                    </div>

                    <div class="col-lg-4" style="margin-bottom: 15px">
                        <label class="control-label">Difficulty:</label>
                        <select id="trivia_difficulty" name="trivia_difficulty" class="form-control" v-model="difficulty">
                            <option value="easy" selected>Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>

                    <div class="col-lg-12" style="margin-bottom: 15px">
                        <label id="type_1title" class="control-label">Question Statement:</label>
                        <textarea id="trivia_question" name="trivia_question" class="form-control" placeholder="Type your question here" v-model="trivia_question"></textarea>
                    </div>

                    <div class="col-lg-12" v-show="answerBlock" style="margin-bottom: 15px">
                        <div id="type_1">
                            <label class="control-label" v-show="correctAnswerBlock">Correct Answer:</label>
                            <div style="margin-bottom: 15px" v-show="correctAnswerBlock">
                                <input id="correct_answer" name="correct_answer" v-model="correct_answer" type="text" placeholder="Comma Separated: Max of 2" value="" :required="correctAnswerBlock ? true : false">
                            </div>

                            <label class="control-label" v-show="inCorrectAnswerBlock">Incorrect Answers:</label>
                            <div style="margin-bottom: 15px" v-show="inCorrectAnswerBlock">
                                <input id="incorrect_answer" name="incorrect_answer" v-model="incorrect_answer" type="text" placeholder="Comma Separated: Max of 4" value="" :required="inCorrectAnswerBlock ? true : false">
                            </div>
                        </div>
                    </div>

                    <div id="type_2" v-show="booleanBlock" class="col-lg-12" style="margin-bottom: 15px">
                        <label class="control-label">Correct Answer:</label>
                        <div>
                            <label class="control-label">True</label>
                            <input id="booleanAnswer1" name="booleanAnswer" v-model="booleanAnswer" type="radio" value="1" :required="booleanBlock ? true : false" checked>

                            <label class="control-label">False</label>
                            <input id="booleanAnswer2" name="booleanAnswer" v-model="booleanAnswer" type="radio" value="0" :required="booleanBlock ? true : false">
                        </div>
                    </div>

                    
                        
                    <?php if (getprofile()->usertype != '2') { ?>
                        <div class="col-lg-12" v-if="edit">
                            <div class="row" v-if="status == 2">
                                <div class="col mt-3 text-right">
                                    <h3>Admin Remark: </h3>
                                    <p id="remark" v-model="remark"> {{ remark }} </p>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="col-lg-12">
                            <div id="type_3">
                                <div class="row">
                                    <div class="col text-right">
                                        <label>Approve</label>
                                        <input id="status1" name="status" v-model="status" type="radio" value="1">

                                        <label>Decline</label>
                                        <input id="status2" name="status" v-model="status" type="radio" value="2">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <textarea id="remark" name="remark" class="form-control" v-model="remark" placeholder="Enter remarks here."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>  
                </form>
                
                <div class="row mt-2">
                    <span id="clearQuiz" v-if="dirty" class="btn red" v-on:click="clearForm()">Clear</span>
                    <button class="btn btn-primary ml-auto" v-on:click="!edit ? addQuestion() : updateQuestion()">Submit</button>
                </div>
            </div>

        </div>
    </div> 
</div>