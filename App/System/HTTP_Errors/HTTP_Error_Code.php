<?php
/* 
Trogy.NZ Framework (Originally Geared)
@author: Marc Anderson (marc.anderson@trogy.nz)
@license: MIT / COMMONS CLAUSE

Use this to manually throw error pages.

Default Error Pages
Licence: Apache-2.0 License
https://github.com/alexphelps/server-error-pages
*/

function Geared_HTTP_Error($ErrorCode){
    switch($ErrorCode){
        case '401' :
            header($_SERVER["SERVER_PROTOCOL"] . "401 Unauthorized");
            logger::core('HTTP Error 401');
            require '401.html';
            break;

        case '403' :
            header($_SERVER["SERVER_PROTOCOL"] . "403 Forbidden");
            logger::core('HTTP Error 403');
            require '403.html';
            break;

        case '404' :
            header($_SERVER["SERVER_PROTOCOL"] . "404 Not Found");
            logger::core('HTTP Error 404');
            require '404.html';
            break;
            
        case '405' :
            header($_SERVER["SERVER_PROTOCOL"] . "405 Method Not Allowed");
            logger::core('HTTP Error 405');
            require '405.html';
            break;
        
        case '500' :
            header($_SERVER["SERVER_PROTOCOL"] . "500 Internal Server Error");
            logger::core('HTTP Error 500');
            require '500.html';
            break;
            
        case '503' :
            header($_SERVER["SERVER_PROTOCOL"] . "503 Service Unavailable");
            logger::core('HTTP Error 503');
            require '503.html';
            break;
        default : 
            header($_SERVER["SERVER_PROTOCOL"] . "500 Internal Server Error");
            logger::core('HTTP Error 500 DEAFULT');
            require '500.html';
            break;

        
    }
}
