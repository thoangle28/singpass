<?php
$menuItems = [
  'personal' => [
    'label' => 'Personal Information',
    'description' => 'Your personal information as shown in your financial profile.',
    'selected' => true,
    'template' => 'personal'
  ],
  'contact' => [
    'label' => 'Contact Information',
    'description' => 'Contact information for when needing to get in touch with you.',
    'selected' => false,
    'template' => 'contact'
  ],
  'employment' => [
    'label' => 'Employment',
    'description' => 'Your employment status and income.',
    'selected' => false,
    'template' => 'employment'
  ],
  'loan' => [
    'label' => 'Loan Details',
    'description' => 'Information about the loans you are requesting.',
    'selected' => false,
    'template' => 'loan'
  ],
  'bank' => [
    'label' => 'Bank Information',
    'description' => 'Your banking information.',
    'selected' => false,
    'template' => 'bank'
  ],
  'guarantor' => [
    'label' => 'Surety',
    'description' => 'Information about individuals or organizations that can guarantee your loan.',
    'selected' => false,
    'template' => 'guarantor'
  ],
  'uploadfiles' => [
    'label' => 'Upload Files',
    'description' => 'Drag & Drop the files',
    'selected' => false,
    'template' => 'upload_files'
  ],
  'completion' => [
    'label' => 'Completion',
    'description' => 'Review and submit your application.',
    'selected' => false,
    'template' => 'completion'
  ]
];

$status = 'draft';
$steps_defaut = [
  'personal' => ['status' => 'pending', 'data' => []],
  'contact' => ['status' => $status, 'data' => []],
  'employment' => ['status' => $status, 'data' => []],
  'loan' => ['status' => $status, 'data' => []],
  'bank' => ['status' => $status, 'data' => []],
  'guarantor' => ['status' => $status, 'data' => []],
  'completion' => ['status' => $status, 'data' => []],
  'uploadfiles' => ['status' => $status, 'data' => []],  
];

//Tools

$menuTools = [
  'google' => [
    'label' => 'Google search check',
    'icon' => 'google.svg',
    'status' => 'unchecked',
    'site' => 'https://google.com'
  ],
  /*'ung' => [
    'label' => 'UN page check',
    'icon' => 'chalkboard-user.svg',
    'status' => 'checked',
    'site' => 'https://google.com'
  ],*/
  'cas' => [
    'label' => 'CAs check',
    'icon' => 'user-check.svg',
    'status' => 'checked',
    'site' => 'https://google.com'
  ],
  'mlcb' => [
    'label' => 'Get MLCB report',
    'icon' => 'list-check.svg',
    'status' => 'checked',
    'site' => 'https://google.com'
  ],
  'loan' => [
    'label' => 'Loan cross check',
    'icon' => 'check-double.svg',
    'status' => 'checked',
    'site' => 'https://google.com'
  ],
  'phone' => [
    'label' => 'Validation phone number',
    'icon' => 'phone.svg',
    'status' => 'checked',
    'site' => 'https://google.com'
  ],
  'email' => [
    'label' => 'Validation Email',
    'icon' => 'envelope.svg',
    'status' => 'checked',
    'site' => 'https://google.com'
  ],
  'repayment' => [
    'label' => 'Repayment schedule calculator',
    'icon' => 'calendar-days.svg',
    'status' => 'unchecked',
    'site' => 'https://google.com'
  ]
];

