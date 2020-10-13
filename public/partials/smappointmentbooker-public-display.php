<?php
global $post;;

//// This varible can be overridden with a PHPUnit XML configuration file.
//if(!isset($TEST_SERVER_URL))
//	$TEST_SERVER_URL = "http://api.salonmanager.us";
//
//
//$api = new RestClient(['build_indexed_queries' => FALSE]);
//$result = $api->get($TEST_SERVER_URL . '/authenticate', [
//	'foo' => ' bar', 'baz' => 1, 'bat' => ['foo', 'bar', 'baz[12]']
//],
//	['Access-Token'=>'b1217d8fa0769e3e2fdd1d974281a5d8']);
//print "<pre>";
//var_dump($result);
//print "</pre>";

?>

<div class="tabs-animated-wrap appointment-form" id="sm-appointment-container">
	<div class="tabs">
		<div class="tab" id="sm-tab-service">

		</div>
		<div class="tab" id="sm-tab-addon-service">

		</div>
		<div class="tab" id="sm-tab-technician">

		</div>

		<div class="tab" id="sm-tab-calendar">

		</div>

		<div class="tab" id="sm-tab-customer">

		</div>

		<div class="tab" id="sm-tab-confirmation">

		</div>

	</div>

</div>

<script type="text/javascript">
	window.SMAPIServer = {
		"url": "<?php echo $API_url; ?>",
		"auth-token": "<?php echo $API_token; ?>"
	};
</script>

<script type="text/template" id="template-select-service">
	<div class="content-block-inner">

		<%if (!isAddon){%>
		<h2>Select Service</h2>
		<p>Let's get started by selecting a service for your next visit.</p>
		<%}else{%>
		<h2>Pick an Add-on Service</h2>
		<p>For better scheduling, please select an addon service if you need any. Additional service may require additional
		appointment.</p>
		<%}%>
		<ul style="margin-left:2em; line-height:1.8em">
			<%
			_.each(Services, function(s){
				if ((!isAddon && s.service_is_addon=='1') || (isAddon && s.service_is_addon=='0')) return;
				%>
				<li><a href="#" data-name="<%=s.service_name%>" data-internal-service-name="<%=s.internal_service_name%>" data-duration="<%=s.service_duration%>" data-id="<%=s.id%>" class="service-item"><%=(isAddon ? '+ ' : '') + s.service_name%> (<%=s.service_duration%> min)</a></li>
				<%
			});
			%>
		</ul>
		<hr>
		<%if (isAddon){%>
				<button data-name="" data-duration="0" data-id="0" class="service-item go-back ">Go back</button>
				<button data-name="" data-duration="0" data-id="0" class="service-item go-next ">No add-on needed</button>

		<%}else{%>
			<button onclick="javascript:window.location='<?php echo get_permalink( $post->post_parent ); ?>'">Cancel & Go back</button>
		<%}%>
	</div>
</script>
<script type="text/template" id="template-technician-list">
	<div class="content-block-inner">
		<h2>Select a Technician</h2>
		<p>Please select a Technician who will perform your service.</p>
		<ul style="margin-left:2em; line-height:1.8em;">
			<%
			if (Technicians.length==0) print("No Technician available for this service");
			_.each(Technicians, function(t){
				%>
				<li data-id="<%=t.id%>" class="technician-item"><img src="http://api.salonmanager.us/avatar?f=<%=t.profile_picture%>" /> <span><%=t.nickname%></span></li>
				<%
			});
			%>
		</ul>
		<hr>
		<button data-id="0" class="technician-item go-back">Go back</button>
	</div>
