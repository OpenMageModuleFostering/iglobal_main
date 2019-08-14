<?php

class Iglobal_Stores_AjaxController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{

		echo Mage::getBaseUrl();
	    $this->loadLayout();
	    $this->renderLayout();
	}

	public function icedataAction () {

		//storeID for ice 
		$iceData = array ();
		 if  (Mage::getStoreConfig('iglobal_integration/apireqs/iglobalid')) {
			$iceData['storeId'] = Mage::getStoreConfig('iglobal_integration/apireqs/iglobalid');
		 }
		 //subdomain for iCE
		 if  (Mage::getStoreConfig('iglobal_integration/apireqs/igsubdomain')) {
			$iceData['subdomain'] = Mage::getStoreConfig('iglobal_integration/apireqs/igsubdomain');
		 }

		//Cart Url for redirects
		$iceData['cartUrl'] = Mage::getUrl('checkout/cart');
		//echo $iceData['cartUrl'];

		//Zend_Debug::dump($iceData);
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($iceData));

	}

	public function matdataAction () {
	
		$matData = array ();
		//storeID for mat
		if  (Mage::getStoreConfig('iglobal_integration/apireqs/iglobalid')) {
			$matData['storeId'] = Mage::getStoreConfig('iglobal_integration/apireqs/iglobalid');
		 }
		
		//flag placement parent
		if  (Mage::getStoreConfig('iglobal_integration/igmat/flag_parent')) {
			$matData['flag_parent'] = Mage::getStoreConfig('iglobal_integration/igmat/flag_parent');
		 }
		 
		// TODO: flag placement method		
		if  (Mage::getStoreConfig('iglobal_integration/igmat/flag_method')) {
			$matData['flag_method'] = Mage::getStoreConfig('iglobal_integration/igmat/flag_method');
		 }
		
		// TODO: flag placement code		
		if  (Mage::getStoreConfig('iglobal_integration/igmat/flag_code')) {
			$matData['flag_method'] = Mage::getStoreConfig('iglobal_integration/igmat/flag_code');
		 }
		 
		//figure out what countries are serviced
		if (Mage::getStoreConfig('general/country/allow')) {
			//define iGlobal serviced countries
			$allCountries = array("AP"=>"APO/FPO","AF"=>"Afghanistan","AL"=>"Albania","DZ"=>"Algeria","AS"=>"American Samoa","AD"=>"Andorra","AO"=>"Angola","AI"=>"Anguilla","AG"=>"Antigua","AR"=>"Argentina","AM"=>"Armenia","AW"=>"Aruba","AU"=>"Australia","AT"=>"Austria","AZ"=>"Azerbaijan","BS"=>"Bahamas","BH"=>"Bahrain","BD"=>"Bangladesh","BB"=>"Barbados","BY"=>"Belarus","BE"=>"Belgium","BZ"=>"Belize","BJ"=>"Benin","BM"=>"Bermuda","BT"=>"Bhutan","BO"=>"Bolivia","BQ"=>"Bonaire","BA"=>"Bosnia & Herzegovina","BW"=>"Botswana","BR"=>"Brazil","VG"=>"Virgin Islands (British)","BN"=>"Brunei","BG"=>"Bulgaria","BF"=>"Burkina Faso","BI"=>"Burundi","KH"=>"Cambodia","CM"=>"Cameroon","CA"=>"Canada","IC"=>"Canary Islands","CV"=>"Cape Verde","KY"=>"Cayman Islands","CF"=>"Central African Republic","TD"=>"Chad","CL"=>"Chile","CN"=>"China - People's Republic of","CO"=>"Colombia","KM"=>"Comoros","CG"=>"Congo","CK"=>"Cook Islands","CR"=>"Costa Rica","HR"=>"Croatia","CW"=>"Curaçao","CY"=>"Cyprus","CZ"=>"Czech Republic","DK"=>"Denmark","DJ"=>"Djibouti","DM"=>"Dominica","DO"=>"Dominican Republic","TL"=>"Timor-Leste","EC"=>"Ecuador","EG"=>"Egypt","SV"=>"El Salvador","GQ"=>"Equatorial Guinea","ER"=>"Eritrea","EE"=>"Estonia","ET"=>"Ethiopia","FK"=>"Falkland Islands","FO"=>"Faroe Islands (Denmark)","FJ"=>"Fiji","FI"=>"Finland","FR"=>"France","GF"=>"French Guiana","GA"=>"Gabon","GM"=>"Gambia","GE"=>"Georgia","DE"=>"Germany","GI"=>"Gibraltar","GR"=>"Greece","GL"=>"Greenland (Denmark)","GD"=>"Grenada","GP"=>"Guadeloupe","GU"=>"Guam","GH"=>"Ghana","GT"=>"Guatemala","GG"=>"Guernsey","GN"=>"Guinea","GW"=>"Guinea-Bissau","GY"=>"Guyana","HT"=>"Haiti","HN"=>"Honduras","HK"=>"Hong Kong","HU"=>"Hungary","IS"=>"Iceland","IN"=>"India","ID"=>"Indonesia","IQ"=>"Iraq","IE"=>"Ireland - Republic Of","IL"=>"Israel","IT"=>"Italy","CI"=>"Ivory Coast","JM"=>"Jamaica","JP"=>"Japan","JE"=>"Jersey","JO"=>"Jordan","KZ"=>"Kazakhstan","KE"=>"Kenya","KI"=>"Kiribati","KR"=>"Korea","KW"=>"Kuwait","KG"=>"Kyrgyzstan","LA"=>"Laos","LV"=>"Latvia","LB"=>"Lebanon","LS"=>"Lesotho","LR"=>"Liberia","LT"=>"Lithuania","LI"=>"Liechtenstein","LU"=>"Luxembourg","MO"=>"Macau","MK"=>"Macedonia","MG"=>"Madagascar","MW"=>"Malawi","MY"=>"Malaysia","MV"=>"Maldives","ML"=>"Mali","MT"=>"Malta","MH"=>"Marshall Islands","MQ"=>"Martinique","MR"=>"Mauritania","MU"=>"Mauritius","YT"=>"Mayotte","MX"=>"Mexico","MD"=>"Moldova","FM"=>"Micronesia - Federated States of","MC"=>"Monaco","MN"=>"Mongolia","ME"=>"Montenegro","MS"=>"Montserrat","MA"=>"Morocco","MZ"=>"Mozambique","MM"=>"Myanmar","NA"=>"Namibia","NR"=>"Nauru","NP"=>"Nepal","NL"=>"Netherlands (Holland)","NV"=>"Nevis","NC"=>"New Caledonia","NZ"=>"New Zealand","NI"=>"Nicaragua","NE"=>"Niger","NG"=>"Nigeria","NU"=>"Niue Island","NF"=>"Norfolk Island","MP"=>"Northern Mariana Islands","NO"=>"Norway","OM"=>"Oman","PK"=>"Pakistan","PA"=>"Panama","PG"=>"Papua New Guinea","PY"=>"Paraguay","PW"=>"Palau","PE"=>"Peru","PH"=>"Philippines","PL"=>"Poland","PT"=>"Portugal","PR"=>"Puerto Rico","QA"=>"Qatar","RE"=>"Reunion","RO"=>"Romania","RU"=>"Russia","RW"=>"Rwanda","SM"=>"San Marino","ST"=>"Sao Tome & Principe","SA"=>"Saudi Arabia","SN"=>"Senegal","RS"=>"Serbia & Montenegro","SC"=>"Seychelles","SL"=>"Sierra Leone","SG"=>"Singapore","SK"=>"Slovakia","SI"=>"Slovenia","SB"=>"Solomon Islands","ZA"=>"South Africa","SS"=>"South Sudan","ES"=>"Spain","LK"=>"Sri Lanka","BL"=>"St. Barthelemy","EU"=>"St. Eustatius","KN"=>"St. Kitts and Nevis","LC"=>"St. Lucia","MF"=>"St. Maarten","VC"=>"St. Vincent","SD"=>"Sudan","SR"=>"Suriname","SZ"=>"Swaziland","SE"=>"Sweden","CH"=>"Switzerland","PF"=>"Tahiti","TW"=>"Taiwan","TJ"=>"Tajikistan","TZ"=>"Tanzania","TH"=>"Thailand","TG"=>"Togo","TO"=>"Tonga","TT"=>"Trinidad and Tobago","TN"=>"Tunisia","TR"=>"Turkey","TM"=>"Turkmenistan","TC"=>"Turks and Caicos Islands","TV"=>"Tuvalu","VI"=>"Virgin Islands (U.S.)","UG"=>"Uganda","UA"=>"Ukraine","AE"=>"United Arab Emirates","GB"=>"United Kingdom","US"=>"United States","UY"=>"Uruguay","UZ"=>"Uzbekistan","VU"=>"Vanuatu","VE"=>"Venezuela","VN"=>"Vietnam","VA"=>"Vatican City","WS"=>"Western Samoa","YE"=>"Yemen","ZM"=>"Zambia","ZW"=>"Zimbabwe");
					
			//allowed country list
			$allowedCountries = Mage::getStoreConfig('general/country/allow');
			$allowedCountries = array_flip(explode("," , $allowedCountries));
			
			//countries that are both allowed and iGlobal available
			$servicedCountries = array_intersect_key ($allCountries, $allowedCountries);
			$matData['servicedCountries'] = $servicedCountries;
		}
		
		//domestic country list
		if  (Mage::getStoreConfig('general/country/ig_domestic_countries')) {
			$matData['domesticCountries'] =Mage::getStoreConfig('general/country/ig_domestic_countries');
		 }
		
		
		//store logo url
		if  (Mage::getStoreConfig('iglobal_integration/igmat/store_logo')) {
			$matData['storeLogo'] = Mage::getStoreConfig('iglobal_integration/igmat/store_logo');
		 }
		 
		//Zend_Debug::dump($matData);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($matData));
		
	}
}