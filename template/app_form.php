<?php require_once('menu_tools.php'); ?>
<div class="container-fluid application_form">
  <?php
  global $singPassChecked;
  $singPassChecked = [];
  $singPassChecked = $_SESSION['singPassChecked'];
  if (!$singPassChecked  || !isset($singPassChecked['NRIC_No_FIN']) || !$singPassChecked['NRIC_No_FIN']) { ?>
    <div class="row justify-content-center mt-5">
      <div class="col-12 col-lg-10 col-xl-8 col-xxl-6">
        <div class="card box-shadow">
          <div class="card-body p-3 p-md-4">
            <div class="mx-auto p-3 p-md-5 border border-dashed border-primary rounded bg-light text-center">
              <div class="row">
                <div class="col-12 col-md-12">
                  <div class="mb-3">
                    <p>Please log in securely through <a class="text-danger fw-normal" href="https://www.singpass.gov.sg/" target="_blank">SingPass</a> for verification. Your information is only accessed with your consent and remains fully confidential within this application.</p>
                    <p>When you press "Login with SingPass", you agree to let us use a portion of the data returned from SingPass to process the input information on the financial application form when you request financial profile approval.</p>
                  </div>
                </div>
                <div class="col-12 col-md-12">
                  <div class="login-with-singpass-normal">
                    <?php
                    $singPass = singpass_login_url();
                    if (isset($singPass['error'])) { //if 1?>
                      <div class="alert alert-danger p-3 text-center">
                        <?php print $singPass['error'];?>
                      </div>
                    <?php  } else if( $singPass == '#') {?>
                      <div class="alert alert-danger p-3 text-center">Signature verification failed. Please contact the system administrator to update the information accurately.</div>
                      <?php } else { ?>
                      <div class="row login-form d-flex justify-content-center">
                        <div class="col-12 col-sm-6 mx-auto">
                          <div class="my-3">
                            <input type="hidden" value="<?php print @$singPass['cookie']; ?>" id="singPassConfirm">
                            <input type="hidden" value="<?php print @$singPass['singpass_url']; ?>" id="singPassUrl">
                            <div class="button">
                              <a id="singPassLogin" href="#" class="text-white btn btn-danger btn-small w-100 mx-auto px-4">Login with Singpass</a>
                            </div>
                          </div>
                          <!-- <div class="my-3">
                            <div class="button d-flex align-items-center h-100">
                              <a href="/" class="text-white btn btn-primary btn-small w-100 mx-auto px-5">Cancel</a>
                            </div>
                          </div> -->
                          <div class="my-4 login-or"><hr /><span class="bg-light">OR</span></div>
                        </div>
                      </div>                      
                      <form class="row justify-content-center" id="loginWithNricNo">
                      <div class="col-12 col-sm-12 mx-auto mb-4">You can log in using your NRIC with phone verification. Please ensure that you’re the authorized holder of the NRIC, as using someone else’s information is against legal guidelines.</div>
                        <div class="col-12 col-sm-6 mx-auto">
                          <div class="form-group mb-3">
                            <input type="text" class="form-control" id="nricNo" placeholder="NRIC No." autocomplete="off">
                          </div>
                          <div class="form-group input-group mb-3">
                            <input type="tel" class="form-control" id="phonenumber"  placeholder="Phone Number" autocomplete="off">
                          </div>                      
                          <div class="login-form d-flex gap-3 justify-content-center">                   
                            <div class="my-3">
                              <div class="login-button text-white btn btn-primary btn-small w-100 mx-auto px-5" id="loginNormal">
                                Login
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php wp_nonce_field('login_action', 'login_nonce_field'); ?>
                      </form>
                      <div>
                        <small>If you don't want to log in, please click <a class="text-primary" href="/">here</a> to cancel.</small>
                      </div>
                    <?php 
                    } // if 1?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } else {
    $customer = mc_customer_details();
    $total_loan_active = 0;
    $is_existing = 'new';
    if ($customer && isset($customer->error) && !$customer->error && isset($customer->data)) {
      $outstanding_loan = 0;//$customer->data->outstanding_loan;
      if(isset($customer->data->customer_details->borrower)) {
        $status = [1, 5, 7]; //active, bad, new
        foreach($customer->data->customer_details->borrower as $item) {
          if( $item->loan && isset($item->loan->status) && in_array($item->loan->status, $status)) {
            $outstanding_loan++;
          }
        }
      }
      $total_loan_active = $outstanding_loan;//count($outstanding_loan);
      $is_existing = 'existing';
    }

    $act = isset($_GET['act']) && $_GET['act'] ? $_GET['act'] : '';
    $file = 'loans.php';
    $title = 'SUMMARY';
    switch ($act) {
      case 'newloan':
        $file = 'loan_form.php';
        break;
      default:
        $file = 'loans.php';
        if (isset($_GET['id']) && $_GET['id']) {
          $file = 'receipt.php';
        }        
        break;
    }
    //sendMailToManager();
  ?>
    <div class="row mb-3 mt-5">
      <div class="col-12">
        <div class="card card box-shadow">
          <div class="card-body">
            <div class="row">
              <div class="col-12 col-lg-6">
                <h3 class="mb-3 mt-0 mb-md-0"><?php print $title; ?></h3>
              </div>
              <div class="col-12 col-lg-6">
                <div class="navbar-collapse justify-content-end d-flex">
                  <ul class="nav gap-3">
                    <li class="nav-item">
                      <div class="">
                        <a class="nav-link fw-normal text-white py-1 btn btn-success btn-small mx-auto px-3" href="?act=loan">My Loans</a>
                      </div>
                    </li>
                    <?php
                    $limited_loan_number = get_option('mc_limited_loan_num');
                    $limited_loan_number = (intval($limited_loan_number) > 0) ? $limited_loan_number : 9999;
                    $id = ($total_loan_active < $limited_loan_number) ? 'createNewApplication' : 'noNewApplication';
                    ?>
                    <li class="nav-item">
                      <div class="">
                        <a id="<?php print $id; ?>" class="nav-link fw-normal text-white py-1 btn btn-primary btn-small mx-auto px-3" href="?act=newloan">Create New Application</a>
                      </div>
                    </li>
                    <li class="nav-item">
                      <div class="">
                        <a href="?logout=true" class="btn btn-danger btn-small mx-auto nav-link text-white fw-normal py-1 px-3">Logout</a>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="wrapper pb-5">
      <?php
      require_once($file);
      ?>
    </div>
  <?php } ?>
</div>