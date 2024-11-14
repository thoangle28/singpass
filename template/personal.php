<?php
$_SESSION['mlcbCheckedOk'] = false;
$singPassChecked = $_SESSION['singPassChecked'];
//var_dump( @$singPassChecked);
$personal = [];
$countryList = mc_countries();
$fullname = @$singPassChecked['personal']->name->value;
$fullname = ucwords(strtolower(trim($fullname)));

$fisrtname = '';
$lastname = '';
if ($fullname) {
  $fullname = explode(' ', $fullname);
  $lastname = $fullname[0];
  unset($fullname[0]);
  $fisrtname = implode(' ', $fullname);
}

$cpf = [];
$temp = isset($singPassChecked['personal']->cpfcontributions) ? $singPassChecked['personal']->cpfcontributions : [];

if (isset($temp->history) && $temp->history) {
  foreach ($temp->history as $history) {
    $cpf['date'][] = $history->date->value;
    $cpf['employer'][] = $history->employer->value;
    $cpf['amount'][] = $history->amount->value;
    $cpf['month'][] = $history->month->value;
  }
}

?>
<div class="mb-4 row">
  <div class="col-12 col-md-4 col-lg-2 d-none d-md-flex d-flex-end align-items-end"><label class="col-form-label">Borrower photo</div>
  <div class="col-12 col-md-8 col-lg-10">
    <div class="avatarImage">
      <div class="img-circle position-relative rounded-circle">
        <div class="d-flex align-items-center justify-content-center w-100 h-100">
          <?php
          $avatar =  PLUGIN_DIR_URL . "assets/images/no_image.png";
          if (isset($personal['filePhotoValue']) && $personal['filePhotoValue']) {
            $avatar = $personal['filePhotoValue'];
          } ?>
          <img id="imagePreview" class="rounded-circle" src="<?php print $avatar; ?>" />
        </div>
        <div class="camera position-absolute">
          <img class="upload-image" src="<?php print PLUGIN_DIR_URL . "assets/images/camera.png"; ?>" />
        </div>
      </div>
      <div id="removeAvater" class="d-none img-circle text-danger d-flex align-items-center gap-1 h-100 cursor-pointer mt-2 bg-transparent">
        <img class="check-red" style="height: 18px; width: auto;" src="<?php print PLUGIN_DIR_URL . "assets/images/circle-xmark-regular.svg"; ?>" /><small>Remove</small>
      </div>
      <input type="file" name="filePhoto" id="filePhoto" class="d-none">
      <input type="hidden" name="filePhotoValue" id="filePhotoValue" class="d-none"
        value="<?php print isset($personal['filePhotoValue']) ? $personal['filePhotoValue'] : ''; ?>">
      <div id="errorMessage" class="text-danger d-none"><small>Borrower photo is required</small></div>
      <input class="form-control" type="hidden" name="is_existing" value="<?php print $is_existing; ?>">
      <div class="form-check mt-3 d-none">
        <input class="form-check-input" type="radio" name="customer_type" id="customer_type" checked>
        <label class="form-check-label" for="customer_type">
          <?php print ucfirst($is_existing); ?>
        </label>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 col-md-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label required">NRIC No./FIN</label></div>
      <div class="col-12 col-sm-8 col-md-8 col-lg-8">
        <div class="input-group">
          <?php $nric_no = (isset($singPassChecked['personal']->uinfin)) ? $singPassChecked['personal']->uinfin->value : $singPassChecked['NRIC_No_FIN']; ?>
          <input type="text" class="form-control" disabled name="identification_no" value="<?php print $nric_no; ?>">
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-md-4 d-flex-end "><label class="col-form-label required">Confirm NRIC</label></div>
      <div class="col-12 col-sm-8 col-md-8 col-lg-8">
        <div class="input-group">
          <input type="text" class="form-control" name="identification_no_comfirm" required value="<?php print $nric_no; ?>">
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-md-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-md-4 d-flex-end "><label class="col-form-label required">ID Type</label></div>
      <div class="col-12 col-sm-8 col-md-8 col-lg-8">
        <div class="input-group">
          <?php
          $fisrtLetter = substr($nric_no, 0, 1);
          $id_type = (strtolower($fisrtLetter) !== 's') ? 'foreign_identification_number' : 'singapore_nric_no';
          create_select_control($identification_type, 'identification_type', $id_type, 'required'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-12 col-lg-6">
    <div class="mb-4 row">
      <?php
      $disabled = ($id_type == 'singapore_nric_no' ) ? 'disabled' : '';
      $required = ($disabled != '') ? '' : ' required';
      ?>
      <div class="col-12 col-sm-4 col-md-4 d-flex-end "><label class="col-form-label <?php print $required; ?>" id="lbl_expiry_date">Expiry Date</label></div>
      <div class="col-12 col-sm-8 col-md-8 col-lg-8">
        <div class="input-group">
          <input type="date" data-format="mm/dd/yyyy" <?php print $disabled; ?> <?php print $required; ?> id="identification_expiry"
            value="<?php print isset($personal['identification_expiry']) ? $personal['identification_expiry'] : ''; ?>"
            class="form-control" placeholder="mm/dd/yyyy" name="identification_expiry">
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-md-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-md-4 d-flex-end "><label class="col-form-label required">First Name</label></div>
      <div class="col-12 col-sm-8 col-md-8 col-lg-8">
        <div class="input-group">
          <input type="text" class="form-control" required name="firstname" value="<?php print $fisrtname; ?>">
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-md-4 d-flex-end "><label class="col-form-label required">Last Name</label></div>
      <div class="col-12 col-sm-8 col-md-8 col-lg-8">
        <div class="input-group">
          <input type="text" class="form-control" required name="lastname" value="<?php print $lastname; ?>">
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-md-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-md-4 d-flex-end "><label class="col-form-label required">Source</label></div>
      <div class="col-12 col-sm-8 col-md-8 col-lg-8">
        <div class="input-group">
          <?php
          $soureOptions = source();
          
          $selected = isset($personal['marketing_type_id']) ? $personal['marketing_type_id'] : '';
          create_select_control($soureOptions, 'marketing_type_id', $selected, 'required'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-md-4 d-flex-end "><label class="col-form-label required">Gender</label></div>
      <div class="col-12 col-sm-8 col-md-8 col-lg-8">
        <div class="input-group">
          <?php
          $selected = isset($personal['gender']) ? $personal['gender'] : '';
          create_select_control($gender, 'gender', $selected, 'required'); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-md-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-md-4 d-flex-end "><label class="col-form-label required">Date of Birth</label></div>
      <div class="col-12 col-sm-8 col-md-8 col-lg-8">
        <div class="input-group">
          <input type="date" data-format="mm/dd/yyyy" class="form-control" min="1900-01-01"
            placeholder="mm/dd/yyyy" required name="date_of_birth" value="<?php print @$singPassChecked['personal']->dob->value; ?>">
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-md-4 d-flex-end "><label class="col-form-label required">Nationality</label></div>
      <div class="col-12 col-sm-8 col-md-8 col-lg-8">
        <div class="input-group">
          <?php
          $selected = (isset($singPassChecked['personal']->nationality)) ? $singPassChecked['personal']->nationality->code : 192;
          listDropDownCountries('nationality', @$singPassChecked['personal']->nationality->code, 'required', 0, '', $countryList); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-md-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-md-4 d-flex-end "><label class="col-form-label required">Language Spoken</label></div>
      <div class="col-12 col-sm-8 col-md-8 col-lg-8">
        <div class="input-group">
          <?php
          $selected = isset($personal['spoken_language']) ? $personal['spoken_language'] : '';
          create_select_control($spoken_language, 'spoken_language', $selected, 'required'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-12 col-lg-6">
    <div class="mb-4 row">
      <div class="col-12 col-sm-4 col-md-4 d-flex-end "><label class="col-form-label required">Marital Status</label></div>
      <div class="col-12 col-sm-8 col-md-8 col-lg-8">
        <div class="input-group">
          <?php
          $selected = isset($personal['marital_status']) ? $personal['marital_status'] : '';
          create_select_control($marital_status, 'marital_status', $selected, 'required'); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
    <div class="mb-4 row align-items-center">
      <div class="col-12 col-sm-4 col-md-2 col-lg-2 d-flex-end">&nbsp;</div>
      <div class="col-12 col-sm-8 col-md-10 col-lg-6">
        <div class="input-group">
          <div class="w-100"><label class="col-form-label">Do you currently have any legal actions pending against you?</label></div>
          <?php $check_yes = isset($personal['legal_actions_against']) ? $personal['legal_actions_against'] : 0; ?>
          <div class="d-flex gap-5">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="legal_actions_against" id="legalActionsYes" value="1">
              <label class="form-check-label" for="legalActionsYes">
                Yes
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="legal_actions_against" id="legalActionsNo" value="0" checked>
              <label class="form-check-label" for="legalActionsNo">
                No
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="row">
          <div class="col-12 d-md-block d-lg-none pt-5"></div>
          <div class="col-12 col-lg-12">
            <div class="d-flex-end gap-3 mb-form-submit">
              <input type="hidden" value='<?php print json_encode($cpf); ?>' name="cpf" id="cpfcontributions">
              <?php //require_once('cpf.php'); 
              ?>
              <button class="btn btn-danger px-4 btnCancel">Cancel</button>
              <button class="btn btn-primary px-4" id="btnContinueStep1">Continue</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>