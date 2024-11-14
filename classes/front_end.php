<?php
add_action('wp_ajax_next_step', 'mc_application_form_next_step');
add_action('wp_ajax_nopriv_next_step', 'mc_application_form_next_step');

function mc_application_form_next_step() {
    $steps = $_SESSION['steps'];
  
    $step = $_POST['step'];
    $steps[$step]['status'] = $_POST['status'];
    $steps[$step]['data'] = $_POST['data'];

    $next_step = $_POST['next_step'];
    $steps[$next_step]['status'] = 'pending';

    $_SESSION['steps'] = $steps;

    $step = ($step == 'guarantor') ? 'Surety' : $step;
    $step = ($step == 'loan') ? 'Loan Details' : $step;

    $next_step = ($next_step == 'guarantor') ? 'Surety' : $next_step;
    $next_step = ($next_step == 'loan') ? 'Loan Details' : $next_step;

    $result = [ 'code' => 201, 'message' => 'You have completed filling in the data for step "' . ucwords($step) . '". Please proceed to the next step "' . ucwords($next_step) . '".'];
    //check verify
    $otp_sms_verifytation = get_option('mc_otp_sms_verification');  
    $reuired_otp_sms = ($otp_sms_verifytation === true || $otp_sms_verifytation == 'true');
    if($step == 'contact' && $reuired_otp_sms) {
        $contact = $_POST['data'];
        //$email = (isset($_SESSION['emailVerification'])) ? $_SESSION['emailVerification'] : [];
        //$emailChecked = (isset($email['email']) && $email['email'] == $contact['email'] && $email['status'] == 'confirmed');
        $phone = (isset($_SESSION['phoneVerification'])) ? $_SESSION['phoneVerification'] : [];        
        $phoneChecked = (isset($phone['phone']) && $phone['phone'] == '+65'.$contact['phone'] && $phone['status'] == 'confirmed');
        //$emailChecked && 
        if($phoneChecked) {
            $result = [ 'message' => '', 'code' => 201];
        } else {
            //$text = 'your phone and email';
            //$text = (!$emailChecked && $phoneChecked) ? 'your email' : $text;
            //$text = ($emailChecked && !$phoneChecked) ? 'your phone' : $text;
            $text = 'your phone';
            $result = [ 'message' => 'Please click on the airplane icon(s) to verify ' . $text . ' before proceeding to the next step.', 'code' => 401];
        }
    }
    
    print json_encode($result);

    die();
}

add_action('wp_ajax_home_address', 'mc_application_form_home_address');
add_action('wp_ajax_nopriv_home_address', 'mc_application_form_home_address');

function mc_application_form_home_address() {
    global $type_id;

    $id = $_POST['id'];
    $sectionId =  $_POST['type'];
    $type_id =  $_POST['type_id'];
    $hdbtype = $_POST['hdbtype'];
    $property = $_POST['property'];

    require_once(WP_PLUGIN_DIR.'/mc-application-form/template/menu_tools.php');

    if( $sectionId == '#mc-accordion')
        require_once('home_address.php');   
    else
        require_once('work_address.php');  

    die();
}

add_action('wp_ajax_application', 'mc_application_submitted_form');
add_action('wp_ajax_nopriv_application', 'mc_application_submitted_form');

function mc_application_submitted_form() {
    require_once(WP_PLUGIN_DIR.'/mc-application-form/template/menu_tools.php');
    require_once(WP_PLUGIN_DIR.'/mc-application-form/classes/singpass.php');
    //get comany
    $mc_settings = mc_settings('singpass_company_code');
    
    $formData = [];
    $formData = $_POST['dataForm'];
   
    $verfiy = checkPhoneEmail();
    $result = mc_create_application($formData, $verfiy);
   
    //send mail
    $checkSendMail = json_decode($result);
    if(isset($checkSendMail->error) && !$checkSendMail->error) {
        $loan = loanListing();
        $data = [
            'firstname' => $formData['personal']['firstname'],
            'lastname' => $formData['personal']['lastname'],
            'application_no' => $checkSendMail->id->application_no,
            'email' => $formData['contact']['email_1'],
            'loan_type' => $loan[$formData['loan']['loan_type_id']],
        ];
       
        //admin
        sendNotificationEmail($data, 0);
        //customer
        sendNotificationEmail($data, 1);
        //print '--->'.print_r($data, true).'<-----';
        //die();
    }

    print $result;
    die();
}

