<?php

$_SESSION['emailVerification'] = [];
$_SESSION['phoneVerification'] = [];

$contact = [];

$address = new stdClass();
$address->init = true;
$home_address = [];
$home_address[] = $address;

$work_address = [];

?>
<div class="contact-info">
  <div class="row">
    <div class="col-12 col-md-12 col-lg-6">
      <div class="mb-4 row">
        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label required">Phone Number 1</label></div>
        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
          <div class="input-group">
            <span class="input-group-text">+65</span>
            <input type="text" class="form-control phone-number" name="mobilephone_1" id="mobilephone_1" required value="<?php print @$singPassChecked['personal']->mobileno->nbr->value; ?>">
            <span class="input-group-text" id="confirmedPhone">
              <img id="chkPhoneIcon" class="check-image d-none" src="<?php print PLUGIN_DIR_URL . "assets/images/check.svg"; ?>" />
              <img id="chkPhoneError" class="check-image check-red d-block" src="<?php print PLUGIN_DIR_URL . "assets/images/circle-error.svg"; ?>" />
            </span>
            <span class="input-validation mx-2 d-flex" id="phoneCheck">
              <img id="phoneSubmit" title="Click here to check your phone" class="check-image" src="<?php print PLUGIN_DIR_URL . "assets/images/submit.svg"; ?>" />
              <div id="phoneLoading" class="lds-ellipsis d-none">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
              </div>
            </span>
            <input type="hidden" value="" class="form-control" name="phone_checked" id="phone_checked">
            <div id="phoneVerifycation" class="box d-none popup-box">
              <div class="card box-shadow popup-body">
                <div class="card-body p-4">
                  <h5 class="mb-3">
                    Validate OTP (One Time Passcode)
                    <span id="btnClose" class="float-end p-2 py-1 bg-danger text-white">X</span>
                  </h5>
                  <hr />
                  <div class="text-content my-3">
                    A One Time Passcode has been sent to <span id="pastePhone"></span>.
                    Please check your phone and enter the OTP in the field below to verify your phone number.
                    You have <span class="text-danger" id="countTimePhone">300</span> seconds left to enter the OTP and verify.
                  </div>
                  <div class="form">
                    <div class="input-group mb-3 gap-3">
                      <div>
                        <input type="text" class="form-control" value="" id="phoneVerifyCode" name="phoneVerifyCode">
                        <small class="text-danger d-none" id="phoneVerifyError"></small>
                      </div>
                      <div><button type="button" class="btn btn-custom" id="btnValidatePhone">Validate OTP</button></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-12 col-lg-6">
      <div class="mb-4 row">
        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label">Phone Number 2</label></div>
        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
          <div class="input-group">
            <span class="input-group-text">+65</span>
            <input type="text" class="form-control phone-number" name="mobilephone_2" value="<?php print isset($contact->mobilephone_2) ? $contact->mobilephone_2 : ''; ?>">
            <!--  <span class="input-validation mx-2 d-flex">
            <img class="check-image" src="<?php print PLUGIN_DIR_URL . "assets/images/check.svg"; ?>" />
          </span> -->
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-md-12 col-lg-6">
      <div class="mb-4 row">
        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label required">Email</label></div>
        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
          <div class="input-group alternate-email-group">
            <input type="email" class="form-control" id="emailNeedCheck" name="email_1" required value="<?php print @$singPassChecked['personal']->email->value; ?>">
           <!--  <span class="input-group-text" id="confirmedEmail">
              <img id="chkEmailIcon" class="check-image d-none" src="<?php print PLUGIN_DIR_URL . "assets/images/check.svg"; ?>" />
              <img id="chkEmailError" class="check-image check-red d-block" src="<?php print PLUGIN_DIR_URL . "assets/images/circle-error.svg"; ?>" />
            </span>
            <span class="input-validation mx-2 d-flex" id="emailCheck">
              <img id="emailSubmit" title="Click here to check your email" class="check-image" src="<?php print PLUGIN_DIR_URL . "assets/images/submit.svg"; ?>" />
              <div id="emailLoading" class="lds-ellipsis d-none">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
              </div>
            </span> -->
            <input type="hidden" class="form-control"  value="" name="email_checked" id="email_checked">
            <div id="emailVerifycation" class="box d-none popup-box">
              <div class="card box-shadow popup-body">
                <div class="card-body p-4">
                  <h5 class="mb-3">
                    Validate OTP (One Time Passcode)
                    <span id="btnClose" class="float-end p-2 py-1 bg-danger text-white">X</span>
                  </h5>
                  <hr />
                  <div class="text-content my-3">
                    A One Time Passcode has been sent to <span id="pasteEmail"></span>.
                    Please check your email and enter the OTP below to verify your email address.
                    If you can not see the email in your inbox, make sure to check your SPAM folder.
                    You have <span class="text-danger" id="countTimeEmail">60</span> seconds left to enter the OTP and verify.
                  </div>
                  <div class="form">
                    <div class="input-group mb-3 gap-3">
                      <div>
                        <input type="text" class="form-control" value="" name="emailVerifyCode" id="emailVerifyCode">
                        <small class="text-danger d-none" id="emailVerifyError"></small>
                      </div>
                      <div><button type="button" class="btn btn-custom" id="btnValidateEmail">Validate OTP</button></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-12 col-lg-6">
      <div class="mb-4 row">
        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label">Home</label></div>
        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
          <div class="input-group">
            <span class="input-group-text">+65</span>
            <input type="text" class="form-control phone-number" name="homephone" value="<?php print isset($contact->homephone) ? $contact->homephone : ''; ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12 col-md-12">
      <div class="alert alert-warning small">
      To ensure seamless communication, kindly double-check that the contact information you’ve provided is accurate. This will be our primary means of reaching you for all future correspondence. Thank you for your attention to this matter!
      </div>
    </div>
  </div>
  <div class="row d-none">   
    <div class="col-12 col-md-12 col-lg-6">
      <div class="mb-4 row">
        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label">Alternate Email</label></div>
        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
          <div class="input-group alternate-email-group">
            <input type="email" class="form-control" name="email_2" value="<?php print isset($contact->email_2) ? $contact->email_2 : ''; ?>">
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-12 col-lg-6">
      <div class="mb-4 row d-none">
        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label">Office</label></div>
        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
          <div class="input-group">
            <span class="input-group-text">+65</span>
            <input type="text" class="form-control phone-number" name="officephone" value="">
          </div>
        </div>
      </div>
    </div>
  </div>
  <hr class="mb-2" />
  <div class="row">
    <div class="col-12">
      <h5>2.1 Relatives' Information</h5>
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-md-12 col-lg-6">
      <div class="mb-4 row">
        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-labe required">Next of Kin Name</label></div>
        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
          <div class="input-group">
            <input type="text" class="form-control" name="next_of_kin_name" value="" required>           
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-12 col-lg-6">
      <div class="mb-4 row">
        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label required">Type</label></div>
        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
          <div class="input-group">
            <select name="next_of_kin_type" class="form-control form-select" required>
              <option></option>
              <option value="spouse">Spouse</option>
              <option value="parents">Parents</option>
              <option value="siblings">Siblings</option>
              <option value="friend">Friend</option>
              <option value="others">Others</option>
            </select>
          </div>
        </div>
      </div>
    </div>    
  </div>
  <div class="row">        
    <div class="col-12 col-md-12 col-lg-6">
      <div class="mb-4 row">
        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label required">Phone</label></div>
        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
          <div class="input-group">
            <span class="input-group-text">+65</span>
            <input type="text" class="form-control phone-number" name="next_of_kin_phone" value="" required>           
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<hr />
<?php

