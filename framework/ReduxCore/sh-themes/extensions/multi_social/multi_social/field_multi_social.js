/*global redux_change, redux*/
/* Modified original select js to work with multi_social for font awesome icons LB 11-26-2014 
 * - we have to reset redux-field-init on the redux-field-container in order to re-run additional items
 * - we have to increment the li containers to be unique so that when we clone items from the hidden element
 * - it's a unique class that we append content to.
 *
 *
 * */

(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.multi_social = redux.field_objects.multi_social || {};

    $( document ).ready(
        function() {
            //redux.field_objects.select.init();
        }
    );

    redux.field_objects.multi_social.init = function( selector ) {
        if ( !selector ) {
            //selector = $( document ).find( '.redux-container-select:visible' );
			selector = $( document ).find( '.redux-container-multi_social:visible' );
        }
        
        $( selector ).each(
            function() {
                var el = $( this );
                var parent = el;
                
                if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }
                if ( parent.is( ":hidden" ) ) { // Skip hidden fields
                    return;
                }
                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }
                
                el.find( 'select.redux-select-item' ).each(
                    function() {

                        var default_params = {
                            width: 'resolve',
                            triggerChange: true,
                            allowClear: true
                        };

                        if ( $( this ).siblings( '.select2_params' ).size() > 0 ) {
                            var select2_params = $( this ).siblings( '.select2_params' ).val();
                            select2_params = JSON.parse( select2_params );
                            default_params = $.extend( {}, default_params, select2_params );
                        }

                        if ( $( this ).hasClass( 'font-icons' ) ) {
                            default_params = $.extend(
                                {}, {
                                    formatResult: redux.field_objects.select.addIcon,
                                    formatSelection: redux.field_objects.select.addIcon,
                                    escapeMarkup: function( m ) {
                                        return m;
                                    }
                                }, default_params
                            );
                        }

                        $( this ).select2( default_params );

                        if ( $( this ).hasClass( 'select2-sortable' ) ) {
                            default_params = {};
                            default_params.bindOrder = 'sortableStop';
                            default_params.sortableOptions = {placeholder: 'ui-state-highlight'};
                            $( this ).select2Sortable( default_params );
                        }

                        $( this ).on(
                            "change", function() {
                                redux_change( $( $( this ) ) );
                                $( this ).select2SortableOrder();
                            }
                        );
                    }
                );
            }
        );
    };

    redux.field_objects.multi_social.addIcon = function( icon ) {
        if ( icon.hasOwnProperty( 'id' ) ) {
            return "<span class='elusive'><i class='" + icon.id + "'></i>" + "&nbsp;&nbsp;" + icon.text + "</span>";
        }
    };

	/* Multi Social functions */
    $('.redux-multi-social-add').click(function(){
        /* Modified to account for the clone being the last child
           so we choose the second to last to edit and then run redux.select afterwards
		   - we are using the old redux method to redraw the selects once added, something to be aware of.  Both methods are 
		   included above, unless we can get the new one to work.
        */
		var i = 0;
		$(this).closest('.redux-field-container').find('.redux-multi-text li').each( function() {
			var temp = parseInt($(this).find('.regular-text').attr('id').split('-')[3]);  // get the id from text input
			if (isNaN(temp)) { temp = 0;}
			if (temp >= i) {
				i = temp + 1;
			}
		});

        var id = $(this).data('id');   // Get the container id
        var name = $(this).data('name');
		var nameSelect = $(this).data('name-select');
		var li_class = 'new_social_' + i;
        var new_input = $('#'+id+' li:last-child input.regular-text').clone();
		var new_select = $('#'+id+' li:last-child select.font-icons').clone();
		var new_remove = $('#'+id+' li:last-child .multi-social-remove').clone();
		var entry = '<li class="' + li_class + '"></li>';
		$(entry).insertBefore('#'+id+' li:last-child');
		$('.' + li_class).append(new_input);
		$('.' + li_class).append(new_select);
		$('.' + li_class).append(' ');  // just a space before the remove link
		$('.' + li_class).append(new_remove);
		$('.' + li_class).find('.regular-text').attr("id","opt-multi-social-" + i);
		$('.' + li_class).find('.font-icons').attr("id", "opt-multi-fa-" + i + "-select");
		$('.' + li_class).find('.font-icons').css('display', 'inline-block');
        $('#'+id+' li:nth-last-child(2) input[type="text"]').attr('name' , name);
		$('#'+id+' li:nth-last-child(2) select').attr('name' , nameSelect);
		$(this).closest('.redux-field-container').addClass('redux-field-init');  // needed for function to rerun on these elements
		redux.field_objects.multi_social.init();
    });

	/* Delete social entry */
	$(document).on('click','.multi-social-remove', function() {
		$(this).closest('li').remove();	
	});

})( jQuery );
