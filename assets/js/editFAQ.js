$(document).ready(function() {
    baseURL = window.location.origin;

    var categories = [];
    var subCategories = [];

    var oldContent = new Map();

    addToArray(categories, null, 0);
    addToArray(subCategories, null, 1);

    function addNewTinyMCE(id, rowNum, content, maxChar=1000, width, height) {	
        oldContent.set(rowNum, content);
        tinyMCEInitialize(id, content, maxChar, width, height);	
    }	

    function removeTinyMCE(id) {	
        tinymce.get(id).remove();	
    }

    function addToArray(arr, toAdd, type) {
        if (!toAdd) {
            if (type === 0) {
                $("#category option[value!='null']").each(function() {
                    arr.push($(this).text());
                });
            } else {
                $("#subcategories option[value!='null']").each(function() {
                    arr.push($(this).text());
                });
            }
        } else {
            arr.push($(this).text());
        }
    }

    function editInArray(arr, toEdit, toChange) {
        var index = arr.indexOf(toEdit);
        arr[index] = toChange;
    }

    function removeFromArray(arr, toRemove) {
        var index = arr.indexOf($(toRemove).text());
        if (index !== -1) {
            arr.splice(index, 1)
        }
    }

    function replaceInputs(elem, old=false) {
        $(elem).find("td[class='apply']").remove();
        $(elem).find("td[class='cancel']").remove();

        $(elem).find("td[class!='delete']:not('.edit')").each(function() {
            var id = "";
            var content = "";

            var input = $(this).find("input");
            if (input.length > 0) {
                if ($(input).attr("name").includes("subcategory")) {
                    $("#subCategoryTable").find("tr td[class!='delete']:not('edit')").filter( function(index) {
                        if ($(this).text() == $(input).val()) {
                            id = parseInt($(this).closest("tr").find("td:first").text()); 
                        }
                    });
                } else if ($(input).attr("name").includes("category")) {
                    $("#categoryTable").find("tr td").filter( function(index) {
                        if ($(this).text() == $(input).val()) {
                            id = parseInt($(this).closest("tr").find("td:first").text()); 
                        }
                    })
                } else {
                    id = $(input).data("id");
                }
                
                if (!old) {
                    content = $(input).val();
                } else {
                    content = $(input).data("old");
                }
            } else {
                var textarea = $(this).find("textarea");
                if ($(textarea).hasClass('form-control')) {
                    id = $(textarea).data("id");
                    if (!old) {
                        content = tinymce.get("edit_answer").getContent();
                    } else {
                        console.log(oldContent);
                        dataOld = parseInt($(textarea).data("old"));	
                        content = oldContent.get(dataOld);	
    	
                        oldContent.delete(dataOld);
                    }
                } else {
                    id = $(textarea).data("id");
                    if (!old) {
                        content = $(textarea).val();
                    } else {
                        content = $(textarea).data("old");
                    }
                }
                
            }

            if (id != null) {
                $(this).replaceWith("<td data-id='" + id + "'>" + content + "</td>");
            } else {
                $(this).replaceWith("<td>" + content + "</td>");
            }
        });
    }

    function toggleButtons(elem, type) {
        $(document).find(elem).find("td[class='delete'] button").attr("disabled", false);
        $(document).find(elem).css("background-color", "white");

        if (type === 0) {
            $("#addQuestion").attr("disabled", false);
        } else if (type === 1) {
            $("#addCategory").attr("disabled", false);
        } else if (type === 2) {
            $("#addSubCategory").attr("disabled", false);
        } else {
            toggleAllAddButtons();
        }
        
        $(document).find(elem).find("td[class='delete'] button").attr("disabled", false);
        $(document).find(elem).find("td[class='edit'] button").attr("disabled", false);

        $(document).find(elem).find("td[class='apply']").remove();
        $(document).find(elem).find("td[class='cancel']").remove();
    }

    function toggleAllAddButtons() {
        $("#addQuestion").attr("disabled", false);
        $("#addCategory").attr("disabled", false);
        $("#addSubCategory").attr("disabled", false);
    }

    function toggleAllEditButtons(table, except, b) {
        $(document).find(table + " td[class='edit'] button").each( function() {
            if ($(this).val() != except) {
                $(this).attr("disabled", b);
            }
        });
    }

    $("#refresh").click(function() {
        var category = $("#category option:selected").val();
        var subCategory = $("#subcategories option:selected").val();

        getCategories(category);
        getSubCategories(category, subCategory);
        getQuestions(category, subCategory);
        toggleAllAddButtons();
    });

    $("#category").on('change', function() {
        var open = $(this).data("isopen");
        
        var cid = $("#category option:selected").val();
        getSubCategories(cid);
        getQuestions(cid);
        toggleAllAddButtons();
        
        $(this).data("isopen", !open);
    });
    
    $("#subcategories").on('change', function() {
        var open = $(this).data("isopen");

        var cid = $("#category option:selected").val();
        var scid = $("#subcategories option:selected").val();
        getQuestions(cid, scid);
        toggleAllAddButtons();
        
        $(this).data("isopen", !open);
    });

    //populate tables and dropdowns
    function populateDropDown(elem, type) {
        var id = $(elem).find("td").eq( 0 ).text();
        if (type == 0) {
            var category = $(elem).find("td").eq( 1 ).text();
        } else {
            var category = $(elem).find("td").eq( 2 ).text();
        }
        
        var newOption = $('<option value=' + id +'> ' + category + ' </option>');
            
        if (type == 0) {
            $('#category').append(newOption);
        } else {
            $('#subcategories').append(newOption);
        }
        
    }

    function editDropDown(elem, oldID, type) {
        var id = $(elem).find("td").eq( 0 ).text();
        if (type == 0) {
            var category = $(elem).find("td").eq( 1 ).text();
        } else {
            var category = $(elem).find("td").eq( 2 ).text();
        }
        
        $("option[value='" + oldID + "']").val(id);
        $("option[value='" + id + "']").text(category);
    }

    function depopulateDropDown(elem) {
        var id = $(elem).find("td").eq( 0 ).text();

        $("option[value='" + id + "']").remove();
    }

    function addNewQuestionRow(newId) {
        var table = $("#questionTable");

        newRow = "<tr id='row" + newId + "' style='background-color: green;'>" + 
                    "<td><input type='text' name='id" + newId + "' value='"+ newId +"'></td>" +
                    "<td><textarea type='text' name='question" + newId + "' rows='5' cols='25' value=''></textarea></td>" +	
                    "<td><textarea class='form-control' name='edit_answer' rows='10' cols='30' id='edit_answer' required>" +	
                    "</textarea><label class='error' for='edit_answer' id='edit_answer-error' style='display: none;'></label></td>" +
                    "<td><input type='text' name='category" + newId + "' value=''></td>" +
                    "<td><input type='text' name='subCategory" + newId + "' value=''></td>" +
                    "<td class='delete'><button class='deleteQuestion btn btnSmall' disabled>Delete</button></td>" +
                    "<td class='edit'><button class='editQuestion btn btnSmall' value='" + newId + "' disabled>Edit</button></td>" +
                    "<td class='apply'><button class='addQuestion btn btnSmall' value='" + newId + "'>Apply</button></td>" +
                    "<td class='cancel'><button class='cancelQuestion btn btnSmall' value='cancel'>Cancel</button></td>" +
                "</tr>";

        table.append(newRow);

        addNewTinyMCE("edit_answer", newId, "", "", 1000);

        location.href = "#row" + newId;
    }

    function addNewCategoryRow(newId) {
        var table = $("#categoryTable");

        newRow = "<tr id='row" + newId + "' style='background-color: green;'>" + 
                    "<td><input type='text' name='id" + newId + "' value='"+ newId +"'></td>" +
                    "<td><input type='text' name='category" + newId + "' value=''></td>" +
                    "<td class='delete'><button class='deleteCategory btn btnSmall' disabled>Delete</button></td>" +
                    "<td class='edit'><button class='editCategory btn btnSmall' value='" + newId + "' disabled>Edit</button></td>" +
                    "<td class='apply'><button class='addCategory btn btnSmall' value='" + newId + "'>Apply</button></td>" +
                    "<td class='cancel'><button class='cancelCategory btn btnSmall' value='cancel'>Cancel</button></td>" +
                "</tr>";

        table.append(newRow);

        location.href = "#row" + newId;
    }

    function addNewSubCategoryRow(newId) {
        var table = $("#subCategoryTable");

        newRow = "<tr id='row" + newId + "' style='background-color: green;'>" + 
                    "<td><input type='text' name='id" + newId + "' value='"+ newId +"'></td>" +
                    "<td><input type='text' name='category" + newId + "' value=''></td>" +
                    "<td><input type='text' name='subcategory" + newId + "' value=''></td>" +
                    "<td class='delete'><button class='deleteSubCategory btn btnSmall' disabled>Delete</button></td>" +
                    "<td class='edit'><button class='editSubCategory btn btnSmall' value='" + newId + "' disabled>Edit</button></td>" +
                    "<td class='apply'><button class='addSubCategory btn btnSmall' value='" + newId + "'>Apply</button></td>" +
                    "<td class='cancel'><button class='cancelSubCategory btn btnSmall' value='cancel'>Cancel</button></td>" +
                "</tr>";

        table.append(newRow);

        location.href = "#row" + newId;
    }

    function getCategories(cid) {
        $.ajax({
            method: 'GET',
            url: baseURL+'/faq/getCategories',
            success: function(d) {
                loadCategories(JSON.parse(d), cid);	
            }
        });
    }

    function loadCategories(d, selected=null) {
        $('#category').empty();
        $('#categoryTable tr:gt(0)').remove();

        var def = $('<option selected value="null">All</option>');
        $('#category').append(def);

        $.each(d, function (index, category) {
            if (category.id == selected) {
                var newOption = $('<option selected value=' + category.id +'> ' + category.category + ' </option>');
            } else {
                var newOption = $('<option value=' + category.id +'> ' + category.category + ' </option>');
            }

            $('#category').append(newOption);

            var newOption = $('<tr>' +
                                '<td>'+ category.id +'</td>' +
                                '<td>'+ category.category +'</td>' +
                                '<td class="delete"><button class="deleteCategory btn btnSmall">Delete</button></td>' +
                                '<td class="edit"><button  class="editCategory btn btnSmall" value="'+ category.id +'">Edit</button></td>' +
                            '</tr>');
                        
            $('#categoryTable').append(newOption);
        });
        
        $('#category').trigger("chosen:updated");
    }

    function getSubCategories(cid, scid=null) {
        $.ajax({
            method: 'GET',
            data: ({ catID: cid }),
            url: baseURL+'/faq/getSubCategories',
            success: function(d) {
                if (scid != null) {
                    loadSubCategories(JSON.parse(d), scid);	
                } else {
                    loadSubCategories(JSON.parse(d));	
                }
            }
        });
    }

    function loadSubCategories(d, selected=null) {
        $("#subcategories").empty(); //dropdown
        $('#subCategoryTable tr:gt(0)').remove(); //table

        var def = $('<option selected value="null">All</option>');
        $('#subcategories').append(def);

        $.each(d, function (index, subcategory) {
            if (subcategory.id == selected) {
                var newOption = $('<option selected value=' + subcategory.id +'> ' + subcategory.type + ' </option>');
            } else {
                var newOption = $('<option value=' + subcategory.id +'> ' + subcategory.type + ' </option>');
            }
                   
            $('#subcategories').append(newOption);

            var newOption = $('<tr>' +
                                '<td>'+ subcategory.id +'</td>' +
                                '<td data-id="' + subcategory.catID + '">'+ subcategory.category +'</td>' +
                                '<td>'+ subcategory.type +'</td>' +
                                '<td class="delete"><button class="deleteSubCategory btn btnSmall">Delete</button></td>' +
                                '<td class="edit"><button  class="editSubCategory btn btnSmall" value="' + subcategory.id + '">Edit</button></td>' +
                            '</tr>');
                        
            $('#subCategoryTable').append(newOption);
        });
        
        $('#subcategories').trigger("chosen:updated");
    }

    function getQuestions(cid, scid="null") {
        $.ajax({
            method: 'GET',
            data: ({ catID: cid, subCatID: scid }),
            url: baseURL+'/faq/getQuestions/',
            success: function(d) {
                loadQuestions(JSON.parse(d));	
            }
        });
    }

    function loadQuestions(d) {
        $('#questionTable tr:gt(0)').remove(); //table

        $.each(d, function (index, question) {
            var newOption = $('<tr>' +
                                '<td>'+ question.id +'</td>' +
                                '<td>'+ question.question +'</td>' +
                                '<td>'+ question.answer +'</td>' +
                                '<td data-id="' + question.fcid + '">'+ question.category +'</td>' +
                                '<td data-id="' + question.fscid + '">'+ question.subCategory +'</td>' +
                                '<td class="delete"><button class="deleteQuestion btn btnSmall">Delete</button></td>' +
                                '<td class="edit"><button class="editQuestion btn btnSmall" value="'+ question.id +'">Edit</button></td>' +
                            '</tr>');
                        
            $('#questionTable').append(newOption);
        });
    }

    function getNewID(type) {
        $.ajax({
            method: 'GET',
            data: ({ type: type }),
            url: baseURL+'/faq/getIncrementedID',
            success: function(d) {
                if (type == "question") {
                    toggleAllEditButtons("#questionTable", null, true)
                    addNewQuestionRow(d);
                } else if (type == "category") {
                    addNewCategoryRow(d);
                } else {
                    addNewSubCategoryRow(d);
                }
            }
        });
    }
    //populate tables and dropdowns

    //ajax calls
    function addQuestions(d, tr) {
        $.ajax({
            method: 'POST',
            data: ({ id: d[0], question: d[1], answer: d[2], category: d[3], subcategory: d[4]}),
            url: baseURL + "/faq/addQuestion",
            error: function () { 
                showSweetAlert("Could not add question. Check for duplicate id.", "Whoops!", "error");
            },
            success: function() {
                replaceInputs(tr);
                toggleButtons(tr, 0);
            }
        });
    }
    
    function addCategories(d, tr) {
        $.ajax({
            method: 'POST',
            data: ({ id: d[0], category: d[1]}),
            url: baseURL + "/faq/addCategory",
            error: function () { 
                showSweetAlert("Could not add question. Check for duplicate id.", "Whoops!", "error");
            },
            success: function() {
                addToArray(categories, d[1], 0);

                replaceInputs(tr);
                toggleButtons(tr, 1);
                populateDropDown(tr, 0);
            }
        });
    }

    function addSubCategories(d, tr) {
        $.ajax({
            method: 'POST',
            data: ({ id: d[0], category: d[1], type: d[2]}),
            url: baseURL + "/faq/addSubCategory",
            error: function () { 
                showSweetAlert("Could not add subcategory. Check for duplicate id.", "Whoops!", "error");
            },
            success: function() {
                addToArray(subCategories, d[1], 0);

                replaceInputs(tr);
                toggleButtons(tr, 2);
                populateDropDown(tr, 1);
            }
        });
    }

    function updateQuestions(d, tr) {
        $.ajax({
            method: 'POST',
            data: ({ oldID: d[0], id: d[1], question: d[2], answer: d[3], category: d[4], subcategory: d[5]}),
            url: baseURL + "/faq/updateQuestion",
            error: function() {
                showSweetAlert("Could not update question.", "Whoops!", "error");
            },
            success: function() {
                replaceInputs(tr);
                toggleButtons(tr, 0);
                toggleAllEditButtons("#questionTable", $(tr).find("td[class='edit'] button").val(), false);
                $(tr).find("td[class='delete'] button").attr("disabled", false);
                $(tr).css("background-color", "white");
            }
        });
    }

    function updateCategories(d, tr) {
        $.ajax({
            method: 'POST',
            data: ({ oldID: d[0], id: d[2], category: d[3]}),
            url: baseURL + "/faq/updateCategory",
            error: function() {
                showSweetAlert("Could not update category.", "Whoops!", "error");
            },
            success: function() {
                replaceInputs(tr);
                toggleButtons(tr, 1);
                editDropDown(tr, d[0], 0);
                editInArray(categories, d[1], d[3]);
                $(tr).find("td[class='delete'] button").attr("disabled", false);
                $(tr).css("background-color", "white");
            }
        });
    }

    function updateSubCategories(d, tr) {
        $.ajax({
            method: 'POST',
            data: ({ oldID: d[0], id: d[2], category: d[3], type: d[4]}),
            url: baseURL + "/faq/updateSubCategory",
            error: function() {
                showSweetAlert("Could not update subcategory.", "Whoops!", "error");
            },
            success: function() {
                replaceInputs(tr);
                toggleButtons(tr, 2);
                editDropDown(tr, d[0], 1);
                editInArray(subCategories, d[1], d[4]);
                $(tr).find("td[class='delete'] button").attr("disabled", false);
                $(tr).css("background-color", "white");
            }
        });
    }

    function deleteQuestion(id, tr) {
        $.ajax({
            method: 'POST',
            data: ({ id: id}),
            url: baseURL + "/faq/deleteQuestion",
            error: function () { 
                showSweetAlert("Could not add question.", "Whoops!", "error");
            },
            success: function() {
                $(document).find(tr).remove();
            }
        });
    }

    function deleteCategory(id, tr, category) {
        $.ajax({
            method: 'POST',
            data: ({ id: id}),
            url: baseURL + "/faq/deleteCategory",
            error: function () { 
                showSweetAlert("Could not add category.", "Whoops!", "error");
            },
            success: function() {
                depopulateDropDown(tr);
                $(document).find(tr).remove();
                removeFromArray(categories, category);
            }
        });
    }

    function deleteSubCategory(id, tr, subcategory) {
        $.ajax({
            method: 'POST',
            data: ({ id: id}),
            url: baseURL + "/faq/deleteSubCategory",
            error: function () { 
                showSweetAlert("Could not delete subcategory.", "Whoops!", "error");
            },
            success: function() {
                depopulateDropDown(tr);
                $(document).find(tr).remove();
                removeFromArray(subCategories, subcategory);
            }
        });
    }
    //ajax calls

    //question buttons
    $(document).on("click", "#addQuestion", function(e) {
        // add new row to category table
        $(this).attr("disabled", true);

        var newId = getNewID("question");
    });

    $(document).on("click", ".addQuestion", function(e) {
        // trigger databsae to add question
        var tr = $(this).closest("tr");
        var id = $(tr).find("td:first").find("input").val().trim();
        var question = $(tr).find("td").eq( 1 ).find("textarea").val();
        var answer = tinymce.get("edit_answer").getContent();
        var category = $(tr).find("td").eq( 3 ).find("input").val().trim().toLowerCase();
        var subcategory = $(tr).find("td").eq( 4 ).find("input").val().trim().toLowerCase();
        var data = [id, question, answer, category, subcategory];

        if (data.includes("")) {
            showSweetAlert("Cannot leave empty fields when adding new question.", "Whoops!", "error");
            e.preventDefault();
        } else {
            if (parseInt(id)) {
                if (!categories.includes(category) || !subCategories.includes(subcategory)) {
                    showSweetAlert("Could not add new question. Either the category or subcategory do not exist.", "Whoops!", "error");
                    e.preventDefault();
                } else {
                    showSweetConfirm('Are you sure you want to add question with id of ' + id + '?', "Warning!", "warning", function(confirmed) {
                        if (!confirmed) {
                            e.preventDefault();
                        } else {
                            addQuestions(data, tr);
                            toggleAllEditButtons("#questionTable", null, false)
                        }     
                    }); 
                }
            }
        }
    });

    $(document).on("click", ".cancelQuestion", function(e) {
        // cancel adding question
        $("#addQuestion").attr("disabled", false);

        removeTinyMCE("edit_answer");

        toggleAllEditButtons("#questionTable", null, false)
        var table = $("#questionTable");
        var newId = $(table).find("tr:last").remove();
    });

    $(document).on("click", ".editQuestion", function(e) {
        // trigger database to edit question
        var num;

        var tr = $(this).closest("tr");
        if (jQuery.makeArray($(tr).find("input")).length <= 0) {
            $(tr).find("td[class='delete'] button").attr("disabled", true);

            $(tr).find("td:last").after("<td class='cancel'><button class='cancelEditQuestion btn btnSmall' value='cancel'>Cancel</button></td>");

            $(tr).find("td[class!='delete']:not(.edit):not(.cancel)").each(function(i) {
                var content = $(this).text();
                var id = $(this).data("id");
                var rowNum = "";
                var name = '';
                
                var col = i % 5;
                switch (col) {
                    case 0:
                        num = content;
                        name = 'id' + num;
                        break;
                    case 1:
                        name = 'question' + num;
                        break;
                    case 2:
                        name = 'answer' + num;
                        rowNum = $(tr).index();

                        content = $(this).html();
                        break;
                    case 3:
                        name = 'category' + num;
                        break;
                    case 4:
                        name = 'subcategory' + num;
                        break;   
                }

                if (id != null) {
                    $(this).replaceWith("<td><input type='text' name='ques" + name + "' data-id='" + id + "' data-old='" + content + "' value='" + content + "'></td>");
                } else {
                    if (col == 2) {
                        $(this).replaceWith("<td><textarea class='form-control' name='edit_answer' rows='10' cols='30' id='edit_answer' data-old='" + rowNum + "' required>" +	
                                            "</textarea><label class='error' for='edit_answer' id='edit_answer-error' style='display: none;'></label></td>");	
                        addNewTinyMCE("edit_answer", rowNum, content, 1000);
                    } else {
                        if (name.includes("id")) {
                            $(this).replaceWith("<td><input type='text' name='ques" + name + "' data-old='" + content + "' value='" + content + "'></td>");
                        } else {
                            $(this).replaceWith("<td><textarea type='text' name='ques" + name + "' rows='5' cols='25' maxlength='200' data-old='" + content + "' value='" + content + "'>" + content + "</textarea></td>");
                        }
                        
                    }
                }

                $(tr).css("background-color", "yellow");
            });

            $("#addQuestion").attr("disabled", true);
            toggleAllEditButtons("#questionTable", $(tr).find("td[class='edit'] button").val(), true);
        } else {
            var data = [];
            var id = $(tr).find("td:first input").data("old");

            data.push(id);

            var sameValues = true;
            $(tr).find("td[class!='delete']:not('.edit'):not(.cancel)").each(function(i) {
                var content = "";
                var old = "";

                var input = $(this).find("input");
                if (input.length > 0) {
                    content = ($(input).val().trim() == "null" || $(input).val() == "") ? null : $(input).val().trim().toLowerCase();
                    old = $(input).data("old");

                    if (content != old) {
                        sameValues = false;
                    }
                } else {
                    var textarea = $(this).find("textarea");
                    if ($(textarea).hasClass('form-control')) {
                        var editor = $(this).find("textarea[class*='form-control']");
                        content = tinymce.get("edit_answer").getContent();
                        old = parseInt( $(editor).data("old") );

                        if (tinymce.activeEditor.isDirty()) {
                            sameValues = false;
                        }
                    } else {
                        content = $(textarea).val();
                        old = $(textarea).data("old");

                        if (content != old) {
                            sameValues = false;
                        }
                    }
                }

                data.push(content);
            });

            if (sameValues) {
                showSweetAlert("No fields were changed, will not update.", "Whoops!", "error");
                e.preventDefault();
            } else {
                var category = $(tr).find("td").eq( 3 ).find("input").val().trim().toLowerCase();
                var subcategory = $(tr).find("td").eq( 4 ).find("input").val().trim().toLowerCase();

                if (!categories.includes(category) || !subCategories.includes(subcategory)) {
                    showSweetConfirm("Either the category or subcategory do not exist, do you still want to edit?", "Warning!", "warning", function(confirmed) {
                        if (!confirmed) {
                            e.preventDefault();
                        } else {
                            updateQuestions(data, tr);
                        } 
                    });
                } else {
                    var id = $(document).find(this).closest("tr").find("input:first").val();
                    showSweetConfirm('Are you sure you want to update question with id of ' + id + '?', "Warning!", "warning", function(confirmed) {
                        if (!confirmed) {
                            e.preventDefault();
                        } else {
                            updateQuestions(data, tr);
                        } 
                    });
                }
            }
        }
    });

    $(document).on("click", ".cancelEditQuestion", function(e) {
        // cancel editing subcategory
        var tr = $(this).closest("tr");

        removeTinyMCE("edit_answer");

        replaceInputs(tr, true);
        toggleButtons(tr, 0);
        toggleAllEditButtons("#questionTable", $(tr).find("td[class='edit'] button").val(), false);
        $(this).closest("td").remove();
    });

    $(document).on("click", ".deleteQuestion", function(e) {
        // trigger databsae to delete question
        var tr = $(this).closest("tr");
        var id = $(tr).find("td:first").text();

        $(tr).css("background-color", "red");

        showSweetConfirm('Are you sure you want to DELETE question with id of ' + id + '?', "Warning!", "warning", function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
                $(tr).css("background-color", "white");
            } else {
                deleteQuestion(id, tr);
            } 
        });
    });

    //category buttons
    $(document).on("click", "#addCategory", function(e) {
        // add new row to category table
        $(this).attr("disabled", true);

        var newId = getNewID("category");
    });

    $(document).on("click", ".addCategory", function(e) {
        // trigger database to add category
        var tr = $(this).closest("tr");
        var id = $(tr).find("td input").first().val();
        var category = $(tr).find("td input").eq( 1 ).val();
        var data = [id, category];

        if (id == '' || category == '') {
            showSweetAlert("Cannot leave empty fields when adding new question.", "Whoops!", "error");
            e.preventDefault();
        } else {
            if (parseInt(id)) {
                if (categories.includes(category)) {
                    showSweetAlert("Could not add new question. Category already exists.", "Whoops!", "error");
                    e.preventDefault();
                } else {
                    showSweetConfirm('Are you sure you want to add category with id of ' + id + '?', "Warning!", "warning", function(confirmed) {
                        if (!confirmed) {
                            e.preventDefault();
                        } else {
                            addCategories(data, tr);
                        }  
                    });
                } 
            }
        }
    });

    $(document).on("click", ".cancelCategory", function(e) {
        // cancel adding category
        $("#addCategory").attr("disabled", false);

        var table = $("#categoryTable");
        var newId = $(table).find("tr:last").remove();
    });

    $(document).on("click", ".editCategory", function(e) {
        // trigger database to edit category
        var num;

        var tr = $(this).closest("tr");
        if (jQuery.makeArray($(tr).find("input")).length <= 0) {
            $(tr).find("td[class='delete'] button").attr("disabled", true);

            $(tr).find("td:last").after("<td class='cancel'><button class='cancelEditCategory' value='cancel'>Cancel</button></td>");

            $(tr).find("td[class!='delete']:not(.edit):not(.cancel)").each(function(i) {
                var content = $(this).text();
                var id = $(this).data("id");
                var name = '';

                switch (i % 2) {
                    case 0:
                        num = content;
                        name = 'id' + num;
                        break;
                    case 1:
                        name = 'category' + num;
                        break;
                }
    
                if (id != null) {
                    $(this).replaceWith("<td><input type='text' name='cate" + name + "' data-id='" + id + "' data-old='" + content + "' value='" + content + "'></td>");
                } else {
                    $(this).replaceWith("<td><input type='text' name='cate" + name + "' data-old='" + content + "' value='" + content + "'></td>");
                }

                $(tr).css("background-color", "yellow");
            });
        } else {
            var data = [];
            var oldID = $(tr).find("td:first input").data("old");
            var oldCat = $(tr).find("td:eq( 1 ) input").data("old");

            data.push(oldID);
            data.push(oldCat);

            var sameValues = true;
            $(tr).find("td input").each(function(i) {
                var content = $(this).val();
                var old = $(this).data("old");

                if (content != old) {
                    sameValues = false;
                }

                data.push(content);
            });

            if (sameValues) {
                showSweetAlert("No fields were changed, will not update.", "Whoops!", "error");
                e.preventDefault();
            } else {
                var category = $(tr).find("td input").eq( 1 ).val();
                var old = $(tr).find("td input").eq( 1 ).data("old");

                if (categories.includes(category) && category != old) {
                    showSweetAlert("Could not edit category. The category already exists.", "Whoops!", "error");
                    e.preventDefault();
                } else {
                    var id = $(document).find(this).closest("tr").find("input:first").val();
                    showSweetConfirm('Are you sure you want to update category with id of ' + id + '?', "Warning!", "warning", function(confirmed) {
                        if (!confirmed) {
                            e.preventDefault();
                        } else {
                            updateCategories(data, tr);
                        }    
                    });
                }
            }
        }
    });

    $(document).on("click", ".cancelEditCategory", function(e) {
        // cancel editing category
        var tr = $(this).closest("tr");

        replaceInputs(tr, true);
        toggleButtons(tr, 1);
        $(this).closest("td").remove();
    });

    $(document).on("click", ".deleteCategory", function(e) {
        // trigger database to delete category
        var tr = $(this).closest("tr");
        var id = $(tr).find("td:first").text();
        var cat = $(tr).find("td").eq(1).text();

        $(tr).css("background-color", "red");

        showSweetAlert("Deleteing an entire category will set the fields where it once was to NULL", "Warning", "warning");
        showSweetConfirm('Are you sure you want to DELETE category with id of ' + id + ' ?', "Warning!", "warning", function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
                $(tr).css("background-color", "white");
            } else {
                deleteCategory(id, tr, cat);
            }     
        }); 
    });

    //subcategory buttons
    $(document).on("click", "#addSubCategory", function(e) {
        // add new row to subcategory table
        $(this).attr("disabled", true);

        var newId = getNewID("subcategory");
    });

    $(document).on("click", ".addSubCategory", function(e) {
        // trigger database to add subcategory
        var tr = $(this).closest("tr");
        var id = $(tr).find("td input").first().val();
        var category = $(tr).find("td input").eq( 1 ).val();
        var subcategory = $(tr).find("td input").eq( 2 ).val();
        var data = [id, category, subcategory];

        if (id == '' || subcategory == '') {
            showSweetAlert("Cannot leave empty fields when adding new question.", "Whoops!", "error");
            e.preventDefault();
        } else {
            if (parseInt(id)) {
                if (!categories.includes(category) || subCategories.includes(subcategory)) {
                    showSweetAlert("Could not add new question. Category either does not exist, or subcategory already exists.", "Whoops!", "error");
                    e.preventDefault();
                } else { 
                    showSweetConfirm('Are you sure you want to add subcategory with id of ' + id + '?', "Warning!", "warning", function(confirmed) {
                        if (!confirmed) {
                            e.preventDefault();
                        } else {
                            addSubCategories(data, tr);
                        }      
                    }); 
                } 
            }
        }
    });

    $(document).on("click", ".cancelSubCategory", function(e) {
        // cancel adding subcategory
        $("#addSubCategory").attr("disabled", false);

        var table = $("#subCategoryTable");
        $(table).find("tr:last").remove();
    });

    $(document).on("click", ".editSubCategory", function(e) {
        // trigger database to edit subcategory
        var num;

        var tr = $(this).closest("tr");
        if (jQuery.makeArray($(tr).find("input")).length <= 0) {
            $(tr).find("td[class='delete'] button").attr("disabled", true);

            $(tr).find("td:last").after("<td class='cancel'><button class='cancelEditSubCategory' value='cancel'>Cancel</button></td>");

            $(tr).find("td[class!='delete']:not(.edit):not(.cancel)").each(function(i) {
                var content = $(this).text();
                var id = $(this).data("id");
                var name = '';

                switch (i % 3) {
                    case 0:
                        num = content;
                        name = 'id' + num;
                        break;
                    case 1:
                        name = 'category' + num;
                        break;
                    case 2:
                        name = 'type' + num;
                        break;
                }

                if (id != null) {
                    $(this).replaceWith("<td><input type='text' name='subC" + name + "' data-id='" + id + "' data-old='" + content + "' value='" + content + "'></td>");
                } else {
                    $(this).replaceWith("<td><input type='text' name='subC" + name + "' data-old='" + content + "' value='" + content + "'></td>");
                }
    
                $(tr).css("background-color", "yellow");
            });
        } else {
            var data = [];
            var oldID = $(tr).find("td:first input").data("old");
            var oldCat = $(tr).find("td:eq( 2 ) input").data("old");

            data.push(oldID);
            data.push(oldCat);

            var sameValues = true;
            $(tr).find("td input").each(function(i) {
                var content = $(this).val();
                var old = $(this).data("old");

                if (content != old) {
                    sameValues = false;
                }

                data.push(content);
            });

            if (sameValues) {
                showSweetAlert("No fields were changed, will not update.", "Whoops!", "error");
                e.preventDefault();
            } else {
                var subcategory = $(tr).find("td input").eq( 2 ).val();
                var old = $(tr).find("td input").eq( 2 ).data("old");

                if (subCategories.includes(subcategory) && subcategory != old) {
                    showSweetAlert("Could not edit subcategory. The subcategory already exists.", "Whoops!", "error");
                    e.preventDefault();
                } else {
                    var id = $(document).find(this).closest("tr").find("input:first").val();
                    showSweetConfirm('Are you sure you want to update subcategory with id of ' + id + ' ?', "Warning!", "warning", function(confirmed) {
                        if (!confirmed) {
                            e.preventDefault();
                        } else {
                            updateSubCategories(data, tr);
                        }       
                    });  
                }
            }
        }
    });

    $(document).on("click", ".cancelEditSubCategory", function(e) {
        // cancel editing subcategory
        var tr = $(this).closest("tr");

        replaceInputs(tr, true);
        toggleButtons(tr, 2);
        $(this).closest("td").remove();
    });

    $(document).on("click", ".deleteSubCategory", function(e) {
        // trigger database to delete subcategory
        var tr = $(this).closest("tr");
        var id = $(tr).find("td:first").text();
        var subCat = $(tr).find("td").eq(2).text();
        
        $(tr).css("background-color", "red");

        showSweetConfirm('Are you sure you want to DELETE subcategory with id of ' + id + ' ?', "Warning!", "warning", function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
                $(tr).css("background-color", "white");
            } else {
                deleteSubCategory(id, tr, subCat);
            }       
        }); 
    });
});