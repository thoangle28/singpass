<?php
$completion = [];
?>
<div class="mc-accordion accordion my-3" id="accordionGuarantor">
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        <strong>Surety Information</strong>
      </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionGuarantor">
      <div class="accordion-body">
        <div class="row mb-2">
          <div class="col-12 text-danger">
            <small>If you enter NRIC No., all fields in this section will be compulsory. If NRIC No., is empty, all fields in this section will be erased by the system.</small>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label need-required">NRIC No.</label>
            <div class="input-group">
              <input type="text" class="form-control need-required" name="guarantor_info.identification_no" id="guarantor_nric_no">
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label need-required">ID Type</label>
            <div class="input-group">
              <select class="form-select form-control need-required" name="guarantor_info.identification_type">
                <option value=""></option>
                <option value="singapore_nric_no">Singapore NRIC No</option>
                <option value="foreign_identification_number">Foreign Identification Number</option>
              </select>
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label need-required">First Name</label>
            <div class="input-group">
              <input type="text" class="form-control need-required" name="guarantor_info.firstname">
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label need-required">Last Name</label>
            <div class="input-group">
              <input type="text" class="form-control need-required" name="guarantor_info.lastname">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label form-label need-required">Gender</label>
            <div class="input-group">
              <?php create_select_control($gender, 'guarantor_info.gender', '', '', 'need-required'); ?>
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label need-required">Date Of Birth</label>
            <div class="input-group">
              <input type="date" data-format="mm/dd/yyyy" class="form-control need-required" 
              min="1900-01-01" placeholder="mm/dd/yyyy" name="guarantor_info.date_of_birth">
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label form-label need-required">Nationality</label>
            <div class="input-group">
              <?php
              listDropDownCountries('guarantor_info.nationality', 192, '', 0, 'need-required', $countryList); ?>
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label need-required">Obligation</label>
            <div class="input-group">
              <select class="form-select form-control need-required" name="guarantor_info.obligation_code">
                <option value=""></option>
                <option value="G">Surety</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
        <strong>Contact Information</strong>
      </button>
    </h2>
    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionGuarantor">
      <div class="accordion-body">
        <div class="row">
          <div class="col-12 col-lg-4 mb-4">
            <label class="col-form-label form-label need-required">Phone Number 1</label>
            <div class="input-group">
              <span class="input-group-text">+65</span>
              <input type="text" class="form-control phone-number need-required" name="guarantor_info.phone_1">
            </div>
          </div>
          <div class="col-12 col-lg-4 mb-4">
            <label class="col-form-label form-label">Phone Number 2</label>
            <div class="input-group">
              <span class="input-group-text">+65</span>
              <input type="text" class="form-control phone-number" name="guarantor_info.phone_2">
            </div>
          </div>
          <div class="col-12 col-lg-4 mb-4">
            <label class="col-form-label form-label">Home</label>
            <div class="input-group">
              <span class="input-group-text">+65</span>
              <input type="text" class="form-control phone-number" name="guarantor_info.phone_home">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-lg-6 mb-4">
            <label class="col-form-label form-label">Email</label>
            <div class="input-group">
              <input type="email" class="form-control" name="guarantor_info.email">
            </div>
          </div>
          <div class="col-12 col-lg-6 mb-4">
            <label class="col-form-label form-label">Alternate Email</label>
            <div class="input-group">
              <input type="email" class="form-control" name="guarantor_info.email_alternate">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label need-required">Property Type</label>
            <div class="input-group">
              <?php $args = ['HDB' => 'HDB', 'Private Residential' => 'Private Residential']; ?>
              <?php create_select_control($args, 'guarantor_info.property_type', '', '', 'need-required'); ?>
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label need-required">Housing Type</label>
            <div class="input-group">
              <?php
              print housingType('guarantor_info.housing_type', '', 'need-required','');
              ?>
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label">Unit</label>
            <div class="input-group">
              <input type="text" class="form-control" name="guarantor_info.unit">
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label need-required">Block</label>
            <div class="input-group">
              <input type="text" class="form-control need-required" name="guarantor_info.block">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-lg-6 mb-4">
            <label class="col-form-label form-label">Building</label>
            <div class="input-group">
              <input type="text" class="form-control" name="guarantor_info.building">
            </div>
          </div>
          <div class="col-12 col-lg-6 mb-4">
            <label class="col-form-label form-label need-required">Street</label>
            <div class="input-group">
              <input type="text" class="form-control need-required" name="guarantor_info.street">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label need-required">Postal</label>
            <div class="input-group">
              <input type="text" class="form-control need-required" name="guarantor_info.postal">
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label need-required">Country</label>
            <div class="input-group">
              <?php
              listDropDownCountries('guarantor_info.country_id', 192, '', 1, 'need-required', $countryList); ?>
            </div>
          </div>
          <div class="col-12 col-lg-6 mb-4">
            <label class="col-form-label form-label">Address Label</label>
            <div class="input-group">
              <input type="text" class="form-control" name="guarantor_info.address_label">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
        <strong>Employment</strong>
      </button>
    </h2>
    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionGuarantor">
      <div class="accordion-body">
        <div class="row">
          <div class="col-12 col-lg-4 mb-4">
            <label class="col-form-label form-label need-required">Employment status</label>
            <div class="input-group">
              <?php $select = '';
              create_select_control($employment_status, 'guarantor_info.employment_status', $select, '', 'need-required'); ?>
            </div>
          </div>
          <div class="col-12 col-lg-4 mb-4">
            <label class="col-form-label form-label emp-need-required">Company Name</label>
            <div class="input-group">
              <input type="text" class="form-control emp-need-required" name="guarantor_info.company_name">
            </div>
          </div>
          <div class="col-12 col-lg-4 mb-4">
            <label id="guarantor_info.job_type_label" class="col-form-label form-label need-required">Industry</label>
            <div class="input-group">
              <?php $select = '';
              create_select_control($jobOptions, 'guarantor_info.job_type_id', $select, '', 'need-required'); ?>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-lg-12 mb-4">
            <label class="col-form-label form-label emp-need-required">Address</label>
            <div class="input-group">
              <input type="text" class="form-control emp-need-required" name="guarantor_info.company_address">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label emp-need-required">Office No.</label>
            <div class="input-group">
              <span class="input-group-text">+65</span>
              <input type="text" class="form-control emp-need-required phone-number" name="guarantor_info.company_telephone">
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label emp-need-required">Postal Code</label>
            <div class="input-group">
              <input type="text" class="form-control emp-need-required" name="guarantor_info.company_postal_code">
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label emp-need-required">Position</label>
            <div class="input-group">
              <?php $select = '';
              create_select_control($positions, 'guarantor_info.position', $select, '', 'emp-need-required'); ?>
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label emp-need-required">Occupation</label>
            <div class="input-group">
              <input type="text" class="form-control emp-need-required" name="guarantor_info.occupation">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label need-required">Annual Gross Income</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="text" class="form-control annual_income income need-required" name="guarantor_info.annual_income" value="">
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label need-required">Gross Monthly Income</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="text" class="form-control monthly_income income need-required" name="guarantor_info.monthly_income_1" value="">
            </div>
            <div class="text-muted"><small>1st month</small></div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label opacity-0">2nd Month</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="text" class="form-control monthly_income income need-required" name="guarantor_info.monthly_income_2" value="">
            </div>
            <div class="text-muted"><small>2nd month</small></div>
          </div>
          <div class="col-12 col-lg-3 mb-4">
            <label class="col-form-label form-label opacity-0">3rd Month</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="text" class="form-control monthly_income income need-required" name="guarantor_info.monthly_income_3" value="">
            </div>
            <div class="text-muted"><small>3rd month</small></div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-lg-6 mb-4">
            <label class="col-form-label form-label">Average Monthly Income</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="text" class="form-control income ave_monthly_income" name="guarantor_info.month_income_avg" disabled value="">
            </div>
          </div>
          <div class="col-12 col-lg-6 mb-4">
            <label class="col-form-label form-label">Past 6 Month Gross Income</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="text" class="form-control income six_month_income" name="guarantor_info.six_month_income" disabled>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-lg-6 mb-4">
            <label class="col-form-label form-label">Income Document</label>
            <div class="px-3 income_document">
              <div class="row">
                <?php
                $checked = '';
                foreach ($docOptions as $value => $text) {
                ?>
                  <div class="form-check col-6 col-sm-3 col-lg-3 mb-3">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input form-control" <?php print $checked; ?> type="checkbox" id="<?php print $text; ?>" value="<?php print $value; ?>" name="guarantor_info.income_document">
                      <label class="form-check-label w-auto" for="<?php print $text; ?>"><?php print $text; ?></label>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-justify-content-end">
          <div class="col-12 col-sm-8 col-lg-6 mb-4">
            <div class="quick-file-uploader gap-3 image-singpass d-flex px-3 py-2 border border-dashed border-primary rounded mt-3 justify-content-between bg-light align-items-start">
              <div class="mt-2 chooseFiles">
                <img class="w-24" src="<?php print PLUGIN_DIR_URL . "assets/images/file-arrow-up.svg"; ?>" />
              </div>
              <div>
                <div class="chooseFiles">
                  <div><small><strong>Quick File Uploader</strong></small></div>
                  <div><small class="text-muted"> Please select at least 1 file from the computer (file upload maximum 200MB and only upload files in PDF format).</small></div>
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
                <input type="hidden" name="guarantor_info.income_document_files" id="upload_files" value="" class="form-control document_files">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
        <strong>Bank Information</strong>
      </button>
    </h2>
    <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionGuarantor">
      <div class="accordion-body">
        <div class="row">
          <div class="col-12 col-lg-4 mb-4">
            <label class="col-form-label form-label">Bank Name</label>
            <div class="input-group">
              <input type="text" class="form-control" name="guarantor_info.bank_name">
            </div>
          </div>
          <div class="col-12 col-lg-4 mb-4">
            <label class="col-form-label form-label">Bank Acc</label>
            <div class="input-group">
              <input type="text" class="form-control" name="guarantor_info.bank_acc">
            </div>
          </div>          
          <div class="col-12 col-lg-4 mb-4">
            <label class="col-form-label form-label">Date Of Salary</label>
            <div class="input-group">
              <?php create_select_control($dayOfMonth, 'guarantor_info.salary_date', ''); ?>
            </div>
          </div>
          <div class="col-12 col-lg-3 mb-4 d-none">
            <label class="col-form-label form-label">Bank Code</label>
            <div class="input-group">
              <input type="text" class="form-control" name="guarantor_info.bank_code">
            </div>
          </div>
        </div>
        <div class="row d-none">
          <div class="col-12 col-lg-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="mlcb" checked disabled name="guarantor_info.allow_mlcb">
              <label class="form-check-label" for="mlcb">
                Opt-in consent to disclose information to MLCB
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-lg-12">
    <div class="mb-4 row">
      <div class="col col-lg-12">
        <div class="d-flex-end gap-3 mb-form-submit">
          <button class="btn btn-danger px-4 btnCancel">Cancel</button>
          <!-- <button class="btn btn-secondary px-4" id="btnDraftStep6">Save Draft</button> -->
          <button class="btn btn-primary px-4" id="btnContinueStep6">Continue</button>
        </div>
      </div>
    </div>
  </div>
</div>