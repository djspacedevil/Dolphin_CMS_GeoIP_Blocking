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

//Functions

function hole_laender($countries, $mode, $admin_country) {
	$alle_laender = '<center>No Country found.</center>';
	if(is_array($countries)) {
		$alle_laender = '<center><table style="width:90%;"><tr>';
		$count = 1;
		foreach ($countries as $country) {
			if ($count == 1) {$alle_laender .= '<tr>';}
			$alle_laender .= '<td>
								<input type="checkbox" name="land['.$country['iso_code'].']" value="'.$country['iso_code'].'"'.
										(($mode == 'allow' && $country['allow'] == 1)?'checked="checked"':'autocomplete="off"'). 
										(($mode == 'block' && $country['block'] == 1)?'checked="checked"':'autocomplete="off"').
										
										'>
							  </td>
							  <td>
								'.$country['name'].'
							  </td>';
			if ($count == 3) {$alle_laender .= '</tr>'; $count = 0;}					  
			$count++;				  
		}
		$alle_laender .= '</tr><table></center>';
	}
	return $alle_laender;
}

function create_htaccess($mod, $use, $mode) {
	if ($use == 'test') {
		$htaccess_file = __DIR__ . '/../test_folder/.htaccess';
	} else if ($use == 'yes' || $use == 'no') {
		$htaccess_file = substr(__DIR__,0, -37) . '/.htaccess';
	}
	
		chmod($htaccess_file, 0646);
		if(file_exists($htaccess_file)) {
		
		$htaccess = file_get_contents($htaccess_file);
		$htaccess = explode('##Goesi_Country_Blocking', $htaccess);
		$countries_block = $mod->_oDb->getAllCountriesByRule('block');
		$countries_allow = $mod->_oDb->getAllCountriesByRule('allow');
		//#GeoIPEnable On
		//#GeoIPDBFile '.$_SERVER["DOCUMENT_ROOT"].'/modules/goesi/geoip_blocking/data/GeoIP.dat
		$new_roles .= '##Goesi_Country_Blocking
<IfModule mod_geoip.c>
';
	foreach ($countries_block as $country) {
	$new_roles .= 'SetEnvIf GEOIP_COUNTRY_CODE '.$country['iso_code'].' BlockCountry
';
	}
	foreach ($countries_allow as $country) {
	$new_roles .= 'SetEnvIf GEOIP_COUNTRY_CODE '.$country['iso_code'].' AllCountry
';
	}
	
	$new_roles .= 'deny from env=BlockCountry
allow from env=AllCountry
</IfModule>
##Goesi_Country_Blocking';

		if ($use != 'no') {
		$new_htaccess = $htaccess['0'].$new_roles.$htaccess['2'];
		} else if ($use == 'no') {
		$new_htaccess = $htaccess['0'].$htaccess['2'];
		}
		
		if (@file_put_contents($htaccess_file, $new_htaccess) === false) {
			$mod->_oDb->setLastError('Can not save .htaccess, please set the user/group to the default site user or set chmod 0646.');
			return false;
		} else {
			$mod->_oDb->setLastError('');
			chmod($htaccess_file, 0644);
			return true;
		}
		//unlink(__DIR__ . '/../test_folder/.htaccess');
		
		} else {
			return false;
		}
	
	return false;
}
?>