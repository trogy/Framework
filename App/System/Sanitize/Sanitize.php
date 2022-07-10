<?php
namespace TGNZ;
class Sanitize{
    public static function Sanitize_String($Data){
        $Data = strip_tags($Data);
        $Data = trim($Data);
        $Data = stripslashes($Data);
        $Data = htmlspecialchars($Data);
        return $Data;
    }
    public static function Sanitize_Email($Data){
        $Data = filter_var($Data, FILTER_SANITIZE_EMAIL);
        return $Data;
    }
    public static function Sanitize_Int($Data){
        $Data = filter_var($Data, FILTER_SANITIZE_NUMBER_INT);
        return $Data;
    }
    public static function Sanitize_Float($Data){
        $Data = filter_var($Data, FILTER_SANITIZE_NUMBER_FLOAT);
        return $Data;
    }
    public static function Sanitize_URL($Data){
        $Data = filter_var($Data, FILTER_SANITIZE_URL);
        return $Data;
    }
}