</script>
<script type="text/template" id="template-calendar">
	<div class="content-block-inner">
		<h2>Select Date & Time</h2>
		<p>Select a date, then pick your arrival time.</p>
		<div class="row">
			<div class="col-md-auto">
				<label>Select Date:</label>
				<input data-toggle="datepicker" id="txtDate" type="text" style="display:none" />
				<div class="calendar-container" style=""></div>
			</div>
		</div>
		<hr>

        <div class="row">
			<div class="col-md-auto">
                <p>Earliest Availability: <b><a href="#" class="first-availability"></a></b></p>
                <button class="select-first-availability">Book me this time</button> or
                <p>&nbsp;</p>
                <p><b>Select a time bellow:</b></p>
				<div class="time-container"></div>
			</div>
		</div>
		<hr>
		<button class="go-back time-item">Go back</button>
	</div>
</script>

<script type="text/template" id="template-confirmation">
	<div class="content-block-inner">
		<h2>Thank you!</h2>
		<p>Your appointment has been scheduled at <b><%=AppointmentInfo['service-time']%></b> on <b><%=AppointmentInfo['service-date']%></b>.
			Your confirmation number is <b><%=code%></b></p>
		<p>If you need to cancel or reschedule your appointment, please kindly give us a call.</p>
		<p>We appreciate your business and hopr you have a good day.</p>

		<hr>
		<button onclick="javascript:window.location='<?php echo get_permalink( $post->post_parent ); ?>'">Finish</button>
	</div>
</script>
<script type="text/template" id="template-booking-policy-popup">
	<div class="popup popup-booking-policy">
		<div class="content-block">

			<h2>Online Booking Policy</h2>

			<p>By using this online booking service, you must read, understand, and agree with the following terms &amp; conditions:</p>

			<div  class="booking-policy">
				<%=content%>
			</div>
			<p>&nbsp;</p>
			<p align="center"><button class="close-popup" data-popup=".popup-booking-policy" onclick="javascript:closeModal('.popup-booking-policy')">Close</button></p>
		</div>

	</div>
</script>

<script type="text/template" id="template-customer">
	<div class="content-block-inner">
		<h2>Almost Done!</h2>
		<p>Please enter your phone number beginning with area code and your name to continue.</p>
		<ul style="padding-left:2em">
			<li>Appointment Date: <b><%=AppointmentInfo['service-date']%></b></li>
			<li>Appointment Time: <b><%=AppointmentInfo['service-time']%></b></li>
			<li>Service: <b><%=AppointmentInfo['service-name-display']%></b></li>
			<li>Expected Duration: <b><%=(parseInt(AppointmentInfo['service-duration']) + parseInt(AppointmentInfo['service-addon-duration']))%> minutes</b></li>
			<li>Requested Technician: <b><%=AppointmentInfo['technician-name'] ? AppointmentInfo['technician-name'] : 'Anyone'%></b></li>
		</ul>
		<hr>
		<div class="row" style="margin-top:1em">
			<div class="col-xl-6">
				<label>Phone Number:</label>
				<input id="txtPhone" type="number" />
			</div>
		</div>
		<div class="row" style="margin-top:1em">
			<div class="col-xl-6">
				<label>Name:</label>
				<input id="txtName" type="text" />
			</div>
		</div>
		<div class="row" style="margin-top:1em">
			<div class="col-xl-6">
				<label>Special Request:</label>
				<textarea id="txtNote"></textarea>
			</div>
		</div>
		<div class="row"  style="margin-top:1em">
			<div class="col-md-auto">
				By booking with us, you must read and agree with our <a href="#" class="show-booking-policy">Online Booking Terms &amp; Conditions</a>.<br><br>
				<label style="font-weight:normal"><input type="checkbox" id="chkReceiveSMSNotification" checked="checked"/> Please text me SMS Reminder & Notification</label>
				<label style="font-weight:normal"><input type="checkbox" id="chkReceiveSMSPromotion" checked="checked"/> Please text me any future promotional programs</label>
			</div>
		</div>
		<hr>
		<button class="go-back">Go back</button>
		<button class="go-finish">Finish</button>
	</div>
</script>

<script type="text/javascript">
	(function( $ ) {


		$(function(){
			(new AppointmentBookForm("#sm-appointment-container")).render();
		});
	})( jQuery );

</script>