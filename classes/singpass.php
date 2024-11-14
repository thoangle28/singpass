<?php

function singpass_login_url()
{
  $singpassInfo = [
    'cookie' => '',
    'singpass_url' => '#'
  ];

  $mc_singpass = get_option('mc_singpass');
 
  if (!$mc_singpass) {
    return $singpassInfo;
  }

  try {
    $environment = $mc_singpass['singpass_environment'];
    $auth_url = $mc_singpass['singpass_athorize_url'];
    if( ($environment != 'prod.')) $auth_url = str_replace('{urlEnvironmentPrefix}', $environment, $auth_url);
    $signatureAuth = createSignature();
    $body =[
      'redirect_uri' => $mc_singpass['singpass_callback_url'], 
      'company_id' => $signatureAuth['company_id'],
      'signatureAuth' => $signatureAuth['signatureAuth']
    ];  
    $response = wp_remote_post($mc_singpass['singpass_codechallenge_url'], [
      'timeout' => 30, //30s
      'body' => $body
    ]);

    if ( ! is_wp_error( $response ) ) {
      $data = (is_array($response) && isset($response['body']) && $response['body'])
        ? json_decode($response['body']) : [];
      
      if (isset($data->error) && ($data->error == '1' || $data->error == 1)) return '#';
      
      $code_challenge = $data->data->pkceCodePair->codeChallenge; 
      $singpassConfirm = [
        'sessionId' => $data->data->sessionId,
        'codeVerifier' => $data->data->pkceCodePair->codeVerifier
      ];

      $_SESSION['singPassCode'] = $singpassConfirm;

      $cookieSingpass = base64_encode(serialize($singpassConfirm));
      $singpassInfo['cookie'] = $cookieSingpass;

      $query = http_build_query([
        'client_id' => $mc_singpass['singpass_client_id'],
        'redirect_uri' => $mc_singpass['singpass_callback_url'],
        'scope' => $mc_singpass['singpass_scope'],
        'purpose_id' => $mc_singpass['singpass_purpose_id'],
        'code_challenge' => $code_challenge,
        'code_challenge_method' => 'S256'
      ]);

      $query = urldecode($query);
      $auth_url = $auth_url . '?' . $query;

      $singpassInfo['singpass_url'] = $auth_url;
    } else {
      $singpassInfo['error'] = $response->get_error_message();
    }
  } catch (Exception $e) {
    $singpassInfo['error'] = $e->getMessage();
  }

  return $singpassInfo;
}

/**
 * Get personal info from singPass
 */
function mc_get_personal()
{
  $authCode = '';
  if (isset($_GET['code']) && $_GET['code']) {
    $authCode = $_GET['code'];
  } else {
    $authCode =  isset($_COOKIE['mc_singpass_code']) ? $_COOKIE['mc_singpass_code'] : $authCode;
  }

  if (!$authCode) return [];

  $mc_singpass = get_option('mc_singpass');

  if (!$mc_singpass) {
    return [];
  }

  //read cookie
  $verfiyCode = [];
  $verfiyCode = isset($_COOKIE["mc_singpass_verify"]) ? $_COOKIE["mc_singpass_verify"] : '';
  $verfiyCode = $verfiyCode ? unserialize(base64_decode($verfiyCode)) : '';

  //get autho code from cookie
  if (!$verfiyCode) return [];

  $signatureAuth = createSignature();
  $body = [
    'authCode' => $authCode,
    'codeVerifier' => $verfiyCode['codeVerifier'],
    'redirect_uri' => $mc_singpass['singpass_callback_url'],    
    'company_id' => $signatureAuth['company_id'],
    'signatureAuth' => $signatureAuth['signatureAuth']
  ];

  $headers = ['sid' => $verfiyCode['sessionId']];

  $options = array(
    'timeout' => 30, //30s
    'body'    => $body,
    'headers' => $headers
  );

  $response = ["error" => true, 'message' => 'Please login to get info from singPass.'];
  try {
    $response = wp_remote_post($mc_singpass['singpass_personal_url'], $options);
    if (is_array($response) && !is_wp_error($response)) {
      return json_decode($response['body']);
    } else {
      return json_encode(["error" => true, 'message' => $response->get_error_message()]);
    }
  } catch (Exception $e) {
    return json_encode(["error" => true, 'message' => $e->getMessage()]);
  }

  return $response && $response['body'] ? json_decode($response['body']) : $response;
}

