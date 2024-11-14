<?php

//$app = $_SESSION['steps'];

$personal = [
  //['label' => 'Customer Type', 'value' => '', 'key' => 'is_existing'],
  ['label' => 'ID Type', 'value' => '', 'key' => 'identification_type'],
  ['label' => 'Expiry Date', 'value' => '', 'key' => 'identification_expiry'],
  ['label' => 'Gender', 'value' => '', 'key' => 'gender'],
  ['label' => 'Nationality', 'value' => '', 'key' => 'nationality'],
  ['label' => 'NRIC No./FIN', 'value' => '', 'key' => 'identification_no'],
  ['label' => 'Name of Applicant', 'value' => '', 'key' => 'name_of_applicant'], //first + last
  ['label' => 'Date of Birth', 'value' => '', 'key' => 'date_of_birth'],
  ['label' => 'Language Spoken', 'value' => '', 'key' => 'spoken_language'],
  ['label' => 'Source', 'value' => '', 'key' => 'marketing_type_id'],
  ['label' => 'Marital Status', 'value' => '', 'key' => 'marital_status'],
  ['label' => 'Do you currently have any legal actions pending against you?', 'value' => '', 'key' => 'legal_actions_against'],
];
?>
<div class="personal-information border border-secondary ">
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="bg-secondary text-white px-3 py-2 fw-bold">Personal Information</div>
    </div>
  </div>
  <div class="p-3">
    <?php display_coloumns($personal, 5, 20); ?>
  </div>
</div>

<!-- Contact Information-->
<?php
$contact = [
  ['label' => 'Phone Number 1', 'value' => '', 'key' => 'mobilephone_1'],
  ['label' => 'Phone Number 2', 'value' => '', 'key' => 'mobilephone_2'],
  ['label' => 'Home', 'value' => '', 'key' => 'homephone'],
  ['label' => 'Email', 'value' => '', 'key' => 'email_1'],
  //['label' => 'Alternate Email', 'value' => '', 'key' => 'email_2']
];
?>
<div class="contact-information border border-secondary contact-info">
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="bg-secondary text-white px-3 py-2 fw-bold">Contact Information</div>
    </div>
  </div>
  <div class="p-3">
    <?php display_coloumns($contact, 3, 4); ?>
  </div>
</div>
<!-- Relatives' Information-->
<?php
$contact = [  
  ['label' => 'Next of Kin Name', 'value' => '', 'key' => 'next_of_kin_name'],
  ['label' => 'Type', 'value' => '', 'key' => 'next_of_kin_type'],
  ['label' => 'Phone', 'value' => '', 'key' => 'next_of_kin_phone']
];
?>
<div class="contact-information border border-secondary contact-info">
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="bg-secondary text-white px-3 py-2 fw-bold">Relatives' Information</div>
    </div>
  </div>
  <div class="p-3">
    <?php display_coloumns($contact, 3, 4); ?>
  </div>
</div>
<!-- Home Addresses -->
<?php
$homeAddress = [
  ['label' => 'Property Type', 'value' => '', 'key' => 'property_type'],
  ['label' => 'Residential Status', 'value' => '', 'key' => 'home_ownership'],
  ['label' => 'Block', 'value' => '', 'key' => 'block'],
  ['label' => 'Postal ', 'value' => '', 'key' => 'postal'],
  ['label' => 'Housing Type', 'value' => '', 'key' => 'housing_type'],
  //['label' => 'Staying Condition', 'value' => '', 'key' => 'home_ownership'],
  ['label' => 'Building', 'value' => '', 'key' => 'building'],
  ['label' => 'Country  ', 'value' => '', 'key' => 'country'],
  ['label' => 'Existing Staying', 'value' => '', 'key' => 'existing_staying'],
  ['label' => 'Unit', 'value' => '', 'key' => 'unit'],
  ['label' => 'Street', 'value' => '', 'key' => 'street'],
  ['label' => 'Address Label', 'value' => '', 'key' => 'address_label'],
];
?>
<div class="contact-home-address border border-secondary contact-info">
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="bg-secondary text-white px-3 py-2 fw-bold">Home Addresses</div>
    </div>
  </div>
  <div class="p-3" id="homeAddress">
    <div class="home-item row-item row-item-0">
      <?php display_coloumns($homeAddress, 4, 3); ?>
    </div>
  </div>
</div>

