/**
 * NOO Site Script.
 *
 * Javascript used in NOO-Framework
 * This file contains base script used on the frontend of NOO theme.
 *
 * @package    NOO Framework
 * @subpackage NOO Site
 * @version    1.0.0
 * @author     Kan Nguyen <khanhnq@nootheme.com>
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       http://nootheme.com
 */
// =============================================================================

;(function($){
	$.fn.nooLoadmore = function(options,callback){
		var defaults = {
			agentID:0,
			contentSelector: null,
			contentWrapper:null,
			nextSelector: "div.navigation a:first",
			navSelector: "div.navigation",
			itemSelector: "div.post",
			dataType: 'html',
			finishedMsg: "<em>Congratulations, you've reached the end of the internet.</em>",
			loading:{
				speed:'fast',
				start: undefined
			},
			state: {
				isDuringAjax: false,
				isInvalidPage: false,
				isDestroyed: false,
				isDone: false, // For when it goes all the way through the archive.
				isPaused: false,
				isBeyondMaxPage: false,
				currPage: 1
			}
		};
		var options = $.extend(defaults, options);

		return this.each(function(){
			var self = this;
			var $this = $(this),
			    wrapper = $this.find('.loadmore-wrap'),
			    action = $this.find('.loadmore-action'),
			    btn = action.find(".btn-loadmore"),
			    loading = action.find('.loadmore-loading');

			options.contentWrapper = options.contentWrapper || wrapper;



			var _determinepath = function(path){
				if (path.match(/^(.*?)\b2\b(.*?$)/)) {
					path = path.match(/^(.*?)\b2\b(.*?$)/).slice(1);
				} else if (path.match(/^(.*?)2(.*?$)/)) {
					if (path.match(/^(.*?page=)2(\/.*|$)/)) {
						path = path.match(/^(.*?page=)2(\/.*|$)/).slice(1);
						return path;
					}
					path = path.match(/^(.*?)2(.*?$)/).slice(1);

				} else {
					if (path.match(/^(.*?page=)1(\/.*|$)/)) {
						path = path.match(/^(.*?page=)1(\/.*|$)/).slice(1);
						return path;
					} else {
						options.state.isInvalidPage = true;
					}
				}
				return path;
			}
			var path = $(options.nextSelector).attr('href');
			path = _determinepath(path);

			// callback loading
			options.callback = function(data, url) {
				if (callback) {
					callback.call($(options.contentSelector)[0], data, options, url);
				}
			};

			options.loading.start = options.loading.start || function() {
				btn.hide();
				$(options.navSelector).hide();
				loading.show(options.loading.speed, $.proxy(function() {
					loadAjax(options);
				}, self));
			};

			var loadAjax = function(options){
				var callback=options.callback,
				    desturl,frag,box,children,data;

				options.state.currPage++;
				// Manually control maximum page
				if ( options.maxPage !== undefined && options.state.currPage > options.maxPage ){
					options.state.isBeyondMaxPage = true;
					return;
				}
				desturl = path.join(options.state.currPage);
				$.post(nooGmapL10n.ajax_url,{
					action:'noo_agent_ajax_property',
					page:options.state.currPage,
					agent_id:options.agentID
				},function(res){

					if(res.content != '' && res.content !=null && res.content != undefined){
						$(options.contentWrapper).append(res.content);
						loading.hide();
						btn.show(options.loading.speed);
					}else{
						btn.hide();
						action.append('<div style="margin-top:5px;">' + options.finishedMsg + '</div>').animate({ opacity: 1 }, 2000, function () {
							action.fadeOut(options.loading.speed);
						});
						return ;
					}
				},'json');
			}


			btn.on('click',function(e){
				e.stopPropagation();
				e.preventDefault();
				options.loading.start.call($(options.contentWrapper)[0],options);
			});
		});
	};
	var nooGetViewport = function() {
	    var e = window, a = 'inner';
	    if (!('innerWidth' in window )) {
	        a = 'client';
	        e = document.documentElement || document.body;
	    }
	    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
	};
	var nooGetURLParameters = function(url) {
	    var result = {};
	    var searchIndex = url.indexOf("?");
	    if (searchIndex == -1 ) return result;
	    var sPageURL = url.substring(searchIndex +1);
	    var sURLVariables = sPageURL.split('&');
	    for (var i = 0; i < sURLVariables.length; i++)
	    {       
	        var sParameterName = sURLVariables[i].split('=');      
	        result[sParameterName[0]] = sParameterName[1];
	    }
	    return result;
	};
	var nooInit = function() {
		if($( '.navbar' ).length) {
			var $window = $( window );
			var $body   = $( 'body' ) ;
			var navTop = $( '.navbar' ).offset().top;
			var lastScrollTop = 0,
				navHeight = 0,
				defaultnavHeight = $( '.navbar-nav' ).outerHeight();
			
			var navbarInit = function () {
				if(nooGetViewport().width > 992){
					var $this = $( window );
					var $navbar = $( '.navbar' );
					if ( $navbar.hasClass( 'fixed-top' ) ) {
						var navFixedClass = 'navbar-fixed-top';
						if( $navbar.hasClass( 'shrinkable' )  && !$body.hasClass('one-page-layout')) {
							navFixedClass += ' navbar-shrink';
						}
						var adminbarHeight = 0;
						if ( $body.hasClass( 'admin-bar' ) ) {
							adminbarHeight = $( '#wpadminbar' ).outerHeight();
						}
						var checkingPoint = navTop + defaultnavHeight;
						if($body.hasClass('one-page-layout')){
							checkingPoint = navTop;
						}

						if ( ($this.scrollTop() + adminbarHeight) > checkingPoint ) {
							if( $navbar.hasClass( 'navbar-fixed-top' ) ) {
								lastScrollTop = $this.scrollTop();
								return;
							}

							if( ! $navbar.hasClass('navbar-fixed-top') && ( ! $navbar.hasClass( 'smart_scroll' ) ) || ( $this.scrollTop() < lastScrollTop ) ) {								
								navHeight = $navbar.hasClass( 'shrinkable' ) ? Math.max(Math.round($( '.navbar-nav' ).outerHeight() - ($this.scrollTop() + adminbarHeight) + navTop),60) : $( '.navbar-nav' ).outerHeight();
								if($body.hasClass('one-page-layout')){
									navHeight = defaultnavHeight;
								}
								$('.navbar-wrapper').css({'min-height': navHeight+'px'});
								$navbar.closest('.noo-header').css({'position': 'relative'});
								$navbar.css({'min-height': navHeight+'px'});
								$navbar.find('.navbar-nav > li > a').css({'line-height': navHeight+'px'});
								$navbar.find('.navbar-brand').css({'height': navHeight+'px'});
								$navbar.find('.navbar-brand img').css({'max-height': navHeight+'px'});
								$navbar.find('.navbar-brand').css({'line-height': navHeight+'px'});
								$navbar.find('.calling-info').css({'max-height': navHeight+'px'});
								$navbar.addClass( navFixedClass );
								if( !$body.hasClass('one-page-layout') ) {
									$navbar.css('top', 0 - navHeight).animate( { 'top': adminbarHeight }, 300);
								} else {
									$navbar.css('top', adminbarHeight);
								}

								lastScrollTop = $this.scrollTop();
								return;
							}
						}

						lastScrollTop = $this.scrollTop();

						$navbar.removeClass( navFixedClass );
						$navbar.css({'top': ''});
						
						$('.navbar-wrapper').css({'min-height': ''});
						$navbar.closest('.noo-header').css({'position': ''});
						$navbar.css({'min-height': ''});
						$navbar.find('.navbar-nav > li > a').css({'line-height': ''});
						$navbar.find('.navbar-brand').css({'height': ''});
						$navbar.find('.navbar-brand img').css({'max-height': ''});
						$navbar.find('.navbar-brand').css({'line-height': ''});
								$navbar.find('.calling-info').css({'max-height': ''});
					}
				}
			};
			$window.bind('scroll',navbarInit).resize(navbarInit);
			if( $body.hasClass('one-page-layout') ) {
				var adminbarHeight = 0;
				if ( $body.hasClass( 'admin-bar' ) ) {
					adminbarHeight = $( '#wpadminbar' ).outerHeight();
				}
	
				// Scroll link
				$('.navbar-scrollspy > .nav > li > a[href^="#"]').click(function(e) {
					e.preventDefault();
					var target = $(this).attr('href').replace(/.*(?=#[^\s]+$)/, '');
					if (target && ($(target).length)) {
						var position = Math.max(0, $(target).offset().top );
							position = Math.max(0,position - (adminbarHeight + $('.navbar').outerHeight()) + 5);
						
						$('html, body').animate({
							scrollTop: position
						},{
							duration: 800, 
				            easing: 'easeInOutCubic',
				            complete: window.reflow
						});
					}
				});
				
				// Initialize scrollspy.
				$body.scrollspy({
					target : '.navbar-scrollspy',
					offset : (adminbarHeight + $('.navbar').outerHeight())
				});
				
				// Trigger scrollspy when resize.
				$(window).resize(function() {
					$body.scrollspy('refresh');
				});
	
			}
			
		}

		// Slider scroll bottom button
		$('.noo-slider-revolution-container .noo-slider-scroll-bottom').click(function(e) {
			e.preventDefault();
			var sliderHeight = $('.noo-slider-revolution-container').outerHeight();
			$('html, body').animate({
				scrollTop: sliderHeight
			}, 900, 'easeInOutExpo');
		});
		
		//Portfolio hover overlay
		$('body').on('mouseenter', '.masonry-style-elevated .masonry-portfolio.no-gap .masonry-item', function(){
			$(this).closest('.masonry-container').find('.masonry-overlay').show();
			$(this).addClass('masonry-item-hover');
		});
	
		$('body').on('mouseleave ', '.masonry-style-elevated .masonry-portfolio.no-gap .masonry-item', function(){
			$(this).closest('.masonry-container').find('.masonry-overlay').hide();
			$(this).removeClass('masonry-item-hover');
		});
		
		//Init masonry isotope
		$('.masonry').each(function(){
			var self = $(this);
			var $container = $(this).find('.masonry-container');
			var $filter = $(this).find('.masonry-filters a');
			$container.isotope({
				itemSelector : '.masonry-item',
				transitionDuration : '0.8s',
				masonry : {
					'gutter' : 0
				}
			});
			
			imagesLoaded(self,function(){
				$container.isotope('layout');
			});
			
			$filter.click(function(e){
				e.stopPropagation();
				e.preventDefault();
				
				var $this = jQuery(this);
				// don't proceed if already selected
				if ($this.hasClass('selected')) {
					return false;
				}
				self.find('.masonry-result h3').text($this.text());
				var filters = $this.closest('ul');
				filters.find('.selected').removeClass('selected');
				$this.addClass('selected');
	
				var options = {
					layoutMode : 'masonry',
					transitionDuration : '0.8s',
					'masonry' : {
						'gutter' : 0
					}
				}, 
				key = filters.attr('data-option-key'), 
				value = $this.attr('data-option-value');
	
				value = value === 'false' ? false : value;
				options[key] = value;
	
				$container.isotope(options);
				
			});
		});
		
		//Go to top
		$(window).scroll(function () {
			if ($(this).scrollTop() > 500) {
				$('.go-to-top').addClass('on');
			}
			else {
				$('.go-to-top').removeClass('on');
			}
		});
		$('body').on( 'click', '.go-to-top', function () {
			$("html, body").animate({
				scrollTop: 0
			}, 800);
			return false;
		});
		
		//Search
		$('body').on( 'click', '.search-button', function() {
			if ($('.searchbar').hasClass('hide'))
			{
				$('.searchbar').removeClass('hide').addClass('show');
				$('.searchbar #s').focus();
			}
			return false;
		});
		$('body').on('mousedown', $.proxy( function(e){
			var element = $(e.target);
			if(!element.is('.searchbar') && element.parents('.searchbar').length === 0)
			{
				$('.searchbar').removeClass('show').addClass('hide');
			}
		}, this) );

		//Shop mini cart
		$(document).on("mouseenter", ".noo-menu-item-cart", function() {
			clearTimeout($(this).data('timeout'));
			$('.searchbar').removeClass('show').addClass('hide');
			$('.noo-minicart').fadeIn(50);
		});
		$(document).on("mouseleave", ".noo-menu-item-cart", function() {
			var t = setTimeout(function() {
				$('.noo-minicart').fadeOut(50);
			}, 400);
			$(this).data('timeout', t);
		});	
		
		//Shop QuickView
		$(document).on('click','.shop-loop-quickview',function(e){
			var $this = $(this);
			$this.addClass('loading');
			$.post(nooL10n.ajax_url,{
				action: 'woocommerce_quickview',
				product_id: $(this).data('product_id')
			},function(responsive){
				$this.removeClass('loading');
				$modal = $(responsive);
				$('body').append($modal);
				$modal.modal('show');
				$modal.on('hidden.bs.modal',function(){
					$modal.remove();
				});
			});
			e.preventDefault();
			e.stopPropagation();
		});
	};

	function noo_price(price){
		var $currency_position = nooGmapL10n.currency_position,
		    $format;
		switch ( $currency_position ) {
			case 'left' :
				$format = '%1$s%2$s';
				break;
			case 'right' :
				$format = '%2$s%1$s';
				break;
			case 'left_space' :
				$format = '%1$s&nbsp;%2$s';
				break;
			case 'right_space' :
				$format = '%2$s&nbsp;%1$s';
				break;
		}
		price = noo_number_format(price,nooGmapL10n.num_decimals,nooGmapL10n.decimal_sep,nooGmapL10n.thousands_sep)
		return $format.replace('%1$s',nooGmapL10n.currency).replace('%2$s',price);
	}
	$( document ).ready( function () {
		nooInit();
	});
	
	$(document).bind('noo-layout-changed',function(){
		nooInit();	
	});


	/**
	 * Process form login
	 */
		if ( $('#loginmodal').length > 0 ) {

			$('#close-fomr-login').click(function(event) {
				
				$('#loginmodal').modal();

			});

					
			$('.open-register').on('click', function(event) {
				event.preventDefault();
				
				$('.noo-box-register').show();
				$('#noo-form-title').html( $('.open-register').data('title') );
				
				$('#loginmodal').modal();

				$('.noo-box-login').hide();
				$('#noo-forgot-password').hide();
				$('.notice-form-login').hide();


			});


			$('.open-login').on('click', function(event) {
				event.preventDefault();
				
				$('.noo-box-login').show();
				$('#noo-form-title').html( $('.open-login').data('title') );
				
				$('#loginmodal').modal();

				$('.noo-box-register').hide();
				$('#noo-forgot-password').hide();
				$('.notice-form-login').hide();


			});

			$('.open-forgot').on('click', function(event) {
				event.preventDefault();

				$('#noo-forgot-password').show();
				$('#noo-form-title').html( $('.open-forgot').data('title') );
				
				$('#loginmodal').modal();

				$('.noo-box-register').hide();
				$('.noo-box-login').hide();
				$('.notice-form-login').hide();

			});

			$('.open-form-register').on('click', function(event) {
				event.preventDefault();
				
				$('.noo-box-register').show();
				$('#noo-form-title').html( $('.open-register').data('title') );

				$('.noo-box-login').hide();
				$('#noo-forgot-password').hide();
				$('.notice-form-login').hide();


				$('#loginmodal').modal();

			});

			$('.open-form-login').on('click', function(event) {
				event.preventDefault();

				$('.noo-box-login').show();
				$('#noo-form-title').html( $('.open-login').data('title') );
				$('.noo-box-register').hide();
				$('#noo-forgot-password').hide();
				$('.notice-form-login').hide();

				$('#loginmodal').modal();

			});

			/**
			 * Event process when click button login
			 */
				if ( $('.noo-login').length > 0 ) {

					$('.noo-login').each(function(index, el) {

						$(this).one('click', function(event) {
							event.preventDefault();
							
							/**
							 * VAR
							 */
								var $$            = $(this),
									class_wrap	  = $( $$.data('class-wrap') ),
									data          = class_wrap.serialize(),
									user_login    = class_wrap.find('input[name="user_log"]').val(),
									user_password = class_wrap.find('input[name="user_pass"]').val();

							/**
							 * Check value empty
							 */
								if ( user_login == '' || user_password == '' ) {

									if ( user_login == '' ) {

										class_wrap.find('.login_message_area').removeClass('success').addClass('error').html( nooL10n.notice_empty );
										class_wrap.find('.user_log').addClass('has-error');

									} else {

										class_wrap.find('.user_log').removeClass('has-error');

									}

									if ( user_password == '' ) {

										class_wrap.find('.login_message_area').removeClass('success').addClass('error').html( nooL10n.notice_empty );
										class_wrap.find('.user_pass').addClass('has-error');

									} else {
										
										class_wrap.find('.user_pass').removeClass('has-error');

									}

									return;

								}

							/**
							 * Add item to list data
							 */
								data   += '&action=noo_login';
								data   += '&process=login';
								data   += '&security=' + nooL10n.security;

							/**
							 * Process
							 */
								$.ajax({
									url: nooL10n.ajax_url,
									type: 'POST',
									dataType: 'html',
									data: data,
									beforeSend: function() {
										$$.append( '<i class="fa fa-spinner fa-spin"></i>' );
										class_wrap.find('.login_message_area').removeClass('error success').html('');
									}
								})
								.done(function( response ) {
									/**
									 * conver data json
									 */
										var results = $.parseJSON(response);

										$$.find('i').remove()

									if ( results.status === 'success' ) {

										class_wrap.find('.login_message_area').removeClass('error').addClass('success').html( results.msg );

		                                window.location.replace( results.redirecturl );
										

									} else if ( results.status === 'error' ) {

										class_wrap.find('.login_message_area').removeClass('success').addClass('error').html( results.msg );

									}

								})
								.fail(function() {
									console.log("error");
								})
								

						});
						
					});

				}


			/**
			 * Check client
			 */
				if ( $('.user_terms').length > 0 ) {

					$('.user_terms').each(function(index, el) {
						
						$(this).change(function(event) {

							/**
							 * VAR
							 */
								var $$ 				= $(this),
									id_register     = $$.data('id-register');

							/**
							 * Process
							 */
							if ( this.checked ) {

						        $('#' + id_register).prop("disabled", false); 

						    } else {

						        $('#' + id_register).prop("disabled", true); 

						    }

						});
					});

				}

			/**
			 * Event process when click button register
			 */
				
				if ( $('.noo-register').length > 0 ) {

					$('.noo-register').each(function(index, el) {

						$(this).one('click', function(event) {

							event.preventDefault();
							
							/**
							 * VAR
							 */
								var $$                   = $(this),
									class_wrap			 = $( $$.data('class-wrap') );
									data                 = class_wrap.serialize(),
									user_login           = class_wrap.find('input[name="user_login"]').val(),
									user_email           = class_wrap.find('input[name="user_email"]').val(),
									user_password        = class_wrap.find('input[name="user_password"]').val(),
									user_password_retype = class_wrap.find('input[name="user_password_retype"]').val();

							/**
							 * Check value empty
							 */
								if ( user_login == '' || user_email == '' || user_password == '' || user_password_retype == '' ) {

									if ( user_login == '' ) {

										class_wrap.find('.register_message_area').removeClass('success').addClass('error').html( nooL10n.notice_empty );
										$('.user_login').addClass('has-error');

									} else {

										$('.user_login').removeClass('has-error');

									}

									if ( user_email == '' ) {

										class_wrap.find('.register_message_area').removeClass('success').addClass('error').html( nooL10n.notice_empty );
										$('.user_email').addClass('has-error');

									} else {

										$('.user_email').removeClass('has-error');

									}

									if ( user_password == '' ) {

										class_wrap.find('.register_message_area').removeClass('success').addClass('error').html( nooL10n.notice_empty );
										$('.user_password').addClass('has-error');

									} else {

										$('.user_password').removeClass('has-error');

									}

									if ( user_password_retype == '' ) {

										class_wrap.find('.register_message_area').removeClass('success').addClass('error').html( nooL10n.notice_empty );
										$('.user_password_retype').addClass('has-error');

									} else {

										$('.user_password_retype').removeClass('has-error');

									}

									return;

								}

							/**
							 * Check pass
							 */
								if ( user_password_retype !== user_password ) {

									class_wrap.find('.register_message_area').removeClass('success').addClass('error').html( nooL10n.wrong_pass );
									class_wrap.find('.user_password_retype').addClass('has-error');

									return;

								}
								class_wrap.find('.user_login').removeClass('has-error');
								class_wrap.find('.user_email').removeClass('has-error');
								class_wrap.find('.user_password').removeClass('has-error');
								class_wrap.find('.user_password_retype').removeClass('has-error');

							/**
							 * Add item to list data
							 */
								data   += '&action=noo_login';
								data   += '&process=register';
								data   += '&security=' + nooL10n.security;

							/**
							 * Process
							 */
								$.ajax({
									url: nooL10n.ajax_url,
									type: 'POST',
									dataType: 'html',
									data: data,
									beforeSend: function() {
										$$.append( '<i class="fa fa-spinner fa-spin"></i>' );
										class_wrap.find('.register_message_area').removeClass('error success').html('');
									}
								})
								.done(function( response ) {
									/**
									 * conver data json
									 */
										var results = $.parseJSON(response);

										$$.find('i').remove()

									if ( results.status === 'success' ) {

										class_wrap.find('.register_message_area').removeClass('error').addClass('success').html( results.msg );

										window.location.replace( results.redirecturl );

									} else if ( results.status === 'error' ) {

										class_wrap.find('.register_message_area').removeClass('success').addClass('error').html( results.msg );

									}

								})
								.fail(function() {
									console.log("error");
								})
								

						});

					});

				}

			/**
			 * Event process when click button forgot password
			 */
				$('#noo-forgot').on('click', function(event) {
					event.preventDefault();
					
					/**
					 * VAR
					 */
						var $$            = $(this),
							data          = $('#noo-forgot-password').serialize(),
							user_forgot   = $('#user_forgot').val();

					/**
					 * Check value empty
					 */

						if ( user_forgot == '' ) {

							$('#forgot_message_area').removeClass('success').addClass('error').html( nooL10n.notice_empty );
							$('.user_forgot').addClass('has-error');
							return;

						} else {

							$('.user_forgot').removeClass('has-error');

						}


					/**
					 * Add item to list data
					 */
						data   += '&action=noo_login';
						data   += '&process=forgot';
						data   += '&security=' + nooL10n.security;

					/**
					 * Process
					 */
						$.ajax({
							url: nooL10n.ajax_url,
							type: 'POST',
							dataType: 'html',
							data: data,
							beforeSend: function() {
								$$.append( '<i class="fa fa-spinner fa-spin"></i>' );
								$('#forgot_message_area').removeClass('error success').html('');
							}
						})
						.done(function( response ) {
							/**
							 * conver data json
							 */
								var results = $.parseJSON(response);

								$$.find('i').remove()

							if ( results.status === 'success' ) {

								$('.login_message_area').removeClass('error').addClass('success').html( results.msg );

								$('.noo-box-register').hide();
								$('#noo-forgot-password').hide();
								$('.noo-box-login').show('slow');
								$('.user_log').val(user_forgot);
								$('.user_pass').val('');

								$('#noo-form-title').html( $('.open-login').data('title') );

							} else if ( results.status === 'error' ) {

								$('#forgot_message_area').removeClass('success').addClass('error').html( results.msg );

							}

						})
						.fail(function() {
							console.log("error");
						})
						

				});

		}

	$( document ).ready( function () {
		if($('.gprice').length){
			var gsearch_price = $('.gprice'),
			    min_price = gsearch_price.find('.gprice_min').data('min'),
			    max_price = gsearch_price.find('.gprice_max').data('max'),
			    current_min_price = gsearch_price.find('.gprice_min').val(),
			    current_max_price = gsearch_price.find('.gprice_max').val();

			// current_min_price = parseInt( min_price, 10 );
			// current_max_price = parseInt( max_price, 10 );
			gsearch_price.find( ".gprice-slider-range" ).slider({
				range: true,
				animate: true,
				min: min_price,
				max: max_price,
				values: [ current_min_price, current_max_price ],
				create : function( event, ui ) {
					// $this.tooltip({title:$this.text()});
					var controls = $(this).find('.ui-slider-handle');
					$(controls[0]).tooltip({title:noo_price(current_min_price),placement:'top',container:'body',html:true});
					$(controls[1]).tooltip({title:noo_price(current_max_price) ,placement:'top',container:'body',html:true});
				},
				slide: function( event, ui ) {
					var controls = $(this).find('.ui-slider-handle');
					if( ui.value == ui.values[0] ) {
						gsearch_price.find( 'input.gprice_min' ).val( ui.values[0] ).trigger('change');
						$(controls[0]).attr('data-original-title', noo_price(ui.values[0])).tooltip('fixTitle').tooltip('show');
					}

					if( ui.value == ui.values[1] ) {
						gsearch_price.find( 'input.gprice_max' ).val( ui.values[1] ).trigger('change');
						$(controls[1]).attr('data-original-title', noo_price(ui.values[1])).tooltip('fixTitle').tooltip('show');
					}
				}
			});
		}

		if($('.garea').length){
			var gsearch_area = $('.garea'),
			    min_area = gsearch_area.find('.garea_min').data('min'),
			    max_area = gsearch_area.find('.garea_max').data('max'),
			    current_min_area = gsearch_area.find('.garea_min').val(),
			    current_max_area = gsearch_area.find('.garea_max').val();

			// current_min_area = parseInt( min_area, 10 );
			// current_max_area = parseInt( max_area, 10 );
			gsearch_area.find( ".garea-slider-range" ).slider({
				range: true,
				animate: true,
				min: min_area,
				max: max_area,
				values: [ current_min_area, current_max_area ],
				create : function( event, ui ) {
					// $this.tooltip({title:$this.text()});
					var controls = $(this).find('.ui-slider-handle');
					$(controls[0]).tooltip({title:current_min_area+' '+nooGmapL10n.area_unit,placement:'bottom',container:'body',trigger:'hover focus',html:true});
					$(controls[1]).tooltip({title:current_max_area +' '+nooGmapL10n.area_unit ,placement:'bottom',container:'body',trigger:'hover focus',html:true});
				},
				slide: function( event, ui ) {
					var controls = $(this).find('.ui-slider-handle');
					if( ui.value == ui.values[0] ) {
						gsearch_area.find( 'input.garea_min' ).val( ui.values[0] ).trigger('change');
						$(controls[0]).attr('data-original-title', ui.values[0] +' '+nooGmapL10n.area_unit).tooltip('fixTitle').tooltip('show');
					}

					if( ui.value == ui.values[1] ) {
						gsearch_area.find( 'input.garea_max' ).val( ui.values[1] ).trigger('change');
						$(controls[1]).attr('data-original-title', ui.values[1] +' '+nooGmapL10n.area_unit).tooltip('fixTitle').tooltip('show');
					}
				}
			});
		}
	});
	/**
	 * Open tooltip
	 */
		$('.agent-email').tooltip({html: true,container:$('body'),placement:'top'});


})(jQuery);