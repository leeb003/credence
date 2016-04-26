/**
 * Style Switcher functions
 *
 **/

jQuery(function ($) { 
    "use strict";

	$(document).ready( function() {
        var currentLayout = $('.container-fluid');
        if ($('.container-fluid').length) {
            $('.ss-layout option[value=wide]').attr('selected','selected');
        } else {
            $('.ss-layout option[value=boxed]').attr('selected','selected');
			showBackgrounds();
        }
    });

	$('#style-switcher').on('change', '.ss-layout', function() {
		var layout = $(this).val();
		if (layout == 'wide') {
			$('.container').removeClass('container').addClass('container-fluid');
            $.waypoints('refresh');
			hideBackgrounds();
			$('.above-nav, .no-above').css({'margin-top': ''});
			checkParallax();
			if ($('.flexslider').length) {
            	$('.flexslider').data('flexslider').resize();
        	}

			// revslider go to next slide after change
			revSliderNext();
			// layerslider trigger
            $('.ls-container').resize();
            $('.ls-nav-prev').trigger('click');

		} else if (layout == 'boxed') {
			$('.container-fluid').removeClass('container-fluid').addClass('container');
			$.waypoints('refresh');
			showBackgrounds();
			$('.above-nav, .no-above').css({'margin-top': ''});
			checkParallax();
			if ($('.flexslider').length) {
            	$('.flexslider').data('flexslider').resize();
        	}	

			// revslider go to next slide after change
			revSliderNext();
			// layerslider trigger
            $('.ls-container').resize();
            $('.ls-nav-prev').trigger('click');
		} else if  (layout == 'boxed-offset') {
			$('.container-fluid').removeClass('container-fluid').addClass('container');
            $.waypoints('refresh');
            showBackgrounds();
			$('.above-nav, .no-above').css({'margin-top': '60px'});
            checkParallax();
            if ($('.flexslider').length) {
                $('.flexslider').data('flexslider').resize();
            }

            // revslider go to next slide after change
            revSliderNext();
            // layerslider trigger
            $('.ls-container').resize();
            $('.ls-nav-prev').trigger('click');
		}

		//window.dispatchEvent(new Event('resize'));  // trigger resize (mostly for isotope) ie11 no likey
		$(window).trigger('resize');
	});	

	$('#style-switcher').on('change', '.ss-header', function() {
		headerColors();
	});

	// header colors
	function headerColors() {
		var color = $('#style-switcher .ss-header').val();
		if (color == 'dark') {
            $('.navbar-bg-col').css('background-color','#333333');
            $('.navbar-default .navbar-nav > li > a, .navbar-default .top-minor-menu > li > a, .navbar-default .nav-menu-secondary > li > a').css('color','#777777');
            $('.dropdown-menu').css('background-color', '#555555');
            $('.dropdown-menu > li a:hover, .dropdown-menu > li > a, .dropdown-menu > .active > a:hover').css('color', '#999999');
            $('.dropdown-menu .new-column-title').css('color', '#999999');
            $('.dropdown-menu').css('color', '#999999');
            $('.dropdown-menu .buttons .button.wc-forward').css('color', '#999999');
        } else {
            $('.navbar-bg-col').css('background-color','#ffffff');
            $('.dropdown-menu').css('background-color', '#ffffff');
            $('.nav .open > a, .nav .open > a:hover, .nav .open > a:focus').css('color', '#444444');
            $('.dropdown-menu > li a:hover, .dropdown-menu > li > a, .dropdown-menu > .active > a:hover').css('color', '#444444');
            $('.dropdown-menu .new-column-title').css('color', '#444444');
            $('.dropdown-menu').css('color', '#444444');
            $('.dropdown-menu .buttons .button.wc-forward').css('color', '#444444');
        }
	}

	// For fbar sizing events
    $(window).resize(function() {
        revSliderNext();
    });

	// Revolution Slider go to next if present
	function revSliderNext() {
		var api;
   		var slider = jQuery('.rev_slider');
   		if(!slider.length) return;
   		var js = slider.parent().last('script').text();
   		api = 'revapi' + js.split('var revapi')[1].charAt(0);
		eval(api).revnext();
	};


    // Parallax sizing for full width and boxed
    function checkParallax() {
        if ($('.container-fluid').length ) {
            $('.sh-parallax').css( "width",($('.container-fluid').width())+'px');
			$('.sh-fixed').css( "width",($('.container-fluid').width())+'px');
			$('.sh-solidbg').css( "width",($('.container-fluid').width())+'px');
			$('.sh-imagebg').css( "width",($('.container-fluid').width())+'px');
			$('.sh-colorbg').css( "width",($('.container-fluid').width())+'px');
			$('.sh-video').css( "width",($('.container-fluid').width())+'px');
			$('.sh-map-full').css( "width",($('.container-fluid').width())+'px');
            var offset = $('.nested-container').offset();
            var padding = 15;
            var totalOffset = offset.left + padding;
            $('.sh-parallax').css('margin-left', '-' + totalOffset + 'px' );
			$('.sh-fixed').css('margin-left', '-' + totalOffset + 'px' );
			$('.sh-solidbg').css('margin-left', '-' + totalOffset + 'px' );
			$('.sh-imagebg').css('margin-left', '-' + totalOffset + 'px' );
			$('.sh-colorbg').css('margin-left', '-' + totalOffset + 'px' );
			$('.sh-video').css('margin-left', '-' + totalOffset + 'px' );
			$('.sh-map-full').css('margin-left', '-' + totalOffset + 'px' );
        } else {
            $('.sh-parallax').css('margin-left', '-30px');
            $('.sh-parallax').css('margin-right', '-30px');
			$('.sh-parallax').css( "width",($('.container').width())+'px');
			$('.sh-fixed').css('margin-left', '-30px');
            $('.sh-fixed').css('margin-right', '-30px');
            $('.sh-fixed').css( "width",($('.container').width())+'px');
			$('.sh-solidbg').css('margin-left', '-30px');
            $('.sh-solidbg').css('margin-right', '-30px');
            $('.sh-solidbg').css( "width",($('.container').width())+'px');
			$('.sh-imagebg').css('margin-left', '-30px');
            $('.sh-imagebg').css('margin-right', '-30px');
            $('.sh-imagebg').css( "width",($('.container').width())+'px');
			$('.sh-colorbg').css('margin-left', '-30px');
            $('.sh-colorbg').css('margin-right', '-30px');
            $('.sh-colorbg').css( "width",($('.container').width())+'px');
			$('.sh-video').css('margin-left', '-30px');
            $('.sh-video').css('margin-right', '-30px');
            $('.sh-video').css( "width",($('.container').width())+'px');
			$('.sh-map-full').css('margin-left', '-30px');
            $('.sh-map-full').css('margin-right', '-30px');
			$('.sh-map-full').css( "width",($('.container').width())+'px');
        }
    };
	
	$(document).ready(function() {
		$('#style-switcher').removeClass('style-switcher-hidden');
		$('#style-switcher').css('left', '-254px');
	});

	$(document).ready(function() {
		$('#style-switcher .style-switcher-top').toggle(function(){
			$(this).parent().animate({'left' : '0px'}, 300, 'easeInExpo');
		}, function(){
			$(this).parent().animate({'left' : '-254px'}, 300, 'easeOutExpo');
		});
	});

	// show background options
	function showBackgrounds() {
		if (!$('.ss-bg').length ) {
		var bgSel = '<div class="ss-bg">'
				  + '<p>Sample Backgrounds</p>'
				  + '<a href="#" class="ss-bga"><img src="' + switcher_globals.sh_templateUri 
				  + '/images/backgrounds/ecailles.png"></a>'
				  + '<a href="#" class="ss-bga"><img src="' + switcher_globals.sh_templateUri 
				  + '/images/backgrounds/squairy_light.png"></a>'
				  + '<a href="#" class="ss-bga"><img src="' + switcher_globals.sh_templateUri 
				  + '/images/backgrounds/dark_wood.png"></a>'
				  + '<a href="#" class="ss-bga"><img src="' + switcher_globals.sh_templateUri 
				  + '/images/backgrounds/green_cup.png"></a>'
				  + '<a href="#" class="ss-bga"><img src="' + switcher_globals.sh_templateUri 
				  + '/images/backgrounds/old_moon.png"></a>'
			      + '<a href="#" class="ss-bga"><img src="' + switcher_globals.sh_templateUri 
				  + '/images/backgrounds/skulls.png"></a>'
				  + '<a href="#" class="ss-bga"><img src="' + switcher_globals.sh_templateUri 
				  + '/images/backgrounds/stardust.png"></a>'
				  + '<a href="#" class="ss-bga"><img src="' + switcher_globals.sh_templateUri 
				  + '/images/backgrounds/tileable_wood_texture.png"></a>'
				  + '<a href="#" class="ss-bga"><img src="' + switcher_globals.sh_templateUri 
				  + '/images/backgrounds/triangular.png"></a>'
				  + '<a href="#" class="ss-bga"><img src="' + switcher_globals.sh_templateUri 
				  + '/images/backgrounds/tweed.png"></a>'
				  + '<a href="#" class="ss-bga"><img src="' + switcher_globals.sh_templateUri 
				  + '/images/backgrounds/whitewood.jpg"></a>'
				  + '<a href="#" class="ss-bga"><img src="' + switcher_globals.sh_templateUri 
				  + '/images/backgrounds/zwartevilt.png"></a>'
				  + '<a href="#" class="ss-bgfull ss-bga"><img src="' 
				  + switcher_globals.sh_templateUri + '/images/backgrounds/mountains-16.jpg"></a>'
				  + '<a href="#" class="ss-bgfull ss-bga"><img src="' 
				  + switcher_globals.sh_templateUri + '/images/backgrounds/free-16.jpg"></a>'
				  + '<a href="#" class="ss-bgfull ss-bga"><img src="' 
				  + switcher_globals.sh_templateUri + '/images/backgrounds/wooden-16.jpg"></a>'
				  + '<a href="#" class="ss-bgfull ss-bga"><img src="' 
				  + switcher_globals.sh_templateUri + '/images/backgrounds/wall-16.jpg"></a>'
				  + '<a href="#" class="ss-bgfull ss-bga"><img src="' 
				  + switcher_globals.sh_templateUri + '/images/backgrounds/citynight-16.jpg"></a>'
				  + '<a href="#" class="ss-bgfull ss-bga"><img src="' 
				  + switcher_globals.sh_templateUri + '/images/backgrounds/construction-16.jpg"></a>'
				  + '</div>';
		$('.ss-layout').after(bgSel);
		}
	}

	function hideBackgrounds() {
		$('.ss-bg').remove();
	}

	// change background pattern
	$(document).on('click', '.ss-bga', function() {
		var full = false;
		if ($(this).hasClass('ss-bgfull')) {
			var full = true;
		}
		var img = $(this).find('img').prop('src');
		if (full) {
			// get the big picture
			var largImg = img.replace(/-16.jpg$/i, '');
			img = largImg + '.jpg';
			$('body').css('backgroundImage','url(' + img + ')');
			$('body').css('background-attachment', 'fixed');
			$('body').css('background-size', 'cover');
		} else {
			$('body').css('backgroundImage','url(' + img + ')');
			$('body').css('background-attachment', 'fixed');
			$('body').css('background-size', '');
		}
		return false;
	});

	// change page colors
	$(document).on('click', '.ss-pc', function() {
		var colorName = $(this).find('span').attr('class').split(" ")[1];
		var currentColor = $(this).find('span').css('background-color');
		$('.wrapper').css('background-color', currentColor);
		if (colorName != 'white'
			&& colorName != 'silver'
		) {
			//$('.wrapper article').css('color','#efefef');
			//$('h1, h2, h3, h4, h5, h6').css('color', '#efefef');
			//$('.wrapper article a').css('color', '#ffffff');
		} else {
			//$('.wrapper article').css('color','#444444');
			//$('h1, h2, h3, h4, h5, h6').css('color', '#444444');
			//$('.wrapper article a').css('color', '#222222');
		}
		return false;
	});

	// change accent colors
	$(document).on('click', '.ss-acc', function() {
		var currentColor = $(this).find('span').css('background-color');
		var logo = $(this).find('span').attr('class').split(" ")[1];
		var navbarImg = '<img src="' + switcher_globals.sh_templateUri + '/images/demo/small-logo-' + logo + '.png" />';
		$('a.navbar-brand').html(navbarImg);
	});
});


/* jQuery Easing functions */
jQuery(function ($) { 
	"use strict";
    $.easing.jswing = $.easing.swing;

    $.extend($.easing, {
    	easeInExpo: function (x, t, b, c, d) {
        	return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
    	},
    	easeOutExpo: function (x, t, b, c, d) {
        	return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
    	},
    	easeInOutExpo: function (x, t, b, c, d) {
        	if (t==0) return b;
        	if (t==d) return b+c;
        	if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
        	return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
    	}
	});
});
