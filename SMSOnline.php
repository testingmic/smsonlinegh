<?php 
class SMSOnline {

    private $per_sms = 150;
    private $sender = 'SENDER_ID';
    private $apikey = 'API_KEY';

    private $endpoint = [
        'delivery' => [
            'method' => 'POST',
            'url' => 'https://api.smsonlinegh.com/v4/report/message/delivery'
        ],
        'send' => [
            'method' => 'POST',
            'url' => 'https://api.smsonlinegh.com/v4/message/sms/send'
        ],
        'balance' => [
            'method' => 'POST',
            'url' => 'https://api.smsonlinegh.com/v4/report/balance'
        ],
    ];

    public function push(array $params = []) {
        
        if( !isset($params['request']) ) {
            return 'Sorry! The request parameter is required.';
        }

        if( !isset($this->endpoint[$params['request']]) ) {
            return 'Sorry! An invalid request endpoint was parsed.';
        }

        // set the endpoint
        $params['endpoint'] = $this->endpoint[$params['request']];

        // if the request is send
        if( in_array($params['request'], ['send']) ) {

            // set the send of the message
            $params['message']['messages'][0]['sender'] = $this->sender;

            // set the message to send
            $params['message']['messages'][0]['type'] = 0;
            $params['message']['messages'][0]['text'] = 'Hello {$name},'."\n".'{$message}';

            // ensure the recipient list parameter is not empty
            if(empty($params['recipient']) || (isset($params['recipient']) && !is_array($params['recipient']))) {
                return 'Sorry! The recipient list cannot be empty';
            }

            // init value for bug
            $bugs = [];

            // loop through the recipient list
            foreach($params['recipient'] as $key => $person) {

                // confirm that the contact number is set 
                if( !isset($person['contact']) ) {
                    $bugs[] = "Sorry! The contact number on Row {$key} is empty";
                } else {
                    // set the fullname if empty
                    $fullname = $person['fullname'] ?? 'User';

                    // set the destination
                    $params['message']['messages'][0]['destinations'][] = [
                        "to" => $person['contact'],
                        "values" => [$fullname, $person['message'] ?? null]
                    ];
                }
            }

            // unset the recipient id
            unset($params['recipient']);

            // end query if the bug is not empty
            if(!empty($bug)) {
                $error = "";
                foreach($bugs as $bug) {
                    $error .= $bug . "\n";
                }
                return $error;
            }

            // get the text
            preg_match_all("~\{\s*(.*?)\s*\}~", $params['message']['messages'][0]['text'], $replacement);

            // message
            $count = 0;
            $number = 0;

            // get the sms information
            foreach($params['message']['messages'][0]['destinations'] as $each) {
                // get the message
                $message = str_replace($replacement[1], $each['values'], $params['message']['messages'][0]['text']);
                $characters = trim(preg_replace('/[^A-Za-z0-9,.\-]/', ' ', $message));

                // count the number of string
                $number++;
                $count += ceil(strlen($characters) / $this->per_sms);
            }

            // request for sms balance
            $bal_param['endpoint'] = $this->endpoint['balance'];

            // get the balance
            $balance = $this->process($bal_param);

            // get the balance difference
            if(isset($balance['data']['balance'])) {
                $balance = $balance['data']['balance']['amount'];
                if($balance < $count) {
                    return "Sorry! Your outstanding balance is {$balance} which is ".($balance - $count)." units less than the current message to be sent.";
                }
            }

            // perform the request and return the result
            $push = $this->process($params);

            // only return success when all goes well
            if(isset($push['handshake']['label'])) {
                if($push['handshake']['label'] == "HSHK_OK") {
                    return [
                        'status' => "success",
                        'message' => "The message was successfully sent to {$number} recipients.",
                        'request' => $push
                    ];
                }
            }

            return $push;

        }

        return $this->process($params);

    }

    public function process($data) {

        // initialize the curl request
        $curl = curl_init($data['endpoint']['url']);

        // set the data
        $form = isset($data['data']) ? $data['data'] : ($data['message'] ?? []);

        // set the curl array data
        curl_setopt_array($curl, [
			CURLOPT_URL => $data['endpoint']['url'],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_CONNECTTIMEOUT => 10,
		    CURLOPT_TIMEOUT => 30,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $data['endpoint']['method'],
			CURLOPT_POSTFIELDS => json_encode($form),
			CURLOPT_HTTPHEADER => [
                "Accept: application/json",
				"Content-Type: application/json",
                "Host: api.smsonlinegh.com",
				"Authorization: key {$this->apikey}"
            ],
		]);
		
        // execute the request
		$response = curl_exec($curl);
        $result = json_decode($response, true);

		return $result;

    }

}
?>