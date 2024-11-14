<?php
function get_company($code = '') {
   //get company
   $company = [];
   try{
     $companies = mc_company($code);
     $companies = (isset($companies->error) && $companies->error == 'false') ? $companies->data : [];
     foreach($companies as $com) {
       $company[$com->company_code] = [ 
         'company_code' => $com->company_code, 
         'company_id' => $com->id,
         'mlcb_client_id' => $com->mlcb_client_id,
       ];
     }      
   } catch(Exception $e){

   }
   
   return $company;
}

function singpass_settings($params) {
  if ( ! isset( $params['nonce_field'] ) 
      || ! wp_verify_nonce( $params['nonce_field'], 'singpass_action' ) 
  ) {
    $error = ['code' => 400, 'message' => 'Bad request', 'data' => null];    
  } else {
    $data = [];
    $required = false;
   
    if( $params['form'] && is_array($params['form'])) {
      $form = json_encode($params['form']);
      $form = json_decode($form);
      $company_code = '';
      foreach($form as $id => $item) {
        if( is_object($item)) {
          if( $item->name === 'singpass_company_code') {
            $company_code = $item->value;
          } else $data[$item->name] = $item->value;
        }
        if( $item->value == '') $required = true;
      }
    }

    $company = get_company($company_code);
    $company_value = [];
    foreach($company as $com) {
      if( $com['company_code'] == $company_code) {
        $company_value = (array)$com;
      }
    }
   
    $data['singpass_company_code'] = serialize($company_value);

    unset($data['singpass_myinfo']);
    unset($data['singpass_nonce_field']);
   
    //save
    if( !$required ) {
      update_option('mc_singpass', $data);
      $error = ['code' => 200, 'message' => 'The settings for Singpass have been updated successfully!', 'data' => $company_value];   
    } else {
      $error = ['code' => 200, 'message' => 'There are some fields that need data entry. Please fill in all the required information.', 'data' => null ];   
    }
  }

  return wp_json_encode( $error );
}

function api_settings($params) {
  $error = [];

  if ( ! isset( $params['nonce_field'] ) 
      || ! wp_verify_nonce( $params['nonce_field'], 'api_action' ) 
  ) {
    $error = ['code' => 400, 'message' => 'Bad request', 'data' => null];    
  } else {
    $isUrl = filter_var($params['url'], FILTER_VALIDATE_URL);
    $mlcb = filter_var($params['url_mlcb'], FILTER_VALIDATE_URL);
    $mlcb_uid = $params['mlcb_uid'];
    $otp_sms = $params['check_otp'];
    $limited_loan = $params['limited_loan'];

    if( !$params['url'] || !$isUrl) {
      $error = ['code' => 400, 'message' => "You haven't integrated the API yet, or the format is incorrect."]; 
    } else {
      $mlcb_url  = ( !$mlcb ) ? $params['url'] : $params['url_mlcb'];
      update_option('mc_application_endpoint', $params['url']);
      update_option('mc_application_mlcb', $mlcb_url);
      update_option('mc_mlcb_uid', $mlcb_uid);
      update_option('mc_otp_sms_verification', $otp_sms);
      update_option('mc_limited_loan_num', $limited_loan);
      
      $error = ['code' => 200, 'message' => 'The link has been updated.']; 
    }   
  }

  return wp_json_encode( $error );
}

function email_settings($params) {
  $error = [];
  $data = [];
  $required = false;

  if ( ! isset( $params['nonce_field'] ) 
      || ! wp_verify_nonce( $params['nonce_field'], 'email_action' ) 
  ) {
    $error = ['code' => 400, 'message' => 'Bad request', 'data' => null];    
  } else {

    if( $params['form'] && is_array($params['form'])) {
      $form = json_encode($params['form']);
      $form = json_decode($form);

      foreach($form as $id => $item) {
        if( is_object($item)) {
          if( $item->name == 'email_content') 
            $data[$item->name] = stripslashes($params['email_content']);
          else
            $data[$item->name] = sanitize_text_field($item->value);
        }
        if( $data[$item->name] == '') $required = true;
      }
    }
    
    $isEmail = filter_var($data['email_receive'], FILTER_VALIDATE_EMAIL);

    if( !$data['email_receive'] || !$isEmail) {
      $error = ['code' => 400, 'message' => "The email address is not valid."]; 
    } else {
      if( !$required ) {
        unset($data['email_action']);
        unset($data['email_nonce_field']);
        update_option('mc_email_manager', $data);
        $error = ['code' => 200, 'message' => 'The email template have been updated successfully!', 'data' => null ];   
      } else {
        $error = ['code' => 200, 'message' => 'There are some fields that need data entry. Please fill in all the required information.', 'data' => null ];   
      }
    }   
  }

  return wp_json_encode( $error );
}

function email_customer($params) {
  $error = [];
  $data = [];
  $required = false;

  if ( ! isset( $params['nonce_field'] ) 
      || ! wp_verify_nonce( $params['nonce_field'], 'customer_action' ) 
  ) {
    $error = ['code' => 400, 'message' => 'Bad request', 'data' => null];    
  } else {

    if( $params['form'] && is_array($params['form'])) {
      $form = json_encode($params['form']);
      $form = json_decode($form);

      foreach($form as $id => $item) {
        if( is_object($item)) {
          if( $item->name == 'email_content') 
            $data[$item->name] = stripslashes($params['email_content']);
          else
            $data[$item->name] = sanitize_text_field($item->value);
        }
        if( $data[$item->name] == '') $required = true;
      }
    }

    if( !$required ) {
      unset($data['email_action']);
      unset($data['email_nonce_field']);
      update_option('mc_email_customer', $data);
      $error = ['code' => 200, 'message' => 'The email customer have been updated successfully!', 'data' => null ];   
    } else {
      $error = ['code' => 200, 'message' => 'There are some fields that need data entry. Please fill in all the required information.', 'data' => null ];   
    }
  }

  return wp_json_encode( $error );
}