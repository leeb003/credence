// Social Icon selection, hidden div loaded in admin meta for icon select dialog
// this is all for the icon selection for people custom post types
jQuery(function ($) {  // use $ for jQuery

    // Icon selection
    $(document).on('click', '.select-icon', function() {
        var value = $(this).find('i').attr('class').split(' ')[1];
        $('.chosen').removeClass('chosen');
        $(this).addClass('chosen');
        //alert(value);
        $('#service_icon').val(value);
        var newIcon = '<i class="fa ' + value + ' fa-2x"></i>';
        $('.choose-icon').html(newIcon);
        $('#select-icons').dialog('close');
        return false;
    });

    /* People Social Icons */
    $(document).on('click', '.select-social-icon', function() {
        var id = parseInt($('.icon-list').attr('class').split(' ')[1].split('-')[1]);
        var value = $(this).find('i').attr('class').split(' ')[1];
        $('.chosen').removeClass('chosen');
        $(this).addClass('chosen');
        //alert(value);
        $('.sort-' + id).find('.social_icon').val(value);
        var icon='<i class="fa ' + value + ' fa-2x"></i>';
        $('.sort-' + id).find('.choose-social').html(icon);
        $('.choose-icons').dialog('close');
        return false;
    });

    // Add an entry to the form
    $(document).on('click', '.add-social', function() {
        var lastId;
        var id = 0;
        $('.sorting').each( function() {
            lastId = parseInt($(this).attr('class').split(' ')[1].split('-')[1]);
            if (lastId >= id) {
                id = lastId + 1;
            }
        });
        var content = '<tr class="sorting sort-' + id + '">'
            + '<td>Link: <input class="social-link" type="text" name="social_link[]" /></td>'
            + '<td class="icon-td">Icon: <div class="choose-social"><i class="fa fa-gittip fa-2x"></i></div>'
            + '<input type="hidden" class="social_icon" name="social_icon[]" value="fa-gittip" />'
            + '<a href="#" class="remove-link">Remove</a>'
            + '<div class="sorter"><i class="fa fa-arrows"></i></div></td></tr>';
        $(this).closest('tr').after(content);
        return false;
    });

    // Remove an entry from the form
    $(document).on('click', '.remove-link', function() {
        $(this).closest('tr').remove();
        return false;
    });

    //Sorting
    // Return a helper with preserved width of cells
    var fixHelper = function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };

    $(function() {
        $('.meta-table').sortable({
            items: '.sorting',
            helper: fixHelper
        });
    });

	// Icons
    $(document).on('click', '.choose-social', function() {
        var icon = $(this).find('i').attr('class').split(' ')[1];
        var id = parseInt($(this).closest('tr').attr('class').split(' ')[1].split('-')[1]);
		showDialog();
		$(document).find('.icon-list').removeClass().addClass('icon-list').addClass('forid-' + id);
		$('.select-social-icon i').each(function() {
			$(this).parent().removeClass('chosen');
			var curIcon = $(this).attr('class').split(' ')[1];
			if (curIcon == icon) {
				$(this).parent().addClass('chosen');
			}
		});
		return false;
    });

	//Dialog
	function showDialog() {
		$('.choose-icons').dialog({
			modal: false,
			draggable: false,
			resizable: true,
			dialogClass: 'sh_dialog', // Add our own class to override styles that VC removes
			width: '400',
			height: '450',
			buttons: {
		    	'Cancel': function() {
            		$(this).dialog('close');
            	}
			}
		})
	};

    /* End People Social Icons */
});
