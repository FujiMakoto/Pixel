// Application debug level
debug.setLevel(5);

// Reveal hidden input text on focus
$("body").on("focus", ".reveal-on-focus", function() {
    debug.info('Input reveal on focus event triggered');
    $(this).val( $(this).data('reveal-text') );
    $(this).removeClass('reveal-on-focus');
})

// Select input text on focus
$("body").on("focus", ".select-on-focus", function() {
    debug.info('Input select on focus event triggered');
    this.select();
});

// Copy input text on double click (This does not currently work)
$("body").on("dblclick", ".copy-on-dblclick", function() {
    debug.info('Image copy on double click event triggered');
    var copyEvent = new ClipboardEvent('copy', { dataType: 'text/plain', data: $(this).val() } );
    document.dispatchEvent(copyEvent);
});

// Process an image deletion request
$("body").on("click", ".image-options .delete", function(e) {
    debug.info('Image delete button clicked');
    e.preventDefault();
    var deleteKey = $(this).data('delete-key');
    var deleteUrl = $(this).data('delete-url');
    pixel.image.deleteResource(deleteUrl, deleteKey);
})

// Image button toolbar - fade on preview hover
$(".image-preview").hover(function () {
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