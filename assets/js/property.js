jQuery( document ).ready( function ($) {
		if($('[data-paginate="loadmore"]').find('.loadmore-action').length){
			$('[data-paginate="loadmore"]').each(function(){
				var $this = $(this);
				$this.nooLoadmore({
					agentID: $this.closest('.agent-properties').data('agent-id'),
					navSelector  : $this.find('div.pagination'),            
			   	    nextSelector : $this.find('div.pagination a.next'),    
			   	    itemSelector : '.noo_property',
			   	    finishedMsg: nooPropertyL10n.ajax_finishedMsg
				});
			});
		}
		
		if($('#conactagentform').length){
			$('#conactagentform').each(function(){
				var form = $(this);
				form.ajaxForm({
					beforeSubmit: function(arr, $form, options) {
						$form.find('.addclass').closest('div').removeClass("has-error").find('span.error').remove();
						$form.children('.msg').remove();
						$form.find('img.ajax-loader').css({ visibility: 'visible' });
						return true;
					},
					url: nooPropertyL10n.ajax_url,
					type: 'POST',
					dataType: 'json',
					success: function(data, status, xhr, $form) {
						$form.find('img.ajax-loader').css({ visibility: 'hidden' });
						if (! $.isPlainObject(data) || $.isEmptyObject(data))
							return;
						$.each(data.error,function(i,err){
							$form.find('[name='+err.field+']').closest('div').addClass('has-error').append('<span class="error">' + err.message + '</span>').slideDown('fast')
						});
						if(data.msg != ''){
							$form.append('<span class="msg">'+data.msg+'</span>').slideDown('fast');
							$form.find('[placeholder].addclass').each(function(i, n) {
								$(n).val('');
							});
						}
					},
					error: function(xhr, status, error, $form) {
						
					}
				});
			});
		}
		// $('.recent-properties-slider').each(function(){
		// 	var $this = $(this);
		// 	var play = $(this).data("auto") == true;
		// 	var time = $(this).data("slider-time");
		// 	var speed = $(this).data("slider-speed");
		// 	var recentCarouselOptions = {
		// 		responsive: true,
		// 		circular: false,
		// 		infinite:true,
		// 		auto: {
		// 			play : play,
		// 			pauseOnHover: true
		// 		},
		// 		prev: $this.find('.caroufredsel-prev'),
		// 		next: $this.find('.caroufredsel-next'),
		// 		swipe: {
		// 			onTouch: true
		// 		},
		// 		scroll: {
		// 			items: 1,
		// 			duration: speed,
		// 			fx: 'scroll',
		// 			timeoutDuration: time,
		// 			easing: 'swing'
		// 		},
		// 		items: {
		// 			visible: 1
		// 		}
		// 	};
		// 	$this.find('ul').carouFredSel(recentCarouselOptions);
		// 	imagesLoaded($this,function(){
		// 		$this.find('ul').trigger("destroy").carouFredSel(recentCarouselOptions);
		// 	});
		// 	$(window).resize(function(){
		// 		$this.find('ul').trigger("destroy").carouFredSel(recentCarouselOptions);
		// 	});
		// });
		// $('.recent-agents-slider').each(function(){
		// 	var $this = $(this);
		// 	var recentCarouselOptions = {
		// 		responsive: true,
		// 		circular: true,
		// 		infinite:true,
		// 		// width: '100%',
		// 		// height: 'auto',
		// 		auto: {
		// 			play : false,
		// 			pauseOnHover: true
		// 		},
		// 		prev: $this.find('.caroufredsel-prev'),
		// 		next: $this.find('.caroufredsel-next'),
		// 		mousewheel: true,
		// 		swipe: {
		// 			onMouse: true,
		// 			onTouch: true
		// 		},
		// 		scroll: {
		// 			items: null,
		// 			duration: 600,
		// 			fx: 'scroll',
		// 			timeoutDuration: 2000,
		// 			easing: 'swing'
		// 		},
		// 		items: {
		// 			height:'variable',
		// 			//	height: '30%',	//	optionally resize item-height
		// 			visible: {
		// 				min: 1,
		// 				max: 6
		// 			}
		// 		}
		// 	};
		// 	$this.find('ul').carouFredSel(recentCarouselOptions);
		// 	imagesLoaded($this,function(){
		// 		$this.find('ul').trigger("destroy").carouFredSel(recentCarouselOptions);
		// 	});
		// 	$(window).resize(function(){
		// 		$this.find('ul').trigger("destroy").carouFredSel(recentCarouselOptions);
		// 	});
		// });
		// $('.recent-properties-featured').each(function(){
		// 	var $this = $(this);
		// 	var play = $(this).data("auto") == true;
		// 	var time = $(this).data("slider-time");
		// 	var speed = $(this).data("slider-speed");
		// 	var recentCarouselOptions = {
		// 		responsive: true,
		// 		circular: false,
		// 		infinite:true,
		// 		auto: {
		// 			play : play,
		// 			pauseOnHover: true
		// 		},
		// 		prev: $this.find('.caroufredsel-prev'),
		// 		next: $this.find('.caroufredsel-next'),
		// 		swipe: {
		// 			onTouch: true
		// 		},
		// 		scroll: {
		// 			items: 1,
		// 			duration: speed,
		// 			fx: 'scroll',
		// 			timeoutDuration: time,
		// 			easing: 'swing'
		// 		},
		// 		width: '100%',
		// 		height: 'variable',
		// 		items: {
		// 			height:'variable',
		// 			visible: 1
		// 		}
		// 	};
		// 	$this.find('ul').carouFredSel(recentCarouselOptions);
		// 	imagesLoaded($this,function(){
		// 		$this.find('ul').trigger("destroy").carouFredSel(recentCarouselOptions);
		// 	});
		// 	$(window).resize(function(){
		// 		$this.find('ul').trigger("destroy").carouFredSel(recentCarouselOptions);
		// 	});
		// });
		if($('.property-featured .images').length){
			var $this = $(this);
			var featuredCarouselOptions = {
				responsive: true,
				circular: true,
				infinite:true,
				auto: {
					play : false,
					pauseOnHover: true
				},
				prev: $this.find('.slider-control.prev-btn'),
				next: $this.find('.slider-control.next-btn'),
				swipe: {
					onTouch: true
				},
				scroll: {
					items: 1,
					duration: 600,
					fx: 'scroll',
					timeoutDuration: 2000,
					easing: 'swing'
				},
				width: '100%',
				height: 'variable',
				items: {
					height:'variable',
					visible: 1
				}
			};
			$('.property-featured .images').find('ul').carouFredSel(featuredCarouselOptions);
			imagesLoaded($('.property-featured .images'),function(){
				$('.property-featured .images').find('ul').trigger("destroy").carouFredSel(featuredCarouselOptions);
			});
			$(window).resize(function(){
				$('.property-featured .images').find('ul').trigger("destroy").carouFredSel(featuredCarouselOptions);
			});
		}
		if($('.property-featured .thumbnails').length){
			$('.property-featured .thumbnails').each(function(){
				var $this = $(this);
				var thumbnailsCarouselOptions = {
					responsive: true,
					circular: false,
					infinite:true,
					auto: {
						play : false,
						pauseOnHover: true
					},
					prev: $this.find('.caroufredsel-prev'),
					next: $this.find('.caroufredsel-next'),
					swipe: {
						onTouch: true
					},
					scroll: {
						items: 1,
						duration: 600,
						fx: 'scroll',
						timeoutDuration: 2000,
						easing: 'swing'
					},
					width: '100%',
					height: 'variable',
					items: {
						width: 138,
						height:'variable',
						visible: {
							min: 1,
							max: 5
						}
					}
				};
				$this.find('ul').carouFredSel(thumbnailsCarouselOptions);
				imagesLoaded($this,function(){
					$this.find('ul').trigger("destroy").carouFredSel(thumbnailsCarouselOptions);
				});
				$(window).resize(function(){
					$this.find('ul').trigger("destroy").carouFredSel(thumbnailsCarouselOptions);
				});
			});
			//Single image
			$(document).on('click','.property-featured .thumbnails ul li > a',function(e){
				e.stopPropagation();
				e.preventDefault();
				$(this).closest('.thumbnails').find('.selected').removeClass('selected');
				$(this).closest('li').addClass('selected');
				var rel = $(this).data('rel');
				$('.property-featured .images').find('ul').trigger('slideTo',rel);
			});
		}
		$('.properties-toolbar a').tooltip({html: true,container:$('body'),placement:'bottom'});
		$(document).on('click','.properties-toolbar a',function(e){
			e.stopPropagation();
			e.preventDefault();
			var $this = $(this);
			$this.closest('.properties-toolbar').find('.selected').removeClass('selected');
			$this.addClass('selected');
			$this.closest('.properties').removeClass('grid').removeClass('list').addClass($this.data('mode'));
		});

		if($('.gsearch .glocation').length && $('.gsearch .gsub-location').length) {
			$('.gsearch .glocation').find('.dropdown-menu > li > a').on('click',function(e){
				e.stopPropagation();
			    e.preventDefault();
			    var val = $(this).data('value');
			    $('.gsearch .gsub-location .dropdown').children('[data-toggle="dropdown"]').text($('.gsearch .gsub-location .dropdown-menu > li:first a').text());
			    $('.gsearch .gsub-location .gsub_location_input').val('');
			    $('.gsearch .gsub-location').find('.dropdown-menu > li').each( function() {
			    	var parent = $(this).data('parent-location');
			    	if( typeof(parent) !== "undefined" && parent != val ) {
			    		$(this).hide();
			    	} else {
			    		$(this).show();
			    	}
			    });
			});
		}

	/**
	 * Process slider floor plan
	 */
		if ( $('.floor-plan-wrap').length > 0 ) {
			
			$('.floor-plan-wrap').each(function(index, el) {
				
				/**
				 * VAR
				 */
					var $$ = $(this);

				/**
				 * Process
				 */
					$$.owlCarousel({
						autoPlay: false,
				      	items : 4,
				      	itemsDesktop : [1199,3],
				      	itemsDesktopSmall : [979,3],
				      	navigation : true,
					    navigationText : ["<i class='fa fa-angle-left' aria-hidden='true'></i>","<i class='fa fa-angle-right' aria-hidden='true'></i>"],
					    pagination : false,
					    slideSpeed : 1000,
					});

			});

		}

	/**
	 * Validate form submit property
	 */
		if ( $('#new_post').length > 0 ) {

			$('#new_post').each(function(index, el) {
				var $$ = $(this);
				$$.on('click', '#property_submit', function(event) {
					var end_process = false
					$$.find('.form-group').find('.required').each(function(index, el) {
						var $_$         = $(this),
							value_field = $_$.val();
						
						$_$.closest('.form-group').removeClass('validate-error');

						if ( value_field === '' ) {
							$_$.closest('.form-group').addClass('validate-error');
							end_process = true;
						}
					});

					if ( end_process ) {
						$("html, body").animate({ scrollTop: $$.position().top }, "slow");
						return false;
					}
				});

			});

		}

});

