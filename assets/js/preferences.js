$(document).ready(function() {
    const originalPreferences = {
        "notification-prefs" : getFormData('#notification-prefs'),
        "email-prefs" : getFormData('#email-prefs')
    }

    function getFormData(id) {
        var data = {};
        $(id + " input[type='checkbox']").each((key, elem) => {
            var sliced_id = $(elem).attr("id").split("_").slice(1).join("_");
            data[sliced_id] = $(elem).val();
        })

        return data;
    }

    function resetForm(id) {
        var form = $("#" + id);
        for (var key in originalPreferences[id]) {
            $(form).find("input[id*='" + key + "']").val(originalPreferences[id][key]);
        }

        form.trigger("reset");
    }

    function isFormDirty(id) {
        var currentPreferences = getFormData(("#" + id));
        for (var key in currentPreferences) {
            if (originalPreferences[id][key] !== currentPreferences[key]) {
                return true;
            }
        }

        return false;
    }

    function toggleButtons(form, toggle) {
        var submit = $(form).find("button.submit");
        var reset = $(form).find("button.reset");

        if (toggle) {
            submit.attr("disabled", false);
            submit.removeClass("disabled");

            reset.attr("disabled", false);
            reset.removeClass("disabled");
            reset.removeClass("d-none");
        } else {
            submit.attr("disabled", true);
            submit.addClass("disabled");

            reset.attr("disabled", true);
            reset.addClass("disabled");
            reset.addClass("d-none");
        }
    }

    $("#prefs form").on("input", function(e) {
        if (this !== e.target) {
            var value = $(e.target).val();
            $(e.target).val((value == 1) ? 0 : 1);
        }

        var dirty = isFormDirty(this.id);
        if (dirty) {
            toggleButtons(this, true);
        } else {
            toggleButtons(this, false);
        }
    });

    $("#prefs .submit").on("click", function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var id = form.attr("id");

        var form_data = getFormData(("#" + id));
        form_data["form"] = id;

        $.ajax({
            url: '/home/updatePreferences',
            method: 'POST',
            data: form_data,
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            beforeSend: function () {
                $('#divLoading').addClass('show');
            },
            complete: function () {
                $('#divLoading').removeClass('show');
            },
            success: function (data) {
                data = JSON.parse(data);

                if (data.status == "success") {
                    originalPreferences[id] = getFormData(("#" + id));
                    toggleButtons(form, false);
                    showSweetAlert('Your preferences have been updated!', 'Great');
                } else {
                    showSweetAlert('We ran into an error updating your preferences. Try again later!', 'Whoops!', 'error');
                }
            }
        });
    });

    $("#prefs .reset").on("click", function(e) {
        e.preventDefault();

        var form = $(this).closest("form");
        resetForm(form.attr("id"));
        toggleButtons(form, false);
    });
});