/**
 * Company
 */
function mc_company($code = '')
{
  //config/company/listing
  $company = get_application_api();
  $company .= 'customer-portal/mc-company-listing';

  $options = array(
    'body'    => ["status" => true, "orderBy" => "asc"]
  );
  $signatureAuth = createSignature($code);
  $response = wp_remote_get($company, $signatureAuth);

  $results = [];
  if (is_array($response) && !is_wp_error($response)) {
    $results = $response['body']; // use the content
    $results = json_decode($results, false);
    return $results;
  } else {
    $results = json_decode(json_encode($response));
    return $results;
  }
}

/**
 * Country
 */
function mc_countries()
{
  //config/company/listing
  $country = get_application_api();
  $country .= 'customer-portal/mc-country-listing';
  $signatureAuth = createSignature();
  $options = array(
    'timeout' => 30, //30s
    'body'    => [
      "status" => true, 
      'company_id' => $signatureAuth['company_id'],
      'signatureAuth' => $signatureAuth['signatureAuth']
    ]
  );
 
  $response = wp_remote_post($country, $options);
 
  $results = [];
  if (is_array($response) && !is_wp_error($response)) {
    $results = $response['body']; // use the content
    $results = json_decode($results, false);
    return $results;
  } else {
    $results = json_decode(json_encode($response));
    return $results;
  }
}
/**
 * Customer details
 */
function mc_customer_details($nric = '')
{
  //borrower/mc-details
  if (!$nric) {
    $singPassChecked = $_SESSION['singPassChecked'];
    $nric = $singPassChecked['NRIC_No_FIN'];
  }

  $mc_settings = mc_settings('singpass_company_code');

  $customer = get_application_api();
  $customer .= 'borrower/mc-details/' .  $nric; //:nric
  //GET
  $signatureAuth = createSignature();
  $body = ['company_id' => $signatureAuth['company_id'], 'signatureAuth' => $signatureAuth['signatureAuth'] ];
  $options = array(
    'timeout' => 30, //30s
    'body'    => $body
  );

  $response = wp_remote_post(
    $customer,
    $options
  );

  $results = [];
  if (is_array($response) && !is_wp_error($response)) {
    $results = $response['body']; // use the content
    $results = json_decode($results, false);
    return $results;
  } else {
    $results = json_decode(json_encode($response));
    return $results;
  }
}


/**
 * 
 */
function get_application_api()
{
  $application_endpoint = get_option('mc_application_endpoint');
  if (!$application_endpoint) {
    return false;
  }

  return $application_endpoint;
}

function create_signature($time, $com_code = '')
{
  $company = mc_settings('singpass_company_code');
  $client = mc_settings('singpass_client_id');
  $purpose = mc_settings('singpass_purpose_id');
  $company_code = $company['company_code'];
  $company_code = ($company_code) ? $company_code : $com_code;

  $temp = [$client, $purpose, $company_code, $time];
 
  $signature = implode('|', $temp);
  $secret =  $client;

  return hash_hmac('sha256', $signature, $secret);
}

function createSignature( $com_code = '') {
  $createAppTime = time();
  $signature = create_signature($createAppTime, $com_code);
  $company = mc_settings('singpass_company_code');
  $result = [
    'company_id' => 0,
    'signatureAuth' => [
        'time' => $createAppTime,
        'private_key' => ''
      ]
    ];

  if($company) {
      $result = [
      'company_id' => (int)$company['company_id'],
      'signatureAuth' => [
        'time' => $createAppTime,
        'private_key' => $signature
      ]
    ];
  }
  
  return $result;
}

/**
 * Create application
 */
