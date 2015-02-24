/**
 * Pixel cropper module
 */
pixel = pixel || {};
pixel.cropper = (function(){

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
     * Cropper coordinates
     *
     * @type {{}}
     */
    var coords = {};

    /**
     * JCrop API instance
     *
     * @type {{}}
     */
    var jcrop_api = {};

    /**
     * Load a new cropper instance
     *
     * @param {external:jQuery} element
     * @param [callback]
     *
     * @returns {{}}
     */
    publicObj.load = function(element, callback) {

        debug.info('Loading a new cropper instance');
        // Get the real image dimensions
        var trueWidth  = element.data('trueWidth');
        var trueHeight = element.data('trueHeight');
        debug.debug("Cropper trueWidth: " + trueWidth + ", trueHeight: " + trueHeight);

        pixel.select["imagePreview"].Jcrop({
            //aspectRatio: trueWidth / trueHeight,
            trueSize: [trueWidth, trueHeight],
            onSelect: this.setCoords
        }, function () {
            jcrop_api = this;

            // Call the callback
            if (callback && typeof(callback) === "function") { callback(); }
        });

    }

    /**
     * Destroy a loaded cropper instance
     *
     * @param [callback]
     */
    publicObj.destroy = function(callback) {

        debug.info('Destroying the cropper instance');
        jcrop_api.destroy();

        // Call the callback
        if (callback && typeof(callback) === "function") { callback(); }

    }

    /**
     * Set the cropper coordinates
     *
     * @param {Object} c
     */
    publicObj.setCoords = function(c) {

        debug.info("Setting new cropper coordinates - w: " + c.w + ", h:" + c.h + ", x:" + c.x + ", y:" + c.y);
        coords = c;

    }

    /**
     * Execute a crop request
     *
     * @param {string} path
     * @param {Object} [params]
     * @param [callback]
     */
    publicObj.crop = function(path, params, callback) {

        debug.info('Image crop request called');
        // Crop options / parameters
        var params = params || {};
        options.cropUrl = path || null

        // Process the crop request
        _cropRequest(function(isSuccess, jqXHR) {
            publicObj.destroy();

            // The crop request returned successful
            if (isSuccess) {
                debug.info('Image crop request successful');
                if (callback && typeof(callback) === "function") { callback(true); }
            } else {
                debug.error('Image crop request failed');
                if (callback && typeof(callback) === "function") { callback(false); }
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

    }

    /**
     * Process and callback the results of an Ajax crop request. Callback for {@link pixel.cropper#crop}
     * @private
     *
     * @param callback
     */
    function _cropRequest(callback) {

        // Set our form data
        var data = {
            "_token": pixel.config["csrf_token"],
            coords:   coords
        };

        // Submit the crop request
        $.ajax({
            url: options.cropUrl,
            data: data,
            type: 'POST',
            success: function(jqXHR) { callback(true, jqXHR); },
            error: function(jqXHR) { callback(false, jqXHR); }
        })

    }

    /**
     * Return the public scope
     */
    return publicObj;

}());