//country
/*function countries()
{
  return array(
    "AF" => "Afghanistan",
    "AX" => "Aland Islands",
    "AL" => "Albania",
    "DZ" => "Algeria",
    "AS" => "American Samoa",
    "AD" => "Andorra",
    "AO" => "Angola",
    "AI" => "Anguilla",
    "AQ" => "Antarctica",
    "AG" => "Antigua and Barbuda",
    "AR" => "Argentina",
    "AM" => "Armenia",
    "AW" => "Aruba",
    "AU" => "Australia",
    "AT" => "Austria",
    "AZ" => "Azerbaijan",
    "BS" => "Bahamas",
    "BH" => "Bahrain",
    "BD" => "Bangladesh",
    "BB" => "Barbados",
    "BY" => "Belarus",
    "BE" => "Belgium",
    "BZ" => "Belize",
    "BJ" => "Benin",
    "BM" => "Bermuda",
    "BT" => "Bhutan",
    "BO" => "Bolivia",
    "BA" => "Bosnia and Herzegovina",
    "BW" => "Botswana",
    "BV" => "Bouvet Island",
    "BR" => "Brazil",
    "IO" => "British Indian Ocean Territory",
    "BN" => "Brunei Darussalam",
    "BG" => "Bulgaria",
    "BF" => "Burkina Faso",
    "BI" => "Burundi",
    "KH" => "Cambodia",
    "CM" => "Cameroon",
    "CA" => "Canada",
    "CV" => "Cape Verde",
    "KY" => "Cayman Islands",
    "CF" => "Central African Republic",
    "TD" => "Chad",
    "CL" => "Chile",
    "CN" => "China",
    "CX" => "Christmas Island",
    "CC" => "Cocos (Keeling) Islands",
    "CO" => "Colombia",
    "KM" => "Comoros",
    "CG" => "Congo",
    "CD" => "Congo, The Democratic Republic of The",
    "CK" => "Cook Islands",
    "CR" => "Costa Rica",
    "CI" => "Cote D'ivoire",
    "HR" => "Croatia",
    "CU" => "Cuba",
    "CY" => "Cyprus",
    "CZ" => "Czech Republic",
    "DK" => "Denmark",
    "DJ" => "Djibouti",
    "DM" => "Dominica",
    "DO" => "Dominican Republic",
    "EC" => "Ecuador",
    "EG" => "Egypt",
    "SV" => "El Salvador",
    "GQ" => "Equatorial Guinea",
    "ER" => "Eritrea",
    "EE" => "Estonia",
    "ET" => "Ethiopia",
    "FK" => "Falkland Islands (Malvinas)",
    "FO" => "Faroe Islands",
    "FJ" => "Fiji",
    "FI" => "Finland",
    "FR" => "France",
    "GF" => "French Guiana",
    "PF" => "French Polynesia",
    "TF" => "French Southern Territories",
    "GA" => "Gabon",
    "GM" => "Gambia",
    "GE" => "Georgia",
    "DE" => "Germany",
    "GH" => "Ghana",
    "GI" => "Gibraltar",
    "GR" => "Greece",
    "GL" => "Greenland",
    "GD" => "Grenada",
    "GP" => "Guadeloupe",
    "GU" => "Guam",
    "GT" => "Guatemala",
    "GG" => "Guernsey",
    "GN" => "Guinea",
    "GW" => "Guinea-bissau",
    "GY" => "Guyana",
    "HT" => "Haiti",
    "HM" => "Heard Island and Mcdonald Islands",
    "VA" => "Holy See (Vatican City State)",
    "HN" => "Honduras",
    "HK" => "Hong Kong",
    "HU" => "Hungary",
    "IS" => "Iceland",
    "IN" => "India",
    "ID" => "Indonesia",
    "IR" => "Iran, Islamic Republic of",
    "IQ" => "Iraq",
    "IE" => "Ireland",
    "IM" => "Isle of Man",
    "IL" => "Israel",
    "IT" => "Italy",
    "JM" => "Jamaica",
    "JP" => "Japan",
    "JE" => "Jersey",
    "JO" => "Jordan",
    "KZ" => "Kazakhstan",
    "KE" => "Kenya",
    "KI" => "Kiribati",
    "KP" => "Korea, Democratic People's Republic of",
    "KR" => "Korea, Republic of",
    "KW" => "Kuwait",
    "KG" => "Kyrgyzstan",
    "LA" => "Lao People's Democratic Republic",
    "LV" => "Latvia",
    "LB" => "Lebanon",
    "LS" => "Lesotho",
    "LR" => "Liberia",
    "LY" => "Libyan Arab Jamahiriya",
    "LI" => "Liechtenstein",
    "LT" => "Lithuania",
    "LU" => "Luxembourg",
    "MO" => "Macao",
    "MK" => "Macedonia, The Former Yugoslav Republic of",
    "MG" => "Madagascar",
    "MW" => "Malawi",
    "MY" => "Malaysia",
    "MV" => "Maldives",
    "ML" => "Mali",
    "MT" => "Malta",
    "MH" => "Marshall Islands",
    "MQ" => "Martinique",
    "MR" => "Mauritania",
    "MU" => "Mauritius",
    "YT" => "Mayotte",
    "MX" => "Mexico",
    "FM" => "Micronesia, Federated States of",
    "MD" => "Moldova, Republic of",
    "MC" => "Monaco",
    "MN" => "Mongolia",
    "ME" => "Montenegro",
    "MS" => "Montserrat",
    "MA" => "Morocco",
    "MZ" => "Mozambique",
    "MM" => "Myanmar",
    "NA" => "Namibia",
    "NR" => "Nauru",
    "NP" => "Nepal",
    "NL" => "Netherlands",
    "AN" => "Netherlands Antilles",
    "NC" => "New Caledonia",
    "NZ" => "New Zealand",
    "NI" => "Nicaragua",
    "NE" => "Niger",
    "NG" => "Nigeria",
    "NU" => "Niue",
    "NF" => "Norfolk Island",
    "MP" => "Northern Mariana Islands",
    "NO" => "Norway",
    "OM" => "Oman",
    "PK" => "Pakistan",
    "PW" => "Palau",
    "PS" => "Palestinian Territory, Occupied",
    "PA" => "Panama",
    "PG" => "Papua New Guinea",
    "PY" => "Paraguay",
    "PE" => "Peru",
    "PH" => "Philippines",
    "PN" => "Pitcairn",
    "PL" => "Poland",
    "PT" => "Portugal",
    "PR" => "Puerto Rico",
    "QA" => "Qatar",
    "RE" => "Reunion",
    "RO" => "Romania",
    "RU" => "Russian Federation",
    "RW" => "Rwanda",
    "SH" => "Saint Helena",
    "KN" => "Saint Kitts and Nevis",
    "LC" => "Saint Lucia",
    "PM" => "Saint Pierre and Miquelon",
    "VC" => "Saint Vincent and The Grenadines",
    "WS" => "Samoa",
    "SM" => "San Marino",
    "ST" => "Sao Tome and Principe",
    "SA" => "Saudi Arabia",
    "SN" => "Senegal",
    "RS" => "Serbia",
    "SC" => "Seychelles",
    "SL" => "Sierra Leone",
    "SG" => "Singapore",
    "SK" => "Slovakia",
    "SI" => "Slovenia",
    "SB" => "Solomon Islands",
    "SO" => "Somalia",
    "ZA" => "South Africa",
    "GS" => "South Georgia and The South Sandwich Islands",
    "ES" => "Spain",
    "LK" => "Sri Lanka",
    "SD" => "Sudan",
    "SR" => "Suriname",
    "SJ" => "Svalbard and Jan Mayen",
    "SZ" => "Swaziland",
    "SE" => "Sweden",
    "CH" => "Switzerland",
    "SY" => "Syrian Arab Republic",
    "TW" => "Taiwan, Province of China",
    "TJ" => "Tajikistan",
    "TZ" => "Tanzania, United Republic of",
    "TH" => "Thailand",
    "TL" => "Timor-leste",
    "TG" => "Togo",
    "TK" => "Tokelau",
    "TO" => "Tonga",
    "TT" => "Trinidad and Tobago",
    "TN" => "Tunisia",
    "TR" => "Turkey",
    "TM" => "Turkmenistan",
    "TC" => "Turks and Caicos Islands",
    "TV" => "Tuvalu",
    "UG" => "Uganda",
    "UA" => "Ukraine",
    "AE" => "United Arab Emirates",
    "GB" => "United Kingdom",
    "US" => "United States",
    "UM" => "United States Minor Outlying Islands",
    "UY" => "Uruguay",
    "UZ" => "Uzbekistan",
    "VU" => "Vanuatu",
    "VE" => "Venezuela",
    "VN" => "Viet Nam",
    "VG" => "Virgin Islands, British",
    "VI" => "Virgin Islands, U.S.",
    "WF" => "Wallis and Futuna",
    "EH" => "Western Sahara",
    "YE" => "Yemen",
    "ZM" => "Zambia",
    "ZW" => "Zimbabwe"
  );
}*/

