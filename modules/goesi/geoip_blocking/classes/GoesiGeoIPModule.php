<?php
/***************************************************************************
*
*                            GeoIP Blocker
*                      
*     copyright            : (C) 2014 Sven Goessling / SmileAndGo.de
*     website              : http://www.sven-goessling.de
*
*     IMPORTANT: This is a commercial product made by Sven Goessling and cannot be modified for other than personal usage. 
*     This product cannot be redistributed for free or redistribute it and/or modify without written permission from Sven Goessling. 
*     This notice may not be removed from the source code.
*     See license.txt file; if not, write to sven@sven-goessling.de 
*
***************************************************************************/

bx_import('BxDolModule');

class GoesiGeoIPModule extends BxDolModule {

    function GoesiGeoIPModule(&$aModule) {        
        parent::BxDolModule($aModule);
    }
///////////////////////////////////////////////
    //Admin Bereich
    function actionAdministration () {

        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();
	
	$cId = $this->_oDb->getSettingsCategory(); 
	if(empty($cId)) { // if category is not found display page not found
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_goesi_geoip_blocker'));
            return;
        }
        
// Functions
	require_once('GoesiGeoIPOptions.php');

    //var Bilderpfad = "'.BX_DOL_URL_MODULES.'goesi/picture_seller/templates/base/images/icons/";
	//var PostDir = "'.BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri().'administration/";
	
	$geoip_status = '<span style="color:green;">PHP Mode is ON - Check over Remote Server</span><br><span style="color:red;">Server Module GEO IP doesn\'t exist. <br>DONT USE THE MODULE IN HTACCESS LIVE MODE.</span>';
	$GEOIP_COUNTRY_CODE = '';
	//
	if (isset($_SERVER["GEOIP_COUNTRY_CODE"]) ){
		$GEOIP_COUNTRY_CODE = $_SERVER["GEOIP_COUNTRY_CODE"];
		$geoip_status = '<span style="color:green;">Server Module GEO IP exist.<br>Your Location is: '.$GEOIP_COUNTRY_CODE.' - '.$_SERVER["GEOIP_COUNTRY_NAME"].'</span>';
		$this->_oDb->setMod_GeoIP(1);
	} else {
		$this->_oDb->setMod_GeoIP(0);
	}
	
	$this->_oDb->setServer_url();
	$list_countries = hole_laender($this->_oDb->getAllCountries(), $this->_oDb->getMode(), $GEOIP_COUNTRY_CODE);
	
		bx_import('BxDolAdminSettings'); // import class
		
        $mixedResult = '';
		$block_on = '';
		$allow_on = '';
		$modus = $this->_oDb->getMode();
		if ($modus == "block") {
			$block_on = 'selected="selected"';
			$allow_on = 'autocomplete="off"';
		} else if ($modus == "allow") {
			$allow_on = 'selected="selected"';
			$block_on = 'autocomplete="off"';
		}
		$errors = $this->_oDb->getLastError();
		
        if(isset($_POST['save']) && isset($_POST['cat'])) { 
			
			//Settings
			if(isset($_POST['websiteuser'])) {
				$this->_oDb->setBoonex_user(addslashes($_POST['websiteuser']));
			}
			if(isset($_POST['websitetoken'])) {
				$this->_oDb->setServer_token(addslashes($_POST['websitetoken']));
			}
			//
			
			if (isset($_POST['use']) && $_POST['use'] != "") {
				(($_POST['use'] == 'yes')?$this->_oDb->setGeoIPEnable(1):$this->_oDb->setGeoIPEnable(0));
			}
			if (isset($_POST['mode']) && $_POST['mode'] != "") {
				if ($_POST['mode'] == 'block') {
					$this->_oDb->setModus('block');
					$modus = 'block';
				} else {
					$this->_oDb->setModus('allow');
					$modus = 'allow';
				}
			}
			
			if(isset($_POST['land']) && is_array($_POST['land'])) {
				$this->_oDb->cleanCountries($modus);	
				foreach ($_POST['land'] as $land) {
					$this->_oDb->setCountry($land, $modus);
				}
			}
			
			//Create .htaccess here
			create_htaccess($this, $_POST['use'], $_POST['mode']);
			//
			
            $oSettings = new BxDolAdminSettings($cId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }
		
		//
		//Remote GeoIP-Check
		$remote_status = 'Not used';
		if (file_exists(__DIR__. '/../data/check.php')) {
			include(__DIR__ . '/../data/check.php');
			if ($mod_geoip == 0) {
			$remote_status = 'Your IP: '.$response['IP'].'<br>';
			$remote_status .= 'Your Country: '.$response['country_name'].'<br>';
			$remote_status .= 'Your User Mode: '.$response['GuestMode'].'<br>';
			$remote_status .= 'Your Remaining Requests: '.$response['RemainingRequests'].(($response['RemainingRequests'] == "ZERO")?'<br><br><span style="color:darkred;"><b> - YOUR SITE IS OPEN FOR ALL REQUESTS -<br> - Upgrade your licence to become unlimited requests - </b></span><br>':'').'<br>';
			$remote_status .= 'Your Remaining Time Periode Ends: '.$response['End_Time_Periode'].'<br>';
			}
		}
		
		//Remote GeoIP-Check
		//
		
		$aVars = array (
       'module_url' 	=> BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri(),
	   'test_url' => BX_DOL_URL_ROOT . 'modules/goesi/geoip_blocking/test_folder/info.php',
	   'mod_geoip_installed'  => $geoip_status,
	   'list_countries' => $list_countries,
	   'errors' => $errors,
	   'block_on' => $block_on,
	   'allow_on' => $allow_on,
	   'server-name' => (($_SERVER['SERVER_NAME'] != "")?$_SERVER['SERVER_NAME']:$_SERVER['HTTP_HOST']),
	   'website-username' => $this->_oDb->getBoonex_user(),
	   'server-token' => $this->_oDb->getServer_token(),
	   'remote-status' => $remote_status,
        );

        $oSettings = new BxDolAdminSettings($cId); 
        $sResult = $oSettings->getForm();
                   
        if($mixedResult !== true && !empty($mixedResult)) 
            $sResult = $mixedResult . $sResult . header("Location: " . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .'administration/');

	$sContent .= '<meta http-equiv="cache-control" content="no-cache">';
	$sContent .= $this->_oTemplate->parseHtmlByName ('admin', $aVars);


        echo $this->_oTemplate->adminBlock ($sContent, _t('_goesi_geoip_blocker'));

        echo DesignBoxAdmin (_t('_goesi_geoip_blocker'), $sResult);
        $this->_oTemplate->pageCodeAdmin (_t('_goesi_geoip_blocker'));
    }

///////////////////////////////////////////////


///////////////////////////////////////////////
//User Bereich
    function actionHome () {
	//Nothing to see here
		return;
    }

///////////////////////////////////////////////
}

?>
