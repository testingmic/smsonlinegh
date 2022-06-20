# smsonlinegh

A PHP 7.4+ SMSOnline GH class is on the [implementation](https://github.com/ZenophTechnologies/php-sms-api-ghana) by [ZenophTechnologies](https://github.com/ZenophTechnologies), cleaned up and simplified.

[![PHP Version Support][php-badge]][php]
[![License][license-badge]][license]
[![Scrunitizer CI][scrutinizer-badge]][scrutinizer]
[![Packagist downloads][downloads-badge]][downloads]

### quickstart
Use this class to perform simple api requests to the smsnlinegh Platform

include 'SMSOnline.php';<br>
$SMSObj = new SMSOnline;

### delivery report
```
$data['request'] = 'delivery';<br>
$data['data'] = ['reference' => '3129c4072af554c2c1c0d858c59ccf49'];

$request = $SMSObj->push($data);<br>
echo json_encode($request);
```

### send personalized message
```
$data = [];
$data['request'] = 'send';<br>
$data['recipient'] = [<br>
    ['fullname' => 'Test User', 'contact' => '0123456789', 'message' => 'There will be service tomorrow evening.'],<br>
    ['fullname' => 'Another Person', 'contact' => '9876543210', 'message' => 'Kindly note that service has been postponed.']<br>
];<br>
$request = $SMSObj->push($data);<br>
echo json_encode($request);
```

### get sms balance
```
$data = [];<br>
$data['request'] = 'balance';<br>

$request = $SMSObj->push($data);<br>
echo json_encode($request);
```