$identification_type = [
  'singapore_nric_no' => 'Singapore NRIC No',  
  'singapore_pr_no' => 'Singapore PR No',
  'foreign_identification_number' => 'Foreign Identification Number',
  'empl' => 'Employment Pass',
  'work_permit' => 'Work Permit'
];

$marketing_type_id = [
  1 => 'Yellow Pages', 2 => 'Walk-in', 3 => 'Internet'
];

$gender = [
  'MALE' => 'Male', 'FEMALE' => 'Female'
];

$spoken_language = [
  'english' => 'English', 'china' => 'Chinese', 'malaysia' => 'Malay', 'tamil' => 'Tamil'
];

$marital_status = [
  'single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced', 'separated' => 'Separated', 'others' => 'Others'
];

$reason_for_loan = ['personal_loan' => 'Personal'];
$positions = [
  'director/gm' => 'Director / GM', 'senior-manager' => 'Senior Manager', 'manager/assistant' => 'Manager / Assitant Manager',
  'supervisor' => 'Supervisor', 'senior-executive' => 'Senior Executive', 'junior-executive' => 'Junior Manager',
  'non-executive' => 'Non-Executive', 'professional' => 'Professional', 'self-employed' => 'Self Employed', 'others' => 'Others'
];
//-------------------------------------------------------
function convertToArray($obj)
{
  $new_array = [];

  foreach ($obj as $id => $item) {
    $field = (array)$item;
    $key = array_keys($field);
    $new_array[$key[0]] = $field[$key[0]];
  }

  return $new_array;
}

