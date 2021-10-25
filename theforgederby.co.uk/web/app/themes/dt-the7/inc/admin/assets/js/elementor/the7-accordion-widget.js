var the7AccordionHandler = function the7AccordionHandler($scope, $) {
	var $the7Accordion = $scope.find(".the7-adv-accordion"),
	$accordionTitle = $scope.find(".the7-accordion-header"),
	$accordionType = $the7Accordion.data("accordion-type"),
	$accordionLoading = $scope.find('.animate-on-loading'),
	$accordionSpeed = 400;

	$accordionTitle.each( function () {
		if ( $(this).hasClass("active-default") ) {
			$(this).addClass("show active elementor-active");
			$(this).parent().addClass("current");
			if ( $accordionLoading.length ) {
				$(this).next().slideDown( $accordionSpeed );
			} else {
				$(this).parent().find('.elementor-tab-content').show();
			}
		} else if ( $(this).hasClass("deactive-default") ) {
			$(this).removeClass("show active elementor-active active-default");
			$(this).parent().removeClass("current");
		}
	});

	$accordionTitle.unbind( "click");
	$accordionTitle.click( function (e) {
		e.preventDefault();
		var $this = $(this);

		if ( $accordionType === "accordion" ) {
			if ( $this.hasClass("show") ) {
				$this.removeClass("show active elementor-active");
				$this.parent().removeClass("current");
				$this.next().slideUp($accordionSpeed);
			} else { 
				$this.parent().parent().find(".the7-accordion-header").removeClass("show active elementor-active");
				$this.parent().removeClass("current");
				$this.parent().parent().find(".elementor-tab-content").slideUp($accordionSpeed);
				$this.toggleClass("show active elementor-active");
				$this.parent().toggleClass("current");
				$this.next().slideToggle($accordionSpeed);
			}
		} else {
			if ($this.hasClass("show")) {
				$this.removeClass("show active elementor-active");
				$this.parent().removeClass("current");
				$this.next().slideUp($accordionSpeed);
			} else {
				$this.addClass("show active elementor-active");
				$this.parent().toggleClass("current");
				$this.next().slideDown($accordionSpeed);
			}
		}
	});
};


jQuery(window).on( "elementor/frontend/init", function () { 
	elementorFrontend.hooks.addAction( 
		"frontend/element_ready/the7-accordion.default", the7AccordionHandler
	);
});