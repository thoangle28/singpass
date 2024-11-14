<?php
$loan = [];
?>
<div class="row">
  <div class="col-12 col-lg-4">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-6 d-flex-end "><label class="col-form-label required">Loan Type</label></div>
      <div class="col-12 col-sm-8 col-lg-6">
        <div class="input-group">
          <?php
          $loanOptions = loanListing();
          $select = isset($loan->loan_type_id) ?  $loan->loan_type_id : '';
          ?>
          <?php create_select_control($loanOptions, 'loan_type_id', $select, 'calc-installment required'); ?>
        </div>
      </div>
      <input type="hidden" name="loan_rate" value='<?php print json_encode(calculatorInterest()); ?>' id="loanRate">
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label required">Loan Terms</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <?php
          $select = isset($loan->loan_terms) ?  $loan->loan_terms : '';
          ?>
          <input type="number" class="form-control calc-installment" name="loan_terms" id="loan_terms" required value="<?php print $select; ?>">
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label required">Term Unit</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <?php $args = [
            3 => 'Monthly', 2 => 'Bi-Weekly', 1 => 'Weekly', 0 => 'Daily'
          ];
          $select = isset($loan->term_unit) ?  $loan->term_unit : 3;
          ?>
          <?php create_select_control($args, 'term_unit', $select, 'calc-installment required'); ?>
          <input type="hidden" class="form-control" name="loan_interest" value="" id="loanInterest">
          <input type="hidden" class="form-control" name="interest" value="" id="loInterest">
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-lg-6 d-none">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label">No of Active Credit Loan</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <?php $select = isset($loan->no_of_active_credit_loan) ?  $loan->no_of_active_credit_loan : ''; ?>
          <input type="number" class="form-control" name="no_of_active_credit_loan" id="no_of_active_credit_loan" value="<?php print $select; ?>">
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-12 col-sm-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label required">Loan Amount Required</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <span class="input-group-text">$</span>
          <?php $select = isset($loan->loan_amount_requested) ?  $loan->loan_amount_requested : ''; ?>
          <input type="text" class="form-control income" id="loan_amount_requested" name="loan_amount_requested" value="<?php print $select; ?>" required>
          <span class="input-group-text group-check input-mlcb-checked">
            <img class="check-image uncheck" src="<?php print PLUGIN_DIR_URL . "assets/images/square-regular.svg"; ?>" />
            <img class="check-image d-none checked" src="<?php print PLUGIN_DIR_URL . "assets/images/square-check-regular.svg"; ?>" />
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label">Instalment</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <?php 
          //1.	Equal Monthly Installment (EMI) = Amount * (Interest) * (1 + Interest)^(Loan term) / (((1 + Interest)^(Loan term))) - 1)
          $installmen = 0; 
          ?>
          <span class="input-group-text">$</span>
          <input type="text" class="form-control" name="installment" value="<?php print $installment; ?>" disabled="disabled">
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">  
  <div class="col-12 col-lg-12">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-2 d-flex-end mb-text-right"><label class="col-form-label required">Reason For Loan</label></div>
      <div class="col-12 col-sm-8 col-lg-10">
        <div class="input-group">
          <?php $select = isset($loan->loan_reason) ?  $loan->loan_reason : ''; ?>
          <?php create_select_control($reason_for_loan, 'loan_reason', $select, 'required'); ?>
          <!-- <input type="text" class="form-control" name="loan_reason" value="<?php print $select; ?>" required> -->
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
    <div class="mb-4 row align-items-start">
      <div class="col-12 col-sm-4 mb-text-right col-lg-2 d-flex-end"><label class="col-form-label required mt-2">Description</label></div>
      <div class="col-12 col-sm-8 col-lg-10">
        <div class="input-group">
          <?php $select = isset($loan->description) ?  $loan->description : ''; ?>
          <textarea type="text" class="form-control" name="description" required style="min-height: 150px;"><?php print $select; ?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
    <div class="mb-4 row align-items-center">
      <div class="col-12 col-sm-4 col-lg-2 d-flex-end"></div>
      <div class="col-12 col-sm-8 col-lg-10">
        <div class="input-group">
          <div class="quick-file-uploader gap-3 image-singpass d-flex px-3 py-2 border border-dashed border-primary rounded mt-3 justify-content-between bg-light align-items-start">
            <div class="mt-2 chooseFiles">
              <img class="w-24" src="<?php print PLUGIN_DIR_URL . "assets/images/file-arrow-up.svg"; ?>" />
            </div>
            <div>
              <div class="chooseFiles">
                <div class="not-required"><small><strong>Quick File Uploader</strong></small></div>
                <div><small class="text-muted">Please select at least 1 file from the computer (file upload maximum 200MB and only upload files in PDF format).</small></div>
              </div>
              <?php
              $select = [];
              $files = [];
              ?>
              <div class="mt-3 <?php print ($files) ? '' : 'd-none'; ?> fileListPreview">
                <ul id="filePreview" class="p-0 m-0">
                  <?php foreach ($files as $id => $file) {
                  ?>
                    <li class="file-preview d-flex gap-2 mb-3 align-items-end" data-item="<?php print $id; ?>">
                      <span class="no-delete">
                        <img src="<?php print PLUGIN_DIR_URL . "assets/images/pdf.png"; ?>" />
                      </span> <small><?php print $file->document_name; ?> (<?php print round($file->size / 1024, 2); ?>KB)</small>
                    </li>
                  <?php } ?>
                </ul>
              </div>
              <input type="file" multiple name="doc_files" id="doc_files" class="d-none">
              <input type="hidden" name="document_files" id="upload_files" value="" class="form-control document_files">
            </div>
          </div>
        </div>       
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 col-sm-4 col-lg-2 d-flex-end align-items-start">
    <label class="col-form-label required mb-3">Borrower(s) & Surety(ies)</label></div>
  <div class="col-12 col-sm-8 col-lg-10">
    <div class="mb-4 row align-items-center">      
      <div class="col-12 col-sm-12 col-lg-12">        
        <div><label class="col-form-label">Is there any other individual or company (Beneficial Owner) that will benefit from the loan?</label></div>
        <div class="d-flex gap-5 input-group mt-3">
          <div class="form-check">
            <input class="form-check-input form-control" data-required=".benefit" type="radio" name="benefit" id="benefitYes" value="1">
            <label class="form-check-label" for="benefitYes">
              Yes
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input form-control" data-required=".benefit" type="radio" name="benefit" id="benefitNo" value="0" checked>
            <label class="form-check-label" for="benefitNo">
              No
            </label>
          </div>
        </div>
        <div class="mt-3 benefit" style="display:none">
          <div><label class="col-form-label need-required">Please Explain</label></div>
          <textarea class="form-control need-required" name="benefit_explain" value="" rows="4"></textarea>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-sm-4 col-lg-2 d-flex-end"></div>
  <div class="col-12 col-sm-8 col-lg-10">
    <div class="pb-4 row align-items-end h-100">
      <div class="col-12 col-sm-12 col-lg-12">
        <label class="col-form-label required">Are you a politically-exposed person?</label>
        <div class="d-flex gap-5 input-group mt-3">
          <div class="form-check">
            <input class="form-check-input form-control" data-required=".politically" type="radio" name="politically" id="politicallyYes" value="1">
            <label class="form-check-label" for="politicallyYes">
              Yes
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input form-control" data-required=".politically" type="radio" name="politically" id="politicallyNo" value="0" checked>
            <label class="form-check-label" for="politicallyNo">
              No
            </label>
          </div>
        </div>
        <div class="mt-3 politically" style="display:none">
          <div><label class="col-form-label need-required">Please Explain</label></div>
          <textarea class="form-control need-required" name="politically_explain" value="" rows="4"></textarea>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
    <div class="mb-4 row">
      <div class="col-12 col-lg-12">
        <div class="d-flex-end gap-3 mb-form-submit">
          <button class="btn btn-danger px-4 btnCancel">Cancel</button>
          <button class="btn btn-primary px-4" id="btnContinueStep4">Continue</button>
        </div>
      </div>
    </div>
  </div>
</div>