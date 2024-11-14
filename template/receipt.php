<?php
$loan = mc_application_loan_details();
//print_r($loan);
?>
<div class="card h-100 box-shadow">
  <div class="card-body">
    <?php if (!isset($loan->error) || $loan->error) { ?>
      <div class="row jutify-content-center">
        <div class="col-12 col-md-8 col-lg-8">
          <div class="alert alert-info">
            The loan does not exist, please return to the loan list.
          </div>
        </div>
      </div>
    <?php } else {
      $loan_details  = $loan->data->loan_details;
      $loan_repayment_history = $loan->data->loan_info->loan_repayment_history;
      //print_r($loan_repayment_history);
      $loan_repayment  = $loan->data->loan_info->loan_repayment;

      $total_remaining_to_pay = 0;
      $overdueBalance = 0;
      $overdueInterest = 0;
      $interestOverduePayment = 0;

      $instalment_schedule = $loan->data->instalment_schedule;

      $currentDate = date('Y-m-d');
      foreach ( $instalment_schedule as $id => $item ) {
        if( $item->instalment_due_date <= $currentDate && $item->principal_balance > 0) {
          $overdueBalance  += $item->principal_balance;
          $overdueInterest += $item->interest_balance;
          $interestOverduePayment += $item->late_interest_balance;
        }
      }
    ?>
      <div class="row">
        <div class="col-12 col-lg-5 col-xl-4">
          <div class="customer-info">
            <h4>Loan Details</h4>
            <table class="table">
              <tbody>
                <tr>
                  <th scope="row" class="w-50 w-lg-25">Loan Type</th>
                  <td><?php print $loan->data->loan_info->type_name; ?></td>
                </tr>
                <tr>
                  <th scope="row">Loan No</th>
                  <td><?php print $loan->data->loan_info->loan_no; ?></td>
                </tr>
                <tr>
                  <th scope="row">Loan Status</th>
                  <td><?php print loanStatus($loan->data->loan_info->status); ?></td>
                </tr>
                <tr>
                  <th scope="row">Loan Amout</th>
                  <td><?php print format_money($loan->data->loan_details->amount_of_loan); ?></td>
                </tr>               
                <tr>
                  <th scope="row">Outstanding Principle</th>
                  <td><?php print format_money($loan->data->loan_details->total_principal_balance); ?></td>
                </tr>
                <tr>
                  <th scope="row">Outstanding Amount Payable</th>
                  <td><?php print format_money($loan_details->total_payable); ?></td>
                </tr>
                <tr>
                  <th scope="row">Terms</th>
                  <td><?php 
                  $loan_term = $loan->data->loan_info->loan_term;
                  $term_unit = LoanTermUnit($loan->data->loan_details->term_unit);
                  $total_remaining_to_pay = $loan->data->loan_details->remaining_months_must_be_paid;
                  //$loan_term - count($loan_repayment_history) + 1;

                  print $loan_term.' '.$term_unit; 
                  ?></td>
                </tr>            
                <tr>
                  <th scope="row">Start Date</th>
                  <td><?php print dateFormat($loan->data->loan_info->approval_date); ?></td>
                </tr>
                <tr>
                  <th scope="row">Maturity Date</th>
                  <td><?php 
                  $pos = @count($loan->data->loan_info->loan_repayment);
                  print dateFormat($loan->data->loan_info->loan_repayment[$pos - 1]->due_date);
                  ?></td>
                </tr>
                <tr>
                  <th scope="row">Next payment date</th>
                  <td><?php 
                  $current_date = date('Y-m-d').'T00:00:00.000Z';
                  $next_payment = null;
                  foreach($loan_repayment as $item) { 
                    if( !$next_payment && $item->due_date >= $current_date) {
                      $next_payment = $item;
                    }
                  }
                  if( isset($next_payment->due_date)) print dateFormat($next_payment->due_date);
                  ?></td>
                </tr>
                <tr>
                  <th scope="row">Next payment amount</th>
                  <td><?php 
                  if( isset($next_payment->total_amount)) print format_money($next_payment->total_amount);
                  ?></td>
                </tr>
                <tr>
                  <th scope="row">Overdue Balance</th>
                  <td><?php print format_money($overdueBalance);?></td>
                </tr>
                <tr>
                  <th scope="row">Overdue Interest</th>
                  <td><?php print format_money($overdueInterest);?></td>
                </tr> 
                <tr>
                  <th scope="row">Interest on Overdue Payment</th>
                  <td><?php print format_money($interestOverduePayment);?></td>
                </tr>
                <tr>
                  <th scope="row">Total number of remaining is instalments, including next one</th>
                  <td><?php print $total_remaining_to_pay;?></td>
                </tr>
                <tr>
                  <th scope="row">Last payment date</th>
                  <td>
                    <?php 
                      $last_payment = $loan_repayment_history[ count($loan_repayment_history) - 1];
                      print dateFormat($last_payment->repayment_date);
                    ?>
                  </td>
                </tr>
                <tr>
                  <th scope="row">Last payment amount</th>
                  <td><?php 
                  $last_payment_amount = ($last_payment->principal_paid + $last_payment->interest_paid + 
                  $last_payment->late_interest_paid + $last_payment->penalty_paid + $last_payment->amount_of_acceptance)
                  - ($last_payment->discount_interest + $last_payment->discount_late_interest + 
                    $last_payment->discount_late_fee + $last_payment->discount_principal);
                  print format_money(round($last_payment_amount, 2));
                  ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-12 col-lg-7 col-xl-8">
          <div class="loan-payment-history">
            <div class="payment-history">
              <h4>Loan Payment History</h4>             
            </div>
            <table class="table responsive">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Receipt No</th>
                  <th scope="col" class="text-right">Date</th>
                  <th scope="col" class="text-right">Amount</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($loan_repayment_history) {
                  foreach ($loan_repayment_history as $order => $item) {
                    $amount_received = $item->principal_paid + $item->interest_paid +
                      $item->late_interest_paid + $item->penalty_paid + $item->amount_of_acceptance -
                      $item->discount_interest - $item->discount_late_interest - $item->discount_late_fee - $item->discount_principal;
                ?>
                    <tr>
                      <th data-title="#" scope="row"><?php print($order + 1); ?></th>
                      <td data-title="Receipt No">
                        <a href="#" title="Click here to download PDF" class="receiptDownloadPDF" 
                          data-receiptId="<?php print $_GET['id'];?>"  data-receiptNo="<?php print $item->repayment_no; ?>">
                          <?php print $item->repayment_no ?>
                        </a>                        
                      </td>
                      <td data-title="Date" class="text-right"><?php print dateFormat($item->repayment_date); ?></td>
                      <td data-title="Amount Received" class="text-right"><?php print format_money($amount_received) ?></td>               
                    </tr>
                  <?php }
                } else { ?>
                  <tr>
                    <td colspan="10" class="text-center">
                      No matching records found.
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>