add_filter('wordfence_is_api_request', 'mc_bypass_wordfence_for_base64', 10, 1);
function mc_bypass_wordfence_for_base64($is_api_request) {
    if (isset($_POST['action']) && $_POST['action'] === 'application') {
        return true; // Treat this as an API request, bypassing Wordfence checks
    }
    return $is_api_request;
}


/**
 * Send an email to the manager when a new loan request is submitted.
*/
function sendNotificationEmail($data = [], $cusomter = 0) {

    $email_settings = get_option('mc_email_manager'); 
    $to = $email_settings['email_receive'];
    $email_subject = $email_settings['email_subject'];
    $customer_name = $data['lastname'] . ' ' . $data['firstname'];
    if($cusomter) {
        $email_settings = get_option('mc_email_customer'); 
        $to = $data['email'];
        $email_subject = $email_settings['email_subject1'];    
    }

    $email_template = email_template();
    $email_subject = str_replace('{application_no}', $data['application_no'], $email_subject);
    $email_template = str_replace('{content}', $email_settings['email_content'], $email_template);
    $email_template = str_replace('{application_no}', $data['application_no'], $email_template);
    $email_template = str_replace('{customer_name}', strtoupper($customer_name), $email_template);
    $email_template = str_replace('{loan_type}', $data['loan_type'], $email_template);
    
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $sent = wp_mail($to, $email_subject, $email_template, $headers);

    return $sent;
}

/**
 * Check phone & email
*/
function checkPhoneEmail() {
    $phone_verified = '[]';
    $email_verified = '[]';
    $email = @$_SESSION['emailVerification'];
    if( isset($email) && $email['status'] == 'confirmed') {
      $email_verified = '["'.$email['email'].'"]';
    }
    $phone = @$_SESSION['phoneVerification'];
    if( isset($phone) && $phone['status'] == 'confirmed') {
      $phone_verified = '['.$phone['phone'].']';
    }

    return ['email' => $email_verified, 'phone' => $phone_verified];
}

/**
 * Send OTP to email
*/
add_action('wp_ajax_email_verification', 'mc_email_verification');
add_action('wp_ajax_nopriv_email_verification', 'mc_email_verification');
function mc_email_verification() {
    
    require_once(WP_PLUGIN_DIR.'/mc-application-form/classes/singpass.php');

    $verify_url = get_application_api();
    $verify_url .= 'site/wp-send-otp'; 
    
    $email = $_POST['emailtocheck'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    $_SESSION['emailVerification'] = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$email) {
        print json_encode([ 'code' => 400, "message" => "Invalid email address.", 'data' => [ 'show_popup' => 1 ] ]);
    } else {       
        $signatureAuth = createSignature(); 
        $params = ["email" => $email, 'firstname' => $firstname, 'lastname' => $lastname];
        $params = array_merge($params, $signatureAuth);        
        $options = [
            'body' => $params,
            'timeout' => 30,
            'headers' => []
        ];

        $response = wp_remote_post($verify_url,  $options);
        if ( ! is_wp_error( $response ) ) {
            if(isset($response['body'])) {
                $results = json_decode($response['body']);
            
                if( !$results->error || $results->error == 'false') {
                    $_SESSION['emailVerification'] = [
                        'email' => $email,
                        'otp' => $results->otp,
                        'status' => 'notVerify' //confirmed
                    ];
    
                    print json_encode([ 'code' => 200, "message" => "Check email", 'data' => ['show_popup' => 1] ]);
                    die();
                }
            }
            print json_encode([ 'code' => 200, "message" => "Your email is incorrect or is a fake email. Please check and try again.",
                                'data' => ['show_popup' => 0] ]);
        } else {
            print json_encode([ 'code' => 401, "message" => $response->get_error_message(), 'data' => ['show_popup' => 0] ]);
        }
    }

    die();
}