function listDropDownCountries($ctrl_name, $code = 'SG', $requried = '', $isCountry = 0, $class = '', $source = [])
{
  $countries_list = (!$source) ? mc_countries() : $source;
  //find value on array
  $code_id = 0;
  $countries = [];
  $temp = (isset($countries_list->data)) ? $countries_list->data : [];
  //sort country
  asort($temp);
  
  foreach ( $temp as $item) {
    $code_id = ($item->iso === $code) ? $item->id : $code_id;
    $text = ($ctrl_name == 'country' || $isCountry == 1) ? $item->nicename : $item->nationality;
    $text = (!$text) ? $item->nicename : $text;
    $countries[$item->id] = $text;
  }

  $code_id = (gettype($code) === 'integer') ? $code : $code_id;
  return create_select_control($countries, $ctrl_name, $code_id, $requried, $class);
}

function drop_down_countries($countries, $ctrl_name, $code = 'SG', $requried = '', $isCountry = 0, $class = '')
{
  $countries_list = $countries;
  //find value on array
  $code_id = 0;
  $countries = [];
  $temp = (isset($countries_list->data)) ? $countries_list->data : [];
  //sort country
  asort($temp);
  
  foreach ( $temp as $item) {
    $code_id = ($item->iso === $code) ? $item->id : $code_id;
    $text = ($ctrl_name == 'country' || $isCountry == 1) ? $item->nicename : $item->nationality;
    $text = (!$text) ? $item->nicename : $text;
    $countries[$item->id] = $text;
  }

  $code_id = (gettype($code) === 'integer') ? $code : $code_id;
  return create_select_control($countries, $ctrl_name, $code_id, $requried, $class);
}

function list_countries($isCountry = 1, $code = '')
{
  $countries_list = mc_countries();
  //find value on array
  $countries = [];
  $temp = (isset($countries_list->data)) ? $countries_list->data : [];
  //sort country
  asort($temp);
  
  foreach ( $temp as $item) {
    $text = ($isCountry == 1) ? $item->nicename : $item->nationality;
    $text = (!$text) ? $item->nicename : $text;
    $countries[$item->id] = $text;
  }

  return $countries;
}

