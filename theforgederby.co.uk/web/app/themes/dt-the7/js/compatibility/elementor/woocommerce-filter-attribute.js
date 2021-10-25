jQuery(document).ready(function ($) {
    $.productFilterAttr = function (el) {
        var $widget = $(el);
        var $filter_scrollbar = $widget.find('.the7-product-filter.filter-navigation-scroll .filter-container');
        methods = {};
        $widget.vars = {
            toogleSpeed: 250,
            animationSpeed: 150,
            fadeIn: {opacity: 1},
            fadeOut: {opacity: 0}
        };
        // Store a reference to the object
        $.data(el, "productFilterAttr", $widget);
        // Private methods
        methods = {
            init: function () {
                methods.updateScroll();
                $widget.on("click", ".filter-show-more", function (e) {
                    $(this).addClass("hidden").parents(".filter-container").find(".filter-nav li:not(.show)").css($widget.vars.fadeOut).slideDown($widget.vars.toogleSpeed).animate(
                        $widget.vars.fadeIn,
                        {
                            duration: $widget.vars.animationSpeed,
                            queue: false
                        }
                    );
                });
                $widget.find('.the7-product-filter.collapsible').on("click", ".filter-header", function (e) {
                    var $this = $(this),
                        $filter = $this.parent('.the7-product-filter'),
                        $filterCont = $filter.find('.filter-container');
                    if ($filter.hasClass('closed')) {
                        methods.updateScroll();
                        $filterCont.css($widget.vars.fadeOut).slideDown($widget.vars.toogleSpeed).animate(
                            $widget.vars.fadeIn,
                            {
                                duration: $widget.vars.animationSpeed,
                                queue: false,
                                /*complete: function () {
                                    $widget.refresh();
                                }*/
                            }
                        );
                    } else {
                        $filterCont.css($widget.vars.fadeOut).slideUp($widget.vars.toogleSpeed);
                    }
                    $filter.toggleClass('closed');
                });

                $widget.find(".filter-nav-item-container").on("click", function (e) {
                    $(this).find("a")[0].click();
                });

                $filter_scrollbar.bind("mousewheel", function (e, delta) {
                    var dlt = e.deltaY || delta;
                    dlt = dlt < 0 ? -1 : 1;
                    var scrollTop = $(this).scrollTop();
                    var scrollInside = true;
                    if (dlt === -1 && scrollTop + $(this).innerHeight() >= this.scrollHeight) {
                        scrollInside = false;
                    } else if (dlt === 1 && scrollTop <= 0) {
                        scrollInside = false;
                    }
                    if (scrollInside) {
                        e.stopImmediatePropagation();
                    }
                });

                if (typeof dtGlobals != 'undefined') {
                    dtGlobals.addOnloadEvent(function () {
                        $widget.find('.the7-product-filter').addClass("animate");
                    });
                }
            },
            updateScroll: function () {
                if ($widget.find('.the7-product-filter').hasClass('collapsible') && $filter_scrollbar.length && typeof window.the7GetHiddenHeight === "function") {
                    if (window.the7GetHiddenHeight($filter_scrollbar, '') < window.the7GetHiddenHeight($filter_scrollbar, '> .filter-nav')) {
                        $filter_scrollbar.addClass("show-scroll");
                    } else {
                        $filter_scrollbar.removeClass(("show-scroll"));
                    }
                }
            }
        };
        //global functions
        $widget.refresh = function () {
            methods.updateScroll();
        };

        methods.init();
    };

    $.fn.productFilterAttr = function () {
        return this.each(function () {
            if ($(this).data('productFilterAttr') !== undefined) {
                $(this).removeData("productFilterAttr")
            }
            new $.productFilterAttr(this);
        });
    };
});
(function ($) {
    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/the7-woocommerce-filter-attribute.default", function ($widget, $) {
            $(document).ready(function () {
                $widget.productFilterAttr();
            })
        });

        elementorFrontend.hooks.addAction("frontend/element_ready/the7-woocommerce-filter-active.default", function ($widget, $) {
            $(document).ready(function () {
                $widget.productFilterAttr();
            })
        });

        elementorEditorAddOnChangeHandler("the7-woocommerce-filter-attribute:navigation_max_height", refresh);
        elementorEditorAddOnChangeHandler("the7-woocommerce-filter-attribute:navigation_max_height_tablet", refresh);
        elementorEditorAddOnChangeHandler("the7-woocommerce-filter-attribute:navigation_max_height_mobile", refresh);
        var refreshTimeout;

        function refresh(controlView, widgetView) {
            clearTimeout(refreshTimeout);
            var $widget = window.jQuery(widgetView.$el);
            var filterData = $widget.data('productFilterAttr');
            if (typeof filterData !== 'undefined') {
                refreshTimeout = setTimeout(function () {
                    filterData.refresh();
                }, 500);
            }
        }

        function elementorEditorAddOnChangeHandler(widgetType, handler) {
            widgetType = widgetType ? ":" + widgetType : "";
            if (typeof elementor !== 'undefined') {
                elementor.channels.editor.on("change" + widgetType, handler);
            }
        }
    });
})(jQuery);