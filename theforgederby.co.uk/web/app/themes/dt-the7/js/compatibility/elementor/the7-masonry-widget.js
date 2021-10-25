jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction("frontend/element_ready/the7_elements.default", function ($scope) {
        the7ApplyColumns($scope.attr("data-id"), $scope.find(".iso-container"), the7GetElementorMasonryColumnsConfig);
        the7ApplyMasonryWidgetCSSGridFiltering($scope.find(".jquery-filter .dt-css-grid"));
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/the7-wc-products.default", function ($scope) {
        the7ApplyColumns($scope.attr("data-id"), $scope.find(".iso-container"), the7GetElementorMasonryColumnsConfig);
        the7ApplyMasonryWidgetCSSGridFiltering($scope.find(".jquery-filter .dt-css-grid"));
        if (!$scope.hasClass("preserve-img-ratio-y")) {
            window.the7ApplyWidgetImageRatio($scope);
        }
    });
});