/**
 * Email Verification via OTP
*/

add_action('wp_ajax_verify_otp', 'mc_verify_otp');
add_action('wp_ajax_nopriv_verify_otp', 'mc_verify_otp');
function mc_verify_otp() {
    
    require_once(WP_PLUGIN_DIR.'/mc-application-form/classes/singpass.php');

    $verify_url = get_application_api();
    $verify_url .= 'site/wp-verify-otp'; 
    
    $verfiy_info = $_POST['verfiy_info'];

    if (!$verfiy_info) {
        print json_encode([ 'code' => 400, "message" => "The authentication request information is invalid.", 'data' => [ 'confirmed' => 0 ] ]);
    } else {
        //$verfiy_info = json_decode($verfiy_info);
        $signatureAuth = createSignature();
        $params = [
            "otp_info" => $_SESSION['emailVerification']['otp'], 
            'otp_verify' => $verfiy_info['otp_verify'], 
            'application_id' => $verfiy_info['application_id']
        ];
        $params = array_merge($params, $signatureAuth);
        $options = [
            'body' => $params,
            'timeout' => 30,
            'headers' => []
        ];
   
        $response = wp_remote_post($verify_url,  $options);    
        if(isset($response['body'])) {
            $results = json_decode($response['body']);
            if( $results->error != 1 && $results->error != '1') {
                $_SESSION['emailVerification']['status'] = 'confirmed';
                print json_encode([ 'code' => 200, "message" => "Confirmed", 'data' => ['confirmed' => 1] ]);
                die();
            }
        }

        print json_encode([ 'code' => 200, "message" => "The OTP code is incorrect. Please check and re-enter.",
                            'data' => ['confirmed' => 0] ]);
    }

    die();
}

/**
 * Phone Verification via OTP
*/

add_action('wp_ajax_phone_verification', 'mc_phone_verification');
add_action('wp_ajax_nopriv_phone_verification', 'mc_phone_verification');
function mc_phone_verification() {
    
    require_once(WP_PLUGIN_DIR.'/mc-application-form/classes/singpass.php');

    $verify_url = get_application_api();
    $verify_url .= 'site/wp-send-otp'; 
    
    $phone = $_POST['phonetocheck'];
    
    $_SESSION['phoneVerification'] = [];

    if (!$phone) {
        print json_encode([ 'code' => 400, "message" => "Please enter your phone number.", 'data' => [ 'show_popup' => 1 ] ]);
    } else {    
        $signatureAuth = createSignature();
        $params = [ "phone_number" => $phone, 'phone_code' => '+65' ];    
        $params = array_merge($params, $signatureAuth);
        $options = [
            'body' => $params,
            'timeout' => 30,
            'headers' => []
        ];

        $response = wp_remote_post($verify_url,  $options);
        if(isset($response['body'])) {
            $results = json_decode($response['body']);
         
            if( !$results->error || $results->error == 'false') {
                $_SESSION['phoneVerification'] = [
                    'phone' => '+65'.$phone,
                    'otp' => $results->otp,
                    'status' => 'notVerify' //confirmed
                ];               
               
                $message =  "";
                if(!isset($results->otp)) {
                    $message = $results->message;
                    print json_encode([ 'code' => 401, "message" => 'The phone number does not exist or is temporarily inactive.', 'data' => ['show_popup' => 0] ]);
                } else {
                    print json_encode([ 'code' => 200, "message" => $message, 'data' => ['show_popup' => 1] ]);
                }
                die();
            }
        }
        print json_encode([ 'code' => 200, "message" => "Your phone number is not valid or out of service. Please check and try again.",
                            'data' => ['show_popup' => 0] ]);
    }

    die();
}

/**
 * Email Verification via OTP
*/

