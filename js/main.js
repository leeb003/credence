/**
 *
 * Main javascript functions for the theme
 *
 **/

jQuery(function ($) {  // use $ for jQuery
	"use strict";

	// Hide title tags
	$(document).ready(function() {
    	$('.navbar [title]').removeAttr('title');
	});

    // click enable links with menus, currently top level only (not flyouts)
    $(document).on('click', '.nav-link-href', function(e) {
        var target = $(this).closest('a').attr('href');
        if (target != '#') {
            window.location = target;
        }
    });

	// dropdown slide toggle - check window size to determine hover / click events
	$(document).ready(function() {
		var winIsSmall;
		function testWinSize() {
			winIsSmall = $(window).width() < 992;
			if (winIsSmall) { // reset menu
				$('.navbar .dropdown ul.dropdown-menu [data-toggle=dropdown]').find('.open').removeClass('open');
				$('.nav-menu-secondary .dropdown ul.dropdown-menu [data-toggle=dropdown]').find('.open').removeClass('open');
				$('.dropdown-menu').removeAttr('style');  // Remove styles set by slideUp/Down (display:none)
				
			}
		}

		$(window).on('load resize', testWinSize);

		$('.navbar .dropdown, .nav-menu-secondary .dropdown').hover(function() {
			if(!winIsSmall) {
  				$(this).find('.dropdown-menu').first().stop(true, true).delay(150).slideDown(200);
			}
		}, function() {
			if(!winIsSmall) {
  				$(this).find('.dropdown-menu').first().stop(true, true).delay(150).slideUp(200);
			}
		});
		// submenus
		$('.navbar .dropdown-menu .menu-item, .nav-menu-secondary .dropdown-menu .menu-item').hover(function() {
			if(!winIsSmall) {
				$(this).toggleClass('open');
			}
        });
	});

	// Allow multi menu in bootstrap
	$(document).ready(function(){
		$('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
			event.preventDefault(); 
			event.stopPropagation(); 
			$(this).parent().siblings().removeClass('open');
			$(this).parent().toggleClass('open');
		});
	});

	// scroll to top
	$(document).ready(function() {
		if (credence_globals.totop == 'yes') {
			$(window).scroll(function(){
				if ($(this).scrollTop() > 500) {
					$('.upToTop').addClass('showToTop');
				} else {
					$('.upToTop').removeClass('showToTop');
				}
			});

			$(document).on('click', '.toTop', function() {
        		$('html, body').animate({
            		scrollTop: 0}, 600, 'swing');
        		return false;
    		});
		}
	});

	$(window).load(function() {
		if (credence_globals.site_fadein == 'yes') {
			$('#loader').fadeOut(800, function() {
				$('body').css('overflow', 'visible');
			});
		} else {
			$('#loader').hide();
			$('body').css('overflow', 'visible');
		}
	});

	/****************************************************
	 *
	 * sticky navbar
	 * if enabled in theme options
	 *
	 ***************************************************/
	if (credence_globals.topsticky != 'no') {
		$('.top-holder').waypoint('sticky');
		// only if the small menu is visible
		$('.side-nav-small').waypoint('sticky');
	}

	/****************************************************
	 * 
	 * placeholder for ie 9 and non-supporting browser inputs
	 *
	 ***************************************************/
	$('input, textarea').placeholder();

	/****************************************************
	 * 
	 * bxslider for gallery posts
	 *
	 ***************************************************/
	var sliders = new Array();
	$(document).ready(function () {
		var config = {
			adaptiveHeight: true,
        	auto: 'true',
        	speed: '1000',
        	pause: '8000',
		};
		if ($('.bxslider').length ) {
			$('.bxslider').each( function(i, slider) {
				sliders[i] = $(slider).bxSlider(config);
			});
		}	
	});
	// For Window resize reset bxsliders so it will shrink and grow
	$(window).resize(function(){
		$.each(sliders, function(i, slider) {
   			slider.reloadSlider();
		});
	});

	// Reply to Blog Post Form //
	$('#comments').on('click', '.form-submit #submit', function() {
		var author = $('#author').val();
		var email = $('#email').val();
		var comments = $('#comment').val();
		var loggedIn = $('.logged-in-as').text();
		var error = false;
		var message = '';
		if (!loggedIn) {
			if (author == '') {
				message += '<span><i class="fa fa-warning"></i> Please add your name.</span>';
				error = true;
			} 
			if (email == '' || validEmail(email) == false) {
				message += '<span><i class="fa fa-warning"></i> Please add a valid email address.</span>';
				error = true;
			}
		}
		if (comments == '') {
			message += '<span><i class="fa fa-warning"></i> Please add your comment.</span>';
			error = true;
		}

		if (error) {
			$('.reply-error-holder').html(message).removeClass('hidden');
			return false;
		} else {
			$('.reply-error-holder').addClass('hidden');
		}
	});

	// Validate email
	function validEmail(e) {
    	var filter = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
    	return String(e).search (filter) != -1;
	}

	/******************************************
     *
     * Smooth scrolling for webkit - Depricated
     *
     *****************************************/
	/* 
	$(function () {
		if (credence_globals.chrome_smooth != 'no') {
  			var platform = navigator.platform.toLowerCase();
  			if (platform.indexOf('win') == 0 || platform.indexOf('linux') == 0) {
    			if ($.browser.webkit) {
					if (credence_globals.side_menu == 'yes') {  // Side menu needs different selection
      					$.srSmoothscroll({
							// defaults
    						step: 55,
    						speed: 300,
    						ease: 'swing',
							target: $('.content-holder'),
							container: $('body')
  						})
					} else { // top menus
						$.srSmoothscroll({
                            // defaults
                            step: 55,
                            speed: 300,
                            ease: 'swing',
                            target: $('body'),
                            container: $(window)
                        })
					}

    			}
  			}
		}
	});
	*/
	/******************************************
	 *
	 * Responsive sized videos
	 *
	 *****************************************/
	$(document).ready(function(){
    	$(".post").fitVids();
  	});

	/******************************************
	 *
	 * Scroll Down
	 *
	 *****************************************/
	$(".scrolldown").on("click", function(e) {
		var height = $(".top-content")[0].scrollHeight - 24;
		$("html, body").animate({ scrollTop: height}, 800);
    	return false; 
	});


	
    /******************************************
     *
     * Fancybox
     *
     *****************************************/
	if ($('.fancybox').length) {
    	$('.fancybox').fancybox({
        	// No white border
        	padding:0,
        	tpl: {
            	closeBtn: '<a title="Close" class="fancybox-item fancybox-close myClose" href="javascript:;"></a>',
            	prev: '<a title="Previous" class="fancybox-nav fancybox-prev"><span class="myPrev"></span></a>',
            	next: '<a title="Next" class="fancybox-nav fancybox-next"><span class="myNext"></span></a>'
        	},
        	//make sure fancybox knows we are loading images from wordpress
        	'type': 'image',
        	'autoSize' : true,
        	//lock the background when fancybox is active so weird padding doesn't show up
        	helpers : {
            	title: {
                	type: 'inside'
            	},
            	overlay : {
                	locked : false
            	}
        	}
    	});

    	// Media helper. Group items, disable animations, hide arrows, enable media and button helpers.
    	$('.fancybox-media').fancybox({
        	padding: 0,
        	tpl: {
            	closeBtn: '<a title="Close" class="fancybox-item fancybox-close myClose" href="javascript:;"></a>',
            	prev: '<a title="Previous" class="fancybox-nav fancybox-prev"><span class="myPrev"></span></a>',
            	next: '<a title="Next" class="fancybox-nav fancybox-next"><span class="myNext"></span></a>'
        	},
        	arrows : false,
        	helpers : {
            	title: {
                	type: 'inside'
            	},
            	media : {},
            	buttons : {},
            	overlay : {
                	locked : false
            	}
        	}
    	});

		//don't display loader
    	$.fancybox.showLoading = function () {
        	//console.info('My loading');
    	}
	};

	// fancybox dynamic loaded elements
	$(document).ready(function() {
		$(document).on('mouseenter', '.dynamic-fancybox', function() {
			$(this).fancybox({
				// No white border
            	padding:0,
            	tpl: {
                	closeBtn: '<a title="Close" class="fancybox-item fancybox-close myClose" href="javascript:;"></a>',
                	prev: '<a title="Previous" class="fancybox-nav fancybox-prev"><span class="myPrev"></span></a>',
                	next: '<a title="Next" class="fancybox-nav fancybox-next"><span class="myNext"></span></a>'
            	},
            	//make sure fancybox knows we are loading images from wordpress
            	'type': 'image',
            	'autoSize' : true,
            	//lock the background when fancybox is active so weird padding doesn't show up
            	helpers : {
                	title: {
                    	type: 'inside'
                	},
                	overlay : {
                    	locked : false
                	}
            	}
			});
		});
	});

	/******************************************
     *
     * Woo Commerce functions
     *
     *****************************************/

	/* After added to cart */

	$(document).on('click', '.add_to_cart_button', function() {
		$(this).closest('.item-wrapper').find('.woo-added-notice').fadeIn().delay(3000).fadeOut();
		$('.top-cart').closest('li').addClass('active');
	});

	$(document).on('click', '.top-search', function() {
		$('.top-search-holder .search-form').fadeToggle(200);
	});


	/******************************************
     *
     * Isotope Grids
     *
     *****************************************/

	// blog layouts
	if ($('.blog-grid-full').length ) {	
		$('.blog-grid-full').imagesLoaded(function(){
			var $container = $('.blog-grid-full');
			$container.isotope({
				itemSelector: '.grid-post', 

			});
		});
	};

	// portfolios  - layout call is needed for webkit browsers
	$(function() {
		var $container = $('.portfolio-grid');
		var $iso;
		if ($('.portfolio-grid').length ) {
			$container.imagesLoaded(function(){
           		$iso = $container.isotope({
               		itemSelector: '.portfolio-item, .portfolio-item2',
					getSortData: {
						time: function ( itemElem ) {
							var dataTime = $(itemElem).find('.port-holder').attr('data-time');
							return dataTime;
						},
						name: function (itemElem ) {
							var dataName = $(itemElem).find('.port-holder').attr('data-name');
							return dataName;
						}
					},
           		}).isotope('layout');
       		});
		};

		$('.name-sort').click(function() {
			$('.date-sort').removeClass('active');
			var sortValue = $(this).attr('data-filter');
			$iso.isotope({
				sortBy: sortValue,
			});
			$(this).addClass('active');
			return false;
		});

		// filter items on date
		$('.date-sort').click(function() {
			$('.name-sort').removeClass('active');
			var sortValue = $(this).attr('data-filter');	
			$iso.isotope({
                sortBy: sortValue,
			});
			$(this).addClass('active');
			return false;
		});

		// filter items when filter link is clicked
		$('a.catsort').click(function(){
			$('.port-cats a.active').removeClass('active');
			var selector = $(this).attr('data-filter');
			$iso.isotope({ filter: selector, animationEngine : "css" });
			$(this).addClass('active');
			return false;
		});
	});

	 /* debouncedresize: special jQuery event that happens once after a window resize */
	(function($) {
		var $event = $.event,
			$special,
			resizeTimeout;

		$special = $event.special.debouncedresize = {
			setup: function() {
			$( this ).on( "resize", $special.handler );
		},
		teardown: function() {
			$( this ).off( "resize", $special.handler );
		},
		handler: function( event, execAsap ) {
			// Save the context
			var context = this,
				args = arguments,
				dispatch = function() {
					// set correct event type
					event.type = "debouncedresize";
					$event.dispatch.apply( context, args );
				};

				if ( resizeTimeout ) {
					clearTimeout( resizeTimeout );
				}

				execAsap ? dispatch() : resizeTimeout = setTimeout( dispatch, $special.threshold );
			},
			threshold: 150
		};
	})(jQuery);

	// top menu slide down function
	$(document).ready(function() {
		$('a.top-slide-control').toggle(function(event) {
			event.preventDefault();
			$(this).find('i.fa-angle-up').removeClass('hide');
            $(this).find('i.fa-angle-down').addClass('hide');
			$('#top-slide .top-slide-widget').slideDown();
			if ($('#top-slide').find('.sh-map').length) {
				var topMap = $('#top-slide').find('.sh-map');
    			google.maps.event.trigger(topMap[0], 'resize');        // fixes map display in top slide
			} 
		}, function(event) {
			event.preventDefault();
			$('#top-slide .top-slide-widget').slideUp();
			$(this).find('i.fa-angle-up').addClass('hide');
			$(this).find('i.fa-angle-down').removeClass('hide');
		});
	});

	// Off Canvas Sidebar Toggle
	$(document).ready(function () {
  		$('[data-toggle="offcanvas"]').click(function () {
    		$('.row-offcanvas').toggleClass('active')
			return false;
  		});
	});

	// Login Widget
	jQuery(document).ready(function($) {
		$(".tab_content_login").hide();
		$("ul.tabs_login li:first").addClass("active_login").show();
		$(".tab_content_login:first").show();
		$("ul.tabs_login li").click(function() {
			$("ul.tabs_login li").removeClass("active_login");
			$(this).addClass("active_login");
			$(".tab_content_login").hide();
			var activeTab = $(this).find("a").attr("href");
			$(activeTab).fadeIn();
			return false;
		});
	});

	// Smooth Scroll enable if set
	if (credence_globals.sh_smoothScroll == 'yes') {
		// Remove active links for the non-current hash links - we might need to adjust this...
		$(document).ready(function() {       
			var url = window.location.href;
	      	$('.navbar-nav').find('a').each( function() {           
				if ($(this).attr('href') != url) {               
					$(this).closest('li').removeClass('active');           
				}       
			});   
		});

		// smooth scroll function
		$('a[href*="#"]:not([href="#"])').click(function() {
            if ($(this).closest('ul').hasClass('navbar-nav')
				|| $(this).closest('ul').hasClass('side-nav-menu')) {
                if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
                    || location.hostname == this.hostname) {

                    var target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                    var offsetTot = '';
                    if ($(window).width() > 991) {
                        offsetTot = $('.navbar-default').outerHeight();
                    } else {
                        $('.navbar-collapse').collapse('hide');
                        offsetTot = $('.navbar-header').outerHeight();
                    }
                    if (target.length) {
                        $('html,body').animate({
                            scrollTop: target.offset().top - offsetTot
                        }, 1000);
                        return false;
                    }
                }
            }
        });
		// load in correct position function
		$(window).load(function() {
			if ($('.top-holder').length ) {    // Side menu doesnt have top-holder
				var hash = window.location.hash;
				var headerOffset = top;
				if (hash.length) {
					$(document).scrollTop( $(hash).offset().top - $('.top-holder').outerHeight() ); 
				}
			}
		});
	};

});
