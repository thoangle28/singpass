
<?php
    function find_country($id) {
        $country = mc_countries();
        $country = ($country->error == 'false') ? (array)$country->data : [];
        foreach($country as $c) {
            if( $c->id == $id) return $c->nicename;
        }

        return '';
    }

    function showIncomeDoc($str) {
        $income = ['No','No','No','No']; //PaySlip, CPF, NOA, Others
        if(!is_array($str)) {
            $income_document = $str;
            $income_document = str_replace("[",'',$income_document);  
            $income_document = str_replace("]",'',$income_document);  
            $income_document = str_replace('"','',$income_document);  
            $income_document = explode(",", $income_document);
        } else  $income_document = $str;

        foreach($income_document as $id => $item) {
            if( $item == "1" ) $income[0] = 'Yes';
            else if( $item == "2" ) $income[1] = 'Yes';
            else if( $item == "3" ) $income[2] = 'Yes';
            else if( $item == "4" ) $income[3] = 'Yes';
        }

        return $income;
    }

    function grossIncome($m1, $m2, $m3) {
        $m = [
            '&#x2022; 1st month: ' . formatMoneyLocale($m1),
            '&#x2022; 2nd month: ' . formatMoneyLocale($m2),
            '&#x2022; 3rd month: ' . formatMoneyLocale($m3)
        ];

        return implode("<br />", $m);
    }

    function showFileDocuments($file_documents, $description) {
        
        if( !is_array($file_documents)) return '';

        $files = [];
        foreach($file_documents as $item) {
            if($item->description == $description) {
                $files[] = $item;
            }
        }
        //process file
        $lists = [];
        //$listFiles = [];
        foreach($files as $file) {
            $lists[] = '<a href="#" class="preViewPDF" data-base64="$file->base64">'.$file->document_name.'</a>';
        }

        return implode('; ', $lists);
    }
    //---------------
    $obligation = ['G' => 'Surety'];
    $source = source();
    $addresses = address_type();
    $addresses = ($addresses->error == 'false' || !$addresses->error) ? (array)$addresses->data : [];
    $app = mc_application_details($_GET['pid']); 
   
?>
<h3>Application Summary</h3>

