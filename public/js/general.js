$(function () {
    /** Datatable */
    $.fn.dataTable.ext.errMode = 'none'

    /** Back to Top */
    $.scrollUp({
        animation: 'slide',
        scrollText: 'Back to Top',
    })

    /** Internet Detector */
    function toggleAlert () {
        $('#connectionStatus').toggleClass('d-none')
    }
    window.addEventListener('online', toggleAlert)
	window.addEventListener('offline', toggleAlert)

	/** Deactivate Warning */
	$('body').on('click', '#deactivate', function() {
		// prepare letiables
		let id = this.getAttribute('delete_id')

		// show confirm dialog
		bootbox.confirm({
			message: 'This row will be move to trash! Are you sure?',
			size: 'small',
			backdrop: true,
			closeButton: false,
			callback: function (result) {
				if (result) {
					$(`#form_destroy_${id}`).submit()
					loadingModal()
				}
			}
		})
	})
})

/** CSV Export Start */
function escapeRow (value) {
	return '"'+ value +'"'
}

function generateFileTitle (title) {
	let todayDate = moment().format('MMM Do, YYYY hh-mm-ss A')
	return `${title} - ${todayDate}.csv`
}

function exportExcel (data, title) {
	// generate raw csv
	let csvContent = "data:text/csv;charset=utf-8,"
	data.forEach(function(d) {
		csvContent += d.join(",") + "\r\n"
	})
	let encodedUri = encodeURI(csvContent)

	// virtual download link
	let link = document.createElement("a")
	link.setAttribute("href", encodedUri)
	link.setAttribute("download", generateFileTitle(title))
	document.body.appendChild(link)
	link.click()
}
/** CSV Export End */

function thousandSeperator (x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}

function formatSecond(second, format = 'mm:ss') {
	const timeObj = moment.duration({s: second})
	return moment().startOf('day').add(timeObj).format(format)
}

function reloadWithParams(paramJson) {
	// build custom params
	const paramObj = new URLSearchParams(paramJson)
	const params = paramObj.toString()

	// get default url
	let url = window.location.href

	// check params already include
	let prefix = (url.indexOf('?') > -1) ? '&' : '?'

	// add params & reload
	window.location.search += prefix + params
}

/** Design fix on Issue **/

$(document).on("wheel", "input[type=number]", function (e) {
	$(this).blur();
});

var inputBox = document.getElementById("inputPhone");

if(inputBox != null){
	var invalidKeyCode = [69, 38, 40, 189, 187];

	inputBox.addEventListener("keydown", function (e) {
		if (invalidKeyCode.includes(e.which)) {
			e.preventDefault();
		}
	});
}

/** Loading **/
function loadingModal(){
	$('.loading-modal').modal('show');
	setTimeout(function () {
		$('.loading-modal').modal('hide');
	}, 4000);
}
/** end loading **/

/** Design fix on Issue end**/

/** Sharing Warning Modal**/
$('body').on('click', '#sharing', function() {
	// prepare letiables
	let id = this.getAttribute('audio_id')

	// show confirm dialog
	bootbox.confirm({
		message: 'This audio will be public sharing! Are you sure?',
		size: 'small',
		backdrop: true,
		closeButton: false,
		callback: function (result) {
			if (result) {
				$(`#form_sharing_${id}`).submit()
				loadingModal()
			}
		}
	})
})

$('body').on('click', '#private', function() {
	// prepare letiables
	let id = this.getAttribute('audio_id')

	// show confirm dialog
	bootbox.confirm({
		message: 'This audio will be private! Are you sure?',
		size: 'small',
		backdrop: true,
		closeButton: false,
		callback: function (result) {
			if (result) {
				$(`#form_private_${id}`).submit()
				loadingModal()
			}
		}
	})
})
/** Sharing Warning Modal End**/

/** Uploading function **/

