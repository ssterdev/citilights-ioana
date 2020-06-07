
;(function($){
	"use strict";
	$( document ).ready( function () {
		$('.agent-action a').tooltip({html: true,container:$('body'),placement:'top'});

		if($('.s-prop-location').length && $('.s-prop-sub_location').length) {
			$('.s-prop-location').find('.dropdown-menu > li > a').on('click',function(e){
				e.stopPropagation();
			    e.preventDefault();
			    var val = $(this).data('value');
			    $('.s-prop-sub_location .dropdown').children('[data-toggle="dropdown"]').text($('.s-prop-sub_location .dropdown-menu > li:first a').text());
			    $('.s-prop-sub_location').find('.dropdown-menu > li').each( function() {
			    	var parent = $(this).data('parent-location');
			    	if( typeof(parent) !== "undefined" && parent != val ) {
			    		$(this).hide();
			    	} else {
			    		$(this).show();
			    	}
			    });
			});
		}
	});

	if($('.form-group .dropdown').length){
		$('.form-group .dropdown-menu > li > a').on('click',function(e){
			e.stopPropagation();
			e.preventDefault();
			var dropdown = $(this).closest('.dropdown'),
			val = $(this).data('value');
			var plan = $(this).data('plan');
			var price = $(this).data('price');
			dropdown.children('input').val(val);
			dropdown.children('input').trigger('change');
			dropdown.children('[data-toggle="dropdown"]').text($(this).text());

			dropdown.children('[data-toggle="dropdown"]').dropdown('toggle');
		});

		$('.membership-payment .form-group .dropdown').on('click', '> span', function(event) {
			event.preventDefault();
			// $(this).parent().toggleClass('open');
			// console.log("sdscd");
		});

		$('.membership-payment .form-group .dropdown-menu > li > a').on('click',function(e){
			e.stopPropagation();
			e.preventDefault();
			var dropdown = $(this).closest('.dropdown'),
			val = $(this).data('value');
			var plan = $(this).data('plan');
			var price = $(this).data('price');
			dropdown.children('input#package_id').val(val);
			dropdown.children('input#plan').val(plan);
			dropdown.children('input#price').val(price);
			dropdown.children('[data-toggle="dropdown"]').text($(this).text());

			dropdown.children('[data-toggle="dropdown"]').dropdown('toggle');

			$('.membership-payment .form-group .dropdown').removeClass('open');
			
		});

		$('.s-prop-desc textarea').wysihtml5({
			"stylesheets": [noo_dashboard.style_rtl],
			"font-styles": false,
			"blockquote": false,
			"emphasis": true,
			"lists": true,
			"html": false,
			"link": true,
			"image": true,
			"color": false,
		});
	}

	$( ".subscription_post #recurring_payment").click(function () {
		if( $(this).is(":checked") ) {
			$( ".subscription_post .recurring_time" ).show();
		} else {
			$( ".subscription_post .recurring_time" ).hide();
		}
	});

	$( ".profile-form" ).submit(function( event ) {
		event.preventDefault();

		var $form = $( this );
		var data = $form.serializeArray();
		$.ajax({
			type: 'POST',
			url: nooL10n.ajax_url,
			data: data,
			success: function (response) {
				var result = $.parseJSON(response);
				var message = '';
				if( result.success ) {
					message = '<div class="noo-message alert alert-success alert-dimissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' + result.message + '</div>';
				} else {
					message = '<div class="noo-message alert alert-danger alert-dimissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' + result.message + '</div>';
				}
				$form.find('.form-message').empty().append(message);
				$form.find('input[type=password]').val('');
			},
			error: function (errorThrown) {
			}
		});
	});

	$( ".property-action .agent-action .status-property" ).click(function(e) {
		e.preventDefault();
		// if (confirm(noo_dashboard.confirmStatusMsg)) {
			var el = $(this),
			parent = el.parent('.agent-action'),
			data = {
				'prop_id': parent.attr('data-prop-id'),
				'agent_id': parent.attr('data-agent-id'),
				'nonce': noo_dashboard.status_property,
				'action': 'noo_ajax_status_property'
			};

			$.post(nooL10n.ajax_url, data, function (response) {
				var result = $.parseJSON(response);
				if (result.success) {
					// @TODO: add action
					location.reload();
				}
			});
		// }
	});

	$( ".property-action .agent-action .featured-property" ).click(function(e) {
		e.preventDefault();
		if (confirm(noo_dashboard.confirmFeaturedMsg)) {
			var el = $(this),
			parent = el.parent('.agent-action'),
			data = {
				'prop_id': parent.attr('data-prop-id'),
				'agent_id': parent.attr('data-agent-id'),
				'nonce': noo_dashboard.featured_property,
				'action': 'noo_ajax_featured_property'
			};

			$.post(nooL10n.ajax_url, data, function (response) {
				var result = $.parseJSON(response);
				if (result.success) {
					// @TODO: add action
					location.reload();
				}
			});
		}
	});

	$( ".property-action .agent-action .delete-property" ).click(function(e) {
		e.preventDefault();
		if (confirm(noo_dashboard.confirmDeleteMsg)) {
			var el = $(this),
			parent = el.parent('.agent-action'),
			data = {
				'prop_id': parent.attr('data-prop-id'),
				'agent_id': parent.attr('data-agent-id'),
				'nonce': noo_dashboard.delete_property,
				'action': 'noo_ajax_delete_property'
			};

			$.post(nooL10n.ajax_url, data, function (response) {
				var result = $.parseJSON(response);
				if (result.success) {
					el.parent('article').remove();
					location.reload();
				}
			});
		}
	});

	$( ".submission-payment .submission-featured" ).change(function() {
		var $this = $(this),
		$container = $this.closest('.submission-payment');
		if( $this.is(":checked") ) {
			$container.find('.payment-total span.amount').text( $container.attr('data-total-price-text') );
			var listing_price = $container.attr('data-total-price-text').replace(/[^\d\.]/g, '');
			$container.find('input#price_property').val( listing_price );
			$container.find('.payment-action a.paypal-btn').removeClass('disabled');
		} else {
			var listing_price_text = $container.find('.listing-price span.amount' ).length 
				? $container.find('.listing-price span.amount' ).text()
				: $container.attr('data-zero-price-text');
			var listing_price = listing_price_text.replace(/[^\d\.]/g, '');
			$container.find('.payment-total span.amount').text( listing_price_text );
			$container.find('input#price_property').val( listing_price );
			if( $container.attr('data-listing-price') == '0' ) {
				$container.find('.payment-action a.paypal-btn').addClass('disabled');
			}
		}
	});

	$( "form.subscription_post" ).submit(function( event ) {
		event.preventDefault();
		if( ( nooL10n.is_logged_in == 'false' ) && $('.noo-login-form').length > 0 ) {
			$('.noo-login-form').show();
			return;
		}

		var $form = $( this );
		var data = $form.serializeArray();
		$.ajax({
			type: 'POST',
			url: nooL10n.ajax_url,
			data: data,
			success: function (response) {
				var result = $.parseJSON(response);
				var message = '';
				if( result.success ) {
					window.location.replace( result.message );
				} else {
					message = '<div class="noo-message alert alert-danger alert-dimissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' + result.message + '</div>';
				}
				$form.find('.form-message').empty().append(message);
			},
			error: function (errorThrown) {
			}
		});
	});

	$( ".listing-payment-form" ).submit(function( event ) {
		event.preventDefault();

		var $form = $( this );
		var data = $form.serializeArray();
		$.ajax({
			type: 'POST',
			url: nooL10n.ajax_url,
			data: data,
			success: function (response) {
				var result = $.parseJSON(response);
				var message = '';
				if( result.success ) {
					window.location.replace( result.message );
				} else {
					message = '<div class="noo-message alert alert-danger alert-dimissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' + result.message + '</div>';
				}
				$form.find('.form-message').empty().append(message);
			},
			error: function (errorThrown) {
			}
		});
	});

	// ("#noo_additional_details").sortable({
 //    revert: 100,
 //    placeholder: "detail-placeholder",
 //    handle: ".sort-additional-row",
 //    cursor: "move"
 //    });
	
    $('.add-additional-row').click(function (e) {
    e.preventDefault();

    var numVal = $(this).data("key") + 1;
    $(this).data('key', numVal);
    $(this).attr({
        "key": numVal
    });

    var newAdditionalDetail = '<tr>' +
        '<td class="action-field">' +
        '<span class="sort-additional-row"><i class="fa fa-navicon"></i></span>' +
        '</td>' +
        '<td class="field-title">' +
        '<input class="noo-ft-field" type="text" name="additional_features[' + numVal + '][additional_feature_label]" id="additional_feature_label_' + numVal + '" value="">' +
        '</td>' +
        '<td>' +
        '<input class="noo-ft-field" type="text" name="additional_features[' + numVal + '][additional_feature_value]" id="additional_feature_value_' + numVal + '" value="">' +
        '</td>' +
        '<td class="action-field">' +
        '<span data-remove="' + numVal + '" class="remove-additional-row"><i class="fa fa-remove"></i></span>' +
        '</td>' +
        '</tr>';

    $('#noo_additional_details').append(newAdditionalDetail);
    removeAdditionalDetails();
    });

    var removeAdditionalDetails = function () {

        $('.remove-additional-row').click(function (event) {
            event.preventDefault();
            var $this = $(this);
            $this.closest('tr').remove();
        });
    }
    removeAdditionalDetails();

    //Sub properties
        
    var clone_sub_properties = function () {

        $('#rp-item-sub_property_wrap-wrap .rp-clone-sub-property').on('click', '.add-sub-property', function ( event ) {
            event.preventDefault();
            var btn_clone     = $(this),
                total         = btn_clone.data('total'),
                content_clone = $('#clone_element1').clone(true).html();

            content_clone = content_clone.replace(/\[0\]/g, '[' + (total + 1) + ']');
            $('.content-clone1').append('<div class="rp-sub-property-wrap rp-md-12">' + content_clone + '</div>');
            btn_clone.data('total', total + 1);

        });

    }
    clone_sub_properties();

    var remove_sub_properties = function () {

        $('body').on('click', '.remove-sub-property', function ( event ) {
            event.preventDefault();
            $(this).closest('.rp-sub-property-wrap').remove();
        });

    }
    remove_sub_properties();
    
})(jQuery);