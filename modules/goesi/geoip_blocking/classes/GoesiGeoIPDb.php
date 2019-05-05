<?
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

bx_import('BxDolModuleDb');

class GoesiGeoIPDb extends BxDolModuleDb {

	function GoesiGeoIPDb(&$oConfig) {
		parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }
    
   function getSettingsCategory() {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'GeoIP Blocker' LIMIT 1");
    }
	
	function getAllCountries() {
		return $this->getAll("SELECT * FROM `goesi_geoip_countries`");
	}
	
	function getAllCountriesByRule($value) {
		return $this->getAll("SELECT * FROM `goesi_geoip_countries` WHERE `".$value."` = 1");
	}
	
	function getMode() {
		return $this->getOne("SELECT `mode` FROM `goesi_geoip_config` WHERE id = 1");
	}
	
	function setMod_GeoIP($value) {
		return $this->query("UPDATE `goesi_geoip_config` SET `mod_geoip` = ".$value." WHERE id = 1");
	}
	
	function setGeoIPEnable($value) {
		return $this->query("UPDATE `goesi_geoip_config` SET `GeoIPEnable` = ".$value." WHERE id = 1");
	}
	
	function setModus($value) {
		return $this->query("UPDATE `goesi_geoip_config` SET `mode` = '".$value."' WHERE id = 1");
	}
	
	function cleanCountries($mode) {
		if ($mode == 'block') return $this->query("UPDATE `goesi_geoip_countries` SET `block` = 0, `allow` = 1");
		if ($mode == 'allow') return $this->query("UPDATE `goesi_geoip_countries` SET `block` = 1, `allow` = 0");
	}
	
	function setCountry($land, $mode) {
		if ($mode == 'block') {
			return $this->query("UPDATE `goesi_geoip_countries` SET `block` = 1, `allow` = 0 WHERE iso_code = '".$land."'");
		} else if ($mode == 'allow') {
			return $this->query("UPDATE `goesi_geoip_countries` SET `allow` = 1, `block` = 0 WHERE iso_code = '".$land."'");
		}
	}
	
	function getLastError() {
		return $this->getOne("SELECT `last_error` FROM `goesi_geoip_config` WHERE id = 1");
	}
	
	function setLastError($value) {
		return $this->query("UPDATE `goesi_geoip_config` SET `last_error` = '".$value."'");
	}
	
	function setServer_url() {
		return $this->query("UPDATE `goesi_geoip_config` SET `server_url` = '".(($_SERVER['SERVER_NAME'] != "")?$_SERVER['SERVER_NAME']:$_SERVER['HTTP_HOST'])."'");
	}
	
	function getBoonex_user() {
		return $this->getOne("SELECT `boonex_user` FROM `goesi_geoip_config` WHERE id = 1");
	}
	
	function setBoonex_user($value) {
		return $this->query("UPDATE `goesi_geoip_config` SET `boonex_user` = '".$value."'");
	}
	
	function getServer_token() {
		return $this->getOne("SELECT `server_token` FROM `goesi_geoip_config` WHERE id = 1");
	}
	
	function setServer_token($value) {
		return $this->query("UPDATE `goesi_geoip_config` SET `server_token` = '".$value."'");
	}
	
}

?>