add_action('wp_ajax_verify_otp_phone', 'mc_verify_otp_phone');
add_action('wp_ajax_nopriv_verify_otp_phone', 'mc_verify_otp_phone');
function mc_verify_otp_phone() {
    
    require_once(WP_PLUGIN_DIR.'/mc-application-form/classes/singpass.php');

    $verify_url = get_application_api();
    $verify_url .= 'site/wp-verify-otp'; 
    
    $verfiy_info = $_POST['verfiy_info'];

    if (!$verfiy_info) {
        print json_encode([ 'code' => 400, "message" => "The authentication request information is invalid.", 'data' => [ 'confirmed' => 0 ] ]);
    } else {
        //$verfiy_info = json_decode($verfiy_info);
        $signatureAuth = createSignature();
        $params = [
            "otp_info" => $_SESSION['phoneVerification']['otp'], 
            'otp_verify' => $verfiy_info['otp_verify'], 
            'application_id' => $verfiy_info['application_id']
        ];
        $params = array_merge($params, $signatureAuth);
        $options = [
            'body' => $params,
            'timeout' => 30,
            'headers' => []
        ];
   
        $response = wp_remote_post($verify_url,  $options);    
        if(isset($response['body'])) {
            $results = json_decode($response['body']);
            if( $results->error != 1 && $results->error != '1') {
                $_SESSION['phoneVerification']['status'] = 'confirmed';
                print json_encode([ 'code' => 200, "message" => "Confirmed", 'data' => ['confirmed' => 1] ]);
                die();
            }
        }

        print json_encode([ 'code' => 200, "message" => "The OTP code is incorrect. Please check and re-enter.",
                            'data' => ['confirmed' => 0] ]);
    }

    die();
}

/**
 * Check the user's eligibility for a loan using national data.
*/

add_action('wp_ajax_check_mlcb', 'mc_application_form_check_mlcb');
add_action('wp_ajax_nopriv_check_mlcb', 'mc_application_form_check_mlcb');

function mc_application_form_check_mlcb(){
    require_once(WP_PLUGIN_DIR.'/mc-application-form/classes/mlcb.php');
    $data = [];    
    //$data =  json_decode(base64_decode($_POST['dataForm']));
    $data =  json_decode(json_encode($_POST['dataForm']));
    $response = checkMLCB($data, 'ME', 0);
    print_r($response);
    die();
}

/**
 * Generate a PDF file and allow for a preview.
*/

add_action('wp_ajax_receipt_downdoad_pdf', 'mc_receipt_downdoad_pdf');
add_action('wp_ajax_nopriv_receipt_downdoad_pdf', 'mc_receipt_downdoad_pdf');
function mc_receipt_downdoad_pdf() {
    $receipt_id = $_POST['receipt_id'];
    $receipt_no = $_POST['receipt_no'];

    if( !$receipt_id || !$receipt_no) return;

    $createAppTime = time();
    $signature__ = create_signature($createAppTime);

    $mc_settings = mc_settings('singpass_company_code');
    $options = [
        'body' => [
            'company_id' => (Int)$mc_settings['company_id'],
            'signatureAuth' => [
                'time' => $createAppTime,
                'private_key' => $signature__
            ],
            'receipt_no' => $receipt_no
        ]
    ];

    $url_pdf = get_application_api();
    $url_pdf .= 'pdf/wp-get-receipt/'.$receipt_id;
    $response = wp_remote_post($url_pdf, $options);

    if ( is_array( $response ) && ! is_wp_error( $response ) ) {
        print $response['body'];
    } else {
        print json_encode(['error' => true, 'message' => $response->get_error_message()]);
    }

    die();
}


add_action('wp_ajax_background_check', 'mc_background_check');
add_action('wp_ajax_nopriv_background_check', 'mc_background_check');
function mc_background_check() {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    if( !$firstname || !$lastname) return;

    $options = [
        'firstname' => $firstname,
        'lastname' => $lastname,
    ];

    $url_pdf = get_application_api();
    $url_pdf .= 'tool/mc-all-check';
    $response = wp_remote_get($url_pdf, $options);

    if ( is_array( $response ) && ! is_wp_error( $response ) ) {
        print $response['body'];
    } else {
        print json_encode(['error' => true, 'message' => $response->get_error_message()]);
    }
    die();
}


