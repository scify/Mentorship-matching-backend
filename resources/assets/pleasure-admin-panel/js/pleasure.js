var Pleasure = {

	// Pleasure Theme Settings
	settings: {
		name: "Pleasure Dashboard Theme",
		version: "1.0.0",
		rippledButtons: true,

		paths: {
			images: "../../assets/globals/img",
			css: "../assets/globals/css",
			plugins: "assets/globals/plugins"
		},

		ga: {
			urchin: "UA-11816554-27", // UA-XXXXXX-XX
			url: "auto" // or subdomain.teamfox.co
		}
	},

	// Defined Colors
	colors: {
		white: "#fff",
		black: "#000",
		primary: "#5677fc",
		info: "#03a9f4",
		success: "#259b24",
		warning: "#ffc107",
		danger: "#ff5722",

		// Material Colors
		red: "#f34235",
		pink: "#e81d62",
		purple: "#9b26af",
		deep_purple: "#6639b6",
		indigo: "#3e50b4",
		blue: "#2095f2",
		light_blue: "#02a8f3",
		cyan: "#00bbd3",
		teal: "#009587",
		green: "#4bae4f",
		light_green: "#8ac249",
		lime: "#ccdb38",
		yellow: "#feea3a",
		amber: "#fec006",
		orange: "#fe9700",
		deep_orange: "#fe5621",
		brown: "#785447",
		grey: "#9d9d9d",
		blue_grey: "#5f7c8a",

		// Flat colors
		turquoise: "#1abc9c",
		green_sea: "#16a085",
		emerald: "#2ecc71",
		nephritis: "#27ae60",
		peter_river: "#3498db",
		belize_hole: "#2980b9",
		amethyst: "#9b59b6",
		wisteria: "#8e44ad",
		wet_asphalt: "#34495e",
		midnight_blue: "#2c3e50",
		sunflower: "#f1c40f",
		orange: "#f39c12",
		carrot: "#e67e22",
		pumpkin: "#d35400",
		alizarin: "#e74c3c",
		pomegranate: "#c0392b",
		clouds: "#ecf0f1",
		silver: "#bdc3c7",
		concrete: "#95a5a6",
		asbestos: "#7f8c8d"
	},

	callOnResize: [],

	// Detect the browser
	checkTouchScreen: function () {
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			$('body').addClass('touch-screen');
			return true;
		} else {
			$('body').removeClass('touch-screen');
			return false;
		}
	},

	// If window viewports change, call defined functions in callOnResize array
	handleElementsOnResizing: function () {
		var resizing;
		$(window).resize(function () {
			if(resizing) {
				clearTimeout(resizing);
			}
			resizing = setTimeout(function () {
				//Pleasure.toggleSidebarMenu();
				for (var i = 0; i < Pleasure.callOnResize.length; i++) {
					Pleasure.callOnResize[i].call();
				}
			}, 300);
		});
	},

	// Go to top
	goToTop: function () {
		$('a[href="#top"]').click(function () {
			$('html, body').animate({ scrollTop: 0 }, 'fast');
			return false;
		});
	},

	// Handle Dropdown Menu
	handleDropdownMenu: function () {
		// Hold dropdown on click
		$('body').on('click', '.dropdown-menu.keep-open', function (e) {
			e.stopPropagation();
		});
	},

	listenPanelEvents: function () {
		$('.card a[data-toggle="fake-reload"]').on('click',function() {
			var $this = $(this),
					card = $this.parents('.card');

			card.append('<div class="refresh-container"><div class="loading-bar indeterminate"></div></div>');
			setTimeout(function(){
				card.find('.refresh-container').fadeOut(500, function() { card.find('.refresh-container').remove(); });
			}, 2000);
		});

		$('.panel-action a[data-toggle="panel"]').on('click',function() {
			$(this).parents('.panel:first').toggleClass('collapsed');
		});

		$('.panel-action a[data-toggle="remove"]').on('click',function() {
			$(this).parents('.panel:first').remove();
		});

		$('.panel-action a[data-toggle="fake-reload"]').on('click',function() {
			var $this = $(this),
					par = $this.parents('.panel');

			par.find('.panel-body').append('<div class="refresh-container"><div class="loading-bar indeterminate"></div></div>');
			setTimeout(function(){
				par.find('.refresh-container').fadeOut(500, function() { par.find('.refresh-container').remove(); });
			}, 2000);
		});

		$('.panel-action a[data-toggle="reload"]').on('click',function(){
			// Define the panel variables
			var $this = $(this),
					par = $this.parents('.panel:first'),
					dataToRefresh = par.find('.refresh-data'),
					ajax_target = $this.data('ajax-target'),
					ajax_selector = $this.data('ajax-selector');
					ajax_notify = $this.data('ajax-notify');
					ajax_notify_sound = $this.data('ajax-notify-sound');

			// Loading ajax content
			if(ajax_target) {
				var jqxhr = $.ajax({
					cache: false,
					url: ajax_target,
					// Before requesting ajax content trigger spinners and remove the previous alert
					beforeSend: function() {
						$this.addClass("fa-spin");
						par.find('.panel-body').append('<div class="refresh-container"><div class="loading-bar indeterminate"></div></div>');
						par.find('.alert').remove();
					}
				}).done(function(data) {
					par.find('.refresh-container').fadeOut(500, function() { par.find('.refresh-container').remove(); });
					$this.removeClass('fa-spin');

					// Handle the ajax selector
					if(ajax_selector === 'prepend') {
						par.find('.panel-body').prepend(data);
					} else if(ajax_selector === 'append') {
						par.find('.panel-body').append(data);
					} else if(ajax_selector === 'inside') {
						par.find('.panel-body').html(data);
					} else {
						$(ajax_selector).html(data);
					}

					// Handle notification types
					if(ajax_notify === 'toastr')
						toastr.success('The '+ajax_target+' content successfully loaded.');
					if(ajax_notify_sound)
						ion.sound.play(ajax_notify_sound);

				}).fail(function(jqXHR, textStatus) {
					par.find('.refresh-container').fadeOut(500, function() { par.find('.refresh-container').remove(); });
					$this.removeClass('fa-spin');

					// Handle notification types
					if(ajax_notify === 'toastr') {
						toastr.error('There was a problem while loading the '+ajax_target+' content.');
					} else {
						par.find('.panel-body').prepend('<div class="alert alert-danger alert-block" role="alert">There was a problem while loading the '+ajax_target+' content.</div>');
					}
					if(ajax_notify_sound)
						ion.sound.play(ajax_notify_sound);
				});
			}
		});

		// Load ajax data on page init
		$('.panel a[data-init-load="true"]').click();

	},

	draggablePortlets: function () {
		$('.draggable-portlets').sortable({
			connectWith: '.portlets',
			handle: '.portlet-handle',
			cancel: '.portlet-handle-cancel',
			placeholder: 'portlet-placeholder',
			opacity: 0.5,
			dropOnEmpty: true,
			forcePlaceholderSize: true,
			update: function (event, ui) {
				var sorted = $(this).sortable('toArray').toString();
				//console.log(sorted);
				/* POST to server using $.post or $.ajax
				$.ajax({
					data: data,
					type: 'POST',
					url: '/your/url/here'
				});*/
			}
		});
	},

	handleLoadingButtons: function () {
		$('.demo-btn-loading').click(function () {
			var btn = $(this);
			btn.button('loading');
			setTimeout(function () {
				btn.button('reset');
			}, 1500); // Simulate for real loading 1,5 second
		});
	},

	handleTooltipsAndPopovers: function () {
		$('body').tooltip({ selector: '[data-toggle="tooltip"]' });
		$('body').popover({ selector: '[data-toggle="popover"]' });
	},

	handleTabs: function () {
		var hash = document.location.hash;
		var prefix = "tab_";
		if (hash) {
			$('.nav a[href='+hash.replace(prefix,"")+']').trigger('click');
		}
		// Change hash for page-reload
		$(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
			window.location.hash = e.target.hash.replace("#", "#" + prefix);
		});
	},

	handleAccordionAndToggles: function () {
		$('.accordion a[data-toggle="collapse"]').click(function () {
			$(this).parents('.accordion').find('.panel-heading').removeClass('active');
			if ( $(this).parent().next().hasClass('in') ) {
				$(this).parents('.panel-heading').removeClass('active');
			}
			else {
				$(this).parents('.panel-heading').addClass('active');
			}
		});
		$('.toggle a[data-toggle="collapse"]').click(function () {
			$(this).parents('.panel-heading').toggleClass('active');
		});
	},

	handleToastrSettings: function (closeButton, positionClass, sticky, type, closeOthers, title, notification) {

		closeButton = (closeButton !=false) ? true : false;
		positionClass = (!positionClass) ? 'toast-top-right' : positionClass;
		sticky = (sticky !=true) ? timeOut = 5000 : timeOut = 0;

		if(closeOthers == true)
			toastr.remove();
		if(!title)
			title = '';
		if(!notification){
			notification = 'Sample Notification';
		}

		toastr.options = {
			'closeButton': closeButton,
			'positionClass': positionClass,
			'timeOut': timeOut,
			'closeHtml': '<button><i class="ion-android-close"></i></button>',
			'hideMethod': 'slideUp',
			'hideDuration': '500'
		};

		if(type === 'success') {
			toastr.success(notification, title);
		} else if(type === 'warning') {
			toastr.warning(notification, title);
		} else if(type === 'error') {
			toastr.error(notification, title);
		} else {
			toastr.info(notification, title);
		}

	},

	listenToastrNotification: function () {
		$('body').on('click', '.toastr-notify', function () {
			var $this = $(this),
					closeButton = $this.data('toastr-close-button'),
					positionClass = $this.data('toastr-position'),
					sticky = $this.data('toastr-sticky'),
					type = $this.data('toastr-type'),
					closeOthers = $this.data('toastr-close-others'),
					title = $this.data('toastr-title')
					notification = $this.data('toastr-notification');
			Pleasure.handleToastrSettings(closeButton, positionClass, sticky, type, closeOthers, title, notification);
		});
	},

	handleFastClick: function () {
		window.addEventListener('load', function () {
			FastClick.attach(document.body);
		}, false);
	},

	initSelectPicker: function () {
		// Find select elements with "selecter" class
		$('select.selecter').selectpicker();
	},

	initAutoSizeTextarea: function () {
		// Find textarea elements with "js-auto-size" class
		$('.js-auto-size').textareaAutoSize();
	},

	listenInputs: function () {
		// Find input and textarea elements with "inputer" class
		$('.inputer').on('keyup', function () {
			var $formControl = $(this).find('.form-control');
			if($formControl.val() !== undefined) {
                if ($formControl.val().length > 0)
                    $formControl.addClass('valid');
                else
                    $formControl.removeClass('valid');
            }
		});

		// Add initial valid
		$('.inputer').trigger('keyup');

	},

	initInputerBorders: function () {
		// Disabled
		$('.inputer>.input-wrapper>.form-control:disabled').parents('.input-wrapper').addClass('disabled');

		// Readonly
		$('.inputer>.input-wrapper>.form-control[readonly]').parents('.input-wrapper').addClass('readonly');

		$('.inputer>.input-wrapper>.form-control').on('focus', function () {
			$('.input-wrapper.active').removeClass('active');
			$(this).parents('.input-wrapper').addClass('active');
		});
		$('.inputer>.input-wrapper>.form-control').on('blur', function () {
			$('.input-wrapper.active').removeClass('active');
		});
	},

	handleRippledButtons: function () {

		if( this.settings.rippledButtons ) {

			// Thanks to Andrew Tunnecliffe http://codepen.io/atunnecliffe/pen/VYayGp
			var element, ripple, d, x, y;
			var i = 1;
			var queue = [];

			$('.btn').addClass('btn-ripple');

			$(document).on('click', '.btn-ripple', function (e) {
				element = $(this);

				// remove old items from queue and DOM
				// allow max 5 to be loaded
				if (queue.length > 5) {
					$('._' + queue.shift()).remove();
				}

				// Assume user can't click more than 1000 times / second
				//terrible overflow protection.
				if (i > 1000) {
					i = 0;
				}

				// add next item to queue
				i++;
				queue.push(i);

				// Build element
				element.append('<span class="ripple _' + i + '"></span>');
				ripple = element.find('._' + i);

				// Make it big enough to cover whole parent
				if(!ripple.height() && !ripple.width()) {
					d = Math.max(element.outerWidth(), element.outerHeight());
					ripple.css({height: d, width: d});
				}

				// Get origin
				x = e.pageX - element.offset().left - ripple.width() / 2;
				y = e.pageY - element.offset().top - ripple.height() / 2 ;

				// Set location and animate
				ripple.css({top: y+'px', left: x+'px'}).addClass('animate');
			});

		}
	},

	// Material Meaningful Animation
	handleMaterialAnimation: function () {
		// inspired by Kupletsky Sergey http://codepen.io/zavoloklom/pen/wtApI
		var speed = 2000;
		var container =  $('.display-animation');
		container.each(function() {
			var elements = $(this).find('.material-animate');
			elements.each(function() {
					var elementOffset = $(this).offset();
					var offset = elementOffset.left*0.8 + elementOffset.top;
					var delay = parseFloat(offset/speed).toFixed(2);

					$(this)
						.css("-webkit-animation-delay", delay+'s')
						.css("-o-animation-delay", delay+'s')
						.css("animation-delay", delay+'s');

					makeWatcher( $(this) );
				});
		});

		function makeWatcher( element ) {
			var watcher = scrollMonitor.create( element );

			function addClass() {
				if (watcher.enterViewport) {
					element.addClass('material-animated');
					watcher.destroy();
				}
			}

			watcher.stateChange(addClass);
			addClass();
		}
	},

	// Listen Card Events
	listenCardEvents: function () {
		// Toggle Card News More
		$('.toggle-card-news-more').on('click', function () {
			$(this).parents('.card').toggleClass('card-active');
		});
	},

	// Clickable Cards Animations
	listenClickableCards: function () {
		// inspired by Balapa http://codepen.io/balapa/pen/embYYB

		$('.floating-open').on('click', function () {
			var $this = $(this);
			$this.parents('.clickable-button').addClass('clicked');
			$this.parents('.clickable-button').next('.layered-content').addClass('active');

			setTimeout(function () {
				$this.parents('.card-heading').css('overflow', 'hidden');
			}, 100);

		});

		$('.floating-close').on('click', function () {
			var $this = $(this);
			$this.parents('.layered-content').prev('.clickable-button').removeClass('clicked');
			$this.parents('.layered-content').removeClass('active');

			setTimeout(function () {
				$this.parents('.card-heading').css('overflow', 'initial');
			}, 600);

		});
	},

	scrollableTabs: function () {
		var $scrollableTabs = $('.scrollable-tabs');
		$scrollableTabs.each( function () {
			var $navtabs = $(this).find('.nav-tabs'),
					calculatedWidth = 0;
			$navtabs.children().each(function() { calculatedWidth += $(this).width(); });

			$navtabs.width(calculatedWidth+5);
		});
	},

	stickyHeader: function () {
		var $sticky = $('.sticky');

		if( $sticky.length > 0 ) {
			var stickyOffset = $sticky.offset().top;

			$( window ).scroll(function() {
				var scroll = $(window).scrollTop();

				if($('body').hasClass('layout-device')) {
					if (scroll + 61 >= stickyOffset)
						$sticky.addClass('fixed');
					else
						$sticky.removeClass('fixed');
				} else {
					if (scroll >= stickyOffset)
						$sticky.addClass('fixed');
					else
						$sticky.removeClass('fixed');
				}
			});
		}
	},

	init: function () {

		this.checkTouchScreen();

		this.handleElementsOnResizing();

		this.goToTop();
		this.handleDropdownMenu();
		this.listenPanelEvents();
		this.draggablePortlets();
		this.handleLoadingButtons();
		this.handleTooltipsAndPopovers();
		this.handleTabs();
		this.handleAccordionAndToggles();
		this.listenToastrNotification();
		this.handleFastClick();

		// Form Elements
		this.initSelectPicker();
		this.initAutoSizeTextarea();
		this.listenInputs();
		this.initInputerBorders();

		// Rippled Buttons
		this.handleRippledButtons();

		// Handle Material Animation
		this.handleMaterialAnimation();

		// Listen Card Events
		this.listenCardEvents();

		// Listen Clickable Cards
		this.listenClickableCards();

		// Scrollable Tabs
		this.scrollableTabs();

		// Sticky Header
		this.stickyHeader();
	}

};