function mc_create_application($data, $verify = [])
{

  /* if(!isset($_SESSION['mlcbCheckedOk']) || !$_SESSION['mlcbCheckedOk']) {
    $resutls = [
      'error' => true,
      'message' => "You do not meet the eligibility criteria for the loan agreement. Please contact the lending institution directly.",
      'application_no' => ''
    ];
    return json_encode($resutls);
  } */

  $create_url = get_application_api();
  $create_url .= 'application/wp-create';
  //return json_encode($data['home_address']);
 
  $monthly_income = $data['employment']['monthly_income'];
  $installment = $data['loan']['installment'];
  $percent_allow = ($monthly_income > 0 ) ? ($monthly_income*60)/100 : 0;
  //if installment over 60%
  if( floatval($installment) >= floatval($percent_allow)) {
    $resutls = [
      'error' => true,
      'message' => 'error',
      'message_error' => 'Your installment amount exceeds 60% of your monthly income. 
                          Please adjust loan amount or terms to qualify.
                          <br />Monthly payment amount: <b>' . format_money($installment) . '</b><br />Maximum loan amount: <b>' . format_money($percent_allow).'</b>',
      'application_no' => ''
    ];

    return json_encode($resutls);
  }

  $headers = [];
  $createAppTime = time();
  $signature__ = create_signature($createAppTime);

  $mc_settings = mc_settings('singpass_company_code');
  $body = [
    'customer' => customerInfo($data['personal']),
    'borrower' => borrowerInfo($data, $verify),
    'bank_account' => bankAccountInfo($data['bank']),
    'employment'  => employmentInfo($data['employment']),
    'address' => addressInfo($data),
    'file_documents' => fileDocuments($data),
    'avatar' => fileAvatar($data),
    'application' => applicationInfo($data),
    'company_id' => (int)$mc_settings['company_id'],
    'cpf' => cpfInfo($data),
    'tools' => toolsInfo(),
    'signatureAuth' => [
      'time' => $createAppTime,
      'private_key' => $signature__
    ]
  ];
  //return json_encode($body);
  $timeout = 50;
  if (! ini_get('safe_mode')) {
    set_time_limit($timeout + 10);
  }

  $response = wp_remote_post($create_url, [
    'timeout' => $timeout,
    'body' => $body,
    'header' => $headers
  ]);

  if (is_wp_error($response)) {
    $resutls = [
      'error' => false,
      'message' => 'success',
      'message_error' => $response->get_error_message(),
      'application_no' => ''
    ];
    return json_encode($resutls);
  } else
    return $response['body']; //json_encode($body);
}

function customerInfo($data)
{
  $mc_settings = mc_settings('singpass_company_code');
  $args = [
    "company_id" => intval($mc_settings['company_id']),
    "country_id" => (int)$data['nationality'],
    "identification_expiry" => $data['identification_expiry'] ? $data['identification_expiry'] . 'T00:00:00.000Z' : null,
    "identification_type" => $data['identification_type'],
    "identification_no" => $data['identification_no'],
    "date_of_birth" => $data['date_of_birth'] . 'T00:00:00.000Z', //"2000-07-25T00:00:00.000Z",
    "firstname" => $data['firstname'],
    "lastname" => $data['lastname'],
    "gender" => $data['gender'],
    "marital_status" => $data['marital_status'],
    "legal_actions_against" => (int)$data['legal_actions_against'],
    "customer_remark" => json_encode([]),
  ];

  return $args;
}

function borrowerInfo($data, $verify = [])
{

  $brrower = [
    "email_1" => $data['contact']['email_1'],
    "email_2" => $data['contact']['email_2'],
    "employment_status" => $data['employment']['employment_status'],
    "mobilephone_1" => $data['contact']['mobilephone_1'],
    "mobilephone_2" => $data['contact']['mobilephone_2'],
    "mobilephone_3" => null,
    "homephone" => $data['contact']['homephone'],
    "office_telephone" => $data['contact']['officephone'],
    "monthly_income" => floatval($data['employment']['monthly_income']), //number
    "job_type_id" => (int)$data['employment']['job_type_id'],
    "spoken_language" => $data['personal']['spoken_language'],
    "marketing_type_id" => (int)$data['personal']['marketing_type_id'],
    "residential_type" => 0,
    "phone_verified" => $verify['phone'],
    "email_verified" => $verify['email'],
    "guarantor_info" => json_encode($data['surety']),
    "next_of_kin_name" => $data['contact']['next_of_kin_name'],
    "next_of_kin_type" => $data['contact']['next_of_kin_type'],
    "next_of_kin_phone" => $data['contact']['next_of_kin_phone']
    
  ];

  return $brrower;
}

function bankAccountInfo($data)
{
  $bank = [
    "is_giro" => 0,
    "account_number_1" => $data['account_number_1'] ? $data['account_number_1'] : null,
    "bank_code_1" => $data['bank_code_1'] ? $data['bank_code_1'] : null,
    "date_of_salary" => $data['date_of_salary'] ? (int)$data['date_of_salary'] : null,
    "bank_name_1" => $data['bank_name_1'] ? $data['bank_name_1'] : null,
  ];

  return $bank;
}