function email_template()
{
    return '<table style="max-width: 767px; width: 100%; margin: 0 auto;">
       
        <tbody>
            <tr>
                <td>
                    {content}
                </td>
            </tr>
        </tbody>
    </table>';
}


add_action('wp_ajax_calc_repayment', 'mc_calc_repayment');
add_action('wp_ajax_nopriv_calc_repayment', 'mc_calc_repayment');
function mc_calc_repayment() {    

    $resutls = mc_calc_repayment_raw(
                    (float)$_POST['loan_amount'], 
                    (int)$_POST['loan_term'], 
                    (int)$_POST['term_unit'], 
                    (int)$_POST['loan_type_id']
                );
    print $resutls;
    die();
}


function mc_calc_repayment_raw($loan_amount, $loan_terms, $term_unit, $loan_type_id, $ajax = 1) {    
    $fees = interestLatefee($loan_type_id, $term_unit, $loan_terms);

    $params = [
        "first_repayment_date" => date('Y-m-d'),
        "interest_percent" => (float)$fees['interest'],
        "loan_amount" => (float)$loan_amount,
        "monthly_due_date" => 17,
        "term_unit" => (int)$term_unit,
        "total_cycle" => (int) $loan_terms
    ];
    
    $options = [
        'body' => wp_json_encode($params),
        'headers'     => array(
            'Content-Type' => 'application/json',
        ),
        'method'      => 'POST',
        'data_format' => 'body',
    ];

    $url_calc = get_application_api();
    $url_calc .= 'site/mc-calc-repayment';
    $response = wp_remote_post($url_calc, $options);
    $amount_emi = 0;
    $message = '';
    if ( is_array( $response ) && ! is_wp_error( $response ) ) {
        $result = json_decode($response['body']);    
        if( !$result->error && is_array($result->data)) {   
            $amount_emi = $result->data[0]->amount_emi;
            $message = json_encode(['error' => false, 'message' => '', 'data' => $amount_emi, 'interest' => $fees['interest'] ]);
        } else {
            $message = json_encode(['error' => true, 'message' => impode(", ", $result->message), 'data' => '', 'interest' => 0 ]);
        }
    } else {
        $message = json_encode(['error' => true, 'message' => $response->get_error_message(),  'data' => '']);
    }
    
    if( $ajax ) {
        return $message;
    } else {
        return $amount_emi;
    }
}

function custom_write_log($log_message, $log_file = 'sp-raw.log') {
 
    $log_path = PLUGIN_DIR_PATH . 'errors/'.$log_file;
    // Add a timestamp to the log message
    $log_message = '[' . date('Y-m-d H:i:s') . '] ' . print_r($log_message, 1) . PHP_EOL;
    $test_write = file_put_contents($log_path, $log_message, FILE_APPEND);
    if( is_wp_error($test_write) ) print $test_write->get_error_message();
}

/* normal login */
add_action('wp_ajax_verify_otp_login', 'mc_verify_otp_login');
add_action('wp_ajax_nopriv_verify_otp_login', 'mc_verify_otp_login');
function mc_verify_otp_login() {
    
    require_once(WP_PLUGIN_DIR.'/mc-application-form/classes/singpass.php');

    $verify_url = get_application_api();
    $verify_url .= 'site/wp-verify-otp'; 
    
    $verfiy_info = $_POST['verfiy_info'];
    $_SESSION['singPassChecked'] = [];
    if (!$verfiy_info) {
        print json_encode([ 'code' => 400, "message" => "The authentication request information is invalid.", 'data' => [ 'confirmed' => 0 ] ]);
    } else {
        
        $signatureAuth = createSignature();
        $params = [
            "otp_info" => $_SESSION['phoneVerification']['otp'], 
            'otp_verify' => $verfiy_info['otp_verify'], 
            'application_id' => null //$verfiy_info['application_id']
        ];
        $params = array_merge($params, $signatureAuth);
        $options = [
            'body' => $params,
            'timeout' => 30,
            'headers' => []
        ];

        // need remove when go live
        $test_env = 0;

        $response = wp_remote_post($verify_url,  $options);  
        if($test_env || isset($response['body'])) {
            $results = json_decode($response['body']);
            if( $results->error != 1 && $results->error != '1') {
                $_SESSION['phoneVerification']['status'] = 'confirmed';
                $_SESSION['singPassChecked'] = [
                    'personal' => [],
                    'NRIC_No_FIN' => $verfiy_info['nricno'],
                    'last_login' => time() //expire time
                ];                
                print json_encode([ 'code' => 200, "message" => "Login successful!", 'data' => ['confirmed' => 1] ]);
            } else {
                //'confirmed' => $test_env -> show OTP
                print json_encode([ 'code' => 401, "message" => "The OTP code is incorrect. Please check and re-enter.", 
                                'data' => ['confirmed' => 0] ]);
            }

            if( $test_env ) {
                $_SESSION['singPassChecked'] = [
                    'personal' => $customer,
                    'NRIC_No_FIN' => $verfiy_info['nricno'],
                    'last_login' => time() //expire time
                ]; 
            }

        } else 
            print json_encode([ 'code' => 401, "message" => "The OTP code is incorrect. Please check and re-enter.",
                            'data' => ['confirmed' => 0] ]);
    }

    die();
}

