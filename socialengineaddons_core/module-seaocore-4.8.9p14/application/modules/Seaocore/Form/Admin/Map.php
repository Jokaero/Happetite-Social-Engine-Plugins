<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Seaocore_Form_Admin_Map extends Engine_Form
{
	public function init() {
	    //GENERAL HEADING
    $this
            ->setTitle('Google Maps Settings')
            ->setDescription('Here, you can customize the settings for Google Maps on your site. (Note: These settings will only affect the Google Maps that comes in plugins from SocialEngineAddOns installed on your site.)');
            
    //ENTER THE GOOGLE MAP API KEY
    $this->addElement('Text', 'seaocore_google_map_key', array(
        'label' => 'Google Places API Key',
        'description' => 'The Google Places API Key for your website. [Please visit the "Guidelines for configuring Google Places API key" mentioned   above to see how to obtain these credentials.]',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.google.map.key'),
        //'required' => true
    ));

    $this->getElement('seaocore_google_map_key')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    //COLOR VALUE FOR BACKGROUND COLOR
    $this->addElement('Text', 'seaocore_tooltip_bgcolor', array(
			'decorators' => array(
				array('ViewScript', array(
					'viewScript' => 'admin-settings/rainbow-color/_formImagerainbowTooltipBg.tpl',
					'class' => 'form element'
				)
		  ))
    ));
    
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    
    if(Engine_Api::_()->seaocore()->getLocationsTabs()) {

        $this->addElement('Radio', 'seaocore_locationspecific', array(
            'label' => 'Enable Specific Locations',
            'description' => 'Do you want to enable specific locations functionality. If you select "Yes", then user`s can filter the content according to the locations added by you. You can add specific locations from "Manage Locations" section.',
            'multioptions' => array(1 => 'Yes', 0 => 'No'),
            'value' => $coreSettings->getSetting('seaocore.locationspecific', 0),
            'onclick' => 'showLocationSpecific(this.value)',
        ));
        
        $this->addElement('Radio', 'seaocore_locationspecificcontent', array(
            'label' => 'Enable specific locations while content creation',
            'description' => 'Do you want to enable the specific locations while content creation. By enabling this setting users can only choose locations with various content, which are added by you in "Manage Locations" section. (Note: These settings will only affect the location field that comes in plugins from SocialEngineAddOns installed on your site.)',
            'multioptions' => array(1 => 'Yes', 0 => 'No'),
            'value' => $coreSettings->getSetting('seaocore.locationspecificcontent', 0),
        ));        
        
        $locations = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getLocations(array('status' => 1));
        $locationsArray = array();
        $locationsArray[0] = '';
        foreach($locations as $location) {
            $locationsArray[$location->location] = $location->title;
        }
        $this->addElement('Select', 'seaocore_locationdefaultspecific', array(
            'label' => 'Default Location for Content Searching',
            //'description' => 'Which location you want to display by default for searching contents.',
            'multioptions' => $locationsArray,
            'value' => $coreSettings->getSetting('seaocore.locationdefaultspecific', ''),
            'onchange' => 'setLocationDefault(this.value)',
        ));        
        //$this->seaocore_locationdefaultspecific->getDecorator('Description')->setOption('placement', 'append');
        
        $this->addElement('Text', 'seaocore_locationdefault', array(
            'label' => 'Default Location for Content Searching',
            //'description' => 'Which location you want to display by default for searching contents.',
            'value' => $coreSettings->getSetting('seaocore.locationdefault', ''),
        ));
        //$this->seaocore_locationdefault->getDecorator('Description')->setOption('placement', 'append');

        $locationOption = array(
            '0' => '',
            '1' => '1',
            '2' => '2',
            '5' => '5',
            '10' => '10',
            '20' => '20',
            '50' => '50',
            '100' => '100',
            '250' => '250',
            '500' => '500',
            '750' => '750',
            '1000' => '1000',
        );        

        $this->addElement('Select', 'seaocore_locationdefaultmiles', array(
            'label' => 'Default Value for Miles / Kilometers',
            'multiOptions' => $locationOption,
            'value' => $coreSettings->getSetting('seaocore.locationdefaultmiles', 0),
            'disableTranslator' => 'true'
        ));    
        
        $countries = array
        (
        '' => 'Select Country',
        'AF' => 'Afghanistan',
        'AX' => 'Aland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua And Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia And Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo',
        'CD' => 'Congo, Democratic Republic',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Cote D\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FK' => 'Falkland Islands (Malvinas)',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island & Mcdonald Islands',
        'VA' => 'Holy See (Vatican City State)',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran, Islamic Republic Of',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle Of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KR' => 'Korea',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Lao People\'s Democratic Republic',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libyan Arab Jamahiriya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia, Federated States Of',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'AN' => 'Netherlands Antilles',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestinian Territory, Occupied',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barthelemy',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts And Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin',
        'PM' => 'Saint Pierre And Miquelon',
        'VC' => 'Saint Vincent And Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome And Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia And Sandwich Isl.',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard And Jan Mayen',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad And Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks And Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'UM' => 'United States Outlying Islands',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Viet Nam',
        'VG' => 'Virgin Islands, British',
        'VI' => 'Virgin Islands, U.S.',
        'WF' => 'Wallis And Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
        );
        
        $this->addElement('Select', 'seaocore_countrycities', array(
            'label' => 'Select Country',
            'description' => "Which country's cities you want to suggest in city search field. [Note: This setting is useful if city based searching is available in module.]",
            'multiOptions' => $countries,
            'value' => $coreSettings->getSetting('seaocore.countrycities'),
        ));            
        $this->seaocore_countrycities->getDecorator('Description')->setOption('placement', 'append');
        
        $this->addElement('Radio', 'seaocore_locationspecificorder', array(
            'label' => 'Sorting Criteria For Specific Locations',
            'description' => 'Choose the sorting criteria for displaying specific locations.',
            'multioptions' => array(
                'locationcontent_id' => 'Ascending order of creation', 
                'title' => 'Alphabetical order'
            ),
            'value' => $coreSettings->getSetting('seaocore.locationspecificorder', 'locationcontent_id'),
        ));         
    }     
    
    //SUBMIT BUTTON
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }
}