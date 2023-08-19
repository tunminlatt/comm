// global variables
let currentFile
let fileIndex
let fileData = []

$(function () {
    // prepare variables
    let inputs = $('input[type=file]').each(function(i) {
        fileData[i] = {
            files: [],
            oldUploadsToDelete: [],
            tagType: this.getAttribute('category'),
            isMultiple: this.hasAttribute('multiple')
        }
    })

    $('[type=file]').change(function() {
        // prepare variables
        fileIndex = inputs.index(this)
        currentFile = fileData[fileIndex]
        currentFile['files'] = this.files

        let previewDiv = $(this).nextAll('.upload-preview')
        let tagType = currentFile['tagType']
        let isMultiple = currentFile['isMultiple']

        // if single upload, move old upload to delete list
        if (!isMultiple) {
            let oldName = previewDiv.find('a').children().attr('fileName')
            removeOldUpload(previewDiv, oldName)
        }

        // remove previous upload
        let type = isMultiple ? 1 : 3
        cleanUploadPreview(previewDiv, type)

        // add new upload
        for (let i = 0; i < currentFile['files'].length; i++) {
            addNewUpload(previewDiv, i)
        }
    })

    $('body').on('click', '.img-upload-remove', function() {
        // prepare variables
        let previewDiv = $(this).parents('.upload-preview')
        let isOldupload = $(this).parent().hasClass('upload-old')
        let uploadObj = $(this).next().children()
        let index = uploadObj.attr('index')
        let name = uploadObj.attr('fileName')

        // remove clicked file
        $(this).parent().parent().remove()

        if (isOldupload) {
            removeOldUpload(previewDiv, name)
        } else {
            removeNewUpload(previewDiv, index)
        }
    })

    $('body').on('click', '.audio-upload-remove', function() {
        // prepare variables
        let previewDiv = $(this).parents('.upload-preview')
        let isOldupload = $(this).parent().hasClass('upload-old')
        let uploadObj = $(this).next().children()
        let index = uploadObj.attr('index')
        let name = uploadObj.attr('fileName')

        // remove clicked file
        $(this).parent().parent().remove()

        if (isOldupload) {
            removeOldUpload(previewDiv, name)
        } else {
            removeNewUpload(previewDiv, index)
        }
    })
})

function previewUploadTemplate (index, name, url, type = 2) { // 1 - old, 2 - new
    let template
    let uploadType = (type == 1) ? 'upload-old' : 'upload-new'
    let tagType = currentFile['tagType']
    if(typeof currentFile['files'][0] !== 'undefined') {
        if (currentFile['files'][0].type.split('/')[0] == 'video') {
            tagType = 'video'
        }
        if (currentFile['files'][0].type.split('/')[0] == 'image') {
            tagType = 'image'
        }
    }

    if (tagType == 'image') {
        template = '\
            <div class="col-md-3">\
                <div class="thumbnail '+ uploadType +'">\
                    <i class="fa fa-minus-circle img-upload-remove" aria-hidden="true"></i>\
                    <a href="'+ url +'" data-fancybox="gallery">\
                        <img src="'+ url +'" index="'+ index +'" fileName="'+ name +'" class="img-responsive img-fit">\
                    </a>\
                </div>\
            </div>'
    } else if (tagType == 'video') {
        template = '\
            <div class="col-md-4">\
                <div class="thumbnail '+ uploadType +'">\
                    <i class="fa fa-minus-circle img-upload-remove" aria-hidden="true"></i>\
                    <video index="'+ index +'" fileName="'+ name +'" style="width: 100%; height: 150px" controls type="video/*">\
                        <source src="'+ url +'">\
                        Your browser does not support the video tag.\
                    </video>\
                </div>\
            </div>'
    } else if (tagType == 'audio') {
        template = '\
            <div class="col-md-2">\
                <div class="thumbnail '+ uploadType +'">\
                    <i class="fa fa-minus-circle audio-upload-remove" aria-hidden="true"></i>\
                    <audio index="'+ index +'" fileName="'+ name +'"  controls preload="none">\
                        <source src="'+ url +'">\
                        Your browser does not support the audio element.\
                    </audio>\
                </div>\
            </div>'
        } else if (tagType == 'pdf') {
            template = '\
                <div class="col-md-2">\
                    <div class="thumbnail '+ uploadType +'">\
                        <i class="fa fa-minus-circle upload-remove" aria-hidden="true"></i>\
                        <a href="'+ url +'" data-fancybox="gallery" data-type="iframe">\
                            <img src="../../images/pdf.svg" index="'+ index +'" fileName="'+ name +'" class="img-responsive img-fit">\
                        </a>\
                    </div>\
                </div>'
    }

    return template
}

function addOldUpload (oldFiles, index = 0) {
    // prepare variables
    currentFile = fileData[index]

    let fileInput = $(`input[type=file]:eq(${index})`)
    let previewDiv = fileInput.nextAll('.upload-preview')
    let oldUploadCountInput = fileInput.nextAll('[name="old_upload_count[]"]')

    oldFiles.forEach((oldFile, i) => {
        let thumbnail = previewUploadTemplate(i, oldFile['name'], oldFile['data'], 1)
        previewDiv.append(thumbnail)
    })

    // add upload count
    oldUploadCountInput.val(oldFiles.length)
}

function addNewUpload (obj, i) {
    // prepare variables
    let windowURL = window.URL || window.webkitURL
    let file = currentFile['files'][i]
    let name = file.name
    let url = windowURL.createObjectURL(file)

    // display preview uploads
    let thumbnail = previewUploadTemplate(i, name, url)
    obj.append(thumbnail)

    // release reference to uploaded file
    windowURL.revokeObjectURL(file)
}

function cleanUploadPreview (obj, type) { // 1 - new, 2 - old, 3 - both
    let uploadObj = '.upload-new, .upload-old'
    if (type == 1) {
        uploadObj = '.upload-new'
    } else if (type == 2) {
        uploadObj = '.upload-old'
    }

    obj.find(uploadObj).parent().remove()
}

function removeOldUpload (obj, name) {
    // prepare variables
    let oldUploadsToDelete = currentFile['oldUploadsToDelete']
    let oldUploadNameInput = obj.prevAll('[name="old_upload_to_delete[]"]')
    let oldUploadCountInput = obj.prevAll('[name="old_upload_count[]"]')

    // list uploads to delete
    oldUploadsToDelete.push(name)
    oldUploadNameInput.val(JSON.stringify(oldUploadsToDelete))

    // reduce upload count
    oldUploadCountInput.val(oldUploadCountInput.val() - 1)
}

function removeNewUpload (obj, index) {
    // prepare variables
    let dt = new DataTransfer()
    let files = currentFile['files']

    // get all files except deleted
    for (let i = 0; i < files.length; i++) {
        if (i != index) {
            dt.items.add(files[i])
        }
    }

    // update file input
    let fileInput = document.getElementsByClassName('form-control-file')[fileIndex]
    fileInput.files = dt.files

    // update in global variable
    currentFile['files'] = dt.files

    // reorder the preview upload index
    obj.find('.upload-new').each(function(j) {
        $(this).find('a').children().attr('index', j)
    })
}