/* add_action('wp_ajax_verify_nric_fn', 'mc_verify_nric_fn');
add_action('wp_ajax_nopriv_verify_nric_fn', 'mc_verify_nric_fn');

function mc_verify_nric_fn() {
    $nric_fn = $_POST['nric_fn'];
    $validate_nric = validate_nric_fin($nric_fn);

    $confirmed = 0;
    $message = ($validate_nric) ? "" : "The NRIC/FIN you entered is either not in the correct format or does not exist.";
    print json_encode([ 'code' => 200, "message" => $message, 'confirmed' => $validate_nric ]);
    die();
} */

add_action('wp_ajax_login_verification', 'mc_login_verification');
add_action('wp_ajax_nopriv_login_verification', 'mc_login_verification');
function mc_login_verification() {
    $phone = $_POST['phonetocheck'];
    $nric = $_POST['nric_no'];
    //validation the NRIC No
    $checked_nric = validate_sg_nric($nric);
    if(!$checked_nric) {
        print json_encode([ 
            'code' => 400, 
            "message" => $checked_nric.': The NRIC / FIN is incorrect. Please check the characters and number sequence again.', 
            'data' => ['show_popup' => 0] 
        ]);
        die();
    }
    ///customer-portal/check-info
    $customer = mc_customer_check_info($nric, $phone);
    $customer_info = [];
    $match_phone = false; 
    $match_nric = false; 

    if(!$customer->error) {
        $customer_info = $customer->data;
        if( is_array($customer_info)) {
            $match_nric = false;         
            $match_phone = false;  
            foreach($customer_info as $info) {
                if($info->identification_no == $nric && $info->telephone == $phone) {
                    $match_nric = true;         
                    $match_phone = true;  
                    break;
                } else if($info->identification_no == $nric && $info->telephone != $phone) {
                    $match_nric = true;  
                    break;
                } else if($info->identification_no != $nric && $info->telephone == $phone) {
                    $match_phone = true;  
                    break;
                }
            } 
        }        
    }

    if( ($match_nric && !$match_phone) || (!$match_nric && $match_phone)) {
        print json_encode([ 'code' => 200, "message" => 'The NRIC/FIN and phone number you entered already exist in the system but do not belong to an individual.', 
                'data' => ['show_popup' => 0 , 'nric' => $match_nric, 'phone' => $match_phone], 'test' => $customer_info]);
        die();
    } else {
        mc_phone_verification();
    }
}

function sendOTPtoEmail($otp) {
    $to = 'testptesting12345@gmail.com';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $email_template = 'Here is your OPT: '.$otp;
    $sent = wp_mail($to, 'OTP to testing on the phone', $email_template, $headers);
}

/*
 What is the structure of NRIC number? How is the starting letter assigned?
    The structure of the NRIC number/FIN is @xxxxxxx#, where @ is a letter that can be S, T, F, G, or M, depending on the status of the holder:
    Singapore citizens and permanent residents born before 1 January 2000 are assigned the letter S
    Singapore citizens and permanent residents born on or after 1 January 2000 are assigned the letter T
    Singapore citizens and permanent residents are now given new cards with the letter M
    Foreigners issued with long-term passes before 1 January 2000 are assigned the letter F
    Foreigners issued with long-term passes on or after 1 January 2000 are assigned the letter G
 */
