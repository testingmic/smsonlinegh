# smsonlinegh

A PHP 7.4+ SMSOnlineGH class is on the [implementation](https://github.com/ZenophTechnologies/php-sms-api-ghana) by [ZenophTechnologies](https://github.com/ZenophTechnologies), cleaned up and simplified.

### Quickstart
Use this class to perform simple api requests to the smsonlinegh Platform<br><br>
Include the Class in the file to perform the request
```php
include 'SMSOnline.php';
```

Create a new object of the file and set the Sender ID and the Api Keys
```php
$SMSObj = new SMSOnlineGH();
$SMSObj->sender = 'SENDER_ID';
$SMSObj->apikey = 'API_KEY';
```

### Delivery report
Get the message delivery status by passing the message reference id to the method $SMSObj->status()
```php
$request = $SMSObj->status($reference_id);
echo json_encode($request);
```

Sample request response:
```json
{
    "status": "success",
    "message": "The request was successfully processed.",
    "data": [
        {
            "reference": "3129c4072af554c2c1c0d858c59ccf49",
            "delivery": true,
            "category": 1,
            "text": "Hello {$name}, {$message}",
            "type": 0,
            "sender": "EMMALEXTECH",
            "personalised": true,
            "destinations": [
                {
                    "messageId": "a9e9cdfe-1a2b-9075-f098-4f7af3867ded",
                    "validation": {
                        "id": 2401,
                        "label": "DRV_OK"
                    },
                    "to": "233550107770",
                    "country": "Ghana",
                    "message": "Hello Emmanuel Obeng, There will be service tomorrow evening.",
                    "messageCount": 1,
                    "status": {
                        "id": 2110,
                        "label": "DS_DELIVERED"
                    },
                    "submitDateTime": "2022-06-20 21:33:49",
                    "reportDateTime": "2022-06-20 21:33:50"
                }
            ],
            "destinationsCount": 1
        }
    ]
}
```

### Send Personalized Message
Use this method to send an sms to multiple contacts all at a go. Parse the Name, Contact and Message to send 
in an array and set it as the value before making the request.
```php
$data['recipient'] = [
    ['name' => 'Test User', 'contact' => '0123456789', 'message' => 'There will be service tomorrow evening.'],
    ['name' => 'Another Person', 'contact' => '9876543210', 'message' => 'Kindly note that service has been postponed.']
];

$request = $SMSObj->send($data);
echo json_encode($request);
```

### Schedule when to send the message
A message can be scheduled to be delivered at a specific date and time. This can be done by setting the date and time at which the message should be sent to the variable named schedule (YYYY-MM-DD HH:mm).
```php
$data['schedule'] = date('Y-m-d H:i');  // 2022-06-21 21:51
$data['recipient'] = [
    ['name' => 'Test User', 'contact' => '0123456789', 'message' => 'There will be service tomorrow evening.'],
    ['name' => 'Another Person', 'contact' => '9876543210', 'message' => 'Kindly note that service has been postponed.']
];

$request = $SMSObj->send($data);
echo json_encode($request);
```

### Get SMS Unit Balance
```php
$request = $SMSObj->balance();
echo json_encode($request);
```

Sample request response below:
```json
{
    "status": "success",
    "message": "The request was successfully processed.",
    "data": {
        "balance": {
            "amount": 4,
            "currencyName": "Ghana Cedi",
            "currencyCode": "GHS",
            "currencyPriced": false
        }
    }
}
```

### Sample error responses
Below are examples of error responses returned after an unsuccessful request<br>

#### Invalid api key or sender id
```json
{
    "status": "invalid_key",
    "message": "Sorry! An invalid API Key was supplied",
    "data": null
}
```

#### Invalid reference id
```json
{
    "status": "invalid_reference",
    "message": "Sorry! An invalid message reference id was submitted.",
    "data": null
}
```

#### Invalid schedule datetime
```json
{
    "status": "invalid_date",
    "message": "Sorry! An invalid datetime was supplied for the schedule parameter.",
    "data": null
}
```