<?php
$workAddress = [
  ['label' => 'Default Work', 'value' => '', 'key' => 'default_work'],
  ['label' => 'Block', 'value' => '', 'key' => 'block'],
  ['label' => 'Street', 'value' => '', 'key' => 'street'],
  ['label' => 'Country ', 'value' => '', 'key' => 'country'],
  ['label' => 'Unit', 'value' => '', 'key' => 'unit'],
  ['label' => 'Building', 'value' => '', 'key' => 'building'],
  ['label' => 'Postal', 'value' => '', 'key' => 'postal'],
  ['label' => 'Address Label', 'value' => '', 'key' => 'address_label'],
];
?>
<div class="contact-work-address border border-secondary contact-info d-none">
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="bg-secondary text-white px-3 py-2 fw-bold">Work Addresses</div>
    </div>
  </div>
  <div class="p-3" id="workAddress">
    <div class="work-item row-item row-item-0">
      <?php display_coloumns($workAddress, 4, 3); ?>
    </div>
  </div>
</div>
<!-- Employment -->
<?php
$employment = [
  ['label' => 'Employment Status', 'value' => '', 'key' => 'employment_status'],
  ['label' => 'Address', 'value' => '', 'key' => 'address'],
  [
    'label' => 'Gross Monthly Income', 'key' => 'gross_monthly_income',
    'value' => '<ul style="list-style-type: none; padding: 0;">
                <li>1st month: <span data-field="monthly_income_1">$0.00</span></li>
                <li>2nd month: <span data-field="monthly_income_2">$0.00</span></li>
                <li>3rd month: <span data-field="monthly_income_3">$0.00</span></li>
              </ul>'
  ],
  ['label' => 'Company Name', 'value' => '', 'key' => 'company_name'],
  ['label' => 'Position', 'value' => '', 'key' => 'position'],
  ['label' => 'Average Monthly Income', 'value' => '', 'key' => 'monthly_income'],
  ['label' => 'Office No.', 'value' => '', 'key' => 'company_telephone'],
  ['label' => 'Occupation  ', 'value' => '', 'key' => 'occupation'],
  ['label' => 'Past 6 Month Gross Income', 'value' => '', 'key' => 'six_months_income'],
  ['label' => 'Postal Code', 'value' => '', 'key' => 'portal_code'],
  ['label' => 'Industry', 'value' => '', 'key' => 'job_type_id'],
  ['label' => 'Annual Gross Income', 'value' => '', 'key' => 'annual_income'],
  ['label' => 'Yrs of Employment Period', 'value' => '', 'key' => 'yrs_of_employment_period'],
  ['label' => 'Have you been declared bankrupt in the past 5 years?', 'value' => '', 'key' => 'bankrupted'],
  ['label' => 'Do you have any plans to declare bankruptcy in the next 3 months?', 'value' => '', 'key' => 'bankrupt_plan'],
];
?>
<div class="employment-info border border-secondary ">
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="bg-secondary text-white px-3 py-2 fw-bold">Employment</div>
    </div>
  </div>
  <div class="p-3">
    <?php display_coloumns($employment, 3, 4); ?>
  </div>
</div>
<!-- Income Document -->
<?php
$IncomeDoc = [
  ['label' => 'PaySlip', 'value' => '', 'key' => 'payslip'],
  ['label' => 'CPF', 'value' => '', 'key' => 'cpf'],
  ['label' => 'NOA', 'value' => '', 'key' => 'noa'],
  ['label' => 'Others', 'value' => '', 'key' => 'others'],
  ['label' => 'Income Documents', 'value' => '', 'key' => 'income_documents']
];
?>
<div class="income-document border border-secondary ">
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="bg-secondary text-white px-3 py-2 fw-bold">Income Document</div>
    </div>
  </div>
  <div class="p-3">
    <?php display_coloumns($IncomeDoc, 4, 3); ?>
  </div>
</div>
<?php
$IncomeDoc = [
  ['label' => '', 'value' => '', 'key' => 'source_income']
];
?>
<div class="employment-info border border-secondary "> 
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="bg-secondary text-white px-3 py-2 fw-bold">Source Income</div>
    </div>
  </div>
  <div class="p-3">
    <?php display_coloumns($IncomeDoc, 1, 12); ?>
  </div>
