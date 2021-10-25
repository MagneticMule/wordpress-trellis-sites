(function ($) {
    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {
         var carouselRefreshTimeout;
        elementorEditorAddOnChangeHandler("the7-woocommerce-product-data-tabs:type", refreshTabs);
        elementorEditorAddOnChangeHandler("the7-woocommerce-product-data-tabs:type_tablet", refreshTabs);
        elementorEditorAddOnChangeHandler("the7-woocommerce-product-data-tabs:type_mobile", refreshTabs);
        function refreshTabs(controlView, widgetView) {
            clearTimeout(carouselRefreshTimeout);
            carouselRefreshTimeout = setTimeout(function () {
                window.jQuery(widgetView.$el).find(".wc-tabs li").removeClass('active');
                window.jQuery(widgetView.$el).find(".wc-tabs li:visible").first().addClass('active');
                window.jQuery(widgetView.$el).find(".wc-tabs li").first().addClass( 'active' );
                if( window.jQuery(widgetView.$el).find(".wc-tabs").css('display') !== "none"){
                    window.jQuery(widgetView.$el).find(".wc-tabs li").removeClass('active');
                    window.jQuery(widgetView.$el).find(".wc-tabs li:visible").first().addClass( 'active' );
                }
            }, 500);
        }
        elementorFrontend.hooks.addAction("frontend/element_ready/the7-woocommerce-product-data-tabs.default", function ($scope, $) {
            $( document ).ready(function() {
                $scope.find(".wc-tabs-wrapper, .woocommerce-tabs, #rating").each(function () {
                 $(this).trigger( 'init' );
                });
                var active_tab =  $scope.find(".wc-tabs li:visible").first().attr('aria-controls');
                $scope.find(".wc-tabs li").first().addClass( 'active' );
                if($scope.find(".wc-tabs").css('display') !== "none"){
                    $scope.find(".wc-tabs li").removeClass('active');
                    $scope.find(".wc-tabs li:visible").first().addClass( 'active' );
                }
                $scope.find('#' + active_tab).css( 'display', 'block' );
                if($scope.find('.comment-form-rating .stars').length > 1){
                    $scope.find('.comment-respond .stars').not(':first').remove()
                }
                var $stars = $scope.find('.stars a');

                if($stars.length){
                    if($stars.length > 5){
                        $stars.slice(0, 5).remove();
                    }
                    $scope.find('.stars span').append($stars.get().reverse());
                }else{
                    $scope.find( ' #rating' )
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

                $scope.find('.dt-tab-accordion-title').each(function(){
                    var accordion = $(this);
                    if(accordion.parents().hasClass("hide-tab-description") && !accordion.parents().hasClass("hide-tab-additional")){
                         $("#tab-title-additional_information.dt-tab-accordion-title").addClass('first');
                    }else if((accordion.parents().hasClass("hide-tab-additional") && !accordion.parents().hasClass("hide-tab-description")) || (accordion.parents().hasClass("hide-tab-additional") && accordion.parents().hasClass("hide-tab-description")) ){
                         $("#tab-title-reviews.dt-tab-accordion-title").addClass('first');
                    }else{
                        $("#tab-title-description.dt-tab-accordion-title").addClass('first');
                    }
                    $(".dt-tab-accordion-title.first").next().slideDown('fast');
                    accordion.on("click", function(e){
                        var $this = $(this),
                            $thisNext = $this.next();
                        $(".dt-tab-accordion-title").removeClass('active');
                        $this.addClass('active');
                        $(".woocommerce-Tabs-panel").not($thisNext).slideUp('fast');
                        $thisNext.slideDown('fast');
                    });
                });

                active_tab_accordion =   $scope.find('.dt-tab-accordion-title').parents().find(".woocommerce-Tabs-panel:visible").attr('id');
               
                $scope.find('.dt-tab-accordion-title').removeClass( 'active' );
                $scope.find('.dt-tab-accordion-title[aria-controls=' + active_tab_accordion + ']').addClass('active');
            });
        });
    });
    function elementorEditorAddOnChangeHandler(widgetType, handler) {
        widgetType = widgetType ? ":" + widgetType : "";
        elementor.channels.editor.on("change" + widgetType, handler);
    }
})(jQuery);