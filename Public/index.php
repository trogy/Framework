<?php
/* 
Trogy.NZ Framework.
@author: Marc Anderson (marc.anderson@trogy.nz)

<-------------- Trogy.NZ Framework License -------------->

Copyright 2019-2022 Marc Anderson

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), 
to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, 
and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
IN THE SOFTWARE.

“Commons Clause” License Condition v1.0

The Software is provided to you by the Licensor under the License, as defined below, subject to the following condition.

Without limiting other conditions in the License, the grant of rights under the License will not include, and the License
does not grant to you, the right to Sell the Software.

For purposes of the foregoing, “Sell” means practicing any or all of the rights granted to you under the License to provide
to third parties, for a fee or other consideration (including without limitation fees for hosting or consulting/ support
services related to the Software), a product or service whose value derives, entirely or substantially, from the functionality
of the Software. Any license notice or attribution required by the License must also include this Commons Clause License Condition notice.

Software: Trogy.NZ Framework

License: MIT

Licensor: Marc Anderson
<-------------- Framework License -------------->
<---------------------------------------->
*/

//Request URL used for referrer
$REQUEST_URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//Load Composer
require_once('../App/vendor/autoload.php');
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\ErrorHandler\ErrorHandler;
use Symfony\Component\ErrorHandler\DebugClassLoader;
use Spatie\Url\Url;
//Load Core
require_once('../App/System/Loader.php');
//Load Config
require_once('../App/System/Config/Config.php');

//Start App Session
if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
    // session isn't started
    session_start();
}

//Register the error handler
Debug::enable(E_RECOVERABLE_ERROR & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED, false);
//ErrorHandler::register();

//Check XSRF Token on every request
if(!isset($_SESSION['XSRF-TOKEN'])){
    $_SESSION['XSRF-TOKEN'] = XSRF_Generate();
    Logger::core("XSRF-TOKEN Was Not Present Generated Token Is: " . $_SESSION['XSRF-TOKEN']);
}

/* GET REQUEST URL */
$request = $_SERVER['REQUEST_URI'];

/* REMOVE GET VALUE FROM URL */
if(str_contains($request, '?')) {
    $request = substr($request, 0, strpos($request, "?"));
}
if(str_contains($request, '#')) {
    $request = substr($request, 0, strpos($request, "#"));
}
Logger::info("Requested: ". $request . ""); 
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}
if(isset($_GET)){
    if(!empty($_GET)){
        Logger::info("Request GET Values: ". $_SERVER['QUERY_STRING'] . ""); 
    }
}

/*echo $request;*/
/* ROUTER */
switch (strtolower($request)) {
    /* DEFAULT ROUTES */
    case '/' :
        $PAGE_NAME = "Home";
        require '../App/App_Pages/App.php';
        break;
    case '' :
        $PAGE_NAME = "Home";
        require '../App/App_Pages/App.php';
        break;
    /* - ERROR PAGE (404) - */
    default:
        logger::info("404 Page Not Found - Calling Core");
        Geared_HTTP_Error('404');
        break;      
}