function employmentInfo($data)
{
  $employment = [
    "portal_code" => $data['portal_code'] ? (int)$data['portal_code'] : null,
    "annual_income" => floatval($data['annual_income']),
    "address" => $data['address'] ? $data['address'] : null,
    "company_telephone" => $data['company_telephone'] ? $data['company_telephone'] : null,
    "company_name" => $data['company_name'] ? $data['company_name'] : null,
    "monthly_income_1" => floatval($data['monthly_income_1']),
    "monthly_income_2" => floatval($data['monthly_income_2']),
    "monthly_income_3" => floatval($data['monthly_income_3']),
    "occupation" => $data['occupation'] ? $data['occupation'] : null,
    "position" => $data['position'] ? $data['position'] : null,
    "six_months_income" => floatval($data['six_months_income']),
    "bankrupt_plan" => (int)$data['bankrupt_plan'],
    "bankrupted" => (int)$data['bankrupted'],
    "yrs_of_employment_period" => intval($data['yrs_of_employment_period']) ? intval($data['yrs_of_employment_period']) : null,
  ];

  return $employment;
}

function addressInfo($data)
{
  $address = [
    "address_type_id" => 1, // 1: home
    "city" => null,
    "state" => null,
    "country" => null,
    "address_label" => null,
    "block" => null,
    "building" => null,
    "existing_staying" => 0,
    "home_ownership" => ' ',
    "housing_type" => ' ',
    "is_default" => null,
    "postal_code" => null,
    "property_type" => ' ',
    "staying_condition" => ' ',
    "street" => null,
    "unit" => null,
  ];

  $contact = $data['contact'];

  $addresses = [];
  foreach ($data['home_address'] as $item) {
    $temp = (array)$item;
    $temp['address_type_id'] = (int)$data['contact']['home_address_type_id'];
    $temp['postal_code'] = $temp['postal'];
    $temp['building'] = $temp['building'] ? $temp['building'] : null;
    $temp['unit'] = $temp['unit'] ? $temp['unit'] : null;
    $temp['address_label'] = $temp['address_label'] ? $temp['address_label'] : null;
    $temp['country'] = (int)$temp['country'];
    $temp['housing_type'] = (int)$temp['housing_type'];
    unset($temp['postal']);
    $addresses[] = array_merge($address, $temp);    
  }

  foreach ($data['work_address'] as $item) {    
    $temp = (array)$item;
    $temp['address_type_id'] = (int)$data['contact']['work_address_type_id'];
    $temp['postal_code'] = $temp['postal'];
    $temp['building'] = $temp['building'] ? $temp['building'] : null;
    $temp['unit'] = $temp['unit'] ? $temp['unit'] : null;
    $temp['address_label'] = $temp['address_label'] ? $temp['address_label'] : null;
    $temp['country'] = (int)$temp['country'];
    $temp['housing_type'] = ' ';
    $temp['home_ownership'] = ' ';
    $temp['is_default'] = ((int)$temp['is_default'] > 0) ? 1 : null;
    unset($temp['postal']);
    $addresses[] = array_merge($address, $temp);
    
  }

  return $addresses;
}