</div>
<!-- Loan Details -->
<?php
$loanDetails = [
  ['label' => 'Loan Type', 'value' => '', 'key' => 'loan_type_id'],
  //['label' => 'No of Active Credit Loan', 'value' => '', 'key' => 'no_of_active_credit_loan'],
  ['label' => 'Term Unit', 'value' => '', 'key' => 'term_unit'],
  ['label' => 'Loan Amount Required', 'value' => '', 'key' => 'loan_amount_requested'],
  ['label' => 'Loan Terms', 'value' => '', 'key' => 'loan_terms'],
  ['label' => 'Installment', 'value' => '', 'key' => 'installment'],
  ['label' => 'Reason For Loan', 'value' => '', 'key' => 'loan_reason'],
  ['label' => 'Description', 'value' => '', 'key' => 'description'],
  ['label' => 'Loan Documents', 'value' => '', 'key' => 'loan_documents'],
];
?>
<div class="loan-details border border-secondary ">
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="bg-secondary text-white px-3 py-2 fw-bold">Loan Details</div>
    </div>
  </div>
  <div class="p-3">
    <?php display_coloumns($loanDetails, 4, 3); ?>
  </div>
</div>
<?php
$loanDetails = [
  ['label' => 'Is there any other individual or company (Beneficial Owner) that will benefit from the loan?', 'value' => '', 'key' => 'benefit'],
  ['label' => 'If yes', 'value' => '', 'key' => 'benefit_explain'],
  ['label' => 'Are you a politically-exposed person?', 'value' => '', 'key' => 'politically'],
  ['label' => 'If yes', 'value' => '', 'key' => 'politically_explain']  
];
?>
<div class="loan-details border border-secondary ">
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="bg-secondary text-white px-3 py-2 fw-bold">Borrower(s) & Surety(ies)</div>
    </div>
  </div>
  <div class="p-3">
    <div class="row">
      <div class="col-12 col-md-12 col-lg-5">
        <div class="mb-2">
          <h6 class="my-0 fs-12 text-muted px-2">Is there any other individual or company (Beneficial Owner) that will benefit from the loan?</h6>
          <div class="p-2 need-fill-data benefit" data-field="benefit"></div>
        </div>
      </div>
      <div class="col-12 col-md-12 col-lg-7">
        <div class="mb-2">
          <h6 class="my-0 fs-12 text-muted px-2">Explain</h6>
          <div class="p-2 need-fill-data benefit_explain" data-field="benefit_explain"></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-12 col-lg-5">
        <div class="mb-2">
          <h6 class="my-0 fs-12 text-muted px-2">Are you a politically-exposed person?</h6>
          <div class="p-2 need-fill-data politically" data-field="politically"></div>
        </div>
      </div>
      <div class="col-12 col-md-12 col-lg-7">
        <div class="mb-2">
          <h6 class="my-0 fs-12 text-muted px-2">Explain</h6>
          <div class="p-2 need-fill-data politically_explain" data-field="politically_explain"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bank Information -->
<?php
$bankInfo = [
  ['label' => 'Bank Name', 'value' => '', 'key' => 'bank_name_1'],
  ['label' => 'Bank Acc', 'value' => '', 'key' => 'account_number_1'],
  //['label' => 'Bank Code', 'value' => '', 'key' => 'bank_code_1'],
  ['label' => 'Date of Salary', 'value' => '', 'key' => 'date_of_salary']
];
?>
<div class="bank-information border border-secondary ">
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="bg-secondary text-white px-3 py-2 fw-bold">Bank Information</div>
    </div>
  </div>
  <div class="p-3">
    <?php display_coloumns($bankInfo, 3, 4); ?>
  </div>
</div>
<!-- check box -->
<div class="mlcb-information">
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="py-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" id="mlcbInfo" checked disabled name="mlcb">
          <label class="form-check-label" for="mlcbInfo">
            Opt-in consent to disclose information to MLCB
          </label>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Surety -->
