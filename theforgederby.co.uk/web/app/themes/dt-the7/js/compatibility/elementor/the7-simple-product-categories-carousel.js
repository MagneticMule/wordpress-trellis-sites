(function ($) {

    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/the7-simple-product-categories-carousel.default", function($scope, $) {
            if (!$scope.hasClass("preserve-img-ratio-y")) {
                window.the7ApplyWidgetImageRatio($scope);
            }

            // Actually show cells with the fade effect.
            window.the7ProcessEffects($scope.find(".wf-cell:not(.shown)"));
        });
    });
})(jQuery);