function applicationInfo($data)
{
  $income_document = '[]';
  if (isset($data['employment']['income_document']) && is_array($data['employment']['income_document'])) {
    $temp = [];
    foreach ($data['employment']['income_document'] as $id => $val) {
      $temp[] = (int)$val;
    }
    if ($temp) $income_document = '["' . implode('","', $temp) . '"]';
  }

  $loan_fees = $data['loan']['loan_interest'];
  $interest_late_fee = [
    "interest" => floatval($loan_fees['interest']),
    "late_fee" => floatval($loan_fees['late_fee']),
    "late_interest" => floatval($loan_fees['late_interest']),
  ];
  //re-calculator fee & late fee, interset
  $loan_type_id = isset($data['loan']['loan_type_id']) ? $data['loan']['loan_type_id'] : 0;
  $term_unit = isset($data['loan']['term_unit']) ? $data['loan']['term_unit'] : 0;
  $loan_term = isset($data['loan']['loan_terms']) ? $data['loan']['loan_terms'] : 0;

  $fees = interestLatefee($loan_type_id, $term_unit, $loan_term);
  if ($fees) $interest_late_fee = $fees;
  $mc_settings = mc_settings('singpass_company_code');
  $application = [
    "loan_terms" => (int)$data['loan']['loan_terms'],
    "term_unit" => (int)$data['loan']['term_unit'],
    "office_id" => null,
    "loan_amount_requested" => (int)$data['loan']['loan_amount_requested'],
    "loan_type_id" => (int)$data['loan']['loan_type_id'],
    "status" => 1, //'AWAITING_APPROVAL' = 1,
    "application_date" => null,
    "amount_of_acceptance" => ($data['loan']['loan_amount_requested'] * 10) / 100, //loan_amount_requested*10%
    "first_repayment_date" => null,
    "late_interest_per_month_percent" => $interest_late_fee['late_interest'],
    "monthly_late_fee" => $interest_late_fee['late_fee'],
    "monthly_due_date" => 0,
    "application_notes" => json_encode([]),
    "is_existing" => $data['personal']['is_existing'],
    "company_id" => (int)$mc_settings['company_id'],
    "loan_reason" => $data['loan']['loan_reason'],
    "crosscheck_count" => 0,
    "mlcb_count" => 0,
    "interest" =>  $interest_late_fee['interest'],
    "income_document" => $income_document,
    "description" => $data['loan']['description'],
    "no_of_active_credit_loan" => $data['loan']['no_of_active_credit_loan'] ? $data['loan']['no_of_active_credit_loan'] : null,
    "has_beneficial_owner" => $data['loan']['has_beneficial_owner'],
    "beneficial_owner_explanation" => $data['loan']['benefit_explain'],
    "is_politically_exposed" => $data['loan']['is_politically_exposed'],
    "politically_exposed_explanation" => $data['loan']['politically_explain'],
    "income_source" => $data['employment']['source_income']    
  ];

  return  $application;
}

function fileAvatar($data)
{
  return $data['personal']['filePhotoValue'];
}

function fileDocuments($data)
{
  $files = [];

  //avarta
  if( $data['personal']['filePhotoValue']) $files[] = $data['personal']['filePhotoValue'];
  //signature
  //$files[] = $data['completion']['signature'];

  //contact
  //employment
  if ($data['employment']['income_document_files']) {
    foreach ($data['employment']['income_document_files'] as $file) {
      $files[] = $file;
    }
  }
  //loan details
  if ($data['loan']['document_files']) {
    foreach ($data['loan']['document_files'] as $file) {
      $files[] = $file;
    }
  }

  //guarantor details
  if ($data['surety']['income_document_files']) {
    foreach ($data['surety']['income_document_files'] as $file) {
      $files[] = $file;
    }
  }

  //more files
  if ($data['more_files']['files']) {
    foreach ($data['more_files']['files'] as $file) {
      $files[] = $file;
    }
  }

  //signauture
  return $files;
}

function cpfInfo($data = [])
{
  $cpf = [
    "amount" => '[]',
    "date" => '[]',
    "employer" => '[]',
    "month" => '[]',
  ];

  if (isset($data['personal']['cpf']) && $data['personal']['cpf']) {
    $amount = (isset($data['personal']['cpf']->amount) && is_array($data['personal']['cpf']->amount)) ? implode(",", $data['personal']['cpf']->amount) : '';
    $cpf["amount"] = ($amount) ? '[' . $amount . ']' : "";

    $date = (isset($data['personal']['cpf']->date) && is_array($data['personal']['cpf']->date)) ? implode('","', $data['personal']['cpf']->date) : '';
    $cpf["date"]   = ($date) ? '["' . $date . '"]' : "";

    $employer = (isset($data['personal']['cpf']->employer) && is_array($data['personal']['cpf']->employer)) ? implode('","', $data['personal']['cpf']->employer) : '';
    $cpf["employer"] = ($employer) ? '["' . $employer . '"]' : "";

    $month = (isset($data['personal']['cpf']->month) && is_array($data['personal']['cpf']->month)) ? implode('","', $data['personal']['cpf']->month) : '';
    $cpf["month"] = ($month) ? '["' . $month . '"]' : "";
  }

  return $cpf;
}

function toolsInfo($data = [])
{

  $tools = [
    "cas_first_last_name" => '',
    "cas_last_first_name" => '',
    "google_first_last_name" => '',
    "google_last_first_name" => '',
    "un_last_name" => '',
    "un_first_name" => ''
  ];

  return $tools;
}
/**
 * 
 */
function mc_application_approval()
{
  ///application/mc-approval
  $approve_loan = get_application_api();
  $approve_loan .= 'application/mc-approval';
}

