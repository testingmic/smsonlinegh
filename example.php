<?php
include 'SMSOnline.php';
$SMSObj = new SMSOnline;
$SMSObj->sender_id = 'SENDER_ID';
$SMSObj->apikey = 'API_KEY';

// get the delivery status using the message reference id
$SMSObj->request = 'delivery';
$data['data'] = ['reference' => '3129c4072af554c2c1c0d858c59ccf49'];

// personalized messages
$data = [];
$SMSObj->request = 'send';
$data['recipient'] = [
    ['name' => 'Test User', 'contact' => '0123456789', 'message' => 'There will be service tomorrow evening.'],
    ['name' => 'Another Person', 'contact' => '9876543210', 'message' => 'Kindly note that service has been postponed.']
];

// get the account balance
$data = [];
$SMSObj->request = 'balance';

$request = $SMSObj->push($data);

echo json_encode($request);
?>