<div class="card h-100 box-shadow">
  <div class="card-body">
    <div class="row justify-content-center">
      <?php
      $borrower = false;
      if ($customer && isset($customer->error) && !$customer->error) {
        $info = $customer->data->customer_details;
        $profile = $customer->data->overview->profile;
        $borrower = true;
      ?>
        <div class="col-12 col-lg-12 col-xl-4 col-xxl-3">
          <h3>My Info</h3>
          <table class="table">
            <tbody>
              <tr>
                <th scope="row">Customer No</th>
                <td><?php print $info->customer_no; ?></td>
              </tr>
              <tr>
                <th scope="row">Identification No</th>
                <td><?php print $profile->identification_no; ?></td>
              </tr>
              <tr>
                <th scope="row">Country</th>
                <td><?php print $profile->country_id; ?></td>
              </tr>
              <tr>
                <th scope="row">Full Name</th>
                <td><?php print $info->fullName; ?></td>
              </tr>
              <tr>
                <th scope="row">Gender</th>
                <td><?php print ucfirst(strtolower($profile->gender)); ?></td>
              </tr>
              <tr>
                <th scope="row">Email</th>
                <td><?php print obfuscateEmailAddress($profile->email1);
                    ?></td>
              </tr>
              <tr>
                <th scope="row">Mobile</th>
                <td><?php print obfuscatePhone($profile->mobilephone_1); ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      <?php
      }
      ?>
      <div class="col-12 col-lg-12 col-xl-8 col-xxl-9">
        <?php
        if (isset($_GET['opt']) && $_GET['opt'] == 'summary') {
          require_once('summary.php');
        } else {
        $loans = ($borrower) ? mc_application_check_loan() : [];
        $applications = mc_application_pending();
        
        if (!$borrower || (isset($loans->error) && $loans->error) || (isset($applications->error)  && $applications->error)) { ?>
          <div class="row justify-content-center my-5">
            <div class="col-12 col-md-10 col-lg-10">
              <div class="alert alert-info">
                We noticed that you haven't registered an account with Finance360. To take full advantage of our services, please complete your registration and become our customer!
                <br /><br />How to register:<br />
                •⁠ ⁠Click the <a href="?act=newloan" class="fw-normal">Create New Application</a> button<br />
                •⁠ If not, please click <a href="/" class="fw-normal">here</a> to return to the hompage
              </div>
            </div>
          </div>
          <?php } else { //
          $myLoans = ($loans && isset($loans->data) && isset($loans->data->totalLoanActiveOfBorrower)) ? $loans->data->totalLoanActiveOfBorrower : [];
          $loansList = ($loans && isset($loans->data) && isset($loans->data->loans)) ? $loans->data->loans : [];
          $monthly_due_date = ($loansList) ? $loansList[0]->monthly_due_date : '';

          $loanTypes = loanListing();
          $loanToShow = [];
          foreach ($loanTypes as $loan_id => $loanType) {
            $loanItems = [];
            foreach ($myLoans as $id => $loanItem) {
              if ($loanItem->loan_type_id == $loan_id) {
                $loanItems[] = $loanItem;
              }
            }
            if ($loanItems) {
              $loanToShow[] = ['loan_type' => $loanType, 'list' => $loanItems]; //list
            }
          }

          if ($myLoans && is_array($myLoans) && $loanToShow) {
            foreach ($loanToShow as $id => $itemToShow) {
          ?>
              <h3><?php print $itemToShow['loan_type']; ?></h3>
              <table class="table table-hover responsive">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Ref. No</th>
                    <th scope="col" class="text-right">Amount</th>
                    <th scope="col" class="text-right">Outstanding Balance</th>
                    <th scope="col" class="text-right">Due on</th>
                    <th scope="col">Terms</th>
                    <th scope="col" class="text-center">Status</th>
                    <th scope="col" class="text-right">Loan Date</th>
                    <th scope="col" class="text-center">Views</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  foreach ($itemToShow['list'] as $id => $loan) {
                    $total_principal_balance = 0;
                    foreach ($loansList as $t => $item) {
                      if ($total_principal_balance <= 0 && $loan->loan_no ==  $item->loan_acc_no)
                        $total_principal_balance = $item->total_principal_balance;
                    }
                  ?>
                    <tr>
                      <th scope="col" data-title="#"><?php print($id + 1); ?></th>
                      <td data-title="Ref. No">
                        <?php print $loan->loan_no; ?>
                      </td>
                      <td data-title="Amount" class="text-right"><?php print format_money($loan->loan_amount); ?></td>
                      <td data-title="Outsantanding Balance" class="text-right"><?php print format_money($total_principal_balance); ?></td>
                      <td data-title="Due On" class="text-right">
                        <?php
                        $term_unit = termUnit($loan->term_unit);
                        print ($term_unit == 'Monthly') ? getOrdinalSuffix($monthly_due_date) : '';
                        ?>
                      </td>
                      <td data-title="Terms"><?php print $loan->loan_term; ?> <?php print $term_unit; ?></td>
                      <td data-title="Status" class="text-center">
                        <span style="margin: 0 auto; max-width: 100px; display: block;"><?php print loanStatus($loan->status); ?></span>
                      </td>
                      <td data-title="Loan Date" class="text-right"><?php print dateFormat($loan->approval_date); ?></td>
                      <td data-title="Views" class="text-center"><a href="?act=loans&id=<?php print $loan->id; ?>" class="fw-normal">
                          <img class="check-image" src="<?php print PLUGIN_DIR_URL . "assets/images/eye-regular.svg"; ?>" />
                        </a></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            <?php
            } //for
          } // if myLoan

          if ($applications && $applications->data) { ?>
            <h3>Application(s)</h3>
            <table class="table table-hover responsive">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Application No.</th>
                  <th scope="col" class="text-right">Loan Type</th>
                  <th scope="col" class="text-right">Amount</th>
                  <th scope="col">Terms</th>
                  <th scope="col" class="text-center">Status</th>
                  <th scope="col" class="text-right">Application Date</th>
                  <th scope="col" class="text-center">View</th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($applications->data as $id => $app) {
                ?>
                  <tr>
                    <th scope="col" data-title="#"><?php print($id + 1); ?></th>
                    <td data-title="Ref. No">
                      <?php print $app->application_no; ?>
                    </td>
                    <td scope="col" data-title="Loan Type" class="text-right">
                      <?php print @$loanTypes[$app->loan_type_id]; ?>
                    </td>
                    <td data-title="Amount" class="text-right"><?php print format_money($app->loan_amount_requested); ?></td>

                    <td data-title="Terms"><?php print @$app->loan_terms; ?> <?php  $term_unit = termUnit($app->term_unit); print $term_unit; ?></td>
                    <td data-title="Status" class="text-center">
                      <span style="margin: 0 auto; max-width: 100px; display: block;"><?php print loanStatus(0); ?></span>
                    </td>
                    <td data-title="Loan Date" class="text-right"><?php print dateFormat($app->application_date); ?></td>
                    <td data-title="Views" class="text-center"><a href="?act=loans&pid=<?php print $app->id; ?>&opt=summary" class="fw-normal">
                      <img class="check-image" src="<?php print PLUGIN_DIR_URL . "assets/images/eye-regular.svg"; ?>" />
                    </a></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          <?php } //if application
        }  

        if (
          $borrower && (!$loanToShow) && (isset($applications->data) && !$applications->data)
        ) {
          ?>
          <div class="row justify-content-center my-5">
            <div class="col-12 col-md-10 col-lg-10">
              <div class="alert alert-info">
                It appears that you currently do not have any loans with us.
              </div>
            </div>
          </div>
        <?php } 
        } ?>
      </div>
    </div>
  </div>
</div>

<style>
  .application_form .card-body h3 {
    text-align: left;
  }
</style>