function validate_sg_nric($nric) {
    // Convert NRIC to uppercase to handle case insensitivity
    $nric = strtoupper($nric);

    // Regular expression to match the NRIC format
    if (!preg_match('/^([STFGM])(\d{7})([A-Z])$/', $nric, $matches)) {
        return false;
    }

    // Extract the components of the NRIC
    $prefix = $matches[1];
    $digits = $matches[2];
    $checksum_letter = $matches[3];

    // Weights used for each digit in the NRIC
    $weights = [2, 7, 6, 5, 4, 3, 2];

    // Calculate the total by multiplying each digit with its corresponding weight
    $total = 0;
    for ($i = 0; $i < 7; $i++) {
        $total += intval($digits[$i]) * $weights[$i];
    }

    // Add 4 to the total if the NRIC starts with 'T' or 'G'
    if ($prefix == 'T' || $prefix == 'G') {
        $total += 4;
    }
    
    // Add 4 to the total if the NRIC starts with 'M'
    if ($prefix == 'M') {
        $total += 3;
    }

    // Calculate the remainder of the total divided by 11
    $remainder = $total % 11;

    // Mapping of remainders to checksum letters for 'S' and 'T' prefixes
    $st_map = ['J', 'Z', 'I', 'H', 'G', 'F', 'E', 'D', 'C', 'B', 'A'];

    // Mapping of remainders to checksum letters for 'F' and 'G' prefixes
    $fg_map = ['X', 'W', 'U', 'T', 'R', 'Q', 'P', 'N', 'M', 'L', 'K'];

    // Mapping of remainders to checksum letters for 'M'prefixes
    $m_map = ['X', 'W', 'U', 'T', 'R', 'Q', 'P', 'N', 'J', 'L', 'K'];

    // Determine the expected checksum letter based on the prefix
    switch($prefix) {
        case 'S':
        case 'T':
            $expected_checksum = $st_map[$remainder];
        break;
        case 'F':
        case 'G':
            $expected_checksum = $fg_map[$remainder];
        break;
        case 'M':
            $expected_checksum = $m_map[$remainder];
        break;
        default:
            $expected_checksum = '';
        break;
    }

    // Verify if the calculated checksum matches the last letter of the NRIC
    return $checksum_letter === $expected_checksum;
}



add_action('wp_ajax_upload_files', 'mc_application_form_upload_files');
add_action('wp_ajax_nopriv_upload_files', 'mc_application_form_upload_files');
function mc_application_form_upload_files() {
    require_once(WP_PLUGIN_DIR.'/mc-application-form/classes/singpass.php');
    
    $appId = $_POST['appId'];
    $files = $_POST['files'];
  
    $upload_url = get_application_api();
    $upload_url .= 'application/wp-upload-files/'.$appId;     

    if (!is_numeric($appId) || $appId <= 0 || !$files) {
        print json_encode([ 'code' => 401, "message" => "Invalid application ID or files do not exist. Please check the information and try again.", 'data' => [ ] ]);
    } else {
        $signatureAuth = createSignature();
        $mc_settings = mc_settings('singpass_company_code');        

        $params = [
            "company" => (Int)$mc_settings['company_id'],
            'files' => $files
        ];

        $params = array_merge($params, $signatureAuth);
        $options = [
            'body' => $params,
            'timeout' => 30,
            'headers' => []
        ];
     
        $response = wp_remote_post($upload_url,  $options);        
        if(isset($response['body'])) {
            $results = json_decode($response['body']);
            if( !$results->error) {
                print json_encode([ 'code' => 200, "message" => "Files have been successfully updated."]);
            } else 
                print json_encode([ 'code' => 401, "message" => $results->message ]);
        } else 
            print json_encode([ 'code' => 401, "message" => "Files were not updated due to an error."]);
    }

    die();
}