function create_select_control($options, $ctrl_name, $selected, $requried = '', $class = '')
{
?>
  <select name="<?php print $ctrl_name; ?>" id="<?php print $ctrl_name; ?>" class="form-control form-select <?php print $class; ?>" <?php print $requried; ?>>
    <option value=""></option>
    <?php
    foreach ($options as $key => $value) {
      $sl = '';
      if( is_numeric($selected)) {
        $sl = ( (int)$selected == (int)$key ) ? 'selected="selected"' : '';
      } else {
        $sl = ( $selected == $key) ? 'selected="selected"' : '';
      }
    ?>
      <option <?php print $sl; ?> value="<?= $key ?>" title="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($value) ?></option>
    <?php } ?>
  </select>
<?php }

function housing_type() {
  return [
    111 => ['text' => '1-Room Flat (HDB)', 'type' => 'HDB'],
    112 => ['text' => '2-Room Flat (HDB)', 'type' => 'HDB'],
    113 => ['text' => '3-Room Flat (HDB)', 'type' => 'HDB'],
    114 => ['text' => '4-Room Flat (HDB)', 'type' => 'HDB'],
    115 => ['text' => '5-Room Flat (HDB)', 'type' => 'HDB'],
    116 => ['text' => 'Executive Flat (HDB)', 'type' => 'HDB'],
    117 => ['text' => 'Housing and Urban Development Company (HUDC) Flat (excluding those privatized)', 'type' => 'HDB'],
    118 => ['text' => 'Studio Apartment (HDB)', 'type' => 'HDB'],
    121 => ['text' => 'Bungalow', 'type' => 'PR'], //Private Residential
    122 => ['text' => 'Semi-Detached House', 'type' => 'PR'],
    123 => ['text' => 'Terrace House', 'type' => 'PR'],
    131 => ['text' => 'Condominium', 'type' => 'PR'],
    132 => ['text' => 'Executive Condominium', 'type' => 'PR'],
    139 => ['text' => 'Other Apartments nec', 'type' => 'PR'],
    141 => ['text' => 'Shophouse', 'type' => 'PR'],
    149 => ['text' => 'Other Housing Units nec', 'type' => 'PR'],
  ];
}

function housingType($name, $code, $class, $requried = '')
{
  $type = housing_type();

?>
 <select name="<?php print $name; ?>" id="<?php print $name; ?>" 
  class="form-control form-select <?php print $class; ?>" <?php print $requried; ?>>
    <option value="" data-type="Blank"></option>
    <?php
    foreach ($type as $key => $item) {
      $sl = ($code == $item) ? 'selected="selected"' : '';
      $class__ = ($item['type'] !== 'PR') ? '' : 'd-none';
    ?>
      <option <?php print $sl; ?> value="<?= $key ?>" data-type="<?= $item['type'];?>" class="<?= $class__; ?>" 
        title="<?= htmlspecialchars($item['text']);?>"><?= htmlspecialchars($item['text']) ?></option>
    <?php } ?>
  </select>
<?php
  //return create_select_control($type, $name, $code, $required);
}

function getBase64FileSize($base64String)
{
  // Remove the data URL scheme prefix if present
  $base64String = preg_replace('/^data:\w+\/\w+;base64,/', '', $base64String);

  // Calculate the size of the binary data
  $binaryData = base64_decode($base64String);
  $fileSize = strlen($binaryData);

  return round($fileSize / 1024, 2);
}

function getOrdinalSuffix($num)
{
  if( !$num || !is_numeric($num) ) return '';

  $j = $num % 10;
  $k = $num % 100;

  if ($j == 1 && $k != 11) {
    return $num . "st";
  }
  if ($j == 2 && $k != 12) {
    return $num . "nd";
  }
  if ($j == 3 && $k != 13) {
    return $num . "rd";
  }
  return $num . "th";
}

function formatMoneyLocale($amount, $currency = 'USD', $locale = 'en_US') {
  $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
  return $formatter->formatCurrency($amount, $currency);
}
?>