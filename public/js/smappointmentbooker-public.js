(function (global, factory) {
	typeof exports === 'object' && typeof module !== 'undefined' ? factory(require('jquery')) :
		typeof define === 'function' && define.amd ? define(['jquery'], factory) :
			(global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.jQuery));
}(this, (function ($) { 'use strict';

	$ = $ && $.hasOwnProperty('default') ? $['default'] : $;

	// *************** UI HELPERS **********************/
	window.modalStack = [];
	window.popup = function (modal, removeOnClose, animated) {
		if (typeof removeOnClose === 'undefined') removeOnClose = true;
		if (typeof animated === 'undefined') animated = true;
		if (typeof modal === 'string' && modal.indexOf('<') >= 0) {
			var _modal = document.createElement('div');
			_modal.innerHTML = modal.trim();
			if (_modal.childNodes.length > 0) {
				modal = _modal.childNodes[0];
				if (removeOnClose) modal.classList.add('remove-on-close');
				$('body').append(modal);
			}
			else return false; //nothing found
		}
		modal = $(modal);
		if (modal.length === 0) return false;
		if (modal.parents('body').length === 0) {
			if (removeOnClose) modal.addClass('remove-on-close');
			$('body').append(modal[0]);
		}
		modal.show();
		
		window.openModal(modal, animated);
		return modal[0];
	};
	window.openModal = function (modal, animated) {
		if (typeof animated === 'undefined') animated = true;
		modal = $(modal);
		modal[animated ? 'removeClass' : 'addClass']('not-animated');
		
		var isModal = modal.hasClass('modal');
		var isPopover = modal.hasClass('popover');
		var isPopup = modal.hasClass('popup');
		var isLoginScreen = modal.hasClass('login-screen');
		var isPickerModal = modal.hasClass('picker-modal');
		var isActions = modal.hasClass('actions-modal');
		
		// Modal Event Prefix
		var modalType = 'modal';
		if (isPopover) modalType = 'popover';
		if (isPopup) modalType = 'popup';
		if (isLoginScreen) modalType = 'loginscreen';
		if (isPickerModal) modalType = 'picker';
		if (isActions) modalType = 'actions';
		
		
		if ($('.modal.modal-in:not(.modal-out)').length && isModal) {
			window.modalStack.push(function () {
				window.openModal(modal);
			});
			return;
		}
		
		// do nothing if this modal already shown
		if (true === modal.data('f7-modal-shown')) {
			return;
		}
		modal.data('f7-modal-shown', true);
		
		// Move modal
		
		modal.on(modalType + ':close', function() {
			modal.removeData('f7-modal-shown');
		});
		
		if (isModal) {
			modal.show();
			modal.css({
				          marginTop: - Math.round(modal.outerHeight() / 2) + 'px'
			          });
		}
		
		var overlay;
		if (!isLoginScreen && !isPickerModal) {
			if ($('.modal-overlay').length === 0 && !isPopup) {
				$("body").append('<div class="modal-overlay"></div>');
			}
			if ($('.popup-overlay').length === 0 && isPopup) {
				$("body").append('<div class="popup-overlay"></div>');
			}
			overlay = isPopup ? $('.popup-overlay') : $('.modal-overlay');
		}
		
		if (overlay) {
			overlay[animated ? 'removeClass' : 'addClass']('not-animated');
		}
		
		//Make sure that styles are applied, trigger relayout;
		var clientLeft = modal[0].clientLeft;
		
		// Trugger open event
		modal.trigger('open ' + modalType + ':open');
		
		// Picker modal body class
		if (isPickerModal) {
			$('body').addClass('with-picker-modal');
		}
		
		
		// Classes for transition in
		if (!isLoginScreen && !isPickerModal) overlay.addClass('modal-overlay-visible');
		if (isPickerModal && overlay) overlay.addClass('modal-overlay-visible');
		if (animated) {
			modal.removeClass('modal-out').addClass('modal-in').on('transitionend',(function (e) {
				if (modal.hasClass('modal-out')) modal.trigger('closed ' + modalType + ':closed');
				else modal.trigger('opened ' + modalType + ':opened');
			}));
		}
		else {
			modal.removeClass('modal-out').addClass('modal-in');
			modal.trigger('opened ' + modalType + ':opened');
		}
		return true;
	};
	window.closeModal = function (modal, animated) {
		if (typeof animated === 'undefined') animated = true;
		modal = $(modal || '.modal-in');
		if (typeof modal !== 'undefined' && modal.length === 0) {
			return;
		}
		modal[animated ? 'removeClass' : 'addClass']('not-animated');
		var isModal = modal.hasClass('modal');
		var isPopover = modal.hasClass('popover');
		var isPopup = modal.hasClass('popup');
		var isLoginScreen = modal.hasClass('login-screen');
		var isPickerModal = modal.hasClass('picker-modal');
		var isActions = modal.hasClass('actions-modal');
		
		// Modal Event Prefix
		var modalType = 'modal';
		if (isPopover) modalType = 'popover';
		if (isPopup) modalType = 'popup';
		if (isLoginScreen) modalType = 'loginscreen';
		if (isPickerModal) modalType = 'picker';
		if (isActions) modalType = 'actions';
		
		var removeOnClose = modal.hasClass('remove-on-close');
		
		// For Actions
		var keepOnClose = modal.hasClass('keep-on-close');
		
		var overlay;
		
		if (isPopup) overlay = $('.popup-overlay');
		else {
			if (isPickerModal) overlay = $('.picker-modal-overlay');
			else if (!isPickerModal) overlay = $('.modal-overlay');
		}
		
		if (isPopup){
			if (modal.length === $('.popup.modal-in').length) {
				overlay.removeClass('modal-overlay-visible');
			}
		}
		else if (overlay && overlay.length > 0) {
			overlay.removeClass('modal-overlay-visible');
		}
		if (overlay) overlay[animated ? 'removeClass' : 'addClass']('not-animated');
		
		modal.trigger('close ' + modalType + ':close');
		
		// Picker modal body class
		if (isPickerModal) {
			$('body').removeClass('with-picker-modal');
			$('body').addClass('picker-modal-closing');
		}
		
		if (!(isPopover && !app.params.material)) {
			if (animated) {
				modal.removeClass('modal-in').addClass('modal-out').on('transitionend', (function (e) {
					if (modal.hasClass('modal-out')) modal.trigger('closed ' + modalType + ':closed');
					else {
						modal.trigger('opened ' + modalType + ':opened');
						if (isPopover) return;
					}
					
					if (isPickerModal) {
						$('body').removeClass('picker-modal-closing');
					}
					if (isPopup || isLoginScreen || isPickerModal || isPopover) {
						modal.removeClass('modal-out').hide();
						if (removeOnClose && modal.length > 0) {
							modal.remove();
						}
					}
					else if (!keepOnClose) {
						modal.remove();
					}
				}));
			}
			else {
				modal.trigger('closed ' + modalType + ':closed');
				modal.removeClass('modal-in modal-out');
				if (isPickerModal) {
					$('body').removeClass('picker-modal-closing');
				}
				if (isPopup || isLoginScreen || isPickerModal || isPopover) {
					modal.hide();
					if (removeOnClose && modal.length > 0) {
						modal.remove();
					}
				}
				else if (!keepOnClose) {
					modal.remove();
				}
			}
			if (isModal) {
				if (window.modalStack.length) {
					(window.modalStack.shift())();
				}
			}
		}
		else {
			modal.removeClass('modal-in modal-out not-animated').trigger('closed ' + modalType + ':closed').hide();
			if (removeOnClose) {
				modal.remove();
			}
		}
		return true;
	};
	window.showTab = function (tab, tabLink, animated, force) {
		var newTab = $(tab);

		if (arguments.length === 2 && typeof arguments[1] === 'boolean') {
			tab      = arguments[0];
			animated = arguments[1];
		}
		if (arguments.length === 3 && typeof arguments[1] === 'boolean' && typeof arguments[2] === 'boolean') {
			tab      = arguments[0];
			animated = arguments[1];
			force    = arguments[2];
		}
		if (typeof animated === 'undefined') animated = true;
		if (newTab.length === 0) return false;
		if (newTab.hasClass('active')) {
			if (force) newTab.trigger('show tab:show');
			return false;
		}
		var tabs = newTab.parent('.tabs');
		if (tabs.length === 0) return false;

		// Animated tabs
		var isAnimatedTabs = tabs.parent()
		                         .hasClass('tabs-animated-wrap');

		if (isAnimatedTabs) {
			tabs.parent()[animated ? 'removeClass' : 'addClass']('not-animated');
			var tabTranslate = (-newTab.index()) * 100;
			tabs.css({'transform':'translate3d(' + tabTranslate + '%,0,0)'});
		}

		// Remove active class from old tabs
		var oldTab = tabs.children('.tab.active')
		                 .removeClass('active')
		                 .trigger('hide tab:hide');
		// Add active class to new tab
		newTab.addClass('active');
		// Trigger 'show' event on new tab
		newTab.trigger('show tab:show');


		return true;
	};

	// *************** APP CONSTANT ********************/
	const APP_XHR_MAX_REQUEST = 10,
	      UI_CLICK_FUNCTION = 'click',
		  TEMPLATE_NOT_FOUND = 'Template not found';

	// *************** APPOINTMENT BOOKING FORM **********/
	window.AppointmentBookForm = function(container){
		let Me = this,
		    _xhrs = [],
		    _xhrQueue = [];

		let _$Container = $(container);
		let _AppointmentInformation = {
			'service-duration': 0,
			'service-addon-duration': 0
		};
		let _firstAvaiDate, _firstAvaiTime;
		let _$AppointmentInformationContainer;
		let _Services;

		// no container defined or not found in DOM, exit
		if (!_$Container) return;

		// save container and load templates to class member
		Me.$container = _$Container;
		Me.templates = {
			'select-service': _.template($("#template-select-service").html() || TEMPLATE_NOT_FOUND),
			'calendar': _.template($("#template-calendar").html() || TEMPLATE_NOT_FOUND),
			'customer-info': _.template($("#template-customer").html() || TEMPLATE_NOT_FOUND),
			'confirmation': _.template($("#template-confirmation").html() || TEMPLATE_NOT_FOUND),
			'booking-policy': _.template($("#template-booking-policy-popup").html() || TEMPLATE_NOT_FOUND),
			'technician-list': _.template($("#template-technician-list").html() || TEMPLATE_NOT_FOUND)
		};

		/**
		 * fetch get-services and render appointments
		 *
		 * @entry_point
		 */
		Me.render = function(){
			_$AppointmentInformationContainer = $(".appointment-information");
			if (!Me.salon_metadata) {

				Me.showIndicator();
				_requestServerData({
					URI: '/salon-metadata',
					onSuccess: function(r){
						Me.hideIndicator();
						_Services = r.services;
						Me.salon_metadata = r;
						_showServiceList(Me.salon_metadata.services);
					}, onError: function(){
						Me.hideIndicator();
						$("#sm-tab-service").html("<h2>Server Error</h2><p>We encounter a server error. Please reload this page or try again later.</p>");
					}
				});

				return;
			}

			_showServiceList(Me.salon_metadata.services);

		};

		window.showBookingPolicy = _show_booking_policy;
		window.requestServerData = _requestServerData;


		Me.showIndicator = () => {
			if (Me.$container.find("._indicator").length) return;
			Me._indicatorTimer = setTimeout(()=>{
				Me.$container.append("<div class='_indicator'><div class='spinner'></div></div>");
			}, 100);
		}
		Me.hideIndicator = () => {
			clearTimeout(Me._indicatorTimer);
			Me.$container.find("._indicator").remove();
		}
		// global event handler for UI
		_initGlobalEventHandlers();
		
		return;
		//========================== PRIVATE HELPERS ====================================================================/

		function _initGlobalEventHandlers(){
			_$Container.on(UI_CLICK_FUNCTION, '.service-item', _on_service_item_clicked);
			_$Container.on(UI_CLICK_FUNCTION, '.technician-item', _on_technician_item_clicked);
			_$Container.on(UI_CLICK_FUNCTION, '.time-item', _on_time_item_clicked);
			$(document).on(UI_CLICK_FUNCTION, '.show-booking-policy', _show_booking_policy);
			$(document).on(UI_CLICK_FUNCTION, '.close-popup', _close_popup);
			$(document).on(UI_CLICK_FUNCTION, '.first-availability, .select-first-availability', _select_first_availability);
		}
		
		/**
		global close popup
		 */
		function _close_popup(){
			var popup = $(this).data('popup');
			console.log(popup);
			if (popup) window.closeModal(popup);
		}
		/**
		 * show booking policy
		 */
		function _show_booking_policy(){
			$(window).scrollTop(0);
			var $policy = $(".hidden.booking-policy");
			if ($policy.length) window.popup(Me.templates['booking-policy']({content: $policy.html()}));
		}

		/**
		 * finalize appointment data and submit. then show confirmation
		 * @private
		 */
		function _on_finish_button_clicked(e){

			let $this = $(this);
			$this.prop('disabled', true);

			_setAppointmentInformation('customer-phone', $("#txtPhone").val().toString().replace(/\D/g, ''));
			_setAppointmentInformation('customer-name', $("#txtName").val());
			_setAppointmentInformation('note', $("#txtNote").val());
			_setAppointmentInformation('receive-sms-reminder', $("#chkReceiveSMSNotification").is(":checked"));
			_setAppointmentInformation('receive-sms-promotion', $("#chkReceiveSMSPromotion").is(":checked"));


			// validate information
			switch(true){
				case _AppointmentInformation['customer-phone'].length < 10:
					return alert("Invalid phone number. Please enter your phone number starting with area code.");
					$("#txtPhone").focus();
					break;
				case _AppointmentInformation['customer-name'].length < 2:
					return alert("Invalid name, please enter your full name");
					$("#txtName").focus();
					break;
				default:
			}

			Me.showIndicator();
			_requestServerData({
				URI: '/submit-appointment',
				method: 'POST',
				data: _AppointmentInformation,
				onSuccess: function(r){
					if (r.confirmation) {
						$("#sm-tab-confirmation").html(Me.templates['confirmation']({
							code: r.confirmation,
							AppointmentInfo: _AppointmentInformation
						                                                            }));
						showTab("#sm-tab-confirmation");
					}else {
						$this.prop('disabled', false);
					}
					Me.hideIndicator();
				},
				onError : ()=>{
					alert('We\'re sorry, an error has occurred during your process. Please try again.');
					$this.prop('disabled', false);
					Me.hideIndicator();
				}
			})
		}

		/**
		 * show customer tab
		 * @private
		 */
		function _showCustomerTab(){
			var $customerTab = $("#sm-tab-customer").html(Me.templates['customer-info']({
				AppointmentInfo: _AppointmentInformation
			}));
			showTab($customerTab);

			// go back event
			$customerTab.find('.go-back').on(UI_CLICK_FUNCTION, function(){
				showTab($customerTab.prev());
			});

			// check customer's phone and auto fill name
			$customerTab.find("#txtPhone").on('blur', function(){
				var $this = $(this),
				    phone = $this.val();

				phone = phone.toString().replace(/\D/g, '');

				_requestServerData({
					URI: '/get-customer-name',
					data: {
						phone: phone
					},
					onSuccess: function(r){
						if (r.name) $("#txtName").val(r.name);
					}
				});
			});

			// finish event
			$customerTab.find(".go-finish").on(UI_CLICK_FUNCTION, _on_finish_button_clicked);


		}
		/**
		 * set time for appointment
		 * @private
		 */
		function _on_time_item_clicked(){
			var $this = $(this),
			    time = $this.text() + ':00',
			    isGoBack = $this.is('.go-back');

			// if it is a go back, show previous form. (In this context it is form 1)
			if (isGoBack){
				showTab('#sm-tab-technician');
				return;
			}

			_setAppointmentInformation('service-time', time);
			_showCustomerTab();
		}
		/**
		 * show available time form when user select technician
		 * @param e
		 * @private
		 */
		function _on_technician_item_clicked(e){
			var $this = $(this),
			    id = $this.data('id'),
			    name = $this.text(),
			    isGoBack = $this.is('.go-back');

			Me.showIndicator();

			//
			if (id == -1) {
				//return Me.showIndicator();
			}

			// if it is a go back, show previous form. (In this context it is form 1)
			if (isGoBack){
				showTab('#sm-tab-service');
				return;
			}

			_setAppointmentInformation('technician-id', id);
			_setAppointmentInformation('technician-name', name);

			// now request information about technician first available

			_requestServerData({
				URI: '/st-get-technician-first-available',
				method: 'GET',
				data: {
					'service-duration': _AppointmentInformation['service-duration'] + _AppointmentInformation['service-addon-duration'],
					'technician-id': _AppointmentInformation['technician-id']
				},
				onSuccess: function (response) {
					let avails = response.data;
					let date = response.date;

					_requestCalendar();
					Me.hideIndicator();

					if (avails.length) {
						_firstAvaiDate = date;
						_firstAvaiTime = avails[0];
						$(".first-availability").html(avails[0] + " on " + _format_date(date));
					}
				},
				onError: () => {
					Me.hideIndicator();
					window.alert("We're sorry. But there is an error occurred during request. Please try again.");
				}
			})
		}

		function _format_date(raw){
			let date = new Date(raw + " 00:00:00");
			return date.toDateString();
		}

		function _select_first_availability(){
			_setAppointmentInformation('service-time', _firstAvaiTime + ":00");
			_setAppointmentInformation('service-date', _firstAvaiDate);
			_showCustomerTab();
		}

		/**
		 * triggered when client click on any service-item link in step 1 and 2 of the form
		 * this will bring user to step 2, select technician
		 * @private
		 */
		function _on_service_item_clicked(){

			var $this = $(this),
			    name = $this.data('internal-service-name'),
			    display_name = $this.data('name'),
			    duration = $this.data('duration'),
			    id = $this.data('id'),
				isGoBack = $this.is('.go-back'),
				isGoNext = $this.is('.go-next');

			// if it is a go back, show previous form. (In this context it is form 1)
			if (isGoBack){
				showTab('#sm-tab-service');
				return;
			}

			if (isGoNext) {
				_requestTechniciansList();
				return;
			}


			// find service metadata in cache
			var service = _.find(_Services, function(s){return s.id==id});

			if (service && service.service_is_addon == '0') {
				_setAppointmentInformation('service-name', name);
				_setAppointmentInformation('service-name-display', display_name);
				_setAppointmentInformation('service-duration', parseInt(duration));
				_setAppointmentInformation('service-id', id);

				// check if selected service has addon. If yes, show addon menu,
				// otherwise, shoZw next step

				if (service.service_addons.length) {
					_showAddonServiceList(service.service_addons);
				}else {
					_requestTechniciansList();
				}
			}else  {
				_setAppointmentInformation('service-addon', name);
				_setAppointmentInformation('service-addon-display', display_name);
				_setAppointmentInformation('service-addon-duration', parseInt(duration));
				_requestTechniciansList();
			}
			return ;

		}

		function _showTechnicianList(Technicians){
			$("#sm-tab-technician").html(
				Me.templates['technician-list']({Technicians: Technicians})
			);
			showTab("#sm-tab-technician");
		}

		function _getServerDate(date){
			return date.getFullYear() + "-" + doublenum(date.getMonth()+1) + "-" + doublenum(date.getDate());
			function doublenum(s){return s < 10 ? '0' + s.toString() : s}
		}
		
		/**
		 * display selection of time
		 * @param r xhr return from server
		 * @private
		 */
		function _append_available_time(r){

			if (!(r.available
			      && r.available.length > 0)) {
				$(".time-container").html(`I'm sorry, ${_AppointmentInformation['technician-name']} is not available on the selected date.`);
				return;
			}


			var $timeContainer = $(".time-container").empty(),
			    $morning = $("<div class='morning-time'>Morning</div>").appendTo($timeContainer),
			    $afternoon = $("<div class='afternoon-time'>Afternoon</div>").appendTo($timeContainer),
			    $evening = $("<div class='evening-time'>Evening</div>").appendTo($timeContainer);

			_.each(r.available, function(a){
				var entry = $('<li><a href="#" class="time-item">' + a + '</a></li>');

				if (a >= "16:00"){
					entry.appendTo($evening);
				}
				else if (a >= "12:00") {
					entry.appendTo($afternoon);
				}else {
					entry.appendTo($morning);
				}
			});
			
		}
		
		function _on_calendar_picked(o){

			var date = o.date;
			_setAppointmentInformation('service-date', _getServerDate(date));
			_requestServerData({
				URI: '/get-times',
				data: {
					date: _AppointmentInformation['service-date'],
					'technician-id': _AppointmentInformation['technician-id'],
					'service-id': _AppointmentInformation['service-id'],
					'service-duration': _AppointmentInformation['service-duration']
				},
				onSuccess: _append_available_time
			});
		}
		/**
		 * triggered after user selected Technician. we will load Calendar and its event to find hours
		 * @private
		 */
		function _requestCalendar(){
			console.log("Showing calendar");
			$("#sm-tab-calendar").html(
				Me.templates['calendar']({})
			);
			$("#txtDate").datepicker({
				autoHide: true,
				autoPick: true,
				inline: true,
				container: '.calendar-container',
				format: 'mm/dd/yyyy',
				startDate: _.now(),
				pick: _on_calendar_picked
			});
			showTab("#sm-tab-calendar");
		}

		/**
		 * request technician list based on selected service-id from server
		 * @private
		 */
		function _requestTechniciansList(){
			if (!Me.salon_metadata || !Me.salon_metadata.technicians) {
				// fall back to make request to server directly in case there is something wrong in the memory

				Me.showIndicator();
				_requestServerData({
					URI: '/get-technicians',
					data: {
						'service-id': _AppointmentInformation['service-id']
					},
					onSuccess: function(r){
						Me.hideIndicator();
						_showTechnicianList(r.data);
					},
					onError: function(r){
						Me.hideIndicator();
						window.alert("We're sorry. But there is an error occurred during request. Please try again.");
					}
				});
			}else {
				// find the technician that has service-id in technician_services property
				let technicians = [];
				technicians = _.filter(Me.salon_metadata.technicians, tech => {
					return typeof(tech.technician_services) === 'string' && tech.technician_services.split(",").indexOf(String(_AppointmentInformation['service-id'])) > -1;
				});
				_showTechnicianList(technicians);
			}

		}

		/**
		 * shows addon service list
		 * @private
		 */
		function _showAddonServiceList(addons){
			if (typeof addons === 'string') {
				try {
					addons = JSON.parse(addons);
				}catch(e){
					addons = [];
				}
			}
			if (!addons.length) return _requestTechniciansList();

			Me.$container.find("#sm-tab-addon-service").html(Me.templates['select-service']({isAddon: true, Services: addons}));
			showTab("#sm-tab-addon-service");
		}

		/**
		 * show Service list
		 * @param Services
		 * @private
		 */
		function _showServiceList(Services){
			Me.$container.find("#sm-tab-service").html(Me.templates['select-service']({isAddon: false,Services: Services}));
		}


		function _setAppointmentInformation(name, value){
			_AppointmentInformation[name] = value;
			if (name=='service-addon') {
				_AppointmentInformation['service-name'] = _AppointmentInformation['service-name'] + ' + ' + value;
				_AppointmentInformation['service-name-display'] = _AppointmentInformation['service-name-display'] + ' + ' + value;
			}

			// add service-addon-duration to service-duration
			if (name=='service-addon-duration'){
				_AppointmentInformation['service-duration'] += parseInt(value);
			}
		}
		/**
		 * request data from server
		 * @param options
		 * @private
		 */
		function _requestServerData(options){
			var api_url = window.SMAPIServer.url;
			var api_token = window.SMAPIServer['auth-token'];

			window._xhrs = _xhrs;
			window._xhrQueue = _xhrQueue;

			options = _.extend({
				URI: '/404',
				method: 'GET',
				async: false,
				data: null,
				onSuccess: function(){},
				onError: function(){}
			}, options);


			// first check concurrent requests. if concurrent requests is greater than APP_XHR_MAX_REQUEST
			// psh the options data to the queue for later access
			if (_xhrs.length >= APP_XHR_MAX_REQUEST) {
				_xhrQueue.push(options);
			}else{
				__proceedRequest(options);
			}

			return;

			function __showErrorMessage(code){
				switch(code){
					case 10:
						alert("Appointment is duplicated. Please try again with different time.");
						break;
					case 13:	// server error
						alert("API Server error. Please try again later.");
						break;
					case 20: // phone number incorrect
					case 21: // name not defined
				}
			}

			/** proceed a request with request options */
			function __proceedRequest(requestOptions) {
				if (!requestOptions) return;

				// push a new xhr to _xhrs
				var nextId = _xhrs.length-1;
				_xhrs.push($.ajax({
						url    : api_url + requestOptions.URI,
						method : requestOptions.method,
						async  : requestOptions.async,
						data   : requestOptions.data,
						crossDomain: true,
						headers: {
							'Access-Token':api_token
						},
						success: function (data) {
							// since api.php return json in text content type, so jquery does not
							// translate it into json object. we will have to do it manually
							if (typeof(data)=='string') data = JSON.parse(data);
							if (data.error == true) {
								__showErrorMessage(data.code);
								return;
							};
							typeof(requestOptions.onSuccess) == 'function' ? requestOptions.onSuccess(data) : void(0);
						},
						error  : function (xhr, textStatus, errorThrown) {
							console.error(("Server Error<br/>" + xhr.status + "<br/>" + errorThrown));
							typeof(requestOptions.onError) == 'function' ? requestOptions.onError(xhr) : void(0);
						},
						complete: function(){
							// find the current xhr in _xhrs and delete it
							_xhrs.splice(nextId, 1);

							// now proceed item in queue
							var nextRequestOptions = _xhrQueue.shift();
							if (nextRequestOptions) __proceedRequest(nextRequestOptions);
						}
					}
				));
			}   // __proceedRequest
		} // _requestServerData
	};
})));