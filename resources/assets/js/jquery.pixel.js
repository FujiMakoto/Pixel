/**
 * Pixel - jQuery plugins
 */
(function($){

    /**
     * Center scroll on the selected element
     *
     * @param {number} [speed=1025]
     * @returns {*}
     */
    $.fn.centerOn = function(speed) {
        // Scroll speed
        speed = speed || 1025;

        // Center the users viewport on the selected element
        debug.info("Centering the users viewport on " + this.prop("tagName"));
        var viewport = $('html,body');
        viewport.animate({
            scrollTop: this.offset().top - ( $(window).height() - this.outerHeight(true) ) / 2
        }, speed);

        // Stop the animation if the user scrolls
        viewport.bind("scroll mousedown DOMMouseScroll mousewheel keyup", function(e){
            if ( e.which > 0 || e.type === "mousedown" || e.type === "mousewheel"){
                viewport.stop().unbind('scroll mousedown DOMMouseScroll mousewheel keyup');
            }
        });
    };

    /**
     * Generate a data URL from file input and apply as the src of the selected attribute
     *
     * @param input
     * @returns {*}
     */
    $.fn.srcDataUrl = function(input) {
        debug.info('Generating a data URL for the supplied image');
        // Set up a FileReader instance
        var reader = new FileReader();
        if (input.files && input.files[0]) {
            var file = input.files[0];
        } else if (input[0]) {
            var file = input[0];
        }

        // Render the preview
        var element = this;
        if (file) {
            reader.onload = function (e) {
                element.attr('src', e.target.result);
            }

            reader.readAsDataURL(file);
        }
    };

}(jQuery));