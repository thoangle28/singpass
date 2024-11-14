<?php
$steps = $steps_defaut;
$percent = 0;
$total_steps = count($steps) - 1;
foreach ($steps as $key => $step) {
  if ($key != 'completion' && $step['status'] == 'complete') $percent += 1;
}

$percent = 0; //round(($percent/$total_steps)*100,0);

?>
<?php
$limited_loan_number = get_option('mc_limited_loan_num');
$limited_loan_number = (intval($limited_loan_number) > 0) ? $limited_loan_number : 9999;

if( isset($customer->data->customer_details) && $customer->data->customer_details->is_ban ) { 
?>
<div class="row justify-content-center">
  <div class="col-12 col-lg-12">
    <div class="card h-100 box-shadow">
      <div class="card-body pt-5">
        <div class="alert alert-info w-100 w-md-50  mx-auto">
          <p>Dear Customer,</p>
          <p>We regret to inform you that your profile currently does not meet our service requirements due to a credit issue. Therefore, we are unable to provide services to you at this time.</p>
          <p>Thank you for your interest, and we hope to serve you in the future when credit conditions are favorable.</p>
          <p>Sincerely,</p>
          <p>Monetium Credit (s) Pte Ltd</p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } else if ($total_loan_active < $limited_loan_number) { ?>
  <div class="row position-relative">
    <div class="col-12 col-lg-4 col-xxl-3 col-xxl-2-small mb-nav-tabs">
      <div class="card h-100 box-shadow">
        <div class="close-open d-block d-xl-none">
          <span></span>
        </div>
        <div class="card-body pt-5 overflow-hidden">
          <div class="main-menu">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
              <?php
              $i = 1;
              foreach ($menuItems as $key => $item) {
                $active = ($item['selected'])   ? 'active' : '';
                $disabled = '';
                if ($steps[$key]['status'] == 'draft') $disabled = 'disabled';
              ?>
                <button <?php print $disabled; ?> class="p-0 nav-link w-100 <?php print $active; ?>" id="<?php print $key; ?>-tab" data-bs-toggle="pill" data-bs-target="#<?php print $key; ?>" type="button" role="tab" aria-controls="<?php print $key; ?>" aria-selected="<?php print $item['selected']; ?>">
                  <div class="menu-item">
                    <div class="mumber-order"><?php print $i++; ?></div>
                    <div class="menu-item-title text-left">
                      <h5><?php print $item['label']; ?></h5>
                      <p class="small"><?php print $item['description']; ?></p>
                    </div>
                  </div>
                </button>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-8 col-xxl-9 col-xxl-8-large">
      <div class="card h-100 box-shadow">
        <div class="card-body">
          <div class="application-status mb-4 bg-info-subtle">
            <div class="row align-items-end justify-content-end">
              <div class="col-12 col-lg-7 d-none">
                <div class="mb-2 text-danger"><strong>Note:</strong> You must click the "<b>Continue</b>" button to update the data you have entered/changed on the form to ensure it is the latest before sending it to Finance 365.</div>
              </div>
              <div class="col-12 col-lg-5">
                <div class="w-100">
                  <div class="percent-complete d-flex justify-content-between align-items-center w-100 mb-2">
                    <div>Loan Application Completion</div>
                  </div>
                  <div class="progress w-100">
                    <div class="progress-bar <?php print ($percent == 100) ? 'bg-success' : ''; ?>" role="progressbar" style="width: <?php print $percent; ?>%" aria-valuenow="<?php print $percent; ?>" aria-valuemin="0" aria-valuemax="100"><?php print $percent; ?>%</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-content" id="v-pills-tabContent">
            <?php
            $i__ = 1;
            foreach ($menuItems as $key => $item) {
              $active = ($item['selected'])   ? 'show active' : '';
            ?>
              <div class="tab-pane fade <?php print $active; ?>" id="<?php print $key; ?>" role="tabpanel" aria-labelledby="<?php print $key; ?>-tab" tabindex="0">
                <h5><?php print $i__++; ?>. <?php print $item['label']; ?></h5>
                <hr />
                <div class="wrapper-form py-4 px-2" id="step_<?php print $key; ?>">
                  <?php require_once($item['template'] . '.php'); ?>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } else { ?>
  <div class="row justify-content-center">
    <div class="col-12 col-lg-12">
      <div class="card h-100 box-shadow">
        <div class="card-body pt-5">
          <div class="alert alert-info w-50 mx-auto">
            <h5>Create New Application</h5>
            <p>The loan application is unable to submit now. Please contact our friendly agent.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } ?>