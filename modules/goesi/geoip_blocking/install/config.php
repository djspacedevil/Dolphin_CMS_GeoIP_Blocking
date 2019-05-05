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

$aConfig = array(
	/**
	 * Main Section.
	 */	
	'title' => 'GeoIP Blocker/Allower', // module title, this name will be displayed in the modules list
    'version' => '1.0.0', // module version, change this number everytime you publish your mod
	'vendor' => 'Sven-Goessling', // vendor name, also it is a folder name in modules folder
	'update_url' => '', // url to get info about available module updates
	
	'compatible_with' => array( // module compatibility
        '7.1.x'  // it tells that the module can be installed on Dolphin 7.0.0 only.
    ),

    /**
	 * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
	 */
	'home_dir' => 'goesi/geoip_blocking/', // folder where module files are located, it describes path from /modules/ folder.
	'home_uri' => 'geoip_blocking', // module URI, so the module will be accessable via the following urls: m/bloggie/ or modules/?r=bloggie/
	
	'db_prefix' => 'goesi_geoip_block_', // database prefix for all module tables, it is better to compose it from vendor prefix + module prefix, in out case it is me (vendor prefix) and blgg(Bloggie module prefix)
    'class_prefix' => 'GoesiGeoIP', // class prefix for all module classes, it is better to compose it from vendor prefix + module prefix, in out case it is Me (vendor prefix) and Blgg(Bloggie module prefix)

	/**
	 * Installation instructions, for complete list refer to BxDolInstaller Dolphin class
	 */
	'install' => array(
        'update_languages' => 1, // add languages
        'execute_sql' => 1, // process install.sql file
        'recompile_permalinks' => 1, // recompile permalinks cache
	),
	/**
	 * Uninstallation instructions, for complete list refer to BxDolInstaller Dolphin class
	 */    
	'uninstall' => array (
        'update_languages' => 1, // remove added languages
        'execute_sql' => 1, // process uninstall.sql file
        'recompile_permalinks' => 1, // recompile permalinks cache
    ),

	/**
	 * Category for language keys, all language keys will be places to this category, but it is still good practive to name each language key with module prefix, to avoid conflicts with other mods.
	 */
	'language_category' => 'GeoIP Blocker',

	/**
	 * Permissions Section, list all permissions here which need to be changed before install and after uninstall, see examples in other BoonEx modules
	 */
	'install_permissions' => array(),
    'uninstall_permissions' => array(),

	/**
	 * Introduction and Conclusion Section, reclare files with info here, see examples in other BoonEx modules
	 */
	'install_info' => array(
		'introduction' => '',
		'conclusion' => ''
	),
	'uninstall_info' => array(
		'introduction' => '',
		'conclusion' => ''
	)
);

?>