/**
 * 	max 2 loan active
 */
function mc_application_check_loan($nric = '')
{
  if (!$nric) {
    $singPassChecked = $_SESSION['singPassChecked'];
    $nric = $singPassChecked['NRIC_No_FIN'];
  }

  $approve_loan = get_application_api();
  $approve_loan .= 'application/mc-nric/' . $nric; //:nric
  //GET
  $signatureAuth = createSignature();
  $response = wp_remote_post($approve_loan, [
    'body' => $signatureAuth,
    'timeout' => 45,
  ]);

  $results = [];
  if (is_array($response) && !is_wp_error($response)) {
    $results = $response['body']; // use the content
    $results = json_decode($results, false);
    return $results;
  } else {
    $results = json_decode(json_encode($response));
    return $results;
  }
}

/**
 * 	get application with pending status 
 */

function mc_application_pending($nric = '')
{
  if (!$nric) {
    $singPassChecked = $_SESSION['singPassChecked'];
    $nric = $singPassChecked['NRIC_No_FIN'];
  }

  $approve_loan = get_application_api();
  $approve_loan .= 'application/mc-application-pending'; //:nric
  //GET
  $signatureAuth = createSignature();
  $signatureAuth['nric_no'] = $nric;
  $response = wp_remote_post($approve_loan, [
    'body' => $signatureAuth,
    'timeout' => 45,
  ]);
  //print_r($response['body']);
  $results = [];
  if (is_array($response) && !is_wp_error($response)) {
    $results = $response['body']; // use the content
    $results = json_decode($results, false);
    return $results;
  } else {
    $results = json_decode(json_encode($response));
    return $results;
  }
}

/**
 * 	max 2 loan active
 */
function mc_application_loan_details($loan_id = 0)
{
  if ($loan_id <= 0 && isset($_GET['id']) && $_GET['id'] > 0) {
    $loan_id = $_GET['id'];
  }

  $approve_loan = get_application_api();
  $approve_loan .= 'loan/mc-details/' . $loan_id; //:nric
  $signatureAuth = createSignature();
  $response = wp_remote_post($approve_loan, 
      [
      'body' => $signatureAuth,
      'timeout' => 30
      ]
    );

  $results = [];
  if (is_array($response) && !is_wp_error($response)) {
    $results = $response['body']; // use the content
    $results = json_decode($results, false);
    return $results;
  } else {
    $results = json_decode(json_encode($response));
    return $results;
  }
}
/**
 * Return loan listing
*/
function loanListing($company_id = 2)
{
  $loanType = mc_get_options('config/loan_type/listing', []);

  $default = [1 => 'Personal Loan (PL)', 2 => 'Business Loan (BL)'];
  $options = [];
  if (isset($loanType->data) && $loanType->data) {
    foreach ($loanType->data as $type) {
      $options[$type->id] = $type->type_name;
    }
  }

  return $options ? $options : $default;
}

function calculatorInterest()
{
  $loanType = mc_get_options('config/loan_type/listing', []);
  $loanRate = [];
  if (isset($loanType->data) && $loanType->data) {
    foreach ($loanType->data as $type) {    
      //$daily_late_interest = doubleval($type->daily_late_interest);
      //$daily = $late_interest / 31;
      $rate_interest = [
        'daily' => doubleval($type->daily_late_interest),
        'weekly' => doubleval($type->weekly_late_interest),
        'biweekly' =>doubleval($type->biweekly_late_interest),
        'monthly' => doubleval($type->monthly_late_interest)
      ];

      $late_fee = doubleval($type->late_fee);
      $daily = $late_fee / 31;
      $rate_fee = [
        'daily' => round($daily, 2),
        'weekly' => round($daily * 7, 2),
        'biweekly' => round($daily * 14, 2),
        'monthly' => $late_fee
      ];

      $more_month = isset($type->interest_more_month) ? $type->interest_more_month : 3.97;
      $loanRate[] = [
        'type_id' => $type->id,
        'daily' => $type->daily_interest_rate,
        'weekly' => $type->weekly_interest_rate,
        'biweekly' => $type->biweekly_interest_rate,
        'monthly' => $type->monthly_interest_rate,        
        'more_month' => $more_month,
        'annual' => $type->annual_interest_rate,
        'late_interest' => $rate_interest,
        'late_fee' => $rate_fee,
      ];
    }
  }

  return $loanRate;
}

