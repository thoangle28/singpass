<?php
/*
Plugin Name: MC Application Form
Plugin URI: #
Description: Allow consumers to create an information profile and send requests to the finance company.
Version: 1.0
Author: Breez Lab
Author URI: #
License: GPL2
*/
define('PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

if (!class_exists('MC_Application_Form')) {
  class MC_Application_Form
  {

    // Constructor
    public function __construct()
    {
      // Initialize your plugin here
      $this->load_dependencies();
      // Add actions and filters here
      //add_action('admin_notices', array($this, 'admin_notice'));
      add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
      add_action('init', array($this, 'init'));
      add_action('admin_menu', array($this, 'add_admin_menu'));
      //add_action('admin_init', array($this, 'settings_init'));
      add_action('admin_enqueue_scripts', array($this, 'stylesheet_to_admin'));

      //save api
      add_action('wp_ajax_api_application', array($this, 'mc_api_application'));
      add_action('wp_ajax_nopriv_api_application', array($this, 'mc_api_application'));

      //save singpass
      add_action('wp_ajax_singpass_myinfo', array($this, 'mc_singpass_myinfo'));
      add_action('wp_ajax_nopriv_singpass_myinfo', array($this, 'mc_singpass_myinfo'));

      //save email
      add_action('wp_ajax_email_template', array($this, 'mc_email_template'));
      add_action('wp_ajax_nopriv_email_template', array($this, 'mc_email_template'));
      //save email customer
      add_action('wp_ajax_email_customer', array($this, 'mc_email_customer'));
      add_action('wp_ajax_nopriv_email_customer', array($this, 'mc_email_customer'));
    }

    public function mc_upload_mimes($mimes)
    {
      // Add new MIME types here
      $mimes['pem'] = 'application/x-x509-ca-cert'; // .pem file extension
      $mimes['svg'] = 'image/svg+xml';              // .svg file extension
      $mimes['json'] = 'application/json';          // .json file extension

      // Add other file types if needed
      // $mimes['ext'] = 'mime/type'; // Example format

      return $mimes;
    }

    private function load_dependencies()
    {
      // Include the helper class
      require_once PLUGIN_DIR_PATH . 'classes/singpass.php';
      require_once PLUGIN_DIR_PATH . 'classes/mlcb.php';
      require_once PLUGIN_DIR_PATH . 'classes/front_end.php';
      require_once PLUGIN_DIR_PATH . 'admin/settings.php';
    }

    // Admin Notice
    public function admin_notice()
    {
?>
      <div class="notice notice-success is-dismissible">
        <p><?php _e('Hello, this is my first plugin!', 'my-first-plugin'); ?></p>
      </div>
    <?php
    }

    // Enqueue Scripts and Styles
    public function enqueue_scripts()
    {
      $folder = PLUGIN_DIR_URL; //;
      //boostrap 5.3

      wp_enqueue_script('bootstrap', $folder . 'assets/bootstrap/js/bootstrap.min.js', array(), null, true);
      wp_enqueue_script('popper', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js', array(), null, true);
      wp_enqueue_script('sweetalert2', $folder . 'assets/sweetalert2/sweetalert2.min.js', array(), null, true);
      //wp_enqueue_script('stickyfloat', $folder . 'assets/stickyfloat.js', array(), null, true);

      wp_enqueue_style('bootstrap-css', $folder . 'assets/bootstrap/css/bootstrap.min.css');
      wp_enqueue_style('mc-css', $folder . 'assets/custom.css?' . rand());
      wp_enqueue_style('sweetalert2-css', $folder . 'assets/sweetalert2/sweetalert2.min.css');

      // Enqueue jQuery (if not already included)
      wp_enqueue_script('jquery');
      wp_enqueue_script('mc-custom', $folder . 'assets/custom.js?' . rand(), array('jquery'), null, true);
      // Localize the script with new data
      wp_localize_script('mc-custom', 'mc_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('mc_ajax_nonce')
      ));
    }

    public function stylesheet_to_admin()
    {
      wp_enqueue_style('mc-css', PLUGIN_DIR_URL . 'assets/admin.css');
      wp_enqueue_script('jquery');
      wp_enqueue_script('mc-admin', PLUGIN_DIR_URL . 'assets/admin.js?' . rand(), array('jquery'), null, true);
      // Localize the script with new data
      wp_localize_script('mc-admin', 'mc_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('mc_ajax_nonce')
      ));

      //Enqueue the Media Uploader script
      wp_enqueue_media();
    }


    public function init()
    {
      if (!session_id()) {
        session_start();
      }

      $this->register_shortcodes();
      $this->singPassChecked();
      //$this->create_application_form();
    }

    //init singin with singPass
    public function singPassChecked()
    {
      if (!isset($_SESSION['singPassChecked'])) {
        $_SESSION['singPassChecked'] = [];
      }

      //check timeout
      if( isset($_SESSION['singPassChecked']) && isset($_SESSION['singPassChecked']['last_login'])) {
        $login_time = $_SESSION['singPassChecked']['last_login'];   
        $current_time = time();
        $time = 1; //1h -> 24h = 24;
        $expiry_time = $login_time + ($time * 60 * 60); // 1 day in seconds

        if ($current_time > $expiry_time) {
          print '-->'.($current_time > $expiry_time).' 454 454 5556<-----';  
          //Login expired, destroy session
          $_SESSION['singPassChecked'] = [];
          $_SESSION['steps'] = [];
          $redirect = home_url("/application-form/");
          print '<script>window.location.href = "' . $redirect . '";</script>';
          die();
        } 
      }
      //check after login
      if (isset($_GET['code']) && $_GET['code']) {
        $getPersonalInfo = mc_get_personal();  
       
        if ($getPersonalInfo && !isset($getPersonalInfo->error)) {
          $args['personal'] = $getPersonalInfo;
          if (isset($getPersonalInfo->uinfin)) {
            //write singpass  raw 
            custom_write_log($getPersonalInfo, $getPersonalInfo->uinfin->value.'.log');
            $args['NRIC_No_FIN'] = $getPersonalInfo->uinfin->value;
            $args['last_login'] = time(); //expire time
          }
          $_SESSION['singPassChecked'] = $args;
        } else {
          $_SESSION['singPassChecked'] = [];
        }
      }
      //logout
      if (isset($_GET['logout']) && $_GET['logout'] == true) {
        $_SESSION['singPassChecked'] = [];
        $_SESSION['steps'] = [];
      }
    }
    // Register Shortcodes
    public function register_shortcodes()
    {
      add_shortcode('mc_application_form', array($this, 'mc_application_form'));
    }

    //create applicaton form
    public function mc_application_form()
    {
      //ob_start();
      require_once('template/app_form.php');
      //$output_string = ob_get_contents();
      //ob_end_clean();
      //return $output_string;
    }

    // Add Admin Menu
    public function add_admin_menu()
    {
      add_options_page('MC Settings', 'MC Settings', 'manage_options', 'mc_settings', array($this, 'settings_page'));
    }

    // Settings Page
    public function settings_page()
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
        'singpass_environment' => '',
        'singpass_validate_otp' => ''
      ];
      $mc_endpoint = get_option('mc_application_endpoint');
      $mc_endpoint_mlcb = get_option('mc_application_mlcb');
      $mc_mlcb_uid = get_option('mc_mlcb_uid');
      $otp_sms_verifytation = get_option('mc_otp_sms_verification');
      $otp_sms_verifytation = ($otp_sms_verifytation === true || $otp_sms_verifytation == 'true') ? 'checked' : '';
      $mc_singpass = get_option('mc_singpass');
      $limited_loan_number = get_option('mc_limited_loan_num');
      $limited_loan_number = (intval($limited_loan_number) > 0) ? $limited_loan_number : 2;

      if ($mc_singpass) {
        $tem = unserialize($mc_singpass['singpass_company_code']);        
        $mc_singpass['singpass_company_code'] =  $tem['company_code'];
        $mc_singpass['singpass_company_mlcb'] =  $tem['mlcb_client_id'];
      } else $mc_singpass = [];

      $mc_singpass = array_merge($defaults, $mc_singpass);

      //get email for manager
      $mail = get_option('mc_email_manager');
      $receive_email = @$mail['email_receive'];
      $email_content = isset($mail['email_content']) ? $mail['email_content'] : ''; 
      $email_subject = @$mail['email_subject'];
      $email_logo    = @$mail['email_logo'];

      $customer = get_option('mc_email_customer');
      
      $email_content1 = isset($customer['email_content']) ? $customer['email_content'] : ''; 
      $email_subject1 = @$customer['email_subject1'];
      $email_logo1    = @$customer['email_logo1'];
      // Editor settings
      $settings = array(
        'textarea_name' => 'email_content', //Name of the textarea
        'media_buttons' => true, //Show media upload button
        'editor_height' => 400, // Height of the editor 
      );
    ?>
      <div class="wrap">
        <h1>MC Application Settings</h1>
        <div class="apis-emails-settings">
          <div class="header">
            <ul>
              <li><a href="?page=mc_settings&tab=tab1" class="nav-tab <?php echo ((isset($_GET['tab']) && $_GET['tab'] == 'tab1') || (!isset($_GET['tab']))) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Application API', 'textdomain'); ?></a></li>
              <li><a href="?page=mc_settings&tab=tab3" class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'tab3' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Singpass Settings', 'textdomain'); ?></a></li>
              <li><a href="?page=mc_settings&tab=tab2" class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'tab2' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Email Manager', 'textdomain'); ?></a></li>
              <li><a href="?page=mc_settings&tab=tab4" class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'tab4' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Email Customer', 'textdomain'); ?></a></li>
            </ul>
          </div>
          <div class="tab-content">
            <?php if ((isset($_GET['tab']) && $_GET['tab'] == 'tab1') || (!isset($_GET['tab']))) { ?>
              <form method="post" action="" name="api_application">
                <fieldset style="width: 95%;">
                  <h3 class="section-title">Application API</h3>
                  <div class="d-flex">
                    <div class="col-4 col-item"><label class="required">End point</label></div>
                    <div class="col-6 col-item"><input type="text" value="<?php print $mc_endpoint; ?>" size="50" name="api_endpoint" id="api_endpoint"></div>
                  </div>
                  <div class="d-flex">
                    <div class="col-4 col-item"><label class="required">MLCB - End point </label></div>
                    <div class="col-6 col-item"><input type="text" value="<?php print $mc_endpoint_mlcb; ?>" size="50" name="api_endpoint_mlcb" id="api_endpoint_mlcb"></div>
                  </div>
                  <div class="d-flex">
                    <div class="col-4 col-item"><label class="required">MLCB UID </label></div>
                    <div class="col-6 col-item"><input type="text" value="<?php print $mc_mlcb_uid; ?>" size="50" name="mc_mlcb_uid" id="mc_mlcb_uid"></div>
                  </div>
                  <div class="d-flex">
                    <div class="col-4 col-item"><label class="required">Enable check OTP/SMS</label></div>
                    <div class="col-6 col-item"><input type="checkbox" <?php print $otp_sms_verifytation; ?> 
                        value="1" name="api_check_otp_sms" id="api_check_otp_sms" style="margin-top: 8px;"></div>
                  </div>
                  <div class="d-flex">
                    <div class="col-4 col-item"><label class="required">Limited Loan No.</label></div>
                    <div class="col-6 col-item"><input type="number" value="<?php print $limited_loan_number; ?>" name="limited_loan_number" id="limited_loan_number"></div>
                  </div>
                  <div class="d-flex">
                    <div class="col-4 col-item"><label></label></div>
                    <input type="hidden" value="api_application" name="api_application" id="api_application">
                    <div class="col-6 col-item">
                      <input type="button" value="Save change" class="button button-primary" id="btnAppApi">
                    </div>
                  </div>
                </fieldset>
                <?php wp_nonce_field('api_action', 'api_nonce_field'); ?>
              </form>
            <?php } else if (isset($_GET['tab']) && $_GET['tab'] == 'tab2') { ?>
              <div class="email-settings">
                <form method="post" action="" name="emailTemplate" id="emailTemplate">
                  <fieldset style="width: 95%;">
                    <h3 class="section-title">Email settings</h3>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Receive Email</label></div>
                      <div class="col-6 col-item"><input type="email" value="<?php print $receive_email; ?>" size="50" name="email_receive" id="email_receive"></div>
                    </div>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Email Logo</label></div>
                      <div class="col-6 col-item d-flex gap-3 d-flex-row">
                        <input type="text" value="<?php print $email_logo; ?>" size="50" name="email_logo" id="email_logo">
                        <button id="upload_image_button" class="button" data-text="email_logo"><?php esc_html_e('Upload Image', 'textdomain'); ?></button>
                      </div>
                    </div>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Email Subject</label></div>
                      <div class="col-8 col-item">
                        <input type="email" value="<?php print $email_subject; ?>" size="50" name="email_subject" id="email_subject">
                      </div>
                    </div>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Email Content</label></div>
                      <div class="col-8 col-item">
                        <?php wp_editor($email_content, 'email_content', $settings); ?>
                      </div>
                    </div>                  
                    <div class="d-flex">
                      <div class="col-4 col-item"><label></label></div>
                      <input type="hidden" value="email_template" name="email_action" id="email_action">
                      <div class="col-6 col-item">
                        <input type="button" value="Save change" class="button button-primary" id="btnEmail">
                        <?php wp_nonce_field('email_action', 'email_nonce_field'); ?>
                      </div>
                    </div>
                  </fieldset>
                </form>
              </div>
            <?php } else if (isset($_GET['tab']) && $_GET['tab'] == 'tab4') { ?>
              <div class="email-customer">
                <form method="post" action="" name="emailCustomer" id="emailCustomer">
                  <fieldset style="width: 95%;">
                    <h3 class="section-title">Email settings</h3>                   
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Email Logo</label></div>
                      <div class="col-6 col-item d-flex gap-3 d-flex-row">
                        <input type="text" value="<?php print $email_logo1; ?>" size="50" name="email_logo1" id="email_logo1">
                        <button id="upload_image_button" class="button"  data-text="email_logo1"><?php esc_html_e('Upload Image', 'textdomain'); ?></button>
                      </div>
                    </div>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Email Subject</label></div>
                      <div class="col-8 col-item">
                        <input type="email" value="<?php print $email_subject1; ?>" size="50" name="email_subject1" id="email_subject1">
                      </div>
                    </div>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Email Content</label></div>
                      <div class="col-8 col-item">
                        <?php wp_editor($email_content1, 'email_content1', $settings); ?>
                      </div>
                    </div>                    
                    <div class="d-flex">
                      <div class="col-4 col-item"><label></label></div>
                      <input type="hidden" value="email_customer" name="customer_action" id="customer_action">
                      <div class="col-6 col-item">
                        <input type="button" value="Save change" class="button button-primary" id="btnCustomer">
                        <?php wp_nonce_field('customer_action', 'email_nonce_field'); ?>
                      </div>
                    </div>
                  </fieldset>
                </form>
              </div>
            <?php } else { ?>
              <div class="singpass-form">
                <fieldset style="width: 95%;">
                  <form method="post" action="" id="singpassForm">
                    <!--
                  /**
                    * Set the following demo app configurations for the demo app to run
                    *
                    * CLIENT_ID: Client id provided during onboarding
                    * SUBENTITY_ID: optional parameter for platform applications only
                    * CLIENT_PRIVATE_SIGNING_KEY : private signing key for client_assertion
                    * CLIENT_PRIVATE_ENCRYPTION_KEYS : folder to private encryption keys, allow multiple keys to match multiple encryption keys in JWKS
                    * PURPOSE_ID: purpose_id with reference to purpose that will be shown to user on consent page provided during onboarding
                    * SCOPES: Space separated list of attributes to be retrieved from Myinfo
                    * MYINFO_API_AUTHORIZE: The URL for Authorize API
                    */
                  -->

                    <h3 class="section-title">Singpass Settings</h3>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Company Code</label></div>
                      <div class="col-6 col-item">
                        <input type="text" size="50" name="singpass_company_code" value="<?php print @$mc_singpass['singpass_company_code']; ?>">
                        <div><small>The company code is issued through the Finance 360 application. MLCB: <?php print @$mc_singpass['singpass_company_mlcb']; ?></small></div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Client ID</label></div>
                      <div class="col-6 col-item">
                        <input type="text" size="50" name="singpass_client_id" value="<?php print @$mc_singpass['singpass_client_id']; ?>">
                        <div><small>Client id provided during onboarding</small></div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Purpose ID</label></div>
                      <div class="col-6 col-item">
                        <input type="text" size="50" name="singpass_purpose_id" value="<?php print @$mc_singpass['singpass_purpose_id']; ?>">
                        <div><small>with reference to purpose that will be shown to user on consent page provided during onboarding</small></div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Scope</label></div>
                      <div class="col-6 col-item">
                        <input type="text" size="50" name="singpass_scope" value="<?php print @$mc_singpass['singpass_scope']; ?>">
                        <div><small>Space separated list of attributes to be retrieved from Myinfo</small></div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Serect ID</label></div>
                      <div class="col-6 col-item">
                        <input type="text" size="50" name="singpass_serect_id" value="<?php print @$mc_singpass['singpass_serect_id']; ?>">
                        <div><small>The Serect code to confirm with Loan App</small></div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Codechallenge Url</label></div>
                      <div class="col-6 col-item">
                        <input type="text" size="50" name="singpass_codechallenge_url" value="<?php print @$mc_singpass['singpass_codechallenge_url']; ?>">
                        <div><small>The URL for return the codechallenge</small></div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Personal Url</label></div>
                      <div class="col-6 col-item">
                        <input type="text" size="50" name="singpass_personal_url" value="<?php print @$mc_singpass['singpass_personal_url']; ?>">
                        <div><small>The URL for return the personal info</small></div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Callback Url</label></div>
                      <div class="col-6 col-item">
                        <input type="text" size="50" name="singpass_callback_url" value="<?php print @$mc_singpass['singpass_callback_url']; ?>">
                        <div><small>The URL for return the website</small></div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Authorize API</label></div>
                      <div class="col-6 col-item">
                        <input type="text" size="50" name="singpass_athorize_url" value="<?php print @$mc_singpass['singpass_athorize_url']; ?>">
                        <div><small>The URL for Authorize API</small></div>
                      </div>
                    </div>
                    <!-- <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Enable SMS/OTP</label></div>
                      <div class="col-6 col-item">
                        <input type="checkbox" name="singpass_validate_otp" value="1"
                          <?php print @$mc_singpass['singpass_validate_otp'] == 1 ? 'checked' : ''; ?>>
                        <div><small>Used to confirm the active email address and phone number.</small></div>
                      </div>
                    </div> -->
                    <div class="d-flex">
                      <div class="col-4 col-item"><label class="required">Environment</label></div>
                      <div class="col-6 col-item">
                        <?php
                        $environment = ['test.' => "Test", 'prod.' => 'Production'];
                        ?>
                        <select name="singpass_environment" style="width: 150px;">
                          <option value="">-- Select --</option>
                          <?php foreach ($environment as $key => $item) {
                            $selected = ($mc_singpass['singpass_environment'] == $key) ? 'selected="selected"' : "";
                          ?>
                            <option <?php print $selected; ?> value="<?php print $key; ?>"><?php print $item; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="d-flex">
                      <div class="col-4 col-item"><label></label></div>
                      <div class="col-6 col-item">
                        <input type="button" value="Save change" class="button button-primary" id="btnSingpass">
                      </div>
                    </div>
                    <input type="hidden" value="singpass_myinfo" name="singpass_myinfo" id="singpass_myinfo">
                    <?php wp_nonce_field('singpass_action', 'singpass_nonce_field'); ?>
                  </form>
                </fieldset>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
<?php
    }

    //create new page
    public function create_application_form()
    {
      // Code to run on plugin activation
      $post_exists = get_page_by_path('mc-application', OBJECT, 'page');
      $slug = 'mc-application';
      if ($post_exists) return;
      //create new post
      $args = [
        'post_title' => 'MC Application',
        'post_content' => '[mc_application_form]',
        'post_status' => 'publish',
        'post_type' => 'page'
      ];
      // Insert the post into the database
      $post_id = wp_insert_post($args);

      // Check if the post was created successfully
      if (!is_wp_error($post_id)) {
        echo "Post created successfully! Post ID: " . $post_id;
      } else {
        echo "Error creating post: " . $post_id->get_error_message();
      }
    }

    //Ajax
    public function mc_api_application()
    {
      $response = api_settings($_POST);
      print_r($response);
      die();
    }

    public function mc_singpass_myinfo()
    {
      $response = singpass_settings($_POST);
      print_r($response);
      die();
    }

    public function mc_email_template()
    {
      $response = email_settings($_POST);
      print_r($response);
      die();
    }

    public function mc_email_customer()
    {
      $response = email_customer($_POST);
      print_r($response);
      die();
    }
    // Activation Hook
    public static function activate() {}

    // Deactivation Hook
    public static function deactivate()
    {
      // Code to run on plugin deactivation
    }
  }

  // Initialize the plugin
  $mc_app_form = new MC_Application_Form();

  // Activation and Deactivation hooks
  register_activation_hook(__FILE__, array('MC_Application_Form', 'activate'));
  register_deactivation_hook(__FILE__, array('MC_Application_Form', 'deactivate'));
}
