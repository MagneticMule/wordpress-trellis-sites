(function ($) {

    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/the7_nav-menu.default", function ($scope, $) {

            $(document).ready(function () {
                $scope.find(".dt-nav-menu").each(function () {
                    $(this).find(" li.act:last > a").addClass("active-item");
                    if($(this).find(".vertical-sub-nav").length <= 0){
                        $(this).parent().addClass('indicator-off');
                    }
                });
                $scope.find(".dt-sub-menu-display-on_click, .dt-sub-menu-display-on_item_click").css('visibility', 'visible');

                $scope.find(".dt-sub-menu-display-on_click li.has-children, .dt-sub-menu-display-on_item_click li.has-children").each(function () {

                    var item = $(this);
                    var itemLink = item.find(" > a");
                    $(this).each(function () {
                        var $this = $(this);

                        $this_sub = $this.find("> .dt-mega-menu-wrap > .vertical-sub-nav");
                        $this_sub.unwrap();
                        var subMenu = $this.find("> .vertical-sub-nav");
                        if ($this.find(".vertical-sub-nav li").hasClass("act")) {
                            $this.addClass("active");
                        }

                        if ($this.find(".vertical-sub-nav li.act").hasClass("act")) {
                            $this.addClass("open-sub");
                            subMenu.css("opacity", "0").stop(true, true).slideDown({}, 250).animate(
                                {opacity: 1},
                                {queue: false, duration: 150}
                            );
                            $(this).find(" > a").addClass("active");
                            subMenu.layzrInitialisation();
                        }

                        if (itemLink.hasClass("not-clickable-item") && $this.parents("nav").hasClass("dt-sub-menu-display-on_item_click")) {
                            var clickItem = itemLink;
                        } else {
                            var clickItem = itemLink.find(" > .next-level-button");
                        }

                        clickItem.on("click", function (e) {
                            if (itemLink.hasClass("not-clickable-item") && itemLink.parents("nav").hasClass("dt-sub-menu-display-on_item_click")) {
                                var $this = $(this);
                            } else {
                                var $this = $(this).parent();
                            }


                            if ($this.hasClass("active")) {
                                subMenu.css("opacity", "0").stop(true, true).slideUp(250, function () {
                                    $(".dt-nav-menu").layzrInitialisation();
                                    subMenu.find("li").removeClass("open-sub");
                                    subMenu.find("a").removeClass("active");
                                });
                                $this.removeClass("active");
                                $this.parent().removeClass("open-sub");
                            } else {
                                $this.parent().siblings().find(" .vertical-sub-nav").css("opacity", "0").stop(true, true).slideUp(250);
                                subMenu.css("opacity", "0").stop(true, true).slideDown({
                                    start: function () {
                                    }
                                }, 250).animate(
                                    {opacity: 1},
                                    {queue: false, duration: 150}
                                );
                                $this.siblings().removeClass("active");
                                $this.addClass("active");
                                $this.parent().siblings().removeClass("open-sub");
                                $this.parent().siblings().find("a").removeClass("active");
                                $this.parent().addClass("open-sub");

                                $(".dt-nav-menu").layzrInitialisation();
                            }
                        })
                    });

                });
            });
        });

        elementorEditorAddOnChangeHandler("the7_nav-menu:submenu_display", function (controlView, widgetView) {
            if (widgetView.model.getSetting("submenu_display") === "on_click") {
                const icon = widgetView.model.getSetting("selected_icon");
                if (!icon.value) {
                    widgetView.model.setSetting("selected_icon", {
                        value: "fas fa-caret-right",
                        library: "fa-solid"
                    });
                    widgetView.renderOnChange(widgetView.model);
                }
            }
        })
    });

})(jQuery);