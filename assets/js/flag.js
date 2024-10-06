$(document).ready(function() {
    var reason = $("#flag_reason").text();
    addNewTinyMCE("flag_reason", reason);
    
    function addNewTinyMCE(id, content) {	
        oldContent = content;	
        tinyMCEInitialize(id, content);	
    }

    $('.flag-btn').click(function () {
        $("#fg-btn").attr("disabled", true);

        $("#fg-btn").html('Flagged');
        var g_id = $('#uid').attr('data-id');
        var u_id =  $('#uid').val();
        var f_desc = tinymce.get("flag_reason").getContent();

        $.ajax({
            method: "POST",
            data: {
                gid: g_id,
                uid: u_id,
                desc: f_desc
            },
            url: window.location.origin + '/account/flag/add_flag',
            dataType: "JSON",
            success: function (e) {

                if (e.done == 1) {
                    $('#flagModal').modal('hide');
                    showSweetAlert('Thank you for reporting this game. We appreciate it.', 'Great');
                } 
            }

        });
    });
});