<div id="surety-report" class="surty d-none">
  <h4 class="mb-2">Surety</h4>
  <div class="mb-3">Confidential</div>
  <!-- Information -->
  <?php
  $info = [
    ['label' => 'NRIC No.', 'value' => '', 'key' => 'guarantor_info.identification_no'],
    ['label' => 'ID Type', 'value' => '', 'key' => 'guarantor_info.identification_type'],
    ['label' => 'Full Name', 'value' => '', 'key' => 'guarantor_info.full_name'], //first name + last name
    ['label' => 'Gender', 'value' => '', 'key' => 'guarantor_info.gender'],
    ['label' => 'Date of Birth', 'value' => '', 'key' => 'guarantor_info.date_of_birth'],
    ['label' => 'Nationality', 'value' => '', 'key' => 'guarantor_info.nationality'],
    ['label' => 'Obligation', 'value' => '', 'key' => 'guarantor_info.obligation_code']
  ];
  ?>
  <div class="surty-information border border-secondary ">
    <div class="row">
      <div class="col-12 col-lg-12">
        <div class="bg-secondary text-white px-3 py-2 fw-bold">Surety Information</div>
      </div>
    </div>
    <div class="p-3">
      <?php display_coloumns($info, 4, 3); ?>
    </div>
  </div>
  <!-- Contact -->
  <?php
  $contact = [
    ['label' => 'Phone Number 1', 'value' => '', 'key' => 'guarantor_info.phone_1'],
    ['label' => 'Phone Number 2', 'value' => '', 'key' => 'guarantor_info.phone_2'],
    ['label' => 'Home', 'value' => '', 'key' => 'guarantor_info.phone_home'],
    ['label' => 'Email', 'value' => '', 'key' => 'guarantor_info.email'],
    //['label' => 'Alternate Email', 'value' => '', 'key' => 'guarantor_info.email_alternate']
  ];
  ?>
  <div class="surty-contact border border-secondary ">
    <div class="row">
      <div class="col-12 col-lg-12">
        <div class="bg-secondary text-white px-3 py-2 fw-bold">Contact Information</div>
      </div>
    </div>
    <div class="p-3">
      <?php display_coloumns($contact, 3, 4); ?>
    </div>
  </div>
  <!-- Home Address -->
  <?php
  $contact = [
    ['label' => 'Property Type', 'value' => '', 'key' => 'guarantor_info.property_type'],
    ['label' => 'Unit', 'value' => '', 'key' => 'guarantor_info.unit'],
    ['label' => 'Building', 'value' => '', 'key' => 'guarantor_info.building'],
    ['label' => 'Postal', 'value' => '', 'key' => 'guarantor_info.postal'],
    ['label' => 'Housing Type', 'value' => '', 'key' => 'guarantor_info.housing_type'],
    ['label' => 'Block', 'value' => '', 'key' => 'guarantor_info.block'],
    ['label' => 'Street', 'value' => '', 'key' => 'guarantor_info.street'],
    ['label' => 'Country', 'value' => '', 'key' => 'guarantor_info.country_id']
  ];
  ?>
  <div class="surty-home-address border border-secondary ">
    <div class="row">
      <div class="col-12 col-lg-12">
        <div class="bg-secondary text-white px-3 py-2 fw-bold">Home Address</div>
      </div>
    </div>
    <div class="p-3">
      <?php display_coloumns($contact, 4, 3); ?>
    </div>
  </div>
  <!-- Employement -->
  <?php
  $contact = [
    ['label' => 'Employment Status', 'value' => '', 'key' => 'guarantor_info.employment_status'],
    ['label' => 'Address', 'value' => '', 'key' => 'guarantor_info.company_address'],
    [
      'label' => 'Gross Monthly Income',
      'value' =>
      '<ul style="list-style-type: none; padding: 0;">
      <li>1st month: <span data-field="guarantor_info.monthly_income_1">$0.00</span></li>
      <li>2nd month: <span data-field="guarantor_info.monthly_income_2">$0.00</span></li>
      <li>3rd month: <span data-field="guarantor_info.monthly_income_3">$0.00</span></li>
    </ul>', 'key' => 'guarantor_info.gross_monthly_income'
    ],
    ['label' => 'Company Name', 'value' => '', 'key' => 'guarantor_info.company_name'],
    ['label' => 'Position', 'value' => '', 'key' => 'guarantor_info.position'],
    ['label' => 'Average Monthly Income', 'value' => '', 'key' => 'guarantor_info.month_income_avg'],
    ['label' => 'Office No.', 'value' => '', 'key' => 'guarantor_info.company_telephone'],
    ['label' => 'Occupation', 'value' => '', 'key' => 'guarantor_info.occupation'],
    ['label' => 'Past 6 Month Gross Income', 'value' => '', 'key' => 'guarantor_info.six_month_income'],
    ['label' => 'Postal Code', 'value' => '', 'key' => 'guarantor_info.company_postal_code'],
    ['label' => 'Industry', 'value' => '', 'key' => 'guarantor_info.job_type_id'],
    ['label' => 'Annual Gross Income', 'value' => '', 'key' => 'guarantor_info.annual_income'],
  ];
  ?>
  <div class="surty-employment border border-secondary ">
    <div class="row">
      <div class="col-12 col-lg-12">
        <div class="bg-secondary text-white px-3 py-2 fw-bold">Employment</div>
      </div>
    </div>
    <div class="p-3">
      <?php display_coloumns($contact, 3, 4); ?>
    </div>
  </div>
  <!-- Income Document -->
  <?php
  $IncomeDoc = [
    ['label' => 'PaySlip', 'value' => '', 'key' => 'guarantor_info.payslip'],
    ['label' => 'CPF', 'value' => '', 'key' => 'guarantor_info.cpf'],
    ['label' => 'NOA', 'value' => '', 'key' => 'guarantor_info.noa'],
    ['label' => 'Others', 'value' => '', 'key' => 'guarantor_info.others'],
    ['label' => 'Income Documents', 'value' => '', 'key' => 'guarantor_info.income_documents'],
  ];
  ?>
  <div class="surty-income-document border border-secondary ">
    <div class="row">
      <div class="col-12 col-lg-12">
        <div class="bg-secondary text-white px-3 py-2 fw-bold">Income Document</div>
      </div>
    </div>
    <div class="p-3">
      <?php display_coloumns($IncomeDoc, 4, 3); ?>
    </div>
  </div>
  <!-- Bank Information -->
  <?php
  $bankInfo = [
    ['label' => 'Bank Name', 'value' => '', 'key' => 'guarantor_info.bank_name'],
    ['label' => 'Bank Acc', 'value' => '', 'key' => 'guarantor_info.bank_acc'],
    //['label' => 'Bank Code', 'value' => '', 'key' => 'guarantor_info.bank_code'],
    ['label' => 'Date of Salary', 'value' => '', 'key' => 'guarantor_info.salary_date']
  ];
  ?>
  <div class="surtybank border border-secondary ">
    <div class="row">
      <div class="col-12 col-lg-12">
        <div class="bg-secondary text-white px-3 py-2 fw-bold">Bank Information</div>
      </div>
    </div>
    <div class="p-3">
      <?php display_coloumns($bankInfo, 3, 4); ?>
    </div>
  </div>

  <div class="application-mlcb">
    <div class="row">
      <div class="col-12 col-lg-7">
        <div class="py-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="mlcbApplicationInfo" checked disabled name="application_mlcb">
            <label class="form-check-label" for="mlcbApplicationInfo">
              Opt-in consent to disclose information to MLCB
            </label>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-5">
      </div>
    </div>
  </div>
