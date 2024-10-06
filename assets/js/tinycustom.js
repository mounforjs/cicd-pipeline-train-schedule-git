const useDarkMode = !window.matchMedia('(prefers-color-scheme: light)').matches;

const plugins_basic = 'paste autolink advlist lists charmap wordcount';
const toolbar_basic = 'bold italic underline strikethrough | numlist bullist';

const toolbar_normal = 'undo redo | preview | bold italic underline strikethrough | fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor | charmap emoticons | image link';
const plugins_normal = 'preview paste autolink image link charmap advlist lists wordcount imagetools charmap emoticons image';

// global variable to store list of blacklisted words
var restrictedWords = null;

function tinyMCEInitialize(id, content="", maxChar=3000, width="auto", height="300px", toolbarVersion=0) {
    // read list of blacklisted words from the json file
    var jsonPath = window.location.origin + '/assets/words.json' ;
    $.getJSON(jsonPath, function(data){
        restrictedWords = data;
    }).fail(function(){
        console.log("An error has occurred.");
    });

    return tinymce.init({
        selector: 'textarea#'+id,
        init_instance_callback: function(editor) {
            editor.on('PastePostProcess', function(e) {
                tinyMCE.DOM.setAttribs(e.node.childNodes[0], {
                    'width': 'auto',
                    'height': '200px'
                });
            });
        },
        paste_preprocess: function (plugin, args) {
            var editor = tinymce.get(tinymce.activeEditor.id);
            var len = editor.contentDocument.body.innerText.length;
            if (len + args.content.length > editor.settings.max_chars) {
                showSweetAlert('Pasting this exceeds the maximum allowed number of ' + editor.settings.max_chars + ' characters for the input.', "Attention!", "warning");
                args.content = '';
            }
        },
        setup: function(editor) {
    
            editor.on('init', function (e) {
                editor.setContent(content);
            });
        
            var allowedKeys = [8, 13, 16, 17, 18, 20, 33, 34, 35, 36, 37, 38, 39, 40, 46];
                editor.on('keydown', function (e) {
                    if (allowedKeys.indexOf(e.keyCode) != -1) return true;
                    if (tinymce_getContentLength() + 1 > this.settings.max_chars) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                    return true;
                });
    
            editor.on('submit', function(e) {
                e.preventDefault();
                //all your after init logics here.
                tinymce.activeEditor.uploadImages(function(success) {
                    document.forms[0].submit();
                });
            });
    
            var defaultWidth = 'auto';
            var defaultHeight = '200px';
            editor.on('PreInit', function() {
                editor.parser.addNodeFilter('img', function(nodes) {
                    nodes.forEach(function(node) {
                        // If the image has no height or width attributes, then set the default dimensions
                        if (!node.attr('width') && !node.attr('height')) {
                            node.attr('width', defaultWidth);
                            node.attr('height', defaultHeight);
                        }
                    });
                });
            });
    
            editor.on('input', function(e) {
                var regex = new RegExp('\\b(' + restrictedWords.join('|') + ')\\b', 'gi' );
                    
                // Get form content
                var content = editor.getContent({
                    format: 'text'
                });
                    
                var matches = [...content.toLowerCase().matchAll(regex)];
                if (matches.length == 0) {
                    editor.notificationManager.close();
                } else {
                    editor.notificationManager.open({
                        text: 'Contains restricted words!',
                        type: 'error'
                    });
                }
            });
        },
        relative_urls : false,
        remove_script_host : false,
        convert_urls : true,
        document_base_url: location.origin,
        link_default_protocol: location.protocol.slice(0, -1),

        default_link_target: "_blank",
        target_list: [
            {title: 'Same page', value: '_self'},
            {title: 'New page', value: '_blank'}
        ],

        force_br_newlines : true,
        force_p_newlines : false,
        forced_root_block : '',

        width: toString(width),
        height: toString(height),
        plugins: (toolbarVersion == 0) ? plugins_normal : plugins_basic,
        toolbar: (toolbarVersion == 0) ? toolbar_normal : toolbar_basic,
        max_chars: maxChar, // max. allowed chars

        //menus
        branding: false,
        contextmenu: false,
        menubar: false,
        statusbar: true,

        toolbar_mode: 'sliding',
        toolbar_sticky: true,

        //styling
        skin: useDarkMode ? 'oxide-dark' : 'oxide',
        content_css: useDarkMode ? 'dark' : 'default',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',

        submit_patch: true,
        browser_spellcheck: true,
        
        //images
        images_upload_url: window.location.origin + '/tinymce/uploadImage',
        automatic_uploads: false,
        paste_data_images: true,
        image_caption: true,
        image_dimensions: false,
        image_advtab: true,
        imagetools_cors_hosts: ['picsum.photos'],
        image_class_list: [{
                title: 'None',
                value: ''
            },
            {
                title: 'Responsive',
                value: 'img-responsive'
            }
        ],
        file_picker_callback: function(callback, value, meta) {
            /* Provide file and text for the link dialog */
            if (meta.filetype === 'file') {
                callback(location.origin, {
                    text: 'My text'
                });
            }
    
            /* Provide image and alt text for the image dialog */
            if (meta.filetype === 'image') {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
    
                /*
                Note: In modern browsers input[type="file"] is functional without
                even adding it to the DOM, but that might not be the case in some older
                or quirky browsers like IE, so you might want to add it to the DOM
                just in case, and visually hide it. And do not forget do remove it
                once you do not need it anymore.
                */
    
                input.onchange = function() {
                    var file = this.files[0];
    
                    var reader = new FileReader();
                    reader.onload = function() {
                        /*
                        Note: Now we need to register the blob in TinyMCEs image blob
                        registry. In the next release this part hopefully won't be
                        necessary, as we are looking to handle it internally.
                        */
                        var id = 'blobid' + (new Date()).getTime();
                        var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                        var base64 = reader.result.split(',')[1];
                        var blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);
    
                        /* call the callback and populate the Title field with the file name */
                        callback(blobInfo.blobUri(), {
                            title: file.name
                        });
                    };
                    reader.readAsDataURL(file);
                };
    
                input.click();
            }
    
    
            /* Provide alternative source and posted for the media dialog */
            if (meta.filetype === 'media') {
                callback('movie.mp4', {
                    source2: 'alt.ogg',
                    poster: 'https://www.google.com/logos/google.jpg'
                });
            }
        }
    });

    function tinymce_getContentLength() {
        return tinymce.get(tinymce.activeEditor.id).contentDocument.body.innerText.length;
    }
}