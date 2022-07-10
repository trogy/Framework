<?php
/// This was just a branding file now it contains everything... you're welcome.
$FW_APP_NAME = "Trogy.NZ Framework "; //This is the name of the app.
$FW_AUTHOR = "Trogy.NZ (Marc Anderson)"; //This is the author of the app.
$FW_AUTHOR_ADDRESS = "Auckland, NZ"; //This is the address of the company.
$FW_AUTHOR_LOGO_URL = ""; //This is the url of the author's logo.

$FW_APP_URL = ""; //This is the url of the app. (This is not technical its just for sending links in emails)
$FW_DEV_EMAIL = "hello@trogy.nz"; //This is the email of the developer.
$FW_APP_VERSION = "TGNZ_Framework-Default-0.1"; //This is the version of the app.

$FW_SMTP_HOST = ""; //This is the SMTP host.
$FW_SMTP_PORT = 465; //This is the SMTP port. (Usually 587 or 465)
$FW_SMTP_FROM = ""; //This is the SMTP from address.
$FW_SMTP_USER = ""; //This is the SMTP username.
$FW_SMTP_PASS = ""; //This is the SMTP password.

$DB_Host = 'localhost'; //This is the database host.
$DB_User = 'TGNZ'; //This is the database username.
$DB_Pass = 'TGNZ'; //This is the database password.
$DB_Name = 'framework_default'; //This is the database name.


/*
DO NOT EDIT BELOW THIS LINE
----------------------------------------------------------------------------------------------------------------------
*/
try{
$pdo = new PDO ('mysql:host='.$DB_Host.';dbname='.$DB_Name, $DB_User, $DB_Pass);
}
catch(PDOException $e){
    echo 'PDO Error:' . $e->getMessage() . ' | Please check your system configuration and try again.';
    die();
}