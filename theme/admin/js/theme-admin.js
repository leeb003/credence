jQuery(function ($) {  // use $ for jQuery
	"use strict";

	/**
     * Adjust visibility of the meta boxes at startup
     */
	$('#blog_options').hide();
	$('#blogtime_options').hide();
	$('#bloggrid_options').hide();
	$('#portfolio_options').hide();
	$('#page-options-background').hide();

	if ($('#page_template').val() == 'single/single-portfolio.php'
		|| $('#page_template').val() == 'single/single-portfolio2.php'
		|| $('#page_template').val() == 'single/single-portfolio3.php'
	) {
        $('#portfolio_options').show();
	} else if ($('#page_template').val() == 'single/single-bloggrid.php') {
		$('#bloggrid_options').show();
	} else if ($('#page_template').val() == 'single/single-blog.php') {
        $('#blog_options').show();
	} else if ($('#page_template').val() == 'single/single-blogtime.php') {
		$('#blogtime_options').show();
	}

    $(document).on('change', '#page_template', function() {
		$('#blog_options').hide();
		$('#blogtime_options').hide();
		$('#bloggrid_options').hide();
		$('#portfolio_options').hide();
		$('#page-options-background').hide();

		if ($(this).val() == 'single/single-portfolio.php'
			|| $('#page_template').val() == 'single/single-portfolio2.php'    
        	|| $('#page_template').val() == 'single/single-portfolio3.php'
		){
            $('#portfolio_options').show();
		} else if ($(this).val() == 'single/single-bloggrid.php') {
			$('#bloggrid_options').show();
		} else if ($(this).val() == 'single/single-blog.php') {
			$('#blog_options').show();
		} else if ($(this).val() == 'single/single-blogtime.php') {
			$('#blogtime_options').show();
		}

    });
   
	/* Pages Image Upload */
	jQuery(document).ready(function($){
    	var custom_uploader;
    	$('#upload_image_button').click(function(e) {
        	e.preventDefault();
 
        	//If the uploader object has already been created, reopen the dialog
        	if (custom_uploader) {
            	custom_uploader.open();
            	return;
        	}
 
        	//Extend the wp.media object
        	custom_uploader = wp.media.frames.file_frame = wp.media({
            	title: 'Choose Image',
            	button: {
                	text: 'Choose Image'
            	},
            	multiple: false
        	});
 
        	//When a file is selected, grab the URL and set it as the text field's value
        	custom_uploader.on('select', function() {
            	var attachment = custom_uploader.state().get('selection').first().toJSON();
            	$('#upload_image').val(attachment.url);
				var image = '<img src="' + attachment.url + '" width="250" />';
				$('.image-selected').html(image);
        	});
 
        	//Open the uploader dialog
        	custom_uploader.open();
    	});
	});
	/* End Pages Image Upload */

	/* Ads Widget functionality */
	$(document).ready(function($) {
		$("body").on("click", "a.sh_add_ad", function(e){
			e.preventDefault();
			var widget_holder = $(this).closest('.widget-inside');
			var cloner = widget_holder.find('.sh_ads_clone');
 			widget_holder.find('.sh_ads_container').append('<li style="margin-bottom: 15px;">'+cloner.html()+'</li>');
		});
			
		$("body").on("click", "input.sh-ad-size", function(e){
			if($(this).val() == 'custom'){
				$(this).parent().next().show();
			} else {
				$(this).parent().next().hide();
			}				
		});
	});

	/* Instagram Widget */
	$(document).on('change', '.instagram-source', function() {
		var source = $(this);
        if ( source.val() != 'instagram' ) {
			$(document).find('.instagram-attach').closest('p').animate({opacity: 'hide' , height: 'hide'}, 200);
			$(document).find('.instagram-refresh').closest('p').animate({opacity: 'hide' , height: 'hide'}, 200);
		} else {
			$(document).find('.instagram-attach').closest('p').animate({opacity: 'show' , height: 'show'}, 200);
			$(document).find('.instagram-refresh').closest('p').animate({opacity: 'show' , height: 'show'}, 200);
		}
	});
	// Toggle blocked images
	$('body').on('click', '.blocked-images-toggle', function(e){
		e.preventDefault();
		var blocked_container = $(this).next();
			
		if ( blocked_container.is(':hidden') ) {
			$(this).html('[ - Close ]');
		} else {
			$(this).html('[ + Open ]');
		}
		blocked_container.toggle();
	});		

	// Remove blocked images with ajax
	$('body').on('click', '.instagram-container .blocked-images .blocked-column', function(e){
		var li = $(this),
			id = li.data('id'),
			username  = li.closest('.instagram-container').find('input[id$="username"]').val(),
			counter   = li.closest('.instagram-container').find('.blocked-count-nr'),
			ajaxNonce = li.closest('.instagram-container').find('input[name=unblock_images_nonce]').val();
			
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'jr_unblock_images',
				username : username,
				id : id,
				_ajax_nonce: ajaxNonce
			},
			success: function(data, textStatus, XMLHttpRequest) {
				if ( data == 'success' ) {
					li.fadeOut( "slow", function() {
						$(this).remove();	
						counter.html(parseInt(counter.html(), 10) - 1);
					});
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				//console.log(XMLHttpRequest.responseText);
			}
		});
	});

	// Import demo content located in tools
	$(document).on('click', '.import-demo-button', function() {
        var nonce = $('#import_demo_content').val();
        $('.import-demo-button').attr('disabled', 'disabled');
		$('.demo-import-fail-div').addClass('hidden'); // just in case it's shown from a previous fail
        $('.demo-import-wait-div').removeClass('hidden');

        // get the button selected
        var demoChoice = $('.import-demo-choice :selected').val();
        // 1 = main demo
        // 2 = single page demo
        if (!demoChoice) {
            demoChoice = 1;
        }
        $.ajax({
            type : "post",
            dataType : "json",
            url : credenceAdmin.adminUrl,
            data : {action : "import_demo_content", nonce: nonce, demo_choice: demoChoice},
            success: function(response) {
                if(response.type == "success") {
                    var id = 3;
                    $('.demo-import-wait-div').addClass('hidden');
                    $('.demo-import-success-div').removeClass('hidden');
                    $('.import-demo-button').fadeOut();
                    alert('Demo Content Has been Imported!');
                } else {
                    $('.import-demo-button').removeAttr('disabled');
                    $('.demo-import-wait-div').addClass('hidden');
                    $('.demo-import-fail-div').removeClass('hidden');
                }
            }
        });
        return false;
    });
		

});