function document_type()
{
  $type = mc_get_options('config/document_type/listing', []);
  return $type;
}
function documents()
{
  $documents = document_type();
  $documents_type = (isset($documents->data)) ? $documents->data : [];

  $options = [];
  foreach ($documents_type as $type) {
    $options[$type->id] = $type->type_name;
  }

  return $options;
}

function address_type()
{
  $type = mc_get_options('config/address_type/listing', []);
  return $type;
}
function job_type()
{
  $type = mc_get_options('config/job_type/listing', []);
  return $type;
}
function job_listing()
{
  $job_type = job_type();
  $job_type = (isset($job_type->data)) ? $job_type->data : [];

  $options = [];
  foreach ($job_type as $type) {
    $options[$type->id] = $type->job_type_name;
  }

  return $options;
}

function marketing_type()
{
  $type = mc_get_options('customer-portal/mc-source-listing', []);
  return $type;
}
function source()
{
  $source = marketing_type();
  $marketing_type = (isset($source->data)) ? $source->data : [];

  $options = [];
  foreach ($marketing_type as $type) {
    $options[$type->id] = $type->marketing_type_name;
  }

  return $options;
}


function mc_get_options($url, $params)
{
  $end_point = get_application_api();
  $end_point .= $url;

  $mc_settings = mc_settings('singpass_company_code');
  $signatureAuth = createSignature();
  $default = [
    'status' => true,
    'pageSize' => 99999,
    'currentPage' => 1,
    'company_id' => $signatureAuth['company_id'],
    'signatureAuth' => $signatureAuth['signatureAuth']
  ];

  $body = array_merge($default, $params);
  $options = [
    'timeout' => 30, //30s
    'body' => $body
  ];

  $response = wp_remote_post($end_point, $options);

  $results = [];
  if (is_array($response) && !is_wp_error($response)) {
    $results = $response['body']; // use the content
    $results = json_decode($results, false);
    return $results;
  } else {
    $results = json_decode(json_encode($response));
    return $results;
  }
}
/**
 * Return Term Unit
 */
function termUnit($i = 0)
{
  $termUnit = ['Daily', 'Weekly', 'Bi-weekly', 'Monthly'];

  return $termUnit[$i];
}

function LoanTermUnit($i = 0)
{
  $termUnit = ['Day(s)', 'Week(s)', 'Bi-week(s)', 'Month(s)'];

  return $termUnit[$i];
}
/**
 * Return loan status with html
 */
function loanStatus($s)
{
  $status = [
    ['Pending', 'btn-warning'],
    ['Active', 'btn-success'],
    ['Full Settle', 'btn-primary'],
    ['Cancelled', 'btn-secondary'],
    ['Unrecoverable', 'btn-info'],
    ['Bad-Debt', 'btn-danger'],    
    ['Closed', 'btn-dark'],
    ['New', 'btn-info'],
  ];

  $temp = (isset($status[$s])) ? $status[$s] : [];
  if ($temp)
    return '<span class="btn-status btn ' . $temp[1] . 
            ' lh-sm py-1 px-3 d-flex justify-content-center text-white"><small>' . $temp[0] .'</small></span>';
  else
    return '';
}

/**
 * Format Money
 */
function format_money($amount, $symbol = '$')
{
  $formatted_amount = number_format($amount, 2, '.', ',');

  return  $symbol . $formatted_amount;
}

/**
 * Format date
 */
function dateFormat($str, $fm = 'd M, Y')
{
  $date = strtotime($str);
  return date($fm, $date);
}

/**
 * Due date
 */
function monthy_due_date($date)
{
  if ($date == 1) return $date . 'st';
  if ($date == 2) return $date . 'nd';
  if ($date == 3) return $date . 'rd';

  return $date . 'th';
}

/**
 * Find late fee
 */
function lateFee($array_late_fee = [], $date = '')
{
  if (!$array_late_fee) return 0;
  $new_date = date('Y-m-d', strtotime($date) + 86400); //24*60*60

  foreach ($array_late_fee as $item) {
    if ($item->date == $new_date) return $item->late_fee;
  }
}

/** 
 * Get MC settings
 */