$address_type = address_type();

if (isset($address_type->error) && !$address_type->error 
  && isset($address_type->data) && is_array($address_type->data)) {
  $address_list = $address_type->data;
?>
  <div class="mc-accordion accordion my-3 home-address" id="mc-accordion">
    <?php foreach ($address_list as $pid => $type) {
      if (strtolower($type->address_type_name) === 'home') {
    ?>
        <h5 class="mb-3">2.<?php print($pid + 2); ?> <?php print $type->address_type_name; ?> Address</h5>
        <div class="accordion-items">
          <input type="text" value="<?php print  $type->id; ?>" name="home_address_type_id" id="home_address_type_id" class="form-control d-none">
          <?php foreach ($home_address as $id => $address) { ?>
            <div class="accordion-item" data-order="row-item-<?php print $id;?>">
              <h2 class="accordion-header" id="heading<?php print $id; ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php print $id; ?>" aria-expanded="false" aria-controls="collapse<?php print $id; ?>">
                  <strong>Home Address <?php print($id + 1); ?></strong>
                </button>
                <?php if( $id > 0 ) { ?> <span class="remove" title="Click to remove">X</span> <?php } ?>
              </h2>
              <div id="collapse<?php print $id; ?>" class="accordion-collapse collapse <?php print $id == 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php print $id; ?>" data-bs-parent="#mc-accordion">
                <div class="accordion-body">
                  <div class="row">
                    <div class="col-12 col-md-12 col-lg-6">
                      <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label">Property Type</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                          <div class="input-group">
                            <?php
                            $args = ['HDB' => 'HDB', 'Private Residential' => 'Private Residential'];
                            $hdbtype = (isset($singPassChecked['personal']->housingtype)) ? @$singPassChecked['personal']->housingtype->code : 0;
                            $hdbtype1 = (isset($singPassChecked['personal']->hdbtype)) ? @$singPassChecked['personal']->hdbtype->code : 0;
                            $hdbtype = (!$hdbtype) ? $hdbtype1 : $hdbtype;
                            $propertyType = ($hdbtype > 120 ) ? 'Private Residential' : 'HDB';
                            ?>
                            <?php create_select_control($args, 'property_type', $propertyType); ?>                            
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">
                      <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label required">Housing Type</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                          <div class="input-group" data-default="<?php print $hdbtype;?>" data-property="<?php print $propertyType;?>">
                            <?php
                            print housingType('housing_type', $hdbtype, '', 'required');
                            ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12 col-md-12 col-lg-6">
                      <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end"><label class="col-form-label">Existing Staying</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                          <div class="input-group">
                            <?php $args = [1 => 'Yes', 0 => 'No'];
                            $select = 1; ?>
                            <?php create_select_control($args, 'existing_staying', $select); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">
                      <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end"><label class="col-form-label required">Residential Status</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                          <div class="input-group">
                            <?php $args = [
                              'Self-Owned' => 'Self-Owned', 'Rental' => 'Rental', 'Loan / Mortgaged' => 'Loan / Mortgaged',
                              'Living with parents' => 'Living with parents', 'Living with employer' => 'Living with employer'
                            ];
                            $select = isset($address->home_ownership) ? $address->home_ownership : '';
                            ?>
                            <?php create_select_control($args, 'home_ownership', $select, 'required'); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12 col-lg-4">
                      <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-6 d-flex-end "><label class="col-form-label">Unit</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-6">
                          <div class="input-group">
                            <?php 
                              $homeAdd = @$singPassChecked['personal']->regadd;
                              $selected = @$homeAdd->unit->value;
                              $floor = @$homeAdd->floor->value;                             
                              $selected = ($floor &&  $selected) ? ('#'.$floor . '-'.  $selected) : $selected;
                            ?>
                            <input type="text" class="form-control" name="unit" value="<?php print $selected; ?>">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12 col-lg-3">
                      <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label required">Block</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                          <div class="input-group">
                            <?php $selected = (!(isset($address->block))) ? @$singPassChecked['personal']->regadd->block->value : $address->block; ?>
                            <input type="text" class="form-control" name="block" required value="<?php print $selected; ?>">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12 col-lg-5">
                      <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label">Building</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                          <div class="input-group">
                            <?php $selected = (!isset($address->building)) ? @$singPassChecked['personal']->regadd->building->value : $address->building; ?>
                            <input type="text" class="form-control" name="building" value="<?php print ucwords(strtolower($selected)); ?>">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-12">
                      <div class="mb-4 row align-items-center">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-2 d-flex-end"><label class="col-form-label required">Street</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-10">
                          <div class="input-group">
                            <?php $selected = (!isset($address->street)) ? @$singPassChecked['personal']->regadd->street->value : $address->street; ?>
                            <input type="text" class="form-control" name="street" required value="<?php print ucwords(strtolower($selected)); ?>">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-xl-6 col-xxl-4">
                      <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-6 d-flex-end "><label class="col-form-label required">Postal</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 col-xxl-6">
                          <div class="input-group">
                            <?php $selected = (!isset($address->postal)) ? @$singPassChecked['personal']->regadd->postal->value : $address->postal; ?>
                            <input type="text" class="form-control" name="postal" required value="<?php print $selected; ?>">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6 col-xl-6 col-xxl-3">
                      <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4 d-flex-end "><label class="col-form-label required">Country</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 col-xxl-8">
                          <div class="input-group">
                            <?php $selected = (!isset($address->country)) ? @$singPassChecked['personal']->regadd->country->code : (int)$address->country; ?>
                            <?php listDropDownCountries('country', $selected, 'required', 1, '', $countryList); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-12 col-xl-12 col-xxl-5">
                      <div class="mb-4 row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-2 col-xl-2 col-xxl-4 d-flex-end "><label class="col-form-label">Address Label</label></div>
                        <div class="col-12 col-sm-8 col-md-8 col-lg-10 col-xl-10 col-xxl-8">
                          <div class="input-group">
                            <?php $selected = ''; ?>
                            <input type="text" class="form-control" name="address_label" value="<?php print $selected; ?>">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
        <!-- // end home -->
        <div class="row mt-3">
          <div class="col-12 col-md-12 col-lg-6">
          </div>
          <div class="col-12 col-md-12 col-lg-6">
            <div class="mb-4 row">
              <div class="col-12 col-lg-12">
                <div class="d-flex-end gap-3">
                  <input type="hidden" value="<?php print  $type->id; ?>" name="home_address_type_id" id="home_address_type_id">
                  <button id="addMoreHomeAddress" class="btn btn-info px-4 text-white" style="width: 218px;">+ Add Another Address</button>
                </div>
              </div>
            </div>
          </div>
        </div>
  </div>
<?php } else { ?>
  <!-- Work Address -->
  <hr />
  <div class="mc-accordion accordion my-3 work-address" id="mc-work-address">
    <h5 class="mb-3">2.<?php print($pid + 2); ?> <?php print $type->address_type_name; ?> Address</h5>
    <div class="accordion-items">
      <input type="text" value="<?php print  $type->id; ?>" name="work_address_type_id" class="form-control d-none" id="work_address_type_id">
      <?php foreach ($work_address as $id => $address) { ?>
        <div class="accordion-item" data-order="row-item-<?php print $id;?>">
          <h2 class="accordion-header" id="headingWork<?php print($id + 1); ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#work<?php print($id + 1); ?>" aria-expanded="false" aria-controls="work<?php print($id + 1); ?>">
              <strong>Work Address <?php print($id + 1); ?></strong>
            </button>
            <span class="remove" title="Click to remove">X</span>
          </h2>
          <div id="work<?php print($id + 1); ?>" class="accordion-collapse collapse" aria-labelledby="headingWork<?php print($id + 1); ?>" data-bs-parent="#mc-work-address">
            <div class="accordion-body">
              <div class="row">
                <div class="col-12 col-lg-6">
                  <div class="mb-4 row">
                    <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label">Default work</label></div>
                    <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                      <div class="input-group">
                        <div class="form-check form-switch">
                          <?php $checked = (isset($address->is_default)) ? (int)$address->is_default == 1 : ''; ?>
                          <input class="form-check-input form-control" type="checkbox" id="defaultWork" <?php print $checked ? 'checked' : ''; ?> name="is_default" value="">
                          <label class="form-check-label" for="defaultWork">Yes</label>
                          <input type="hidden" value="<?php print  $type->id; ?>" name="address_type_id" class="form-control">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12 col-lg-4">
                  <div class="mb-4 row">
                    <div class="col-12 col-sm-4 col-md-4 col-lg-6 d-flex-end "><label class="col-form-label">Unit</label></div>
                    <div class="col-12 col-sm-8 col-md-8 col-lg-6">
                      <div class="input-group">
                        <input type="text" class="form-control" name="unit" value="<?php print (isset($address->unit)) ? $address->unit : ''; ?>">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-lg-3">
                  <div class="mb-4 row">
                    <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label required">Block</label></div>
                    <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                      <div class="input-group">
                        <input type="text" class="form-control" name="block" required value="<?php print (isset($address->block)) ? $address->block : ''; ?>">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-lg-5">
                  <div class="mb-4 row">
                    <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label">Building</label></div>
                    <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                      <div class="input-group">
                        <input type="text" class="form-control" name="building" value="">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12">
                  <div class="mb-4 row align-items-center">
                    <div class="col-12 col-sm-4 col-md-4 col-lg-2 d-flex-end"><label class="col-form-label required">Street</label></div>
                    <div class="col-12 col-sm-8 col-md-8 col-lg-10">
                      <div class="input-group">
                        <input type="text" class="form-control" name="street" required value="<?php print (isset($address->unit)) ? $address->unit : ''; ?>">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-4">
                  <div class="mb-4 row">
                    <div class="col-12 col-lg-6 d-flex-end "><label class="col-form-label required">Postal</label></div>
                    <div class="col-12 col-lg-6">
                      <div class="input-group">
                        <input type="text" class="form-control" name="postal" required value="<?php print (isset($address->unit)) ? $address->unit : ''; ?>">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3">
                  <div class="mb-4 row">
                    <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label required">Country</label></div>
                    <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                      <div class="input-group">
                        <?php $selected = (!isset($address->country)) ? @$singPassChecked['personal']->regadd->country->id : (int)$address->country; ?>
                        <?php listDropDownCountries('country', $selected, 'required', 1, '', $countryList); ?>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-5">
                  <div class="mb-4 row">
                    <div class="col-12 col-sm-4 col-md-4 col-lg-4 d-flex-end "><label class="col-form-label">Address Label</label></div>
                    <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                      <div class="input-group">
                        <?php $args = ['Office' => 'Office', 'Work Site' => 'Work Site'];
                        $select = isset($address->address_label) ? $address->address_label : '';
                        ?>
                        <?php create_select_control($args, 'address_label', $select); ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
    <!-- // end Work -->
    <div class="row mt-3">
      <div class="col-12 col-md-12 col-lg-6">
      </div>
      <div class="col-12 col-md-12 col-lg-6">
        <div class="mb-4 row">
          <div class="col-12 col-lg-12">
            <div class="d-flex-end gap-3">
              <input type="hidden" value="<?php print  $type->id; ?>" name="other_address_type_id" id="other_address_type_id">
              <button class="btn btn-info px-4 text-white" id="addMoreWorkAddress" style="width: 218px;">+ Add Another Address</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php }  // if
    } //for
  } // if 
?>
<!--//end work -->
<div class="row mt-3">
  <div class="col-12 col-md-12 col-lg-6">
  </div>
  <div class="col-12 col-md-12 col-lg-6">
    <div class="d-flex-end gap-3 mb-form-submit">
      <button class="btn btn-danger px-4 btnCancel">Cancel</button>
      <!-- <button class="btn btn-secondary px-4" id="btnDraftStep2">Save Draft</button> -->
      <button class="btn btn-primary px-4" id="btnContinueStep2">Continue</button>
    </div>
  </div>
</div>