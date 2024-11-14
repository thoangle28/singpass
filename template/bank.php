<?php
//$data = $_SESSION['steps']['bank']['data'];
//$data = ($data) ? json_decode(base64_decode($data)) : [];
$bank = [];
?>
<div class="row">
  <div class="col-lg-6 col-xxl-4">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label">Bank Name</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <?php $select = isset($bank->bank_name_1) ? $bank->bank_name_1 : ''; ?>
          <input type="text" class="form-control" name="bank_name_1" value="<?php print $select; ?>">
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-xxl-4">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label">Bank Account</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <?php $select = isset($bank->account_number_1) ? $bank->account_number_1 : ''; ?>
          <input type="text" class="form-control" name="account_number_1" value="<?php print $select; ?>">
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-12  col-xxl-4">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-2 col-xxl-4 d-flex-end "><label class="col-form-label required">Date of Salary</label></div>
      <div class="col-12 col-sm-8 col-lg-10 col-xxl-8">
        <div class="input-group">
          <?php $dayOfMonth = [];
          for ($i = 1; $i <= 31; $i++) {           
            $dayOfMonth[$i] = getOrdinalSuffix($i);
          }
          $select = isset($bank->date_of_salary) ? $bank->date_of_salary : '';
          ?>
          <?php create_select_control($dayOfMonth, 'date_of_salary', $select,'required'); ?>         
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row d-none"> 
  <div class="col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label">Bank Code</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <?php $select = isset($bank->bank_code_1) ? $bank->bank_code_1 : ''; ?>
          <input type="text" class="form-control" name="bank_code_1" value="<?php print $select; ?>">
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="mb-4 row">
      <div class="col col-lg-12">
        <div class="d-flex-end gap-3 mb-form-submit">
          <button class="btn btn-danger px-4 btnCancel">Cancel</button>
          <!-- <button class="btn btn-secondary px-4" id="btnDraftStep5">Save Draft</button> -->
          <button class="btn btn-primary px-4" id="btnContinueStep5">Continue</button>
        </div>
      </div>
    </div>
  </div>
</div>