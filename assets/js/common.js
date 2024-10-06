document.addEventListener('DOMContentLoaded', function() {
    var date=new Date(),
		year = date.getFullYear(),
		text = "&copy; " + year + " WinWinLabs. ALL RIGHTS RESERVED."
		$("#cDate").html(text);
	}, false);

	text_truncate = function(str, length, ending) {
		if (length == null) {
		length = 25;
		}

		if (ending == null) {
		ending = '...';
		}

		if (str.length > length) {
			return str.substring(0, length - ending.length) + ending;
		} else {
			return str;
		}
};

function showTextBoxLength(textBoxId, spanId){
	var maxLength = 500;
	var textlen = maxLength - $('#'+textBoxId).val().length;
	$('#'+spanId).text(textlen);
	$('#'+spanId).css('color','red');
}

$(document).on('click', 'a[href]', (e) => {
	e.preventDefault();
	if (e.target.getAttribute("href")) {
		var url = new URL(e.target.href);
		var target = e.target.target || ((e.ctrlKey || e.shiftKey) ? "_blank" : "_self");
	} else {
		var url = new URL(e.currentTarget.href);
		var target = e.currentTarget.target || ((e.ctrlKey || e.shiftKey) ? "_blank" : "_self");
	}

	if (url.hash != "" || url.href.includes("javascript:")) {
		return;
	}

	// url match host?
	if (url.origin === location.origin) {
		window.open(url.href, target);
	} else {
		showSweetConfirm("The link '" + url.href + "' will open a new tab. Are you sure?", "Please confirm", "warning", function(confirm) {
			if (confirm) {
				window.open(url.href, target);
			}
		}, "Confirm");
	}
});


function addImageLoader(imagePreview) {
	if ($(imagePreview).parent().find(".loader").length <= 0) {
		$('<div class="loader"><div class="imageLoader"></div></div>').insertBefore(imagePreview);
	} else {
		$(imagePreview).parent().find(".loader").css("display", "");
	}
}

function imageUpload(data) {
	return $.ajax({
		url: window.location.origin + "/ajax/uploadImage",
		type: "POST",
		data: data,
		enctype: 'multipart/form-data',
		processData: false, 
		contentType: false
	});
}

// Multiple images preview in browser
function imagePreview(input, placeToInsertImagePreview, formId, name) {
	var promises = [];

	if (input.files && input.files.length == 1) {
		addImageLoader($(placeToInsertImagePreview));

		fileTypes = input.files[0].type;

		data = new FormData();
		data.append('file', input.files[0]);

		var ajax = imageUpload(data);
		ajax.done(function(data) {
			if (data != 'error') {
				// this is a note that image uploading should be refactored sitewide, pls
				if ($('#'+formId+" input[type!='file'][name='" + name + "']").length > 0) {
					$('#'+formId+" input[name='" + name + "']").val(data);
				} else {
					$('#'+formId).append("<input type='hidden' name='" + name+ "' value='" + data + "'>");
				}

				if ($.inArray(fileTypes, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']) == -1) {
					showSweetAlert("Not a valid image, only JPEG , PNG, or GIF allowed", 'Whoops!', 'error');
				}
			} else {
				console.log('Could not upload!');
			}
		});

		var reader = new FileReader();
		reader.onload = function(event) {
			if(formId=='fund-form'){
				$('.gIconPreview-img').css('background-image', 'none');
			}

			$(placeToInsertImagePreview).attr('src', event.target.result);
		}

		reader.readAsDataURL(input.files[0]);

		promises.push(ajax);

		$.when.apply(null, promises).done(function(){
			$(placeToInsertImagePreview).parent().find(".loader").hide();
		});
  	} else {
		showSweetAlert("Only one image allowed!", 'Whoops!', 'error');
	}
};

function setCookie(key, value, expiry) {
	var expires = new Date();
	expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000));
	document.cookie = key + '=' + value + ';expires=' + expires.toUTCString() + "; path=/";
}

function getCookie(key) {
	var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
	return keyValue ? keyValue[2] : null;
}

function eraseCookie(key) {
	var keyValue = getCookie(key);
	setCookie(key, keyValue, '-1');
}   

// script to detect adblocker
function hasAdblock() {
	var a = document.createElement('div');
	a.innerHTML = '&nbsp;';
	a.className = 'ads ad adsbox doubleclick ad-placement carbon-ads adglare';
	a.style = 'width: 1px !important; height: 1px !important; position: absolute !important; left: -5000px !important; top: -5000px !important;';
	var r = false;
	try {
		document.body.appendChild(a);
		var e = document.getElementsByClassName('adsbox')[0];
		if(e.offsetHeight === 0 || e.clientHeight === 0) r = true;
		if(window.getComputedStyle !== undefined) {
			var tmp = window.getComputedStyle(e, null);
			if(tmp && (tmp.getPropertyValue('display') == 'none' || tmp.getPropertyValue('visibility') == 'hidden')) r = true;
		}
		document.body.removeChild(a);
	} catch (e) {}
	return r;
}

function copyToClipboard(text) {
	if (window.clipboardData && window.clipboardData.setData) {
		// IE specific code path to prevent textarea being shown while dialog is visible.
		return clipboardData.setData("Text", text); 

	} else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
		var textarea = document.createElement("textarea");
		textarea.textContent = text;
		textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
		document.body.appendChild(textarea);
		textarea.select();
		try {
			return document.execCommand("copy");  // Security exception may be thrown by some browsers.
		} catch (ex) {
			console.warn("Copy to clipboard failed.", ex);
			return false;
		} finally {
			document.body.removeChild(textarea);
		}
	}
}

function imgError(elem, url) {
	var src = elem.getAttribute("src");
	if (src) {
		elem.onerror = "";
		elem.src = url;
		return true;
	} else {
		elem.src = url;
		return true;
	}
	
}

$(document).ready(function(){
	$('.showTextBoxLength').each(function(idx, textArea){
		showTextBoxLength( $(textArea).attr('id'), $(textArea).attr('spanId') );
	});

	$(".btn-share").on("click", function(e) {
		e.preventDefault();
		copyToClipboard($(this).attr("href"));
		$(".btn-share").attr('title', 'Copied');
	});

	$('.commonImageUpload').on('change', function() {
        imagePreview(this, $(this).attr('preview-at'), $(this).attr('form-id'), $(this).attr('name'));
    });
});

function startCountdown(leftTime, countdown) {
	var countDownDate = new Date(leftTime).getTime();

	var x = setInterval(function() {
		var localTime = new Date();

		// Get the equivalent UTC time
		var utcTimeString = localTime.toLocaleString('en-US', { timeZone: 'UTC' });
		
		var distance = countDownDate - Date.parse(utcTimeString);

		var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		var seconds = Math.floor((distance % (1000 * 60)) / 1000);

		var countdownElement = document.getElementById(countdown);

		if (!leftTime) {
			countdownElement.innerHTML = "Draft";
			return;
		}
		
		if (distance < 0 && leftTime) {
			clearInterval(x);
			countdownElement.innerHTML = "Live";
		} else {
			countdownElement.innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
		}
	}, 1000);
}