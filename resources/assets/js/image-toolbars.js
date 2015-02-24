// Image button toolbar - fade on preview hover
pixel.select["previewContainer"].hover(function () {
    debug.debug('Image preview hover event triggered');
    var imageOptionsToolbar = $(".btn-toolbar", this);

    if (imageOptionsToolbar.length) {
        imageOptionsToolbar.toggleClass('in');
    }
})

// Disable the download button temporarily on click
$('.btn.download').on('click', function() {
    debug.info('Image download button clicked');
    var downloadButton = $(this);
    downloadButton.attr("disabled", true);
    setTimeout(function() {
        downloadButton.attr("disabled", false);
    }, 2500);
})

// Process an image deletion request
$("body").on("click", ".image-options .delete", function(e) {
    debug.info('Image delete button clicked');
    e.preventDefault();
    var $this = $(this);
    var deleteKey = $this.data('delete-key');
    var deleteUrl = $this.data('delete-url');
    pixel.image.deleteResource(deleteUrl, deleteKey);
})

// Load the image cropper
$("body").on("click", ".image-options .crop", function(e) {
    debug.info('Image crop button clicked');
    e.stopPropagation();
    e.preventDefault();
    var $this = $(this);

    // @todo: This is a bit of a hacky fix for a bug I don't fully understand yet
    pixel.select["previewContainer"].css('display', 'inherit');

    // Load the cropper insance
    pixel.cropper.load($this, function() {
        pixel.select["previewContainer"].removeAttr('style');
        pixel.select["cropToolbar"].toggleClass('hide');
        pixel.select["imageToolbar"].toggleClass('hide');
    });

})

// Destroy the image cropper
$("body").on("click", "#crop-toolbar .cancel", function(e) {
    debug.info('Image cropping cancelled');
    pixel.cropper.destroy(function() {
        pixel.select["cropToolbar"].toggleClass('hide');
        pixel.select["imageToolbar"].toggleClass('hide');
    });
})

// Submit a crop request
$("body").on("click", "#crop-toolbar .submit", function(e) {
    debug.info('Submitting an image crop request');
    var $this = $(this);
    var cropUrl = $this.data('cropUrl');
    $this.attr("disabled", true);

    pixel.cropper.crop(cropUrl, {}, function(isSuccess) {
        $this.attr("disabled", false);
        pixel.select["cropToolbar"].toggleClass('hide');
        pixel.select["imageToolbar"].toggleClass('hide');

        // @todo: Ajaxify
        if (isSuccess) {
            debug.info('Reloading the page');
            window.location.reload();
        }
    });
})