<?php if( $app['customer']->nric_no != $singPassChecked['NRIC_No_FIN'] || $app['application']->status != 1) { ?>
    <div class="row justify-content-center my-5">
        <div class="col-12 col-md-10 col-lg-10">
            <div class="alert alert-info">
                You don't have permission to view the application.
            </div>
        </div>
    </div>
<?php } else { ?>
<div class="wrapper-tables">
    <?php if( isset($customer->data->customer_details) && !$customer->data->customer_details->is_ban ) {  ?>
    <div class="text-right mb-3">
        <button class="btn btn-primary btn-small" id="uploadMoreFiles">Upload Files</button>
    </div>
    <div id="upload_files" class="">
        <div class="upload-wrapper">
            <?php require_once('upload_files.php'); ?>
            <div class="buttons">
                <button type="button" id="submitUpload" class="btn btn-primary">Upload</button>
                <button type="button" id="bntClose" class="btn btn-danger">Close</button>
            </div>
        </div>
    </div>
    <?php } ?>
    <table class="table responsive">
        <tbody>
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Personal Information</strong>
                </td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>ID Type</td><td>Expiry Date</td><td>Gender</td><td>Nationality</td><td>NRIC No./FIN</td>
            </tr>
            <tr>
                <td data-title="ID Type">
                    <?php 
                    $type = (isset($app['customer']->identification_type)) ? $app['customer']->identification_type : 'foreign_identification_number'; 
                    print $identification_type[$type];
                    ?>                
                </td><td data-title="Expiry Date">
                    <?php 
                    if( @$app['customer']->identification_expiry)
                    print @date('m/d/Y', strtotime($app['customer']->identification_expiry));?>
                </td>
                <td data-title="Gender"><?php print $gender[@$app['customer']->gender];?></td>
                <td data-title="Nationality"><?php print find_country(@$app['customer']->country_id);?></td>
                <td data-title="NRIC No./FIN"><?php print @$app['customer']->identification_no;?></td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>Name of Applicant</td><td>Date of Birth</td><td>Language Spoken</td><td>Source</td><td>Marital Status</td>
            </tr>
            <tr>
                <td data-title="Name of Applicant"><?php print @$app['customer']->lastname.' '.@$app['customer']->firstname;?></td>
                <td data-title="Date of Birth"><?php print @date('m/d/Y', strtotime($app['customer']->date_of_birth));?></td>
                <td data-title="Language Spoken"><?php print @$spoken_language[$app['borrower']->spoken_language];?></td>
                <td data-title="Source"><?php print @$source[$app['borrower']->marketing_type_id];?></td>
                <td data-title="Marital Status"><?php print @$marital_status[$app['customer']->marital_status];?></td>
            </tr>
            <tr class="mb-not-show">
                <td colspan="2"><b>Do you currently have any legal actions pending against you?</b></td>
                <td colspan="3" data-title="Do you currently have any legal actions pending against you?">
                    <?php print @$app['customer']->legal_actions_against ? ' Yes' : ' No'; ?>
                </td>
            </tr>
            <tr class="not-show-pc">
                <td colspan="5" class="not-show-before">
                    <span><b>Do you currently have any legal actions pending against you?</b><?php print @$app['customer']->legal_actions_against ? ' Yes' : ' No'; ?></span>
                </td>            
            </tr>
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Contact Information</strong>
                </td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>Phone Number 1</td><td>Phone Number 2</td><td>Home</td><td>Email</td><td>Alternate Email</td>
            </tr>
            <tr>
                <td class="togglEyes" data-title="Phone Number 1">
                <?php if( isset($app['borrower']->mobilephone_1) && $app['borrower']->mobilephone_1) { ?>
                    <div class="d-flex align-items-center">
                    <span class="textToHide phone"><?php print (@$app['borrower']->mobilephone_1) ? '(+65)'.@$app['borrower']->mobilephone_1 : '';?></span>
                    <span class="toggleText eye-icon show-eyes"></span></div><?php } ?>
                </td>
                <td class="togglEyes" data-title="Phone Number 2">
                <?php if( isset($app['borrower']->mobilephone_2) && $app['borrower']->mobilephone_2) { ?>
                    <div class="d-flex">
                    <span class="textToHide phone"><?php print (@$app['borrower']->mobilephone_2) ? '(+65)'.@$app['borrower']->mobilephone_2 : '';?></span>
                    <span class="toggleText eye-icon show-eyes"></span></div><?php }?>
                </td>
                <td class="togglEyes" data-title="Home">
                <?php if( isset($app['borrower']->homephone) && $app['borrower']->homephone) { ?>
                    <div class="d-flex">
                    <span class="textToHide phone"><?php print (@$app['borrower']->homephone) ? '(+65)'.@$app['borrower']->homephone : '';?></span>
                    <span class="toggleText eye-icon show-eyes"></span></div><?php } ?>
                </td>
                <td class="togglEyes" data-title="Email">
                <?php if( isset($app['borrower']->email_1) && $app['borrower']->email_1) { ?>
                    <div class="d-flex">
                    <span class="textToHide email"><?php print @$app['borrower']->email_1;?></span>
                    <span class="toggleText eye-icon show-eyes"></span></div><?php } ?> 
                </td>
                <td class="togglEyes" data-title="Alternate Email">
                <?php if( isset($app['borrower']->email_2) && $app['borrower']->email_2) { ?>
                    <div class="d-flex">
                    <span class="textToHide email"><?php print @$app['borrower']->email_2;?></span>
                    <span class="toggleText eye-icon show-eyes"></span></div><?php } ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Relatives' Information</strong>
                </td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>Next of Kin Name</td><td>Type</td><td>Phone</td><td></td><td></td>
            </tr>
            <tr>
                <td class="togglEyes" data-title="Next of kin name">
                    <?php print @$app['borrower']->next_of_kin_name;?>
                </td>
                <td class="togglEyes" data-title="Type">
                    <?php print @ucfirst($app['borrower']->next_of_kin_type);?>
                </td>
                <td class="togglEyes" data-title="Phone">
                <?php if( isset($app['borrower']->next_of_kin_phone) && $app['borrower']->next_of_kin_phone) { ?>
                    <div class="d-flex">
                    <span class="textToHide phone"><?php print (@$app['borrower']->next_of_kin_phone) ? '(+65)'.@$app['borrower']->next_of_kin_phone : '';?></span>
                    <span class="toggleText eye-icon show-eyes"></span></div><?php } ?>
                </td>
                <td class="togglEyes mb-not-show">          
                </td>
                <td class="togglEyes mb-not-show">
                </td>
            </tr>
            <!-- Home Addresses -->
            <?php
            $addList = [];
            //print_r($app['address']);

            foreach($addresses as $add_type) {
                foreach($app['address'] as $address) {
                    if($add_type->id == $address->address_type_id 
                        && !isset($addList[$add_type->address_type_name]) ) {
                        $addList[$add_type->address_type_name] = []; //create empty array
                    }
    
                    if( $add_type->id == $address->address_type_id) $addList[$add_type->address_type_name][] = $address;
                }
            }
        
            $housing_type = housing_type();
        
            if( $addList && $addList['Home'] ) {
            ?>
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Home Addresses</strong>
                </td>
            </tr>
            <!-- repeater -->
            <?php foreach($addList['Home'] as $item) { ?>
            <tr class="fw-bold mb-not-show">
                <td>Property Type</td><td>Residential Status</td><td>Block</td><td>Postal</td><td>Housing Type</td>
            </tr>
            <tr>
                <td data-title="Property Type"><?php print $item->property_type;?></td>
                <td data-title="Residential Status"><?php print $item->home_ownership;?></td>
                <td data-title="Block"><?php print $item->block;?></td>
                <td data-title="Postal"><?php print $item->postal_code;?></td>
                <td data-title="Housing Type"><?php print $housing_type[$item->housing_type]['text'];?></td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>Building</td><td>Country</td><td>Existing Staying</td><td>Unit</td><td>Street</td>
            </tr>
            <tr>
                <td data-title="Building"><?php print $item->building;?></td><td data-title="Country"><?php print find_country($item->country);?></td>
                <td data-title="Existing Staying"><?php print ($item->existing_staying == 1) ? 'Yes'  : 'No';?></td>
                <td data-title="Unit"><?php print $item->unit;?></td><td data-title="Street"><?php print $item->street;?></td>
            </tr>
            <tr>
                <td class="fw-bold mb-not-show">Address Label</td>
                <td colspan="4" data-title="Address Label"><?php print $item->address_label;?></td>
            </tr>
            <?php } 
            } ?>
            <!-- Work Addresses -->
            <?php 
            unset($addList['Home']);

            if( $addList ) {
                foreach($addList as $key => $list) {
                if(!$list) continue;
            ?>
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong><?php print ucfirst(strtolower($key));?> Addresses</strong>
                </td>
            </tr>
            <!-- repeater -->
            <?php foreach( $list as $item ) { ?>
            <tr class="fw-bold mb-not-show">
                <td>Default Work</td><td>Block</td><td>Street</td><td>Unit</td><td></td>
            </tr>
            <tr>
                <td data-title="Default Work"><?php print ($item->is_default ? "Yes" : "No");?></td>
                <td data-title="Block"><?php print $item->block;?></td>
                <td data-title="Street"><?php print $item->street;?></td>
                <td data-title="Unit"><?php print $item->unit;?></td><td class="mb-not-show"></td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>Building</td><td>Country</td><td>Postal</td><td colspan="2">Address Label</td>
            </tr>
            <tr>
                <td data-title="Building"><?php print $item->building;?></td><td data-title="Country"><?php print find_country($item->country);?></td>
                <td data-title="Postal"><?php print $item->postal_code;?></td><td colspan="2" data-title="Address Label"><?php print $item->address_label;?></td>
            </tr>
            <?php   } 
                }
            } 

            $empStatus = ['EMP' => 'Employed', 'UNEMPINC' => 'Self Employed', 'UNEMP' => 'Unemployed'];
            ?>
            <!-- Employment -->
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Employment</strong>
                </td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>Employment Status</td><td>Company Name</td><td>Address</td><td>Position</td><td>Office No.</td>
            </tr>
            <tr>
                <td data-title="Employment Status"><?php print @$empStatus[$app['borrower']->employment_status];?></td>
                <td data-title="Company Name"><?php print $app['employment']->company_name;?></td>
                <td data-title="Address"><?php print $app['employment']->address;?></td>
                <td data-title="Position"><?php print $positions[$app['employment']->position];?></td>
                <td data-title="Office No."><?php print ($app['employment']->company_telephone) ? "(+65)".$app['employment']->company_telephone : '';?></td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>Occupation</td><td>Postal Code</td><td>Industry</td><td>Yrs of Employment Period</td><td></td>
            </tr>
            <?php $jobOptions = job_listing();?>
            <tr>
                <td data-title="Occupation"><?php print $app['employment']->occupation;?></td>
                <td data-title="Postal Code"><?php print $app['employment']->portal_code;?></td>
                <td data-title="Industry"><?php print $jobOptions[$app['borrower']->job_type_id];?></td>
                <td data-title="Yrs of Employment Period"><?php print $app['employment']->yrs_of_employment_period;?></td>
                <td class="mb-not-show"></td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>Annual Gross Income</td><td>Average Monthly Income</td><td>Past 6 Month Gross Income</td><td>Gross Monthly Income</td><td></td>
            </tr>
            <tr>
                <td data-title="Annual Gross Income"><?php print formatMoneyLocale($app['employment']->annual_income);?></td>
                <td data-title="Average Monthly Income"><?php print formatMoneyLocale(round($app['employment']->annual_income/12, 2));?></td>
                <td data-title="Past 6 Month Gross Income"><?php print formatMoneyLocale($app['employment']->six_months_income);?></td>
                <td data-title="Gross Monthly Income"><?php print grossIncome($app['employment']->monthly_income_1, $app['employment']->monthly_income_2, $app['employment']->monthly_income_3);?></td>
                <td class="mb-not-show"></td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td colspan="2">Have you been declared bankrupt in the past 5 years?</td>
                <td colspan="3">Do you have any plans to declare bankruptcy in the next 3 months?</td>
            </tr>
            <tr class="mb-not-show">
                <td colspan="2"><?php print (@$app['employment']->bankrupted) ? 'Yes' : 'No';?></td>
                <td colspan="3"><?php print (@$app['employment']->bankrupt_plan) ? 'Yes' : 'No';?></td>
            </tr>
            <tr class="not-show-pc">
                <td colspan="5" class="not-show-before">
                    <span><b>Have you been declared bankrupt in the past 5 years?</b>&nbsp;&nbsp;<?php print (@$app['employment']->bankrupted) ? 'Yes' : 'No';?></span>
                </td>            
            </tr>
            <tr class="not-show-pc">
                <td colspan="5" class="not-show-before">
                    <span><b>Do you have any plans to declare bankruptcy in the next 3 months?</b>&nbsp;&nbsp;<?php print (@$app['employment']->bankrupt_plan) ? 'Yes' : 'No';?></span>
                </td>            
            </tr>
            <!-- Income Document -->
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Income Document</strong>
                </td>
            </tr>
            <?php 
                $income = showIncomeDoc($app['application']->income_document);           
            ?>
            <tr class="fw-bold mb-not-show">
                <td>PaySlip</td><td>CPF</td><td>NOA</td><td>Others</td><td></td>
            </tr>
            <tr>
                <td data-title="PaySlip"><?php print $income[0];?></td><td data-title="CPF"><?php print $income[1];?></td>
                <td data-title="NOA"><?php print $income[2];?></td><td data-title="Others"><?php print $income[3];?></td>
                <td class="mb-not-show"></td>
            </tr>
            <tr>
                <td class="fw-bold mb-not-show">Income Documents</td>
                <td colspan="4" data-title="Income Documents">
                    <?php print showFileDocuments($app['file_documents'], 'employment');?>
                </td>
            </tr>
            <tr class="mb-not-show">
                <td class="fw-bold">Source of Income</td>
                <td colspan="4"><?php print $app['application']->income_source;?></td>
            </tr>
            <tr class="not-show-pc">
                <td colspan="5" class="not-show-before">
                    <div class="fw-bold">Source of Income</div>
                    <?php print $app['application']->income_source;?>
                </td>            
            </tr>
            <!-- Loan Details -->
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Loan Details</strong>
                </td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>Loan Type</td><td>Loan Amount Required</td><td>Loan Terms</td><td>Term Unit</td><td>Installment</td>
            </tr>
            <tr>
                <td data-title="Loan Type"><?php $loant_type = loanListing();
                    print $loant_type[$app['application']->loan_type_id];?></td>
                <td data-title="Loan Amount Required"><?php print formatMoneyLocale($app['application']->loan_amount_requested);?></td>
                <td data-title="Loan Terms"><?php print $app['application']->loan_terms;?></td>
                <td data-title="Term Unit"><?php print termUnit($app['application']->term_unit);?></td>
                <td data-title="Installment"><?php 
                    $installment = mc_calc_repayment_raw(
                        $app['application']->loan_amount_requested,
                        $app['application']->loan_terms,
                        $app['application']->term_unit,
                        $app['application']->loan_type_id,
                        0 // not ajax
                    );
                    print formatMoneyLocale($installment);
                ?></td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>Reason For Loan</td><td colspan="2">Description</td><td colspan="2">Loan Documents</td>
            </tr>
            <tr>
                <td data-title="Reason For Loan"><?php print $reason_for_loan[$app['application']->loan_reason];?></td>
                <td colspan="2" data-title="Description"><?php print $app['application']->description;?></td>
                <td colspan="2" data-title="Loan Documents">
                    <?php print showFileDocuments($app['file_documents'], 'loan_details');?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Borrower(s) & Surety(ies)</strong>
                </td>
            </tr>
            <tr class="mb-not-show">
                <td colspan="2" class="fw-bold">Is there any other individual or company (Beneficial Owner) that will benefit from the loan?</td>
                <td colspan="3"><?php print $app['application']->has_beneficial_owner ? 'Yes' : 'No';?></td>
            </tr>
            <tr class="mb-not-show">
                <td colspan="2" class="fw-bold">Explain</td>
                <td colspan="3"><?php print $app['application']->beneficial_owner_explanation;?></td>
            </tr>

            <tr class="not-show-pc">
                <td colspan="5" class="not-show-before">
                    <span><b>Is there any other individual or company (Beneficial Owner) that will benefit from the loan?</b></span>
                </td>          
            </tr>
            <tr class="not-show-pc">
                <td colspan="5" class="not-show-before">
                    <?php print (@$app['application']->has_beneficial_owner) ? 'Yes' : 'No';?>
                </td>     
            </tr>
            <?php if( $app['application']->beneficial_owner_explanation ) { ?>
            <tr class="not-show-pc">
                <td colspan="5" class="not-show-before">
                    <span><b>Explain</b></span>
                </td>            
            </tr> 
            <tr class="not-show-pc">
                <td colspan="5" class="not-show-before">
                    <?php print $app['application']->beneficial_owner_explanation;?>
                </td>            
            </tr>    
            <?php } ?>

            <tr class="mb-not-show">
                <td colspan="2" class="fw-bold">Are you a politically-exposed person?</td>
                <td colspan="3">
                    <?php print $app['application']->is_politically_exposed ? 'Yes' : 'No';?>
                </td>
            </tr>
            <tr class="mb-not-show">
                <td colspan="2" class="fw-bold">Explain</td>
                <td colspan="3">
                    <?php print $app['application']->politically_exposed_explanation;?>
                </td>
            </tr>

            <tr class="not-show-pc">
                <td colspan="5" class="not-show-before">
                    <span><b>Are you a politically-exposed person?</b></span>
                </td>            
            </tr>
            <tr class="not-show-pc">
                <td colspan="5" class="not-show-before">
                    <?php print (@$app['application']->is_politically_exposed) ? 'Yes' : 'No';?>
                </td>            
            </tr>   
            <?php if( $app['application']->politically_exposed_explanation ) { ?>
            <tr class="not-show-pc">
                <td colspan="5" class="not-show-before">
                    <span><b>Explain</b></span>
                </td>            
            </tr> 
            <tr class="not-show-pc">
                <td colspan="5" class="not-show-before">
                    <?php print $app['application']->politically_exposed_explanation;?>
                </td>            
            </tr>    
            <?php } ?>
            <!-- Bank Information -->
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Bank Information</strong>
                </td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>Bank Name</td><td>Bank Account</td><td>Date of Salary</td><td></td><td></td>
            </tr>
            <tr>
                <td data-title="Bank Name"><?php print $app['bank_account']->bank_name_1;?></td>
                <td data-title="Bank Account"><?php print $app['bank_account']->account_number_1;?></td>
                <td data-title="Date of Salary"><?php print getOrdinalSuffix($app['bank_account']->date_of_salary);?></td>
                <td class="mb-not-show"></td><td class="mb-not-show"></td>
            </tr>
            <tr>
                <td colspan="5" class="not-show-before">
                    <img style="width: 16px; height: auto; margin-right: 5px;" 
                    src="<?php print PLUGIN_DIR_URL . "assets/images/square-check-regular.svg"; ?>" /> 
                    Opt-in consent to disclose information to MLCB </td>
            </tr>        
        </tbody>
    </table>
<!-- Surety -->
<?php 
    
    $surety = $app['borrower']->guarantor_info;
    $surety = json_decode($surety);

    if($surety && $surety->identification_no) {
        //print_r($surety);
?>
    <h3>Surety</h3>
    <p>Confidential</p>
    <table class="table responsive">
        <tbody>
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Surety Information</strong>
                </td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>ID Type</td><td>NRIC No./FIN</td><td>Full Name</td><td>Gender</td><td>Date of Birth</td>
            </tr>
            <tr>
                <td data-title="ID Type"><?php print @$identification_type[$surety->identification_type];?></td>
                <td data-title="NRIC No./FIN"><?php print @$surety->identification_no;?></td>
                <td data-title="Full Name"><?php print @$surety->lastname .' '.$surety->firstname;?></td>            
                <td data-title="Gender"><?php print @$gender[$surety->gender];?></td>
                <td data-title="Date of Birth"><?php print @date('m/d/Y', strtotime($surety->date_of_birth));?></td>
            </tr>
        
            <tr class="fw-bold mb-not-show">
                <td>Nationality</td><td>Obligation</td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td data-title="Nationality"><?php print @find_country($surety->nationality);?></td>
                <td data-title="Obligation"><?php print @$obligation[$surety->obligation_code];?></td>
                <td class="mb-not-show"></td><td class="mb-not-show"></td><td class="mb-not-show"></td>
            </tr>
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Contact Information</strong>
                </td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>Phone Number 1</td><td>Phone Number 2</td><td>Home</td><td>Email</td><td>Alternate Email</td>
            </tr>
            <tr>
                <td class="togglEyes" data-title="Phone Number 1">
                    <?php if( isset($surety->phone_1) && $surety->phone_1) { ?>
                    <div class="d-flex align-items-center">
                    <span class="textToHide phone">(+65)<?php print @$surety->phone_1;?></span>
                    <span class="toggleText eye-icon show-eyes"></span></div>
                    <?php } ?>    
                </td>
                <td class="togglEyes" data-title="Phone Number 2">
                    <?php if( isset($surety->phone_2) && $surety->phone_2) { ?>
                    <div class="d-flex align-items-center">
                    <span class="textToHide phone">(+65)<?php print @$surety->phone_2;?></span>
                    <span class="toggleText eye-icon show-eyes"></span></div>
                    <?php } ?></td>
                <td class="togglEyes" data-title="Home">
                    <?php if( isset($surety->phone_home) && $surety->phone_home) { ?>
                    <div class="d-flex align-items-center">
                    <span class="textToHide phone">(+65)<?php print @$surety->phone_home;?></span>
                    <span class="toggleText eye-icon show-eyes"></span></div>
                    <?php } ?></td>
                <td class="togglEyes" data-title="Email">
                    <?php if( isset($surety->email) && $surety->email) { ?>
                    <div class="d-flex align-items-center">
                    <span class="textToHide email"><?php print @$surety->email;?></span>
                    <span class="toggleText eye-icon show-eyes"></span></div>
                    <?php } ?></td>
                <td class="togglEyes" data-title="Alternate Email">
                    <?php if( isset($surety->email_alternate) && $surety->email_alternate) { ?>
                    <div class="d-flex align-items-center">
                    <span class="textToHide email"><?php print @$surety->email_alternate;?></span>
                    <span class="toggleText eye-icon show-eyes"></span></div>
                    <?php } ?></td>
            </tr>
            <!-- Home Addresses -->
            
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Home Addresses</strong>
                </td>
            </tr>
            <!-- repeater -->
            <tr class="fw-bold  mb-not-show">
                <td>Property Type</td><td>Unit</td><td>Building</td><td>Postal</td><td>Housing Type</td>
            </tr>
            <tr>
                <td data-title="Property Type"><?php print @$surety->property_type;?></td>
                <td data-title="Unit"><?php print @$surety->unit;?></td>
                <td data-title="Building"><?php print @$surety->building;?></td>
                <td data-title="Postal"><?php print @$surety->postal;?></td>
                <td data-title="Housing Type"><?php print @$housing_type[$surety->housing_type]['text'];?></td>
            </tr>
            <tr class="fw-bold  mb-not-show">
                <td>Block</td><td>Street</td><td>Country</td><td></td><td></td>
            </tr>
            <tr>
                <td data-title="Block"><?php print @$surety->block;?></td>
                <td data-title="Street"><?php print @$surety->street;?></td>
                <td data-title="Country"><?php print find_country(@$surety->country_id);?></td>
                <td class="mb-not-show"></td><td class="mb-not-show"></td>
            </tr>        
            <!-- Employment -->
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Employment</strong>
                </td>
            </tr>
            <tr class="fw-bold  mb-not-show">
                <td>Employment Status</td><td>Company Name</td><td>Address</td><td>Position</td><td>Office No.</td>
            </tr>
            <tr>
                <td data-title="Employment Status"><?php print @$empStatus[$surety->employment_status];?></td>
                <td data-title="Company Name"><?php print @$surety->company_name;?></td>
                <td data-title="Address"><?php print @$surety->company_address;?></td>
                <td data-title="Position"><?php print @$positions[$surety->position];?></td>
                <td data-title="Office No."><?php print @($surety->company_telephone ? '(+65)'.$surety->company_telephone : '');?></td>
            </tr>
            <tr class="fw-bold  mb-not-show">
                <td>Occupation</td><td>Postal Code</td><td>Industry</td>
                <td></td><td></td>
            </tr>
            <tr>
                <td data-title="Occupation"><?php print @$surety->occupation;?></td>
                <td data-title="Postal Code"><?php print @$surety->company_postal_code;?></td>
                <td data-title="Industry"><?php print @$jobOptions[$surety->job_type_id];?></td>
                <td class="mb-not-show"></td><td class="mb-not-show"></td>
            </tr>
            <tr class="fw-bold  mb-not-show">
                <td>Annual Gross Income</td><td>Average Monthly Income</td><td>Past 6 Month Gross Income</td><td colspan="2">Gross Monthly Income</td>
            </tr>
            <tr>
                <td data-title="Annual Gross Income"><?php print @formatMoneyLocale($surety->annual_income);?></td>
                <td data-title="Average Monthly Income"><?php print formatMoneyLocale($surety->month_income_avg);?></td>
                <td data-title="Past 6 Month Gross Income"><?php print @formatMoneyLocale($surety->six_month_income);?></td>
                <td colspan="2" data-title="Gross Monthly Income"><?php print @formatMoneyLocale($surety->monthly_income_1);?></td>
            </tr>
            <!-- Income Document -->
        
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Income Document</strong>
                </td>
            </tr>
            <?php 
                $income = @showIncomeDoc($surety->income_document);           
            ?>
            <tr class="fw-bold  mb-not-show">
                <td>PaySlip</td><td>CPF</td><td>NOA</td><td>Others</td><td></td>
            </tr>
            <tr>
                <td data-title="PaySlipe"><?php print @$income[0];?></td>
                <td data-title="CPF"><?php print @$income[1];?></td>
                <td data-title="NOA"><?php print @$income[2];?></td>
                <td data-title="Others"><?php print @$income[3];?></td><td class="mb-not-show"></td>
            </tr>
            
            <tr>
                <td class="fw-bold mb-not-show">Income Documents</td>
                <td colspan="4" data-title="Income Documents">
                    <?php print @showFileDocuments($surety->income_document_files, 'guarantor');?>
                </td>
            </tr>
            <!-- Bank Information -->
            <tr>
                <td colspan="5" class="bg-secondary text-white text-left not-show-before">
                    <strong>Bank Information</strong>
                </td>
            </tr>
            <tr class="fw-bold mb-not-show">
                <td>Bank Name</td><td>Bank Account</td><td>Date of Salary</td><td></td><td></td>
            </tr>
            <tr>
                <td data-title="Bank Name"><?php print @$surety->bank_name;?></td>
                <td data-title="Bank Account"><?php print @$surety->bank_acc;?></td>
                <td data-title="Date of Salary"><?php print @getOrdinalSuffix($surety->salary_date);?></td>
                <td class="mb-not-show"></td><td class="mb-not-show"></td>
            </tr>
            <tr>
                <td colspan="5" class="not-show-before">
                    <img style="width: 16px; height: auto; margin-right: 5px;" src="<?php print PLUGIN_DIR_URL . "assets/images/square-check-regular.svg"; ?>" /> Opt-in consent to disclose information to MLCB </td>
            </tr> 
            <?php /* */ ?>
        </tbody>
    </table>
<?php } print_r($app['document_upload']);?>
    <table class="table responsive">
        <tbody>
            <tr>
                <td class="bg-secondary text-white text-left not-show-before">
                    <strong>Files Uploaded</strong>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="not-show-before">
                    <?php foreach($app['file_documents'] as $file) { ?>
                    <a class="fw-normal preViewPDF" href="#" data-base64="<?php print md5($file->document_name);?>"><?php print $file->document_name;?>(<?php print round($file->size/1024,2);?>KB)</a>,
                    <div id="<?php print md5($file->document_name);?>" class="d-none"><?php print $file->base64;?></div>
                    <?php } ?>
                 </td>
            </tr>
        </tbody>
    </table>
</div>

<style>
    table tr td .preViewPDF {
        color: #000;
        font-weight: normal;
    }
    table tr td .preViewPDF:hover {
        color: #bd0000;
        font-weight: normal;
    }
    .togglEyes .phone {
        min-width: 90px;
        display: inline-block;
    }
    .togglEyes .email {
        min-width: 150px;
        display: inline-block;
    }
    .togglEyes .eye-icon {
        display: inline-block;
        width: 18px;
        height: 18px;
        background-position: center;
        background-repeat: no-repeat;
        cursor: pointer;
        margin-left: 10px;
        background-size: cover;
    }
    .togglEyes .hide-eyes {
        background-image: url('/wp-content/plugins/mc-application-form/assets/images/eye.png');
    }
    .togglEyes .show-eyes {
        background-image: url('/wp-content/plugins/mc-application-form/assets/images/visible.png');
    }
    .wrapper-tables table.responsive tr:not(:last-child) {
        margin-top: -1px;
        margin-bottom: 0px;
    }
    #upload_files {
        display: none;
    }
    #upload_files.show {
        display: block;
    }
    #upload_files .upload-wrapper {
        top: 50%;
        left: 50%;
        width: 95%;
        max-width: 1024px;
        transform: translate(-50%, -50%);
        z-index: 100;
        background: #fff;
        padding: 32px;
        position: fixed;
        max-height: 90vh;
        overflow: auto;
    }
    #upload_files::before {
        content: '';
        position: fixed;
        background: rgba(0,0, 0, 0.5);
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        z-index: 99;
    }
    #upload_files .buttons {
        margin-top: 24px;
        text-align: right;
    }
    #upload_files button.btn-primary {
        background-color: var(--active_blue) !important;
        border: 0 !important;
        color: #fff;
    }
    #upload_files button.btn-danger {
        background-color: #bd0000;
        border: 0 !important;
        color: #fff;
    }
    #upload_files button:hover {
        opacity: 0.6;
    }

    @media (max-width: 767px) {
        #upload_files .upload-wrapper {
            padding: 16px;
        }
    }
