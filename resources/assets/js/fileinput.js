/**
 * bootstrap-fileinput - Constructor
 */
pixel.config["imageInput"].fileinput({
    uploadUrl: pixel.config["upload_path"],
    uploadExtraData: {"_token": pixel.config["csrf_token"]},
    //maxFileSize: pixel.config["max_size"],
    browseIcon: '<i class="fa fa-folder-open"></i> ',
    layoutTemplates: {
        icon: '<span class="fa fa-picture-o kv-caption-icon"></span> ',
        main1: '{preview}\n' +
        '<div class="kv-upload-progress hide"></div>\n' +
        '<div class="input-group {class}">\n' +
        '   {caption}\n' +
        '   <div class="input-group-btn">\n' +
        '       {remove}\n' +
        '       {cancel}\n' +
        '       {browse}\n' +
        '   </div>\n' +
        '</div>',
        progress: '<div class="progress">\n' +
        '    <div class="progress-bar progress-bar-success progress-bar-striped accented text-center" role="progressbar" aria-valuenow="{percent}" aria-valuemin="0" aria-valuemax="100" style="width:{percent}%;">\n' +
        '        {percent}%\n' +
        '     </div>\n' +
        '</div>'
    },
    allowedFileTypes: ['image'],
    showPreview: false
});

/**
 * bootstrap-fileinput - Validation error
 */
pixel.config["imageInput"].on('fileuploaderror', function(event, file, previewId, index, reader) {
    debug.error('Image file input validation error');
});

/**
 * bootstrap-fileinput - Image loaded
 */
pixel.config["imageInput"].on('fileloaded', function(event, file, previewId, index, reader) {
    debug.info('Image file input loaded');
    // Render the preview image
    $(pixel.config["imagePreview"]).srcDataUrl(this);

    // Once the preview image has loaded..
    pixel.config["imagePreview"]
        .on('load', function() {
            debug.info('Image input preview loaded');
            // Are we triggering on a reset?
            if (this.src == pixel.config["previewImage"]) {
                return false;
            }

            // Load the color pallet for this image and apply accent styling
            pixel.image.load(pixel.config["imagePreview"][0]);
            pixel.image.accentuate(function () {
                $(pixel.config["imagePreview"]).centerOn();
                pixel.config["imageInput"].fileinput('upload');
            })
        })
        // Image read error, ignore and start upload
        .error(function() {
            debug.error('Failed to load file input preview image');
            pixel.config["imageInput"].fileinput('upload');
        })
});

/**
 * bootstrap-fileinput - Image cleared
 */
pixel.config["imageInput"].on('fileclear', function(event) {
    debug.info('Image file input cleared');
    pixel.config["imagePreview"].attr('src', pixel.config["previewImage"]);
});

/**
 * bootstrap-fileinput - Batch upload success
 */
pixel.config["imageInput"].on('filebatchuploadsuccess', function(event, data, previewId, index) {
    // Make sure we have a response
    if (data['response']) {
        debug.info('Image upload successful');
        var response = data['response'];
    } else {
        debug.error('Image upload appears successful, but no proper response data was returned');
        return false;
    }

    // Redirect if we received a redirect response
    if (response['redirect']) {
        debug.info('Image upload response is a redirect');
        window.location.replace(response['redirect'])
    }

    // Update the body class
    $(".upload-container").addClass('image-show-container').removeClass('upload-container');

    // Replace our upload form with the image details
    if (response['templates'] && response['templates']['imageDetails']) {
        // Tweak our image details template
        var $imageDetails = $(response['templates']['imageDetails']);
        $imageDetails.find("#details-container").addClass('fade');

        // Replace our header text
        pixel.config["secHeaderText"].html(response['header']['text']);
        pixel.config["secHeaderSubtext"].html(response['header']['subtext']);

        // Insert our image toolbar
        pixel.config["imageToolbar"].html(response['templates']['imageToolbar']);

        // Center the users viewport on the preview image
        $(pixel.config["imagePreview"]).centerOn();

        // Fade out the upload form
        pixel.config["uploadForm"].addClass('fade');

        // Wait for the transition effect, then insert our replaced HTML and fade back in
        setTimeout(function() {[]
            pixel.config["uploadForm"].attr('id', 'image-details');
            pixel.config["uploadForm"].html($imageDetails);
            pixel.config["uploadForm"].addClass('in');
        }, 200);
    }

    // Update the browsers URL
    if (response['uploadUrl']) {
        window.history.pushState(null, null, response['uploadUrl']);
    }

});

/**
 * bootstrap-fileinput - Batch upload error
 */
pixel.config["imageInput"].on('filebatchuploaderror', function(event, data) {
    debug.error('Image file upload failed');
    swal({
        title: 'Upload Failed',
        text: data.jqXHR.responseJSON || 'An unknown error occurred during your upload.',
        type: "error",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Okay"
    }, function() {
        pixel.config["imagePreview"].attr('src', pixel.config["previewImage"]);
        pixel.config["imageInput"].fileinput('reset');
    });
});


/**
 * Drag and drop upload container
 *
 * @type {*|jQuery|HTMLElement}
 */
var uploadContainer = $('body');

/**
 * Drag and drop upload events
 */
uploadContainer.on('dragenter', function (e)
{
    debug.debug('Drag enter event triggered');
    // Prevent default browser behavior
    e.stopPropagation();
    e.preventDefault();
});

uploadContainer.on('dragover', function (e)
{
    debug.debug('Drag over event triggered');
    // Prevent default browser behavior
    e.stopPropagation();
    e.preventDefault();
});

uploadContainer.on('drop', function (e)
{
    debug.debug('Drag drop event triggered');
    // Prevent default browser behavior
    e.preventDefault();

    // Render the preview image and register the drag and drop upload event
    $(pixel.config["imagePreview"]).srcDataUrl(e.originalEvent.dataTransfer.files);
    pixel.config["imageInput"].fileinput('change', e, 'dragdrop');
});