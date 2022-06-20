<?php
include 'SMSOnline.php';
$SMSObj = new SMSOnline;

// get the delivery status using the message reference id
$data['request'] = 'delivery';
$data['data'] = ['reference' => '3129c4072af554c2c1c0d858c59ccf49'];

// personalized messages
$data = [];
$data['request'] = 'send';
$data['recipient'] = [
    ['fullname' => 'Emmanuel Obeng', 'contact' => '0550107770', 'message' => 'There will be service tomorrow evening.'],
    ['fullname' => 'Fred Asamoah', 'contact' => '0571408340', 'message' => 'Kindly note that service has been postponed.']
];

// get the account balance
$data = [];
$data['request'] = 'balance';

$request = $SMSObj->push($data);

echo json_encode($request);
?>