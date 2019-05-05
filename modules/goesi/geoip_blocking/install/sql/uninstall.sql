--liste
DROP TABLE `goesi_geoip_config`;
DROP TABLE `goesi_geoip_countries`;

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'GeoIP Blocker' LIMIT 1);
--DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'goesi_geoip_blocking_permalinks';

-- permalinks
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=geoip_blocking/';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'goesi_geoip_blocker';