$("#audio").on("canplaythrough", function (e) {
    let seconds = e.currentTarget.duration
    let duration = moment.duration(seconds, "seconds")

    let time = ""
    let hours = duration.hours()
    if (hours > 0) {
        time = hours + ":";
	}
	let secText = duration.seconds()
	if(duration.seconds() < 10){
		secText = "0" + duration.seconds()
	}

	time = time + duration.minutes() + ":" + secText
    $('[name=duration]').val(time)
});

$("#inputRecording").change(function (e) {
    let file = e.currentTarget.files[0]

    let fileInput = document.getElementById('inputRecording');
    let filePath = fileInput.value;
    let allowedExtensions = /(\.wav|\.mp3|\.m4a|\.weba|\.mpga)$/i;

    if (file.size > 1073741824) {
        toastr.error('File size is greater than 1GB')
        $(this).val("");
        $('.audio-upload-remove').trigger('click');
    }

    if (!allowedExtensions.exec(filePath)) {
        toastr.error('File type is invalid')
        $(this).val("");
        $('.audio-upload-remove').trigger('click');
    }
    objectUrl = URL.createObjectURL(file)
    $("#audio").prop("src", objectUrl)
});

$("#inputImage").change(function (e) {
    let file = e.currentTarget.files[0]
    let allowedExtensions = ['image/tiff', 'image/pjp', 'image/pjpeg', 'image/jfif', 'image/tif', 'image/gif', 'image/svg', 'image/bmp', 'image/png', 'image/jpeg', 'image/svgz', 'image/webp', 'image/ico', 'image/xbm', 'image/dib', 'video/mp4', 'video/webm', 'video/ogg'];

	if (file.size > 1073741824) {
        toastr.error('File size is greater than 1GB')
        $(this).val("");
        $('.audio-upload-remove').trigger('click');
	}

    if (!allowedExtensions.includes(file.type)) {
        toastr.error('File type is invalid')
        $(this).val("");
        $('.img-upload-remove').trigger('click');
	}
});

$("#inputApk").change(function (e) {
    let file = e.currentTarget.files[0]

    let fileInput = document.getElementById('inputApk');
    let filePath = fileInput.value;
    let allowedExtensions = /(\.apk)$/i;

    if (!allowedExtensions.exec(filePath)) {
        toastr.error('File type is invalid')
        $(this).val("");
        $('.img-upload-remove').trigger('click');
    }
});
/** Uploading function end**/

/* Datatable nav link responsive */
  $('.nav-link').click(function(){
	$($.fn.dataTable.tables( true ) ).css('width', '100%');
	$($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
});
/* Datatable nav link responsive End */

/* Get Request Parameter */
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};
/* End Get Request Parameter */

$('body').on('click', '#forceDelete', function() {
	// prepare letiables
	let id = this.getAttribute('delete_id')

	// show confirm dialog
	bootbox.confirm({
		message: 'This action and it related record(s) will be destory! Are you sure?',
		size: 'small',
		backdrop: true,
		closeButton: false,
		callback: function (result) {
			if (result) {
				$(`#form_forceDelete_${id}`).submit()
				loadingModal()
			}
		}
	})
})


$('body').on('click', '#header-about-admin', function() {
	$.get( '/users/about', function( data ) {
		let description = data.description;
		Swal.fire({
			title: "About !",
			text: "Write something and change.",
			input: 'textarea',
			inputValue: description,
			showCancelButton: true,
			cancelButtonColor: "#dc3545",
			confirmButtonColor: "#28a745",
			confirmButtonText: "Yes, Change it!",
		}).then((result) => {
			if (result.value) {
				let description = result.value;
				$.get( "/users/aboutStore", { description: description } )
					.done(function( data ) {
					Swal.fire({
						position: 'top',
						title: data,
						showConfirmButton: false,
						timer: 1500
					})
				});
			}
		});
	});
})


$('body').on('click', '#header-about-manager', function() {
	$.get( '/users/about', function( data ) {
		let description = data.description;
		Swal.fire(
			'About !',
			description,
		)
	});
})