(function ($) {
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/the7-woocommerce-product-review.default", function ($widget, $) {
                $(document).ready(function () {
                    /*$widget.find("#rating").each(function () {
                        $(this).trigger('init');
                    });*/

                    var $widget_prod_comments = $widget.find('.the7-elementor-product-comments');
                    if ($widget_prod_comments.find('.comment-form-rating .stars').length > 1) {
                        $widget_prod_comments.find('.comment-respond .stars').not(':first').remove()
                    }

                    var $widget_stars = $widget.find('.stars a');
                    if ($widget_stars.length) {
                        if ($widget_stars.length > 5) {
                            $widget_stars.slice(5, $widget_stars.length).remove();
                            $widget_stars = $widget.find('.stars a');
                        }
                        $widget.find('.stars span').append($widget_stars.get().reverse());
                    } else {
                        $widget.find('#rating')
                            .hide()
                            .before(
                                '<p class="stars">\
                                    <span>\
                                        <a class="star-5" href="#">5</a>\
                                        <a class="star-4" href="#">4</a>\
                                        <a class="star-3" href="#">3</a>\
                                        <a class="star-2" href="#">2</a>\
                                        <a class="star-1" href="#">1</a>\
                                    </span>\
                                </p>'
                            );
                    }

                    $widget_stars.on('click', function () {
                        var $star = $(this),
                            $rating = $(this).closest('#respond').find('#rating'),
                            $container = $(this).closest('.stars');

                        $rating.val($star.text());
                        $star.siblings('a').removeClass('active');
                        $star.addClass('active');
                        $container.addClass('selected');

                        return false;
                    });
                })
            }
        );
    });
})(jQuery);