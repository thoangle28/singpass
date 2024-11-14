<?php
$employment = [];
$jobOptions = job_listing();
?>
<div class="row">
  <div class="col-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label required">Employed Status</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <?php $employment_status = ['EMP' => 'Employed', 'UNEMPINC' => 'Self Employed', 'UNEMP' => 'Unemployed'];
          $select = '';
          ?>
          <?php create_select_control($employment_status, 'employment_status', $select, 'required'); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label need-required">Company Name</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <?php $select = ''; ?>
          <input type="text" class="form-control need-required" name="company_name" value="<?php print $select; ?>">
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label need-required">Address</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <?php $select = ''; ?>
          <input type="text" class="form-control need-required" name="address" value="<?php print $select; ?>">
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label need-required">Office No.</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <span class="input-group-text">+65</span>
          <?php $select = ''; ?>
          <input type="text" class="form-control phone-number need-required" name="company_telephone" value="<?php print $select; ?>">
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label need-required">Postal Code</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <?php $select = ''; ?>
          <input type="text" class="form-control need-required" name="portal_code" value="<?php print $select; ?>">
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label need-required">Position</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">         
          <?php create_select_control($positions, 'position', $select, '','need-required'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label need-required">Occupation</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <?php $select = ''; ?>
          <input type="text" class="form-control need-required" name="occupation" value="<?php print $select; ?>">
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label required">Industry</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <?php create_select_control($jobOptions, 'job_type_id', $select, 'required'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end "><label class="col-form-label two-lines need-required">Yrs of Employment Period</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <input type="text" class="form-control need-required" name="yrs_of_employment_period" value="<?php print $select; ?>">
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-lg-12 col-xxl-4">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-2 col-xxl-6 d-flex-end d-flex align-items-start"><label class="col-form-label required mt-10">Annual Gross Income</label></div>
      <div class="col-12 col-sm-8 col-lg-10 col-xxl-6">
        <div class="input-group">
          <span class="input-group-text">$</span>
          <?php $select = isset($singPassChecked['personal']->noa->amount->value) ? @$singPassChecked['personal']->noa->amount->value : ''; ?>
          <input type="text" class="form-control annual_income income" name="annual_income" value="<?php print $select; ?>" required>
        </div>
        <div><small class="text-muted">include AWS and Bonus</small></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6 col-xxl-4">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 col-xxl-6 d-flex-end "><label class="col-form-label required two-lines">Average Monthly Income</label></div>
      <div class="col-12 col-sm-8 col-lg-8 col-xxl-6">
        <div class="input-group">
          <span class="input-group-text">$</span>
          <?php $select = ''; ?>
          <input type="text" class="form-control income ave_monthly_income" name="monthly_income" value="" disabled>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6 col-xxl-4">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-6 d-flex-end "><label class="col-form-label required two-lines">Past 6 Month Gross Income</label></div>
      <div class="col-12 col-sm-8 col-lg-6">
        <div class="input-group">
          <span class="input-group-text">$</span>
          <?php $select = ''; ?>
          <input type="text" class="form-control income six_month_income" name="six_months_income" value="" disabled>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-lg-4 d-flex-end d-flex align-items-start">
        <label class="mt-2 col-form-label required pt-3px">Gross Monthly Income</label></div>
      <div class="col-12 col-sm-8 col-lg-8">
        <div class="input-group">
          <span class="input-group-text">$</span>
          <?php $select = ''; ?>
          <input type="text" class="form-control monthly_income income" name="monthly_income_1" value="" required>
        </div>
        <div><small class="text-muted">1st month</small></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-8 col-lg-3">
    <div class="mb-4 row">
      <div class="col-12 col-sm-6 d-flex d-lg-none"></div>
      <div class="col-12 col-sm-6 col-lg-12">
        <div class="input-group">
          <span class="input-group-text">$</span>
          <?php $select = ''; ?>
          <input type="text" class="form-control monthly_income income" name="monthly_income_2" value="" required>
        </div>
        <div><small class="text-muted">2nd month</small></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-4 col-lg-3">
    <div class="mb-4 row">
      <div class="col-12 col-sm-12 col-lg-12">
        <div class="input-group">
          <span class="input-group-text">$</span>
          <?php $select = ''; ?>
          <input type="text" class="form-control monthly_income income" name="monthly_income_3" value="" required>
        </div>
        <div><small class="text-muted">3rd month</small></div>
      </div>
    </div>
  </div>
</div>
<div class="row align-items-start">  
  <div class="col-12 col-sm-4 col-lg-2 d-flex-end mb-3"><label class="col-form-label mt-1">Income Document</label></div>
  <div class="col-12 col-sm-8 col-lg-10">
    <div class="row">
      <div class="col-12 col-sm-12 col-lg-8">
        <?php 
          $docOptions = documents(); 
          $docOptions = (count($docOptions) <= 0 ) ? [ 1 => 'PaySlip' , 2 => 'CPF', 3 => 'NOA', 4 => 'Others'] : $docOptions;
        ?>
        <div class="input-group gap-2" id="income_document">
        <?php $select = []; ?>
          <?php foreach ($docOptions as $value => $text) { 
            $checked = (in_array($value, $select)) ? "checked" : "";
            ?>
            <div class="form-check form-check-inline">
              <input class="form-check-input form-control" <?php print $checked;?> type="checkbox" 
                id="inlineCheckbox<?php print $value; ?>" value="<?php print $value; ?>" name="income_document">
              <label class="form-check-label" for="inlineCheckbox<?php print $value; ?>"><?php print $text; ?></label>
            </div>
          <?php } ?>
        </div>
        <div class="mb-4 row">
          <div class="col-12 col-sm-12 col-lg-12">
            <div class="quick-file-uploader gap-3 image-singpass d-flex px-2 py-2 border border-dashed border-primary rounded mt-3 justify-content-between bg-light align-items-start">
              <div class="mt-2 chooseFiles">
                <img class="w-24" src="<?php print PLUGIN_DIR_URL . "assets/images/file-arrow-up.svg"; ?>" />
              </div>
              <div>
                <div class="chooseFiles">
                  <div class="need-required required"><small><strong>Quick File Uploader</strong></small></div>
                  <div><small class="text-muted">Please select at least 1 file from the computer (file upload maximum 200MB and only upload files in PDF format).</small></div>
                </div>
                <?php             
                $select = [];
                $files = [];
                ?>
                <div class="mt-3 <?php print ($files) ? '' : 'd-none'; ?> fileListPreview">
                  <ul id="filePreview" class="p-0 m-0">
                    <?php foreach($files as $id => $file) { 
                    ?>
                      <li class="file-preview d-flex gap-2 mb-3 align-items-end" data-item="<?php print $id;?>">
                        <span class="no-delete">
                          <img src="<?php print PLUGIN_DIR_URL . "assets/images/pdf.png"; ?>" />
                        </span> <small><?php print $file->document_name;?> (<?php print round($file->size/1024, 2); ?>KB)</small></li>
                    <?php } ?>
                  </ul>
                </div>
                <input type="file" multiple name="doc_files" id="doc_files" class="d-none">
                <input type="hidden" name="income_document_files" id="upload_files" value="" class="form-control document_files" required>
              </div>
            </div>
            <div class="input-group mt-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="supply_doc_offile" id="supply_doc_offile" value="1">
                <label class="form-check-label" for="supply_doc_offile">
                  Supply document offline
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row mb-4 align-items-start">
  <div class="col-12 col-sm-4 col-lg-2 d-flex-end"><label class="col-form-label required mt-1">Source of Income</label></div>
  <div class="col-12 col-sm-8 col-lg-10">
    <div class="d-flex gap-5 input-group">
      <textarea class="form-control" name="source_income" rows="4" required></textarea>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-sm-12 col-lg-12">
    <div class="mb-4 row align-items-start">
      <div class="col-12 col-sm-4 col-lg-2 d-flex-end mb-3"><label class="col-form-label mt-1">Bankruptcy</label></div>
      <div class="col-12 col-sm-8 col-lg-10">
        <div class="row">
          <div class="col-12 col-lg-12">
            <div class="group-1 mb-3">
              <div class="mb-2">
                <label class="col-form-label required">Have you been declared bankrupt in the past 5 years?</label>
              </div>
              <div class="input-group btn-status">
                <?php $args = [1 => 'Yes', 0 => 'No'];
                $select = '';
                ?>
                <?php create_select_control($args, 'bankrupted', $select, 'required'); ?>
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-12">
            <div class="group-1 mb-3">
              <div class="mb-2">
                <label class="col-form-label required">Do you have any plans to declare bankruptcy in the next 3 months?</label>
              </div>
              <div class="input-group btn-status">
                <?php $args = [1 => 'Yes', 0 => 'No']; $select = ''; ?>
                <?php create_select_control($args, 'bankrupt_plan', $select, 'required'); ?>
              </div>
            </div>
          </div>
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
          <button class="btn btn-primary px-4" id="btnContinueStep3">Continue</button>
        </div>
      </div>
    </div>
  </div>
</div>