/**
 * Event window load
 */
jQuery(window).ready(function($) {

	/**
	 * Process button action property
	 */
		$('.noo-main').on('click', '.property-action-button', function(event) {
			event.preventDefault();
						
			/**
			 * VAR
			 */
				var $$ 	    = $(this),
					action  = $$.data( 'action' ),
					user_id = $$.data( 'user' ),
					id      = $$.data( 'id' );

			/**
			 * Process
			 */
				if ( action === 'sharing' ) {

					// var clas = $$.data( 'class' );
					// $( '.' + clas ).toggleClass('show');

				} else if ( action === 'favorites' ) {
					
					/**
					 * Check user login
					 */
						if ( user_id === 0 ) {
							$('#loginmodal').modal();
							$('.notice-form-login').show();
							return;
						}

					var status = $$.data( 'status' );

					if ( status === 'add_favorites' ) {

						$$.data( 'status', 'is_favorites' );

					} else if ( status === 'is_favorites' ) {

						window.location.replace( $$.data('url') ); 
						
						return;
						// $$.data( 'status', 'add_favorites' );

					}

					/**
					 * Call ajax process add favorites
					 */
					$.ajax({
						url: nooPropertyL10n.ajax_url,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'noo_favorites',
							security: nooPropertyL10n.security,
							status : status,
							user_id : user_id,
							property_id : id
						},
					})
					.done(function( response ) {
						
						if ( response.status === 'ok' ) {

							if ( status === 'add_favorites' ) {

								$$.removeClass('fa-heart-o').addClass('fa-heart');

							} else if ( status === 'is_favorites' ) {

								$$.removeClass('fa-heart').addClass('fa-heart-o');

							}

							console.log(response.msg);

						} else {

							alert( response.msg );

						}

					})
					.fail(function() {
						console.log("error");
					});
					

				} else if ( action === 'compare' ) {

					/**
					 * VAR
					 */
						var thumbnail = $$.data('thumbnail'),
						 	div 	  = $$.data('div');

					/**
					 * Process
					 */
					 	if ( $('.item-compare').length > 3 ) {

					 		alert( nooPropertyL10n.notice_max_compare );
					 		return;
					 	}

						$$.data('action', '');

						$('.' + div ).show('slow');

						/**
						 * Add item new
						 * @type {String}
						 */
						$('.' + div + ' .list-compare').append(
							'<div class="item-compare remove-compare-' + id + '">' +
							'<img src="' + thumbnail + '">' +
							'<input type="hidden" value="' + id + '" name="list_compare[]" />' +
							'<i class="fa fa-trash-o" data-id="' + id + '" data-div-wrap="' + div + '" data-div="remove-compare-' + id + '"></i>' +
							'</div>'
						).show('slow');

				}
		});

		$('.noo-main').on('mouseenter', '.property-action-button', function(event) {
			event.preventDefault();

			/**
			 * VAR
			 */
				var $$ 	    = $(this),
					action  = $$.data( 'action' ),
					user_id = $$.data( 'user' ),
					id      = $$.data( 'id' );

			/**
			 * Process
			 */

				if ( action === 'sharing' ) {

					var clas = $$.data( 'class' );
					$( '.' + clas ).toggleClass('show');

				}

		});
		$('.property-action-button').tooltip({html: true,container:$('body'),placement:'bottom'});

	/**
	 * Process event when click button remove compare
	 */
		
		$('.noo-main').on('click', '.list-compare .item-compare i', function(event) {
			event.preventDefault();
			/**
			 * VAR
			 */
				var $$ 		 = $(this),
					id 	     = $$.data('id'),
					div 	 = $$.data('div'),
					div_wrap = $$.data('div-wrap');

			/**
			 * Process
			 */
				// $('.' + div).hide('slow', function() {
					$('.' + div).remove();
				// });

				$('.compare-' + id ).data('action', 'compare');

				if ( $('.item-compare').length < 1 ) {
					$('.' + div_wrap).hide('slow');
				}

		});


	/**
	 * Process event when click button remove favorites
	 */
		
		$('.noo-main').on('click', '.remove_favorites', function(event) {
			
			/**
			 * VAR
			 */
				var $$ 	    = $(this),
					user_id = $$.data( 'user' ),
					div 	= $$.data( 'div' ),
					id      = $$.data( 'id' );

			/**
			 * Process
			 */
				$.ajax({
					url: nooPropertyL10n.ajax_url,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'noo_favorites',
						security: nooPropertyL10n.security,
						user_id : user_id,
						property_id : id,
						status: 'is_favorites'
					},
				})
				.done(function( response ) {
					
					if ( response.status === 'ok' ) {

						$('#' + div).remove();

						console.log(response.msg);

					} else {

						alert( response.msg );

					}

				})
				.fail(function() {
					console.log("error");
				});
					
			});


	/**
	 * Process event select sorted by...
	 */
		// if( $('.noo-main').find('.properties-ordering').length ){
			$('.noo-main').on( 'click', '.properties-ordering .dropdown-menu > li > a', function(e){
				e.stopPropagation();
				e.preventDefault();
				var value = $(this).data('value');
				$(this).closest('.properties-ordering').find('input[name=orderby]').prop('value',value);
				$(this).closest('.dropdown').children('[data-toggle="dropdown"]').dropdown('toggle');
				$(this).closest('form').submit();
				//console.log($(this).closest('form'));
			});
		// }


});