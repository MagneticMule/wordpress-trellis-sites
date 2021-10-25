(function ($) {

    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {
        elementorEditorAddOnChangeHandler("the7-elements-simple-posts-carousel:item_preserve_ratio", function(controlView, widgetView) {
            window.jQuery(widgetView.$el).the7WidgetImageRatio("refresh");
        });
    });
})(jQuery);