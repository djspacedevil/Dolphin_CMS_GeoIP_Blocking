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

	$url = 'https://smile.smileandgo.de/GeoIP.php';
	if (!isset($_SERVER['REMOTE_ADDR'])) {
		$_SERVER['REMOTE_ADDR'] = gethostbyname(gethostname());
	}
	// Connect
		if(isset($GLOBALS['db']['host'])) $db['host'] = $GLOBALS['db']['host'];
		if(isset($GLOBALS['db']['user'])) $db['user'] = $GLOBALS['db']['user'];
		if(isset($GLOBALS['db']['passwd'])) $db['passwd'] = $GLOBALS['db']['passwd'];
		if(isset($GLOBALS['db']['db'])) $db['db'] = $GLOBALS['db']['db'];

		$con = new mysqli($db['host'],$db['user'],$db['passwd'],$db['db']);
		if ($con->connect_errno) {
		printf("Connect failed: %s\n", $con->connect_error);
		exit();
	}
	//
	$result = $con->query("SELECT * FROM `goesi_geoip_config` WHERE `id` = '1' ");
	$server_data['server_url'] = 'No_URL';
	$server_data['boonex_user'] = 'boonex_user';
	if(isset($_SERVER['SERVER_NAME']) || isset($_SERVER['HTTP_HOST'])) {
		$server_data['server_token'] = hash('sha256',(($_SERVER['SERVER_NAME'] != "")?$_SERVER['SERVER_NAME']:$_SERVER['HTTP_HOST']));
	} else {
		$server_data['server_token'] = hash('sha256', $_SERVER['REMOTE_ADDR']);
	}
	$mode = 'block';
	$GeoIPEnable = 0;
	$mod_geoip = 0;
	if ($result->num_rows == 1) {
		$data = mysqli_fetch_assoc($result);
		if ($data['server_url'] != "") $server_data['server_url'] = $data['server_url'];
		if ($data['boonex_user'] != "") $server_data['boonex_user'] = $data['boonex_user'];
		if ($data['server_token'] != "") $server_data['server_token'] = $data['server_token'];
		if ($data['mode'] != "") $mode = $data['mode'];
		if ($data['GeoIPEnable'] != "") $GeoIPEnable = $data['GeoIPEnable'];
		if ($data['mod_geoip'] != "") $mod_geoip = $data['mod_geoip'];
	}
	//Check mod_geoip
	if ($mod_geoip == 0) {
		$fields = array(
					'Request_IP' => urlencode($_SERVER['REMOTE_ADDR']),
					'Servername' => urlencode($server_data['server_url']),
					'User' => urlencode($server_data['boonex_user']),
					'Token' => urlencode($server_data['server_token'])
				);
		
		//url-ify the data for the POST
		$fields_string = '';
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		$head = curl_exec($ch);
	
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($head, 0, $header_size);
		curl_close($ch); 
	
		$body = substr($head, $header_size);
		$response = json_decode($body, TRUE);
	
		if (is_array($response)) {
			$result = $con->query("SELECT `iso_code`, `block`, `allow` FROM `goesi_geoip_countries` WHERE `iso_code` = '".$response['country_code']."'");
			if ($result->num_rows > 0) {
				$country = mysqli_fetch_assoc($result);
				if($mode == 'block' && $country['block'] == 1) {
					ob_start();
					ob_end_clean();
					header('HTTP/1.1 403 Forbidden');
					echo 'Your Country is blocked';
					ob_end_clean();
					exit;
				} else if($mode == 'allow' && $country['allow'] == 0) {
					ob_start();
					ob_end_clean();
					header('HTTP/1.1 403 Forbidden');
					echo 'Your Country is blocked';
					ob_end_clean();
					exit;
				} 
			}
		}
	}
	
?>