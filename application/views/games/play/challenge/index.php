<div class="challengequestions pt-2">
    <script src="<?php echo asset_url('assets/tinymce/tinymce.min.js'); ?>"></script>
    <script src="<?php echo asset_url('assets/js/tinycustom.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo asset_url('assets/css/tinymce.css'); ?>">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <script src="https://unpkg.com/axios@0.21.1/dist/axios.min.js"></script>

    <div>
        <form id="" action="" onsubmit="return false;">
            <div id="vue-challenge">
                <!-- One "tab" for each step in the form: -->
                <div class="tab">
                    <!-- transition -->
                    <transition :duration="{ enter: 500, leave: 300 }" enter-active-class="animated zoomIn" leave-active-class="animated zoomOut" mode="out-in" v-cloak v-if="quiz !== null">
                        <!--qusetionContainer-->
                        <div class="questionContainer" v-if="questionIndex<quiz.questions.length && !showScore" v-bind:key="questionIndex">
                            <header>
                                <!--progress-->
                                <div class="progressContainer">
                                    <progress class="progress is-info is-small" :value="(answer.length/quiz.questions.length)*100" max="100">{{((answer.length/quiz.questions.length)*100).toFixed(2)}}%</progress>
                                    <p>{{((answer.length/quiz.questions.length)*100).toFixed(2)}}% complete</p>
                                </div>
                                <!--/progress-->
                            </header>
                            <center>
                            <img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/shadediv.png" width="100%" alt="divider">
                            <center>
                            <!-- questionTitle -->

                            <?php if ($type == 'challenge') { ?>
                                <div id="vue-time-app" class="col-sm-12 mb-2 text-center <?php echo ($credit_type != 'free') ? "" : "d-none"; ?>">
                                    <span id="timer">
                                        <h8 class="text-warning pr-1">Q{{questionIndex + 1}}. Time</h8>
                                        <i class="fas fa-clock"></i> {{ time }}</span>
                                    <small>
                                        <div class="mytooltip">
                                            <i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title="" 
                                                aria-hidden="true" data-original-title=""></i>
                                            <span class="tooltiptext">
                                                This timer shows time taken for each question you attempt. The timer starts when you first view the question, 
                                                stops if you switch to another question, and resumes when you come back to the respective question.
                                            </span>
                                        </div>
                                    </small>
                                </div>
                            <?php } ?>

                            <h2 class="titleContainer title" v-html="`${quiz.questions[questionIndex].text}`"></h2>
                            <!-- quizOptions -->
                            <div class="optionContainer">
                                <div class="option choices" v-for="(response, index) in quiz.questions[questionIndex].responses" @click="selectOption(index, quiz.questions[questionIndex].id, response.text)" :class="{ 'is-selected': userResponses[questionIndex] == index, disabled: start_time == null}" :disabled="start_time == null" :key="index">
                                    {{ index | charIndex }}. {{ response.text }}
                                </div>
                            </div>
                            <div class="optionContainer" v-if="quiz.questions[questionIndex].type == 'one'">
                                <div class="option">
                                    <input type="text" @input="setTextAnswer($event, quiz.questions[questionIndex].id, questionIndex)" class="form-control" :class="{disabled: start_time == null}" :value="(typeof answer[this.getIndex( quiz.questions[questionIndex].id )] == 'object') ? answer[this.getIndex( quiz.questions[questionIndex].id )].value : ''"  :disabled="start_time == null"/>
                                </div>
                            </div>
                            <div class="optionContainer" v-if="quiz.questions[questionIndex].type == 'review'">
                                <div class="option">
                                    <textarea rows="5" @keyup="setTextAnswer($event, quiz.questions[questionIndex].id, questionIndex)" class="form-control" :class="{disabled: start_time == null}" placeholder="Text goes here!" :disabled="start_time == null">{{ (typeof answer[this.getIndex( quiz.questions[questionIndex].id )] == 'object') ? answer[this.getIndex( quiz.questions[questionIndex].id )].value : '' }}</textarea>
                                </div>
                            </div>
                            <!--quizFooter: navigation and progress-->
                            <footer class="questionFooter">
                                <center>
                                <img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/shadedivbottom.png" width="100%"><br><br>
                                <center>
                                <!--pagination-->
                                <nav class="pagination" role="navigation" aria-label="pagination">
                                    <!-- back button -->
                                    <a class="btn lightblue button" v-on:click="prev();" :class="{disabled: start_time == null}" :disabled="start_time == null || questionIndex < 1">Back</a>
                                    <!-- next button -->
                                    
                                    <a class="btn lightblue button" :class="{ 'is-active' : userResponses[questionIndex] != null, disabled: start_time == null}" v-on:click="next();" :disabled="start_time == null || questionIndex>=quiz.questions.length" v-if="quiz.questions.length-1 === answer.length && questionIndex < quiz.questions.length-1">
                                    Next
                                    </a>

                                    <a class="btn lightblue button" :class="{ 'is-active' : userResponses[questionIndex] != null, disabled: start_time == null}" v-on:click="next();" :disabled="start_time == null || questionIndex>=quiz.questions.length" v-if="quiz.questions.length-1 > answer.length && (questionIndex != quiz.questions.length - 1)">
                                    {{ (userResponses[questionIndex]==null)?'Skip': 'Next' }}
                                    </a>
                                    <a class="btn lightblue button is-active" v-on:click="submit();" v-if="quiz.questions.length == answer.length">Submit</a>
                                </nav>
                                <!--/pagination-->
                            </footer>
                            <!--/quizFooter-->
                        </div>
                        <!--/questionContainer-->
                        <!--quizCompletedResult-->
                        <div v-if="showScore" v-bind:key="questionIndex" class="quizCompleted has-text-centered">
                            <!-- quizCompletedIcon: Achievement Icon -->
                            <span class="icon">
                            <i class="fa" :class="showScore.score>70?'fa-check-circle-o is-active':'fa-times-circle'"></i>
                            </span>
                            <br>
                        </div>
                        <!--/quizCompetedResult-->
                    </transition>
                </div>
            
                <style scoped>
                    button.answered {
                        background: lightgreen;
                        color: green;
                        font-weight: bold;
                    }
                </style>
                <div v-if="quiz !== null && !showScore">
                    <ul id="questionpagenation">
                        <li>
                            <button class="button" @click="prevChunk" :disabled="currentIndex === 0">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </li>

                        <li v-for="(questionGroup, index) in paginatedQuestions" :key="index">
                            <button class="button" :class="(getIndex(questionGroup.value.id) == -1)?'':'is-active'" :class="
                                [(getIndex(questionGroup.value.id) > -1) ? 'answered' : '']"  @click="setNow(currentIndex+index);">
                                {{ currentIndex+index+1 }}
                            </button>
                        </li>
                        <li>
                            <button class="button" @click="nextChunk" :disabled="(questions.length - currentIndex) < pageSize">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </li>
                    </ul>
                    <p>Total Questions: {{ quiz.questions.length }}</p>
                </div>
            </div>
        </form>
    </div>

    <script src="<?php echo asset_url('assets/js/challenge/challenge.js'); ?>"></script>
    <script>
        function Game() {
            var game = new Challenge();

            this.ready = async () => {
                return new Promise((resolve, reject) => {
                    resolve(game.ready);
                });
            }

            this.start = async () => {
                return new Promise((resolve, reject) => {
                    game.start();
                    resolve(game.finished);
                });
            }

            this.end = () => {
                return game.end();
            }
        }
    </script>
</div>