$(document).ready(function() {
    // get the clicked anchor text inside html tag
    $("a").click(function() {
        $(".heading").text($(this).text());
    });

    $('.scr-td').on('click', function() {
        $(".scr-td").removeClass("selected");
        $(this).addClass("selected");
        $('#rating').val($(this).text());
    });

    $('.scr-winwin-td').on('click', function() {
        $(".scr-winwin-td").removeClass("selected");
        $(this).addClass("selected");
        $('#winwinrating').val($(this).text());
    });

    $("#feedbackModal textarea").each(function(editor) {
        var id = $(this).attr("id");
        var content = $(this).text();

        tinyMCEInitialize(id, content, 3000, undefined, undefined, toolbarVersion=1).then(function() {
            tinymce.get("feedback_desc").contentDocument.addEventListener("keydown", function() {
                if (tinymce.get("feedback_desc").getContent() == '') {
                    $("#feedback-submit").addClass("disabled");
	                $("#feedback-submit").attr("disabled", true);
                } else {
                    $("#feedback-submit").removeClass("disabled");
                    $("#feedback-submit").attr("disabled", false);
                }
            })
        });
    });

    $('.nav-td').on('click', function() {
        $('.nav-td').removeClass("box");
        $('.nav-td').removeClass("arrow-bottom");
        $(this).addClass("box");
        $(this).addClass("arrow-bottom");
    });

    $('.feedback-category').on('click', function(e) {
        $("#feedback-tabs").removeClass("d-none");

        var id = this.dataset.id;
        $('#maincat').val(id);
        $('.sub-div').addClass('d-none');
        $('#sub_div' + id).removeClass('d-none');

        if ($('#sub_div' + id).length > 0) {
            $("#feedback-sub-div-" + id).show();
            $("#feedback-desc-header").show();
            $('.feedback-desc').hide();
        } else {
            $("#feedback-desc-header").hide();
            $('.feedback-desc').show();
        }
    });

    $('.feedback-subcategory').on('click', function(e) {
        var parent_id = this.dataset.parent;
        $('#subcat').val(this.dataset.id);
        $("#feedback-sub-div-" + parent_id).hide();
        $("#feedback-desc-header .back-btn").data("back", parent_id);
        $("#feedback-desc-header.heading").text(this.text);
        $("#feedback-desc-header").show();
        $('.feedback-desc').show();
    });

    $('.back-btn').on('click', function(e) {
        $('#feedback-sub-div-' + $(this).data("back")).show();
        $("#feedback-desc-header").hide();
        $('.feedback-desc').hide();
    });

    $('#feedback_desc').keyup(function() {
        var text = tinymce.get("feedback_desc").getContent();
        if (text.length > 0) {
            $(this).prop("disabled", false);
            $(this).removeClass("disabled");
        } else {
            $(this).prop("disabled", true);
            $(this).addClass("disabled");
        }
    });

    $('.cancel-btn').on('click', function() {
        reset_form();
    });

    $('#feedback-submit').on("click", function(e) {
        e.preventDefault();
    
        $(this).prop("disabled", true);
        $(this).addClass("disabled");
    
        var form = $('#feedback-form')[0];
        var info = new FormData(form);
        info.append('feedback_desc_editor', tinymce.get("feedback_desc").getContent());
       
        $.ajax({
            url: window.location.origin + '/feedback/user_feedback',
            type: 'POST',
            data: info,
            enctype: 'multipart/form-data',
            processData: false, // tell jQuery not to process the data
            contentType: false, // tell jQuery not to set contentType
            beforeSend: function () {
                $("#feedbackModal .load").removeClass("d-none");
            },
            complete: function () {
                $("#feedbackModal .load").addClass("d-none");
            },
            success: function(response) {
                if (response) {
                    showSweetAlert('Thank you for your valuable feedback!', 'Success!');
                    reset_form();
                }
            },
            error: function() {
                $(this).prop("disabled", false);
                $(this).removeClass("disabled");
            }
        });
    });

    $('#feedback_images').on('change', function() {
        $("#feedback-submit").addClass("disabled");
        $("#feedback-submit").attr("disabled", true);

        attachFeedbackImages(this, $(this).attr('preview-at'), $(this).attr('form-id'));
    });
});

function reset_form() {
    $('#feedbackModal').modal('hide');
    $("#feedback-form")[0].reset();

    $('.scr-td:eq(10)').click();
    $('.scr-winwin-td:eq(10)').click();

    var form = $("#feedback-form");
    form.find("td.active").removeClass("active box arrow-bottom")
    form.find(".tab-pane.active").removeClass("active");
    form.find(".tab-content.tab-bg").addClass("d-none");

    tinymce.get("feedback_desc").setContent("");

    $('ul.feedback_icons').empty();
    $('input[name="feedbackimages_url[]"]').remove();

    $('#maincat').val('');
    $('#subcat').val('');
    $('#rating').val('');
    $('#winwinrating').val('');
}

// upload, attach, and show image previews
function attachFeedbackImages(input, placeToInsertImagePreview, formId) {
	var promises = [];

	if (input.files) {
		addImageLoader($(placeToInsertImagePreview));

		var filesAmount = input.files.length;
		for (i = 0; i < filesAmount; i++) {
			fileTypes = input.files[i].type;

			data = new FormData();
			data.append('file', input.files[i]);

			var ajax = imageUpload(data).done(function(data) {
				if (data != 'error') {
					$('#'+formId).append("<input type='hidden' name='feedback_images_url[]' value='" + data + "'>");

					if ($.inArray(fileTypes, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']) == -1) {
						showSweetAlert("Not a valid image, only JPEG , PNG, or GIF allowed", 'Whoops!', 'error');
					}
				} else {
					showSweetAlert("Could not upload images!", 'Whoops!', 'error');
				}
			});

			var reader = new FileReader();
			reader.onload = function(event) {
				$(placeToInsertImagePreview).append("<li style='background-image:url(" + event.target.result + ")'><i title='Delete' class='fa fa-remove igmrem' aria-hidden='true' style='margin-left:50px'></i></li>");
                $('.igmrem').on('click', function() {
                    $(this).parent("li").remove();
                });
			}

			reader.readAsDataURL(input.files[i]);

			promises.push(ajax);
		}

		$.when.apply(null, promises).done(function(){
			$(placeToInsertImagePreview).parent().find(".loader").hide();

            $("#feedback-submit").removeClass("disabled");
            $("#feedback-submit").attr("disabled", false);
		});
  	}
};