</style>
<script>
    (function($){
        $(document).ready(function(){
            $('.preViewPDF').click(function(e){
                e.preventDefault();
                base64Pdf = $(this).attr('data-base64');
                base64File = $('#' + base64Pdf);
                if( base64File.length > 0 ) base64Pdf = base64File.text();
                //PDF
                if(base64Pdf.startsWith('JVBER'))
                    viewPdf(base64Pdf);
                else {
                    // Create a new window and show the image
                    const newWindow = window.open();
                    base64Pdf = 'data:image/png;base64,' + base64Pdf;
                    newWindow.document.write(`<img src="${base64Pdf}" style="max-width:80%;height:auto;" alt="Preview">`);
                }
            });
            $('#uploadMoreFiles').click(function(){
                $('#upload_files').addClass('show');
            });

            $('#upload_files button.btn-danger').click(function(){
                $('#upload_files').removeClass('show');
                $('#file-input').val('');
                $('#file-list textarea').remove();
                $('#file-list .file-preview').remove();
            });

            uploadLoadMorFiles();
        });

        $(window).on('load', function(){
            showToHiddeText();
        });

        function base64ToBlob(base64, mime) {
            //check file
            if (base64) {
            temp = base64.split(",");
            if (temp.length == 2) base64 = temp[temp.length - 1];
            }

            const byteCharacters = atob(base64);
            const byteNumbers = new Array(byteCharacters.length);
            for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            return new Blob([byteArray], { type: mime });
        }

        function viewPdf(base64Pdf) {
            // Replace this with your base64-encoded PDF string
            const mimeType = "application/pdf";
            // Convert base64 to Blob
            const pdfBlob = base64ToBlob(base64Pdf, mimeType);
            // Create a URL for the Blob
            const pdfUrl = URL.createObjectURL(pdfBlob);
            // Option 1: Open PDF in a new window
            window.open(pdfUrl, "View PDF", "width=1000,height=1000");
        }

        function showToHiddeText() {
            toggleEyes = document.querySelectorAll('.togglEyes');   
            toggleEyes.forEach((parent) => {
                const textToHide = parent.querySelector('.textToHide');
                const toggleText = parent.querySelector('.toggleText');
                
                if( !textToHide || typeof textToHide === 'undefined') return;

                let isEmail = textToHide.classList.contains('email');
               
                if( typeof isEmail === 'undefined') isEmail = false;

                changToShowEyes(textToHide, toggleText, isEmail);
            });
        }

        // Function to replace text with asterisks
        function hideText(text, email) {
            numtoShow = 4;
            len = text.length - numtoShow;           
            endtext = text.substr(text.length - numtoShow, text.length);       
         
            if( email ) {                
                $item = text.split('@'); //xxxx@xxxx
                firstText = $item[0].substr(0, 2); //email
                endText = $item[0].substr($item[0].length - 1, $item[0].length); //email
                return firstText + ('*'.repeat($item[0].length - 3)) + endText + '@' + $item[1];
            } else 
                return ('*'.repeat(len)) + endtext;
        }

        function changToShowEyes(textToHide, toggleText, email) {
            //const toggleText = document.querySelector(textToggle);
            //const textToHide = document.querySelector(textShowHide);
            
            // Save the original text
            const originalText = textToHide.textContent;
            let isHidden = true;
           
            //textToHide.textContent = hideText(originalText, email);
            toggleText.classList.add('show-eyes')
            toggleText.classList.remove('hide-eyes');

            toggleText.addEventListener('click', function() {
                if (isHidden) {
                    // Show the original text
                    textToHide.textContent = originalText;
                    this.classList.add('hide-eyes');
                    this.classList.remove('show-eyes');
                    //this.textContent = '';  // Change icon to "hide"
                } else {
                    // Hide the text with asterisks
                    textToHide.textContent = hideText(originalText, email);
                    this.classList.add('show-eyes')
                    this.classList.remove('hide-eyes');
                    //this.textContent = '';  // Change icon to "show"
                }
                isHidden = !isHidden;  // Toggle the state
            });

            // Initially hide the text
            textToHide.textContent = hideText(originalText, email);
        }
        
        function uploadLoadMorFiles() {
            $('#continue-upload').remove();

            $('#submitUpload').click(function(e){
                const fileInput = $('#file-input');
                $('#submitUpload').text('Processing....');
                
                if (fileInput[0].files.length === 0) {
                    event.preventDefault(); // Stop form submission
                    Swal.fire({
                        title: "Upload files",
                        html: "No file selected or the selected file format is invalid. Please choose a PDF or image file (JPG, PNG) to proceed.",
                        icon: "error",
                    });
                    $('#submitUpload').text('Upload');
                    return;
                } else {
                    files = fileInput[0].files;
                    fileLists = []; // Clear previous file list
                    filesPreview = [];
                    $('#file-list .file-preview').each(function(){
                        fname = $(this).attr('id');
                        filesPreview.push(fname);
                    });
                    //console.log(filesPreview);

                    Array.from(files).forEach((file, index) => {
                        const reader = new FileReader();
                        let fileName = file.name.toLowerCase().replaceAll(' ','_');
                        if( filesPreview.includes(fileName)) {
                            reader.onload = function (e) {
                                const base64 = e.target.result;                        
                                const fileBase64 = {
                                    name: fileName,
                                    base64: base64,
                                    document_name: file.name || "",
                                    size: file.size,
                                    type: file.type,
                                    description: 'files'
                                };

                                fileLists.push(fileBase64);
                            };

                            reader.onerror = function () {
                                alert('Error reading file: ' + file.name);
                            };

                            // Convert file to base64
                            reader.readAsDataURL(file);
                        }
                    });

                    //send to server
                    setTimeout(function() {
                        $.ajax({
                            type: "POST",
                            url: mc_ajax.ajax_url,
                            dataType: "json",
                            data: {
                                action: "upload_files",
                                appId: <?php print $_GET['pid'];?>,
                                files: fileLists
                            },
                            success: function(response) {
                                if( response.code != 200 ) {
                                    Swal.fire({
                                        title: "Upload files",
                                        html: response.message,
                                        icon: "error",
                                    });
                                    $('#submitUpload').text('Upload');
                                } else {
                                    Swal.fire({
                                        title: "Upload files",
                                        html: response.message,
                                        icon: "success",
                                    });
                                    $('#upload_files #bntClose').trigger('click');
                                    $('#submitUpload').text('Upload');
                                }
                            },
                            error: function(){

                            }
                        });
                    }, 1000);
                }
            });
        }
    })(jQuery);
</script>
<?php } ?>
