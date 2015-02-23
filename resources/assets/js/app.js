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