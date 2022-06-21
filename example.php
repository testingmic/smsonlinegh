<?php
// require the class file
require_once 'SMSOnlineGH.php';

// create a new object
$SMSObj = new SMSOnlineGH();

// set the various values
$SMSObj->sender_id = 'SENDER_ID';
$SMSObj->apikey = 'API_KEY';

// get the delivery status using the message reference id
$reference_id = 'MESSAGE_REFERENCE_ID';
$request = $SMSObj->status($reference_id);

print "<pre>"; print_r($request); print "</pre>";

// get the account balance
$request = $SMSObj->balance();

print "<pre>"; print_r($request); print "</pre>";

// // send personalized messages
$data['schedule'] = date('Y-m-d H:i');
$data['recipient'] = [
    ['name' => 'Test User', 'contact' => '0123456789', 'message' => 'There will be service tomorrow evening.'],
    ['name' => 'Another Person', 'contact' => '9876543210', 'message' => 'Kindly note that service has been postponed.']
];
// $request = $SMSObj->send($data);

print "<pre>"; print_r($request); print "</pre>";
?>