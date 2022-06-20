# smsonlinegh

A PHP 7.4+ SMSOnline GH class is on the [implementation](https://github.com/ZenophTechnologies/php-sms-api-ghana) by [ZenophTechnologies](https://github.com/ZenophTechnologies), cleaned up and simplified.

### Quickstart
Use this class to perform simple api requests to the smsnlinegh Platform

```php
include 'SMSOnline.php';
$SMSObj = new SMSOnline;
$SMSObj->sender = 'SENDER_ID';
$SMSObj->apikey = 'API_KEY';
```

### Delivery report
```php
$SMSObj->request = 'delivery';
$data['data'] = ['reference' => '3129c4072af554c2c1c0d858c59ccf49'];

$request = $SMSObj->push($data);
echo json_encode($request);
```

### Send Personalized Message
```php
$data = [];
$SMSObj->request = 'send';
$data['recipient'] = [
    ['name' => 'Test User', 'contact' => '0123456789', 'message' => 'There will be service tomorrow evening.'],
    ['name' => 'Another Person', 'contact' => '9876543210', 'message' => 'Kindly note that service has been postponed.']
];
$request = $SMSObj->push($data);
echo json_encode($request);
```

### Get SMS Balance
```php
$data = [];
$SMSObj->request = 'balance';

$request = $SMSObj->push($data);
echo json_encode($request);
```