/**
 * Pixel image module
 */
pixel = pixel || {};
pixel.image = (function(){

    /**
     * Public object scope
     *
     * @type {{}}
     */
    var publicObj = {};

    /**
     * Global module options
     *
     * @type {{}}
     */
    var options = {};

    /**
     * Loaded image
     *
     * @type {{}}
     */
    var image = {};

    /**
     * Load and process a new image
     *
     * @param inputImage
     *
     * @returns {{}}
     */
    publicObj.load = function(inputImage) {

        // Get the dominant color for this image
        var colorThief = new ColorThief();
        var color = colorThief.getColor(inputImage);

        // Set the image information
        image.color = {
            red:   color[0],
            green: color[1],
            blue:  color[2]
        }

        return image;

    }

    /**
     * Accent page elements using a loaded images RGB color values
     *
     * @param callback
     */
    publicObj.accentuate = function(callback) {

        debug.info('Image accentuation request called');
        // Get the color scheme and theme for this dominant color
        $.get(pixel.config["base_path"] + '/ajax/accentuation', image.color)
            .done(function(data) {
                // Append the custom styling to our <head>
                if (data.styling) {
                    $( data.styling ).appendTo( "head" );
                }

                // Switch our header
                if (data.colorScheme && data.colorScheme.name) {
                    pixel.config["headerSecondary"].addClass(data.colorScheme.name);
                    pixel.config["headerSecondary"].addClass('in');
                }

                // Success callback
                debug.info('Image accentuation successful');
                if (typeof(callback) == "function") callback(true);
            })
            // Error callback
            .error(function() {
                debug.warn('Image accentuation failed');
                if (typeof(callback) == "function") callback(false);
            })

    }

    /**
     * Reset page element changes and clear any loaded images
     */
    publicObj.reset = function() {

        debug.info('Pixel image reset triggered');
        // Reset script attributes
        options = {};
        image   = {};

        // Remove custom styling
        $('head style').remove();
        pixel.config["headerSecondary"].attr('class', 'header secondary fade');

    }

    /**
     * Process a resource deletion request
     * @public
     *
     * @param {string} path
     * @param {string} [key]
     * @param {Object} [params]
     */
    publicObj.deleteResource = function(path, key, params) {

        debug.info('Image deletion request called');
        // Delete options / parameters
        var params = params || {};
        options.warningTitle = params.warningTitle || 'Are you sure?',
        options.warningText  = params.warningText  || 'This image will be permanently deleted. There\'s no undoing this!',
        options.successTitle = params.successTitle || 'Deleted!',
        options.successText  = params.successText  || 'Your image has been successfully deleted.',
        options.cancelTitle  = params.cancelTitle  || 'Canceled',
        options.cancelText   = params.cancelText   || 'Your image has not been deleted.',
        options.deleteUrl    = params.deleteUrl    || document.URL,
        options.deleteKey    = key                 || null

        // Prompt the user for confirmation
        _deletePrompt(function(isConfirmed) {
            // Abort if the user pressed cancel
            if ( ! isConfirmed) {
                debug.info('Image deletion request cancelled');
                swal(options.cancelTitle, options.cancelText, "error");
                return false;
            }

            // Process the delete request
            debug.info('Image deletion request confirmed');
            $(".sweet-alert.visible :button").attr('disabled', true);

            _deleteRequest(function(isSuccess, jqXHR) {
                $(".sweet-alert.visible :button").attr('disabled', false);

                // The DELETE request returned successful
                if (isSuccess) {
                    debug.info('Image deletion request successful');
                    swal({
                        title: options.successTitle,
                        text: options.successText,
                        type: "success",
                        closeOnConfirm: false
                    }, function() {
                        debug.info('Redirecting to the homepage');
                        window.location.href = pixel.config["home_path"];
                    })
                } else {
                    debug.error('Image deletion request failed');
                    swal({
                        title: 'Error',
                        text: jqXHR.responseJSON || 'An unknown error occurred while processing your request',
                        type: "error",
                        confirmButtonClass: 'btn-danger',
                        confirmButtonText: 'Dammit',
                        closeOnConfirm: false
                    }, function() {
                        debug.info('Reloading the page');
                        window.location.reload();
                    })
                }
            });
        });

    }

    /**
     * Prompt the user for confirmation of a delete request. Callback for {@link pixel#deleteResource}
     * @private
     */
    function _deletePrompt(callback) {

        debug.info('Prompting the user for image deletion confirmation');
        // @todo: This is probably a hack
        sweetAlertInitialize();

        swal({
            title: options.warningTitle,
            text:  options.warningText,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
            function (isConfirmed) { callback(isConfirmed) });

    }

    /**
     * Process and callback the results of an Ajax delete request. Callback for {@link pixel#deleteResource}
     * @private
     */
    function _deleteRequest(callback) {

        // Set our form data, including a delete key if available
        var data = {"_token": pixel.config["csrf_token"]};
        if (options.deleteKey) { data.deleteKey = options.deleteKey; }

        // Submit the delete request
        $.ajax({
            url: options.deleteUrl,
            data: data,
            type: 'DELETE',
            success: function(jqXHR) { callback(true, jqXHR); },
            error: function(jqXHR) { callback(false, jqXHR); }
        })

    }

    /**
     * Return the public scope
     */
    return publicObj;

}());