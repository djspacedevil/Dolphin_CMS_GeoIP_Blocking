## 
Thanks for buying my module.
If you need help with the installation. Write a mail to sven@sven-goessling.de Subject: "GEOIP Blocker Installation" 
Write your problem, when you become a error message, write the complete error message. Look in your error_log for this.
This will help me to find what is wrong.

PHP5+ 

##

Install instruction: 

######################################################
## Method 1 - Remote GeoIP Check:
Step 1:
Upload all files to the Modules folder of your installation

Step 2: Go to your backend and install the modul.

Step 3: Insert your boonex user name in the GeoIP Blocker/Allower Area 
Optimal: Insert your private token.

Step 4:
Open /inc/header.inc.php 
Search Line : define('BX_DOL_URL_ROOT', $site['url']);
Insert above:
//Remote GeoIP-Check
if (file_exists(__DIR__. '/../modules/goesi/geoip_blocking/data/check.php')) {
	include_once(__DIR__ . '/../modules/goesi/geoip_blocking/data/check.php');
}
//Remote GeoIP-Check

Step 5: Have Fun with it.
--> First Run, send me your boonex-username, web-URL (look at module-backend) and Paypal Transaktion-ID to sven@sven-goessling.de to become the frist 3 month for free.
    I will send you your free private token back.

-- You have 10.000 free requests (visits) per hour to the Lookup-Server.
-- When you have more requests, install the GeoIP Server Module or you can buy unlimited requests per month for 2$, 20$ for one year or 39$ for two years.

--> 2$ Month Test-Pack: http://www.boonex.com/m/one-month-unlimited-geoip-blocker-token
--> 20$ Year Pack : http://www.boonex.com/m/one-year-unlimited-geoip-blocker-token
--> 39$ 2-Year Pack : http://www.boonex.com/m/two-years-unlimited-geoip-blocker-token
##
######################################################

######################################################
## Method 2 - Local GeoIP Check:
--You need root rights when you will use this Module.

Step 1: 
Make sure that you have install the server module for GEOIP on your Server.

If you have a CentOS Server you can use this:

1.1. look your installed kernel: uname -r
1.2. install all needed modules: yum install gcc gcc-c++ make automake unzip zip xz kernel-devel-`uname -r` iptables-devel perl-Text-CSV_XS geoip

1.2.1
When you dont grep it over yum, install it manuel: rpm -Uvh kernel-devel-2.6.32-220.2.1.el6.x86_64.rpm  <- The number of your kernel version

1.3. cd /opt/
1.4. wget http://sourceforge.net/projects/xtables-addons/files/Xtables-addons/1.47/xtables-addons-1.47.1.tar.xz/download && tar xvf xtables-addons-1.47.1.tar.xz

cd xtables-addons-1.47.1/
make
make install
cd geoip/
./xt_geoip_dl

upload CSV.pm CSV_XS.rm CSV_PP.pm to /usr/lib64/perl5/Text/

./xt_geoip_build GeoIPCountryWhois.csv

edit xt_geoip_build remove "_XS" when the step will not run.

mkdir -p /usr/share/xt_geoip/
'cp' -fr {BE,LE} /usr/share/xt_geoip/

service httpd restart

Create a php file in a test folder on your website with:
<?php
phpinfo();
?>

Open it in a Browser, when you find 
_SERVER["GEOIP_ADDR"]	
_SERVER["GEOIP_CONTINENT_CODE"]
_SERVER["GEOIP_COUNTRY_CODE"]
_SERVER["GEOIP_COUNTRY_NAME"]

The installation is done.

Step 2:
Upload all files to the Modules folder of your installation

Step 3: Go to your backend and install the modul.

Step 4: Check the rights of the file /modules/goesi/geoip_blocking/test_folder/.htaccess
Set the same user as your site will run. eg. websiteuser:psaserv   dont use www-data:www-data

Step 5: Open the Module in the Backend and Test your Config.

Step 6: When your test is fine, check the rights of the file /.htaccess in your home folder.
Set the same user as your site will run. eg. www-data:www-data

Step 7: Think happy ;) Now your site is a little bit secure ;)
##
######################################################