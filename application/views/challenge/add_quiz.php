<div class="modal" id="quizModal">
    <div class="modal-dialog modal-xl">
        <div class="load d-none"><div class="imageLoader"></div></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add Quiz</h3>
                <button type="button" class="close" data-dismiss="modal" ref="closeModal">&times;</button>
            </div>

            <div class="modal-body">
                <form id="new_quiz" data-id="" name="quizForm" @change="setDirty()">
                    <div id="newQuizConfig" class="row">
                        <div class="col-sm-3">
                            <label id="type_1title" class="control-label">Quiz Name</label>
                            <input id="question" name="name" v-model="name" class="form-control" required type="text" value="<?php echo @$quiz->name; ?>">
                        </div>

                        <div class="col-sm-3">
                            <label class="control-label">Category</label>
                            <select id="category" name="category" v-model="category" class="form-control" @change="setCategory($event)">
                                <?php foreach ($category as $cat) { ?>
                                <option value="<?php echo $cat->id; ?>"><?php echo $cat->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <label class="control-label">Difficulty</label>
                            <select id="difficulty" name="difficulty" v-model="difficulty" class="form-control" readonly disabled>
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-lg-9 mt-2">
                            <select id="selectFilter" name="selectFilter" class="form-control col-sm-2 ml-auto mb-2">
                                <option value="0" selected>All</option>
                                <option value="1">Yours</option>
                            </select>

                            <table class="table table-bordered" style="height: 100%; "id="selectQuestions" data-type="selectQuestions"  @click='toggleQuestion()'>
                                <thead>
                                    <tr>
                                        <th><b>ID</b></th>
                                        <th><b>Category</b></th>
                                        <th><b>Type</b></th>
                                        <th><b>Difficulty</b></th>
                                        <th><b>Question / Statement</b></th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-1 pr-2">
                                        <small v-if="addedQuestions.length > 0"> Length: {{ addedQuestions.length }} </small>
                                        <span id="clearQuestions" v-if="addedQuestions.length > 0" class="btn red xsmall pull-right" v-on:click="clearQuestions()">Clear</span>
                                    </div>
                                </div>
                            </div>

                            <div class="listOfQues">
                            <div v-if="addedQuestions.length === 0 && question_required === 1" style="font-size: 28px; color: red; background: white; padding-left: 10px;">Please add question.</div>
                                <div v-for="(aq, index) in addedQuestions" :key="index" class="selected_question">
                                    <div class="details_bar">
                                        <span class="mx-1"><b>{{index+1}}.</b></span>
                                        <input type="hidden" name="question[]" v-bind:value="aq.id"/>

                                        <a class="my-auto" v-if="index != 0" @click="orderQuestions(1)"><i class="fa fa-chevron-up fa-sm blue mx-0" aria-hidden="true"></i></a>
                                        <a class="my-auto" v-if="index != addedQuestions.length-1" @click="orderQuestions(0)"><i class="fa fa-chevron-down fa-sm blue mx-0" aria-hidden="true"></i></a>

                                        <a class="pull-right my-auto" @click="removeQuestion()"><i class="fa fa-times fa-sm red" aria-hidden="true"></i></a>
                                        <a class="pull-right my-auto" @click="toggleDetails()"><i class="fa fa-plus fa-sm green" aria-hidden="true"></i></a>
                                    </div>
                                    
                                    <div class="details">
                                        <div class="addedques">
                                            <strong><span v-html="aq.question"></span></strong>
                                        </div>

                                        <div class="quesdetails d-none">
                                            <small>
                                                <span><b>Difficulty:</b> {{ aq.difficulty.toUpperCase() }}</span><br>
                                                <div v-if="aq.type == 'multiple'">
                                                    <span><b>Type:</b> Multiple</span><br>
                                                    <span><b>Correct:</b> {{ JSON.parse(aq.correct_answer)[0] }}</span><br>
                                                    <span v-if="JSON.parse(aq.correct_answer)[1] !== undefined">, {{ JSON.parse(aq.correct_answer)[1] }}</span>
                                                    <span><b>Incorrect:</b> {{ JSON.parse(aq.incorrect_answer)[0] }}</span><br>
                                                    <span v-if="JSON.parse(aq.incorrect_answer)[1] !== undefined">, {{ JSON.parse(aq.incorrect_answer)[1] }}</span>
                                                    <span v-if="JSON.parse(aq.incorrect_answer)[2] !== undefined">, {{ JSON.parse(aq.incorrect_answer)[2] }}</span>
                                                </div>
                                                <div v-if="aq.type == 'boolean'">
                                                    <span><b>Type:</b> Boolean</span><br>
                                                    <span><b>Correct:</b> {{ (aq.boolean_answer == 1) ? 'True' : 'False' }}</span>
                                                </div>
                                                <div v-if="aq.type == 'one'">
                                                    <span><b>Type:</b> One</span><br>
                                                    <span><b>Correct:</b> {{ JSON.parse(aq.correct_answer)[0] }}</span>
                                                </div>
                                                <div v-if="aq.type == 'review'">
                                                    <span><b>Type:</b> Review</span>
                                                </div>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="mr-auto my-auto">
                            <span id="clearQuiz" v-if="dirty" class="btn red" v-on:click="clearForm()">Clear</span>
                            <span class="btn" @click="toggleRandom()">Randomize</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div id="type_3">
                                <?php if (getprofile()->usertype != '2') { ?>
                                    <div class="row">
                                        <div class="col mt-3 text-right" v-if="is_publish == 1 && status == 2">
                                            <h3>Admin Remark: </h3>
                                            <p id="remark" v-model="remark"> {{ remark }} </p>
                                        </div>
                                    </div>
                                <?php } else { ?>
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
                                <?php } ?>
                            </div>
                        </div> 
                    </div>

                    <hr>

                    <div class="row mt-2">
                        <div class="col">
                            <div class="row">
                                <div class="ml-auto">
                                    <label>
                                        <input id="booleanAnswer" v-model="is_publish" type="radio" value="0" checked="checked"> 
                                        Draft
                                    </label>
                                </div>

                                <div class="ml-2">
                                    <label>
                                        <input id="booleanAnswer2" v-model="is_publish" type="radio" value="1"> 
                                        Publish
                                    </label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="ml-auto">
                                    <span class="btn" v-on:click="!edit ? addQuiz() : updateQuiz()">Submit</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div id="randomizeQues" class="randomizeQues d-none">
                <div class="randomizeContent">
                    <div class="modal-header">
                        <h3 class="modal-title">Random Questions</h3> 
                        <button type="button" class="close" @click="toggleRandom()">×</button>
                    </div>
                                            
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <label class="control-label">Difficulty:</label>
                                <select id="randomdifficulty" name="randomdifficulty">
                                    <option value="easy">Easy</option>
                                    <option value="medium">Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                            <div class="col">
                                <label class="control-label">Type:</label>
                                <select id="randomtype" name="randomtype">
                                    <option value="multiple">Multiple Choice</option>
                                    <option value="boolean">True or False</option>
                                    <option value="review">Review</option>
                                    <option value="one">Enter One</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <label class="control-label">Category:</label>
                                <select id="randomcategory" name="randomcategory">
                                    <?php foreach ($category as $cat) { ?>
                                        <option value="<?php echo $cat->id; ?>"><?php echo $cat->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <label class="control-label">Created By:</label>
                                <select id="randomFilter" name="randomFilter" class="form-control" v-model="randomCreatedBy">
                                    <option value="0" selected>All</option>
                                    <option value="1">Yours</option>
                                </select>
                            </div>
                            <div class="col-sm-2 ml-auto">
                                <label class="control-label">Amount:</label>
                                <input v-model="randomCount" type="number" min="0" max="100" name="randomCount" id="randomCount" class="form-control" step="1" value="1"@change="setRandomCount($event)">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="ml-auto my-auto">
                                <span class="btn" @click="addRandomQuestions()">Add</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>