<?php 
class SMSOnlineGH {

    private $per_sms = 150;
    public $message;
    public $request;

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

    public function send($data = null) {

        // set the request to balance
        $this->request = 'send';

        // return the response
        return $this->push($data);
    }

    public function status($reference_id = null) {

        // check if the reference id is empty
        if( empty($reference_id) ) {
            return [
                'code' => $this->responses('MV_ERR_TPL_REF_INVALID')['code'],
                'msg' => $this->responses('MV_ERR_TPL_REF_INVALID')['msg']
            ];
        }

        // set the request to balance
        $this->request = 'delivery';

        // return the response
        return $this->push(['reference' => $reference_id]);
    }

    public function balance() {

        // set the request to balance
        $this->request = 'balance';

        // return the response
        return $this->push();
    }

    private function push(array $params = []) {
        
        if( empty($this->request) ) {
            return 'Sorry! The request parameter is required.';
        }

        if( !isset($this->endpoint[$this->request]) ) {
            return 'Sorry! An invalid request endpoint was parsed.';
        }

        // set the endpoint
        $params['endpoint'] = $this->endpoint[$this->request];

        // if the request is send
        if( in_array($this->request, ['send']) ) {

            // set the send of the message
            $params['message']['messages'][0]['sender'] = $this->sender_id;

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
                if( !isset($person['contact']) || (isset($person['contact']) && !$this->isvalid_phone($person['contact']))) {
                    $bugs[] = "Sorry! An invalid contact number ({$person['contact']}) for user on row ".($key + 1)." was detected.";
                } else {

                    // set the fullname if empty
                    $fullname = $person['name'] ?? 'User';

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
            if(!empty($bugs)) {
                $error = "";
                foreach($bugs as $bug) {
                    $error .= $bug . "<br>";
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
        }

        // perform the request and return the result
        $request = $this->process($params);

        // only return success when all goes well
        if(isset($request['handshake']['label'])) {

            // get the response
            $response = $this->responses($request['handshake']['label'], $number ?? null);

            // return the result
            return [
                'status' => $response['code'],
                'message' => $response['msg'],
                'data' => $request['data']['messages'] ?? $request['data']
            ];

        }

        return [
            'status' => 'unexpected_error',
            'message' => 'Sorry! An unexpected error was encountered while processing the request.',
            'data' => $request
        ];

    }

    private function process($data) {

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
    
    private function isvalid_phone($number) {
        return (bool) preg_match("/^[+0-9]{8,15}+$/", $number);
    }

    private function responses($label, $number = 0){
        
        $response = [
            'HSHK_OK' => [
                'code' => 'success',
                'msg' => !empty($number) ? "The message was successfully sent to {$number} recipients." : "The request was successfully processed.",
            ],
            'HSHK_ERR_UA_AUTH' => [
                'code' => 'invalid_key',
                'msg' => 'Sorry! An invalid API Key was supplied'
            ],
            'HSHK_ERR_SM_DATETIME' => [
                'code' => 'invalid_date',
                'msg' => 'Sorry! An invalid datetime was supplied for the schedule parameter.'
            ],
            'MV_ERR_TPL_REF_INVALID' => [
                'code' => 'invalid_reference',
                'msg' => 'Sorry! An invalid message reference id was submitted.'
            ]
        ];

        return $response[$label] ?? ['code' => 'unknown', 'msg' => 'An unknown label returned.', 'label' => $label];

    }

}
?>