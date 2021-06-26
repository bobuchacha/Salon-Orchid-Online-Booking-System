<?php
/**
 * Created by PhpStorm.
 * User: bobuchacha
 * Date: 7/10/18
 * Time: 2:30 PM
 */

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

if (isset($_SERVER['HTTP_ORIGIN'])) {
	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

define("API_DEBUG", true);

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

	exit(0);
}


// include essential library
require_once('../../includes/class-data-encapsulator.php');
require_once('../../includes/class-restclient.php');
require_once('../../includes/class-simplerouter.php');





$Router = new SimpleRouter();
$API = new RestClient(['build_indexed_queries' => FALSE]);

$AccessTokenData = DataEncapsulation::decrypt($Router->get_header('Access-Token'));
$AccessTokenData = json_decode($AccessTokenData);


// now that I have all information that I need
$account_id = $AccessTokenData->{'account-id'};
$location_id = $AccessTokenData->{'location-id'};
$api_token = $AccessTokenData->{'api-token'};
$API_Headers = ['Access-Token'=>$api_token, 'Account-ID'=>$account_id, 'Location-ID'=>$location_id];
//$API_URL = 'http://localhost:8000';
$API_URL = 'http://api5.salonmanager.us';
//$API_URL = 'http://api.salonmanager.us/v4';

// check for authentication before processing anything else
//authenticate();

header("Author: Thang Cao");
header("API-Version: 1.1");

if (API_DEBUG) {
    header("Response-Server: $API_URL");
}

/**
 *  fetch services from main API server and return to wordpress
 */
$Router->get("/salon-metadata", function() use ($Router, $API){
	$data = (request_api_data('/salontime-salon-metadata', 'GET'));

	if ($data) {

		$Router->response($data);

	}
	else {

		$Router->response([
			"error" => true,
		    "message" => "API Server Error",
            "returned" => $data
		]);

	}

});

/**
 *  fetch services from main API server and return to wordpress
 */
$Router->get("/get-services", function() use ($Router, $API){
	$data = (request_api_data('/st-get-services', 'GET'));

	if ($data) {

		$Router->response($data);

	}
	else {

		$Router->response([
			"error" => true,
		    "message" => "API Server Error"
		]);

	}

});


/**
 *  fetch services from main API server and return to wordpress
 */
$Router->get("/get-technicians", function() use ($Router, $API){
	$data = (request_api_data('/st-get-technicians', 'GET', ['service-id'=>intval($Router->request_get('service-id'))]));

	if ($data) {

		$Router->response($data);

	}
	else {

		$Router->response([
			"error" => true,
			"message" => "API Server Error"
		]);

	}

});

/**
 *  fetch services from main API server and return to wordpress
 */
$Router->get("/get-times", function() use ($Router, $API){
	$data = (request_api_data('/st-get-available-times', 'GET', [
		'service-id'=>intval($Router->request_get('service-id')),
		'service-duration'=>intval($Router->request_get('service-duration')),
		'technician-id'=>intval($Router->request_get('technician-id')),
		'date'=>($Router->request_get('date')),
	]));

	if ($data) {

		$Router->response($data);

	}
	else {

		$Router->response([
			"error" => true,
			"message" => "API Server Error"
		]);

	}

});


/**
 * fetch customer's name using phone number and return name
 */
$Router->get("/get-customer-name", function() use ($Router, $API){
	$data = (request_api_data('/st-get-customer-name', 'GET', [
		'phone' => $Router->request_get("phone")
	]));

	if ($data) {

		$Router->response($data);

	}
	else {

		$Router->response([
			"error" => true,
			"message" => "API Server Error"
		]);

	}
});

$Router->get("/avatar", function() use ($Router, $API){
	global $API_URL, $API_Headers;
	$file = file_get_contents($API_URL . '/avatar?f=' .$Router->request_get("f"));
	echo $file;
	die();

});

/**
 * send Appointment create command to API server
 */
$Router->post("/submit-appointment", function() use ($Router, $API){
//	$service_duration = intval($Router->request_post('service-duration'));
//	$service_name = ($Router->request_post('service-name'));
//	$tech_id = ($Router->request_post('technician-id'));
//	$service_date = ($Router->request_post('service-date'));
//	$service_time = ($Router->request_post('service-time'));
//	$customer_phone = ($Router->request_post('customer-phone'));
//	$customer_name = ($Router->request_post('customer-name'));
//	$note = ($Router->request_post('note'));
//	$SMSReminder = ($Router->request_post('receive-sms-reminder'));
//	$SMSCommunication = ($Router->request_post('receive-sms-promotion'));

//	$data = request_api_data("/st-submit-appointment", 'POST', [
//		'service-duration' => $service_duration,
//		'service-name' => $service_name,
//		'technician-id' => $tech_id,
//		'service-date' => $service_date,
//		'service-time' => $service_time,
//		'customer-phone' => $customer_phone,
//		'customer_name' => $customer_name,
//		'note' => $note,
//		'receive-sms-reminder' => $SMSReminder,
//		'receive-sms-promotion' => $SMSCommunication
//	]);

	// By pass $_POST to API Server
	global $_POST;
	$data = request_api_data("/st-submit-appointment", 'POST', $_POST);
	if ($data) {

		$Router->response($data);

	}
	else {

		$Router->response([
			                  "error" => true,
			                  "message" => "API Server Error"
		                  ]);

	}

});

/**
 * get first avaiability
 */
$Router->get('/st-get-technician-first-available', function () use ($Router, $API){
    $data = (request_api_data('/st-get-technician-first-available', 'GET', [
        'technician-id' => $Router->request_get("technician-id"),
        'service-duration' => $Router->request_get("service-duration")
    ]));

    if ($data) {

        $Router->response($data);

    }
    else {

        $Router->response([
            "error" => true,
            "message" => "API Server Error"
        ]);

    }
});


/**
 * send Appointment create command to API server
 */
$Router->get("/test", function() use ($Router, $API){

	global $_GET;
	$data = request_api_data("/st-test", 'GET', $_GET);
	if ($data) {

		$Router->response($data);

	}
	else {

		$Router->response([
			"error" => true,
			"message" => "API Server Error"
		]);

	}

});


$Router->run();
//================================================== HELPERS ===========================================================
function request_api_data($uri, $method = 'GET', $queryString = []){
	global $API, $API_URL, $API_Headers;
	if ($method == 'GET') {
		$result = $API->get($API_URL . $uri, $queryString, $API_Headers);
	}elseif ($method == 'POST') {
		$result = $API->post($API_URL . $uri, $queryString, $API_Headers);
	}else {
		return false;
	}

	if ($result->error) {
	    global $Router;
	    $Router->response([
	        "error" => true,
            "message" => $result->error,
            "uri" => $result->url
        ]);
    }

	if (API_DEBUG) {
	    $query = json_encode($queryString);
	    header("Requested: {$API_URL}/{$uri}");
	    header("Requested-Method: {$method}");
	    header("Requested-Query: {$query}");
    }
	try {
		$r = json_decode($result->response);
	}catch(Exception $e){
	}
	return $r ? $r : $result->response;

}
function authenticate(){
	global $Router;
	$result = request_api_data('/st_authenticate');

	try {
		if ($result->success == true) {
			return true;
		}
		else {
			$Router->response([
				                  'error'   => true,
				                  'message' => 'Token not authorized'
			                  ]);

		}
	}catch(Exception $e){}
}
