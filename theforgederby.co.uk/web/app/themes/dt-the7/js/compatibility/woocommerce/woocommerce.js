jQuery(document).ready(function($) {
    var $body = $("body"),
        $window = $(window),
        $page = $("#page");

    $body.on("wc_cart_button_updated", function(event, $button) {
        if($button.hasClass('elementor-button')){
            $button.next().addClass($button.attr("class")).removeClass("added add_to_cart_button ajax_add_to_cart");
        }
        if(!$button.siblings(".added_to_cart.wc-forward").find('.popup-icon').length > 0 && $button.parents().hasClass("woo-buttons-on-img")){
            $button.siblings(".added_to_cart.wc-forward").wrapInner('<span class="filter-popup"></span>').append($button.find('i.popup-icon'));
        }
    } );
    
    $.fn.touchWooHoverImage = function() {
        return this.each(function() {
            var $img = $(this);
            if ($img.hasClass("woo-ready")) {
                return;
            }
            var origY,origX;
            $body.on("touchend", function(e) {
                $(".mobile-true .cart-btn-on-hover .woo-buttons-on-img").removeClass("is-clicked");
            });
            var $this = $(this);
            $this.on("touchstart", function(e) {
                origY = e.originalEvent.touches[0].pageY;
                origX = e.originalEvent.touches[0].pageX;
            });
            $this.on("touchend", function(e) {
                var touchEX = e.originalEvent.changedTouches[0].pageX,
                    touchEY = e.originalEvent.changedTouches[0].pageY;
                if( origY == touchEY || origX == touchEX ){
                    if ($this.hasClass("is-clicked")) {
                        if(!$(e.target).parents().hasClass("woo-buttons")){
                            if($(e.target).parent().hasClass("woo-buttons-on-img")){
                                $(e.target).trigger('click');
                            }else{
                                window.location.href = $this.find("a").first().attr("href");
                            }
                        }
                    } else {
                        if(!$(e.target).parents().hasClass("woo-buttons")){
                            e.preventDefault();
                            $(".mobile-true .cart-btn-on-hover .woo-buttons-on-img").removeClass("is-clicked");
                            $this.addClass("is-clicked");
                            return false;
                        }
                    }
                }
            });

            $img.addClass("woo-ready");
        });
    };


    $.fn.touchWooHoverBtn = function() {
        return this.each(function() {

            $body.on("touchend", function(e) {
                $(".mobile-true .cart-btn-on-img .woo-buttons").removeClass("is-clicked");
            });

            var $this = $(this);
            var origY,origX;
            if ($this.hasClass("woo-ready")) {
                return;
            }
            $this.on("touchstart", function(e) {
                origY = e.originalEvent.touches[0].pageY;
                origX = e.originalEvent.touches[0].pageX;
            });
            $this.on("touchend", function(e) {
                var touchEX = e.originalEvent.changedTouches[0].pageX,
                    touchEY = e.originalEvent.changedTouches[0].pageY;
                if( origY == touchEY || origX == touchEX ){
                   // if ($this.hasClass("is-clicked") || $this.find("a.added_to_cart").length > 0) {
                        if($(e.target).parents().hasClass("woo-buttons")){
                            e.preventDefault();
                            $(e.target).trigger('click');
                        }else{
                            window.location.href = $this.find("a").first().attr("href");
                        }
                    // } else {
                    //     e.preventDefault();
                    //     $(".mobile-true .cart-btn-on-img .woo-buttons").removeClass("is-clicked");
                    //     $this.addClass("is-clicked");
                    //     return false;
                    // }
                }
            });

            $this.addClass("woo-ready");
        });
    };
    //add mobile hovers
    $context = $("html.mobile-true");
    $(".cart-btn-on-hover .woo-buttons-on-img", $context).touchWooHoverImage();
    $(".cart-btn-on-img .woo-buttons", $context).touchWooHoverBtn();

    /* #Header elements
        ================================================== */
    $(".woocommerce-billing-fields").find("input[autofocus='autofocus']").blur();
    $(".woocom-project").each(function(){
        var $this = $(this);
        if($this.find("img.show-on-hover").length > 0){
            $this.find("img").first().addClass("hide-on-hover");
        }
    });

    var cartTimeoutShow,
        cartTimeoutHide;

    function showSubCart(elem, $dropCart){
        if (elem.hasClass("dt-hovered")){
            return false;
        }
        dtGlobals.isHovering = true;
        elem.addClass("dt-hovered");
        if ($page.width() - ($dropCart.offset().left - $page.offset().left) - $dropCart.width() < 0) {
            $dropCart.addClass("right-overflow");
        }
        /*Bottom overflow menu*/
        // if ($window.height() - ($dropCart.offset().top - dtGlobals.winScrollTop) - $dropCart.innerHeight() < 0 && $dropCart.innerHeight() <= $window.height()) {
        //     $dropCart.addClass("bottom-overflow");
        // };
        if(elem.parents(".dt-mobile-header").length > 0) {
            $dropCart.css({
                top: elem.position().top - 13 - $dropCart.height()
            });
        }
        /*move button to top if cart height is bigger then window*/
        if ($dropCart.height()  > ($window.height() - $dropCart.position().top)) {
            $dropCart.addClass("show-top-buttons");
        }

        /*hide search*/
        var $header = $(".masthead, .dt-mobile-header");
        $(".searchform .submit", $header).removeClass("act");
        $(".mini-search").removeClass("act");
        $(".mini-search.popup-search .popup-search-wrap", $header).stop().animate({
            "opacity": 0
        }, 150, function() {
            $(this).css("visibility", "hidden");
        });

        clearTimeout(cartTimeoutShow);
        clearTimeout(cartTimeoutHide);

        cartTimeoutShow = setTimeout(function() {
            if(elem.hasClass("dt-hovered")){
                $dropCart.stop().css("visibility", "visible").animate({
                    "opacity": 1
                }, 150);
            }
        }, 100);
        return true;
    }

    function hideSubCart(elem, $dropCart){
        if (!elem.hasClass("dt-hovered")){
            return false;
        }
        elem.removeClass("dt-hovered");

        clearTimeout(cartTimeoutShow);
        clearTimeout(cartTimeoutHide);

        cartTimeoutHide = setTimeout(function() {
            if(!elem.hasClass("dt-hovered")){
                $dropCart.stop().animate({
                    "opacity": 0
                }, 150, function() {
                    $(this).css("visibility", "hidden");
                });
                setTimeout(function() {
                    if(!elem.hasClass("dt-hovered")){
                        $dropCart.removeClass("right-overflow");
                        $dropCart.removeClass("bottom-overflow");
                        /*move button to top if cart height is bigger then window*/

                        $dropCart.removeClass("show-top-buttons");

                    }
                }, 400);
            }
        }, 150);
        elem.removeClass("dt-clicked");
        dtGlobals.isHovering = false;
        return true;
    }

    /*!Shopping cart top bar*/
    function setupMiniCart() {
        $(".mobile-false .shopping-cart.show-sub-cart").each(function(){
            var $this = $(this),
                $dropCart = $this.children('.shopping-cart-wrap'),
                showOnClick = $this.hasClass("show-on-click"),
                action = 'mouseenter tap';

            if(showOnClick){
                action = 'click tap';
            }
            $this.on(action, function(e) {
                if(e.type === "click" || e.type === "tap") {
                    var $target = $(e.target);
                    var preventEvent = true;
                    if (showOnClick && $this.hasClass("dt-hovered") && $target.closest($dropCart).length) {
                        preventEvent = false;
                    }
                    if (preventEvent) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    if (showOnClick && !$target.closest($dropCart).length) {
                        if (hideSubCart($this, $dropCart)) {
                            return;
                        }
                    }
                }
                showSubCart($this, $dropCart);
            });
            if (showOnClick){
                //close widget on click outside container
                $body.on("click", function(e) {
                    var $target = $(e.target);
                    if(!$target.closest($dropCart).length){
                        hideSubCart($this, $dropCart);
                    }
                });
                //also close on search form,  $body.on("click") does not work because search button blocking event bubbling
                var $header = $(".masthead, .dt-mobile-header");
                $(".searchform .submit", $header).on("click", function(e) {
                    hideSubCart($this, $dropCart);
                });
            }
            else {
                $this.on("mouseleave", function (e) {
                    hideSubCart($this, $dropCart);
                });
            }
        });
    }
    $(document.body).on('wc_fragments_loaded wc_fragments_refreshed', function () {
        setupMiniCart();
        $(".mobile-true .shopping-cart.show-sub-cart").touchDropdownCart();
    });

    $.fn.touchDropdownCart = function() {
        return this.each(function() {
            var $item = $(this);
            if ($item.hasClass("item-ready")) {
                return;
            }

            $body.on("touchend", function(e) {
                $(".mobile-true .shopping-cart.show-sub-cart .wc-ico-cart").removeClass("is-clicked");
                hideSubCart($('.wc-ico-cart'), $('.shopping-cart-wrap'));
            });
            var $this = $(this).find('.wc-ico-cart'),
                $thisTarget = $this.attr("target") ? $this.attr("target") : "_self",
                $dropCart = $item.children('.shopping-cart-wrap');
            hideSubCart($this, $dropCart);
            $this.on("touchstart", function(e) {
                origY = e.originalEvent.touches[0].pageY;
                origX = e.originalEvent.touches[0].pageX;
            });
            $this.on("touchend", function(e) {
                var touchEX = e.originalEvent.changedTouches[0].pageX,
                    touchEY = e.originalEvent.changedTouches[0].pageY;
                if( origY == touchEY || origX == touchEX ){

                    if ($this.hasClass("is-clicked")) {
                        hideSubCart($this, $dropCart);
                        window.open($this.attr("href"), $thisTarget);
                    } else {
                        e.preventDefault();
                        showSubCart($this, $dropCart);
                        $(".mobile-true .shopping-cart.show-sub-cart .wc-ico-cart").removeClass("is-clicked");
                        $this.addClass("is-clicked");
                        return false;
                    }
                }
            });

            //$item.addClass("item-ready");
        });
    };

    // EDD cart ajax handler.
    $( document.body ).on( 'edd_cart_item_removed edd_cart_item_added', function( event, response ) {
        var data = {
            action:		'the7_edd_cart_micro_widget',
        };

        xhr = $.ajax({
            type:		'POST',
            url:		dtLocal.ajaxurl,
            data:		data,
            success:	function( response ) {
                $('.edd-shopping-cart').replaceWith( $(response) );
                setupMiniCart();
                $('.mobile-true .shopping-cart.show-sub-cart').touchDropdownCart();
                showDropOnAddedToCart('5000');
            }
        });
    });

    //Cart plus/minus btns

    //var $quantity = $('.quantity');
    function quantityPlus() {
        $('.quantity').on('click', '.plus', function(e) {
            var $input = $(this).prev('input.qty'),
                max = parseFloat( $input.attr( 'max' ) ),
                step = parseInt( $input.attr( 'step' ), 10 ),
                the_val = $input.val().length > 0 ? (parseInt( $input.val(), 10 ) + step ) : (0 + step);

            the_val = the_val > max ? max : the_val;
            $input.val(the_val).change();
        });
    }

    quantityPlus();
    function quantityMinus() {
        $('.quantity').on('click', '.minus', function(e) {
            var $input = $(this).next('input.qty'),
                min = parseFloat( $input.attr( 'min' ) ),
                step = parseInt( $input.attr( 'step' ), 10 ),
                the_val = $input.val().length > 0 ? parseInt( $input.val(), 10 ) - step : (0 - step);

            the_val = the_val < 0 ? 0 : the_val;
            the_val = the_val < min ? min : the_val;
            $input.val(the_val).change();
        });
    };
    quantityMinus();
    $(document).ajaxComplete(function(){
        $('.quantity').off('click', '.plus');
        quantityPlus();
        $('.quantity').off('click', '.minus');
        quantityMinus();

    });
    $(document).on("yith-wcan-ajax-filtered", function(i){
        //Layzr init for list layout
        $(".layzr-loading-on, .vc_single_image-img").layzrInitialisation();
        //Layzr init for grid layout
        $('.yit-wcan-container').find('.dt-css-grid').IsoLayzrInitialisation();

        //Masonry layout
        i = 0;
        var $container = $('.yit-wcan-container').find('.wf-container');
        $container.IsoLayzrInitialisation();

        $container.addClass("cont-id-"+i).attr("data-cont-id", i);
        jQuery(window).off("columnsReady");
        $container.off("columnsReady.fixWooIsotope").one("columnsReady.fixWooIsotope.IsoInit", function() {
            $container.addClass("dt-isotope").IsoInitialisation('.iso-item', 'masonry', 400);
            $container.isotope("on", "layoutComplete", function () {
                $container.trigger("IsoReady");
            });
        });

        $container.on("columnsReady.fixWooIsotope.IsoLayout", function() {
            $container.isotope("layout");
        });

        $container.one("columnsReady.fixWooIsotope", function() {
            jQuery(".preload-me", $container).heightHack();
        });

        $container.one("IsoReady", function() {
            $container.IsoLayzrInitialisation();
        });
        jQuery(window).off("debouncedresize.fixWooIsotope").on("debouncedresize.fixWooIsotope", function () {
            $container.simpleCalculateColumns($container);
        }).trigger("debouncedresize.fixWooIsotope");
    });
    $( document ).on( 'ixProductFilterRequestProcessed', function( event ) {
        loadingEffects();
        //Layzr init for list layout
        $(".layzr-loading-on, .vc_single_image-img").layzrInitialisation();
        //Layzr init for grid layout
        //$('.yit-wcan-container').find('.dt-css-grid').IsoLayzrInitialisation();

        //Masonry layout
        i = 0;
        var $container = $('.dt-products.wf-container');

        $container.IsoLayzrInitialisation();

        $container.addClass("cont-id-"+i).attr("data-cont-id", i);
        jQuery(window).off("columnsReady");
        $container.off("columnsReady.fixWooFilter").one("columnsReady.fixWooFilter.IsoInit", function() {
            $container.addClass("dt-isotope").IsoInitialisation('.iso-item', 'masonry', 400);
            $container.isotope("on", "layoutComplete", function () {
                $container.trigger("IsoReady");
            });
        });

        $container.on("columnsReady.fixWooFilter.IsoLayout", function() {
            $container.isotope("layout");
        });

        $container.one("columnsReady.fixWooFilter", function() {
            jQuery(".preload-me", $container).heightHack();
        });

        $container.one("IsoReady", function() {
            $container.IsoLayzrInitialisation();
        });
        jQuery(window).off("debouncedresize.fixWooFilter").on("debouncedresize.fixWooFilter", function () {
            $container.simpleCalculateColumns($container);
            $container.isotope("layout");
        }).trigger("debouncedresize.fixWooFilter");
    } );

    // Fix cart caching problem on page load.
    $( document.body ).on( 'wc_fragments_loaded', function() {
        var $miniCart = $('.shopping-cart');

        if ( ! $miniCart.exists() ) {
            return;
        }

        var local_hash = dtLocal.wcCartFragmentHash;
        var cart_hash = $miniCart.first().attr('data-cart-hash');

        if ( local_hash && local_hash !== cart_hash ) {
            $( document.body ).trigger( 'wc_fragment_refresh' );
        }
    } );

    function showDropOnAddedToCart(t) {
        var $microCart = $(".shopping-cart-wrap");
        $microCart.each(function(){
            var $dropCart = $(this);
            if(!$dropCart.find(".cart_list").hasClass("empty")){
                if ($page.width() - ($dropCart.offset().left - $page.offset().left) - $dropCart.width() < 0) {
                    $dropCart.addClass("right-overflow");
                };
                setTimeout(function() {
                    $dropCart.stop().css("visibility", "visible").animate({
                        "opacity": 1
                    }, 200);
                }, 300);
                clearTimeout(cartTimeoutHide);

                cartTimeoutHide = setTimeout(function() {
                    $microCart.stop().animate({
                        "opacity": 0
                    }, 200, function() {
                        $microCart.css("visibility", "hidden");
                        $microCart.removeClass("right-overflow");
                    });

                }, t);
            }
        });
    }

    var addedToCart = !!$("span.added-to-cart").length;
    $body.on( 'adding_to_cart', function() {
        addedToCart = true;
    });
    $body.on( 'wc_fragments_loaded wc_fragments_refreshed', function() {
        if (addedToCart) {
            addedToCart = false;
            showDropOnAddedToCart("5000");
        }
    } );
    $body.on( 'wc_fragments_loaded wc_fragments_refreshed update_checkout checkout_error init_add_payment_method', function() {
        $('.woocommerce-error, .woocommerce-info, .woocommerce-message').each(function(){
            var $this = $(this);
            $this.find(".close-message").on('click', function(){
                $(this).parent().addClass('hide-message');
            })
        })
    });

    /* #collapse wc sidebar
 ================================================== */
    var sidebarOverlayClass =".mobile-sticky-sidebar-overlay";
    if(!$(sidebarOverlayClass).length > 0){
        var appendText = '<div class="' + sidebarOverlayClass + '"></div>';

        var $pageInner = $(".page-inner");
        if($pageInner.length > 0){
            $pageInner.append(appendText);
        }else{
            $body.append(appendText);
        }
    }
    var $sidebarOverlay = $(sidebarOverlayClass);
    if($('.dt-wc-sidebar-collapse').length > 0){
        $('<div class="wc-sidebar-toggle"></div>').prependTo('#sidebar');
        $('.wc-sidebar-toggle').on('click', function(){
            var $this = $(this);

            if ($this.hasClass("active")){
                $this.removeClass("active");
                $page.removeClass("show-mobile-sidebar").addClass("closed-mobile-sidebar");
                $sidebarOverlay.removeClass("active");
            }else{
                $('.wc-sidebar-toggle').removeClass("active");
                $this.addClass('active');
                $page.addClass("show-mobile-sidebar").removeClass("closed-mobile-sidebar");
                $sidebarOverlay.addClass("active");
            }
        });
        $sidebarOverlay.on("click", function (){
            var $this = $(this);
            if($(this).hasClass("active")){
                $('.wc-sidebar-toggle').removeClass("active");
                $this.removeClass("active");
                $page.removeClass("show-mobile-sidebar").addClass("closed-mobile-sidebar");
                $sidebarOverlay.removeClass("active");
            }
        });
    }
});