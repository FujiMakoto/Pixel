Dropzone.options.albumUploadForm = {
    init: function() {
        this.on("sending", function() {
            $(".upload-container .continue").addClass('disabled');
        });
        this.on("queuecomplete", function() {
            $(".upload-container .continue").removeClass('disabled');
        });
    },

    paramName: "image",
    maxFilesize: (pixel.config["max_size"] / 1000) || 256, // MB
    maxFiles: 50,
    acceptedFiles: 'image/*',
    addRemoveLinks: true
};