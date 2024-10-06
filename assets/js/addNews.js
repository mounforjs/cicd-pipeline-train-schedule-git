$(document).ready( function() {
    baseURL = window.location.origin;

    var eWidth = $("#excerptContent").width();
    var eHeight = 96;

    var aWidth = $("#article").width();
    var aHeight = 500;

    addNewTinyMCE("add_excerpt", "", 2000, eWidth, eHeight);	
    addNewTinyMCE("add_article", "", 8000, aWidth, aHeight);	

    function addNewTinyMCE(id, content, maxChar=1000, width, height) {	
        oldContent = content;	
        tinyMCEInitialize(id, content, maxChar);	
    }

    function addArticle(id, t, su, p, e, fi, c) {
        $.ajax({
            method: 'POST',
            data: ({ id: id, title: t, shorturl: su, published: p, excerpt: e, featured_image: fi, content: c}),
            url: baseURL + "/news/addArticle",
            error: function() {
                showSweetAlert("Could not add article.", "Whoops!", "error");
            },
            success: function(data) {
                var newUrl = window.location.origin + '/news/article/' + JSON.parse(data).slug;
                window.location.href = newUrl;
            }
        });
    }

    function checkShortURL() {
        var short = $("#shorten_url").val();

        return $.ajax({
            method: 'POST',
            data: { short: short },
            url: window.location.origin + "/short/check_short_url",
            success: function(data) {
                data = JSON.parse(data);

                if (data.status == "success") {
                    $("#shorten_url").css("background-color", "rgb(0 187 11 / 37%)");
                    $("#addArticle").attr("disabled", false);
                } else {
                    $("#shorten_url").css("background-color", "rgb(187 0 0 / 37%)");
                    $("#addArticle").attr("disabled", true);
                }
            }
        });
    }

    var previousCheck = null;
    $("#shorten_url").on("keyup", function(e) {
        $("#addArticle").attr("disabled", true);
        
        if (previousCheck) { previousCheck.abort(); }
        previousCheck = checkShortURL();
    });

    $("#addArticle").click( function(e) {
        var articleTitle = $("#title");
        var id = $(articleTitle).data("id");
        var title = $(articleTitle).find("input").val();
        var shorturl = $("#shorten_url").val();
        var publishedState = $("#published").find("input:checked").val();
        var image = $(".featuredImageWrapper img").attr("src");
        var excerpt = decodeHTML(tinymce.get('add_excerpt').getContent());	
        var content = tinymce.get('add_article').getContent();

        if (tinymce.get('add_excerpt').isDirty() || tinymce.get('add_article').isDirty()) {
            showSweetConfirm("Create new article?", "Attention", $icon='info', function(confirmed) {
                if (!confirmed) {
                    e.preventDefault();
                } else {
                    addArticle(id, title, shorturl, publishedState, excerpt, image, content);
                }
            });
        } else {
            showSweetAlert("No changes have been made.", "Whoops!", "error");
        }
    });

    $("#cancelChanges").click( function(e) {
        var url = $(this).data("url");

        showSweetConfirm("Are you sure you want to cancel?", "Attention", $icon='info', function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
            } else {
                window.location.href = url;
            }
        });
    });

    $("input[type='radio']").click( function() {
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
});