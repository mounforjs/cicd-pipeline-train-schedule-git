function Challenge() {
    this.ready = app.ready;
    this.finished = app.finished;

    this.start = app.start;
    this.end = app.end;
    this.get_score = app.get_score;
}

document.addEventListener('DOMContentLoaded', function() { 
    promiseReadyResolve = null; promiseReadyReject = null;
    promiseEndResolve = null; promiseEndReject = null;

    app = new Vue({
        el: "#vue-challenge",
        data: {
            game_session_id: document.querySelector("input[name='game_session_id']").value,
            time: "00:00",

            ready : new Promise((resolve, reject) => {
                promiseReadyResolve = resolve;
                promiseReadyReject = reject;
            }),

            finished : new Promise((resolve, reject) => {
                promiseEndResolve = resolve;
                promiseEndReject = reject;
            }),

            start_time : null,
            end_time: null,
            total_time: null,

            quiz: { questions : []},
            questionIndex: 0,
            answer: [],
            interval: [],
            userResponses: [],
            showScore: false,

            nextPreviousPagination:3,
            onLoadPagination:8,
            counter: 1,

            questions: [],
            questionChunks: [],
            pageSize: 8,
            currentIndex: 0,
            selectedQuestion: null,
        },
        filters: {
            charIndex: function(i) {
                return String.fromCharCode(97 + i);
            }
        },
        created() {
            axios.get(window.location.origin + "/challenge/getQuizQuestionForPlaying?game_session_id=" + this.game_session_id)
            .then(resp => {
                this.spinnerShow = null;
                this.quiz = resp.data

                this.toggle_input(false);

                for (let i = 0; i < this.quiz.questions.length; i++) {
                    Vue.set(this.quiz.questions[i], 'time', 0);
                }

                Object.freeze(this.quiz.questions);
                Object.freeze(this.quiz);
            }).then(() => {
                promiseReadyResolve();
            }).catch(error => {
                promiseReadyReject();
            });
        },

        watch: {
            currentIndex() {
              this.paginatedQuestions
            }
        },

        mounted() {
            this.questionChunks = this.chunkArray(this.quiz.questions, this.pageSize);
            this.selectedQuestion = this.questionChunks[0][0];
          },

        computed: {
            paginatedQuestions() {
                const start = this.currentIndex;
                const end = start + this.pageSize;
                if (start > 0) {
                  return this.quiz.questions.slice(start, end).map((element, index) => {
                    return { index: index + start, value: element };
                  });
                } else {
                  return this.quiz.questions.slice(start, end).map((element, index) => {
                    return { index: index, value: element };
                  });
                }
              }
        },
          
        methods: {
            format_time(time) {
                time = Number(time);
                var h = Math.floor(time / 360000);
                var m = Math.floor(time / 60000);
                var s = Math.floor(time / 1000) - (m * 60);

                if (h > 0) {
                    this.time = h + ":" + ((m < 10) ? "0" + m : m); + ":" + ((s < 10) ? "0" + s : s);
                } else {
                    this.time = ((m < 10) ? "0" + m : m) + ":" + ((s < 10) ? "0" + s : s);
                }
            },

            chunkArray(arr, chunkSize) {
                let index = 0;
                const chunkedArray = [];
                while (index < arr.length) {
                  chunkedArray.push(arr.slice(index, index + chunkSize));
                  index += chunkSize;
                }
                return chunkedArray;
            },

            showQuestions(index) {
                this.selectedQuestion = this.quiz.questions[index];
            },

            prevChunk() {
                if (this.currentIndex > 0) {
                    this.currentIndex -= this.pageSize;
                }
            },

            nextChunk() {
                if (this.currentIndex < this.quiz.questions.length && (this.quiz.questions.length - this.currentIndex) >= this.pageSize) {
                    this.currentIndex += this.pageSize;
                }
            },

            setNow(index) {
                    clearInterval(this.interval[this.questionIndex])
                    this.questionIndex = index
                    this.startTime(index);
            },

            getIndex(id) {
                return this.answer.findIndex(x => x.id === parseInt(id));
            },
            
            setTextAnswer(event, id, index) {
                Vue.set(this.userResponses, this.questionIndex, index);
                if (this.getIndex(id) > -1) {
                    this.answer[this.getIndex(id)].value = event.target.value
                } else {
                    this.answer.push({
                        "id": parseInt(id),
                        "value": event.target.value
                    })
                }
            },
            selectOption: function(index, id, option) {
                Vue.set(this.userResponses, this.questionIndex, index);
                if (this.getIndex(id) > -1) {
                    this.answer[this.getIndex(id)].value = option
                } else {
                    this.answer.push({
                        "id": parseInt(id),
                        "value": option
                    })
                }
            },
            next: function() {
                var id = this.quiz.questions[this.questionIndex].id
                if (this.getIndex(id) > -1) {
                    this.answer[this.getIndex(id)].time = this.quiz.questions[this.questionIndex].time
                }

                if (this.questionIndex < this.quiz.questions.length) {
                    this.questionIndex++;

                    clearInterval(this.interval[this.questionIndex - 1])

                    this.startTime(this.questionIndex)
                }
            },
            prev: function() {
                if (this.quiz.questions.length > 0) {
                    clearInterval(this.interval[this.questionIndex])
                    this.questionIndex--
                    this.startTime(this.questionIndex)
                }
            },
            start() {
                this.start_time = Date.now()
                this.startTime(0);
                this.toggle_input(true);

                this.userResponses = Array(this.quiz.questions.length).fill(null);
            },
            submit() {
                this.end_time = Date.now();
                this.calculate_time();

                promiseEndResolve();
            },
            toggle_input(toggle) {
                const tags = ["a", "div.option", "input", "button", "textarea", "select"];
                tags.forEach(elem => {
                    const nodes = document.querySelector("#game-wrapper").querySelectorAll(elem);
                    for (let i = 0; i < nodes.length; i++) {
                        if (!toggle) {
                            nodes[i].classList.add("disabled");
                            nodes[i].disabled = true;
                        } else {
                            nodes[i].classList.remove("disabled");
                            nodes[i].disabled = false;
                        }
                        
                    }
                });
            },
            end() {
                this.toggle_input(false);

                return { elapsed : this.end_time - this.start_time, answer : JSON.stringify(this.answer) };
            },
            calculate_time() {
                for (let i = 0; i < this.quiz.questions.length; i++) {
                    if (this.answer[this.getIndex(this.quiz.questions[i].id)] !== undefined) {
                        this.answer[this.getIndex(this.quiz.questions[i].id)].time = this.quiz.questions[i].time;
                    }
                }

                clearInterval(this.interval[this.questionIndex])
            },
            startTime(index) {
                start_time = Date.now() - this.quiz.questions[index].time;

                this.interval[index] = setInterval(() => {
                    this.quiz.questions[index].time = Date.now() - start_time;
                    app.format_time(this.quiz.questions[index].time)
                }, 1000)
            },
        }
    });
});