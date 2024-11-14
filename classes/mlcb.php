<?php

function checkMLCB($data, $report_type = 'BM', $mlcb_count = 0) {

    $mc_settings = mc_settings('singpass_company_code');
    $mc_mlcb_uid = get_option('mc_mlcb_uid');
    $mc_mlcb_uid = (!$mc_mlcb_uid) ? 'L120TEST01' : $mc_mlcb_uid;
    $body = [
        'annual_income' => $data->annual_income,
        'block' =>  $data->block,
        'building' =>  $data->building,
        'street' =>  $data->street,
        'unit' =>  $data->unit,
        'postal_code' =>  $data->postal_code,
        'company_name' =>  $data->company_name,
        'mlcb_client_id' => $mc_settings['mlcb_client_id'],
        'mlcb_user_id' => $mc_mlcb_uid, //'L120TAN014',
        'gender' =>  $data->gender,
        'employment_status' =>  $data->employment_status,
        'application_no' => time(),
        'country_id' =>  $data->country_id,        
        'loan_amount_requested' => floatval($data->loan_amount_requested),
        'monthly_income' =>  floatval($data->monthly_income),
        'monthly_income_1' =>  floatval($data->monthly_income_1),
        'monthly_income_2' =>  floatval($data->monthly_income_2),
        'monthly_income_3' => floatval($data->monthly_income_3),
        'six_months_income' =>  floatval($data->six_months_income),
        'mobilephone_1' =>  $data->mobilephone_1,
        'identification_no' =>  $data->identification_no,
        'identification_type' =>  $data->identification_type,
        'fullname' =>  $data->fullname,
        'date_of_birth' => $data->date_of_birth, //y-m-d
        'residential_type' => null,
        'mlcb_count' => $mlcb_count,
        'report_type' =>  $report_type
    ];
    //return json_encode($body); 
    $signatureAuth = createSignature();
    $body = array_merge($body, $signatureAuth);

    $results = [
        'status' => 'fail',
        'code' => 36501, //01: The loan amount is too large. 02: The loan conditions are not met.
        'message' => [],
        'data' => []
    ];

    $headers = [];
    $create_url = get_option('mc_application_mlcb');  
    $create_url = ( !$create_url) ? get_application_api() : trim($create_url);
    $create_url .= 'mlcb/wp-report'; 
    $response = wp_remote_post($create_url, [
        'body' => $body,
        'timeout' => 60,
        'header' => $headers
    ]);
    
    if(is_wp_error($response1)) {
        $messages = $response1->get_error_message();
    } else {
        $temp = json_decode($response['body']);
        $messages = $temp->errors_message;
    }
    $default = ['nric_no' => '', 'date' => '', 'id_type' => '', 'obligation' => '', 'fullname' => '', 'balance' => 0];
    $_SESSION['mlcbCheckedOk'] = false;
    if( is_array($messages) && count($messages) > 0 ) {
        $results['message'] = implode(("<br />"), $messages);
        $results['code'] = 36502;
        $results['status'] = 'fail';
        $results['data'] = $default;
        $results['balance_format'] = formatMoney(0);
        $results['continue'] = 0;
        //$results['test'] = $response;
        //$results['body'] = $body;
        //$results['url'] = $create_url;
        $_SESSION['mlcbCheckedOk'] = false;
    } else {        
        $balance = (isset($temp->success)) ? floatval($temp->success->balance) : 0;
        $results['message'] = [];
        $results['code'] = 36500; // Success
        $results['status'] = 'success';
        $results['data'] = (isset($temp->success)) ? $temp->success : $default;
        $results['balance_format'] = formatMoney($balance);
        $results['continue'] = 0;
        //$results['test'] = $response;
        //$results['body'] = $body;
        //$results['url'] = $create_url;

        $amount = floatval($data->loan_amount_requested);
        if($balance > 0) {
            $_SESSION['mlcbCheckedOk'] = ($balance >= $amount);           
            $results['continue'] = ($balance >= $amount) ? 1 : 0;
        } else {
            $_SESSION['mlcbCheckedOk'] = false;
            $results['continue'] = 0;
        }
    }

    //$results['body'] = $body;
    return json_encode($results); //$response['body']
}

 
function formatMoney($amount, $symbol = '$') {
  $formatted_amount = number_format($amount, 2, '.', ',');
  return  $symbol.$formatted_amount;
}