</div>
<div class="mlcb-information">
  <div class="row">
    <div class="col-12 col-lg-6">
    </div>
    <div class="col-12 col-lg-6">
      <div class="py-3 d-flex-end">
        <div class="form-submit me-5 gap-4 d-flex">
          <button class="btn btn-danger px-4 btnCancel">Cancel</button>
          <!-- <button class="btn btn-secondary px-4" id="btnDraftStep7">Save Draft</button> -->
          <button class="btn btn-success px-4" id="btnContinueStep7">Submit</button>
        </div>
      </div>
    </div>
  </div>
</div>
<style>
  .next_of_kin_type {
    text-transform: capitalize;
  }
</style>
<?php
function display_coloumns($listItems = [], $numcols = 4, $w = 3)
{
  foreach ($listItems as $id => $item) { ?>
    <?php if ($id % $numcols == 0) { ?>
      <div class="row">
      <?php } ?>
      <div class="col-12 col-sm-6 col-lg-<?php print $w; ?>">
        <div class="mb-2">
          <h6 class="my-0 fs-12 text-muted px-2"><?php print $item['label']; ?></h6>
          <div class="p-2 need-fill-data <?php print @$item['key'];?>" data-field="<?php print @$item['key']; ?>" 
          <?php print (isset($item['key']) && $item['key']) ? 'id="' . $item['key'] . '"' : ""; ?>><?php print $item['value']; ?></div>
        </div>
      </div>
      <?php if (($id > 0 && ($id + 1 + $numcols) % $numcols == 0) || $id == count($listItems) - 1) { ?>
      </div>
<?php } //if
    } //for
  } ?>