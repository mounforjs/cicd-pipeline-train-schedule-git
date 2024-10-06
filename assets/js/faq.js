$(function () {
    var base_url = window.location.origin;

    var $select = $('#faqQuestionSearch').selectize({
       //"onChange": refreshAdminMailSuggest,
       closeAfterSelect: true,
       maxOptions: 20,
       "valueField": "id",
       "labelField": "question",
       "searchField": "question",
       "options": [],
       persist: true,
       "create": false,
       onChange: function(value) {
         window.location = base_url +'/faq/question/' + value;
       },
       render: {
          option: function(item, escape) {
          return (
                "<div class='category'>" +
                     "<div>" + 
                      "<span class='cate'>" +
                         "<span class='name'>" + escape(item.category) + "</span>" +
                      "</span>" +
                     "</div>" +
                     "<div>" + 
                      "<span class='question'>" +
                         "<span class='name'>" + escape(item.question) + "</span>" +
                      "</span>" +
                     "</div>" +
                     "<div>" + 
                      "<span class='answer'>" +
                         "<span class='name'>" + escape(decodeHTML(item.answer.replace(/<\/?[^>]+>/gi, ''))) + "</span>" +
                      "</span>" +
                     "</div>" +
                "</div>"
             );
          }
       },
       load: function(query, callback) {
          if (!query.length || query.length < 3) {
             var selectize = $select[0].selectize;
             selectize.clearOptions();
             return callback();
          }

          $.ajax({
                url: base_url + '/faq/getAllQuestions',
                type: 'GET',
                data: "search=" + query,
                error: function() {
                   callback();
                },
                success: function(res) {
                   var data = JSON.parse(res);
                   callback(data.faq);
                }
          });
       }
    });

    $(document).on('click', function(event) {
       if ($(event.target).closest(".selectize-control, .selectize-dropdown").length <= 0) {
          var selectize = $select[0].selectize;
          selectize.clearOptions();
       }
    });

    function decodeHTML(value) {
       return $("<textarea/>").html(value).text();
    }
});