function mc_settings($key = '')
{
  $defaults = [
    'singpass_company_code' => '',
    'singpass_client_id' => '',
    'singpass_purpose_id' => '',
    'singpass_scope' => '',
    'singpass_serect_id' => '',
    'singpass_codechallenge_url' => '',
    'singpass_personal_url' => '',
    'singpass_callback_url' => '',
    'singpass_athorize_url' => '',
    'singpass_environment' => ''
  ];
  $mc_singpass = get_option('mc_singpass');
  if ($mc_singpass) {
    $tem = unserialize($mc_singpass['singpass_company_code']);
    $mc_singpass['singpass_company_code'] =  $tem;
  }

  $mc_singpass = array_merge($defaults, $mc_singpass);
  if ($key) return $mc_singpass[$key];
  else
    return $mc_singpass;
}

/**
 * Obfuscated Email: j*****0@example.com
 */
function obfuscateEmailAddress($email)
{
  if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) return $email;
  // Split the email into username and domain parts
  list($username, $domain) = explode('@', $email);

  // Obfuscate the username part, keeping the first & last letter and replacing the rest with asterisks
  $usernameObfuscated = substr($username, 0, 1) . str_repeat('*', strlen($username) - 2) . substr($username, strlen($username) - 1, strlen($username));

  // Combine the obfuscated username with the domain
  $obfuscatedEmail = $usernameObfuscated . '@' . $domain;

  return $obfuscatedEmail;
}
/**
 * obfuscated Phone: 98******89
 */
function obfuscatePhone($phone)
{
  // Obfuscate the phone number with format: dd*****dd
  if (!$phone) return '';
  $obfuscatedPhone = substr($phone, 0, 2) . str_repeat('*', strlen($phone) - 2) . substr($phone, strlen($phone) - 2, strlen($phone));
  return $obfuscatedPhone;
}

function interestLatefee($loan_type_id = 0, $term_unit = 3, $loan_terms = 0)
{

  $loandInterest = calculatorInterest();
  
  $term_keys = ['daily', 'weekly', 'biweekly', 'monthly', 'annual'];
  $term_time = $term_keys[$term_unit];
  $late_fee = [];
  if (is_array($loandInterest) && $loandInterest) {
    foreach ($loandInterest as $fee) {
      if ($loan_type_id == $fee['type_id']) {    
        $interest = $fee[$term_time];
        if($term_time == 'monthly' && $loan_terms > 1) $interest = $fee['more_month'];
        $late_fee = [
          "interest" => $interest,
          "late_fee" => $fee['late_fee'][$term_time],
          "late_interest" => $fee['late_interest'][$term_time],
        ];
      }
    }
  }
  return $late_fee;
}


/**
 * 	Return application detail
 */
function mc_application_details($pid = 0)
{
  $app_id = 0;
  if ($loan_id <= 0 && isset($_GET['pid']) && $_GET['pid'] > 0) {
    $app_id = $_GET['pid'];
  }

  $app_draft = get_application_api();
  $app_draft .= 'application/mc-detail-app/' . $app_id;
  $signatureAuth = createSignature();
  $response = wp_remote_post($app_draft, 
      [
      'body' => $signatureAuth,
      'timeout' => 30
      ]
    );
  
  $results = [];
  if (is_array($response) && !is_wp_error($response)) {
    $results = $response['body']; // use the content
    $results = json_decode($results, false);
    if(!$results->error) {
      $data = [];
      $data['customer'] = $results->data->customer;
      $data['application'] = $results->data->application;
      $data['borrower'] = $results->data->borrower;
      $data['address'] = $results->data->address;
      $data['bank_account'] = $results->data->bank_account;
      $data['employment'] = $results->data->employment;
      $data['loan_details'] = $results->data->loan->loan_details;
      $data['file_documents'] = $results->data->file_documents;
      //print '<pre>'.print_r($results->data, 1).'</pre>';
      return $data;
    }

    return $results;
  } else {
    $results = json_decode(json_encode($response));
    return $results;
  }
}


/**
 * Customer Check Info
 */
function mc_customer_check_info($nric = '', $phone = '') {

  $customer = get_application_api();
  $customer .= 'customer-portal/check-info';

  $body = ['nric_no' => $nric, 'phone_number' => $phone ];
  $options = array(
    'timeout' => 30, //30s
    'body'    => $body
  );

  $response = wp_remote_post(
    $customer,
    $options
  );

  $results = [];
  if (is_array($response) && !is_wp_error($response)) {
    $results = $response['body']; // use the content
    $results = json_decode($results, false);
    return $results;
  } else {
    $results = json_decode(json_encode($response));
    return $results;
  }
}