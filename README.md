# smsonlinegh

Use this class to perform simple api requests to the smsnlinegh Platform

include 'SMSOnline.php';
$SMSObj = new SMSOnline;

// get the delivery status using the message reference id
$data['request'] = 'delivery';
$data['data'] = ['reference' => '3129c4072af554c2c1c0d858c59ccf49'];

$request = $SMSObj->push($data);
echo json_encode($request);

// send personalized messages
$data = [];
$data['request'] = 'send';
$data['recipient'] = [
    ['fullname' => 'Test User', 'contact' => '0123456789', 'message' => 'There will be service tomorrow evening.'],
    ['fullname' => 'Another Person', 'contact' => '9876543210', 'message' => 'Kindly note that service has been postponed.']
];
$request = $SMSObj->push($data);
echo json_encode($request);

// get the account balance
$data = [];
$data['request'] = 'balance';

$request = $SMSObj->push($data);
echo json_encode($request);