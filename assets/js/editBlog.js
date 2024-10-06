$(document).ready( function() {
    baseURL = window.location.origin;

    var oldTitle = "";
    var oldPublishedState = "";
    var oldImage = "";
    var oldExcerpt = "";
    var oldContent = "";

    function addNewTinyMCE(id, content, maxChar=1000, width, height) {	
        oldContent = content;	
        tinyMCEInitialize(id, content, maxChar, width, height);	
    }	
    function removeTinyMCE(id) {	
        tinymce.get(id).remove();	
    }

    function replaceInputs(title, short_url, published, content, image, excerpt) {
        try {
            if (tinymce.get("edit_excerpt")) {	
                removeTinyMCE("edit_excerpt");	
            }	
            if (tinymce.get("edit_blog")) {	
                removeTinyMCE("edit_blog");	
            }
        } catch (e) {
            console.log(e);
        }

        $(document).find(".featuredImageWrapper img").attr("src", image);

        $("#title").replaceWith("<h1 id='title'>" + title.trim() + "</h1>");

        $(".btn-share").attr("href", location.origin + "/" + short_url);
        $("#shorten_url").parent().remove();

        if (published == 1) {
            $("#published input[name='published']").prop("checked", true);
            $("#published input[name='unpublished']").prop("checked", false);
        } else {
            $("#published input[name='published']").prop("checked", false);
            $("#published input[name='unpublished']").prop("checked", true);
        }

        $("#published").hide();

        $("#excerptContent").replaceWith('<div id="excerptContent" class="row"><textarea class="excerptEditor answer-text" name="edit_excerpt" rows="10" cols="30" id="edit_excerpt" maxlength="500" style="width: 100%;">' + excerpt + '</textarea></div>');
        $("#post").replaceWith("<div id='post'>" + content.trim() + "</div>");
        

        $("#excerpt").hide();

        oldTitle = "";
        oldURL = "";
        oldPublishedState = "";
        oldImage = "";
        oldExcerpt = "";
        oldContent = "";
    }

    function beginEdit() {
        $("#applyChanges").show();
        $("#cancelChanges").show();
        $("#editBlogPost").hide();
        $("#deleteBlogPost").hide();
    }

    function endEdit() {
        $("#applyChanges").hide();
        $("#cancelChanges").hide();
        $("#editBlogPost").show();
        $("#deleteBlogPost").show();
    }

    function updateBlogPost(id, t, su, p, e, i, c) {
        $.ajax({
            method: 'POST',
            data: ({ id: id, title: t, shorturl: su, published: p, excerpt: e, featured_image: i, content: c}),
            url: baseURL + "/blog/updateBlogPost",
            error: function() {
                showSweetAlert("Could not update blog post.", "Whoops!", "error");
            },
            success: function(data) {
                replaceInputs(t, su, p, c, i, e);

                endEdit();

                var newUrl = window.location.origin + '/blog/post/' + JSON.parse(data).slug;
                window.location.href = newUrl;
            }
        });
    }

    function deleteBlogPost(id) {
        $.ajax({
            method: 'POST',
            data: ({ id: id}),
            url: baseURL + "/blog/deleteBlogPost",
            error: function() {
                showSweetAlert("Could not delete blog post.", "Whoops!", "error");
            },
            success: function() {
                var newUrl = window.location.origin + '/blog';
                window.location.href = newUrl;
            }
        });
    }

    $("#editBlogPost").click( function() {
        var blogPostTitle = $("#title");
        var share = $(".share");
        var post = $("#post");
        var image = $(".featuredImageWrapper");
        var imgSrc = $(image).find("img").attr("src");
        var id = $(blogPostTitle).data("id");
        var title = $(blogPostTitle).text().trim();
        var url = ($(".btn-share").data("short") == true) ? $(".btn-share").attr("href").split("/").slice(3).join("/") : "";
        var excerpt = $("#excerptContent");
        var content = $(post).html();
        
        var width = $(post).width();
        var height = $(post).height();

        if ($(post).find("textarea").length <= 0) {
            oldTitle = title;
            oldURL = url;
            oldPublishedState = $("#published input:checked").val();
            oldImage = imgSrc;
            oldExcerpt = excerpt.text();
            oldContent = content;

            $("#excerpt").show();
            
            $(image).replaceWith("<div class='col-sm-3 nopadding featuredImageWrapper'><img src='" + imgSrc + "' alt='" + title + "'/><div class='row'><div id='articleImage' class='col'><input type='file' id='imageUpload' accept='.png, .jpg, .jpeg' name='articleImage_img_path' class='newsArticleUploader' preview-at='.featuredImageWrapper img'></div></div></div>");

            $(blogPostTitle).replaceWith("<h1 id='title' data-id='" + id + "'><input class='blogPostTitle' type='text' name='title' style='width: 20ch; max-width: fit-content;' value='" + title + "'></h1>");
            share.append(" <label for='shorten_url'>" + location.origin + "/ <input class='shorturl' id='shorten_url' name='shorten_url' type='text' value='" + url + "' placeholder=''/></label>");
            $("#published").show();
            
            $(post).replaceWith('<div id="post"><textarea class="newsEditor answer-text" name="edit_blog" rows="5" cols="30" id="edit_blog" maxlength="2000"></textarea></div>');
            
            $(excerpt).replaceWith('<div id="excerptContent" class="row"><textarea class="excerptEditor answer-text" name="edit_excerpt" rows="10" cols="30" id="edit_excerpt" maxlength="500"></textarea></div>');
            var ewidth = "calc(100% - " + $(".featuredImageWrapper").width() + "px);";

            addNewTinyMCE("edit_excerpt", excerpt.text(), 2000, ewidth, height);	
            addNewTinyMCE("edit_blog", content, 8000, width, height);

            beginEdit();
        }
    });

    $("#deleteBlogPost").click( function(e) {
        

        var id = $("#title").data("id");

        showSweetConfirm("Are you sure you want to delete this blog post?", "Attention", $icon='warning', function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
            } else {
                deleteBlogPost(id);
            }
        });
    });

    $("#applyChanges").click( function() {
        var blogPostTitle = $("#title");
        var id = $(blogPostTitle).data("id");
        var title = $(blogPostTitle).find("input").val();
        var short_url = $("#shorten_url").val();
        var publishedState = $("#published").find("input:checked").val();
        var image = $(".featuredImageWrapper img").attr("src");
        var excerpt = decodeHTML(tinymce.get('edit_excerpt').getContent());	
        var content = tinymce.get('edit_blog').getContent();

        if (tinymce.get('edit_blog').isDirty() || tinymce.get('edit_excerpt').isDirty() || title != oldTitle || publishedState != oldPublishedState || image != oldImage || excerpt != oldExcerpt) {
            updateBlogPost(id, title, short_url, publishedState, excerpt, image, content);
        } else {
            showSweetAlert("No changes have been made.", "Whoops!", "error");
        }
    });

    $("#cancelChanges").click( function() {
        var post = $("#post");

        if ($(post).find("textarea").length > 0) {
            replaceInputs(oldTitle, oldURL, oldPublishedState, oldContent, oldImage, oldExcerpt);
            endEdit();
        }
    });

    $(document).on("click", "input[type='radio']", function() {
        var published = $(document).find("#published input[name='published']");
        var unpublished = $(document).find("#published input[name='unpublished']");

        if ($(this).val() == 1) {
            $(this).prop("checked", true);
            $(unpublished).prop("checked", false);
        } else {
            $(this).prop("checked", true);
            $(published).prop("checked", false);
        }
    });

    function imagesPreview(input, placeToInsertImagePreview, name) {
        if (input.files) {
            var filesAmount = input.files.length;
            var flag = 1;
            for (i = 0; i < filesAmount; i++) {

                fileTypes = input.files[i].type;
                var image = input.files[i];
                data = new FormData();
                data.append('file', input.files[i]);
                $.ajax({
                    url: window.location.origin + "/ajax/uploadImage",
                    type: "POST",
                    data: data,
                    enctype: 'multipart/form-data',
                    processData: false, // tell jQuery not to process the data
                    contentType: false // tell jQuery not to set contentType
                }).done(function(data) {
                    if (data != "error") {
                        $('.featuredImageWrapper').append("<input type='hidden' name='" + name+ "' value='" + data + "'>");
                        
                        $(placeToInsertImagePreview).attr("src", data);

                        if ($.inArray(fileTypes, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']) == -1) {
                            showSweetAlert("Not a valid image, only JPEG , PNG, or GIF allowed", "Whoops!", "error");
                        }
                    } else {
                        console.log("Image upload failed.");
                    }
                });
            }
        }
    }

    $(document).on('change', '.newsArticleUploader', function() {
        imagesPreview(this, $(this).attr('preview-at'), $(this).attr('name'));
    });

    function decodeHTML(value) {
        return $("<textarea/>").html(value).text();
     }

    function checkShortURL() {
        var short = $("#shorten_url").val();

        return $.ajax({
            method: 'POST',
            data: { short: short },
            url: window.location.origin + "/short/check_short_url",
            beforeSend: function() {
                $("#applyChanges").attr("disabled", true);
            },
            success: function(data) {
                data = JSON.parse(data);

                if (data.status == "success") {
                    $("#shorten_url").css("background-color", "rgb(0 187 11 / 37%)");
                    $("#applyChanges").attr("disabled", false);
                } else {
                    $("#shorten_url").css("background-color", "rgb(187 0 0 / 37%)");
                    $("#applyChanges").attr("disabled", true);
                }
            }
        });
    }

    var previousCheck = null;
    $(document).on("keyup", "#shorten_url", function(e) {
        $("#applyChanges").attr("disabled", true);

        if (previousCheck) { previousCheck.abort(); }
        if (oldURL == $("#shorten_url").val() || $("#shorten_url").val() == "") { 
            $("#shorten_url").css("background-color", "unset");
            $("#applyChanges").attr("disabled", false);
            return; 
        }
        previousCheck = checkShortURL();
    });
});