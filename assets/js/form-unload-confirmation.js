const forms = document.querySelectorAll("form"); 
const forms_clean = [];

window.onbeforeunload = areFormsDirty;
window.onload = function() {
    forms.forEach(function(form) {
        forms_clean.push(new FormData(form));
    });
};

function areFormsDirty(e) {
    for (var i = 0; i < forms.length; i++) {
        var form_clean = forms_clean[i];
        var form_dirty = new FormData(forms[i]);

        var dirty = false;
        for (let [key, value] of form_dirty.entries()) {
            if (value instanceof File) {
                var file1 = form_clean.get(key)
                dirty = (file1.name != value.name);
            } else {
                if (form_clean.get(key) !== value) {
                    if (tinymce.get(key) != undefined) {
                        dirty = (form_clean.get(key).trim() !== tinymce.get(key).getContent().trim());
                    } else if (form_clean.get(key) instanceof String) {
                        dirty = (form_clean.get(key).trim() !== value.trim());
                    } else {
                        dirty = true;
                    }
                }
            }
            
            if (dirty) {
                var msg = "Any changes will be lost.";

                (e || window.event).returnValue = msg;
                return msg;
            }
        }
    }
}