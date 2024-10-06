function showSweetAlert($text='', $title='', $icon='success'){
    if($icon ==''){
        $icon ='success';
    }

    return swal({
        icon: $icon,
        title:  $title,
        text:  $text,
        timer: 3000,
        buttons: false
    });
}

function showSweetAlertForce($text='', $title='', $icon='warning'){
    if($icon ==''){
        $icon ='warning';
    }
    
    return swal({
        icon: $icon,
        title:  $title,
        text:  $text,
        buttons: false,
        closeOnClickOutside: false
    });
}

function showSweetUserConfirm($text='', $title='', $icon='warning', callback=null){
    return swal({
        icon: $icon,
        title:  $title,
        content:  $text,
        buttons: {
            confirm: {
                text: "Continue!",
                value: true,
                visible: true,
                className: "",
                closeModal: true
            }
        },
        closeOnClickOutside: false
    }).then((confirmed) => {
        if (callback) {
            callback(confirmed);
        }
    });
}

function showSweetConfirm($text='', $title='', $icon='warning', callback=null, btn1="Confirm", btn2="Cancel",){
    if($icon ==''){
        $icon ='warning';
    }

    buttons = {};

    if (btn1) {
        buttons.btn1 = {
            text: btn1,
            value: true,
            visible: true,
            className: "",
            closeModal: true
        };
    }

    if (btn2) {
        buttons.btn2 = {
            text: btn2,
            value: false,
            visible: true,
            className: "",
            closeModal: true,
        };
    }
    
    return swal({
        icon: $icon,
        title:  $title,
        text:  $text,
        buttons: buttons,
        closeOnClickOutside: false
    }).then((confirmed) => {
        if (callback) {
            callback(confirmed);
        }
    });
}

function showSweetConfirmWithImages($text='', $title='', $icon='warning', images, callback){
    var content = document.createElement("div");
    content.style.display = "inline-block";

    images.forEach(function(img) {
        var image = document.createElement("img");
        image.className = "galleryth";
        image.setAttribute("src", img);
        
        content.appendChild(image);
    });

    return swal({
        icon: $icon,
        title:  $title,
        text:  $text,
        content: content,
        buttons: {
            confirm: {
                text: "Confirm",
                value: true,
                visible: true,
                className: "",
                closeModal: true
            },
            cancel: {
                text: "Cancel",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
            }
        }
    }).then((confirmed) => {
        callback(confirmed);
    });;
}

const showSweetTimeout = async ($title='', $icon='warning', timeOffset, callback) => {
    var initialTime = Math.floor(300 + (timeOffset / 1000));
    let initialMin = String(Math.floor(initialTime / 60)).padStart(2, '0');
    let initialSec = String(initialTime - (initialMin * 60)).padStart(2, '0');

    var wrapper = document.createElement("div");
    wrapper.innerHTML = "You will be automatically logged out in <span class='timer'>" + (initialMin + ":" + initialSec) + "</span>.";

    var time = initialTime;
    var timer = setInterval(function() {
        time--;
        if (time < 0) {
            clearInterval(timer);
            return;
        }

        let min = String(Math.floor(time / 60)).padStart(2, '0');
        let sec = String(time - (min * 60)).padStart(2, '0');
        $('.swal-content .timer').text(min + ":" + sec);
    }, 1000);

    return await swal({
        icon: $icon,
        title:  $title,
        content:  wrapper,
        timer: (300000 - timeOffset),
        allowOutsideClick: false,
        buttons: {
            confirm: {
                text: "I'm Here!",
                value: true,
                visible: true,
                className: "",
                closeModal: true
            }
        },
        closeOnClickOutside: false
    }).then((confirmed) => {
        clearInterval(timer);
        return callback(confirmed);
    });
}


function showSweetAlertWithBtnAndFormSubmit($text='', $title='', $icon='success', $buttonText, $formID){

if($icon ==''){
	$icon ='success';
}

return swal({
    icon: $icon,
    title:  $title,
    text:  $text,
    buttons: true,
    buttons: $buttonText
}).then((isConfirm) => {
	if (isConfirm)  {
	$('#divLoading').addClass('show');
	$('#'+$formID).get(0).submit();
	}
});


}

function showSweetAlerForAdBlocker($text='', $title='', $icon='success', $buttonText){

    if($icon ==''){
        $icon ='success';
    }
    
    return swal({
        icon: $icon,
        title:  $title,
        text:  $text,
        buttons: true,
        buttons: $buttonText
    }).then((isConfirm) => {
        if (isConfirm)  {
        }
    });
    
    
    }