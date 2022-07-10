<?php
use TGNZ\Auth;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
logger::info('Loading!');
function XSRF_Generate(){
    return bin2hex(openssl_random_pseudo_bytes(64));
}
//This function will compare the provided two tokens and regenerate a new token.
function XSRF_Check($Token, $Session_Token){
    if($Token == $Session_Token){
        $_SESSION['XSRF-TOKEN'] = XSRF_Generate();
        Logger::core("XSRF Token Verified & Regenerated XSRF TOKEN IS " . $_SESSION['XSRF-TOKEN']);
        return true;
    }
    else{
        return false;
    }
}
//Check if a users login is still present in session
function Login_Check(){
    global $REQUEST_URL;
    if(isset($_SESSION['Login']->Login_Status)){
        if($_SESSION['Login']->Login_Status == true){
            return true;
        }
        else{
            header('Location: /'.$FW_LOGIN_PAGE.'?Error=No Active Session&referer='.$REQUEST_URL, true);
            exit();
        }
    }
    else{
        header('Location: /'.$FW_LOGIN_PAGE.'?Error=No Active Session&referer='.$REQUEST_URL,true);
        exit();    
    }
}
//Send mail using PHPMailer
function SendMail($SendTo, $Subject, $Message){
    global $FW_SMTP_HOST;
    global $FW_SMTP_PORT;
    global $FW_SMTP_FROM;
    global $FW_SMTP_USER;
    global $FW_SMTP_PASS;
    global $FW_APP_NAME;

    try{
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $FW_SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = $FW_SMTP_USER;
        $mail->Password = $FW_SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $FW_SMTP_PORT;
        $mail->setFrom($FW_SMTP_FROM, $FW_APP_NAME);
        $mail->addAddress($SendTo);
        $mail->isHTML(true);
        $mail->Subject = $Subject;
        $mail->Body = $Message;
        $mail->send();
        return true;
    } catch (Exception $e) {
        Logger::error('Error in function SendMail() ' . $mail->ErrorInfo );
        return false;
    }
}
function SendMailWithReplyTo($SendTo, $ReplyTo, $Subject, $Message){
    global $FW_SMTP_HOST;
    global $FW_SMTP_PORT;
    global $FW_SMTP_FROM;
    global $FW_SMTP_USER;
    global $FW_SMTP_PASS;
    global $FW_APP_NAME;

    try{
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $FW_SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = $FW_SMTP_USER;
        $mail->Password = $FW_SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $FW_SMTP_PORT;
        $mail->setFrom($FW_SMTP_FROM, $FW_APP_NAME);
        $mail->addAddress($SendTo);
        $mail->addReplyTo($ReplyTo);
        $mail->isHTML(true);
        $mail->Subject = $Subject;
        $mail->Body = $Message;
        $mail->send();
        return true;
    } catch (Exception $e) {
        Logger::error('Error in function SendMail() ' . $mail->ErrorInfo );
        return false;
    }
}
//Format bytes to megabytes
function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('', 'K', 'M', 'G', 'T');   

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}